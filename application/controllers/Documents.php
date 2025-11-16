<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * DOCUMENTS 
 *
 * ============================================================================ */

class Documents extends MY_Controller 
{
    public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        $this->load->model('Document_model');
        $this->load->helper(array('file', 'string'));
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

        $post_data = catch_post();

        foreach($post_data as $k => $v)
        {
            switch($k)
            {
				case 'category':
					$validation_rules = 'required';
                    break;

                case 'id':
					$validation_rules = 'required|numeric';
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
		_FILES['upload_file']:
        [name] => 011.JPG
        [type] => image/jpeg
        [tmp_name] => /tmp/phpY55ZvQ
        [error] => 0
        [size] => 56288
        */

		/*
		[file_name] => 8_zbrkultr.jpg
		[file_type] => image/jpeg
		[file_path] => /var/www/kovao.com/chimie/documents/
		[full_path] => /var/www/kovao.com/chimie/documents/8_zbrkultr.jpg
		[raw_name] => 8_zbrkultr
		[orig_name] => 8_zbrkultr.jpg
		[client_name] => IMG_2840.jpg
		[file_ext] => .jpg
		[file_size] => 840.62
		[is_image] => 1
		[image_width] => 3902
		[image_height] => 1226
		[image_type] => jpeg
		[image_size_str] => width="3902" height="1226"
		*/

        $config['upload_path'] = $this->config->item('documents_path');
        $config['max_size'] = 10240; // in KB

        //
        // Question (image)
        //

		if ($post_data['category'] == 'question')
		{
			$question_id = $post_data['id'];

            $prefix = ($this->is_DEV ? 'dev_' : '') . 'e' . $this->ecole_id . 'g' . $this->groupe_id;

        	$config['file_name'] = $prefix . '_' . $this->now_epoch . '_' . strtolower(random_string('alpha', 8));
			$config['allowed_types'] = 'gif|jpeg|jpg|png';
        }

        //
        // Liste d'eleves (txt)
        //

		elseif ($post_data['category'] == 'liste')
		{
			$semestre_id = $post_data['id'];

        	$config['file_name']     = 'e' . $this->enseignant['enseignant_id'] . 's' . $semestre_id . '_' . strtolower(random_string('alpha', 8));
			$config['allowed_types'] = 'csv';
		}

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('upload_file'))
        {
            echo $this->upload->display_errors();
			echo json_encode(FALSE);
			return;
		}

		$filedata = $this->upload->data();

		$result = FALSE;

        //
        // EDITEUR > Question (image)
        //

		if ($post_data['category'] == 'question' && $filedata['is_image'])
        {
			$result = $this->Document_model->ajouter_document($question_id, $filedata);
		}
 
        //
        // CONFIGURATION > Liste d'eleves (txt)
        //

		elseif ($post_data['category'] == 'liste')
        {
            // Determiner la plateforme

            if (array_key_exists('plateforme', $post_data) && ! empty($post_data['plateforme']))
            {
                $plateforme = $post_data['plateforme'];
            }
            elseif (array_key_exists('plateforme', $this->ecole) && ! empty($this->ecole['plateforme']))
            {
                $plateforme = $this->ecole['plateforme'];
            }
            else
            {
                echo "Il n'a pas été possible de déterminer la plateforme.";
                return FALSE;
            }

            // Verifier que les parametres sont presents

            if ( ! array_key_exists('cours_id', $post_data) ||
                 ! array_key_exists('numero_groupe', $post_data)
               )
            {
                echo "Il manque des paramètres.";
                return FALSE;
            }

            // Verifier que numero_groupe est securitaire.

            if (strpos($post_data['numero_groupe'], ' ') || strpos($post_data['numero_groupe'], "'"))
            {
                echo "Le numéro de groupe contient des caractères interdits.";
                return FALSE;
            }

            $post_data['numero_groupe'] = trim($post_data['numero_groupe']);

            // Preparer les parametres

            $params = array(
                'cours_id'      => $post_data['cours_id'],
                'numero_groupe' => $post_data['numero_groupe']
            );
            
            // Ajouter la liste des eleves

            if ($plateforme == 'colnet')
            {
                $result = $this->Document_model->ajouter_eleves_colnet_csv($semestre_id, $filedata, $params);
            }
            elseif ($plateforme == 'omnivox')
            {
                $result = $this->Document_model->ajouter_eleves_omnivox_csv($semestre_id, $filedata, $params);
            }
            else
            {
                echo "La plateforme de votre choix n'est pas supportée.";
                return FALSE;
            }
		}

		if ($result !== FALSE)
        {
            //
            // (!) Important
            //
            // Mettre a jour les comptes autorises avec le nouveau groupe
            //

            if ($post_data['category'] == 'liste')
            {
                $this->Etudiant_model->rafraichir_comptes_autorises($semestre_id, $post_data['cours_id'], $post_data['numero_groupe']);
            }

			echo json_encode($result);
			return;
		}

        echo json_encode(FALSE);
        return;
    }

    /* -------------------------------------------------------------------------------------------- 
     *
     * Modifier caption
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_caption()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
			redirect(base_url());
		}

        if (($post_data = catch_post(array('ids' => array('doc_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		$result = $this->Document_model->modifier_caption($post_data['doc_id'], $post_data['doc_caption']);

		if ( ! $result)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
	}
}

/* End of file documents.php */
/* Location: ./application/controllers/documents.php */
