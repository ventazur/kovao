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
 * CONSULTER
 *
 * ----------------------------------------------------------------------------
 *
 * Permet de consulter une evaluation corrigee, par un enseignant et un etudiant.
 *
 * ============================================================================ */

class Consulter extends MY_Controller 
{
	public function __construct()
    {
    	parent::__construct();

		$this->load->library('form_validation');
	}

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
    public function _remap($method = NULL)
    {
        //
        // La methode est une fonction.
        //

        $valid_methods = array(
            'index'
        );

        if (in_array($method, $valid_methods))
        { 
			$this->$method();
			return;
		}	

        //
        // La methode est une reference
        //

        if (ctype_alpha($method) && (strlen($method) >= 8 || strlen($method) <= 12))
        {
            $this->_voir($method);
            return;
        }

        //
        // Les methodes ajax ne peuvent avoir le meme nombre de caracteres
        // que les references des soumissions (presentement 8).
        //

        $valid_ajax_methods = array(
            'changer_points_soumission',
            'changer_points',
            'effacer_ajustement_soumission',
            'effacer_ajustement',
            'voir_defilement',
			'rotation_image'
        );

        if (in_array($method, $valid_ajax_methods))
        {
            if ( ! $this->est_enseignant)
            {
                redirect(base_url());
                return;
            }

            $this->$method();
            return;
        }

        redirect(base_url());
        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
		//
		// Preparation des parametres du formulaire (form)
		//

		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');
        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

        //
        // Definition des messages d'erreur
        //

		$errors = array(
			'reference' => null
		);

		$this->form_validation->set_rules('reference', 'Référence', 'required|min_length[8]|max_length[12]');

		//
		// Validation du formulaire (form)
		// 

        $this->data['errors'] = $errors;

       	if ($this->form_validation->run() == FALSE)
        {
			//
			// Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.
			//

			if ($this->form_validation->error('reference') !== '')
			{
				$this->data['errors']['reference'] = 'is-invalid'; // pour bootstrap
			}
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			// Verification de l'autorisation a se connecter.
			//

            $post_data = $this->input->post(NULL, TRUE);

            //
            // Verifier qu'une reference est presente
            //

            if (array_key_exists('reference', $post_data))
            {
                $post_data['reference'] = trim($post_data['reference']);
            }

            //
            // Verifier le sous domaine de cette soumission.
            //

            $sous_domaine_soumission = $this->Evaluation_model->sous_domaine_soumission($post_data['reference']);

            if ($this->sous_domaine == $sous_domaine_soumission || $sous_domaine_soumission == FALSE)
            {
                redirect(base_url() . 'consulter/' . $post_data['reference']);
            }
            else
            {
                // redirect('https://' . $sous_domaine_soumission . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/consulter/' . $post_data['reference']);
                redirect('https://' . $sous_domaine_soumission . '.' . $this->domaine . '/consulter/' . $post_data['reference']);
            }

            return;
        }

		$this->_affichage('www');
		return;

		$this->load->view('commons/header', $this->data);
		$this->load->view('consulter/consulter_corrections', $this->data);
		$this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Defilement
     *
     * ------------------------------------------------------------------------
     *
     * Permet de voir les evaluations l'une apres l'autre.
     * Cette fonction sert a generer le premier url de la consultation.
     *
     * ------------------------------------------------------------------------ */
    public function voir_defilement()
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

        if ( ! array_key_exists('soumission_references', $post_data) || empty($post_data['soumission_references']))
        {
            echo json_encode(FALSE);
            return;
        }

        $_SESSION['consulter_soumission_references'] = serialize($post_data['soumission_references']);
        $_SESSION['consulter_defilement_expiration'] = $this->now_epoch + 60*60*12; // 12 heures

        // $soumission_references = unserialize($post_data['soumission_references']);
        $soumission_references = $post_data['soumission_references'];

        echo json_encode('consulter/' . $soumission_references[0] . '/defilement');
        return;
	}

    /* ------------------------------------------------------------------------
     *
     * Voir une evaluation
     *
     * ------------------------------------------------------------------------
     *
     * Permet de voir l'evaluation.
     *
     * ------------------------------------------------------------------------ */
	public function _voir($reference)
    {
        $soumission = $this->Evaluation_model->extraire_soumission_reference($reference);

        //
		// Verifier l'existence de cette soumission.
        //

		if (empty($soumission))
		{
            // Cette soumission n'existe pas.

			$this->_affichage('introuvable');
			return;
        }

        //
        // Verifier si l'evaluation est corrigee
        //

        if ( ! $soumission['corrections_terminees'])
        {
            if ( ! ($this->est_enseignant && $this->enseignant_id == $soumission['enseignant_id'] && $this->uri->segment(3) == 'noncorrigee'))
            {
                // L'evaluation n'est pas encore corrigee.
                // Une reference qui n'existe pas pointe egalement vers la page d'une evaluation non-corrigee.
                // Ceci evitera aux etudiants de deviner les numeros de reference.

                $this->data['soumission'] = decompresser_soumissions($soumission);

                $this->_affichage('non-corrigee');
                return;
            }
        }

        //
        // Permettre la consultation des soumissions des etudiants inscrits seulement
        // par l'etudiant lui-meme. L'etudiant doit etre connecte pour pouvoir
        // consulter ses corrections. Attention, les enseignants doivent pouvoir
        // consulter l'evaluation corrigee.
        //

        if ( ! empty($soumission['etudiant_id']))
        {
            if ( ! $this->logged_in)
            {
                $_SESSION['redirect_consultation'] = $soumission['soumission_reference'];

                log_alerte(
                    array(
                        'code'       => 'CNXREQ1',
                        'desc'       => "La connexion est requise pour consulter votre évaluation corrigée.",
                        'importance' => 0,
                        'extra'      => 'soumission_reference = ' . $soumission['soumission_reference']
                    )
                );

                redirect(base_url() . 'erreur/spec/CNXREQ1');
                return;
            }

            elseif ( ! $this->est_enseignant && ($this->etudiant_id != $soumission['etudiant_id']))
            {
                // Les etudiants peuvent consulter les soumissions des autres etudiants, mais pour se faire ils ont besoin
                // de la reference qui n'est pas facile a deviner.

                log_alerte(
                    array(
                        'code'       => 'CNSLDAME',
                        'desc'       => "Un étudiant a consulté l'évaluation corrigée d'un autre étudiant.",
                        'importance' => 1,
                        'extra'      => 'soumission_etudiant_id = ' . $soumission['etudiant_id'] . ', etudiant_id = ' . $this->etudiant_id
                    )
                );
            }
        }

        //
        // Verifier que le nombre maximal de visionnement n'a pas ete atteint.
        // Ceci dans le but d'eviter que l'etudiant partage son evaluation corrigee et que toute la classe la consulte.
        //

        //
        // Limiter le nombre de vues des soumissions corrigees par l'etudiant NON INSCRIT.
        //

        if ( ! $this->logged_in)
        {
            if ($soumission['vues'] >= $this->config->item('corrections_max_vues'))
            {
                // L'evaluation corrigee a ete vue trop de fois (plus que le nombre de fois maximum alloue).

                $this->_affichage('max-vues');
                return;
            }
        }

        //
        // Limiter le nombre de vues des soumissions corrigees par l'etudiant INSCRIT.
        //

        // (!) Pour l'instant, les etudiants inscrits n'ont pas de limite de visionnements.

        //
        // Verifier certains criteres de visualisation
        // avant de permettre la visualisation aux etudiants
        //
        
        if  ( ! $this->est_enseignant)
        {
            //
            // Verifier si l'etudiant a la permission de visualiser ses corrections.
            //

            if ( ! 
                 (
                   $soumission['permettre_visualisation'] && 
                   ($soumission['permettre_visualisation_expiration'] == 0 || $soumission['permettre_visualisation_expiration'] > $this->now_epoch)
                 )
               )
            {
                // L'enseignant n'a pas donne la permission de visualiser les corrections.

                // Le message pretend que la soumission n'a pas encore ete corrigee.
                // Ceci simplement pour ne pas que les etudiants tentent de "deviner" les codes de reference des soumissions.

                $this->data['soumission'] = decompresser_soumissions($soumission);

                $this->_affichage('non-corrigee');
                return;
            }

            //
            // Verifier que le semestre de la soumission est presentement en vigueur.
            //
            // Un etudiant ne peut voir ses corrections que si le semestre est toujours en vigueur.
            // Ceci dans le but d'eviter que les etudiants consultent les soumissions corrigees des autres (anciens) semestres
            // pour s'aider a remplir celle du semestre en vigueur.
            //

            $semestres = $this->Semestre_model->lister_semestres(
                array(
                    'groupe_id'     => $this->groupe_id,
                    'enseignant_id' => $soumission['enseignant_id']
                )
            ); 

            //
            // Verifier si le semestre existe.
            //

            if ( ! array_key_exists($soumission['semestre_id'], $semestres))
            {
				// Cette erreur apparaitra si l'enseignant (ou l'admin) a efface le semestre de la soumission.
				// *** Cette erreur est apparue dans une autre circonstance (circonstance iconnue pour l'instant), sans
				//     que le semestre de la soumission ait ete efface. (2022/03/07)

                generer_erreur('KIX4519', "Le semestre correspondant à cette soumission est inexistant.");
                return;
            }

            //
            // Verifier le semstre de la soumission est celui presentement en vigueur.
            //

            $semestre = $semestres[$soumission['semestre_id']];

            if ($this->now_epoch > $semestre['semestre_fin_epoch'] || $this->now_epoch < $semestre['semestre_debut_epoch'])
            {
                // La soumission a ete redigee pour un semestre qui n'est plus en vigueur.
                // OU moins probable mais possible, l'enseignant a change les dates de debut et de fin du semestre.

                $this->data['soumission'] = decompresser_soumissions($soumission);

                $this->_affichage('ancien-semestre');
                return;
            }
        }

        //
        // Pour les enseignants seulement !
        //
        // Registre de l'activite de l'etudiant pendant son evaluation
        // Le but fondamental est de determiner si qqn d'autre a fait l'evaluation de l'etudiant en se connectant sur son compte.
        //

        elseif ( 
                  is_numeric($soumission['etudiant_id'])                  && 
                  ! empty($soumission['etudiant_id'])                     &&
                  ($this->enseignant['privilege'] > 89 || ($this->enseignant_id == $soumission['enseignant_id']))
               )
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

		//
		// Extraire les documents des etudiants
		//

        $documents = $this->Document_model->extraire_documents_soumission_terminee($soumission['soumission_id']);
        
        // 
        // Verifier si le documents sont uniques (plagiat),
        // seulement pour les enseignants
        //

        $documents_verification = array();

        if ($this->est_enseignant && $this->appartenance_groupe && ! empty($documents))
        {
            $documents_a_verifier   = array();

            foreach($documents as $docs)
            {
                foreach($docs as $d)
                {
                    $documents_a_verifier[$d['doc_id']] = $d['doc_sha256_file'];
                }
            }

            if ( ! empty($documents_a_verifier))
            {
                $documents_verification = $this->Document_model->detecter_documents_identiques($documents_a_verifier);
            }
        }

        //
        // Les permissions ont ete verifiees, alors permettre la visualisation.
        //

        $evaluation_id = $soumission['evaluation_id'];

        $soumission = decompresser_soumissions($soumission);

        $evaluation    = $soumission['evaluation_data'];
        $cours         = $soumission['cours_data']; 
        $images        = $soumission['images_data'];
        $questions     = $soumission['questions_data'];

        //
        // Assigner des numeros aux questions
        //

        $i = 1;
        foreach($questions as &$q)
        {
            $q['question_no'] = $i++;
        }

		//
		// Extraire les grilles de correction
		//

        $gc = ! empty($soumission['grilles_data']) ? unserialize($soumission['grilles_data']) : array();

        // 
        // Determiner les renseignements de l'enseignant de la soumission
        //

        // Avant d'aller faire une requete a la base de donnees pour obtenir les informations de l'enseignant, 
        // verifier que ces donnees ne se trouvent pas deja dans la soumission.
        // source: https://stackoverflow.com/questions/13169588/how-to-check-if-multiple-array-keys-exists

        $keys = array('enseignant_id', 'enseignant_nom', 'enseignant_prenom', 'enseignant_genre');

        if (count(array_intersect_key(array_flip($keys), $cours)) === count($keys)) 
        {
            $enseignant = array(
                'enseignant_id' => $cours['enseignant_id'],
                'nom'           => $cours['enseignant_nom'],
                'prenom'        => $cours['enseignant_prenom'],
                'genre'         => $cours['enseignant_genre']
            );
        }
        else
        {
            // Retrocompatibilite (anterieure a 2019-01-01)
            // Les anciennes soumissions ne possedaient pas les informations de l'enseignant, il faut les extraire.

            $enseignant = $this->Enseignant_model->extraire_enseignant($soumission['enseignant_id']);
        }

		//
		// Verifier si les images sont toujours existantes sur le disque.
		//

		if ( ! empty($images))
        {
			foreach($images as $question_id => $image)
            {
                if ( ! array_key_exists('s3', $image) || ! $image['s3'])
                {
                    //
                    // Creer ce champ manuellement (requis depuis 2020-10-17).
                    //

                    $images[$question_id]['s3'] = 1;

                    //
                    // Verifier si les images existent dans le nuage.
                    //
                    // (!) Ceci ajoute une latence d'environ 100 ms.
                    //     Alors on peut considerer que S3 n'a pas perdu les fichiers.
                    //

                    /*
                    if ( ! $this->Document_model->existe_s3(array('dossier' => 'evaluations', 'key' => $image['doc_filename'])))
                    {
                        unset($images[$question_id]);
                        continue;
                    }
                    */

                    //
                    // Verifier si les images existent sur le disque.
                    // 
                    // (!) Il n'y a plus d'images sur le disque depuis 2020/12/28.
                    //

                    /*
                    if ( ! file_exists($this->config->item('documents_path') . $image['doc_filename']))
                    {
                        unset($images[$question_id]);
                        continue;
                    }	
                    */
                }
			}
        }

		//
		// Extraire les ajustements
		//

        $ajustements = array();

		if ( ! empty($soumission['ajustements_data']))
		{
			$ajustements = unserialize($soumission['ajustements_data']);
        }	

        //
        // Extraire les commentaires
        //
        
        $commentaires = array();

        if (array_key_exists('commentaires_data_gz', $soumission) && $soumission['commentaires_data_gz'] != NULL)
        {
            $commentaires = unserialize(gzuncompress($soumission['commentaires_data_gz']));
        }

        //
        // Verifier si une barre de defilement est necessaire, si oui generer les liens.
        //

        if (
            $this->uri->segment(3) == 'defilement'                          && 
            array_key_exists('consulter_soumission_references', $_SESSION)  && 
            array_key_exists('consulter_defilement_expiration', $_SESSION)
           )
        {

            $soumission_references = unserialize($_SESSION['consulter_soumission_references']); 
            $soumission_expiration = $_SESSION['consulter_defilement_expiration'];

            if ( ! empty($soumission_references) && $soumission_expiration > $this->now_epoch)
            {
                // Determiner l'index actuel

                $i = array_search($reference, $soumission_references);

                $defilement_prec = NULL;
                $defilement_suiv = NULL;

                $prem_key = 0;
                $dern_key = key(array_slice($soumission_references, -1, 1, TRUE));
                
                $defilement_prec_prem = $soumission_references[$prem_key];
                $defilement_suiv_dern = $soumission_references[$dern_key];

                if ($i > 0)
                {
                    $defilement_prec = $soumission_references[$i - 1];
                }

                if ($i < (count($soumission_references) - 1))
                {
                    $defilement_suiv = $soumission_references[$i + 1];
                }

                $this->data['defilement_prec']      = $defilement_prec;
                $this->data['defilement_prec_prem'] = $defilement_prec_prem;
                $this->data['defilement_suiv']      = $defilement_suiv;
                $this->data['defilement_suiv_dern'] = $defilement_suiv_dern;
            }
        }

        //
        // Laboratoire
        //

        $lab_data = array();

        if ($soumission['lab'])
        {
            $lab_data = json_decode($soumission['lab_data'], TRUE);
        }

        //
        // Preparer les donnees pour l'affichage.
        //

        $this->data = array_merge(
            $this->data,
            array(
                'cours'             => $cours,
                'enseignant'        => $enseignant,
                'evaluation'        => $evaluation,
                'questions'         => $questions,
				'images'	        => $images,
                'documents'         => $documents,
                'documents_verification' => $documents_verification,
                'soumission'        => $soumission,
                'ajustements'       => $ajustements,
                'grilles'           => $gc,
                'commentaires'      => $commentaires,
                'lab'               => $soumission['lab'],
                'lab_prefix'        => $lab_data['lab_prefix'] ?? NULL,
				'version_etudiante' => $this->uri->segment(3) == 'etudiant' ? TRUE : FALSE,
                'activite'          => ! empty($activite_pertinente) ? $activite_pertinente : array(),
                'activite2'         => $activite2 ?? array(),
                'fureteurs_desc'    => $fureteurs_desc ?? array(),
                'retour_resultats'  => base_url() . 'resultats/evaluation/' . $soumission['evaluation_id'] . '/semestre/' . $soumission['semestre_id']
            )
        );

        //
        // Laboratoire
        //
        
        if ($soumission['lab'])
        {
            $this->data = array_merge(
                $this->data,
                array(
                    'montre_tags' => FALSE,
                    'montre_corrections' => TRUE
                )
            );
        }

        //
        // Incrementer le nombre de vues
        // Logger la consultation pour les etudiants seulement (et non les enseignants)
        //

        if ( ! $this->est_enseignant)
        {
            $this->Evaluation_model->incrementer_vues_soumission($soumission['soumission_id']); 
            $this->Evaluation_model->log_soumission_consultation(
                array(
                    'soumission_id'        => $soumission['soumission_id'], 
                    'soumission_reference' => $soumission['soumission_reference'], 
                    'etudiant_id'          => $soumission['etudiant_id'],
                    'enseignant_id'        => $soumission['enseignant_id']
                )
            );
        }

		$this->_affichage('voir');
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Changer les points alloues d'une soumission
     *
     * ------------------------------------------------------------------------ */
    public function changer_points_soumission()
    {
        $post_data = $this->input->post();

        if (verifier_ids($post_data, array('soumission_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        foreach($post_data as $k => $v)
		{
			switch($k)
            {
				case 'soumission_reference' :
                case 'points_obtenus' :
				case 'nouveau_points_obtenus' :
				case 'soumission_points' :
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
            echo json_encode($errors);
            return FALSE;
        }

        $post_data['nouveau_points_obtenus'] = str_replace(',', '.', $post_data['nouveau_points_obtenus']);

        if ( ! is_numeric($post_data['nouveau_points_obtenus']))
        {
            echo json_encode(FALSE);
            return;
        }

		$post_data['nouveau_points_obtenus'] = sw_point($post_data['nouveau_points_obtenus']);

		$result = $this->Evaluation_model->ajuster_corrections_soumission(
			$post_data['soumission_id'],
			$post_data['nouveau_points_obtenus'],
			$post_data['soumission_points']
		);

        echo json_encode($result);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Changer les points alloues a une question
     *
     * ------------------------------------------------------------------------ */
    public function changer_points()
    {
        $post_data = $this->input->post();

        if ( ! array_key_exists('soumission_id', $post_data) || empty($post_data['soumission_id']) || ! is_numeric($post_data['soumission_id']))
        {
            echo json_encode(FALSE);
            return;
        }

        foreach($post_data as $k => $v)
		{
			switch($k)
            {
				case 'soumission_reference' :
                case 'points_obtenus' :
				case 'nouveau_points_obtenus' :
                case 'points' :
					$validation_rules = 'required';
					break;
                case 'question_id' :
                case 'tableau_no' :
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
            echo json_encode($errors);
            return FALSE;
        }

        $post_data['nouveau_points_obtenus'] = str_replace(',', '.', $post_data['nouveau_points_obtenus']);

        if ( ! is_numeric($post_data['nouveau_points_obtenus']))
        {
            echo json_encode(FALSE);
            return;
        }

		$post_data['nouveau_points_obtenus'] = sw_point($post_data['nouveau_points_obtenus']);

        //
        // Laboratoire
        //

        if (array_key_exists('tableau_no', $post_data))
        {
            $result = $this->Evaluation_model->ajuster_corrections(
                $post_data['soumission_id'],
                array(
                    'tableau_no'             => $post_data['tableau_no'],
                    'nouveau_points_obtenus' => str_replace(',', '.', $post_data['nouveau_points_obtenus']),
                    'points'                 => str_replace(',', '.', $post_data['points'])
                )
            );
        }

        //
        // Evaluation
        //

        elseif (array_key_exists('question_id', $post_data))
        {
            $result = $this->Evaluation_model->ajuster_corrections(
                $post_data['soumission_id'],
                array(
                    'question_id'            => $post_data['question_id'],
                    'nouveau_points_obtenus' => $post_data['nouveau_points_obtenus'],
                    'points'                 => $post_data['points']
                )
            );
        }

        echo json_encode($result);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Effacer les ajustements d'une question
     *
     * ------------------------------------------------------------------------ */
    public function effacer_ajustement_soumission()
    {
        if (($post_data = catch_post(array('ids' => array('soumission_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

		$result = $this->Evaluation_model->effacer_ajustement_soumission($post_data['soumission_id']);

        echo json_encode($result);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Effacer les ajustements d'une question, ou d'un tableau
     *
     * ------------------------------------------------------------------------ */
    public function effacer_ajustement()
    {
        $post_data = $this->input->post();

        if ( ! array_key_exists('soumission_id', $post_data) || empty($post_data['soumission_id']) || ! is_numeric($post_data['soumission_id']))
        {
            echo json_encode(FALSE);
            return;
        }

        $result = $this->Evaluation_model->effacer_ajustement(
            $post_data['soumission_id'], 
            array(
                'question_id' => $post_data['question_id'] ?? 0,
                'tableau_no'  => $post_data['tableau_no'] ?? 0
            )
        );

        echo json_encode($result);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * Rotation d'une image
     *
     * ------------------------------------------------------------------------ */
	public function rotation_image()
    {
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
				$this->load->view('consulter/corrections_aucun_semestre', $this->data);
				break;

			case 'introuvable' :
				$this->load->view('consulter/evaluation_introuvable', $this->data);
                break;

			case 'non-corrigee' :
				$this->load->view('consulter/corrections_non_corrigee', $this->data);
                break;

            case 'max-vues' :
                $this->load->view('consulter/corrections_max_vues', $this->data);
                break;

            case 'ancien-semestre' :
                $this->load->view('consulter/corrections_ancien_semestre', $this->data);
                break;

			case 'voir' :
                $this->load->view('consulter/consulter', $this->data);
                break;

            case 'en-direct' :
				$this->load->view('evaluation/evaluation', $this->data);
                break;

			case 'www' :
                $this->load->view('consulter/corrections_consulter_www', $this->data);
				break;

			default :
				$this->load->view('consulter/corrections_non_corrigee', $this->data);
				break;
		}

        $this->load->view('commons/footer');
	}
}
