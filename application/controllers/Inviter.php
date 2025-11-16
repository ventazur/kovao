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
 * INVITER
 *
 * ============================================================================ */

class Inviter extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        //
        // Permissions
        //

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        //
        // Verifier que les inscriptions sont permises
        //

        if ( ! $this->config->item('inscription_permise') || ! $this->config->item('inscription_permise_enseignant'))
        {
            generer_erreur('INSNP2', "Les nouvelles inscriptions ne sont pas permises pour l'instant.");
            exit;
        }

        //
        // Initialisation
        //

        $this->load->library('form_validation');
        $this->load->model('Inscription_model');

        $this->data['current_controller'] = strtolower(__CLASS__);
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
        // Regles
        //

        $this->form_validation->set_rules(
            'courriel', 'Courriel', 
            'required|valid_email|callback__verifier_deja_inscrit[courriel]|callback__verifier_deja_invite[courriel]',
            array(
                'required'               => 'Ce champ est obligatoire',
                'valid_email'            => 'Ce courriel est invalide.',
                '_verifier_deja_inscrit' => 'Cet enseignant est déjà inscrit.',
                '_verifier_deja_invite'  => 'Cet enseignant a déjà été invité.'
            )
        );

        //
        // Messages d'erreurs
        //

		$errors = array(
            'courriel' => NULL
        );

		//
		// Validation du formulaire (form)
        // 
        
        $form_has_errors = TRUE;

       	if ($this->form_validation->run() !== FALSE)
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            $post_data = $this->input->post(NULL, TRUE);
            $post_data['enseignant_id'] = $this->enseignant_id;

            //
            // Ajouter l'invitation
            //

            if ($this->Inscription_model->inviter_enseignant($post_data) === TRUE)
            {
                //
                // Envoyer un courriel d'invitation
                //

				$clef    = $this->Inscription_model->extraire_clef_invitation($post_data['courriel']); 
				$hote    = $this->Inscription_model->extraire_enseignant_hote($post_data['enseignant_id']);

				$contenu_data = array(
					'clef' => $clef,
					'hote' => $hote
				);

                if (
                    $this->Courriel_model->envoyer_courriel(
                        array(
                            'destination_courriel' => $post_data['courriel'], 
                            'sujet'                => 'Invitation à joindre KOVAO',
                            'contenu'              => 'inviter/invitation_email',
                            'contenu_data'         => $contenu_data,
                            'raison'               => 'invitation_enseignant'
                        )
                    )
                )
				{
                	$this->_affichage('invitation-envoyee');
				}
				else
				{
                    generer_erreur('IV313', "Il n'a pas été possible d'envoyer le courriel d'invitation.");
				}

                return;
            }
            else
            {
                generer_erreur('IV312', "Il n'a pas été possible de créer l'invitation.");
                return;
            }
        }

        if ($form_has_errors)
        {
            foreach($errors as $k => $v) 
            {
                if ($this->form_validation->error($k) !== '')
                {
                    $errors[$k] = 'is-invalid';
                }
            }

            $this->data['errors'] = $errors;
        }

        $this->_affichage();
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
     * Verifier si ce courriel est deja invite
     *
     * ------------------------------------------------------------------------ */
    function _verifier_deja_invite($courriel)
    {
        return $this->Inscription_model->verifier_deja_invite($courriel);
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
            case 'invitation-envoyee' :
                $this->load->view('inviter/invitation_envoyee', $this->data);
                break;

            default :
                $this->load->view('inviter/inviter', $this->data);
        }

        $this->load->view('commons/footer', $this->data);
	}
}
