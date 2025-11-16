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
 * QUESTION MODEL
 *
 * ============================================================================ */

class Question_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Permissions accordees pour effectuer des changements sur une question
     *
     * -------------------------------------------------------------------------------------------- */
    function permissions_question($question_id, $question = NULL)
    {
        // Note :
        // Si les donnees de la question ont deja ete extraites, il faut simplement
        // les passer en arguement dans $question
        //
        //
        // Les permissions
        //

        $permissions = array(
            'modifier',
            'ajouter_reponse',
            'effacer'
        );

        //
        // Les permissions accordees par default (aucune)
        //

        $permissions_accordees = array();

        //
        // Extraire la question
        //

        if ( ! 
            ( 
                ! empty($question) && 
                  array($question) && 
                  array_key_exists('ajout_par_enseignant_id', $question) && 
                  array_key_exists('enseignant_id', $question) && 
                  array_key_exists('public', $question)
             )
           )
        {
            $this->db->from  ('questions as q, evaluations as e');
            $this->db->where ('q.question_id', $question_id);
            $this->db->where ('q.efface', 0);
            $this->db->where ('q.evaluation_id = e.evaluation_id');
            $this->db->where ('e.groupe_id', $this->enseignant['groupe_id']);
            $this->db->where ('e.efface', 0);
            $this->db->limit (1);

            $query = $this->db->get();

            if ( ! $query->num_rows())
            {
                return array();
            }

            $question = $query->row_array();
        }

        foreach($permissions as $permission)
        {
            //
            // Le responsable de l'evaluation peut tout faire sur les questions.
            //

            if ($question['enseignant_id'] == $this->enseignant['enseignant_id'])
            {
                $permissions_accordees[] = $permission;
                continue;
            }

            //
            // Le responsable de la question peut tout faire sur les questions.
            //

            if ($question['ajout_par_enseignant_id'] == $this->enseignant['enseignant_id'])
            {
                $permissions_accordees[] = $permission;
                continue;
            }

            //
            // Les permissions speciales pour l'admin.
            //
            
            if (permis('admin'))
            {
                $permissions_accordees[] = $permission;
                continue;
            }

            //
            // Si ce n'est pas une evaluation du departement ne donner aucune autre permission.
            //

            if ( ! $question['public'])
            {
                continue;
            }

            //
            // Les permissions speciales pour les editeurs.
            //
 
            if (permis('editeur'))
            {
                $permissions_accordees[] = $permission;
                continue;
            }
        }

        return $permissions_accordees;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une question
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * Si 'groupe_id' est specifie, la question sera extraite seulement si le groupe_id 
     * correspond a celui de l'evaluation auquel est associe la question.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_question($question_id, $groupe_id = NULL)
    {
        $this->db->from     ('questions as q, evaluations as e');
        $this->db->select   ('q.*, e.cours_id, e.enseignant_id');
        $this->db->where    ('e.evaluation_id = q.evaluation_id');
        $this->db->where    ('q.question_id', $question_id);
        $this->db->where    ('q.efface', 0);

        if ( ! empty($groupe_id))
        {
            $this->db->where    ('q.evaluation_id = e.evaluation_id');
            $this->db->where    ('e.groupe_id', $groupe_id);
        }
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;

        $question = $query->row_array();

        return $question;
    } 

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des questions
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_questions($evaluation_id, $options = array())
    {
    	$options = array_merge(
            array(
                'question_ids' => array(), // depuis 2019-02-01
                'actif'        => NULL
           ),
           $options
       	);

        $this->db->from  ('questions as q, enseignants as en');
        $this->db->select('q.*, en.nom as enseignant_nom, en.prenom as enseignant_prenom');

        $this->db->where ('q.evaluation_id', $evaluation_id);
        $this->db->where ('q.efface', 0);

        if ( ! empty($options['question_ids']))
        {
            $this->db->where_in ('q.question_id', $options['question_ids']);
        }

        if ($options['actif'])
        {
            $this->db->where ('q.actif', 1);
        }

        $this->db->where('q.ajout_par_enseignant_id = en.enseignant_id');

        // Extraire seulement les types de questions connus (ceci pour prevenir les types de questions encore en developpement) (depuis 2019/01/13)
        $this->db->where_in('q.question_type', array_keys($this->config->item('questions_types')));

        $this->db->order_by('q.ordre', 'asc');
        $this->db->order_by('q.question_id', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }
        
        $questions = array_keys_swap($query->result_array(), 'question_id');

        return $questions;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des questions pour enregistrement dans une soumission
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette methode a pour objectif de diminuer l'information enregistree dans une soumission
     * en selectionnant seulement les champs pertinents.
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_questions_pour_soumission($evaluation_id, $options = array())
    {
    	$options = array_merge(
            array(
                'question_ids' => array(), 
                'actif'        => NULL
           ),
           $options
        );

        $this->db->from    ('questions as q');
        $this->db->where   ('q.evaluation_id', $evaluation_id);
        $this->db->where   ('q.efface', 0);
        //
        // Options
        //

        if ( ! empty($options['question_ids']))
        {
            $this->db->where_in ('q.question_id', $options['question_ids']);
        }

        if ($options['actif'])
        {
            $this->db->where ('q.actif', 1);
        }

        //
        // Extraction
        //

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }
        
        return array_keys_swap($query->result_array(), 'question_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter une nouvelle question
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_question($evaluation_id, $post_data)
    {
        //
        // Verifier que la question peut etre ajoutee par cet enseignant.
        //

        if ( ! in_array('ajouter_question', $this->Evaluation_model->permissions_evaluation($evaluation_id)))
        {
            echo "Vous n'avez pas la permission d'ajouter une question à cette évaluation.";
            return FALSE;
        }

        //
        // Ajouter la question
        //

        $post_data['question_texte'] = verifier_tags($post_data['question_texte']);

        $data = array(
            'evaluation_id'   => $evaluation_id,
            'question_texte'  => _html_in($post_data['question_texte']),
            'question_type'   => $post_data['question_type'],
            'question_points' => $post_data['question_points'],
            'actif'           => 0, // Les questions sont crees non activees.
            'ajout_par_enseignant_id' => $this->enseignant['enseignant_id'],
            'ajout_date'      => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'     => $this->now_epoch
        );

        $this->db->insert('questions', $data);

        $n_question_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier une question
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_question($question_id, $post_data)
    {
        //
        // Verifier que la question peut etre modifiee par cet enseignant.
        //

        if ( ! in_array('modifier', $this->permissions_question($question_id)))
        {
            echo "Vous n'avez pas la permission de modifier cette question.";
            return FALSE;
        }

        //
        // Modifier la question
        //

        $post_data['question_texte'] = verifier_tags($post_data['question_texte']);

        $data = array(
            'question_texte'  => _html_in($post_data['question_texte']),
            'question_type'   => $post_data['question_type']
        );
    
        if (array_key_exists('question_points', $post_data))
        {
            $data['question_points'] = $post_data['question_points'];
        }

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Activer / Desactiver une question
     *
     * -------------------------------------------------------------------------------------------- */
    function activer_desactiver_question($question_id)
    {
        $question = $this->extraire_question($question_id, $this->enseignant['groupe_id']);

        if ( ! $question)
        {
            return FALSE;
        }

        $data = array(
            'actif' => $question['actif'] ? 0 : 1
        );

        //
        // Reduire le bloc
        // 
        // Si necessaire, reduire d'une unite le nombre de question a choisir dans le bloc.
        //

        if ($question['actif'])
        {
            // Il faut s'assurer de reduire le nb de questions de ce bloc si ce qu'il contient est insuffisant.

            if ( ! empty($question['bloc_id']) && is_numeric($question['bloc_id']))
            {
                $bloc = $this->extraire_bloc($question['bloc_id']);

                $nb_questions = $bloc['bloc_nb_questions']; // Le bloc doit choisir X questions.
                $nb_questions_dans_bloc = 0; // Le bloc contient X questions.

                if ( ! empty($bloc))
                {
                    $nb_questions_dans_bloc = $this->nb_questions_dans_bloc($question['bloc_id']); 
                }

                if ($nb_questions > 0 && $nb_questions == $nb_questions_dans_bloc)
                {
                    // Reduire d'une unite
                    $this->db->where ('bloc_id', $question['bloc_id']);
                    $this->db->set   ('bloc_nb_questions', 'bloc_nb_questions-1', FALSE);
                    $this->db->update('blocs');
                }
            }
        }

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une question (d'une meme evaluation)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Effacer une question a des implications sur l'evaluation.
     *
     * Condition :
     *
     * Les questions doivent appartenir a la meme evaluation.
     *
     * Ce qu'il faut effacer :
     *
     * - la question
     * - les reponses -> les tolerances, les similarites
     * - l'image
     * - la grille de correction -> les elements
     *
     * Ce qu'il faut ajuster :
     *
     * - reduire les blocs
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_question_et_reponses($question_id)
    {
        //
        // Verifier que la question peut etre modifiee par cet enseignant.
        //

        $this->db->from  ('questions as q, evaluations as e');
        $this->db->where ('q.question_id', $question_id);
        $this->db->where ('q.efface', 0);

        if ( ! permis('editeur'))
        {
            $this->db->where('q.ajout_par_enseignant_id', $this->enseignant['enseignant_id']);
        }

        $this->db->where ('q.evaluation_id = e.evaluation_id');
        $this->db->where ('e.groupe_id', $this->enseignant['groupe_id']);
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        //
        // Determiner les reponses de cette reponse
        //

        $reponse_ids = array();

        $this->db->from  ('reponses as r');
        $this->db->where ('r.efface', 0);
        $this->db->where ('r.question_id', $question_id);

        $query = $this->db->get();

        if ($query->num_rows())
        {
            foreach($query->result_array() as $r)
            {
                $reponse_ids[] = $r['reponse_id'];
            }
        }

        $this->db->trans_begin();

        //
        // Effacer les reponses
        //

        if ( ! empty($reponse_ids))
        {
            $data = array(
                'efface' => 1,
                'efface_epoch' => $this->now_epoch,
                'efface_date'  => date_humanize($this->now_epoch, TRUE)
            );

            $this->db->where_in('reponse_id', $reponse_ids);
            $this->db->update  ('reponses', $data);

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }

            // 
            // Effacer les tolerances
            //

            $this->db->where ('question_id', $question_id);
            $this->db->delete('questions_tolerances');

            // 
            // Effacer les similarites
            //

            $this->db->where ('question_id', $question_id);
            $this->db->delete('questions_similarites');
        }

        //
        // Effacer les documents
        //

        if ( ! $this->Document_model->effacer_documents_par_question($question_id))
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Reduire le bloc
        // 
        // Si necessaire, reduire d'une unite le nombre de question a choisir dans le bloc.
        //

        if ( ! empty($question['bloc_id']) && is_numeric($question['bloc_id']))
        {
            $bloc = $this->extraire_bloc($question['bloc_id']);

            $nb_questions = $bloc['bloc_nb_questions']; // Le bloc doit choisir X questions.
            $nb_questions_dans_bloc = 0; // Le bloc contient X questions.

            if ( ! empty($bloc))
            {
                $nb_questions_dans_bloc = $this->nb_questions_dans_bloc($question['bloc_id']); 
            }

            if ($nb_questions > 0 && $nb_questions == $nb_questions_dans_bloc)
            {
                // Reduire d'une unite
                $this->db->where ('bloc_id', $question['bloc_id']);
                $this->db->set   ('bloc_nb_questions', 'bloc_nb_questions-1', FALSE);
                $this->db->update('blocs');
            }
        }

        //
        // Effacer la grille de correction et ses elements
        //

        if ($this->effacer_grille_correction($question_id) === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Effacer la question
        //

        $data = array(
            'efface' => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer une question sondage
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_question_sondage($question_id, $checked)
    {
        if ( ! in_array('modifier', $this->permissions_question($question_id)))
        {
            return FALSE;
        }

        $question = $this->extraire_question($question_id, $this->enseignant['groupe_id']);

        if ($question == FALSE)
            return FALSE;

        $data = array(
            'sondage' => $question['sondage'] ? 0 : 1
        );

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer reponses aleatoires d'une question
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_reponses_aleatoires($question_id, $checked)
    {
        if ( ! in_array('modifier', $this->permissions_question($question_id)))
        {
            return FALSE;
        }

        $question = $this->extraire_question($question_id, $this->enseignant['groupe_id']);

        if ($question == FALSE)
            return FALSE;

        $data = array(
            'reponses_aleatoires' => $question['reponses_aleatoires'] ? 0 : 1
        );

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer selecteur 
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_selecteur($question_id, $checked)
    {
        if ( ! in_array('modifier', $this->permissions_question($question_id)))
        {
            return FALSE;
        }

        $question = $this->extraire_question($question_id, $this->enseignant['groupe_id']);

        if ($question == FALSE)
            return FALSE;

        $data = array(
            'selecteur' => $question['selecteur'] ? 0 : 1
        );

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer l'ordre
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_ordre($question_id, $ordre)
    {
        if ( ! in_array('modifier', $this->permissions_question($question_id)))
        {
            return FALSE;
        }

        $data = array(
            'ordre' => $ordre
        );

        $this->db->where ('question_id', $question_id);
        $this->db->update('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Dupliquer une question
     *
     * Cette function permet de dupliquer une question dans une meme evaluation.
     *
     * -------------------------------------------------------------------------------------------- */
    function dupliquer_question($question_id)
    {
        //
        // Extraire la question
        //

        $question = $this->extraire_question($question_id);

        if ( ! $question)
        {
			log_alerte(
				array(
					'code'  => 'DUP7801',
                    'desc'  => "La question a être dupliquée n'a pas été trouvée dans la base de donnée.",
                    'extra' => 'question_id=' . $question_id . ',enseignant_id=' . $this->enseignant_id
				)
            );

            return FALSE;
        }

        //
        // Extraire les reponses
        //

        $reponses = $this->Reponse_model->lister_reponses($question_id);

        //
        // Extraire les tolerances
        //
        // Question a reponse numerique
        // Question a reponse numerique par equation
        //

        $tolerances = array();

        if (in_array($question['question_type'], array(6, 9)))
        {
            $tolerances = $this->Question_model->extraire_tolerances($question_id);
        }

        //
        // Extraire la similarite
        //

        $similarite = array();

        if ($question['question_type'] == 7)
        {
            $similarite = $this->Question_model->extraire_similarite($question_id);
        }

        // 
        // Extraire l'image associee a la question
        //

        $image = $this->Document_model->extraire_image($question_id);

        //
        // Extraire les grilles
        //

        $gc = $this->extraire_grilles_correction($question_id);

        //
        // Dupliquer la question
        //

        $this->db->trans_begin();

        $data = $question;

        // Enlever les champs inexistants
        $champs = $this->db->list_fields('questions');

        foreach($data as $k => $val)
        {
            if ( ! in_array($k, $champs))
                unset($data[$k]);
        }

        unset($data['question_id']);

        $data['actif']                   = 0;
        $data['ajout_par_enseignant_id'] = $this->enseignant_id;
        $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
        $data['ajout_epoch']             = $this->now_epoch;

        $this->db->insert('questions', $data);

        $n_question_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
			log_alerte(
				array(
					'code'  => 'DUP9512',
                    'desc'  => "Il n'a pas été possible de dupliquer la question.",
                    'extra' => $this->db->error()['message']
				)
            );

            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Dupliquer les reponses
        //

        if ( ! empty($reponses))
        {
            $data_batch = array();

            $champs = $this->db->list_fields('reponses');

            foreach($reponses as $r)
            {
                $data = $r;

                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                        unset($data[$k]);
                }

                unset($data['reponse_id']);

                /*
                if ( ! empty($data['question_type']))
                {
                    // Les questions de type 3 et 5 ont besoin de cette information.

                    if ($data['question_type'] == 1 || $data['question_type'] == 4)
                        unset($data['question_type']);
                }
                */
                
                $data['question_id']             = $n_question_id;
                $data['ajout_par_enseignant_id'] = $this->enseignant_id;
                $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
                $data['ajout_epoch']             = $this->now_epoch;

                $data_batch[] = $data;
            }

            if ( ! empty($data_batch))
            {
                $this->db->insert_batch('reponses', $data_batch);

                if ( ! $this->db->affected_rows())
                {
                    log_alerte(
                        array(
                            'code'  => 'DUP9912',
                            'desc'  => "Il n'a pas été possible de dupliquer les réponses.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    $this->db->trans_rollback();
                    return FALSE;
                }
            }
        }

        //
        // Dupliquer les tolerances
        //

        if ( ! empty($tolerances))
        {
            $data_batch = array();

            $champs = $this->db->list_fields('questions_tolerances');

            foreach($tolerances as $t)
            {
                $data = $t;

                foreach($data as $k => $val)
                {
                    if ( ! in_array($k, $champs))
                        unset($data[$k]);
                }

                unset($data['tolerance_id']);
                
                $data['question_id'] = $n_question_id;

                $data_batch[] = $data;
            }

            if ( ! empty($data_batch))
            {
                $this->db->insert_batch('questions_tolerances', $data_batch);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();

                    log_alerte(
                        array(
                            'code'  => 'DUP9913',
                            'desc'  => "Il n'a pas été possible de dupliquer les tolérances.",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    return array(
                        'status'  => 'error',
                        'code'    => 'DUP9913',
                        'message' => "Il n'a pas été possible de dupliquer les tolérances."
                    );
                }
            }
        }

        //
        // Dupliquer la similarite
        //

        if ( ! empty($similarite))
        {
            $champs = $this->db->list_fields('questions_similarites');

            $data = $similarite;

            foreach($data as $k => $val)
            {
                if ( ! in_array($k, $champs))
                    unset($data[$k]);
            }

            if (array_key_exists('similarite_id', $data))
            {
                unset($data['similarite_id']);
            }
            
            $data['question_id'] = $n_question_id;

            $this->db->insert('questions_similarites', $data);

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();

                log_alerte(
                    array(
                        'code'  => 'DUP9914',
                        'desc'  => "Il n'a pas été possible de dupliquer la similarité.",
                        'extra' => $this->db->error()['message']
                    )
                );

                return array(
                    'status'  => 'error',
                    'code'    => 'DUP9913',
                    'message' => "Il n'a pas été possible de dupliquer le similarités."
                );
            }
        }

        //
        // Dupliquer l'image (document)
        //

        if ( ! empty($image))
        {
            $data = $image;

            $champs = $this->db->list_fields('documents');

            // Enlever les champs inexistants
            foreach($data as $k => $val)
            {
                if ( ! in_array($k, $champs))
                    unset($data[$k]);
            }

            unset($data['doc_id']);

            $data['question_id']             = $n_question_id;
            $data['ajout_par_enseignant_id'] = $this->enseignant_id;
            $data['ajout_date']              = date_humanize($this->now_epoch, TRUE);
            $data['ajout_epoch']             = $this->now_epoch;

            $this->db->insert('documents', $data);

            if ( ! $this->db->affected_rows())
            {
                log_alerte(
                    array(
                        'code'  => 'DUP9191',
                        'desc'  => "Il n'a pas été possible de dupliquer l'image de la question.",
                        'extra' => $this->db->error()['message']
                    )
                );

                $this->db->trans_rollback();
                return FALSE;
            }
        } 

        //
        // Dupliquer la grille de correction
        //

        if ( ! empty($gc) && array_key_exists($question_id, $gc))
        {
            $gc = $gc[$question_id];

            $grille_id = $gc['grille_id'];

            $data = array(
                'evaluation_id'     => $gc['evaluation_id'],
                'question_id'       => $n_question_id,
                'grille_affichage'  => $gc['grille_affichage'],
                'ajout_date'        => date_humanize($this->now_epoch, TRUE),
                'ajout_epoch'       => $this->now_epoch
            );

            $this->db->insert('questions_grilles_correction', $data);

            if ( ! $this->db->affected_rows())
            {
                log_alerte(
                    array(
                        'code'  => 'DUP9192',
                        'desc'  => "Il n'a pas été possible de dupliquer la grille de correction.",
                        'extra' => $this->db->error()['message']
                    )
                );

                $this->db->trans_rollback();
                return FALSE;
            }

            $n_grille_id = $this->db->insert_id();

            // Dupliquer les elements

            if (array_key_exists('elements', $gc) && ! empty($gc['elements']))
            {
                $data = array();

                foreach($gc['elements'] as $e)
                {
                    $data[] = array(
                        'question_id'       => $n_question_id,
                        'grille_id'         => $n_grille_id,
                        'element_type'      => $e['element_type'],
                        'element_desc'      => $e['element_desc'],
                        'element_ordre'     => $e['element_ordre'],
                        'element_pourcent'  => $e['element_pourcent'],
                        'ajout_date'        => date_humanize($this->now_epoch, TRUE),
                        'ajout_epoch'       => $this->now_epoch
                    );
                }

                $this->db->insert_batch('questions_grilles_correction_elements', $data);

                if ( ! $this->db->affected_rows())
                {
                    log_alerte(
                        array(
                            'code'  => 'DUP9193',
                            'desc'  => "Il n'a pas été possible de dupliquer les elements de la grille",
                            'extra' => $this->db->error()['message']
                        )
                    );

                    $this->db->trans_rollback();
                    return FALSE;
                }
            }
        }

        if ($this->db->trans_status() === FALSE)
        {
            log_alerte(
                array(
                    'code'  => 'CQE7431',
                    'desc'  => "Une erreur est survenue lors du copiage de la question et/ou de ses composantes."
                )
            );

            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     *  BLOCS DE QUESTIONS
     *
     * -------------------------------------------------------------------------------------------- */

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un nouveau bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_bloc($evaluation_id, $post_data = array())
    {
        if ( ! in_array('modifier', $this->Evaluation_model->permissions_evaluation($evaluation_id)))
        {
            echo "Vous n'avez pas la permission d'ajouter un bloc à cette évaluation.";
            return FALSE;
        }

        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if ( ! ($this->enseignant_id == $evaluation['enseignant_id'] || permis('editeur')))
        {
            return FALSE;
        }

        // Verifier que le label n'est pas deja pris.

        $blocs = $this->extraire_blocs($evaluation_id);

        if ( ! empty($blocs))
        {
            foreach($blocs as $bloc_id => $b)
            {
                if ($post_data['bloc_label'] == $b['bloc_label'])
                {
                    return FALSE;
                }
            }
        }

        $data = array(
            'evaluation_id' => $evaluation_id,
            'bloc_label'    => $post_data['bloc_label'],
            'bloc_points'   => $post_data['bloc_points'],
            'bloc_desc'     => $post_data['bloc_desc'],
        );

        $this->db->insert('blocs', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_bloc($bloc_id, $post_data)
    {
        // @TODO 
        // Il faudrait pouvoir donner l'autorisation a celui qui a ajoute le bloc de le modifier,
        // mais pour l'instant ce n'est pas possible car ce n'est pas inscrit dans la base de donnees a
        // qui appartient le bloc.

        if ( ! in_array('modifier', $this->Evaluation_model->permissions_evaluation($post_data['evaluation_id'])))
        {
            echo "Vous n'avez pas la permission de modifier un bloc à cette évaluation.";
            return FALSE;
        }

        $evaluation = $this->Evaluation_model->extraire_evaluation($post_data['evaluation_id']);

        //
        // Verifier que le bloc peut etre modifie par l'enseignant.
        //

        $bloc = $this->extraire_bloc($bloc_id);

        if (empty($bloc))
            return FALSE;

        $data = array();

        foreach($post_data as $field => $value)
        {
            if (in_array($field, array('bloc_id', 'evaluation_id')))
                continue;

            if ($field == 'bloc_nb_questions')
            {
                // Il faut s'assurer que le nombre de questions choisi n'excede pas
                // le nombre de questions que contient ce bloc.

                if ( ! array_key_exists('evaluation_id', $post_data))
                    continue;

                $nb_questions_dans_bloc = $this->nb_questions_dans_bloc($bloc_id);

                if ($post_data['bloc_nb_questions'] > $nb_questions_dans_bloc)
                    continue;
            }

            if ($field == 'bloc_label')
            {
                //
                // Modifier le label
                //

                if ( ! array_key_exists('evaluation_id', $post_data))
                    continue;

                // Il faut s'assurer que le nouveau bloc_label choisi n'est pas presentement utilise.

                $blocs = $this->extraire_blocs($post_data['evaluation_id']);
                $bloc_labels = array_keys_swap($blocs, 'bloc_label');

                if ( ! array_key_exists($post_data['bloc_label'], $bloc_labels) && $post_data['bloc_label'] != $bloc['bloc_label'])
                {
                    $data['bloc_label'] = $post_data['bloc_label'];
                }
            }

            // Inclure les changements seulement/
            if ($post_data[$field] !== $bloc[$field])
            {
                $data[$field] = $post_data[$field];
            }
        } 

        if (empty($data))
        {
            // Aucun changement detecte
            return TRUE;
        }

        $this->db->where('bloc_id', $bloc_id);
        $this->db->update('blocs', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_bloc($bloc_id, $evaluation_id)
    {
        if ( ! in_array('modifier', $this->Evaluation_model->permissions_evaluation($evaluation_id)))
        {
            echo "Vous n'avez pas la permission d'effacer un bloc de cette évaluation.";
            return FALSE;
        }

        $this->db->trans_begin();

        $data = array(
            'efface' => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->where('bloc_id', $bloc_id);
        $this->db->where('evaluation_id', $evaluation_id);

        $this->db->update('blocs', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Desassigner toutes les questions de ce bloc.
        //

        $this->db->from  ('questions as q');
        $this->db->where ('q.bloc_id', $bloc_id);
        $this->db->where ('q.efface', 0);
        $this->db->where ('q.evaluation_id', $evaluation_id);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $questions = $query->result_array();
            $question_ids = array_keys(array_keys_swap($questions, 'question_id'));

            $data = array(
                'bloc_id' => NULL
            );

            $this->db->where_in ('question_id', $question_ids);
            $this->db->update('questions', $data);
            
            $query = $this->db->get();
        
            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un bloc et toutes ses questions
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_bloc_questions($bloc_id, $evaluation_id)
    {
        if ( ! in_array('modifier', $this->Evaluation_model->permissions_evaluation($evaluation_id)))
        {
            echo "Vous n'avez pas la permission d'effacer un bloc de cette évaluation.";
            return FALSE;
        }

        $this->db->trans_begin();

        $data = array(
            'efface' => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->where('bloc_id', $bloc_id);
        $this->db->where('evaluation_id', $evaluation_id);

        $this->db->update('blocs', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Extraire et effacer toutes les questions de ce bloc
        //

        $this->db->from  ('questions as q');
        $this->db->where ('q.bloc_id', $bloc_id);
        $this->db->where ('q.efface', 0);
        $this->db->where ('q.evaluation_id', $evaluation_id);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $questions    = $query->result_array();
            $question_ids = array_column($questions, 'question_id');

            $data = array(
                'efface' => 1,
                'efface_epoch' => $this->now_epoch,
                'efface_date'  => date_humanize($this->now_epoch, TRUE)
            );

            $this->db->where_in ('question_id', $question_ids);
            $this->db->update   ('questions', $data);
            
            $query = $this->db->get();
        
            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Copier un bloc
     *
     * Cette function permet de copier un bloc, ses questions et ses variables, dans une autre
     * evaluation (importation et exportation).
     *
     * @TODO :
     * Il y a une certaine redondance dans cette function et celle de copier une evaluation.
     *
     * -------------------------------------------------------------------------------------------- */
    function copier_bloc($bloc_id, $evaluation_id, $evaluation_id_cible)
    {   
        //
        // Extraire le bloc
        //

        $bloc = $this->extraire_bloc($bloc_id);

        if (empty($bloc))
        {
            return array(
                'status'  => 'error',
                'code'    => 'QBC2341',
                'message' => "Ce bloc est introuvable."
            );
        }

        //
        // Extraire toutes les questions du bloc
        //

        $this->db->from  ('questions as q');
        $this->db->where ('q.bloc_id', $bloc_id);
        $this->db->where ('q.evaluation_id', $evaluation_id);
        
        $query = $this->db->get();
        $count = $this->db->count_all_results();
        
        if ( ! $query->num_rows() > 0)
        {
			log_alerte(
				array(
					'code'  => 'QBC2343',
                    'desc'  => "Ce bloc ne contient aucune question.",
                    'extra' => 'bloc_id=' . $bloc_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',enseignant_id=' . $this->enseignant_id
				)
            );

            return array(
                'status'  => 'error',
                'code'    => 'QBC2343',
                'message' => "Ce bloc ne contient aucune question. Il n'est donc pas nécessaire de l'importer ou l'exporter."
            );
        }

        $questions = $query->result_array();
        
        //
        // Extraire l'evaluation d'ORIGINE
        //

        $evaluation_orig = $this->Evaluation_model->extraire_evaluation($evaluation_id); 

        //
        // Extraire l'evaluation CIBLE
        //

        $evaluation_cible = $this->Evaluation_model->extraire_evaluation($evaluation_id_cible); 

        if ($evaluation_orig['groupe_id'] != $this->groupe_id || $evaluation_cible['groupe_id'] != $this->groupe_id)
        {
			log_alerte(
				array(
					'code'  => 'QBC2345',
                    'desc'  => "L'évaluation d'origine, ou l'évaluation cible, n'appartient pas au groupe de l'enseignant.",
                    'extra' => 'bloc_id=' . $bloc_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',cours_id_cible=' . $cours_id_cible . ',enseignant_id=' . $this->enseignant_id
				)
            );

            return array(
                'status'  => 'error',
                'code'    => 'QBC2345',
                'message' => "L'évaluation d'origine, ou l'évaluation cible, n'appartient pas au groupe de l'enseignant."
            );
        }

        //
        // Determiner s'il s'agit d'une importation ou exportation
        //

        $importation = FALSE;
        $exportation = FALSE;

        if ($evaluation_cible['public'])
        {
            $exportation = TRUE;
        }
        else
        {
            $importation = TRUE;
        }

        if ($exportation)
        {
            if ($evaluation_orig['enseignant_id'] != $this->enseignant_id && ! $evaluation_orig['public'])
            {
                log_alerte(
                    array(
                        'code'  => 'QBC2347',
                        'desc'  => "L'évaluation d'origine n'appartient pas à l'enseignant.",
                        'extra' => 'question_id=' . $question_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',cours_id_cible=' . $cours_id_cible . ',enseignant_id=' . $this->enseignant_id
                    )
                );

                return array(
                    'status'  => 'error',
                    'code'    => 'QBC2347',
                    'message' => "L'évaluation d'origine n'appartient pas à l'enseignant (exportation)."
                );
            }
        }

        if ($importation)
        {
            if ($evaluation_cible['enseignant_id'] != $this->enseignant_id)
            {
                log_alerte(
                    array(
                        'code'  => 'QBC2348',
                        'desc'  => "L'évaluation cible n'appartient pas à l'enseignant.",
                        'extra' => 'question_id=' . $question_id . ',evaluation_id_cible=' . $evaluation_id_cible . ',cours_id_cible=' . $cours_id_cible . ',enseignant_id=' . $this->enseignant_id
                    )
                );

                return array(
                    'status'  => 'error',
                    'code'    => 'QBC2348',
                    'message' => "L'évaluation cible n'appartient pas à l'enseignant (importation)."
                );
            }
        }

        //
        // Extraire tous les blocs presents dans l'evaluation cible
        //

        $blocs_cible = $this->extraire_blocs($evaluation_id_cible);

        //
        // Extraire toutes les variables presentes dans les evaluations
        //

        $variables_orig  = $this->Evaluation_model->extraire_variables($evaluation_id);
        $variables_cible = $this->Evaluation_model->extraire_variables($evaluation_id_cible);

        //
        // Determiner si un bloc de meme etiquette existe dans l'evaluation_cible
        //

        foreach($blocs_cible as $b)
        {
            if ($b['bloc_label'] == $bloc['bloc_label'])
            {
                return array(
                    'status'   => 'error',
                    'code'     => 'QBC2349',
                    'message'  => "Un bloc de même étiquette existe dans l'évaluation cible.",
                    'solution' => "Veuillez changer l'étiquette du bloc de l'évaluation d'origine, ou de l'évaluation cible, puis recommencer."
                );
            }
        }

        //
        // VARIABLES DANS LES QUESTIONS ET REPONSES
        //
        // Determiner les variables utilisees dans les questions et les reponses du bloc a copier
        //

        $variables_trouvees = array();

        $images_toutes   = array();
        $reponses_toutes = array();

        foreach($questions as $q)
        {
            $question_id = $q['question_id'];

            $reponses = $this->Reponse_model->lister_reponses($question_id);

            $reponses_toutes[$question_id] = $reponses;
            $images_toutes[$question_id]   = $this->Document_model->extraire_image($question_id);

            //
            // Dans les enonces de toutes les questions
            //

            if (preg_match_all('/<var>(.+?)<\/var>/', $q['question_texte'], $matches))
            {
                $variables_trouvees = array_merge($variables_trouvees, $matches[1]);
            }

            //
            // Si la question ne comporte pas de reponse, inutile d'aller plus loin.
            //

            if (empty($reponses))
                continue;

            //
            // Si c'est une question a developpement, inutile d'aller plus loin.
            //

            if ($q['question_type'] == 2)
                continue;

            //
            // Les variables dans les questions a coefficients variables
            //

            $questions_types = array(3);

            if (in_array($q['question_type'], $questions_types))
            {
                foreach($reponses as $r)
                {
                    if (preg_match_all('/([ABCDFGHIJKLMNOPQRSTUVWXYZ]{1})/', $r['reponse_texte'], $matches))
                    {
                        $variables_trouvees = array_merge($variables_trouvees, $matches[1]);
                    } 
                }
            }

            //
            // Les variables dans les questions a choix unique et multiples
            //

            $questions_types = array(1, 4);

            if (in_array($q['question_type'], $questions_types))
            {
                foreach($reponses as $r)
                {
                    if (preg_match_all('/<var>(.+?)<\/var>/', $q['question_texte'], $matches))
                    {
                        $variables_trouvees = array_merge($variables_trouvees, $matches[1]);
                    }
                }
            }

        } // foreach $questions

        //
        // Verifier qu'il soit possible de copier les variables.
        //

        if ( ! empty($variables_trouvees))
        {
            //
            // Dedoublonner le tableau des variables trouvees.
            //

            $variables_trouvees = array_unique($variables_trouvees);

            foreach($variables_trouvees as $v)
            {
                // Verifier que cette variable est bien definie dans l'evaluation d'origine.
                // (Peut-etre qu'elle est utilisee sous forme <var>X</var> mais non definie par exemple.)
                // Si c'est le pas, pas besoin de la copier.

                if ( ! array_key_exists($v, $variables_orig))
                {
                    // Ceci est le code pour enlever un element d'un tableau (lorsqu'on ne connait pas la key).
                    unset($variables_trouvees[array_search($v, $variables_trouvees)]);
                    continue;
                }

                if (array_key_exists($v, $variables_cible))
                {
                    return array(
                        'status'   => 'error',
                        'code'     => 'QBC2355',
                        'message'  => "La variable " . $v . ",  utilisée par une des questions du bloc, existe déjà dans l'évaluation cible.",
                        'solution' => "Veuillez changer l'étiquette de la variable " . $v . " dans l'évaluation d'origine, ou dans l'évaluation cible, puis recommencer."
                    );
                }
            }
        }

        $this->db->trans_begin();

        //
        // Copier le bloc
        //

        $data = $bloc;

        unset($data['bloc_id']);

        $data['evaluation_id'] = $evaluation_id_cible;
        $data['bloc_nb_questions'] = 0; // Les questions seront desactivees donc il faut repartir a 0.

        $this->db->insert('blocs', $data);

        $n_bloc_id = $this->db->insert_id();

        //
        // Copier les variables
        //

        if ( ! empty($variables_trouvees))
        {
            $data_batch = array();

            foreach($variables_trouvees as $v)
            {
                $data = $variables_orig[$v];

                unset($data['variable_id']);

                $data['evaluation_id'] = $evaluation_id_cible;
                $data['modification_epoch'] = $this->now_epoch;

                $data_batch[] = $data;

            }

            $this->db->insert_batch('variables', $data_batch);
        }

        //
        // Copier les questions
        //

        $questions_champs = $this->db->list_fields('questions');

        foreach($questions as $q)
        {
            $question_id = $q['question_id'];

            $data = $q;

            // Enlever les champs inexistants
            foreach($data as $k => $val)
            {
                if ( ! in_array($k, $questions_champs))
                    unset($data[$k]);
            }

            unset($data['question_id']);

            $data['evaluation_id'] = $evaluation_id_cible;
            $data['actif']         = 0;
            $data['bloc_id']       = $n_bloc_id;
            $data['ajout_par_enseignant_id'] = $this->enseignant_id;
            $data['ajout_date']    = date_humanize($this->now_epoch, TRUE);
            $data['ajout_epoch']   = $this->now_epoch;

            $this->db->insert('questions', $data);

            $n_question_id = $this->db->insert_id();

            //
            // Copier l'image (le document) de la question, si existante.
            //

            if (array_key_exists($q['question_id'], $images_toutes) && ! empty($images_toutes[$q['question_id']]))
            {
                $data = $images_toutes[$q['question_id']];

                unset($data['doc_id']);

                $data['question_id'] = $n_question_id;
                $data['ajout_date']  = date_humanize($this->now_epoch, TRUE);
                $data['ajout_epoch'] = $this->now_epoch;
                $data['ajout_par_enseignant_id'] = $this->enseignant_id;

                $this->db->insert('documents', $data);
            }

            //
            // Copier les reponses
            //

            if (array_key_exists($q['question_id'], $reponses_toutes) && ! empty($reponses_toutes[$q['question_id']]))
            {
                $reponses = $reponses_toutes[$q['question_id']];

                $questions_types_a_conserver = array(2, 3, 5);

                $reponses_champs = $this->db->list_fields('reponses');

                foreach($reponses as $r)
                {
                    if (empty($r))
                        continue;

                    $data = $r;

                    // Enlever les champs inexistants
                    foreach($data as $k => $val)
                    {
                        if ( ! in_array($k, $reponses_champs))
                            unset($data[$k]);
                    }

                    unset($data['reponse_id']);

                    $data['question_id']   = $n_question_id;
                    $data['ajout_date']    = date_humanize($this->now_epoch, TRUE);
                    $data['ajout_epoch']   = $this->now_epoch;
                    $data['ajout_par_enseignant_id'] = $this->enseignant_id;

                    unset($data['question_question_type']);

                    $this->db->insert('reponses', $data);
                }
            } // reponses

            //
            // Copier les tolerances
            //

            if ($q['question_type'] == 6)
            {
                $tolerances = array();
                $tolerances = $this->Question_model->extraire_tolerances($question_id);
    
                if ( ! empty($tolerances))
                {
                    $data_batch = array();

                    $champs = $this->db->list_fields('questions_tolerances');

                    foreach($tolerances as $t)
                    {
                        $data = $t;

                        foreach($data as $k => $val)
                        {
                            if ( ! in_array($k, $champs))
                                unset($data[$k]);
                        }

                        unset($data['tolerance_id']);
                        
                        $data['question_id'] = $n_question_id;

                        $data_batch[] = $data;
                    }
                }

                if ( ! empty($data_batch))
                {
                    $this->db->insert_batch('questions_tolerances', $data_batch);
                }
            }

            //
            // Copier les similarites
            //
        
            if ($q['question_type'] == 7)
            {
                $similarite = array();
                $similarite = $this->Question_model->extraire_similarite($question_id);

                if ( ! empty($similarite))
                {
                    $data = $similarite;

                    if (array_key_exists('similarite_id', $data))
                    {
                        unset($data['similarite_id']);
                    }

                    $data['question_id'] = $n_question_id;

                    $this->db->insert('questions_similarites', $data);
                }
            }

         } // foreach $questions

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            echo "QBC776611 : Une erreur est survenue lors du copiage du bloc et/ou de ses composantes.";
            return FALSE;
        }

        $this->db->trans_commit();

        return $evaluation_id_cible;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Assigner (une question) a un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function assigner_bloc($bloc_id, $question_id)
    {
        $question = $this->extraire_question($question_id);

        //
        // La question est deja assignee a ce bloc.
        //

        if ($question['bloc_id'] == $bloc_id)
            return TRUE;

        //
        // La question est un sondage donc elle ne peut pas etre assignee a un bloc.
        //

        if ($question['sondage'])
            return TRUE;

        $this->db->trans_begin();

        //
        // Changer l'association de la question au nouveau bloc
        //

        $data = array(
            'bloc_id' => $bloc_id
        );
            
        $this->db->where  ('question_id', $question_id);
        $this->db->update ('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Reduire le bloc
        // 
        // Si necessaire, reduire d'une unite le nombre de question a choisir dans le bloc.
        //

        if ($question['actif'] && ! empty($question['bloc_id']) && is_numeric($question['bloc_id']))
        {
            $ancien_bloc_id     = $question['bloc_id'];
            $ancien_bloc        = $this->extraire_bloc($question['bloc_id']);
            $nb_questions_total = $this->nb_questions_dans_bloc($ancien_bloc_id); // Le bloc contient X questions.
            $nb_questions_choisies = $ancien_bloc['bloc_nb_questions'];  // Le bloc doit choisir X questions.

            if ($nb_questions_choisies > $nb_questions_total)
            {
                // Reduire d'une unite
                $this->db->where ('bloc_id', $ancien_bloc_id);
                $this->db->set   ('bloc_nb_questions', 'bloc_nb_questions-1', FALSE);
                $this->db->update('blocs');

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();
                    return FALSE;
                }
            }
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Desassigner (une question) d'un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function desassigner_bloc($question_id)
    {
        $question = $this->extraire_question($question_id);

        $this->db->trans_begin();

        //
        // Enlever l'association de la question au bloc.
        //

        $data = array(
            'bloc_id' => NULL
        );
            
        $this->db->where  ('question_id', $question_id);
        $this->db->update ('questions', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Reduire le bloc
        // 
        // Si necessaire, reduire d'une unite le nombre de question a choisir dans le bloc.
        //

        if ($question['actif'] && ! empty($question['bloc_id']) && is_numeric($question['bloc_id']))
        {
            $ancien_bloc_id     = $question['bloc_id'];
            $ancien_bloc        = $this->extraire_bloc($question['bloc_id']);
            $nb_questions_total = $this->nb_questions_dans_bloc($ancien_bloc_id); // Le bloc contient X questions.
            $nb_questions_choisies = $ancien_bloc['bloc_nb_questions'];  // Le bloc doit choisir X questions.

            if ($nb_questions_choisies > $nb_questions_total)
            {
                // Reduire d'une unite
                $this->db->where ('bloc_id', $ancien_bloc_id);
                $this->db->set   ('bloc_nb_questions', 'bloc_nb_questions-1', FALSE);
                $this->db->update('blocs');

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();
                    return FALSE;
                }
            }
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_bloc($bloc_id)
    {
        $this->db->select  ('b.*');
        $this->db->from    ('blocs as b, evaluations as ev');
        $this->db->where   ('b.bloc_id', $bloc_id);
        $this->db->where   ('b.efface', 0);
        $this->db->where   ('b.bloc_actif', 1);
        $this->db->where   ('b.evaluation_id = ev.evaluation_id');
        $this->db->where   ('ev.groupe_id', $this->enseignant['groupe_id']);
        $this->db->limit   (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les blocs de questions
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_blocs($evaluation_id)
    {
        $this->db->from     ('blocs as b');
        $this->db->where    ('b.evaluation_id', $evaluation_id);
        $this->db->where    ('b.bloc_actif', 1);
        $this->db->where    ('b.efface', 0);
        $this->db->order_by ('b.bloc_label', 'asc');

        $query = $this->db->get();
        $count = $this->db->count_all_results();
        
        if ( ! $query->num_rows() > 0)
            return array();

        return array_keys_swap($query->result_array(), 'bloc_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner le nombre de questions dans un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function nb_questions_dans_bloc($bloc_id)
    {
        $this->db->from     ('questions as q');
        $this->db->where    ('q.efface', 0);
        $this->db->where    ('q.bloc_id', $bloc_id);
        $this->db->where    ('q.actif', 1);
        
        $query = $this->db->get();

        return $query->num_rows();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner le nombre de questions dans les blocs selectionnes
     *
     * -------------------------------------------------------------------------------------------- */
    function nb_questions_dans_blocs($bloc_ids = array())
    {
        $this->db->from     ('questions as q');
        $this->db->where    ('q.efface', 0);
        $this->db->where_in ('q.bloc_id', $bloc_ids);
        $this->db->where    ('q.actif', 1);
        
        $query = $this->db->get();
    
        if ( ! $query->num_rows() > 0)
        {
            $questions = array();
        }
        else
        {
            $questions = $query->result_array();
        }

        $blocs = array();

        // initiliaser le tableau a retourner

        foreach($bloc_ids as $bloc_id)
        {
            $blocs[$bloc_id] = 0;
        }

        // iterer a travers les questions pour trouver le compte

        foreach($questions as $q)
        {
            $blocs[$q['bloc_id']]++;
        }

        return $blocs;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Determiner le nombre de questions dans un bloc
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_questions_dans_blocs($bloc_ids = array(), $options = array())
    {
    	$options = array_merge(
        	array(
                'evaluation_id' => NULL, // champ obligatoire
                'actif' => TRUE
           ),
           $options
       	);

        $this->db->from     ('questions as q');
        $this->db->where    ('q.efface', 0);
        $this->db->where_in ('q.bloc_id', $bloc_ids);

        if ($options['actif'])
            $this->db->where ('q.actif', 1);
        
        $query = $this->db->get();
    
        if ( ! $query->num_rows() > 0)
        {
            $questions = array();
        }
        else
        {
            $questions = $query->result_array();
        }

        $liste = array();

        // initiliaser le tableau a retourner

        foreach($bloc_ids as $bloc_id)
        {
            $liste[$bloc_id] = array();
        }

        // iterer a travers les questions pour trouver le compte

        foreach($questions as $q)
        {
            $liste[$q['bloc_id']][] = $q['question_id'];
        }

        return $liste;
    }

    //
    // TOLERANCES
    //

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les tolerances
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_tolerances($question_id)
    {
        $this->db->from    ('questions_tolerances');
        $this->db->where   ('question_id', $question_id);
        $this->db->order_by('penalite', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $tolerances = $query->result_array();

		// Ordonner les tolerances en ordre croissant. 

		usort($tolerances, function($a, $b) {
			return $a['tolerance'] <=> $b['tolerance'];
		});

        return $tolerances;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter une tolerance
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_tolerance($question_id, $post_data)
    {
        if ( ! in_array('modifier', $this->Question_model->permissions_question($question_id)))
        {
            return FALSE;
        }

        $tolerance = str_replace(',', '.', trim($post_data['tolerance']));

        //
        // Verifier qu'une valeur de tolerance identique n'existe pas pour cette reponse
        //

        $tolerances = $this->extraire_tolerances($question_id);

        if ( ! empty($tolerances))
        {
            foreach($tolerances as $t)
            {
                if ($t['tolerance'] == $tolerance)
                {
                    return FALSE;
                }
            }
        }

        //
        // Ajouter la tolerance
        //

        $data = array(
            'question_id' => $question_id,
            'tolerance'  => $tolerance,
            'type'       => $post_data['type'],
            'penalite'   => trim($post_data['penalite'])
        );

        $this->db->insert('questions_tolerances', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une tolerance
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_tolerance($question_id, $tolerance_id)
    {
        if ( ! in_array('modifier', $this->Question_model->permissions_question($question_id)))
        {
            return FALSE;
        }

        $this->db->where('question_id', $question_id);
        $this->db->where('tolerance_id', $tolerance_id);

        $this->db->delete('questions_tolerances');

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire la similarite
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_similarite($question_id)
    {
        $this->db->from    ('questions_similarites');
        $this->db->where   ('question_id', $question_id);
        $this->db->limit   (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array(
                'question_id' => $question_id,
                'similarite'  => $this->config->item('questions_types')[7]['similarite']
            );
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les grilles de correction (et ses elements) d'une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_grilles_correction_par_evaluation_id($evaluation_id)
    {
        $this->db->from     ('questions_grilles_correction as gc, evaluations as e, questions as q');

        $this->db->select   ('gc.*');

        $this->db->where    ('e.evaluation_id', $evaluation_id);
        $this->db->where    ('q.evaluation_id = e.evaluation_id');
        $this->db->where    ('gc.question_id = q.question_id');

        $this->db->where    ('gc.efface', 0);
        $this->db->where    ('q.efface', 0);
        $this->db->where    ('e.efface', 0);

        // $this->db->where    ('e.enseignant_id', $this->enseignant_id);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $gc = $query->result_array();
        $gc = array_keys_swap($gc, 'question_id');

        $grille_ids = array_column($gc, 'grille_id');

        //
        // Extraire les elements
        //

        $this->db->from     ('questions_grilles_correction_elements as gce');
        $this->db->where_in ('gce.grille_id', $grille_ids);
        $this->db->where    ('gce.efface', 0);
        $this->db->order_by ('gce.element_ordre', 'asc');
        $this->db->order_by ('gce.element_id', 'asc');

        $query = $this->db->get();

        //
        // Preparer le tableau de sortie
        //

        if ($query->num_rows() > 0)
        {
            $elements = $query->result_array();

            foreach($elements as $e)
            {
                $q_id = $e['question_id'];

                if ( ! array_key_exists('elements', $gc[$q_id]))
                {
                    $gc[$q_id]['elements']    = array();
                    $gc[$q_id]['pourcentage'] = 0;
                }

                $gc[$q_id]['elements'][] = $e;

                if ($e['element_type'] == 1)
                {
                    $gc[$q_id]['pourcentage'] += $e['element_pourcent']; 
                }
            }
        }

        return $gc;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une grille de correction (et ses elements), d'une ou plusieurs questions
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_grilles_correction($question_ids = array())
    {
        if (empty($question_ids))
        {
            return array();
        }

        if ( ! is_array($question_ids))
        {
            $question_ids = array($question_ids);
        }

        $this->db->from     ('questions_grilles_correction as gc, evaluations as e, questions as q');
        $this->db->select   ('gc.*');
        $this->db->where_in ('gc.question_id', $question_ids);
        $this->db->where    ('gc.efface', 0);
        $this->db->where    ('gc.question_id = q.question_id');
        $this->db->where    ('q.efface', 0);
        $this->db->where    ('q.evaluation_id = e.evaluation_id');
        $this->db->where    ('e.efface', 0);
         
        // $this->db->where    ('e.enseignant_id', $this->enseignant_id);
        
        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $gc = $query->result_array();
        $gc = array_keys_swap($gc, 'question_id');

        $grille_ids = array_column($gc, 'grille_id');

        //
        // Extraire les elements
        //

        $this->db->from     ('questions_grilles_correction_elements as gce');
        $this->db->where_in ('gce.grille_id', $grille_ids);
        $this->db->where    ('gce.efface', 0);
        $this->db->order_by ('gce.element_ordre', 'asc');
        $this->db->order_by ('gce.element_id', 'asc');

        $query = $this->db->get();

        //
        // Preparer le tableau de sortie
        //

        if ($query->num_rows() > 0)
        {
            $elements = $query->result_array();

            foreach($elements as $e)
            {
                $q_id = $e['question_id'];

                if ( ! array_key_exists('elements', $gc[$q_id]))
                {
                    $gc[$q_id]['elements']    = array();
                    $gc[$q_id]['pourcentage'] = 0;
                }

                $gc[$q_id]['elements'][] = $e;

                if ($e['element_type'] == 1)
                {
                    $gc[$q_id]['pourcentage'] += $e['element_pourcent']; 
                }
            }
        }

        return $gc;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter (creer) une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_grille_correction($post_data)
    {
        // Champs obligatoires

        if ( ! array_key_exists('question_id', $post_data) || ! array_key_exists('evaluation_id', $post_data))
        {
            return FALSE;
        }        

        if ( ! empty($this->extraire_grilles_correction($post_data['question_id'])))
        {
            return FALSE;
        }

        $data = array(
            'evaluation_id'    => $post_data['evaluation_id'],
            'question_id'      => $post_data['question_id'],
            'grille_affichage' => $post_data['grille_affichage'],
            'ajout_date'       => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'      => $this->now_epoch
        );

        $this->db->insert('questions_grilles_correction', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Importer une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function importer_grille_correction($post_data)
    {       
        // $post_data['question_id']         => L'endroit ou copier la grille.
        // $post_data['question_id_origine'] => La question qui contient la grille a copier.

        //
        // Extraire les informations de la question, et l'evaluation
        //

        $this->db->select   ('q.*, e.evaluation_id, e.enseignant_id, e.public');
        $this->db->from     ('questions as q, evaluations as e');
        $this->db->where    ('q.question_id', $post_data['question_id_origine']);
        $this->db->where    ('q.evaluation_id = e.evaluation_id');
        $this->db->where    ('q.efface', 0);
        $this->db->where    ('e.efface', 0);
        $this->db->limit    (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $row = $query->row_array();

        if ( ! $row['public'] && $row['enseignant_id'] != $this->enseignant_id)
        {       
            return FALSE;
        }

        //
        // Extraire la grille
        //

        $this->db->from   ('questions_grilles_correction as gc');
        $this->db->where  ('gc.question_id', $post_data['question_id_origine']);
        $this->db->where  ('gc.efface', 0);
        $this->db->limit  (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }
                                                                                                                                                                                                                                  
        $grille = $query->row_array();
        
        //
        // Extraire les elements de la grille
        //

        $this->db->from   ('questions_grilles_correction_elements as ge');
        $this->db->where  ('ge.question_id', $post_data['question_id_origine']);
        $this->db->where  ('ge.efface', 0);

        $query = $this->db->get();

        $elements = array();

        if ($query->num_rows() > 0)
        {
            $elements = $query->result_array();
        }

        //
        // Importation
        //

        $this->db->trans_begin();

        //
        // Importer la grille
        //

        $data = $grille;

        unset($data['grille_id']);

        $data['ajout_date']    = date_humanize($this->now_epoch, TRUE);
        $data['ajout_epoch']   = $this->now_epoch;
        $data['question_id']   = $post_data['question_id'];
        $data['evaluation_id'] = $post_data['evaluation_id'];

        $this->db->insert('questions_grilles_correction', $data);

        $grille_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Importer les elements de la grille
        //

        if ( ! empty($elements) && is_array($elements))
        {
            $data = array();

            foreach($elements as $e)
            {
                unset($e['element_id']);

                $e['grille_id']   = $grille_id;
                $e['question_id'] = $post_data['question_id'];
                $e['ajout_date']  = date_humanize($this->now_epoch, TRUE);
                $e['ajout_epoch'] = $this->now_epoch;

                $data[] = $e;
            }

            $this->db->insert_batch('questions_grilles_correction_elements', $data);

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_grille_correction($post_data)
    {
        // Champs obligatoires

        if ( ! array_key_exists('question_id', $post_data) || ! array_key_exists('grille_id', $post_data))
        {
            return FALSE;
        }        

        $question_id = $post_data['question_id'];
        $grille_id   = $post_data['grille_id'];

        $gc = $this->extraire_grilles_correction($question_id);
       
        if (empty($gc))
        {
            return FALSE;
        }

        if ($gc[$question_id]['grille_id'] != $grille_id)
        {
            return FALSE;
        }

        $data = array(
            'grille_affichage' => $post_data['grille_affichage']
        );

        $this->db->where ('grille_id', $grille_id);
        $this->db->update('questions_grilles_correction', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une grille de correction ET tous ses elements
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_grille_correction($question_id)
    {
        $gc = $this->extraire_grilles_correction($question_id);
       
        if (empty($gc))
        {
            // La grille est introuvable.
            return 9; 
        }

        //
        // Effacer la grille
        //

        $this->db->trans_begin();

        $data = array(
            'efface'        => 1,
            'efface_date'   => date_humanize($this->now_epoch, TRUE),
            'efface_epoch'  => $this->now_epoch
        );

        $this->db->where ('question_id', $question_id);
        $this->db->where ('efface', 0);
        $this->db->update('questions_grilles_correction', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Effacer es elements
        //

        $elements = $this->extraire_elements($question_id);

        if ( ! empty($elements))
        {
            $data = array();

            foreach($elements as $e)
            {
                $data[] = array(
                    'element_id'   => $e['element_id'],
                    'efface'       => 1,
                    'efface_date'  => date_humanize($this->now_epoch, TRUE),
                    'efface_epoch' => $this->now_epoch
                );
            }

            $this->db->update_batch('questions_grilles_correction_elements', $data, 'element_id');

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        } 

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un element a une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_element_grille($post_data)
    {
        // Champs obligatoires

        if ( ! array_key_exists('question_id', $post_data) || ! array_key_exists('grille_id', $post_data))
        {
            return FALSE;
        }        

        $question_id = $post_data['question_id'];
        $grille_id   = $post_data['grille_id'];

        $gc = $this->extraire_grilles_correction($question_id);
       
        if (empty($gc))
        {
            return FALSE;
        }

        if ($gc[$question_id]['grille_id'] != $grille_id)
        {
            return FALSE;
        }

        $data = array(
            'question_id'      => $post_data['question_id'],
            'grille_id'        => $post_data['grille_id'],
            'element_desc'     => trim($post_data['element_desc']),
            'element_type'     => $post_data['element_type'],
            'element_ordre'    => $post_data['element_ordre'],
            'element_pourcent' => $post_data['element_pourcent'],
            'ajout_date'       => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'      => $this->now_epoch
        );

        $this->db->insert('questions_grilles_correction_elements', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Dupliquer un element a une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function dupliquer_element_grille($post_data)
    {
        // Champs obligatoires

        if ( ! array_key_exists('question_id', $post_data) || 
             ! array_key_exists('grille_id', $post_data)   ||
             ! array_key_exists('element_id', $post_data)
           )
        {
            return FALSE;
        }        

        $question_id = $post_data['question_id'];
        $grille_id   = $post_data['grille_id'];
        $element_id  = $post_data['element_id'];

        $gc = $this->extraire_grilles_correction($question_id);
       
        if (empty($gc))
        {
            return FALSE;
        }

        if ($gc[$question_id]['grille_id'] != $grille_id)
        {
            return FALSE;
        }

        //
        // Extraire l'element
        //

        $this->db->from   ('questions_grilles_correction_elements');
        $this->db->where  ('question_id', $question_id);
        $this->db->where  ('grille_id',   $grille_id);
        $this->db->where  ('element_id',  $element_id);
        $this->db->where  ('efface',      0);
        $this->db->limit  (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $element = $query->row_array();

        $data = array(
            'question_id'       => $element['question_id'],
            'grille_id'         => $element['grille_id'],
            'element_type'      => $element['element_type'],
            'element_desc'      => $element['element_desc'],
            'element_ordre'     => $element['element_ordre'],
            'element_pourcent'  => $element['element_pourcent'],
            'ajout_date'        => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'       => $this->now_epoch,
        );

        $this->db->insert('questions_grilles_correction_elements', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier un element a une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_element_grille($post_data)
    {
        // Champs obligatoires

        if ( ! array_key_exists('question_id', $post_data) || 
             ! array_key_exists('grille_id', $post_data)   ||
             ! array_key_exists('element_id', $post_data)
           )
        {
            return FALSE;
        }        

        $question_id = $post_data['question_id'];
        $grille_id   = $post_data['grille_id'];
        $element_id  = $post_data['element_id'];

        $gc = $this->extraire_grilles_correction($question_id);
       
        if (empty($gc))
        {
            return FALSE;
        }

        if ($gc[$question_id]['grille_id'] != $grille_id)
        {
            return FALSE;
        }

        //
        // Extraire l'element
        //

        $this->db->from   ('questions_grilles_correction_elements');
        $this->db->where  ('question_id', $question_id);
        $this->db->where  ('grille_id',   $grille_id);
        $this->db->where  ('element_id',  $element_id);
        $this->db->limit  (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $element = $query->row_array();

        //
        // Modifier
        //

        if (array_key_exists('element_desc', $post_data))
        {
            $post_data['element_desc'] = trim($post_data['element_desc']);
        }

        $data = array();

        $champs_modifiables = array('element_desc', 'element_type', 'element_ordre', 'element_pourcent');

        foreach($element as $k => $v)
        {
            if ( ! in_array($k, $champs_modifiables))
                continue;

            if (array_key_exists($k, $post_data) && $post_data[$k] != $v)
            {
                $data[$k] = $post_data[$k];
            }
        } 

        if ( ! empty($data))
        {
            $this->db->where ('element_id', $element_id);
            $this->db->update('questions_grilles_correction_elements', $data);
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les elements d'une grille
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_elements($question_id)
    {
        $this->db->from     ('questions_grilles_correction_elements');
        $this->db->where    ('question_id', $question_id);
        $this->db->where    ('efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
        
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un element d'une grille de correction
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_element_grille($post_data)
    {
        // Champs obligatoires

        if ( ! array_key_exists('question_id', $post_data) || 
             ! array_key_exists('grille_id', $post_data)   ||
             ! array_key_exists('element_id', $post_data)
           )
        {
            return FALSE;
        }        

        $question_id = $post_data['question_id'];
        $grille_id   = $post_data['grille_id'];
        $element_id  = $post_data['element_id'];

        $gc = $this->extraire_grilles_correction($question_id);
       
        if (empty($gc))
        {
            return FALSE;
        }

        if ($gc[$question_id]['grille_id'] != $grille_id)
        {
            return FALSE;
        }

        //
        // Effacer l'element
        //

        $data = array(
            'efface'       => 1,
            'efface_date'  => date_humanize($this->now_epoch, TRUE),
            'efface_epoch' => $this->now_epoch
        );

        $this->db->where ('element_id', $element_id);
        $this->db->update('questions_grilles_correction_elements', $data);

        return TRUE;
    }

}
