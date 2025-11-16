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
 * ERREUR
 *
 * ----------------------------------------------------------------------------
 *
 * Gestionnaire des erreurs affichees aux utilisateurs.
 *
 * ============================================================================ */

class Erreur extends MY_Controller 
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
        $this->data['erreur'] = array(
            'code'    => $this->session->flashdata('erreur_code'),
            'message' => $this->session->flashdata('erreur_message'),
            'url'     => $this->session->flashdata('erreur_url')
        );

        if (empty($this->data['erreur']['code']) || empty($this->data['erreur']['message']))
        {
            redirect(base_url());
            exit;
        } 

        $this->_affichage();
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Code
     *
     * ------------------------------------------------------------------------ */
    public function code()
    {
        // Un hack avec le helper 'generer_erreur' pour que le code d'erreur s'affiche dans les logs.

        $this->index();

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Erreurs specifiques
     *
     * ------------------------------------------------------------------------ */
    public function spec($page)
    {
        if (empty($page))
        {
            redirect(base_url()); 
            exit;
        }

        $alerte = array();

        switch($page)
        {
            case 'connexion' :
                $alerte['code']       = 'CNX911';
                $alerte['desc']       = "Vous avez excédé le nombre de tentatives de connexion alloué.";
                $alerte['extra']      = $this->agent->agent_string(); 
                $alerte['importance'] = 3;
                break;

            case 'cookie' :
                $alerte['code']       = 'COK101';
                $alerte['desc']       = "Votre fureteur bloque les cookies.";
                $alerte['extra']      = $this->agent->agent_string(); 
                $alerte['importance'] = 0;
                break;

            case 'plateforme' :
                $alerte['code']       = 'PTF101';
                $alerte['desc']       = "Votre plateforme n'est pas supportée.";
                $alerte['extra']      = $this->agent->agent_string(); 
                $alerte['importance'] = 0;
                break;

            case 'EVPLN1' :
                
                $this->data['evaluation_debut_epoch'] = $this->session->flashdata('evaluation_debut_epoch') ?? NULL;
                break;

            case 'custom' :

                if (($erreur_info = $this->session->userdata('erreur_info')) == NULL)
                {
                    redirect(base_url());
                    return;
                }

                $alerte['code']  = @$erreur_info['code'];
                $alerte['desc']  = @$erreur_info['message'];
                $alerte['extra'] = @$erreur_info['extra'];

                $this->data['erreur']['code']     = $erreur_info['code'];
                $this->data['erreur']['message']  = $erreur_info['message'];
                $this->data['erreur']['solution'] = $erreur_info['solution'];

                break;

            default:
                if (array_key_exists('erreur_info', $_SESSION) && ! empty($_SESSION['erreur_info']))
                {
                    $erreur_info = $_SESSION['erreur_info'];
                    unset($_SESSION['erreur_info']);

                    $alerte['code']  = @$erreur_info['code'];
                    $alerte['desc']  = @$erreur_info['message'];
                    $alerte['extra'] = @$erreur_info['extra'];
                }

                break;
        }

        if ( ! empty($alerte))
        {
            log_alerte($alerte);
        }

        $this->load->view('commons/header', $this->data);
        $this->load->view('erreur/spec_' . $page, $this->data);
        $this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);
        $this->load->view('erreur/erreur', $this->data);
        $this->load->view('commons/footer');
    }
}
