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
 * INSCRIPTION
 *
 * ============================================================================ */

class Inscription extends MY_Controller 
{
	public function __construct()
    {
    	parent::__construct();

        if ($this->logged_in)
        {
            redirect(base_url());
            exit;
        }

        //
        // Verifier que les inscriptions sont permises
        //

        if ( ! $this->config->item('inscription_permise'))
        {
            generer_erreur('INSNP1', "Les nouvelles inscriptions ne sont pas permises pour l'instant.");
            exit;
        }

        if ($this->current_method == 'etudiant')
        {
            if ( ! $this->config->item('inscription_permise_etudiant'))
            {
                generer_erreur('INSETU', "L'inscription des étudiant(e)s n'est pas permise pour l'instant.");
                exit;
            }
        }

        if ($this->current_method == 'enseignant')
        {
            if ( ! $this->config->item('inscription_permise_enseignant'))
            {
                generer_erreur('INSENS', "L'inscription des enseignant(e)s n'est pas permise pour l'instant.");
                exit;
            }
        }

        //
        // Initilialisation
        //

        $this->load->library('form_validation');
        $this->load->model('Inscription_model');

        $this->data['current_controller'] = strtolower(__CLASS__);
        $this->data['semestre_id']        = NULL;

        //
        // Courriels autorises pour les enseignants
        //
    
        $this->courriels_autorises = array(
            '@ventbleu.com', // DEV
            '@clg.qc.ca'     // College Lionel-Groulx
        );
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        //
        // *** Il faut desormais le lien direct pour s'inscrire. ***
        //
        // base_url() . inscription/enseignant/code_unique
        // base_url() . inscription/etudiant
        //

        // redirect(base_url() . 'inscription/etudiant');

        redirect(base_url());
        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Inscription pour les ENSEIGNANTS
     *
     * ------------------------------------------------------------------------ */
    public function enseignant()
    {
        // Le status *** enseignant *** est ajoute directement au formulaire
        // et sera transmis dans le $_POST. Mais pour ne pas que l'usager tente
        // de modifier cette information, il est encrypte et sera verifie.

		$status = array(
			'epoch' => $this->now_epoch,
			'type'  => 'enseignant'
		);

		$this->data = array_merge(
			$this->data, 
			array(
				'status' => $status
			)
		);

        $this->_formulaire();
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Inscription pour les ETUDIANTS
     *
     * ------------------------------------------------------------------------ */
    public function etudiant()
    {
        // Le status *** etudiant *** est ajoute directement au formulaire
        // et sera transmis dans le $_POST. Mais pour ne pas que l'usager tente
        // de modifier cette information, il est encrypte et sera verifie.

        $status = array(
            'epoch' => $this->now_epoch,
            'type'  => 'etudiant'
        );

        $this->data = array_merge(
			$this->data, 
			array(
				'status' => $status
			)
		);

        $this->_formulaire();
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Formulaire d'inscription
     *
     * ------------------------------------------------------------------------ */
    public function _formulaire()
    {
        //
        // Capturer les champs remplis
        //
        
        $post_data = $this->input->post(NULL, TRUE);

		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Regles
        //

		$this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|min_length[1]|max_length[25]',
            array(
                'required' => 'Ce champ est obligatoire',
                'min_length' => 'La longueur de votre prénom est trop court.',
                'max_length' => 'La longueur de votre prénom est trop long.'
            )
        );

		$this->form_validation->set_rules('nom', 'Nom', 'trim|required|min_length[1]|max_length[25]',
            array(
                'min_length' => 'La longueur de votre nom est trop court.',
                'max_length' => 'La longueur de votre nom est trop long.'
            )
        );

        $this->form_validation->set_rules('genre', 'Genre', 'required');
        $this->form_validation->set_rules('password1', 'Mot de passe', 'required|min_length[8]|max_length[50]|alpha_numeric');
        $this->form_validation->set_rules('password2', 'Mot de passe', 'required|matches[password1]');

		if ($this->current_method == 'etudiant')
		{
            if ($this->groupe_id != 0)
            {
                $this->form_validation->set_rules('numero_da', ucfirst($this->ecole['numero_da_nom']) ?: 'Matricule', 'trim|required');
            }

			$this->form_validation->set_rules('courriel', 'Courriel', 
				'trim|required|valid_email|callback__verifier_deja_inscrit[courriel]|callback__verifier_courriels_jetables[courriel]',
				array(	
                    '_verifier_deja_inscrit'       => 'Cet usager est déjà inscrit.',
                    '_verifier_courriels_jetables' => 'Cette adresse courriel est interdite.'
				)
			);
		}

        if ($this->current_method == 'enseignant')
        {
			$this->form_validation->set_rules('courriel', 'Courriel', 
				'trim|required|valid_email|callback__verifier_deja_inscrit[courriel]|callback__verifier_courriel_autorise[courriel]',
				array(	
                    '_verifier_deja_inscrit'       => 'Cet usager est déjà inscrit.',
                    '_verifier_courriel_autorise'  => 'Vous devez utiliser votre courriel institutionnel (@clg.qc.ca)'
				)
			);
        }

        //
        // Messages d'erreurs
        //

		$errors = array(
            'nom'       => NULL,
            'prenom'    => NULL,
            'numero_da' => NULL,
            'genre'     => NULL,
            'courriel'  => NULL,
			'password1' => NULL,
            'password2' => NULL,
            'numero_da' => NULL,
            'code'      => NULL
		);

        $this->form_validation->set_message('required',      'Ce champ est obligatoire.');
        $this->form_validation->set_message('valid_email',   'Le courriel entré invalide.');
        $this->form_validation->set_message('is_unique',     'Ce courriel existe déjà dans notre base de données.');
        $this->form_validation->set_message('min_length',    'Le mot de passe doit contenir au minimum 8 caractères.');
        $this->form_validation->set_message('max_length',    'Le mot de passe doit contenir au maximum 30 caractères.');
        $this->form_validation->set_message('alpha_numeric', 'Le mot de passe doit contenir des lettres et des chiffres seulement.'); 
        $this->form_validation->set_message('matches',       'Les mots de passe ne concordent pas.');
        $this->form_validation->set_message('in_list',       'Le code est incorrect.');

		//
		// Validation du formulaire (form)
        // 

        $form_has_errors = FALSE;

       	if ($this->form_validation->run() == FALSE)
        {
            // Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.

            if ( ! empty($post_data))
            {
                $form_has_errors = TRUE;

                foreach($errors as $k => $v) 
                {
                    if ($this->form_validation->error($k) !== '')
                    {
                        $errors[$k] = 'is-invalid';
                    }
                }
            }
            
            $this->data['errors'] = $errors;

            // Le status (enseignant || etudiant) est ajoute directement au formulaire
            // et sera transmis dans le $_POST. Pour ne pas que l'usager tente
            // de modifier cette information, il est encrypte et sera verifie.

			$status    = $this->data['status'];
			$status_s  = serialize($status);
			$status_se = $this->encryption->encrypt($status_s);

			$this->data = array_merge(
				$this->data, 
				array(
					'status_se' => $status_se
				)
            );
        }

        if ( ! $form_has_errors & ! empty($post_data)) 
        {
			//
			// Le formulaire a ete rempli correctement.
			//

			//
			// Validation du reCAPTCHA pour les etudiants
			//

			$post_data['recaptcha_score'] = NULL;

            if ( ! isset($post_data['recaptcha_response']) || empty($post_data['recaptcha_response']))
            {
                generer_erreur('IS71CAP', "reCAPTCHA : Il n'a pas été possible de vérifier que vous êtes un humain.");
                return;
            }

            $recaptcha_url      = $this->config->item('google_recaptcha')['api_uri'];
            $recaptcha_secret   = $this->config->item('google_recaptcha')['priv_key'];
            $recaptcha_response = $post_data['recaptcha_response'];

            //
            // Demander le score reCAPTCHA a Google.
            //

            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);

            //
            // Verifier que l'action reCAPTCHA correspond (important de verifier selon google).
            //

            if ($recaptcha->action != 'inscription')
            {
                generer_erreur2(
                    array(
                        'code' => 'IS72CAP', 
                        'desc' => "reCAPTCHA : L'action ne correspond pas à celle demandée."
                    )
                );
                return;
            }

            //
            // Prendre action selon le score reCAPTCHA.
            //

            if ($recaptcha->score < 0.70) 
            {
                generer_erreur2(
                    array(
                        'code' => 'IS73CAP', 
                        'desc' => "reCAPTCHA : Nous pensons que vous n'êtes pas humain."
                    )
                );
                return;
            }

            //
            // Enregistrer le score reCAPTCHA.
            //

            $post_data['recaptcha_score'] = $recaptcha->score;

            //
            // Verification du status et de l'autorisation a s'inscrire
            //
           
            if ( ! array_key_exists('status_se', $post_data))
            {
                redirect();
                exit;
            }

            $status = unserialize($this->encryption->decrypt($post_data['status_se']));

            if ( ! array_key_exists('epoch', $status)  || 
                 ! array_key_exists('type', $status)   ||
                 $status['epoch'] > $this->now_epoch
               )
            {
                redirect();
                exit;
            }

            //
            // Ajouter un usager (etudiant OU enseignant)
            //

            $clef = $this->Auth_model->ajouter_inscription($status['type'], $post_data, $status);

            if ($clef === FALSE)
            {
                generer_erreur2(
                    array(
                        'code'  => 'IS8912', 
                        'desc'  => "Il n'a pas été possible d'ajouter un nouvel usager.",
                        'extra' => 'courriel: ' . $post_data['courriel'],
                        'importance' => 3
                    )
                );
                return;
            }

            //
            // Envoyer un courriel de confirmation
            //

            $courriel = $post_data['courriel'];

            if ($this->Auth_model->envoie_clef_autorisation($courriel, $clef) != TRUE)
            {
                generer_erreur2(
                    array(
                        'code' => 'IS7101', 
                        'desc' => "Il n'a pas été possible de vous envoyer une clef pour confirmer votre courriel."
                    )
                );
                exit;
            }

            //
            // Afficher une page avec les instructions suivantes
            //

            redirect(base_url() . 'inscription/confirmation');
            exit;
        }

        $this->load->view('commons/header', $this->data);

        switch($this->current_method)
        {
            case 'etudiant' :
                $this->load->view('inscription/inscription_etudiant', $this->data);
                break;

            case 'enseignant' :
                $this->load->view('inscription/inscription_enseignant', $this->data);
                break;

            default :
                redirect();
                exit;
        }

		$this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * Confirmation d'ajout
     *
     * ------------------------------------------------------------------------ */
    public function confirmation($clef = NULL)
    {
        //
        // Si aucun clef n'est presente, c'est simplement pour presenter les instructions
        // au nouvel usager pour la procedure pour la confirmation de son courriel.
        //

		if ($clef == NULL)
        {
			$this->load->view('commons/header', $this->data);
			$this->load->view('/inscription/confirmation');
			$this->load->view('commons/footer');

			return;
		}

		$result = $this->Auth_model->verifier_clef_confirmer_courriel($clef);

		if (is_array($result) && $result['status'] == FALSE)
		{
			$this->load->view('commons/header', $this->data);
			$this->load->view('/inscription/confirmation_erreur', $result);
			$this->load->view('commons/footer');

			return;
		}

        $this->load->view('commons/header', $this->data);

        if (array_key_exists('enseignant', $result) && $result['enseignant'])
        {
            $this->load->view('/inscription/confirmation_clef_valide_enseignant', $result);
        }
        elseif (array_key_exists('etudiant', $result) && $result['etudiant'])
        {
            $this->load->view('/inscription/confirmation_clef_valide_etudiant', $result);
        }
        else
        {
            $this->load->view('/inscription/confirmation_clef_valide', $result);
        }

		$this->load->view('commons/footer');
    }

    /* --- CALLBACK FUNCTIONS ------------------------------------------------- */

    /* ------------------------------------------------------------------------
     *
     * Verifier si ce courriel est deja inscrit
     *
     * ------------------------------------------------------------------------ */
    function _verifier_deja_inscrit($courriel)
    {
        return $this->Inscription_model->verifier_deja_inscrit($courriel);
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier si ce couriel est autorise
     *
     * ------------------------------------------------------------------------ */
    function _verifier_courriel_autorise($courriel)
    {
        /* J'ai desactive les courriels autorises pour que les
		 * enseignants des autres cegeps puissent s'inscrire. -2023/08/25

        foreach($this->courriels_autorises as $c)
        {
            if (preg_match('/' . $c . '$/', $courriel))
            {
                return TRUE;
            }
        }

        return FALSE;
         */

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier si ce courriel est un courriel jetable
     *
     * ------------------------------------------------------------------------ */
    function _verifier_courriels_jetables($courriel)
    {
        return $this->Inscription_model->verifier_courriels_jetables($courriel);
    }

}
