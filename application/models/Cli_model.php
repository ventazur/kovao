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
	 * (!) Un meme document peut etre utilise dans plusieurs evaluations appartenant a des 
	 * enseignants differents. Il faut donc faire attention a ne pas supprimer le fichier sur
	 * le disque/S3 car il pourrait etre encore utilise.
	 *
	 * (!!) Un document (image) qui semble efface de toutes les evaluations pourraient se trouver
	 *      encore utile dans les soumissions residuelles. Il ne faut pas les purger.
	 *
	 * $effacement est la surete
	 *
     * -------------------------------------------------------------------------------------------- */
	function purger_documents_enseignants($effacement = 0, $jours = 180)
	{
		$maintenant = new DateTimeImmutable();
		$intervalle = new DateInterval('P' . $jours . 'D'); // P180D

		$date_passee = $maintenant->sub($intervalle);

		$epoch = $date_passee->getTimestamp();

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
			'fichiers_supprimes'  => 0,
			'fichiers' => []
		];

		$json_options = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

		//
		// Extraire le nom des fichiers de tous les documents existants
		//

		$docs_fichiers = [];

		$this->db->where('efface', 0);

		$query = $this->db->get($this->documents_t);

		if ($query->num_rows() > 0)
		{
			$docs_fichiers = array_column($query->result_array(), 'doc_filename');
			$docs_fichiers = array_unique($docs_fichiers);
		}

		//
		// Extraire tous les documents a effacer respectant le critere d'expiration en jours
		//

		$docs_effaces = [];

		$this->db->where('efface', 1);
		$this->db->where('efface_epoch <', $epoch);

		$query = $this->db->get($this->documents_t);

		if ($query->num_rows() > 0)
		{
			$docs_effaces = $query->result_array();
			$docs_effaces_ids = array_column($docs_effaces, 'doc_id');
			$docs_effaces_fichiers = array_column($query->result_array(), 'doc_filename');
			$docs_effaces_fichiers = array_unique($docs_effaces_fichiers);
		}

		//
		// Extraire les images des soumissions
		//

		$soumissions = [];
		$soumissions_images = [];

		$this->db->where('efface', 0);

		$query = $this->db->get($this->soumissions_t);

		if ($query->num_rows() > 0)
		{
			$soumissions = $query->result_array();
		}

		if ( ! empty($soumissions))
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
					$soumissions_images[] = $img['doc_filename'];
                }
			}
		}

		//
		// Determiner les fichiers dont il faut supprimer le fichier du disque/S3
		//

		$fichiers_a_supprimer = [];

		foreach($docs_effaces_fichiers as $f)
		{
			if (in_array($f, $docs_fichiers))
				continue;

			if (in_array($f, $soumissions_images))
				continue;

			$fichiers_a_supprimer[] = $f;
		}

		//
		// Supprimer les fichiers
		//

		if ( ! empty($fichiers_a_supprimer))
		{
			echo 'Suppression de ' . count($fichiers_a_supprimer) . ' fichiers...' . "\n";

			if ($effacement)
			{
				$s3Client = new S3Client([
					'version' 		=> '2006-03-01',
					'region' 		=> $this->config->item('region', 'amazon'),
					'credentials' 	=> [
						'key'    => $this->config->item('api_key', 'amazon'),
						'secret' => $this->config->item('api_secret', 'amazon'),
					]			
				]);

				$bucket = 'kovao';

				foreach($fichiers_a_supprimer as $f)
				{
					if ($s3Client->doesObjectExist($bucket, 'evaluations/' . $f)) 
					{
						$result = $s3Client->deleteObject([
							'Bucket' => $bucket,
							'Key'    => 'evaluations/' . $f, 
						]);
					}

					$rapport_data['fichiers'][] = $f;
					$rapport_data['fichiers_supprimes']++;

					echo '.';
				}
			} // effacement
		}

		//
		// Effacer les lignes de tous les documents a supprimer
		//

		echo 'Suppression de ' . count($docs_effaces) . ' documents effacés...' . "\n";

		if ($effacement)
		{
			$chunk_size = 500;
			$chunks = array_chunk($docs_effaces_ids, $chunk_size);

			foreach($chunks as $c)
			{	
				$this->db->where_in('doc_id', $c);
				$this->db->delete($this->documents_t);
			}
		}

		//
		// Ecrire le rapport
		//

		if ($effacement)
		{
			$rapport_data['taille_supprimee'] = $taille_supprimee;
			$rapport['data'] = json_encode($rapport_data, $json_options);

			$this->db->insert('rapports_maintenance', $rapport);
		}

		echo "\n";

		exit;
	}

    /* --------------------------------------------------------------------------------------------
     *
	 * Purger les documents des etudiants
	 *
	 * --------------------------------------------------------------------------------------------
	 *
	 * Les documents des etudiants (table 'documents_etudiants') sont les televersements
	 * des etudiants.
	 *
	 * Il faut purger les documents effaces de la table 'documents_etudiants'.
	 *
     * -------------------------------------------------------------------------------------------- */
	function purger_documents_etudiants($jours = 30)
	{
		// Determiner la date la plus eloignee pour l'effacement
		// base sur le nombre de jours de l'argument

		$maintenant = new DateTimeImmutable();
		$intervalle = new DateInterval('P' . $jours . 'D'); // P180D

		$date_passee = $maintenant->sub($intervalle);
		$epoch = $date_passee->getTimestamp();

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

        if ( ! $query->num_rows() > 0)
             return 0;

		$docs = $query->result_array();
		$doc_ids = array_column($docs, 'doc_id');

		//
		// Supprimer les documents etudiants (des soumissions)
		//

		echo 'Suppression de ' . count($docs) . ' documents effacés avant le ' . date_humanize($epoch) . "\n";

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

			// surete
			if ( ! $d['efface'])
				continue;

			try 
			{
				if ($s3Client->doesObjectExist($bucket, 'soumissions/' . $d['doc_filename'])) 
				{
					$result = $s3Client->deleteObject([
						'Bucket' => $bucket,
						'Key'    => 'soumissions/' . $d['doc_filename'],
					]);

					$taille_supprimee += $d['doc_filesize'];
				}

				if ($s3Client->doesObjectExist($bucket, 'soumissions/' . $d['doc_tn_filename'])) 
				{
					$result = $s3Client->deleteObject([
						'Bucket' => $bucket,
						'Key'    => 'soumissions/' . $d['doc_tn_filename'], 
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

		$chunk_size = 500;
		$chunks = array_chunk($doc_ids, $chunk_size);

		foreach($chunks as $c)
		{	
			$this->db->where_in('doc_id', $c);
			$this->db->delete($this->documents_etudiants_t);
		}

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

    /* --------------------------------------------------------------------------------------------
     *
	 * Nettoyer S3 documents
	 *
	 * ---------------------------------------------------------------------------------------------
	 *
	 * Cette fonction liste tous les objets dans le bucket S3 d'un repertoire indique, et 
	 * determine le nombre d'objets ayant une entree dans la base de donnees.
	 *
	 * Les objets introuvables sont supprimes.
     *
	 * @TODO la supression
	 *
     * -------------------------------------------------------------------------------------------- */
	function nettoyer_s3_documents($repertoire)
	{
		if ( ! in_array($repertoire, ['evaluations', 'soumissions']))
			exit(9);

		$objets = $this->Document_model->lister_objets_s3(['repertoire' => $repertoire]);

		if (empty($objets))
			exit;

		// Correspondance repertoire <-> table

		$rt = [
			'evaluations' => 'documents',			// enseignants
			'soumissions' => 'documents_etudiants'	// etudiants
		];

		$introuvables = [];

		foreach($objets as $o)
		{
			// Les thumbnails ne sont pas indexes, et sont lies a un document

			if (preg_match('/_tn\./', $o))
				continue;

			// Chercher le fichier dans la base de donnees

			$this->db->where('efface', 0);
			$this->db->where('doc_filename', $o);

			$query = $this->db->get($rt[$repertoire]);

			// Le document est introuvable

			if ( ! $query->num_rows() > 0)
			{
				$introuvables[] = $o;
			}
		}	

		if (empty($introuvables))
			exit;

		$suppressions = 0;

		foreach($introuvables as $d)
		{
			$doc_filename 	 = $d;
			$doc_tn_filename = NULL;

			if ($repertoire == 'soumissions')
			{
				$parts = pathinfo($d);
				$doc_tn_filename = $parts['filename'] . '_tn.' . $parts['extension'];
			}

			/*
			if ($s3Client->doesObjectExist($bucket, $repertoire . '/' . $doc_filename))
			{
				$result = $s3Client->deleteObject([
					'Bucket' => $bucket,
					'Key'    => $repertoire . '/' . $doc_filename
				]);

				$suppressions++;
			}	
			*/

			if (empty($doc_tn_filename))
				continue;

			/*
			if ($s3Client->doesObjectExist($bucket, $repertoire . '/' . $doc_tn_filename))
			{
				$result = $s3Client->deleteObject([
					'Bucket' => $bucket,
					'Key'    => $repertoire . '/' . $doc_tn_filename
				]);

				$suppressions++;
			}	
			*/
		}

		echo $suppressions . ' objets supprimes' . "\n";
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
