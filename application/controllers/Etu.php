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

// ============================================================================
//
// ETU (ETU_DIANTS)
//
// ----------------------------------------------------------------------------
//
// Le panneau de controle des etudiants sur leur page principale
//
// ============================================================================

class Etu extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_etudiant)
        {
            redirect(base_url());
            exit;
        }

        $this->data['current_controller'] = strtolower(__CLASS__);
	}

    /* ----------------------------------------------------------------
     *
     * INDEX
     *
     * ---------------------------------------------------------------- */
	public function index()
    {
        // Routes :
        //      /               : Menu deroulant pour choisir une evaluation
        //                        + Afficher les evaluations en cours qui peuvent etre poursuivies
        //      etu/chercher    : Chercher une evaluation a remplir avec le code de reference (@TODO)
        //      etu/resultats   : Afficher toutes les evaluations remplis par l'etudiant, classe par semestres

		// Routes (Ajax) :
		//
		//		etu/ajouter_soumission : Ajouter une soumission aux resultats d'un etudiant

        $this->data['current_controller'] .= '/' . strtolower(__FUNCTION__);

        $this->_affichage(strtolower(__FUNCTION__));
    }

    /* ----------------------------------------------------------------
     *
     * (ajax) Ajouter une soumission aux resultats d'un etudiant
     *
     * ---------------------------------------------------------------- */
    public function ajouter_soumission()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = catch_post();

		if (empty($post_data['reference']) || empty($post_data['empreinte']))
		{
			echo json_encode(3);
			return;
		}

		$reference = strtolower(trim($post_data['reference']));
		$empreinte = strtolower(trim($post_data['empreinte']));

		if ( ! ctype_alpha($reference))
		{
			echo json_encode(0);
			return;
		}

		if ( ! ctype_alnum($empreinte))
		{
			echo json_encode(0);;
			return;
		}

		$result = $this->Etudiant_model->ajouter_soumission_resultats($reference, $empreinte);

		echo json_encode($result);
		return;
    }

    /* ----------------------------------------------------------------
     *
     * EVALUATIONS
     *
     * ---------------------------------------------------------------- */
    public function evaluations()
    {
        $this->data['current_controller'] .= '/' . strtolower(__FUNCTION__);

        $this->_affichage(strtolower(__FUNCTION__));
    }

    /* ----------------------------------------------------------------
     *
     * RESULTATS
     *
     * ---------------------------------------------------------------- */
    public function resultats()
    {
        $debug = FALSE; // active ou desactive les benchmarks

        $this->data['current_controller'] .= '/' . strtolower(__FUNCTION__);

        //
        // Extraire les soumissions
        //

        if ($debug) $this->benchmark->mark('soumissions_d');

        $soumissions = $this->Etudiant_model->extraire_soumissions($this->etudiant_id);
        $soumissions = array_keys_swap($soumissions, 'soumission_id');

        $this->data['soumissions'] = decompresser_soumissions($soumissions);

        if ($debug) $this->benchmark->mark('soumissions_f');

        //
        // Extraire les semestres
        //
        
        if ($debug) $this->benchmark->mark('semestres_d');

        $this->data['semestres'] = array();

        if ( ! empty($soumissions))
        {
            $semestre_ids = array_column($soumissions, 'semestre_id');
            $semestre_ids = array_unique($semestre_ids);

            $this->data['semestres'] = $this->Semestre_model->extraire_semestres(
                array(
                    'semestre_ids' => $semestre_ids
                )
            );
        }

        if ($debug) $this->benchmark->mark('semestres_f');

        //
        // Extraire les cours
        //
        
        if ($debug) $this->benchmark->mark('cours_d');
        
        $this->data['cours'] = array();

        if ( ! empty($soumissions))
        {
            $cours_ids = array_column($soumissions, 'cours_id');
            $cours_ids = array_unique($cours_ids);
            
            $this->data['cours'] = $this->Cours_model->lister_cours(
                array(
                    'cours_ids' => $cours_ids
                )
            );
        }

        if ($debug) $this->benchmark->mark('cours_f');

        //
        // Les ponderations
        //

        $this->data['ponderations'] = array();
        $this->data['mes_ponderations'] = array();

        if ($this->config->item('evaluation_ponderation') && ! empty($soumissions))
        {
            $evaluation_ids = array_column($soumissions, 'evaluation_id');
            $evaluation_ids = array_unique($evaluation_ids);

            //
            // Extraire les ponderations officielles ou officieuses
            //

            if ($debug) $this->benchmark->mark('ponderations_d');

            $this->data['ponderations'] = $this->Evaluation_model->extraire_ponderations_par_soumissions($soumissions);

            if ($debug) $this->benchmark->mark('ponderations_f');

            //
            // Extraire les ponderations entrees par l'etudiant
            //
            
            if ($debug) $this->benchmark->mark('mes_ponderations_d');
            
            $this->data['mes_ponderations'] = $this->Evaluation_model->extraire_mes_ponderations_par_soumissions($soumissions);
            
            if ($debug) $this->benchmark->mark('mes_ponderations_f');
        }

        //
        // Performance 
        //
        // - Le rang par evaluation
        // - L'ecart a la moyenne
        //

        if ($debug) $this->benchmark->mark('performances_d');

        $this->data['perf'] = array();

        if ( ! empty($soumissions))
        {
            $this->data['perf'] = $this->Etudiant_model->determiner_performance($soumissions);
        }
        
        if ($debug) $this->benchmark->mark('performances_f');

        //
        // Rang par semestres / cours
        //

        if ($debug) $this->benchmark->mark('rangs_d');

        $this->data['rangs_semestres_cours'] = array();

        if (
            $this->etudiant['montrer_rang_cours']                &&
            $this->config->item('evaluation_ponderation')        && 
            $this->config->item('evaluation_montrer_rang_cours') && 
            ! empty($soumissions)
           )
        {
            // $this->data['rangs_semestres_cours'] = $this->Etudiant_model->determiner_rangs_cours($this->etudiant_id);
            $this->data['rangs_semestres_cours'] = $this->Etudiant_model->determiner_rangs_cours_complet($this->etudiant_id);

            krsort($this->data['rangs_semestres_cours']);
        }

        if ($debug) $this->benchmark->mark('rangs_f');

        //
		// Benchmark
		//
		// Cette fonction peut devenir lente alors trouvons les goulots d'etranglement.
        //

        if ($debug)
        {
            $benchmark = array(
                'soumissions'       => $this->benchmark->elapsed_time('soumissions_d', 'soumissions_f'),
                'semestres'         => $this->benchmark->elapsed_time('semestres_d', 'semestres_f'),
                'cours'             => $this->benchmark->elapsed_time('cours_d', 'cours_f'),
                'ponderations'      => $this->benchmark->elapsed_time('ponderations_d', 'ponderations_f'),
                'mes_ponderations'  => $this->benchmark->elapsed_time('mes_ponderations_d', 'mes_ponderations_f'),
                'performances'      => $this->benchmark->elapsed_time('performances_d', 'performances_f'),
                'rangs'             => $this->benchmark->elapsed_time('rangs_d', 'rangs_f')
            );

            p($benchmark);
        }

        $this->_affichage(strtolower(__FUNCTION__));
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) (Resultats) Ajuster la ponderation
     *
     * ------------------------------------------------------------------------ */
	public function resultats_ajuster_ponderation()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        //
        // Validation des entrees
        //

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id', 'semestre_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'ponderation' :
					$validation_rules = 'required';
					break;
			}
			
            if ( ! empty($validation_rules))
			{
				$this->form_validation->set_rules($k, '', $validation_rules);
                unset($validation_rules);
            }
        }

        if ($this->form_validation->run() == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');

            $errors = array();
            foreach($post_data as $k => $v)
            {
                if (form_error($k) !== '')
                    $errors[$k] = form_error($k);
            }

            echo json_encode($errors);
            return FALSE;
        }

        //
        // Ajuster la ponderation
        //

		$this->Evaluation_model->ajuster_ponderation($post_data);

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) (Resultats) Effacer la ponderation
     *
     * ------------------------------------------------------------------------ */
	public function resultats_effacer_ponderation()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        //
        // Validation des entrees
        //

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('evaluation_id', 'semestre_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
	
        //
        // Effacer la ponderation
        //

		$this->Evaluation_model->effacer_ponderation($post_data['evaluation_id'], $post_data['semestre_id']);

		echo json_encode(TRUE);
		return;
	}

    /* ----------------------------------------------------------------
     *
     * AFFICHAGE
     *
     * ---------------------------------------------------------------- */
    public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

        switch($page)
        {
            case 'evaluations' :
            case 'resultats' :
                $this->load->view('etudiants/' . $page, $this->data);
                break;
            default :
                $this->load->view('etudinats/bienvenue', $this->data);
        }

        $this->load->view('commons/footer', $this->data);
    }
}
