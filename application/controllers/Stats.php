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
 * STATS (STATISTIQUES)
 *
 * ============================================================================ */

class Stats extends MY_Controller 
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
     * index
     *
     * ------------------------------------------------------------------------ */
    public function index()
    {
        redirect(base_url());
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Ecrire une variable dans la session de l'usager
     *
     * ------------------------------------------------------------------------ */
    public function ecrire_session()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (empty($post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('soumission_ids', $post_data) || empty($post_data['soumission_ids']))
        {
            echo json_encode(FALSE);
            return;
        }

        $soumission_ids = $post_data['soumission_ids'];

        if ( ! array_key_exists('groupe_no', $post_data) || empty($post_data['groupe_no']))
        {
            $groupe_no = NULL;
        }
        else
        {
            $groupe_no = $post_data['groupe_no'];
        }

        $requete = $post_data['requete'] ?? NULL;

        $_SESSION['stats_soumission_ids' . $requete] = $soumission_ids;
        $_SESSION['stats_groupe_no' . $requete]      = $groupe_no;

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Statistiques d'une evaluation ou d'une question
     *
     * ------------------------------------------------------------------------
     *
     * - depuis sa creation
     * - pour un semestre / groupe (2024-12-12)
     *
     * ------------------------------------------------------------------------ */
	public function evaluation()
    {
        $args = $this->uri->uri_to_assoc(2);

        //
        // Extraire la requete
        //

        $this->data['requete'] = $requete = $options['requete'] = $args['req'] ?? NULL;

        //
        // Enregistrer les informations de la requete
        //

        $evaluation_id = $_SESSION['stats_evaluation_id' . $requete]  = $args['evaluation'];

        //
        // Indiquer l'origine (evaluation ou resultats)
        //

        // Ceci est une requete pour les statistiques d'une question, apres l'origine
        if ( ! array_key_exists('question', $args))
        {
            if (array_key_exists('semestre', $args))
            {
                $semestre_id = $_SESSION['stats_semestre_id' . $requete] = $args['semestre'] ?? NULL;
                $groupe_no   = $_SESSION['stats_groupe_no' . $requete] = $args['groupe'] ?? NULL;

                $stats_origine = $this->data['stats_origine'] = $_SESSION['stats_origine' . $requete] = 'resultats';
            }
            else
            {
                $stats_origine = $this->data['stats_origine'] = $_SESSION['stats_origine' . $requete] = 'editeur';
            }
        }

        $stats_origine = $this->data['stats_origine'] = $_SESSION['stats_origine' . $requete];
        $semestre_id = $semestre_id ?? NULL;
        $groupe_no = $groupe_no ?? NULL;

        $options['stats_origine'] = $stats_origine;

        //
        // Extraire l'evaluation
        //

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

		if (empty($evaluation))
		{
            redirect(base_url() . 'evaluations/editeur/' . $evaluation_id);
            exit;
		}

        if ($this->enseignant_id != $evaluation['enseignant_id'] && $this->enseignant['privilege'] < 90)
        {
            redirect(base_url() . 'evaluations/editeur/' . $evaluation_id);
            exit;
        }

        //
        // Prevoir les URLs de retour
        //

        if ($stats_origine == 'editeur')
        {
            $this->data['stats_retour_resultats'] = base_url() . 'evaluations/editeur/' . $evaluation_id;
            $this->data['stats_retour_resultats_stats'] = base_url() . 'stats/evaluation/' . $evaluation_id . '/req/' . $requete;

            $_SESSION['stats_retour_resultats' . $requete]       = $this->data['stats_retour_resultats'];
            $_SESSION['stats_retour_resultats_stats' . $requete] = $this->data['stats_retour_resultats_stats'];
        }
        else
        {
            $this->data['stats_retour_resultats'] = base_url() . 'resultats/evaluation/' . $evaluation_id . '/semestre/' . $_SESSION['stats_semestre_id' . $requete];

            if ( ! empty($_SESSION['stats_groupe_no'. $requete]))
            {
                $this->data['stats_retour_resultats_stats'] = base_url() . 'stats/evaluation/' . $evaluation_id . '/semestre/' . $_SESSION['stats_semestre_id' . $requete] . '/groupe/' . $_SESSION['stats_groupe_no' . $requete] . '/req/' . $requete;
            }
            else
            {
                $this->data['stats_retour_resultats_stats'] = base_url() . 'stats/evaluation/' . $evaluation_id . '/semestre/' . $_SESSION['stats_semestre_id' . $requete] . '/req/' . $requete;
            }

            $_SESSION['stats_retour_resultats' . $requete]       = $this->data['stats_retour_resultats'];
            $_SESSION['stats_retour_resultats_stats' . $requete] = $this->data['stats_retour_resultats_stats'];
        }

        //
        // Extraire les soumission_ids
        //

        if ($stats_origine == 'editeur')
        { 
            //
            // Extraire les soumissions a partir de la date de creation de la question
            //

            $soumissions = $this->Evaluation_model->extraire_soumissions(
                array(
                    'enseignant_id' => $evaluation['enseignant_id'],
                    'semestre_id'   => NULL,
                    'evaluation_id' => $evaluation_id,
                    'epoch'         => $evaluation['ajout_epoch'],
                    'ordre'         => 'remise_desc'
                )
            );

            if (empty($soumissions))
            {
                generer_erreur('STATEVN', "Aucune soumission de cette évaluation n'a été trouvée.",
                    array(
                        'url' 		 => base_url() . 'evaluations/editeur/' . $evaluation_id,
                        'importance' => 0
                    )
                );
                exit;
            }

            $soumission_ids = array_keys($soumissions);
        }

        if ($stats_origine == 'resultats')
        {
            $soumission_ids = array();

            if ( ! isset($_SESSION['stats_soumission_ids' . $requete]))
            {
                die('Houston, nous avons un problème avec les soumission_ids, code GHJQ');

                redirect(base_url() . 'resultats');
                exit;
            }

            $soumission_ids = unserialize(htmlspecialchars_decode($_SESSION['stats_soumission_ids' . $requete]));
        }
        
        //
        // Statistiques d'une question
        //

        if (array_key_exists('question', $args) && ! empty($args['question']) && is_numeric($args['question']))
        {
            $this->_question($soumission_ids, $groupe_no, $args['question'], $options);
            return;
        }

        //
        // Statistiques d'une evaluation
        //

        $this->_evaluation($soumission_ids, $groupe_no, $options);
        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Statistiques des resultats d'un groupe
     * version 2 (mars 2020)
     * version 3 (decembre 2024)
     *
     * ------------------------------------------------------------------------ */
    public function resultats($methode, $id = NULL)
    {
        $args = $this->uri->uri_to_assoc();

        //
        // Extraire la requete
        //

        $requete = $args['req'] ?? NULL;

        $this->data['requete'] = $requete;

        //
        // Indiquer l'origine (evaluation ou resultats)
        //

        $options['stats_origine']    = 'resultats';
        $this->data['stats_origine'] = 'resultats';

        $_SESSION['stats_origine_' . $requete] = 'resultats';

        //
        // Verifier la methode demandee
        //

        $methodes_valides = array('evaluation', 'question');

        if ( ! in_array($methode, $methodes_valides))
        {
            redirect(base_url() . 'resultats');
            exit;
        }

        // 2024-12-12

        if ($_SESSION['stats_origine_' . $requete] == 'resultats')
        {
            $this->data['stats_retour_resultats'] = base_url() . 'resultats/evaluation/' . $args['evaluation'] . '/semestre/' . $args['semestre'];
            $this->data['stats_retour_resultats_stats'] = base_url() . 'stats/resultats/evaluation/' . $args['evaluation'] . '/semestre/' . $args['semestre'] . '/req/' . $requete;

            $_SESSION['stats_retour_resultats_' . $requete]       = $this->data['stats_retour_resultats'];
            $_SESSION['stats_retour_resultats_stats_' . $requete] = $this->data['stats_retour_resultats_stats'];
        }

        //
        // Enregistrer les informations de la requete
        //

        $_SESSION['stats_evaluation_id' . $requete] = $args['evaluation'];
        $_SESSION['stats_semestre_id' . $requete] = $args['semestre'];

        //
        // Extraire les soumission_ids de la session
        //
        // Les soumission_ids ont ete ecrits dans la session lorsque l'enseignant
        // a clique sur le lien.
        //

        $soumission_ids = array();

        if ( ! isset($_SESSION['stats_soumission_ids' . $requete]))
        {
            redirect(base_url() . 'resultats');
            exit;
        }

        $soumission_ids = unserialize(htmlspecialchars_decode($_SESSION['stats_soumission_ids' . $requete]));

        //
        // Extraire le groupe no de la session
        //
        // Si le groupe_no est NULL, cela signifie que l'enseignant veut voir
        // les statistiques cumulatives (plusieurs groupes).
        //

        $groupe_no = $_SESSION['stats_groupe_no' . $requete] ?? 'plusieurs groupes';

        // 
        // Resultats d'une evaluation d'un groupe
        //

        $options['requete'] = $requete;

        if ($methode == 'evaluation')
        {
            /*
                $_SESSION['stats_retour' . $requete]            = 'resultats/evaluation/' . $_SESSION['stats_evaluation_id' . $requete] . '/semestre/' . $_SESSION['stats_semestre_id' . $requete];
                $_SESSION['stats_retour_evaluation' . $requete] = 'resultats/evaluation/' . $_SESSION['stats_evaluation_id' . $requete] . '/req/' . $requete;

                if ( ! empty($args['evaluation'] && ! empty($args['semestre'])))
                {
                    $_SESSION['stats_retour' . $requete]            = 'resultats/evaluation/' . $args['evaluation'] . '/semestre/' . $args['semestre'];
                    $_SESSION['stats_retour_evaluation' . $requete] = 'resultats/evaluation/' . $args['evaluation'] . '/req/' . $requete;
                }
                else
                {
                    $_SESSION['stats_retour' . $requete]            = 'resultats';
                    $_SESSION['stats_retour_evaluation' . $requete] = 'resultats/evaluation/' . $args['evaluation'] . '/req/' . $requete;
                }
            */

            $this->_evaluation($soumission_ids, $groupe_no, $options);
            return;
        }

        //
        // Resultats d'une question d'un groupe
        //

        if ($methode == 'question')
        {
            $options = array_merge($options, $this->uri->uri_to_assoc(5));

            $this->_question($soumission_ids, $groupe_no, $id, $options);
            return;
        }

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Statistiques des resultats d'une evaluation d'un groupe
     *
     * ------------------------------------------------------------------------ */
    public function _evaluation($soumission_ids, $groupe_no = NULL, $options = array())
    {
        $requete = $options['requete'] ?? NULL;

        //
        // Expirer l'ordre des questions lors d'une visualisation precedente
        // (pour _question)
        //

        unset($_SESSION['stats_question_ordre_expiration' . $requete]);
        unset($_SESSION['stats_question_ordre_question_ids' . $requete]);

        //
        // Extraire les soumissions 
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions_selectionnees(
            array(
                'soumission_ids' => $soumission_ids
            )
        );

        if (empty($soumissions))
        {
            redirect(base_url() . 'resultats');
            exit;
        }

        //
        // Compiler les statistiques
        //

        // Evaluation

        $evaluation = array();
        $cours      = array();

        $e_nombre         = 0;
        $e_points_totaux  = 0; 
        $e_points_obtenus = 0;

        // Questions (index: question_id)

        $q_nombre         = array();
        $q_points_totaux  = array();
        $q_points_obtenus = array();
        $q_textes         = array();

        // Hash des evaluations 
        // Ceci va servir a determiner le nombre d'evaluations uniques

        $hash = array();    // hash des questions telles qu'elles ont ete posees (avec le meme ordre)
        $hash_s = array();  // hash des questions

        $hash_soumission_ids = array();

        foreach($soumissions as $s)
        {
            if (empty($evaluation))
            {
                $evaluation = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                $cours      = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
            }

            $soumission_id = $s['soumission_id'];
            $questions     = json_decode(gzuncompress($s['questions_data_gz']), TRUE);
            $question_ids  = array_keys($questions);

			$ajustements = array();

			if ( ! empty($s['ajustements_data']))
			{
				$ajustements = unserialize($s['ajustements_data']);
			}

            $e_nombre++;
            $e_points_totaux  += $s['points_total'];
            $e_points_obtenus += $s['points_obtenus']; 

            foreach($questions as $q)
            {
                $question_id = $q['question_id'];

                if ( ! array_key_exists($question_id, $q_nombre))
                {
                    $q_nombre[$question_id]         = 0;
                    $q_points_totaux[$question_id]  = 0;
                    $q_points_obtenus[$question_id] = 0;
                    $q_textes[$question_id]         = '';
                }

                $q_nombre[$question_id]++;

                if ( ! array_key_exists('sondage', $q) || ! $q['sondage'])
                {
                    if (array_key_exists($question_id, $ajustements))
                    {
                        $q_points_obtenus[$question_id] += $ajustements[$question_id]['points_obtenus'];
                    }
                    else	
                    {			
                        $q_points_obtenus[$question_id] += $q['points_obtenus'];
                    }
                }

                $q_points_totaux[$question_id]  += $q['question_points'];
                $q_textes[$question_id]          = $q['question_texte'];
            }
        }

        ksort($q_nombre);

        $data = array(
            'evaluation'       => $evaluation,
            'evaluation_id'    => $evaluation['evaluation_id'],
            'cours'            => $cours,
            'groupe_no'        => $groupe_no,
            'soumissions'      => $soumissions,
            'soumission_ids'   => $soumission_ids,
            'e_nombre'         => $e_nombre,
            'e_points_totaux'  => $e_points_totaux,
            'e_points_obtenus' => $e_points_obtenus,
            'questions'        => $questions,
            'q_nombre'         => $q_nombre,
            'q_points_totaux'  => $q_points_totaux,
            'q_points_obtenus' => $q_points_obtenus,
            'q_textes'         => $q_textes,
            'origine'          => $options['stats_origine'],
            'retour'           => base_url() . @$_SESSION['stats_retour' . $requete],
            'eval_diff'        => array_count_values($hash),   // evaluations differentes en considerant l'ordre
            'eval_diff_s'      => array_count_values($hash_s), // evaluations differentes en ne considerant pas l'ordre des questions
            'hash_soumission_ids' => $hash_soumission_ids
        );

        $this->data = array_merge($this->data, $data);

        $this->_affichage('evaluation');
    }

    /* ------------------------------------------------------------------------
     *
     * Question
     *
     * ------------------------------------------------------------------------ */
    public function _question($soumission_ids, $groupe_no, $question_id, $options = array())
    {
        $requete = $options['requete'] ?? NULL;

        // Determiner l'ordre de presentation des questions pour la barre de defilemenet
        //
        // - Cet ordre peut provenir d'une requete POST via un hack pour dissimuler un POST dans un lien normal.
        // - Cet ordre peut provenir de la session.
        // - Cet ordre peut etre l'ordre par defaut qui est celui des question_ids (croissant).
        //

        $ordre = 'question'; // ordre par defaut
        $ordre_question_ids = array();

        $post_data = $this->input->post();

        if (array_key_exists('ordre', $post_data) && ! empty($post_data['ordre']))
        {
            $_SESSION['stats_question_ordre_question_ids' . $requete] = $post_data['ordre'];
            $_SESSION['stats_question_ordre_expiration' . $requete]   = $this->now_epoch + 60*60; 

            $ordre = 'session';
            $ordre_question_ids = json_decode($post_data['ordre']);
        }
        else
        {
            if (isset($_SESSION['stats_question_ordre_expiration' . $requete]) && $_SESSION['stats_question_ordre_expiration'. $requete] > $this->now_epoch)
            {
                $ordre = 'session';
                $ordre_question_ids = json_decode($_SESSION['stats_question_ordre_question_ids' . $requete]);
            }
        }

        //
        // Extraire les soumissions 
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions_selectionnees(
            array(
                'soumission_ids' => $soumission_ids
            )
        );

        //
        // Extraire les images (elles ne sont pas stockees dans les soumissions)
        //

        $images = $this->Document_model->extraire_images(array($question_id));

        //
        // Conserver seulement les soumissions dont la question est presente
        //

        $evaluation_data = array();
        $cours_data      = array();

        $question   = array();
        $numeros_da = array();

        $soumissions_a_conserver = array();
        $questions_a_conserver   = array();

        $points_obtenus = 0;
        $points_totaux  = 0;

        $reponse_correcte = '';

        //
        // Extraire toutes les soumissions dont cette question est presente
        //

        foreach($soumissions as $s)
        {
            $soumission_id  = $s['soumission_id'];

            //
            // Extraire (seulement la premire fois) les donnees de l'evaluation et du cours
            //

            if (empty($evaluation_data))
            {
                $evaluation_data = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                $cours_data      = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
            }

            if ( ! in_array($s['numero_da'], $numeros_da))
            {
                $numeros_da[] = $s['numero_da'];
            }

            //
            // Extraire les ajustements aux points obtenus
            //

			$ajustements = array();

			if ( ! empty($s['ajustements_data']))
			{
				$ajustements = unserialize($s['ajustements_data']);
			}

            //
            // Iterer a travers les questions (pour chaque evaluation)
            //

            $questions_data = json_decode(gzuncompress($s['questions_data_gz']), TRUE);

            foreach($questions_data as $q)
            {
                //
                // Extraire les question_ids pour l'ordre
                //

                if ($ordre == 'question' && ! in_array($q['question_id'], $ordre_question_ids))
                {
                    $ordre_question_ids[] = $q['question_id'];
                }

                //
                // Si ce n'est pas la question pertinente, continuer a la prochaine.
                //

                if ($q['question_id'] != $question_id)
                {
					continue;
				}

				if (empty($question))
				{
					$question = $q;
                }

				$q['soumission_id']        = $soumission_id;
				$q['soumission_reference'] = $s['soumission_reference'];
				$q['soumission_epoch']     = $s['soumission_epoch'];
				$q['soumission_date']      = $s['soumission_date'];

                if ( ! array_key_exists('sondage', $q) || ! $q['sondage'])
                {
                    if (array_key_exists($question_id, $ajustements))
                    {
                        $points_obtenus += $ajustements[$question_id]['points_obtenus'];
                    }
                    else
                    {
                        $points_obtenus += $q['points_obtenus'];
                    }

                    if ( ! in_array($q['question_type'], array(2, 10, 12)))
                    {
                        $reponse_correcte = $q['reponse_correcte_texte'];
                    }
                }

				$points_totaux  += $q['question_points'];

				$soumissions_a_conserver[$soumission_id] = $s;
                $questions_a_conserver[] = $q;

            } // foreach $questions_data

        } // foreach $soumissions

        //
        // Ordonner les question_ids pour l'ordre des questions (si cet ordre est demande)
        //

        if ($ordre == 'question')
        {
            sort($ordre_question_ids);
        }

        //
        // Generer la barre de defilement
        //

        if ( ! empty($ordre_question_ids))
        {
            $question_ids = $ordre_question_ids;

            // Determiner l'index actuel

            $i = array_search($question_id, $question_ids);

            $defilement_prec = NULL;
            $defilement_suiv = NULL;

            $prem_key = 0;
            $dern_key = key(array_slice($question_ids, -1, 1, TRUE));

            $defilement_prec_prem = $question_ids[$prem_key];
            $defilement_suiv_dern = $question_ids[$dern_key];

            if ($i > 0)
            {
                $defilement_prec = $question_ids[$i - 1];
            }

            if ($i < (count($question_ids) - 1))
            {
                $defilement_suiv = $question_ids[$i + 1];
            }

            $this->data['defilement_prec']      = $defilement_prec;
            $this->data['defilement_prec_prem'] = $defilement_prec_prem;
            $this->data['defilement_suiv']      = $defilement_suiv;
            $this->data['defilement_suiv_dern'] = $defilement_suiv_dern;
            $this->data['defilement_index']     = $i + 1; // sinon ca commence a zero
            $this->data['defilement_total']     = count($question_ids);

            $url = base_url() . 'stats/evaluation/' . $_SESSION['stats_evaluation_id' . $requete] . '/question/';

            if ($_SESSION['stats_origine' . $requete] == 'resultats')
            {
                $url = base_url() . 'stats/resultats/question/';
            }
            else
            {
                $url = base_url() . 'stats/evaluation/' . $_SESSION['stats_evaluation_id' . $requete] . '/question/';
            }

            $this->data['defilement_prec_url']      = $url . $defilement_prec . '/req/' . $requete;
            $this->data['defilement_suiv_url']      = $url . $defilement_suiv . '/req/' . $requete;
            $this->data['defilement_prec_prem_url'] = $url . $defilement_prec_prem . '/req/' . $requete;
            $this->data['defilement_suiv_dern_url'] = $url . $defilement_suiv_dern . '/req/' . $requete;
        }

        //
        // Extraire les noms et prenoms des etudiants a partir des numeros da.
        //

        $numeros_da = $this->Groupe_model->extraire_noms_de_numeros_da($numeros_da);

        $this->data['cours']             = $cours_data;
        $this->data['question']          = $question;
        $this->data['question_id']       = $question_id;
        $this->data['reponse_correcte']  = $reponse_correcte;
        $this->data['images']            = $images;
        $this->data['evaluation_id']     = $evaluation_data['evaluation_id'];
        $this->data['groupe_no']         = $groupe_no;
        $this->data['soumissions']       = $soumissions_a_conserver;
        $this->data['soumission_ids']    = $soumission_ids;
        $this->data['questions']         = $questions_a_conserver;
        $this->data['points_obtenus']    = $points_obtenus;
        $this->data['points_totaux']     = $points_totaux;
        $this->data['numeros_da']        = $numeros_da;
        // $this->data['retour_evaluation'] = base_url() . 'stats/' . $_SESSION['stats_retour_evaluation' . $requete];

        $this->_affichage('question');
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Defilement des questions
     *
     * ------------------------------------------------------------------------ */
    public function defilement_questions($page = NULL)
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

        if ( ! array_key_exists('question_ids', $post_data) || empty($post_data['question_ids']))
        {
            echo json_encode(FALSE);
            return;
        }

        $_SESSION['stats_question_question_ids'] = serialize($post_data['question_ids']);
        $_SESSION['stats_question_expiration'] = $this->now_epoch + 60*60*12; // 12 heures

        echo json_encode(base_url() . 'stats/resultats/question/' . $post_data['question_ids'][0] . '/defilement');
        return;
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
            case 'evaluation' :
                $this->load->view(strtolower(__CLASS__) . '/evaluation', $this->data);
                break;

            case 'question' :
                $this->load->view(strtolower(__CLASS__) . '/question', $this->data);
                break;
		}

        $this->load->view('commons/footer');
	}

}
