<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * KOVAO - Système d’évaluation open source
 * Copyright (C) 2018–2025 KOVAO Project
 *
 * FR : Ce fichier fait partie du projet KOVAO.
 * Distribué sous licence GNU AGPL-3.0 avec conditions additionnelles.
 * Les versions dérivées peuvent être distribuées sous un autre nom,
 * mais doivent mentionner leur origine dans le projet KOVAO.
 * Voir le fichier LICENSE pour les détails.
 *
 * EN: This file is part of the KOVAO project.
 * Licensed under GNU AGPL-3.0 with additional terms.
 * Derivative versions may be distributed under another name,
 * but must credit the original KOVAO project.
 * See LICENSE for details.
 */

/* ============================================================================
 *
 * RECHERCHE
 *
 * ============================================================================ */

class Recherche extends MY_Controller 
{
	public function __construct()
    {
    	parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        $this->data['current_controller'] = strtolower(__CLASS__);

        $this->load->model('Recherche_model');
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */

    public function index()
    {
        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Recherche en direct (live search)
     *
     * ------------------------------------------------------------------------ */
	public function recherche_en_direct()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier la requete
        //

        if (strlen($post_data['requete']) < 3)
        {
            echo json_encode(FALSE);
            return;
        }

        $requete = $post_data['requete'];

        //
        // Chercher par matricule
        //

        if (ctype_digit($requete))
        {
            $requete = trim($requete);

            $resultats = $this->Recherche_model->recherche_matricule($requete);
        }
    
        //
        // Chercher par texte
        //

        else
        {
            $requete = filter_input(INPUT_POST, 'requete', FILTER_SANITIZE_SPECIAL_CHARS);
            $requete = strip_accents(trim($requete));

            if ( ! preg_match('/^[A-Za-z0-9 \-_\']+$/i', $requete))
            {
                echo json_encode(9);
                return;
            }

            $resultats = $this->Recherche_model->recherche_texte($requete);
        }

        $html = '';

        //
        // Aucun resultat trouve
        //

        if ( ! $resultats['soumissions_compte'] && ! $resultats['evaluations_compte'])
        {
            $html = $this->load->view('bienvenue/_recherche_resultats_aucun', '', TRUE);

            echo json_encode($html);
            return;
        }

        $resultats['cours'] = $this->Cours_model->lister_cours(array('groupe_id' => $this->groupe_id));

        if ( ! empty($resultats['etudiants']))
        {
            $html .= $this->load->view('bienvenue/_recherche_resultats_etudiants', $resultats, TRUE);
        }

        if ( ! empty($resultats['soumissions']))
        {
            $html .= $this->load->view('bienvenue/_recherche_resultats_soumissions', $resultats, TRUE);
        }

        if ( ! empty($resultats['evaluations'])) 
        {
            $html .= $this->load->view('bienvenue/_recherche_resultats_evaluations', $resultats, TRUE);
        }

        echo json_encode($html);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Recherche en direct ADMIN (live search)
     *
     * ------------------------------------------------------------------------ */
	public function recherche_en_direct_admin()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier la requete
        //

        if (strlen($post_data['requete']) < 3)
        {
            echo json_encode(FALSE);
            return;
        }

        $requete = $post_data['requete'];

        //
        // Chercher par texte
        //

        $requete = filter_input(INPUT_POST, 'requete', FILTER_SANITIZE_SPECIAL_CHARS);
        $requete = strip_accents(trim($requete));

        if ( ! preg_match('/^[A-Za-z0-9 \-_\']+$/i', $requete))
        {
            echo json_encode(9);
            return;
        }

        $resultats = $this->Recherche_model->recherche_etudiants_admin($requete);

        $html = '';

        //
        // Aucun resultat trouve
        //

        if ( ! $resultats['etudiants_compte'])
        {
            $html = $this->load->view('bienvenue/_recherche_resultats_aucun', '', TRUE);

            echo json_encode($html);
            return;
        }

        if ( ! empty($resultats['etudiants']))
        {
            $html .= $this->load->view('bienvenue/_recherche_resultats_etudiants_admin', $resultats, TRUE);
        }

        echo json_encode($html);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Rechercher
     *
     * ------------------------------------------------------------------------ */
	public function rechercher()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post()) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'search_query' :
					$validation_rules = 'required';
                    break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }
    
        //
        // Recherche en direct
        //

        $semestres = (array_key_exists('semestres', $post_data) && $post_data['semestres'] == 'on') ? TRUE : FALSE;

        if ($this->enseignant['privilege'] >= 90)
        {
            $etudiants = (array_key_exists('etudiants', $post_data) && $post_data['etudiants'] == 'on') ? TRUE : FALSE;
        }
        else
        {
            $etudiants = FALSE;
        }

        $search_query = filter_input(INPUT_POST, 'search_query', FILTER_SANITIZE_SPECIAL_CHARS);
        $search_query = strip_accents(trim($search_query));

        if ( ! preg_match('/^[a-z0-9 \-]+$/i', $search_query))
        {
            echo json_encode(3);
            return;
        }

        $results = $this->Recherche_model->rechercher2($search_query, array('semestres' => $semestres, 'etudiants' => $etudiants));  

        if ($results == FALSE)
        {
            echo json_encode(1);
            return;
        }

        if (count($results) > 100)
        {
            echo json_encode(2);
            return;
        }

        $this->data['soumissions'] = $results;

        $html = $this->load->view('recherche/_etudiants', $this->data, TRUE);

        echo json_encode($html);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
	{
        $this->load->view('commons/header', $this->data);
        $this->load->view('recherche/etudiants', $this->data);
        $this->load->view('commons/footer');
    }
}
