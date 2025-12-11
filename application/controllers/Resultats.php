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
 * RESULTATS
 *
 * ============================================================================ */

class Resultats extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

		$this->load->model(array('Ecole_model', 'Groupe_model', 'Admin_model'));
	}

    /* ------------------------------------------------------------------------
     *
     * remap
     *
     * ------------------------------------------------------------------------ */
	public function _remap($method)
    {
        //
		// Les methodes AJAX
        //

        $ajax_methods = array(
            'effacer_soumission', 
            'remettre_zero_vues', 
            'rendre_visible', 'rendre_invisible', 
            'ajuster_ponderation', 'effacer_ponderation'
        );

		if (in_array($method, $ajax_methods))
		{
			if ( ! $this->input->is_ajax_request()) 
			{
				redirect(base_url() . $this->data['current_controller']);
				exit;
			}

			$this->$method();
			return;
		}

        //
		// Les methodes non AJAX
        //

        // Les versions fonctionelles :
        // - version 2 (index2)
        // - version 4 (index4)

        $default_method = 'index4';

        if ($method == 'index')
        {
            $this->$default_method(); // version 4
            return;
        }

        $valid_methods = array('semestre', 'evaluation');

        if ( ! in_array($method, $valid_methods))
        {
            redirect(base_url() . $this->data['current_controller']);
            exit;
        }

        switch($method)
        {
            //
            // Lister toutes les evaluations pour un semestre specifique
            //

            case 'semestre' :

                $args = $this->uri->uri_to_assoc(2);

                if (array_key_exists('semestre', $args) && strlen($args['semestre']) == 5 && ctype_alnum($args['semestre']))
                {
                    $semestre_id = $this->_semestre($args['semestre']);

                    $this->$default_method($semestre_id);
                    return;
                }
                break;

            //
            // Lister toutes les soumissions pour une evaluation d'un semestre specifique
            //

            case 'evaluation' :

                $args = $this->uri->uri_to_assoc(2);

                if (
                    array_key_exists('evaluation', $args) && array_key_exists('semestre', $args) &&
                    ctype_digit($args['evaluation']) && ctype_digit($args['semestre'])
                   )
                {
                    $this->_evaluation($args['evaluation'], $args['semestre']);
                    return;
                }
                break;
        }

        redirect(base_url());
        exit;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Ajuster la ponderation
     *
     * ------------------------------------------------------------------------ */
	public function ajuster_ponderation()
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
     * (AJAX) Effacer la ponderation
     *
     * ------------------------------------------------------------------------ */
	public function effacer_ponderation()
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

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Rendre une soumission (evaluation corrigee) visible
     *
     * ------------------------------------------------------------------------ */
	public function rendre_visible()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post()) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'soumission_ids' :
					$validation_rules = 'required';
					break;

                case 'soumission_references' :
                    // $validation_rules = 'required';
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

		$soumission_ids = unserialize(htmlspecialchars_decode($post_data['soumission_ids']));

		if (empty($soumission_ids))
		{
			echo json_encode(FALSE);
			return;
		}

        //
        // Rendre visible
        //

		$this->Evaluation_model->rendre_visible($soumission_ids, $post_data['date'], $post_data['heure']);

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Rendre une soumission (evaluation corrigee) visible
     *
     * ------------------------------------------------------------------------ */
	public function rendre_invisible()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post()) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
			
		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'soumission_ids' :
					$validation_rules = 'required';
					break;

                case 'soumission_references' :
                    // $validation_rules = 'required';
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

		$soumission_ids = unserialize(htmlspecialchars_decode($post_data['soumission_ids']));

		if (empty($soumission_ids))
		{
			echo json_encode(FALSE);
			return;
		}

        //
        // Rendre invisible
        //

		$this->Evaluation_model->rendre_invisible($soumission_ids);

		echo json_encode(TRUE);
		return;
	}


    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer une soumission
     *
     * ------------------------------------------------------------------------ */
	public function effacer_soumission()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post()) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
			
		//
        // validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'soumission_id' :
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
	
		$this->Evaluation_model->effacer_soumission($post_data['soumission_id']);

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Remettre a zero les vues
     *
     * ------------------------------------------------------------------------ */
	public function remettre_zero_vues()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post()) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! array_key_exists('soumission_id', $post_data) || empty($post_data['soumission_id']))
        {
            echo json_encode(FALSE);
            return;
        }

		$this->Evaluation_model->remettre_zero_vues($post_data['soumission_id']);

		echo json_encode(TRUE);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * Semestre
     *
     * ------------------------------------------------------------------------
	 *
	 * Determiner le semestre_id a partir du code du semestre.
     *
     * ------------------------------------------------------------------------ */
    public function _semestre($semestre_code)
    {
        $semestres = $this->semestres;

        if ( ! isset($semestre_code) || empty($semestre_code) || empty($semestres))
        {
            redirect(base_url() . $this->data['current_controller']);
            exit;
        }

        $semestre_id   = NULL;
        $semestre_code = strtolower($semestre_code);

        foreach($semestres as $s)
        {
            if ($semestre_code == strtolower($s['semestre_code']) || $semestre_code == $s['semestre_id'])
            {
                $semestre_id = $s['semestre_id'];
                break;
            }
        } 

        if (empty($semestre_id))
        {
            redirect(base_url() . $this->data['current_controller']);
            exit;
        }

		return $semestre_id;
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
    public function index()
    {
        redirect(base_url);
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Index : version 2
     *
     * ------------------------------------------------------------------------ */
    public function index2($semestre_id = NULL, $ordre = 'remise')
    {
        //
        // Determiner le semestre actif selon la configuration de l'enseignant, ou selon specifie.
        //

        $semestre_id = empty($semestre_id) ? $this->enseignant['semestre_id'] : $semestre_id;

		//
		// Il n'a pas ete possible de determiner le semestre actif selon la configuration.
		// Alors determiner le semestre actif selon les dates, ou le semestre le plus recent.
		//

		if (empty($semestre_id))
        {
            $semestre_id = $this->Semestre_model->extraire_dernier_semestre();		
        }

        //
        // Il n'y a aucun semestre recent (ou passe) configure dans le systeme pour ce groupe.
        //

        if (empty($semestre_id))
        {
            $this->_affichage('aucun-resultat');
            return;
        }

        //
        // Extraire toutes les soumissions de l'enseignant pour le semestre actif.
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions(
            array(
                'enseignant_id'         => $this->enseignant['enseignant_id'],
                'semestre_id'           => $semestre_id,
                'corrections_terminees' => 1,
                'ordre'                 => $ordre,
            )
        );

        //
        // Aucune soumission trouvee
        //

        if (empty($soumissions))
        {
            $this->data['semestre_id'] = $semestre_id;
            $this->data['semestres']   = $this->semestres;

            $this->_affichage('aucun-resultat-pour-ce-semestre');
            return;
        }

		//
		// Eleves
        //

		$eleves = $this->Cours_model->lister_eleves($semestre_id, array('organisation' => 'groupe'));

        //
        // Eleves - Creer un index pour les groupes/numero_da
        //

        $eleves_c_nda = array();

        if ( ! empty($eleves))
        {
            foreach($eleves as $cours_id => $groupes)
            {
                if ( ! array_key_exists($cours_id, $eleves_c_nda))
                {
                    $eleves_c_nda[$cours_id] = array();
                }

                foreach($groupes as $groupe => $eleve)
                {
                    foreach($eleve as $eleve_id => $e)
                    {
                        $eleves_c_nda[$cours_id][$e['numero_da']] = $groupe; 
                    }
                }
            }
        }

        //
        // Extraire les donnees des soumissions, puis associer les soumissions aux bons groupes/eleves.
        //

        $evaluations_eleves = array(); // Ce tableau sera utilise pour lire les soumissions dans la view.
        $numeros_da         = array(); // Ce tableau sera utilise pour creer la clef de tri pour les table de la view.
        $cours_data         = array(); 
        $evaluations_data   = array(); 

        foreach($soumissions as &$s)
        {
            if (
                 is_numeric($s['numero_da']) &&
                 ! in_array($s['numero_da'], $numeros_da)
               )
                $numeros_da[] = $s['numero_da'];

            $cours_id      = $s['cours_id'];
            $evaluation_id = $s['evaluation_id'];

            //
            // Calculer la duree de l'evaluation.
            //
            
            $s['duree'] = calculer_duree($s['soumission_debut_epoch'], $s['soumission_epoch']);

            //
            // Extraire les donnees
            //

            if ( ! array_key_exists($cours_id, $cours_data))
            {
                $cours_data[$cours_id] = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
            }

            if ( ! array_key_exists($evaluation_id, $evaluations_data))
            {
                $evaluations_data[$evaluation_id] = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                $evaluations_data[$evaluation_id]['cours_id'] = $cours_id;
            }

            $cours_id = $s['cours_id'];
            $evaluation_id = $s['evaluation_id'];
            
            $c = $cours_data[$cours_id];
            $e = $evaluations_data[$evaluation_id];
            // $q = json_decode($s['questions_data'], TRUE);

            $e['cours_id'] = $cours_id;

            //
            // Extraire les donneex extra
            //

            $s['extra'] = empty($s['extra_data']) ? NULL : json_decode($s['extra_data'], TRUE);

            //
            // Associer les soumissions aux groupes/eleves.
            //

            $groupe = 999; // le groupe par default, 999 = groupe inconnu

            if ( ! array_key_exists($evaluation_id, $evaluations_eleves))
            {
                $evaluations_eleves[$evaluation_id] = array();
            }

            //
            // Determiner le groupe de l'eleve.
            //

            /*
             * Tres francy mais tres lent alors un index '$eleves_c_nda' a ete cree
             * au debut de cette fonction pour chercher rapidement l'association numero_da <=> groupe.
             *
            if (($path = array_search_recursive($s['numero_da'], $eleves[$cours_id])) !== FALSE)
            {
                // Un eleve a ete trouve dans la liste d'eleves.
                // Changer le groupe par default pour son groupe.

                $groupe = $path[0];
            }
            */

            if (is_array($eleves_c_nda) && array_key_exists($cours_id, $eleves_c_nda) && array_key_exists($s['numero_da'], $eleves_c_nda[$cours_id]))
            {
                $groupe = $eleves_c_nda[$cours_id][$s['numero_da']];
            }

            //
            // Determiner si des modifications de la correction sont possibles.
            //

            $modifier_correction = 0; // 0 pas possible, 1 possible

            if ( ! empty($q))
            {
                foreach($q as $question)
                {
                    if ($question['question_type'] == 2)
                    {
                        $modifier_correction = 1;
                        break;
                    }
                }
            }
    
            $s['modifier_correction'] = $modifier_correction;
                
            //
            // Ajouter la soumission dans le groupe determine.
            //

            if ( ! array_key_exists($groupe, $evaluations_eleves[$evaluation_id]))
            {
                $evaluations_eleves[$evaluation_id][$groupe] = array();
            }

            $evaluations_eleves[$evaluation_id][$groupe][] = $s['soumission_id'];
        }

        //
        // Ordonner les groupes en ordre croissant de soumission_id (donc de date de remise).
        //

        if ( ! empty($evaluations_eleves))
        {   
            foreach($evaluations_eleves as $evaluation_id => $ee)
            {
                ksort($evaluations_eleves[$evaluation_id]);

                foreach($ee as $groupe => $soumission_ids)
                {
                    $this->session->set_userdata('stats_' . $evaluation_id . '_' . $groupe, $soumission_ids);
                }
            }
        }

        //
        // Extraire les noms et prenoms des etudiants a partir des numeros da.
        //
        
        $numeros_da = $this->Groupe_model->extraire_noms_de_numeros_da($numeros_da);

        $this->data = array_merge(
            $this->data,
            array(
                'cours_raw'   	 	 => $cours_data,
                'soumissions' 	 	 => $soumissions,
                'evaluations' 	 	 => $evaluations_data,
				'evaluations_eleves' => $evaluations_eleves,
                'eleves'	 	 	 => $eleves,
                'semestres'          => $this->semestres,
                'semestre_id'        => $semestre_id,
                'ordre'				 => $ordre,
                'numeros_da'         => $numeros_da
            )
        );

        $this->_affichage('resultats2');
    }

    /* ------------------------------------------------------------------------
     *
     * Index : version 4
     *
     * ------------------------------------------------------------------------ */
    public function index4($semestre_id = NULL, $ordre = 'remise')
    {
        //
        // Determiner le semestre actif selon la configuration de l'enseignant, ou selon specifie.
        //

        $semestre_id = empty($semestre_id) ? $this->enseignant['semestre_id'] : $semestre_id;

		//
		// Il n'a pas ete possible de determiner le semestre actif selon la configuration.
		// Alors determiner le semestre actif selon les dates, ou le semestre le plus recent.
		//

		if (empty($semestre_id))
        {
            $semestre_id = $this->Semestre_model->extraire_dernier_semestre();		
		}

        //
        // Il n'y a aucun semestre recent (ou passe) configure dans le systeme pour ce groupe.
        //

        if (empty($semestre_id))
        {
            $this->_affichage('aucun-resultat');
            return;
        }

        //
        // Cours
        //

        $this->data['cours_raw'] = $this->Cours_model->lister_cours(array('groupe_id' => $this->groupe_id));

        //
        // Extraire toutes les soumissions de l'enseignant pour le semestre actif.
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions(
            array(
                'enseignant_id'         => $this->enseignant['enseignant_id'],
                'semestre_id'           => $semestre_id,
                'corrections_terminees' => 1,
                'ordre'                 => $ordre,
            )
        );

        //
        // Aucune soumission trouvee
        //

        if (empty($soumissions))
        {
            $this->data['semestre_id'] = $semestre_id;
            $this->data['semestres']   = $this->semestres;

            $this->_affichage('aucun-resultat-pour-ce-semestre');
            return;
        }

        //
        // Extraire les donnees des soumissions
        //

        $cours_data       = array();
        $evaluations_data = array(); 
        $visibilites      = array();

        foreach($soumissions as $s)
        {
            $cours_id      = $s['cours_id'];
            $evaluation_id = $s['evaluation_id'];
            $lab           = $s['lab'];

            if ( ! array_key_exists($cours_id, $cours_data))
            {
                $cours_data[$cours_id] = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
            }

            if ( ! array_key_exists($evaluation_id, $evaluations_data))
            {
                $evaluations_data[$evaluation_id] = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                $evaluations_data[$evaluation_id]['cours_id'] = $cours_id;
                $evaluations_data[$evaluation_id]['semestre_id'] = $s['semestre_id'];
                $evaluations_data[$evaluation_id]['count'] = 1;
                $evaluations_data[$evaluation_id]['lab'] = $lab;
            }
            else
            {
                $evaluations_data[$evaluation_id]['count'] += 1;
            }

            if (array_key_exists('evaluation_reference', $s) && ! empty($s['evaluation_reference']))
            {
                $evaluations_data[$evaluation_id]['evaluation_reference'] = $s['evaluation_reference'];
            }

            //
            // Determiner la visibilite de chaque evaluation
            //

            if ($s['permettre_visualisation'])
            {
                if ($s['permettre_visualisation_expiration'] == 0 || $s['permettre_visualisation_expiration'] > $this->now_epoch)
                {
                    $visibilites[$evaluation_id][] = $s['soumission_id'];
                }
			}
        }

        //
        // Ponderations
        //

        $ponderations = array();

        if ( ! empty($evaluations_data))
        {
            $evaluation_ids = array_keys($evaluations_data);

            $ponderations = $this->Evaluation_model->extraire_ponderations($evaluation_ids, $semestre_id);
        }

        $this->data['evaluations']  = $evaluations_data;
        $this->data['cours']        = $cours_data;
        $this->data['semestres']    = $this->semestres;
        $this->data['semestre_id']  = $semestre_id;
        $this->data['ponderations'] = $ponderations;
        $this->data['visibilites']  = $visibilites;

        $this->_affichage('resultats5');
    }

    /* ------------------------------------------------------------------------
     *
     * Confirmite des soumissions
     *
     * ------------------------------------------------------------------------ */
    public function conformite($evaluation_id, $semestre_id)
    {   
        if ( ! (ctype_digit($evaluation_id) &&  ctype_digit($semestre_id)))
        {
            redirect(base_url() . $this->current_controller);
            exit;
        }

        $soumissions = $this->Evaluation_model->extraire_soumissions(
            array(
                'enseignant_id'         => $this->enseignant['enseignant_id'],
                'evaluation_id'         => $evaluation_id, 
                'semestre_id'           => $semestre_id,
                'corrections_terminees' => 1
            )
        );

        //
        // Organiser les donnees
        //

        $adresse_ips  = array();
        $unique_ids   = array();

        $soumissions_identiques = array();      
        $soumissions_identiques_plus = array(); // sans tenir compte de l'ordre des questions;

        $etudiant_ids = array();

        if ( ! empty($soumissions))
        {
            foreach($soumissions as $soumission_id => $s)
            {
                $question_ids = array();

                //
                // Considerer seulement les soumissions des etudiants inscrits
                //

                if (empty($s['etudiant_id']))
                {
                    unset($soumissions[$soumission_id]);
                }

                $etudiant_ids[] = $s['etudiant_id'];

                //
                // Extraire les donnees de la soumission
                //

                $s_data = json_decode($s['soumission_data'], TRUE);

                //
                // Extraire les question_ids
                //

                $s_questions_data = json_decode(gzuncompress($s['questions_data_gz']), TRUE);

                $question_ids       = $s_questions_data;
                $question_ids_ordre = $s_questions_data;

                ksort($question_ids_ordre);

                $question_ids_hash       = hash('sha256', json_encode($question_ids));
                $question_ids_ordre_hash = hash('sha256', json_encode($question_ids_ordre));

                //
                // Soumissions identiques
                //

                if ( ! array_key_exists($question_ids_hash, $soumissions_identiques))
                {
                    $soumissions_identiques[$question_ids_hash] = array();
                } 

                $soumissions_identiques[$question_ids_hash][] = $soumission_id;

                //
                // Soumissions identiques plus
                //

                if ( ! array_key_exists($question_ids_hash, $soumissions_identiques_plus))
                {
                    $soumissions_identiques_plus[$question_ids_ordre_hash] = array();
                } 

                $soumissions_identiques_plus[$question_ids_ordre_hash][] = $soumission_id;

                //
                // Adresse IPs
                //

                if (array_key_exists('adresse_ip', $s_data) && ! empty($s_data['adresse_ip']))
                {
                    if ( ! array_key_exists($s_data['adresse_ip'], $adresse_ips))
                    {
                        $adresse_ips[$s_data['adresse_ip']] = array();
                    }

                    $adresse_ips[$s_data['adresse_ip']][] = $soumission_id; 
                }

                //
                // Unique IDs
                //

                if (array_key_exists('unique_id', $s) && ! empty($s['unique_id']))
                {
                    if ( ! array_key_exists($s['unique_id'], $unique_ids))
                    {
                        $unique_ids[$s['unique_id']] = array();
                    }

                    $unique_ids[$s['unique_id']][] = $soumission_id; 
                }
            
            } // foreach($soumissions)
        } // ! empty($soumissions)

        $data = array(
            'adresse_ips' => $adresse_ips,
            'unique_ids'  => $unique_ids,
            'soumissions_identiques' => $soumissions_identiques,
            'soumissions_identiques_plus' => $soumissions_identiques_plus
        );

        $this->data = array_merge($this->data, $data);

        $this->_affichage('conformite');
    }

    /* ------------------------------------------------------------------------
     *
     * Evaluation (version 2)
     *
     * ------------------------------------------------------------------------
     *
     * Lister les soumissions d'une evaluation, d'un semestre specifique
     *
     * 2023-11-16 : Inclure les etudiants n'ayant pas soumis leur evaluation.
     * 2024-08-09 : Inclure les laboratoires
     *
     * ------------------------------------------------------------------------ */
    public function _evaluation($evaluation_id, $semestre_id)
    {
        //
        // Extraire toutes les soumissions de cette evaluation, pour ce semestre
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions(
            array(
                'enseignant_id'         => $this->enseignant['enseignant_id'],
                'evaluation_id'         => $evaluation_id,
                'semestre_id'           => $semestre_id,
                'corrections_terminees' => 1,
                'ordre'                 => 'remise'
            )
        );

        //
        // Aucune soumission trouvee
        //

        if (empty($soumissions))
        {
            $this->data['semestre_id'] = $semestre_id;
            $this->data['semestres']   = $this->semestres;

            $this->_affichage('aucun-resultat-pour-evaluation');
            return;
        }

        //
        // Extraire les numeros DA de toutes les soumissions
        //

        $soumissions_nda = array_column($soumissions, 'numero_da');

		//
		// Eleves
        //

		$eleves = $this->Cours_model->lister_eleves($semestre_id, array('organisation' => 'groupe'));

        //
        // Eleves - Creer un index pour les groupes/numero_da
        //

        $eleves_c_nda = array();

        if ( ! empty($eleves))
        {
            foreach($eleves as $cours_id => $groupes)
            {
                if ( ! array_key_exists($cours_id, $eleves_c_nda))
                {
                    $eleves_c_nda[$cours_id] = array();
                }

                foreach($groupes as $groupe => $eleve)
                {
                    foreach($eleve as $eleve_id => $e)
                    {
                        $eleves_c_nda[$cours_id][$e['numero_da']] = $groupe; 
                    }
                }
            }
        }

        //
        // Eleves - Creer un index pour les groupes/numero_da
        //
        // version 2 (2023-11-16)
        //
        
        $eleves_c_nda2 = array();

        if ( ! empty($eleves))
        {
            foreach($eleves as $cours_id => $groupes)
            {
                if ( ! array_key_exists($cours_id, $eleves_c_nda2))
                {
                    $eleves_c_nda2[$cours_id] = array();
                }

                foreach($groupes as $groupe => $eleve)
                {
                    if ( ! array_key_exists($groupe, $eleves_c_nda2[$cours_id]))
                    {
                        $eleves_c_nda2[$cours_id][$groupe] = array();
                    }

                    foreach($eleve as $eleve_id => $e)
                    {
                        $eleves_c_nda2[$cours_id][$groupe][$e['numero_da']] = 
                            array(
                                'eleve_id' => $e['eleve_id'],
                                'numero_da' => $e['numero_da'],
                                'nom' => $e['eleve_nom'],
                                'prenom' => $e['eleve_prenom']
                            );
                    }
                }
            }
        }

        //
        // Extraire les donnees des soumissions, puis associer les soumissions aux bons groupes/eleves.
        //

        $evaluations_eleves = array(); // Ce tableau sera utilise pour lire les soumissions dans la view.
        $numeros_da         = array(); // Ce tableau sera utilise pour creer la clef de tri pour les table de la view.
        $cours_data         = array(); 
        $evaluations_data   = array(); 

        // $evaluations_unique  = array(); // Les memes questions, dans le meme ordre (OBSOLETE)
        $evaluations2_unique = array(); // Les memes questions, sans considerer l'ordre

        // $soumissions_unique  = array(); // Les memes questions, les memes reponses, dans le meme ordre (OBSOLETE)
        $soumissions2_unique = array(); // Les memes questions, les memes reponses, sans considerer l'ordre

        //
        // Les moniteurs
        // 

        $adresse_ips           = array();
        $adresse_ips_etudiants = array();

        $fureteurs_unique   = array(); // fureteur id
        $ordinateurs_unique = array(); // unique id

        $activite_debut     = NULL;
        $activite_fin       = NULL;
        $etudiant_ids       = array();

        $activite           = array();
        $activite_louche    = FALSE;
        $etudiants          = array();

        $soumissions_references = array(); // Afin de verifier l'unicite des document

        //
        // Detection de l'activite louche
        //

        $evaluation_references = array(); // Toutes les references de l'evaluation, en general une seule
        $etudiants_debut_epoch = array(); // Pour chaque etudiant, le moment ou l'evaluation a ete commencee
        $etudiants_fin_epoch   = array(); // Pour chaque etudiant, le moment ou l'evaluation a ete soumise

		//
		// Barre de defilement pour la recorrection
		//

		$evaluations_liste = array();
		$evaluations_liste[$evaluation_id] = array();

        foreach($soumissions as &$s)
		{       
			$evaluations_liste[$evaluation_id][] = $s['soumission_reference'];

            //
            // Enregistrer la reference de l'evaluation
            //

            if ( ! in_array($s['evaluation_reference'], $evaluation_references))
            {
                $evaluation_references[] = $s['evaluation_reference'];
            }

            //
            // Les etudiants INSCRITS
            //

            if ($s['etudiant_id'])
            {
                //
                // Enregistrer le debut et la fin de l'evaluation
                //

                $etudiants_debut_epoch[$s['etudiant_id']] = $s['soumission_debut_epoch'];
                $etudiants_fin_epoch[$s['etudiant_id']] = $s['soumission_epoch'];

                //
                // Enregistrer les adresses IPs
                //

                /*
                if ( ! array_key_exists($s['adresse_ip'], $adresse_ips_etudiants))
                {
                    $adresse_ips_etudiants[$s['adresse_ip']] = array();
                }

                $adresse_ips_etudiants[$s['adresse_ip']][] = $s['etudiant_id'];
                */
            }

            $soumission_id   = $s['soumission_id'];
            $soumission_data = json_decode($s['soumission_data'], TRUE);

            $soumissions_references[$soumission_id] = $s['soumission_reference'];

            if (
                 is_numeric($s['numero_da']) &&
                 ! in_array($s['numero_da'], $numeros_da)
               )
                $numeros_da[] = $s['numero_da'];

            $cours_id      = $s['cours_id'];
            $evaluation_id = $s['evaluation_id'];

            // un hack  car j'ai besoin de savoir si l'evaluation est un lab ou non, et comme
            // toutes les soumissions proviennent de la meme evaluation... (sauf que c'est reassigne
            // a chaque iteration)

            $lab = $s['lab'];

            //
            // Calculer la duree de l'evaluation.
            //
            
            $s['duree'] = calculer_duree($s['soumission_debut_epoch'], $s['soumission_epoch']);

            // ----------------------------------------------------------------
            //
            // Conformite
            //
            // ----------------------------------------------------------------

            //
            // Verifier l'unicite des adresses IPs
            //

            if ( ! array_key_exists($s['adresse_ip'], $adresse_ips))
            {
                $adresse_ips[$s['adresse_ip']] = array();
            }

            $adresse_ips[$s['adresse_ip']][] = $soumission_id;

            //
            // Verifier l'unicite des evaluations
            //

            $questions_data = json_decode(gzuncompress($s['questions_data_gz']), TRUE);
            
            $q_data = array();

            $champs_preserver = array(
                'question_id', 'evaluation_id', 'question_texte', 'question_type', 'question_points', 'reponse_correcte', 'reponse_correcte_texte'
            );

            // En principe, ce tableau ne devrait jamais etre vide mais dans quelques cas isoles, les questions n'ont pu etre enregistrees
            // du a un caractere problematique.

            if ( ! empty($questions_data))
            {
                foreach($questions_data as $question_id => $qd)
                {
                    if (array_key_exists($question_id, $q_data))
                    {
                        $q_data[$question_id] = array();
                    }

                    foreach($champs_preserver as $champ)
					{
						if ( ! is_array($qd))
							continue;

                        if ( ! array_key_exists($champ, $qd))
                            continue;

                        $q_data[$question_id][$champ] = $qd[$champ];
                    }
                }
            }

            //
            // Verifier l'unicite des evaluations2 (sans considerer l'ordre des questions)
            //

            $q_data_ordre = $q_data;

            ksort($q_data_ordre);

            $q_data_ordre = json_encode($q_data_ordre);
            
            $questions_hash = hash('sha256', $q_data_ordre);

            if ( ! array_key_exists($questions_hash, $evaluations2_unique))
            {
                $evaluations2_unique[$questions_hash] = array();
            }
            
            $evaluations2_unique[$questions_hash][] = $soumission_id;

            //
            // Verifier l'unicite des soumissions
            //

            $q_data = array();

            $champs_preserver = array(
                'question_id', 'evaluation_id', 'question_texte', 'question_type', 'question_points', 'reponse_correcte', 'reponse_correcte_texte',
                'reponse_repondue', 'reponse_repondue_texte'
            );

            if ( ! empty($questions_data))
            {
                foreach($questions_data as $question_id => $qd)
                {
                    if (array_key_exists($question_id, $q_data))
                    {
                        $q_data[$question_id] = array();
                    }

                    foreach($champs_preserver as $champ)
                    {
						if ( ! is_array($qd))
							continue;

                        if ( ! array_key_exists($champ, $qd))
                            continue;

                        $q_data[$question_id][$champ] = $qd[$champ];
                    }
                }
            }

            //
            // Verifier l'unicite des soummissions 2 (sans considerer l'ordre des questions)
            //

            $q_data_ordre = $q_data;

            ksort($q_data_ordre);

            $q_data_ordre = json_encode($q_data_ordre);
            
            $questions_hash = hash('sha256', $q_data_ordre);

            if ( ! array_key_exists($questions_hash, $soumissions2_unique))
            {
                $soumissions2_unique[$questions_hash] = array();
            }
            
            $soumissions2_unique[$questions_hash][] = $soumission_id;

            //
            // Verifier l'unicite des fureteurs
            //

            $fureteur = NULL;

            if (is_array($soumission_data))
            {
                if (array_key_exists('fureteur_id', $soumission_data))
                {
                    $fureteur = $soumission_data['fureteur_id'];
                }
                elseif (array_key_exists('agent_string', $soumission_data))
                {
                    $fureteur = hash('sha256', $soumission_data['agent_string']);
                }   
            }

            if ( ! empty($fureteur))
            {
                if ( ! array_key_exists($fureteur, $fureteurs_unique))
                {
                    $fureteurs_unique[$fureteur] = array();
                }
                
                $fureteurs_unique[$fureteur][] = $soumission_id;
            }

            //
            // Verifier l'unicite des ordinataeurs
            //

            $ordinateur = NULL;

            if (array_key_exists('unique_id', $s) && ! empty($s['unique_id']))
            {
                $ordinateur = $s['unique_id'];
            }

            if ( ! empty($ordinateur))
            {
                if ( ! array_key_exists($ordinateur, $ordinateurs_unique))
                {
                    $ordinateurs_unique[$ordinateur] = array();
                }
                
                $ordinateurs_unique[$ordinateur][] = $soumission_id;
            }

            //
            // Extraire les donnees
            //

            if ( ! array_key_exists($cours_id, $cours_data))
            {
                $cours_data[$cours_id] = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
            }

            if ( ! array_key_exists($evaluation_id, $evaluations_data))
            {
                $evaluations_data[$evaluation_id] = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                $evaluations_data[$evaluation_id]['cours_id'] = $cours_id;
            }

            $cours_id = $s['cours_id'];
            $evaluation_id = $s['evaluation_id'];
            
            $c = $cours_data[$cours_id];
            $e = $evaluations_data[$evaluation_id];
            // $q = json_decode($s['questions_data'], TRUE);

            $e['cours_id'] = $cours_id;

            //
            // Extraire les donneex extra
            //

            $s['extra'] = empty($s['extra_data']) ? NULL : json_decode($s['extra_data'], TRUE);

            //
            // Associer les soumissions aux groupes/eleves.
            //

            $groupe = 999; // le groupe par default, 999 = groupe inconnu

            if ( ! array_key_exists($evaluation_id, $evaluations_eleves))
            {
                $evaluations_eleves[$evaluation_id] = array();
            }

            //
            // Determiner le groupe de l'eleve.
            //

            if (is_array($eleves_c_nda) && array_key_exists($cours_id, $eleves_c_nda) && array_key_exists($s['numero_da'], $eleves_c_nda[$cours_id]))
            {
                $groupe = $eleves_c_nda[$cours_id][$s['numero_da']];
            }

            //
            // Determiner si des modifications de la correction sont possibles.
            //

            $modifier_correction = 0; // 0 pas possible, 1 possible

            if ( ! empty($q))
            {
                foreach($q as $question)
                {
                    if ($question['question_type'] == 2)
                    {
                        $modifier_correction = 1;
                        break;
                    }
                }
            }
    
            $s['modifier_correction'] = $modifier_correction;
                
            //
            // Ajouter la soumission dans le groupe determine.
            //

            if ( ! array_key_exists($groupe, $evaluations_eleves[$evaluation_id]))
            {
                $evaluations_eleves[$evaluation_id][$groupe] = array();
            }

            $evaluations_eleves[$evaluation_id][$groupe][] = $s['soumission_id'];

            //
            // Preparer l'extraction de l'activite
            //

            if ($activite_debut == NULL || $activite_fin == NULL)
            {
                $activite_debut = $s['soumission_debut_epoch'];
                $activite_fin   = $s['soumission_epoch'];
            }
            else
            {
                if ($activite_debut > $s['soumission_debut_epoch'])
                {
                    $activite_debut = $s['soumission_debut_epoch'];
                }

                if ($activite_fin < $s['soumission_epoch'])
                {
                    $activite_fin = $s['soumission_epoch'];
                }
            }

            if ( ! empty($s['etudiant_id']))
            {
                $etudiant_ids[] = $s['etudiant_id'];
            }

        } // foreach $soumissions

		// Sert a populer la liste des evaluations (soumissions) des etudiants pour faciliter la recorrection
		$_SESSION['evaluations_liste'] = serialize($evaluations_liste);

        //
        // Extraire l'activite des etudiants inscrits
        //

        $activite = array();

        if ( ! empty($etudiant_ids))
        {
            $activite = $this->Evaluation_model->detecter_activite_louche(
                array(
                    'etudiant_ids'          => $etudiant_ids, 
                    'evaluation_references' => $evaluation_references,
                    'debut_epoch'           => $activite_debut,
                    'fin_epoch'             => $activite_fin,
                    'etudiants_debut_epoch' => $etudiants_debut_epoch,
                    'etudiants_fin_epoch'   => $etudiants_fin_epoch
                )
            );

            $etudiants = $this->Etudiant_model->extraire_etudiants(array('etudiant_ids' => $etudiant_ids));

            $meme_ip_louche      = $activite['meme_ip_louche'];
            $aide_externe_louche = $activite['aide_externe_louche'];
        }

        //
        // Ordonner les groupes en ordre croissant de soumission_id (donc de date de remise).
        //

        if ( ! empty($evaluations_eleves))
        {   
            foreach($evaluations_eleves as $evaluation_id => $ee)
            {
                ksort($evaluations_eleves[$evaluation_id]);

                foreach($ee as $groupe => $soumission_ids)
                {
                    $this->session->set_userdata('stats_' . $evaluation_id . '_' . $groupe, $soumission_ids);
                }
            }
        }

        //
        // Extraire les noms et prenoms des etudiants a partir des numeros da.
        //

        $numeros_da = $this->Groupe_model->extraire_noms_de_numeros_da($numeros_da);

        //
        // Verifier l'unicite des documents
        //

        $documents_verification = $this->Document_model->detecter_documents_identiques_multi($soumissions_references);

        //
        // Preparer l'affichage
        //

        $this->data = array_merge(
            $this->data,
            array(
                'cours_raw'   	 	     => $cours_data,
                'soumissions' 	 	     => $soumissions,
                'evaluations' 	 	     => $evaluations_data,
                'evaluations_eleves'     => $evaluations_eleves,
                'lab'                    => $lab,
                'eleves'	 	 	     => $eleves,
                'eleves_nda'             => $eleves_c_nda2,
                'semestres'              => $this->semestres,
                'semestre_id'            => $semestre_id,
                'numeros_da'             => $numeros_da,
                'adresse_ips'            => $adresse_ips,
                'evaluations2_unique'    => $evaluations2_unique,
                'soumissions2_unique'    => $soumissions2_unique,
                'fureteurs_unique'       => $fureteurs_unique,
                'ordinateurs_unique'     => $ordinateurs_unique,
                'activite'               => $activite,
                'meme_ip_louche'         => $meme_ip_louche ?? FALSE,
                'aide_externe_louche'    => $aide_externe_louche ?? FALSE,
                'documents_verification' => $documents_verification,
                'etudiants'              => $etudiants,
                'ecole_ips'              => $this->Ecole_model->extraire_ecole_ips($this->groupe['ecole_id'])
            )
        );

        $this->_affichage('evaluation');
    }

    /* ------------------------------------------------------------------------
     *
     * Statistiques d'un groupe
     *
     * ------------------------------------------------------------------------ */
    public function stats($evaluation_id)
    {
        /*
        $evaluation = $this->Evaluation_model->extraire_evaluation($evaluation_id);

        if (empty($evaluation) || ($this->enseignant_id != $evaluation['enseignant_id'] && ! permis('devel')))
        {
            redirect(base_url());
            exit;
        }
        */

        //
        // Extraire les soumissions a partir de la date de creation de la question
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions(
            array(
                'enseignant_id' => $evaluation['enseignant_id'],
                'semestre_id'   => NULL,
                'evaluation_id' => $evaluation_id,
                'epoch'         => $evaluation['ajout_epoch'],
                'ordre'         => 'remise_desc'
            )
        );

        if (empty($soumissions))
        {
            $this->data['evaluation_id'] = $evaluation_id;
            $this->data['evaluation']    = $evaluation;
            $this->data['soumissions']   = $soumissions;

            $this->_affichage('statistiques');
            return;
        }

        //
        // Compiler les statistiques
        //

        // Evaluation

        $e_nombre         = 0;
        $e_points_totaux  = 0; 
        $e_points_obtenus = 0;

        // Questions (index: question_id)

        $q_nombre         = array();
        $q_points_totaux  = array();
        $q_points_obtenus = array();
        $q_textes         = array();

        foreach($soumissions as $s)
        {
            $soumission_id = $s['soumission_id'];
            $questions     = json_decode($s['questions_data'], TRUE);

            $e_nombre++;
            $e_points_totaux  += $s['points_total'];
            $e_points_obtenus += $s['points_obtenus']; 

            foreach($questions as $q)
            {
                $question_id = $q['question_id'];

                if ( ! array_key_exists($question_id, $q_nombre))
                {
                    $q_nombre[$question_id]         = 0;
                    $q_points_totaux[$question_id]  = 0;
                    $q_points_obtenus[$question_id] = 0;
                    $q_textes[$question_id]         = '';
                }

                $q_nombre[$question_id]++;
                $q_points_obtenus[$question_id] += $q['points_obtenus'];
                $q_points_totaux[$question_id]  += $q['question_points'];

                $q_textes[$question_id]          = $q['question_texte'];
            }
        }

        $data = array(
            'evaluation'       => $evaluation,
            'evaluation_id'    => $evaluation_id,
            'soumissions'      => $soumissions,
            'e_nombre'         => $e_nombre,
            'e_points_totaux'  => $e_points_totaux,
            'e_points_obtenus' => $e_points_obtenus,
            'q_nombre'         => $q_nombre,
            'q_points_totaux'  => $q_points_totaux,
            'q_points_obtenus' => $q_points_obtenus,
            'q_textes'         => $q_textes
        );

        $this->data = array_merge($this->data, $data);

        $this->_affichage('statistiques');
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage($page = NULL)
    {
        $this->load->view('commons/header', $this->data);

		switch($page)
		{
			case 'aucun-semestre' :
				$this->load->view('resultats/resultats_aucun_semestre', $this->data);
				break;

			case 'aucun-semestre-selectionne' :
				$this->load->view('resultats/resultats_aucun_semestre_selectionne', $this->data);
				break;

            case 'aucun-resultat' :
                $this->load->view('resultats/resultats_aucun_resultat', $this->data);
                break;

			case 'aucun-resultat-pour-ce-semestre' :
                $this->load->view('resultats/resultats_aucun_resultat_pour_semestre', $this->data);
				break;

			case 'aucun-resultat-pour-evaluation' :
                $this->load->view('resultats/resultats_aucun_resultat_pour_evaluation', $this->data);
                break;

            case 'evaluation' :
                $this->load->view('resultats/resultats_evaluation', $this->data);
                break;

            case 'resultats2' :
                $this->load->view('resultats/resultats2', $this->data);
                break;

            case 'resultats4' :
                $this->load->view('resultats/resultats4', $this->data);
                break;

            case 'resultats5' :
                $this->load->view('resultats/resultats5', $this->data);
                break;

			default :
                $this->load->view('resultats/resultats5', $this->data);
				break;
		}

        $this->load->view('commons/footer');
	}

}
