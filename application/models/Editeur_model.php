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
 * EDITEUR MODEL
 *
 * ----------------------------------------------------------------------------
 *
 * Une tentative pour refactoriser les fonctions pertinentes a l'editeur
 * d'evaluation, et de regrouper ces fonctions dans un meme model.
 *
 * L'idee conductrice de ce model est d'utiliser le cache pour separer les 
 * operations sans affecter la vitesse d'execution (ou en minimiser l'impact).
 *
 * ATTN : Ce model n'est presentement pas utilise en production et est en cours
 *        de developpement.
 *
 * ============================================================================ */

class Editeur_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();
    }

    /* ------------------------------------------------------------------------
     *
     * LES EXTRACTIONS
     *
     * ------------------------------------------------------------------------ */

    /* ------------------------------------------------------------------------
     *
     * Extraire les evaluations des enseignants
     *
     * ------------------------------------------------------------------------ */
    function extraire_evaluations_par_enseignants($enseignant_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'variables' => TRUE,
                'questions' => FALSE,
                'questions_reponses'    => FALSE,
                'questions_tolerances'  => FALSE,
                'questions_similarites' => FALSE,
                'questions_grilles'     => FALSE
            ),
            $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . md5(serialize($enseignant_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }
	
		// @TODO
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les evaluations
     *
     * ------------------------------------------------------------------------ */
    function extraire_evaluations($evaluations_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'variables' => TRUE,
                'questions' => FALSE,
                'questions_reponses'    => FALSE,
                'questions_tolerances'  => FALSE,
                'questions_similarites' => FALSE,
                'questions_grilles'     => FALSE
            ),
            $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($evaluation_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les variables
     *
     * ------------------------------------------------------------------------ */
    function extraire_variables($evaluations_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
           ),
           $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($evaluation_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire une question
     *
     * ------------------------------------------------------------------------ */
    function extraire_question($question_id, $options = array())
    {
        $r = $this->extraire_questions(array($question_id));

        if ( ! $r['status'])
        {
            return $r;
        }

        $r['data'] = array_shift($r['data']);

        return $r;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire des questions
     *
     * ------------------------------------------------------------------------ */
    function extraire_questions($question_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'reponses'    => TRUE,
                'tolerances'  => TRUE,
                'similarites' => TRUE,
                'grilles'     => TRUE  // grilles de correction
           ),
           $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($question_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $this->db->from     ('questions as q, evaluations as e, cours as c');
        $this->db->select   ('q.*, e.enseignant_id, e.groupe_id, e.public, c.cours_id');
        $this->db->where    ('e.evaluation_id = q.evaluation_id');
        $this->db->where    ('e.cours_id = c.cours_id');
        $this->db->where_in ('q.question_id', $question_ids);
        $this->db->where    ('q.efface', 0);

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array(
                'status'     => FALSE,
                'error_code' => 'EXQ001',
                'error_msg'  => "Aucune question trouvée"
            );
        }

        $questions = $query->result_array();
        $questions = array_keys_swap($questions, 'question_id');

        foreach($questions as $q)
        {
            //
            // Verifier les permissions
            //

            if ($this->groupe_id != 0 && $this->groupe_id != $q['groupe_id'])
            {
                return array(
                    'status'     => FALSE,
                    'error_code' => 'EXQ002',
                    'error_msg'  => "La question ID " . $q['question_id'] . " n'appartient pas à ce groupe."
                );
            }

            if ($this->enseignant['privilege'] < 90)
            {
                if ( ! $q['public'] && $this->enseignant_id != $q['enseignant_id'])
                {
                    // Cette question ne vous appartient pas.
                    
                    return array(
                        'status'     => FALSE,
                        'error_code' => 'EXQ003',
                        'error_msg'  => "La question ID " . $q['question_id'] . " ne vous appartient pas."
                    );
                }
            }
        }

        //
        // Extraire les reponses
        //

        if ($options['reponses'])
        {
            $reponses = $this->extraire_reponses_questions($question_ids);

            foreach($reponses as $q_id => $r)
            {
                if ( ! array_key_exists($q_id, $questions))
                    continue;

                if ( ! array_key_exists('reponses', $questions[$q_id]))
                {
                    $questions[$q_id]['reponses'] = array();
                }

                if ( ! empty($r))
                {
                    $questions[$q_id]['reponses'][] = $r;
                }
            }
        }

        //
        // Extraire les tolerances
        //

        if ($options['tolerances'])
        {
            $tolerances = $this->extraire_tolerances($question_ids);
            
            foreach($tolerances as $t)
            {
                $q_id = $t['question_id'];

                if ( ! array_key_exists($q_id, $questions))
                    continue;

                if ( ! array_key_exists('tolerances', $questions[$q_id]))
                {
                    $questions[$q_id]['tolerances'] = array();
                }

                $questions[$q_id]['tolerances'][] = $t;
            }
        }

        //
        // Extraire les similarites
        //
        
        if ($options['similarites'])
        {
            $similarites = $this->extraire_similarites($question_ids);

            foreach($similarites as $s)
            {
                $q_id = $s['question_id'];

                if ( ! array_key_exists($q_id, $questions))
                    continue;

                $questions[$q_id]['similarite'] = $s['similarite'];
            }
        }

        //
        // Extraire les grilles de correction
        //
        
        if ($options['grilles'])
        {
            $gc = $this->extraire_grilles($question_ids);
        }

        $r = array(
            'status' => TRUE,
            'data'   => array($questions)
        );

        $this->kcache->save($cache_key, $r, 'questions', 30);

        return $r;
    } 

    /* ------------------------------------------------------------------------
     *
     * Extraire les tolerances
     *
     * -----------------------------------------------------------------------
     *
     * Type 1 : absolue
     * Type 2 : relative
     *
     * ------------------------------------------------------------------------ */
    function extraire_tolerances($question_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
           ),
           $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($question_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $this->db->from     ('questions_tolerances');
        $this->db->where_in ('question_id', $question_ids);
        $this->db->order_by ('tolerance', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            $this->kcache->save($cache_key, array(), 'questions', 30);

            return array();
        }

        //
        // Classer les tolerances par question
        //

        $tolerances = array();

        foreach($query->result_array() as $row)
        {
            $q_id = $row['question_id'];

            if ( ! array_key_exists($q_id, $tolerances))
            {
                $tolerances[$q_id] = array();
            }

            $tolerances[$q_id][] = $row;
        }
        
        $this->kcache->save($cache_key, $tolerances, 'questions', 30);

        return $tolerances;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire la similarite
     *
     * -----------------------------------------------------------------------
     *
     * Il ne peut y avoir qu'une seule similarite par question.
     *
     * ------------------------------------------------------------------------ */
    function extraire_similarites($question_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
           ),
           $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($question_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $similarites = array();

        $this->db->select   ('qs.*');
        $this->db->from     ('questions_similarites as qs, questions as q');
        $this->db->where    ('q.question_id = qs.question_id');
        $this->db->where    ('q.question_type', 7);
        $this->db->where_in ('qs.question_id', $question_ids);
        $this->db->limit    (1);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $row)
            {
               $q_id = $row['question_id']; 

               $similarites[$q_id] = $row['similarite'];
            }
        }

        //
        // Pour toutes les autres questions dont la similarite n'a pas ete specifiee par
        // l'enseignnant (dont qui n'a pas ete trouvee dans la base de donnees), il faut 
        // assigner celle par defaut.
        //

        foreach($question_ids as $q_id)
        {
            if ( ! array_key_exists($q_id, $similarites))
            {
                $similarites[$q_id] = $this->config->item('questions_types')[7]['similarite'];
            }
        }

        $this->kcache->save($cache_key, $similarites, 'questions', 30);

        return $similarites;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les grilles de correction
     *
     * ------------------------------------------------------------------------ */
    function extraire_grilles($question_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'elements' => TRUE
            ),
            $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($question_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        $gc = array(); // grilles de correction

        $this->db->from     ('questions_grilles_correction as gc');
        $this->db->where_in ('gc.question_id', $question_ids);
        $this->db->where    ('gc.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            $this->kcache->save($cache_key, array(), 'questions', 30);

            return array();
        }

        $gc = $query->result_array();
        $gc = array_keys_swap($gc, 'question_id');

        $grille_ids = array_column($gc, 'grille_id');

        //
        // Extraire les elements
        //

        $elements = array();

        $this->db->from     ('questions_grilles_correction_elements as gce');
        $this->db->where_in ('gce.grille_id', $grille_ids);
        $this->db->where    ('gce.efface', 0);
        $this->db->order_by ('gce.element_ordre', 'asc');
        $this->db->order_by ('gce.element_id', 'asc');

        $query = $this->db->get();

        //
        // Preparer le tableau de sortie
        //

        if ( ! $query->num_rows() > 0)
        {
            $this->kcache->save($cache_key, $gc, 'questions', 30);

            return $gc;
        }

        foreach($query->result_array() as $row)
        {
            $q_id = $row['question_id'];

            if ( ! array_key_exists('elements', $gc[$q_id]))
            {
                $gc[$q_id]['elements']    = array();
                $gc[$q_id]['pourcentage'] = 0;
            }

            $gc[$q_id]['elements'][] = $row;

            if ($e['element_type'] == 1)
            {
                $gc[$q_id]['pourcentage'] += $row['element_pourcent']; 
            }
        }

        $this->kcache->save($cache_key, $gc, 'questions', 30);

        return $gc;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les reponses de plusieurs questions
     *
     * ------------------------------------------------------------------------ */
    function extraire_reponses_questions($question_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'filter_symbols' => FALSE
           ),
           $options
       	);

        $cache_key = __CLASS__ . '_' . __FUNCTION__ . '_' . $this->enseignant_id . '_' . md5(serialize($question_ids)) . '_' . md5(serialize($options));

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }

        //
        // Le type de question n'est pas indique dans les reponses 
        // pour les questions des types 1, 4 et 11 car elles sont interchangeables.
        //

        $reponses = array();

        $this->db->from     ('reponses as r, questions as q');
        $this->db->select   ('r.*, q.question_type as question_question_type');
        $this->db->where_in ('r.question_id', $question_ids);
        $this->db->where    ('r.question_id = q.question_id');
        $this->db->where    ('r.efface', 0);
        $this->db->where    ('q.efface', 0);

        $query = $this->db->get();

        if ($query->num_rows())
        {
            foreach($query->result_array as $row)
            {
                $q_id = $row['question_id'];

                // 
                // 1. Retrocompatibilite avant 2018-12-23
                //
                // Pour les anciennes questions a choix unique et choix multiples,
                // ne pas permettre les anciennes reponses d'etre affichees pour les deux types de questions 
                // car ceci pourrait creer des doublons dans les reponses d'anciennes evaluations.
                //
                // A l'epoque, il fallait reentrer toutes les reponses lorsqu'on changeait le type de
				// question entre 1, 4 et 11 alors que mainenant les reponses a ces types de questions ne 
				// sont plus fixees donc on peut changer entre 1, 4 et 11 sans reentrer les reponses.
                //
                // 2. Situation actuelle
                //
                // Ceci est verifie meme maintenant car j'ai remarque que les reponses de ces types de questions
                // sont parfois indiquees dans les nouvelles reponses. Je crois que le type de question de ces 
				// reponses n'est pas verifie. Il est possible qu'en copiant d'anciennes reponses, les types sont 
				// copies egalement.
				// A investiguer... (@TODO) (2020-10-22)
                //

                if (in_array($row['question_question_type'], array(1, 4, 11)))
                {
                    // 1. Retrocompatibilite

                    if ($row['ajout_epoch'] < 1545627792) // Ce epoch correspond a la premiere question depuis le 2018-12-24.
                    {
                        if ( ! empty($row['question_type']) && $row['question_type'] != $row['question_question_type'])
                        {
                            continue;
                        }
                    }

                    // 2. Situation actuelle

                    else
                    {
                        if ( ! empty($row['question_type']))
                        {
                            $row['question_type'] = NULL;
                        }
                    }
                }

                //
                // Verifier que les reponses possedent le type de question concordant
                //
                // Exceptions : 
                //
                // - Question a choix unique (TYPE 1)
                // - Question a choix multiples (TYPE 4)
                // - Question a choix multiples stricte (TYPE 11)
                //
                // Etant donne que les reponses des questions des types 1, 4, 11 sont
                // les memes, ces trois types de questions sont interchangeables.
                //
                
                if ( ! empty($row['question_type']))
                {
                    if ($row['question_type'] != $row['question_question_type'])
                    {
                        continue;
                    }
                }

                //
                // Verifier les questions qui requierent une equation pour reponse
                //
                // Question a choix unique par equations (TPE 3)
                // Question a reponse numerique par equations (TYPE 9)
                //

                if (in_array($row['question_question_type'], array(3, 9)))
                {
                    if ( ! $r['equation'])
                    {
                        continue;
                    }
                }

                //
                // Filter les symboles si demande
                //

                if ($options['filter_symbols'] && array_key_exists('reponse_texte', $row))
                {
                    $row['reponse_texte'] = filter_symbols($r['reponse_texte']);
                }

                //
                // Ajouter la reponse au tableau des reponses
                //

                if ( ! array_key_exists($q_id, $reponses))
                {
                    $reponses[$q_id] = array();
                }

                $reponses[$q_id][] = $row;
            }
        }

        //
        // Ajouter les questions qui n'ont aucune reponse au tableau,
        // car les autres fonctions s'attendent a un tableau vide pour les
        // questions sans reponse.
        //

        foreach($question_ids as $q_id)
        {
            if ( ! array_key_exists($q_id, $reponses))
            {
                $reponses[$q_id] = array();
            }
        }

        $this->kcache->save($cache_key, $reponses, 'reponses', 30);

        return $reponses;
    }

    /* ------------------------------------------------------------------------
     *
     * LES OPERATIONS
     *
     * ------------------------------------------------------------------------ */

    /* ------------------------------------------------------------------------
     *
     * Copier des questions
     *
     * ------------------------------------------------------------------------
     *
     * Copier des questions vers une autre evaluation.
     *
     * ------------------------------------------------------------------------ */
    function copier_questions($question_ids = array(), $options = array())
    {
    	$options = array_merge(
            array(
                'evaluation_id_cible' => NULL   // champ obligatoire
           ),
           $options
       	);

        //
        // Verifier la presence des champs obligatoires
        //

        $champs_obligatoires = array('evaluation_id_cible');

        foreach($champs_oblitatoires as $c)
        {
            if ( ! array_key_exists($c, $options) || empty($options[$c]))
            {
                return array(
                    'status'     => FALSE,
                    'error_code' => 'CPQ001',
                    'error_msg'  => "Un ou plusieurs champs obligatoires manquants dans les options."
                );;
            } 
        }

        //
        // Extraire les questions
        //

        $r = $this->extraire_questions($question_ids);

        if ( ! $r['status'])
        {
             return $r;
        }

        $questions = $r['data'];

        //
        // Extraire les reponses de questions
        //
    }

}
