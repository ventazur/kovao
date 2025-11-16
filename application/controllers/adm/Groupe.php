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
 * ADM (ADMIN) > GROUPE
 *
 * ============================================================================ */

use jlawrence\eos\Parser;

class Groupe extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! ($this->est_enseignant && $this->enseignant['privilege'] > 89))
        {
            redirect(base_url());
            exit;
        }

        // Pourquoi ? (2020-03-19)
        unset($this->data['semestre_id']);

        $this->load->model('Admin_model');

        $this->data['sous_dir'] = 'adm';
        $this->data['onglet']   = $this->uri->segment(3);
    }

    /* ------------------------------------------------------------------------
     *
     * Admin > Index
     *
     * ------------------------------------------------------------------------ */
    public function index()
    {
        $this->data['onglet'] = 'alertes';

        $this->alertes();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Alertes
     *
     * ------------------------------------------------------------------------ */
    public function alertes()
    {
        //
        // Extraire la liste des arguments
        //

        $args = $this->uri->uri_to_assoc(4);

        //
        // Definir l'importance des alertes a afficher
        //

        $importance = 2;

        if (array_key_exists('importance', $args) && $args['importance'] > 0 && $args['importance'] < 10)
        {
            $importance = $args['importance'];
        }
    
        // Extraire les alertes selon les arguments

        $alertes = $this->Admin_model->extraire_alertes(
            array(
                'groupe_id'  => $this->groupe_id,
                'limite'     => 100,
                'importance' => $importance
            )
        );

        $data = array(
            'alertes'            => $alertes,
            'alertes_importance' => $importance,
        );

        $this->data = array_merge($data, $this->data);

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Activite
     *
     * ------------------------------------------------------------------------ */
    public function activite()
    {
        $activite = $this->Admin_model->voir_activite(
            array(
                'groupe_id'   => $this->groupe_id,
                'apres_epoch' => 60*60*24*10 // 10 jours
            )
        );
    
        // L'activite sommaire

        $activite_temps = array(
            60*5 		=> 0,  // 5m
            60*15 		=> 0, // 15m
            60*60 		=> 0, // 1h
            60*60*6 	=> 0, // 6h
            60*60*12 	=> 0, // 12h
            60*60*24 	=> 0, // 24h
            60*60*24*3  => 0, // 3j
            60*60*24*7 	=> 0  // 7j
        );

        $activite_total = 0;

        $activite_complete = array_merge(
            $activite['non_connectes'],
            $activite['etudiants'],
            $activite['enseignants']
        );

        if ( ! empty($activite_complete))
        {
            $now = date('U');

            foreach($activite_complete as $a)
            {
                $activite_total++;

                $epoch_diff = $now - $a['epoch'];

                foreach($activite_temps as $k => $v)
                {
                    if ($epoch_diff < $k)
                    {
                        $activite_temps[$k]++;
                    }
                }
            }
        }

        $data = array(
            'activite' 	     => $activite,
            'activite_temps' => $activite_temps,
            'activite_total' => $activite_total
        );

        $this->data = array_merge($data, $this->data);

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Enseignants
     *
     * ------------------------------------------------------------------------ */
    function enseignants() 
    {
        $data['enseignants'] = $this->Enseignant_model->lister_enseignants();

        $data['evaluations'] = $this->Evaluation_model->lister_evaluations(
            array(
                'public'    => FALSE,
                'actif'     => NULL
            )
        );

        // Compiler les evaluations par enseignant

        $evaluations_enseignants = array();

        if ( ! empty($data['evaluations'])) 
        {
            foreach($data['evaluations'] as $e)
            {
                if ( ! array_key_exists($e['enseignant_id'], $evaluations_enseignants))
                    $evaluations_enseignants[$e['enseignant_id']] = array();

                $evaluations_enseignants[$e['enseignant_id']][] = $e['evaluation_id'];
            }
        }

        $data['evaluations_enseignants'] = $evaluations_enseignants;

        $data['evaluations_selectionnees'] = $this->Enseignant_model->nombre_evaluations_selectionnees(
            array(
                'semestre_id' => $this->semestre_id
            )
        );

        $soumissions = $this->Admin_model->extraire_nombre_soumissions_enseignants(
            array_keys(
                $data['enseignants']
            )
        );

        $data['semestre']             = $this->semestre;
        $data['semestre_id']          = $this->semestre_id;
        $data['soumissions_total']    = $soumissions['soumissions_total'];
        $data['soumissions_semestre'] = $soumissions['soumissions_semestre'];

        $this->data = array_merge($data, $this->data);

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Etudiants
     *
     * ------------------------------------------------------------------------ */
    function etudiants()
    {
        $data['etudiants'] = $this->Etudiant_model->extraire_etudiants(array('limite' => 101));
        $data['etudiants_soumissions'] = $this->Etudiant_model->nombre_soumissions_etudiants(array_keys($data['etudiants']));

        $this->data = array_merge($data, $this->data);

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Enseignant
     *
     * ------------------------------------------------------------------------ */
    function enseignant()
    {
        $enseignant_id = $this->uri->uri_to_assoc()['enseignant'];

        $data['enseignant'] = $this->Enseignant_model->extraire_enseignant($enseignant_id);

        $data['cours'] = $this->Cours_model->lister_cours(
            array(
                'groupe_id' => $this->groupe_id
            )
        );

        $data['evaluations'] = $this->Evaluation_model->lister_evaluations(
            array(
                'enseignant_id' => $enseignant_id,
                'public'        => FALSE,
                'actif'         => NULL,
            )
        );

        $data['evaluations_selectionnees'] = $this->Enseignant_model->enseignants_evaluations_selectionnees(
            $this->groupe_id,
            $this->semestre_id,
            array(
                'enseignant_id' => $enseignant_id
            )
        );

        $this->data = array_merge($data, $this->data);

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Soumissions
     *
     * ------------------------------------------------------------------------ */
    function soumissions()
    {
        $soumissions = $this->Evaluation_model->dernieres_soumissions(
            NULL, 
            array(
                'groupe_id' => NULL,
                'limite'	=> 500,
                'admin'     => TRUE
            )
        );
        $soumissions = array_keys_swap($soumissions, 'soumission_id');

        // Calcul de la duree
        if ( ! empty($soumissions))
        {
            foreach($soumissions as $s)
            {
                $soumission_id = $s['soumission_id'];
                $soumissions[$soumission_id]['duree'] = calculer_duree($s['soumission_debut_epoch'], $s['soumission_epoch']);
            }
        }

        //
        // Les dernieres et meilleures journees de soumissions
        //
        
        $journees = array();			 // Nb de soumissions par jour
        $dernieres_journees = array();	 // Nb de soumissions pour les derniers jours
        $meilleures_journees = array();  // Les journees avec le plus de soumissions

        $stats = $this->Evaluation_model->soumissions_stats(
            array(
                'groupe_id' => $this->groupe_id
            )
        );

        if ( ! empty($stats))
        {
            $i=1;

            foreach($stats as $s)
            {
                // Determiner le nombre de soumissions par jour

                $date = date_humanize($s['soumission_epoch']);

                if ( ! array_key_exists($date, $journees))
                {
                    $journees[$date] = 1;
                    continue;
                }

                $journees[$date] += 1;
            }
        }

        arsort($journees);
        $meilleures_journees = array_slice($journees, 0, 30);

        krsort($journees);
        $dernieres_journees = array_slice($journees, 0, 60);

        $this->data = array_merge($this->data, 
            array(
                'soumissions' 		  => $soumissions,
                'dernieres_journees'  => $dernieres_journees,
                'meilleures_journees' => $meilleures_journees,
                'groupes'             => $this->Groupe_model->lister_groupes_simplement(),
                'enseignants' 		  => $this->Enseignant_model->lister_enseignants(array('groupe_id' => NULL))
            )
        );

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Consultations
     *
     * ------------------------------------------------------------------------ */
    function consultations()
    {
        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Evaluations
     *
     * ------------------------------------------------------------------------ */
    function evaluations()
    {
        // Extraire les evaluations pouvant etre remplies par les etudiants 

        $evaluations = $this->Evaluation_model->extraire_toutes_evaluations_selectionnees(
            array(
                'groupe_id'   => $this->groupe_id,
                'semestre_id' => $this->semestre_id
            )
        );

        $data['evaluations'] = $evaluations;

        // Extraire tous les etudiants en train de remplir des evaluations

        $etudiants_redaction = array();

        if ( ! empty($evaluations))
        {
            $min_epoch = min(array_column($evaluations, 'ajout_epoch'));

            $evaluations_references = array_column($evaluations, 'evaluation_reference');

            $etudiants_redaction = $this->Etudiant_model->extraire_etudiants_redaction($evaluations_references, $min_epoch);
        }

        $data['etudiants_redaction'] = $etudiants_redaction;

        $this->data = array_merge($data, $this->data);

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Groupe > Affichage 
     *
     * ------------------------------------------------------------------------ */
	public function _affichage()
    {
        $this->load->view('commons/header', $this->data);
        $this->load->view($this->data['sous_dir'] . '/groupe', $this->data);
        $this->load->view('commons/footer');
    }
}
