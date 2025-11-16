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
 * GROUPES
 *
 * ============================================================================ */

class Groupes extends MY_Controller {

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
	public function index()
    {
        $ecoles  = array();

        //
        // Groupes
        //
 
        $groupes = $this->Groupe_model->lister_groupes2();

        //
        // Groupes en attente d'approbation
        //

        $demandes = $this->Groupe_model->extraire_demandes_joindre_groupe();

        //
        // Extraire les 'ecole_id' des groupes/demandes pertinentes.
        //

        if ( ! empty($groupes))
        {
            $ecole_ids = array();

            foreach($groupes as $g)
            {
                if ( ! in_array($g['ecole_id'], $ecole_ids))
                    $ecole_ids[] = $g['ecole_id'];
            }
        }

        if ( ! empty($demandes))
        {
            $ecole_ids = array();

            foreach($demandes as $d)
            {
                if ( ! in_array($d['ecole_id'], $ecole_ids))
                    $ecole_ids[] = $d['ecole_id'];
            }
        }

        //
        // Extraire les ecoles
        //

        if ( ! empty($ecole_ids))
        {
            $ecoles = $this->Ecole_model->lister_ecoles(array('ecole_ids' => $ecole_ids));
        }

        // Ajouter le groupe par default

        $groupes[0] = $this->config->item('groupe_www');
        $groupes[0]['groupe_url'] = $this->config->item('main_url');

        $this->data['ecoles']   = $ecoles;
        $this->data['groupes']  = $groupes;
        $this->data['demandes'] = $demandes;

        $this->data['permission_creer_groupe'] = $this->Groupe_model->permission_creer_groupe();

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Joindre ce groupe (le groupe en cours)
     *
     * ------------------------------------------------------------------------ */
	public function joindre()
    {
		if ($this->groupe_id == 0 || empty($this->groupe_id))
		{
			generer_erreur('GRPJ01', "Vous ne pouvez pas joindre le groupe Personnel, car il vous appartient déjà.");
			exit;
		}
		
		$groupes = $this->Groupe_model->lister_groupes2();

		if (array_key_exists($this->groupe_id, $groupes))
		{
			generer_erreur('GRPJ02', "Vous êtes déjà membre de ce groupe.");
			exit;
		}

        $demande = $this->Groupe_model->extraire_demandes_joindre_groupe();

		if ( ! empty($demande))
		{
			generer_erreur('GRPJ21', "Votre demande est en attente d'approbation");
			exit;
		}

		// Joindre le groupe

		if ($this->Groupe_model->demande_joindre_groupe() === FALSE)
		{
			generer_erreur('GRPJ05', "Il n'a pas été possible de créer votre demande pour joindre ce groupe.");
			exit;
		}

		redirect(base_url());
		exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Lister les groupes
     *
     * ------------------------------------------------------------------------ */
	public function lister()
    {
        if ($this->enseignant['privilege'] < 50)
        {
            redirect(base_url());
            exit;
        }

        $ecoles_groupes = $this->Groupe_model->lister_ecoles_groupes();

        $this->data = array_merge($this->data, array('ecoles_groupes' => $ecoles_groupes));

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Creer
     *
     * ------------------------------------------------------------------------ */
	public function creer($ecole_id = NULL)
    {
        if ( ! $this->Groupe_model->permission_creer_groupe())
        {
            redirect(base_url());
            return;
        }

        if ($ecole_id == NULL || $ecole_id == 0 || ! ctype_digit($ecole_id))
        {
            $this->data['ecoles'] = $this->Ecole_model->lister_ecoles(array('personnel' => FALSE)); 

            $this->_affichage(__FUNCTION__);
            return;
        }

        $ecole = $this->data['ecole'] = $this->Ecole_model->extraire_ecole(array('ecole_id' => $ecole_id)); 


		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback"><i class="fa fa-exclamation-circle" style="margin-right: 7px"></i>', '</div>');

        //
        // Regles
        //

        $this->form_validation->set_rules('denomination', 'Dénomination', '');

        $this->form_validation->set_rules(
            'nom-groupe', 'Nom du groupe', 'trim|required|min_length[5]|max_length[75]',
            array(
                'required'   => 'Ce champ est obligatoire',
                'min_length' => 'La nom de ce groupe est trop court.',
                'max_length' => 'La nom de ce groupe est trop long.'
            )
        );
        
        $this->form_validation->set_rules(
            'nom-court-groupe', 'Nom court du groupe', 'trim|required|min_length[5]|max_length[25]',
            array(
                'required'   => 'Ce champ est obligatoire',
                'min_length' => 'La nom de ce groupe est trop court.',
                'max_length' => 'La nom de ce groupe est trop long.'
            )
        );

        $this->form_validation->set_rules(
            'sous-domaine', 'Sous-domaine', 'trim|required|alpha|regex_match[/^' . strtolower($ecole['ecole_nom_court']) . '/]|min_length[' . (3 + strlen($ecole['ecole_nom_court'])) . ']|max_length[25]|callback__verifier_sous_domaine_existant[sous_domaine]',
            array(	
                'required'    => 'Ce champ est obligatoire',
                'alpha'       => 'Vous ne pouvez utiliser que des lettres pour le sous-domaine.',
                'min_length'  => 'Ce sous-domaine est trop court, minimum ' . (3 + strlen($ecole['ecole_nom_court'])) . ' caractères.',
                'max_length'  => 'Ce sous-domaine est trop long.',
                'regex_match' => 'Le sous-domaine doit commencer par les lettres <strong>' . strtolower($ecole['ecole_nom_court']) . '</strong>.',
                '_verifier_sous_domaine_existant' => 'Ce sous-domaine existe déjà.',
            )
        );
        
        //
        // Messages d'erreurs
        //

        $errors = array(
            'denomination'     => NULL,
            'nom-groupe'       => NULL,
            'nom-court-groupe' => NULL,
            'sous-domaine'     => NULL
		);

        //
        // Capturer les champs remplis
        //
        
        $post_data = $this->input->post(NULL, TRUE);

		//
		// Validation du formulaire (form)
        // 
        
        $form_has_errors = TRUE;

        $this->data['premiere_fois'] = FALSE;

       	if ($this->form_validation->run() == FALSE)
        {
            // Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.

            // Le status (enseignant || etudiant) est ajoute directement au formulaire
            // et sera transmis dans le $_POST. Pour ne pas que l'usager tente
            // de modifier cette information, il est encrypte et sera verifie.

            if (empty($post_data))
            {
                $this->data['premiere_fois'] = TRUE;
            }
            else
            {
                if ($this->form_validation->error('nom-groupe') !== '')
                {
                    $this->data['errors']['nom-groupe'] = 'is-invalid';
                }

                if ($this->form_validation->error('nom-court-groupe') !== '')
                {
                    $this->data['errors']['nom-court-groupe'] = 'is-invalid';
                }

                if ($this->form_validation->error('sous-domaine') !== '')
                {
                    $this->data['errors']['sous-domaine'] = 'is-invalid';
                }
            }
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            $post_data['ecole_id'] = $ecole_id;

            if ($this->Groupe_model->creer_groupe($post_data))
            {
                $this->data['nouveau_groupe'] = $post_data;

                $this->_affichage(__FUNCTION__ . '_groupe_termine');
                return;
            }
            
            generer_erreur('GRC88188', "Une erreur s'est produite lors de la création de votre groupe.");
            return;
        }

        $this->_affichage(__FUNCTION__ . '_groupe');
    }

    /* ------------------------------------------------------------------------
     *
     * CALLBACK - Verifier sous-domaine existant
     *
     * ------------------------------------------------------------------------ */
    function _verifier_sous_domaine_existant($sous_domaine)
    {
        return $this->Groupe_model->verifier_sous_domaine_existant($sous_domaine);
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
            case 'lister' :
                $this->load->view('groupes/lister', $this->data);
                break;

            case 'creer' :
                $this->load->view('groupes/creer', $this->data);
                break;

            case 'creer_groupe' :
                $this->load->view('groupes/creer_groupe', $this->data);
                break;

            case 'creer_groupe_termine' :
                $this->load->view('groupes/creer_groupe_termine', $this->data);
                break;

            default:
                $this->load->view('groupes/groupes', $this->data);
        }

        $this->load->view('commons/footer', $this->data);
	}
}
