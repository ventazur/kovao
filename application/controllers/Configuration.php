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
 * CONFIGURATION
 *
 * ============================================================================ */

class Configuration extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        $this->load->model('Admin_model');
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        $this->_index_commun();
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Index commun (www et sous-domaine)
     *
     * ------------------------------------------------------------------------ */
    public function _index_commun()
    {
        //
        // Lister les semestres disponibles
        // 
        
        $semestres = $this->Semestre_model->lister_semestres(
            array(
                'enseignant_id' => $this->enseignant_id,
                'groupe_id'     => $this->groupe_id,
                'exclure_echus' => TRUE
            )
        );

        //
        // Determiner le semestre selectionne
        //

        $semestre_selectionne = $this->enseignant['semestre_id'];
        $semestre_status      = NULL; // 'passe', 'actuel', 'futur'

        if ( ! empty($semestre_selectionne) && is_numeric($semestre_selectionne))
        {
            $s = $semestres[$semestre_selectionne];

            // Verifier le status du semestre

            if ($s['semestre_debut_epoch'] > $this->now_epoch)
            {
                $semestre_status = 'futur';
            } 
            elseif ($s['semestre_fin_epoch'] < $this->now_epoch)
            {
                $semestre_status = 'passe';
            }
            elseif ($s['semestre_debut_epoch'] < $this->now_epoch && $s['semestre_fin_epoch'] < $this->now_epoch)
            {
                $semestre_status = 'actuel';
            }
        }

        //
        // Lister les cours disponibles selon le groupe de l'enseignant.
        //        

        $cours_raw = $this->Cours_model->lister_cours(
            array(
                'groupe_id' => $this->groupe_id,
                'desuet'    => FALSE // Ne pas afficher les cours desuets
            )
        );

        //
        // Lister les cours que l'enseignant donne
        //        

        $cours_selectionnes = $this->Cours_model->lister_cours_selectionnes($this->enseignant_id, $semestre_selectionne);

        //
        // Lister les evaluations
        //
        
        $evaluations_par_cours = $this->_extraire_evaluations(array_keys($cours_selectionnes));

        //
        // Lister les evaluations selectionnees par l'enseignant pour le semestre selectionne
        //

        $evaluations_selectionnees = $this->Evaluation_model->lister_evaluations_selectionnees($this->enseignant_id, $semestre_selectionne);

		//
		// Lister les eleves
		//

        $eleves = $this->Cours_model->lister_eleves(
            $semestre_selectionne, 
            array(
                'cours_ids' => array_keys($cours_raw)
            )
        );

        $comptes           = array();
        $comptes_da        = array();
        $comptes_autorises = array();

        if ($this->groupe_id != 0)
        {
            //
            // Extraire les eleves deja autorises pour chacun de vos cours
            //

            $comptes_autorises = $this->Etudiant_model->extraire_comptes_autorises($this->enseignant['semestre_id']);

            // p($comptes_autorises); die;

            //
            // Chercher les comptes des eleves
            //
            
            if ( ! empty($eleves))
            {
                $numero_das = array_column_multi('numero_da', $eleves);
            }

            if ( ! empty($numero_das))
            {
                $comptes = $this->Etudiant_model->extraire_comptes_etudiants($numero_das);

                foreach($numero_das as $numero_da)
                {
                    if (($found = array_search_multi($numero_da, $comptes, 'etudiant_id')) != array())
                    {
                        $comptes_da[$numero_da] = $found;
                    }
                }
            }
        }

        //
        // Preparer les variables pour l'affichage
        //

        $this->data = array_merge(
            $this->data,
            array(
                'semestres'             => $semestres,
                'semestre_selectionne'  => $semestre_selectionne,
                'semestre_status'       => $semestre_status,
                'cours_raw'             => $cours_raw,
                'cours_selectionnes'    => $cours_selectionnes,
                'evaluations_par_cours' => $evaluations_par_cours,
                'evaluations_selectionnees' => $evaluations_selectionnees,
                'eleves'                => $eleves,             // Tous les eleves (etudiants) d'apres les listes fournies par l'enseignant
                'comptes'               => $comptes,            // Tous les comptes des etudiants
                'comptes_da'            => $comptes_da,         // La correspondance compte - Numero DA
                'comptes_autorises'     => $comptes_autorises   // Tous les comptes autorises par l'enseignant
            )
        );

        $this->_affichage(__FUNCTION__);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les evaluations
     *
     * ------------------------------------------------------------------------ */
    public function _extraire_evaluations($cours_ids = array())
    {
        if (empty($cours_ids))
        {
            return array();
        }

        $evaluations = array();

        foreach($cours_ids as $cours_id)
        {
            if ( ! array_key_exists($cours_id, $evaluations))
            {
                $evaluations[$cours_id] = array();
            }

            $e = $this->Evaluation_model->extraire_evaluations_par_cours_ids(
                array(
                    'cours_ids' => array($cours_id)
                )
            );

            $evaluations[$cours_id] = $e;
        }

        return $evaluations;
    }

    /* ------------------------------------------------------------------------
     *
     * A J A X
     *
     * ------------------------------------------------------------------------ */

    /* ------------------------------------------------------------------------
     *
     * (ajax) Selectionner ou deselectionner un semestre
     *
     * ------------------------------------------------------------------------ */
    public function selection_semestre()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Vous ne pouvez accéder directement cette page.');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Semestre_model->selection_semestre($this->enseignant_id, $post_data['semestre_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * (ajax) Selectionner ou deselectionner un cours
     *
     * ------------------------------------------------------------------------ */
    public function selection_cours()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Vous ne pouvez accéder directement cette page.');
        }

        if (($post_data = catch_post(array('ids' => array('cours_id', 'semestre_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		if ($this->Cours_model->selection_cours($this->enseignant['enseignant_id'], $post_data['semestre_id'], $post_data['cours_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * (ajax) Selectionner ou deselectionner une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function selection_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Vous ne pouvez accéder directement cette page.');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id', 'evaluation_id', 'cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if (array_key_exists('evaluation_selectionnee', $post_data) && $post_data['evaluation_selectionnee'] != 1)
        {
            //
            // Verifier l'integrite de l'evaluation lors de la selection seulement
            //

            $result = $this->Evaluation_model->verifier_integrite_evaluation($post_data['evaluation_id']);

            if ($result !== TRUE)
            {
                echo json_encode(9);
                return;
            }
        }

		if ($this->Evaluation_model->selection_evaluation($post_data['semestre_id'], $post_data['evaluation_id'], $post_data['cours_id']) !== TRUE)
        {
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * (ajax) Associer un compte
     *
     * ------------------------------------------------------------------------ */
    public function associer_compte()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('etudiant_id', 'semestre_id', 'cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($this->Etudiant_model->associer_compte($post_data) === TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Dissocier un compte
     *
     * ------------------------------------------------------------------------ */
    public function dissocier_compte()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('etudiant_id', 'semestre_id', 'cours_id')))) === FALSE)
        {
            echo json_encode('ERREUR : parametres manquants');
            return;
        }

        if ($this->Etudiant_model->dissocier_compte($post_data) === TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode('ERREUR : ne peut dissocier compte');
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Effacement d'une liste
     *
     * ------------------------------------------------------------------------ */
    public function effacer_liste()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id', 'cours_id')))) === FALSE)
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
				case 'groupe' :
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
		// effetuer l'effacement d'une liste
		//

		if ($this->Cours_model->effacer_liste_etudiants($post_data['semestre_id'], $post_data['cours_id'], $post_data['groupe']))
		{
			echo json_encode(TRUE);
			return;
		}

		echo json_encode(FALSE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Ajouter un etudiant a une liste
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_etudiant_liste()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id', 'cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
            {
                case 'nom' :
                case 'prenom' :
                case 'numero_da' :
                case 'groupe' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        //
        // Validation du formulaire
        //

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
            return;
        }

		//
		// Ajouter l'etudiant
		//

		if ($this->Cours_model->ajouter_etudiant_liste($post_data))
		{
			echo json_encode(TRUE);
			return;
		}

		echo json_encode(FALSE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Modifier un etudiant
     *
     * ------------------------------------------------------------------------ */
    public function modifier_etudiant_liste()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if ( ! array_key_exists('eleve_id', $post_data)     ||
             ! is_numeric($post_data['eleve_id'])
           )
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
            {
                case 'groupe' :
                case 'numero_da' :
					$validation_rules = 'required';
                    break;
                case 'temps_supp' :
                    $validation_rules = 'required';
                    break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        //
        // Validation du formulaire
        //

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
            return;
        }

		//
		// Modifier l'etudiant de la liste
		//

		if ($this->Cours_model->modifier_etudiant_liste($post_data))
		{
			echo json_encode(TRUE);
			return;
		}

		echo json_encode(FALSE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Effacer un etudiant d'une liste
     *
     * ------------------------------------------------------------------------ */
    public function effacer_etudiant_liste()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('eleve_id', 'semestre_id', 'cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
            {
                case 'groupe' :
                case 'numero_da' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        //
        // Validation du formulaire
        //

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
            return;
        }

		//
		// Effacer l'etudiant de la liste
		//

		if ($this->Cours_model->effacer_etudiant_liste($post_data))
		{
			echo json_encode(TRUE);
			return;
		}

		echo json_encode(FALSE);
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

        if ($page == '_index_commun')
        {
            $this->load->view('configuration/configuration_commun', $this->data);
        }

        $this->load->view('commons/footer');
    }
}
