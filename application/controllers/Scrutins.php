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
 * SCRUTINS
 *
 * ----------------------------------------------------------------------------
 *
 * Les scrutins sont reserves aux groupes.
 *
 * ============================================================================ */

class Scrutins extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->config->item('scrutins') || ! $this->est_enseignant || ! $this->groupe_id)
        {
            redirect(base_url());
            exit;
        }

        $this->load->model('Vote_model');

        $this->data['current_controller'] = strtolower(__CLASS__);
	}

    /* ------------------------------------------------------------------------
     *
     * Modifier la question d'un scrutin
     *
     * ------------------------------------------------------------------------ */
    public function modifier_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
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
				case 'scrutin_texte' :
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
		// effetuer les modifications a la question du scrutin
		//
    
		if ($this->Vote_model->modifier_question($post_data['scrutin_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Ajouter un choix au scrutin
     *
     * ------------------------------------------------------------------------ */
    public function ajout_choix()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
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
				case 'choix_texte' :
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

        $scrutin_id = $post_data['scrutin_id'];

        unset($post_data['scrutin_id']);

        if ($this->Vote_model->ajouter_choix($scrutin_id, $post_data) == TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un choix au scrutin
     *
     * ------------------------------------------------------------------------ */
    public function effacer_choix()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id', 'choix_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id = $post_data['scrutin_id'];
        $choix_id   = $post_data['choix_id'];

        if ($this->Vote_model->effacer_choix($scrutin_id, $choix_id) == TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Changer la participation d'un participant
     *
     * ------------------------------------------------------------------------ */
    public function changer_participation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id', 'enseignant_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id    = $post_data['scrutin_id'];
        $enseignant_id = $post_data['enseignant_id'];

        if ($this->Vote_model->changer_participation($scrutin_id, $enseignant_id) == TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Changer le respect du code morin
     *
     * ------------------------------------------------------------------------ */
    public function changer_code_morin()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id = $post_data['scrutin_id'];

        if ($this->Vote_model->changer_code_morin($scrutin_id) == TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Changer l'anonimite d'un scrutin
     *
     * ------------------------------------------------------------------------ */
    public function changer_anonyme()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id = $post_data['scrutin_id'];

        if ($this->Vote_model->changer_anonyme($scrutin_id) == TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Changer la date d'echeance
     *
     * ------------------------------------------------------------------------ */
    public function changer_date_echeance()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('date_echeance', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id      = $post_data['scrutin_id'];
        $date_echeance = $post_data['date_echeance'];

        if (empty($date_echeance))
        {
            $result = $this->Vote_model->changer_date_echeance($scrutin_id, '');
        }
        else
        {
            if ( ! preg_match('/2[0-9]{3}-[0-9]{2}-[0-9]{2}/', $date_echeance))
            {
                echo json_encode(FALSE);
                return;
            }

            $result = $this->Vote_model->changer_date_echeance($scrutin_id, $date_echeance);
        }

        if ($result)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* -------------------------------------------------------------------------------------------- 
     *
     * Upload
     *
     * -------------------------------------------------------------------------------------------- */
    function upload()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            redirect(base_url());
            exit;
        }

        if ( ! isset($_FILES['upload_file']))
        {
            echo json_encode('ERREUR: Aucun fichier téléchargé.');
            return;
        } 

        $upload_file = $_FILES['upload_file'];

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id  = $post_data['scrutin_id'];

        $config['upload_path'] = $this->config->item('documents_path');
		$config['max_size'] = 10240; // in KB

        $config['file_name'] = 's' . $scrutin_id . '_' . strtolower(random_string('alpha', 8));
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|ppt|pptx';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('upload_file'))
        {
			echo json_encode(FALSE);
			return;
		}

		$filedata = $this->upload->data();

        if (($result = $this->Vote_model->ajouter_document($scrutin_id, $filedata)) != FALSE)
        {
			echo json_encode(TRUE);
			return;
		}

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier la description (caption) d'un document
     *
     * ------------------------------------------------------------------------ */
    public function modifier_document_caption()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id', 'scrutin_doc_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'doc_caption' :
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

		if ($this->Vote_model->modifier_document_caption($post_data['scrutin_doc_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un document du scrutin
     *
     * ------------------------------------------------------------------------ */
    public function effacer_document()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id', 'scrutin_doc_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id     = $post_data['scrutin_id'];
        $scrutin_doc_id = $post_data['scrutin_doc_id'];

        if ($this->Vote_model->effacer_document($scrutin_id, $scrutin_doc_id) == TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un scrutin
     *
     * ------------------------------------------------------------------------ */
    public function effacer_scrutin()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('scrutin_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_id = $post_data['scrutin_id'];

        $this->Vote_model->effacer_scrutin($scrutin_id);

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un scrutin lance
     *
     * ------------------------------------------------------------------------ */
    public function effacer_scrutin_lance()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if ( ! array_key_exists('scrutin_reference', $post_data) || empty($post_data['scrutin_reference']) ||  ! ctype_alpha($post_data['scrutin_reference']))
        {
            echo json_encode(FALSE);
            return;
        }

        $scrutin_reference = $post_data['scrutin_reference'];

        $this->Vote_model->effacer_scrutin_lance($post_data['scrutin_reference']);

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        //
        // Lister les scrutins qui requiert le vote de l'enseignant
        //

        $this->data['scrutins'] = $this->Vote_model->extraire_scrutins_a_voter();

        //
        // Lister les scrutins participatifs
        //

        $this->data['scrutins_participatifs'] = $this->Vote_model->extraire_scrutins_participatifs(); 

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Creer un scrutin
     *
     * ------------------------------------------------------------------------ */
	public function creer()
    {
		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Regles
        //

        $this->form_validation->set_rules('scrutin_texte', 'texte', 'required');
		$this->form_validation->set_rules('groupe_id', 'groupe', 'required|numeric');
        $this->form_validation->set_rules('enseignant_id', 'enseignant', 'required|numeric');

        //
        // Messages d'erreurs
        //

		$errors = array(
            'scrutin_texte' => NULL
		);

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
		// Validation du formulaire (form)
        // 
        
        $form_has_errors = TRUE;

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.
            //

            $enseignants = $this->Enseignant_model->lister_enseignants(
                array(
                    'groupe_id' => $this->groupe_id,
                    'actif'     => TRUE
                )
            );

            $this->data['enseignants'] = $enseignants;

            $this->_affichage('creer');
            return;
        }

        //
        // Le formulaire a ete rempli avec success. 
        // Creer ce scrutin.
        //

        $post_data = $this->input->post(NULL, TRUE);

        if (($scrutin_id = $this->Vote_model->creer_scrutin($post_data)) === FALSE)
        {
            // Il y a eu un probleme a la creation de votre scrutin.

            generer_erreur('VTN999', "Il y a eu un problème à la créaction de votre scrutin.");
            return;
        }

        redirect(base_url() . $this->current_controller . '/editeur/' . $scrutin_id);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Editer un scrutin
     *
     * ------------------------------------------------------------------------ */
	public function editeur($scrutin_id)
    {
        if (empty($scrutin_id) || ! is_numeric($scrutin_id))
        {
            redirect(base_url() . 'votes');
            return;
        }

        if (($scrutin = $this->Vote_model->extraire_scrutin($scrutin_id, array('enseignant_id' => $this->enseignant_id))) === FALSE)
        {
            redirect(base_url() . 'votes');
            return;
        }

        $choix        = $scrutin['choix'];
        $participants = $scrutin['participants'];
        $documents    = $scrutin['documents'];

        unset($scrutin['choix'], $scrutin['participants'], $scrutin['documents']);

        $this->data['scrutin']      = $scrutin; 
        $this->data['scrutin_id']   = $scrutin_id;
        $this->data['choix']        = $choix;
        $this->data['participants'] = $participants;
        $this->data['documents']    = $documents;

        $this->data['enseignants'] = $this->Enseignant_model->lister_enseignants(
            array(
                'groupe_id' => $this->groupe_id,
                'actif'     => TRUE
            )
        );

        $this->_affichage('editeur');
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Previsualisation
     *
     * ------------------------------------------------------------------------ */
    public function previsualisation($scrutin_id)
    {
        if (empty($scrutin_id) || ! is_numeric($scrutin_id))
        {
            redirect(base_url() . 'votes');
            return;
        }

        $scrutin = $this->Vote_model->extraire_scrutin($scrutin_id);

        $choix = $scrutin['choix'];
        $documents = $scrutin['documents'];

        unset($scrutin['choix'], $scrutin['documents']);

        $this->data['reference']        = NULL;
        $this->data['scrutin_id']       = $scrutin_id;
        $this->data['scrutin']          = $scrutin;
        $this->data['choix']            = $choix;
        $this->data['documents']        = $documents;
        $this->data['previsualisation'] = TRUE;

        $this->_affichage('scrutin');
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Lancer un scrutin
     *
     * ------------------------------------------------------------------------ */
    public function lancer($scrutin_id)
    {
        if (empty($scrutin_id) || ! is_numeric($scrutin_id))
        {
            redirect(base_url() . 'votes');
            return;
        }

        $result = $this->Vote_model->lancer_scrutin($scrutin_id);

        if (is_array($result) && strtolower($result['status']) == 'error')
        {       
            $this->data['scrutin_erreur_url'] = base_url() . 'scrutins/editeur/' . $scrutin_id;

            $this->data = array_merge($this->data, $result);
            $this->_affichage('scrutin_erreur_gabarit');
            return;
        }

        $this->data['scrutin_lance'] = $this->Vote_model->extraire_scrutin_par_reference($result['scrutin_reference']);

        $this->_affichage('scrutin_lance');
    }

    /* ------------------------------------------------------------------------
     *
     * Gerer vos scrutins
     *
     * ------------------------------------------------------------------------ */
    public function gerer()
    {
        $data = array(
            'scrutins'                   => $this->Vote_model->extraire_scrutins(array('en_preparation' => TRUE)),
            'scrutins_lances_termines'   => $this->Vote_model->extraire_scrutins_lances(array('enseignant_id' => $this->enseignant_id, 'termine' => TRUE)),
            'scrutins_lances_en_vigueur' => $this->Vote_model->extraire_scrutins_lances(array('enseignant_id' => $this->enseignant_id))
        );

        // Etablir une liste de scrutin_ids pour ceux lances et en vigueur.
        // Enlever les scrutins lances des scrutins (a editer)
        
        $data['scrutin_ids_lances'] = array();
        
        if ( ! empty($data['scrutins_lances_en_vigueur']))
        {
            foreach($data['scrutins_lances_en_vigueur'] as $s)
            {
                $data['scrutin_ids_lances'][] = $s['scrutin_id'];

                unset($data['scrutins'][$s['scrutin_id']]);
            }
        }

        $this->data = array_merge($this->data, $data);

        $this->_affichage('gerer');
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

        if (empty($page))
        {
            $this->load->view('scrutins/scrutins', $this->data);
        }
        else
        {
            $this->load->view('scrutins/' . $page, $this->data);
        }

        $this->load->view('commons/footer');
    }
   
}
