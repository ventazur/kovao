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

/* ====================================================================
 *
 * DECONNEXION
 *
 * ==================================================================== */

class Deconnexion extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        $this->data['current_controller'] = strtolower(__CLASS__);
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
	{
		$this->load->view('commons/header', $this->data);
		$this->load->view('deconnexion');
		$this->load->view('commons/footer');
	}

    /* ------------------------------------------------------------------------
     *
     * Confirmation de deconnexion
     *
     * ------------------------------------------------------------------------ */
    public function confirmation()
    {
        $this->logged_in      = FALSE;
        $this->est_enseignant = FALSE;
        $this->est_etudiant   = FALSE;

        delete_cookie($this->config->item('email_cookie_name', 'cookies'));
        delete_cookie($this->config->item('password_cookie_name', 'cookies'));
        delete_cookie($this->config->item('type_cookie_name', 'cookies'));

        $this->session->sess_destroy();

        redirect(base_url());

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Terminer l'usurpation
     *
     * ------------------------------------------------------------------------ */
    public function terminer_usurpation()
    {
        delete_cookie('udata');

        redirect($this->usurp['usurp_redirect']);
        die;
    }
}
