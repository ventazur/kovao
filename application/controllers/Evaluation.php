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
 * EVALUATION
 *
 * ============================================================================ */

use jlawrence\eos\Parser;
// use chillerlan\QRCode\QRCode; // Les codes QR ne sont pas presentement utilises.

class Evaluation extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        //
        // Parametres
        //

        $this->traces = FALSE;

		if ($this->logged_in)
		{
        	$this->traces = TRUE;
		}
    }

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
    public function _remap($method = NULL, $args = array())
    {
		$valid_methods = array(
		//	 12345678
            'index',
            'endirect',
            'previsualisation',
            'soumission',
            'terminee',
        );

        // Ces methodes doivent avoir plus de 12 lettres, dans le cas ou il serait necessaire 
        // d'augmenter la longueur des references (actuellement les references ont 6 lettres).

        $valid_ajax_methods = array(
        //   123456789012
            'ping_etudiant',
            'enregistrer_nom_traces',
            'enregistrer_numero_da_traces',
	        'enregistrer_reponse_checkbox_traces',
	        'enregistrer_reponse_radio_traces',
            'enregistrer_reponse_text_traces',
            'enregistrer_reponse_textarea_traces',
            'enregistrer_reponse_numerique_traces',
			'enregistrer_lab_input_traces',
            'enregistrer_lab_select_identificaction_traces',
            'confirmer_lab_partenaires',
            'verifier_numero_da',
            'document_selection',
            'document_annulation',
            'televersement',
			'effacer_document_soumission',
            'rotation_image'
        );

        if (in_array($method, $valid_methods))
        {
            $this->$method($args);
            return;
        }

        if (in_array($method, $valid_ajax_methods))
        {
            if ( ! $this->input->is_ajax_request()) 
            {
                exit('No direct script access allowed');
            }

            $this->$method($args);
            return;
        }

        //
        // Verifier s'il s'agit d'une reference a une evaluation.
        //

        if (strlen($method) == 6)
        {
            $this->ref($method);
            return;
        }

        redirect(base_url());
        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * L'index est charge lorsque non connecte.
     *
     * ------------------------------------------------------------------------ */
    public function index($args = NULL)
    {
        $this->_affichage('evaluation-trouver');
	}

    /* ------------------------------------------------------------------------
     *
     * Evaluation terminee par votre enseignant
     *
     * ------------------------------------------------------------------------ */
    function terminee($args = array())
    {
        if (in_array('lab_partenaire', $args))
        {
            $this->_affichage('evaluation-terminee-lab-partenaire');
            return;
        }

        if (in_array('non_inscrit', $args))
        {
            $this->_affichage('evaluation-terminee-enseignant-non-inscrit');
            return;
		}

        if (in_array('abruptement', $args))
        {
            $this->_affichage('evaluation-terminee-enseignant-abruptement');
            return;
        }

        $this->_affichage('evaluation-terminee-enseignant');
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Document selectionne
     *
     * ------------------------------------------------------------------------ */
	public function document_selection()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        $champs_obligatoires = array('evaluation_id', 'evaluation_reference', 'question_id', 'fichiers');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $post_data) || empty($post_data[$c]))
            {
                return;
            }
        }

        if ( ! is_array($post_data['fichiers']))
        {
            return;
        }

        //
        // Log
        //

        $fichiers = $post_data['fichiers'];

        if (count($fichiers) > 1)
        {
            $action = count($fichiers) . " documents " . str_replace('"', '', json_encode($fichiers)) . " ont été sélectionnés pour la question id = " . $post_data['question_id'] . ".";
        }
        else
        {
            $action = "Un document [" . $fichiers[0] . "] a été sélectionné pour la question id = " . $post_data['question_id'] . ".";
        }

		$fichiers_arr = array();

		foreach($fichiers as $f)
		{
			$fichiers_arr[$f] = array(
				'question_id' => $post_data['question_id']
			);
		}

        $this->Evaluation_model->ecrire_activite_evaluation(
            array(
                'action'                => $action,
                'action_court'          => 'document_selectionne',
                'action_data'           => json_encode($fichiers_arr),
                'evaluation_id'         => $post_data['evaluation_id'],
                'evaluation_reference'  => $post_data['evaluation_reference'],
                'question_id'           => $post_data['question_id']
            )
        );
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Document annulation
     *
     * ------------------------------------------------------------------------ */
	public function document_annulation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        $champs_obligatoires = array('evaluation_id', 'evaluation_reference', 'question_id', 'random_str');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $post_data) || empty($post_data[$c]))
            {
                return;
            }
        }

        //
        // Log
        //

        $this->Evaluation_model->ecrire_activite_evaluation(
            array(
                'action'                => "Le document sélectionné [" . $post_data['random_str'] . "] a été annulé avant son téléversement.",
                'action_court'          => 'document_annule',
                'action_data'           => json_encode(array($post_data['random_str'])),
                'evaluation_id'         => $post_data['evaluation_id'],
                'evaluation_reference'  => $post_data['evaluation_reference'],
                'question_id'           => $post_data['question_id']
            )
        );
    }

    /* -------------------------------------------------------------------------------------------- 
     *
     * (AJAX) Televersement
	 *
     * -------------------------------------------------------------------------------------------- */
    function televersement()
    {
        if ( ! isset($_FILES['upload_file']))
        {
            echo json_encode('ERREUR : Aucun fichier téléversé.');
            return;
        } 

        $upload_file = $_FILES['upload_file'];

        $post_data = catch_post();

        foreach($post_data as $k => $v)
        {
            switch($k)
            {
                case 'evaluation_id' :
                case 'question_id':
                    $validation_rules = 'required|numeric';
                    break;

                case 'evaluation_reference' :
                    $validation_ruels = 'required|alpha';
                    break;
            }

            if (isset($validation_rules))
            {
                $this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        } 

        $errors = array();

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            foreach($post_data as $k => $v)
            {	
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return;
		}

		/*
		 * Documentation
		 *

        /* _FILES['upload_file']:
            [name] => 011.JPG
            [type] => image/jpeg
            [tmp_name] => /tmp/phpY55ZvQ
            [error] => 0
            [size] => 56288
        */

        /*
            [file_name]     => mypic.jpg
            [file_type]     => image/jpeg
            [file_path]     => /path/to/your/upload/
            [full_path]     => /path/to/your/upload/jpg.jpg
            [raw_name]      => mypic
            [orig_name]     => mypic.jpg
            [client_name]   => mypic.jpg
            [file_ext]      => .jpg
            [file_size]     => 22.2
            [is_image]      => 1
            [image_width]   => 800
            [image_height]  => 600
            [image_type]    => jpeg
            [image_size_str] => width="800" height="200"
        */

        //
        // Verifier si l'inscription est requise et que l'etudiant est toujours connecte
        // Ceci pour regler un probleme ou un etudiant se serait deconnecte sur une autre fenetre.
        //

        if ($post_data['inscription_requise'] && ! $this->logged_in)
        {
            echo json_encode(9);
            return;
        }
        
        //
        // Conserver le random string du fichier pour le suivre pendant le processus de televersement.
        //
        
        $random_str = $post_data['random_str'];

        //
        // Log
        //

        $this->Evaluation_model->ecrire_activite_evaluation(
            array(
                'action'                => "Le document [" . $random_str . "] a été téléversé.",
                'action_court'          => 'document_televerse',
                'action_data'           => json_encode(array($random_str)),
                'evaluation_id'         => $post_data['evaluation_id'],
                'evaluation_reference'  => $post_data['evaluation_reference'],
                'question_id'           => $post_data['question_id']
            )
        );
    
        //
        // Verifier si le format est accepte.
        //

        if ( ! in_array(strtolower($upload_file['type']), $this->config->item('documents_mime_types')))
        {

            $this->Evaluation_model->ecrire_activite_evaluation(
                array(
                    'action'                => "Le document [" . $random_str . "] a été refusé pour cause de mauvais format.",
                    'action_court'          => 'document_televerse_refuse_format',
                    'action_data'           => json_encode(array($random_str)),
                    'evaluation_id'         => $post_data['evaluation_id'],
                    'evaluation_reference'  => $post_data['evaluation_reference'],
                    'question_id'           => $post_data['question_id']
                )
            );

            echo json_encode(
                array(
                    'code' => 'DOC57123',
                    'desc' => "Le fichier " . $upload_file['name'] . "n'est pas d'un format accepté."
                )
            );
            return;
        }

        //
        // Parametres du fichier
        //

        $ran_str = strtolower(random_string('alpha', 6));

        if (in_array($upload_file['type'], $this->config->item('documents_mime_types')))
        {
            $extension = $this->config->item($upload_file['type'], 'documents_mime_types_properties')['extension'];
        }
        else
        {
            $extension = substr(strstr($upload_file['type'], '/', FALSE), 1);
        }

        $file_name_pre = ($this->is_DEV ? 'dev_' : '') . 'e' . $this->ecole_id . 'g' . $this->groupe_id . 's_' . $this->now_epoch . '_' . $ran_str;
        $file_name     = $file_name_pre . '.' . $extension;

        //
        // Relocaliser le fichier a sa destination finale
        //

        if ( ! move_uploaded_file($upload_file['tmp_name'], FCPATH . $this->config->item('documents_path_s') . $file_name))
        {
            echo json_encode('Le fichier n\'a pu être relocalisé à sa destination finale.');
            return;
        }

        $filedata = array(
            'extension'     => $extension,
            'file_name'     => $file_name,
            'file_name_pre' => $file_name_pre,
            'path'          => FCPATH . $this->config->item('documents_path_s'),
            'full_path'     => FCPATH . $this->config->item('documents_path_s') . $file_name,
            'mime_type'     => $upload_file['type'],
            'is_image'      => 0
        );

		$filedata['file_size'] = filesize($filedata['full_path']);

        if (strstr($upload_file['type'], '/', TRUE) == 'image')
        {
            $filedata['is_image']     = 1;
            $filedata['image_width']  = getimagesize($filedata['full_path'])[0];
            $filedata['image_height'] = getimagesize($filedata['full_path'])[1];
        }

        // Ajouter le document a la soumission

        if (($result = $this->Document_model->ajouter_document_soumission($filedata, $post_data)) === FALSE)
        {
            // Log
            $this->Evaluation_model->ecrire_activite_evaluation(
                array(
                    'action'                => "Le document téléversé [" . $random_str . "] a été refusé et effacé.",
                    'action_court'          => 'document_televerse_refuse_efface',
                    'action_data'           => json_encode(array($random_str)),
                    'evaluation_id'         => $post_data['evaluation_id'],
                    'evaluation_reference'  => $post_data['evaluation_reference'],
                    'question_id'           => $post_data['question_id']
                )
            );

            // Effacer le document
            unlink($filedata['full_path']);

            // Effacer le thumbnail du document
            unlink($filedata['path'] . $filedata['file_name_pre'] . '_tn.' . $filedata['extension']);

            echo json_encode($result);
            return;
        }

        // Log
        $this->Evaluation_model->ecrire_activite_evaluation(
            array(
                'action'                => "Le document téléversé [" . $random_str . "] a été accepté et enregistré [doc id = " . $result['doc_id'] . "].",
                'action_court'          => 'document_televerse_enregistre',
                'action_data'           => json_encode(array($random_str => array('doc_id' => $result['doc_id']))),
                'evaluation_id'         => $post_data['evaluation_id'],
                'evaluation_reference'  => $post_data['evaluation_reference'],
                'question_id'           => $post_data['question_id']
            )
        );

        echo json_encode($result);
        return;

		/*
		 * Documentation
		 *

        Extend your custom server-side upload handler to return a JSON response akin to the following output

        {"files": [
          {
            "name": "picture1.jpg",
            "size": 902604,
            "url": "http:\/\/example.org\/files\/picture1.jpg",
            "thumbnailUrl": "http:\/\/example.org\/files\/thumbnail\/picture1.jpg",
            "deleteUrl": "http:\/\/example.org\/files\/picture1.jpg",
            "deleteType": "DELETE"
          },
          {
            "name": "picture2.jpg",
            "size": 841946,
            "url": "http:\/\/example.org\/files\/picture2.jpg",
            "thumbnailUrl": "http:\/\/example.org\/files\/thumbnail\/picture2.jpg",
            "deleteUrl": "http:\/\/example.org\/files\/picture2.jpg",
            "deleteType": "DELETE"
          }
        ]}

        To return errors to the UI, just add an error property to the individual file objects:

        {"files": [
          {
            "name": "picture1.jpg",
            "size": 902604,
            "error": "Filetype not allowed"
          },
          {
            "name": "picture2.jpg",
            "size": 841946,
            "error": "Filetype not allowed"
          }
        ]}

        When removing files using the delete button, the response should be like this:

        {"files": [
          {
            "picture1.jpg": true
          },
          {
            "picture2.jpg": true
          }
        ]}

        */
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un document
     *
     * ------------------------------------------------------------------------ */
	public function effacer_document_soumission()
    {
        $post_data = $this->input->post();

		$ids_obligatoires = array('doc_id', 'question_id', 'evaluation_id');

		foreach($ids_obligatoires as $id)
		{
			if ( ! array_key_exists($id, $post_data) || ! is_numeric($post_data[$id]))
			{
				echo json_encode(FALSE);
				return;
			}
		}

		$result = $this->Document_model->effacer_document_soumission(
            $post_data['doc_id'], 
            $post_data['question_id'], 
            $post_data['evaluation_id'], 
            $post_data['evaluation_reference'], 
            $post_data['etudiant_session_id']
		);

        // Log
        $this->Evaluation_model->ecrire_activite_evaluation(
            array(
                'action'                => "Le document [id = " . $post_data['doc_id'] . "] a été effacé par l'étudiant.",
                'action_court'          => 'document_efface',
                'action_data'           => json_encode(array('doc_id' => $post_data['doc_id'])),
                'evaluation_id'         => $post_data['evaluation_id'],
                'evaluation_reference'  => $post_data['evaluation_reference'],
                'question_id'           => $post_data['question_id']
            )
        );

		echo json_encode($result);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Rotation d'une image
     *
     * ------------------------------------------------------------------------ */
	public function rotation_image()
    {
        $post_data = $this->input->post();

		$ids_obligatoires = array('doc_id', 'question_id', 'evaluation_id');

		foreach($ids_obligatoires as $id)
		{
			if ( ! array_key_exists($id, $post_data) || ! is_numeric($post_data[$id]))
			{
				echo json_encode(FALSE);
				return;
			}
		}

		$result = $this->Document_model->rotation_image_soumission(
			$post_data['rotation'], $post_data['doc_id'], $post_data['question_id'], $post_data['evaluation_id'], $post_data['evaluation_reference'], $post_data['etudiant_session_id']
		);

		echo json_encode($result);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Ping de l'etudiant en redaction de son evaluation
     *
     * ------------------------------------------------------------------------ */
	public function ping_etudiant()
    {
        //
        // Verifier la configuration concernant le ping des etudiants en redaction
        //

        if ( ! $this->config->item('ping_etudiant_evaluation'))
        {
            echo json_encode(FALSE);
            return;
        }

        $post_data = $this->input->post();

        if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
        {
            echo json_encode(FALSE);
            return; 
        }

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']))
        {
            echo json_encode(FALSE);
            return; 
        }

		//
		// Extraire les informations pour le ping
		//

        $ping = $this->Evaluation_model->ping_etudiant(
            array(
                'evaluation_reference' => $post_data['evaluation_reference'],
                'evaluation_id'        => $post_data['evaluation_id'],
                'notification'         => $post_data['notification']
            )
        );

		//
		// Extraire les informations sur l'evaluation
		//

        $evaluation = $this->Evaluation_model->extraire_evaluation_par_reference_ping($post_data['evaluation_reference']);

		if ( ! $evaluation)
		{
			$evaluation = array('terminee_abruptement' => TRUE);
        }

		$r = array_merge($ping, $evaluation);

		echo json_encode($r);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier le numero DA (ou matricule)
     *
     * ------------------------------------------------------------------------ */
	public function verifier_numero_da()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if ($this->config->item('verifier_numero_da') != 1)
        {
            // Ne pas verifier le numero DA,
            // tel que specifie dans les parametres dynamiques (base de donnees).

            echo json_encode(TRUE);
            return;
        }

        $post_data = $this->input->post();

        $champs_obligatoires = array('enseignant_id', 'numero_da');

        foreach($champs_obligatoires as $champ)
        {
            if ( ! array_key_exists($champ, $post_data))
            {
                echo json_encode(FALSE);
                return;
            }
        }        

        //
        // Validation des champs requis
        //

        if ( ! ctype_digit($post_data['enseignant_id']) || ! ctype_alnum($post_data['numero_da']))
        {
            echo json_encode(FALSE);
            return;
        }

        echo json_encode($this->Evaluation_model->verifier_numero_da($post_data['enseignant_id'], $post_data['numero_da']));
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire la session_id pour les etudiants NON inscrits
     *
     * ------------------------------------------------------------------------ */
	public function _extraire_session_id($evaluation_reference)
    {
        if ($this->logged_in)
        {
            return NULL;
        }

        if (array_key_exists($evaluation_reference, $_SESSION) && ! empty($_SESSION[$evaluation_reference]))
        {
            return $_SESSION[$evaluation_reference];
        }
        
        //
        // La session_id change tous les 300 secondes (selon config.php) donc il faut
        // enregistrer la session_id originale dans le cookie, pour etre en
        // mesure de retrouver les traces.
        //

        $_SESSION[$evaluation_reference] = session_id();

        return session_id();
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Eneregistrer le nom de l'etudiant dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_nom_traces()
    {
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('nom', $post_data) || empty($post_data['nom']))
		{
			echo json_encode(FALSE);
			return;
		}

		$session_id = NULL;

		//
		// Etudiants
		//

		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			//
			// Verifier que cette evaluation est toujours en vigueur
			//
			// - Ne pas enregistrer les traces si cette evaluation a ete terminee par l'enseignant
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

		$traces_arr['nom'] = $post_data['nom'];

        //
        // Enregistrer les traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr, 
            array(
                'session_id' => $session_id
            )
        );
		
		echo json_encode($r);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enregistrer le numero DA de l'etudiant dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_numero_da_traces()
	{
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('numero_da', $post_data) || empty($post_data['numero_da']))
		{
			echo json_encode(FALSE);
			return;
		}		

		$session_id = NULL;

		//
		// Etudiants
		//

		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

		$traces_arr['numero_da'] = $post_data['numero_da'];

        //
        // Enregistrer les nouvelles traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr,
            array(
                'session_id' => $session_id
            )
        );

		echo json_encode($r);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enregistrer les reponses "checkbox" dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_reponse_checkbox_traces()
	{
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id', 'reponse_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		$session_id = NULL;

		//
		// Etudiants
		// 

		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
		
			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

        	$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

		$question_id = $post_data['question_id'];
		$reponse_id  = $post_data['reponse_id'];

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

        if ( ! array_key_exists($question_id, $traces_arr))
        {
            $traces_arr[$question_id] = array($reponse_id);
        }
        else
        {
            if (in_array($reponse_id, $traces_arr[$question_id]))
            {
                $key = array_search($reponse_id, $traces_arr[$question_id]);
                unset($traces_arr[$question_id][$key]);
            }
            else
            {
                $traces_arr[$question_id][] = $reponse_id;
            }
        }		

        //
        // Enregistrer les nouvelles traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr,
            array(
                'session_id' => $session_id
            )
        );

		echo json_encode($r);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enregistrer les reponses "radio" dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_reponse_radio_traces()
	{
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id', 'reponse_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		$session_id = NULL;

		//
		// Etudiants
		//
	
		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
		
			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
        
			$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

		$question_id = $post_data['question_id'];
		$reponse_id  = $post_data['reponse_id'];

		$traces_arr[$question_id] = $reponse_id;

        //
        // Enregister les nouvelles traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr,
            array(
                'session_id' => $session_id
            )
        );
		
		echo json_encode($r);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enregistrer les reponses "text" dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_reponse_text_traces()
	{
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('reponse', $post_data) || empty($post_data['reponse']))
		{
			echo json_encode(FALSE);
			return;
		}

		$session_id = NULL;

		//
		// Etudiants
		//

		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);
	
		$question_id = $post_data['question_id'];
		$reponse     = str_replace('"', '', $post_data['reponse']);

		$traces_arr[$question_id] = $reponse;

        //
        // Enregister les traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr,
            array(
                'session_id' => $session_id
            )
        );
		
		echo json_encode($r);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Enregistrer les reponses "textarea" dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_reponse_textarea_traces()
	{
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('reponse', $post_data))
		{
			echo json_encode(FALSE);
			return;
		}

		$session_id = NULL;

		//
		// Etudiants
		//

		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
		
			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);
	
		$question_id = $post_data['question_id'];
		$reponse     = $post_data['reponse'];

		$traces_arr[$question_id] = $reponse;

        //
        // Enregister les traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr,
            array(
                'session_id' => $session_id
            )
        );
		
		echo json_encode($r);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Enregistrer les reponses numeriques dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_reponse_numerique_traces()
	{
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('reponse', $post_data))
		{
			echo json_encode(FALSE);
			return;
		}

		$session_id = NULL;

		//
		// Etudiants
		//

		if ($this->est_etudiant)
		{
			if ( ! array_key_exists('evaluation_reference', $post_data) || empty($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
		
			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}

			$session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		}

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

		$question_id = $post_data['question_id'];
        $reponse     = filter_input(INPUT_POST, 'reponse', FILTER_SANITIZE_SPECIAL_CHARS);

		$traces_arr[$question_id] = $reponse;

        //
        // Enregister les traces
        //

        $r = $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr,
            array(
                'session_id' => $session_id
            )
        );
		
		echo json_encode($r);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enregistrer l'identite des partenaires de laboratoire dans les traces 
     * (AJAX) Enregistrer les champs des laboratoires dans les traces
     *
     * ------------------------------------------------------------------------ */
	public function enregistrer_lab_input_traces()
    {
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

		$post_data = $this->input->post();

		$champs_obligatoires = array('evaluation_id', 'evaluation_reference', 'champ', 'champ_val', 'champ_type');

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $post_data))
			{
				echo json_encode(FALSE);
				return;
			}

			if ($c == 'evaluation_id' && empty($post_data['evaluation_id']))
			{
				echo json_encode(FALSE);
				return;
			}
		}

        //
        // Verifier que cette evaluation est toujours en vigueur
        //
        // - Ne pas enregistrer les traces si cette evaluation a ete terminee par l'enseignant
        // 

		if ($this->est_etudiant)
		{
			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
		}

		if ($this->est_enseignant)
		{
			$post_data['evaluation_reference'] = 'previsual';
		}

		// DESUET
        // $session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		$session_id = NULL;

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

        $champs_identification = array('lab_place', 'lab_partenaire1', 'lab_partenaire2', 'lab_partenaire3', 'lab_partenaire2_nom', 'lab_partenaire3_nom');

        //
        // Valeur du champ
        //

        $champ_val = $post_data['champ_val'];

        if ($post_data['champ_type'] == 'select')
        {
            $traces_arr['lab'][$post_data['champ']] = $champ_val;
        }
        else
        {
            $champ_val = trim($post_data['champ_val']);

            if (in_array($post_data['champ'], $champs_identification))
            {
                $traces_arr[$post_data['champ']] = $champ_val;
            }
            else
            {
                // Ce n'est pas un champ identification, donc une donnee experimentale.
                $champ_val = str_replace(' ', '', $champ_val);
                $champ_val = n_sci_fix($champ_val);

                $traces_arr['lab'][$post_data['champ']] = $champ_val;
            }
        }

        //
        // Enregistrer les traces
        //

        $r = $this->Evaluation_model->ecrire_traces(
			$post_data['evaluation_reference'], 
			$post_data['evaluation_id'], 
			$traces_arr, 
            array(
                'session_id' => $session_id
            )
        );

        $r_arr = array(
            'res'       => $r,          // (bool)
            'champ_val' => $champ_val   // valeur du champ arrangee pour les erreurs d'input
        );

		echo json_encode($r_arr);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enregistrer l'identite des partenaires de laboratoire dans les traces *** avec SELECT ***
     *
     * ------------------------------------------------------------------------ */
    public function enregistrer_lab_select_identificaction_traces()
    {
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

		$post_data = $this->input->post();

		$champs_obligatoires = array('evaluation_id', 'evaluation_reference', 'partenaire', 'eleve_id');

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $post_data))
			{
				echo json_encode(FALSE);
				return;
			}

			if ($c == 'evaluation_id' && empty($post_data['evaluation_id']))
			{
				echo json_encode(FALSE);
				return;
			}
		}

        //
        // Verifier que cette evaluation est toujours en vigueur
        //
        // - Ne pas enregistrer les traces si cette evaluation a ete terminee par l'enseignant
        // 

		if ($this->est_etudiant)
		{
			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($post_data['evaluation_reference']))
			{
				echo json_encode(FALSE);
				return;
			}
		}

		if ($this->est_enseignant)
		{
			$post_data['evaluation_reference'] = 'previsual';
		}

		// DESUET
        // $session_id = $this->_extraire_session_id($post_data['evaluation_reference']);
		$session_id = NULL;

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $session_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $traces_arr = unserialize($traces);

        $champ = $post_data['partenaire'] . '_eleve_id'; // lab_partenaireN_eleve_id   N = 2 ou 3

        $traces_arr[$champ] = $post_data['eleve_id'];

        //
        // Enregistrer les traces
        //

        $r = $this->Evaluation_model->ecrire_traces(
			$post_data['evaluation_reference'], 
			$post_data['evaluation_id'], 
			$traces_arr, 
            array(
                'session_id' => $session_id
            )
        );
		
		echo json_encode($r);
		return;

    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Confirmer les partenaires de laboratoire
     *
     * ------------------------------------------------------------------------ */
    public function confirmer_lab_partenaires()
    {
        if ( ! @$this->traces)
        {
            echo json_encode(TRUE);
            return;
        }

        $post_data = $this->input->post();

        $r = $this->Lab_model->confirmer_lab_partenaires($post_data);

        echo json_encode($r);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Previsualisation de l'evaluation par l'enseignant
     *
     * ------------------------------------------------------------------------ */
	public function previsualisation($args = array())
    {
		//
		// Validation de l'argument
		//

        if ( ! is_array($args)       || 
               empty($args[0])       ||
             ! is_numeric($args[0])  ||
             ! ctype_digit($args[0])
           )
        {
            redirect(base_url());
            return;
        }

        $evaluation_id = $args[0];

		//
		// Verifier que les conditions de previsualisation soient remplies.
		//

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            return;
        }

        //
        // Extraire l'evaluation (sommaire)
        //

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if (empty($evaluation) || ! is_array($evaluation))
        {
            redirect(base_url());
            return;
        }

        //
        // Verifier les permissions
        //
        // - Une evaluation privee ne peut etre previsualisee que par son enseignant proprietaire.
        // - Une evaluation publique au groupe peut etre previsualisee par tous les membres du groupe.
        //

        if ($this->enseignant['privilege'] < 100)
        {
            //
            // Groupe Personnel
            //   

            if ($this->groupe_id == 0)
            {
                if ($this->enseignant_id != $evaluation['enseignant_id'])
                {
                    redirect(base_url());
                    return;
                }
            }

            //
            // Groupe
            //

            else
            {
                // Evaluation privee

                if ($evaluation['public'] != 1)
                {
                    if ($this->enseignant_id != $evaluation['enseignant_id'])
                    {
                        redirect(base_url());
                        return;
                    }
                }

                // Evaluation departementale (du groupe)

                else
                {
                    if ($this->groupe_id != $evaluation['groupe_id'])
                    {
                        redirect(base_url());
                        return;
                    }
                }
            }
        }

        //
        // Est-ce que la version etudiante est demandee ?
        //

        $this->data['previsualisation_etudiante'] = ($this->uri->segment(4) == 'etudiant' ? TRUE : FALSE);

        //
        // Verifier l'integrite de l'evaluation
        //

        $status = $this->Evaluation_model->verifier_integrite_evaluation($evaluation_id);

        //
        // Il y a des erreurs dans l'evaluation
        //

        if ($status !== TRUE)
        {
            $this->data['erreur'] = $status;
        }

        //
        // Montrer l'evaluation
        //
  
        $this->_montrer_evaluation($evaluation_id, TRUE, array('enseignant' => $this->enseignant_id));
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Montrer l'evaluation a l'etudiant selon la reference
     *
     * ------------------------------------------------------------------------ */
    public function ref($evaluation_reference)
    {
        //
        // Validation des arguments requis
        //

        if (
              empty($evaluation_reference)       || 
            ! ctype_alpha($evaluation_reference) || 
              strlen($evaluation_reference) != 6
          )
        {
            redirect(base_url());
            return;
        }

        // 
        // Verifier que cette evaluation existe et qu'elle est en vigueur.
        //

        if (($evaluation = $this->Evaluation_model->verifier_evaluation_reference($evaluation_reference)) == FALSE)
        {
            redirect(base_url());
            return;
        }

        // 
        // Verifier que cette evaluation appartient a ce sous-domaine.
        //
        
        if ($this->sous_domaine != $evaluation['sous_domaine'])
        {
            redirect(base_url());
            return;
        }

        //
        // Verifier que l'evaluation n'est pas bloquee
        //

		if ($evaluation['bloquer'])
		{
			if ( ! $this->est_etudiant)
			{
				redirect(base_url() . 'erreur/spec/EVBLK1');
				exit;
			}

			if ($this->est_etudiant)
			{
				// Verifier si l'etudiant est deja en redaction de cette evaluation.
				// Un etudiant en redaction peut continuer a rediger.
	
				if ($this->Evaluation_model->lire_traces_externe($evaluation['evaluation_reference'], $evaluation['evaluation_id'], $this->etudiant_id) === FALSE)
				{
					redirect(base_url() . 'erreur/spec/EVBLK1');
					exit;
				}
			}
        }

        //
        // Verifier si la date planifiee est respectee
        //

        if ($evaluation['debut_epoch'] > $this->now_epoch)
        {
            // Enregistrer l'heure du debut de l'evaluation dans la session pour
            // la communiquer a l'etudiant dans la vue.

            $_SESSION['evaluation_debut_epoch'] = $evaluation['debut_epoch'];

            $this->session->mark_as_flash('evaluation_debut_epoch');

            redirect(base_url() . 'erreur/spec/EVPLN1');
			exit;
        }

		//
		// Inscription requise
        // 

		if ($evaluation['inscription_requise'])
        {
			//
			// Verifier que l'etudiant est inscrit
			//

			if ( ! $this->est_etudiant)
            {
                //
                // Un enseignant essaie de voir son evaluation, redirigeons-le vers la previsualisation.
                //

                if ($this->logged_in && $this->est_enseignant)
                {
                    // Ceci n'est pas l'evaluation de cet enseignant.

                    if ($evaluation['enseignant_id'] != $this->enseignant_id)
                    {
                        redirect(base_url());
                    }

                    redirect(base_url() . 'evaluation/previsualisation/' . $evaluation['evaluation_id'] . '/etudiant');
                    exit;            
                }

				$_SESSION['redirect_evaluation'] = $evaluation_reference;

				log_alerte(
					array(
						'code'       => 'INSREQ1',
						'desc'       => "L'inscription est requise pour accéder à cette évaluation.",
						'importance' => 0,
						'extra'      => 'evaluation_id = ' . $evaluation['evaluation_id'] . 
										', evaluation_reference = ' . $evaluation_reference
					)
				);

				redirect(base_url() . 'erreur/spec/INSREQ1');
				return;
			}

			//
			// Verifier les filtres d'etudiants
			//

			if (
				$evaluation['filtre_enseignant']	 		  ||
				$evaluation['filtre_enseignant_autorisation'] ||
				$evaluation['filtre_cours'] 				  ||
				$evaluation['filtre_cours_autorisation'] 	  ||
				$evaluation['filtre_groupe'] 				  ||
				$evaluation['filtre_groupe_autorisation']
			   )
			{
				//
				// Verifier si l'autorisation est necessaire
				//

				$filtre_autorisation = FALSE;

				if (
					$evaluation['filtre_enseignant_autorisation'] ||
					$evaluation['filtre_cours_autorisation']      ||
					$evaluation['filtre_groupe_autorisation']
				   )
				{
				   $filtre_autorisation = TRUE;
				}

				//
				// Authorisation requise
				//
				// L'etudiant doit etre autorise pour acceder cette evaluation
				//

				if ($filtre_autorisation)
				{
					$comptes_autorises = $this->Etudiant_model->extraire_comptes_autorises(
						$evaluation['semestre_id'],
						array(
							'enseignant_id' => $evaluation['enseignant_id'],
							'cours_id'      => $evaluation['cours_id']
						)
					);

					if ( ! array_key_exists($this->etudiant_id, $comptes_autorises))
					{
						// Cet etudiant n'est pas autorise.

						redirect(base_url() . 'erreur/spec/FTE8899');
						exit;
					}

					//
					// Verifier les filtres qui requiert l'autorisation
					//

					if ($evaluation['filtre_enseignant_autorisation'])
					{
						// Cet etudiant n'est pas avec cet enseignant.

						// Deja verifie precedemment
					}

					elseif ($evaluation['filtre_cours_autorisation'])
					{
						if ($evaluation['cours_id'] != $comptes_autorises[$this->etudiant_id]['cours_id'])
						{
							// Cet etudiant n'est pas dans ce cours.

							redirect(base_url() . 'erreur/spec/FTE7741');
							exit;
						}                
					}

					elseif ($evaluation['filtre_groupe_autorisation'])
					{
						if ($evaluation['cours_id'] != $comptes_autorises[$this->etudiant_id]['cours_id'])
						{
							// Cet etudiant n'est pas dans ce cours.

							redirect(base_url() . 'erreur/spec/FTE7741');
							exit;
						}                

						if ($evaluation['filtre_groupe_autorisation'] != $comptes_autorises[$this->etudiant_id]['cours_groupe'])
						{
							// Cet etudiant n'est pas le groupe requis de ce cours.

							redirect(base_url() . 'erreur/spec/FTE7799');
							exit;
						}                
					}
				}

				//
				// Authorisation non-requie
				//
				// L'etudiant doit etre dans les listes d'eleves de son enseignant,
				// et il doit avoir entre son numero_da dans son profil.
				//

				else
				{
					$eleves = $this->Cours_model->lister_eleves_pour_filtres(
						$evaluation['semestre_id'],
						array(
							'enseignant_id' => $evaluation['enseignant_id']
						)
					);

					$eleve_trouve = FALSE;

					foreach($eleves as $e)
					{
						if (empty($e['numero_da']))
							continue;

						if (empty($this->etudiant['numero_da']))
							continue;

						if ($e['numero_da'] != $this->etudiant['numero_da'])
							continue;

						$eleve_trouve = TRUE;

						//
						// Verifier les filtres
						//

						if ($evaluation['enseignant_id'] != $e['enseignant_id'])
						{
							// Cet etudiant n'est pas avec cet enseignant.

							redirect(base_url() . 'erreur/spec/FTE8900');
							exit;
						}

						if ($evaluation['filtre_enseignant'])
						{
							// Cet etudiant n'est pas avec cet enseignant.

							// Deja verifie precedemment
						}

						elseif ($evaluation['filtre_cours'])
						{
							if ($evaluation['cours_id'] != $e['cours_id'])
							{
								// Cet etudiant n'est pas dans ce cours.

								redirect(base_url() . 'erreur/spec/FTE7742');
								exit;
							}						
						}

						elseif ($evaluation['filtre_groupe'])
						{
							if ($evaluation['cours_id'] != $e['cours_id'])
							{
								// Cet etudiant n'est pas dans ce cours.

								redirect(base_url() . 'erreur/spec/FTE7742');
								exit;
							}						

							if ($evaluation['filtre_groupe'] != $e['cours_groupe'])
							{
								// Cet etudiant n'est pas dans ce groupe.

								redirect(base_url() . 'erreur/spec/FTE7800');
								exit;
							}						
						}

						//
						// L'etudiant remplit les criteres du filtre.
						//

						break;
					}

					//
					// L'etudiant n'a pas ete trouve dans la liste d'eleves de l'enseignant. 
					//

					if ( ! $eleve_trouve)
					{
						redirect(base_url() . 'erreur/spec/FTE8901');
						exit;
					}

				}
			} // filtre actif

		} // inscription requise

        //
        // Verifier que le semestre de cette evaluation est en vigueur.
        //

        $semestres = $this->Semestre_model->lister_semestres(
            array(
                'groupe_id'     => $this->groupe_id, 
                'enseignant_id' => $evaluation['enseignant_id']
            )
        );

        if (empty($semestres) || ! array_key_exists($evaluation['semestre_id'], $semestres))
        {
            generer_erreur('GS5618', "Le semestre correspondant à cette évaluation est inexistant.");
            return;
        }

        $semestre = $semestres[$evaluation['semestre_id']];

        if ( ! ($semestre['semestre_debut_epoch'] < $this->now_epoch && $semestre['semestre_fin_epoch'] > $this->now_epoch)) 
        {
            $periode = $semestre['semestre_debut_date'] . ' ⟷  ' . $semestre['semestre_fin_date'];

            generer_erreur('GS5619', "Le semestre correspondant à cette évaluation n'est pas en vigueur (" . $periode . ").");
            return;
        }

        $evaluation_id = $evaluation['evaluation_id'];
        $enseignant_id = $evaluation['enseignant_id'];

        //
        // Verifier l'integrite de l'evaluation
        //

        $status = $this->Evaluation_model->verifier_integrite_evaluation($evaluation_id);

        //
        // Il y a des erreurs dans l'evaluation
        //

        if ($status !== TRUE)
        {
            if ($previsualisation && $this->est_enseignant)
            {
                // Presenter un message d'erreur a l'enseignant.

                $this->data['erreur'] = $status;
            }
            else
            {
                //
                // Ne pas afficher certaines erreurs (erreurs muettes).
                //

                $erreurs_muettes = array('VIE1190', 'VIE1191');

                if ( ! in_array($status['code'], $erreurs_muettes))
                {
                    // Presenter un message d'erreur aux etudiants,

                    // Les codes des erreurs VIE1190 et VIE1191 correspondent a des reponses identiques
                    // generees par des equations, mais dont une iteration raisonnable, par exemple 12 fois,
                    // permet de generer des reponses uniques avec un haut taux de probabilite.
                    // Donc ces erreurs ne seront pas presentees aux etudiants.

                    generer_erreur(
                        $status['code'],
                        $status['message'] . '<br />' .
                        "Veuillez communiquer avec votre enseignante ou enseignant pour régler ce problème."
                    );
                    return;
                }
            }
        }

        //
        // Si l'evaluation est un laboratoire, 
        // verifier que l'etudiant n'est pas le partenaire d'un autre etudiant ayant deja ouvert l'evaluation.
        //

        if ($evaluation['lab'] && $this->est_etudiant)
        {
            $lab_dop = $this->Lab_model->lab_deja_ouvert_par_partenaire($evaluation_reference);

            if ($lab_dop['status'])
            {
                generer_erreur($lab_dop['code'], $lab_dop['erreur']);
                return;
            }
        }

        //
        // Montrer l'evaluation
        //

        $this->_montrer_evaluation($evaluation_id, FALSE, 
            array(
                'evaluation_reference' => $evaluation_reference
            )
        );
        
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Montrer l'evaluation a l'etudiant
     *
     * ------------------------------------------------------------------------
     *
     * Version 5: 2019/06/04
     *
     * ------------------------------------------------------------------------ */
	public function _montrer_evaluation($evaluation_id, $previsualisation = FALSE, $arguments = array())
    {
        //
		// Evaluation
		//

		$evaluation           = $this->Evaluation_model->extraire_evaluation($evaluation_id);
        $evaluation_reference = empty($arguments['evaluation_reference']) ? NULL : $arguments['evaluation_reference'];

        //
        // Est-ce un laboratoire ?
        //

        $lab = $evaluation['lab'];

        if ($lab)
        {
            $lab_parametres = ! empty($evaluation['lab_parametres']) ? json_decode($evaluation['lab_parametres'], TRUE) : array();
            $lab_valeurs    = ! empty($evaluation['lab_valeurs']) ? json_decode($evaluation['lab_valeurs'], TRUE) : array();
            $lab_points     = ! empty($evaluation['lab_points']) ? json_decode($evaluation['lab_points'], TRUE) : array();
            $lab_prefix     = $evaluation['lab_prefix'];
            $lab_vue        = $evaluation['lab_vue'];

            //
            // Extraire la liste des eleves du laboratoire
            //
            
            $lab_eleves = array();
            $lab_cours_groupe = 0;

            if ($lab && $this->est_etudiant && ! empty($this->etudiant['numero_da']))
            {
                $lab_liste_eleves_etudiant = liste_eleves_groupe_etudiant(
                    $this->etudiant['numero_da'], 
                    array(
                        'semestre_id'	=> $this->semestre_id,
                        'enseignant_id' => $evaluation['enseignant_id'],
                        'cours_id'		=> $evaluation['cours_id']
                    )
                );

                $lab_eleves = $lab_liste_eleves_etudiant['liste_eleves_cours'];
                $lab_cours_groupe = $lab_liste_eleves_etudiant['eleve_cours_groupe'];
            }

            // 
            // Organiser les points des champs du laboratoire en ordre de numero de tableau
            //

            $lab_points_tableaux = generer_lab_points_tableaux($lab_points);
            $lab_points_totaux   = $lab_points_tableaux['points_totaux'];
        }

        //
        // Extraire les informations rel_evaluations
        //

        $rel_evaluation = $this->Evaluation_model->verifier_evaluation_reference($evaluation_reference);

        //
        // Extraire la session_id (originale, lors du premier chargement)
        //

        $session_id = $this->_extraire_session_id($evaluation_reference) ?: session_id();

        //
        // Ceci pour afficher la barre d'information
        //

        $this->data['evaluation_details'] = array(
            'enseignant_nom'    => $evaluation['enseignant_nom'],
            'enseignant_prenom' => $evaluation['enseignant_prenom'],
            'enseignant_genre'  => $evaluation['enseignant_genre']
        );

        //
        // Verifier si cette evaluation a deja ete envoyee.
        //

        $soumission_deja_envoyee = $this->Evaluation_model->soumission_envoyee( 
            array(
                'session_id'           => $session_id,
                'evaluation_reference' => $evaluation_reference,
                'evaluation_id'        => $evaluation['evaluation_id'],
            )
        );

        if ($soumission_deja_envoyee !== FALSE)
        {
            $this->Evaluation_model->effacer_traces(
                $evaluation_reference, 
                $evaluation['evaluation_id'], 
                array(
                    'session_id' => $session_id
                )
            );

            $this->_affichage('soumission-deja-envoyee');

            return;
        }

		//
		// Certaines evaluations ont un temps limite, alors verifier que l'etudiant est au courant
		// que des qu'il charge l'evaluation, le temps debute.
		//
		
		$evaluation_option = $this->uri->segment(3);

		if ( ! $previsualisation && $evaluation_option != 'go' && ! empty($rel_evaluation['temps_limite']) && ctype_digit($rel_evaluation['temps_limite']))
		{
			// Verifier si l'etudiant a deja charge cette evaluation

			if ( ! $this->Evaluation_model->evaluation_deja_chargee($rel_evaluation['evaluation_id'], $rel_evaluation['evaluation_reference']))
			{
				// Cette evaluation n'a jamais ete chargee. 
				// Il faut envoyer une notification a l'etudiant pour l'avertir que cette evaluation comporte un temps limite.

                $temps_limite = $rel_evaluation['temps_limite'];

                if ($this->etudiant_id)
                {
                    $temps_supp = $this->Etudiant_model->extraire_etudiant_id_temps_supp(
                        $this->etudiant_id, 
                        array(
                            'groupe_id'   => $rel_evaluation['groupe_id'] ?? NULL,
                            'cours_id'    => $rel_evaluation['cours_id'] ?? NULL,
                            'semestre_id' => $rel_evaluation['semestre_id'] ?? NULL
                        )
                    );
                    $temps_limite = $temps_limite + ($temps_limite * $temps_supp/100);
                }

				$this->data['evaluation_reference'] = $rel_evaluation['evaluation_reference'];
				$this->data['temps_limite'] = $temps_limite;

                $this->_affichage('evaluation-temps-limite-confirmation');

				return;
			}
		}

		//
        // Verifier si cette evaluation (evaluation_reference) appartient a ce groupe,
        // sinon rediriger vers le groupe approprie
		//

		$groupe = $this->Groupe_model->extraire_groupe(array('evaluation_id' => $evaluation_id));

        if ($groupe['groupe_id'] != $this->groupe_id)
        {
            // Cette evaluation ne fait pas partie de ce groupe.
            // Rediriger vers le bon groupe.

            // redirect('https://' . $groupe['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/evaluation/' . $evaluation_reference);
            redirect('https://' . $groupe['sous_domaine'] . '.' . $this->domaine . '/evaluation/' . $evaluation_reference);
            exit;
        }

		//
		// Ecole
        //

        $ecole = $this->Ecole_model->extraire_ecole(array('ecole_id' => $this->ecole_id));

        //
        // Enseignant
        //

        $enseignant = $this->Enseignant_model->extraire_enseignant($evaluation['enseignant_id']);

		//
		// Cours
		//

		$cours = $this->Cours_model->extraire_cours(array('evaluation_id' => $evaluation_id));

        //
        // Blocs
        //

        $blocs    = $this->Question_model->extraire_blocs($evaluation_id);
        $bloc_ids = array_keys($blocs);

        if ( ! empty($blocs))
        {
            $blocs_questions = $this->Question_model->lister_questions_dans_blocs($bloc_ids, array('actif' => TRUE));
        }

        //
        // Ajuster le niveau de dangerosite des fichiers acceptes en televersement
        //

        $documents_mime_types = json_encode($this->config->item('documents_mime_types'));

        if ( ! ($this->config->item('permettre_fichiers_dangereux') && $enseignant['permettre_fichiers_dangereux']))
        {
            $dmt = array();

            foreach($this->config->item('documents_mime_types_properties') as $t => $prop)
            {
                if ($prop['danger'] === FALSE)
                {
                    $dmt[] = $t;
                }
            }

            $documents_mime_types = json_encode($dmt);
        }

		//
		// Questions
        //

        $questions = array();

        // --------------------------------------------------------------------
        //
        // Determiner si l'etudiant a deja charge ou non cette evaluation
        //
        // --------------------------------------------------------------------
        //
        // En previsualisation par l'enseignant, on considere toujours qu'il
        // s'agit d'un premier chargement.
        //
        // --------------------------------------------------------------------

        if (($traces = $this->Evaluation_model->lire_traces($evaluation_reference, $evaluation_id, $session_id)) === FALSE)
        {
            log_alerte(
                 array(
                     'code'  => 'XYX7117',
                     'desc'  => "Vous avez déjà complété cette évaluation.",
                     'extra' => 'evaluation_reference = ' . $evaluation_reference . 'evaluation_id = ' . $evaluation_id,
                     'importante' => 6
                 )
             );

            generer_erreur('XYX7117', "Vous avez déjà complété cette évaluation.");
            return;
        }

		$traces_arr = unserialize($traces);

        if ( ! $previsualisation && array_key_exists('questions_choisies', $traces_arr) && ! empty($traces_arr['questions_choisies']))
        {
            //
            // Lecture des traces
            // 

            $questions_choisies = $traces_arr['questions_choisies'];
            $questions          = $this->Question_model->lister_questions($evaluation_id, array('question_ids' => $questions_choisies));

            // Ceci servira a enregistrer les questions dans dans l'evaluation, de facon encryptee.
            $questions_choisies_encryptees = $this->encryption->encrypt(serialize($traces_arr['questions_choisies']));
        }
        
        //
        // Enregistrer / Mettre-a-jour ces informations dans les traces a chaque chargement
        //

        $traces_arr['session_id']   = $session_id;
        $traces_arr['adresse_ip']   = $this->input->ip_address();
        $traces_arr['unique_id']    = $this->Admin_model->generer_unique_id(); 
        $traces_arr['agent_string'] = $this->agent->agent_string() ?: NULL;
        $traces_arr['fureteur_id']  = $this->Admin_model->generer_fureteur_id();

        //
        // Est-ce que l'etudiant a deja charge cette evaluation
        //

        $chargement_premiere_fois = TRUE; // On assume oui, et on modifie ce drapeau par la suite.

        // --------------------------------------------------------------------
        //
        // L'etudiant a deja charge cette evaluation
        //
        // --------------------------------------------------------------------

        //
        // Si l'etudiant a deja charge cette evaluation, donc les questions ont deja ete choisies, 
        // il faut les organiser selon l'ordre de l'evaluation, ou l'ordre aleatoire deja decide au premier chargement
        //

        if ( ! empty($questions) && is_array($questions))
        {
            $chargement_premiere_fois = FALSE;

            // Est-ce que l'enseignant veut presenter les questions aleatoirement ?

            if ($evaluation['questions_aleatoires'])
            {
                // Les questions doivent etre reordonnees selon l'ordre des questions choisies lors d'un chargement precedent.

                $questions_tmp = array();

                foreach($questions_choisies as $q_id)
                {
                    $questions_tmp[$q_id] = $questions[$q_id];
                }

                $questions = $questions_tmp;
            }
        }

        // --------------------------------------------------------------------
        //
        // Le premier chargement de l'etudiant
        //
        // --------------------------------------------------------------------

        if (empty($questions))
        {
            // Mesures de securite pour eviter les chargements a repetition par les etudiants.

            if ( ! $this->est_enseignant)
            {
                // Verifier que cette evaluation peut etre chargee.

                if (($minutes_restantes = $this->Evaluation_model->permission_charger_evaluation($evaluation_id, $evaluation_reference)) !== TRUE)
                {
                    $this->data['minutes_restantes'] = $minutes_restantes;

                    $this->_affichage('evaluation-chargement-abusif');
                    return;
                }

                // Enregistrer ce chargement

                $this->Evaluation_model->demande_chargement_evaluation($evaluation_id, $evaluation_reference);
            }

            //
            // Extraire toutes les questions pertinentes a cette evaluation.
            //

            $questions_raw = $this->Question_model->lister_questions($evaluation_id, array('actif' => TRUE));

            //
            // Les blocs
            //

            //
            // Determiner les questions qui seront choisies pour chaque bloc.
            //

            $questions_blocs_choisies = array(); // liste de question_ids;

            if ( ! empty($blocs))
            {
                foreach($blocs as $bloc_id => $b)
                {
                    // Ne pas inclure les blocs vides.

                    if ($b['bloc_nb_questions'] == 0)
                    {
                        continue;
                    }

                    // Inclure toutes les questions des blocs complets (i.e. toutes les questions sont a choisir (ex. 4/4))
                    
                    if ($b['bloc_nb_questions'] == count($blocs_questions[$bloc_id]))
                    {
                        foreach($blocs_questions[$bloc_id] as $q_id)
                        {
                            $questions_blocs_choisies[] = $q_id;
                        }

                    }
                    
                    // Choisir des questions aleatoirement parmi toutes les questions du bloc.
                    
                    elseif ($b['bloc_nb_questions'] < count($blocs_questions[$bloc_id]))
                    {
                        shuffle($blocs_questions[$bloc_id]);

                        for($i = 1; $i <= $b['bloc_nb_questions']; $i++)
                        {
                            $q_id = array_shift($blocs_questions[$bloc_id]);

                            $questions_blocs_choisies[] = $q_id;
                        }
                    }

                    // Ceci ne devrait jamais avoir lieu et ne sert qu'a detecter les problemes.

                    else
                    {
                        // Pour regler ce probleme automatiquement, il faudrait recompter le nombre de questions inclus dans le bloc problematique puis
                        // ajuster en consequence, mais cette erreur ne s'est encore jamais produite alors ce n'est probablement pas necessaire.

						log_alerte(
							 array(
								 'code'  => 'FRA9123',
								 'desc'  => "Le nombre de questions dans le bloc est plus grand que le nombre de questions qu'il contient.",
                                 'extra' => 'evaluation_id = ' . $evaluation_id . ', bloc_id = ' . $bloc_id,
                                 'importante' => 6
							 )
						 );

                        generer_erreur('FRA9124', "Une erreur s'est produite avec cette évaluation, veuillez contacter votre enseignante ou enseignant.");
                        return;
                    }

                } // foreach $blocs
            } // if ! empty($blocs)

            //
            // Determiner les questions de l'evaluation
            //

            foreach($questions_raw as $question_id => $q)
            {
                // Inclure toutes les questions qui ne sont pas dans un bloc.

                if (empty($q['bloc_id']))
                {
                    $questions[$question_id] = $q;

                    continue;
                }
                
                // Inclure les questions qui ont ete prelablement choisies des blocs.

                if (in_array($question_id, $questions_blocs_choisies))
                {
                    $questions[$question_id] = $q;
                }
            }

			// Est-ce qu'il faut melanger l'ordre des questions?

			if ($evaluation['questions_aleatoires'])	
			{
				shuffle($questions);
                $questions = array_keys_swap($questions, 'question_id');
			}

            //
            // Enregistrer les questions choisies dans l'evaluation ET la session, ou les traces, selon le cas.
            //

            // Encrypter les questions choisies dans l'evaluation pour eviter une manipulation de l'etudiant.

            $questions_choisies_encryptees = $this->encryption->encrypt(serialize(array_keys($questions)));

            //
            // Enregistrer ces informations dans les traces lors de ce premier chargement.
            //
            // - question_ids & ordre de presentation des question (questions_choisies)
            // - soumission_debut_epoch
            //
            // Conserver ces informations pendant 7 jours (sujet a changement).
            // 

            $traces_arr['questions_choisies']     = array_keys($questions);
            $traces_arr['soumission_debut_epoch'] = $this->now_epoch;

            // $this->Evaluation_model->ecrire_traces($evaluation_reference, $evaluation_id, $traces_arr, $session_id);  

        } // if empty($questions)

        //
        // Blocs & Questions : Ajuster les points selon les points des blocs
        //

        if ( ! empty($blocs))
        {
            foreach($questions as $question_id => $q)
            {
                if (empty($q['bloc_id']))
                    continue;
    
                if ( ! array_key_exists($q['bloc_id'], $blocs))
                    continue;

                $questions[$question_id]['question_points'] = $blocs[$q['bloc_id']]['bloc_points'];
            }
        }

        //
        // Images (associees aux questions)
        //

		$images = $this->Document_model->extraire_images(array_keys($questions));

		//
        // Variables 
        //
        
        $variables = array();

        // Information sur les variables

        $variables_raw   = $this->Evaluation_model->extraire_variables($evaluation_id);
        $plus_petit_cs   = 999; // necessaire pour determiner le plus petit CS des variables
        $iteration       = 0;   // iteration de securite pour eviter une boucle infinie

        //
        // Extraire les variables des traces ou de la session, selon le cas
        //

        if ( ! $previsualisation && array_key_exists('variables', $traces_arr) && ! empty($traces_arr['variables']))
        {
            $variables = $traces_arr['variables'];
        }

        //
        // Iterer pour generer des reponses uniques aux questions a coefficients variables
        //

        while ($iteration < 50)
        {
            $iteration++;

            // Verifier si des variables sont presentes
            
            if ( ! empty($variables_raw) && is_array($variables_raw))
            {
                // Verifier si les variables sont deja presentes dans les traces ou la session de l'etudiant

                if ( ! $previsualisation && ! empty($variables))
                {
                    // Verifier si toutes les variables sont presentes

                    $labels = array_keys($variables_raw);

                    foreach($labels as $label)
                    {
                        if ( ! array_key_exists($label, $variables))
                        {
                            $variables = array();
                            break;
                        }
                    }
                }

                if (empty($variables))
                {
                    // Les valeurs des variables n'existent pas dans la session de l'etudiant, il faut les creer et les ecrire dans la session de l'etudiant.

                    $variables = determiner_valeurs_variables($variables_raw);
                }

                //
                // 1. Determiner le plus petit CS de l'operation pour ajuster la reponse de l'equation (si necessaire).
				// 2. Transformer les petits nombres en nombres decimaux ($variables_safe).
                //

				$variables_safe = $variables;

                foreach($variables as $label => $v)
                {
                    if (cs($v) < $plus_petit_cs)
                    {
                        $plus_petit_cs = cs($v);
                    }

					$variables_safe[$label] = $v;

					if (strpos($v, 'E-') !== FALSE)
				 	{
						$variables_safe[$label] = number_format($v, 50);
					}
                }

            } // if ! empty($variables_raw)

            //
            // Verifier l'unicite des reponses generees par les equations (questions a coefficients variables)
            // qui seront presentees a l'etudiant.
            // 
            // Dans la function verifier l'integrite d'une evaluation, on fait une simulation de reponses possibles
            // sur un certain nombre d'iterations. Maintenant, on genere les vrais reponses pour l'evaluation,
            // donc on doit les verifier a nouveau.
            //
            // Si elles ne sont pas uniques, regenerer les variables et recommencer l'iteration.
            //

            // Les reponses sont acceptees si elles sont uniques. On presume qu'elles le seront.
            $reponses_acceptees = TRUE;

            // Les reponses generees (index: reponse_id)
            $reponses_generees = array();

            if ( ! empty($questions) && is_array($questions))
            {
                foreach($questions as $question_id => $q)
                {
                    if ($q['question_type'] != 3)
                    {
                        // Ceci n'est pas une question a coefficients variables.
                        continue;
                    }

                    $reponses = $this->Reponse_model->lister_reponses($question_id);
                    $valeurs  = array();

                    foreach($reponses as $reponse_id => $r)
                    {
                        if ( ! $r['equation'])
                        {
                            // Cette reponse n'est pas une question.
                            continue;
                        }

                        try 
                        {
                            $resolu = Parser::solve(str_replace(',', '.', $r['reponse_texte']), $variables_safe); 
                        } 
                        catch (Exception $e) 
                        {
                            generer_erreur('GE481235', "Il y a un problème avec votre équation, vérifiez le message suivant :<br /><br /><pre>" . $e->getMessage() . "</pre>");
                            return;
                        }

                        // Ne pas considerer les CS si CS == 99.

                        if ($r['cs'] != 99)
                        {
                            if ( ! empty($r['cs']))
                            {
                                $resolu = cs_ajustement($resolu, $r['cs']);
                            }
                            elseif ($plus_petit_cs < 999)
                            {
                                $resolu = cs_ajustement($resolu, $plus_petit_cs);
                            }
                        }

                        $resolu = str_replace('.', ',', $resolu);

                        $valeurs[] = $resolu;
                        $reponses_generees[$reponse_id] = $resolu;
                    }

                    if (empty($valeurs))
                    {
                        generer_erreur2(
                            array(
                                'code'  => 'GE481299.1', 
                                'desc'  => "Il y a un problème avec votre équation car aucune réponse n'a pu être générée.",
                                'extra' => 'enseignant_id = ' . $this->enseignant_id . ', evaluation_id = ' . $evaluation_id
                            )
                        );
                        return;
                    }

                    // Verifier l'unicite des reponses par comptage.

                    if (count(array_unique($valeurs)) < count($valeurs))
                    {
                        // Les reponses ne sont pas uniques

                        $reponses_acceptees = FALSE;

                        // Effacer les variables
                        $variables       = array();
                        $variables_eleve = array();
                        $_SESSION[$session_item_nom] = '';

                        // Reseeder le random
                        srand(time() + $iteration);

                        break; // sort de la loop foreach
                    }

                } // foreach $questions

            } // is_array && ! empty($questions)

            // 
            // Enregistrer les variables dans les traces de l'etudiant
            // (sauf pour la previsualisation)
            //

            if ($reponses_acceptees)
            {
                if ( ! empty($variables)) 
                {
                    $variables_choisies_encryptees = $this->encryption->encrypt(serialize($variables));

                    // Il faut absoluement ecrire les variables dans la session, MEME en mode previsualisation (!)
                    // meme si celles-ci ne seront pas lu de la session pour les enseignants.

                    $traces_arr['variables'] = $variables;

                    // $this->Evaluation_model->ecrire_traces($evaluation_reference, $evaluation_id, $traces_arr, $session_id);
                }

                break; // sortir de la loop while
            }

        } // while

        // 
        // Il n'a pas ete possible de generer des reponses uniques base sur vos variables.
        //

        if ( ! $reponses_acceptees)
        {
            generer_erreur(
                'GE891249', 
                "Il n'a pas été possible de générer des réponses uniques basées sur le choix de vos variables, de vos équations, ou de vos chiffres significatifs.",
                array('importance' => 3)
            );
            return;
        }

        //
        // Extraire l'ordre des reponses des traces, s'il ne s'agit pas du premier chargement
        //

        $reponses_ordre = array();

        if ( ! $previsualisation && array_key_exists('reponses_ordre', $traces_arr) && ! empty($traces_arr['reponses_ordre']))
        {
            $reponses_ordre = $traces_arr['reponses_ordre'];
        }

		//
		// Points de l'evaluation
		//

        $points_evaluation = 0;

		if ($lab)
		{
			$points_evaluation += $lab_points_totaux;
		}

		//
		// Validation des questions et association des reponses
		//

        $reponses = array();     // contient les reponses de toutes les questions
        $documents = array();    // contient les documents des questions de type 10

		foreach($questions as $question_id => $q)
        {
            //
            // Tous les types de questions
            //

            //
            // Remplacer les variables dans le texte des questions par leur valeur numerique.
            //
            // (!) Il peut y avoir des variables dans n'importe quel type de question.
            //

            if ( ! empty($variables))
            {
                $questions[$question_id]['question_texte'] = remplacer_variables_question($questions[$question_id]['question_texte'], $variables, $variables_raw);
            }

            //
            // Ajouter les points de la question aux points de l'evaluation
            //

            if ( ! $q['sondage'])
            {
                $points_evaluation = $points_evaluation + $q['question_points'];
            }

            // ----------------------------------------------------------------
            //
            // Question a developpement       (TYPE 2)
            // Question a developpement court (TYPE 12)
            //
            // ----------------------------------------------------------------

            $questions_types = array(2, 12);

            if (in_array($q['question_type'], $questions_types))
            {
                continue;
            }

            // ----------------------------------------------------------------
            //
            // Question a repondre par televersement de documents (TYPE 10)
            //
            // ----------------------------------------------------------------

            $questions_types = array(10);

            if (in_array($q['question_type'], $questions_types))
            {
                // Extraire les documents

                $documents[$question_id] = $this->Document_model->extraire_documents_soumission(
                    $question_id, $evaluation_id, $evaluation_reference, 
                    array(
                        'session_id' => $session_id
                    )
                );

                continue;
            }

            // ----------------------------------------------------------------
            //
            // Question a choix unique (TYPE 1)
            // Question a choix unique par equations (TYPE 3)
            // Question a choix multiples (TYPE 4)
            // Question a choix multiples stricte (TYPE 11)
            //
            // ----------------------------------------------------------------

            $questions_types = array(1, 3, 4, 11);

            if (in_array($q['question_type'], $questions_types))
            {
                $r = $this->Reponse_model->lister_reponses($question_id);

                // 
                // Ceci a deja ete verifie dans le VIE (4 avril 2020).
                //

                if ( ! $previsualisation)
                {
                    if (empty($r))
                    {
                        // Il n'y a aucune reponse trouvee pour cette question.

                        generer_erreur('GE67123', 
                            "Il y a une question qui ne possède aucune réponse.<br />
                             Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                        return;
                    }

                    elseif (in_array($q['question_type'], array(3, 4, 11)) && count($r) < 2)
                    {
                        // Il n'y a qu'une seule reponse trouvee pour cette question.
                        // (Exception : les questions de type 1)

                        generer_erreur('GE67124', 
                            "Il y a une question qui ne possède qu'une seule réponse.<br />
                            Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                        return;
                    }

                    else
                    {
                        // Verifier qu'au moins une reponse correcte est selectionnee.

                        $reponse_correcte_presente = FALSE;

                        foreach($r as $rep)
                        {
                            if ($rep['reponse_correcte'])
                            {
                                $reponse_correcte_presente = TRUE;
                                break;
                            }
                        }

                        // Les questions a choix multiples peuvent ne pas avoir de reponse correcte.
                        if ( ! $reponse_correcte_presente && $q['question_type'] != 4)
                        {
                            // Il n'y a aucune reponse correcte presente.

                            generer_erreur('GE67125', 
                                "Il y a une question dont il n'y a aucune réponse correcte.<br />
                                 Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                            return;
                        }
                    }
                }

                //
                // Remplacer les variables dans les reponses SANS equation, si necessaire.
                //

                if ( ! empty($variables))
                { 
                    foreach($r as $reponse_id => $rep)
                    {
                        if ($rep['equation'])
                            continue;

                        if (preg_match('/\<var\>/', $rep['reponse_texte']))
                        {
                            foreach ($variables as $var => $var_val)
                            {
                                $var_val = str_replace('.', ',', $var_val);
                                $rep['reponse_texte'] = str_replace('<var>' . $var . '</var>', $var_val, $rep['reponse_texte']);
                            }
                            
                            $r[$reponse_id]['reponse_texte'] = $rep['reponse_texte'];
                        }
                    }
                } // if ! empty($variables)

                //
                // Rendre la presentation des reponses aleatoires
                // 

                if ($q['reponses_aleatoires'])
                {
                    $r_ordre_original = $r;

                    shuffle_assoc($r);

                    if ( ! empty($reponses_ordre) && is_array($reponses_ordre))
                    {
                        // Regenerer les reponses comme si elles venaient de la base de donnees pour pouvoir
                        // les comparer avec celles provenant vraiment de la base de donnees

                        $reponse_ids_question = $reponses_ordre[$question_id];
                        asort($reponse_ids_question);
                        $reponse_ids_question = array_values($reponse_ids_question);

                        // Verifier que les reponses de l'ordre correspondent aux reponses de l'evaluation.
                        // Si c'est le cas, reordonner les reponses.
                        // Dans le cas contraire, utiliser le nouvel ordre genere par shuffle_assoc.

                        if (array_column($r_ordre_original, 'reponse_id') == $reponse_ids_question)
                        {
                            $r_ordre_n = array();

                            foreach($reponses_ordre[$question_id] as $r_id)
                            {
                                $r_ordre_n[$r_id] = $r_ordre_original[$r_id];
                            }

                            $r = $r_ordre_n;
                        }
                    }
                }

                //
                // Inserer les reponses de cette question dans le tableau des reponses
                // 

                $reponses[$question_id] = $r;

                //
                // Ajouter les reponses generees precedemment
                //

                if ($q['question_type'] == 3)
                {
                    foreach($r as $reponse_id => $rep)
                    {
                        if (array_key_exists($reponse_id, $reponses_generees))
                        {
                            $reponses[$question_id][$reponse_id]['reponse_equation'] = $reponses_generees[$reponse_id];
                        }
                        else
                        {
                            generer_erreur(
                                'GE561266', 
                                "Il y a aucune réponse générée pour l'une de vos équations de la question ID " . $question_id . ".<br />
                                 Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur."
                            );
                            return;
                        }
                    }
                }

            } // questions types : 1, 3, 4

            // ----------------------------------------------------------------
            //
            // Questions a reponse numerique entiere      (TYPE 5)
            // Questions a reponse numerique              (TYPE 6)
            // Questions a reponse litterale courte       (TYPE 7)
            // Questions a reponse numerique par equation (TYPE 9)
            //
            // ----------------------------------------------------------------

            if (in_array($q['question_type'], array(5, 6, 7, 9)))
            {
                $r = $this->Reponse_model->lister_reponses($question_id);

                if ( ! $previsualisation && empty($r))
                {
                    // Il n'y a aucune reponse trouvee pour cette question.

                    generer_erreur('GE67126', 
                        "Il y a une question qui ne possède aucune réponse (Question ID : " . $question_id . ").<br />
                         Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                    return;
                }

                $reponses[$question_id] = $r;

            } // question_type 5, 6, 7, 9
        }

        //
        // Enregistrer l'ordre des reponses dans les traces
        //

        if (empty($reponses_ordre))
        {
            // Il s'agit du premier chargement, donc il faut enregistrer l'ordre des questions

            if ( ! empty($reponses) && is_array($reponses))
            {
                foreach($reponses as $question_id => $reponse)
                {
                    $reponse_ids = array_column($reponse, 'reponse_id');

                    if ( ! array_key_exists($question_id, $reponses_ordre))
                    {
                        $reponses_ordre[$question_id] = array();
                    }

                    $reponses_ordre[$question_id] = $reponse_ids;
                }
                
                $traces_arr['reponses_ordre'] = $reponses_ordre;
            } 
        }

        //
        // Session
        //
        // La session ID a deja ete determinee plus haut.
        //

        // $session_id = $session_id ?: session_id();

        //
        // Traces
        //

        if ( ! $previsualisation)
        {
            // Ecrire les traces avec les derniers changements

            $this->Evaluation_model->ecrire_traces($evaluation_reference, $evaluation_id, $traces_arr, 
                array(
                    'session_id' => $session_id
                )
            );
        }
        else
        {
            // Tenter d'extraire rel_evaluation si cette evaluation est deja en ligne, 
            // ceci afin de reproduire au mieux une previsualisation fidele

            $rel_evaluation = $this->Evaluation_model->evaluation_parametres_previsualisation(
                array(
                    'evaluation_id' => $evaluation['evaluation_id'],
                    'semestre_id'   => $this->Semestre_model->semestre_en_vigueur($this->groupe_id, $evaluation['enseignant_id'])['semestre_id'] ?? NULL
                )
            );
        }

        //
        // Ajouter des informations a l'evaluation
        //

        if (is_array($rel_evaluation))
        {
			$evaluation['fin_epoch'] = $rel_evaluation['fin_epoch'];

            // Ajouter le temps limite

            $evaluation['temps_limite'] = $rel_evaluation['temps_limite'];

            // Ajouter le temps supplementaire, si present

            $temps_limite = $rel_evaluation['temps_limite'];

            if ($this->est_etudiant)
            {
                $temps_supp = $this->Etudiant_model->extraire_etudiant_temps_supp($this->etudiant['numero_da']);

                if ($temps_supp > 0)
                {
                    $evaluation['temps_limite'] = $rel_evaluation['temps_limite'] + ($rel_evaluation['temps_limite'] * $temps_supp/100);
                } 
			}

			// Tronquer le temps limite selon la planification de la fin de l'evaluation par l'enseignant, si applicable

			if ( 
				! empty($evaluation['fin_epoch']) && 
				  ctype_digit($evaluation['fin_epoch']) && 
				  array_key_exists('soumission_debut_epoch', $traces_arr) &&
				  ctype_digit($traces_arr['soumission_debut_epoch'])
			   )
			{
				$temps_limite_possible = floor(($evaluation['fin_epoch'] - $traces_arr['soumission_debut_epoch'] + 60) / 60);

				if ($temps_limite_possible < $evaluation['temps_limite'])
				{
					// Fixer le temps limite reel

					$evaluation['temps_limite'] = $temps_limite_possible;
				}
			}
        }

        //
        // Preparer les donnees pour l'affichage
        //
        
		$this->data = array_merge(
			array(
				'semestre_id'          => $this->Semestre_model->semestre_en_vigueur($this->groupe_id, $evaluation['enseignant_id'])['semestre_id'] ?? NULL,
				'ecole'		           => $ecole,
                'groupe'               => $groupe,
                'cours'                => $cours,
                'enseignant'           => $enseignant,
                'enseignant_id'        => $evaluation['enseignant_id'],
                'session_id'           => $session_id,
                'evaluation'           => $evaluation,
                'evaluation_reference' => $evaluation_reference,
                'rel_evaluation'       => $rel_evaluation ?? array(),
                'questions'            => $questions,
                'questions_choisies'   => $questions_choisies_encryptees,
                'images'		       => $images,
                'reponses'             => $reponses,
                'documents'            => $documents,
                'documents_mime_types' => $documents_mime_types,
                'lab'                  => $lab,         
                'points_evaluation'    => $points_evaluation,
                'en_direct'            => FALSE, // Un flag pour permettre la visualisation en direct par l'enseignant
                'previsualisation'     => $previsualisation,
                'previsualisation_etudiante' => $previsualisation_etudiante ?? FALSE,
                'iteration'            => $iteration,
                'variables'            => (empty($variables) ? FALSE : TRUE), // des variables sont prensentes ?
                'variables_choisies'   => (empty($variables) ? NULL : $variables_choisies_encryptees),
                'traces'			   => $traces_arr,
                'soumission_debut_epoch'  => (is_array($traces_arr) && array_key_exists('soumission_debut_epoch', $traces_arr)) ? $traces_arr['soumission_debut_epoch'] : $this->now_epoch,
                'soumission_deja_envoyee' => $soumission_deja_envoyee
			), $this->data
        );

        if ($lab)
        {
            $this->data = array_merge(
                array(
                    'lab_parametres'       => $lab_parametres,
                    'lab_valeurs'          => $lab_valeurs,
                    'lab_points'		   => $lab_points,
                    'lab_points_tableaux'  => $lab_points_tableaux,
                    'lab_vue'              => $lab_vue,
                    'lab_prefix'           => $lab_prefix,
                    'lab_eleves'           => $lab_eleves,
                    'lab_cours_groupe'	   => $lab_cours_groupe,
                    'montre_tags'          => ($previsualisation && $this->uri->segment(4) != 'etudiant') ? TRUE : FALSE
                ), $this->data
            );

            // $this->data['lab_vue_html'] = $this->load->view('laboratoire/' . $groupe['sous_domaine'] . '/' . $lab_vue, $this->data, TRUE);
        }

        //
        // Log
        //

        if (@$chargement_premiere_fois)
        {
            $action       = "L'étudiant charge l'évaluation pour la première fois.";
            $action_court = 'evaluation_chargement_premiere_fois'; 
        }
        else
        {
            $action       = "L'étudiant charge l'évaluation.";
            $action_court = 'evaluation_chargement'; 
        }

        $this->Evaluation_model->ecrire_activite_evaluation(
            array(
                'action'                => $action,
                'action_court'          => $action_court,
                'evaluation_id'         => $evaluation_id,
                'evaluation_reference'  => $evaluation_reference
            )
        );

        //
        // Afficher l'evaluation
        //

        $this->_affichage('evaluation');
    }

    /* ------------------------------------------------------------------------
     *
     * Soumission d'une evaluation
     *
     * ------------------------------------------------------------------------
     *
     * Version 2 : 2021-01
     *
     * ------------------------------------------------------------------------ */
    public function soumission()
    {
		/*
		array
		Array
		(
			[etudiant_id] => 1
			[evaluation_id] => 577
			[enseignant_id] => 1
			[evaluation_reference] => jqvber
			[groupe_id] => 1
			[semestre_id] => 23
			[questions] => 1
			[questions_choisies] => b609c5b545f25adcbf901fa7dbf7aab53c049808c1f836001fa5c167579111ad4cc3f88f8b3eebff79337b806e1721b01dd7888455f5542e59376a6e9f12e708ShT5dpuU3Bwc3aC4p6HM+KX6Ni8+T7xoIaKVcmpEk1CkrQ==
			[variables_choisies] => 58c82e5e30d6d507cd5f9a76270943f35b820438c06a6615cad95df27288b70efa7ff1ae7ae55eea1e5f124dccaacafca5c137850b9aa7f4ea13c2349de1a51dhFA5S83KwghK6b2pVzorbxiX2j/l3caKC9GiR3qqav8mtoujxME3mw==
			[confirmation1_q] => J'ai bien vérifié toutes mes réponses.
			[confirmation1] => on
			[confirmation2_q] => Je suis bien informé que seul le premier envoi sera pris en compte.
			[confirmation2] => on
			[session_id] => mbo6o3dh07jqsqne8nm62ids2t7bk0im
			[soumission_debut_epoch] => 1609881626
			[previsualisation] => 0
			[temps_ecoule] => 
			[question_21510] => 85955
			[prenom_nom] => Mario Lemieux
			[numero_da] => 200841857
		)
		*/

		//
		// Verifier les prerequis et les conditions pour soumettre une evaluation
        //
        
        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id', 'enseignant_id', 'groupe_id', 'semestre_id', 'questions')) === FALSE)
        {
			//
            // Ceci se produit lorsque des etudiants tentent d'acceder cette page directement.
			//	

            redirect(base_url());
            exit;
        }

		//
        // Validation du formulaire
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
            {
                case 'evaluation_reference' :

                    if ( ! (array_key_exists('previsualisation', $post_data) && $post_data['previsualisation']))
                    {
                        $validation_rules = 'required';
                    }
                    break;

                case 'questions_choisies' :
				case 'session_id' :
                case 'soumission_debut_epoch' :
				case 'previsualisation' :
					$validation_rules = 'required';
					break;

				case 'prenom_nom' :
				case 'numero_da' :
					if ( ! $post_data['lab'])
					$validation_rules = 'trim|required';
					break;

				case 'lab_partenaire1' :
					if ($post_data['lab']) 
						$validation_rules = 'trim|required';
					break;

				case 'confirmation1' :
					// J'ai bien verifie toutes mes reponses.
					// $validation_rules = 'required|regex_match[/on/]';
					break;

				case 'confirmation2' :
					// Je suis informe que seul le premier envoi est pris en compte.
					// if ( ! $this->logged_in)
					//	$validation_rules = 'required|regex_match[/on/]';
                    // break;
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
                {
                    $errors[$k] = form_error($k);
                }
            }

            // Cette erreur ne devrait jamais apparaitre a l'etudiant.

            generer_erreur2(
				array(
					'code'  	 => 'GS7878',
					'desc'  	 => "Le formulaire comporte des erreurs de validation.",
					'extra' 	 => $post_data,
					'importance' => 6
                )
            );
            return;
        }

		//
		// Verifier les conditions pour soumettre une evaluation
		//

        //
        // Si cette soumission provient d'un etudiant inscrit, 
		// verifier qu'il est toujours connecte avant de soumettre l'evaluation.
        //
        
        if ( ! empty($post_data['etudiant_id']))
        {
            if (($this->Auth_model->connexion_cookie()) == FALSE)
            {
                log_alerte(
                     array(
                         'code'       => 'ESS655101',
                         'desc'       => "Un étudiant inscrit a tenté d'envoyer son évaluation sans être connecté.",
                         'importance' => 1,
                         'extra'      => 'evaluation_reference = ' . $evaluation_reference .
                                         ', evaluation_id = ' . $evaluation_id, 
                                         ', etudiant_id = '   . $etudiant_id . 
                                         ', prenom_nom = '    . $post_data['prenom_nom'] .
                                         ', numero_da = '     . $post_data['numero_da']
                     )
                 );

                redirect(base_url() . 'connexion');
                exit;
            }
        }

		//
		// Verifier que cette soumission n'a pas deja ete envoyee pour le semestre en vigueur.
		//

		if ( ! $post_data['previsualisation'])
		{
			$soumission_envoyee = $this->Evaluation_model->soumission_envoyee(
				array(
					'session_id'           => $post_data['session_id'],
					'evaluation_reference' => $post_data['evaluation_reference'],
					'evaluation_id'        => $post_data['evaluation_id'],
					'numero_da'            => filter_input(INPUT_POST, 'numero_da', FILTER_SANITIZE_SPECIAL_CHARS)
				)
			);

			if ($soumission_envoyee !== FALSE)
			{
				//
				// La soumission a deja ete envoyee
				//

				//
				// Effacer les traces de l'etudiant
				//

				$this->Evaluation_model->effacer_traces(
					$post_data['evaluation_reference'],
					$post_data['evaluation_id'],
					array(
						'etudiant_id' => $post_data['etudiant_id'],
						'session_id'  => $post_data['session_id']
					)
				);

				log_alerte(
					 array(
						 'code'       => 'GC581191',
						 'desc'       => "Cette soumission a déjà été envoyée.",
						 'importance' => 1,
						 'extra'      => 'evaluation_reference = ' . $post_data['evaluation_reference'] .
										 ', evaluation_id = ' 	   . $post_data['evaluation_id'] . 
										 ', prenom_nom = '  	   . $post_data['prenom_nom'] .
										 ', numero_da = '   	   . $post_data['numero_da'] .
										 ', status = '   	       . ($soumission_envoyee['non_terminee'] ? 'non_terminee' : 'terminee')
					 )
				 );

				//
				// Afficher une page d'erreur selon si la soumission a ete envoyee par l'etudiant ou terminee par l'enseignant.
				//

				if (array_key_exists('non_terminee', $soumission_envoyee) && $soumission_envoyee['non_terminee'] == TRUE)
				{
					$this->_affichage('soumission-deja-envoyee-enseignant');
				}
				else
				{
					$this->_affichage('soumission-deja-envoyee');
				} 

				return;

			} // $soumission_envoyee !== FALSE
		}

		//
		// Verifier, par les traces, que cette soumission n'a pas deja ete envoyee pour le semestre en vigueur.
		//

		if ( ! $post_data['previsualisation'])
        {
            $traces_completes = $this->Evaluation_model->extraire_traces(
                $post_data['evaluation_reference'],
                array(
                    'etudiant_id' => $post_data['etudiant_id'],
                    'semestre_id' => $post_data['semestre_id']
                )
            );

            if ( ! empty($traces_completes['soumission_id']))
            {
                $this->_affichage('evaluation-terminee-vague');
                return;
            }
        }

        //
        // Verifier si cette evaluation est toujours ouverte (active)
        //

        // @TODO

		//
		// Enregistrer la soumission
		//

		$soumission = $this->Soumission_model->enregistrer_soumission_formulaire($post_data);

        if (empty($soumission) || $soumission == FALSE)
        {
            //
            // Cette erreur ne devrait jamais se produire.
            //

            generer_erreur2(
                array(
                    'code'  => 'SEE2000',
                    'desc'  => "Il y a eu un problème avec l'envoi de votre évaluation.",
                    'extra' => 'evaluation_id = ' . $post_data['evaluation_id'],
                    'importante' => 5
                )
            );
            exit;
        }

        //
        // Preparer les donnees pour l'affichage
        //

        $this->data = array_merge($this->data, $soumission);

        /*
        array(
            'prenom_nom' 			  => $post_data['prenom_nom'],
            'numero_da'  			  => $post_data['numero_da'],
            'reference'  			  => $soumission_reference,
            'empreinte'  			  => $this->Evaluation_model->generer_empreinte($soumission_reference),
            'enseignant' 			  => $this->Enseignant_model->extraire_enseignant($post_data['enseignant_id']), // necessaire pour le groupe_id = 0`
            'permettre_visualisation' => $evaluation['permettre_visualisation'],
            'corrections_terminees'   => $corrections_terminees,
            'qr_image'				  => NULL // voir plus bas
        )
        */

        //
        // Preparer les informations pour le QR Code.
        //
        // $qr_data = $reference . ';' . $this->data['empreinte'];
        // $this->data['qr_image'] = (new QRCode)->render($qr_data);
        //

        //
        // Si l'etudiant est inscrit et qu'il s'agit d'une evaluation formative,
        // afficher immediatement la correction.
        //

        if ($this->est_etudiant && $soumission['formative'])
        {
            redirect(base_url() . 'consulter/' . $soumission['soumission_reference']);
            exit;
        }
	
        $this->_affichage('soumission');

        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Montrer l'evaluation en direct d'un etudiant inscrit
     *
     * ------------------------------------------------------------------------ */
    function endirect()
    {
        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        $args = $this->uri->uri_to_assoc(3);

        $etudiant_id          = $args['etudiant'];
        $evaluation_reference = $args['reference'];

        if (($evaluation_meta = $this->Evaluation_model->verifier_evaluation_reference($evaluation_reference)) == FALSE)
        {
			generer_erreur2(
				array(
					'code'  => 'CNSDIR1',
					'desc'  => "Cette évaluation est introuvable.",
					'extra' => 'evaluation_reference = ' . $evaluation_reference
				)
			);

            exit;
        }

        $evaluation_id = $evaluation_meta['evaluation_id'];

        //
        // Extraire les traces de l'etudiant
        //

        if (($traces = $this->Evaluation_model->lire_traces_externe($evaluation_reference, $evaluation_id, $etudiant_id)) === FALSE)
        {
            if ($traces['evaluation_envoyee'])
            {
                generer_erreur2(
                    array(
                        'code'  => 'CNSDIR2',
                        'desc'  => "Cette évaluation a été complétée et envoyée.",
                        'extra' => 'enseignant_id = ' . $this->enseignant_id . ', evaluation_reference = ' . $evaluation_reference
                    )
                );
                exit;
            }
            elseif ($traces['efface'])
            {
                generer_erreur2(
                    array(
                        'code'  => 'CNSDIR21',
                        'desc'  => "Cette évaluation n'a pas été complétée.",
                        'extra' => 'enseignant_id = ' . $this->enseignant_id . ', evaluation_reference = ' . $evaluation_reference
                    )
                );
                exit;
            }

            generer_erreur2(
                array(
                    'code'  => 'CNSDIR22',
                    'desc'  => "Cette évaluation n'a jamais débutée ou est expirée.",
                    'extra' => 'enseignant_id = ' . $this->enseignant_id . ', evaluation_reference = ' . $evaluation_reference
                )
            );
            exit;
        }

        $traces_arr = unserialize($traces);

        //
		// Evaluation
		//

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if (empty($evaluation))
        {
			generer_erreur2(
				array(
					'code'  => 'CNSDIR3',
					'desc'  => "Cette évaluation est introuvable.",
					'extra' => 'evaluation_reference = ' . $evaluation_reference
				)
            );
            exit;
        }

		//
        // Verifier si cette evaluation appartient a cet enseignant.
        //

        if ($this->enseignant['privilege'] < 90)
        {
            if ($this->enseignant_id != $evaluation_meta['enseignant_id'])
            {
                generer_erreur2(
                    array(
                        'code'  => 'CNSDIR4',
                        'desc'  => "Cette évaluation ne vous appartient pas.",
                        'extra' => 'evaluation_reference = ' . $evaluation_reference
                    )
                );
                exit;
            }
        }

		//
        // Verifier si cette evaluation (evaluation_reference) appartient a ce groupe.
		//

		$groupe = $this->Groupe_model->extraire_groupe(array('evaluation_id' => $evaluation_id));

        if ($evaluation['groupe_id'] != $this->groupe_id)
        {
			generer_erreur2(
				array(
					'code'  => 'CNSDIR5',
					'desc'  => "Cette évaluation en cours de rédaction n'appartient pas à ce groupe.",
					'extra' => 'evaluation_reference = ' . $evaluation_reference
				)
            );
            exit;
        }

        //
        // Extraire les informations rel_evaluations
        //

        $rel_evaluation = $this->Evaluation_model->verifier_evaluation_reference($evaluation_reference);

        //
        // Ceci pour afficher la barre d'information
        //

        $this->data['evaluation_details'] = array(
            'enseignant_nom'    => $evaluation['enseignant_nom'],
            'enseignant_prenom' => $evaluation['enseignant_prenom'],
            'enseignant_genre'  => $evaluation['enseignant_genre']
        );

		//
		// Ecole
        //

        $ecole = $this->Ecole_model->extraire_ecole(array('ecole_id' => $this->ecole_id));

        //
        // Enseignant
        //

        $enseignant = $this->Enseignant_model->extraire_enseignant($evaluation_meta['enseignant_id']);

        //
        // Etudiant
        //

        $etudiant = $this->Etudiant_model->extraire_etudiant($etudiant_id);

        $traces_arr['nom']       = $etudiant['prenom'] . ' ' . $etudiant['nom'];
        $traces_arr['numero_da'] = $etudiant['numero_da'];

		//
		// Cours
		//

		$cours = $this->Cours_model->extraire_cours(array('evaluation_id' => $evaluation_id));

        //
        // Blocs
        //

        $blocs    = $this->Question_model->extraire_blocs($evaluation_id);
        $bloc_ids = array_keys($blocs);

        if ( ! empty($blocs))
        {
            $blocs_questions = $this->Question_model->lister_questions_dans_blocs($bloc_ids, array('actif' => TRUE));
        }

        //
        // Ajuster le niveau de dangerosite des fichiers acceptes en televersement
        //

        $documents_mime_types = json_encode($this->config->item('documents_mime_types'));

        if ( ! ($this->config->item('permettre_fichiers_dangereux') && $enseignant['permettre_fichiers_dangereux']))
        {
            $dmt = array();

            foreach($this->config->item('documents_mime_types_properties') as $t => $prop)
            {
                if ($prop['danger'] === FALSE)
                {
                    $dmt[] = $t;
                }
            }

            $documents_mime_types = json_encode($dmt);
        }

        //
        // Laboratoire
        //

        $lab = $evaluation['lab'];

        if ($lab)
        {
            $lab_parametres = ! empty($evaluation['lab_parametres']) ? json_decode($evaluation['lab_parametres'], TRUE) : array();
            $lab_valeurs    = ! empty($evaluation['lab_valeurs']) ? json_decode($evaluation['lab_valeurs'], TRUE) : array();
            $lab_points     = ! empty($evaluation['lab_points']) ? json_decode($evaluation['lab_points'], TRUE) : array();
            $lab_prefix     = $evaluation['lab_prefix'];
            $lab_vue        = $evaluation['lab_vue'];

            //
            // Extraire la liste des eleves du laboratoire
            //
            
            $lab_eleves = array();
            $lab_cours_groupe = 0;

            $etudiant_numero_da = $traces_arr['numero_da'] ?: NULL;

            if ($etudiant_numero_da)
            {
                $lab_liste_eleves_etudiant = liste_eleves_groupe_etudiant(
                    $etudiant_numero_da, 
                    array(
                        'semestre_id'	=> $this->semestre_id,
                        'enseignant_id' => $evaluation['enseignant_id'],
                        'cours_id'		=> $evaluation['cours_id']
                    )
                );

                $lab_eleves = $lab_liste_eleves_etudiant['liste_eleves_cours'];
                $lab_cours_groupe = $lab_liste_eleves_etudiant['eleve_cours_groupe'];
            }

            // 
            // Organiser les points des champs du laboratoire en ordre de numero de tableau
            //

            $lab_points_tableaux = generer_lab_points_tableaux($lab_points);
            $lab_points_totaux   = $lab_points_tableaux['points_totaux'];
        }


		//
		// Questions
        //

        $questions = array();

        //
        // Lecture des traces
        // 

        $questions_choisies = $traces_arr['questions_choisies'];
        $questions          = $this->Question_model->lister_questions($evaluation_id, array('question_ids' => $questions_choisies));

        if ( ! empty($questions) && is_array($questions))
        {
            // Est-ce que l'enseignant veut presenter les questions aleatoirement ?

            if ($evaluation['questions_aleatoires'])
            {
                // Les questions doivent etre reordonnees selon l'ordre des questions choisies lors d'un chargement precedent.

                $questions_tmp = array();

                foreach($questions_choisies as $q_id)
                {
                    $questions_tmp[$q_id] = $questions[$q_id];
                }

                $questions = $questions_tmp;
            }
        }

        //
        // Blocs & Questions : Ajuster les points selon les points des blocs
        //

        if ( ! empty($blocs))
        {
            foreach($questions as $question_id => $q)
            {
                if (empty($q['bloc_id']))
                    continue;
    
                if ( ! array_key_exists($q['bloc_id'], $blocs))
                    continue;

                $questions[$question_id]['question_points'] = $blocs[$q['bloc_id']]['bloc_points'];
            }
        }

        //
        // Images (associees aux questions)
        //

		$images = $this->Document_model->extraire_images(array_keys($questions));

		//
        // Variables 
        //
        
        $variables = array();

        // Information sur les variables

        $variables_raw   = $this->Evaluation_model->extraire_variables($evaluation_id);
        $plus_petit_cs   = 999; // necessaire pour determiner le plus petit CS des variables
        $iteration       = 0;   // iteration de securite pour eviter une boucle infinie

        //
        // Extraire les variables des traces ou de la session, selon le cas
        //

        if ( array_key_exists('variables', $traces_arr) && ! empty($traces_arr['variables']))
        {
            $variables = $traces_arr['variables'];
        }

        //
        // Iterer pour generer des reponses uniques aux questions a coefficients variables
        //

        while ($iteration < 50)
        {
            $iteration++;

            // Verifier si des variables sont presentes
            
            if ( ! empty($variables_raw) && is_array($variables_raw))
            {
                // Verifier si les variables sont deja presentes dans les traces ou la session de l'etudiant

                if ( ! empty($variables))
                {
                    // Verifier si toutes les variables sont presentes

                    $labels = array_keys($variables_raw);

                    foreach($labels as $label)
                    {
                        if ( ! array_key_exists($label, $variables))
                        {
                            $variables = array();
                            break;
                        }
                    }
                }

                if (empty($variables))
                {
                    // Les valeurs des variables n'existent pas dans la session de l'etudiant, il faut les creer et les ecrire dans la session de l'etudiant.

                    // $variables = $this->_determiner_coefficients_variables($variables_raw, $previsualisation);
                    $variables = determiner_valeurs_variables($variables_raw);
                }

                //
                // Determiner le plus petit CS de l'operation pour ajuster la reponse de l'equation (si necessaire)
                //

                foreach($variables as $label => $v)
                {
                    if (cs($v) < $plus_petit_cs)
                    {
                        $plus_petit_cs = cs($v);
                    }
                }

            } // if ! empty($variables_raw)

            //
            // Verifier l'unicite des reponses generees par les equations (questions a coefficients variables)
            // qui seront presentees a l'etudiant.
            // 
            // Dans la function verifier l'integrite d'une evaluation, on fait une simulation de reponses possibles
            // sur un certain nombre d'iterations. Maintenant, on genere les vrais reponses pour l'evaluation,
            // donc on doit les verifier a nouveau.
            //
            // Si elles ne sont pas uniques, regenerer les variables et recommencer l'iteration.
            //

            // Les reponses sont acceptees si elles sont uniques. On presume qu'elles le seront.
            $reponses_acceptees = TRUE;

            // Les reponses generees (index: reponse_id)
            $reponses_generees = array();

            if ( ! empty($questions) && is_array($questions))
            {
                foreach($questions as $question_id => $q)
                {
                    if ($q['question_type'] != 3)
                    {
                        // Ceci n'est pas une question a coefficients variables.
                        continue;
                    }

                    $reponses = $this->Reponse_model->lister_reponses($question_id);
                    $valeurs  = array();

                    foreach($reponses as $reponse_id => $r)
                    {
                        if ( ! $r['equation'])
                        {
                            // Cette reponse n'est pas une question.
                            continue;
                        }

                        try 
                        {
                            $resolu = Parser::solve(str_replace(',', '.', $r['reponse_texte']), $variables); 
                        } 
                        catch (Exception $e) 
                        {
                            generer_erreur('GE481235', "Il y a un problème avec votre équation, vérifiez le message suivant :<br /><br /><pre>" . $e->getMessage() . "</pre>");
                            return;
                        }

                        // Ne pas considerer les CS si CS == 99.

                        if ($r['cs'] != 99)
                        {
                            if ( ! empty($r['cs']))
                            {
                                $resolu = cs_ajustement($resolu, $r['cs']);
                            }
                            elseif ($plus_petit_cs < 999)
                            {
                                $resolu = cs_ajustement($resolu, $plus_petit_cs);
                            }
                        }

                        $resolu = str_replace('.', ',', $resolu);

                        $valeurs[] = $resolu;
                        $reponses_generees[$reponse_id] = $resolu;
                    }

                    if (empty($valeurs))
                    {
                        generer_erreur2(
                            array(
                                'code'  => 'GE481299.2', 
                                'desc'  => "Il y a un problème avec votre équation car aucune réponse n'a pu être générée.",
                                'extra' => 'enseignant_id = ' . $this->enseignant_id . ', evaluation_id = ' . $evaluation_id
                            )
                        );
                        return;
                    }

                    // Verifier l'unicite des reponses par comptage.

                    if (count(array_unique($valeurs)) < count($valeurs))
                    {
                        // Les reponses ne sont pas uniques

                        $reponses_acceptees = FALSE;

                        // Effacer les variables
                        $variables       = array();
                        $variables_eleve = array();
                        $_SESSION[$session_item_nom] = '';

                        // Reseeder le random
                        srand(time() + $iteration);

                        break; // sort de la loop foreach
                    }

                } // foreach $questions

            } // is_array && ! empty($questions)

            // 
            // Enregistrer les variables dans les traces de l'etudiant
            // (sauf pour la previsualisation)
            //

            if ($reponses_acceptees)
            {
                if ( ! empty($variables)) 
                {
                    $variables_choisies_encryptees = $this->encryption->encrypt(serialize($variables));

                    // Il faut absoluement ecrire les variables dans la session, MEME en mode previsualisation (!)
                    // meme si celles-ci ne seront pas lu de la session pour les enseignants.

                    $traces_arr['variables'] = $variables;

                    // $this->Evaluation_model->ecrire_traces($evaluation_reference, $evaluation_id, $traces_arr, $session_id);
                }

                break; // sortir de la loop while
            }

        } // while

        // 
        // Il n'a pas ete possible de generer des reponses uniques base sur vos variables.
        //

        if ( ! $reponses_acceptees)
        {
            generer_erreur(
                'GE891249', 
                "Il n'a pas été possible de générer des réponses uniques basées sur le choix de vos variables, de vos équations, ou de vos chiffres significatifs.",
                array('importance' => 3)
            );
            return;
        }


        //
        // Extraire l'ordre des reponses des traces, s'il ne s'agit pas du premier chargement
        //

        $reponses_ordre = array();

        if (array_key_exists('reponses_ordre', $traces_arr) && ! empty($traces_arr['reponses_ordre']))
        {
            $reponses_ordre = $traces_arr['reponses_ordre'];
        }

		//
		// Points de l'evaluation
		//

        $points_evaluation = 0;

		if ($lab)
		{
			$points_evaluation += $lab_points_totaux;
		}

		//
		// Validation des questions et association des reponses
		//

        $reponses = array();     // contient les reponses de toutes les questions
        $documents = array();    // contient les documents des questions de type 10

		foreach($questions as $question_id => $q)
        {
            //
            // Tous les types de questions
            //

            //
            // Remplacer les variables dans le texte des questions par leur valeur numerique.
            //

            if ( ! empty($variables)) 
            {
                foreach ($variables as $var => $var_val)
                {
                    $var_ns = $variables_raw[$var]['ns'];
                    $var_cs = $variables_raw[$var]['cs'];

                    $q_tmp = json_decode($questions[$question_id]['question_texte']) ?: $questions[$question_id]['question_texte'];

                    if ($var_ns)
                    {
                        $var_val = ns_format($var_val, FALSE);
                        $var_val = cs_ajustement($var_val, $var_cs);
                    }
                    elseif ($var_cs)
                    {
                        // Ceci est maintenant ajuste directement lors de la determination de la valeur des variables (2020-05-17)
                        // $var_val = cs_ajustement($var_val, $var_cs);
                        $var_val = str_replace('.', ',', $var_val);
                    }
                    
                    $var_val = str_replace('.', ',', $var_val);

                    $q_tmp = str_replace('<var>' . $var . '</var>', $var_val, $q_tmp);

                    $questions[$question_id]['question_texte'] = json_encode($q_tmp);
                }
            }

            //
            // Ajouter les points de la question aux points de l'evaluation
            //

            if ( ! $q['sondage'])
            {
                $points_evaluation = $points_evaluation + $q['question_points'];
            }

            // ----------------------------------------------------------------
            //
            // Question a developpement       (TYPE 2)
            // Question a developpement court (TYPE 12)
            //
            // ----------------------------------------------------------------

            $questions_types = array(2, 12);

            if (in_array($q['question_type'], $questions_types))
            {
                continue;
            }

            // ----------------------------------------------------------------
            //
            // Question a repondre par televersement de documents (TYPE 10)
            //
            // ----------------------------------------------------------------

            $questions_types = array(10);

            if (in_array($q['question_type'], $questions_types))
            {
                //
                // Extraire les documents
                //

                $documents[$question_id] = $this->Document_model->extraire_documents_soumission(
                    $question_id, $evaluation_id, $evaluation_reference, 
                    array(
                        'en_direct'   => TRUE,
                        'etudiant_id' => $etudiant_id
                    )
                );

                continue;
            }

            // ----------------------------------------------------------------
            //
            // Question a choix unique (TYPE 1)
            // Question a choix unique par equations (TYPE 3)
            // Question a choix multiples (TYPE 4)
            // Question a choix multiples stricte (TYPE 11)
            //
            // ----------------------------------------------------------------

            $questions_types = array(1, 3, 4, 11);

            if (in_array($q['question_type'], $questions_types))
            {
                $r = $this->Reponse_model->lister_reponses($question_id);

                // 
                // Ceci a deja ete verifie dans le VIE (4 avril 2020).
                //

                if (empty($r))
                {
                    // Il n'y a aucune reponse trouvee pour cette question.

                    generer_erreur('GE67123', 
                        "Il y a une question qui ne possède aucune réponse.<br />
                         Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                    return;
                }

                elseif (in_array($q['question_type'], array(3, 4, 11)) && count($r) < 2)
                {
                    // Il n'y a qu'une seule reponse trouvee pour cette question.
                    // (Exception : les questions de type 1)

                    generer_erreur('GE67124', 
                        "Il y a une question qui ne possède qu'une seule réponse.<br />
                        Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                    return;
                }

                else
                {
                    // Verifier qu'au moins une reponse correcte est selectionnee.

                    $reponse_correcte_presente = FALSE;

                    foreach($r as $rep)
                    {
                        if ($rep['reponse_correcte'])
                        {
                            $reponse_correcte_presente = TRUE;
                            break;
                        }
                    }

                    // Les questions a choix multiples peuvent ne pas avoir de reponse correcte.
                    if ( ! $reponse_correcte_presente && $q['question_type'] != 4)
                    {
                        // Il n'y a aucune reponse correcte presente.

                        generer_erreur('GE67125', 
                            "Il y a une question dont il n'y a aucune réponse correcte.<br />
                             Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                        return;
                    }
                }

                //
                // Remplacer les variables dans les reponses SANS equation, si necessaire.
                //

                if ( ! empty($variables))
                { 
                    foreach($r as $reponse_id => $rep)
                    {
                        if ($rep['equation'])
                            continue;

                        if (preg_match('/\<var\>/', $rep['reponse_texte']))
                        {
                            foreach ($variables as $var => $var_val)
                            {
                                $var_val = str_replace('.', ',', $var_val);
                                $rep['reponse_texte'] = str_replace('<var>' . $var . '</var>', $var_val, $rep['reponse_texte']);
                            }
                            
                            $r[$reponse_id]['reponse_texte'] = $rep['reponse_texte'];
                        }
                    }
                } // if ! empty($variables)

                //
                // Rendre la presentation des reponses aleatoires
                // 

                if ($q['reponses_aleatoires'])
                {
                    $r_ordre_original = $r;

                    shuffle_assoc($r);

                    if ( ! empty($reponses_ordre) && is_array($reponses_ordre))
                    {
                        // Regenerer les reponses comme si elles venaient de la base de donnees pour pouvoir
                        // les comparer avec celles provenant vraiment de la base de donnees

                        $reponse_ids_question = $reponses_ordre[$question_id];
                        asort($reponse_ids_question);
                        $reponse_ids_question = array_values($reponse_ids_question);

                        // Verifier que les reponses de l'ordre correspondent aux reponses de l'evaluation.
                        // Si c'est le cas, reordonner les reponses.
                        // Dans le cas contraire, utiliser le nouvel ordre genere par shuffle_assoc.

                        if (array_column($r_ordre_original, 'reponse_id') == $reponse_ids_question)
                        {
                            $r_ordre_n = array();

                            foreach($reponses_ordre[$question_id] as $r_id)
                            {
                                $r_ordre_n[$r_id] = $r_ordre_original[$r_id];
                            }

                            $r = $r_ordre_n;
                        }
                    }
                }

                //
                // Inserer les reponses de cette question dans le tableau des reponses
                // 

                $reponses[$question_id] = $r;

                //
                // Ajouter les reponses generees precedemment
                //

                if ($q['question_type'] == 3)
                {
                    foreach($r as $reponse_id => $rep)
                    {
                        if (array_key_exists($reponse_id, $reponses_generees))
                        {
                            $reponses[$question_id][$reponse_id]['reponse_equation'] = $reponses_generees[$reponse_id];
                        }
                        else
                        {
                            generer_erreur(
                                'GE561266', 
                                "Il y a aucune réponse générée pour l'une de vos équations de la question ID " . $question_id . ".<br />
                                 Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur."
                            );
                            return;
                        }
                    }
                }

            } // questions types : 1, 3, 4

            // ----------------------------------------------------------------
            //
            // Questions a reponse numerique entiere      (TYPE 5)
            // Questions a reponse numerique              (TYPE 6)
            // Questions a reponse litterale courte       (TYPE 7)
            // Questions a reponse numerique par equation (TYPE 9)
            //
            // ----------------------------------------------------------------

            if (in_array($q['question_type'], array(5, 6, 7, 9)))
            {
                $r = $this->Reponse_model->lister_reponses($question_id);

                if (empty($r))
                {
                    // Il n'y a aucune reponse trouvee pour cette question.

                    generer_erreur('GE67126', 
                        "Il y a une question qui ne possède aucune réponse (Question ID : " . $question_id . ").<br />
                         Veuillez communiquer avec votre enseignante ou enseignant pour régler cette erreur.");
                    return;
                }

                $reponses[$question_id] = $r;

            } // question_type 5, 6, 7, 9
        }

        //
        // Enregistrer l'ordre des reponses dans les traces
        //

        if (empty($reponses_ordre))
        {
            // Il s'agit du premier chargement, donc il faut enregistrer l'ordre des questions

            if ( ! empty($reponses) && is_array($reponses))
            {
                foreach($reponses as $question_id => $reponse)
                {
                    $reponse_ids = array_column($reponse, 'reponse_id');

                    if ( ! array_key_exists($question_id, $reponses_ordre))
                    {
                        $reponses_ordre[$question_id] = array();
                    }

                    $reponses_ordre[$question_id] = $reponse_ids;
                }
                
                $traces_arr['reponses_ordre'] = $reponses_ordre;
            } 
        }

        //
        // Session
        //

        $session_id = session_id();

        //
        // Activite de l'etudiant pendant la consultation d'une evaluation en direct
        //

        if (
            $this->est_enseignant     && 
            is_numeric($etudiant_id)  && 
            ($this->enseignant['privilege'] > 89 || ($this->enseignant_id == $evaluation['enseignant_id']))
           )
        {
            $activite = $this->Evaluation_model->extraire_activite_evaluation($etudiant_id, $evaluation['ajout_epoch'], date('U'));

            $activite_pertinente = array();
            $fureteurs_desc = array();

            if ( ! empty($activite))
            {
                foreach($activite as $a)
                {
                    if ( ! preg_match('/evaluation\/' . $evaluation_reference . '/', $a['uri']))
                        continue;
                    
                    if (empty($a['fureteur_id']))
                    {
                        $a['fureteur_id'] = hash('sha256', $a['plateforme'] . $a['fureteur']);
                    }

                    $fureteurs_desc[$a['fureteur_id']] = $a['plateforme'] . ', ' . $a['fureteur'] . ( ! empty($a['mobile']) ? ' (' . $a['mobile'] . ')' : '');

                    $activite_pertinente[] = $a;
                    continue;
                }
            }

            // 
            // Registre de l'activite de l'etudiant (version 2)
            //
		
            $activite2 = $this->Evaluation_model->extraire_activite_evaluation_direct(
                array(
					'etudiant_id'		   => $etudiant_id,
					'evaluation_reference' => $evaluation_reference,
                )
            );
        }

        //
        // Ajouter des informations a l'evaluation
        //

        if (is_array($rel_evaluation))
        {
            $evaluation['temps_limite'] = $rel_evaluation['temps_limite'];

            // Ajouter le temps supplementaire, si present

            $temps_supp = $this->Etudiant_model->extraire_etudiant_temps_supp($etudiant['numero_da']);

            if ($temps_supp > 0)
            {
                $evaluation['temps_limite'] = $rel_evaluation['temps_limite'] + ($rel_evaluation['temps_limite'] * $temps_supp/100);
            } 
        }

        //
        // Preparer les donnees pour l'affichage
        //

		$this->data = array_merge(
			array(
				'semestre_id'          => $this->Semestre_model->semestre_en_vigueur($this->groupe_id, $evaluation['enseignant_id'])['semestre_id'],
				'ecole'		           => $ecole,
                'groupe'               => $groupe,
                'cours'                => $cours,
                'enseignant'           => $enseignant,
                'enseignant_id'        => $evaluation_meta['enseignant_id'],
                'session_id'           => $session_id,
                'evaluation'           => $evaluation,
                'evaluation_reference' => $evaluation_reference,
                'rel_evaluation'       => $rel_evaluation,
                'questions'            => $questions,
                'questions_choisies'   => NULL,
                'images'		       => $images,
                'reponses'             => $reponses,
                'documents'            => $documents,
                'documents_mime_types' => $documents_mime_types,
                'points_evaluation'    => $points_evaluation,
                'en_direct'            => TRUE,
                'previsualisation'     => FALSE,
                'previsualisation_etudiante' => FALSE,
                'lab'                  => $lab,         
                'iteration'            => $iteration,
                'variables'            => (empty($variables) ? FALSE : TRUE), // des variables sont prensentes ?
                'variables_choisies'   => NULL,
                'traces'			   => $traces_arr,
                'soumission_debut_epoch'  => (is_array($traces_arr) && array_key_exists('soumission_debut_epoch', $traces_arr)) ? $traces_arr['soumission_debut_epoch'] : $this->now_epoch,
                'soumission_deja_envoyee' => FALSE,
                'activite'             => ! empty($activite_pertinente) ? $activite_pertinente : array(),
				'activite2'			   => $activite2 ?? array(),
                'fureteurs_desc'       => ! empty($fureteurs_desc) ? $fureteurs_desc : array(),
			), $this->data
        );

        if ($lab)
        {
            $this->data = array_merge(
                array(
                    'lab_parametres'       => $lab_parametres,
                    'lab_valeurs'          => $lab_valeurs,
                    'lab_points'		   => $lab_points,
                    'lab_points_tableaux'  => $lab_points_tableaux,
                    'lab_vue'              => $lab_vue,
                    'lab_prefix'           => $lab_prefix,
                    'lab_eleves'           => $lab_eleves,
                    'lab_cours_groupe'	   => $lab_cours_groupe,
                ), $this->data
            );
        }

        $this->_affichage('evaluation');
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
            case 'soumission' :
                $this->load->view('evaluation/soumission', $this->data);
                break;

            case 'soumission-deja-envoyee' :
                $this->load->view('evaluation/soumission_deja_envoyee', $this->data);
                break;

            case 'soumission-deja-envoyee-enseignant' :
                $this->load->view('evaluation/soumission_deja_envoyee_enseignant', $this->data);
                break;

			case 'evaluation' :
				$this->load->view('evaluation/evaluation', $this->data);
                break;

            case 'evaluation-erreur' :
				$this->load->view('evaluation/evaluation_erreur', $this->data);
                break;

            case 'evaluation-trouver' :
                $this->load->view('evaluation/evaluation_trouver', $this->data);
                break;

            case 'evaluation-chargement-abusif' :
                $this->load->view('evaluation/evaluation_chargement_abusif', $this->data);
                break;

            case 'evaluation-terminee-enseignant' :
                $this->load->view('evaluation/evaluation_terminee_enseignant', $this->data);
                break;

            case 'evaluation-terminee-vague' :
                $this->load->view('evaluation/evaluation_terminee_vague', $this->data);
                break;

            case 'evaluation-terminee-enseignant-abruptement' :
                $this->load->view('evaluation/evaluation_terminee_enseignant_abruptement', $this->data);
				break;

            case 'evaluation-terminee-enseignant-non-inscrit' :
                $this->load->view('evaluation/evaluation_terminee_enseignant_non_inscrit', $this->data);
				break;

            case 'evaluation-terminee-lab-partenaire' :
                $this->load->view('evaluation/evaluation_terminee_lab_partenaire', $this->data);
                break;

			case 'evaluation-temps-limite-confirmation' :
				$this->load->view('evaluation/evaluation_temps_limite_confirmation', $this->data);
				break;

			default :
                echo "La vue n'a pu être chargée correctement.";
				break;
		}

        $this->load->view('commons/footer');
	}
}
