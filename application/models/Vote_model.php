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
 * VOTE MODEL
 *
 * ============================================================================ */

class Vote_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* ------------------------------------------------------------------------
     *
     * Lister les scrutins lances en vigueur
     *
     * ------------------------------------------------------------------------ */
    function scrutins_lances_en_vigueur($options = array())
    {
        $options = array_merge(
            array(
                'enseignant_id' => NULL       
            ),
            $options
        );

        $this->db->from  ('scrutins_lances');
        $this->db->where ('groupe_id', $this->groupe_id);
        $this->db->where ('termine', 0);
        $this->db->where ('echeance_epoch >', $this->now_epoch);
        $this->db->where ('efface', 0);

        if ( ! empty($options['enseignant_id']))
        {
            $this->db->where ('enseignant_id', $options['enseignant_id']);
        }

        $this->db->order_by ('lance_epoch', 'asc');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
            return array();

        $scrutins_lances = $query->result_array();
        $scrutins_lances = array_keys_swap($scrutins_lances, 'scrutin_lance_id');

        return $scrutins_lances;
    }

    /* ========================================================================
     *
     * GESTION DES SCRUTINS
     *
     * ======================================================================== */

    /* ------------------------------------------------------------------------
     *
     * Creer scrutin
     *
     * ------------------------------------------------------------------------ */
    function creer_scrutin($post_data, $options = array())
    {
        $options = array_merge(
            array(
           ),
           $options
        );

        $data = array(
            'groupe_id'      => $post_data['groupe_id'],
            'enseignant_id'  => $post_data['enseignant_id'],
            'anonyme'        => $post_data['anonyme'],
            'scrutin_texte'  => htmlentities($post_data['scrutin_texte']),
            'creation_date'  => date_humanize($this->now_epoch, TRUE),
            'creation_epoch' => $this->now_epoch
        );

        $this->db->trans_begin();

        $this->db->insert('scrutins', $data);
        $scrutin_id = $this->db->insert_id();

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Participants
        //

        $data = array();

        if (isset($post_data['participants']) && is_array($post_data['participants']) && ! empty($post_data['participants']))
        {
            foreach($post_data['participants'] as $enseignant_id)
            {
                $data1 = array(
                    'scrutin_id'    => $scrutin_id,
                    'enseignant_id' => $enseignant_id
                );

                $data[] = $data1;
            }

            if ( ! empty($data))
            {
                $this->db->insert_batch('scrutins_participants', $data);

                if ( ! $this->db->affected_rows())
                {
                    $this->db->trans_rollback();
                    return FALSE;
                }
            }
        } // if participants

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return $scrutin_id;
    }

    /* ------------------------------------------------------------------------
     *
     * Terminer scrutin
     *
     * ------------------------------------------------------------------------ */
    function terminer_scrutin($scrutin_lance_id)
    {
		$data = array(
			'termine'	 	=> 1,
			'termine_date' 	=> date_humanize($this->now_epoch, TRUE),
			'termine_epoch' => $this->now_epoch
		);

		$this->db->where ('scrutin_lance_id', $scrutin_lance_id);
		$this->db->where ('enseignant_id', $this->enseignant_id);
		$this->db->update('scrutins_lances', $data);

		return TRUE;	
	}

    /* ------------------------------------------------------------------------
     *
     * Extraire scrutin
     *
     * ------------------------------------------------------------------------ */
    function extraire_scrutin($scrutin_id, $options = array())
    {
        $options = array_merge(
            array(
                'enseignant_id' => NULL, // permettre seulement au responsable du scrutin de l'extraire
                'permission'    => FALSE // si TRUE, ne sortir que les informations de base du scrutin
           ),
           $options
        );

        if ( ! empty($options['enseignant_id']))
        {
            $this->db->where ('enseignant_id', $options['enseignant_id']);
        }

        $this->db->from  ('scrutins');
        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->where ('groupe_id', $this->groupe_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $scrutin = $query->row_array();

        //
        // Cette fonction a ete appelee pour simplement verifier si l'enseignant est le responsable.
        //

        if ( ! empty($options['enseignant_id']) && $options['permission'] == TRUE)
        {
            return $scrutin;
        }

        //
        // Extraire les documents
        //

        $documents = array();

        $scrutin['documents'] = $this->extraire_documents($scrutin_id);

        //
        // Extraire les choix
        //
        
        $choix = array();

        $scrutin['choix'] = $this->extraire_choix($scrutin_id);

        //
        // Extraire les participants
        //

        $participants = array();

        $this->db->from     ('scrutins_participants');
        $this->db->where    ('scrutin_id', $scrutin_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $participants = $query->result_array();
            $participants = array_keys_swap($participants, 'enseignant_id');
        }

        $scrutin['participants'] = $participants;

        return $scrutin;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire scrutins (plusieurs)
     *
     * ------------------------------------------------------------------------ */
    function extraire_scrutins($options = array())
    {
        $this->db->from  ('scrutins');
        $this->db->where ('groupe_id', $this->groupe_id);
		$this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('efface', 0);

        $query = $this->db->get();

        $scrutins = array();

        if ($query->num_rows() > 0)
        {
            $scrutins = $query->result_array();
            $scrutins = array_keys_swap($scrutins, 'scrutin_id');
        }

        return $scrutins;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire scrutins lances
     *
     * ------------------------------------------------------------------------ */
    function extraire_scrutins_lances($options = array())
    {
        $options = array_merge(
            array(
                'enseignant_id' => NULL,
                'termine'       => FALSE
           ),
           $options
        );

		$this->db->from   ('scrutins_lances as sl');
        $this->db->where  ('sl.groupe_id', $this->groupe_id);
        $this->db->where  ('sl.efface', 0);

        if ($options['termine'] == TRUE)
        {
            $this->db->where   ('sl.termine', 1);
            $this->db->or_where('sl.echeance_epoch <', $this->now_epoch);
        }
		else
		{
            $this->db->where ('sl.termine', 0);
            $this->db->where ('sl.echeance_epoch >', $this->now_epoch);
		}

        if ( ! empty($options['enseignant_id']))
        {
            $this->db->where ('sl.enseignant_id', $options['enseignant_id']);
        }

        $this->db->order_by ('sl.lance_epoch', 'desc');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $scrutins_lances = $query->result_array();
        $scrutins_lances = array_keys_swap($scrutins_lances, 'scrutin_lance_id');

        //
        // Determiner le taux de participation de chacun des scrutins lances
        //

        $scrutin_lance_ids = array_keys($scrutins_lances);

        foreach($scrutins_lances as $scrutin_lance_id => &$sl)
        {
            if ($sl['efface'])
            {
                // Je ne decrais pas avoir besoin de ette ligne mais pour une raison inconnue,
                // le statement sql ne veut pas reconnaitre efface = 0 plus haut.
                unset($scrutins_lances[$scrutin_lance_id]);
            }

            $p = $this->extraire_participants_scrutin_lance($scrutin_lance_id);
            $v = $this->extraire_votes($scrutin_lance_id);

            $sl['participants'] = count($p);
            $sl['votes']        = count($v);
        }

        return $scrutins_lances;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire scrutins lances a voter
     *
     * ------------------------------------------------------------------------ */
    function extraire_scrutins_lances_a_voter($options = array())
    {
		$this->db->from	  ('scrutins_lances as sl, scrutins_lances_participants as slp');
		$this->db->select ('sl.*');
        $this->db->where  ('sl.groupe_id', $this->groupe_id);
        $this->db->where  ('sl.efface', 0);

		// Ne pas considerer les scrutins deja votes par l'enseignant
		$this->db->where  ('sl.scrutin_lance_id = slp.scrutin_lance_id');
		$this->db->where  ('slp.enseignant_id', $this->enseignant_id);
		$this->db->where  ('slp.vote_termine', 0);

		// Ne pas considerer les scrutins termines ou echus
		$this->db->where  ('sl.termine', 0);
		$this->db->where  ('sl.echeance_epoch >', $this->now_epoch);

        $this->db->order_by ('sl.lance_epoch', 'asc');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $scrutins_lances = $query->result_array();
        $scrutins_lances = array_keys_swap($scrutins_lances, 'scrutin_lance_id');

        return $scrutins_lances;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les choix d'un scrutin
     *
     * ------------------------------------------------------------------------ */
    function extraire_choix($scrutin_id, $options = array())
    {
        $this->db->from  ('scrutins_choix');
        $this->db->where ('scrutin_id', $scrutin_id);
		$this->db->where ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return $query->result_array();
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les choix d'un scrutin *lance*
     *
     * ------------------------------------------------------------------------ */
    function extraire_choix_lances($scrutin_lance_id, $options = array())
    {
        $this->db->from  ('scrutins_lances_choix');
        $this->db->where ('scrutin_lance_id', $scrutin_lance_id);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return $query->result_array();
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les participants d'un scrutin
     *
     * ------------------------------------------------------------------------ */
    function extraire_participants($scrutin_id)
    {
        $this->db->from  ('scrutins_participants');
        $this->db->where ('scrutin_id', $scrutin_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }

        return array();
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les participants d'un scrutin lance
     *
     * ------------------------------------------------------------------------ */
    function extraire_participants_scrutin_lance($scrutin_lance_id)
    {
        $this->db->from  ('scrutins_lances_participants as sl, enseignants as e');
		$this->db->select('sl.*, e.prenom, e.nom');
		$this->db->where ('sl.enseignant_id = e.enseignant_id');
        $this->db->where ('sl.scrutin_lance_id', $scrutin_lance_id);
		$this->db->order_by('e.nom', 'asc');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }

        return array();
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire un document
     *
     * ------------------------------------------------------------------------ */
    function extraire_document($scrutin_doc_id, $options = array())
    {   
        $this->db->from  ('scrutins_documents');
        $this->db->where ('scrutin_doc_id', $scrutin_doc_id);
        $this->db->where ('groupe_id', $this->groupe_id);
		$this->db->where ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $document = $query->row_array();

        if ( ! empty($document['doc_filename']) && file_exists(FCPATH . $this->config->item('documents_path') . $document['doc_filename']))
        {
            return $document;
        }

        return FALSE;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les documents
     *
     * ------------------------------------------------------------------------ */
    function extraire_documents($scrutin_id, $options = array())
    {
        $this->db->from  ('scrutins_documents');
        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->where ('groupe_id', $this->groupe_id);
		$this->db->where ('efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $documents = array();

        foreach($query->result_array() as $doc)
        {
            if ( ! empty($doc['doc_filename']) && file_exists(FCPATH . $this->config->item('documents_path') . $doc['doc_filename']))
            {
                $documents[] = $doc;
            }
        }

        return $documents;
    }

    /* ------------------------------------------------------------------------
     *
     * Extraire les documents *lances*
     *
     * ------------------------------------------------------------------------ */
    function extraire_documents_lances($scrutin_lance_id, $options = array())
    {
        $this->db->from  ('scrutins_lances_documents');
        $this->db->where ('scrutin_lance_id', $scrutin_lance_id);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $documents = array();

        foreach($query->result_array() as $doc)
        {
            if ( ! empty($doc['doc_filename']) && file_exists(FCPATH . $this->config->item('documents_path') . $doc['doc_filename']))
            {
                $documents[] = $doc;
            }
        }

        return $documents;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier la question d'un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_question($scrutin_id, $post_data)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, array('enseignant_id' => $this->enseignant_id))))
        {
            redirect(base_url());
            exit;
        }

        //
        // Modifier la question
        //
        
        $data = array(
            'scrutin_texte' => htmlentities(mb_strimwidth(strip_tags($post_data['scrutin_texte']), 0, 250, '...'))
        );

        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->update('scrutins', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un choix a un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_choix($scrutin_id, $post_data)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        $data = array(
            'scrutin_id' => $scrutin_id,
            'choix_texte' => $post_data['choix_texte']
        );

        $this->db->insert('scrutins_choix', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer une choix a un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_choix($scrutin_id, $choix_id)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        $this->db->where('choix_id', $choix_id);
        $this->db->where('scrutin_id', $scrutin_id);
        $this->db->delete('scrutins_choix');

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer la participation
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_participation($scrutin_id, $enseignant_id)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        $this->db->from  ('scrutins_participants');
        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->where ('enseignant_id', $enseignant_id);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            // Rendre participant
            
            $data = array(
                'scrutin_id'    => $scrutin_id,
                'enseignant_id' => $enseignant_id
            );

            $this->db->insert('scrutins_participants', $data);
            
            return TRUE;
        }

        // Rendre non-participant
        
        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->where ('enseignant_id', $enseignant_id);
        $this->db->delete('scrutins_participants');

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le respect du code morin
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_code_morin($scrutin_id)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        $data = array();

        if ($scrutin['code_morin'])
        {
            $data['code_morin'] = 0;
        }
        else
        {
            $data['code_morin'] = 1;
        }

        $this->db->where('scrutin_id', $scrutin_id);
        $this->db->update('scrutins', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer l'anonimite d'un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_anonyme($scrutin_id)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        $data = array();

        if ($scrutin['anonyme'])
        {
            $data['anonyme'] = 0;
        }
        else
        {
            $data['anonyme'] = 1;
        }

        $this->db->where('scrutin_id', $scrutin_id);
        $this->db->update('scrutins', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer la date d'echeance d'un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_date_echeance($scrutin_id, $date_echeance)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        if (empty($date_echeance))
        {
            $date_echeance = NULL;
            $date_echeance_epoch = NULL;
        }
        else
        {
            $date_echeance_epoch = date_epochize($date_echeance, 'end');
        }
        
        if ($date_echeance_epoch == $scrutin['echeance_epoch'])
        {
            echo "La date d'echeance est identique à celle déjà enregistrée.";
            return FALSE;
        }

        $data = array(
            'echeance_date'  => $date_echeance,
            'echeance_epoch' => $date_echeance_epoch
        );

        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->update('scrutins', $data);

        return TRUE;
    }


    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un document
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_document($scrutin_id, $filedata)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        //
        // Hasher le nom du fichier avec la hash du fichier pour etre certain qu'il sagit d'un fichier unique.
        //

        $hash_file = hash_file('sha256', FCPATH . $this->config->item('documents_path') . $filedata['orig_name']);
        $hash      = hash('sha256', $filedata['orig_name'] . $hash_file);

        $data = array(
            'groupe_id'       => $this->groupe_id,
            'scrutin_id'      => $scrutin_id,
            'doc_filename'    => $this->security->sanitize_filename($filedata['orig_name']),
            'doc_sha256'      => $hash,
            'doc_sha256_file' => $hash_file,
            'doc_filesize'    => $filedata['file_size'] / 1000,
            'doc_is_image'    => $filedata['is_image'],
            'doc_mime_type'   => @$filedata['file_type'],
            'doc_size_h'      => @$filedata['image_height'],
            'doc_size_w'      => @$filedata['image_width'],
            'ajout_date'      => date_humanize($this->now_epoch, TRUE),
            'ajout_epoch'     => $this->now_epoch,
            'ajout_par_enseignant_id' => $this->enseignant_id
        );

        $this->db->insert('scrutins_documents', $data);

        if ( ! $this->db->affected_rows())
        {
            log_alerte(
				array(
					'code' => 'DOC5813',
					'desc' => "Il n'a pas été possible d'enregistrer le document."
				)
            );

            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier la description (caption) d'un document
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_document_caption($scrutin_doc_id, $post_data)
    {
        if (empty($scrutin = $this->extraire_scrutin($post_data['scrutin_id'],
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        if (empty($document = $this->extraire_document($scrutin_doc_id)))
        {       
            redirect(base_url());
            exit;
        } 

        //
        // Modifier la description du document
        //
        
        $data = array(
            'doc_caption'  => htmlentities(mb_strimwidth(strip_tags($post_data['doc_caption']), 0, 150, '...'))
        );

        $this->db->where ('scrutin_doc_id', $scrutin_doc_id);
        $this->db->update('scrutins_documents', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer document
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_document($scrutin_id, $scrutin_doc_id)
    {
        if (empty($scrutin = $this->extraire_scrutin($scrutin_id,
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            redirect(base_url());
            exit;
        }

        //
        // Verifier que le document existe.
        //
                
        $this->db->from ('scrutins_documents as d');
        $this->db->where('scrutin_id', $scrutin_id);
        $this->db->where('scrutin_doc_id', $scrutin_doc_id);
        $this->db->where('groupe_id', $this->groupe_id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        //
        // Effacer physiquement le fichier du disque
        //

        $doc = $query->row_array();

        if ( ! empty($doc))
        {
            if ( ! empty($doc['doc_filename']) && file_exists(FCPATH . $this->config->item('documents_path') . $doc['doc_filename']))
            {
                if ( ! unlink(FCPATH . $this->config->item('documents_path') . $doc['doc_filename']))
                {
                    $this->db->trans_rollback();
                    return 0;
                }

                $this->db->where('scrutin_doc_id', $scrutin_doc_id);
                $this->db->delete('scrutins_documents');
            }
        }

        /* TODO
        $this->db->where('scrutin_doc_id', $doc_id);
        $this->db->update('scrutins_documents', 
            array(
                'efface'       => 1,
                'efface_epoch' => $this->now_epoch,
                'efface_date'  => date_humanize($this->now_epoch, TRUE)
            )
        );
        */

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_scrutin($scrutin_id)
    {
		// Verifier la permission d'effacement

		$scrutin = $this->extraire_scrutin($scrutin_id);

		if (empty($scrutin) || ! is_array($scrutin))
		{
			return FALSE;
		}

		if ($scrutin['enseignant_id'] != $this->enseignant_id)
		{
			return FALSE;
		}

        $this->db->trans_begin();

		$data = array(
			'efface' => 1,
			'efface_date' => date_humanize($this->now_epoch, TRUE),
			'efface_epoch' => $this->now_epoch
		);

		// Effacer les choix

		$this->db->where ('scrutin_id', $scrutin_id);
		$this->db->update('scrutins_choix', $data);

		// Effacer les documents

		$this->db->where  ('scrutin_id', $scrutin_id);
		$this->db->update ('scrutins_documents', $data);

		// Effacer les participants

		$this->db->where  ('scrutin_id', $scrutin_id);
		$this->db->update ('scrutins_participants', $data);

		// Effacer le scrutin

		$this->db->where  ('scrutin_id', $scrutin_id);
		$this->db->update ('scrutins', $data);

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
     * Effacer un scrutin lance
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_scrutin_lance($scrutin_reference)
    {
        $scrutin = $this->extraire_scrutin_par_reference($scrutin_reference);

		if (empty($scrutin) || ! is_array($scrutin))
		{
			return FALSE;
        }

        $scrutin_lance_id = $scrutin['scrutin_lance_id'];

		// Verifier la permission d'effacement par l'utilisateur

		if ($scrutin['enseignant_id'] != $this->enseignant_id)
		{
			return FALSE;
        }

        //
        // Verifier que ce scrutin lance peut etre efface selon les conditions suivantes
        //
        // - Il doit etre termine.
        // - Il ne doit pas y avoir eu de votes. 
        //

        if ( ! ($scrutin['termine'] || ($scrutin['echeance_epoch'] < $this->now_epoch)))
        {
            return FALSE;
        }
            
        if (count($this->extraire_votes($scrutin_lance_id)) > 0)
        {
            return FALSE;
        }

        $this->db->trans_begin();

		$data = array(
			'efface' => 1,
			'efface_date'  => date_humanize($this->now_epoch, TRUE),
			'efface_epoch' => $this->now_epoch
		);

		// Effacer les choix

		$this->db->where ('scrutin_lance_id', $scrutin_lance_id);
		$this->db->update('scrutins_lances_choix', $data);

		// Effacer les documents

		$this->db->where  ('scrutin_lance_id', $scrutin_lance_id);
		$this->db->update ('scrutins_lances_documents', $data);

		// Effacer les participants

		$this->db->where  ('scrutin_lance_id', $scrutin_lance_id);
		$this->db->update ('scrutins_lances_participants', $data);

		// Effacer le scrutin

		$this->db->where  ('scrutin_lance_id', $scrutin_lance_id);
		$this->db->update ('scrutins_lances', $data);

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
     * Lancer un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function lancer_scrutin($scrutin_id)
    {
        //
        // Gabarit du tableau de retour d'erreur
        //

        $erreur = array(
            'status'   => NULL, // [error|ok]
            'code'     => NULL,
            'titre'    => NULL, // titre de la page d'erreur
            'message'  => NULL, // message d'erreur
            'solution' => NULL, // solution possible a cette erreur
            'extra'    => array()
        );

        if (empty($scrutin = $this->extraire_scrutin($scrutin_id, 
            array(
                'enseignant_id' => $this->enseignant_id,
                'permission'    => TRUE
            )
        )))
        {
            return array(
                'status'   => 'error',
                'code'     => 'HOP4510',
                'titre'    => "Lançage de scrutin interdit",
                'message'  => "Vous n'avez pas la permission de lancer ce scrutin."
            );
        }

        //
        // Verifier que ce scrutin n'est pas deja lance.
        //

        $scrutins_lances_en_vigueur = $this->scrutins_lances_en_vigueur();

        if ( ! empty($scrutins_lances_en_vigueur))
        {
            foreach($scrutins_lances_en_vigueur as $s)
            {
                if ($s['scrutin_id'] == $scrutin_id)
                {
                    return array(
                        'status'   => 'error',
                        'code'     => 'HOP4515',
                        'titre'    => "Lançage de scrutin interdit",
                        'message'  => "Ce scrutin est déjà lancé.",
                        'solution' => "Veuillez terminer le scrutin en vigueur puis le relancer."
                    );
                }
            }
        }

		//
		// Verifier que la date d'echeance est posterieure.
        //

        if ( 
            array_key_exists('echeance_epoch', $scrutin) &&
            ! empty($scrutin['echeance_epoch'])          &&
             $scrutin['echeance_epoch'] < ($this->now_epoch + 24*60*60)
           )

        {
            return array(
                'status'   => 'error',
                'code'     => 'HOP4520',
                'titre'    => "Lançage de scrutin interdit",
                'message'  => "Vous devez laisser au moins 24h aux participants pour remplir votre scrutin.",
                'solution' => "Changer la date d'écheance pour une date postérieure."
            );
        }
        
        //
        // Verifier qu'il y a au moins trois participants.
        //

        $participants = $this->extraire_participants($scrutin_id);

        if (count($participants) < 3)
        {
            return array(
                'status'   => 'error',
                'code'     => 'HOP4525',
                'titre'    => "Lançage de scrutin interdit",
                'message'  => "Il n'y a pas suffisamment de participants à ce scrutin.",
                'solution' => "Vous devez sélectionner au moins 3 participants."
            );
        }

        //
        // Extraire les choix
        // 

        $choix = $this->extraire_choix($scrutin_id);
        $choix = array_keys_swap($choix, 'choix_id');

        if (count($choix) < 2)
        {
            return array(
                'status'   => 'error',
                'code'     => 'HOP4529',
                'titre'    => "Lançage de scrutin interdit",
                'message'  => "Il n'y a pas suffisamment de choix à ce scrutin.",
                'solution' => "Le scrutin doit avoir au moins 2 choix."
            );
        }

		/*
        $choix_data = array();

        foreach($choix as $c)
        {
            $choix_data[$c['choix_id']] = array(
                'choix_texte' => $c['choix_texte']
            );
        }

        $choix_data = serialize($choix_data);
		*/

        //
        // Extraire les documents
        //

        $documents = $this->extraire_documents($scrutin_id);

        //
        // Lancer le scrutin
        //

        $this->db->trans_begin();

        // Creer une reference

        $scrutin_reference = strtolower(random_string('alpha', 10));

        // Verifier et ajuster si necessaire les limites de la date d'echeance

        if (
            ! array_key_exists('echeance_epoch', $scrutin)                  ||
            empty($scrutin['echeance_epoch'])                               ||
            ($scrutin['echeance_epoch'] > ($this->now_epoch + 90*60*60*24)) ||
            ($scrutin['echeance_epoch'] < ($this->now_epoch + 60*60*24))
           )
        {
            $scrutin['echeance_epoch'] = date_epochize(date_humanize($this->now_epoch + 90*60*60*24), 'end');
            $scrutin['echeance_date']  = date_humanize($scrutin['echeance_epoch'], TRUE);
        }

        $data = array(
            'scrutin_id'        => $scrutin_id,
            'groupe_id'         => $this->groupe_id,
            'enseignant_id'     => $this->enseignant_id,
            'scrutin_reference' => $scrutin_reference,
            'scrutin_texte'     => $scrutin['scrutin_texte'],
            // 'choix_data'     => $choix_data, // serialized array
            'lance_epoch'       => $this->now_epoch,
            'lance_date'        => date_humanize($this->now_epoch, TRUE),
            'echeance_epoch'    => $scrutin['echeance_epoch'],
            'echeance_date'     => $scrutin['echeance_date'],
            'anonyme'           => $scrutin['anonyme'],
            'code_morin'        => $scrutin['code_morin']
        );

        $this->db->insert('scrutins_lances', $data);

        $scrutin_lance_id = $this->db->insert_id();

        //
        // Inscrire les participants
        //

        $data = array();

        foreach($participants as $p)
        {
            $data[] = array(
                'scrutin_lance_id'  => $scrutin_lance_id,
                'enseignant_id'     => $p['enseignant_id']
            );
        }

        $this->db->insert_batch('scrutins_lances_participants', $data);

        //
        // Enregistrer les choix
        //
    
        $data = array();

        foreach($choix as $c)
        {
            $data[] = array(
                'choix_texte'      => $c['choix_texte'],
                'scrutin_lance_id' => $scrutin_lance_id
            );
        }

        $this->db->insert_batch('scrutins_lances_choix', $data);
        
        //
        // Enregistrer les documents
        //

        $data_documents = array();

        if ( ! empty($documents) && is_array($documents))
        {
            foreach($documents as $d)
            {
				$doc_filename_nouveau = 'sl_' . $scrutin_reference . '_' . $d['doc_filename'];

                $data_documents[] = array(
                	'scrutin_lance_id' 		=> $scrutin_lance_id,
					'doc_filename_original' => $d['doc_filename'],
					'doc_filename'		    => $doc_filename_nouveau,
					'doc_caption'		    => $d['doc_caption'],
					'doc_sha256'		    => NULL,
					'doc_sha256_file'	    => NULL,
					'doc_filesize'		    => $d['doc_filesize'],
				    'doc_is_image'		    => $d['doc_is_image'],
					'doc_size_h'			=> $d['doc_size_h'],
					'doc_size_w'			=> $d['doc_size_w'],
					'doc_mime_type'			=> $d['doc_mime_type']
                );
            }

            $this->db->insert_batch('scrutins_lances_documents', $data_documents);
        }

        //
        // Changer le status du scrutin original
        //

        $data = array(
            'lance'                   => 1,
            'lance_scrutin_lance_id'  => $scrutin_lance_id,
            'lance_scrutin_reference' => $scrutin_reference
        );

        $this->db->where ('scrutin_id', $scrutin_id);
        $this->db->update('scrutins', $data);

        // Verifier les erreurs de base de donnees

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            return array(
                'status'   => 'error',
                'code'     => 'HOP4530',
                'titre'    => "Erreur de lançage de scrutin",
                'message'  => "Une erreur avec la base de données s'est produite lors du lançage.",
                'solution' => "Veuillez contacter l'administrateur."
            );
        }

		//
		// Copier les documents avec leur nouveau nom
		//

		if ( ! empty($data_documents) && is_array($data_documents))
		{
			foreach($data_documents as $dd)
			{
				if (file_exists(FCPATH . $this->config->item('documents_path') . $dd['doc_filename_original']))
				{
					if (copy(FCPATH . $this->config->item('documents_path') . $dd['doc_filename_original'], FCPATH . $this->config->item('documents_path') . $dd['doc_filename']))
					{
						$hash_file = hash_file('sha256', FCPATH . $this->config->item('documents_path') . $dd['doc_filename']);

						$data = array(
							'doc_sha256_file' => $hash_file,
							'doc_sha256'      => hash('sha256', $dd['doc_filename'] . $hash_file)
						);

						$this->db->where ('scrutin_lance_id', $scrutin_lance_id);
						$this->db->where ('doc_filename', $dd['doc_filename']);
						$this->db->update('scrutins_lances_documents', $data);
					}
					else
					{
						generer_erreur('SCRU671', "Il n'a pas été possible de copier les documents pour lancer le scrutin.");
						exit;
					}
				}
			}
		}

        $this->db->trans_commit();

        return array(
            'status'            => 'ok',
            'scrutin_reference' => $scrutin_reference
        );
	}

    /* ========================================================================
     *
     * PROCESSUS DE VOTATION
     *
     * ======================================================================== */

    /* ------------------------------------------------------------------------
     *
     * Extraire scrutin par reference
     *
     * ------------------------------------------------------------------------
     *
     * Extraire tous les elements necessaires pour montrer le scrutin.
     *
     * ------------------------------------------------------------------------ */
    function extraire_scrutin_par_reference($scrutin_reference, $options = array())
    {
        $options = array_merge(
           array(
           ),
           $options
        );

        $this->db->from  ('scrutins_lances as sl, enseignants as e');
        $this->db->select('sl.*, e.nom, e.prenom');
        $this->db->where ('sl.scrutin_reference', $scrutin_reference);
        $this->db->where ('sl.enseignant_id = e.enseignant_id');
        $this->db->where ('sl.groupe_id', $this->groupe_id);
        $this->db->where ('sl.efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $scrutin    = $query->row_array();

        $scrutin_id       = $scrutin['scrutin_id'];
        $scrutin_lance_id = $scrutin['scrutin_lance_id'];

        //
        // Extraire les documents
        //

		$scrutin['documents'] = $this->extraire_documents_lances($scrutin_lance_id);

        //
        // Extraire les choix
        //
        
		$scrutin['choix'] = $this->extraire_choix_lances($scrutin_lance_id);

        //
        // Extraire les participants
        //

        $this->db->from  ('scrutins_lances_participants');
        $this->db->where ('scrutin_lance_id', $scrutin_lance_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $participants = $query->result_array();
        }

        $scrutin['participants'] = $participants;

        return $scrutin;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier si l'enseignant a la permission de voter
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_permission_de_voter($scrutin_reference)
    {
        $this->db->from  ('scrutins_lances as sl, scrutins_lances_participants as slp');
        $this->db->where ('sl.scrutin_reference', $scrutin_reference);
        $this->db->where ('sl.scrutin_lance_id = slp.scrutin_lance_id');
        $this->db->where ('slp.enseignant_id', $this->enseignant_id);

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier si l'enseignant a deja vote
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_enseignant_deja_vote($scrutin_reference)
    {
        $this->db->from  ('scrutins_lances as sl, scrutins_lances_participants as slp');
        $this->db->where ('sl.scrutin_reference', $scrutin_reference);
        $this->db->where ('sl.scrutin_lance_id = slp.scrutin_lance_id');
        $this->db->where ('slp.enseignant_id', $this->enseignant_id);
        $this->db->where ('slp.vote_termine', 1);
		$this->db->limit (1);

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Il y a combien de scrutins qui requiert le vote de l'enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function scrutins_a_voter($options = array())
    {
        $options = array_merge(
            array(
                'enseignant_id' => $this->enseignant_id
            ),
            $options
        );

        $this->db->from  ('scrutins_lances_participants as slp, scrutins_lances as sl');

        $this->db->where ('slp.enseignant_id', $options['enseignant_id']);
        $this->db->where ('slp.vote_termine', 0);
        $this->db->where ('slp.scrutin_lance_id = sl.scrutin_lance_id');
        $this->db->where ('sl.groupe_id', $this->groupe_id);
        $this->db->where ('sl.echeance_epoch >', $this->now_epoch);
        $this->db->where ('sl.termine', 0);
        $this->db->where ('sl.efface', 0);
        
        return $this->db->count_all_results();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les scrutins qui requiert un vote
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_scrutins_a_voter($options = array())
    {
        $options = array_merge(
            array(
                'enseignant_id' => $this->enseignant_id
            ),
            $options
        );

        // Extraire les scrutins en vigueur

        $scrutins_lances = $this->scrutins_lances_en_vigueur();

        if (empty($scrutins_lances))
        {
            // Aucun scrutin a voter
            return array();
        }

        $scrutin_lance_ids = array_keys($scrutins_lances);

        // Verifier si l'enseignant a deja repondu a certains scrutins

        $this->db->select   ('scrutin_lance_id');
        $this->db->from     ('scrutins_lances_participants');
        $this->db->where    ('enseignant_id', $options['enseignant_id']);
        $this->db->where    ('vote_termine', 0);
        $this->db->where_in ('scrutin_lance_id', $scrutin_lance_ids);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             $scrutins_a_repondre = array();
                                                                                                                                                                                                                                  
        $scrutins_a_repondre = $query->result_array();
        $scrutins_a_repondre = array_keys_swap($scrutins_a_repondre, 'scrutin_lance_id');
        $scrutins_a_repondre_lance_id = array_keys($scrutins_a_repondre);

        // Eliminer les scrutins deja repondus

        foreach($scrutins_lances as $s)
        {
            if ( ! in_array($s['scrutin_lance_id'], $scrutins_a_repondre_lance_id))
            {
                unset($scrutins_lances[$s['scrutin_lance_id']]);
            }
        }

        return $scrutins_lances;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les scrutins participatifs
     *
     * --------------------------------------------------------------------------------------------
     *
     * Extraire les scrutins dont l'enseignant a participes. 
     * Ces scrutins peuvent etre encore en vigueur ou etre termines.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_scrutins_participatifs()
    {
        $this->db->from  ('scrutins_lances as sl, scrutins_lances_participants as slp');
		$this->db->where ('sl.groupe_id', $this->groupe_id);
        $this->db->where ('sl.scrutin_lance_id = slp.scrutin_lance_id');
        $this->db->where ('slp.enseignant_id', $this->enseignant_id);
		$this->db->where ('slp.vote_termine', 1);
		$this->db->order_by ('sl.lance_epoch', 'desc');

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Comptabiliser un vote
     *
     * -------------------------------------------------------------------------------------------- */
    function comptabiliser_vote($scrutin_reference, $scrutin_lance_id, $scrutin_id, $scrutin_lance_choix_id)
    {
        // Exemple d'erreur
        $erreur = array(
            'status'   => '',
            'code'     => '',
            'titre'    => '',
            'message'  => '',
            'view'     => NULL, // le nom du fichier de la view
            'solution' => ''
        );

        //
        // Verifier que cet enseignant n'a pas deja vote.
        // Verifier que cet enseignant peut voter.
        //

        $scrutins_a_voter = $this->extraire_scrutins_a_voter();
        
        if ( ! array_key_exists($scrutin_lance_id, $scrutins_a_voter))
        {
            return array(
                'titre'   => "Erreur lors de la votation",
                'message' => "Vous ne pouvez voter, ou vous avez déjà voté à ce scrutin."
            );
        }

        //
        // Verifier l'integrite de la reference
        //

        if ($scrutins_a_voter[$scrutin_lance_id]['scrutin_reference'] != $scrutin_reference)
        {
            return array(
                'titre'   => "Erreur lors de la votation",
                'message' => "La référence de ce scrutin est invalide."
            );
        }

		//
		// Extraire le scrutin de la liste des scrutins a voter
		//

        $scrutin = $scrutins_a_voter[$scrutin_lance_id];

		//
		// Verifier que le scrutin n'est pas termine.
		//

		if ($scrutin['termine'])
		{
            return array(
                'titre'   => "Scrutin terminé",
                'message' => "Ce scrutin est terminé et n'accepte plus de vote.",
            );
        }

		//
		// Verifier que le scrutin n'est pas echu
		//

		if ($this->now_epoch > $scrutin['echeance_epoch'])
		{
            return array(
                'view'    => 'scrutin_echu',
                'titre'   => "Scrutin échu",
                'message' => "La date d'échéance de ce scrutin est passée.",
            );
        }

        //
        // Verifier que le choix de l'enseignant correspond a un choix possible
        //

        $choix = $this->extraire_choix_lances($scrutin_lance_id);
        $choix = array_keys_swap($choix, 'scrutin_lance_choix_id');

        if ( ! array_key_exists($scrutin_lance_choix_id, $choix))
		{
            return array(
                'view'    => 'choix_introuvable',
                'titre'   => "Choix introuvble",
                'message' => "Votre choix n'a pu être trouvé.",
            );
        }

        //
        // Enregistrer le vote
        //

        $this->db->trans_begin();

        $vote_salt   = random_string('alnum', 12);
        $vote_sha256 = hash('sha256', $scrutin_reference . $scrutin_lance_choix_id . $this->now_epoch . $vote_salt);

        $data = array(
            'scrutin_reference' 	  => $scrutin_reference,
            'scrutin_lance_id'  	  => $scrutin_lance_id,
            'vote_salt'         	  => $vote_salt,
            'vote_sha256'       	  => $vote_sha256,
            'scrutin_lance_choix_id'  => $scrutin_lance_choix_id,
            'enseignant_id'     	  => $scrutin['anonyme'] ? NULL : $this->enseignant_id,
            'date'              	  => date_humanize($this->now_epoch, TRUE),
            'epoch'             	  => $this->now_epoch
        ); 

        $this->db->insert('scrutins_lances_votes', $data);

        //
        // Signaler que ce particpant a vote
        //

        $data = array(
            'vote_termine' => 1
        );

        $this->db->where ('scrutin_lance_id', $scrutin_lance_id);
        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->update('scrutins_lances_participants', $data);
        

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            return array(
                'titre'   => "Erreur lors de la votation",
                'message' => "Nous n'avons pu comptabiliser votre vote."
            );
        }

        $this->db->trans_commit();

        return substr($vote_sha256, 0, 10);
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les votes d'un scrutin
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_votes($scrutin_lance_id)
    {
        $this->db->from  ('scrutins_lances_votes');
        $this->db->where ('scrutin_lance_id', $scrutin_lance_id);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->result_array();
    }
}
