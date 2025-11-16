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
 * SCRUTIN
 *
 * ----------------------------------------------------------------------------
 *
 * Les scrutins sont reserves aux groupes.
 *
 * ============================================================================ */

class Scrutin extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->config->item('scrutins') || ! $this->est_enseignant || ! $this->groupe_id)
        {
            redirect(base_url());
            exit;
        }

        $this->load->model('Vote_model');

        $this->data['current_controller'] = strtolower(__CLASS__);
	}

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
	public function _remap($method, $args = array())
    {
		if (ctype_alpha($method) && strlen($method) == 10)
        {
            if (empty($args))
            {
                $this->scrutin($method);
                return;
            }

            $valid_actions = array(
                'voter',        // Voter a un scrutin
                'resultats',    // Resultats d'un scrutin
                'terminer'      // Terminer un scrutin
            );

            $action = $args[0];
            $arg1   = isset($args[1]) ? $args[1] : NULL;

            if (in_array($action, $valid_actions))
            {
                $action = '_' . $action;

                // Les scrutins a terminer doivent etre confirme avec un 1 au 2e parametre

                switch($action)
                {
                    case '_terminer':
                        $this->$action($method, $arg1);
                        break;

                    default:
                        $this->$action($method);
                }

                return;
            }
		}

        redirect(base_url() . 'scrutins');
        exit;
	}

    /* ------------------------------------------------------------------------
     *
     * Scrutin
     *
     * ------------------------------------------------------------------------
     *
     * Montrer le scrutin
     *
     * ------------------------------------------------------------------------ */
	public function scrutin($scrutin_reference)
    {
        $scrutin = $this->Vote_model->extraire_scrutin_par_reference($scrutin_reference);

        //
        // Scrutin introuvable
        //

        if ($scrutin == FALSE || empty($scrutin))
        {
            $data = array(
                'titre'   => 'Scrutin introuvable',
                'message' => "Nous n'avons pu trouver ce scrutin, ou vous n'avez pas la permission de voter à ce scrutin."
            );

            $this->data = array_merge($this->data, $data);

            $this->_affichage('scrutin_erreur_gabarit');
            return;
        }

        //
        // Scrutin termine
        //

        if ($scrutin['termine'])
        {
            $data = array(
                'titre'   => 'Scrutin terminé',
                'message' => "Ce scrutin est terminé et ne peut plus être répondu."
            );

            $this->data = array_merge($this->data, $data);

            $this->_affichage('scrutin_erreur_gabarit');
            return;
        }

        //
        // Scrutin echu
        //

        if ($scrutin['echeance_epoch'] < $this->now_epoch)
        {
            $data = array(
                'titre'   => 'Scrutin échu',
                'message' => "Ce scrutin est échu et ne peut plus être répondu."
            );

            $this->data = array_merge($this->data, $data);

            $this->_affichage('scrutin_erreur_gabarit');
            return;
        }

        //
        // Verifier si l'enseignant a deja repondu au scrutin
        //

        if ( ! $this->Vote_model->verifier_permission_de_voter($scrutin_reference))
        {
            return FALSE;
        }

        //
        // Verifier si l'enseignant a deja repondu au scrutin
        //

        $this->data['deja_vote'] = $this->Vote_model->verifier_enseignant_deja_vote($scrutin_reference);

        // @TODO
       
        $this->data['scrutin']           = $scrutin;
        $this->data['scrutin_reference'] = $scrutin_reference;
        $this->data['scrutin_id']        = $scrutin['scrutin_id'];
        $this->data['scrutin_lance_id']  = $scrutin['scrutin_lance_id'];
        $this->data['choix']             = $scrutin['choix'];
        $this->data['documents']         = $scrutin['documents'];

        $this->data['previsualisation']  = FALSE;

        $this->_affichage('scrutin');
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Soumission d'un vote
     *
     * ------------------------------------------------------------------------ */
    public function _voter()
    {
        if (($post_data = catch_post(array('ids' => array('scrutin_id', 'scrutin_lance_id', 'scrutin_lance_choix_id', 'enseignant_id')))) === FALSE)
        {
            redirect(base_url());
            exit;
        }

        //
        // Verifier que le scrutin n'est pas en mode previsualisation.
        //

        if (empty($post_data['scrutin_reference']) || $post_data['previsualisation'] == TRUE)
        {
            $this->_affichage('scrutin_previsualisation');
            return;
        }

        $scrutin_id        = $post_data['scrutin_id'];
        $scrutin_lance_id  = $post_data['scrutin_lance_id'];
        $scrutin_reference = $post_data['scrutin_reference'];

        //
        // Verifier les donnees
        //

        if ( ! is_numeric($post_data['scrutin_lance_choix_id']))
        {
            generer_erreur('SCRUV661', "Nous n'avons pas trouvé votre choix de vote.");
            exit;
        }

        //
        // Comptabilise le vote
        //

        $result = $this->Vote_model->comptabiliser_vote($scrutin_reference, $scrutin_lance_id, $scrutin_id, $post_data['scrutin_lance_choix_id']);

        if (is_string($result) && strlen($result) == 10)
        {
            // Inclure l'empreinte dans le message de confirmation de vote pour les scrutins anonymes
            if (array_key_exists('anonyme', $post_data) && $post_data['anonyme'])
            {
                $this->data['empreinte'] = $result;
            }

            $this->_affichage('scrutin_vote_fait');
            return;
        }
        elseif (is_array($result))
        {
            if (array_key_exists('view', $result) && ! empty($result['view']))
            {
                // @TODO
                // Verifier que le fichier de la view existe, sinon rediriger vers un message d'erreur general

                $this->_affichage($result['view']);
                return;
            }
            else
            {
                $this->data = array_merge($this->data, $result);
                $this->_affichage('scrutin_erreur_gabarit');
                return;
            }
        }
        else
        {
            generer_erreur('SCRU9123', "Il y a eu une erreur suite à l'enregistrement de votre vote. Si vous ne pouvez pas voter à nouveau, votre vote a été enregistré correctement.");
            exit;
        }

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Resultats d'un scrutin
     *
     * ------------------------------------------------------------------------ */
    public function _resultats($scrutin_reference)
    {
        if (($scrutin = $this->Vote_model->extraire_scrutin_par_reference($scrutin_reference)) === FALSE)
        {
            // Ce scrutin n'existe pas.

            redirect(base_url() . 'scrutins');
            return;
        }

        // Verifier que l'enseignant a deja vote
        // (Les enseignants qui n'ont pas encore vote ne peuvent consulter les resultats)

        if ( ! $this->Vote_model->verifier_enseignant_deja_vote($scrutin_reference))
        {
            redirect(base_url() . 'scrutin/' . $scrutin_reference);
            return;
        }

        $this->data['scrutin']            = $scrutin;
        $this->data['scrutin_id']         = $scrutin['scrutin_id'];
        $this->data['scrutin_lance_id']   = $scrutin['scrutin_lance_id'];
        $this->data['choix']              = array_keys_swap($scrutin['choix'], 'scrutin_lance_choix_id');
        $this->data['documents']          = $scrutin['documents'];
        $this->data['participants']       = $this->Vote_model->extraire_participants_scrutin_lance($scrutin['scrutin_lance_id']);
        $this->data['enseignants']        = $this->Enseignant_model->lister_enseignants();
        $this->data['participants_total'] = count($scrutin['participants']);

        $scrutin_id       = $scrutin['scrutin_id'];
        $scrutin_lance_id = $scrutin['scrutin_lance_id'];

        //
        // Compilation des votes
        //

        $votes = $this->Vote_model->extraire_votes($scrutin_lance_id);

        $resultats       = array();
        $resultats_total = 0;

        if ( ! empty($votes))
        {
            foreach($votes as $v)
            {
                $resultats_total++;

                if ( ! array_key_exists($v['scrutin_lance_choix_id'], $resultats))
                {
                    $resultats[$v['scrutin_lance_choix_id']] = 1;
                    continue;
                }

                $resultats[$v['scrutin_lance_choix_id']]++;
            }
        }

        if ($scrutin['anonyme'])
        {
            shuffle($votes);
        }
        else
        {
            $votes = array_keys_swap($votes, 'enseignant_id');
        }

        $this->data['votes']           = $votes;
        $this->data['resultats']       = $resultats;
        $this->data['resultats_total'] = $resultats_total;

        $this->_affichage('resultats');
    }

    /* ------------------------------------------------------------------------
     *
     * Terminer un scrutin
     *
     * ------------------------------------------------------------------------ */
    public function _terminer($scrutin_reference, $epoch)
    {
        $epoch_diff = 2*60; // 2 minutes

        if ( ! is_numeric($epoch) || ($this->now_epoch - $epoch) > $epoch_diff)
        {
            redirect(base_url() . 'scrutins/gerer');
            exit;
        }

        // Verifier la permission de terminer le scrutin
        
        $scrutin = $this->Vote_model->extraire_scrutin_par_reference($scrutin_reference);

        if (
            is_array($scrutin)                                  && 
            ! empty($scrutin)                                   && 
            array_key_exists('enseignant_id', $scrutin)         &&
            $scrutin['enseignant_id'] == $this->enseignant_id
           )
        {
            $this->Vote_model->terminer_scrutin($scrutin['scrutin_lance_id']);
        }

        redirect(base_url() . 'scrutins/gerer');
        exit;

    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

        if (empty($page))
        {
            $this->load->view('scrutins/scrutins', $this->data);
        }
        else
        {
            $this->load->view('scrutins/' . $page, $this->data);
        }

        $this->load->view('commons/footer');
    }
   
}
