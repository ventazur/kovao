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

        $this->db->from ('ci_sessions');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;
                                                                                                                                                                                                                                  
        $sessions = $query->result_array();

        //
        // Effacer les sessions plus vieilles que l'interval voulu
        //
        
        if (count($sessions))
        {
            $interval = date('U') - (60*60*24*7); // 7 jours

            foreach($sessions as $s)
            {
                if ($s['timestamp'] < ($interval))
                {
                    $this->db->where('id', $s['id']);
                    $this->db->delete('ci_sessions');

                    $effacements++;
                }
            }
        }

        return $effacements;
    }

    /* --------------------------------------------------------------------------------------------
     *
	 * Purger les soumissions effacees (OBSOLETE)
	 *
	 * --------------------------------------------------------------------------------------------
	 *
	 * Cette function est remplacee par 'purger_items' qui inclus les soumissions.
     *
     * -------------------------------------------------------------------------------------------- */
    function OBSOLETE_purger_soumissions()
    {
        $effacements = 0;

        $this->db->from  ('soumissions');
        $this->db->where ('efface', 1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return $effacements;
                                                                                                                                                                                                                                  
        $row = $query->row_array();
        
        $soumissions_a_effacer = array_keys_swap($query->result_array(), 'soumission_id');
        
        $epoch_30jours = date('U') - 60*60*24*30;

        foreach($soumissions_a_effacer as $soumission_id => $s)
        {
            //
            // Verifier qu'ils ont ete effaces il y a plus de 30 jours.
            // De cette facon, je m'assure qu'il y a un backup quelque part de ces fichiers.
            //
            if ( ! empty($s['efface_epoch']) && $s['efface_epoch'] > $epoch_30jours)
            {
                unset($soumissions_a_effacer[$soumission_id]);
                continue;
            }
        }

        if ( ! empty($soumissions_a_effacer))
        {
            foreach($soumissions_a_effacer as $soumission_id => $s)
            {
                if (empty($soumission_id)) continue;
    
                $this->db->where ('soumission_id', $soumission_id);
                $this->db->delete('soumissions');

                $effacements++;
            }
        }

        return $effacements;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Purger les documents (images) effaces des evaluations 
     *
     * version 4 (2020-10-17)
     *
     * -------------------------------------------------------------------------------------------- */
    function purger_documents($effacement, $expiration_jours = 0)
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

    /* --------------------------------------------------------------------------------------------
     *
     * Purger les items (OBSOLETE)
     *
     * --------------------------------------------------------------------------------------------
     * 
     * Cette fonction permet d'effacer les items:
     * evaluations, blocs, variables, questions et reponses 
     *
     * -------------------------------------------------------------------------------------------- */
    function OBSOLETE_purger_items()
    {
        $effacements     = 0;
        $non_effacements = 0;
        $expiration      = date('U') - 60*60*24*30; // 30 jours

        $this->db->trans_begin();

        //
        // Items
        //

        $tables = array(
            // table         // id
            'evaluations' => 'evaluation_id',
            'blocs'       => 'bloc_id',
            'variables'   => 'variable_id',
            'questions'   => 'question_id',
            'reponses'    => 'reponse_id'
        );
        
        foreach($tables as $table => $id)
        {
            $this->db->from  ($table);
            $this->db->where ('efface', 1);
        
            $query = $this->db->get();
        
            if ($query->num_rows() > 0)
            {
                $items_a_effacer = $query->num_rows;

                foreach ($query->result_array() as $row)
                {
                    if ($row['efface_epoch'] > $expiration)
                    {
                        $non_effacements++;
                        continue;
                    }

                    $this->db->where ($id, $row[$id]);
                    $this->db->where ('efface', 1);
                    $this->db->where ('efface_epoch <', $expiration);
                    $this->db->delete($table);

                    if ($this->db->affected_rows() > 1)
                    {
                        // Prevenir l'effacement de la table entiere dans le cas d'un probleme.
                        $this->db->trans_rollback();
                        return "Erreur Y888 : L'effacement de la table entière [" . $table . "] a été prévnu.";
                    }

                    $effacements++;

                } // foreach result_array

            } // if num_rows > 0

        } // foreach $tables

        $this->db->trans_commit();

        $effacements_inflection     = ($effacements > 1 ? 's' : '');
        $non_effacements_inflection = ($non_effacements > 1 ? 's' : ''); 

        $str =  $effacements . ' item' . $effacements_inflection . ' purgé' . $effacements_inflection;
        $str .= ' (' . $non_effacements . ' item' . $non_effacements_inflection . ' à purger plus tard)';

        return $str;
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
