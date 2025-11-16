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
 * FORUMS
 *
 * ============================================================================ */

class Forums extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        //
        // Les forums sont reserves aux groupes
        //

        if ( ! $this->config->item('forums') || ! $this->est_enseignant || ! $this->groupe_id)
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
        //
        // Extraire les messages
        //

        $messages = $this->Forums_model->extraire_messages(); 

        $message_ids = array();

        if ( ! empty($messages))
        {
            $message_ids = array_keys($messages);
        }

        //
        // Extraire les messages lus
        //

        $this->data['message_ids_lus'] = $this->Forums_model->message_ids_lus();

        //
        // Extraire les messages suivis
        // 
        // Les messages suivis sont les messages dont l'auteur est l'enseignant,
        // ou encore dont l'enseignant a commente.
        // 

        $messages_suivis    = $this->Forums_model->messages_suivis();
        $message_ids_suivis = array_column($messages_suivis, 'message_id'); 

        //
        // Le tableau des message_ids avec de nouveaux commentaires (nc)
        //

        $message_ids_nc = array();

        //
        // Extraire les commentaires
        //

        $commentaires = $this->Forums_model->extraire_commentaires($message_ids);

        if ( ! empty($commentaires))
        {
            foreach($commentaires as $message_id => $c)
            {
                if (empty($c)) 
                    continue;

                if ( ! in_array($message_id, $message_ids_suivis))
                    continue;

                foreach($c as $cm)
                {
                    // Ne pas presenter les commentaires de l'auteur comme de nouveaux commantaires
                    if ($cm['enseignant_id'] == $this->enseignant_id)
                        continue;

                    if ($cm['ajout_epoch'] < $messages_suivis[$message_id]['derniere_lecture_epoch'])
                        continue;

                    $message_ids_nc[] = $message_id;
                }
            } // foreach
        }

        //
        // Classer les messages selon les derners ajouts, ou selon les nouveaux commentaires
        //

        $messages_ordre = array();

        foreach($messages as $message_id => $m)
        {
            $messages_ordre[$message_id] = $m['ajout_epoch'];

            if ( ! array_key_exists($message_id, $commentaires))
                continue;

            foreach($commentaires[$message_id] as $c)
            {
                if ($c['ajout_epoch'] > $messages_ordre[$message_id])
                {
                    $messages_ordre[$message_id] = $c['ajout_epoch'];

                    // On peut briser le foreach car le premier commentaire est le plus recent, 
                    // qui a deja ete extrait de cet ordre par la base de donnees.
                    break;
                }
            }
        }

        arsort($messages_ordre);

        $this->data = array_merge(
            $this->data,
            array(
                'messages'           => $messages,
                'messages_ordre'     => array_keys($messages_ordre),
                'commentaires'       => $commentaires,
                'commentaires_epoch' => $messages_ordre,
                'message_ids_nc'     => $message_ids_nc
            )
        );

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Lire un message
     *
     * ------------------------------------------------------------------------ */
    public function lire($message_id)
    {
        //
        // Extraire le message
        //

        $message = $this->Forums_model->extraire_message($message_id);

        if (empty($message))
        {
            generer_erreur('FLI771', "Ce message des forums n'a pu être trouvée.", array('importance' => 1));
            return;
        }

        //
        // Marque le message comme lu
        //

        $this->Forums_model->marquer_message_lu($message_id);

        //
        // Mettre a jou la derniere lecture, si c'est un message suivi
        // 
        // TRUE : message suivi
        // FALSE : message non suivi
        //
        
        $this->data['message_suivi'] = $this->Forums_model->lecture_message_suivi($message_id); 

        //
        // Formulaire de publication d'un commentaire
        //

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

		$this->form_validation->set_rules('commentaire', 'Commentaire', 'required|min_length[3]',
            array(
                'required'   => 'Ce champ est obligatoire.',
                'min_length' => 'La longueur de votre commentaire est trop court.'
            )
        );

		$errors = array('commentaire' => NULL);

        //
        // Capturer les champs remplis
        //
        
        $post_data = $this->input->post();

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

            if ($form_has_errors)
            {
                foreach($errors as $k => $v) 
                {
                    if ($this->form_validation->error($k) !== '')
                    {
                        $errors[$k] = 'is-invalid';
                    }
                }

                $this->data = array_merge(
                    $this->data,
                    array('errors' => $errors)
                );
            }
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            $this->Forums_model->publier_commentaire($message_id, $message, $post_data);

            // Afin d'eviter de republier le meme commentaire deux fois de suite.
            redirect(current_url());
            return;
        }
        
        $commentaires = $this->Forums_model->extraire_commentaires($message_id);

        $this->data = array_merge(
            $this->data,
            array(
                'message' => $message,
                'commentaires' => $commentaires
            )
        );

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Publier un message
     *
     * ------------------------------------------------------------------------ */
    public function publier()
    {
		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Regles
        //

		$this->form_validation->set_rules('titre', 'Titre', 'required|min_length[3]|max_length[250]',
            array(
                'required' => 'Ce champ est obligatoire.',
                'min_length' => 'La longueur de votre titre est trop court.',
                'max_length' => 'La longueur de votre titre est trop long.'
            )
        );

		$this->form_validation->set_rules('message', 'Message', 'required|min_length[3]',
            array(
                'required'   => 'Ce champ est obligatoire.',
                'min_length' => 'La longueur de votre message est trop court.'
            )
        );

        //
        // Messages d'erreurs
        //

		$errors = array(
            'titre'     => NULL,
            'message'   => NULL
		);

        //
        // Capturer les champs remplis
        //
        
        $post_data = $this->input->post();

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

            if ($form_has_errors)
            {
                foreach($errors as $k => $v) 
                {
                    if ($this->form_validation->error($k) !== '')
                    {
                        $errors[$k] = 'is-invalid';
                    }
                }

                $this->data = array_merge(
                    $this->data,
                    array('errors' => $errors)
                );
            }
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            if ( ! array_key_exists('permettre_commentaires', $post_data))
            {
                $post_data['permettre_commentaires'] = 0;
            }
            elseif ($post_data['permettre_commentaires'] == 'on')
            {
                $post_data['permettre_commentaires'] = 1;
            }
            else
            {
                $post_data['permettre_commentaires'] = 0;
            }

            if ($this->Forums_model->publier_message($post_data) == TRUE)
            {
                redirect(base_url() . __CLASS__);
                exit;
            }
        }
        
        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier un message
     *
     * ------------------------------------------------------------------------ */
    public function modifier($message_id)
    {
        //
        // Extraire le message
        //

        $message = $this->Forums_model->extraire_message($message_id);

        if (empty($message))
        {
            generer_erreur('FLI772', "Cette publication des forums n'a pu être trouvée.", array('importance' => 1));
            return;
        }

        $this->data['message'] = $message;

		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Regles
        //

		$this->form_validation->set_rules('titre', 'Titre', 'required|min_length[3]|max_length[250]',
            array(
                'required' => 'Ce champ est obligatoire.',
                'min_length' => 'La longueur de votre titre est trop court.',
                'max_length' => 'La longueur de votre titre est trop long.'
            )
        );

		$this->form_validation->set_rules('message', 'Message', 'required|min_length[3]',
            array(
                'required'   => 'Ce champ est obligatoire.',
                'min_length' => 'La longueur de votre message est trop court.'
            )
        );

        //
        // Messages d'erreurs
        //

		$errors = array(
            'titre'     => NULL,
            'message'   => NULL
		);

        //
        // Capturer les champs remplis
        //
        
        $post_data = $this->input->post();

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

            if ($form_has_errors)
            {
                foreach($errors as $k => $v) 
                {
                    if ($this->form_validation->error($k) !== '')
                    {
                        $errors[$k] = 'is-invalid';
                    }
                }

                $this->data = array_merge(
                    $this->data,
                    array('errors' => $errors)
                );
            }
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            if ( ! array_key_exists('permettre_commentaires', $post_data))
            {
                $post_data['permettre_commentaires'] = 0;
            }
            elseif ($post_data['permettre_commentaires'] == 'on')
            {
                $post_data['permettre_commentaires'] = 1;
            }
            else
            {
                $post_data['permettre_commentaires'] = 0;
            }

            if ($this->Forums_model->modifier_message($message_id, $post_data) == TRUE)
            {
                redirect(base_url() . strtolower(__CLASS__) . '/lire/' . $message_id);
                exit;
            }
        }
        
        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un message ou un commentaire
     *
     * ------------------------------------------------------------------------ */
    public function effacer()
    {
        $args = $this->uri->uri_to_assoc(3);

        $operations_valides = array('message', 'commentaire');

        //
        // Effacement d'un message
        //

        if (array_key_exists('message', $args) && ctype_digit($args['message']))
        {
            if ($this->Forums_model->effacer_message($args['message']))
            {
                redirect(base_url() . 'forums');
                exit;
            }
        }

        //
        // Effacement d'un commentaire
        //

        elseif (array_key_exists('commentaire', $args) && ctype_digit($args['commentaire']))
        {
            if (($message_id = $this->Forums_model->effacer_commentaire($args['commentaire'])) !== FALSE)
            {
                redirect(base_url() . 'forums/lire/' . $message_id);
                exit;    
            }
        }

        redirect(base_url() . 'forums');
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Suivre un message
     *
     * ------------------------------------------------------------------------ */
    public function suivre_message()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('message_id')))) === FALSE)
		{
            echo json_encode(FALSE);
            return;
        }

        $this->Forums_model->suivre_message($post_data['message_id']);

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Ne plus suivre un message
     *
     * ------------------------------------------------------------------------ */
    public function arret_suivre_message()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('message_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }


        $this->Forums_model->arret_suivre_message($post_data['message_id']);

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * _Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

        switch($page)
        {   
            case 'publier':
                $this->load->view(strtolower(__CLASS__) . '/publier', $this->data);
                break;

            case 'lire':
                $this->load->view(strtolower(__CLASS__) . '/lire', $this->data);
                break;
        
            case 'modifier':
                $this->load->view(strtolower(__CLASS__) . '/modifier', $this->data);
                break;

            default:
                $this->load->view(strtolower(__CLASS__) . '/forums', $this->data);
        }

        $this->load->view('commons/footer', $this->data);
    }
}
