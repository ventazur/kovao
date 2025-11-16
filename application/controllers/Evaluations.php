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
 * EVALUATIONS
 *
 * ============================================================================ */

class Evaluations extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }
	}

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
    public function _remap($methode, $args = array())
    {
        $lister = array('cours', 'archives', 'groupe');

        if (in_array($methode, $lister))
        {
            $this->liste();
            return;
        }
        else
        {
			if (method_exists($this, $methode)) 
			{
            	call_user_func_array(array($this, $methode), $args);
        	} 
			else 
			{
				show_404();
        	}
        }

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        $this->liste();
        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Liste de toutes les evaluations d'un enseignant ou d'un groupe
     *
     * ------------------------------------------------------------------------ */
	public function liste()
    {
        $this->data['current_method'] = __FUNCTION__;

        $arguments = $this->uri->segment_array();

        $cours_raw = $this->Cours_model->lister_cours(
            array(
                'groupe_id' => $this->groupe_id   
            )
        );

        $view = NULL;

        //
        // Archives
        //

        if (in_array('archives', $arguments))
        {
            //
            // Archives du groupe
            //

            if (in_array('groupe', $arguments))
            {
                $evaluations = $this->Evaluation_model->lister_evaluations(
                    array(
                        'public'        => TRUE,
                        'archives'      => TRUE,
                        'actif'         => FALSE
                    )
                );

                $this->data['enseignants'] = $this->Enseignant_model->lister_enseignants(array('groupe_id' => $this->groupe_id));

                $view = 'liste-archives';
            }

            //
            // Mes archives
            //
           
            else
            {
                $evaluations = $this->Evaluation_model->lister_evaluations(
                    array(
                        'enseignant_id' => $this->enseignant['enseignant_id'],
                        'archives'      => TRUE,
                        'actif'         => FALSE
                    )
                );

                $view = 'liste-mes-archives';
            }
        }

        //
        // Evaluations
        //

        else 
        {
            //
            // Evaluations du groupe
            //

            if (in_array('groupe', $arguments))
            {
                // 
                // Il n'y pas d'evaluations de groupe pour le groupe Personnel.
                //

                if ($this->groupe_id == 0)
                {
                    redirect(base_url());
                    return;
                }

                $evaluations = $this->Evaluation_model->lister_evaluations(
                    array(
                        'public' => (in_array('groupe', $arguments) ? TRUE : FALSE), // public = groupe
                        'actif'  => FALSE
                    )
                );

                $this->data['enseignants'] = $this->Enseignant_model->lister_enseignants(array('groupe_id' => $this->groupe_id));

                $view = 'liste-evaluations';
            }

            //
            // Mes evaluations
            //

            else
            {
                $evaluations = $this->Evaluation_model->lister_evaluations(
                    array(
                        'enseignant_id' => $this->enseignant['enseignant_id'],
                        'actif'         => FALSE
                    )
                );

                $view = 'liste-mes-evaluations';
            }
        }

        //
        // Cours avec au moins une evaluation existante
        // (array de cours_id)
        //

        $cours_evaluations_existent = array();

        foreach($evaluations as $e)
        {
            if ( ! in_array($e['cours_id'], $cours_evaluations_existent))
                $cours_evaluations_existent[] = $e['cours_id'];
        }

        //
        // Prepare l'affichage
        //

        $this->data = array_merge(
            array(
                'cours_raw' => $cours_raw,
                'evaluations' => $evaluations,
                'cours_evaluations_existent' => $cours_evaluations_existent
            ), $this->data
        );

        $this->_affichage($view);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Editeur d'evaluation (pour les enseignants)
	 *
     * ------------------------------------------------------------------------ 
     *
     * Version 4 : Ajout des laboratoires (2024/07/31)
     *
     * ------------------------------------------------------------------------ */
	public function editeur($evaluation_id = NULL)
    {
        $this->data['current_method'] = strtolower(__FUNCTION__);

        //
        // Verifier que l'evaluation_id est sepcifie.
        //

        if ($evaluation_id == NULL)
		{
            redirect(base_url());
            exit;
		}

        //
        // Extraire l'evaluation
        //

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

		if (empty($evaluation))
		{
			redirect(base_url());
			exit;
        }

        //
        // Permissions
        //

        // Ceci empeche les enseignants d'aller voir les evaluations privees des autres enseignants,
        // tout en permettant a l'admin et les developpeurs la consultation,
        // ce qui facilite le travail de debuggage lorsqu'un enseignant se plaint d'un probleme avec son evaluation.

        if ($this->enseignant['privilege'] < 90)
        {

            // Les enseignants ne peuvent acceder les evaluations des autres groupes.
            if ($evaluation['groupe_id'] != $this->groupe_id)
            {
                redirect(base_url());
                exit;
            }

            // Les enseignants ne peuvent acceder les evaluations privees des autres enseignants, 
            // mais ils peuvent pour les evaluations du departement.
            if ( ! $evaluation['public'] && ($evaluation['enseignant_id'] != $this->enseignant_id))
            {
                redirect(base_url());
                exit;
            }
        }

        //
        // Groupes : Lister les groupes de l'enseignant
        //

        $groupes = $this->Groupe_model->lister_groupes2();

        //
        // Cours
        //

        $cours         = $this->Cours_model->extraire_cours(array('cours_id' => $evaluation['cours_id']));
        $cours_tous    = $this->Cours_model->lister_cours(); // Tous les cours de l'enseignant (de tous les groupes)

        //
        // Determiner si "Copier" pour un BLOC devrait etre affiche ou non.
        //

        $cours_avec_evaluation = $this->Cours_model->lister_cours_avec_evaluation(
            array(
                'evaluation_id' => $evaluation_id,
                'sans_cadenas'  => 1
            )
        );

        //
        // Determiner si "Copier" pour une EVALUATION devrait etre affiche ou non.
        //

        // @TODO 

        //
        // Determiner les cours cibles (destination) pour la copie d'une evaluation
        //

        $groupes_copy = array();

        foreach($cours_tous as $c)
        {
            if ($c['cours_id'] == $evaluation['cours_id'])
                continue;

            if (in_array($c['groupe_id'], $groupes_copy))
                continue;

            $groupes_copy[] = $c['groupe_id'];
        }

        // Rendre le groupe actuel le groupe par default lorsqu'il est possible de copier une evaluation a un cours de ce groupe.
        // Ceci signifie placer le groupe actuel a l'index 0.

        if (in_array($this->groupe_id, $groupes_copy))
        {
            array_unshift($groupes_copy, $this->groupe_id);
            $groupes_copy = array_unique($groupes_copy);
        }

        //
        // Blocs
        // 

        $blocs = $this->Question_model->extraire_blocs($evaluation_id);

        $bloc_labels = array();
        $nb_questions_dans_blocs = array();

        if ( ! empty($blocs))
        {
            $bloc_labels = array_keys(array_keys_swap($blocs, 'bloc_label'));
            $nb_questions_dans_blocs = $this->Question_model->nb_questions_dans_blocs(array_keys($blocs));
        }

        //
        // Laboratoires
        //

        $lab            = $evaluation['lab'];
        $lab_parametres = ! empty($evaluation['lab_parametres']) ? json_decode($evaluation['lab_parametres'], TRUE) : array();
        $lab_valeurs    = ! empty($evaluation['lab_valeurs']) ? json_decode($evaluation['lab_valeurs'], TRUE) : array();
        $lab_points     = ! empty($evaluation['lab_points']) ? json_decode($evaluation['lab_points'], TRUE) : array();
        $lab_prefix     = $evaluation['lab_prefix'];
        $lab_vue        = $evaluation['lab_vue'];

		//
		// Complementer/Fixer lab_valeurs avec informations contenus dans lab_points
		//
	
		$lab_valeurs = complementer_lab_valeurs($lab_valeurs, $lab_points);

		// 
		// Organiser les points des champs du laboratoire en ordre de numero de tableau
		//

		$lab_points_tableaux = array();
		$lab_points_total    = 0;

		if ( ! empty($lab_points))
		{
			foreach($lab_points as $c => $c_arr)
			{
				$no_tableau = $c_arr['tableau'];

				if ( ! array_key_exists($no_tableau, $lab_points_tableaux))
					$lab_points_tableaux[$no_tableau] = array();

				$lab_points_tableaux[$no_tableau][$c] = $c_arr;

				$lab_points_total += $c_arr['points'];
			}

			foreach($lab_points_tableaux as &$s_arr)
			{
				ksort($s_arr);
			}
		}
	
        //
        // Questions
        //

        $questions = $this->Question_model->lister_questions($evaluation_id);

		$question_ids = array_keys($questions);

        //
        // Reponses (de toutes les questions)
        //

        $reponses_toutes = $this->Reponse_model->lister_reponses_toutes($question_ids);

        //
        // Grilles de correction
        //

        $grilles_correction = $this->Question_model->extraire_grilles_correction($question_ids);

        //
        // Extraire les variables de l'evaluation
        //

        $variables_presentes = $this->Evaluation_model->extraire_variables($evaluation_id);
        $variables_generees  = array();

        //
        // Generer des variables pour les tests
        //

        if ( ! empty($variables_presentes))
        {
            $variables_generees = determiner_valeurs_variables($variables_presentes);
        }

		//
		// Images associes aux questions
		//
	
		$images = array();		

		if ( ! empty($question_ids))
		{
			$images = $this->Document_model->extraire_images($question_ids);
		}

        // Permissions
        // +
        // Reponses 
        // + 
        // Determiner le pointage totale de l'evaluation
        // (sans additionner les questions incluses dans des blocs)
        // +
        // Determiner les variables par question (pour la navigation)
        //

        $permissions_questions = array();

        $nb_questions_reel = 0; // Le nombre de questions reel (a remplir par l'etudiant) pour l'evaluation

        $reponses   = array();
        $tolerances = array();
        $similarite = array();

		//
		// Pointage de l'evaluation
		//

        $pointage = 0;

		if ($lab)
		{
			$pointage += $lab_points_total;
		}

        $variables_par_question_id = array();

        foreach($questions as $question_id => $q)
        {
            //
            // Si le texte de la question est en JSON, il faut le convertir.
            //

            $questions[$question_id]['question_texte'] = json_decode($q['question_texte']) ?: $q['question_texte'];

            // 
            // Verifier la permission
            //

            // Ces renseignements sont necessaires pour les permissions des questions.
            $q['enseignant_id'] = $evaluation['enseignant_id'];
            $q['public']        = $evaluation['public'];

            $permissions_questions[$question_id] = $this->Question_model->permissions_question($question_id, $q);

            $reponses[$question_id] = array();
            
            if (array_key_exists($question_id, $reponses_toutes) && ! empty($reponses_toutes[$question_id]))
            {
                $reponses[$question_id] = $reponses_toutes[$question_id];
            }

			//
            // if ( ! empty($q['bloc_id'])
            //     continue; 
			//

            if ($q['actif'] && ! $q['sondage']) 
            {
                // Ne pas compter les points des questions sans reponse.
				//
                // (Je ne suis plus certain que cela soit pertinent suite a une
                // verification d'integrite plus approfondie des evaluations lors
                // de la previsualisation. (2019/02/01))
                //
                // if (empty($reponses[$question_id]) && $q['question_type'] != 2) 
                //    continue;

                // Le pointage des questions comportant des blocs est calcule plus loin.
                if (empty($q['bloc_id']))
                {
                    $pointage = $pointage + $q['question_points'];
                    $nb_questions_reel++;
                }
            }

            //
            // Determiner les variables presentes
            // dans l'enonce de la question et dans les reponses
            //

			$variables_trouvees = array();

            //
            // Extraire les variables de l'enonce de la question
            //

			if (preg_match_all('/<var>(.+?)<\/var>/', $q['question_texte'], $matches))
			{
          		// La question comporte des variables dans l'enonce.
  
 				$variables_trouvees = array_merge($variables_trouvees, $matches[1]);
			}

            //
            // Extraire les variables des reponses pour les questions suivantes :
            // 
            // Question a choix unique par equations (TYPE 3)
            // Question a reponse numerique par equation (TYPE 9)
            //

			if (in_array($q['question_type'], array(3, 9)))
            {
                //
				// Extraire les variables des equations
                //

				foreach($reponses[$question_id] as $r)
				{
					if (preg_match_all('/([ABCDFGHIJKLMNOPQRSTUVWXYZ]{1})/', $r['reponse_texte'], $matches))
					{
						// L'equation comporte une ou plusieurs variables

						$variables_trouvees = array_merge($variables_trouvees, $matches[1]);
					}
				}	
			}
			else
            {
                //
				// Extraire les variables des reponses pour les autres types de questions
                //

				foreach($reponses[$question_id] as $r)
				{
					if (preg_match_all('/<var>(.+?)<\/var>/', $r['reponse_texte'], $matches))
					{
						// La question comporte des variables dans l'enonce.
		  
						$variables_trouvees = array_merge($variables_trouvees, $matches[1]);
					}
				}
			} // else

			if ( ! empty($variables_trouvees))
			{
				// Dedoublonner le tableau des variables et les placer en ordre alpha.
			
				$variables_trouvees = array_unique($variables_trouvees);

				sort($variables_trouvees);

				$variables_par_question_id[$question_id] = implode('', $variables_trouvees);
            }

            //
            // Extraire les tolerances
            //
            // - Question a reponse numerique (TYPE 6)
            // - Question a reponse numerique par equation (TYPE 9)
            //

            if (in_array($q['question_type'], array(6, 9)))
            {
                $tolerances[$question_id] = $this->Question_model->extraire_tolerances($question_id);
            }

            //
            // Extraire la similarite
            //

            if ($q['question_type'] == 7)
            {
                $similarite[$question_id] = $this->Question_model->extraire_similarite($question_id);
            }

        } // foreach $questions

        foreach($blocs as $b)
        {
            $pointage = $pointage + ($b['bloc_points'] * $b['bloc_nb_questions']);
            $nb_questions_reel = $nb_questions_reel + $b['bloc_nb_questions'];
        }

        //
        // Verifier si cette evaluation de l'enseignant est presentement selectionnee pour les etudiants
        //

        $evaluation_a_remplir = FALSE;

        if ( ! $evaluation['public'])
        {
            $evaluations_selectionnees = $this->Evaluation_model->lister_evaluations_selectionnees($this->enseignant_id, $this->semestre_id);

            if ( ! empty($evaluations_selectionnees) && array_key_exists($evaluation_id, $evaluations_selectionnees))
            {
                $evaluation_a_remplir = TRUE;
            }
        }

        //
        // Preparer l'affichage
        //

        $this->data = array_merge(
            array(
                'evaluation_id'             => $evaluation_id,
                'evaluation'                => $evaluation,
                'permissions'               => $this->Evaluation_model->permissions_evaluation($evaluation_id, $evaluation),
                'groupes'                   => $groupes,
                'groupes_copy'              => $groupes_copy, // Les groupes disponibles avec un cours de destination existant pour la copie d'une evaluation.
                'cours'                     => $cours,        // Le cours actuel
                'cours_tous'                => $cours_tous,   // Liste de tous les cours de l'enseignant
                'cours_avec_evaluation'     => $cours_avec_evaluation,
                'blocs'                     => $blocs,
                'bloc_labels'               => $bloc_labels,
                'nb_questions_dans_blocs'   => $nb_questions_dans_blocs, // index: bloc_id
                'nb_questions_reel'         => $nb_questions_reel,
                'lab'                       => $lab,
                'lab_parametres'            => $lab_parametres,
                'lab_valeurs'               => $lab_valeurs,
				'lab_points'				=> $lab_points,
				'lab_points_tableaux'		=> $lab_points_tableaux,
                'lab_prefix'                => $lab_prefix,
                'lab_vue'                   => $lab_vue,
                'questions'                 => $questions,
                'permissions_questions'     => $permissions_questions,
                'images'                    => $images,
                'reponses'                  => $reponses,
                'gc'                        => $grilles_correction,
                'tolerances'                => $tolerances,
                'similarite'                => $similarite,
                'pointage'                  => $pointage,
                'variables_presentes'       => $variables_presentes,
                'variables_generees'        => $variables_generees,
                'variables_par_question_id' => $variables_par_question_id,
                'evaluation_a_remplir'      => $evaluation_a_remplir
            ), $this->data
        );

        $this->_affichage('editeur');
	}

    /* ------------------------------------------------------------------------
     *
     * Filtre pour les etudiants
	 *
     * ------------------------------------------------------------------------ */
	public function filtres($evaluation_reference = NULL)
    {
        if ($this->enseignant_id != 1)
        {
            redirect(base_url());
            die;
        }

        if (empty($evaluation_reference) || ! ctype_alpha($evaluation_reference) || strlen($evaluation_reference) != 6)
        {
            redirect(base_url());
            exit;
        } 

        $evaluation = $this->Evaluation_model->extraire_evaluation_par_reference($evaluation_reference);

        if (empty($evaluation))
        {
            redirect(base_url());
            exit;
        }

        if ($this->enseignant['privilege'] < 89 && ($this->enseignant_id != $evaluation['enseignant_id']))
        {
            redirect(base_url());
            exit;
        }
        
        $this->data['evaluation_reference'] = $evaluation_reference;
        $this->data['evaluation'] = $evaluation;
        $this->data['cours_id']   = $evaluation['cours_id'];
        $this->data['eleves']     = $this->Cours_model->lister_eleves($this->semestre_id, array('cours_ids', array($evaluation['cours_id'])));
        $this->data['cours']      = $this->Cours_model->extraire_cours(array('cours_id' => $evaluation['cours_id']));

        $this->_affichage('filtres');
    }

    /* ------------------------------------------------------------------------
     *
     * Exporter une evaluation en format JSON
	 *
     * ------------------------------------------------------------------------ */
	public function exporter_json()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if (($r = $this->Evaluation_model->exporter_json($post_data['evaluation_id'])) !== TRUE)
        {
			echo json_encode($r);
			return;
		}

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Mettre une evaluation en ligne
	 *
     * ------------------------------------------------------------------------ */
	public function mettre_en_ligne()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if (($r = $this->Soumission_model->mettre_en_ligne($post_data['evaluation_id'])) !== TRUE)
        {
			echo json_encode($r);
			return;
		}

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Mettre une evaluation hors ligne
	 *
     * ------------------------------------------------------------------------ */
	public function mettre_hors_ligne()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

		if ( ! array_key_exists('evaluation_reference', $post_data) && ! ctype_alpha($post_data['evaluation_reference']))
        {
            echo json_encode(
				array(
					'status'     => FALSE,
					'error_code' => 2,
					'error_msg'  => "Argument (evaluation_reference) invalide"
				)
			);
            return;
        }

        $enregistrer_evaluation = TRUE;

        if (array_key_exists('enregistrer', $post_data) && $post_data['enregistrer'] != 1)
        {
            $enregistrer_evaluation = FALSE;
        }

        $r = $this->Soumission_model->mettre_hors_ligne(
            $post_data['evaluation_reference'], 
            array(
                'etudiant_id'            => $post_data['etudiant_id'] ?? NULL,
                'enregistrer_evaluation' => $enregistrer_evaluation
            )
        );

        if ($r !== TRUE)
        {
            echo json_encode($r);
            return;
        }

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Version imprimable (sommaire)
	 *
     * ------------------------------------------------------------------------ */
	public function sommaire($evaluation_id = NULL)
    {
        $this->data['current_method'] = strtolower(__FUNCTION__);

        //
        // Verifier que les criteres d'accessibilite minimum soient atteints pour cette fonction.
        //

        if ( ! $this->logged_in)
		{
            redirect(base_url());
            exit;
		}

        if ($evaluation_id == NULL)
		{
            redirect(base_url());
            exit;
		}

        //
        // Extraire l'evaluation
        //

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

		if (empty($evaluation))
		{
			redirect(base_url());
			exit;
        }

        //
        // Permissions
        //

        // Ceci empeche les enseignants d'aller voir les evaluations privees des autres enseignants,
        // tout en permettant a l'admin et les developpeurs la consultation,
        // ce qui facilite le travail de debuggage lorsqu'un enseignant se plaint d'un probleme avec son evaluation.

        if ( ! permis('admin'))
        {
            // Les enseignants ne peuvent acceder les evaluations des autres groupes.
            if ($evaluation['groupe_id'] != $this->groupe_id)
            {
                redirect(base_url());
                exit;
            }

            // Les enseignants ne peuvent acceder les evaluations privees des autres enseignants, 
            // mais ils peuvent pour les evaluations du departement.
            if ( ! $evaluation['public'] && ($evaluation['enseignant_id'] != $this->enseignant_id))
            {
                redirect(base_url());
                exit;
            }
        }

        //
        // Cours
        //

        $cours = $this->Cours_model->extraire_cours(array('cours_id' => $evaluation['cours_id']));

        //
        // Blocs
        // 

        $blocs = $this->Question_model->extraire_blocs($evaluation_id);

        $bloc_labels = array();
        $nb_questions_dans_blocs = array();

        if ( ! empty($blocs))
        {
            $bloc_labels = array_keys(array_keys_swap($blocs, 'bloc_label'));
            $nb_questions_dans_blocs = $this->Question_model->nb_questions_dans_blocs(array_keys($blocs));
        }

        //
        // Questions
        //

        $questions = $this->Question_model->lister_questions($evaluation_id);

		$question_ids = array_keys($questions);

        //
        // Reponses (de toutes les questions)
        //

        $reponses_toutes = $this->Reponse_model->lister_reponses_toutes($question_ids);
        
        //
        // Extraire les variables de l'evaluation
        //

        $variables_presentes = $this->Evaluation_model->extraire_variables($evaluation_id);

		//
		// Images associes aux questions
		//
	
		$images = array();		

		if ( ! empty($question_ids))
		{
			$images = $this->Document_model->extraire_images($question_ids);
		}

        // Permissions
        // +
        // Reponses 
        // + 
        // Determiner le pointage totale de l'evaluation
        // (sans additionner les questions incluses dans des blocs)
        // +
        // Determiner les variables par question (pour la navigation)
        //

        $permissions_questions = array();

        $reponses   = array();
        $tolerances = array();

        $pointage = 0;

        $variables_par_question_id = array();

        foreach($questions as $question_id => $q)
        {
            // Ces renseignants sont necessaires pour les permissions des questions.
            $q['enseignant_id'] = $evaluation['enseignant_id'];
            $q['public']        = $evaluation['public'];

            $permissions_questions[$question_id] = $this->Question_model->permissions_question($question_id, $q);

			$variables_trouvees = array();

            $reponses[$question_id] = array();
            
            if (array_key_exists($question_id, $reponses_toutes) && ! empty($reponses_toutes[$question_id]))
            {
                $reponses[$question_id] = $reponses_toutes[$question_id];
            }

			//
            // if ( ! empty($q['bloc_id'])
            //     continue; 
			//

            if ($q['actif']) 
            {
                // Ne pas compter les points des questions sans reponse.
				//
                // (Je ne suis plus certain que cela soit pertinent suite a une
                // verification d'integrite plus approfondie des evaluations lors
                // de la previsualisation. (2019/02/01))
                //
                // if (empty($reponses[$question_id]) && $q['question_type'] != 2) 
                //    continue;

                // Le pointage des questions comportant des blocs est calcule plus loin.
                if (empty($q['bloc_id']))
                {
                    $pointage = $pointage + $q['question_points'];
                }
            }

            // Extraire les variables de l'enonce
            
			if (preg_match_all('/<var>(.+?)<\/var>/', $q['question_texte'], $matches))
			{
          		// La question comporte des variables dans l'enonce.
  
 				$variables_trouvees = array_merge($variables_trouvees, $matches[1]);
			}
		
			// Extraire les variables des reponses

			if ($q['question_type'] == 3)
			{
				// Extraire les variables des equations pour une question a coefficients variables

				foreach($reponses[$question_id] as $r)
				{
					if (preg_match_all('/([ABCDFGHIJKLMNOPQRSTUVWXYZ]{1})/', $r['reponse_texte'], $matches))
					{
						// L'equation comporte un ou plusieurs variables

						$variables_trouvees = array_merge($variables_trouvees, $matches[1]);
					}
				}	
			}
			else
			{
				// Extraire les variables du texte des reponses pour les autres questions
			
				foreach($reponses[$question_id] as $r)
				{
					if (preg_match_all('/<var>(.+?)<\/var>/', $r['reponse_texte'], $matches))
					{
						// La question comporte des variables dans l'enonce.
		  
						$variables_trouvees = array_merge($variables_trouvees, $matches[1]);
					}
				}
			} // else

			if ( ! empty($variables_trouvees))
			{
				// Dedoublonner le tableau des variables et les placer en ordre alpha.
			
				$variables_trouvees = array_unique($variables_trouvees);

				sort($variables_trouvees);

				$variables_par_question_id[$question_id] = implode('', $variables_trouvees);
            }

            // Extraire les tolerances
        
            if ($q['question_type']  == 6)
            {
                $tolerances[$question_id] = $this->Question_model->extraire_tolerances($question_id);
            }
        } // foreach $questions

        foreach($blocs as $b)
        {
            $pointage = $pointage + ($b['bloc_points'] * $b['bloc_nb_questions']);
        }

        //
        // Preparer l'affichage
        //

        $this->data = array_merge(
            array(
                'evaluation_id' => $evaluation_id,
                'evaluation'    => $evaluation,
                'permissions'   => $this->Evaluation_model->permissions_evaluation($evaluation_id, $evaluation),
                'cours'         => $cours,
                'blocs'         => $blocs,
                'bloc_labels'   => $bloc_labels,
                'nb_questions_dans_blocs' => $nb_questions_dans_blocs, // index: bloc_id
                'questions'     => $questions,
                'permissions_questions' => $permissions_questions,
                'images'        => $images,
                'reponses'      => $reponses,
                'tolerances'    => $tolerances,
                'pointage'      => $pointage,
                'variables_presentes' => $variables_presentes,
				'variables_par_question_id' => $variables_par_question_id
            ), $this->data
        );

        $this->_affichage('sommaire');
	}

    /* ------------------------------------------------------------------------
     *
     * Creer une evaluation (pour les enseignants)
     *
     * ------------------------------------------------------------------------ */
    public function creer()
    {
        if ( ! $this->logged_in)
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire tous les cours du groupe
        //

        $cours = $this->Cours_model->lister_cours(array('groupe_id' => $this->groupe_id));

        if (empty($cours))
        {
            $this->_affichage('creer-aucun-cours');
            return;
        }

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //
        // Definition des messages d'erreur
        //

		$errors = array();
		$errors = array(
			'evaluation_titre' => null
		);

        $this->form_validation->set_rules('evaluation_titre', 'Titre', 'required');
        $this->form_validation->set_rules('evaluation_cours_id', 'Cours_ID', 'required');
        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
		// Validation du formulaire (form)
		// 

        $this->data['errors'] = $errors;

       	if ($this->form_validation->run() == FALSE)
        {
			//
			// Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.
			//

			if ($this->form_validation->error('evaluation_titre') !== '')
			{
				$this->data['errors']['evaluation_titre'] = 'is-invalid'; // pour bootstrap
			}
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			// Verification de l'autorisation a se connecter.
			//

			$post_data = $this->input->post(NULL, TRUE);

            $evaluation_id = $this->Evaluation_model->creer_evaluation($post_data);

            if ($evaluation_id != FALSE)
            {
                redirect(base_url() . 'evaluations/editeur/' . $evaluation_id);
                return;
            }
        }

        $this->data = array_merge(
            array(
                'cours_raw' => $cours
            ), $this->data
        );

        $this->_affichage('creer');
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer responsable : Lister les enseignants
     *
     * ------------------------------------------------------------------------ */
    public function lister_enseignants()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
		{
            echo json_encode(FALSE);
            return;
        }

        $enseignants = $this->Enseignant_model->lister_enseignants(
            array(
                'groupe_id' => $this->groupe_id,
                'actif'     => 1   
            )
        );

        if (empty($enseignants))
        {
            echo json_encode(FALSE);
            return FALSE;
        }

        $evaluation = $this->Evaluation_model->extraire_evaluation($post_data['evaluation_id']);

        if (empty($evaluation))
        {
            echo json_encode(FALSE);
            return FALSE;
        }

        //
        // preparer le html
        //

        $html = '';

        foreach($enseignants as $e)
        {
            if ($e['enseignant_id'] == $evaluation['enseignant_id']) continue;

            $html .= '<option value="' . $e['enseignant_id'] . '">' . $e['prenom'] . ' ' . $e['nom'] . '</option>';
        }

        echo json_encode($html);
        return;
    }

    /* --------------------------------------------------------------------------
     *
     * (AJAX) Effacer reponses non selectionnees d'une question a choix multiples
     *
     * -------------------------------------------------------------------------- */
    public function effacer_reponses_non_selectionnees()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Reponse_model->effacer_reponses_non_selectionnees($post_data['evaluation_id'], $post_data['question_id'], $post_data['reponse_ids']) !== FALSE)
		{
			echo json_encode(TRUE);
			return;
        }
		
		echo json_encode(FALSE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer le responsable de cette evaluation
     *
     * ------------------------------------------------------------------------ */
    public function changer_responsable()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'enseignant_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }


		if ($this->Evaluation_model->changer_responsable($post_data['evaluation_id'], $post_data['enseignant_id']) !== FALSE)
		{
			echo json_encode(TRUE);
			return;
		}
		
		echo json_encode(FALSE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Lister cours
     *
     * ------------------------------------------------------------------------ */
    public function lister_cours()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post()) === FALSE)
		{
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('cours_id', $post_data) || ! array_key_exists('groupe_id', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        $cours_raw = $this->Cours_model->lister_cours(
            array(
                'groupe_id' => $post_data['groupe_id']   
            )
        );

        // Enlever le cours actuel comme destination cible de la copie.

        if (array_key_exists($post_data['cours_id'], $cours_raw))
        {
            unset($cours_raw[$post_data['cours_id']]);
        }

        // Preparer le html

        if (empty($cours_raw))
        {
            echo json_encode(FALSE);
            return FALSE;
        }

        $html = '';

        foreach($cours_raw as $c)
        {
            if ($c['cours_id'] == $post_data['cours_id'])
                continue;

            $html .= '<option value="' . $c['cours_id'] . '">' . $c['cours_nom'] . ' (' . $c['cours_code_court'] . ')</option>';
        }

        echo json_encode($html);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Copier une evaluation vers un autre cours
     *
     * ------------------------------------------------------------------------ */
    public function dupliquer_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// Dupliquer l'evaluation
		//

        $result = $this->Evaluation_model->importer_exporter_copier_evaluation($post_data['evaluation_id'], $post_data['cours_id']);

        // result:
        //  - array signifie une erreur
        //  - numeric signifie le nouvel evaluation_id
        //  - false signifie une erreur generique

        if (is_numeric($result)) // le nouveau evaluation_id
        {
            $url = base_url() . 'evaluations/editeur/' . $result;

            echo json_encode(
                array(
                    'action' => 'redirect',
                    'url'    => $url
                )
            );
            return;
        }

		echo json_encode(FALSE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Copier une evaluation vers un autre cours
     *
     * ------------------------------------------------------------------------ */
    public function importer_exporter_copier_evaluation_vers_cours()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('cours_id', $post_data))
        {
            $post_data['cours_id'] = NULL;
        }

		//
		// Copier vers un autre cours
		//

        $result = $this->Evaluation_model->importer_exporter_copier_evaluation($post_data['evaluation_id'], $post_data['cours_id']);

        // result:
        //  - array signifie une erreur
        //  - numeric signifie le nouvel evaluation_id
        //  - false signifie une erreur generique

        if (is_numeric($result)) // le nouveau evaluation_id
        {
            if ($post_data['cours_id'] == NULL) // importation ou exportation
            {
                $evaluation = $this->Evaluation_model->extraire_evaluation($result);
                $groupe_id  = $evaluation['groupe_id'];
            }
            else
            {
                $cours     = $this->Cours_model->extraire_cours(array('cours_id' => $post_data['cours_id']));
                $groupe_id = $cours['groupe_id'];
            }

            $groupe = $this->Groupe_model->extraire_groupe2(array('groupe_id' => $groupe_id));

            // $url = 'https://' . $groupe['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/evaluations/editeur/' . $result;
            $url = 'https://' . $groupe['sous_domaine'] . '.' . $this->domaine . '/evaluations/editeur/' . $result;

            echo json_encode(
                array(
                    'action' => 'redirect',
                    'url'    => $url
                )
            );
            return;
        }

        if (is_array($result) && array_key_exists('message', $result))
        {
            echo json_encode(
                array(
                    'status'  => 'erreur',
                    'message' => $result['message']
                )
            );
            return;
        }

		echo json_encode(FALSE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Importer ou exporter une evaluation vers un autre cours
     *
     * ------------------------------------------------------------------------ */
    public function importer_exporter_evaluation_vers_cours()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// Copier vers un autre cours
		//

		if (($result = $this->Evaluation_model->importer_exporter_copier_evaluation($post_data['evaluation_id'], $post_data['cours_id'])) !== FALSE)
		{
            if (is_array($result) && array_key_exists('status', $result))
            {
                echo json_encode($result);
                return;
            }
		}
		
		echo json_encode($result);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function effacer_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
		{
            echo json_encode(FALSE);
            return;
		}

		//
		// Effacer
		//

		if ($this->Evaluation_model->effacer_evaluation($post_data['evaluation_id']) == TRUE)
		{
			echo json_encode(TRUE);
			return;
		}
		
		echo json_encode(FALSE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Achiver/Desarchiver une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function archiver_desarchiver_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		//
		// Archiver ou desarchiver une evaluation
		//

		if ($this->Evaluation_model->archiver_desarchiver_evaluation($post_data['evaluation_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Activer/Deactiver une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function activer_desactiver_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		//
		// Activer ou desactiver une evaluation
		//

		if ($this->Evaluation_model->activer_desactiver_evaluation($post_data['evaluation_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer questions aleatoires (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function changer_questions_aleatoires()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->changer_questions_aleatoires($post_data['evaluation_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer le status d'inscription requise
     *
     * ------------------------------------------------------------------------ */
    public function changer_inscription_requise()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->changer_inscription_requise($post_data['evaluation_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer le status du temps en redaction
     *
     * ------------------------------------------------------------------------ */
    public function changer_temps_en_redaction()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->changer_temps_en_redaction($post_data['evaluation_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer le status d'evaluation formative
     *
     * ------------------------------------------------------------------------ */
    public function changer_evaluation_formative()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->changer_evaluation_formative($post_data['evaluation_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Permettre ou interdire les changements a l'evaluation (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function changer_cadenas()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->changer_cadenas($post_data['evaluation_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modification de la description
     *
     * ------------------------------------------------------------------------ */
    public function modifier_description()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'description' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Effetuer la modification a la description
		//

		if ($this->Evaluation_model->modifier_description($post_data['evaluation_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacement des description
     *
     * ------------------------------------------------------------------------ */
    public function effacer_description()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->effacer_description($post_data['evaluation_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer une question sondage
     *
     * ------------------------------------------------------------------------ */
    public function changer_question_sondage()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Question_model->changer_question_sondage($post_data['question_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer reponses aleatoires (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function changer_reponses_aleatoires()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Question_model->changer_reponses_aleatoires($post_data['question_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer selecteur (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function changer_selecteur()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Question_model->changer_selecteur($post_data['question_id'], $post_data['checked']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une reponse
     *
     * ------------------------------------------------------------------------ */
    public function effacer_reponse()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('reponse_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		//
		// effectuer l'effacement de la reponse
		//

		if ($this->Reponse_model->effacer_reponse($post_data['reponse_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer l'ordre servant a la classification des evaluations.
     *
     * ------------------------------------------------------------------------ */
    public function changer_ordre_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($post_data['ordre'] != '0')
        {
            if ( ! is_float($post_data['ordre']) && ! is_numeric($post_data['ordre']))
            {
                echo json_encode(FALSE);
                return;
            }
        }

		if ($this->Evaluation_model->changer_ordre($post_data['evaluation_id'], $post_data['ordre']) !== TRUE)
        {
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer l'ordre servant a la classification des questions.
     *
     * ------------------------------------------------------------------------ */
    public function changer_ordre_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($post_data['ordre'] != '0')
        {
            if ( ! is_float($post_data['ordre']) && ! is_numeric($post_data['ordre']))
            {
                echo json_encode(FALSE);
                return;
            }
        }

		if ($this->Question_model->changer_ordre($post_data['question_id'], $post_data['ordre']) !== TRUE)
        {
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer le type d'une reponse (correcte [1] ou erronee [2->0])
     *
     * ------------------------------------------------------------------------ */
    public function changer_reponse_type()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('reponse_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		//
		// effectuer le changement de type de la reponse
		//

		if ($this->Reponse_model->changer_reponse_type($post_data['reponse_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajouter une reponse
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_reponse()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('question_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//

        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'reponse_texte' :
					$validation_rules = 'required';
					break;
	
				case 'reponse_type' : // ceci est reponse_correcte
					$validation_rules = 'required|numeric';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
        // Verifier les particularites selon le type de question
        //

        //
        // Question a reponse numerique entiere (TYPE 5)
        //

        if ($post_data['question_type'] == 5)
        {
            // La reponse ne peut contenir qu'une valeur entiere positive ou negative.
            if ( ! preg_match('/^-?\d+$/', trim($post_data['reponse_texte'])))
            {
                echo json_encode(FALSE);
                return;
            }

            if (preg_match('/-0/', trim($post_data['reponse_texte'])))
            {
                $post_data['reponse_texte'] = '0';
            }

            if (empty($post_data['unites']))
            {
                unset($post_data['unites']);
            }
        }

        //
        // Question a reponse numerique (TYPE 6)
        //

        if ($post_data['question_type'] == 6)
        {
            if (empty($post_data['unites']))
            {
                unset($post_data['unites']);
            }
        }

		//
		// Effectuer l'ajout de la reponse
		//

		if ($this->Reponse_model->ajouter_reponse($post_data['question_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modifier une reponse
     *
     * ------------------------------------------------------------------------ */
    public function modifier_reponse()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('question_id', 'reponse_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'reponse_texte' :
					$validation_rules = 'required';
					break;
	
				case 'reponse_type' :
					$validation_rules = 'required|numeric';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
        // Verifier des particularites selon le type de question
        //
        if (array_key_exists('question_type', $post_data) && $post_data['question_type'] == 5)
        {
            // La reponse ne peut contenir qu'une valeur entiere positive ou negative.
            if ( ! preg_match('/^-?\d+$/', trim($post_data['reponse_texte'])))
            {
                echo json_encode(FALSE);
                return;
            }

            if (preg_match('/-0/', trim($post_data['reponse_texte'])))
            {
                $post_data['reponse_texte'] = '0';
            }

            if (empty($post_data['unites']))
                $post_data['unites'] == NULL;
        }
	
		if ($this->Reponse_model->modifier_reponse($post_data['reponse_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modifier une equation
     *
     * ------------------------------------------------------------------------ */
	/*
	 * Meme que la modification d'une reponse
     *
     */

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajouter une tolerance
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_tolerance()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($post_data['type'] == 1)
        {
            $post_data['tolerance'] = $post_data['tolerance_absolue'];
        }
        else
        {
            $post_data['tolerance'] = $post_data['tolerance_relative'];
        } 

		//
        // Validation des entrees
		//

        foreach($post_data as $k => $v)
		{
			switch($k)
            {
                case 'type' :
                case 'penalite' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

        // Empecher les tolerances de zero

        if (empty($post_data['tolerance']))
        {
			echo json_encode(FALSE);
			return;
        }

        // Empecher les tolerances et penalites negatives

        $post_data['tolerance'] = str_replace('-', '', $post_data['tolerance']);
        $post_data['penalite']  = str_replace('-', '', $post_data['penalite']);

		//
		// Ajouter une tolerance
		//

		if ($this->Question_model->ajouter_tolerance($post_data['question_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une tolerance
     *
     * ------------------------------------------------------------------------ */
    public function effacer_tolerance()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id', 'tolerance_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
        
		//
		// Effacer une tolerance
		//

		if ($this->Question_model->effacer_tolerance($post_data['question_id'], $post_data['tolerance_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modifier la similarite d'une question a reponse litterale courte
     *
     * ------------------------------------------------------------------------ */
    public function modifier_similarite()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('reponse_similarite', $post_data) || ! is_numeric($post_data['reponse_similarite']))
        {
            echo json_encode(FALSE);
            return;
        }

        $similarite = trim($post_data['reponse_similarite']);

        //
        // Fixer les plages minimum et maximum pour la similarite.
        //

        if ($similarite > 100)
        {
            $similarite = 100;
        }

        if ($similarite < $this->config->item('questions_types')[7]['similarite_min'])
        {
            $similarite = $this->config->item('questions_types')[7]['similarite_min'];
        }

        //
        // Exiecuter le changement de similarite
        //

        if ($this->Reponse_model->modifier_similarite($post_data['question_id'], $similarite) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacement d'une equation
     *
     * ------------------------------------------------------------------------ */
    public function effacer_equation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id', 'reponse_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Reponse_model->effacer_equation($post_data['question_id'], $post_data['reponse_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajout d'une nouvelle question
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'question_texte' :
					$validation_rules = 'required';
					break;
	
				case 'question_type' :
					$validation_rules = 'required|numeric';
					break;

				case 'question_points' :
					$validation_rules = 'required|decimal';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Effectuer l'ajout de la question
		//

		if ($this->Question_model->ajouter_question($post_data['evaluation_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Activer/Deactiver une question
     *
     * ------------------------------------------------------------------------ */
    public function activer_desactiver_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		if ($this->Question_model->activer_desactiver_question($post_data['question_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}
		
		echo json_encode(TRUE);
		return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modification le titre d'une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function modifier_titre()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'evaluation_titre' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// effetuer les modifications au titre
		//

		if ($this->Evaluation_model->modifier_titre($post_data['evaluation_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modification les instructions
     *
     * ------------------------------------------------------------------------ */
    public function modifier_instructions()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = $this->input->post()) == NULL)
        {
            echo json_encode(FALSE);
            return;
        }
	
        foreach($post_data as $k => $v)
		{
            switch($k)
            {
                case 'evaluation_id' :
                    $validation_rules = 'required|numeric';

				case 'instructions' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Effetuer les modifications aux instructions
		//

		if ($this->Evaluation_model->modifier_instructions($post_data['evaluation_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacement des instructions
     *
     * ------------------------------------------------------------------------ */
    public function effacer_instructions()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->effacer_instructions($post_data['evaluation_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modification d'une question
     *
     * ------------------------------------------------------------------------ */
    public function modifier_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('question_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'question_texte' :
					$validation_rules = 'required';
					break;
	
				case 'question_type' :
					$validation_rules = 'required|numeric';
					break;

				case 'question_points' :
					$validation_rules = 'required|decimal';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Effectuer les modifications a la question demandee
		//

		if ($this->Question_model->modifier_question($post_data['question_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Change la reponse (toggle) de Vrai a Faux et de Faux a Vrai
     *
     * ------------------------------------------------------------------------ */
    public function reponse_toggle()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if ( ! array_key_exists('question_id', $post_data) || empty($post_data['question_id']) || ! ctype_digit($post_data['question_id']))
        {
            return FALSE;
        }

        if ( ! array_key_exists('reponse_id', $post_data) || empty($post_data['reponse_id']) || ! ctype_digit($post_data['reponse_id']))
        {
            return FALSE;
        }

        if ( ! array_key_exists('reponse_correcte', $post_data) || ! ctype_digit($post_data['reponse_correcte']))
        {
            return FALSE;
        }

        $this->Reponse_model->modifier_reponse_toggle(
            $post_data['question_id'], 
            $post_data['reponse_id'],
            $post_data['reponse_correcte']
        );

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Lister les cours comportant au moins une evaluation (privee ou publique)
	 *
	 * Cette function permet de populer la liste des evaluations a choisir
 	 * pour ensuite copier la question vers cette evaluation. 
     *
     * ------------------------------------------------------------------------ */
    public function lister_cours_avec_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $public = 0;

        if (array_key_exists('evaluation_public', $post_data))
        {
            $public = $post_data['evaluation_public'];
        }

        $cours_raw = $this->Cours_model->lister_cours_avec_evaluation(
            array(
                'evaluation_id' => $post_data['evaluation_id'],
                'public'        => $public,
                'sans_cadenas'  => TRUE
            )
        );

        // preparer le html

        if (empty($cours_raw))
        {
            echo json_encode(FALSE);
            return FALSE;
        }

        $html = '';

		$premier_cours_id = NULL;

        foreach($cours_raw as $c)
        {
			if (empty($premier_cours_id))	
			{
				$premier_cours_id = $c['cours_id'];
            	$html .= '<option value="' . $c['cours_id'] . '" selected="selected">' . $c['cours_nom'] . ' (' . $c['cours_code_court'] . ')</option>';
	
				continue;
			}

            $html .= '<option value="' . $c['cours_id'] . '">' . $c['cours_nom'] . ' (' . $c['cours_code_court'] . ')</option>';
        }

        echo json_encode($html);
        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Lister les evaluations d'un cours
	 *
	 * Cette function permet de populer la liste des evaluations a choisir
 	 * pour ensuite copier la question vers cette evaluation. 
     *
     * ------------------------------------------------------------------------ */
    public function lister_evaluations()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('cours_id', 'evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $public = 0;

        if (array_key_exists('evaluation_public', $post_data))
        {
            $public = $post_data['evaluation_public'];
        }

        $evaluations = $this->Evaluation_model->extraire_evaluations_pour_select($post_data['cours_id'], $post_data['evaluation_id'], $public);

        $html = '';

		$premiere_evaluation_id = NULL;

        foreach($evaluations as $e)
        {
			if (empty($premiere_evaluation_id))	
			{
				$premiere_evaluation_id = $e['evaluation_id'];
            	$html .= '<option value="' . $e['evaluation_id'] . '" selected="selected">' . $e['evaluation_titre'] . '</option>';
	
				continue;
			}

           	$html .= '<option value="' . $e['evaluation_id'] . '">' . $e['evaluation_titre'] . '</option>';
        }

        echo json_encode($html);
        return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Copier une question vers une autre evaluation qui appartient a l'enseignant.
	 *
     * ------------------------------------------------------------------------ */
    public function copier_question_vers_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id', 'evaluation_id', 'cours_id')))) === FALSE)
		{
            echo json_encode(FALSE);
            return;
        }

		$result = $this->Evaluation_model->copier_question($post_data['question_id'], $post_data['evaluation_id'], $post_data['cours_id']);	

		if (is_array($result) && array_key_exists('status', $result))
		{
			$this->session->set_flashdata('erreur_info', $result);
			echo json_encode($result);
			return;
		}

		echo json_encode($result);
		return;
    }

    /* ========================================================================
     *
     * VARIABLES
     *
     * ======================================================================== */

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajout d'une variable
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_variable()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }
		
        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'variable_nom' :
					$validation_rules = 'required|alpha|max_length[1]';
					break;
	
                case 'variable_minimum' :
                case 'variable_maximum' :
                case 'variable_decimales' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

		if ($post_data['variable_maximum'] < $post_data['variable_minimum'])
		{
			echo json_encode('La valeur minimum doit être inférieure à la valeur maximum');
			return FALSE;
		}

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		if ($this->Evaluation_model->ajouter_variable($post_data['evaluation_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modification d'une variable
     *
     * ------------------------------------------------------------------------ */
    public function modifier_variable()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('variable_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if (array_key_exists('variable_effacement', $post_data))
            unset($post_data['variable_effacement']);

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'variable_nom' :
					$validation_rules = 'required|alpha';
					break;
                case 'minimum' :
                case 'maximum' :
                case 'decimals' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

		if ($post_data['maximum'] < $post_data['minimum'])
		{
			echo json_encode('La valeur minimum doit être inférieure à la valeur maximum');
			return FALSE;
		}

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }
    
        // Le nombre de decimales est limite a 8 (autrement, ca cause une erreur)

        if ($post_data['decimales'] > 8)
        {
            $post_data['decimales'] = 8;
        }

        if ($post_data['decimales'] < 0)
        {
            $post_data['decimales'] = 0;
        }

        //
        // Modifier la variable
        //

		if ($this->Evaluation_model->modifier_variable($post_data['variable_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }


    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacement d'une variable
     *
     * ------------------------------------------------------------------------ */
    public function effacer_variable()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('variable_id', 'evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('variable_effacement', $post_data) || $post_data['variable_effacement'] != 'on')
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Evaluation_model->effacer_variable($post_data['variable_id'], $post_data['evaluation_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Dupliquer une question et de ses reponses
     *
     * ------------------------------------------------------------------------ */
    public function dupliquer_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Question_model->dupliquer_question($post_data['question_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une question et de ses reponses
     *
     * ------------------------------------------------------------------------ */
    public function effacer_question()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// effetuer l'effacement
		//

		if ($this->Question_model->effacer_question_et_reponses($post_data['question_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacement d'un document (d'une image)
     *
     * ------------------------------------------------------------------------ */
    public function effacer_document()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('doc_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Document_model->effacer_document($post_data['doc_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ========================================================================
     *
     * GRILLES DE CORRECTION
     *
     * ======================================================================== */

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Creer une grille de correction
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_grille_correction()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'grille_type' :
				case 'grille_affichage' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Effectuer l'ajout d'une grille de correction
		//

		if ($this->Question_model->ajouter_grille_correction($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Importer une grille de correction
     *
     * ------------------------------------------------------------------------ */
    public function importer_grille_correction()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id', 'question_id', 'question_id_origine')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// Importer la grille de correction
		//

		if ($this->Question_model->importer_grille_correction($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modifier une grille de correction
     *
     * ------------------------------------------------------------------------ */
    public function modifier_grille_correction()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id', 'grille_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
            {
				case 'grille_type' :
                case 'grille_affichage' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Modifier l'effacement de la grille
		//

		if ($this->Question_model->modifier_grille_correction($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une grille de correction
     *
     * ------------------------------------------------------------------------ */
    public function effacer_grille_correction()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
            {
				case 'grille_type' :
                case 'grille_affichage' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

        //
        // Verifier le parametre de securite
        //

        if ( ! array_key_exists('grille_effacement', $post_data) || $post_data['grille_effacement'] != 'on')
        {
			echo json_encode(FALSE);
			return;
        }

		//
		// Effectuer l'effacement de la grille et de ses elements
		//

		if ($this->Question_model->effacer_grille_correction($post_data['question_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajouter un element
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_element_grille()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('question_id', 'grille_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'element_desc' :
                case 'element_type' :
                case 'element_ordre' :
                case 'element_pourcent' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Ajouter un element a une grille
		//

		if ($this->Question_model->ajouter_element_grille($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Dupliquer un element
     *
     * ------------------------------------------------------------------------ */
    public function dupliquer_element_grille()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('question_id', 'grille_id', 'element_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
	
		//
		// Dupliquer un element
		//

		if ($this->Question_model->dupliquer_element_grille($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }


    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modifier un element
     *
     * ------------------------------------------------------------------------ */
    public function modifier_element_grille()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('question_id', 'grille_id', 'element_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
	
		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'element_desc' :
                case 'element_type' :
                case 'element_ordre' :
                case 'element_pourcent' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		//
		// Modifier un element a une grille
		//

		if ($this->Question_model->modifier_element_grille($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer un element
     *
     * ------------------------------------------------------------------------ */
    public function effacer_element_grille()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id', 'grille_id', 'element_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// Effacer un element a une grille
		//

		if ($this->Question_model->effacer_element_grille($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ========================================================================
     *
     * BLOCS
     *
     * ======================================================================== */

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajout d'un bloc
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_bloc()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }
		
        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'bloc_label' :
					$validation_rules = 'required|alpha|max_length[1]';
					break;
	
                case 'bloc_points' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		if ($this->Question_model->ajouter_bloc($post_data['evaluation_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Modification d'un bloc
     *
     * ------------------------------------------------------------------------ */
    public function modifier_bloc()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('bloc_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if (array_key_exists('bloc_effacement', $post_data))
            unset($post_data['bloc_effacement']);

		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'bloc_points' :
                case 'bloc_nb_questions' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

		if ($this->Question_model->modifier_bloc($post_data['bloc_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacement d'un bloc
     *
     * ------------------------------------------------------------------------ */
    public function effacer_bloc()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('bloc_id', 'evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if (array_key_exists('bloc_effacement_questions', $post_data) && $post_data['bloc_effacement_questions'] == 'on')
        {
            if ($this->Question_model->effacer_bloc_questions($post_data['bloc_id'], $post_data['evaluation_id']) !== TRUE)
            {
                echo json_encode(FALSE);
                return;
            }
        }
        elseif (array_key_exists('bloc_effacement', $post_data) && $post_data['bloc_effacement'] == 'on')
        {
            if ($this->Question_model->effacer_bloc($post_data['bloc_id'], $post_data['evaluation_id']) !== TRUE)
            {
                echo json_encode(FALSE);
                return;
            }
        }
        else
        {
            echo json_encode(FALSE);
            return;
        }

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Importer ou exporter un bloc vers une autre evaluation
	 *
     * ------------------------------------------------------------------------ */
    public function copier_bloc_vers_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('bloc_id', 'evaluation_id', 'evaluation_id_cible')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		$result = $this->Question_model->copier_bloc($post_data['bloc_id'], $post_data['evaluation_id'], $post_data['evaluation_id_cible']);	

		if (is_array($result) && array_key_exists('status', $result))
		{
			$this->session->set_flashdata('erreur_info', $result);
			echo json_encode($result);
			return;
		}

		echo json_encode($result);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Assigner a un bloc
     *
     * ------------------------------------------------------------------------ */
    public function assigner_bloc()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('bloc_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Question_model->assigner_bloc($post_data['bloc_id'], $post_data['question_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Desassigner a un bloc
     *
     * ------------------------------------------------------------------------ */
    public function desassigner_bloc()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		if ($this->Question_model->desassigner_bloc($post_data['question_id']) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Ajouter un champ aux tableaux
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_ajouter_champ_sauvegarde()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
            echo json_encode(FALSE);
            return;
        }

		$champs_obligatoires = array('nom_champ', 'valeur', 'nsci', 'unites');

		foreach($champs_obligatoires as $c)
		{
			if ( ! array_key_exists($c, $post_data))
			{
				echo 'champ obligatoire absent';
				echo json_encode(FALSE);
				return;
			}

			if ($c == 'valeur')
			{
				if (empty($post_data[$c]))
				{
					echo 'la valeur ne peut pas etre nulle';
					echo json_encode(FALSE);
					return;
				}
			}
		}

        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id)
        {
			echo 'permission refusee';
            echo json_encode(FALSE);
            return;
        }

        if ( ! empty($evaluation['lab_valeurs']))
        {
            $lab_valeurs = json_decode($evaluation['lab_valeurs'], TRUE);
        }
        else
        {
            $lab_valeurs = array();
        }

        $champ = $post_data['nom_champ'];

        //
        // Verifier que ce champ n'existe pas deja.
        //

        if (array_key_exists($champ, $lab_valeurs))
        {
            // Ce champ existe.
			echo 'ce champ existe deja';
            echo json_encode(FALSE);
            return;
        }

        //
        // Construire le json
        //

		$n_champ_arr = array(
			$champ => array(
				'valeur'    => str_replace(',', '.', $post_data['valeur']),
				'nsci'	    => $post_data['nsci'],
                'unites'    => $post_data['unites']
			)
		);

        $lab_valeurs = array_merge($lab_valeurs, $n_champ_arr);
        $lab_valeurs = json_encode($lab_valeurs);

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->update($this->evaluations_t, array('lab_valeurs' => $lab_valeurs));

        echo json_encode(TRUE);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Modifier le champ d'un tableau
     *
     * ------------------------------------------------------------------------
     *
     * Cette fonction genere le corps du modal pour modifier un champ,
     * mais ne modifie par le champ lui-meme.
     * Ce modal est genere dynamiquement.
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_modifier_champ()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

		$post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
		   	echo 'evaluation_id est absent ou erronne'; 
            echo json_encode(FALSE);
            return;
        }
        
        if ( ! array_key_exists('champ', $post_data) || empty($post_data['champ']))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if ( ! $evaluation['lab'])
        {
		   	echo "ce n'est pas un laboratoire";
            echo json_encode(FALSE);
            return;
        }

        $lab_valeurs = json_decode($evaluation['lab_valeurs'], TRUE);
		$lab_points  = json_decode($evaluation['lab_points'], TRUE);

		$lab_valeurs = complementer_lab_valeurs($lab_valeurs, $lab_points);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id && ! permis('admin_lab'))
        {
		   	// echo "permission refusee";
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier que le champ existe dans les valeurs du laboratoire
        //

        if ( ! array_key_exists($post_data['champ'], $lab_valeurs))
        {
		   	echo 'il existe deja un champ ' . $post_data['champ'] . ' dans $lab_valeurs';
            echo json_encode(FALSE);
            return;
        }

        $data = array(
            'evaluation_id' => $evaluation_id,
            'champ'         => $post_data['champ'],
            'lv'            => $lab_valeurs,
            'lab_prefix'    => $evaluation['lab_prefix']
        );

        //
        // Retourner la page html
        //

        echo json_encode(
            array(
                'html' => $this->load->view('laboratoires/_editeur_tableaux_modal_champ_body2', $data, TRUE)
            )
        );
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Sauvegarer les modifications d'un champ d'un tableau
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_modifier_champ_sauvegarde()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
            echo json_encode(FALSE);
            return;
        }
        
        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id && ! permis('admin_lab'))
        {
		   	// echo "permission refusee";
            echo json_encode(FALSE);
            return;
        }

        $lab_valeurs = json_decode($evaluation['lab_valeurs'], TRUE);

		//
		// Verifier si le champ existe
		//

		$champ = $post_data['nom_champ'];

		if ( ! array_key_exists($champ, $lab_valeurs))
		{
            echo json_encode(FALSE);
            return;
		}

		//
		// Proprietes acceptees
		//

		$proprietes = array('valeur', 'nsci', 'unites', 'tolerance');

		//
		// Apporter les modifications
		//
		// J'ai pense a enlever les champs inutiles mais j'ai decide de les garder si jamais l'enseignant
		// s'est trompe entre est_incertitude (ou n'est pas), alors les infos ne seront pas perdues
		// (par exemple : unites, nsci, dans le cas d'une incertitude).
		// Mais il faut faire attention a ne pas les utiliser par erreur. (2024-08-12)
		//
		
		$changement_detecte = FALSE;

		foreach($proprietes as $p)
		{
			if ( ! array_key_exists($p, $post_data))
				continue;

			if ( ! array_key_exists($p, $lab_valeurs[$champ]))
				$lab_valeurs[$champ][$p] = 0;

			if ($p == 'valeur')
			{
				$post_data[$p] = str_replace(',', '.', $post_data[$p]);
			}

			if ($lab_valeurs[$champ][$p] != $post_data[$p])
			{
				$lab_valeurs[$champ][$p] = $post_data[$p];
				$changement_detecte = TRUE;
			}
		}

        //
        // Reconstruire le json avec les changements
        //

		// unset($post_data['evaluation_id']);
        // unset($post_data['nom_champ']);

        if ($changement_detecte)
        {
            $this->db->where('evaluation_id', $evaluation_id);
            $this->db->update($this->evaluations_t, array('lab_valeurs' => json_encode($lab_valeurs)));
        }

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Effacer un champ d'un tableau
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_effacer_champ()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
            echo json_encode(FALSE);
            return;
        }
        
        if ( ! array_key_exists('champ', $post_data) || empty($post_data['champ']))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id && ! permis('admin_lab'))
        {
		   	// echo "permission refusee";
            echo json_encode(FALSE);
            return;
        }

        $lab_valeurs = json_decode($evaluation['lab_valeurs'], TRUE);

		$champ = $post_data['champ'];

		//
		// Verifier ce que ces champs existent
		//  
	
		if (array_key_exists($champ, $lab_valeurs))
		{
			unset($lab_valeurs[$champ]);
		}

        //
        // Reconstruire le json avec les changements
        //

		$this->db->where('evaluation_id', $evaluation_id);
		$this->db->update($this->evaluations_t, array('lab_valeurs' => json_encode($lab_valeurs)));

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Ajouter des points a un champ
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_ajouter_points_sauvegarde()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
		   	echo 'evalation_id est absent ou errone';
            echo json_encode(FALSE);
            return;
        }

        $champs_obligatoires = array('nom_champ', 'type', 'tableau', 'points', 'tolerance', 'cs', 'cspp', 'est_incertitude', 'incertitude', 'desc');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $post_data))
            {
                echo json_encode(FALSE);
                return;
            }

			if ($c == 'type' && empty($post_data[$c]))
			{
				$post_data[$c] == 'standard';
			}

			if ($c == 'tableau')
			{
				if (empty($post_data[$c]) || $post_data[$c] == 0 || $post_data[$c] < 0)
				{
					$post_data[$c] = 1;
				}
			}

			if ($c == 'points')
			{
				if (empty($post_data[$c]) || $post_data[$c] == 0 || $post_data[$c] < 0)
				{
					$post_data[$c] = 1;
				}
			}

			if ($c == 'est_incertitude')
			{
				if (empty($post_data[$c]))
				{
					$post_data[$c] = 0;
				}
			}
       }

		// Les champs existants :
		//
		// 'nom_champ'
		// 'type'
		// 'tableau'
        // 'points'
        // 'tolerance' (pour la comparaison)
		// 'cs'   (nombre de chiffres significatifs)
		// 'cspp' (cs penalite en pourcentage)
		// 'est_incertitude'
		// 'incertitude' (le nom du champ de son incertitude)
		// 'desc'		

        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id && ! permis('admin_lab'))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! empty($evaluation['lab_points']))
        {
            $lab_points = json_decode($evaluation['lab_points'], TRUE);
        }
        else
        {
            $lab_points = array();
        }

        //
        // Verifier que ce champ n'existe pas deja.
        //

        if (array_key_exists($post_data['nom_champ'], $lab_points))
        {
            // Ce champ existe.
			echo 'ce champ existe deja';
            echo json_encode(FALSE);
            return;
        }

        //
        // Construire le json
        //

        $champ = $post_data['nom_champ'];

        $n_champ_arr = array(
            $champ => array(
				'type'	    => $post_data['type'] ?? 'standard',
                'tableau'   => $post_data['tableau'] ?? 1,
                'points'    => $post_data['points'] ?? 1,
                'tolerance' => $post_data['tolerance'] ?? 0,
				'cs'	    => $post_data['cs'] ?? 0,
				'cspp'	    => $post_data['cspp'] ?? 0,
				'est_incertitude' => $post_data['est_incertitude'],
				'incertitude' => $post_data['incertitude'],
                'desc'    	  => $post_data['desc'] ?? NULL
            )
        );

        $lab_points = array_merge($lab_points, $n_champ_arr);

        $lab_points = json_encode($lab_points);

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->update($this->evaluations_t, array('lab_points' => $lab_points));

        echo json_encode(TRUE);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Modifier des points a un champ
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_modifier_points_sauvegarde()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

		$post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
            // p(array('erreur' => 'Il manque le champ [evaluation_id].')); 
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('nom_champ_origine', $post_data) || empty($post_data['nom_champ_origine']))
        {
            // p(array('erreur' => 'Il manque le champ [nom_champ_origine].')); 
            echo json_encode(FALSE);
            return;
        }
        
        // $champs_obligatoires = array('nom_champ_origine', 'nom_champ', 'type', 'tableau', 'points', 'cs', 'cspp', 'est_incertitude', 'incertitude', 'desc');
        $champs_obligatoires = array('nom_champ_origine', 'nom_champ');

        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $post_data))
            {
                // p(array('erreur' => 'Il manque le champ [' . $c . '].')); 
                echo json_encode(FALSE);
                return;
            }
        }

        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        unset($post_data['evaluation']);

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id)
        {
            // p(array('erreur' => "Le propriétaire de l'évaluation n'est pas vous.", 'evaluation_enseignant_id' => $evaluation['enseignant_id'], 'enseignant_id' => $this->enseignant_id)); 
            echo json_encode(FALSE);
            return;
        }

        if ( ! empty($evaluation['lab_points']))
        {
            $lab_points = json_decode($evaluation['lab_points'], TRUE);
        }
        else
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier que ce champ n'existe pas deja.
        //

        if ( ! array_key_exists($post_data['nom_champ_origine'], $lab_points))
        {
            // Ce champ n'existe pas, donc ne peut pas etre modifier.
            // p(array('erreur' => 'Il manque le champ [nom_champ_origine] dans $lab_points.')); 
            echo json_encode(FALSE);
            return;
        }

		//
		// Verifier les proprietes du champ qui ont changees.
		//

		$champ = $post_data['nom_champ_origine'];
		$proprietes = array(
			'type', 'tableau', 'points', 'tolerance', 'cs', 'cspp', 'est_incertitude', 'incertitude', 'desc', 'eq', 'eq_na'
		);

		// Les champs existants :
		//
		// ('nom_champ_origine')
		// 'nom_champ'
		// 'type'
		// 'tableau'
		// 'points'
		// 'cs'   (nombre de chiffres significatifs)
		// 'cspp' (cs penalite en pourcentage)
		// 'est_incertitude'
		// 'incertitude' (le nom du champ de son incertitude)
		// 'desc'		

        //
        // Detecter les changements
        //

		foreach($proprietes as $p)
		{
			if ( ! array_key_exists($p, $post_data))
			{
				continue;
			}

			if ( ! array_key_exists($p, $lab_points[$champ]))
			{
				if ($p == 'type')
					$lab_points[$champ][$p] == 'standard';

				else
					$lab_points[$champ][$p] = NULL;
			}

			if ($lab_points[$champ][$p] != $post_data[$p])
			{
				if ($p == 'tableau')
				{
					if ($post_data[$p] < 0)
						$post_data[$p] = 1;

					if ($post_data[$p] > 99)
						$post_data[$p] = 99;
				}

				if ($p == 'points')
                {
                    $post_data[$p] = str_replace(',', '.', $post_data[$p]);

					if ($post_data[$p] < 0)
						$post_data[$p] = abs($post_data[$p]);

					if ($post_data[$p] > 100)
						$post_data[$p] = 100;
				}

                if ($p == 'tolerance')
                {
                    $post_data[$p] = str_replace(',', '.', $post_data[$p]);

					if ($post_data[$p] < 0)
                        $post_data[$p] = abs($post_data[$p]);

					if ($post_data[$p] > 50)
						$post_data[$p] = 50;
                }

				$lab_points[$champ][$p] = $post_data[$p];
			}
		}

		//
		// Est-ce que le nom a change ?
		//

		if ($champ != $post_data['nom_champ'])
		{
			$nouveau_nom_champ = $post_data['nom_champ'];

			$lab_points[$nouveau_nom_champ] = $lab_points[$champ];
			unset($lab_points[$champ]);
		}

        //
        // Construire le json
        //

        $lab_points = json_encode($lab_points);

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->update($this->evaluations_t, array('lab_points' => $lab_points));

        echo json_encode(TRUE);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX MODAL) Effacer les points d'un champ
     *
     * ------------------------------------------------------------------------ */
    function modal_tableau_modifier_points_effacer()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        //
        // Verifier les champs obligatoires
        //

        if ( ! array_key_exists('evaluation_id', $post_data) || empty($post_data['evaluation_id']) || ! is_numeric($post_data['evaluation_id']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('nom_champ', $post_data) || empty($post_data['nom_champ']))
        {
            echo json_encode(FALSE);
            return;
        }
        
        //
        // Extraire l'evaluation
        //

        $evaluation_id = $post_data['evaluation_id'];

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        //
        // Verifier les permissions
        //

        if ($evaluation['enseignant_id'] != $this->enseignant_id)
        {
            // p(array('erreur' => "Le propriétaire de l'évaluation n'est pas vous.", 'evaluation_enseignant_id' => $evaluation['enseignant_id'], 'enseignant_id' => $this->enseignant_id)); 
            echo json_encode(FALSE);
            return;
        }

        if ( ! empty($evaluation['lab_points']))
        {
            $lab_points = json_decode($evaluation['lab_points'], TRUE);
        }
        else
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Verifier que ce champ existe.
        //

        if ( ! array_key_exists($post_data['nom_champ'], $lab_points))
        {
            // Ce champ n'existe pas, donc ne peut pas etre modifier.
            echo json_encode(FALSE);
            return;
        }
	
		unset($lab_points[$post_data['nom_champ']]);

        //
        // Construire le json
        //

        $lab_points = json_encode($lab_points);

        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->update($this->evaluations_t, array('lab_points' => $lab_points));

        echo json_encode(TRUE);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer lab indviduel
     *
     * ------------------------------------------------------------------------ */
	public function changer_lab_individuel()
    {
        $post_data = $this->input->post();

		$ids_obligatoires = array('evaluation_id');

		foreach($ids_obligatoires as $id)
		{
			if ( ! array_key_exists($id, $post_data) || ! is_numeric($post_data[$id]))
			{
				echo json_encode(FALSE);
				return;
			}
        }

        if ( ! array_key_exists('individuel', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        $result = $this->Lab_model->changer_lab_individuel($post_data['evaluation_id'], $post_data['individuel']);

		echo json_encode($result);
		return;

    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Changer les parametres de la precorrection
     *
     * ------------------------------------------------------------------------ */
	public function changer_precorrection_parametres()
    {
        $post_data = $this->input->post();

		$ids_obligatoires = array('evaluation_id');

		foreach($ids_obligatoires as $id)
		{
			if ( ! array_key_exists($id, $post_data) || ! is_numeric($post_data[$id]))
			{
				echo json_encode(FALSE);
				return;
			}
        }

        if ( ! array_key_exists('param', $post_data) || empty($post_data['param']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('val', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        $parametres_valides = array('precorrection', 'precorrection_essais', 'precorrection_penalite');

        if ( ! in_array($post_data['param'], $parametres_valides))
        {
            echo json_encode(FALSE);
            return;
        }

        $post_data['val'] = str_replace(',', '.', $post_data['val']);
    
        $result = $this->Lab_model->changer_precorrection_parametres(
            $post_data['evaluation_id'], $post_data['param'], $post_data['val']
        );

		echo json_encode($result);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage($page = NULL)
    {
		unset($this->data['semestre_id']);
        $this->load->view('commons/header', $this->data);

		switch($page)
        {
			case 'aucun-semestre' :
				$this->load->view('evaluations/evaluations_aucun_semestre', $this->data);
				break;

            case 'liste-evaluations' :
                $this->load->view('evaluations/evaluations2', $this->data);
                break;

			case 'liste-mes-evaluations' :
                $this->load->view('evaluations/mes_evaluations2', $this->data);
				break;
        
            case 'liste-archives' :
                $this->load->view('evaluations/archives', $this->data);
                break;

            case 'liste-mes-archives' :
                $this->load->view('evaluations/mes_archives', $this->data);
                break;

            case 'editeur' :
                $this->load->view('evaluations/editeur', $this->data);
                break;

            case 'sommaire' :
                $this->load->view('evaluations/sommaire', $this->data);
                break;

            case 'creer' :
                $this->load->view('evaluations/creer', $this->data);
                break;

            case 'creer-aucun-cours' :
                $this->load->view('evaluations/creer_aucun_cours', $this->data);
                break;

            case 'statistiques' :
                $this->load->view('evaluations/statistiques', $this->data);
                break;

            case 'filtres' :
                $this->load->view('evaluations/filtres', $this->data);
                break;

			default :
                $this->load->view('evaluations/evaluations', $this->data);
				break;
		}

        $this->load->view('commons/footer');
	}
}
