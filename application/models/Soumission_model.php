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

// ============================================================================
//
// SOUMISSION MODEL
//
// ============================================================================

class Soumission_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     *  Extraire les soumissions d'un etudiant
	 *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumissions_etudiant($etudiant_id, $options = array())
    {
        $options = array_merge(
            array(
				'enseignant_id' => NULL
            ),
            $options
        );

        $this->db->from  ('soumissions');
        $this->db->where ('etudiant_id', $etudiant_id);
        $this->db->where ('efface', 0);

        if ( ! empty($options['enseignant_id']) && $this->enseignant['privilege'] < 90)
        {
            $this->db->where ('enseignant_id', $options['enseignant_id']);
        }

        $this->db->order_by ('soumission_epoch', 'desc');

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'soumission_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     *  Mettre en ligne une evaluation
	 *
     * --------------------------------------------------------------------------------------------
	 *
     * On peut seulement mettre en ligne une evaluation pour le semestre en vigueur.
     *
     * -------------------------------------------------------------------------------------------- */
    function mettre_en_ligne($evaluation_id)
    {
        if ( ! is_numeric($evaluation_id) || ! ctype_digit($evaluation_id))
        {
            return array(
                'status'     => FALSE,
                'error_code' => 2,
                'error_msg'  => "Argument (evaluation_id) invalide"
            );
        }

        //
        // Verifier que l'enseignant a un semestre en vigueur
        //

        if ($this->enseignant['semestre_id'] === NULL)
        {
            return array(
                'status'     => FALSE,
                'error_code' => 3,
                'error_msg'  => "Aucun semestre en vigueur"
            );
        }

        //
        // Verifier que le semestre en vigueur de l'enseignant est le semestre courant
        //

        if ($this->enseignant['semestre_id'] != $this->semestre_id)
        {
            return array(
                'status'     => FALSE,
                'error_code' => 33,
                'error_msg'  => "Vous ne pouvez mettre en ligne une évaluation que pour le semestre courant."
            );
        }

        //
        // Extraire l'evaluation
        //

        if (empty($evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id)))
        {
            return array(
                'status'     => FALSE,
                'error_code' => 4,
                'error_msg'  => "Évaluation introuvable"
            );
        }

		//
		// Verifier la permission
		//

        if ($evaluation['enseignant_id'] != $this->enseignant_id)
        {
            return array(
                'status'     => FALSE,
                'error_code' => 5,
                'error_msg'  => "Permission refusée (mettre en ligne)"
            );
        }

        //
        // Verifier que ce cours est selectionne dans la configuration de l'enseignant
        //

        $cours = $this->Cours_model->lister_cours_selectionnes($this->enseignant_id, $this->semestre_id);

        if ( ! array_key_exists($evaluation['cours_id'], $cours))
        {
            return array(
                'status'     => FALSE,
                'error_code' => 55,
                'error_msg'  => "Vous ne donnez pas ce cours ce semestre. Veuillez sélectionner ce cours dans la configuration si c'est le cas."
            );
        }

        //
        // Determiner si cette evaluation est deja en ligne.
        //
        // Cette evaluation (evaluation_id) pourrait etre en ligne depuis un semestre precedent
        // (dont un enseignant n'aurait jamais mis hors ligne), alors il ne faut pas en tenir compte.
		// 	
		// Anciennement, on pouvait mettre en ligne des evaluations du groupe (plus maintenant).
        //

        $this->db->from  ('rel_enseignants_evaluations as ree');
        $this->db->where ('ree.evaluation_id', $evaluation_id);
        $this->db->where ('ree.semestre_id', $this->semestre_id);
		$this->db->where ('ree.efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();

        if ($query->num_rows())
        {
			return array(
				'status'     => FALSE,
				'error_code' => 6,
				'error_msg'  => "Cette évaluation est déjà en ligne."
			);
		}

		//
		// Verifier l'integrite de l'evaluation (seulement lors de la selection)
		//

		if (($erreurs = $this->Evaluation_model->verifier_integrite_evaluation($evaluation_id)) !== TRUE)
		{
			return array(
				'status'     => FALSE,
				'error_code' => 9,
                'error_msg'  => "Cette évaluation comporte des erreurs. Veuillez la prévisualiser.",
                'error_arr'  => $erreurs
			);
		}

		//
		// Creer un code de reference pour l'evaluation et verifier son unicite.
		//
		
		$surete = 1;

        while($surete < 100)
        {
            $evaluation_reference = strtolower(random_string('alpha', 6));

            //
            // Le numero de reference doit etre unique.
            //

            $this->db->from  ('rel_enseignants_evaluations');
            $this->db->where ('evaluation_reference', $evaluation_reference);
			// $this->db->where ('efface', 0); // pour eviter des problemes possibles, la reference doit etre unique parmi les evaluations actives et effacees
            $this->db->limit (1);

            $query = $this->db->get(); 

            if ( ! $query->num_rows())
            {
				// Le numero de reference genere est unique, sortons de cette boucle.
                break;
            }

            $surete++;

			log_alerte(
				array(
					'code' => 'ESS101',
					'desc' => "Ce numéro de référence d'évaluation (" . $evaluation_reference . ") existe déjà, il faut en générer un autre (" . $surete . ")."
				)
			);

            if ($surete >= 100)
            {
                generer_erreur('ESS100', "Une incrémentation de sûreté a été activée en tentant de générer le numéro de référence d'une évaluation.");
                return;
            }
        }

        //
        // Verifier que l'inscription est requise si l'enseignant souhaite que toutes
        // ses evaluations le soit.
        // Si ce n'est pas le cas, changer le parametre dans l'evaluation.
        //

        /* 
         * OBSOLETE depuis 2023-01-14
         *
        if (array_key_exists('inscription_requise', $this->enseignant) && $this->enseignant['inscription_requise'])
        {
            if ( ! $evaluation['inscription_requise'])
            {
                // Changer le parametre dans l'evaluation

                $this->db->where  ('evaluation_id', $evaluation['evaluation_id']);
                $this->db->where  ('enseignant_id', $this->enseignant_id);
                $this->db->where  ('public', 0);
                $this->db->update ('evaluations', array('inscription_requise' => 1));
            }    

            $evaluation['inscription_requise'] = 1;
        }
        */

        //
        // Mettre en ligne l'evaluation
        //

        $this->db->trans_begin();

        $data = array(
            'enseignant_id' => $this->enseignant_id,
            'groupe_id'     => $this->groupe_id,
            'semestre_id'   => $this->semestre_id,
            'evaluation_id' => $evaluation_id,
            'cours_id'      => $evaluation['cours_id'],
            'evaluation_reference' => $evaluation_reference,
            // 'inscription_requise'  => $evaluation['inscription_requise'], // inscription_requise par defaut depuis 2023-01-14
            'lab'           => $evaluation['lab'],
            'cacher'        => $this->enseignant['cacher_evaluation'],
			'ajout_date'	=> date_humanize($this->now_epoch, TRUE),
			'ajout_epoch'	=> $this->now_epoch
        );

        $this->db->insert('rel_enseignants_evaluations', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();

			return array(
				'status'     => FALSE,
				'error_code' => 99,
				'error_msg'  => "Il n'a pas été possible d'écrire dans la base de données."
			);
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

			return array(
				'status'     => FALSE,
				'error_code' => 999,
				'error_msg'  => "Une erreur s'est produite avec la base de données."
			);
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* ---------------------------------------------------------------------------
     *
     * Mettre hors ligne une evaluation
	 *
     * ---------------------------------------------------------------------------
	 *
     * Ceci correspond a terminer une evaluation.
     *
     * --------------------------------------------------------------------------- */
	function mettre_hors_ligne($evaluation_reference, $options = array())
	{
        $options = array_merge(
            array(
				'etudiant_id'			 => NULL,
                'enregistrer_evaluation' => TRUE
            ),
            $options
        );

        //
        // Extraire les parametres de l'evaluation et les preferences de l'enseignant
        //

        $this->db->from  ('rel_enseignants_evaluations as ree, enseignants as en');
        $this->db->where ('ree.evaluation_reference', $evaluation_reference);
        $this->db->where ('en.enseignant_id = ree.enseignant_id');
        $this->db->where ('ree.efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array(
                'status'     => FALSE,
                'error_code' => 3,
                'error_msg'  => "Aucun enseignant assigné à cette évaluation (" . $evaluation_reference . ")"
            );
        }

        $evaluation = $query->row_array();

		//
		// Verifier la permission de mettre hors ligne cette evaluation
		//
		// - L'enseignant ET/OU l'admin peuvent terminer une evaluation
        // - Le cli n'a pas besoin de verifier la permission
        //

		if ( ! is_cli() && $this->enseignant['privilege'] < 90)
        {
            if ($evaluation['enseignant_id'] != $this->enseignant_id)
            {
				return array(
					'status'     => FALSE,
					'error_code' => 3,
					'error_msg'  => "Permission refusée (mettre hors ligne)"
				);
            }
        }

        $this->db->trans_begin();

        //
        // Enregistrer les evaluations non terminees
        //

        if ($this->config->item('evaluations_non_terminees') && $options['enregistrer_evaluation'])
        {
			$this->enregistrer_soumission_traces(
				$evaluation_reference,
                array(
                    'etudiant_id'   => $options['etudiant_id'],
                    'non_terminee'  => TRUE
				)
			);
        }

		//
		// Mettre hors ligne cette evaluation
        //

		if (empty($options['etudiant_id']))
        {
            $rel_data = array(
                'efface' => 1,
                'efface_date' => date_humanize($this->now_epoch, TRUE),
                'efface_epoch' => $this->now_epoch,
                'efface_par_cli' => is_cli() ? 1 : 0 // depuis 2023-03-30
            );

			$this->db->where  ('evaluation_reference', $evaluation_reference);
            $this->db->update ('rel_enseignants_evaluations', $rel_data); // depuis 2023-01-14

			if ( ! $this->db->affected_rows())
			{
				$this->db->trans_rollback();

				return array(
					'status'     => FALSE,
					'error_code' => 4,
					'error_msg'  => "Erreur d'écriture dans la base de données (rollback)"
				);
			}

			//
			// Effacer toutes les traces des etudiants inscrits
			//

			$this->db->where  ('etudiant_id !=', NULL);
			$this->db->where  ('evaluation_reference', $evaluation_reference);
			$this->db->update ('etudiants_traces', array('efface' => 1));

			//
			// Effacer (supprimer) toutes les traces des etudiants NON inscrits
			//

			$this->db->where  ('etudiant_id', NULL);
			$this->db->where  ('efface', 0); // Celles qui n'ont pas ete effacees ci-haut.
			$this->db->where  ('evaluation_reference', $evaluation_reference);
			$this->db->delete ('etudiants_traces');
        }

        $this->db->trans_commit();

		return TRUE;
    }

    // ------------------------------------------------------------------------
    //
    // Preparer pour l'enregistrement d'une soumission
    //
    // => a partir du formulaire
    //
    // ------------------------------------------------------------------------
    function enregistrer_soumission_formulaire($post_data, $options = array())
    {
    	$options = array_merge(
           array(
           ),
           $options
        );

        $evaluation_reference = $post_data['evaluation_reference'];

        //
        // Extraire rel_evaluation
        //

        if ( ! $post_data['previsualisation'])
        {
            if (($rel_evaluation = $this->Evaluation_model->extraire_rel_evaluation($evaluation_reference)) === FALSE)
            {
                log_alerte(
                    array(
                        'code'       => 'GS7812',
                        'desc'       => "Il n'a pas été possible d'extraire les relations de l'évaluation.",
                        'importance' => 2,
                        'extra'      => 'evaluation_reference = ' . $evaluation_reference
                    )
                );

                return FALSE;
            }
        }
        else
        {
            //
            // Lorsqu'un enseignant est en previsualisation,
            // essayons de simuler une vraie evaluation
            //

            //
            // Un semestre doit etre actif pour y accompagner les resultats
            //
    
            if ( ! array_key_exists('semestre_id', $post_data) || empty($post_data['semestre_id']))
            {
                generer_erreur2(
                    array(
                        'code'  => 'GS7000',
                        'desc'  => "Un semestre doit être actif pour envoyer une évaluation.",
                        'extra' => 'voir KIT #11'
                    )
                );
                exit;
            }

            $evaluation = $this->Evaluation_model->extraire_evaluation($post_data['evaluation_id']);

            $rel_evaluation = array(
                'enseignant_id'        => $evaluation['enseignant_id'],
                'groupe_id'            => $evaluation['groupe_id'],
                'semestre_id'          => $this->semestre_id,
                'cours_id'             => $evaluation['cours_id'],
                'evaluation_id'        => $post_data['evaluation_id'],
                'evaluation_reference' => NULL,
                'lab'                  => $evaluation['lab'],
                'ajout_date'           => date_humanize($this->now_epoch - 60*60*24*7, TRUE),
                'ajout_epoch'          => $this->now_epoch - 60*60*24*7
            );
        }

        //
        // Extraire les questions choisies
        //

        $post_data['questions_choisies'] = unserialize(
            $this->encryption->decrypt($post_data['questions_choisies'])
        );

        //
        // Extraire les variables choisies
        //

        if (empty($post_data['variables_choisies']))
        {
            $post_data['variables'] = array();
        }
        else
        {
            $post_data['variables'] = unserialize(
                $this->encryption->decrypt($post_data['variables_choisies'])
            );
        }

        return 
            $this->enregistrer_soumission(
                $post_data, 
                array(
                    'rel_evaluation'   => $rel_evaluation,
                    'previsualisation' => $post_data['previsualisation'] ? TRUE : FALSE
                )
            );

    } // formulaire

    // ------------------------------------------------------------------------
    //
    // Preparer pour l'enregistrement d'une soumission
	//
	// ------------------------------------------------------------------------
    //
    // => a partir des traces
    //
    // ------------------------------------------------------------------------
    function enregistrer_soumission_traces($evaluation_reference, $options = array())
    {
    	$options = array_merge(
           array(
				'etudiant_id'   => NULL,
                'non_terminee'  => TRUE
           ),
           $options
        );

		//
		// Extraire les traces
		//

        $traces = $this->Evaluation_model->extraire_traces(
            $evaluation_reference,
            array(
                'etudiant_id'                  => $options['etudiant_id'],
                'etudiants_inscrits_seulement' => TRUE
            )
        );

		if (empty($traces))
        {
            // Une evaluation peut etre fermee et aucun etudiant n'etait en redaction, donc aucune trace trouvee.

            $this->Admin_model->debogage(
                array(
                    'evaluation_reference' => $evaluation_reference,
                    'code' => 'DSMEST002',
                    'msg' => "aucune trace trouvee pour cette evaluation (aucun etudiant en redaction)",
                    'class' => __CLASS__,
                    'function' => __FUNCTION__
                )
            );

            return;
        }	

        //
        // Extraire rel_evaluation
        //

        if (($rel_evaluation = $this->Evaluation_model->extraire_rel_evaluation($evaluation_reference)) === FALSE)
        {
            log_alerte(
                array(
                    'code'       => 'GS7812',
                    'desc'       => "Il n'a pas été possible d'extraire les relations de l'évaluation.",
                    'importance' => 2,
                    'extra'      => 'evaluation_reference = ' . $evaluation_reference
                )
            );

            return FALSE;
        }

        //
        // Iterer a travers toutes les traces (de tous les etudiants)
        //

		$soumission_ids = array();
		$soumission_references = array();

		foreach($traces as $t)
        {
			//
			// Convertir les traces pour etre en symbiose avec post_data du formulaire
			//

			$t_data = unserialize($t['data']);

            //
            // Laboratoire
            //

            if ($rel_evaluation['lab'])
            {
                /*
                 * PERTINENT
                 *
                 * Ce code est pertinent (2024-08) mais il est entre commentaire car il n'a jamais ete teste,
                 * et je ne peux pas le tester maintenant.
                 *
                 */

                $evaluation = $this->Evaluation_model->extraire_evaluation($rel_evaluation['evaluation_id']);

                $lab_prefix = $evaluation['lab_prefix'];

                //
                // Il faut refaire les champs en incluant le 'lab_prefix' pour simuler que ces champs
                // proviennent du formulaire.
                //

                $lab_valeurs_etudiants = array();

                if (array_key_exists('lab', $t_data) && is_array($t_data['lab']) && ! empty($t_data['lab']))
                {
                    foreach($t_data['lab'] as $c => $cv)
                    {   
                        if (in_array($c, array('lab_partenaire2', 'lab_partenaire3')))
                        {
                            $t_data[$c] = $cv;
                            unset($t_data['lab'][$c]);
                        }

                        $n_champ = $lab_prefix . '-' . $c;

                        $lab_valeurs_etudiants[$n_champ] = $cv;
                    }

                    $t_data['lab_valeurs_etudiants'] = $lab_valeurs_etudiants;
                }
            }

            $t_data = $t + $t_data;

			$soumission = $this->enregistrer_soumission(
				$t_data, 
				array(
					'origine'		 => 'traces',
					'rel_evaluation' => $rel_evaluation,
					'non_terminee'   => $options['non_terminee']
				)
            );

            if ($soumission['soumission_id'] == NULL)
            { 
                log_alerte(
                    array(
                        'code'       => 'SET9911',
                        'desc'       => "La soumission_id est NULL.",
                        'importance' => 2,
                        'extra'      => json_encode($t_data)
                    )
                );
            }

			$soumission_ids[] = $soumission['soumission_id'];
			$soumission_references[] = $soumission['soumission_reference'];
        }

		//
		// Enregistrer l'activite des terminaisons
		//

		$this->db->insert('activite_terminer_evaluations', 
			array(
				'enseignant_id'         => $rel_evaluation['enseignant_id'],
				'evaluation_id'         => $rel_evaluation['evaluation_id'],
				'evaluation_reference'  => $evaluation_reference,
				'groupe_id'             => $rel_evaluation['groupe_id'],
				'terminaisons_forcees'  => count($soumission_ids),
				'soumission_ids'        => json_encode($soumission_ids),
				'soumission_references' => json_encode($soumission_references),
				'date'                  => date_humanize($this->now_epoch, TRUE),
				'epoch'                 => $this->now_epoch,
				'cli'                   => is_cli() ? TRUE : FALSE
			)
		);

		return;

    } // traces

    // ------------------------------------------------------------------------
    //
    // Enregistrer et corriger une soumission
    //
    // ------------------------------------------------------------------------
    function enregistrer_soumission($data = array(), $options = array())
    {

    	$options = array_merge(
            array(
                'origine'           => 'formulaire',   // 'formulaire' || 'traces'
                'rel_evaluation'    => array(),        // *obligatoire* 
                'previsualisation'  => FALSE,
                'non_terminee'      => FALSE
           ),
           $options
        );

        // Verifier le(s) champ(s) obligatoire(s)

        if (empty($options['rel_evaluation']))
        {
            log_alerte(
                array(
                    'code'       => 'ENRSM81',
                    'desc'       => "La rel_evaluation est introuvable ou vide.",
                    'importance' => 2,
                    'extra'      => NULL
                )
            );

            return FALSE;
        }

        extract($options);

        $evaluation_id = $data['evaluation_id'];

        $evaluation_reference = $rel_evaluation['evaluation_reference'] ?? NULL;

        // ------------------------------------------------------------
        //
        // Preparer les informations communes
        //
        // ------------------------------------------------------------

        //
        // Verifier si ces elements sont deja dans le cache.
        //
        
        $cache_key = __FUNCTION__ . $evaluation_reference . md5(serialize($options)) . $origine;

        if ($origine == 'traces' && ($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            $champs_obligatoires = array('evaluation', 'cours', 'groupe', 'ecole', 'semestre', 'enseignant', 'variables_raw', 'blocs');

            foreach($champs_obligatoires as $c)
            {
                if ( ! array_key_exists($c, $cache))
                {
                    generer_erreur2(
                        array(
                            'code' => 'ENRSM1',
                            'desc' => "Un problème s'est produit pour extraire les informations du cache."
                        )
                    );
                    exit;
                }
            }

            extract($cache);
        }
        else
        {
            //
            // Evaluation
            //
                    
            $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

            $lab = $evaluation['lab'];

            //
            // Cours
            //

            $cours = $this->Cours_model->extraire_cours(array('evaluation_id' => $evaluation_id));

            //
            // Groupe
            //

            $groupe = $this->Groupe_model->extraire_groupe(array('evaluation_id' => $evaluation_id));

            //
            // Ecole
            //

            $ecole = $this->Ecole_model->extraire_ecole(array('ecole_id' => $groupe['ecole_id']));

            //
            // Semestre
            //

            $semestre = $this->Semestre_model->extraire_semestre(array('semestre_id' => $rel_evaluation['semestre_id']));

            if ($semestre == FALSE)
            {
                generer_erreur2(
                    array(
                        'code'  => 'SOUM56129', 
                        'desc'  => "Vous ne pouvez pas soumettre une évaluation lorsqu'il n'y a pas de semestre actif.",
                        'extra' => NULL,
                        'importante' => 1
                    )
                );
                exit;
            }

            //
            // Enseignant
            //

            $enseignant = $this->Enseignant_model->extraire_enseignant(
                $evaluation['enseignant_id'],
                array(
                    'groupe_id' => $groupe['groupe_id']
                )
            );

            //
            // Extraire les variables
            //

            $variables_raw = $this->Evaluation_model->extraire_variables($evaluation_id);

            //
            // Extraire les blocs
            //

            $blocs = $this->Question_model->extraire_blocs($evaluation_id);

			//
			// Sauvegarder dans le cache
			//

            if ($origine == 'traces')
            {
                $this->kcache->save(
                    $cache_key, 
                    array(
                        'evaluation'    => $evaluation,
                        'cours'         => $cours,
                        'groupe'        => $groupe,
                        'ecole'         => $ecole,
                        'semestre'      => $semestre,
                        'enseignant'    => $enseignant,
                        'variables_raw' => $variables_raw,
                        'blocs'         => $blocs
                    ),
                    'enregistrer_soumission', 
                    60
                );
            }
        } // else cache

		//
		// Regrouper les donnees de l'evaluation dans une seule array (evaluation_data)
		//

        $evaluation_data = array(
            'evaluation_id'        => $evaluation['evaluation_id'],
            'evaluation_titre'     => $evaluation['evaluation_titre'],
            'public'               => $evaluation['public'],
            'instructions'         => $evaluation['instructions'],
            'questions_aleatoires' => $evaluation['questions_aleatoires']
        );

		//
		// Regrouper les donnees du cours dans une seule array (cours_data)
		//

		$cours_data = array(
			'ecole_id' 			  => $ecole['ecole_id'],
			'ecole_nom' 		  => $ecole['ecole_nom'],
			'groupe_id' 		  => $groupe['groupe_id'],
			'groupe_nom' 		  => $groupe['groupe_nom'],
			'cours_id' 			  => $cours['cours_id'],
            'cours_code' 		  => $cours['cours_code'],
            'cours_code_court'    => $cours['cours_code_court'],
			'cours_nom' 		  => $cours['cours_nom'],
            'cours_nom_court' 	  => $cours['cours_nom_court'],
            'enseignant_id'       => $enseignant['enseignant_id'],
            'enseignant_nom'      => $enseignant['nom'],
            'enseignant_prenom'   => $enseignant['prenom'],
            'enseignant_genre'    => $enseignant['genre'],
			'semestre_id' 		  => $semestre['semestre_id'],
            'semestre_nom' 		  => $semestre['semestre_nom'],
            'semestre_code'       => $semestre['semestre_code'],
			'semestre_debut_date' => $semestre['semestre_debut_date'],
			'semestre_fin_date'	  => $semestre['semestre_fin_date']
		);

        // ------------------------------------------------------------
        //
        // Preparer les informations specifiques
        //
        // ------------------------------------------------------------

        //
        // Etudiant
        //

		$etudiant    = array();
        $etudiant_id = $data['etudiant_id'] ?? NULL;

        if ($etudiant_id)
        {
            $etudiant = $this->Etudiant_model->extraire_etudiant(
                $etudiant_id,
                array(
                    'groupe_id' => $cours['groupe_id']
                )
            );

			$etudiant['prenom_nom'] = $etudiant['prenom'] . ' ' . $etudiant['nom'];
			$etudiant['numero_da']  = $etudiant['numero_da'] ?? NULL;
        }
		else
		{
			$etudiant['prenom_nom'] = $data['prenom_nom'] ?? NULL;
			$etudiant['numero_da']  = $data['numero_da']  ?? NULL;
        }

        //
        // Questions choisies
        //
        
        if ( ! array_key_exists('questions_choisies', $data) || empty($data['questions_choisies']))
        {
            log_alerte(
                array(
                    'code'       => 'SET9000',
                    'desc'       => "Il n'a pas été possible d'extraire les questions choisies.",
                    'importance' => 1,
                    'extra'      => 'origine = ' . $origine,
                                    ', evaluation_id = ' . $evaluation['evaluation_id'] . 
                                    ', evaluation_reference = ' . $evaluation_reference
                )
            );
        }

        //
        // Extraire les questions
        //

		if ( ! array_key_exists('questions_choisies', $data))
		{
			$data['questions_choisies'] = array();
		}

        $questions = $this->Question_model->lister_questions_pour_soumission(
            $evaluation_id,
            array(
                'question_ids' => $data['questions_choisies'],
                'actif'        => TRUE
            )
		);

        //
        // Reordonneer les questions selon l'ordre des questions choisies lors du chagement initial.
        //

		if ( ! empty($data['questions_choisies']))
		{
        	$questions_tmp = array();

			foreach($data['questions_choisies'] as $q_id)
			{
				$questions_tmp[$q_id] = $questions[$q_id];
			}

			$questions = $questions_tmp;
		}

        if ( ! $lab && empty($questions)) 
        {
            log_alerte(
                array(
                    'code'       => 'SET9001',
                    'desc'       => "Les questions sont introuvables.",
                    'importance' => 1,
                    'extra'      => 'origine = ' . $origine, 
                                    ', evaluation_id = ' . $evaluation['evaluation_id'] . 
                                    ', evaluation_reference = ' . $evaluation_reference
                )
            );
            return array();
        }

        //
        // Extraire les valeurs des variables utilisees dans l'evaluation de cet etudiant specifique
        //

        $variables = array();

        if ( ! empty($variables_raw))
        {    
            //
            // Cette evaluation necessite des variables.
            // 

            // Extraire les variables de l'evaluation

            if ( ! array_key_exists('variables', $data) || empty($data['variables']))
            {
                log_alerte(
                    array(
                        'code'       => 'SET9003',
                        'desc'       => "Les variables sont introuvables.",
                        'importance' => 1,
                        'extra'      => 'origine = ' . $origine, 
                                        ', evaluation_id = ' . $evaluation['evaluation_id'] . 
                                        ', evaluation_reference = ' . $evaluation_reference
                    )
                );
                return FALSE;
            }

            $variables = $data['variables'];

            //
            // Verifier si toutes les variables sont presentes.
            //

            $labels = array_keys($variables_raw);

            $variables_conformes = TRUE;

            foreach($labels as $label)
            {
                if ( ! array_key_exists($label, $variables))
                {
                    $variables_conformes = FALSE;
                    break;
                }
            }

            if ( ! $variables_conformes)
            {
                log_alerte(
                    array(
                        'code'    => 'SET67144', 
                        'desc'    => "Les variables ne sont pas conformes",
                        'extra'   => 
                            'etudiant_id = '   . ($etudiant_id ?? 'non inscrit') . ', ' . 
                            'evaluation_id = ' . ($evaluation_id)
                    )
                );
                return FALSE;
            } 

            // Ecrire les variables dans la soumission

            $evaluation_info['variables'] = json_encode($variables);

        } // ! empty($variables_raw)

        //
        // Verifier l'integrite de l'evaluation, pour les questions presentees a l'etudiant.
        //

        $status = $this->Evaluation_model->verifier_integrite_evaluation(
            $evaluation_id, 
            array_keys($questions),
            array(
                'variables_valeurs' => $variables
            )
        );

        if ($status != TRUE || is_array($status))
        {
            // L'evaluation comporte des erreurs.

            log_alerte(
                array(
                    'code'    => 'SET9002', 
                    'desc'    => "L'évaluation comporte des erreurs. Veuillez contacter votre enseignante ou enseignant.",
                    'extra'   => 
                        'etudiant_id = '    . ($etudiant_id ?? 'non inscrit')                                    . ', ' . 
                        'evaluation_id = '  . ($evaluation_id)                                                   . ', ' .
                        'code = '           . (array_key_exists('code', $status) ? $status['code'] : NULL)       . ', ' .
                        'desc = '           . (array_key_exists('message', $status) ? $status['message'] : NULL) . ', ' .
                        'extra = '          . (array_key_exists('extra', $stats) ? $status['extra'] : NULL)  
                )
            );
            return FALSE;
        }

        //
        // Modifier les points de chaque question selon son appartenance a un bloc
        //

        if ( ! empty($blocs))
        {
            foreach($questions as $question_id => $q)
            {
                if ( ! empty($q['bloc_id']))
                {
                    $questions[$question_id]['question_points'] = $blocs[$q['bloc_id']]['bloc_points'];
                }
            }
        }

        //
        // Extraire les images
        //

        $images = $this->Document_model->extraire_images(array_keys($questions));

        //
        // Extraire les documents televerses par l'etudiant pour cette evaluation
        //

        $documents = $this->Document_model->extraire_documents_etudiants_evaluation2(
            $evaluation_reference, 
            array(
                'etudiant_id'	=> $etudiant_id,
                'session_id'    => $data['session_id'],
                'evaluation_id' => $evaluation_id
            )
        );

        // -------------------------------------------------------------------------
        //
        // CORRECTION
        //
        // -------------------------------------------------------------------------

        // Les "points total" correspondent aux points que l'etudiant pourrait avoir, au maximum, 
        // s'il avait tout bon dans les questions corrigees.

        $points_total = 0;

        // Les points obtenus par l'etudiant aux questions corrigees.
        // @DESUET

        $points_obtenus = 0;

        // Les points maximum que l'etudiant pourrait avoir, en tenant compte de toutes les
        // questions, corrigees ou non.

        $points_evaluation = 0;

        // La correction est terminee si $points_total = $points_evaluation.

        $corrections_terminees = 1;

        // Les questions IDs avec des documents, ceux-ci doivent etre assignes a cette soumission

        $questions_ids_docs = array();

        // Activer le flag si une ou des questions a corriger manuellement sont presentes
    
        $corrections_manuelles = 0;

        //
        // Laboratoire
        //
        
        $lab = $evaluation['lab'] ?? 0;

        if ($lab)
        {
            $lab_valeurs = ! empty($evaluation['lab_valeurs']) ? json_decode($evaluation['lab_valeurs'], TRUE) : array();
            $lab_points  = ! empty($evaluation['lab_points']) ? json_decode($evaluation['lab_points'], TRUE) : array();
            $lab_prefix  = $evaluation['lab_prefix'];

            //
            // $lab_points_tableaux
            //
            // Array
            //
            //  [1] => (tableau 1)
            //		'champ1' => 'points', 'points_obtenus'
            //		'champ2' => 'points', 'points_obtenus'
            //		'points_totaux' = n
            //		'points_totaux_obtenus' = n
            //
            //  [lab_points_totaux] => 0
            //  [lab_points_totaux_obtenus] => 0
            //

            $lab_points_tableaux = generer_lab_points_tableaux($lab_points);

            //
            // Extraire les champs a corriger
            //
        
            $lab_champs_a_corriger = array();

            //
            // Dans les traces, les champs et les valeurs se trouvent dans $data['lab_valeurs_etudiants']
            //

            if ($origine == 'traces' && array_key_exists('lab_valeurs_etudiants', $data) && ! empty($data['lab_valeurs_etudiants']))
            {
                foreach($data['lab_valeurs_etudiants'] as $c => $cv)
                {
                    // La correction du labo (Lab_model) identifie les champs des laboratoires
                    // parce qu'ils commencent par le prefix. Donc pour simuler le formulaire,
                    // on ajoute le prefix.

                    $lab_champs_a_corriger[$c] = array(
                        'valeur' => $cv
                    );

                    //$lab_champs_a_corriger[$lab_prefix . '-' . $c] = array(
                    //    'valeur' => $cv
                    //);
                }
            }

            //
            // Dans le formulaire, les champs sont directement dans $data mais sont precedes
            // d'un prefix $lab_prefix
            //

            if ($origine == 'formulaire')
            {
                foreach($data as $c => $cv)
                {
                    if (preg_match('/^' . $lab_prefix . '-(.+)$/', $c, $matches)) 
                    {
                        $lab_champs_a_corriger[$c] = array(
                            'valeur' => $cv
                        );
                    }
                }
            }

            $lab_corrections = $this->Lab_model->corriger_laboratoire(
                $evaluation_id, $lab_champs_a_corriger, array('evaluation' => $evaluation)
            );

            if ($lab_corrections['erreur'])
            {
                generer_erreur2(
                    array(
                        'code'  => 'LAB100', 
                        'desc'  => "Une erreur ses produits pendant la correction.",
                        'extra' => $lab_corrections['erreur_msg'],
                        'importante' => 7
                    )
                );
            }

            //
            // Extraire les donnees de precorrections
            // C'est donnees sont seulement dans les traces.
            //
        
            if ($origine == 'traces')
            {
                $precorrections = $data['precorrections'] ?? array();
            }

            if ($origine == 'formulaire')
            {
                $traces = $this->Evaluation_model->lire_traces($evaluation_reference, $evaluation_id);
                $traces_arr = unserialize($traces);
                $precorrections = $traces_arr['precorrections'] ?? array();
            }

            //
            // Laboratoire DATA
            //

            $lab_data = array(
                'lab_place'                   => $data['lab_place'] ?? 0,
                'lab_partenaire2_nom'		  => $data['lab_partenaire2_nom'] ?? NULL,
                'lab_partenaire2_matricule'   => $data['lab_partenaire2_matricule'] ?? NULL,
                'lab_partenaire2_etudiant_id' => $data['lab_partenaire2_etudiant_id'] ?? NULL,
                'lab_partenaire2_eleve_id'    => $data['lab_partenaire2_eleve_id'] ?? NULL,
				'lab_partenaire3_nom'		  => $data['lab_partenaire3_nom'] ?? NULL,
                'lab_partenaire3_matricule'   => $data['lab_partenaire3_matricule'] ?? NULL,
                'lab_partenaire3_etudiant_id' => $data['lab_partenaire3_etudiant_id'] ?? NULL,
                'lab_partenaire3_eleve_id'    => $data['lab_partenaire3_eleve_id'] ?? NULL,
                'lab_prefix'                  => $lab_prefix ?? NULL,
                'lab_precorrections'          => $precorrections,
				'lab_parametres' 			  => $evaluation['lab_parametres'] ?? NULL,
                'lab_vue'                     => $evaluation['lab_vue'],
                'lab_corr_controller'         => $evaluation['lab_corr_controller']
            );

            //
            // Enlever la penalite des precorrections excessives
            //

            // Determinons la penalite (lab_penalite)

            $lab_parametres = json_decode($lab_data['lab_parametres'], TRUE);
            $lab_penalite = $lab_parametres['precorrection_penalite'] ?? 0;
            $lab_penalite = str_replace(',', '.', $lab_penalite);

            $lab_corrections_points_obtenus = $lab_corrections['points_bilan']['points_totaux_obtenus'] ?? 0;

            // Appliquer la penalite seulement si possible

            if ($lab_penalite > 0 && array_key_exists('penalite', $precorrections) && $precorrections['penalite'] > 0)
            {
                $lab_penalite_total = $lab_penalite * $precorrections['penalite'];

                // Il n'est pas possible d'exceder une penalite de plus de 100%.

                if ($lab_penalite_total > 100)
                {
                    $lab_penalite_total = 100;
                }

                $lab_data['lab_precorrections']['penalite_pct'] = $lab_penalite_total;
                $lab_data['lab_precorrections']['penalite_points'] = ($lab_corrections['points_bilan']['points_totaux'] * $lab_penalite_total/100);

                // Calculer les points obtenus

                $lab_corrections_points_obtenus = $lab_corrections_points_obtenus - ($lab_corrections['points_bilan']['points_totaux'] * $lab_penalite_total/100);

                // Ne pas enlever plus de points que les points totaux des tableaux

                if ($lab_corrections_points_obtenus < 0)
                {
                    $lab_corrections_points_obtenus = 0;
                }
            }

            //
            // Ajouter les points du laboratoire aux points de l'evaluation
            //

            $points_total      += $lab_corrections['points_bilan']['points_totaux'] ?? 0;
            $points_evaluation += $lab_corrections['points_bilan']['points_totaux'] ?? 0;

            // $points_obtenus += $lab_corrections['points_bilan']['points_totaux_obtenus'] ?? 0;
            $points_obtenus    += $lab_corrections_points_obtenus;
        }

        //
        // Questions
        //

        foreach($questions as $question_id => $q)
        {
            $question_id = $q['question_id'];
            
            //
            // Remplacer les variables du texte des questions par leur valeur numerique.
            //
            // (!) Il peut y avoir des variables dans n'importe quel type de question.
            //

            if ( ! empty($variables))
            {
                $questions[$question_id]['question_texte'] = remplacer_variables_question($questions[$question_id]['question_texte'], $variables, $variables_raw);
            }
            
            //
            // Cette question n'est PAS un sondage.
            // Ajouter les points de cette question aux points totaux de l'evaluation,
            //

            if ( ! $q['sondage'])
            {
                // 
                // Ajouter les points de cette question aux points totaux de l'evaluation,
                // sauf s'il s'agit d'une question-sondage.
                //

                $points_evaluation = $points_evaluation + $questions[$question_id]['question_points'];

                //
                // La question est corrigee donc ajoutons les points de la questions aux 
                // points totaux.
                //

                // Je pense que cette ligne etait mal placee, alors je l'ai mise apres la correction des questions. (6 mai 2025)
                // Les points totaux depassaient les points de l'evaluation pour certaines questions.
                // $points_total = $points_total + $q['question_points'];
            }

            //
            // Extraire les reponses
            //

            $reponses = $this->Reponse_model->lister_reponses($question_id);

            //
            // Remplacer les variables dans les reponses SANS equation, si necessaire.
            //

            if ( ! empty($reponses) && ! empty($variables))
            {
                foreach($reponses as $reponse_id => $r)
                {
                    if ($r['equation'])
                        continue;

                    if (preg_match('/\<var\>/', $r['reponse_texte']))
                    {
                        foreach ($variables as $var => $var_val)
                        {
                            $var_val = str_replace('.', ',', $var_val);
                            $r['reponse_texte'] = str_replace('<var>' . $var . '</var>', $var_val, $r['reponse_texte']);
                        }
                        
                        $reponses[$reponse_id]['reponse_texte'] = $r['reponse_texte'];
                    }
                }
            }

            // ------------------------------------------------------------
            //
            // Debut de la correction
            //
            // ------------------------------------------------------------

            //
            // Reponse de l'etudiant a la question
            //

            $reponse_etudiant = NULL;

            if ($origine == 'formulaire')
            {
                if (array_key_exists('question_' . $question_id, $data))
                {
                    $reponse_etudiant = $data['question_' . $question_id];
                }
            }

            if ($origine == 'traces')
            {
                if (array_key_exists($question_id, $data))
                {
                    $reponse_etudiant = $data[$question_id];
                }
            }

            // ------------------------------------------------------------
            //
            // Question a choix unique (TYPE 1)
            //
            // ------------------------------------------------------------

            if ($q['question_type'] == 1)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_1(
                    array(
                        'question_id'         => $question_id,
                        'sondage'             => $q['sondage'],
                        'reponse_repondue_id' => $reponse_etudiant ?? 0,
                        'reponses'            => $reponses,
                        'points'              => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];
                            
            } // TYPE 1

            // ------------------------------------------------------------------------
            //
            // Question a developpement (TYPE 2)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 2)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_2(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant ?? NULL,
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Verifier si cette question doit etre corrigee manuellement.
                //

                if ( ! $res['corrigee'])
                {
                    $corrections_terminees = 0;
                    $corrections_manuelles = 1;
                }

            } // TYPE 2

            // ------------------------------------------------------------------------
            //
            // Question a coefficients variables (TYPE 3)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 3)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_3(
                    array(
                        'question_id'         => $question_id,
                        'sondage'             => $q['sondage'],
                        'reponse_repondue_id' => $reponse_etudiant ?? 0,
                        'reponses'            => $reponses,
                        'variables'           => $variables,
                        'points'              => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPE 3

            // ------------------------------------------------------------------------
            //
            // Question a choix multiples (TYPE 4)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 4)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_4(
                    array(
                        'question_id'            => $question_id,
                        'sondage'                => $q['sondage'],
                        'reponses_repondues_ids' => is_array($reponse_etudiant) ? $reponse_etudiant : array(),
                        'reponses'               => $reponses,
                        'points'                 => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPE 4

            // ------------------------------------------------------------------------
            //
            // Question a reponse numerique entiere (TYPE 5)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 5)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_5(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant,
                        'reponses'         => $reponses,
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPR 5

            // ------------------------------------------------------------------------
            //
            // Question a reponse numerique (TYPE 6)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 6)
            {       
                //
                // Corriger la question
                //

                $res = corriger_question_type_6(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant,
                        'reponses'         => $reponses,
                        'tolerances'       => $this->Question_model->extraire_tolerances($question_id),
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPE 6

            // ------------------------------------------------------------------------
            //
            // Question a reponse litterale courte (TYPE 7)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 7)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_7(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant ?? NULL,
                        'reponses'         => $reponses,
                        'similarite'       => $this->Question_model->extraire_similarite($question_id),
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPE 7

            // ------------------------------------------------------------------------
            //
            // Question a reponse numerique par equation (TYPE 9)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 9)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_9(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant ?? NULL,
                        'reponses'         => $reponses,
                        'variables'        => $variables,
                        'tolerances'       => $this->Question_model->extraire_tolerances($question_id),
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPE 9

            // ------------------------------------------------------------------------
            //
            // Question a repondre par televersement de documents (TYPE 10)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 10)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_10(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant ?? NULL,
                        'documents'        => $documents,
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajouter les documents dans le tableau des documents pour qu'ils soient enregistres.
                //

                if ($res['documents_trouves'] > 0)
                {
                    $questions_ids_docs[] = $question_id;
                }

                //
                // Verifier si cette question doit etre corrigee manuellement.
                //

                if ( ! $res['corrigee'])
                {
                    $corrections_terminees = 0;
                    $corrections_manuelles = 1;
                }

            } // TYPE 10

            // ------------------------------------------------------------------------
            //
            // Question a choix multiples (TYPE 11)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 11)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_11(
                    array(
                        'question_id'            => $question_id,
                        'sondage'                => $q['sondage'],
                        'reponses_repondues_ids' => is_array($reponse_etudiant) ? $reponse_etudiant : array(),
                        'reponses'               => $reponses,
                        'points'                 => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Ajoutons les points obtenus aux points totaux obtenus pour l'evaluation.
                //

                $points_obtenus = $points_obtenus + $res['points_obtenus'];

            } // TYPE 11

            // ------------------------------------------------------------------------
            //
            // Question a developpement (TYPE 12)
            //
            // ------------------------------------------------------------------------

            elseif ($q['question_type'] == 12)
            {
                //
                // Corriger la question
                //

                $res = corriger_question_type_12(
                    array(
                        'question_id'      => $question_id,
                        'sondage'          => $q['sondage'],
                        'reponse_repondue' => $reponse_etudiant ?? NULL,
                        'points'           => $questions[$question_id]['question_points']
                    )
                );

                //
                // Enregistrer l'evaluation telle qu'elle s'est produite dans la soumission.
                //

                $questions[$question_id] = array_merge($questions[$question_id], $res);

                //
                // Verifier si cette question doit etre corrigee manuellement.
                //

                if ( ! $res['corrigee'])
                {
                    $corrections_terminees = 0;
                    $corrections_manuelles = 1;
                }

            } // TYPE 12


            //
            // Si la question est corrigee donc ajoutons les points de la questions aux 
            // points totaux.
            //

            if ($res['corrigee'])
            {
                $points_total = $points_total + $q['question_points'];
            }

        } // Iteration a travers toutes les questions

        // ------------------------------------------------------------------
        //
        // FIN DE LA CORRECTION
        //
        // ------------------------------------------------------------------

        //
        // Extra
        //

        $extra = array();

        if ($origine == 'formulaire')
        {
			if (array_key_exists('temps_ecoule', $data) && ! empty($data['temps_ecoule']))
			{
				$temps_ecoule_str = json_decode($data['temps_ecoule']);

				$extra['temps_ecoule_str'] = $temps_ecoule_str;
			}
        }

		if (array_key_exists('secondes_en_redaction', $data) && ! empty($data['secondes_en_redaction']))
		{
			$extra['temps_redaction_str'] = calculer_duree(0, $data['secondes_en_redaction']);
		}

        $extra['temps_limite'] = (array_key_exists('temps_limite', $rel_evaluation) && ! empty($rel_evaluation['temps_limite'])) ? $rel_evaluation['temps_limite'] : 0;

        //
        // Generer la reference pour la soumission
        //

        $soumission_reference = $this->generer_soumission_reference();

		// 
		// Genererer l'empreinte
		//

		$empreinte = $this->Evaluation_model->generer_empreinte($soumission_reference);

		//
		// Generer les donnees de la soumission
		//

		$donnees_soumissionnaire = array(
			'agent_string' => $data['agent_string'] ?? $this->agent->agent_string(), 
            'fureteur_id'  => $data['fureteur_id']  ?? $this->Admin_model->generer_fureteur_id()
		);

		//
		// Enregistrer l'evaluation
		//

		$data_in = array(
			'groupe_id'                 => $groupe['groupe_id'],
			'semestre_id'               => $semestre['semestre_id'],
			'cours_id'                  => $cours['cours_id'],
			'evaluation_id'             => $evaluation_id,
			'enseignant_id'             => $enseignant['enseignant_id'],
			'etudiant_id'               => $etudiant_id,
			'session_id'                => $data['session_id'],
			'unique_id'                 => $data['unique_id']  ?? $this->Admin_model->generer_unique_id(),
			'adresse_ip'                => $data['adresse_ip'] ?? $this->input->ip_address(),
			'evaluation_reference'      => $evaluation_reference,
			'soumission_reference'      => $soumission_reference,
			'empreinte'                 => $empreinte,
			'soumission_data'           => json_encode($donnees_soumissionnaire),
			'soumission_debut_epoch'    => $data['soumission_debut_epoch'],
			'soumission_date'           => date_humanize($this->now_epoch, TRUE),
			'soumission_epoch'          => $this->now_epoch,
			'prenom_nom'                => $etudiant['prenom_nom'],
			'numero_da'                 => $etudiant['numero_da'],
			'courriel'                  => $data['courriel'] ?? NULL,
			'cours_data_gz'             => gzcompress(json_encode($cours_data), 4)      ?: json_encode($cours),
			'evaluation_data_gz'        => gzcompress(json_encode($evaluation_data), 4) ?: json_encode($evaluation),
			'questions_data_gz'         => gzcompress(json_encode($questions), 8)       ?: json_encode($questions),
			'images_data_gz'            => empty($images)    ? NULL : (gzcompress(json_encode($images), 8)    ?: json_encode($images)),
			'documents_data_gz'         => empty($documents) ? NULL : (gzcompress(json_encode($documents), 8) ?: json_encode($documents)),
			'extra_data'                => empty($extra) ? NULL : json_encode($extra),
			'points_obtenus'            => $points_obtenus,
			'points_total'              => $points_total,
			'points_evaluation'         => $points_evaluation,
			'permettre_visualisation'   => $evaluation['formative'] ? 1 : 0,
			'corrections_terminees'     => $corrections_terminees,
			'corrections_manuelles'     => $corrections_manuelles,
			'non_terminee'              => $non_terminee,
			'version'                   => 3
        ); 

		if ($lab)
        {
            $data_in['lab']					= $lab;
            $data_in['lab_etudiant2_id']    = $lab_data['lab_partenaire2_etudiant_id'] ?? NULL;
            $data_in['lab_etudiant3_id']    = $lab_data['lab_partenaire3_etudiant_id'] ?? NULL;
            $data_in['lab_data']            = json_encode($lab_data);
			$data_in['lab_valeurs']			= $evaluation['lab_valeurs'];   // deja au format JSON
            $data_in['lab_points']			= $evaluation['lab_points'];    // deja au format JSON
            $data_in['lab_points_champs']   = json_encode($lab_corrections['points_champs']);
			$data_in['lab_points_tableaux'] = json_encode($lab_corrections['points_bilan']['points_tableaux']);
		}

        $this->db->insert('soumissions', $data_in);

        $soumission_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        //
        // Si c'est un laboratoire avec des partenaires, il faut partager la soumission.
        //

        if ($lab && ! empty($lab_partenaires) && is_array($lab_partenaires))
        {
            foreach($lab_partenaires as $lab_partenaire)
            {
                $soumission_p_data = array(
                    'soumission_id' => $soumission_id,
                    'etudiant_id'   => $lab_partenaire['etudiant_id']
                );

                $this->db->insert('soumissions_partagees', $soumission_p_data);
            } 
        }

        //
        // Assigner les documents des questions de type 10 a cette soumission.
        //

        if ( ! empty($documents) && count($documents) > 0)
        {
            $data_d = array();

            foreach($documents as $d)
            {
                $data_d[] = array(
                    'doc_id'               => $d['doc_id'],
                    'soumission_id'        => $soumission_id,
                    'soumission_reference' => $soumission_reference
                );
            }

            if ( ! empty($data_d))
            {
                $this->db->update_batch('documents_etudiants', $data_d, 'doc_id');
            }
        }

		//
		// Log
		// 

		if ( ! empty($etudiant_id))
        {
            $planificateur = TRUE;

			if ($origine == 'formulaire')
			{
				$action        = "L'évaluation a été soumise par l'étudiant.";
                $action_court  = 'soumission_etudiant';
                $planificateur = FALSE;
			}
			else
			{
				if (is_cli())
				{
					$action       = "L'évaluation a été soumise automatiquement par le planificateur.";
					$action_court = 'soumission_cli';
				}
				else
				{
					$action        = "L'évaluation a été soumise par l'enseignant.";
                    $action_court  = 'soumission_enseignant';
                    $planificateur = FALSE;
				}
			}

			$this->Evaluation_model->ecrire_activite_evaluation_soumission(
				array(
					'semestre_id'			=> $semestre['semestre_id'],
					'etudiant_id'			=> $etudiant_id,
					'action'                => $action,
					'action_court'          => $action_court,
					'evaluation_id'         => $evaluation_id,
					'evaluation_reference'  => $evaluation_reference,
					'soumission_id'			=> $soumission_id,
                    'soumission_reference'	=> $soumission_reference,
                    'planificateur'         => $planificateur
				)
			);

			//
			// Insrire les informations de la soumission dans l'activite de l'evaluation
			//
		
			if ( ! empty($etudiant_id))
			{
				$this->Evaluation_model->finaliser_activite_evaluation(
					array(
						'semestre_id'		   => $semestre['semestre_id'],
						'soumission_id' 	   => $soumission_id,
						'soumission_reference' => $soumission_reference,
						'evaluation_id' 	   => $evaluation_id,
						'evaluation_reference' => $evaluation_reference,
						'etudiant_id' 		   => $etudiant_id
					)
				);
			}
		}

		//
		// Envoyer un courriel de confirmation d'envoi de l'evaluation
		//

		$courriel_envoye = FALSE;

        if ( 
            ( ! empty($etudiant_id) && $etudiant['courriel_evaluation_envoyee']) || // Les etudiants inscrits
            (   empty($etudiant_id) && @$this->est_enseignant )                  || // Les enseignants
            (   empty($etudiant_id) && ! empty($data['courriel']))                  // Les etudiants non inscrits qui ont entre leur courriel
           )
        {
			$courriel = NULL;

			if ( ! empty($etudiant_id))
			{
				$courriel = $etudiant['courriel'];
			}
			elseif (@$this->est_enseignant)
			{
				$courriel = $this->enseignant['courriel'];
			}	
			else
			{
				$courriel = $data['courriel'];
			}	

			$contenu_data = array(
				'evaluation'  => $evaluation_data,
				'prenom_nom'  => $etudiant['prenom_nom'],
				'numero_da'   => $etudiant['numero_da'],
				'reference'   => $soumission_reference,
				'empreinte'   => $empreinte,
				'salutations' => (date('H') > 17 || date('H') < 5) ? 'Bonsoir' : 'Bonjour'
			);

			if (
				$this->Courriel_model->envoyer_courriel(
					array(
						'destination_courriel' => $courriel,
						'sujet'                => "Confirmation d'envoi de votre évaluation [" . $soumission_reference . ']',
						'contenu'              => (is_cli() ? 'evaluation/evaluation_confirmation_email3_cli' : 'evaluation/evaluation_confirmation_email3'),
						'contenu_data'         => $contenu_data,
						'raison'               => 'soumission',
						'raison_data'          => array('soumission_reference' => $soumission_reference)
					)
				)
			)
			{
				$courriel_envoye = TRUE;
			}
		}

        //
        // Effacer les chargements de cet etudiant
        //

        if ( ! $previsualisation)
        {
            $c_data = array(
                'efface'       => 1,
                'efface_epoch' => $this->now_epoch,
                'efface_date'  => date_humanize($this->now_epoch, TRUE)
            );

            $this->db->where ('evaluation_reference', $evaluation_reference);
            $this->db->where ('epoch >=', $rel_evaluation['ajout_epoch']);

            if ( ! empty($etudiant_id))
            {
                $this->db->where ('etudiant_id', $etudiant_id);
            }
            else
            {
                $this->db->where ('session_id', $data['session_id']);
            }

            $this->db->update('evaluations_securite_chargements', $c_data);
        }

		//
		// Effacer les traces de cette evaluation
		//

		if ( ! $previsualisation)
		{
			$this->Evaluation_model->effacer_traces(
				$data['evaluation_reference'],
				$evaluation_id,
				array(
					'etudiant_id'		   => $etudiant_id,
					'session_id'           => $data['session_id'],
					'evaluation_terminee'  => ($non_terminee ? FALSE : TRUE),
					'soumission_id'        => $soumission_id,
					'soumission_reference' => $soumission_reference
				)
			);
        }

		//
		// Enregistrer, de facon encryptee, les informations sur l'identite de l'etudiant dans un cookie,
		// pour etre en mesure de l'identifier lorsqu'il se connecte au site.
		//

        if ( ! is_cli() && ! $this->logged_in) 
        {
            // Les enseignants pourraient envoyer une soumission pour tester avec la previsualisation.
            // Ne pas les enregistrer comme des etudiants.

            $etudiant = array(
                'prenom_nom' => $data['prenom_nom'],
                'cours_data' => gzuncompress($data_in['cours_data_gz']),
                'numero_da'  => $data['numero_da']
            );

            set_cookie('adata', $this->encryption->encrypt(serialize($etudiant)), 60*60*24*999);
        }

		//
		// Effacer le cache pour que cette soumission apparaisse dans les corrections
		//

		if ($corrections_manuelles)
		{
        	$this->kcache->remove_category('corrections', $data_in['enseignant_id']);
		}

		//
		// Tableau de retour
        //

        return array(
			'prenom_nom'		   => $etudiant['prenom_nom'],
			'numero_da'			   => $etudiant['numero_da'],
            'soumission_id'        => $soumission_id,
            'soumission_reference' => $soumission_reference,
			'empreinte'			   => $empreinte,
			'courriel_envoye'	   => $courriel_envoye,
			'formative'			   => $evaluation['formative'],
			'permettre_visualisation' => $evaluation['formative']  // retrocompatibilite
        );
    }

    // ------------------------------------------------------------------------
    //
    // Generer une reference unique pour une soumission
    //
    // ------------------------------------------------------------------------
    function generer_soumission_reference()
    {
        $this->load->helper('string');
        
		//
        // La surete permet d'eviter une boucle infinie lors de la generation d'un numero de reference unique.
		//

        $surete = 0;

        while($surete < 100)
        {
            $soumission_reference = strtolower(random_string('alpha', 8));

            //
            // Le numero de reference doit etre unique.
            //

            $this->db->from ('soumissions as s');
            $this->db->where('soumission_reference', $soumission_reference);
            $this->db->limit(1);

            $query = $this->db->get(); 

            if ( ! $query->num_rows())
            {
                //
                // Le numero de reference genere est unique, sortons de cette boucle.
                //

                return $soumission_reference;
            }

            $surete++;

			log_alerte(
				array(
					'code'       => 'ESS099',
                    'desc'       => "Ce numéro de référence de soumission (" . $soumission_reference . ") existe déjà, il faut en générer un autre (" . $surete . ").",
                    'importance' => 7
				)
			);

            if ($surete > 90)
            {
                generer_erreur2(
                    array(
                        'code'  => 'ESS100', 
                        'desc'  => "L'incrémentation de sûreté a été activée en tentant de générer plus de 90 fois un numéro de référence unique pour une soumission.",
                        'extra' => 'compteur surete = ' . $surete,
                        'importante' => 7
                    )
                );
                return;
            }
        } // while
    }

} // class
