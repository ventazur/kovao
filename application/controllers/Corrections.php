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
 * CORRECTIONS
 *
 * ============================================================================ */

class Corrections extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            return;
        }

        $this->data['current_controller'] = strtolower(__CLASS__);
	}

    /* ------------------------------------------------------------------------
     *
     * (ajax) Allouer points
     *
     * ------------------------------------------------------------------------ */
    public function allouer_points()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id', 'question_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
	
        foreach($post_data as $k => $v)
		{
			switch($k)
			{
				case 'points' :
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

        if (($points_obtenus = $this->Evaluation_model->allouer_points($post_data)) !== TRUE)
        {
            echo json_encode($points_obtenus);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Allouer points d'une grille
     *
     * ------------------------------------------------------------------------ */
    public function allouer_points_grille()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id', 'question_id', 'grille_id')) === FALSE)
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
				case 'points' :
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

        if (($points_obtenus = $this->Evaluation_model->allouer_points_grille($post_data)) !== FALSE)
        {
            echo json_encode($points_obtenus);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Allouer points manuellement
     *
     * ------------------------------------------------------------------------ */
    public function allouer_points_manuel()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id', 'question_id')) === FALSE)
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
				case 'points_obtenus' :
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
		// Un hack vraiment laid pour etre consistant avec les autres fonctions
		// pour allouer des points.
		//

		$post_data['points'] = str_replace(',', '.', $post_data['points_obtenus']);

        //
        // Verifier qu'il s'agit d'un nombre
        //

        if ( ! is_numeric($post_data['points']))
        {
            echo json_encode(FALSE);
            return;
        }

		//
		// Allouer les points
		//

        if (($points_obtenus = $this->Evaluation_model->allouer_points_manuel($post_data)) !== FALSE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Resetter les corrections d'une grille pour une question
     *
     * ------------------------------------------------------------------------ */
    public function reset_corrections_grille()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('soumission_id', 'evaluation_id', 'question_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($this->Evaluation_model->reset_corrections_grille($post_data))
        {
            $_SESSION['redirect_corrections']             = $post_data['soumission_reference'];
            $_SESSION['redirect_corrections_question_id'] = $post_data['question_id'];

            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Sauvegarder le commentaire (pour une soumission)
     *
     * ------------------------------------------------------------------------ */
    public function sauvegarder_commentaire_soumission()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if (empty($post_data['commentaire']))
        {
            echo json_encode(TRUE);
            return;
        }

        $r = $this->Evaluation_model->correction_sauvegarder_commentaire_soumission($post_data);

        echo json_encode($r);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Sauvegarder le commentaire (pour une question)
     *
     * ------------------------------------------------------------------------ */
    public function sauvegarder_commentaire()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if (empty($post_data['commentaire']))
        {
            echo json_encode(TRUE);
            return;
        }

        $r = $this->Evaluation_model->correction_sauvegarder_commentaire(
            $post_data['soumission_id'],
            array(
                'question_id' => $post_data['question_id'] ?? 0,
                'tableau_no'  => $post_data['tableau_no'] ?? 0,
                'commentaire' => $post_data['commentaire']
            )
        );

        echo json_encode($r);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer le commentaire a l'etudiant (pour une soumission)
     *
     * ------------------------------------------------------------------------ */
    public function effacer_commentaire_soumission()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('soumission_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $r = $this->Evaluation_model->correction_effacer_commentaire_soumission($post_data);

        echo json_encode($r);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer le commentaire a l'etudiant (pour une question)
     *
     * ------------------------------------------------------------------------ */
    public function effacer_commentaire()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $r = $this->Evaluation_model->correction_effacer_commentaire(
            $post_data['soumission_id'],
            array(
                'tableau_no'  => $post_data['tableau_no'] ?? 0,
                'question_id' => $post_data['question_id'] ?? 0
            )
        );

        echo json_encode($r);
        return; 
    }

    /* ------------------------------------------------------------------------
     *
     * Corriger
     *
     * ------------------------------------------------------------------------
     *
     * Il est possible de fournir soit la soumission_id, ou la soumission_reference,
     * pour corriger la soumission choisie.
     *
     * ------------------------------------------------------------------------ */
	public function corriger($arg)
    {
        if ( ! $this->est_enseignant)
		{
            redirect(base_url());
			exit;
		}

        //
        // Verifier s'il s'agit de la soumission_id, ou de la soumission_reference
        //

        if (ctype_digit($arg))
        {
            $soumission = $this->Evaluation_model->extraire_soumission($arg);
        }
        elseif (ctype_alpha($arg))
        {
            $soumission = $this->Evaluation_model->extraire_soumission_reference($arg);
        }
        else
        {
            redirect(base_url());
            exit;
        }

        //
        // Verifier que cette soumission existe
        //

        if (empty($soumission))
        {
            redirect(base_url());
            exit;
        }

        $soumission_id = $soumission['soumission_id'];

        // 
        // Effacer toutes les redirections
        // 

        unset($_SESSION['redirect_corrections']);
        unset($_SESSION['redirect_corrections_question_id']);

		//
		// Verifier que la soumission_id soit valide (pas du texte ou autre)
		//

		// @TODO

        //
        // Verifier que cette soumission appartient à cet enseignant
        //

        if ($soumission['enseignant_id'] != $this->enseignant_id)
        {
            redirect(base_url());
            exit;
        }

        //
        // Extraire les informations connexes a la soumission
        //

        $evaluation_id = $soumission['evaluation_id'];

        $evaluation = json_decode(gzuncompress($soumission['evaluation_data_gz']), TRUE);

        $enseignant = $this->Enseignant_model->extraire_enseignant($soumission['enseignant_id']);

        $cours     = json_decode(gzuncompress($soumission['cours_data_gz']), TRUE);
        $questions = json_decode(gzuncompress($soumission['questions_data_gz']), TRUE);

        //
        // Extraire les commentaires
        //

        if (array_key_exists('commentaires_data_gz', $soumission) && $soumission['commentaires_data_gz'] != NULL)
        {
            $commentaires = unserialize(gzuncompress($soumission['commentaires_data_gz']));
        }
        else
        {
            $commentaires = array();
        }

        //
        // Extraire les images
        //

        // Retrocompatibilite
        // Les anciennes soumissions n'avaient pas ce champ.
        if (array_key_exists('images_data_gz', $soumission) && $soumission['images_data_gz'] != NULL)
        {
            $images = json_decode(gzuncompress($soumission['images_data_gz']), TRUE);
        }
        else
        {
            $images = array();
        }

		if ( ! empty($images))
        {
			foreach($images as $question_id => $image)
            {
                if ( ! $image['s3'])
                {
                    //
                    // Verifier si les images sont toujours existantes sur le disque.
                    //

                    if ( ! file_exists($this->config->item('documents_path') . $image['doc_filename']))
                    {
                        unset($images[$question_id]);
                        continue;
				    }	
                }
                else
                {
                    /*
                     * Cette etape rajoute 100ms et je ne suis pas certain qu'elle soit necessaire.
                     *
                    if ( ! $this->Document_model->existe_s3(array('dossier' => 'evaluations', 'key' => $image['doc_filename'])))
                    {
                        unset($images[$question_id]);
                        continue;
                    }
                    */
                }
			}
        }

		//
		// Extraire les documents des etudiants
		//

        $documents = $this->Document_model->extraire_documents_soumission_terminee($soumission_id);

		//
		// Grilles de correction
		//

        $gc = $this->Question_model->extraire_grilles_correction_par_evaluation_id($evaluation_id);

        //
        // Barre de defilement
        //

		$evaluations_liste = array();

		$soumission_ref_clef  = 0;
		$soumission_refs  = array();
		$soumission_ref_p = NULL; // precedente
		$soumission_ref_s = NULL; // suivante

		if ( ! empty($this->session->evaluations_liste))
		{
			$evaluations_liste = unserialize($this->session->evaluations_liste);

			if ($evaluations_liste && array_key_exists($evaluation_id, $evaluations_liste))
			{
				$clef = array_search($soumission['soumission_reference'], $evaluations_liste[$evaluation_id]);

				$soumission_ref_clef = $clef + 1;
				$soumission_refs = $evaluations_liste[$evaluation_id];

				// soumission precedente
				if (array_key_exists($clef - 1, $evaluations_liste[$evaluation_id]))
				{
					$soumission_ref_p = $evaluations_liste[$evaluation_id][$clef - 1];
				}

				// soumission suivante
				if (array_key_exists($clef + 1, $evaluations_liste[$evaluation_id]))
				{
					$soumission_ref_s = $evaluations_liste[$evaluation_id][$clef + 1];
				}
			}
		}

        //
        // Questions a corriger
        //

        $questions_a_corriger = array();
		$i = 0;

        foreach($questions as $q)
        {
			$i++;
		
			// Ne pas corriger les question-sondages

			if (array_key_exists('sondage', $q) && $q['sondage'])
			    continue;

            //
			// Corriger seulement les questions des types : 
			//
			// - a developpement (2)
			// - a televersement (10)
			// - a developpement court (12)
            //

			if ( ! in_array($q['question_type'], array(2, 10, 12)))
			    continue;

            //
            // Ne pas corriger les questions des types 2 et 12 si l'etudiant n'a rien ecrit
            //

            // Cette ligne empeche de recorriger manuellement la question si l'etudiant a obtenu 0 (2021-09-01).
            //if (in_array($q['question_type'], array(2, 12)) && $q['corrigee'] && $q['points_obtenus'] <= 0)
            //    continue; 

            if (in_array($q['question_type'], array(2, 12)) && empty(trim($q['reponse_repondue'])))
                continue;

            //
            // Ne pas corriger les questions de type 10 si l'etudiant n'a pas televerse de document, 
            // car elles ont deja ete corrigees.
            //

			if ($q['question_type'] == 10 && $q['reponse_repondue'] == 9)
			    continue;

            //
			// Ajouter la question dans la liste des questions a corriger
            //

			$question_id = $q['question_id'];

			$q['question_no'] = $i;

			$questions_a_corriger[$question_id] = $q;
        }

        //
        // Securite
        //

        if (1 == 2)
        {
            // 
            // Registre de l'activite de l'etudiant (version 2)
            //

            $activite2 = $this->Evaluation_model->extraire_activite_evaluation2(
                array(
                    'soumission_reference' => $soumission['soumission_reference']
                )
            );

            //
            // Si la version 2 n'est pas disponible (pour les soumissions avant le 1er avril 2021), alors
            // faire determiner le registre selon la version 1
            //

            if (empty($activite2))
            {
                $activite = $this->Evaluation_model->extraire_activite_evaluation($soumission['etudiant_id'], $soumission['soumission_debut_epoch'], $soumission['soumission_epoch']);

                $activite_pertinente = array();
                $fureteurs_desc = array();

                if ( ! empty($activite))
                {
                    foreach($activite as $a)
                    {
                        if ( ! preg_match('/evaluation\/' . $soumission['evaluation_reference'] . '/', $a['uri']))
                            continue;

                        //
                        // Creer un fureteur_id.
                        // Retrocompatibilite pour l'activite avant le 2020-06-03.
                        //

                        if (empty($a['fureteur_id']))
                        {
                            $a['fureteur_id'] = hash('sha256', $a['plateforme'] . $a['fureteur']);
                        }

                        $fureteurs_desc[$a['fureteur_id']] = $a['plateforme'] . ', ' . $a['fureteur'] . ( ! empty($a['mobile']) ? ' (' . $a['mobile'] . ')' : '');

                        $activite_pertinente[] = $a;
                        continue;
                    }

                    //
                    // Ajouter les donnees de la soumission
                    // 

                    $s_data = json_decode($soumission['soumission_data'], TRUE);

                    // Retrocompatibilite pour les soumissions avant le 2020-06-03.
                    if ( ! array_key_exists('fureteur_id', $s_data))
                    {
                        $this->agent->parse($s_data['agent_string']);
                        $s_data['fureteur_id'] = hash('sha256', $this->agent->platform($s_data['agent_string']) . $this->agent->browser($s_data['agent_string']) . ' ' . $this->agent->version());
                    }

                    $activite_pertinente[] = array(
                        'epoch'          => $soumission['soumission_epoch'],
                        'adresse_ip'     => $soumission['adresse_ip'],
                        'etudiant_id'    => $soumission['etudiant_id'],
                        'unique_id'      => $soumission['unique_id'],
                        'fureteur_id'    => $s_data['fureteur_id'],
                        'fureteurs_desc' => $fureteurs_desc,
                        'uri'            => 'soumission ' . $soumission['soumission_reference']
                    );
                }
            } // if empty(activite2)
        }

        $this->data = array_merge(
            array(
                'cours'         => $cours,
                'enseignant'    => $enseignant,
                'evaluation'    => $evaluation,
                'questions'     => $questions,
                'questions_a_corriger' => $questions_a_corriger,
                'images'        => $images,
                'soumission'    => $soumission,
                'commentaires'  => $commentaires,
                'gc'            => $gc,
                'documents'     => $documents,
                'activite'      => $activite ?? NULL,
                'activite2'     => $activite2 ?? NULL,
                'fureteurs_desc'   => $fureteurs_desc ?? array(),
                'soumission_ref_p' => $soumission_ref_p,
                'soumission_ref_s' => $soumission_ref_s,
				'soumission_refs'  => $soumission_refs,
                'soumission_ref_clef' => $soumission_ref_clef,
                'version_etudiante'   => FALSE // pour les labos (consulter, correcctions) (ajout le 5 mars 2025)
            ), $this->data
        );

        $this->load->view('commons/header', $this->data);
        $this->load->view('corrections/corriger', $this->data);
        $this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * Index (liste des corrections)
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        if ( ! $this->enseignant['semestre_id'] && empty($semestre_id))
        {
            // Aucun semestre en vigueur

            $this->_affichage('aucun-semestre');
            return;
        }

        //
        // Le semestre actif de l'enseignant
        //

        $semestre_id = empty($semestre_id) ? $this->enseignant['semestre_id'] : $semestre_id;

        //
        // Extraire toutes les evaluations en attente de correction
        //

        $soumissions = $this->Evaluation_model->extraire_soumissions(
            array(
                'enseignant_id' => $this->enseignant['enseignant_id'],
                'semestre_id' => $semestre_id,
                'corrections_terminees' => 0
            )
        );

        //
        // Enseignant
        //

        $enseignant = $this->Enseignant_model->extraire_enseignant($this->enseignant['enseignant_id']);

        //
        // Cours
        //

        $cours_raw = $this->Cours_model->lister_cours_selectionnes($this->enseignant['enseignant_id'], $semestre_id);

        //
        // Est-ce qu'il y a des evaluations qui ne sont pas dans les cours selectionnes ?
        // Si oui, ajoutez les cours aux cours selectionnes.
        // 
        // A quoi ca sert ?
        // Par exemple, un enseignant test une evaluation (via previsualisation) pour un cours qu'il ne donne pas.
        //
        // Cette portion pourrait etre enlevee sans affecter le fonctionnement du site.
        //
        
        if ( ! empty($soumissions)) 
        {
            $cours_des_soumissions = array();

            foreach($soumissions as $s)
            {
                $cours = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
                $cours_id = $cours['cours_id'];

                // Ceci sert a ajouter des cours dont des soumissions ont ete trouvees mais dont ces cours ne sont pas 
                // dans les cours selectionnes pour le semestre selectionne.
                if ( ! array_key_exists($cours_id, $cours_raw))
                {
                    $cours = array();
                    $cours = $this->Cours_model->extraire_cours(array('cours_id' => $cours_id));

                    $cours['cours_id']      = $cours_id;
                    $cours['enseignant_id'] = $this->enseignant['enseignant_id'];

                    // $cours['semestre_id']   = $this->enseignant['semestre_id']; // remplace par la ligne suivante
                    $cours['semestre_id']  = $semestre_id;

                    $cours_raw[$cours_id] = $cours;

                    unset($cours_id, $cours);
                } 
            } 
        }

        //
        // Verifier dans quel(s) cours(s) il y a des evaluations en attente
        //

        foreach($cours_raw as $cours_id => $c)
        {
            $soumission_trouvee = 0;

            if ( ! empty($soumissions))
            {
                foreach($soumissions as $soumission_id => $s)
                {
                    $s_cours = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
        
                    if ($s_cours['cours_id'] == $cours_id)
                    {
                        $soumission_trouvee = 1;
                    }
                }
            }

            $cours_raw[$cours_id]['soumission_trouvee'] = $soumission_trouvee;
        }

        if ( ! empty($soumissions))
        {
            //
            // Preparer les soumissions
            //
            
            $evaluations    = array();
            $evaluation_ids = array();

            foreach($soumissions as $soumission_id => $s) 
            {
                $s_cours = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
                $s_evaluation = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);

                $evaluations[$s['evaluation_id']] = array_merge($s_evaluation, $s_cours);

                // p($s_evaluation); die;

                $soumissions[$soumission_id]['cours_id'] = $s_cours['cours_id'];

                if ( ! in_array($s['evaluation_id'], $evaluation_ids))
                {
                    $evaluation_ids[] = $s['evaluation_id'];
                }
            }

			//
			// Organiser les evaluations selon la presentation a l'ecran
			//

			// evaluation_id > groupe > soumission_id

			$eleves = $this->Cours_model->lister_eleves($semestre_id, array('organisation' => 'groupe'));

			$evaluations_eleves = array();

			if ( ! empty($soumissions))
			{
				foreach($soumissions as $s)
				{
					$evaluation_id = $s['evaluation_id'];
					$groupe = 0;

					if ( ! array_key_exists($evaluation_id, $evaluations_eleves))
						$evaluations_eleves[$evaluation_id] = array();

					if (($path = array_search_recursive($s['numero_da'], $eleves)) !== FALSE)
					{
						// Eleve trouve

						$groupe = $path[1];
					}

					if ( ! array_key_exists($groupe, $evaluations_eleves[$evaluation_id]))
					{
						$evaluations_eleves[$evaluation_id][$groupe] = array();
					}

					$evaluations_eleves[$evaluation_id][$groupe][] = $s['soumission_id'];
				}
			}

            // 
            // Determiner l'ordre des evaluations
            //

            $evaluations_liste = array();

            foreach($cours_raw as $c)
            {
                foreach($evaluations as $evaluation_id => $e)
                {
                    $evaluations_liste[$evaluation_id] = array();
                    
                    foreach($soumissions as $soumission_id => $s)
                    {
                        if ($s['evaluation_id'] != $evaluation_id) continue;

                        $evaluations_liste[$evaluation_id][] = $s['soumission_reference'];
                    }
                }
            }

            //
            // Permet de sauter d'une evaluation a l'autre, vers l'avant et vers l'arriere.
            //

            $_SESSION['evaluations_liste'] = serialize($evaluations_liste);
            unset($_SESSION['evaluations_chemin']);

            $this->data = array_merge(
                $this->data,
                array(
                    'enseignant'  => $enseignant,
                    'cours_raw'   => $cours_raw,
                    'evaluations' => $evaluations,
					'evaluations_eleves' => $evaluations_eleves, 
                    'soumissions' => $soumissions
                )
            );

        } // if ! empty($soumissions)
        else
        {
            $this->data = array_merge(
                $this->data,
                array(
                    'cours_raw' => $cours_raw,
                    'cours' => @$cours
                )
            );
        }

        $this->_affichage(); 
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * Liste des soumissions a corriger manuellement
     *
     * ------------------------------------------------------------------------ */
	public function index_DEV()
    {
        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Rotation d'une image
     *
     * ------------------------------------------------------------------------ */
	public function rotation_image()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $post_data = $this->input->post();

		$ids_obligatoires = array('doc_id', 'soumission_id');

		foreach($ids_obligatoires as $id)
		{
			if ( ! array_key_exists($id, $post_data) || ! is_numeric($post_data[$id]))
			{
				echo json_encode(FALSE);
				return;
			}
		}

		$result = $this->Document_model->rotation_image_soumission_terminee(
			$post_data['rotation'], $post_data['doc_id'], $post_data['soumission_id'] 
		);

		echo json_encode($result);
		return;
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
				$this->load->view('corrections/corrections_aucun_semestre', $this->data);
				break;

			case 'non-corrigee' :
				$this->load->view('corrections/non_corrigee', $this->data);
                break;

            case 'max-vues' :
                $this->load->view('corrections/max_vues', $this->data);
                break;

            case 'ancien-semestre' :
                $this->load->view('corrections/ancien_semestre', $this->data);
                break;

			default :
                $this->load->view('corrections/corrections', $this->data);
				break;
		}

        $this->load->view('commons/footer');
	}
}
