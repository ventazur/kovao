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
 * OUTILS
 *
 * ============================================================================ */

use jlawrence\eos\Parser;

class Outils extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        $this->data['current_controller'] = strtolower(__CLASS__);
	}

    /* ------------------------------------------------------------------------
     *
     * INDEX
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/outils', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * ETUDIANTS
     *
     * ------------------------------------------------------------------------ */
    public function etudiants($methode = NULL)
    {
        switch($methode)
        {
            //
            // Les etudiants relies
            // 

            case 'relies' :

                $this->data['etudiants_relies'] = $this->Admin_model->detecter_etudiants_relies(
                    array(
                        'enseignant_id' => $this->enseignant_id
                    )
                );

                $this->load->view('commons/header', $this->data);
                $this->load->view('outils/etudiants_relies', $this->data);
                $this->load->view('commons/footer', $this->data);

                break;

            //
            // Default
            //

            default :

                die("Cette méthode est invalide.");
        }

        return;

    }

    /* ------------------------------------------------------------------------
     *
     * EMPREINTE
     *
     * ------------------------------------------------------------------------ */
    public function empreinte()
    {
        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

        $this->form_validation->set_rules('reference', 'Référence', 'required');    
        $this->form_validation->set_rules('empreinte', 'Empreinte', 'required');    

        if ($this->form_validation->run() == FALSE)
        {
            // ERREUR ou PREMIER CHARGEMENT
        }
        else
        {
            // SUCCES
            
            $post_data = $this->input->post();

            $empreinte_valide = $this->Evaluation_model->generer_empreinte($post_data['reference']);

            $alerte = array(
                'reference' => $post_data['reference'],
                'empreinte' => $post_data['empreinte']
            );

            $soumission = $this->Evaluation_model->extraire_soumission_par_reference($post_data['reference'], array('tous_les_enseignants' => TRUE));

            if ( ! empty($soumission))
            {
                $soumission['cours_data']      = gzuncompress($soumission['cours_data_gz']);
                $soumission['evaluation_data'] = gzuncompress($soumission['evaluation_data_gz']);

                $enseignant = $this->Enseignant_model->extraire_enseignant($soumission['enseignant_id']);
            }

            $this->data['soumission'] = $soumission ?? array();
            $this->data['enseignant'] = $enseignant ?? array();

            if ($empreinte_valide == $post_data['empreinte'])
            {
                $alerte['status'] = 'valide';
            }
            else
            {
                $alerte['status'] = 'erreur';
            }

            $this->data['alerte'] = $alerte;
        }

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/empreinte', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * RECHERCHE
     *
     * ------------------------------------------------------------------------ */
    public function recherche($param)
    {
        $recherches_possibles = array('etudiant');

        if ( ! in_array($param, $recherches_possibles))
        {
            redirect(base_url());
            exit;
        }

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/recherche_' . $param, $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Recherche en direct (live search)
     *
     * ------------------------------------------------------------------------ */
	public function recherche_en_direct()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $this->load->model('Recherche_model');

        $post_data = $this->input->post();

        //
        // Verifier la requete
        //

        if (strlen($post_data['requete']) < 3)
        {
            echo json_encode(FALSE);
            return;
        }

        $requete = $post_data['requete'];
        $requete = filter_input(INPUT_POST, 'requete', FILTER_SANITIZE_SPECIAL_CHARS);
        $requete = strip_accents(trim($requete));

        if ( ! preg_match('/^[A-Za-z0-9 \-_\']+$/i', $requete))
        {
            echo json_encode(9);
            return;
        }

        $resultats = $this->Recherche_model->recherche_etudiants_pour_enseignants($requete);

        $html = '';

        if ( ! empty($resultats['etudiants']))
        {
            $html .= $this->load->view('outils/_recherche_resultats_etudiants', $resultats, TRUE);
        }

        echo json_encode($html);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * TESTAGE DES TOLERANCES POUR UNE QUESTION
     *
     * ------------------------------------------------------------------------ */
    public function tolerances($question_id)
    {
        if (empty($question_id))
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire les donnees de la reponse
        //

        $reponse = $this->Reponse_model->extraire_reponse($question_id);

        if (empty($reponse))
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire les tolerances
        //

        $tolerances = $this->Question_model->extraire_tolerances($question_id);

        if (empty($tolerances))
        {
            redirect(base_url());
            exit;
        }

        $data = array(
            'reponse'    => $reponse,
            'tolerances' => $tolerances
        );

        $this->data = array_merge($this->data, $data);

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/tolerances', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * TOLERANCES > CALCULER LE POINTAGE
     *
     * ------------------------------------------------------------------------ */
    public function tolerances_calculer_pointage()
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

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'bonne_reponse' :
                case 'reponse' :
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
        // Recontruire le tableau des tolerances.
        //
        
        $tolerances     = array();
        $tolerances_val = array(); // Les valeurs pour s'assurer de l'unicite.

        foreach($post_data as $k => $v)
        {
            if (preg_match('/(.*)([1-9])$/', $k, $matches))
            {
                $i = $matches[2] - 1;

                if ( ! array_key_exists($i, $tolerances))
                    $tolerances[$i] = array();

                $v = str_replace(',', '.', $v);

                $tolerances[$i][$matches[1]] = $v;

                // Verifier l'unicite des valeurs des tolerances
                if ($matches[1] == 'tolerance')
                {
                    if (in_array($v, $tolerances_val))
                    {
                        echo json_encode(array('tolerances' => TRUE));
                        exit;
                    }

                    $tolerances_val[] = $v;
                }
            }
        }

        //
        // Ordonner en ordre croissant les tolerances.
        //

        usort($tolerances, function($a, $b) {
                return $a['tolerance'] <=> $b['tolerance'];
        });

        //
        // Calculer le pointage
        //

        $pointage_total = 10;
        $pointage       = corriger_question_numerique($post_data['reponse'], $post_data['bonne_reponse'], $pointage_total, $tolerances)['points_obtenus'];

        echo json_encode($pointage);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * QUESTION A REPONSE NUMERIQUE (TYPE 6)
     *
     * ------------------------------------------------------------------------ */
	public function question6($question_id)
	{
        if (empty($question_id))
        {
            redirect(base_url());
            exit;
        }

        $question = $this->Question_model->extraire_question($question_id);

        if (empty($question))
        {
            redirect(base_url());
            exit;
        }

		//
		// Verifier si cette question appartient a l'enseignant
		//

		if ($question['enseignant_id'] != $this->enseignant_id)
		{
            redirect(base_url());
            exit;
        }

		//
		// Verifier si cette question est du type requis.
		//

		if ( ! in_array($question['question_type'], array(6)))
		{
            redirect(base_url());
            exit;
		}

        //
        // Extraire la reponse
        //

        $reponse = $this->Reponse_model->extraire_reponse($question_id);

        if (empty($reponse))
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire les tolerances
        //

        $tolerances = $this->Question_model->extraire_tolerances($question_id);

        $data = array(
			'question'		     => $question,
			'type'				 => $question['question_type'],
            'reponse'    		 => $reponse,
            'tolerances' 		 => $tolerances
        );

        $this->data = array_merge($this->data, $data);

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/question_type_6', $this->data);
        $this->load->view('commons/footer', $this->data);
	}

    /* ------------------------------------------------------------------------
     *
     * QUESTION A REPONSE LITTERALE COURTE (TYPE 7)
     *
     * ------------------------------------------------------------------------ */
    public function question7($question_id)
    {
        if (empty($question_id))
        {
            redirect(base_url());
            exit;
        }

        $question = $this->Question_model->extraire_question($question_id);

        if (empty($question))
        {
            redirect(base_url());
            exit;
        }

		//
		// Verifier si cette question appartient a l'enseignant
		//

		if ($question['enseignant_id'] != $this->enseignant_id)
		{
            redirect(base_url());
            exit;
		}

		//
		// Verifier si cette question est du type requis.
		//

		if ( ! in_array($question['question_type'], array(7)))
		{
            redirect(base_url());
            exit;
		}
		
        //
        // Extraire les reponses
        //

        $reponses = $this->Reponse_model->lister_reponses($question_id);

        if (empty($reponses))
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire la similarite
        //

        $similarite = $this->Question_model->extraire_similarite($question_id);

        $data = array(
			'question'	 => $question,
            'reponses'   => $reponses,
            'similarite' => $similarite['similarite']
        );

        $this->data = array_merge($this->data, $data);

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/question_type_7', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * Question a reponse litterale courte (TYPE 7)
	 *
	 * Calculer le pointage
     *
     * ------------------------------------------------------------------------ */
    public function question7_calculer_pointage()
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

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'question_id' :
				case 'reponses_hypothetiques' :
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

		// Est-ce que l'enseignant veut connaitre...
		// - la similarite calculee
		// - la similarite suggeree

		$reps = explode("\n", $post_data['reponses_hypothetiques']);

		// Aseptiser les reponses hypothetiques

		foreach($reps as $index => $r)
		{
			$r_aseptisee = trim($r);

			if (empty($r_aseptisee))
				unset($reps[$index]);
		}

		//
		// Aucune reponse detectee
		//

		if (count($reps) < 1)
		{
			echo json_encode(array('reponses_hypothetiques' => 'erreur'));
			return;
		}

		//
		// Similarite suggeree
		//

		if (count($reps) > 1)
		{
			echo json_encode(
				array(
					'similarite' => suggerer_similarite($reps, $post_data['bonnes_reponses'])
				)
			);
			return;
		}

		//
		// Similarite calculee
		//

		else
		{
			// Calculer le pointage (version 3)

        	$pointage_total = 10;
			$pointage       = corriger_question_litterale_courte3($reps[0], $post_data['bonnes_reponses'], $pointage_total, $post_data['similarite']);

			echo json_encode($pointage);
			return;
		}

		return;
    }

    /* ------------------------------------------------------------------------
     *
     * QUESTION A REPONSE NUMERIQUE PAR EQUATION (TYPE 9)
     *
     * ------------------------------------------------------------------------ */
    public function question9($question_id)
    {
        $question = $this->Question_model->extraire_question($question_id);

		//
		// Verifier si cette question existe et est du type requise (type 9)
		//

		if ( ! in_array($question['question_type'], array(9)))
		{
            redirect(base_url());
            exit;
        }

        if (empty($question) || $question['question_type'] != 9)
        {
            redirect(base_url());
            exit;
        }

		//
		// Verifier si cette question appartient a l'enseignant
		//

        if ($this->enseignant['privilege'] < 90)
        {
            if ($question['enseignant_id'] != $this->enseignant_id)
            {
                redirect(base_url());
                exit;
            }
        }

        //
        // Extraire les variables
        //

        $variables_raw = $this->Evaluation_model->extraire_variables($question['evaluation_id']);

        //
        // Determiner les valeurs des variables aleatoirement
        //

        $variables = determiner_valeurs_variables($variables_raw);

        //
        // Changer le texte de la question pour incorporer les variables
        //

        $question_originale = $question['question_texte'];

        $question['question_texte'] = remplacer_variables_question($question['question_texte'], $variables, $variables_raw);

        //
        // Extraire la reponse
        //

        $reponse = $this->Reponse_model->extraire_reponse($question_id);

        if (empty($reponse))
        {
            redirect(base_url());
            exit;
        }

        $data = array(
            'reponses'           => array($reponse), 
            'variables'          => $variables,
            'resoudre_seulement' => TRUE
        );

        $r = corriger_question_type_9($data);

        $reponse['reponse_brute'] = $r['reponse_correcte_brute'];
        $reponse_correcte = str_replace('.', ',', $r['reponse_correcte']);

        //
        // Extraire les tolerances
        //

        $tolerances = $this->Question_model->extraire_tolerances($question_id);

        $data = array(
			'question'		     => $question,
			'type'				 => $question['question_type'],
			'variables'			 => $variables,
            'reponse'    		 => $reponse,
			'reponse_correcte' 	 => $reponse_correcte,
            'tolerances' 		 => $tolerances,
            'question_originale' => $question_originale
        );

        $this->data = array_merge($this->data, $data);

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/question_type_9', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * Question a reponse numerique par equation (TYPE 9)
	 *
	 * Calculer le pointage
     *
     * ------------------------------------------------------------------------ */
    public function question9_calculer_pointage()
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

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'reponse' :
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
		// Tolerances
        //
        // Recontruire le tableau des tolerances.
        //
        
        $tolerances     = array();
        $tolerances_val = array(); // Les valeurs pour s'assurer de l'unicite.

        foreach($post_data as $k => $v)
        {
            if (preg_match('/(.*)([1-9])$/', $k, $matches))
            {
                $i = $matches[2] - 1;

                if ( ! array_key_exists($i, $tolerances))
                    $tolerances[$i] = array();

                $v = str_replace(',', '.', $v);

                $tolerances[$i][$matches[1]] = $v;

                // Verifier l'unicite des valeurs des tolerances
                if ($matches[1] == 'tolerance')
                {
                    if (in_array($v, $tolerances_val))
                    {
                        echo json_encode(array('tolerances' => TRUE));
                        exit;
                    }

                    $tolerances_val[] = $v;
                }
            }
        }

        //
        // Calculer le pointage
        //

        $pointage_total = 10;
        $pointage       = corriger_question_numerique($post_data['reponse'], $post_data['bonne_reponse'], $pointage_total, $tolerances)['points_obtenus'];

        echo json_encode($pointage);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * TESTAGE DE LA SIMILARITE
     *
     * ------------------------------------------------------------------------ */
    public function similarite($question_id)
    {
        if (empty($question_id))
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire les donnees de la reponse
        //

        $reponse = $this->Reponse_model->extraire_reponse($question_id);

        if (empty($reponse))
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire la similarite
        //

        $similarite = $this->Question_model->extraire_similarite($question_id);

        if (empty($similarite))
        {
            $similarite = array(
                'question_id' => $question_id,
                'similarite'  => $this->config->item('questions_types')[7]['similarite']
            );
        }

        $data = array(
            'reponse'    => $reponse,
            'similarite' => $similarite['similarite']
        );

        $this->data = array_merge($this->data, $data);

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/similarite', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * *DESUET* SIMILARITE > CALCULER LE POINTAGE
     *
     * ------------------------------------------------------------------------ */
    public function DESUET_similarite_calculer_pointage()
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

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

		//
        // Validation des entrees
		//
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
                case 'bonne_reponse' :
                case 'reponse' :
                case 'similarite':
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
        // Calculer le pointage
        //

        $pointage_total = 10;
        $pointage       = corriger_question_litterale_courte($post_data['reponse'], $post_data['bonne_reponse'], $pointage_total, $post_data['similarite']);

        echo json_encode($pointage);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * MATRICE
     * Generer des equipes aleatoires et differentes pour 7 labos.
     *
     * ------------------------------------------------------------------------ */
    public function matrice()
    {
        die('EXIT');

        $nb_etudiants = 32;
        $nb_labos     = 7;
        $no_places    = 16;

        //
        // Initialisation des variables
        //

        $labos = array();
        $etudiants = array();

        for($i=1; $i <= $nb_labos; $i++)
            $labos[$i] = array();

        for($i=0; $i < $nb_etudiants; $i++)
            $etudiants[] = $i;

        $boucle   = 1;   // iteration de la boucle
        $securite = 100; // fin de boucle de securite

        while ($boucle < $securite)
        {   
            $boucle++;

            $etudiants_rand = array();
            $paires = array();

            for($i=0; $i <  $nb_etudiants; $i++)
                $paires[$i] = array();

            //
            // Formation des equipes
            //

            for($j=1; $j <= $nb_labos; $j++)
            {
                $equipes = array();
                $choisis = array();

                for($k=0; $k < $nb_etudiants; $k++)
                {   
                    if (in_array($k, $equipes))
                        continue;

                    $e = $etudiants;

                    $choisis[] = $k;

                    // Exclure les partenaires deja choisis
                    foreach($choisis as $c)
                        unset($e[$c]);

                    // Exclure les anciens partenaires de k
                    foreach($paires[$k] as $p)
                        unset($e[$p]);

                    // Choisir le partenaire

                    $partenaire = array_rand($e);

                    $choisis[]  = $partenaire;
                    $paires[$partenaire][] = $k;
                    $paires[$k][] = $partenaire;

                    $equipes[$k] = $partenaire;
                    $equipes[$partenaire] = $k;
                }

                ksort($equipes); 
                $etudiants_rand[$j] = $equipes;
            }

            // Verifier les doublons

            $paires = array();
            $doublon = FALSE;

            foreach($etudiants_rand as $e)
            {
                foreach($e as $p_key => $p_val)
                {
                    if ($p_val === NULL)
                    {
                        $doublon = TRUE;
                        break;
                    }

                    if ( ! array_key_exists($p_key, $paires))
                        $paires[$p_key] = array();

                    if (in_array($p_val, $paires[$p_key]))
                    {
                        $doublon = TRUE;
                        break;
                    }

                    $paires[$p_key][] = $p_val;
                }
            }

            if ($doublon)
            {
                continue;
            }
        }

        $this->data['equipes'] = $etudiants_rand;

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/matrice', $this->data);
        $this->load->view('commons/footer', $this->data);
    }
}
