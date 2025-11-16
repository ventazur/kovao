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
 * LABORATOIRE
 *
 * ----------------------------------------------------------------------------
 *
 * Ceci est un projet prototype pour permettre aux etudiants de remplir
 * un rapport de laboratoire pendant la realisation de l'experience au labo.
 *
 * ----------------------------------------------------------------------------
 *
 * Tables :
 *
 * 'lab_data'            : donnees diverses sur le laboratoire
 * 'lab_valeurs'         : valeurs pour les comparaisons (incluant les unites et la nsci)
 * 'lab_points'          : proprietes de chaque champ (type de champ, points, description, etc.)
 *                         (aurait du se nommer 'lab_champs')
 * 'lab_points_champs'   : donnees sur la correction de chaque champ
 * 'lab_points_tableaux' : donnees sur les points totaux et obtenus pour chaque tableau
 *
 * ============================================================================ */

class Laboratoire extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->logged_in)        
        {
            redirect(base_url());
            exit;
        }
    }

    /* ----------------------------------------------------------------
     *
     * Index
     *
     * ---------------------------------------------------------------- */
    public function index()
    {
        redirect(base_url());
        exit;
    }

    /* ----------------------------------------------------------------
     *
     * Precorrections - RESET
     *
     * ---------------------------------------------------------------- */
	public function precorrections_reset()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }
	
        $post_data = $this->input->post();

        if ( ! $this->est_enseignant)
        {
            echo json_encode(FALSE);
            return;
        }

        $evaluation_reference = 'previsual';
        $evaluation_id = $post_data['evaluation_id'];

        if (($traces = $this->Evaluation_model->lire_traces($evaluation_reference, $evaluation_id)) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        // extraire evaluation

        $evaluation = $this->Evaluation_model->extraire_evaluation($post_data['evaluation_id']);

        if (empty($evaluation))
        {
            echo json_encode(['res' => FALSE]);
			return;
        }

        if (empty($evaluation['lab_parametres']))
        {
            $r['res']        = FALSE;
            $r['erreur']     = TRUE;
			$r['erreur_msg'] = "Il n'y a pas de parametre, donc aucune concernant la precorrection.";
			
			echo json_encode($r);
			return;
        }

        $lab_parametres = json_decode($evaluation['lab_parametres'], TRUE);

        $traces_arr = unserialize($traces);

        $traces_arr['precorrections'] = array(
            'compte'        => 0,
            'penalite'      => 0,
            'penalite_str'  => 0,
            'dates'         => array()
        );

        //
        // Enregistrer les traces
        //

        $r = $this->Evaluation_model->ecrire_traces(
			$evaluation_reference,
			$evaluation_id,
			$traces_arr, 
            array(
                'session_id' => NULL
            )
        );

        echo json_encode(
            array(
                'res' => $r,
                'precorrection_essais' => $lab_parametres['precorrection_essais'] ?? 0
            )
        );
        return;
    }

    /* ----------------------------------------------------------------
     *
     * Traces des precorrections
     *
     * ---------------------------------------------------------------- */
	public function _traces_precorrections($evaluation_id, $evaluation_reference = NULL, $lab_parametres = array())
    {
		//
		// Etudiants
		// 

		if ($this->est_etudiant)
        {
			//
			// Verifier que cette evaluation est toujours en vigueur
			// 

			if ( ! $this->Evaluation_model->verifier_evaluation_reference_simple($evaluation_reference))
			{
				echo json_encode(FALSE);
				return;
			}
        }

        //
        // Enseignant
        //

        if ($this->est_enseignant)
        { 
            $evaluation_reference = 'previsual';
        }

        //
        // Ajouter | Modifier les traces
        //

        if (($traces = $this->Evaluation_model->lire_traces($evaluation_reference, $evaluation_id)) === FALSE)
        {
            return 0;
        }

        $traces_arr = unserialize($traces);

        $n_compte = 1;

        if (array_key_exists('precorrections', $traces_arr))
        {
            $n_compte = $traces_arr['precorrections']['compte'] + 1;
        }

        $pp = precorrections_penalite(
            $n_compte,
            $lab_parametres['precorrection_essais'] ?? '10',
            $lab_parametres['precorrection_penalite'] ?? '0.5'
        );

        $precorrections_dates = $traces_arr['precorrections']['dates'] ?? array();
        $precorrections_dates[$n_compte] = date_humanize(date('U'), TRUE);
    
        $traces_arr['precorrections'] = array(
            'compte'        => $n_compte,
            'penalite'      => $pp['penalite'],
            'penalite_str'  => str_replace('.', ',', $pp['penalite_str']),
            'dates'         => $precorrections_dates
        );

        //
        // Enregistrer les traces
        //

        $r = $this->Evaluation_model->ecrire_traces(
			$evaluation_reference,
			$evaluation_id,
			$traces_arr, 
            array(
                'session_id' => NULL
            )
        );

        return $traces_arr['precorrections'];        
    }

    /* ----------------------------------------------------------------
     *
     * (AJAX) Correction d'un laboratoire
     *
     * ---------------------------------------------------------------- */
	public function corriger_laboratoire_ajax()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }
	
        $post_data = $this->input->post();

		//
		// Verifier que les donnees sur l'evaluation sont presentes 
		//

		if ( ! array_key_exists('evaluation_data', $post_data) || empty($post_data['evaluation_data']))
		{
			$r['erreur'] = TRUE;
			$r['erreur_msg'] = "Les donnees sur l'evaluation sont manquantes.";
			
			echo json_encode($r);
			return;
		}

		if ( ! array_key_exists('champs_data', $post_data) || empty($post_data['champs_data']) || ! is_array($post_data['champs_data']))
		{
			$r['erreur'] = TRUE;
			$r['erreur_msg'] = "Les donnees sur les champs sont manquantes.";
			
			echo json_encode($r);
			return;
		}

		//
		// Extraire les champs
		//

        $lab_prefix = $post_data['evaluation_data']['lab_prefix'] ?? NULL;

		$champs_a_corriger = array();

        foreach($post_data['champs_data'] as $c_arr)
        {
			if (preg_match('/^' . $lab_prefix . '-(.+)$/', $c_arr['name'], $matches)) 
			{
				$champ = $matches[1];
			} 

            /*
			$champs_a_corriger[$champ] = array(
				'valeur' => $c_arr['val']
            );
             */

            $champs_a_corriger[$champ]['valeur'] = $c_arr['val'];
        }
        
        //
        // Extraire l'evaluation pour verifier les parametres de la precorrection
        //
        // Afin de les etudiants ne devinent pas une evaluation_id avec des precorrections activees,
        // ou avantageuses, j'ajoute le lab_prefix pour limiter les possibilites. Ce n'est pas tres
        // securitaire pour autant. A ameliorer.

        $evaluation = $this->Evaluation_model->extraire_evaluation(
            $post_data['evaluation_data']['evaluation_id'], 
            array(
                'lab_prefix' => $post_data['evaluation_data']['lab_prefix']
            )
        );

        if (empty($evaluation))
        {
			$r['erreur'] = TRUE;
			$r['erreur_msg'] = "L'evaluation est introuvable.";
			
			echo json_encode($r);
			return;
        }

        if (empty($evaluation['lab_parametres']))
        {
			$r['erreur'] = TRUE;
			$r['erreur_msg'] = "Il n'y a pas de parametre, donc aucune concernant la precorrection.";
			
			echo json_encode($r);
			return;
        }

        $lab_parametres = json_decode($evaluation['lab_parametres'], TRUE);

        if ( ! $lab_parametres['precorrection'])
        {
			$r['erreur'] = TRUE;
			$r['erreur_msg'] = "La precorrection a ete desasctivee par l'enseignant.";
			
			echo json_encode($r);
			return;
        }

        $r = $this->Lab_model->corriger_laboratoire(
			$post_data['evaluation_data']['evaluation_id'], 
			$champs_a_corriger,
			array(
				'precorrection' => TRUE
			)
        );

        $r['precorrections'] = $this->_traces_precorrections(
            $post_data['evaluation_data']['evaluation_id'],
            $post_data['evaluation_data']['evaluation_reference'],
            $lab_parametres
        );

        echo json_encode($r);
		return;
    }

    /* ----------------------------------------------------------------
     *
	 * (AJAX) recorrection_tableaux
	 *
	 * ----------------------------------------------------------------
	 *
	 * Cette methode permet de recorriger les tableaux sans changer
	 * la correction initiale. La nouvelle correction apparait dans les
	 * outils de developpement du fureteur -> dans requetes AJAX -> message.
	 *
	 * @TODO Changer la correction initiale par la nouvelle suite a l'approbation
	 *       de l'enseignant.
     *
     * ---------------------------------------------------------------- */
	public function recorriger_tableaux()
	{
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }
	
        $post_data = $this->input->post();

		//
		// Verifier que les donnees sur l'evaluation sont presentes 
		//

		if ( ! array_key_exists('soumission_id', $post_data) || empty($post_data['soumission_id']))
		{
			echo json_encode('erreur : soumission_id absent');
			return;
		}

        //
        // Extraire la soumission
        //

        $soumission = $this->Evaluation_model->extraire_soumission($post_data['soumission_id'], array('extraire_gz' => TRUE));
    
        if (empty($soumission))
        {
            echo json_encode('erreur : soumission introuvable');
            return;
        }

        if ( ! $soumission['lab'])
        {
            echo json_encode("erreur : cette soumission n'est pas un lab");
            return;
        }

        $lab_data = array(
            'lab_data'              => json_decode($soumission['lab_data'], TRUE),
            'lab_valeurs'           => json_decode($soumission['lab_valeurs'], TRUE),
            'lab_points'            => json_decode($soumission['lab_points'], TRUE),
            'lab_points_champs'     => json_decode($soumission['lab_points_champs'], TRUE),
            'lab_points_tableaux'   => json_decode($soumission['lab_points_tableaux'], TRUE)
        );

        $lab_data['lab_parametres'] = json_decode($lab_data['lab_data']['lab_parametres'], TRUE);

        //
        // Il faut reconstruire la variable 'champs_a_corriger' pour etre compatible avec
        // la methode corriger_laboratoire.
        //
        
        $champs_a_corriger = array();

        foreach($lab_data['lab_points_champs'] as $c => $c_arr)
        {
            $champs_a_corriger[$c] = array(
                'valeur' => $c_arr['reponse']
            );
        }

        $r = $this->Lab_model->corriger_laboratoire(
            $soumission['evaluation_id'],
			$champs_a_corriger,
			array(
				'precorrection' => TRUE
			)
        );

        //
        // Calcul les points totaux
        //

        $points_tableaux_totaux = $r['points_bilan']['points_totaux'];
        $points_tableaux_obtenus = $r['points_bilan']['points_totaux_obtenus'];

        $precorrections_penalite_pct_unite = $lab_data['lab_parametres']['precorrection_penalite'];

        $precorrections_compte       = $lab_data['lab_data']['lab_precorrections']['penalite'] ?? 0;
        //$precorrections_penalite_pct = $lab_data['lab_data']['lab_precorrections']['penalite_pct'] ?? 0;
        $precorrections_penalite_pct = $precorrections_penalite_pct_unite * $precorrections_compte ?? 0;

        $precorrections_penalite = 0;

        if ($precorrections_penalite_pct > 0 && $precorrections_compte > 0)
        {
            $precorrections_penalite = $points_tableaux_totaux * ($precorrections_penalite_pct/100);
        }

        $points_tableaux_obtenus = $points_tableaux_obtenus - $precorrections_penalite;

        if ($points_tableaux_obtenus < 0)
        {
            $points_tableaux_obtenus = 0;
        }

        // ancien points des tableaux
        
        $lab_points_tableaux = $lab_data['lab_points_tableaux'];
        $lab_points_tableaux_obtenus = 0;

        foreach ($lab_points_tableaux as $t)
        {
            $lab_points_tableaux_obtenus += $t['points_obtenus'];
        }

        // points des questions

        $questions_points_totaux = 0;
        $questions_points_obtenus = 0;

        foreach($soumission['questions_data'] as $q)
        {
            $questions_points_totaux  += $q['question_points'];
            $questions_points_obtenus += $q['points_obtenus']; 
        }   

        p(
            array(
                'points_bilan' => array(
                    'points_tableaux_totaux'  => $points_tableaux_totaux,
                    'points_tableaux_obtenus_nouveau' => $points_tableaux_obtenus,
                    'points_tableaux_obtenus_ancien'  => ($lab_points_tableaux_obtenus - $precorrections_penalite),
                    'points_questions_totaux' => $questions_points_totaux,
                    'points_questions_obtenus' => $questions_points_obtenus,
                    'points_evaluation' => ($points_tableaux_obtenus + $questions_points_obtenus) . '/' . ($points_tableaux_totaux + $questions_points_totaux)
                )
            )
        );

        p($r); die;

        // echo json_encode($r);
		// return;
    }
}
