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
 * GROUPE
 *
 * ============================================================================ */

class Groupe extends MY_Controller 
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
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index($groupe_id)
    {
        redirect(base_url());
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Gerer
     *
     * ------------------------------------------------------------------------ */
	public function gerer()
    {
        if ($this->groupe_id == 0)
        {
            // Le groupe Personnel de l'etudiant

            $this->gerer_groupe_www($this->config->item('groupe_www'), $this->config->item('ecole_www'));
            return;
        }
        else
        {
            $groupe = $this->Groupe_model->extraire_groupe(array('groupe_id' => $this->groupe_id));
            $ecole  = $this->Ecole_model->extraire_ecole(array('groupe_id' => $this->groupe_id));

            if ($this->enseignant['niveau'] >= $this->config->item('niveaux')['admin_groupe'])
            {
                $this->_gerer_groupe_sous_domaine($this->groupe_id, $groupe, $ecole);
                return;
            }
        }
        
        generer_erreur('GRP5566', "Vous n'avez pas la permission de gérer ce groupe.");
        die;
    }

    /* ------------------------------------------------------------------------
     *
     * Gerer son groupe www (groupe par default)
     *
     * ------------------------------------------------------------------------ */
    public function gerer_groupe_www($groupe, $ecole)
    {
        if ($this->groupe_id != 0)
        {
            redirect(base_url());
            exit;
        }

        $semestres = $this->Semestre_model->lister_semestres(
            array(
                'groupe_id'     => $this->groupe_id,
                'enseignant_id' => $this->enseignant_id
            )
        );

        $cours_raw = $this->Cours_model->lister_cours(
            array(
                'groupe_id' => $this->groupe_id
            )
        );

        $this->data = array_merge(
            $this->data,
            array(
                'ecole'     => $ecole,
                'ecole_id'  => $ecole['ecole_id'],
                'groupe'    => $groupe,
                'groupe_id' => $this->groupe_id,
                'semestres' => $semestres,
                'cours_raw' => $cours_raw
            )
        );

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

        $this->_affichage('gerer_groupe');
    }

    /* ------------------------------------------------------------------------
     *
     * Gerer un groupe sous-domaine
     *
     * ------------------------------------------------------------------------ */
    public function _gerer_groupe_sous_domaine($groupe_id, $groupe, $ecole)
    {
        $semestres   = $this->Semestre_model->lister_semestres(array('groupe_id' => $groupe_id));
        $cours_raw   = $this->Cours_model->lister_cours(array('groupe_id' => $groupe_id));
        $enseignants = $this->Enseignant_model->lister_enseignants(array('groupe_id' => $groupe_id));

        //
        // Enlever (moi) des groupes autre que le groupe CLG > chimie.
        //

        if ( ! empty($enseignants))
        {
            if ($groupe != 1 && array_key_exists(1, $enseignants))
            {
                unset($enseignants[1]);
            }
        }

        //
        // Les demandes pour joindre le groupe
        //

        $enseignants_approbation  = $this->Groupe_model->lister_enseignants_approbation();
        $enseignants_a_approuver  = FALSE;
        $demandes                 = $enseignants_approbation;
        $anciennes_demandes       = FALSE; // les anciennes demandes refusees
        $anciennes_demandes_count = 0;

        if ( ! empty($enseignants_approbation))
        {
            foreach($enseignants_approbation as $key => $ea)
            {
                if ( ! $ea['traitement'])
                {
                    $enseignants_a_approuver = TRUE;
                    continue;
                }           

                if ($ea['acceptee'] == 1 || ($ea['demande_expiration'] > $this->now_epoch))
                {
                    unset($enseignants_approbation[$key]);
                }

                if ($ea['refusee'] == 1)
                {
                    $anciennes_demandes = TRUE;
                    $anciennes_demandes_count++;
                }
            }
        }

        $this->data = array_merge(
            $this->data,
            array(
                'ecole'              => $ecole,
                'ecole_id'           => $ecole['ecole_id'],
                'groupe'             => $groupe,
                'groupe_id'          => $groupe_id,
                'semestres'          => $semestres,
                'cours_raw'          => $cours_raw,
                'enseignants'        => $enseignants,
                'enseignants_approbation' => $enseignants_approbation,
                'enseignants_a_approuver' => $enseignants_a_approuver,
                'demandes'           => $demandes,
                'anciennes_demandes' => $anciennes_demandes,
                'anciennes_demandes_count' => $anciennes_demandes_count
            )
        );

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

        $this->_affichage('gerer_groupe');
    }

    /* ------------------------------------------------------------------------
     *
     * Inviter
     *
     * ------------------------------------------------------------------------ */
	public function inviter()
    {
        $groupe_id = $this->groupe_id;

        if ($groupe_id == 0)
        {
            redirect(base_url());
            exit;
        }

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * FONCTIONS AJAX
     *
     * ------------------------------------------------------------------------ */

    /* ------------------------------------------------------------------------
     *
     * (ajax) Groupe inscription permise
     *
     * ------------------------------------------------------------------------ */
    public function groupe_inscription_permise_toggle()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('groupe_id')))) === FALSE)
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
				case 'permission' :
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

		if ($this->Groupe_model->inscription_permise_toggle($post_data['groupe_id'], $post_data['permission']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Groupe nouveau code d'inscription
     *
     * ------------------------------------------------------------------------ */
    public function groupe_nouveau_code_inscription()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('groupe_id')))) === FALSE)
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
				case 'code' :
					$validation_rules = 'required|alpha_numeric|min_length[5]|max_length[15]';
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

		if ($this->Groupe_model->nouveau_code_inscription($post_data['groupe_id'], $post_data['code']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Groupe effacer code d'inscription
     *
     * ------------------------------------------------------------------------ */
    public function groupe_effacer_code_inscription()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('groupe_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Groupe_model->effacer_code_inscription($post_data['groupe_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Ajouter un semestre
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_semestre()
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
				case 'semestre_nom' :
					$validation_rules = 'required';
					break;
	
				case 'semestre_code' :
					$validation_rules = 'required';
					break;

				case 'semestre_debut_date' :
					$validation_rules = 'required|regex_match[/20[0-9]{2}-[0-9]{2}-[0-9]{2}/]';
					break;

				case 'semestre_fin_date' :
					$validation_rules = 'required|regex_match[/20[0-9]{2}-[0-9]{2}-[0-9]{2}/]';
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
		// Ajouter le semestre
		//

		if (($result = $this->Semestre_model->ajouter_semestre($this->groupe_id, $post_data)) !== TRUE)
        {
            // 'chronologie' : La date du debut est posterieure a la date de fin.
            // 'recoupe'     : Il y a deux semestres dont les dates se recoupent.
            // 'meme_code'   : IL y a deux semestres avec le meme code.

			echo json_encode(array('erreur' => $result));
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Editer un semestre
     *
     * ------------------------------------------------------------------------ */
    public function editer_semestre()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id')))) === FALSE)
        {
            p($this->input->post());
            echo 'ici'; die;
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
                case 'groupe_id' : 
                    $validations_rules = 'required';
                    break;

				case 'semestre_nom' :
					$validation_rules = 'required';
					break;
	
				case 'semestre_code' :
					$validation_rules = 'required';
					break;

				case 'semestre_debut_date' :
					$validation_rules = 'required|regex_match[/20[0-9]{2}-[0-9]{2}-[0-9]{2}/]';
					break;

				case 'semestre_fin_date' :
					$validation_rules = 'required|regex_match[/20[0-9]{2}-[0-9]{2}-[0-9]{2}/]';
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
        // Modifier le semestre
        //

		if (($result = $this->Semestre_model->modifier_semestre($post_data['semestre_id'], $post_data)) !== TRUE)
        {
            // 'chronologie' : La date du debut est posterieure a la date de fin.
            // 'recoupe'     : Il y a deux semestres dont les dates se recoupent.
            // autres...

			echo json_encode(array('erreur' => $result));
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Effacer un semestre
     *
     * ------------------------------------------------------------------------ */
    public function effacer_semestre()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier que la confirmation d'effacement a ete selectionnee.
        //

        if ( ! array_key_exists('confirmation_effacer_semestre', $post_data) ||
               $post_data['confirmation_effacer_semestre'] != 'on'
           )
        {
			$this->session->set_flashdata('flash_message',
				array(
					'message' => "Vous devez confirmer l'effacement en cochant la case appropriée.",
					'alert'   => 'danger'
              	)
          	);

            echo json_encode(FALSE);
            return;
        }

		if (($result = $this->Semestre_model->effacer_semestre($post_data['semestre_id'])) !== TRUE)
		{
			echo json_encode(array('erreur' => $result));
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Ajouter un cours
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_cours()
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
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'groupe_id' : 
                case 'cours_nom' :
                case 'cours_nom_court' :
                case 'cours_code' :
                case 'cours_code_court' :
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

        $groupe_id = $post_data['groupe_id'];

        unset($post_data['groupe_id']);

		if (($result = $this->Cours_model->ajouter_cours($groupe_id, $post_data)) !== TRUE)
        {
			echo json_encode(array('erreur' => $result));
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Editer un cours
     *
     * ------------------------------------------------------------------------ */
    public function editer_cours()
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
                case 'cours_id' :
                case 'groupe_id' :
                case 'cours_nom' :
                case 'cours_nom_court' :
                case 'cours_code' :
                case 'cours_code_court' :
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
        // Desuet
        //

        if ( ! array_key_exists('desuet', $post_data))
        {
            $post_data['desuet'] = 0;
        }       
        elseif ($post_data['desuet'] == 'on')
        {
            $post_data['desuet'] = 1; 
        }

		if ($this->Cours_model->editer_cours($post_data['cours_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Effacer un cours
     *
     * ------------------------------------------------------------------------ */
    public function effacer_cours()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('groupe_id', $post_data) || ! is_numeric($post_data['groupe_id']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('confirmation_effacer_cours', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        if ($post_data['confirmation_effacer_cours'] != 'on')
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Cours_model->effacer_cours($post_data['cours_id'], $post_data['groupe_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Editer un enseignant
     *
     * ------------------------------------------------------------------------ */
    public function editer_enseignant()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

		if ( ! permis('admin_editer_enseignant'))
		{
            echo json_encode(FALSE);
            return;
		}

        if (($post_data = catch_post(array('ids' => array('enseignant_id', 'groupe_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Un sysop *seulement* peut modifier les informations personnelles d'un enseignant ou etudiant.
        //
		if ($this->enseignant['niveau'] < $this->config->item('niveaux')['sysop'])
        {
            $enlever_champs = array('nom', 'prenom', 'genre', 'courriel', 'password', 'password2');

            foreach($post_data as $k => $v)
            {
                if (in_array($k, $enlever_champs))
                {
                    unset($post_data[$k]);
                }
            }

            if (empty($post_data))
                return FALSE;
		}

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'nom':
                case 'prenom':
                case 'genre':
                case 'niveau':
					$validation_rules = 'required';
					break;

                case 'courriel':
					$validation_rules = 'required|valid_email';
					break;

                case 'password':
					$validation_rules = 'alpha_numeric';
					break;

                case 'password2':
                    $validation_rules = 'matches[password]';
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

        $enseignant_id = $post_data['enseignant_id'];
        $groupe_id     = $post_data['groupe_id'];
        $password      = NULL;

        if ( ! empty($post_data['password']) && ($post_data['password'] == $post_data['password2']))
        {
            $password = $post_data['password'];
        } 

        unset($post_data['password'], $post_data['password2']);
        unset($post_data['enseignant_id'], $post_data['groupe_id']);

        //
        // Editer un enseignant
        //

        if ( ! empty($post_data))
        {
            $this->Enseignant_model->editer_enseignant($enseignant_id, $groupe_id, $post_data);
        }

        //
        // Editeur le mot-de-passe d'un enseignant
        //

        if ( ! empty($password))
        {
            $this->Auth_model->editer_password($enseignant_id, $password);
        }

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Activer ou desactiver un enseignant (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function activer_enseignant()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('enseignant_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ( ! permis('admin_editer_enseignant'))
		{
            echo json_encode(FALSE);
            return;
		}

		if ($this->Enseignant_model->activer_enseignant($post_data['enseignant_id']))
		{
            echo json_encode(TRUE);
            return;
		}

		echo json_encode(FALSE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (ajax) Changer le responsable d'un groupe
     *
     * ------------------------------------------------------------------------ */
    public function changer_responsable_groupe()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('enseignant_id', 'groupe_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Groupe_model->changer_responsable_groupe($post_data['groupe_id'], $post_data['enseignant_id']))
		{
            echo json_encode(TRUE);
            return;
		}

		echo json_encode(FALSE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (ajax) Approuver un enseignant en attente d'approbation
     *
     * ------------------------------------------------------------------------ */
    public function approuver_enseignant()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('joindre_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $this->Groupe_model->approuver_enseignant($post_data['joindre_id']);

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Desapprouver un enseignant en attente d'approbation
     *
     * ------------------------------------------------------------------------ */
    public function desapprouver_enseignant()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('joindre_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($this->Groupe_model->desapprouver_enseignant($post_data['joindre_id']) == TRUE)
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
	public function _affichage($page = NULL)
    {
        $this->load->view('commons/header', $this->data);

        switch($page)
        {
            case 'inviter' :
                $this->load->view('groupe/inviter', $this->data);
                break;

            case 'gerer_groupe' :
                $this->load->view('groupe/gestion', $this->data);
                break;

            default:
                $this->load->view('groupe/groupe', $this->data);
        }

        $this->load->view('commons/footer', $this->data);
	}
}
