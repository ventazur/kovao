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
 * REPONSE MODEL
 *
 * ============================================================================ */

class Reponse_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une reponse
     *
     * --------------------------------------------------------------------------------------------
     * 
     * Cette fonction est concue pour ces types de questions :
     *
     * - Question a reponse numerique entiere      (TYPE 5)
     * - Question a reponse numerique              (TYPE 6)
     * - Question a reponse litterale courte       (TYPE 7)
     * - Question a reponse numerique par equation (TYPE 9)
     * 
     * -------------------------------------------------------------------------------------------- */
    function extraire_reponse($question_id)
    {
        $this->db->from     ('reponses as r, questions as q');
        $this->db->select   ('r.*, q.question_type as question_question_type');
        $this->db->where    ('r.question_id', $question_id);
        $this->db->where    ('r.efface', 0);
        $this->db->where    ('r.question_id = q.question_id');
        $this->db->where    ('r.question_type = q.question_type');
        $this->db->where_in ('r.question_type', array(5, 6, 7, 9));

        $query = $this->db->get();

        if ( ! $query->num_rows())
            return FALSE;

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une reponse unique
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_reponse_unique($reponse_id)
    {
        $this->db->from     ('reponses as r');
        $this->db->where    ('r.reponse_id', $reponse_id);
        $this->db->where    ('r.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
            return FALSE;

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des reponses (VERSION 2, 2018-12-23)
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_reponses($question_id, $options = array())
    {
    	$options = array_merge(
        	array(
                'filter_symbols' => FALSE
           ),
           $options
       	);

        $this->db->from   ('reponses as r, questions as q');
        $this->db->select ('r.*, q.question_type as question_question_type');
        $this->db->where  ('r.question_id', $question_id);
        $this->db->where  ('r.efface', 0);
        $this->db->where  ('r.question_id = q.question_id');
        $this->db->where  ('q.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $reponses = array_keys_swap($query->result_array(), 'reponse_id');

        foreach($reponses as $reponse_id => $r)
        {
            // Si la question est une questions a coefficients variables...
            // Elles doivent avoir des equations comme reponses.
            if ($r['question_question_type'] == 3)
            {
                if ( ! $r['equation'])
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            // Si la question est une reponse numerique, il faut que le question_type de la 
            // reponse soit defini a 5.
            if ($r['question_question_type'] == 5)
            {
                if ($r['question_type'] != 5)
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            // Si la question est une reponse numerique, il faut que le question_type de la 
            // reponse soit defini a 6.
            if ($r['question_question_type'] == 6)
            {
                if ($r['question_type'] != 6)
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            // Si la question est une reponse litterale courte, il faut que le question_type de la 
            // reponse soit defini a 7.
            if ($r['question_question_type'] == 7)
            {
                if ($r['question_type'] != 7)
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            // Si la question est une reponse numerique, il faut que le question_type de la 
            // reponse soit defini a 5.
            if ($r['question_question_type'] == 9)
            {
                if ($r['question_type'] != 9)
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            //
            // Si la question n'est pas une question a coefficients variables...
            // Les equations ne peuvent pas etre des reponses aux autres types de questions.
            if ($r['equation'])
            {
                $types_equations_permises = array(3, 9);

                if ( ! in_array($r['question_question_type'], $types_equations_permises))
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            // Retrocompatibilite avant 2018-12-23
            // Ne pas permettre les anciennes reponses d'etre affichees pour plusieurs types de questions (choix unique et choix multiples)
            // car ceci pourrait creer des doublons dans les reponses d'anciennes evaluations.
            if ( ! empty($r['question_type']))
            {
                if ($r['question_type'] != $r['question_question_type'])
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            if ($options['filter_symbols'])
            {
                $reponses[$reponse_id]['reponse_texte'] = filter_symbols($r['reponse_texte']);
            }
        }

        return $reponses;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister les reponses de plusieurs questions simultanement (pour l'editeur d'evaluation)
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_reponses_toutes($question_ids = array(), $options = array())
    {
    	$options = array_merge(
        	array(
                'filter_symbols' => FALSE
           ),
           $options
        );

        if (empty($question_ids))
        {
            return array();
        }

        $this->db->from     ('reponses as r, questions as q');
        $this->db->select   ('r.*, q.question_type as question_question_type');
        $this->db->where_in ('r.question_id', $question_ids);
        $this->db->where    ('r.efface', 0);
        $this->db->where    ('r.question_id = q.question_id');
        $this->db->where    ('q.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $reponses  = array_keys_swap($query->result_array(), 'reponse_id');
        $questions = array();

        foreach($reponses as $reponse_id => $r)
        {
            $question_id = $r['question_id'];

            if ( ! array_key_exists($question_id, $questions))
            {
                $questions[$question_id] = array();
            }

            //
            // Verifier que les reponses possedent le type de question concordant
            //
            // Exceptions : 
            // - Question a choix unique (TYPE 1)
            // - Question a choix multiples (TYPE 4)
            // - Question a choix multiples stricte (TYPE 11)
            //

            if ( ! in_array($r['question_question_type'], array(1, 4, 11)))
            {
                // question_question_type = question
                // question_type = reponse

                if ($r['question_question_type'] != $r['question_type'])
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }
            else
            {
                // Retrocompatibilite avant 2018-12-23

                // Pour les anciennes questions a choix unique et choix multiples,
                // ne pas permettre les anciennes reponses d'etre affichees pour les deux types de questions 
                // car ceci pourrait creer des doublons dans les reponses d'anciennes evaluations.

                if ( ! empty($r['question_type']))
                {
                    if ($r['question_question_type'] != $r['question_type'])
                    {
                        unset($reponses[$reponse_id]);
                        continue;
                    }
                }
            }

            //
            // Question a choix unique par equations (TPE 3)
            // Question a reponse numerique par equations (TYPE 9)
            //
            // Ces questions doivent avoir des equations comme reponses.
            //

            if (in_array($r['question_question_type'], array(3, 9)))
            {
                if ( ! $r['equation'])
                {
                    unset($reponses[$reponse_id]);
                    continue;
                }
            }

            if ($options['filter_symbols'])
            {
                $reponses[$reponse_id]['reponse_texte'] = filter_symbols($r['reponse_texte']);
            }

            $questions[$question_id][$reponse_id] = $r;
        }

        return $questions;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter une reponse
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_reponse($question_id, $post_data)
    {
        if ( ! in_array('ajouter_reponse', $this->Question_model->permissions_question($question_id)))
        {
            return FALSE;
        }

        // Ne pas associer le type des questions pour ces questions :
        // - Question a choix unique (TYPE 1) 
        // - Question a choix multiples (TYPE 4)
        // - Question a choix multiples stricte (TYPE 11)
        // afin que l'enseignant puisse passer d'un type a l'autre sans devoir reentrer toutes les reponses.

        $question_type = in_array($post_data['question_type'], array(1, 4, 11)) ? NULL : $post_data['question_type'];

        //
        // Verifier les tags du texte de la question.
        //

        $post_data['reponse_texte'] = trim($post_data['reponse_texte']);

        if (in_array($question_type, array(1, 4, 11)))
        {
            $post_data['reponse_texte'] = verifier_tags($post_data['reponse_texte']);
        }

        // Preparer les donnees

        $data = array(
            'question_id'      => $post_data['question_id'],
            'question_type'    => $question_type,
            'reponse_texte'    => $post_data['reponse_texte'],
            'equation'         => $post_data['equation'],
            'unites'           => ( ! isset($post_data['unites']) ? NULL : $post_data['unites']),
            'cs'               => ( ! isset($post_data['cs']) ? NULL : $post_data['cs']),
            'notsci'           => ( ! isset($post_data['notsci']) || empty($post_data['notsci']) ? 0 : 1), 
            'reponse_correcte' => ($post_data['reponse_type'] == 1 ? 1 : 0),
            'ajout_date'       => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'      => $this->now_epoch,
            'ajout_par_enseignant_id' => $this->enseignant['enseignant_id']
        );

        $this->db->insert('reponses', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        //
        // Pour la question a reponse litterale courte (TYPE 7),
        // ajouter la similarite par default 
        //

        if ($question_type == 7)
        {
            $this->Reponse_model->modifier_similarite(
                $question_id,
                $this->config->item('questions_types')[7]['similarite']
            );
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier une reponse (ou une equation)
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_reponse($reponse_id, $post_data)
    {
        //
        // Verifier que la question peut etre modifiee par cet enseignant.
        //

        $question_id = $post_data['question_id'];

        if ( ! in_array('modifier', $this->Question_model->permissions_question($question_id)))
        {
            return FALSE;
        }

        $question = $this->Question_model->extraire_question($question_id);

        //
        // Verifier les tags du texte de la question.
        //

        $post_data['reponse_texte'] = trim($post_data['reponse_texte']);

        if (in_array($question['question_type'], array(1, 4, 11)))
        {
            $post_data['reponse_texte'] = verifier_tags($post_data['reponse_texte']);
        }

        // Collecter les donnees actuelles de facon effectuer seulement les modifications.

        $this->db->from ('reponses as r');
        $this->db->where('r.reponse_id', $reponse_id);
        $this->db->where('r.question_id', $question_id);
        $this->db->where('r.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $reponse = $query->row_array();

        // Parfois les checkbox non coches n'apparaissent pas dans $post_data;
        if ( ! array_key_exists('notsci', $post_data))
        {
            $post_data['notsci'] = 0;
        }
        else
        {
            $post_data['notsci'] = 1;
        }

        $data = array();

        foreach($post_data as $key => $val)
        {
            if (array_key_exists($key, $reponse))
            {
                if ($reponse[$key] != $post_data[$key])
                {
                    $data[$key] = $post_data[$key];
                }
            }
        }

        if ( ! empty($data))
        {
            $this->db->where ('reponse_id', $reponse_id);
            $this->db->update('reponses', $data);

            if ( ! $this->db->affected_rows())
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier similarite
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_reponse_toggle($question_id, $reponse_id, $reponse_correcte = 0)
    {
        if ( ! in_array('modifier', $this->Question_model->permissions_question($question_id)))
        {
            return FALSE;
        }

        $data = array(
            'reponse_correcte' => $reponse_correcte == 1 ? 0 : 1
        );

        $this->db->where    ('question_id', $question_id);
        $this->db->where    ('reponse_id', $reponse_id);
        $this->db->update   ('reponses', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier une equation
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_equation($reponse_id, $post_data)
    {
        //
        // Verifier que la question peut etre modifiee par cet enseignant.
        //

        $question_id = $post_data['question_id'];

        $this->db->from  ('questions as q, evaluations as e, cours as c');
        $this->db->where ('q.question_id', $question_id);
        $this->db->where ('q.evaluation_id = e.evaluation_id');
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        if ($this->enseignant['groupe_id'] != $question['groupe_id'])
        {
            // Cet enseignant n'a pas la permission de modifier une reponse de cette evaluation.

            return FALSE;
        }

        //
        // Modifier l'equation
        //

        // Collecter les donnees actuelles de facon effectuer seulement les modifications.

        $this->db->from ('reponses as r');
        $this->db->where('r.reponse_id', $reponse_id);
        $this->db->where('r.question_id', $question_id);
        $this->db->where('r.equation', 1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $reponse = $query->row_array();

        $data = array();

        foreach($post_data as $key => $val)
        {
            if (array_key_exists($key, $reponse))
            {
                if ($reponse[$key] != $post_data[$key])
                {
                    $data[$key] = $post_data[$key];
                }
            }
        }

        if ( ! empty($data))
        {
            $this->db->where ('reponse_id', $reponse_id);
            $this->db->update('reponses', $data);

            if ( ! $this->db->affected_rows())
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une equation
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_equation($question_id, $reponse_id)
    {
        //
        // Verifier que la reponse peut etre effacee par cet enseignant.
        //

        $this->db->from  ('reponses as r, questions as q, evaluations as e, cours as c');
        $this->db->where ('r.reponse_id', $reponse_id);
        $this->db->where ('r.question_id', $question_id);
        $this->db->where ('r.equation', 1);
        $this->db->where ('r.question_id = q.question_id');
        $this->db->where ('q.evaluation_id = e.evaluation_id');
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        if ($this->enseignant['groupe_id'] != $question['groupe_id'])
        {
            // Cet enseignant n'a pas la permission de modifier une reponse de cette evaluation.

            return FALSE;
        }

        $this->db->where('reponse_id', $reponse_id);
        $this->db->delete('reponses');

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le type de la reponse
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_reponse_type($reponse_id, $post_data)
    {
        //
        // Verifier que la reponse peut etre changee par cet enseignant.
        //

        $this->db->from  ('reponses as r, questions as q, evaluations as e, cours as c');
        $this->db->where ('r.reponse_id', $reponse_id);
        $this->db->where ('r.question_id = q.question_id');
        $this->db->where ('q.evaluation_id = e.evaluation_id');
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $question = $query->row_array();

        if ($this->enseignant['groupe_id'] != $question['groupe_id'])
        {
            // Cet enseignant n'a pas la permission d'effacer cette question.
            // Cet enseignant n'appartient pas au groupe qui est le proprietaire de cette question.

            return FALSE;
        }

        //
        // effectuer les changements
        //

        // ATTN: type 1 = correcte, type 2 = erronee (type 2 = valeur 0 dans la base de donnee)

        $reponse_correcte = ($post_data['reponse_type'] == 1 ? 1 : 0);

        $this->db->set('reponse_correcte', $reponse_correcte);
        $this->db->where ('reponse_id', $reponse_id);
        $this->db->update('reponses');

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une reponse
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_reponse($reponse_id)
    {
        //
        // Verifier que la reponse peut etre effacee par cet enseignant.
        //
       
        $this->db->from  ('reponses as r, questions as q, evaluations as e, cours as c');
        $this->db->where ('r.reponse_id', $reponse_id);
        $this->db->where ('r.efface', 0);
        $this->db->where ('r.question_id = q.question_id');
        $this->db->where ('q.efface', 0);

        if ( ! permis('editeur'))
            $this->db->where('q.ajout_par_enseignant_id', $this->enseignant['enseignant_id']);

        $this->db->where ('q.evaluation_id = e.evaluation_id');
        $this->db->where ('e.groupe_id', $this->enseignant['groupe_id']);

        $this->db->where ('q.evaluation_id = e.evaluation_id');
        $this->db->where ('e.cours_id = c.cours_id');
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $data = array(
            'efface'       => 1,
            'efface_epoch' => $this->now_epoch,
            'efface_date'  => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->where  ('reponse_id', $reponse_id);
        $this->db->update ('reponses', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer plusieurs reponses
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_reponses($question_id, $reponse_ids)
    {
        if (empty($reponse_ids))
        {
            return TRUE;
        }

        //
        // Verifier que la reponse peut etre effacee par cet enseignant.
        //
       
        $this->db->from   ('evaluations as e, questions as q, reponses as r');
        $this->db->select ('r.reponse_id');
        $this->db->where  ('q.efface', 0);
        $this->db->where  ('r.efface', 0);
        $this->db->where  ('r.question_id = q.question_id');
        $this->db->where  ('q.evaluation_id = e.evaluation_id');
        $this->db->where  ('e.groupe_id', $this->enseignant['groupe_id']);
        $this->db->where_in ('r.reponse_id', $reponse_ids);

        if ( ! permis('editeur'))
        {
            $this->db->where('q.ajout_par_enseignant_id', $this->enseignant['enseignant_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $reponses_reponse_ids = array_column($query->result_array(), 'reponse_id');

        if (empty($reponses_reponse_ids) || empty($reponse_ids))
        {
            return FALSE;
        }

        $this->db->trans_begin();

        $data = array();

        foreach($reponse_ids as $r_id)
        {
            // Cette verification est probablement inutile.
            if ( ! in_array($r_id, $reponses_reponse_ids)) 
                continue;

            $data[] = array(
                'reponse_id'   => $r_id,
                'efface'       => 1,
                'efface_epoch' => $this->now_epoch,
                'efface_date'  => date_humanize($this->now_epoch, TRUE)
            );
        }

        if ( ! empty($data))
        {
            $this->db->update_batch ('reponses', $data, 'reponse_id');
        }

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
     * Effacer les reponses non selectionnees
     *
     * -------------------------------------------------------------------------------------------
     *
     * Dans l'editeur d'evaluation pour les questions a choix multiples ayant plusieurs reponses (> 6),
     * il est possible de selectionner les reponses que l'on veut conserver pour effacer les autres.
     * Ceci est pratique lorsqu'on cree un banque de questions et qu'on decide les reponses a
     * conserver.
     *
     * (!) Pour les questions a choix multiple seulement.
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_reponses_non_selectionnees($evaluation_id, $question_id, $reponse_ids)
    {
        //
        // Extraire toutes les reponses de la question
        //

        $reponses = $this->lister_reponses($question_id);

        //
        // Verifier que les reponses a conserver soi
        //

        $reponses_reponse_ids = array_keys($reponses);

        $intersec = array_intersect($reponse_ids, $reponses_reponse_ids);

        if ($reponse_ids == $intersec)
        {
            $diff = array_diff($reponses_reponse_ids, $reponse_ids);

            //
            // Effacer toutes les reponses non selectionnees
            //
        
            if ($this->effacer_reponses($question_id, $diff))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier similarite
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_similarite($question_id, $similarite)
    {
        $this->db->from     ('questions_similarites');
        $this->db->where    ('question_id', $question_id);
        $this->db->limit    (1);
        
        $query = $this->db->get();

        $ajouter = FALSE;

        if ( ! $query->num_rows() > 0)
        {
            $ajouter = TRUE;
        }

        $data = array(
            'similarite' => $similarite
        );
            
        if ($ajouter)
        {
            //
            // Ajouter une nouvelle similarite
            //

            $data['question_id'] = $question_id;

            $this->db->insert('questions_similarites', $data);
        }

        else
        {
            $row = $query->row_array();

            //
            // Modifier une similarite existante
            //

            $this->db->where('question_id', $question_id);
            $this->db->update('questions_similarites', $data);
        }
        
        return TRUE; 
    }
}
