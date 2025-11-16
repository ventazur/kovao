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
 * ADMIN
 *
 * ============================================================================ */

use jlawrence\eos\Parser;
// use chillerlan\QRCode\QRCode; // pour : function voir_vue

class Admin extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant) 
        {
            redirect(base_url());
            exit;
        }

        if ($this->enseignant['privilege'] < 90)
        {
            redirect(base_url());
            exit;
        }

        $this->data['current_controller'] = strtolower(__CLASS__);

        // Pourquoi ? (2020-03-19)
        unset($this->data['semestre_id']);

		$this->load->model('Admin_model');
		$this->load->helper(['general_tests', 'evaluations_tests']);
    }

    /* ------------------------------------------------------------------------
     *
     * Admin > Index
     *
     * ------------------------------------------------------------------------ */
    public function index()
    {
        redirect(base_url() . 'adm/systeme');
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Etudiant
     *
     * ------------------------------------------------------------------------ */
    function etudiant($etudiant_id)
    {
        $etudiant = $this->Etudiant_model->extraire_etudiant($etudiant_id);

        if ($etudiant == FALSE)
        {
            redirect(base_url());
            exit;
        }

        $s_semestres = array(); // semestres
        $s_cours     = array(); // cours
        $cours_ids   = array(); // Tous les cours suivis
        $cours_data  = array(); // Tous les cours suivis

        $soumissions = $this->Soumission_model->extraire_soumissions_etudiant($etudiant_id);

        $evaluations_envoyees = count($soumissions);

        if ( ! empty($soumissions))
        {
            $soumissions = decompresser_soumissions($soumissions);

            //
            // 2. Etablir un tableau cours -> enseignant base sur ces soumissions.
            // 3. Extraire les informaions sur chaque cours
            // 4. Enlever les soumissions qui ne proviennent pas de cet enseignant (confidentialite).
            //

            foreach($soumissions as $s)
            {
                //
                // 2.
                //

                $semestre_id = $s['semestre_id'];

                $cours_id    = $s['cours_id'];
                $cours_ids[] = $s['cours_id'];

                if ( ! array_key_exists($semestre_id, $s_semestres))
                {
                    $s_semestres[$semestre_id] = date_epochize($s['cours_data']['semestre_debut_date']);
                    $s_cours[$semestre_id]     = [];
                }

                $s_cours[$semestre_id][$cours_id] = $s['cours_data'];

                //
                // 3.
                //

                $cours_data[$cours_id] = array(
                    'ecole'  => empty($s['groupe_id']) ? $this->config->item('ecole_www')  : $this->Ecole_model->extraire_ecole(array('groupe_id' => $s['groupe_id'])),
                    'groupe' => empty($s['groupe_id']) ? $this->config->item('groupe_www') : $this->Groupe_model->extraire_groupe(array('groupe_id' => $s['groupe_id']))
                );
            }
        }

        arsort($s_semestres);

        //
        // Extraire les performances
        //

        if ( ! empty($soumissions))
        {
            $this->data['perf'] = $this->Etudiant_model->determiner_performance($soumissions);
        }

        //
        // Extraire les rangs
        //

        $this->data['rangs_semestres_cours'] = $this->Etudiant_model->determiner_rangs_cours_complet($etudiant_id);

        krsort($this->data['rangs_semestres_cours']);

        //
        // Extraire les groupes et les ecoles
        //

        $this->data['etudiant']    = $etudiant;
        $this->data['soumissions'] = $soumissions;
        $this->data['s_semestres'] = $s_semestres;
        $this->data['s_cours']     = $s_cours;
        $this->data['cours_data']  = $cours_data;
        $this->data['activite']    = $this->Etudiant_model->extraire_etudiant_activite($etudiant_id);
        $this->data['etudiant']    = $etudiant;

        $this->data['evaluations_envoyees']  = $evaluations_envoyees;
        // $this->data['derniere_connexion'] = $this->Etudiant_model->derniere_connexion($etudiant_id);
        $this->data['derniere_connexion']    = $this->data['activite'][0] ?? array();

        $this->load->view('commons/header', $this->data);
        $this->load->view('admin/etudiant', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * Enseignant
     *
     * ------------------------------------------------------------------------ */
    function enseignant($enseignant_id)
    {
        $enseignant = $this->Enseignant_model->extraire_enseignant($enseignant_id);

        //
        // Extraire les soumissions
        //

        $soumissions = $this->Enseignant_model->extraire_soumissions($enseignant_id);

        //
        // Extraire les cours donnes
        //
        
        $s_semestres 	   = array(); // semestres
        $s_cours     	   = array(); // cours
        $s_semestres_cours = array();
        $s_soumissions     = array(); // Le nombre de soumissions par semestre/cours
        $s_evaluations     = array(); // Le nombre d'evaluations par semestre/cours
        $cours_data  	   = array(); // Tous les cours suivis

        if ( ! empty($soumissions))
        {
            foreach($soumissions as $s)
            {
                if ( ! array_key_exists($s['semestre_id'], $s_semestres))
                {
                    $s['cours_data'] = json_decode(@gzuncompress($s['cours_data_gz']), TRUE);

                    $s_semestres[$s['semestre_id']] = array(
                        'semestre_nom'        => $s['cours_data']['semestre_nom'],
                        'semestre_code'       => $s['cours_data']['semestre_code'] ?? 'H2018',
                        'semestre_debut_date' => $s['cours_data']['semestre_debut_date'],
                        'semestre_fin_date'   => $s['cours_data']['semestre_fin_date']
                    );

                    $s_semestres_cours[$s['semestre_id']] = array();
                }

                if ( ! array_key_exists($s['cours_id'], $s_cours))
                {
                    if ( ! array_key_exists('cours_data', $s))
                    {
                        $s['cours_data'] = json_decode(@gzuncompress($s['cours_data_gz']), TRUE);
                    }

                    $s_cours[$s['cours_id']] = array(
                      'cours_code' 		=> $s['cours_data']['cours_code'],
                      'cours_code_court'  => $s['cours_data']['cours_code_court'],
                      'cours_nom'         => $s['cours_data']['cours_nom'],
                      'cours_nom_court'   => $s['cours_data']['cours_nom_court']
                    );
                }

				if ( ! in_array($s['cours_id'], $s_semestres_cours[$s['semestre_id']]))
				{
					$s_semestres_cours[$s['semestre_id']][] = $s['cours_id'];
				}

				//
				// Le nombre de soumissions
				//

				$label = $s['semestre_id'] . '_' . $s['cours_id'];

				if ( ! array_key_exists($label, $s_soumissions))
				{
					$s_soumissions[$label] = 0;
					$s_evaluations[$label] = array();
				}
				
				$s_soumissions[$label]++;

				//
				// Le nombre d'evaluations
				//

				if ( ! in_array($s['evaluation_id'], $s_evaluations[$label]))
				{
					$s_evaluations[$label][] = $s['evaluation_id'];
				}
            }
        }

		//
		// Extraire toutes les evaluations crees
		//	
      
        $evaluations = $this->Evaluation_model->lister_evaluations(
          array(
            'enseignant_id' => $enseignant_id,
            'actif'         => NULL
          )
        );

		//
		// Evaluations en redaction
		//	

		$evaluations_redaction = $this->Evaluation_model->extraire_toutes_evaluations_selectionnees(
			array(
				'enseignant_id' => $enseignant_id
			)
		);

		//
		// Extraire tous les etudiants en redaction de ces evaluations
		//

		$etudiants_redaction = array();

		if ( ! empty($evaluations_redaction))
		{
			$min_epoch = min(array_column($evaluations_redaction, 'ajout_epoch'));
			$evaluations_references = array_column($evaluations_redaction, 'evaluation_reference');

			$etudiants_redaction = $this->Etudiant_model->extraire_etudiants_redaction($evaluations_references, $min_epoch);
		}

        $this->data['enseignant'] 		  = $enseignant;
		$this->data['s_cours']			  = $s_cours;
		$this->data['s_semestres']        = $s_semestres;
		$this->data['s_semestres_cours']  = $s_semestres_cours;
		$this->data['s_soumissions']      = $s_soumissions;
        $this->data['s_evaluations']      = $s_evaluations;

        $this->data['evaluations']        = $evaluations;

		$this->data['evaluations_redaction'] = $evaluations_redaction;
		$this->data['etudiants_redaction']   = $etudiants_redaction;

        $this->data['derniere_connexion'] = $this->Enseignant_model->derniere_connexion($enseignant_id);

        $this->load->view('commons/header', $this->data);
        $this->load->view('admin/enseignant', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * Admin > Groupe > Activer
     *
     * ------------------------------------------------------------------------ */
	public function groupe_activer($groupe_id)
	{
		if ( ! ctype_digit($groupe_id))
		{
			redirect(base_url());
			exit;
		}

		$this->Groupe_model->groupe_activer($groupe_id);

		redirect(base_url() . 'admin/systeme/groupes');
		exit;
	}

    /* ------------------------------------------------------------------------
     *
     * Admin > Groupe > Desactiver
     *
     * ------------------------------------------------------------------------ */
	public function groupe_desactiver($groupe_id)
	{
		if ( ! ctype_digit($groupe_id))
		{
			redirect(base_url());
			exit;
		}

		$this->Groupe_model->groupe_desactiver($groupe_id);

		redirect(base_url() . 'admin/systeme/groupes');
		exit;
	}

    /* ------------------------------------------------------------------------
     *
     * Admin > Systeme/Groupe > Alertes
     *
     * ------------------------------------------------------------------------ */
    public function _alertes($args, $groupe_id = NULL)
    {
        // Verifier les arguments : importance

        $importance = $this->config->item('alertes_importance');

        if (array_key_exists('importance', $args) && $args['importance'] > 0 && $args['importance'] < 10)
        {
            $importance = $args['importance'];
        }
    
        // Extraire les alertes selon les arguments

        return $data = array(
            'alertes' => $this->Admin_model->extraire_alertes(
                array(
                    'groupe_id'  => $groupe_id,
                    'limite'     => 100,
                    'importance' => $importance
                )
            ),
            'alertes_importance' => $importance
        );
    }

    /* ------------------------------------------------------------------------
     *
     * Test de messagerie (VERSION 2)
     *
     * ------------------------------------------------------------------------ */
    public function test_messagerie($fournisseur = NULL)
    {
        if ( ! ($this->est_enseignant && $this->enseignant['privilege'] > 99))
        {
			//
			// Vous n'etes pas un administrateur du systeme. Degagez !
			//

            redirect(base_url());
            exit;
        }

        $string = strtolower(random_string('alpha', 4));

        $courriel = 'sebastieng@gmail.com'; 
        $subject  = 'Test de messagerie [' . $string . ']';
        $contenu  = 'admin/test_messagerie';
        // $contenu  = 'inscription/inscription_clefenvoyee_email2';

        $contenu_data = array(
            'string'     => $string,
            'date_human' => date_humanize($this->now_epoch, TRUE)
        );

        $envoi_reussi = FALSE;

        if (
            $this->Courriel_model->envoyer_courriel(
                array(
                    'fournisseur'          => $fournisseur ?? NULL,
                    'destination_courriel' => $courriel, 
                    'sujet'                => $subject, 
                    'contenu'              => $contenu, 
                    'contenu_data'         => $contenu_data,
                    'raison'               => 'test'
                )
            )
        )
        {
            $envoi_reussi = TRUE;
        }

        $this->data['string']       = $string;
        $this->data['date_human']   = $contenu_data['date_human'];
        $this->data['envoi_reussi'] = $envoi_reussi;

        $this->_affichage('test_messagerie');
    }

    /* ------------------------------------------------------------------------
     *
     * Statistiques de messagerie (courriels)
     *
     * ------------------------------------------------------------------------ */
    public function stats_messagerie()
    {
        $stats = $this->Courriel_model->statistiques_fournisseurs();

        p($stats);
    }

    /* ------------------------------------------------------------------------
     *
     * Statistiques de messagerie pour Amazon
     *
     * ------------------------------------------------------------------------ */
    public function stats_messagerie_mailjet()
    {
        $stats = $this->Courriel_model->statistiques_mailjet();

        p($stats);
    }

    /* ------------------------------------------------------------------------
     *
     * Statistiques de messagerie pour Amazon
     *
     * ------------------------------------------------------------------------ */
    public function stats_messagerie_amazon()
    {
        $stats = $this->Courriel_model->statistiques_amazon();

        p($stats);
    }

    /* ------------------------------------------------------------------------
     *
     * Voir courriel : confirmation d'envoi d'une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function voir_courriel_evaluation_confirmation()
    {
        if ( ! ($this->est_enseignant && $this->enseignant['privilege'] > 99))
        {
            redirect(base_url());
            exit;
        }

        $data = array(
            'evaluation' => array(
                'evaluation_titre' => "Labo 1 : Instruments de mesures"
            ),
            'prenom_nom' => 'Sébastien Guillemette',
            'reference'  => 'fjipqlow',
            'empreinte'  => '822123kljjk123213'
        );

        $this->load->view('evaluation/evaluation_confirmation_email', $data);
        return;

    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Ajouter un enseignant
     *
     * ------------------------------------------------------------------------ */
    public function ajouter_enseignant()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('groupe_id')))) === FALSE)
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
                case 'nom' :
                case 'prenom' :
                case 'genre' :
                case 'niveau' :
                case 'courriel' :
                case 'password' :
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

		if ($this->Enseignant_model->ajouter_enseignant($post_data['groupe_id'], $post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * (ajax) Changer un parametre dynamique
     *
     * ------------------------------------------------------------------------ */
    public function changer_parametre()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        $parametres = $this->Admin_model->extraire_parametres_dynamiques();
        $parametres = array_keys_swap($parametres, 'clef');

        $post_data = $this->input->post();

        $clef   = $post_data['clef'];
        $valeur = $post_data['valeur'];

        //
        // Verifier que ce parametre existe et que la nouvelle valeur est difference
        //

        if ( ! array_key_exists($clef, $parametres))
        {
            echo json_encode(FALSE);
            return;
        }

        //
        // Changer la valeur s'il s'agit d'un checkbox (boolean)
        //

        if ($parametres[$clef]['type'] == 'boolean')
        {
            if ($valeur == 'on')
            {
                $valeur = 1;
            }
            elseif ($valeur == 'off')
            {
                $valeur = 0;
            }
            else
            {
                echo json_encode(FALSE);
                return;
            }           
        }

        if ($parametres[$clef]['valeur'] == $valeur)
        {
            echo json_encode(FALSE);
            return;
        }

		if ( ! $this->Admin_model->changer_parametre_dynamique($clef, $valeur))
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }
    /* ------------------------------------------------------------------------
     *
     * Admin > Usurper l'identite d'un etudiant ou d'un enseignant
     *
     * ------------------------------------------------------------------------
     *
     * Cette methode a pour but de faciliter le deboggage.
     *
     * ------------------------------------------------------------------------ */
	public function usurper()
	{
		/*
		if ( ! $this->is_DEV)
		{
			redirect(base_url());
			exit;
		}
		*/

        if ($this->enseignant['privilege'] < 99)
        {
            redirect(base_url());
            exit;
        }

        $type = $this->uri->segment(3);
        $id   = $this->uri->segment(4);

        if ( ! ($type == 'etudiant' || $type == 'enseignant'))
        {
            redirect(base_url());
            exit;
        }

        if ( ! ctype_digit($id))
        {
            redirect(base_url());
            exit;
        }

        //
        // Il est interdit d'usurper ces enseignants.
        //

        $enseignant_ids_interdits = array(1);

        if ($type == 'enseignant' && in_array($id, $enseignant_ids_interdits))
        {
            redirect(base_url());
            exit;
        }

        //
        // Reinitiliser la session
        //

        $_SESSION = array();

        //
        // Creer le cookie
        //

        $usurp = array(
            'type'       => $type,
            'id'         => $id,
            'code'       => hash('sha256', $this->input->ip_address() . $this->config->item('usurp_code')),
			'expiration' => $this->now_epoch + $this->config->item('usur_expiration'),
            'redirect'   => base_url()
        );

		set_cookie('udata', $this->encryption->encrypt(serialize($usurp)), $this->config->item('usurp_expiration'));

        redirect(base_url());
        die;
    }

    /* ------------------------------------------------------------------------
     *
     * Admin > Document
     *
     * ------------------------------------------------------------------------ */
    public function document()
    {
        $args = $this->uri->uri_to_assoc(3);

        if ( ! array_key_exists('id', $args) || ! array_key_exists('type', $args))
        {
            echo 'Les arguments sont mauvais.';
            exit;
        }

        $doc = array();

        $docs     = array();
        $docs_ids = array();

        $docs_questions = array();

        $soumissions = array();

        //
        // Document d'une evaluation
        //

        if ($args['type'] == 'evaluations')
        {
            $this->db->from   ('documents as d, questions as q, evaluations as e');
            $this->db->select ('d.*, e.enseignant_id');
            $this->db->where  ('d.doc_id', $args['id']);
            $this->db->where  ('d.question_id = q.question_id');
            $this->db->where  ('q.evaluation_id = e.evaluation_id');  
            $this->db->limit  (1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                $doc = $query->row_array();
            }

            if ( ! empty($doc))
            {
                //
                // Extraire tous les copies de ce document (ceux avec le meme nom)
                //

                $this->db->from   ('documents as d, questions as q, evaluations as e');
                $this->db->select ('d.*, e.evaluation_id, e.enseignant_id');
                $this->db->where  ('d.doc_filename', $doc['doc_filename']);
                $this->db->where  ('d.question_id = q.question_id');
                $this->db->where  ('q.evaluation_id = e.evaluation_id');  

                $query = $this->db->get();
                
                $docs = $query->result_array();

                $docs_ids = array_column($docs, 'doc_id');

                //
                // Extraire tous les documents relies a ces questions
                //

                $question_ids = array_column($docs, 'question_id');

                $this->db->from     ('documents as d, questions as q, evaluations as e');
                $this->db->select   ('d.*, e.evaluation_id, e.enseignant_id');
                $this->db->where_in ('d.question_id', $question_ids);
                $this->db->where    ('d.question_id = q.question_id');
                $this->db->where    ('q.evaluation_id = e.evaluation_id');  
                
                $query = $this->db->get();

                $docs_questions = $query->result_array();

                //
                // Extraire toutes les soumissions
                //
                
                $this->db->from     ('soumissions as s');
                $this->db->where    ('s.enseignant_id', $doc['enseignant_id']);
                // $this->db->where    ('s.soumission_epoch >=', $doc['ajout_epoch']);

                $query = $this->db->get();
                
                $s_tmp = $query->result_array();

                //
                // Filter les soumissions pertinentes
                //

                if ( ! empty($s_tmp))
                {
                    foreach($s_tmp as $s)
                    {
                        $s['images_data'] = $s['images_data_gz'] ? json_decode(gzuncompress($s['images_data_gz']), TRUE) : NULL;

                        unset($s['evaluation_data_gz'], $s['cours_data_gz'], $s['questions_data_gz'], $s['images_data_gz'], $s['documents_data_gz']);

                        if (empty($s['images_data']))
                            continue;

                        foreach($question_ids as $q_id)
                        {
                            if (array_key_exists($q_id, $s['images_data']))
                            {
                                $soumissions[] = $s;
                                break;
                            }
                        }
                    }
                }
            } // if ! empty($doc)
        }

        /*
            //
            // Extraire les soumissions utilisant ces images
            //

            $question_ids = array_column($docs, 'question_id');
            $evaluations  = array();

            $this->db->from     ('questions as q, evaluations as e');
            $this->db->select   ('q.question_id, e.enseignant_id, e.evaluation_id, q.efface as question_efface, e.efface as evaluation_efface');
            $this->db->where_in ('q.question_id', $question_ids);
            $this->db->where    ('q.evaluation_id = e.evaluation_id');
            $this->db->where    ('q.efface', 0);
            $this->db->where    ('e.efface', 0);
            
            $query = $this->db->get();

            if ($query->num_rows() > 0)
            {
                $evaluations = $query->result_array();
            }
        */

        $this->data['id']               = $args['id'];
        $this->data['doc']              = $doc;
        $this->data['docs']             = $docs;
        $this->data['docs_questions']   = $docs_questions;
        $this->data['question_ids']     = @$question_ids;
        $this->data['type']             = $args['type'];
        $this->data['soumissions']      = $soumissions;

        $this->_affichage('document_inspecteur_evaluations');
    }

    /* ------------------------------------------------------------------------
     *
     * Admin > Affichage 
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

		switch($page)
        {
            /*
            case 'groupe' :
                $this->load->view('admin/groupe', $this->data);
                break;

            case 'systeme' :
                $this->load->view('admin/systeme', $this->data);
                break;
            */

            case 'detecter_documents_superflus' :
                $this->load->view('admin/documents_superflus', $this->data);
                break;

            case 'detecter_documents_superflus_soumissions' :
                $this->load->view('admin/documents_superflus_soumissions', $this->data);
                break;
            
            case 'detecter_documents_manquants_evaluations' :
                $this->load->view('admin/documents_manquants_evaluations', $this->data);
                break;

            case 'detecter_documents_manquants_soumissions' :
                $this->load->view('admin/documents_manquants_soumissions', $this->data);
                break;

            case 'detecter_documents_etudiants_manquants_soumissions' :
                $this->load->view('admin/documents_etudiants_manquants_soumissions', $this->data);
                break;

            case 'document_inspecteur_evaluations' :
                $this->load->view('admin/document_inspecteur_evaluations', $this->data);
                break;

            case 'test_messagerie' :
                $this->load->view('admin/test_messagerie_resultat', $this->data);
                break;

            default:
                $this->load->view('admin/admin', $this->data);
                break;
        }

        $this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * Ceci permet de faciliter l'edition des vues (views) par leur affichage
     * sans passer par le processus normal relie a cette vue.
     *
     * ------------------------------------------------------------------------ */
    function vue()
    {
        if ( ! ($this->est_enseignant && $this->enseignant['privilege'] > 99))
        {
			//
			// Vous n'etes pas un administrateur du systeme. Degagez !
			//

            redirect(base_url());
            exit;
        }

        //
        // Un fonction pour aplatir un tableau
        //

        function flatten(array $array) 
        { 
            $a = array();
            foreach($array as $k => $v)
            {
                $a[] = $k;
                $a[] = $v;
            }
            return $a;
        }

        $args = flatten($this->uri->uri_to_assoc(3));

        // 
        // Preparer le tableau des donnees pour l'affichage
        //

        switch($args[0])
        {
            case 'evaluation' :

                if ($args[1] == 'soumission')
                {
                    $this->data['prenom_nom'] = 'Sébastien Guillemette';
                    $this->data['numero_da']  = '123456789';
                    $this->data['reference']  = 'abcdefgh';
                    $this->data['empreinte']  = '000000000000';
                    $this->data['courriel_envoye'] = 0;
                    $this->data['qr_image']   = NULL;

                    // $this->data['qr_image']   = (new QRCode)->render('abcdefgh;000000000') ?: NULL;
                    
                    $this->load->view('commons/header', $this->data);
                    $this->load->view($args[0] . '/' . $args[1], $this->data);
                    $this->load->view('commons/footer');

                    return;
                }

                if (
                    $args[1] == 'evaluation_confirmation_email'  ||
                    $args[1] == 'evaluation_confirmation_email2' ||
                    $args[1] == 'evaluation_confirmation_email3' ||
                    $args[1] == 'evaluation_confirmation_email4'
                   )
                {
                    $this->data['prenom_nom']  = 'Sébastien Guillemette';
                    $this->data['salutations'] = (date('G') > 5 || date('G') < 18) ? 'Bonjour' : 'Bonsoir';
                    $this->data['evaluation']  = array('evaluation_titre' => 'Examen 1');
                    $this->data['reference']   = 'aaabbbccc';
                    $this->data['empreinte']   = '123aaa123bbbb';
                    
                    $this->load->view($args[0] . '/' . $args[1], $this->data);
                    
                    return;
                }
        }

        $this->load->view('commons/header', $this->data);
        $this->load->view($args[0] . '/' . $args[1], $this->data);
        $this->load->view('commons/footer');
    }

    /* ------------------------------------------------------------------------
     *
     * Voir soumission
     *
     * ------------------------------------------------------------------------ */
    function voir_soumission($soumission_reference = NULL)
    {
        if (empty($soumission_reference))
        {
            echo 'Aucune référence fournie';
            die;
        }

        $this->db->from  ('soumissions');
        $this->db->where ('soumission_reference', $soumission_reference);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            echo 'Aucune soumission trouvée';
            die;
        }

        $s = $query->row_array();

        $s['soumission_data'] = json_decode($s['soumission_data'], TRUE);
        $s['evaluation_data'] = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
        $s['cours_data']      = json_decode(gzuncompress($s['cours_data_gz']), TRUE);
        $s['questions_data']  = json_decode(gzuncompress($s['questions_data_gz']), TRUE);
        $s['images_data']     = $s['images_data_gz'] ? json_decode(gzuncompress($s['images_data_gz']), TRUE) : NULL;
        $s['documents_data']  = $s['documents_data_gz'] ? json_decode(gzuncompress($s['documents_data_gz']), TRUE) : NULL;

        unset($s['evaluation_data_gz'], $s['cours_data_gz'], $s['questions_data_gz'], $s['images_data_gz'], $s['documents_data_gz']);

        p($s);
    }

    /* ------------------------------------------------------------------------
     *
     * Voir traces
     *
     * ------------------------------------------------------------------------ */
    function voir_traces($session_id = NULL)
    {
        if (empty($session_id))
        {
            echo 'Aucune session_id fournie';
            die;
        }

        $this->db->from  ('etudiants_traces');
        $this->db->where ('session_id', $session_id);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            echo 'Aucune traces trouvée';
            die;
        }

        $t = $query->row_array();

        $t['data'] = unserialize($t['data']);

        p($t);
    }

    /* ------------------------------------------------------------------------
     *
     * Calculer le nombre de temps cumulatif passe par les etudiants en redaction
     *
     * ------------------------------------------------------------------------ */
    function voir_cache()
    {
        p($this->kcache->get_metadata());
        p($this->kcache->cache_info());
    }

    /* ------------------------------------------------------------------------
     *
     * Calculer le nombre de temps cumulatif passe par les etudiants en redaction
     *
     * ------------------------------------------------------------------------ */
    function temps_passe_redaction($enseignant_id = NULL, $semestre_id = NULL)
    {
        $temps_passe = $this->Admin_model->temps_passe_en_redaction($enseignant_id, $semestre_id);

        p($temps_passe);
    }

	/* ------------------------------------------------------------------------
     *
     * Detecter les documents (images) superflues
     *
     * ------------------------------------------------------------------------ */
    function detecter_documents_superflus()
    {       
        $this->data = array_merge(
            $this->data,
            $this->Document_model->detecter_documents_superflus()
        );

        $this->_affichage(__FUNCTION__);
    }

	/* ------------------------------------------------------------------------
     *
     * Verifier les documents manquants des soumissions
     *
     * ------------------------------------------------------------------------ */
    function detecter_documents_superflus_soumissions()
    {       
        $this->data['documents_superflus'] = $this->Document_model->detecter_documents_superflus_soumissions();

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier les documents manquants des evaluations
     *
     * ------------------------------------------------------------------------ */
    function detecter_documents_manquants_evaluations()
    {       
        $this->data = array_merge(
            $this->data,
            $this->Document_model->detecter_documents_manquants_evaluations()
        );

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier les documents manquants des soumissions
     *
     * ------------------------------------------------------------------------ */
    function detecter_documents_manquants_soumissions()
    {       
        $this->data = array_merge(
            $this->data,
            $this->Document_model->detecter_documents_manquants_soumissions()
        );

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Verifier les documents ETUDIANTS manquants des soumissions
     *
     * ------------------------------------------------------------------------ */
    function detecter_documents_etudiants_manquants_soumissions()
    {       
        echo '@TODO';
        exit;

        $this->data = array_merge(
            $this->data,
            $this->Document_model->detecter_documents_etudiants_manquants_soumissions()
        );

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Detecter les etudiants relies
     *
     * ------------------------------------------------------------------------ */
    function etudiants_relies()
    {       
        $this->data['etudiants_relies'] = $this->Admin_model->detecter_etudiants_relies();

        $this->load->view('commons/header', $this->data);
        $this->load->view('outils/etudiants_relies', $this->data);
        $this->load->view('commons/footer', $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * Combien d'etudiants se sont connectes chaque jour
     *
     * ------------------------------------------------------------------------ */
    function etudiants_connexions()
    {       
        $this->data['etudiants_connexions'] = $this->Admin_model->stats_etudiants_connexions();

        /*
        $this->load->view('commons/header', $this->data);
        $this->load->view('admin/etudiants_connexions', $this->data);
        $this->load->view('commons/footer', $this->data);
        */
    }

    /* ------------------------------------------------------------------------
     *
     * Tester une fonction
     *
     * ------------------------------------------------------------------------ */
    function tests($m = NULL)
    {
        $this->load->helper('evaluations_tests_helper');

        $methodes_valides = array(
            'cs_test',
            'cs_ajustement_test',
            'nombre_decimales_test',
            'incertitude_ajustement_test',
            'determiner_valeurs_variables_test',
            'corriger_question_numerique_test',
            'corriger_question_type_9_test',
            'corriger_question_litterale_courte3_test',
            'ns_format_test',
            'verifier_tags_test',
            'lab_corriger_methode_extremes_test',
            'format_nombre_test',
            'nsdec_test'
        );
            
        if (in_array($m, $methodes_valides))
        {
            $m();
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Reponses avec types
     *
     * ------------------------------------------------------------------------
     *
     * Ces reponses ne devraient pas avoir de types definies (les types 1, 4, 11). 
     *
     * ------------------------------------------------------------------------ */
    function reponses_avec_types()
    {
        $this->db->select   ('r.reponse_id, r.question_id, r.question_type');
        $this->db->from     ('reponses as r');
        $this->db->where_in ('r.question_type', array(1, 4, 11));
        $this->db->where    ('r.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
            return FALSE;

        $reponse_ids = array();
        $reponses = array();

        foreach ($query->result_array() as $row)
        {
            $reponses[$row['reponse_id']] = $row;
            $reponse_ids[] = $row['reponse_id'];
        }

        p(count($reponse_ids));

        p($reponse_ids);

    }

    /* ------------------------------------------------------------------------
     *
     * Reponses multitypes
     *
     * ------------------------------------------------------------------------ */
    function reponses_multitypes()
    {
        $this->db->select   ('r.*, e.evaluation_id, e.public, q.question_type as question_question_type');
        $this->db->from     ('evaluations as e, questions as q, reponses as r');
        $this->db->where    ('q.evaluation_id = e.evaluation_id');
        $this->db->where    ('q.question_id = r.question_id');
        $this->db->where_in ('q.question_type', array(1, 4, 11));
        $this->db->where    ('r.question_type !=', NULL);
        $this->db->where    ('q.efface', 0);
        $this->db->where    ('r.efface', 0);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;

        $questions = array();

        foreach ($query->result_array() as $row)
        {
            if ( ! array_key_exists($row['question_id'], $questions))
                $questions[$row['question_id']] = array();

            $questions[$row['question_id']][] = $row;
        }

        $questions_trouvees = array(); 
        $question_ids = array();

        $types = array();

        foreach($questions as $question_id => $reponses)
        {
            $types = array();

            foreach($reponses as $r)
            {   
                if ( ! in_array($r['question_type'], array(1, 4, 11)))
                    continue;

                if ( ! in_array($r['question_type'], $types))
                {
                    $types[] = $r['question_type'];
                }
            }

            if (count($types) > 1)
            {
                $question_ids[] = $question_id;
                $questions_trouvees[$question_id] = $reponses;
            }
        }

        p($question_ids);
        p($questions_trouvees);
    }

    /* ------------------------------------------------------------------------
     *
     * Testo
     *
     * ------------------------------------------------------------------------ */
    function _testo()
    {
        /*
         *
         * Tentative pour regler le probleme des changements d'heure.
         *
         */

        $format = 'Y-m-d H:i:s';

        $d = '2023-03-12 02:00:00';

        $date = DateTimeImmutable::createFromFormat($format, $d);
        echo 'Date: ' . $d . ' --- Epoch: ' . $date->format('U') . '<br />';
        echo 'Epoch: ' . date_epochize_plus($d) . '<br />';

        $d = '2023-03-12 03:00:00';

        $date = DateTimeImmutable::createFromFormat($format, $d);
        echo 'Date: ' . $d . ' --- Epoch: ' . $date->format('U') . '<br />';
        echo 'Epoch: ' . date_epochize_plus($d) . '<br />';

        $d = '2023-03-12 04:00:00';

        $date = DateTimeImmutable::createFromFormat($format, $d);
        echo 'Date: ' . $d . ' --- Epoch: ' . $date->format('U') . '<br />';

    }
}
