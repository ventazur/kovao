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
 * ENSEIGNANT
 *
 * ----------------------------------------------------------------------------
 *
 * Les methodes pertinentes aux enseignants
 *
 * ============================================================================ */

class Enseignant extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        if ($this->enseignant_id != 1)
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
    public function _remap($enseignant_id = NULL)
    {
        //
        // Verification de l'enseignant_id
        //
        
        if ( ! ctype_digit($enseignant_id))
        {
            $enseignant_id = $this->enseignant_id;
        }

        //
        // Verification de l'enseignant
        //

        if (($enseignant = $this->Enseignant_model->extraire_enseignant($enseignant_id)) === FALSE)
        {
            die("Cet enseignant est introuvable.");
            redirect(base_url());
            exit;
        }

        if ( ! $enseignant['actif'] || $enseignant['efface'])
        {
            die("Cet enseignant est inactif.");
            redirect(base_url());
            exit;
        }

        $args = $this->uri->uri_to_assoc(3);

        //
        // Les arguments valides
        //

        $args_valides = array();

        foreach($args as $k => $v)
        {
            if ( ! in_array($k, $args_valides))
            {
                die("Il n'est pas possible de répondre à votre requête.");
                redirect(base_url());
                exit;
            }
        }

        $this->_voir($enseignant_id, $enseignant, $args);
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        die("Il n'a a rien à faire ici.");
    }

    /* ------------------------------------------------------------------------
     *
     * Voir
     *
     * ------------------------------------------------------------------------ */
    function _voir($enseignant_id, $enseignant)
    {
        $this->_affichage('enseignant');
    }

    /* ------------------------------------------------------------------------
     *
     * Editeur
     *
     * ------------------------------------------------------------------------ */
    function _editeur($enseignant_id, $enseignant)
    {
		// @TODO

    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);


        switch ($page) 
        {
            case 'enseignant' :
                $this->load->view('enseignant/enseignant');
                break;

            default:
                $this->load->view('enseignant/enseignant');
        }

        $this->load->view('commons/footer', $this->data);
    }
}
