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
 * CLI - Command Line Interpreter
 *
 * ============================================================================ */

class Cli extends CI_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! is_cli())
        {
            generer_erreur('CLI483810', 'Ce controlleur doit être exécuté par la ligne de commande uniquement.');
            die;
        }

        //
        // Determiner s'il s'agit de l'environnement de production ou de developpement.
        //

        $this->is_DEV = FALSE;

        if ($this->config->item('is_DEV'))
        {
            $this->is_DEV = TRUE;

			$this->db->close();
			$this->config->set_item('active_group', 'dev');
			$this->load->database();
        }
        
        //
        // Initialisation de la configuration dynamique
        //

        $this->Settings_model->initialisation();
        
        //
        // Intialisation des variables globales
        //

        $this->now_epoch = date('U');
        
        //
        // Initialisation des variables pour les tables de la base de donnees.
        //

        if ( ! empty($this->config->item('database_tables'))) 
        {
            foreach($this->config->item('database_tables') as $table)
            {
                $this->{$table . '_t'} = $table;
            }
        }

        //
		// Initialisation de l'encryption 
        //

        $this->encryption->initialize(
            $this->config->item('encryption_settings')
        );

        // 
        // Chargement du model pour le CLI
        //

        $this->load->model('Cli_model');
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        echo 'index';
    }

    /* ------------------------------------------------------------------------
     *
     * terminer_evaluations_expirees
     *
     * ------------------------------------------------------------------------
     *
     * Cette function termine toutes les evaluations dont la date (et l'heure)
     * de terminaison est arrivee a echeance. 
     * Les evaluations en cours de redaction des etudiants sont enegistrees et
     * corrigees.
     *
     * ------------------------------------------------------------------------ */
    public function terminer_evaluations_expirees()
    {
        echo $this->Cli_model->terminer_evaluations_expirees();
    }

    /* ------------------------------------------------------------------------
     *
     * terminer_evaluations_temps_limite
     *
     * ------------------------------------------------------------------------
     *
     * Cette function termine toutes les evaluations dont les etudiants ont
     * depasse le temps limite alloue pour l'evaluation.
     * Les evaluations en cours de redaction des etudiants sont enegistrees et
     * corrigees.
     *
     * ------------------------------------------------------------------------ */
    public function terminer_evaluations_temps_limite()
    {
        echo $this->Cli_model->terminer_evaluations_temps_limite();
    }

    /* ------------------------------------------------------------------------
     *
     * purger_sessions
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a purger les sessions expirees de la base de donnees.
     * Les sessions plus vieilles que 7 jours seront purgees.
     * Ceci devrait se faire automatiquement mais pour une raison inconnue,
     * cela ne se fait pas, alors je le fais manuellement chaque soir en
     * executant cette fonction.
     *
     * ------------------------------------------------------------------------ */
    public function purger_sessions()
    {
        echo $this->Cli_model->purger_sessions();
    }

    /* ------------------------------------------------------------------------
     *
     * purger_soumissions (OBSOLETE)
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a purger les soumissions effacees.
     *
     * ------------------------------------------------------------------------ */
    public function OBSOLETE_purger_soumissions()
    {
        echo $this->Cli_model->purger_soumissions();
	}

    /* ------------------------------------------------------------------------
     *
     * purger_soumissions
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a purger les soumissions effacees.
     *
     * ------------------------------------------------------------------------ */
    public function effacer_etudiants_inactifs()
    {
        echo $this->Admin_model->effacer_etudiants_inactifs();
    }

    /* ------------------------------------------------------------------------
     *
     * purger_documents
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a purger les documents effaces.
     * L'argument '$effacement' doit etre a 1 pour vraiment effacer les fichiers,
     * autrement, il y aura affichage de ce qui aurait du etre efface.
     *
     * ------------------------------------------------------------------------ */
    public function purger_documents($effacement = 0)
    {
        echo $this->Cli_model->purger_documents($effacement);
    }

    /* ------------------------------------------------------------------------
     *
     * purger_items
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a purger les items effaces:
     * evaluations, blocs, variables, questions et reponses
     *
     * ------------------------------------------------------------------------ */
    public function purger_items()
    {
        echo $this->Cli_model->purger_items();
	}

    /* ------------------------------------------------------------------------
     *
     * purger_suppressions
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a purger les traces expirees.
     *
     * ------------------------------------------------------------------------ */


    /* ------------------------------------------------------------------------
     *
     * modifier_table_activite
     *
     * ------------------------------------------------------------------------
     *
     * Cette function sert a modifier la table activite afin de mettre a jour
     * les champs 'annee', 'mois', 'jour', 'heure', 'minute' qui servent
     * a generer des statistiques de frequentation du site.
     *
     * La table ete modifiee mais cette function pourrait etre utilise
     * eventuellement, si les modifications ne sont pas faites a chaque requete
     * de l'utilisateur pour gagner des microsecondes.
     *
     * ------------------------------------------------------------------------ */
    public function modifier_table_activite()
    {
        // return $this->Admin_model->activite_modifier_table();
    }

    /* ------------------------------------------------------------------------
     *
     * nouveaux_courriels_jetables
     *
     * ------------------------------------------------------------------------
     *
     * Cette function met a jour la liste des courriels jetables.
     *
     * ------------------------------------------------------------------------ */
    public function nouveaux_courriels_jetables($securite = NULL)
    {
        return $this->Cli_model->nouveaux_courriels_jetables($securite);
    }
}
