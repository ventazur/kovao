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
 * FORUMS MODEL
 *
 * ----------------------------------------------------------------------------
 *
 * forums_notifications              : la derniere fois entre dans les forums
 * forums_notifications_messages     : les messages lus
 * forums_notifications_commentaires : les messages suivis
 *
 * ============================================================================ */

class Forums_model extends CI_Model 
{

	function __construct()
	{
        parent::__construct();
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les messages IDs lus
     *
     * ------------------------------------------------------------------------ */
    function message_ids_lus()
    {
        $this->db->from  ('forums_notifications_messages_lus');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('groupe_id', $this->groupe_id);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return array_column($query->result_array(), 'message_id'); 
    }

    /* ------------------------------------------------------------------------
     *
     * Compter les nouveaux messages depuis la derniere lecture.
     *
     * ------------------------------------------------------------------------ */
    function nouveaux_messages_compte()
    {
        /*
        $cache_key = __FUNCTION__ . md5($this->enseignant_id . '_' . $this->groupe_id);

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }
        */

        //
        // Les forums sont actives ou desactives
        //

        if ( ! $this->config->item('forums'))
        {
            return 0;
        }

        $intervalle_max = $this->config->item('forums_intervalle_max') ?? 60*60*24*7;

        // Verifier si l'enseignant a une entree dans les notifications des forums

        $this->db->from  ('forums_messages');
        $this->db->where ('enseignant_id !=', $this->enseignant_id); // Ne pas presenter les messages de l'auteur comme nouveau
        $this->db->where ('ajout_epoch >', ($this->now_epoch - $intervalle_max));
        $this->db->where ('groupe_id', $this->groupe_id);
        $this->db->where ('efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            // Il n'y a aucun message.

            return 0;
        }

        // Tous les messages du groupe

        $message_ids = array_column($query->result_array(), 'message_id');

        // Tous les messages lus

        $message_ids_lus = $this->message_ids_lus();

        // Intersect

        $message_ids_non_lus = array_diff($message_ids, $message_ids_lus);

        $count = count($message_ids_non_lus);

        // $this->kcache->save($cache_key, $count, 'forums', 30);

        return $count;
    }

    /* ------------------------------------------------------------------------
     *
     * Compter les nouveaux commentaires pour les messages suivis
     *
     * ------------------------------------------------------------------------ */
    function nouveaux_commentaires_compte()
    {
        /*
        $cache_key = __FUNCTION__ . md5($this->enseignant_id . '_' . $this->groupe_id);

        if (($cache = $this->kcache->get($cache_key)) !== FALSE)
        {
            return $cache;
        }
        */

        //
        // Les commentaires sont actives ou desactives
        //

        if ( ! $this->config->item('forums') || ! $this->config->item('forums_commentaires'))
        {
            return 0;
        }

        // Extraire les messages suivis

        $messages_suivis = $this->messages_suivis();

        if (empty($messages_suivis))
        {
            // Il n'y a aucun message suivi.
            return 0;
        }

        $message_ids = array_column($messages_suivis, 'message_id');

        // Extraire les commentaires de ces messages

        $commentaires = $this->extraire_commentaires($message_ids);

        if (empty($commentaires))
        {
            // Les messages suivis n'ont aucun commentaire.

            return 0;
        }

        $count = 0;

        foreach($commentaires as $message_id => $c)
        {
            if ( ! array_key_exists($message_id, $messages_suivis))
                continue;

            if ( ! empty($c))
            {
                foreach($c as $cm)
                {
                    if ($messages_suivis[$message_id]['derniere_lecture_epoch'] > $cm['ajout_epoch'])
                        continue;

                    if ($cm['enseignant_id'] == $this->enseignant_id)
                        continue;

                    $count++;
                }
            }
        }

        // $this->kcache->save($cache_key, $count, 'forums', 30);

        return $count;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les messages
     *
     * ------------------------------------------------------------------------ */
    function extraire_messages()
    {
        $this->db->from  ('forums_messages as fm, enseignants as e');
        $this->db->select('fm.*, e.nom, e.prenom, e.genre');
        $this->db->where ('fm.groupe_id', $this->groupe_id);
        $this->db->where ('fm.enseignant_id = e.enseignant_id');
        $this->db->where ('fm.efface', 0);
        $this->db->order_by ('fm.ajout_date', 'desc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $messages = array_keys_swap($query->result_array(), 'message_id');

        return $messages;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire un message
     *
     * ------------------------------------------------------------------------ */
    function extraire_message($message_id)
    {
        $this->db->from  ('forums_messages as fm, enseignants as e');
        $this->db->select('fm.*, e.nom, e.prenom, e.genre');
        $this->db->where ('fm.message_id', $message_id);
        $this->db->where ('fm.groupe_id', $this->groupe_id);
        $this->db->where ('fm.enseignant_id = e.enseignant_id');
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return $query->row_array();
    }

    /* ------------------------------------------------------------------------
     *
     *  Extraire les messages suivis
     *
     * ------------------------------------------------------------------------ */
    function messages_suivis($message_id = NULL)
    {
        $this->db->from  ('forums_notifications_messages_suivis');
        $this->db->where ('enseignant_id', $this->enseignant_id);

        if ( ! empty($message_id))
        {
            // Lorsque le message_id est specifie, 
            // cette methode sert seulement a verifier si ce message est deja suivi.
            $this->db->where ('message_id', $message_id);
        }

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'message_id');
    }

    /* ------------------------------------------------------------------------
     *
     *  Mettre a jour le message suivi
     *
     * ------------------------------------------------------------------------
     *
     * Si ce n'est pas un message suivi, retourne FALSE
     * Si c'est un message suivi, mettre a jour et retourne TRUE
     *
     * ------------------------------------------------------------------------ */
    function lecture_message_suivi($message_id)
    {
        $this->db->from  ('forums_notifications_messages_suivis');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('message_id', $message_id);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            // Ce message n'est pas suivi donc rien a mettre a jour.
            return FALSE;
        }

        $data = array(
            'derniere_lecture_date'  => date_humanize($this->now_epoch, TRUE),
            'derniere_lecture_epoch' => $this->now_epoch
        );

        $this->db->where  ('enseignant_id', $this->enseignant_id);
        $this->db->where  ('message_id', $message_id);
        $this->db->update ('forums_notifications_messages_suivis', $data);

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Marquer un message comme lu
     *
     * ------------------------------------------------------------------------ */
    function marquer_message_lu($message_id)
    {
        $this->db->from  ('forums_notifications_messages_lus');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('groupe_id', $this->groupe_id);
        $this->db->where ('message_id', $message_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return TRUE;
        }

        $data = array(
            'enseignant_id' => $this->enseignant_id,
            'groupe_id'     => $this->groupe_id,
            'message_id'    => $message_id
        );

        $this->db->insert('forums_notifications_messages_lus', $data);        
        
        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Publier un message
     *
     * ------------------------------------------------------------------------ */
    function publier_message($post_data)
    {
        $this->db->trans_begin();

        $data = array(
            'titre'                  => _html_in($post_data['titre'], array('strip_tags' => TRUE)),
            'contenu'                => _html_in($post_data['message']),
            'permettre_commentaires' => $post_data['permettre_commentaires'],
            'groupe_id'              => $this->groupe_id,
            'enseignant_id'          => $this->enseignant_id,
            'ajout_epoch'            => $this->now_epoch,
            'ajout_date'             => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->insert('forums_messages', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $n_message_id = $this->db->insert_id();

        // 
        // Activer le suivi des commentaires pour ce message
        //

        $data = array(
            'enseignant_id'          => $this->enseignant_id,
            'message_id'             => $n_message_id,
            'derniere_lecture_date'  => date_humanize($this->now_epoch, TRUE),
            'derniere_lecture_epoch' => $this->now_epoch
        );

        $this->db->insert('forums_notifications_messages_suivis', $data);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Modifier un message
     *
     * ------------------------------------------------------------------------ */
    function modifier_message($message_id, $post_data)
    {
        $message = $this->extraire_message($message_id);

        if ($message['enseignant_id'] != $this->enseignant_id)
        {
            generer_erreur('FMOD4512', "Ce message ne peut etre modifie par cet enseignant.");
            return;
        }

        if ($message['titre'] == $post_data['titre'] &&
            $message['contenu'] == $post_data['message'] &&
            $message['permettre_commentaires'] == $post_data['permettre_commentaires']
           )
        {   
            // Aucun changement detecte
            return TRUE;
        }   

        $data = array(
            'json'        => 1,
            'edite'       => 1,
            'edite_epoch' => $this->now_epoch,
            'edite_date'  => date_humanize($this->now_epoch, TRUE)
        );

        if ($message['titre'] != $post_data['titre'] || $message['contenu'] != $post_data['message'])
        {
            // Les deux sont ecrits en meme temps au cas ou il s'agirait d'un titre ou d'un message qui n'etait pas en json.
            $data['titre']   = _html_in($post_data['titre'], array('strip_tags' => TRUE));
            $data['contenu'] = _html_in($post_data['message']);
        }

        $data['permettre_commentaires'] = $post_data['permettre_commentaires'];

        $this->db->where ('message_id', $message_id);
        $this->db->update('forums_messages', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les commentaires
     *
     * ------------------------------------------------------------------------ */
    function extraire_commentaires($message_ids = array())
    {
        if (empty($message_ids))
        {
            return array();
        }

        //
        // Les commentaires de plusieurs messages
        //

        if (is_array($message_ids))
        {
            $this->db->from     ('forums_commentaires as fc, enseignants as e');
            $this->db->select   ('fc.*, e.nom, e.prenom, e.genre');
            $this->db->where_in ('fc.message_id', $message_ids);
            $this->db->where    ('fc.enseignant_id = e.enseignant_id');
            $this->db->where    ('fc.efface', 0);
            $this->db->order_by ('fc.ajout_epoch', 'desc');
            
            $query = $this->db->get();
            
            if ( ! $query->num_rows() > 0)
            {
                return array();
            }

            $commentaires = array();

            foreach ($query->result_array() as $row)
            {
                $message_id     = $row['message_id'];
                $commentaire_id = $row['commentaire_id'];

                if ($row['json'])
                {
                    $row['commentaire_contenu'] = $row['commentaire_contenu'];
                }

                if ( ! array_key_exists($message_id, $commentaires))
                {
                    $commentaires[$message_id] = array();
                }
                
                $commentaires[$message_id][] = $row;
            }

            return $commentaires;
        }

        //
        // Les commentaires d'un seul message
        //

        if ( ! is_numeric($message_ids))
        {
            return array();
        }

        $this->db->from     ('forums_commentaires as fc, enseignants as e');
        $this->db->select   ('fc.*, e.nom, e.prenom, e.genre');
        $this->db->where    ('fc.message_id', $message_ids);
        $this->db->where    ('fc.enseignant_id = e.enseignant_id');
        $this->db->where    ('fc.efface', 0);
        $this->db->order_by ('fc.ajout_epoch', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $commentaires = array();

        foreach ($query->result_array() as $row)
        {
            if ($row['json'])
            {
                $row['commentaire_contenu'] = $row['commentaire_contenu'];
            }

            $commentaires[] = $row;
        }

        return $commentaires;
    }

    /* ------------------------------------------------------------------------
     *
     * Publier un commentaire
     *
     * ------------------------------------------------------------------------ */
    function publier_commentaire($message_id, $message, $post_data)
    {
        $this->db->trans_begin();

        $data = array(
            'message_id'          => $message_id,
            'enseignant_id'       => $this->enseignant_id,
            'commentaire_contenu' => _html_in($post_data['commentaire'], array('strip_tags' => TRUE)),
            'ajout_epoch'         => $this->now_epoch,
            'ajout_date'          => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->insert('forums_commentaires', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        // 
        // Activer le suivi des commentaires pour ce message
        //

        $message_suivi = ! empty($this->messages_suivis($message_id)) ? TRUE : FALSE;

        if ( ! $message_suivi)
        {
            $data = array(
                'enseignant_id'          => $this->enseignant_id,
                'message_id'             => $message_id,
                'derniere_lecture_date'  => date_humanize($this->now_epoch, TRUE),
                'derniere_lecture_epoch' => $this->now_epoch
            );

            $this->db->insert('forums_notifications_messages_suivis', $data);
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un message
     *
     * ------------------------------------------------------------------------ */
    function effacer_message($message_id)
    {
        $this->db->from  ('forums_messages');
        $this->db->where ('message_id', $message_id);
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $message = $query->row_array();

        $this->db->trans_begin();

        //
        // Effacer le message
        //

        $data = array(
            'efface' => 1
        );

        $this->db->where ('message_id', $message_id);
        $this->db->update('forums_messages', $data);

        //
        // Effacer les commentaires s'y rapportant
        //

        $commentaires = $this->extraire_commentaires($message_id);

        if ( ! empty($commentaires))
        {
            $commentaire_ids = array_column($commentaires, 'commentaire_id');

            $data = array(
                'efface' => 1
            );

            $this->db->where_in('commentaire_id', $commentaire_ids);
            $this->db->update  ('forums_commentaires', $data);
        }

        //
        // Effacer toutes les demandes de notification en tant que messags suivi
        //

        $this->db->where  ('message_id', $message_id);
        $this->db->delete ('forums_notifications_messages_suivis');

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Effacer un commentaire
     *
     * ------------------------------------------------------------------------ */
    function effacer_commentaire($commentaire_id)
    {
        $this->db->from  ('forums_commentaires');
        $this->db->where ('commentaire_id', $commentaire_id);
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $commentaire = $query->row_array();

        //
        // Verifier si l'effacement est permis
        //

        if 
        (
            ! ($commentaire['ajout_epoch'] + $this->config->item('forums_commentaires_effacement_delai')) > $this->now_epoch
        )
        {
            return FALSE;
        }

        $data = array(
            'efface' => 1
        );

        $this->db->where ('commentaire_id', $commentaire_id);
        $this->db->update('forums_commentaires', $data);    

        return ($commentaire['message_id']);
    }

    /* ------------------------------------------------------------------------
     *
     * Suivre un message
     *
     * ------------------------------------------------------------------------ */
    function suivre_message($message_id)
    {
        $this->db->from  ('forums_notifications_messages_suivis');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('message_id', $message_id);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return;
        }
                                                                                                                                                                                                                                  
        $data = array(
            'enseignant_id'          => $this->enseignant_id,
            'message_id'             => $message_id,
            'derniere_lecture_date'  => date_humanize($this->now_epoch, TRUE),
            'derniere_lecture_epoch' => $this->now_epoch
        );

        $this->db->insert('forums_notifications_messages_suivis', $data);

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Ne plus suivre un message
     *
     * ------------------------------------------------------------------------ */
    function arret_suivre_message($message_id)
    {
        $this->db->from  ('forums_notifications_messages_suivis');
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('message_id', $message_id);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return;
        }

        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('message_id',    $message_id);
        $this->db->delete('forums_notifications_messages_suivis');

        return;
    }

} // class
