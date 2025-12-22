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
 * CLI MODEL
 *
 *============================================================================= */

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;		

class Cli_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Terminer les evaluations expirees
     *
     * --------------------------------------------------------------------------------------------
     *
     * Version 2 (2021-01-07)
     *
     * - Les evaluations ne peuvent se terminer plus tard que la fin d'un semestre.
     *
     * -------------------------------------------------------------------------------------------- */
    function terminer_evaluations_expirees()
    {
        //
        // Extraire les evaluations expirees
        //

        $this->db->from     ('rel_enseignants_evaluations');
        $this->db->where    ('fin_epoch !=', NULL);
        $this->db->where    ('fin_epoch >', 0);
        $this->db->where    ('fin_epoch <', date('U'));
        $this->db->where    ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            //
            // Aucune evaluation a terminer trouvee
            //

            return;
        }
                                                                                                                                                                                                                                  
        $rel_evaluations = $query->result_array();

        $this->db->trans_begin();

        foreach($rel_evaluations as $rel_evaluation)
        {
            //
            // Enregistrer toutes les evaluations non-terminees des etudiants
            //

            if ($this->config->item('evaluations_non_terminees'))
            {
               $this->Soumission_model->mettre_hors_ligne($rel_evaluation['evaluation_reference']);
            }
        }

        $this->db->trans_commit();

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Terminer les evaluations temps limite
     *
     * --------------------------------------------------------------------------------------------
     *
     * 2024-10-12 : version 2
     *
     * -------------------------------------------------------------------------------------------- */
    function terminer_evaluations_temps_limite()
    {
        // $this->now_epoch n'est pas disponible car cette function est executee a partir du CLI.

        $maintenant = date('U');

        //
        // Extraire les evaluations ayant un temps limite et presentement actives
        //

        $this->db->from     ('rel_enseignants_evaluations');
        $this->db->where    ('temps_limite >', 0);
        $this->db->where    ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            //
            // Aucune evaluation a terminer trouvee
            //

            return;
        }
                                                                                                                                                                                                                                  
        $rel_evaluations = $query->result_array();

        // Les traces des etudiants pour chaque evaluation.

        $rel_evaluations_traces = array();

        foreach($rel_evaluations as $re)
        {
            if (empty($re['temps_limite']) || $re['temps_limite'] == 0 || $re['temps_limite'] < 0)
                continue;

            // Le temps limite de l'evaluation

            $temps_limite = $re['temps_limite'];            

            //
            // Extraire les traces des etudiants
            //

            $this->db->from   ('etudiants_traces');
            $this->db->select ('etudiant_id, semestre_id, soumission_debut_epoch');
            $this->db->where  ('evaluation_reference', $re['evaluation_reference']);
            $this->db->where  ('evaluation_terminee', 0);
            $this->db->where  ('evaluation_envoyee', 0);
            $this->db->where  ('efface', 0);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                // Les traces des etudiants

                $evaluations_en_cours = $query->result_array();

                foreach($evaluations_en_cours as $eec)
                {   
                    $temps_supp = 0;

                    $secondes_ecoulees = $maintenant - $eec['soumission_debut_epoch'];

                    if ($secondes_ecoulees > ($temps_limite * 60))
                    {
                        // Le temps limite pour l'evaluation est atteint.

                        //
                        // Verifions si l'etudiant a droit a du temps supplementaire
                        //
                        // (Les laboratoires n'ont pas de temps supplementaire)
                        //

                        $temps_supp = 0;

                        if ( ! $re['lab'])
                        {
                            $temps_supp = $this->Etudiant_model->extraire_etudiant_id_temps_supp(
                                $eec['etudiant_id'], 
                                array(
                                    'cours_id'    => $re['cours_id'],
                                    'groupe_id'   => $re['groupe_id'],
                                    'semestre_id' => $re['semestre_id']
                                )
                            );
                        }
                                
                        //
                        // Cet etudiant a droit a du temps supplementaire, verifions si le nouveau temps limite (n_temps_limite) est atteint.
                        //

                        if ($temps_supp > 0)
                        {
                            $n_temps_limite = $temps_limite + ($temps_limite * $temps_supp/100);

                            // arrondir a la minute superieure
                            $n_temps_limite = ceil($n_temps_limite);

                            if ($secondes_ecoulees < ($n_temps_limite * 60))
                            {
                                // Finalement le temps limite n'est pas atteint pour cet etudiant qui a du temps supplementaire.
                                continue;
                            }
                        }

                        //
                        // Terminer l'evaluation 
                        //

                        $this->Soumission_model->enregistrer_soumission_traces(
                            $re['evaluation_reference'],
                            array(
                                'etudiant_id' => $eec['etudiant_id']
                            )
                        ); 

                        // Ecrire le log

						$action       = "Le temps limite permis (" . $temps_limite . ' minute' . ($temps_limite > 1 ? 's' : '') . ") est échu.";
						$action_court = 'temps_echu';

						$this->Evaluation_model->ecrire_activite_evaluation(
							array(
								'action'                => $action,
								'action_court'          => $action_court,
								'etudiant_id'			=> $eec['etudiant_id'],
								'semestre_id'			=> $re['semestre_id'],
								'evaluation_id'         => $re['evaluation_id'],
								'evaluation_reference'  => $re['evaluation_reference'],
								'planificateur'		    => TRUE
							)
						);
                    }

                } // foreach evaluations_en_cours

            } // if rows exists

        } // foreach rel

        return;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Purger les sessions expirees
     *
     * -------------------------------------------------------------------------------------------- */
    function purger_sessions()
    {
        $effacements = 0;

        //
        // Charger toutes les sessions
        // 

        $query = $this->db->get('ci_sessions');
        
        if ( ! $query->num_rows() > 0)
             return 0;
                                                                                                                                                                                                                                  
        $sessions = $query->result_array();

        //
        // Effacer les sessions plus vieilles que l'interval voulu
        //

		if ( ! empty($sessions))
			return 0;

		$interval = date('U') - (60*60*24*7); // 7 jours

		$session_ids = [];

		foreach($sessions as $s)
		{
			if ($s['timestamp'] < ($interval))
			{
				$session_ids[] = $s['id'];
			}
		}

		if ( ! empty($session_ids))
		{
				$this->db->where_in('id', $session_ids);
				$this->db->delete('ci_sessions');
		}

        return count($session_ids);
	}

    /* --------------------------------------------------------------------------------------------
     *
	 * Purger les documents des enseignants
	 *
	 * --------------------------------------------------------------------------------------------
	 *
	 * Un meme document peut etre utilise dans plusieurs evaluations appartenant a des 
	 * enseignants differents. Il faut donc faire attention a ne pas supprimer le fichier sur
	 * le disque/S3 car il pourrait etre encore utilise.
	 *
     * -------------------------------------------------------------------------------------------- */
	function purger_documents_enseignants($jours = 180)
	{
		try 
		{
			$maintenant = new DateTimeImmutable();
			$intervalle = new DateInterval('P' . $jours . 'D'); // P180D

			$date_passee = $maintenant->sub($intervalle);

			$epoch = $date_passee->getTimestamp();
		} 
		catch (Exception $e) 
		{
			exit(9);
		}

		//
		// Preparer le rapport
		//

		$rapport = [
			'cli' 	 => 1,
			'erreur' => 0,
			'action' => 'purger_documents_enseignants',
			'epoch'  => date('U'),
			'date'   => date_humanize(date('U'), TRUE)
		];

		$rapport_data = [
			'documents_supprimes' => 0,
			'erreurs_suppressions' => 0,
			'documents' => []
		];

		$json_options = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

		//
		// Extraire les empreintes (sha256) de tous les documents existants
		//

		$this->db->where('efface', 0);

		$query = $this->db->get($this->documents_t);

        if ( ! $query->num_rows() > 0)
             return 0;

		$docs_sha256 = array_column($query->result_array(), 'doc_sha256');
		$docs_sha256 = array_unique($docs_sha256);

		//
		// Extraire tous les documents a effacer respectant le critere d'expiration en jours
		//
		
		$this->db->where('efface', 1);
		$this->db->where('efface_epoch <', $epoch);

		$query = $this->db->get($this->documents_t);

        if ( ! $query->num_rows() > 0)
             return 0;

		$docs = $query->result_array();
		$doc_ids = array_column($docs, 'doc_id');

		//
		// Determiner les documents dont il faut supprimer le fichier du disque/S3
		//

		$docs_a_supprimer = [];

		foreach($docs as $d)
		{
			if (in_array($d['doc_sha256'], $docs_sha256))
				continue;

			$docs_a_supprimer[] = $d;
		}

		//
		// Supprimer les documents enseignants (des evaluations)
		//

		$s3Client = new S3Client([
			'version' 		=> '2006-03-01',
			'region' 		=> $this->config->item('region', 'amazon'),
			'credentials' 	=> [
				'key'    => $this->config->item('api_key', 'amazon'),
				'secret' => $this->config->item('api_secret', 'amazon'),
			]			
		]);

		$bucket = 'kovao';

		$taille_supprimee = 0;

		$doc_ids_supprimes = [];

		if ( ! empty($docs_a_supprimer))
		{
			foreach($docs_a_supprimer as $d)
			{
				if ( ! $d['s3'])
					continue;

				try 
				{
					if ($s3Client->doesObjectExist($bucket, 'evaluations/' . $d['doc_filename'])) 
					{
						$result = $s3Client->deleteObject([
							'Bucket' => $bucket,
							'Key'    => 'evaluations/' . $d['doc_filename'], // Nom exact du fichier dans S3
						]);

						$taille_supprimee += $d['doc_filesize'];
					}

					$doc_ids_supprimes[] = $d['doc_id'];
				}
				catch (Aws\S3\Exception\S3Exception $e)
				{
					echo 'E'; 
					
					$rapport_data['erreurs_suppressions']++;

					$rapport_data['documents'][] = [
						'doc_id'	   => $d['doc_id'],	
						'question_id'  => $d['question_id'],
						'doc_filename' => $d['doc_filename'],
						'doc_sha256'   => $d['doc_sha256'],
						'doc_filesize' => $d['doc_filesize'],
						'erreur'	   => 1
					];

					continue;
				}

				$rapport_data['documents'][] = [
					'doc_id'	   => $d['doc_id'],	
					'question_id'  => $d['question_id'],
					'doc_filename' => $d['doc_filename'],
					'doc_sha256'   => $d['doc_sha256'],
					'doc_filesize' => $d['doc_filesize']
				];

				$rapport_data['documents_supprimes']++;

				echo '.';
			}
		} // ! empty $docs_a_supprimer

		//
		// Effacer les lignes de tous les documents a supprimer
		//

		$this->db->where_in('doc_id', $doc_ids);
		$this->db->delete($this->documents_t);

		//
		// Ecrire le rapport
		//

		$rapport_data['taille_supprimee'] = $taille_supprimee;
		$rapport['data'] = json_encode($rapport_data, $json_options);

		$this->db->insert('rapports_maintenance', $rapport);

		echo "\n";

		exit;
	}

    /* --------------------------------------------------------------------------------------------
     *
	 * Purger les documents des etudiants (soumissions)
	 *
	 * --------------------------------------------------------------------------------------------
	 *
	 * Les documents sont accompagnes de leur 'thumbnail' qu'il faut egalement supprimer.
	 *
     * -------------------------------------------------------------------------------------------- */
	function purger_documents_etudiants($jours = 30)
	{
		try 
		{
			$maintenant = new DateTimeImmutable();
			$intervalle = new DateInterval('P' . $jours . 'D'); // P180D

			$date_passee = $maintenant->sub($intervalle);

			$epoch = $date_passee->getTimestamp();
		} 
		catch (Exception $e) 
		{
			exit(9);
		}

		//
		// Preparer le rapport
		//

		$rapport = [
			'cli' 	 => 1,
			'erreur' => 0,
			'action' => 'purger_documents_etudiants',
			'epoch'  => date('U'),
			'date'   => date_humanize(date('U'), TRUE)
		];

		$rapport_data = [
			'documents_supprimes' => 0,
			'erreurs_suppressions' => 0,
			'documents' => []
		];

		$json_options = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

		//
		// Extraire tous les documents a effacer respectant le critere d'expiration en jours
		//
		
		$this->db->where('efface', 1);
		$this->db->where('efface_epoch <', $epoch);

		$query = $this->db->get($this->documents_etudiants_t);
		$query = $this->db->get($this->documents_etudiants_t);

        if ( ! $query->num_rows() > 0)
             return 0;

		$docs = $query->result_array();
		$doc_ids = array_column($docs, 'doc_id');

		//
		// Supprimer les documents etudiants (des soumissions)
		//

		$s3Client = new S3Client([
			'version' 		=> '2006-03-01',
			'region' 		=> $this->config->item('region', 'amazon'),
			'credentials' 	=> [
				'key'    => $this->config->item('api_key', 'amazon'),
				'secret' => $this->config->item('api_secret', 'amazon'),
			]			
		]);

		$bucket = 'kovao';

		$taille_supprimee = 0;

		foreach($docs as $d)
		{
			if ( ! $d['s3'])
				continue;

			try 
			{
				if ($s3Client->doesObjectExist($bucket, 'soumissions/' . $d['doc_filename'])) 
				{
					$result = $s3Client->deleteObject([
						'Bucket' => $bucket,
						'Key'    => 'soumissions/' . $d['doc_filename'], // Nom exact du fichier dans S3
					]);

					$taille_supprimee += $d['doc_filesize'];
				}

				if ($s3Client->doesObjectExist($bucket, 'soumissions/' . $d['doc_tn_filename'])) 
				{
					$result = $s3Client->deleteObject([
						'Bucket' => $bucket,
						'Key'    => 'soumissions/' . $d['doc_filename'], // Nom exact du fichier dans S3
					]);

					$taille_supprimee += $d['doc_tn_filesize'];
				}

			}
			catch (Aws\S3\Exception\S3Exception $e)
			{
				echo 'E'; 
				
				$rapport_data['erreurs_suppressions']++;

				$rapport_data['documents'][] = [
					'doc_id'	   	  => $d['doc_id'],	
					'question_id'  	  => $d['question_id'],
					'doc_filename' 	  => $d['doc_filename'],
					'doc_tn_filename' => $d['doc_tn_filename'],
					'doc_sha256_file' => $d['doc_sha256_file'],
					'doc_filesize'    => $d['doc_filesize'],
					'doc_tn_filesize' => $d['doc_tn_filesize'],
					'erreur'	      => 1
				];

				continue;
			}

			$rapport_data['documents'][] = [
				'doc_id'	      => $d['doc_id'],	
				'question_id'     => $d['question_id'],
				'doc_filename'    => $d['doc_filename'],
				'doc_tn_filename' => $d['doc_filename'],
				'doc_sha256_file' => $d['doc_sha256_file'],
				'doc_filesize'    => $d['doc_filesize'],
				'doc_tn_filesize' => $d['doc_tn_filesize']
			];

			$rapport_data['documents_supprimes']++;

			echo '.';
		}

		//
		// Effacer les lignes de tous les documents a supprimer
		//

		$this->db->where_in('doc_id', $doc_ids);
		$this->db->delete($this->documents_etudiants_t);

		//
		// Ecrire le rapport
		//

		$rapport_data['taille_supprimee'] = $taille_supprimee;
		$rapport['data'] = json_encode($rapport_data, $json_options);

		$this->db->insert('rapports_maintenance', $rapport);

		echo "\n";

		exit;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Purger les documents (images) effaces des evaluations 
     *
     * version 4 (2020-10-17)
     *
     * -------------------------------------------------------------------------------------------- */
    function OBSOLETE_purger_documents($effacement, $expiration_jours = 0)
    {
        //
        // (!)
        // Cette fonction semble purger des documents qu'il ne faut pas purger.
        // Pourtant, elle a ete en production pendant plusieurs mois et je n'ai rien perdu.
        //
        // Il est suggere de la verifier ou d'utiliser une fonction plus conservatrice qui
        // est Document_model->documents_superflus()
        //
        
        exit;

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        //
        // Ces documents sont en realite des images
        //

        $effacements_documents = 0;
        $effacements_fichiers  = 0;

        $expiration = 60*60*24 * $expiration_jours; // 60*60*24*30; // 30 jours

        //
        // Extraire les documents a effacer (et expire selon le parametre)
        //

        $this->db->from  ('documents');
        $this->db->where ('efface', 1);
        $this->db->where ('efface_epoch <', date('U') - $expiration);

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return 'Aucun document à effacer.';
        }

        $documents_a_effacer = $query->result_array();
        $documents_a_effacer = array_keys_swap($documents_a_effacer, 'doc_id');

        //
        // Extraire tous les documents qui ne sont pas a effacer
        //

        $documents = array();

        $this->db->from  ('documents');
        $this->db->where ('efface', 0);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $documents = array_keys_swap($documents, 'doc_id');
        }

        $document_ids = array_keys($documents);

        //
        // Extraire toutes les soumissions
        //

        $soumissions = $this->Evaluation_model->extraire_toutes_soumissions();

        //
        // Determiner les fichiers a conserver
        //
        // 1. Les documents utilisant ces fichiers
        // 2. Les soumissions utilisant ces fichiers
        //
    
        $fichiers_sha256_a_conserver = array(); // pour les documents et soumissions
        $fichiers_noms_a_conserver   = array(); // pour les soumissions (retrocompatibilite)

        // 1. Les documents utilisant ces fichiers

        foreach($documents as $d)
        {
            if ( ! in_array($d['doc_sha256'], $fichiers_sha256_a_conserver))
            {
                $fichiers_sha256_a_conserver[] = $d['doc_sha256'];
            }    
        }

        // 2. Les soumissions utilisant ces fichiers
        
        if ( ! empty($soumissions))
        {
            foreach($soumissions as $s)
            {
                if (empty($s['images_data_gz']))
                    continue;

                $images = json_decode(gzuncompress($s['images_data_gz']), TRUE);

                if (empty($images) || ! is_array($images))
                {
                    continue;
                }

                foreach($images as $i)
                {
                    // Par precaution et pour retrocompatibilite, ne pas effacer les documents avant l'instauration des empreintes
                    // (quelques documents seulement, ce qui ne devrait pas affecter l'espace utilise).
                    // Il faut utiliser le nom du fichier pour detecter ces fichiers.

                    if ( ! array_key_exists('doc_sha256', $i) || empty($i['doc_sha256']))
                    {
                        if ( ! in_array($i['doc_filename'], $fichiers_noms_a_conserver))
                        {
                            $fichiers_noms_a_conserver[] = $i['doc_filename'];
                        }

                        continue;
                    }

                    if ( ! in_array($i['doc_sha256'], $fichiers_sha256_a_conserver))
                    {
                        $fichiers_sha256_a_conserver[] = $i['doc_sha256'];
                    }
                }
            }
        }

        //
        // 3. Effacer les documents et fichiers
        //

        $this->db->trans_begin();

        $fichiers_a_effacer = array();
        $taille_liberee     = 0;

        foreach($documents_a_effacer as $doc_id => $doc)
        {
            //
            // Effacement de l'entree dans la base de donnees
            //

            if ($effacement == 1)
            {
                $this->db->where ('doc_id', $doc_id);
                $this->db->where ('efface', 1);
                $this->db->delete('documents');

                $effacements_documents++;
            }

            //
            // Est-ce qu'il faut conserver le fichier?
            //

            if (in_array($doc['doc_sha256'], $fichiers_sha256_a_conserver))
            {
                continue;
            }

            if (in_array($doc['doc_filename'], $fichiers_noms_a_conserver))
            {
                continue;
            }    

            $fichiers_a_effacer[] = $doc['doc_filename'];

            //
            // Effacement du fichier sur le disque
            // 

            if ($effacement == 1)
            {
                // Ne pas effacer les anciens fichiers (sans empreinte) present dans les soumissions.
                // Le nom du fichier est la seule facon de les detecter.

                /*
                if ($doc['s4'])
                {
                    if ( ! $this->Document_model->_effacer_s3(array('dossier' => 'evaluations', 'key' => $doc['doc_filename'])))
                    {
                        $this->db->trans_rollback();
                        return "Ce fichier ne peut être effacé de S3.";
                    }
                    else
                    {
                        $effacements_fichiers++;
                    }
                }
                else
                {
                    if ( ! empty($doc['doc_filename']) && file_exists(FCPATH . $this->config->item('documents_path') . $doc['doc_filename']))
                    {
                        if ( ! unlink(FCPATH . $this->config->item('documents_path') . $doc['doc_filename']))
                        {
                            $this->db->trans_rollback();
                            return "Ce fichier ne peut être effacé. Vérifier les permissions du système de fichiers.";
                        }
                        else
                        {
                            $effacements_fichiers++;
                        }
                    }
                }
                */

            } // effacement == 1
        }

        $this->db->trans_commit();

        if ($effacement == 1)
        {
            $str = $effacements_documents . ' document' . ($effacements_documents > 1 ? 's' : '') . ', ' 
                    . $effacements_fichiers . ' fichier' . ($effacements_fichiers > 1 ? 's' : '');
        }
        else
        {
            $fichiers_a_effacer = array_unique($fichiers_a_effacer);

            $str  = count($documents_a_effacer) . ' document(s) à effacer de la base de données';
            $str .= "\n";
            $str .= count($fichiers_a_effacer) . ' fichier(s) à effacer du disque ou de S3';
            $str .= "\n";
            $str .= 'Emplacement : ' . FCPATH . $this->config->item('documents_path');
            $str .= "\n";

            p($fichiers_a_effacer);
        }

        return $str;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Purger les items
     *
     * --------------------------------------------------------------------------------------------
	 * 
	 * Cette fonction permet de supprimer les items effaces.
	 *
	 * (!) Les documents doivent etre supprimes en meme temps que le fichier sur le disque/S3, dans
	 *     une autre function dediee a cette fin.
	 *
	 * version 2 (2025-12-19)
     *
     * -------------------------------------------------------------------------------------------- */
    function purger_items()
	{
		// supprimer les items effaces depuis plus de...
		//  30 :  30 jours
		// 180 : 180 jours

		$tables_a_purger = [
			30 => [
				'activite', 'activite_debug', 'activite_evaluation', 
				'courriels_envoyes', 
				'cours',
				'ecoles',
				'enseignants', 'enseignants_groupes', 'enseignants_groupes_demandes', 'enseignants_traces',
				'etudiants', 'etudiants_cours', 'etudiants_evaluations_notifications', 'etudiants_numero_da', 'etudiants_traces',
				'groupes',
				'inscriptions', 'inscriptions_invitations',
				'usagers_oubli_motdepasse'
			],
			180 => [
				'blocs',
				'evaluations', 'evaluations_ponderations', 'evaluations_securite_chargements', 
				'questions', 'questions_grilles_correction', 'questions_grilles_correction_elements',
				'questions_similarites', 'questions_tolerances',
				'rel_enseignants_evaluations',
				'reponses',
				'semestres',
				'soumissions', 'soumissions_consultees', 'soumissions_partagees',
				'variables'
			]
		];

		//
		// Preparer le rapport
		//
		
		$rapport = [
			'cli' 	 => is_cli(),
			'action' => 'purger_items',
			'epoch'  => date('U'),
			'data'	 => [],
			'date'   => date_humanize(date('U'), TRUE)
		];

		$json_options = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

		//
		// Supprimer les items de plusieurs tables
		//

		$tables = $this->db->list_tables();

		$this->db->trans_begin();

		foreach($tables_a_purger as $expiration => $t_arr)
		{
			try 
			{
				$maintenant = new DateTimeImmutable();
				$intervalle = new DateInterval('P' . $expiration . 'D'); // P180D

				$date_passee = $maintenant->sub($intervalle);

				$epoch = $date_passee->getTimestamp();
			} 
			catch (Exception $e) 
			{
				$rapport['erreur'] = 1;
				$rapport['data'] = json_encode(['erreur_msg' => 'erreur de date' . ' ' . $e->getMessage()], $json_options);

				$this->db->insert('rapports_maintenance', $rapport);
				exit;
			}

			foreach($t_arr as $t)
			{
				if ( ! in_array($t, $tables))
				{
					$rapport['data'][$t] = 'inexistante';
					continue;
				}

				$key = $this->db->primary($t);
				
				$this->db->reset_query();

				$this->db->where('efface', 1);

				if ($t == 'usagers_oubli_motdepasse')
				{
					$this->db->where('clef_utilisee_epoch <', $epoch);
					$this->db->or_where('efface_epoch <', $epoch);
				}
				else
				{
					$this->db->where('efface_epoch <', $epoch);
				}

				$query = $this->db->get($t);

				if ( ! $query->num_rows() > 0)
				{
					$rapport['data'][$t] = 0;
					continue;
				}

				$ids_a_supprimer = [];

				foreach($query->result_array() as $row)
				{
					$ids_a_supprimer[] = $row[$key];
				}

				if ( ! empty($ids_a_supprimer))
				{
					$count = 0;

					$chunk_size = 500;
					$chunks = array_chunk($ids_a_supprimer, $chunk_size);

					foreach($chunks as $c)
					{	
						$this->db->where_in($key, $c);
						$this->db->delete($t);
						
						$count += $this->db->affected_rows();
					}
					
					$rapport['data'][$t] = $count;
				}
			}
		}

		//
		// Rediger le rapport
		//

		$rapport['data'] = json_encode($rapport['data'], $json_options);
		$this->db->insert('rapports_maintenance', $rapport);

        if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();

			$rapport['erreur'] = 1;
			$this->db->insert('rapports_maintenance', $rapport);

            exit;
		}

        $this->db->trans_commit();

		exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Enregistrer les nouveaux domaines de courriels jetables
     *
     * ------------------------------------------------------------------------ */
    function nouveaux_courriels_jetables($securite = NULL)
    {
        //
        // La liste est mise a jour regulierement : 
		// https://github.com/tompec/disposable-email-domains
        //

        $domaines_url = 'https://github.com/tompec/disposable-email-domains/raw/refs/heads/main/index.json';

        if ($securite != 1)
        {
            echo "\n" . 'Lien a verifier: ' . $domaines_url . "\n";
			echo 'Verifier le lien URL avant de charger la mise a jour des courriels jetables. Ensuite, utilisez 1 pour confirmer.' . "\n\n";
			exit;
        }

		//
		// Telecharger la liste
		//

		$domaines = @file_get_contents($domaines_url);

		if ($domaines === FALSE) 
		{
			echo 'Impossible de recuperer la liste';
			exit(9);
		}

        $domaines = json_decode($domaines, TRUE);

		//
		// Verifier que le tableau n'est pas vide.
		//

		if (empty($domaines))
		{
			exit(9);
		}

		//
		// Verifier que le tableau est unidimensionnel tel que suppose etre le json
		//

		if (count($domaines) !== count($domaines, COUNT_RECURSIVE))
		{
			exit(9);
		}
		
        //
        // Extraire les domaines deja reconnus
        //

        $domaines_db = array();

        $query = $this->db->get('inscriptions_courriels_jetables');

        if ($query->num_rows() > 0)
        {
            $domaines_db = array_column($query->result_array(), 'domaine');
        }

        //
        // Trouver les nouveaux domaines
        //

        $data = array();

        $nouveaux = array_diff($domaines, $domaines_db);

        if ( ! empty($nouveaux))
        {
            foreach($nouveaux as $d)
			{
				if ( ! filter_var($d, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME))
					continue;

                $data[] = array(
                    'domaine' => $d
                );
            } 
        }

        //
        // Ajouter les nouveaux domaines
        //

        if ( ! empty($data))
        {
            $this->db->insert_batch('inscriptions_courriels_jetables', $data);
        }

		echo 'nombre de domaines ajoutes = ' . count($data) . "\n";
        exit;
    }
}
