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
 * INSCRIPTION MODEL
 *
 * ============================================================================ */

class Inscription_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Inviter un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
	function inviter_enseignant($data)
    {
        $data['invitation_hash']  = random_string('alnum', 16);
        $data['expiration_epoch'] = date('U') + $this->config->item('inscription_invitation_expiration');
        $data['expiration_date']  = date_humanize($data['expiration_epoch'], TRUE);

        $this->db->insert('inscriptions_invitations', $data);

        return TRUE;
    } 

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier si le courriel d'un enseignant/etudiant est deja inscrit
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_deja_inscrit($courriel)
    {
        if ( ! filter_var($courriel, FILTER_VALIDATE_EMAIL))
        {
            return FALSE;
        }

        $this->db->from  ('enseignants');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return FALSE;
        }
                                                                                                                                                                                                                                  
        $this->db->from  ('etudiants');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier si le courriel est un courriel jetable (disposable email)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Liste : https://github.com/ivolo/disposable-email-domains
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_courriels_jetables($courriel)
    {
        // Attention
        //
        // FALSE : courriel jetable
        // TRUE  : courriel non jetable (courriel accepte)

        $domaine = strstr($courriel, '@');
        $domaine = str_replace('@', '', $domaine);
        $domaine = strtolower($domaine);

        $this->db->from  ('inscriptions_courriels_jetables');
        $this->db->where ('domaine', $domaine);
        $this->db->where ('actif', 1);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier si le courriel d'un enseignant est deja invite
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_deja_invite($courriel)
    {
        if ( ! filter_var($courriel, FILTER_VALIDATE_EMAIL))
        {
            return FALSE;
        }

        $this->db->from  ('inscriptions_invitations');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->where ('expiration_epoch >', date('U'));
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return FALSE;
        }

        $this->db->from  ('inscriptions');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->where ('clef_activation_expiration >', date('U'));
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire la clef (hash) d'invitation
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_clef_invitation($courriel)
    {
        $this->db->select('invitation_hash');
        $this->db->from  ('inscriptions_invitations');
        $this->db->where ('courriel', $courriel);
        $this->db->where ('efface', 0);
        $this->db->where ('expiration_epoch >', date('U'));
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $row = $query->row_array();

        return $row['invitation_hash'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les renseignements de l'enseignant(e) qui invite (l'hote).
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_enseignant_hote($enseignant_id)
    {
        $this->db->select('enseignant_id, nom, prenom, genre');
        $this->db->from  ('enseignants');
        $this->db->where ('enseignant_id', $enseignant_id);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les informations de l'invitation avec la clef.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_invitation($clef)
    {
        $this->db->select('invitation_id, enseignant_id, courriel');
        $this->db->from  ('inscriptions_invitations');
        $this->db->where ('invitation_hash', $clef);
        $this->db->where ('efface', 0);
        $this->db->where ('expiration_epoch >', date('U'));
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array();
    }

} // class
