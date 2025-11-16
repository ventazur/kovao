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
 * CONNEXION
 *
 * ============================================================================ */

class Connexion extends MY_Controller 
{
	public function __construct()
    {
    	parent::__construct();

        if ($this->logged_in)
        {
            redirect(base_url());
            exit;
        }

        $this->data['current_controller'] = strtolower(__CLASS__);

        $this->load->library('form_validation');
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
	{
		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Definition des messages d'erreur
        //

		$errors = array();
		$errors = array(
			'email' => NULL,
			'password' => NULL
		);

		$this->form_validation->set_rules('email', 'Courriel', 'required');
        $this->form_validation->set_rules('password', 'Mot-de-passe', 'required');

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
		// Validation du formulaire (form)
		// 

        $this->data['errors'] = $errors;

       	if ($this->form_validation->run() == FALSE)
        {
			//
			// Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.
			//

			if ($this->form_validation->error('email') !== '')
            {
                $message_alerte = "Ce courriel est invalide.";
				$this->data['errors']['email'] = 'is-invalid'; // pour bootstrap
			}

			if ($this->form_validation->error('password') !== '')
			{
                $message_alerte = "Le mot-de-passe est invalide.";
				$this->data['errors']['password'] = 'is-invalid';	// pour bootstrap
			}
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			// Verification de l'autorisation a se connecter.
			//

			$post_data = $this->input->post(NULL, TRUE);

            $result = $this->Auth_model->connexion_formulaire($post_data);

            if ($result === TRUE)
            {
                redirect(base_url());
                return;
            }

            switch($result)
            {
                case 'introuvable' :
                    $message_alerte = "Ce compte est introuvable.";
                    break;

                case 'inscription-en-cours' :
                    $message_alerte = "Consulter vos courriels pour activer votre compte.";
                    break;

                case 'mauvais-groupe' :
                    $message_alerte = "Ce compte n'appartient pas à ce groupe.";
                    break;

                case 'mauvais-mot-de-passe' :
                    $message_alerte = "Ce mot-de-passe est incorrect.";
                    break;

                case 'inactif' :
                    $message_alerte = "Ce compte n'a pas été activé.";
                    break;
            }

        }

        if ( ! empty($message_alerte))
        {
            $this->data = array_merge($this->data, array('message_alerte' => $message_alerte));
        }

		$this->load->view('commons/header', $this->data);
		$this->load->view('connexion/connexion3', $this->data);
		$this->load->view('commons/footer');
	}

    /* ------------------------------------------------------------------------
     *
     * Oublie
     *
     * ------------------------------------------------------------------------ */
	public function oublie()
    {
		$post_data = $this->input->post(NULL, TRUE);

        //
        // L'usager a oublie son mot-de-passe. 
        //

        //
        // Definition des messages d'erreur
        //

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

		$this->form_validation->set_rules('courriel', 'Courriel', 'trim|required|valid_email');

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');
        $this->form_validation->set_message('valid_email', 'Le courriel entré est invalide.');

		//
		// Validation du formulaire (form)
		// 

		$this->data['flash_message'] = array();

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
			//
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

			$result = $this->Auth_model->envoie_clef_reinitialisation2($post_data['courriel']);

			if ($result == TRUE && ! is_array($result))
			{
				// Un courriel a ete envoye avec la clef de reinitialisation.

				redirect(base_url() . $this->data['current_controller'] . '/clefenvoyee');
				return;
			}
			else
			{
				if (array_key_exists('message', $result))
				{
					$this->data['flash_message'] = $result;
				}
			}
        }

		$this->load->view('commons/header', $this->data);
		$this->load->view('connexion/connexion_oublie', $this->data);
		$this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * Clef envoyee
     *
     * ------------------------------------------------------------------------ */
	public function clefenvoyee()
    {
		$this->load->view('commons/header', $this->data);
		$this->load->view('connexion/connexion_clefenvoyee', $this->data);
		$this->load->view('commons/footer');
	}

    /* ------------------------------------------------------------------------
     *
     * Reinitialisation du mot-de-passe
     *
     * ------------------------------------------------------------------------ */
	public function reinitialisation($clef)
    {
		//
		// Verifier l'existence et la validite de la clef
		//

		$this->data['clef']          = $clef;
		$this->data['flash_message'] = array();

        $result = $this->Auth_model->verifier_clef_reinitialisation($clef);

        if ($result['status'] == FALSE)
		{
			$this->data['flash_message'] = $result;	

			$this->load->view('commons/header', $this->data);
			$this->load->view('connexion/connexion_reinitialisation_erreur', $this->data);
			$this->load->view('commons/footer');

			return;
		}

        //
        // Enseignant
        //

        if ( ! empty($result['enseignant_id']))
        {
            $type    = 'enseignant';
            $type_id = $result['enseignant_id'];
        }

        //
        // Etudiant
        //

        elseif ( ! empty($result['etudiant_id']))
        {
            $type    = 'etudiant';
            $type_id = $result['etudiant_id'];
        }

        else 
        {
            generer_erreur('ZXZ5510', "Il n'a pas été possible de déterminer le type d'usager.");
            die;
        }

        //
        // Generer les informations a inclure dans le formulaire
        //

        $this->data['status_se'] = $this->encryption->encrypt(
            serialize(
                array(
                    'type'    => $type,
                    'type_id' => $type_id,
                    'epoch'   => $this->now_epoch
                )
            )
        );

		$post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

		$this->form_validation->set_rules('password1', 'Mot de passe', 'required|min_length[8]|max_length[50]|alpha_numeric');
		$this->form_validation->set_rules('password2', 'Confirmation', 'required|matches[password1]');

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');
        $this->form_validation->set_message('min_length', 'Ce champ ne comporte pas assez de caractères.');
        $this->form_validation->set_message('max_length', 'Ce champ ne comporte pas assez de caractères.');
        $this->form_validation->set_message('alpha_numeric', 'Ce champ doit comporter seulement des lettres et/ou des chiffres.');
        $this->form_validation->set_message('matches', 'Le mot-de-passe et sa confirmation ne concordent pas.');

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
			//
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

			$post_data = $this->input->post(NULL, TRUE);

            //
            // Verifier du status et de l'autorisation a s'inscrire
            //
           
            if ( ! array_key_exists('status_se', $post_data))
            {
                redirect();
                exit;
            }

            $status = unserialize($this->encryption->decrypt($post_data['status_se']));

            if ( ! array_key_exists('epoch',   $status)  || 
                 ! array_key_exists('type',    $status)  ||
                 ! array_key_exists('type_id', $status)  ||
                 $status['epoch'] > $this->now_epoch
               )
            {
                redirect();
                exit;
            }

			// Changer le mot-de-passe
	
			if ($this->Auth_model->editer_password($status['type'], $status['type_id'], $post_data['password1']))
            {
                $this->Auth_model->effacer_clef_reinitialisation($clef);

                log_alerte(
                    array(
                        'code'  => 'PRF5566',
                        'desc'  => "Un " . $status['type'] . " a changé son mot-de-passe après l'avoir oublié.",
                        'extra' => $status['type'] . '_id = ' . $status['type_id']
                    )
                );

				$this->load->view('commons/header', $this->data);
				$this->load->view('connexion/connexion_reinitialisation_succes', $this->data);
				$this->load->view('commons/footer');
			}
			else
			{
				$this->data['flash_message'] = array(
					'status'  => FALSE,
					'message' => "Il n'a pas été possible de mettre à jour votre mot-de-passe",
					'alert'   => 'danger'
				);

				$this->load->view('commons/header', $this->data);
				$this->load->view('connexion/connexion_reinitialisation_erreur', $this->data);
				$this->load->view('commons/footer');

			}

			return;
		}

		$this->load->view('commons/header', $this->data);
		$this->load->view('connexion/connexion_reinitialisation', $this->data);
		$this->load->view('commons/footer');
	}
}
