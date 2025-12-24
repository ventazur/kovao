<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * DOCUMENT MODEL
 *
 * ============================================================================ */

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;		

class Document_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire l'image d'une question
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_image($question_id)
    {
        if (empty($question_id))
        {
            return FALSE;
        }

        $images = $this->extraire_images(array($question_id));

        return array_shift($images);
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire des images (pour les questions)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_images($question_ids = array())
    {
        if (empty($question_ids) || ! is_array($question_ids))
        {
            return FALSE;
        }

        $this->db->from    ('documents as d');
        $this->db->where   ('efface', 0);
        $this->db->where_in('question_id', $question_ids);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'question_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les documents televerses par l'etudiant pour cette evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * - Un enseignant en previsualisation
     * - Un etudiant inscrit
     * - Un etudiant non inscrit
     *
     * !!! Remplacer par la version 2, puis rendre cette-ci desuette.
     * 
     * -------------------------------------------------------------------------------------------- */
    function extraire_documents_etudiants_evaluation($evaluation_reference = NULL, $etudiant_id = NULL, $session_id = NULL, $options = array())
    {
    	$options = array_merge(
            array(
                'evaluation_id' => NULL
           ),
           $options
        );

        $documents = array();

        //
        // Un enseignant en previsualisation
        //

        if (empty($evaluation_reference))
        {
            if ($this->est_enseignant)
            {
                $this->db->where ('d.etudiant_session_id', $session_id ?? session_id());

                // Ca fonctionne sans cela mais pour eviter des problemes
                if ( ! empty($options['evaluation_id']))
                {
                    $this->db->where ('d.evaluation_id', $options['evaluation_id']);
                }
            }
            else
            {
                return $documents;
            }
        }
        else
        {
            //
            // Un etudiant inscrit
            //

            if ($this->est_etudiant)
            {
                $this->db->where ('d.etudiant_id', $etudiant_id ?? $this->etudiant_id);
                $this->db->where ('d.evaluation_reference', $evaluation_reference);
            }

            //
            // Un etudiant non inscrit
            //

            else
            {
                $this->db->where ('d.etudiant_session_id', $session_id ?? session_id());
                $this->db->where ('d.evaluation_reference', $evaluation_reference);
            }
        }

        //
        // Extraire les documents non assignes a une soumission
        //

        $this->db->select ('d.doc_id, d.question_id, d.s3, d.doc_filename, d.doc_tn_filename, d.doc_sha256_file, d.doc_tn_sha256_file, d.ajout_date, d.ajout_epoch');
        $this->db->from   ('documents_etudiants as d');
        $this->db->where  ('d.soumission_id', NULL);
        $this->db->where  ('d.efface', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $documents = $query->result_array();
            $documents = array_keys_swap($documents, 'doc_id');
        }

        return $documents;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les documents televerses par l'etudiant pour cette evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * - Un enseignant en previsualisation
     * - Un etudiant inscrit
     * - Un etudiant non inscrit
     *
     * Version 2 (2020-12-09) : 
     *
     * - Il faut tenir compte des evaluations non terminees mais dont la terminaison est forcee 
     *   par l'enseignant.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_documents_etudiants_evaluation2($evaluation_reference = NULL, $options = array())
    {
    	$options = array_merge(
            array(
                'etudiant_id'   => @$this->etudiant_id ?? NULL,
                'session_id'    => session_id(),
                'evaluation_id' => NULL
           ),
           $options
        );

        $documents = array();

        //
        // Un enseignant en previsualisation
        //

        if (empty($evaluation_reference))
        {
            if ($this->est_enseignant)
            {
                $this->db->where ('d.etudiant_session_id', $options['session_id'] ?? session_id());
                $this->db->where ('d.evaluation_id', $options['evaluation_id']);
            }
            else
            {
                //
                // Il y a un probleme, l'evaluation_reference ne peut pas etre vide 
                // si ce n'est pas un enseignant en previsualisation
                //

                return $documents;
            }
        }

        //
        // Un etudiant
        //

        else
        {
            //
            // Un etudiant inscrit et NON inscrit
            // Chercher par l'evaluation_reference
            //

            $this->db->where ('d.evaluation_reference', $evaluation_reference);

            //
            // Un etudiant inscrit
            //

            if ( ! empty($options['etudiant_id']))
            {
                $this->db->where ('d.etudiant_id', $options['etudiant_id']);
            }

            //
            // Un etudiant non inscrit
            //

            elseif ( ! empty($options['session_id']))
            {
                $this->db->where ('d.etudiant_session_id', $options['session_id']);
            }
        }

        //
        // Extraire les documents non assignes a une soumission
        //

        $this->db->select ('d.doc_id, d.question_id, d.s3, d.doc_filename, d.doc_tn_filename, d.doc_sha256_file, d.doc_tn_sha256_file, d.ajout_date, d.ajout_epoch');
        $this->db->from   ('documents_etudiants as d');
        $this->db->where  ('d.soumission_id', NULL);
        $this->db->where  ('d.efface', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $documents = $query->result_array();
            $documents = array_keys_swap($documents, 'doc_id');
        }

        return $documents;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un document (image)
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_document($question_id, $filedata)
    {
        // Chaque question ne peut avoir qu'un seul document associe. 

        $this->db->from  ('documents as d');
        $this->db->where ('question_id', $question_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();

        if ($query->num_rows())
        {
			// Cette erreur ne devrait jamais se produire.
	
            log_alerte(
				array(
					'code' => 'DOC4512',
					'desc' => "L'usager a tenté de télécharger un deuxième document pour une même question.",
					'importance' => 6
				)
            );

            return FALSE;
        }

        // Hasher le nom du fichier avec la hash du fichier pour etre certain qu'il s'agit d'un fichier unique.

        $hash_file = hash_file('sha256', FCPATH . $this->config->item('documents_path') . $filedata['orig_name']);
        $hash      = hash('sha256', $filedata['orig_name'] . $hash_file);

        $data = array(
            'groupe_id'       => $this->groupe_id,
            'question_id'     => $question_id,
            'doc_filename'    => $this->security->sanitize_filename($filedata['orig_name']),
            'doc_sha256'      => $hash,
            'doc_sha256_file' => $hash_file,
            'doc_filesize'    => $filedata['file_size'] / 1000,
            'doc_is_image'    => $filedata['is_image'],
            'doc_mime_type'   => $filedata['file_type'],
            'doc_size_h'      => $filedata['image_height'],
            'doc_size_w'      => $filedata['image_width'],
            'ajout_date'      => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'     => $this->now_epoch,
            'ajout_par_enseignant_id' => $this->enseignant['enseignant_id']
        );

		if ($this->config->item('utiliser_s3'))
		{
			if (
				$this->_enregistrer_s3(
					array(
						'dossier' => 'evaluations',
						'key'	  => $data['doc_filename'],
						'source'  => $this->config->item('documents_path') . $data['doc_filename']
					)
				)
               )
			{
				$data['s3'] = TRUE;
			}
		}

		$this->db->insert('documents', $data);

		if ( ! $this->db->affected_rows())
		{
			log_alerte(
				array(
					'code' => 'DOC5812',
					'desc' => "Il n'a pas été possible d'enregistrer le document."
				)
			);

			return FALSE;
		}

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire un objet de amazon s3
     *
     * -------------------------------------------------------------------------------------------- */
    function _extraire_s3($options = array())
    {
    	$options = array_merge(
        	array(
				'bucket'   => 'kovao',				// le bucket sur S3
                'dossier'  => NULL,  				// le dossier sur S3
				'key'	   => NULL, 				// la clef sur S3
				'save_dir' => '/tmp/'				// le repertoire ou sauvegarder le fichier (doit se terminer par /)
           ),
           $options
        );

		$classes = array('STANDARD', 'REDUCED_REDUNDANCY', 'STANDARD_IA', 'ONEZONE_IA', 'INTELLIGENT_TIERING', 'GLACIER', 'DEEP_ARCHIVE', 'OUTPOSTS');

		$champs_obligatoires = array('bucket', 'key');

		//
		// Verifier la presence des champs obligatoires
		//

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $options) || empty($options[$c]))
			{
				log_alerte(
					array(
						'code' => 'DOC7987',
						'desc' => "Un champ est manquant pour l'extraction de S3."
					)
				);

				echo json_encode("DOC7987 : Un champ est manquant pour l'extraction de S3.");
				return FALSE;
			}
		}

		//
		// Traiter le dossier
		//

		$dossier = $options['dossier'] ? $options['dossier'] . '/' : NULL;

		//
		// Extraire de S3
		//

		try 
		{
			$s3Client = new S3Client([
				'version' 		=> '2006-03-01',
				'region' 		=> $this->config->item('region', 'amazon'),
				'credentials' 	=> [
					'key'    => $this->config->item('api_key', 'amazon'),
					'secret' => $this->config->item('api_secret', 'amazon'),
				]			
			]);

			$result = $s3Client->getObject([
				'Bucket' 	 	=> $options['bucket'],
				'Key' 		 	=> $dossier . $options['key'],
				'SaveAs'		=> $options['save_dir'] . $options['key']
			]);

			return $result;
		} 
		catch (S3Exception $e) 
		{
			log_alerte(
				array(
					'code'	=> 'DOC8000',
					'desc' 	=> $e->getMessage(),
					'importance' => 3
				)
			);

			return FALSE;
		}

		return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Existence d'un objet dans Amazon s3
	 *
	 * ---------------------------------------------------------------------------------------------
	 *
	 * La verification d'un objet entraine une latence.
     *
     * -------------------------------------------------------------------------------------------- */
    function existe_s3($options = array())
    {
    	$options = array_merge(
        	array(
				'bucket'  => 'kovao',		// le bucket sur S3
                'dossier' => NULL,  		// le dossier sur S3
                'key'	  => NULL, 			// la clef sur S3
                'info'    => FALSE          // retourner les informations sur l'objet
           ),
           $options
        );

		$classes = array('STANDARD', 'REDUCED_REDUNDANCY', 'STANDARD_IA', 'ONEZONE_IA', 'INTELLIGENT_TIERING', 'GLACIER', 'DEEP_ARCHIVE', 'OUTPOSTS');

		$champs_obligatoires = array('bucket', 'key');

		//
		// Verifier la presence des champs obligatoires
		//

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $options) || empty($options[$c]))
			{
				log_alerte(
					array(
						'code' => 'DOC1988',
						'desc' => "Un champ est manquant pour l'existence sur S3."
					)
				);

				echo json_encode("DOC1988 : Un champ est manquant pour l'existence S3.");
				return FALSE;
			}
		}

		//
		// Traiter le dossier
		//

		$dossier = $options['dossier'] ? $options['dossier'] . '/' : NULL;

		//
		// Existence sur S3
        //

		try 
		{
			$s3Client = new S3Client([
				'version' 		=> '2006-03-01',
				'region' 		=> $this->config->item('region', 'amazon'),
				'credentials' 	=> [
					'key'    => $this->config->item('api_key', 'amazon'),
					'secret' => $this->config->item('api_secret', 'amazon'),
				]			
			]);

            if ($s3Client->doesObjectExist($options['bucket'], $dossier . $options['key']))
            {
                return TRUE;
            }

            return FALSE;
		} 

		catch (S3Exception $e) 
		{
			log_alerte(
				array(
					'code'	=> 'DOC9991',
					'desc' 	=> $e->getMessage(),
					'importance' => 3
				)
			);
		}

		return FALSE;
	}
	
    /* --------------------------------------------------------------------------------------------
     *
     * Enregistrer un objet dans Amazon s3
     *
     * -------------------------------------------------------------------------------------------- */
    function _enregistrer_s3($options = array())
    {
    	$options = array_merge(
        	array(
				'acl'	  => 'public-read',	// le controle d'acces a l'objet sur S3
				'bucket'  => 'kovao',		// le bucket sur S3
                'dossier' => NULL,  		// le dossier sur S3
				'key'	  => NULL, 			// la clef sur S3
				'source'  => NULL, 			// le fichier sur le disque
				'classe'  => 'STANDARD_IA',	// la classe de storage
				'effacer' => TRUE			// effacer le fichier du disque apres l'enregistrement sur S3
           ),
           $options
        );

		$classes = array('STANDARD', 'REDUCED_REDUNDANCY', 'STANDARD_IA', 'ONEZONE_IA', 'INTELLIGENT_TIERING', 'GLACIER', 'DEEP_ARCHIVE', 'OUTPOSTS');

		$champs_obligatoires = array('bucket', 'key', 'source');

		//
		// Verifier la presence des champs obligatoires
		//

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $options) || empty($options[$c]))
			{
				log_alerte(
					array(
						'code' => 'DOC7988',
						'desc' => "Un champ est manquant pour l'enregistrement sur S3."
					)
				);

				echo json_encode("DOC7988 : Un champ est manquant pour l'enregistrement S3.");
				return FALSE;
			}
		}
    
		//
		// Verifier que le fichier existe sur le disque
		//

		if ( ! file_exists($options['source']))
		{
			log_alerte(
				array(
					'code' => 'DOC7991',
					'desc' => "Ce fichier n'existe pas sur le disque.",
					'importance' => 3
				)
			);

			echo json_encode("DOC7991 : Ce fichier n'existe pas sur le disque.");
			return FALSE;
		}

        //
        // Ajuster la classe de storage
        //
        // La classe STANDARD_IA charge pour l'acces.
        // La classe STANDARD_IA charge un minimum de 128 Ko de storage meme pour les fichiers plus petits.
        //

        // Tous les fichiers des evaluations doivent avoir la classe STANDARD.

        if ($options['dossier'] == 'evaluations')
        {
            $options['classe'] = 'STANDARD';
        }

        // Tous les thumbnails doivent avoir la classe STANDARD.

        if (preg_match('/_tn\./', $options['key']))
        {
            $options['classe'] = 'STANDARD';
        } 

		//
		// Traiter le dossier
		//

		$dossier = $options['dossier'] ? $options['dossier'] . '/' : NULL;

		//
		// Enregistrer dans S3
		//

		try 
		{
			$s3Client = new S3Client([
				'version' 		=> '2006-03-01',
				'region' 		=> $this->config->item('region', 'amazon'),
				'credentials' 	=> [
					'key'    => $this->config->item('api_key', 'amazon'),
					'secret' => $this->config->item('api_secret', 'amazon'),
				]			
			]);

			$result = $s3Client->putObject([
				'ACL'			=> $options['acl'],
				'Bucket' 	 	=> $options['bucket'],
				'Key' 		 	=> $dossier . $options['key'],
				'SourceFile' 	=> $options['source'],
				'ContentType'	=> mime_content_type($options['source']),
				'StorageClass'  => $options['classe']
			]);

			if ($result['@metadata']['statusCode'] == 200)
			{
				//
				// Effacer le fichier source
				//

				if ($options['effacer'])
				{
                	unlink($options['source']);
				}

				return TRUE;
			}
		} 
		catch (S3Exception $e) 
		{
			log_alerte(
				array(
					'code'	=> 'DOC7999',
					'desc' 	=> $e->getMessage(),
					'importance' => 3
				)
			);
		}

		return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un objet dans amazon s3
     *
     * -------------------------------------------------------------------------------------------- */
    function _effacer_s3($options = array())
    {
    	$options = array_merge(
        	array(
				'bucket'  => 'kovao',				// le bucket sur S3
                'dossier' => NULL,  				// le dossier sur S3
				'key'	  => NULL 					// la clef sur S3
           ),
           $options
        );

		$champs_obligatoires = array('bucket', 'key');

		//
		// Verifier la presence des champs obligatoires
		//

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $options) || empty($options[$c]))
			{
				log_alerte(
					array(
						'code'  => 'DOC0988',
                        'desc'  => "Un champ est manquant pour l'effacement sur S3.",
                        'extra' => "EtudiantID : " . @$this->etudiant_id ?? NULL
					)
				);

				echo json_encode("DOC9988 : Un champ est manquant pour l'effacement S3.");
				return TRUE;
			}
		}

		//
		// Traiter le dossier
		//

		$dossier = $options['dossier'] ? $options['dossier'] . '/' : NULL;

		//
		// Verifier que les developpeurs n'effacent pas un fichier de production.
		//

		if (strpos($options['key'], 'dev_') === FALSE && $this->is_DEV)
		{
			log_alerte(
				array(
					'code' => 'DOC6669',
					'desc' => "Vous ne pouvez pas effacer un fichier de production en mode développement.",
					'extra' => 'key = ' . $dossier . $options['key']
				)
			);

			echo json_encode("DOC6669 : Il est strictement interdit d'effacer un fichier de production en mode développement (" . $dossier . $options['key'] . ")");
			return FALSE;
		}
	
		//
		// Effacement dans S3
		//

		try 
		{
			$s3Client = new S3Client([
				'version' 		=> '2006-03-01',
				'region' 		=> $this->config->item('region', 'amazon'),
				'credentials' 	=> [
					'key'    => $this->config->item('api_key', 'amazon'),
					'secret' => $this->config->item('api_secret', 'amazon'),
				]			
			]);

			$result = $s3Client->deleteObject([
				'Bucket' 	 	=> $options['bucket'],
				'Key' 		 	=> $dossier . $options['key']
			]);

			return TRUE;
		} 
		catch (S3Exception $e) 
		{
			log_alerte(
				array(
					'code'	=> 'DOC9999',
					'desc' 	=> $e->getMessage(),
					'importance' => 3
				)
			);

			return FALSE;
		}

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouer un document a une soumission (des etudiants)
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_document_soumission($filedata, $post_data)
    {
        if (empty($filedata) || empty($post_data))
        {
            return FALSE;
        }

        //
        // Verifier l'existente et la nature des champs obligatoires de $post_data
        //

        if (empty($post_data['question_id']) || empty($post_data['etudiant_session_id']) || empty($post_data['evaluation_id']))
        {
            return FALSE;
        }

        if ( ! is_numeric($post_data['question_id']) || ! is_numeric($post_data['evaluation_id']))
        {
            return FALSE;
        }

        if ( ! $this->est_enseignant)
        {
            if (empty($post_data['evaluation_reference']))
            {
                return FALSE;
            }
        }

        $question_id   = $post_data['question_id'];
        $evaluation_id = $post_data['evaluation_id'];

        //
        // Verifier si le nombre de fichier maximum a ete atteint.
        //

        $this->db->from ('documents_etudiants');
        $this->db->where('question_id', $question_id);
        $this->db->where('efface', 0);

        if ( ! $this->logged_in)
        {
            $this->db->where('etudiant_session_id', $post_data['etudiant_session_id']);
            $this->db->where('evaluation_reference', $post_data['evaluation_reference']);
        }
        else
        {
            if ($this->est_etudiant)
            {
                $this->db->where('etudiant_id', $this->etudiant_id);
                $this->db->where('evaluation_reference', $post_data['evaluation_reference']);
            }

            if ($this->est_enseignant)
            {
                $this->db->where('evaluation_id', $evaluation_id);
                $this->db->where('etudiant_session_id', $post_data['etudiant_session_id']);
            }
        }

        $query = $this->db->get();

        if ($this->db->count_all_results() > $this->config->item('questions_types')[10]['docs_max'])
        {
            log_alerte(
				array(
					'code' => 'DOC4412',
					'desc' => "L'etudiant a televerse le maximum de fichier pour cette question."
				)
            );

            return FALSE;
        }

        //
        // Initialisation de la bibliotheque pour la manipulation des images.
        //

        $this->load->library('image_lib');

        /* -----------------------------------------------------------------
         *
         * Manipulation des images
         *
         * ----------------------------------------------------------------- */
        if ($filedata['is_image'])
        {
            /* SAMPLE:
			array
			Array
			(
				[extension] => jpeg
				[file_name] => dev_e1g1s_1641051886_idjzmk.jpeg
				[file_name_pre] => dev_e1g1s_1641051886_idjzmk
				[path] => /var/www/kovao.dev/main/public/storage_s/
				[full_path] => /var/www/kovao.dev/main/public/storage_s/dev_e1g1s_1641051886_idjzmk.jpeg
				[mime_type] => image/jpeg
				[is_image] => 1
				[file_size] => 638851
				[image_width] => 1920
				[image_height] => 1440
			)
            */

            // La taille maximale des images televersees.

            $image_max_hw = $this->config->item('image_max_hw');

            // La taille prevue du thumbnail cree a partir de l'image originale.

            $image_tn_taille = $this->config->item('image_tn_taille');

            //
            // Reduire la taille originale des fichiers, si necessaire
            //

            if ($filedata['image_width'] > $image_max_hw || $filedata['image_height'] > $image_max_hw)
            {
                $image_config['image_library']  = 'gd2';
                $image_config['source_image']   = $filedata['full_path'];
                $image_config['quality']        = $this->config->item('image_qualite'); 
                $image_config['create_thumb']   = FALSE;  // work on the original
                $image_config['maintain_ratio'] = TRUE;
                $image_config['width']          = $image_max_hw;
                $image_config['height']         = $image_max_hw;

                $this->image_lib->initialize($image_config); 

                if ( ! $this->image_lib->resize())
                {
                    unlink($filedata['full_path']);

                    echo $this->image_lib->display_errors();
                    return FALSE;
                }

                $this->image_lib->clear();

                $image_info = getimagesize($filedata['full_path']);

                $filedata['image_width']  = $image_info[0];
                $filedata['image_height'] = $image_info[1];
                $filedata['file_type']    = $image_info['mime'];
                // $filedata['file_size']    = filesize($filedata['full_path']);

                unset($image_info);
            }

            //
            // Creer un thumbnail a partir de l'original.
            // 

            $ts = $this->config->item('image_tn_taille');

            $thumb_config = array();

            $thumb_config['image_library']  = 'GD2';
            $thumb_config['source_image']   = $filedata['full_path'];
            $thumb_config['thumb_marker']   = '';
            $thumb_config['quality']        = $this->config->item('image_tn_qualite'); 
            $thumb_config['create_thumb']   = TRUE;
            $thumb_config['maintain_ratio'] = TRUE;
            $thumb_config['width']          = $ts;
            $thumb_config['height']         = $ts;
            $thumb_config['new_image']      = $filedata['path'] . $filedata['file_name_pre'] . '_tn.jpeg';

            $this->image_lib->initialize($thumb_config); 

            if ( ! $this->image_lib->resize())
            {
                echo $this->image_lib->display_errors();
                return;
            }

            $image_info                       = getimagesize($thumb_config['new_image']);

            // $thumbnail_data                   = $filedata;
            $thumbnail_data                   = array();

            $thumbnail_data['file_name']      = basename($thumb_config['new_image']);
            $thumbnail_data['full_path']      = $thumb_config['new_image'];

            $thumbnail_data['image_width']    = $image_info[0];
            $thumbnail_data['image_height']   = $image_info[1];

            $thumbnail_data['file_type']      = $image_info['mime'];
            $thumbnail_data['file_size']      = filesize($thumb_config['new_image']);
            $thumbnail_data['thumbnail_size'] = $ts;

            $this->image_lib->clear();

        } // if is_image

        /* -----------------------------------------------------------------
         *
         * Manipulation d'un PDF
         *
         * ----------------------------------------------------------------- */
        elseif ($filedata['mime_type'] == 'application/pdf')
        {
            /*
			array
			Array
			(
				[extension] => pdf
				[file_name] => dev_e1g1s_1641051833_cbdspw.pdf
				[file_name_pre] => dev_e1g1s_1641051833_cbdspw
				[path] => /var/www/kovao.dev/main/public/storage_s/
				[full_path] => /var/www/kovao.dev/main/public/storage_s/dev_e1g1s_1641051833_cbdspw.pdf
				[mime_type] => application/pdf
				[is_image] => 0
				[file_size] => 148832
			)
            */

            $gs_input_file  = $filedata['full_path'];
            $gs_output_file = $filedata['path'] . $filedata['file_name_pre'] . '.jpeg';

            exec("/usr/bin/gs -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMT -sDEVICE=jpeg -dJPEGQ=80 -r72 -dFirstPage=1 -dLastPage=1 -sOutputFile=" . $gs_output_file . " -f" . $gs_input_file, $exec_output, $exec_return);
            // fonctionne mais lent: exec("/usr/bin/gs -q -dQuiet -dSAFER -dBATCH -dNOPAUSE -dNOPROMT -sDEVICE=jpeg -r" . $ts . " -dFirstPage=1 -dLastPage=1 -sOutputFile=" . $gs_output_file . " -f" . $gs_input_file, $exec_output, $exec_return);
            // $exec_return will return non-zero upon an error

            //
            // Creer le thumbnail
            //

            if ( ! $exec_return) 
            {
                $ts = $this->config->item('image_tn_taille');

                $thumb_config = array();

                $thumb_config['image_library']  = 'GD2';
                $thumb_config['source_image']   = $gs_output_file;
                $thumb_config['thumb_marker']   = '';
                $thumb_config['quality']        = $this->config->item('image_tn_qualite'); 
                $thumb_config['create_thumb']   = TRUE;
                $thumb_config['maintain_ratio'] = TRUE;
                $thumb_config['width']          = $ts;
                $thumb_config['height']         = $ts;
                $thumb_config['new_image']      = $filedata['path'] . $filedata['file_name_pre'] . '_tn.jpeg';

                $this->image_lib->initialize($thumb_config); 

                if ( ! $this->image_lib->resize())
                {
                    echo $this->image_lib->display_errors();
                    return FALSE;
                }

                $image_info = getimagesize($thumb_config['new_image']);

                // $thumbnail_data                   = $filedata;

                $thumbnail_data                   = array();
                $thumbnail_data['file_name']      = basename($thumb_config['new_image']); // $ts . '_' . $filedata['file_name'];
                $thumbnail_data['full_path']      = $thumb_config['new_image'];

                $thumbnail_data['image_width']    = $image_info[0];
                $thumbnail_data['image_height']   = $image_info[1];

                $thumbnail_data['file_type']      = $image_info['mime'];
                $thumbnail_data['file_size']      = filesize($thumb_config['new_image']);
                $thumbnail_data['thumbnail_size'] = $ts;

                $this->image_lib->clear();
            }
                
            unlink($gs_output_file);
        }

        /* -----------------------------------------------------------------
         *
         * Manipulation des autres types de fichiers
         *
         * ----------------------------------------------------------------- */
        else
        {
			/*
		   Array
		   (
			iarray
			Array
			(
				[extension] => vnd.openxmlformats-officedocument.wordprocessingml.document
				[file_name] => dev_e1g1s_1641051682_ndvztm.vnd.openxmlformats-officedocument.wordprocessingml.document
				[file_name_pre] => dev_e1g1s_1641051682_ndvztm
				[path] => /var/www/kovao.dev/main/public/storage_s/
				[full_path] => /var/www/kovao.dev/main/public/storage_s/dev_e1g1s_1641051682_ndvztm.vnd.openxmlformats-officedocument.wordprocessingml.document
				[mime_type] => application/vnd.openxmlformats-officedocument.wordprocessingml.document
				[is_image] => 0
				[file_size] => 15939
			)
			*/

        }

        $this->db->trans_begin();

        //
        // Enregistrer les documents
        //

        $data = array(
            'groupe_id'            => $this->groupe_id,
            'question_id'          => $question_id,
            'etudiant_id'          => $this->est_etudiant ? $this->etudiant_id : NULL,
            'etudiant_session_id'  => $this->est_etudiant ? NULL : $post_data['etudiant_session_id'],
            'evaluation_id'        => $evaluation_id,
            'evaluation_reference' => $post_data['evaluation_reference'],
            'doc_filename'         => $filedata['file_name'], 
            'doc_sha256_file'      => hash_file('sha256', $filedata['full_path']),
            'doc_filesize'         => filesize($filedata['full_path']),
            'doc_is_image'         => $filedata['is_image'],
            'doc_mime_type'        => $filedata['mime_type'],
            'doc_size_h'           => $filedata['is_image'] ? $filedata['image_height'] : NULL,
            'doc_size_w'           => $filedata['is_image'] ? $filedata['image_width'] : NULL,
            'ajout_date'           => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'          => $this->now_epoch,
            'modif_date'           => date_humanize($this->now_epoch, TRUE),
            'modif_epoch'          => $this->now_epoch
        );

		$fichiers = array();
		$fichiers[] = $filedata['file_name'];

        if ( ! empty($thumbnail_data))
        {
            $data = array_merge($data,
                array(
                    'doc_tn_filename'    => $thumbnail_data['file_name'],
                    'doc_tn_sha256_file' => hash_file('sha256', $thumbnail_data['full_path']),
                    'doc_tn_filesize'    => $thumbnail_data['file_size'],
                    'doc_tn_is_image'    => 1,
                    'doc_tn_size_h'      => $thumbnail_data['image_height'],
                    'doc_tn_size_w'      => $thumbnail_data['image_width'],
                    'doc_tn_mime_type'   => 'image/jpeg'
                )
            );

			$fichiers[] = $thumbnail_data['file_name'];
        }

		if ($this->config->item('utiliser_s3'))
		{
			$s3_succes = TRUE;

			foreach($fichiers as $f)
			{
				if ( ! $this->_enregistrer_s3(array( 'dossier' => 'soumissions', 'key' => $f, 'source' => $this->config->item('documents_path_s') . $f)))
				{
					$s3_succes = FALSE;

					log_alerte(
						array(
							'code' => 'DOC33441',
							'desc' => "Il n'a pas été possible d'enregistrer les documents sur S3.",
							'importance' => 3
						)
					);

					break;
				}
			}

			if ($s3_succes)
			{
				$data['s3'] = TRUE;

				foreach($fichiers as $f)
				{
					if (file_exists($this->config->item('documents_path_s') . $f))
					{
						unlink($this->config->item('documents_path_s') . $f);
					}
				}
			}
		}

        $this->db->insert('documents_etudiants', $data);

        $data['doc_id'] = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();

            log_alerte(
				array(
					'code' => 'DOC5812',
					'desc' => "Il n'a pas été possible d'enregistrer le document."
				)
            );

            return FALSE;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            echo "Une erreur provenant de la base de donnees ete signalee.";
            return FALSE;
        }

        $this->db->trans_commit();

        return $data;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un document d'une evaluation non soumise
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * ------------------------------------------------------------------------------------------- */
    public function effacer_document_soumission($doc_id, $question_id, $evaluation_id, $evaluation_reference, $session_id = NULL)
    {
        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.doc_id', $doc_id);
        $this->db->where  ('de.question_id', $question_id);
        $this->db->where  ('de.evaluation_id', $evaluation_id);
        $this->db->where  ('de.efface', 0);

        if ( ! $this->logged_in)
        {
            $this->db->where ('de.evaluation_reference', $evaluation_reference);
            $this->db->where ('de.etudiant_session_id',  $session_id ?: session_id());
        }
        else
        {
            if ($this->est_enseignant)
            {
                $this->db->where ('de.etudiant_session_id', $session_id ?: session_id());
            }

            if ($this->est_etudiant)
            {
                $this->db->where ('de.evaluation_reference', $evaluation_reference);
                $this->db->where ('de.etudiant_id', $this->etudiant_id);
            }
        }

        $this->db->limit(1);

        $query = $this->db->get();

        $doc = $query->result_array();

        if (empty($doc) || count($doc) != 1)
        {
            return FALSE;
        }

        $doc = $doc[0];

        //
        // Effacer le document
        //

        $this->db->where ('doc_id', $doc_id);
        $this->db->where ('efface', 0);
        $this->db->delete('documents_etudiants');

        if ($this->db->affected_rows())
        {
			if ($doc['s3'])
            {
                if ( ! empty($doc['doc_filename']))
                {
                    $this->_effacer_s3(array('dossier' => 'soumissions', 'key' => $doc['doc_filename']));
                }

				if ( ! empty($doc['doc_tn_filename']))
				{
				    $this->_effacer_s3(array('dossier' => 'soumissions', 'key' => $doc['doc_tn_filename']));
				}
			}
			else
			{
				if (file_exists(FCPATH . $this->config->item('documents_path_s') . $doc['doc_filename']))
				{
					unlink(FCPATH . $this->config->item('documents_path_s') . $doc['doc_filename']);
				}

				if (file_exists(FCPATH . $this->config->item('documents_path_s') . $doc['doc_tn_filename']))
				{
					unlink(FCPATH . $this->config->item('documents_path_s') . $doc['doc_tn_filename']);
				}
			}
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer les documents d'une soumission terminee
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * ------------------------------------------------------------------------------------------- */
    public function effacer_documents_soumission_terminee($soumission_id, $options = array())
    {
    	$options = array_merge(
        	array(
                'unlink' => TRUE // TRUE = effacer les documents du disque, FALSE = changer le flag efface pour 1
           ),
           $options
        );
         
        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.soumission_id', $soumission_id);
        $this->db->where  ('de.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return TRUE;
        }

        $docs = $query->result_array();
        
        $data = array();
        $path = FCPATH . $this->config->item('documents_path_s');

        foreach($docs as $d)
        {
			if ($options['unlink'])
			{
				//
				// Effacer le document de la base de donnees
				//

				$this->db->where ('doc_id', $d['doc_id']);
				$this->db->delete('documents_etudiants');

				if ($this->db->affected_rows())
				{
					if ($d['s3']) 
					{
						//
						// Effacer les documents de S3
						//
		
						if ( ! empty($d['doc_filename']))
						{
							$this->_effacer_s3(array('dossier' => 'soumissions', 'key' => $d['doc_filename']));
						}

						if ( ! empty($d['doc_tn_filename']))
						{
							$this->_effacer_s3(array('dossier' => 'soumissions', 'key' => $d['doc_tn_filename']));
						}
					}	
					else
					{
						//
						// Effacer les documents du disque
						//

						if (file_exists($path . $d['doc_filename']))
						{
							unlink($path . $d['doc_filename']);
						}        

						if (file_exists($path . $d['doc_tn_filename']))
						{
							unlink($path . $d['doc_tn_filename']);
						}        
					}
				} // affected_rows
			}
			else
			{
				//
				// Changer le flag 'efface' == 1 dans la base de donnees
				//

                $data[] = array(
                    'doc_id'       => $d['doc_id'],
                    'efface'       => 1,
                    'efface_epoch' => $this->now_epoch,
                    'efface_date'  => date_humanize($this->now_epoch, TRUE)
                );
			}
        }

        if ( ! $options['unlink'] && ! empty($data))
        {
            $this->db->update_batch('documents_etudiants', $data, 'doc_id');
        }

        return TRUE;
    } 

    /* --------------------------------------------------------------------------------------------
     *
     * Rotation d'une image d'une evaluation non soumise
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * ------------------------------------------------------------------------------------------- */
    public function rotation_image_soumission($rotation, $doc_id, $question_id, $evaluation_id, $evaluation_reference, $session_id = NULL)
    {
        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.doc_id', $doc_id);
        $this->db->where  ('de.doc_is_image', 1);
        $this->db->where  ('de.question_id', $question_id);
        $this->db->where  ('de.evaluation_id', $evaluation_id);
        $this->db->where  ('de.efface', 0);

        if ( ! $this->logged_in)
        {
            $this->db->where ('de.evaluation_reference', $evaluation_reference);
            $this->db->where ('de.etudiant_session_id', $session_id ?: session_id());
        }
        else
        {
            if ($this->est_enseignant)
            {
                $this->db->where ('de.etudiant_session_id', $session_id ?: session_id());
            }

            if ($this->est_etudiant)
            {
                $this->db->where ('de.evaluation_reference', $evaluation_reference);
                $this->db->where ('de.etudiant_id', $this->etudiant_id);
            }
        }

        $this->db->limit(1);

        $query = $this->db->get();

        $doc = $query->result_array();

        if (empty($doc) || count($doc) != 1)
        {
            return FALSE;
        }

        $doc = $doc[0];

        //
        // Initialisation de la bibliotheque pour la manipulation des images.
        //

        $this->load->library('image_lib');

        //
        // Rotation de l'image et du thumbnail
        //

		$fichiers = array(
			$doc['doc_filename'], $doc['doc_tn_filename']
		);

		$n_sha256 = array();

		$config['image_library']  = 'gd2';
		$config['rotation_angle'] = ($rotation == 'right' ? '270' : '90');

		foreach($fichiers as $f)
		{
			if ($doc['s3'])
			{
				if ($this->_extraire_s3(array('dossier' => 'soumissions', 'key'	=> $f)) !== FALSE) 
				{
					$config['source_image']	= '/tmp/' . $f;
					$config['new_image']	= '/tmp/' . $f;
				}
			}
			else
			{
				$config['source_image']	  = FCPATH . $this->config->item('documents_path_s') . $f;
				$config['new_image']	  = FCPATH . $this->config->item('documents_path_s') . $f;
			}

			$this->image_lib->initialize($config); 

			if ( ! $this->image_lib->rotate())
			{
				log_alerte(
					array(
						'code' => 'DOC1707',
						'desc' => "Il n'a pas été possible d'effectuer la rotation de l'image : " . $f
					)
				);

				return FALSE;
			}

			if ($doc['s3'])
			{
				foreach($fichiers as $f)
				{
					if (file_exists('/tmp/' . $f))
					{
						$n_sha256[$f] = hash_file('sha256', '/tmp/' . $f);

						$this->_enregistrer_s3(
							array(
								'dossier' => 'soumissions',
								'key'	  => $f,
								'source'  => '/tmp/' . $f
							)
						);
					}
				}
			}
			else
			{
				//
				// Generer le nouveau sha256 du fichier
				//

				$n_sha256[$f] = hash_file('sha256', FCPATH . $this->config->item('documents_path_s') . $f);
			}
		}

        //
        // Enregistrer les modifications dans la base de donnees
        //

        $this->db->trans_begin();

        $this->db->where('doc_id', $doc_id);
        $this->db->update('documents_etudiants', 
            array(
                'modif_epoch' => $this->now_epoch,
                'modif_date'  => date_humanize($this->now_epoch, TRUE),
				'doc_sha256_file' 	 => $n_sha256[$doc['doc_filename']],
				'doc_tn_sha256_file' => $n_sha256[$doc['doc_tn_filename']]
            )
        );

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();

            log_alerte(
				array(
					'code'  => 'DOC1710',
                    'desc'  => "Il n'a pas été possible de modifier l'enregistrement du document dans la base de données.",
                    'extra' => 'doc_id = ' . $doc_id
				)
            );

            return FALSE;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            log_alerte(
				array(
					'code' => 'DOC1711',
					'desc' => "Il eu une erreur de modification d'un enregistrement de la base de données."
				)
            );

            return FALSE;
        }

        $this->db->trans_commit();

		if ($doc['s3'])
		{
			return array(
				'uri'    	  => $this->config->item('s3_url', 'amazon') . 'soumissions/' . $doc['doc_filename'],
				'uri_tn' 	  => $this->config->item('s3_url', 'amazon') . 'soumissions/' . $doc['doc_tn_filename'],
				'doc_sha256_file' => $n_sha256[$doc['doc_filename']]
			);
		}
		else
		{
			return array(
				'uri'    	  => base_url() . $this->config->item('documents_path_s') . $doc['doc_filename'],
				'uri_tn' 	  => base_url() . $this->config->item('documents_path_s') . $doc['doc_tn_filename'],
				'doc_sha256_file' => $n_sha256[$doc['doc_filename']]
			);
		}
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rotation d'une image d'une soumission terminee
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * ------------------------------------------------------------------------------------------- */
    public function rotation_image_soumission_terminee($rotation, $doc_id, $soumission_id)
    {
        if (empty($rotation))
            $rotation = 'right';

        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.doc_id', $doc_id);
        $this->db->where  ('de.soumission_id', $soumission_id);
        $this->db->where  ('de.doc_is_image', 1);
        $this->db->where  ('de.efface', 0);
        
        $this->db->limit(1);

        $query = $this->db->get();

        $doc = $query->result_array();

        if (empty($doc) || count($doc) != 1)
        {
            return FALSE;
        }

        $doc = $doc[0];

        //
        // Initialisation de la bibliotheque pour la manipulation des images.
        //

        $this->load->library('image_lib');

        //
        // Rotation de l'image et du thumbnail
        //

		$fichiers = array(
			$doc['doc_filename'], $doc['doc_tn_filename']
		);

		$n_sha256 = array();

		$config['image_library']  = 'gd2';
		$config['rotation_angle'] = ($rotation == 'right' ? '270' : '90');

		foreach($fichiers as $f)
		{
			if ($doc['s3'])
			{
				if ($this->_extraire_s3(array('dossier' => 'soumissions', 'key'	=> $f)) !== FALSE) 
				{
					$config['source_image']	= '/tmp/' . $f;
					$config['new_image']	= '/tmp/' . $f;
				}
			}
			else
			{
				$config['source_image']	  = FCPATH . $this->config->item('documents_path_s') . $f;
				$config['new_image']	  = FCPATH . $this->config->item('documents_path_s') . $f;
			}

			$this->image_lib->initialize($config); 

			if ( ! $this->image_lib->rotate())
			{
				log_alerte(
					array(
						'code' => 'DOC1707',
						'desc' => "Il n'a pas été possible d'effectuer la rotation de l'image : " . $f
					)
				);

				return FALSE;
			}

			if ($doc['s3'])
			{
				foreach($fichiers as $f)
				{
					if (file_exists('/tmp/' . $f))
					{
						$n_sha256[$f] = hash_file('sha256', '/tmp/' . $f);

						$this->_enregistrer_s3(
							array(
								'dossier' => 'soumissions',
								'key'	  => $f,
								'source'  => '/tmp/' . $f
							)
						);
					}
				}
			}
			else
			{
				//
				// Generer le nouveau sha256 du fichier
				//

				$n_sha256[$f] = hash_file('sha256', FCPATH . $this->config->item('documents_path_s') . $f);
			}
		}

        //
        //
        // Enregistrer les modifications dans la base de donnees
        //

        $this->db->trans_begin();

        $this->db->where ('doc_id', $doc_id);
        $this->db->update('documents_etudiants', 
			array(
				'modif_epoch' => $this->now_epoch,
				'modif_date'  => date_humanize($this->now_epoch, TRUE),
				'doc_sha256_file' 	 => $n_sha256[$doc['doc_filename']],
				'doc_tn_sha256_file' => $n_sha256[$doc['doc_tn_filename']]
			)
		);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();

            log_alerte(
				array(
					'code' => 'DOC1710',
					'desc' => "Il n'a pas été possible de modifier l'enregistrement du document dans la nase de données."
				)
            );

            return FALSE;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            log_alerte(
				array(
					'code' => 'DOC1711',
					'desc' => "Il eu une erreur de modification d'un enregistrement de la base de données."
				)
            );

            return FALSE;
        }

        $this->db->trans_commit();

		if ($doc['s3'])
		{
			return array(
				'uri'    	  => $this->config->item('s3_url', 'amazon') . 'soumissions/' . $doc['doc_filename'],
				'uri_tn' 	  => $this->config->item('s3_url', 'amazon') . 'soumissions/' . $doc['doc_tn_filename'],
				'doc_sha256_file' => $n_sha256[$doc['doc_filename']]
			);
		}
		else
		{
			return array(
				'uri'    	  => base_url() . $this->config->item('documents_path_s') . $doc['doc_filename'],
				'uri_tn' 	  => base_url() . $this->config->item('documents_path_s') . $doc['doc_tn_filename'],
				'doc_sha256_file' => $n_sha256[$doc['doc_filename']]
			);
		}
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les documents d'une evaluation non soumise
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     * - les enseignants en direct
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_documents_soumission($question_id, $evaluation_id, $evaluation_reference, $options = array())
    {
    	$options = array_merge(
            array(
                'session_id'  => NULL,
                'en_direct'   => FALSE,
                'etudiant_id' => NULL
           ),
           $options
        );

        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.question_id', $question_id);
        $this->db->where  ('de.evaluation_id', $evaluation_id);
        $this->db->where  ('de.soumission_id', NULL);
        $this->db->where  ('de.efface', 0);
        
        if ( ! $this->logged_in)
        {
            $this->db->where ('de.evaluation_reference', $evaluation_reference);
            $this->db->where ('de.etudiant_session_id', $options['session_id'] ?: session_id());
        }
        else
        {
            if ($this->est_enseignant)
            {
                if ($options['en_direct'])
                {
                    if (empty($options['etudiant_id']))
                    {
						generer_erreur2(
							array(
								'code'  => 'DOCEXS117',
								'desc'  => "Il manque un paramètre pour exécuter cette opération.",
								'extra' => 'evaluation_reference = ' . $evaluation_reference
							)
						); 
						exit;
                    }
                            
                    $this->db->where ('de.etudiant_id', $options['etudiant_id']);
                }
                else
                {
                    $this->db->where ('de.etudiant_session_id', $options['session_id'] ?: session_id());
                }
            }

            if ($this->est_etudiant)
            {
                $this->db->where ('de.evaluation_reference', $evaluation_reference);
                $this->db->where ('de.etudiant_id', $this->etudiant_id);
            }
        }
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            array();
        }

        return ($query->result_array());
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les documents d'une evaluation soumise
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_documents_soumission_terminee($soumission_id)
    {
        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.soumission_id', $soumission_id);
        $this->db->where  ('de.efface', 0);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            array();
        }

        //
        // Classer les documents par question_id
        //
        // @TODO
        // Verifier si les documents sont sur le disque 
        // (Remplacer par une image d'un fichier introuvable)
        //

        $docs = array();

        foreach($query->result_array() as $arr)
        {
            $question_id = $arr['question_id'];

            if ( ! array_key_exists($question_id, $docs))
            {
                $docs[$question_id] = array();
            }

            $docs[$question_id][] = $arr;
        }

        return $docs;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Assigner les documents d'une question a une soumission soumise
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il faut faire attention car il y a plusieurs categories d'usagers qui pourraient soumettre :
     *
     * - les etudiants inscrits
     * - les etudiants inscrits : les evaluations non envoyees
     * - les etudiants non inscrits
     * - les enseignants en previsualisation
     *
     * -------------------------------------------------------------------------------------------- */
    function assigner_documents_soumission($options = array())
    {
    	$options = array_merge(
            array(
                'evaluation_reference' => NULL, // (*) obligatoire
                'etudiant_id'          => @$this->etudiant_id ?? NULL,
                'session_id'           => session_id()
           ),
           $options
        );

        $this->db->from   ('documents_etudiants as de');
        $this->db->where  ('de.question_id', $options['question_id']);
        $this->db->where  ('de.evaluation_id', $options['evaluation_id']);
        $this->db->where  ('de.soumission_reference', NULL);
        $this->db->where  ('de.efface', 0);
        
        // Les etudiants non inscrits

        if ( ! $this->logged_in)
        {
            $this->db->where ('de.evaluation_reference', $options['evaluation_reference']);
            $this->db->where ('de.etudiant_session_id', $options['session_id']);
        }
        else
        {
            // Les etudiants inscrits
            // Les etudiants inscrits : les evaluations non envoyees
    
            if ($options['etudiant_id'])
            {
                $this->db->where ('de.evaluation_reference', $options['evaluation_reference']);
                $this->db->where ('de.etudiant_id', $options['etudiant_id']);
            }

            // Les enseignants en previsualisation

            elseif ($this->est_enseignant)
            {
                $this->db->where ('de.etudiant_session_id', $options['session_id']);
            }
        }
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $docs = $query->result_array();

        $data_batch = array();

        foreach($docs as $d)
        {
            $data = array(
                'doc_id'               => $d['doc_id'],
                'soumission_id'        => $options['soumission_id'],
                'soumission_reference' => $options['soumission_reference']
            );

            $data_batch[] = $data;
        }

        $this->db->update_batch('documents_etudiants', $data_batch, 'doc_id');

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter eleves (Omnivox / CSV)
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_eleves_omnivox_csv($semestre_id, $filedata, $params)
    {
        // Determiner le cours et le groupe a partir du nom du fichier.

        $cours_id = $params['cours_id'];
        $groupe   = (int) $params['numero_groupe'];

		$i = 0;
		if (($handle = fopen($filedata['full_path'], "r")) !== FALSE) 
        {
            $data = array();

    		while (($line = fgetcsv($handle, 1000, ";")) !== FALSE) 
			{
				$i++;
				
                $row = array();

				if ($i == 1)
                {
                    $mat_key = NULL; // matricule
                    $grp_key = NULL; // groupe
                    $nom_key = NULL; // nom
                    $pre_key = NULL; // prenom
                    $cpm_key = NULL; // code permanent
                    $prg_key = NULL; // programme

                    // Determiner la signification de chaque colonne.
                    foreach($line as $id => $l)
                    {
                        if (preg_match('/^Mat/', $l))
                        {
                            $mat_key = $id; continue; 
                        }

                        if (preg_match('/^Groupe/', $l))
                        {
                            $grp_key = $id; continue;
                        }

                        if (preg_match('/^Nom/', $l))
                        {
                            $nom_key = $id; continue;
                        }

                        if (preg_match('/^Pr.nom/', $l))
                        {
                            $pre_key = $id; continue;
                        }

                        if (preg_match('/^Code/', $l))
                        {
                            $cpm_key = $id; continue;
                        }

                        if (preg_match('/^Prog/', $l))
                        {
                            $prg_key = $id; continue;
                        }
                    }

					continue;
				}

                $cherche  = array('=', '"');
                $remplace = array('', '');

                $row = array(
                    'semestre_id'    => $semestre_id,
                    'groupe_id'		 => $this->groupe_id,
                    'enseignant_id'  => $this->enseignant['enseignant_id'],
                    'cours_id'	     => $cours_id,
                    'cours_groupe'   => $groupe,
                    'eleve_nom'		 => str_replace($cherche, $remplace, iconv('ISO-8859-1', 'UTF-8', $line[$nom_key])),
                    'eleve_prenom'   => str_replace($cherche, $remplace, iconv('ISO-8859-1', 'UTF-8', $line[$pre_key])),
                    'numero_da'		 => str_replace($cherche, $remplace, $line[$mat_key]),
                    'programme_code' => empty($prg_key) ? NULL : str_replace($cherche, $remplace, $line[$prg_key]),
                    'code_permanent' => empty($cpm_key) ? NULL : str_replace($cherche, $remplace, $line[$cpm_key])
                );

                $data[] = $row;
            }
        } 

		fclose($handle);

		unlink($filedata['full_path']);

		//
		// Inserer les donnes.
		//

		// Il est possible que ceci termine dans le cas d'un duplicat sans inserer les nouvelles entrees.
		// $this->db->insert_batch('eleves', $data);

		if ( ! empty($data))
		{
			foreach($data as $d)
			{
				$this->db->insert('eleves', $d);
			}
		}

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter eleves (Colnet / CSV)
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_eleves_colnet_csv($semestre_id, $filedata, $params = array())
    {
		$data = array();

        $cours_id = $params['cours_id'];
        $groupe   = (int) $params['numero_groupe'];

        $eleves = $this->Cours_model->lister_eleves($semestre_id);

        if (array_key_exists($cours_id, $eleves) && array_key_exists($groupe, $eleves[$cours_id]))
        {
            $eleves = $eleves[$cours_id][$groupe];
        }
        else
        {
            $eleves = array();
        }

		$i = 0;
		if (($handle = fopen($filedata['full_path'], "r")) !== FALSE) 
		{
    		while (($line = fgetcsv($handle, 1000, ";")) !== FALSE) 
			{
				$i++;
				
				$row = array();

				if ($i == 1)
				{
					// La premiere ligne du fichier ne contient pas d'information sur les eleves.
					continue;
				}

				if ($i > 1)
                {
                    $duplicat = FALSE;

                    if ( ! empty($eleves))
                    {
                        foreach($eleves as $e)
                        {
                            if ($e['numero_da'] == $line[2])
                            {
                                $duplicat = TRUE;
                            }
                        }

                    }

                    if ($duplicat)
                    {
                        continue;
                    }

					$row = array(
						'semestre_id'    => $semestre_id,
						'groupe_id'		 => $this->groupe_id,
						'enseignant_id'  => $this->enseignant['enseignant_id'],
						'cours_id'	     => $cours_id,
						'cours_groupe'   => $groupe,
						'eleve_nom'		 => iconv('ISO-8859-1', 'UTF-8', $line[0]),
						'eleve_prenom'   => iconv('ISO-8859-1', 'UTF-8', $line[1]),
						'numero_da'		 => trim($line[2]),
						'programme_code' => trim($line[3])
					);

					$data[] = $row;
				}
			}
		}

		fclose($handle);

		unlink($filedata['full_path']);

		//
		// Inserer les donnes.
		//

		// Il est possible que ceci termine dans le cas d'un duplicat sans inserer les nouvelles entrees.
		// $this->db->insert_batch('eleves', $data);

		if ( ! empty($data))
		{
			foreach($data as $d)
			{
				$this->db->insert('eleves', $d);
			}
		}

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer document
     *
	 * --------------------------------------------------------------------------------------------
	 *
     * (!) 
	 * Il ne faut pas effacer les documents (images) du disque (ou S3) avant d'avoir verifie 
	 * qu'il n'y ait pas d'autres evaluations qui utilisent ces documents (images). 
	 * Ceci est verifie lors de la purge nocture (purger_documents dans Cli_model).
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_document($doc_id)
    {
		//
        // Extraire le document
		//        

        $this->db->from   ('documents as d, questions as q, evaluations as e');
		$this->db->select ('d.*, e.public, e.enseignant_id');

        $this->db->where  ('d.doc_id', $doc_id);
        $this->db->where  ('d.groupe_id', $this->groupe_id);
		$this->db->where  ('d.efface', 0);

		$this->db->where  ('d.question_id = q.question_id');
		$this->db->where  ('q.efface', 0);

		$this->db->where  ('q.evaluation_id = e.evaluation_id');
		$this->db->where  ('e.efface', 0);

        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $d = $query->row_array();

		//
		// Verifier de ne pas effacer un document en production lors du developpement
		//

		if ($this->is_DEV && strpos($d['doc_filename'], 'dev_') === FALSE)
		{
            log_alerte(
				array(
					'code'  	 => 'DOCDEL9',
					'desc'  	 => "Le développeur a tenté d'effacer un document en production.",
					'importance' => 3,
					'extra' 	 => 'enseignant_id = ' . $this->enseignant_id . ', doc_id = ' . $d['doc_id'],
				)
            );

			return FALSE;
		}

		//
        // Verifier la permission d'effacer le document
		//        

		$permission = FALSE;

		if ($d['public'])
		{
			// Les documents publics sont ceux des evaluations du groupe.

 			if ($d['enseignant_id'] == $this->enseignant_id || $this->enseignant['niveau'] >= 20)
			{
				$permission = TRUE;
			}
		}
		else
		{
			if ($d['enseignant_id'] == $this->enseignant_id)
			{
				$permission = TRUE;
			}
		}

		//
		// Effacer le document (changer le drapeau) dans la base de donnees
		//

		if ($permission || $this->enseignant['privilege'] > 89)
		{
			$this->db->where  ('doc_id', $doc_id);
			$this->db->update ('documents', 
				array(
					'efface'       => 1,
					'efface_epoch' => $this->now_epoch,
					'efface_date'  => date_humanize($this->now_epoch, TRUE)
				)
			);
		}

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer documents par question
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_documents_par_question($question_id)
    {
		//
        // La permission d'effacer les documents devrait deja avoir ete obtenue.
		//

		//
        // Extraire les documents a effacer.
		//
        
        $this->db->from ('documents as d');
        $this->db->where('question_id', $question_id);
        $this->db->where('groupe_id', $this->groupe_id);
        $this->db->where('efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            // Aucun document trouve.
            return TRUE;
        }

        $documents = $query->result_array();

        foreach($documents as $d)
        {
            if ( ! $this->effacer_document($d['doc_id']))
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier caption
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_caption($doc_id, $doc_caption)
    {
        // Permission de modifier le document ?
        
        $this->db->from ('documents as d');
        $this->db->where('doc_id', $doc_id);
        $this->db->where('efface', 0);
        $this->db->where('groupe_id', $this->groupe_id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $data = array(
            'doc_caption' => htmlentities($doc_caption)
        );

        $this->db->where('doc_id', $doc_id);
        $this->db->update('documents', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detection des documents identiques pour contrer le plagiat.
     *
     * -------------------------------------------------------------------------------------------- */
    function detecter_documents_identiques($documents_sha)
    {
        $this->db->from         ('documents_etudiants as de, soumissions as s, enseignants as e, groupes as g, semestres as sm, cours as c');
        $this->db->select       (
                                 'de.*, s.soumission_id'
                                 . ', s.soumission_reference, s.prenom_nom, s.numero_da, s.groupe_id, s.semestre_id, s.cours_id, s.etudiant_id, s.enseignant_id'
                                 . ', e.nom as enseignant_nom, e.prenom as enseignant_prenom'
                                 . ', g.sous_domaine'
                                 . ', sm.semestre_code'
                                 . ', c.cours_code, c.cours_nom_court'
                                );
        $this->db->where_in     ('de.doc_sha256_file', $documents_sha);
        // $this->db->where_not_in ('doc_id', array_keys($documents)); // Ne pas considerer les documents a chercher
        $this->db->where        ('de.soumission_reference = s.soumission_reference');
        $this->db->where        ('s.enseignant_id = e.enseignant_id');
        $this->db->where        ('s.groupe_id = g.groupe_id');
        $this->db->where        ('s.semestre_id = sm.semestre_id');
        $this->db->where        ('s.cours_id = c.cours_id');
        $this->db->where        ('s.efface', 0);
        $this->db->where        ('de.efface', 0);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            // Peu probable car les documents de la soumission devraient etre trouves.
            return array();
        }

        //
        // Tous les documents ayant les memes hash de la base de doneees.
        //

        $documents_tous = array_keys_swap($query->result_array(), 'doc_id');

        //
        // Extraire les documents de la soumission seulement
        //

        $documents_soumission = array_intersect_key($documents_tous, $documents_sha);

        //
        // Extraire les documents identiques a ceux de la soumission seulement
        //

        $documents = array_diff_key($documents_tous, $documents_soumission);

        //
        // Preparation du rapport
        // 

        $documents_rapport = array();

        foreach($documents_soumission as $doc_id => $document)
        {
            $documents_rapport[$doc_id] = array();

            $doc_hash = $document['doc_sha256_file'];

            foreach($documents as $d)
            {
                //
                // Il se peut que le meme document a ete utilise plusieurs fois par le meme etudiant,
                // dans la meme soumission.
                //

                if ($d['soumission_reference'] == $document['soumission_reference'])
                {
                    continue;
                }

                //
                // Un document identique a ete trouve.
                //
                
                if ($d['doc_sha256_file'] == $doc_hash)
                {
                    $data = array(
                        'doc_id'                => $d['doc_id'],
                        'doc_sha256_file'       => $d['doc_sha256_file'],
                        'doc_filename'          => $d['doc_filename'],
                        'soumission_id'         => $d['soumission_id'],
                        'soumission_reference'  => $d['soumission_reference'],
                        'groupe_id'             => $d['groupe_id'],
                        'groupe_sous_domaine'   => $d['sous_domaine'],
                        'semestre_id'           => $d['semestre_id'],
                        'semestre_code'         => $d['semestre_code'],
                        'cours_id'              => $d['cours_id'],
                        'cours_code'            => $d['cours_code'],
                        'cours_nom'             => $d['cours_nom_court'],
                        'etudiant_id'           => $d['etudiant_id'],
                        'etudiant_prenom_nom'   => $d['prenom_nom'],
                        'etudiant_numero_da'    => $d['numero_da'],
                        'enseignant_id'         => $d['enseignant_id'],
                        'enseignant_prenom_nom' => $d['enseignant_prenom'] . ' ' . $d['enseignant_nom']
                    );

                    $documents_rapport[$doc_id][] = $data;
                }
            } // foreach inner
        } // foreach outer

        return $documents_rapport;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detection des documents identiques de plusieurs soumissions
     *
     * -------------------------------------------------------------------------------------------- */
    function detecter_documents_identiques_multi($soumissions_references)
    {
        //
        // Extraire tous les documents de ces soumissions
        //

        $this->db->from     ('documents_etudiants as de, soumissions as s');
        $this->db->select   ('de.*, s.soumission_id as origine_soumission_id, s.soumission_reference as origine_soumission_reference, s.prenom_nom as origine_soumission_prenom_nom');
        $this->db->where_in ('de.soumission_reference', $soumissions_references);
        $this->db->where    ('de.soumission_reference = s.soumission_reference');
        $this->db->where    ('de.efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $documents_soumissions = array_keys_swap($query->result_array(), 'doc_id');

        $documents_sha = array();

        foreach($documents_soumissions as $ds)
        {
            $documents_sha[$ds['doc_id']] = $ds['doc_sha256_file'];
        }

        //
        // Extraire tous les documents avec ces hash, incluant les documents originaux
        //

        $this->db->from         ('documents_etudiants as de, soumissions as s, enseignants as e, groupes as g, semestres as sm, cours as c');
        $this->db->select       (
                                 'de.*, s.soumission_id'
                                 . ', s.soumission_reference, s.prenom_nom as soumission_prenom_nom, s.numero_da, s.groupe_id, s.semestre_id, s.cours_id, s.etudiant_id, s.enseignant_id'
                                 . ', e.nom as enseignant_nom, e.prenom as enseignant_prenom'
                                 . ', g.sous_domaine'
                                 . ', sm.semestre_code'
                                 . ', c.cours_code, c.cours_nom_court'
                                );
        $this->db->where_in     ('de.doc_sha256_file', $documents_sha);
        $this->db->where        ('de.soumission_reference = s.soumission_reference');
        $this->db->where        ('s.enseignant_id = e.enseignant_id');
        $this->db->where        ('s.groupe_id = g.groupe_id');
        $this->db->where        ('s.semestre_id = sm.semestre_id');
        $this->db->where        ('s.cours_id = c.cours_id');
        $this->db->where        ('s.efface', 0);
        $this->db->where        ('de.efface', 0);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            // Peu probable car les documents de la soumission devraient etre trouves.
            return array();
        }

        //
        // Tous les documents ayant les memes hash de la base de doneees.
        //

        $documents_tous = array_keys_swap($query->result_array(), 'doc_id');
    
        $documents_tous_sha = array();

        foreach($documents_tous as $dt)
        {
            $documents_tous_sha[$dt['doc_id']] = $dt['doc_sha256_file'];
        }

        //
        // Preparation du rapport
        // 

        $documents_rapport = array();

        foreach($documents_soumissions as $ds)
        {
            $doc_id = $ds['doc_id'];

            foreach($documents_tous as $dt)
            {
                if ($ds['soumission_reference'] == $dt['soumission_reference'])
                {
                    continue;
                }

                if ($ds['doc_sha256_file'] == $dt['doc_sha256_file'])
                {
                    if ( ! array_key_exists($doc_id, $documents_rapport))
                    {
                        $documents_rapport[$doc_id] = array();
                    }            

                    $data = array(
                        'doc_id'                => $dt['doc_id'],
                        'doc_sha256_file'       => $dt['doc_sha256_file'],
                        'doc_filename'          => $dt['doc_filename'],
                        'origine_soumission_id'         => $ds['origine_soumission_id'],
                        'origine_soumission_reference'  => $ds['origine_soumission_reference'],
                        'origine_soumission_prenom_nom' => $ds['origine_soumission_prenom_nom'],
                        'soumission_id'         => $dt['soumission_id'],
                        'soumission_reference'  => $dt['soumission_reference'],
                        'groupe_id'             => $dt['groupe_id'],
                        'groupe_sous_domaine'   => $dt['sous_domaine'],
                        'semestre_id'           => $dt['semestre_id'],
                        'semestre_code'         => $dt['semestre_code'],
                        'cours_id'              => $dt['cours_id'],
                        'cours_code'            => $dt['cours_code'],
                        'cours_nom'             => $dt['cours_nom_court'],
                        'etudiant_id'           => $dt['etudiant_id'],
                        'etudiant_prenom_nom'   => $dt['soumission_prenom_nom'],
                        'etudiant_numero_da'    => $dt['numero_da'],
                        'enseignant_id'         => $dt['enseignant_id'],
                        'enseignant_prenom_nom' => $dt['enseignant_prenom'] . ' ' . $dt['enseignant_nom']
                    );

                    $documents_rapport[$doc_id][] = $data;
                }

            } // foreach
        } // foreach

        return $documents_rapport;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter les documents manquantes des evaluations
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ce sont en realite des images que les enseignants ont televersees pour les questions de leurs
     * evaluations.
     *
     * -------------------------------------------------------------------------------------------- */
	function detecter_documents_manquants_evaluations()
	{
		$objets = $this->lister_objets_s3(['repertoire' => 'evaluations']);

        $documents_manquants = array();
        $fichiers_manquants = array();

        $documents_verifies = 0;

        //
        // Extraire tous les documents
        //

        $documents = array();

        $this->db->from   ('documents as d');
        $this->db->where  ('d.efface', 0);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $documents = $query->result_array();
        }

        //
        // Verifier la presence des documents (documents) sur le disque
        //

        if ( ! empty($documents))
        {
            foreach($documents as $d)
            {
                if (empty($d['doc_filename']))
                    continue;

                if ($d['s3'])
				{
					if ( ! in_array($d['doc_filename'], $objets))
					{
                        $documents_manquants[$d['doc_id']] = $d;
                        $fichiers_manquants[$d['doc_id']]  = $d['doc_filename'];
                    }

                    $documents_verifies++;
                }
                else
                {
                    if ( ! file_exists(FCPATH . $this->config->item('documents_path') . $d['doc_filename']))
                    {
                        $documents_manquants[$d['doc_id']]  = $d;
                        $fichiers_manquants[$d['doc_id']] = $d['doc_filename'];
                    }
                    
                    $documents_verifies++;
                }

            }
        }

        return array(
            'documents_manquants' => $documents_manquants,
            'documents_verifies'  => $documents_verifies
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter les documents manquants des soumissions
     *
     * --------------------------------------------------------------------------------------------
	 *
	 * Il y a deux types de documents dans les soumissions :
	 *
	 * - les televersements des etudiants (dans la table documents_etudiants)
	 * - les images des questions (dans la table documents)
     *
     * -------------------------------------------------------------------------------------------- */
    function detecter_documents_manquants_soumissions()
	{
		$fichiers_manquants = [];

		$fichiers_verifies = [];
        $documents_verifies = 0;

		//
		// Verifier que les televersements des etudiants ne sont pas manquants
		//

		$objets = $this->lister_objets_s3(['repertoire' => 'soumissions']);

        $documents_etudiants = array();

        $this->db->where ('efface', 0);
        $query = $this->db->get($this->documents_etudiants_t);

        if ($query->num_rows() > 0)
		{
			$documents_etudiants = $query->result_array();
			$documents_etudiants = array_keys_swap($documents_etudiants, 'doc_id');
		}

		if ( ! empty($objets) && ! empty($documents_etudiants))
		{
			foreach($documents_etudiants as $d)
			{
				$d_arr = [
					'doc_id' => $d['doc_id'],
					'doc_filename' => $d['doc_filename'],
					'doc_mime_type' => $d['doc_mime_type'],
					'groupe_id' => $d['groupe_id'],
					'ajout_date' => $d['ajout_date'],
					's3' => $d['s3'],
					'des_soumissions' => 0,
					'soumission_id' => $d['soumission_id'],
					'soumission_reference' => $d['soumission_reference']
				];

				if ( ! empty($d['doc_filename']))
				{
					$fichiers_verifies[] = $d['doc_filename'];
					$documents_verifies++;
				
					if ($d['s3'])
					{
						if ( ! in_array($d['doc_filename'], $objets))
						{
							$fichiers_manquants[] = $d_arr;
						}
					}
					else
					{
						if ( ! file_exists(FCPATH . $this->config->item('documents_path') . $d['doc_filename']))
						{
							$fichiers_manquants[] = $d_arr;
						}
					}
				}

				if ( ! empty($d['doc_tn_filename']))
				{
					$d_arr['doc_filename'] = $d['doc_tn_filename'];
					$d_arr['doc_mime_type'] = $d['doc_tn_mime_type'];

					$fichiers_verifies[] = $d['doc_tn_filename'];
					$documents_verifies++;
				
					if ($d['s3'])
					{
						if ( ! in_array($d['doc_tn_filename'], $objets))
						{
							$fichiers_manquants[] = $d_arr;
						}
					}
					else
					{
						if ( ! file_exists(FCPATH . $this->config->item('documents_path') . $d['doc_tn_filename']))
						{
							$fichiers_manquants[] = $d_arr;
						}
					}
				}
			}
		}

		//
		// Verifier que les documents (images) des enseignants presentent dans 
		// les soumissions ne sont pas manquantes
		//

		$objets = $this->lister_objets_s3(['repertoire' => 'evaluations']);

        $soumissions = array();

        $this->db->select ('soumission_id, soumission_reference, images_data_gz');
        $this->db->where  ('efface', 0);
        
        $query = $this->db->get($this->soumissions_t);
        
        if ($query->num_rows() > 0)
        {
            $soumissions = $query->result_array();
        }

		if ( ! empty($objets) && ! empty($soumissions))
		{
            foreach($soumissions as $s)
            {
                if (empty($s['images_data_gz']))
                    continue;

				$images = json_decode(gzuncompress($s['images_data_gz']), TRUE);

                if (empty($images) || ! is_array($images))
					continue;

                foreach($images as $img)
				{
					$img_arr = [
						'doc_id' => $img['doc_id'],
						'doc_filename' => $img['doc_filename'],
						'doc_mime_type' => $img['doc_mime_type'],
						'groupe_id' => $img['groupe_id'],
						'ajout_date' => $img['ajout_date'],
						's3' => 1,
						'des_soumissions' => 1,
						'soumission_id' => $s['soumission_id'],
						'soumission_reference' => $s['soumission_reference']
					];

                    if (in_array($img['doc_filename'], $fichiers_verifies))
                        continue;

					if ( ! in_array($img['doc_filename'], $objets) && ! file_exists(FCPATH . $this->config->item('documents_path') . $img['doc_filename']))
					{
						$fichiers_manquants[] = $img_arr;
					}

                    $fichiers_verifies[] = $img['doc_filename'];
					$documents_verifies++;

                }
			}

        } // if

        return array(
            'fichiers_manquants' => $fichiers_manquants,
            'documents_verifies' => $documents_verifies
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter les documents etudiants manquants des soumissions
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ces documents sont ceux envoyes par les etudiants pour repondre aux questions de leurs
     * evaluations (soumissions).
     *
     * -------------------------------------------------------------------------------------------- */
    function detecter_documents_etudiants_manquants_soumissions()
    {
        $fichiers_manquants = array();
        $fichiers_verifies  = array(); // Pour eviter de verifier plusieurs fois le meme fichier
        $documents_verifies = 0;

        //
        // Extraire les soumissions
        //

        $soumissions = array();

        $this->db->select ('s.soumission_id');
        $this->db->from   ('soumissions as s');
        $this->db->where  ('s.efface', 0);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $soumissions = $query->result_array();
        }

        //
        // Extraire les documents etudiants
        //

        $documents = array();
        $fichiers = array();

        $this->db->select ('d.doc_id, d.soumission_id, d.s3, d.doc_filename, d.doc_sha256_file, d.doc_tn_filename, d.doc_tn_sha256_file, d.doc_mime_type, d.ajout_date, d.ajout_epoch, d.efface');
        $this->db->from   ('documents_etudiants as d');
        $this->db->where  ('d.efface', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $documents = $query->result_array();
        }

        //
        // Verifier les documents manquants
        //

        // Ceci ne peut pas etre fait car les documents, ou les pointeurs vers les documents, ne sont pas enregitres dans la soumission.
        // Par contre, a partir du 2020-10-21 18h00, les informations sur les documents etudiants sont enregistres dans la soumission.

        // Eventuellement, cette fonction pourra etre continuee.
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter les documents superflues
     *
     * --------------------------------------------------------------------------------------------
     *
     * Il y a deux sortes de documents (images) superflus :
     * 
     * 1. Les documents dont la drapeau 'efface' est a 1 de la table 'documents'. Avant de les effacer
     *    du disque ou de S3, il faut verifier si ces fichiers ne sont pas utilises pour d'autres 
     *    documents ou si des soumissions ne l'utilisent pas.
     *
     * 2. Les documents presents sur le disque dont aucune entree n'est trouvable dans la table
     *    'documents'. Il faut toutefois verifier qu'aucune soumission ne les utilisent.
     *
     * -------------------------------------------------------------------------------------------- */
    function OBSOLETE_detecter_documents_superflus($options = array())
    {
    	$options = array_merge(
            array(
                'effacement' => 0,
                'expiration' => 0  // 60*60*24*7; // 7 jours
           ),
           $options
        );

        $documents_superflus = array();
        $documents_superflus_disque = array();

        $expiration = 60*60*24 * $options['expiration'];

        //
        // Extraire tous les documents
        //

        $documents = array();

        $this->db->select ('d.doc_id, d.groupe_id, d.doc_filename, d.doc_sha256, d.doc_sha256_file, d.doc_mime_type, d.s3, d.ajout_date, d.ajout_epoch, d.efface');
        $this->db->from   ('documents as d');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $documents = $query->result_array();
        }

        //
        // Extraire les soumissions
        //

        $soumissions = array();

        $this->db->select ('s.soumission_id, s.images_data_gz');
        $this->db->from   ('soumissions as s');
        $this->db->where  ('s.efface', 0);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $soumissions = $query->result_array();
        }

        // ------------------------------------------------------------
        //
        // 1.
        //
        // ------------------------------------------------------------

        // ------------------------------------------------------------
        //
        // Trouver tous les documents effaces
        //
        // ------------------------------------------------------------

        $documents_effaces_candidats = array();
        $documents_effaces_candidats_sha256 = array();
        $documents_effaces_candidats_fichiers = array();

        foreach($documents as $d)
        {
            if ($d['efface'])
            {
                $documents_effaces_candidats[$d['doc_id']] = $d;
                $documents_effaces_candidats_sha256[$d['doc_id']] = $d['doc_sha256'];
                $documents_effaces_candidats_fichiers[$d['doc_id']] = $d['doc_filename'];
            }
        }

        if ( ! empty($documents_effaces_candidats))
        {
            //
            // Verifier qu'ils ne sont pas utilises par d'autres documents.
            //

            foreach($documents as $d)
            {
                if ($expiration)
                { 
                    if ($d['efface'] && $d['efface_epoch'] < (date('U') - $expiration))
                        continue;
                }
                else
                {
                    if ($d['efface'])
                        continue;
                }

                if (in_array($d['doc_sha256'], $documents_effaces_candidats_sha256))
                {
                    foreach($documents_effaces_candidats as $dec)
                    {
                        if ($d['doc_sha256'] == $dec['doc_sha256'])
                        {
                            unset($documents_effaces_candidats[$dec['doc_id']]);
                            unset($documents_effaces_candidats_fichiers[$dec['doc_id']]);
                        }
                    }
                }
            }

            //
            // Verifier qu'ils ne sont pas utilises dans les soumissions
            //
        
            if ( ! empty($soumissions))
            {
                foreach($soumissions as $s)
                {
                    if (empty($s['images_data_gz']))
                        continue;

                    $images = json_decode(gzuncompress($s['images_data_gz']), TRUE);

                    if (empty($images) || ! is_array($images))
                        continue;

                    foreach($images as $i)
                    {
                        if (in_array($i['doc_filename'], $documents_effaces_candidats_fichiers))
                        {
                            if (($key = array_search($i['doc_filename'], $documents_effaces_candidats_fichiers)) !== FALSE) 
                            {
                                unset($documents_effaces_candidats[$key]);
                            }
                        }
                    }
                } // foreach
            } // if ! empty($soumissions)

            $documents_superflus = $documents_effaces_candidats;
        } 

        // --------------------------------------------------------------------
        //
        // Trouver les documents (fichiers) candidats, i.e. ceux qui ne sont 
        // plus dans la base de donnees de la table 'documents'.
        //
        // --------------------------------------------------------------------
        
        $documents_fichiers = array_diff(scandir(FCPATH . $this->config->item('documents_path')), array('..', '.', 'index.html'));

        if ( ! empty($documents_fichiers))
        {
            foreach($documents_fichiers as $df)
            {
                if (preg_match('/^dev_/', $df))
                {
                    $documents_superflus_disque[] = $df;
                    continue;
                }

                $trouve = FALSE;

                foreach($documents as $d)
                {
                    if ($d['doc_filename'] == $df)
                    {
                        $trouve = TRUE;
                        break;
                    }
                }

                if ( ! $trouve)
                {
                    $documents_superflus_disque[] = $df;
                }
            }
        }

        // ------------------------------------------------------------
        //
        // 2.
        //
        // ------------------------------------------------------------

        //
        // Verifier qu'aucune soumission n'utilise ces documents
        //
        
        if ( ! empty($documents_superflus_disque))
        {
            if ( ! empty($soumissions))
            {
                foreach($soumissions as $s)
                {
                    if (empty($s['images_data_gz']))
                        continue;

                    $images = json_decode(gzuncompress($s['images_data_gz']), TRUE);

                    if (empty($images) || ! is_array($images))
                        continue;

                    foreach($images as $i)
                    {
                        if (in_array($i['doc_filename'], $documents_superflus_disque))
                        {
                            if (($key = array_search($i['doc_filename'], $documents_superflus_disque)) !== FALSE) 
                            {
                                    unset($documents_superflus_disque[$key]);
                            }
                        }
                    }
                } // foreach

            } // if ! empty($soumissions)
        }

        //
        // Notes
        //
        // $fichiers_a_effacer  : contient le nom des fichiers de $documents_superflus,
        //                        ces documents peuvent etre sur le disque ou sur S3,
        //                        ne contient pas de doublon
        //
        // $documents_superflus : contient les documents de la base de donnees a effacer,
        //                        contient des doublons pour les noms de fichiers
        //
        // $documents_superflus_disque : contient les noms de fichiers du disque a effacer
        //                               ne contient pas de doublon

        $fichiers_a_effacer = array_column($documents_superflus, 'doc_filename');
        $fichiers_a_effacer = array_unique($fichiers_a_effacer);

        return array(
            'documents_superflus' => $documents_superflus,
            'documents_superflus_disque' => $documents_superflus_disque
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Detecter les documents superflus des soumissions
     *
     * -------------------------------------------------------------------------------------------
     *
     * Il s'agit de :
     *
     * - documents de soumissions purgees
     * - documents d'evaluations dedbutees jamais envoyees
     *
     * -------------------------------------------------------------------------------------------- */
    function OBSOLETE_detecter_documents_superflus_soumissions($options = array())
    {
    	$options = array_merge(
            array(
                'expiration' => 0,  // 60*60*24*7; // 7 jours
				'groupe_id'  => NULL 
           ),
           $options
        );

		$documents_superflus  = array();
		$documents_soumissions = array();

		//
		// Extraire toutes les soumissions (incluant les soumissions effacees)
		//

		$this->db->select ('s.soumission_id');
        $this->db->from   ('soumissions as s');

		$query = $this->db->get();

		if ( ! $query->num_rows() > 0)
		{
			return array();
		}
	
		$soumission_ids = $query->result_array();
		$soumission_ids = array_column($soumission_ids, 'soumission_id');

		//
		// Extraire les documents des etudiants
		//

		$this->db->select ('d.doc_id, d.soumission_id, d.groupe_id, d.s3, d.doc_filename, d.doc_mime_type, d.ajout_date, d.ajout_epoch, d.evaluation_reference, d.efface');
		$this->db->from   ('documents_etudiants as d');
		
		$query = $this->db->get();

		if ( ! $query->num_rows() > 0)
		{
			return array();
		}
		
        $documents = $query->result_array();
        
        //
        // Extraire tous les semestres actifs
        //

        $semestre_ids_actifs = array();

        $this->db->from  ('semestres');
        $this->db->where ('semestre_debut_epoch <=', $this->now_epoch);
        $this->db->where ('semestre_fin_epoch >=', $this->now_epoch);

        $query = $this->db->get();

        if ($query->num_rows())
        {
            $semestre_ids_actifs = array_column($query->result_array(), 'semestre_id');
        }

        //
        // Extraire toutes les evaluations en ligne de semestres actifs
        //

        $evaluation_references = array();

        if ( ! empty($semestre_ids_actifs))
        {
            $this->db->from     ('rel_enseignants_evaluations');
            $this->db->where    ('efface', 0);
            $this->db->where_in ('semestre_id', $semestre_ids_actifs);

            $query = $this->db->get();

            if ($query->num_rows())
            {
                $evaluation_references = array_column($query->result_array(), 'evaluation_reference');
            }
        }

		foreach($documents as $d)
		{
			if (empty($d['soumission_id']))
            {
				//
				// Ce document n'a jamais ete soumis.
                // Il est possible que l'etudiant soit en train de rediger son evaluation.
                //

                if (empty($d['evaluation_reference']))
                {
					$documents_superflus[] = $d;
					continue;
                }

                //
                // Verifier si ce document fait parti d'une evaluation en redaction (d'un semestre actif)
                //
        
                if (in_array($d['evaluation_reference'], $evaluation_references))
                {
                    continue; 
                }

                // 
                // Le document superflus, lui donner une marge de surete
				//

				if ($d['ajout_epoch'] < ($this->now_epoch - $options['expiration']))
				{	
					$documents_superflus[] = $d;
					continue;
				}
			}

			elseif ( ! in_array($d['soumission_id'], $soumission_ids))
			{
				$documents_superflus[] = $d;
			}
		}

		//
		// Ces documents peuvent etre effaces et purges.
		// 

		return $documents_superflus;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Lister les objets S3
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_objets_s3($options = array())
    {
    	$options = array_merge(
			array(
				'repertoire' => 'evaluations'
           ),
           $options
		);

		$s3Client = new S3Client([
			'version' 		=> '2006-03-01',
			'region' 		=> $this->config->item('region', 'amazon'),
			'credentials' 	=> [
				'key'    => $this->config->item('api_key', 'amazon'),
				'secret' => $this->config->item('api_secret', 'amazon'),
			]			
		]);

		$results = $s3Client->getPaginator('ListObjectsV2', [
			'Bucket' => 'kovao',
			'Prefix' => $options['repertoire'] . '/'
		]);

		$objets = [];

		foreach ($results as $result) 
		{
			if (isset($result['Contents'])) 
			{
				foreach ($result['Contents'] as $object)
				{
					if ($object['Size'] == 0)
						continue; 

					$key = $object['Key'];

					$doc_filename = basename($key);

					$objets[] = $doc_filename;
				}
			}
		}

		return $objets;
	}
}
