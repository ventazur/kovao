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
 * LAB MODEL
 *
 * ============================================================================ */

use jlawrence\eos\Parser;

class Lab_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();
    }

    /* ----------------------------------------------------------------
     *
     * Verifier que l'evaluation n'est pas deja ouverte par le partenaire.
     *
     * ---------------------------------------------------------------- */
    function lab_deja_ouvert_par_partenaire($evaluation_reference)
    {
        if ( ! $this->est_etudiant)
            return FALSE;

        $codes_erreurs = array(
            0 => ['status' => FALSE],
            8 => ['status' => TRUE,  'code' => 'GS45789', 'erreur' => "Le laboratoire a déjà été ouvert par votre partenaire. Veuillez travailler en équipe sur sa version."],
            9 => ['status' => TRUE,  'code' => 'GS45790', 'erreur' => "Le laboratoire a déjà été soumis par votre partenaire. Le travail est complété."]
        );

        //
        // Verifier si deja des traces sont presentes d'une evaluation ouverte par son partenaire.
        //

        $this->db->from     ('etudiants_traces');
        $this->db->where    ('evaluation_reference', $evaluation_reference);
		$this->db->where    ('evaluation_envoyee', 0);
		// $this->db->where	('efface_par_etudiant', 0);
        $this->db->where    ('lab', 1);

        $this->db->group_start();
            $this->db->where    ('lab_etudiant2_id', $this->etudiant_id);
            $this->db->or_where ('lab_etudiant3_id', $this->etudiant_id);
        $this->db->group_end();

        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $codes_erreurs[8];
        }

        //
        // Verifier si une soumission n'est pas deja ete envoyee pour ce laboratoire par son partenaire.
        //

        $this->db->from  ('soumissions');
        $this->db->where ('evaluation_reference', $evaluation_reference);
        $this->db->where ('efface', 0);
        $this->db->where ('lab', 1);

        $this->db->group_start();
            $this->db->where    ('lab_etudiant2_id', $this->etudiant_id);
            $this->db->or_where ('lab_etudiant3_id', $this->etudiant_id);
        $this->db->group_end();

        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $codes_erreurs[9];
        }

        return $codes_erreurs[0];
    }

    /* ----------------------------------------------------------------
     *
     * Confirmer le numero de place
     * Confirmer les partenaires de laboratoire
     *
     * ---------------------------------------------------------------- */
    function confirmer_lab_partenaires($post_data)
    {
        $mg['alert'] = 'danger';

        //
        // Etudiant
        //

        if ($this->est_etudiant)
        {
            $traces_completes = $this->Etudiant_model->extraire_traces_completes($post_data['evaluation_reference'], $post_data['evaluation_id']);

            if (empty($traces_completes))
            {
                $mg['message'] = "Les traces n'ont pas été enregistrées.";
                $this->session->set_flashdata('message_general', $mg);
                return FALSE;
            }

            $traces = $traces_completes['data'];
            $traces_arr = unserialize($traces);

            $lab_place = $traces_arr['lab_place'] ?? NULL;

            $lab_partenaire2_eleve_id = $traces_arr['lab_partenaire2_eleve_id'] ?? NULL;
            $lab_partenaire3_eleve_id = $traces_arr['lab_partenaire3_eleve_id'] ?? NULL;

            $lab_individuel = array_key_exists('lab_individuel', $post_data) && $post_data['lab_individuel'] ? TRUE : FALSE;

            //
            // Verifier la selection de la place
            //

            if (empty($lab_place))
            {
                $mg['message'] = "Le numéro de place n'a pas été sélectionné.";
                $this->session->set_flashdata('message_general', $mg);
                return FALSE;
            }

            //
            // Verifier la selection des partenaires
            //

            if ($lab_individuel)
            {
                $lab_partenaire2_eleve_id = 0;
            }
            else
            {
                if ($lab_partenaire2_eleve_id !== '0' && empty($lab_partenaire2_eleve_id))
                {
                    $mg['message'] = "Le partenaire 2 ne peut pas être vide.";
                    $this->session->set_flashdata('message_general', $mg);
                    return FALSE;
                }
                if ($lab_partenaire2_eleve_id === '0' && ! empty($lab_partenaire3_eleve_id))
                {
                    $mg['message'] = "Le partenaire 2 doit être sélectionné avant le partenaire 3.";
                    $this->session->set_flashdata('message_general', $mg);
                    return FALSE;
                }

                if ($lab_partenaire2_eleve_id == $lab_partenaire3_eleve_id)
                {
                    $mg['message'] = "Les deux partenaires sont les mêmes.";
                    $this->session->set_flashdata('message_general', $mg);
                    return FALSE;
                }
            }

            //
            // Extraire les informations sur les etudiants (etudiant_id)
            //

            $lab_etudiant2_id = NULL;
            $lab_etudiant3_id = NULL;

            if ( ! empty($lab_partenaire2_eleve_id))
            {
                $etudiant2 = $this->Etudiant_model->extraire_etudiant_de_eleve($traces_arr['lab_partenaire2_eleve_id']);

                if (empty($etudiant2))
                {
                    $mg['message'] = "Le profil étudiant du partenaire 2 est introuvable.";
                    $this->session->set_flashdata('message_general', $mg);
                    return FALSE;
                }

                if (array_key_exists('etudiant_id', $etudiant2))
                {
                    $lab_etudiant2_id = $etudiant2['etudiant_id'];
                }

                $etudiant2_nom = $etudiant2['prenom'] . ' ' . $etudiant2['nom'];

            }

            if ( ! empty($lab_partenaire3_eleve_id))
            {
                $etudiant3 = $this->Etudiant_model->extraire_etudiant_de_eleve($traces_arr['lab_partenaire3_eleve_id']);

                if (empty($etudiant3))
                {
                    $mg['message'] = "Le profil étudiant du partenaire 3 est introuvable.";
                    $this->session->set_flashdata('message_general', $mg);
                    return FALSE;
                }

                if (array_key_exists('etudiant_id', $etudiant3))
                {
                    $lab_etudiant3_id = $etudiant3['etudiant_id'];
                }

                $etudiant3_nom = $etudiant3['prenom'] . ' ' . $etudiant3['nom'];
            }
     
            //
            // Mettre a jour les traces pour indiquer que les partenaires ont ete confirmes
            //

            $traces_arr['lab_partenaires_confirmes'] = TRUE;

            $traces_arr['lab_etudiant2_id']          = $lab_etudiant2_id ?? NULL;
            $traces_arr['lab_partenaire2_eleve_id']  = $lab_partenaire2_eleve_id ?? NULL;
            $traces_arr['lab_partenaire2_nom']       = $etudiant2_nom ?? NULL;

            $traces_arr['lab_etudiant3_id']          = $lab_etudiant3_id ?? NULL;
            $traces_arr['lab_partenaire3_eleve_id']  = $lab_partenaire3_eleve_id ?? NULL;
            $traces_arr['lab_partenaire3_nom']       = $etudiant3_nom ?? NULL;

            $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr);

            //
            // Ajouter les etudiants aux traces completes (hors de data)
            //

            if ( ! empty($lab_etudiant2_id))
            {
                $data = array(
                    'lab_etudiant2_id' => $lab_etudiant2_id,
                    'lab_etudiant3_id' => $lab_etudiant3_id ?? NULL
                );

                $this->db->where('id', $traces_completes['id']);
                $this->db->update('etudiants_traces', $data);
            }

            //
            // Effacer les traces des partenaires s'ils avaient deja ouvert le laboratoire
            //

            if ( ! empty($lab_etudiant2_id) || ! empty($lab_etudiant3_id))
            {
                //
                // Extraire les traces a effacer
                //

                $this->db->from  ('etudiants_traces');
                $this->db->where ('evaluation_reference', $post_data['evaluation_reference']);
                $this->db->where ('evaluation_id', $post_data['evaluation_id']);
                $this->db->where ('efface', 0);

                if ($lab_etudiant2_id && $lab_etudiant3_id)
                {
                    $this->db->group_start();
                        $this->db->where    ('etudiant_id', $lab_etudiant2_id);
                        $this->db->or_where ('etudiant_id', $lab_etudiant3_id);
                    $this->db->group_end();
                }
                elseif ($lab_etudiant2_id)
                {
                    $this->db->where ('etudiant_id', $lab_etudiant2_id);
                }
                elseif ($lab_etudiant3_id)
                {
                    $this->db->where ('etudiant_id', $lab_etudiant3_id);
                }
                
                $query = $this->db->get();
                
                if ($query->num_rows() > 0)
                {
                    //
                    // Effacer les traces des etudiants
                    //

                    $etudiants_traces = $query->result_array();

                    $traces_id = array_column($etudiants_traces, 'id');

                    if ( ! empty($traces_id))
                    {
                        $data_traces = array(
                            'efface' => 1
                        );

                        $this->db->where_in('id', $traces_id);
                        $this->db->update('etudiants_traces', $data_traces);
                    }
                }
            }

        } // est etudiant

        //
        // Enseignant
        //

        elseif ($this->est_enseignant)
        {
            $traces_completes = $this->Enseignant_model->extraire_traces_completes($post_data['evaluation_reference'], $post_data['evaluation_id']);

            if (empty($traces_completes))
            {
                $mg['message'] = "Les traces n'ont pas été enregistrées.";
                $this->session->set_flashdata('message_general', $mg);
                return FALSE;
            }

            $traces = $traces_completes['data'];
            $traces_arr = unserialize($traces);

            $lab_place = $traces_arr['lab_place'] ?? NULL;
            $lab_partenaire2 = $traces_arr['lab_partenaire2'] ?? NULL;
            $lab_partenaire3 = $traces_arr['lab_partenaire3'] ?? NULL;

            //
            // Verifier la selection de la place
            //

            if (empty($lab_place))
            {
                $mg['message'] = "Le numéro de place n'a pas été sélectionné.";
                $this->session->set_flashdata('message_general', $mg);
                return FALSE;
            }

            //
            // Mettre a jour les traces pour indiquer que les partenaires ont ete confirmes
            //

            $traces_arr['lab_partenaires_confirmes'] = TRUE;

            $traces_arr['lab_partenaire2_nom']       = $lab_partenaire2 ?? NULL;
            $traces_arr['lab_partenaire3_nom']       = $lab_partenaire3 ?? NULL;

            $this->Evaluation_model->ecrire_traces($post_data['evaluation_reference'], $post_data['evaluation_id'], $traces_arr);
        }

        return TRUE;
    }

    /* ----------------------------------------------------------------
     *
     * Changer lab individuel
     *
     * ---------------------------------------------------------------- */
	public function changer_lab_individuel($evaluation_id, $individuel)
    {/*{{{*/
        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if ( ! $evaluation['lab'])
            return FALSE;

        $lab_parametres = json_decode($evaluation['lab_parametres'], TRUE);

        $lab_parametres['individuel'] = (int) $individuel;

        $lab_parametres_json = json_encode($lab_parametres);

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', array('lab_parametres' => $lab_parametres_json));

        return TRUE;
    }/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Changer les parametres de la precorrection
     *
     * ---------------------------------------------------------------- */
	public function changer_precorrection_parametres($evaluation_id, $param, $val)
    {/*{{{*/
        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if ( ! $evaluation['lab'])
            return FALSE;

        if ( ! array_key_exists('lab_parametres', $evaluation) || empty($evaluation['lab_parametres']))
        {
            $lab_parametres = array(
                'precorrection' => (float) 1,
                'precorrection_essais' => (float) 10,
                'precorrection_penalite' => (float) 0.5
            );
        }
        else
        {
            $lab_parametres = json_decode($evaluation['lab_parametres'], TRUE);
        }

        $lab_parametres[$param] = (float) $val;

        $lab_parametres_json = json_encode($lab_parametres);

        $this->db->where ('evaluation_id', $evaluation_id);
        $this->db->update('evaluations', array('lab_parametres' => $lab_parametres_json));

        return TRUE;
    }/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction d'un laboratoire
     *
     * ---------------------------------------------------------------- */
	public function corriger_laboratoire($evaluation_id, $champs_a_corriger, $options = array())
	{/*{{{*/
		$options = array_merge(
			 array(
                 'precorrection' => FALSE,
                 'evaluation'    => array() // $evaluation de extraire_evaluation (si deja extraite)
			 ),
			 $options
        );

		//
		// A retourner
		//

		$r = array(
			'erreur' 		=> FALSE,
            'erreur_code'   => 0,
            'erreur_msg'	=> NULL,
			'points_champs' => array(), // champ => 'points', 'points_obtenus', 'reponse_correcte', 'reponse', 'succes'
			'points_bilan'	=> array(
				'points_tableaux' => array(),
				'points_totaux'	  => 0,
				'points_totaux_obtenus' => 0
			)
		);
	
		//
		// Verifier les arguments obligatoires
		//

		if (empty($evaluation_id) || ! is_numeric($evaluation_id))
		{
            $r['erreur']      =  TRUE;
            $r['erreur_code'] = 9;
			$r['erreur_msg']  = "L'evaluation_id n'est pas correctement entre.";
			return $r;
		}

		if (empty($champs_a_corriger))
        {
            /*
			$r['erreur']      =  TRUE;
            $r['erreur_code'] = 8;
			$r['erreur_msg']  = "Les champs a corriger ne sont pas presents.";
            return $r;
            */
		}

		//
		// Extraire l'evaluation
		//

        if (empty($options['evaluation']))
        {
            $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);	
        }
        else
        {
            $evaluation = $options['evaluation'];
        }

		if ( ! $evaluation['lab'])
		{
            $r['erreur']      =  TRUE;
            $r['erreur_code'] = 99;
			$r['erreur_msg']  = "Cette evaluation n'est pas un laboratoire.";
			return $r;
		}
		
		//
		// Extraire les informations sur le laboratoire
		//
		
        $lab_valeurs = ! empty($evaluation['lab_valeurs']) ? json_decode($evaluation['lab_valeurs'], TRUE) : array();
        $lab_points  = ! empty($evaluation['lab_points']) ? json_decode($evaluation['lab_points'], TRUE) : array();
        $lab_prefix  = $evaluation['lab_prefix'];

        $lab_valeurs = complementer_lab_valeurs($lab_valeurs, $lab_points);

        //
        // Verifier que les champs sont exempts de prefix,
        //

        $champs_tous = array();

        foreach($champs_a_corriger as $c => $c_arr)
        {
            if (preg_match('/^' . $lab_prefix . '-(.+)$/', $c, $matches)) 
            {
                $c = $matches[1];
			}

            $champs_tous[$c] = $c_arr;
			// $champs_tous[$c]['valeur'] = str_replace(',', '.', $champs_tous[$c]['valeur']);

			if (array_key_exists($c, $lab_valeurs) && array_key_exists('nsci', $lab_valeurs[$c]) && ! empty($lab_valeurs[$c]['nsci']))
			{
				$nsci = '1e' . $lab_valeurs[$c]['nsci'];
				$nsci = (float) $nsci;

				$champs_tous[$c]['nsci'] = $nsci;
			}

			/*
            if (preg_match('/^' . $lab_prefix . '-(.+)$/', $c, $matches)) 
            {
                $c_sans_prefix = $matches[1];

                $champs_tous[$c_sans_prefix] = $c_arr;
            }
            else
            {
                $champs_tous[$c] = $c_arr;
            }
			*/
        }

		//
		// Determiner les types de champs
		//

		$types = array(
			'standard' 		=> array(),
			'calcul'		=> array(),
			'comparaison'	=> array(),
			'absorbance_d'	=> array(),
            'precision'     => array(),
            'exactitude'    => array(),
            'validite'      => array()
        );

		foreach($champs_tous as $c => $c_arr)
		{
			$type = $lab_points[$c]['type'] ?? 'standard';
			$types[$type][] = $c;
        }

		//
		// Les array pour enregistrer les resultats des corrections
		//
	
		$points_champs = array();

		$points_bilan = array(
			'points_tableaux' 		=> array(),
			'points_totaux'	 	 	=> 0,
			'points_totaux_obtenus' => 0
		);

        $points_tableaux = array();
        $points_totaux = 0;

        //
        // Initialisation
        //

        foreach ($lab_points as $c => $c_arr)
        {
            //
            // Initialiser tous les champs dans points_champs
            //

			$points_champs[$c] = array(
				'points' 			=> $lab_points[$c]['points'],
                'points_obtenus' 	=> 0,
                'reponse'           => $champs_tous[$c]['valeur'] ?? NULL,
                'reponse_correcte'  => NULL,
                'cs_correcte'       => $lab_points[$c]['cs'],   // le nombre de CS attendu
                'cs'                => FALSE,                   // le nombre de CS est bon (TRUE) ou mauvais (FALSE)
				'corrige' 			=> TRUE,                    // une fois corrige, changer ce drapeau
				'succes'  		    => FALSE                    // indique a la precorrection si la reponse est bonne (TRUE) ou non (FALSE)
			);

            $points_bilan['points_totaux'] += $c_arr['points']; 

            $points_bilan['points_tableaux'] = $this->_tableaux_points_totaux(
                $points_bilan['points_tableaux'],
                $c_arr['tableau'],
                $c_arr['points'],
                0
            );
        }

        $r['points_champs'] = $points_champs;
        $r['points_bilan']  = $points_bilan;

		//
		// Corriger les entrees standards
		//

		$rs = $this->_corriger_standards(
			$types['standard'],
			array(
				'precorrection' => $options['precorrection'],
				'lab_points' 	=> $lab_points,
				'champs_tous' 	=> $champs_tous,
				'points_champs' => $r['points_champs'],
				'points_bilan'	=> $r['points_bilan']
			)
        );

		$r = array_merge($r, $rs);

		//
		// Corriger les comparaisons
		//

		$rc = $this->_corriger_comparaisons(
			$types['comparaison'], 
			array(
				'precorrection' => $options['precorrection'],
				'lab_valeurs'	=> $lab_valeurs,
				'lab_points' 	=> $lab_points,
				'champs_tous' 	=> $champs_tous,
				'points_champs' => $r['points_champs'],
				'points_bilan'	=> $r['points_bilan']
			)
		);

		$r = array_merge($r, $rc);
	
		//
		// Corriger les incertitudes sur les absorbances
		//

		$ra = $this->_corriger_absorbances(
			$types['absorbance_d'], 
			array(
				'precorrection' => $options['precorrection'],
				'lab_points' 	=> $lab_points,
				'champs_tous' 	=> $champs_tous,
				'points_champs' => $r['points_champs'],
				'points_bilan'	=> $r['points_bilan']
			)
        );

		$r = array_merge($r, $ra);

		//
		// Corriger les calculs
		//

        /* AVANT 
        if ( ! empty($evaluation['lab_corr_controller']))
        {
            $methode = "_{$evaluation['lab_corr_controller']}";
            $methode_args = array(
                $types['calcul'],
                array(
                    'precorrection' => $options['precorrection'],
                    'lab_points' 	=> $lab_points,
                    'champs_tous' 	=> $champs_tous,
                    'points_champs' => $r['points_champs'],
                    'points_bilan'	=> $r['points_bilan']
                )
            );
                
            $rr = call_user_func_array([$this, $methode], $methode_args);

            $r = array_merge($r, $rr);
        }
        */

		//
		// Corriger les calculs
		//

        if ( ! empty($evaluation['lab_corr_controller']))
        {
            $methode = "_{$evaluation['lab_corr_controller']}";
            // $methode = '_' . 'corr_spectro_mo_v2';
            
            $regles = $this->$methode();

            $rr = $this->_corriger_calculs(
                $types['calcul'],
                array(
                    'precorrection' => $options['precorrection'],
                    'regles'     	=> $regles,
                    'lab_points' 	=> $lab_points,
                    'champs_tous' 	=> $champs_tous,
                    'points_champs' => $r['points_champs'],
                    'points_bilan'	=> $r['points_bilan']
                )
            );

            $r = array_merge($r, $rr);
        }

        //
        // Enlever certains champs lors de la precorrection
        // (pour eviter qu'un petit malin regarde ce qui est retourne par le serveur pour y trouver la reponse)
        //
        // 'reponses_non_arrondies'
        //
        // 'points_bilan'
        //
        // 'points_champs'  => 'reponse_correcte'
        //                  => 'cs_correcte'
        //                  => 'cs_valeur'
        //                  => 'points'
        //                  => 'points_obtenus'
        //

        $points_champs_exclusions = array(
			'reponse_correcte', 'reponse_correcte_ajustee', 'cs_correcte', 'cs_valeur', 'points', 'points_obtenus'
		);

		if ($options['precorrection'] && ! $this->est_enseignant)
        {
            unset($r['reponses_non_arrondies']);
            unset($r['points_bilan']);

            if ( ! empty($r['points_champs']))
            {
                foreach($r['points_champs'] as $c => &$c_arr)
                {
                    foreach($points_champs_exclusions as $k)
                    {
                        if (array_key_exists($k, $c_arr))
                            unset($c_arr[$k]);
                    }
                }
            }
        }

        /*
		$r = array(
			'erreur' 		=> FALSE,
			'erreur_msg'	=> NULL,
            'points_champs' => array(),         // champ => array(
                                                //    'points',            : les points
                                                //    'points_obtenus',    : les points obtenus
                                                //    'reponse_correcte',  : la reponse correcte attendue
                                                //    'reponse',    : la reponse de l'etudiant
                                                //    'cs_correcte' : le nb de CS attendu de la reponse
                                                //    'cs_valeur'   : le nb de CS de la reponse de l'etudiant
                                                //    'cs' :        : les CS ont ete corriges (bool)
                                                //    'corrige'     : le champ a ete corrige
                                                //    'succes'      : la reponse de l'etudiant est bonnne (sans considerer les CS)
			'points_bilan'	=> array(
				'points_tableaux' => array(),   // 1 => array('points' => n, 'points_obtenus' => n), 2 => array(...)
				'points_totaux'	  => 0,
				'points_totaux_obtenus' => 0
            ),
            'reponses_non_arrondies' => array() // champ => reponse_non_arrondie
        );
        */

		return $r;
	}/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction - Determiner les points totaux de chaque tableau
     *
     * ---------------------------------------------------------------- */
	public function _tableaux_points_totaux($points_tableaux, $tableau_no, $points, $points_obtenus)
	{/*{{{*/
		if ( ! array_key_exists($tableau_no, $points_tableaux))
		{
			$points_tableaux[$tableau_no] = array(
				'points' => $points,
				'points_obtenus' => $points_obtenus
			);

			return $points_tableaux;
		}

		$points_tableaux[$tableau_no]['points'] += $points;

		return $points_tableaux;
	}/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction - Determiner les points obtenus de chaque tableau
     *
     * ---------------------------------------------------------------- */
	public function _tableaux_points_obtenus($points_tableaux, $tableau_no, $points, $points_obtenus)
	{/*{{{*/
		if ( ! array_key_exists($tableau_no, $points_tableaux))
		{
			$points_tableaux[$tableau_no] = array(
				'points'         => $points,
				'points_obtenus' => $points_obtenus
			);

			return $points_tableaux;
		}

		$points_tableaux[$tableau_no]['points_obtenus'] += $points_obtenus;

		return $points_tableaux;
	}/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction d'un calcul
     *
     * ----------------------------------------------------------------
     *
     * Verifier si la valeur de l'etudiant correspond a la reponse correcte, 
     * sans considerer les chiffres significatifs.
     *
     * ---------------------------------------------------------------- */
	public function _corriger_calcul($champ, $reponse, $reponse_correcte, $points_champs, $lab_points, $champs_tous, $equation_champs = array())
    {/*{{{*/
		$r = array(
            'points_obtenus'            => 0,
            'reponse_correcte'          => $reponse_correcte,   // reponse de l'enseignant
            'reponse_correcte_ajustee'  => NULL                 // reponse de l'enseignant au nombre de CS demande
        );

		$reponse_cs = cs($reponse); // le nombre de CS de la reponse de l'etudiant
                
        //
        // Ajuster la reponse correcte selon le nombre de CS requis par l'enseignant
        //

        // La reponse correcte ne devrait pas etre ajustee ici, mais plutot dans _corriger_cs (2025-03-17)

        /*
        if ($lab_points['cspp'])
        {
            if ($lab_points['cs'] > 0 && $lab_points['cs'] < 99)
            {
                $r['reponse_correcte_ajustee'] = $reponse_correcte_ajustee = cs_ajustement($reponse_correcte, $lab_points['cs']);
            }
            else 
            {
                // Le nombre de CS > 99, alors probablement une valeur aberrante.
                // Faire comme si aucun CS avait ete choisi.
               
                $r['reponse_correcte_ajustee'] = cs_ajustement($reponse_correcte, $reponse_cs);
            }
        }
        else
        {
            // L'enseignant n'a pas specifie de CS a cette reponse, alors ajustons la reponse correcte
            // au nombre de CS donne par l'etudiant.

            $r['reponse_correcte_ajustee'] = cs_ajustement($reponse_correcte, $reponse_cs);
        }
        */

        //
        // Correction
        //

        // *** 
        //
        //      Il faut corriger la reponse de l'etudiant selon la reponse correcte
        //      ajustee a l'incertitude de l'etudiant.
        //
        //      OU
        //
        //      Il faut corriger la reponse de l'etudiant selon la reponse correcte
        //      ajustee au CS de l'etudiant. Les CS seront corriges par la suite.
        //
        // ***

        //
        // Incertitude
        //

        if (array_key_exists('incertitude', $lab_points) && ! empty($lab_points['incertitude']))
        {
            $champ_incertitude = $lab_points['incertitude'];

            if (array_key_exists($champ_incertitude, $champs_tous) && ! empty($champs_tous[$champ_incertitude]['valeur']))
            {
                $incertitude = $champs_tous[$champ_incertitude]['valeur'];

                $reponse_ajustee = incertitude_ajustement($reponse, $incertitude);
                $reponse_correcte_ajustee = incertitude_ajustement($reponse_correcte, $incertitude);
            
                if ($reponse_ajustee == $reponse_correcte_ajustee)
                {
                    $r['points_obtenus'] = format_nombre($lab_points['points'], array('virgule' => FALSE));
                    $r['succes'] = TRUE;
                }
            }
        }

        //
        // CS
        //

        $reponse_correcte_ajustee_cs_etudiant = cs_ajustement($reponse_correcte, $reponse_cs);

        if ((string) $reponse_correcte_ajustee_cs_etudiant == (string) $reponse)
        {
            $r['points_obtenus'] = format_nombre($lab_points['points'], array('virgule' => FALSE));
            $r['succes'] = TRUE;
        }
        else
        {
            // Il y a un bug vraiment subtil lorsque la valeur de la reponse non ajustee est 0,999936
            // et que l'etudiant doit conserver seulement 3 decimales (pour concorder avec son incertitude)
            // alors sa reponse devient 1,000 et comporte 4 CS. Mais comme 1,000 comporte
            // 4 CS et qu'apres le dernier 9 c'est une 3, cs_ajustement pense que 4 CS devrait etre 0,9999.

            $nombre_tronque = str_replace('.', '', $reponse_correcte_ajustee_cs_etudiant);
            $nombre_tronque = ltrim($nombre_tronque, '0');

            // Cette situation semble se produire seulement lorsque la reponse ajustee commence par un 9 et se termine par 9,
            // comme par exemple: 0,9999; 0,0009999; 99,999
            // car lorsqu'on ajoute un 1 au 9 le plus a droite, cela augmente le nombre de CS et decale le nombre de decimales.

            if (strlen($nombre_tronque) > 0 && $nombre_tronque[0] === '9' && substr($nombre_tronque, -1))
            {
                if (nombre_decimales($reponse_correcte) !== nombre_decimales($reponse))
                {
                    $cs_moins_un = $reponse_cs - 1;

                    $reponse_temp = cs_ajustement($reponse_correcte, $cs_moins_un);
                    $reponse_temp = cs_ajustement($reponse_temp, $reponse_cs);

                    $reponse_correcte_ajustee_cs_etudiant = $reponse_temp;

                    if ((string) $reponse_correcte_ajustee_cs_etudiant == (string) $reponse)
                    {
                        $r['points_obtenus'] = format_nombre($lab_points['points'], array('virgule' => FALSE));
                        $r['succes'] = TRUE;
                    }
                }
            }
        }

        return $r;
    }/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction des calculs (a partir des equations)
     *
     * ---------------------------------------------------------------- */
    public function _corriger_calculs($liste_champs, $opt = array())
    {
		$opt_necessaires = array('precorrection', 'regles', 'lab_points', 'champs_tous', 'points_champs', 'points_bilan');
		
		// liste_champs				: liste des champs a corriger
		// opt						: array des options suivantes : 
        //		=> precorrection	: (bool)
        //		=> equations        : array des equations pour chaque champ a calculer
		// 		=> lab_points 		: array des points de tous les champs
		// 		=> champs_tous 		: array des champs provenant du formulaire, ainsi que leur valeur de input
		// 		=> points_champs 	: array a retourner avec mise a jour des points (a retourner)
		//		=> points_bilan		: array des totaux divers (a retourner)

		if (empty($opt) || ! is_array($opt))
		{
            $r['erreur']      =  TRUE;
            $r['erreur_code'] = 3;
			$r['erreur_msg']  = "L'argument opt est vide ou n'est pas du type demande.";
			return $r;
		}

		foreach($opt_necessaires as $o)
		{
			if ( ! array_key_exists($o, $opt))
			{
				$r['erreur']      = TRUE;
                $r['erreur_code'] = 4;
				$r['erreur_msg']  = "Le champ [" . $o . "] dans opt est inexistant ou n'est pas du type demande.";
				return $r;
			}
		}

        $regles     	= $opt['regles'];
		$lab_points    	= $opt['lab_points'];
		$champs_tous   	= $opt['champs_tous'];
		$points_champs 	= $opt['points_champs'];
        $points_bilan  	= $opt['points_bilan'];

        //
        // Verifier que les equations des champs a calculer sont presentes
        //
        
        foreach($liste_champs as $champ)
        {
            if ( ! array_key_exists($champ, $regles))
            {
                die("Une regle est manquante pour le champ : $champ");
			}

			//
			// Regles : adopter l'equation et les nombres arrondes de l'enseignant, si present
			//

			if (array_key_exists('eq', $lab_points[$champ]) && ! empty($lab_points[$champ]['eq']))
			{
				$regles[$champ]['equation'] = $lab_points[$champ]['eq'];
			}

			if (array_key_exists('eq_na', $lab_points[$champ]) && ! empty($lab_points[$champ]['eq_na']))
			{
				// un enseignant qui ne veut aucun champ arrondi peut indiquer "aucun" ou un nom quelconque
				// qui n'est pas un champ de l'equation

				$na = $lab_points[$champ]['eq_na'];
				$na = str_replace(' ', '', $na);
				$na = str_replace('[', '', $na);
				$na = str_replace(']', '', $na);
				$na = explode(',', $na);

				$regles[$champ]['na_champs'] = $na;
			}
		}

        //
        // Extraire toutes les valeurs des champs inclus dans les equations
        //

        $valeurs = array();
		$reponses_non_arrondies = array();

        foreach($regles as $champ => $regle)
		{	
			$calcul_possible = TRUE; // supposons qu'un calcul est possible
			$champ_calcule   = TRUE; // Est-ce qu'il y a une reponse numerique?			

			$methodes_calculs = array('equation', 'extremes', 'precision', 'exactitude', 'validite');

			if (is_array($regle) && array_key_exists('methode', $regle))
			{
				//
				// METHODES SPECIALES
				//
				
				// 'm_champs' : Les champs necessaires a la methode

				if (array_key_exists('m_champs', $regle))
				{
					if ( ! in_array($regle['methode'], $methodes_calculs))
						continue;

					//
					// Verifier si les valeurs des m_champs sont definies et non-nulles
					//

					foreach($regle['m_champs'] as $c)
					{
						if ( ! array_key_exists($c, $champs_tous) || ! array_key_exists('valeur', $champs_tous[$c]) || ($champs_tous[$c]['valeur'] == NULL))
						{
							$calcul_possible = FALSE;
						}
					}

					if ( ! $calcul_possible)
						continue;
				}
		
				//
				// EQUATION
				//

				if ($regle['methode'] == 'equation')
				{
					$equation = $regle['equation'];

					preg_match_all('/\[(.*?)\]/', $equation, $equation_champs);

					if (empty($equation_champs[1]) || ! is_array($equation_champs[1]))
						continue;

                    // [1] = les champs de l'equation sans les crochets

                    //
                    // Remplacer les champs
                    //

                    $na = array_key_exists('na', $regle) && $regle['na'] == 1 ? TRUE : FALSE;
                    $na_champs = $regle['na_champs'] ?? FALSE;

					foreach($equation_champs[1] as $c)
                    {
                        $valeur = NULL;
                        $na_champ = FALSE;

                        if (is_array($na_champs) && in_array($c, $na_champs))
                        {
                            $na_champ = TRUE;
                        }

                        //
                        // Toujours prendre les valeurs non arrondies si elles sont disponibles
                        //
                        // (a moins d'indication contraire avec le clef 'na' => TRUE)
                        //
                        // na = nombre arrondi
                        //

                        if ($na == TRUE || $na_champ == TRUE)
                        {
                            if (array_key_exists($c, $champs_tous) && array_key_exists('valeur', $champs_tous[$c]) && $champs_tous[$c]['valeur'] !== NULL)
                            {
								$valeur = $champs_tous[$c]['valeur'];
                            }
                            else
                            {
                                $calcul_possible = FALSE;
                                break;
                            }
                        }

                        else
                        {
                            if (array_key_exists($c, $points_champs) && array_key_exists('reponse_correcte', $points_champs[$c]) && ! empty($points_champs[$c]['reponse_correcte']))
                            {
                                $valeur = $points_champs[$c]['reponse_correcte'];
                            }
                            elseif (array_key_exists($c, $champs_tous) && array_key_exists('valeur', $champs_tous[$c]) && $champs_tous[$c]['valeur'] !== NULL)
                            {
                                $valeur = $champs_tous[$c]['valeur'];
                            }
                            else
                            {
                                $calcul_possible = FALSE;
                                break;
                            }
                        }

                        //
                        // Traiter les valeurs pour les rendre appropriees pour les equatioons.
                        //
        
                        $valeur = str_replace(' ', '', $valeur);
                        $valeur = str_replace(',', '.', $valeur);
                        $valeur = n_sci_fix($valeur);

                        //
                        // Il y avait un probleme avec les valeurs en format nsci avec 'e' ou 'E'.
                        // Ceci converti la notation scientifique en notation decimale. (2025-03-15)
                        //
                        
                        $valeur = nsdec($valeur);

                        if (array_key_exists($c, $champs_tous) && array_key_exists('nsci', $champs_tous[$c]) && ! empty($champs_tous[$c]['nsci']))
                        {
                            $valeur = $valeur * $champs_tous[$c]['nsci'];
                        }

                        //
                        // Remplacer les champs par leur valeur respective dans l'equation
                        //

                        $equation = str_replace("[$c]", $valeur, $equation);
                    }

                    //
                    // Tous les champs doivent etre rempli pour faire le calcul.
                    //

                    if ( ! $calcul_possible)
						continue;

					//
					// Initialiser le champ dans points_champs
					//			
					
					$points_champs[$champ]['corrige'] = TRUE;

					//
					// Evaluer la reponse correcte
					//

					$points_champs[$champ]['eq_remplacee'] = $equation;

					try 
					{
						$reponse_correcte = Parser::solve($equation);
                    } 

					catch (Exception $e) 
                    {
                        // Ceci sert a capter les divisions par zero.

						$reponse_correcte = 0;
					}

					$reponses_non_arrondies[$champ] = $reponse_correcte;

					$points_champs[$champ]['reponse_correcte'] = $reponse_correcte;
				}

				//
				// EXTREMES
				//
				// La methode des extremes pour trouver l'incertitude absolue d'une moyenne
				//

				if ($regle['methode'] == 'extremes')
				{
                    $vals = array();
                    $vals_d = array();

					foreach($regle['m_champs'] as $i => $c)
                    {
                        if ($i % 2 == 0)
                        {   
                            $vals[$i] = $champs_tous[$c]['valeur'];
                        }
                        else
                        {
                            $vals_d[$i - 1] = $champs_tous[$c]['valeur'];
                        }
					}					

					//
					// Evaluer la reponse correcte (selon les donnees fournies par l'etudiant)
					//

                    if ( ! empty($vals))
                    {
                        $reponse_correcte = lab_corriger_methode_extremes($vals, $vals_d);

                        $reponses_non_arrondies[$champ] = $reponse_correcte;

                        $champs_tous[$champ]['reponse_calculee'] = $reponse_correcte.
                        $points_champs[$champ]['reponse_correcte'] = $reponse_correcte;
                    }

				} // extremes

				//
				// PRECISION
				//
				// Determiner si le resultat est precis ou non selon si
				// l'incertitude relative <= 5,0%.
				// L'incertitude relative fournie doit deja etre en pourcentage.
				//

				if ($regle['methode'] == 'precision')
				{
					$c = $regle['m_champs'][0];

					$inc_rel = str_replace(',', '.', $champs_tous[$c]['valeur']);

					// Si l'etudiant n'a pas repondu, il doit avoir une mauvaise reponse.
					if (array_key_exists($champ, $champs_tous) && $champs_tous[$champ]['valeur'] != NULL)
					{	
						$points_champs[$champ]['reponse'] = $champs_tous[$champ]['valeur'];

						$points_champs[$champ] = array_merge(
							$points_champs[$champ], 
							$this->_corriger_validite(
								'precision',
								$points_champs[$champ],
								$points_bilan,
								array(
									'precorrection' => $opt['precorrection'],
									'precision_champ' => $champ,
									'precis'    => $champs_tous[$champ]['valeur'],
									'inc_rel'   => $inc_rel
								)
							)
						);

					}

					$champ_calcule = FALSE;

				} // precision

				//
				// EXACTITUDE
				//

				if ($regle['methode'] == 'exactitude')
				{
					$c_inc_rel  = $regle['m_champs'][0];
					$c_p_ecart  = $regle['m_champs'][1];
					$c_v_theo   = $regle['m_champs'][2];
					$c_v_theo_d = $regle['m_champs'][3];
					$c_v_exp    = $regle['m_champs'][4];
					$c_v_exp_d  = $regle['m_champs'][5];

					$inc_rel  = str_replace(',', '.', $champs_tous[$c_inc_rel]['valeur']);
					$p_ecart  = str_replace(',', '.', $champs_tous[$c_p_ecart]['valeur']);
					$v_theo   = str_replace(',', '.', $champs_tous[$c_v_theo]['valeur']);
					$v_theo_d = str_replace(',', '.', $champs_tous[$c_v_theo_d]['valeur']);
					$v_exp    = str_replace(',', '.', $champs_tous[$c_v_exp]['valeur']);
					$v_exp_d  = str_replace(',', '.', $champs_tous[$c_v_exp_d]['valeur']);

					// Si l'etudiant n'a pas repondu, il doit avoir une mauvaise reponse.
					if (array_key_exists($champ, $champs_tous) && $champs_tous[$champ]['valeur'] != NULL)
					{	
						$points_champs[$champ]['reponse'] = $champs_tous[$champ]['valeur'];

						$points_champs[$champ] = array_merge(
							$points_champs[$champ], 
							$this->_corriger_validite(
								'exactitude',
								$points_champs[$champ],
								$points_bilan,
								array(
									'precorrection' => $opt['precorrection'],
									'exactitude_champ' => $champ,
									'exact'    => $champs_tous[$champ]['valeur'],
									'inc_rel'  => $inc_rel,
									'p_ecart'  => $p_ecart,
									'v_theo'   => $v_theo,
									'v_theo_d' => $v_theo_d,
									'v_exp'    => $v_exp,
									'v_exp_d'  => $v_exp_d
								)
							)
						);
					}

					$champ_calcule = FALSE;

				} // exactitude

				//
				// VALIDITE
				//

				if ($regle['methode'] == 'validite')
				{
					$c_precis = $regle['m_champs'][0];
					$c_exact  = $regle['m_champs'][1];

					$precis = str_replace(',', '.', $champs_tous[$c_precis]['valeur']);
					$exact  = str_replace(',', '.', $champs_tous[$c_exact]['valeur']);

					// Si l'etudiant n'a pas repondu, il doit avoir une mauvaise reponse.
					if (array_key_exists($champ, $champs_tous) && $champs_tous[$champ]['valeur'] != NULL)
					{	
						$points_champs[$champ]['reponse'] = $champs_tous[$champ]['valeur'];

						$points_champs[$champ] = array_merge(
							$points_champs[$champ], 
							$this->_corriger_validite(
								'validite',
								$points_champs[$champ],
								$points_bilan,
								array(
									'precorrection' => $opt['precorrection'],
									'validite_champ' => $champ,
									'valide' => $champs_tous[$champ]['valeur'],
									'precis' => $precis,
									'exact'  => $exact
								)
							)
						);
					}

					$champ_calcule = FALSE;

				} // validite

				//
				// Corriger la reponse et les CS pour une reponse numerique
				//

				if ($champ_calcule)
                {
					//
					// Reponse de l'etudiant pour le champ a calculer
					//
			
					$reponse = NULL;

					if (array_key_exists($champ, $champs_tous) && array_key_exists('valeur', $champs_tous[$champ]))
					{
						$reponse = str_replace(',', '.', $champs_tous[$champ]['valeur']);
					}

					//
					// Corriger la reponse
                    //

					$points_champs[$champ] = array_merge(
						$points_champs[$champ], 
                        $this->_corriger_calcul(
                            $champ, 
                            $reponse,  // reponse de l'etudiant
                            $reponse_correcte, 
                            $points_champs[$champ], 
                            $lab_points[$champ],
                            $champs_tous,
                            $equation_champs
                        )
					);

					//
					// Corriger les CS 
					// 	- cs et cpp ne sont pas 0
					//  - les points obtenus ne sont pas 0
					//

					if ($lab_points[$champ]['cs'] && $lab_points[$champ]['cspp'])
                    {
                        $donnees = array();

                        /*
                         * Ceci cause des problemes car ca ajoute des deductions de CS lorsqu'il ne devrait pas y en avoir.
                         *
                         */
                        if (empty($lab_points[$champ]['incertitude']) && $lab_points[$champ]['cs'] == 99 && ! empty($equation_champs) && array_key_exists(1, $equation_champs) && is_array($equation_champs[1]) && ! empty($equation_champs[1]))
                        {
                            // Extraire les donnees

                            foreach($equation_champs[1] as $c)
                            {
                                if (array_key_exists($c, $champs_tous) && array_key_exists('valeur', $champs_tous[$c]))
                                {
                                    $donnees[] = str_replace(',', '.', $champs_tous[$c]['valeur']);
                                }
                            }
                        }

						$points_champs[$champ] = array_merge(
							$points_champs[$champ],
                            $this->_corriger_cs(
                                $champ, $champs_tous, $points_champs[$champ], $lab_points[$champ],
                                array('donnees' => $donnees)
                            )
                        );
                    }

				} // champ_calcule

				//
				// Mettre a jour les totaux
				// 

				// $points_bilan['points_totaux'] += $lab_points[$c]['points'];
				$points_bilan['points_totaux_obtenus'] += $points_champs[$champ]['points_obtenus'];

				$points_bilan['points_tableaux'] = $this->_tableaux_points_obtenus(
					$points_bilan['points_tableaux'], 
					$lab_points[$champ]['tableau'], 
					$points_champs[$champ]['points'],
					$points_champs[$champ]['points_obtenus']
				);

				continue;

			} // calculs avec une methode speciale

		}

		return array(
			'erreur'		=> FALSE,
			'erreur_msg'	=> NULL,
			'points_champs' => $points_champs,
			'points_bilan'	=> $points_bilan,
			'reponses_non_arrondies' => $reponses_non_arrondies
		);
    }

    /* ----------------------------------------------------------------
     *
     * Correction les CS d'une valeur
     *
     * ---------------------------------------------------------------- */
	public function _corriger_cs($champ, $champs_tous, $points_champs, $lab_points, $options = array())
    {
        $options = array_merge(
            array(
                'donnees'   => array() // dans le cas ou CS == 99 et le nombre de CS 
                                       // doit etre determine selon certaines donnees (valeurs)
            ),
            $options
        );

		$r = array(
            'points_obtenus'   => $points_champs['points_obtenus'],
            'reponse_correcte' => $points_champs['reponse_correcte'],
			'cs_correcte'      => $lab_points['cs'] ?? 0,
			'cs_valeur'	       => 0,
			'cspp'	 		   => $lab_points['cspp'] ?? 0,
			'cs_corrige'	   => TRUE,
			'cs'			   => FALSE
		);

        $reponse_correcte = $points_champs['reponse_correcte'];
        $calcul = FALSE;

        if ($lab_points['type'] == 'calcul')
        {
            $calcul = TRUE;
        }

        //
        // Corriger CS n'est pas necessaire dans ces cas :
        //

        if ( ! array_key_exists('cs', $lab_points) || $lab_points['cs'] == 0)
        {
            return $r;
        }

        if ( ! array_key_exists($champ, $champs_tous) || ! array_key_exists('valeur', $champs_tous[$champ]) || $champs_tous[$champ]['valeur'] == NULL)
        {
            return $r;
        }

        //
        // Convertir le nombre du francais a l'anglais si necessaire.
        //

        $champ_val = str_replace(',', '.', $champs_tous[$champ]['valeur']);
        $champ_val = str_replace(' ', '', $champ_val); 

        //
        // Determiner le nombre de CS de la reponse de l'etudiant
        //

        $r['cs_valeur'] = cs($champ_val);

        //
        // Les CS des valeurs a 0 ne peuvent pas etre determines.
        //

        if ($champ_val == 0)
        {
            $r['cs'] = TRUE;

            return $r;
        }

		//
		// Corriger les CS
		//

        //
        // Il faut corriger les CS selon 
        //
        // - le nombre de CS des donnees du calcul (propagation des CS sur un resultat)
        // - le nombre de decimales de son incertitude
        //

        if ($lab_points['cs'] == 99)
        {
            if (array_key_exists('est_incertitude', $lab_points) && $lab_points['est_incertitude'])
            {
                return $r;
            }

            //
            // Determiner le nombre de CS d'une valeur base sur son incertitude
            //

            if ( ! empty($lab_points['incertitude']))
            { 
                $champ_d = $lab_points['incertitude'];

                //
                // verifions que la valeur de l'incertitude a ete entree par l'etudiant
                //
                
                if ( ! array_key_exists($champ_d, $champs_tous))
                {
                    $r['cs'] = TRUE;
                    return $r;
                }

				$champ_d_val = str_replace(',', '.', $champs_tous[$champ_d]['valeur']);

				$n_decimales_vd = nombre_decimales($champ_d_val);

                $n_decimales_v = nombre_decimales($champ_val);

                if ($n_decimales_v == $n_decimales_vd)
                { 
                    $r['cs'] = TRUE;

                    if ($calcul)
                        $r['reponse_correcte_ajustee'] = number_format($reponse_correcte, $n_decimales_vd, '.', '');
                    else
                        $r['reponse_correcte_ajustee'] = $champ_val;
                }
                else
                {
                    if ($calcul)
                        $r['reponse_correcte_ajustee'] = number_format($reponse_correcte, $n_decimales_vd, '.', '');
                    else
                        $r['reponse_correcte_ajustee'] = number_format($champ_val, $n_decimales_vd, '.', '');

                    $penalite = $lab_points['points'] * ($lab_points['cspp'] / 100);

                    $r['points_obtenus'] = $r['points_obtenus'] - $penalite;
                    $r['points_obtenus'] = format_nombre($r['points_obtenus'], array('virgule' => FALSE));

                    if ($r['points_obtenus'] < 0)
                    {
                        $r['points_obtenus'] = 0;
                    }
                }
            }

            //
            // si les donnees comparatives sont indiquees
            // (seulement pour les multiplications et divisions)
            //

            elseif ( ! empty($options['donnees']))
            {
                $min_cs = 99;

                foreach($options['donnees'] as $d)
                {
                    $d_cs = cs($d);

                    if ($d_cs < $min_cs)
                    {
                        $min_cs = $d_cs;
                    }
                }

                //
                // Le nombre de CS minimum (a conserver) a ete determine
                //

                //
                // Determiner la reponse correcte ajustee
                //
                
                $r['reponse_correcte_ajustee'] = cs_ajustement($r['reponse_correcte'], $min_cs);

                //
                // Si le calcul n'etait pas correcte, ce n'est pas necessaire de continuer pour enlever une penalite.
                //

                if ($points_champs['points_obtenus'] > 0) 
                {
                    return $r;
                }

                //
                // Verifier si le nombre de CS de l'etudiant correspond a ce qui est attendu.
                //

                if ($min_cs == cs($champ_val))
                {
                    $r['cs'] = TRUE;
                }
                else
                {
                    $penalite = $lab_points['points'] * ($lab_points['cspp'] / 100);

                    $r['points_obtenus'] = $r['points_obtenus'] - $penalite;
                    $r['points_obtenus'] = format_nombre($r['points_obtenus'], array('virgule' => FALSE));

                    if ($r['points_obtenus'] < 0)
                    {
                        $r['points_obtenus'] = 0;
                    }
                }
            }

            return $r;
        }

        //
        // Il faut corriger les CS selon le nombre de CS indique par l'enseignanat
        //

        $r['reponse_correcte_ajustee'] = cs_ajustement($r['reponse_correcte'], $r['cs_correcte']);

		if ($r['cs_correcte'] == $r['cs_valeur'])
		{
            $r['cs'] = TRUE;
        }	

		else
		{
			$penalite = $lab_points['points'] * ($lab_points['cspp'] / 100);

			$r['points_obtenus'] = $r['points_obtenus'] - $penalite;
            $r['points_obtenus'] = format_nombre($r['points_obtenus'], array('virgule' => FALSE));

			if ($r['points_obtenus'] < 0)
			{
				$r['points_obtenus'] = 0;
			}
		}

		return $r;
    }

    /* ----------------------------------------------------------------
     *
     * Correction des champs standards
     *
     * ----------------------------------------------------------------
     *
     * Dans ce type de champs, il n'y a pas de bonne reponse.
     * Toutes les reponses sont bonnes pour autant que l'etudiant entre 
     * une valeur raisonnable.
     * La seule penalite est sur les chiffres significatifs, si l'enseignant
     * a indique le nombre prevu de CS a la reponse.
     *
     * ---------------------------------------------------------------- */
	public function _corriger_standards($liste_champs, $opt) 
	{/*{{{*/
		$opt_necessaires = array('precorrection', 'lab_points', 'champs_tous', 'points_champs', 'points_bilan');
		
		// liste_champs				: liste des champs a corriger
		// opt						: array des options suivantes : 
		//		=> precorrection	: (bool)
		// 		=> lab_points 		: array des points de tous les champs
		// 		=> champs_tous 		: array des champs provenant du formulaire, ainsi que leur valeur de input
		// 		=> points_champs 	: array a retourner avec mise a jour des points (a retourner)
		//		=> points_bilan		: array des totaux divers (a retourner)

		if (empty($opt) || ! is_array($opt))
		{
			$r['erreur'] =  TRUE;
			$r['erreur_msg'] = "L'argument opt est vide ou n'est pas du type demande.";
			return $r;
		}

		foreach($opt_necessaires as $o)
		{
			if ( ! array_key_exists($o, $opt))
			{
				$r['erreur'] =  TRUE;
				$r['erreur_msg'] = "Le champ [" . $o . "] dans opt est inexistant ou n'est pas du type demande.";
				return $r;
			}
		}

		//
		// Corriger
		//

		$lab_points    = $opt['lab_points'];
		$champs_tous   = $opt['champs_tous'];
		$points_champs = $opt['points_champs'];
        $points_bilan  = $opt['points_bilan'];

		foreach($liste_champs as $c)
		{
			if ( ! array_key_exists($c, $champs_tous))
                continue;

            if ( ! array_key_exists($c, $lab_points))
                continue;

            //
            // Verifier que c'est un champ de type standard
            // 
        
			if ($lab_points[$c]['type'] != 'standard')
                continue;

            //
            // Verifier que le champ est non nul.
            //

            if ($champs_tous[$c]['valeur'] != NULL)
            {
                //
                // La reponse correcte correspond a l'entree de l'etudiant.
                //

                $points_champs[$c]['reponse_correcte'] = $champs_tous[$c]['valeur'];

                //
                // Le simple fait qu'il soit rempli, ce champ est un "succes"
                // pour la precorrection. Les CS seront corriges plus tard.
                //

                $points_champs[$c]['succes'] = NULL; // NULL = bleu

                //
                // Fixer la virgule en point pour etre consistant
                //

			    // $champs_tous[$c]['valeur']    = str_replace(',', '.', $champs_tous[$c]['valeur']);			
                // $points_champs[$c]['reponse'] = str_replace(',', '.', $champs_tous[$c]['valeur']);

                //
                // Accorder les points
                //

				$points_champs[$c]['points_obtenus'] = format_nombre($lab_points[$c]['points'], array('virgule' => FALSE));

                //
                // Corriger les CS (ne pas le faire en precorrection pour les etudiants)
                //

                if ($lab_points[$c]['cs'] && $lab_points[$c]['cspp'] && $points_champs[$c]['points_obtenus'] > 0)
                {
                    $points_champs[$c] = array_merge(
                        $points_champs[$c], 
                        $this->_corriger_cs($c, $opt['champs_tous'], $points_champs[$c], $lab_points[$c])
                    );
                }
            }

			$points_bilan['points_totaux_obtenus'] += $points_champs[$c]['points_obtenus'];

			$points_bilan['points_tableaux'] = $this->_tableaux_points_obtenus(
				$points_bilan['points_tableaux'], 
				$lab_points[$c]['tableau'], 
				$points_champs[$c]['points'],
				$points_champs[$c]['points_obtenus']
			);

		} // foreach

		return array(
			'erreur'		=> FALSE,
			'erreur_msg'	=> NULL,
			'points_champs' => $points_champs,
			'points_bilan'	=> $points_bilan
		);
	}/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction des incertitudes sur l'absorbance
     *
     * ---------------------------------------------------------------- */
	public function _corriger_absorbances($liste_champs, $opt) 
	{/*{{{*/
		$opt_necessaires = array('precorrection', 'lab_points', 'champs_tous', 'points_champs', 'points_bilan');
		
		// liste_champs				: liste des champs a corriger
		// opt						: array des options suivantes : 
		//		=> precorrection	: (bool)
		// 		=> lab_points 		: array des points de tous les champs
		// 		=> champs_tous 		: array des champs provenant du formulaire, ainsi que leur valeur de input
		// 		=> points_champs 	: array a retourner avec mise a jour des points (a retourner)
		//		=> points_bilan		: array des totaux divers (a retourner)

		if (empty($opt) || ! is_array($opt))
		{
			$r['erreur'] =  TRUE;
			$r['erreur_msg'] = "L'argument opt est vide ou n'est pas du type demande.";
			return $r;
		}

		foreach($opt_necessaires as $o)
		{
			if ( ! array_key_exists($o, $opt))
			{
				$r['erreur'] =  TRUE;
				$r['erreur_msg'] = "Le champ [" . $o . "] dans opt est inexistant ou n'est pas du type demande.";
				return $r;
			}
		}

		//
		// Corriger les incertitudes sur les absorbances
		//

		$lab_points    = $opt['lab_points'];
		$champs_tous   = $opt['champs_tous'];
		$points_champs = $opt['points_champs'];
		$points_bilan  = $opt['points_bilan'];

		foreach($liste_champs as $c)
		{
            $ca = str_replace('_d', '', $c); // le champ absorbance principal (pas l'incertitude)
            
            //
            //
            //

			if ( ! array_key_exists($c, $champs_tous))
                continue;

			if ( ! array_key_exists($ca, $champs_tous))
                continue;

            //
            // Ce champ n'est pas de type absorbance_d.
            //

			if ($lab_points[$c]['type'] != 'absorbance_d')
                continue;

			if ($champs_tous[$ca]['valeur'] === NULL)
            {
                // ATTN : L'absorbance peut valoir 0.

				// Il n'est pas possible de determiner l'incertitude de l'absorbance si l'absorbance n'est pas definie.
				continue;
			}

            //
            // Debuter la correction
            //

			//
			// Initialiser les champ dans points_champs
			//

            $points_champs[$c]['corrige'] = TRUE;

			//
			// Fixer la virgule en point pour etre consistant
			//
			
            $reponse_avec_point = str_replace(',', '.', $champs_tous[$c]['valeur']);			

			//
			// Determine la reponse correcte (incertitude correcte)
			//

			$reponse_correcte = incertitude_absorbance($champs_tous[$ca]['valeur']);
            $points_champs[$c]['reponse_correcte'] = $reponse_correcte;
            
			//
			// Comparer les valeurs
			//

			if ($reponse_avec_point == $reponse_correcte)
			{
				$points_champs[$c]['points_obtenus'] = format_nombre($lab_points[$c]['points'], array('virgule' => FALSE));
				$points_champs[$c]['succes']		 = TRUE;
			}

			//
			// Corriger les CS 
			// 	- cs et cpp ne sont pas 0
			//  - les points obtenus ne sont pas 0
			//

			if ($lab_points[$c]['cs'] && $lab_points[$c]['cspp'] && $points_champs[$c]['points_obtenus'] > 0)
			{
				$points_champs[$c] = array_merge(
					$points_champs[$c], 
					$this->_corriger_cs($c, $opt['champs_tous'], $points_champs[$c], $lab_points[$c])
				);
			}

			// $points_bilan['points_totaux'] += $lab_points[$c]['points'];
			$points_bilan['points_totaux_obtenus'] += $points_champs[$c]['points_obtenus'];

			$points_bilan['points_tableaux'] = $this->_tableaux_points_obtenus(
				$points_bilan['points_tableaux'], 
				$lab_points[$c]['tableau'], 
				$points_champs[$c]['points'],
				$points_champs[$c]['points_obtenus']
			);

		} // foreach

		return array(
			'erreur'		=> FALSE,
			'erreur_msg'	=> NULL,
			'points_champs' => $points_champs,
			'points_bilan'	=> $points_bilan
		);
	}/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction des comparaisons
     *
     * ---------------------------------------------------------------- */
	public function _corriger_comparaisons($liste_champs, $opt = array())
	{/*{{{*/
		$opt_necessaires = array('precorrection', 'lab_valeurs', 'lab_points', 'champs_tous', 'points_champs', 'points_bilan');

		// liste_champs				: liste des champs a corriger
		// opt						: array des options suivantes : 
		//		=> precorrection	: (bool)
		//		=> lab_valeurs		: array des valeurs a comparer
		// 		=> lab_points 		: array des points de tous les champs
		// 		=> champs_tous 		: array des champs provenant du formulaire, ainsi que leur valeur de input
		// 		=> points_champs 	: array a retourner avec mise a jour des points (a retourner)
		//		=> points_bilan		: array des totaux divers (a retourner)

		if (empty($opt) || ! is_array($opt))
		{
			$r['erreur'] =  TRUE;
			$r['erreur_msg'] = "L'argument opt est vide ou n'est pas du type demande.";
			return $r;
		}

		foreach($opt_necessaires as $o)
		{
			if ( ! array_key_exists($o, $opt))
			{
				$r['erreur'] =  TRUE;
				$r['erreur_msg'] = "Le champ [" . $o . "] dans opt est inexistant ou n'est pas du type demande.";
				return $r;
			}
		}

		//
		// Corriger les comparaisons
		//
		
		$lab_valeurs   = $opt['lab_valeurs'];
		$lab_points    = $opt['lab_points'];
		$champs_tous   = $opt['champs_tous'];
		$points_champs = $opt['points_champs'];
		$points_bilan  = $opt['points_bilan'];

		foreach($liste_champs as $c)
		{
			if ( ! array_key_exists($c, $champs_tous))
				continue;

            //
            // Ce champ n'est pas de type comparaison.
            //

			if ($lab_points[$c]['type'] != 'comparaison')
				continue;

            //
            // La valeur de ce champ n'a pas ete definie.
            //

			if ( ! array_key_exists($c, $lab_valeurs))
				continue;

			//
			// Initialiser les champ dans points_champs
			//

            $points_champs[$c]['corrige'] = TRUE;
            $points_champs[$c]['reponse_correcte'] = $lab_valeurs[$c]['valeur'];

            //
            // Verifier si ce champ est non nul.
			//

            if ($champs_tous[$c]['valeur'] != NULL)
            {
                //
                // Fixer la virgule en point pour etre consistant
                //

                // $champs_tous[$c]['valeur']    = str_replace(',', '.', $champs_tous[$c]['valeur']);			
                // $points_champs[$c]['reponse'] = $champs_tous[$c]['valeur'];

                $reponse_avec_points = str_replace(',', '.', $champs_tous[$c]['valeur']); 

                //
                // Comparer les valeurs
                //

                // Est-ce qu'il y a une tolerance prensente ?

                if ( ! empty($lab_points[$c]['tolerance']))
                {
                    $tolerance = $lab_points[$c]['tolerance'];
                    $tolerance = str_replace(',', '.', $tolerance);

                    $tolerance = $tolerance / 100;
                    $variation = $lab_valeurs[$c]['valeur'] * $tolerance;

                    $v_min = $lab_valeurs[$c]['valeur'] - $variation;
                    $v_max = $lab_valeurs[$c]['valeur'] + $variation;

                    if ($reponse_avec_points >= $v_min && $reponse_avec_points <= $v_max)
                    {
                        $points_champs[$c]['points_obtenus'] = format_nombre($lab_points[$c]['points'], array('virgule' => FALSE));
                        $points_champs[$c]['succes']		 = TRUE;
                    }
                }
                else
				{

                    if ($reponse_avec_points == $lab_valeurs[$c]['valeur'])
                    {
                        $points_champs[$c]['points_obtenus'] = format_nombre($lab_points[$c]['points'], array('virgule' => FALSE));
                        $points_champs[$c]['succes']		 = TRUE;
                    }
                }

                //
                // Corriger les CS
                //

                if ($lab_points[$c]['cs'] && $lab_points[$c]['cspp'] && $points_champs[$c]['points_obtenus'] > 0)
                {
                    $points_champs[$c] = array_merge(
                        $points_champs[$c], 
                        $this->_corriger_cs($c, $champs_tous, $points_champs[$c], $lab_points[$c])
                    );
                }
            } // valeur != NULL
		
			// $points_bilan['points_totaux'] += $lab_points[$c]['points'];
			$points_bilan['points_totaux_obtenus'] += $points_champs[$c]['points_obtenus'];

            $points_bilan['points_tableaux'] = $this->_tableaux_points_obtenus(
				$points_bilan['points_tableaux'], 
				$lab_points[$c]['tableau'], 
				$points_champs[$c]['points'],
				$points_champs[$c]['points_obtenus']
			);

		} // foreach

		return array(
			'erreur'		=> FALSE,
			'erreur_msg'	=> NULL,
			'points_champs' => $points_champs,
			'points_bilan'	=> $points_bilan
		);
	}/*}}}*/

    /* ----------------------------------------------------------------
     *
     * Correction de la validite (precision + exactitude)
     *
     * ----------------------------------------------------------------
     *
     * Cette methode corrige la precision, l'exactitude et la validite,
     * un a la fois.
     *
     * Elle doit etre appelee de la correction d'un lab specifique.
     *
     * ---------------------------------------------------------------- */
	public function _corriger_validite($type, $points_champs, $points_bilan, $opt = array())
	{/*{{{*/
		$r = array(
			'erreur' => FALSE,
			'erreur_msg' => NULL
		);

		$opt_necessaires = array('precorrection');

		// type                     : 'precision', 'exactitude' ou 'validite'
		// opt						: array des options suivantes : 
		//		=> precorrection	: (bool)
		//		=> lab_valeurs		: array des valeurs a comparer
		// 		=> lab_points 		: array des points de tous les champs
		// 		=> champs_tous 		: array des champs provenant du formulaire, ainsi que leur valeur de input
		// 		=> points_champs 	: array a retourner avec mise a jour des points (a retourner)
		//		=> points_bilan		: array des totaux divers (a retourner)

        //      => precision        : nom du champ
        //      => inc_rel          : incertitude relative pertinente (de la reponse pour determiner la precision)

        //      => exactitude       : nom du champ
        //      => p_ecart          : pourcentage d'ecart (pertinente) entre les deux valeurs (admise et experimentale)

        //      => validite         : nom du champ
        //      => precision        : precis ou non precis
        //      => exactitude       : exact ou non exact

		if (empty($opt) || ! is_array($opt))
		{
			$r['erreur'] =  TRUE;
			$r['erreur_msg'] = "L'argument opt est vide ou n'est pas du type demande.";
			return $r;
		}

		foreach($opt_necessaires as $o)
		{
			if ( ! array_key_exists($o, $opt))
			{
				$r['erreur'] =  TRUE;
				$r['erreur_msg'] = "Le champ [" . $o . "] dans opt est inexistant ou n'est pas du type demande.";
				return $r;
			}
		}

        //
        // Precorrection
        //
        // Ne pas precorriger a moins d'etre un enseignant
        //

        if ($opt['precorrection'] && ! @$this->enseignant)
        {
            return $r;
        }

        //
        // Precision
        //

        if ($type == 'precision')
        {
            $r = array(
                'points_obtenus' => 0
            );

            $precis = 0;

            if ($opt['inc_rel'] <= 5)
            {
                $precis = 1;
            }

            if ($precis == $opt['precis'])
            {
			    $r['points_obtenus'] = $points_champs['points'];
                $r['succes'] = TRUE;
            }

            $r['reponse_correcte'] = $precis;
        }

        //
        // Exactitude
        //

        if ($type == 'exactitude')
        {
            $r = array(
                'points_obtenus' => 0
            );

            //
            // Exactitude par calcul
            //

            $exact = 0;

            if ($opt['p_ecart'] <= $opt['inc_rel'])
            {
                $exact = 1;
                $r['exactitude_methode'] = 'calcul';
            }

            if ( ! $exact)
            {
                // 
                // Exactitude par la methode de chevauchement des incertitudes
                // 

                $v_theo_min = $opt['v_theo'] - $opt['v_theo_d'];
                $v_theo_max = $opt['v_theo'] + $opt['v_theo_d'];
                $v_exp_min  = $opt['v_exp']  - $opt['v_exp_d'];
                $v_exp_max  = $opt['v_exp']  + $opt['v_exp_d'];

				// pre 2025-10-05
				// Cette comparaison bogue lorsque les valeurs sont egales et que ce sont des floats.
				//$exact = ($v_exp_min <= $v_theo_max && $v_exp_max >= $v_theo_min);

				$exact1 = float_cmp($v_exp_min, $v_theo_max);
				$exact2 = float_cmp($v_exp_max, $v_theo_min);

				if ($exact1 <= 0 && $exact2 >= 0)
				{
					$exact = TRUE;
				}

				/*
				p($v_exp_min); 
				p($v_theo_max);
				p($v_exp_max); 
				p($v_theo_min); 
				die;
				*/

                if ($exact)
                {
                    $r['exactitude_methode'] = 'chevauchement';
                }
            }

            if ($exact == $opt['exact'])
            {
			    $r['points_obtenus'] = $points_champs['points'];
                $r['succes'] = TRUE;
            }
                
            $r['reponse_correcte'] = $exact;
        } // exactitude

        //
        // Validite
        //

        if ($type == 'validite')
        {
            $r = array(
                'points_obtenus' => 0
            );

            $valide = 0;

            if ($opt['precis'] && $opt['exact'])
            {
                $valide = 1;
            }

            if ($valide == $opt['valide'])
            {
			    $r['points_obtenus'] = $points_champs['points'];
                $r['succes'] = TRUE;
            }

            $r['reponse_correcte'] = $valide;
        }

        return $r;

    } // _corriger_precision}}}

    /* ----------------------------------------------------------------
     *
     * Extraire equation
     *
     * ---------------------------------------------------------------- */
    public function extraire_equation($corr, $clef = NULL)
    {
        if (METHOD_EXISTS($this, '_' . $corr) && ! empty($clef))
        {
            $m = '_' . $corr;

            $arr = $this->$m();

            if (empty($arr) || ! array_key_exists($clef, $arr) || ! array_key_exists('equation', $arr[$clef]) || empty($arr[$clef]['equation']))
               return NULL;

            return $arr[$clef]['equation'];
        }
	}

    /* ----------------------------------------------------------------
     *
     * Extraire equation nombre arrondies (na)
     *
     * ---------------------------------------------------------------- */
    public function extraire_equation_na($corr, $clef = NULL)
    {
        if (METHOD_EXISTS($this, '_' . $corr) && ! empty($clef))
        {
            $m = '_' . $corr;

            $arr = $this->$m();

            if (empty($arr) || ! array_key_exists($clef, $arr) || ! array_key_exists('na_champs', $arr[$clef]) || empty($arr[$clef]['na_champs']))
               return NULL;

            return $arr[$clef]['na_champs'];
        }
	}

    /* ----------------------------------------------------------------
     *
     * ----- METHODES SPECIFIQUES AUX DIFFERENTS LABORATOIRES -----
     *
     * ---------------------------------------------------------------- */

	//
	// par default : les nombres complets avec toutes les decimales
	//
	// na 		   : nombre arrondi (oui, arrondi !) si TRUE
	// na_champs   : champs que l'on doit utiliser les nombres arrondies
	//

    /* ----------------------------------------------------------------
     *
     * SN1 > Validite
     *
     * ----------------------------------------------------------------
	 *
     * Cette function sert seulement a corriger les champs necessitant
	 * des calculs lies specifiquement a ce laboratoire.
     * 
     * ---------------------------------------------------------------- */
	public function _corr_validite_v1()
	{
		return array(

			//
			// Tableau 2
			//

			'm_eau_1'			=> ['methode' => 'equation', 'equation' => '([m_vol_1] - [m_becher_vide])'],
			'm_eau_2'			=> ['methode' => 'equation', 'equation' => '[m_vol_2] - [m_vol_1]'],
			'm_eau_3'			=> ['methode' => 'equation', 'equation' => '[m_vol_3] - [m_vol_2]'],
			'm_eau_4'			=> ['methode' => 'equation', 'equation' => '[m_vol_4] - [m_vol_3]'],
			'm_eau_moy'			=> ['methode' => 'equation', 'equation' => '([m_eau_1] + [m_eau_2] + [m_eau_3] + [m_eau_4]) / 4'],
			'd_m_eau_moy' 		=> ['methode' => 'extremes', 'm_champs' => ['m_eau_1', 'd_m_eau', 'm_eau_2', 'd_m_eau', 'm_eau_3', 'd_m_eau', 'm_eau_4', 'd_m_eau']],
			'inc_rel'			=> ['methode' => 'equation', 'equation' => '[d_m_eau_moy] / [m_eau_moy] * 100', 'na_champs' => ['d_m_eau_moy', 'm_eau_moy']],
			'm_eau_attendue' 	=> ['methode' => 'equation', 'equation' => '10.00 * [p_eau]'],
			'd_m_eau_attendue'  => ['methode' => 'equation', 'equation' => '((0.04/10.00) + ([d_p_eau]/[p_eau])) * [m_eau_attendue]'],
			'p_ecart'			=> ['methode' => 'equation', 'equation' => '(abs([m_eau_moy] - [m_eau_attendue]) / [m_eau_attendue]) * 100', 'na_champs' => ['m_eau_moy', 'm_eau_attendue']],

			//
			// Tableau 3
			//

			'precision'	 => ['methode' => 'precision',  'm_champs' => ['inc_rel']],
			'exactitude' => ['methode' => 'exactitude', 'm_champs' => ['inc_rel', 'p_ecart', 'm_eau_attendue', 'd_m_eau_attendue', 'm_eau_moy', 'd_m_eau_moy']],
			'validite'	 => ['methode' => 'validite',   'm_champs' => ['precision', 'exactitude']]
		);
	}

    /* ----------------------------------------------------------------
     *
     * NYA > Validite
     *
     * ----------------------------------------------------------------
	 *
     * Cette function sert seulement a corriger les champs necessitant
	 * des calculs lies specifiquement a ce laboratoire.
     * 
     * ---------------------------------------------------------------- */
	public function _corr_validite_v1_nya()
	{
		return array(

			//
			// Tableau 2
			//

			//
			// pipette
			//
			
			'm_eau_p-1'	  	=> ['methode' => 'equation', 'equation' => '[m_becher_p-1] - [m_becher_vide_p]'],
			'm_eau_p-2'	  	=> ['methode' => 'equation', 'equation' => '[m_becher_p-2] - [m_becher_p-1]'],
			'm_eau_p-3'	  	=> ['methode' => 'equation', 'equation' => '[m_becher_p-3] - [m_becher_p-2]'],
			'm_eau_moy_p_d' => ['methode' => 'extremes', 'm_champs' => ['m_eau_p-1', 'm_eau_d', 'm_eau_p-2', 'm_eau_d', 'm_eau_p-3', 'm_eau_d']],	
			'm_eau_moy_p' 	=> ['methode' => 'equation', 'equation' => '([m_eau_p-1] + [m_eau_p-2] + [m_eau_p-3]) / 3'],

			'v_exp_p'		=> ['methode' => 'equation', 'equation' => '[m_eau_moy_p] / [rho]'],
			'v_exp_p_d'		=> ['methode' => 'equation', 'equation' => '(([m_eau_moy_p_d]/[m_eau_moy_p]) + ([rho_d]/[rho])) * [v_exp_p]'],

			'inc_rel_p'		=> ['methode' => 'equation', 'equation' => '[v_exp_p_d] / [v_exp_p] * 100'],
			'p_ecart_p'		=> ['methode' => 'equation', 'equation' => '(abs([v_theo_p] - [v_exp_p]) / [v_theo_p]) * 100'],

			'precision_p'	=> ['methode' => 'precision',  'm_champs' => ['inc_rel_p']],
			'exactitude_p'  => ['methode' => 'exactitude', 'm_champs' => ['inc_rel_p', 'p_ecart_p', 'v_theo_p', 'v_theo_p_d', 'v_exp_p', 'v_exp_p_d']],
			'validite_p'	=> ['methode' => 'validite',   'm_champs' => ['precision_p', 'exactitude_p']],

			//
			// burette
			//
			'm_eau_b-1'	  	=> ['methode' => 'equation', 'equation' => '[m_becher_b-1] - [m_becher_vide_b]'],
			'm_eau_b-2'		=> ['methode' => 'equation', 'equation' => '[m_becher_b-2] - [m_becher_b-1]'],
			'm_eau_b-3'		=> ['methode' => 'equation', 'equation' => '[m_becher_b-3] - [m_becher_b-2]'],
			'm_eau_moy_b_d' => ['methode' => 'extremes', 'm_champs' => ['m_eau_b-1', 'm_eau_d', 'm_eau_b-2', 'm_eau_d', 'm_eau_b-3', 'm_eau_d']],	
			'm_eau_moy_b' 	=> ['methode' => 'equation', 'equation' => '([m_eau_b-1] + [m_eau_b-2] + [m_eau_b-3]) / 3'],

			'v_exp_b'		=> ['methode' => 'equation', 'equation' => '[m_eau_moy_b] / [rho]'],
			'v_exp_b_d'		=> ['methode' => 'equation', 'equation' => '(([m_eau_moy_b_d]/[m_eau_moy_b]) + ([rho_d]/[rho])) * [v_exp_b]'],

			'inc_rel_b'		=> ['methode' => 'equation', 'equation' => '[v_exp_b_d] / [v_exp_b] * 100'],
			'p_ecart_b'		=> ['methode' => 'equation', 'equation' => '(abs([v_theo_b] - [v_exp_b]) / [v_theo_b]) * 100'],

			'precision_b'	=> ['methode' => 'precision',  'm_champs' => ['inc_rel_b']],
			'exactitude_b'  => ['methode' => 'exactitude', 'm_champs' => ['inc_rel_b', 'p_ecart_b', 'v_theo_b', 'v_theo_b_d', 'v_exp_b', 'v_exp_b_d']],
			'validite_b'	=> ['methode' => 'validite',   'm_champs' => ['precision_b', 'exactitude_b']],
		);
	}

    /* ----------------------------------------------------------------
     *
     * SN1 > Durete
     *
     * ----------------------------------------------------------------
	 *
     * Cette function sert seulement a corriger les champs necessitant
	 * des calculs lies specifiquement a ce laboratoire.
     * 
     * ---------------------------------------------------------------- */
	public function _corr_durete_v1()
	{
		return array(

			// Regles

			//
			// Tableau 3
			//

			'v_moy'		=> ['methode' => 'equation', 'equation' => '([v_edta-1] + [v_edta-2] + [v_edta-3] + [v_edta-4]) / 4'],
			'v_moy_d' 	=> ['methode' => 'extremes', 'm_champs' => ['v_edta-1', 'v_edta_d', 'v_edta-2', 'v_edta_d', 'v_edta-3', 'v_edta_d', 'v_edta-4', 'v_edta_d']],
			'durete'  	=> ['methode' => 'equation', 'equation' => '([c_edta] * [v_moy] / [v_eau]) * 100.09 * 1000', 'na_champs' => array('v_moy')],
			'durete_d' 	=> ['methode' => 'equation', 'equation' => '(([c_edta_d]/[c_edta]) + ([v_moy_d]/[v_moy]) + ([v_eau_d]/[v_eau])) * [durete]', 'na_champs' => array('v_moy', 'v_moy_d')],
			// 'inc_rel'  	=> ['methode' => 'equation', 'equation' => '([durete_d]/[durete]) * 100', 'na' => TRUE],
			'inc_rel'  	=> ['methode' => 'equation', 'equation' => '([durete_d]/[durete]) * 100', 'na_champs' => array('durete_d', 'durete')],
			// 'p_ecart'	=> ['methode' => 'equation', 'equation' => '(abs([v_adm] - [durete]) / [v_adm]) * 100', 'na' => TRUE],
			'p_ecart'	=> ['methode' => 'equation', 'equation' => '(abs([v_adm] - [durete]) / [v_adm]) * 100', 'na_champs' => array('v_adm', 'durete')],

			//
			// Tableau 4
			//

			'precision'	 => ['methode' => 'precision',  'm_champs' => ['inc_rel']],
			'exactitude' => ['methode' => 'exactitude', 'm_champs' => ['inc_rel', 'p_ecart', 'v_adm', 'v_adm_d', 'durete', 'durete_d']],
			'validite'	 => ['methode' => 'validite',   'm_champs' => ['precision', 'exactitude']]
		);
    }

    /* ----------------------------------------------------------------
     *
     * SN1 > Forces intermoleculaires
     *
     * ---------------------------------------------------------------- */
    public function _corr_forces_intermoleculaires_v1()
    {
        return array(

            //
            // Tableau 2
            //
            
            'dt_1'   => ['methode' => 'equation', 'equation' => 'abs([t2_1] - [t1_1])'],
            'dt_2'   => ['methode' => 'equation', 'equation' => 'abs([t2_2] - [t1_2])'],
            'dt_3'   => ['methode' => 'equation', 'equation' => 'abs([t2_3] - [t1_3])'],
            'dt_4'   => ['methode' => 'equation', 'equation' => 'abs([t2_4] - [t1_4])'],
            'dt_5'   => ['methode' => 'equation', 'equation' => 'abs([t2_5] - [t1_5])'],
            'dt_6'   => ['methode' => 'equation', 'equation' => 'abs([t2_6] - [t1_6])'],
            'dt_7'   => ['methode' => 'equation', 'equation' => 'abs([t2_7] - [t1_7])'],

            'dx_1'   => ['methode' => 'equation', 'equation' => '[x2_1] - [x1_1]'],
            'dx_2'   => ['methode' => 'equation', 'equation' => '[x2_2] - [x1_2]'],
            'dx_3'   => ['methode' => 'equation', 'equation' => '[x2_3] - [x1_3]'],
            'dx_4'   => ['methode' => 'equation', 'equation' => '[x2_4] - [x1_4]'],
            'dx_5'   => ['methode' => 'equation', 'equation' => '[x2_5] - [x1_5]'],
            'dx_6'   => ['methode' => 'equation', 'equation' => '[x2_6] - [x1_6]'],
            'dx_7'   => ['methode' => 'equation', 'equation' => '[x2_7] - [x1_7]'],

            'v_moy_1' => ['methode' => 'equation', 'equation' => '[dt_1] / [dx_1]'],
            'v_moy_2' => ['methode' => 'equation', 'equation' => '[dt_2] / [dx_2]'],
            'v_moy_3' => ['methode' => 'equation', 'equation' => '[dt_3] / [dx_3]'],
            'v_moy_4' => ['methode' => 'equation', 'equation' => '[dt_4] / [dx_4]'],
            'v_moy_5' => ['methode' => 'equation', 'equation' => '[dt_5] / [dx_5]'],
            'v_moy_6' => ['methode' => 'equation', 'equation' => '[dt_6] / [dx_6]'],
            'v_moy_7' => ['methode' => 'equation', 'equation' => '[dt_7] / [dx_7]']

        );
    }

    /* ----------------------------------------------------------------
     *
     * SN2 > Spectrophotometrie methyl-orange
     *
     * ----------------------------------------------------------------
	 *
     * Cette function sert seulement a corriger les champs necessitant
	 * des calculs lies specifiquement a ce laboratoire.
     * 
     * ---------------------------------------------------------------- */
    public function _corr_spectro_mo_v1()
    {
        return array(

            //
            // Tableau 2
            //
            
            'c_dil-1'   => ['methode' => 'equation', 'equation' => '[c_conc] * [v_conc-1] / [v_dil] * 1e6'],
            'c_dil-2'   => ['methode' => 'equation', 'equation' => '[c_conc] * [v_conc-2] / [v_dil] * 1e6'],
            'c_dil-3'   => ['methode' => 'equation', 'equation' => '[c_conc] * [v_conc-3] / [v_dil] * 1e6'],
            'c_dil-4'   => ['methode' => 'equation', 'equation' => '[c_conc] * [v_conc-4] / [v_dil] * 1e6'],
            'c_dil-5'   => ['methode' => 'equation', 'equation' => '[c_conc] * [v_conc-5] / [v_dil] * 1e6'],

            'c_dil_d-1' => ['methode' => 'equation', 'equation' => '(([c_conc_d]/[c_conc]) + ([v_dil_d]/[v_dil]) + ([v_conc_d]/[v_conc-1])) * [c_dil-1]'],
            'c_dil_d-2' => ['methode' => 'equation', 'equation' => '(([c_conc_d]/[c_conc]) + ([v_dil_d]/[v_dil]) + ([v_conc_d]/[v_conc-2])) * [c_dil-2]'],
            'c_dil_d-3' => ['methode' => 'equation', 'equation' => '(([c_conc_d]/[c_conc]) + ([v_dil_d]/[v_dil]) + ([v_conc_d]/[v_conc-3])) * [c_dil-3]'],
            'c_dil_d-4' => ['methode' => 'equation', 'equation' => '(([c_conc_d]/[c_conc]) + ([v_dil_d]/[v_dil]) + ([v_conc_d]/[v_conc-4])) * [c_dil-4]'],
            'c_dil_d-5' => ['methode' => 'equation', 'equation' => '(([c_conc_d]/[c_conc]) + ([v_dil_d]/[v_dil]) + ([v_conc_d]/[v_conc-5])) * [c_dil-5]'],

            //
            // Tableau 4
            //

            'c_exp'     => ['methode' => 'equation', 'equation' => '([a_absorb] - [droite_b]) / [droite_m]'],
            'inc_rel'   => ['methode' => 'equation', 'equation' => '[c_exp_d] / [c_exp] * 100'],
            'p_ecart'   => ['methode' => 'equation', 'equation' => 'abs(((([c_exp]) - [c_adm]) / [c_adm])) * 100'],

            //
			// Tableau 5
			//

			'precision'	 => ['methode' => 'precision',  'm_champs' => ['inc_rel']],
			'exactitude' => ['methode' => 'exactitude', 'm_champs' => ['inc_rel', 'p_ecart', 'c_adm', 'c_adm_d', 'c_exp', 'c_exp_d']],
			'validite'	 => ['methode' => 'validite',   'm_champs' => ['precision', 'exactitude']]
        );
    }

    /* ----------------------------------------------------------------
     *
     * SN2 > Cryoscopie
     *
     * ---------------------------------------------------------------- */
    public function _corr_cryo_v1()
    {
        return array(

            //
            // Tableau 2
            //
            
            'eau'       => ['methode' => 'equation', 'equation' => '[becher_sel_eau] - [becher_sel]'],
            'masse_c_d' => ['methode' => 'equation', 'equation' => '[masse_p_d] + [masse_p_d]'],
            'sel'       => ['methode' => 'equation', 'equation' => '[becher_sel] - [becher]'],

            //
            // Tableau 3
            //

            'd_tcong'   => ['methode' => 'equation', 'equation' => 'abs([tcong_sln] - [tcong_eau])'],
            'd_tcong_d' => ['methode' => 'equation', 'equation' => '[tcong_eau_d] + [tcong_sln_d]'],
            'b_exp'     => ['methode' => 'equation', 'equation' => 'abs([d_tcong]) / (1.86*2)'],
            'mm_exp'    => ['methode' => 'equation', 'equation' => '[sel] / ((abs([d_tcong]) * ([eau]/1000)) / (1.86*2))'],
            'p_ecart'   => ['methode' => 'equation', 'equation' => 'abs([mm_ref] - [mm_exp])/[mm_ref] *100']
        );
    }

    /* ----------------------------------------------------------------
     *
     * SN2 > Cinetique
     *
     * ---------------------------------------------------------------- */
    public function _corr_cinetique_v1()
    {
        return array(

            //
            // Tableau 1
            //
            
            'dv_calc_piece' => ['methode' => 'equation', 'equation' => 'ln(2) / [k_piece]'],
            'dv_calc_bain'  => ['methode' => 'equation', 'equation' => 'ln(2) / [k_bain]'],
            'energie_activation' => ['methode' => 'equation', 'equation' => '(-0.0083145*(ln([k_bain]/[k_piece])))/((1/([temperature_bain] + 273.15)) - (1/([temperature_piece] + 273.15)))']
        );
    }

    /* ----------------------------------------------------------------
     *
     * SN2 > Equilibres
     *
     * ----------------------------------------------------------------
     *
     * na = nombres arrondis (valeurs arrondies)
     *
     * ---------------------------------------------------------------- */
    public function _corr_equilibres_v1()
    {
        return array(

            //
            // Tableau 1
            //

            'fescn_etalon_1_conc' => ['methode' => 'equation', 'equation' => '[kscn_mere_vol_1] / [fescn_etalons_vol] * [kscn_mere_conc] * 1e4'],
            'fescn_etalon_2_conc' => ['methode' => 'equation', 'equation' => '[kscn_mere_vol_2] / [fescn_etalons_vol] * [kscn_mere_conc] * 1e4'],
            'fescn_etalon_3_conc' => ['methode' => 'equation', 'equation' => '[kscn_mere_vol_3] / [fescn_etalons_vol] * [kscn_mere_conc] * 1e4'],
            'fescn_etalon_4_conc' => ['methode' => 'equation', 'equation' => '[kscn_mere_vol_4] / [fescn_etalons_vol] * [kscn_mere_conc] * 1e4'],
            'fescn_etalon_5_conc' => ['methode' => 'equation', 'equation' => '[kscn_mere_vol_5] / [fescn_etalons_vol] * [kscn_mere_conc] * 1e4'],

            //
            // Tableau 3
            //

            'fecl3_diluee_conc' => ['methode' => 'equation', 'equation' => '[fecl3_mere_vol] * [fecl3_mere_conc] / [fecl3_diluee_vol] * 1e3'],

            //
            // Tableau 4
            //

            'fe_initial_conc'  => ['methode' => 'equation', 'equation' => '[fecl3_diluee_conc] / 2', 'na_champs' => array('fecl3_diluee_conc')],
            'scn_initial_conc' => ['methode' => 'equation', 'equation' => '[kscn_mere_conc] / 2 * 1e4'],
            'fescn_eq_conc'    => ['methode' => 'equation', 'equation' => '([absorbance] - [droite_b]) / [droite_m]'],
            'fe_eq_conc'       => ['methode' => 'equation', 'equation' => '([fe_initial_conc] * 1e-3 - [fescn_eq_conc] * 1e-4) * 1e3'],
            'scn_eq_conc'      => ['methode' => 'equation', 'equation' => '([scn_initial_conc] * 1e-4 - [fescn_eq_conc] * 1e-4) * 1e4'],

            //
            // Tableau 5
            //

            'kc'      => ['methode' => 'equation', 'equation' => '[fescn_eq_conc] * 1e-4 / ([fe_eq_conc] * 1e-3 * [scn_eq_conc] * 1e-4)'],
            'p_ecart' => ['methode' => 'equation', 'equation' => 'abs([kc] - [kc_classe])/[kc_classe] * 100', 'na' => TRUE]
        );
    }

    /* ----------------------------------------------------------------
     *
     * SN2 > Titrage AB
     *
     * ---------------------------------------------------------------- */
    public function _corr_titrage_v1()
    {
        return array(

            //
            // Tableau 1
            //

            'vol_moy'         => ['methode' => 'equation', 'equation' => '([vol_naoh_1] + [vol_naoh_2] + [vol_naoh_3])/3'],
            'vol_moy_d'       => ['methode' => 'extremes', 'm_champs' => ['vol_naoh_1', 'vol_naoh_d', 'vol_naoh_2', 'vol_naoh_d', 'vol_naoh_3', 'vol_naoh_d']],
            'conc_inconnu'    => ['methode' => 'equation', 'equation' => '([vol_moy] * [conc_naoh]) / [vol_titre]', 'na_champs' => ['vol_moy']], 
            'conc_inconnu_d'  => ['methode' => 'equation', 'equation' => '(([vol_moy_d]/[vol_moy]) + ([conc_naoh_d]/[conc_naoh]) + ([vol_titre_d]/[vol_titre])) * [conc_inconnu]', 'na_champs' => ['vol_moy', 'vol_moy_d']]
        );
    }

    /* ----------------------------------------------------------------
     *
     * SN2 > Preparation 1
     *
     * ---------------------------------------------------------------- */
    public function _corr_preparation1_v1()
    {
        return array(

            //
            // Tableau 2
            //

            'naoh_p'    => ['methode' => 'equation', 'equation' => '[naoh_masse_solution]/[naoh_volume_solution]'],
            'naoh_p_d'  => ['methode' => 'equation', 'equation' => '(([masse_solution_d]/[naoh_masse_solution]) + ([volume_solution_d]/[naoh_volume_solution]))*[naoh_p]'],
			'naoh_masse_solution' => ['methode' => 'equation', 'equation' => '[naoh_fiole_pleine] - [naoh_fiole_vide]'],

            'nh4cl_p'    => ['methode' => 'equation', 'equation' => '[nh4cl_masse_solution]/[nh4cl_volume_solution]'],
            'nh4cl_p_d'  => ['methode' => 'equation', 'equation' => '(([masse_solution_d]/[nh4cl_masse_solution]) + ([volume_solution_d]/[nh4cl_volume_solution]))*[nh4cl_p]'],
			'nh4cl_masse_solution' => ['methode' => 'equation', 'equation' => '[nh4cl_fiole_pleine] - [nh4cl_fiole_vide]']
        );
    }

}
