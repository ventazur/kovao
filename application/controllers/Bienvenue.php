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
 * BIENVENUE
 *
 * ============================================================================ */ 

class Bienvenue extends MY_Controller 
{
	public function __construct()
    {
    	parent::__construct();

        $this->data['current_controller'] = strtolower(__CLASS__);

		$this->load->model('Admin_model');
	}

    /* ------------------------------------------------------------------------
     *
     * Index DISPATCH
     *
     * ------------------------------------------------------------------------ 
     *
     * L'index dispatch l'usager vers la situation qui convient.
     *
     * sous-domaine (vide) :
     *
     *   (vide)         => redirect(www)
     *
     * usager non connecte :
     *
     *   www            => _non_connecte_www
     *   perso          => redirect(www)
     *   sous-domaine   => _non_connecte_sous_domaine
     *
     * usager connecte :
     *
     *  - enseignants
     *
     *    www           => _enseignants_www
     *    perso         => _enseignants_commun
     *    sous-domaine (avec appartenance) => _enseignants_commun
     *    sous-domaine (sans appartenance) => _enseignants_sous_domaine_sans_appartenance
     *
     *  - etudiant
     *    
     *    www           => _etudiants_www ou _page_accueil_www
     *    perso         => redirect(www)
     *    sous-domaine  => _etudiants_commun
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
		// ------------------------------------------------------
        //
        // Le sous-domaine est vide (ex. https://kovao.com).
        //
		// ------------------------------------------------------

        if (empty($this->sous_domaine))
        {
            die('vide');
            redirect('https://www.' . $this->config->item('domaine'));
            exit;
        }

		// ------------------------------------------------------
        //
        // L'usager n'est PAS connecte.
        //
		// ------------------------------------------------------

		if ( ! $this->logged_in)
		{
			redirect(base_url() . 'connexion');
			exit;

			/*
			if ($this->sous_domaine == 'www')
			{
                $this->_non_connecte_www();
				return;
            }

            // L'usager (probablement un enseignant) tente de rejoindre son groupe perso mais il n'est pas encore connecte.
            // Les etudiants n'ont pas de groupe perso.

            if ($this->sous_domaine == 'perso')
            {
                redirect($this->config->item('main_url'));
                return;
            }

            $this->_non_connecte_sous_domaine();
			return;
		 	*/
		}

		// ------------------------------------------------------
        //
        // L'usager EST connecte.
        //
		// ------------------------------------------------------

		$this->data['afficher_nav_groupe'] = TRUE;

		//
		// Enseignants
		// 

		if ($this->est_enseignant)
        {
            if ($this->sous_domaine == 'www')
            {
                $this->_enseignants_www();
                return;
            }

            // Le groupe perso de l'enseignant

            if ($this->sous_domaine == 'perso')
            {
                $this->_enseignants_commun();
                return;
            }

            // L'enseignant n'appartient pas a ce groupe.
            
			if ( ! $this->appartenance_groupe)
			{
				$this->_enseignants_sous_domaine_sans_appartenance();
				return;
			}

            $this->_enseignants_commun();
            return;
		}

		//
		// Etudiants
		//

		if ($this->est_etudiant)
		{
			if ($this->sous_domaine == 'www')
            {
                $this->_page_accueil_www();
				// $this->_etudiants_www(); // 2023-12-11
				return;
			}

            // Les etudiants n'ont pas de groupe perso (pour l'instant).

            if ($this->sous_domaine == 'perso')
            {
                redirect($this->config->item('main_url'));
                return;
            }

            $this->_etudiants_sous_domaine();
            // $this->_non_connecte_sous_domaine(); // connecte ou non-connecte
            return;
		}

        // Cette erreur ne devrait jamais se produire.

		generer_erreur('BN9811', "Il n'a pas été possible de déterminer votre status d'étudiant ou d'enseignant.");
		exit;
	}

    /* ------------------------------------------------------------------------
     *
     * Fonctions du dispatch
     *
     * ----------------------------------------------------------------------- */

    /* ------------------------------------------------------------------------
     *
     * NON CONNECTE : www
     *
     * ------------------------------------------------------------------------ 
     *
     * Afficher la page d'accueil de KOVAO.
     *
     * ------------------------------------------------------------------------ */
    public function _non_connecte_www()
    {
        $this->data['groupes']   = $this->Groupe_model->lister_groupes_tous();
        $this->data['ecole_ids'] = array_column($this->data['groupes'], 'ecole_id');
        $this->data['ecoles']    = $this->Ecole_model->lister_ecoles(
                                        array(
                                            'ecole_ids' => $this->data['ecole_ids'], 
                                            'personnel' => FALSE
                                        )
                                   );

		$this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * NON CONNECTE : sous-domaine
     *
     * ------------------------------------------------------------------------ 
     *
     * Permettre aux etudiants non-connectes de remplir les evaluations 
     * disponibles sur la page d'accueil du groupe possedant un sous-domaine.
     *
     * ------------------------------------------------------------------------ */
    public function _non_connecte_sous_domaine()
    {
		//
		// Extraire les evaluations disponibles pour les etudiants de ce groupe
        //

        $data = $this->_evaluations_disponibles();

        if ( ! empty($data) && is_array($data))
        {
            $this->data = array_merge($this->data, $data);

            $this->_affichage(__FUNCTION__);
        }

        return;
    }

    /* ------------------------------------------------------------------------
     *
     * CONNECTE : enseignants : www
     *
     * ------------------------------------------------------------------------ 
     *
     * La page d'accueil www des enseignants connectes
     *
     * ------------------------------------------------------------------------ */
    public function _enseignants_www()
    {
        // Extraire les groupes de l'enseignant

        $this->data['mes_groupes'] = $this->Groupe_model->lister_groupes2(array('inclure_personnel' => FALSE));
        $this->data['mes_groupes_ids'] = array_keys($this->data['mes_groupes']);
        $this->data['mes_ecoles_ids'] = array_column($this->data['mes_groupes'], 'ecole_id');
        $this->data['mes_ecoles'] = $this->Ecole_model->lister_ecoles(
            array(
                'ecole_ids' => $this->data['mes_ecoles_ids'],
                'personnel' => FALSE
            )
        );

        $ecoles = $this->Ecole_model->lister_ecoles(array('personnel' => FALSE));
        $groupes = $this->Groupe_model->lister_groupes_tous();

        // Enlever mes ecoles de ecoles, et mes groupes de groupes

        if ( ! empty($this->data['mes_ecoles']))
        {
            foreach($ecoles as $ecole_id => $ecole)
            {
                if (array_key_exists($ecole_id, $this->data['mes_ecoles']))
                    unset($ecoles[$ecole_id]);
            }
        }

        if ( ! empty($this->data['mes_groupes']))
        {
            foreach($groupes as $groupe_id => $groupe)
            {
                if (array_key_exists($groupe_id, $this->data['mes_groupes']))
                    unset($groupes[$groupe_id]);
            }
        }

        $this->data['ecoles'] = $ecoles;
        $this->data['groupes'] = $groupes;
        $this->data['groupe_ids'] = array_keys($groupes);

        $this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * CONNECTE : enseignants : perso / sous-domaine (avec appartenance)
     *
     * ------------------------------------------------------------------------ */
    public function _enseignants_commun()
    {
        //
        // Les demandes pour joindre le groupe
        //

       	if ($this->groupe_id != 0 && $this->enseignant['niveau'] >= $this->config->item('niveaux')['admin_groupe'])
        {
			$enseignants_approbation = $this->Groupe_model->lister_enseignants_approbation();
			$enseignants_a_approuver = FALSE;

			if ( ! empty($enseignants_approbation))
			{
				foreach($enseignants_approbation as $key => $ea)
				{
					if ( ! $ea['traitement'])
					{
						$enseignants_a_approuver = TRUE;
						continue;
					}           

					if ($ea['acceptee'] == 1 || ($ea['demande_expiration'] > $this->now_epoch))
					{
						unset($enseignants_approbation[$key]);
					}
				}
			}

			$this->data['enseignants_approbation'] = $enseignants_approbation;
			$this->data['enseignants_a_approuver'] = $enseignants_a_approuver;
		}

        //
        // Extraire le scrutin s'il y en a seulement 1
        //

        if ($this->config->item('scrutins'))
        {
            if (array_key_exists('scrutins_a_voter', $this->data) && $this->data['scrutins_a_voter'] == 1)
            {
                $scrutins = $this->Vote_model->extraire_scrutins_lances_a_voter();

                foreach($scrutins as $s)
                {
                    $this->data['scrutin'] = $s;
                    break;
                }
            }
        }

        //
        // Verifier s'il y a un semestre actif
        //

        if ( ! array_key_exists('semestre_id', $this->enseignant) || empty($this->enseignant['semestre_id']))
        {
            // Il n'y a aucun semestre actif. Veuillez choisir un semestre (Configuration).
            
                $this->data['kalertes'][] = array(
                    'msg' => "Vous n'avez sélectionné aucun semestre. " . ' Veuillez choisir un semestre (<i class="bi bi-arrow-right"></i> ' . 
                                 '<a href="' . site_url() . '/configuration" style="text-decoration: none">Configuration</a>' .
                                 ').'
                );
        }
        elseif (array_key_exists($this->enseignant['semestre_id'], $this->semestres))
        {
            if ($this->semestres[$this->enseignant['semestre_id']]['semestre_debut_epoch'] > date('U'))
            {
                // Le semestre selectionn n'est pas encore actif.

                $this->data['kalertes'][] = array(
                    'msg' => "Le semestre sélectionné débutera le " . date_french_full($this->semestres[$this->enseignant['semestre_id']]['semestre_debut_epoch']) . '.'
                );
            }
            elseif ($this->semestres[$this->enseignant['semestre_id']]['semestre_fin_epoch'] < date('U'))
            {
                // Le semestre courant est termine. Veuillez choisir un semestre (Configuration).

                $this->data['kalertes'][] = array(
                    'msg' => 'Le semestre est terminé.'
                );
            }
        }

        //
        // Mettre a jour l'activite des etudiants
        //

        $dernieres_soumissions = $this->Evaluation_model->dernieres_soumissions(
            $this->enseignant['enseignant_id'], 
            array(
                'limite'      => 20,
                // 'semestre_id' => $this->enseignant['semestre_id'] // 2024-06-29
                'semestre_id' => $this->semestre_id // les dernires soumissions du semestre courant
            )
        );

        if ( ! empty($dernieres_soumissions))
        {
            foreach($dernieres_soumissions as &$s)
            {
                $soumission_id = $s['soumission_id'];
                $s['duree'] = calculer_duree($s['soumission_debut_epoch'], $s['soumission_epoch']);
                $s['epoch'] = $s['soumission_epoch'];
            }
        }

        //
        // Determiner les evaluations pouvant etre remplies par les etudiants de cet enseignant
        //

        $cours_ids = array();
        $cours_raw = array();

        $evaluations_en_vigueur = $this->Enseignant_model->enseignants_evaluations_selectionnees(
            $this->groupe_id, 
            $this->semestre_id, 
            array(
                'enseignant_id' => $this->enseignant_id
            )
        );

        $evaluation_ids = array_keys($evaluations_en_vigueur);

        $evaluations = array();

        //
        // Determiner les etudiants en redaction
        //

        $etudiants_redaction = array();
        $etudiants_inscrits_redaction = array(); // seulement les etudiant_ids

        if ( ! empty($evaluations_en_vigueur))
        {
            $min_epoch = min(array_column($evaluations_en_vigueur, 'ajout_epoch'));

            $evaluations_references = array_column($evaluations_en_vigueur, 'evaluation_reference');

            $etudiants_redaction = $this->Etudiant_model->extraire_etudiants_redaction(
                $evaluations_references, 
                $min_epoch, 
                array('notifications' => TRUE)
            );

            //
            // Determiner le nombre d'etudiants inscrits en redaction
            // 
            // Ceci servira a activer les communications pour chaque evaluation.
            //

            $etudiants_inscrits_redaction = array();
            $lab_etudiant_ids = array();

            foreach($evaluations_en_vigueur as $e)
            {
                if ( ! array_key_exists($e['evaluation_reference'], $etudiants_inscrits_redaction))
                {
                    $etudiants_inscrits_redaction[$e['evaluation_reference']] = 0;
                }        

                foreach($etudiants_redaction as $er)
                {
                    if ($er['evaluation_reference'] != $e['evaluation_reference'])
                        continue;

                    if (empty($er['etudiant_id']))
                        continue;

                    $etudiants_inscrits_redaction[$er['evaluation_reference']]++;
                }
            }
        }

        //
        // Determiner les cours de cet enseignant
        //

        if ( ! empty($evaluation_ids)) 
        {
            $evaluations = $this->Evaluation_model->extraire_evaluations(array('evaluation_ids' => $evaluation_ids));
        }

        if ( ! empty($evaluations))
        {
            foreach($evaluations as $e)
            {
                if ( ! in_array($e['cours_id'], $cours_ids))
                    $cours_ids[] = $e['cours_id'];
            }

            $cours_raw = $this->Cours_model->lister_cours(array('cours_ids' => $cours_ids));
        }

        //
        // Corrections consultees
        //

        $corrections_consultees = $this->Evaluation_model->corrections_consultees(
            array(
                'limite'      => 20,
                'semestre_id' => $this->enseignant['semestre_id']
            )
        );

        //
        // Extraire les cours_groupes
        //

        $cours_groupes = $this->Enseignant_model->extraire_cours_groupes();

        // Preparation pour l'affichage

        $this->data['cours_raw']                 = $cours_raw;
        $this->data['cours_groupes']             = $cours_groupes;
        $this->data['evaluations']               = $evaluations;
        $this->data['evaluations_en_vigueur']    = $evaluations_en_vigueur;
        $this->data['dernieres_soumissions']     = $dernieres_soumissions;
        $this->data['corrections_consultees']    = $corrections_consultees;
        $this->data['resultats_cumules_session'] = $this->Evaluation_model->resultats_cumules_session();
        $this->data['etudiants_redaction']       = $etudiants_redaction;
        $this->data['etudiants_inscrits_redaction'] = $etudiants_inscrits_redaction;

        //
        // Afficher la page
        //

		$this->_affichage(__FUNCTION__, $this->data);
    }

    /* ------------------------------------------------------------------------
     *
     * CONNECTE : enseignants : sous-domaine (sans appartenance)
     *
     * ------------------------------------------------------------------------ */
    public function _enseignants_sous_domaine_sans_appartenance()
    {
        $this->data['demande'] = array(); // On assume aucune demande en cours.

        //
        // Extraire les informations du groupe par rapport a l'enseignant
        // (l'enseignant a peut-etre ete seulement desactive)
        //

        //
        // Extraire les informations de l'enseignant pour ce groupe, si elles existent.
        //

        $enseignant_groupe = $this->Enseignant_model->extraire_enseignant_groupe($this->enseignant_id);

        $this->data['enseignant_groupe'] = $enseignant_groupe;

        if ( ! empty($enseignant_groupe))
        {
            $this->_affichage(__FUNCTION__);
            return;
        }

        //
        // Extraire une demande en cours, si elle existe
        //

        $demande = $this->Groupe_model->extraire_demande_joindre_groupe();

        $this->data['demande'] = $demande;

        if ( ! $this->groupe['inscription_permise'])
        {
            $this->_affichage(__FUNCTION__);
            return;
        }

		//
		// Preparation des parametres du formulaire (form)
		//

        $this->form_validation->set_error_delimiters('<small style="color: crimson">', '</small>');

		//
		// Validation du formulaire (form)
		// 

        $this->data['errors'] = array('code-inscription' => NULL);

        //
        // Verifier les elements $_POST
        //

        $post_data = $this->input->post(NULL, TRUE);

        if ($this->groupe['inscription_code'])
        {
            $this->form_validation->set_rules  ('code-inscription', 'Code inscription', 'required');
            $this->form_validation->set_message('required', 'Ce champ est obligatoire.');

            if (
                array_key_exists('code-inscription', $post_data) && 
                ! empty($post_data['code-inscription']) &&
                $post_data['code-inscription'] != $this->groupe['inscription_code']
               )
            {
                $this->data['errors']['code-erreur']      = TRUE;
				$this->data['errors']['code-inscription'] = 'is-invalid'; // pour bootstrap
            }
        }

       	if ($this->form_validation->run() == FALSE)
        {
			//
			// Il y a des erreurs dans le formulaire *OU* c'est la premiere fois que le formulaire est affiche.
			//

			if ($this->form_validation->error('code-inscription') !== '')
			{
				$this->data['errors']['code-inscription'] = 'is-invalid'; // pour bootstrap
			}
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			// Verification de l'autorisation a se connecter.
			//

            if (
                $this->groupe['inscription_permise'] &&
                 ! empty($post_data['code-inscription']) && 
                 $post_data['code-inscription'] == $this->groupe['inscription_code']
               )
            {
                //
                // Demander a joindre ce groupe
                //

                if ($this->Groupe_model->demande_joindre_groupe() != FALSE)
                {
                    redirect(base_url());
                    exit;
                }
            }

        }

        $this->_affichage(__FUNCTION__);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * CONNECTE : etudiants : www
     *
     * ------------------------------------------------------------------------ */
    public function _page_accueil_www()
    {
        $ecoles = $this->Ecole_model->lister_ecoles(array('personnel' => FALSE));
        $groupes = $this->Groupe_model->lister_groupes_tous();

        $this->data['groupes']   = $groupes;
        $this->data['ecole_ids'] = array_column($groupes, 'ecole_id');
        $this->data['ecoles']    = $this->Ecole_model->lister_ecoles(
                                        array(
                                            'ecole_ids' => $this->data['ecole_ids'], 
                                            'personnel' => FALSE
                                        )
                                   );

		$this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * CONNECTE : etudiants : www
     *
     * ------------------------------------------------------------------------ */
    public function _etudiants_www()
    {
		$this->_affichage(__FUNCTION__);
    }

    /* ------------------------------------------------------------------------
     *
     * Fonctions utilitaires
     *
     * ----------------------------------------------------------------------- */

    /* ------------------------------------------------------------------------
     *
     * Evaluation en cours de redaction
     *
     * ------------------------------------------------------------------------ */
    function _evaluations_en_cours()
    {
        if ( ! $this->est_etudiant)
        {
            return array();
        }

        return $this->Evaluation_model->evaluations_traces();
    }

    /* ------------------------------------------------------------------------
     *
     * Menu deroulant des evaluations disponibles
     *
     * ------------------------------------------------------------------------ */
    public function _evaluations_disponibles()
    {
		//
		// Quel est le semestre en cours ?
		//

		if (empty($this->semestre))
        {
            $this->_affichage('aucun-semestre');
            return;
		}

        $semestre    = $this->semestre;
        $semestre_id = $this->semestre_id;

		//
		// Qui sont les enseignants actifs ?  Ceux avec un semestre, des cours et des evaluations selectionnees.
        //

        $enseignants_actifs = $this->Enseignant_model->enseignants_evaluations_selectionnees(
            $this->groupe_id, 
            $semestre['semestre_id'],
            array(
                'cacher_cachees'  => TRUE,
                'cacher_bloquees' => TRUE,
                'respecter_date'  => TRUE
            )
        );

		//
		// Determiner les cours dont des evaluations ont ete selectionnees
		//

        $cours_ids = array();
        $cours_raw = array();

        if ( ! empty($enseignants_actifs))
        {
            foreach($enseignants_actifs as $ea)
            {
                $cours_id = $ea['cours_id'];

                if ( ! in_array($cours_id, $cours_ids))
                {
                    $cours_ids[] = $cours_id;
                }
            }

            $cours_raw = $this->Cours_model->lister_cours(array('cours_ids' => $cours_ids));

            // Reordonner le cours_ids
            // pour que les cours apparaissent en ordre de code de cours

            if ( ! empty($cours_raw) && ! empty($cours_ids))
            {
                $cours_ids_ordre = array();

                foreach($cours_raw as $cours_id => $c)
                {
                    if (in_array($cours_id, $cours_ids))
                        $cours_ids_ordre[] = $cours_id;
                }

                // Avant de faire le changement, il faut s'assurer que tous les cours sont la.
                if (count($cours_ids) == count($cours_ids_ordre))
                {
                    $cours_ids = $cours_ids_ordre;
                }
            } 
        } // if ! empty($enseignants_actifs)

        //
        // Extraire les evaluations en cours de redaction
        //

        $evaluations_en_cours = $this->_evaluations_en_cours();

        //
        // Preparation pour l'affichage
        //

		return array(	
			'cours_ids'            => $cours_ids,
			'cours_raw'            => $cours_raw,
			'enseignants_actifs'   => $enseignants_actifs,
			'evaluations_en_cours' => $evaluations_en_cours
      	);
	}

    /* ------------------------------------------------------------------------
     *
     * Connecte : Etudiants (sous-domaine)
     *
     * ------------------------------------------------------------------------ */
    public function _etudiants_sous_domaine()
    {
        //
        // Redirection
        //
        // L'etudiant provient d'une connexion ou d'une inscription ou il a tente precedemment de : 
        //
        // - charger une evaluation
        // - consulter une evaluation corrigee
        //
        
        if (array_key_exists('redirect_evaluation', $_SESSION) && $_SESSION['redirect_evaluation'] != NULL)
        {
            $evaluation_reference = $_SESSION['redirect_evaluation'];

            unset($_SESSION['redirect_evaluation']);

            redirect(base_url() . 'evaluation/' . $evaluation_reference);
            exit;
        }

        if (array_key_exists('redirect_consultation', $_SESSION) && $_SESSION['redirect_consultation'] != NULL)
        {
            $soumission_reference = $_SESSION['redirect_consultation'];

            unset($_SESSION['redirect_consultation']);

            redirect(base_url() . 'consulter/' . $soumission_reference);
            exit;
        }

		//
		// Extraire les evaluations disponibles pour les etudiants
		//

        $data = $this->_evaluations_disponibles();

        if ( ! empty($data) && is_array($data))
        {   
            //
            // Un semestre est en vigueur
            //

            $soumissions = $this->Etudiant_model->extraire_soumissions(
                $this->etudiant_id,
                array('semestre_id' => $this->semestre_id)
            );

            //
            // Extraire les cours / groupes dont l'etudiant est autorise
            //

			$compte_autorise = $this->Etudiant_model->verifier_compte_autorise($this->semestre_id, $this->etudiant_id);

            //
            // Extraire l'etudiant des listes d'eleves
            //

            $eleve = array();

            if ( ! empty($this->etudiant['numero_da']))
            {
                $eleve = $this->Cours_model->extraire_eleve($this->etudiant['numero_da']);
            }

            //
            // Suggerer les evaluations pour cet etudiant
            //

			$data['suggestions'] = $this->Etudiant_model->evaluations_a_suggerer();

            //
            // Enlever certaines suggestions
            //
            // 1. Les evaluations qui ne correspondent pas aux filtres
            // 2. Les evaluations deja commencees
            // 3. Les evaluations deja remplies et envoyees
            // 4. Les laboratoires dont son partenaire a deja commence
            //

            $data['suggestions_compte'] = 0;

            if ( ! empty($data['suggestions']))
            {
                foreach($data['suggestions'] as $cours_id => $c) 
                {
                    foreach($c as $evaluation_id => $e)
					{
                        //
                        // Verifier la presence de filtres
                        //
                        
                        if (
                            $e['filtre_enseignant']	 		      ||
                            $e['filtre_enseignant_autorisation']  ||
                            $e['filtre_cours'] 				      ||
                            $e['filtre_cours_autorisation'] 	  ||
                            $e['filtre_groupe'] 				  ||
                            $e['filtre_groupe_autorisation']
                           )
                        {
                            $permis = FALSE;

                            //
                            // Verifier les filtres
                            //

                            if ($e['filtre_enseignant_autorisation'])
							{
                                foreach($compte_autorise as $ca)
                                {
                                    if ($ca['enseignant_id'] == $e['enseignant_id'])
                                    {
                                        $permis = TRUE;
                                        break;
                                    } 
                                }
                            }

                            elseif ($e['filtre_cours_autorisation'])
							{
                                foreach($compte_autorise as $ca)
                                {
                                    if (
                                        $ca['enseignant_id'] == $e['enseignant_id'] &&
                                        $ca['cours_id']      == $e['cours_id']
                                       )
                                    {
                                        $permis = TRUE;
                                        break;
                                    } 
                                }
                            }

                            elseif ($e['filtre_groupe_autorisation'])
							{
                                foreach($compte_autorise as $ca)
								{
                                    if (
                                        $ca['enseignant_id'] == $e['enseignant_id'] &&
                                        $ca['cours_id']      == $e['cours_id']      &&
                                        $ca['cours_groupe']  == $e['filtre_groupe_autorisation']
                                       )
									{
                                        $permis = TRUE;
                                        break;
                                    } 
                                }
                            }

                            elseif ($e['filtre_enseignant'])
                            {
                                foreach($eleve as $ev)
                                {
                                    if ($ev['enseignant_id'] == $e['enseignant_id'])
                                    {
                                        $permis = TRUE;
                                        break;
                                    } 
                                }
                            }

                            elseif ($e['filtre_cours'])
                            {
                                foreach($eleve as $ev)
                                {
                                    if (
                                        $ev['enseignant_id'] == $e['enseignant_id'] &&
                                        $ev['cours_id']      == $e['cours_id']
                                       )
                                    {
                                        $permis = TRUE;
                                        break;
                                    } 
                                }
                            }

                            elseif ($e['filtre_groupe'])
                            {
                                foreach($eleve as $ev)
                                {
                                    if (
                                        $ev['enseignant_id'] == $e['enseignant_id'] &&
                                        $ev['cours_id']      == $e['cours_id']      &&
                                        $ev['cours_groupe']  == $e['filtre_groupe']
                                       )
                                    {
                                        $permis = TRUE;
                                        break;
                                    } 
                                }
                            }

                            //
                            // Enlever l'evaluation des suggestions si elle n'est pas permise
                            //

                            if ( ! $permis)
							{
								unset($data['suggestions'][$cours_id][$evaluation_id]);
								continue;
                            }
						}

                        //
                        // Verifier dans les evaluations en cours (evaluations commencees).
                        //

                        $trouvee = FALSE;

                        if ( ! empty($data['evaluations_en_cours']))
						{
							foreach($data['evaluations_en_cours'] as $eac)
                            {		
                                if ($evaluation_id == $eac['evaluation_id'])
                                {
                                    $trouvee = TRUE;
                                    break;
                                }
                            }	
                        }

                        //
                        // Verifier dans les soumissions (evaluations remplies).
						//

                        if ( ! empty($soumissions) && $trouvee === FALSE)
                        {
                            foreach($soumissions as $s)
                            {		
                                if ($evaluation_id == $s['evaluation_id'])
                                {
                                    $trouvee = TRUE;
                                    break;
                                }
                            }	
                        }

                        //
                        // Verifier que le laboratoire n'a pas deja ete ouvert par son partenaire
                        //

                        if ($e['lab'] && $trouvee === FALSE)
						{
                            $trouvee = $this->Lab_model->lab_deja_ouvert_par_partenaire(
                                $data['suggestions'][$cours_id][$evaluation_id]['evaluation_reference']
							)['status'];
						}

                        //
                        // Est-ce que l'evaluation a ete trouvee ?
                        //

                        if ($trouvee === TRUE)
                        {
                            unset($data['suggestions'][$cours_id][$evaluation_id]);
                        }
                        else
                        {
                            // Ceci afin de tenir compte des evaluations cachees et qui ne se retrouvent pas dans $evaluations_en_cours.
                            // Cette facon a ete changee le 27 mars 2020 et les evaluations cachees ne sont plus affichees
                            // dans le compte de l'etudiant.

                            $data['suggestions_compte'] += 1;
                        }
                    }

                    if (empty($data['suggestions'][$cours_id]))
                    {
                        unset($data['suggestions'][$cours_id]);
                    }

                } // foreach suggestions

            } // empty suggestions

            //
            // Preparation pour l'affichage
            //

            $this->data = array_merge($this->data, $data);

            $this->_affichage(__FUNCTION__);
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Methodes AJAX
     *
     * ------------------------------------------------------------------------ */

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Horloge du serveur
     *
     * ------------------------------------------------------------------------
     *
     * Possiblement desuet (2020-12-25)
     *
     * ------------------------------------------------------------------------ */
    public function horloge_serveur()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Accès direct interdit');
        }

        echo json_encode(date('D M d Y H:i:s O'));
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX)  Communiquer avec les etudiants en redaction
     *
     * ------------------------------------------------------------------------ */
    public function communiquer()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Accès direct interdit');
        }

        if ( ! $this->est_enseignant)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! $this->config->item('ping_etudiant_evaluation'))
        {
            // Si les pings sont desactives, les messages ne peuvent pas fonctionner

            echo json_encode(FALSE);
            return;
        }

        $post_data = $this->input->post(NULL, TRUE);

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        $this->form_validation->set_message('register', "Ce champ est obligatoire.");
        $this->form_validation->set_message('min_length', "Le nombre minimum de caractères est de 10.");
        $this->form_validation->set_message('max_length', "Le nombre maximum de caractères est de 514.");

        foreach($post_data as $k => $v)
        {
			switch($k)
			{
				case 'evaluation_reference' :
					$validation_rules = 'required';
                    break;

                case 'message' :
                    $validation_rules = 'trim|required|min_length[10]|max_length[512]';
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

        $etudiant_id = (array_key_exists('etudiant_id', $post_data) && ! empty($post_data['etudiant_id'])) ? $post_data['etudiant_id'] : NULL;

        $r = $this->Evaluation_model->communiquer_etudiants($post_data['evaluation_reference'], $post_data['message'], $etudiant_id);

        if ($r < 1)
        {
            echo json_encode(
                array(
                    'message' => "Aucun étudiant inscrit en rédaction"
                )
            );
            return;
        }

        echo json_encode(TRUE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Choisir un cours (repondre par les enseignants qui donnent ce cours)
     *
     * ------------------------------------------------------------------------ */
    public function choisir_cours()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Accès direct interdit');
        }

        if (($post_data = catch_post(array('ids' => array('cours_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		//
		// Determiner les enseignants qui donnent ce cours
		//

        $enseignants = $this->Enseignant_model->enseignants_evaluations_selectionnees(
            $this->groupe_id, 
            $this->semestre_id, 
            array(
                'cours_id'        => $post_data['cours_id'],
                'cacher_cachees'  => TRUE,
                'cacher_bloquees' => TRUE,
                'respecter_date'  => TRUE
            )
        );

        //
        // Dedoublonner le tableau
        //
       
        $enseignants = array_keys_swap($enseignants, 'enseignant_id');

        //
        // Reinitialiser les index pour que javascript utilise l'ordre presente, et non l'ordre de l'index.
        // (Ceci pour contrer une difference d'affichage entre les tableaux sous PHP et Javascript.)
        //

        $enseignants = array_values($enseignants);

		echo json_encode($enseignants);
		return;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Choisir un enseignant (repondre par les evaluations)
     *
     * ------------------------------------------------------------------------ */
    public function choisir_enseignant()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Accès direct interditt');
        }

        if (($post_data = catch_post(array('ids' => array('semestre_id', 'cours_id', 'enseignant_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
		
		//
		// Determiner les evaluations donnees par cet enseignant pour ce cours
        //

        $evaluations = $this->Evaluation_model->lister_evaluations_selectionnees(
            $post_data['enseignant_id'],
            $post_data['semestre_id'],
            $post_data['cours_id'],
            FALSE, // id_pour_index?
            array(
                'cacher_cachees'  => TRUE, 
                'cacher_bloquees' => TRUE,
                'respecter_date'  => TRUE,
                'en_redaction'    => TRUE
            ) 
        );

		echo json_encode($evaluations);
		return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Aller vers evaluation
     *
     * ------------------------------------------------------------------------ */
    public function aller_vers_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('Accès direct interdit');
        }

        if (($post_data = catch_post()) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ( ! ctype_alpha($post_data['ref']) || strlen($post_data['ref']) != 6)
        {
            echo json_encode(FALSE);
            return;
        }
        
        $ref_details = $this->Evaluation_model->verifier_evaluation_reference($post_data['ref']);

        if ($ref_details == FALSE || empty($ref_details) || ! is_array($ref_details) || ! array_key_exists('sous_domaine', $ref_details))
        {
            echo json_encode(FALSE);
            return;
        }

        // $url = 'https://' . $ref_details['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/evaluation/' . $post_data['ref']; // -(2024-06-26)
        $url = 'https://' . $ref_details['sous_domaine'] . '.' . $this->domaine . '/evaluation/' . $post_data['ref'];

        echo json_encode($url);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Etudiants > Effacer les traces d'une evaluation en redaction
     *
     * ------------------------------------------------------------------------ */
    public function effacer_traces_redaction()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit("Aucun direct interdit");
        }

        if ( ! $this->est_etudiant)
        {
            exit("Vous n'êtes pas un étudiant.");
        }

        $post_data = catch_post();

        if ( 
            ! array_key_exists('evaluation_reference', $post_data)  ||
              strlen($post_data['evaluation_reference']) != 6       ||
            ! ctype_alpha($post_data['evaluation_reference'])
           )
        {
            echo json_encode(FALSE);
            return;
        }

        $result = $this->Evaluation_model->effacer_traces_redaction($post_data['evaluation_reference']);

        echo json_encode($result);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enseignants > Cacher une evaluation (toggle)
     *
     * ------------------------------------------------------------------------ */
    public function cacher_evaluation_toggle()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit("Aucun direct inderdit");
        }

        if ( ! $this->est_enseignant)
        {
            exit("Vous n'êtes pas un enseignant.");
        }

        $post_data = catch_post();

        if ( ! array_key_exists('reference', $post_data))
        {
            echo json_encode(FALSE);
            return;
        }

        $result = $this->Enseignant_model->cacher_evaluation_toggle($post_data['reference']);

        echo json_encode($result);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enseignants > Filtres
     *
     * ------------------------------------------------------------------------ */
    public function parametres_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit("Aucun direct inderdit");
        }

        if ( ! $this->est_enseignant)
        {
            exit("Vous n'êtes pas un enseignant.");
        }

        $post_data = $this->input->post(NULL, TRUE);

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
        
        if (empty($post_data['evaluation_reference']))
        {
            echo json_encode(FALSE);
            return;
        }

        if ($this->Evaluation_model->parametres_evaluation($post_data) === TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enseignants > Filtres
     *
     * ------------------------------------------------------------------------ */
    public function filtres_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit("Aucun direct inderdit");
        }

        if ( ! $this->est_enseignant)
        {
            exit("Vous n'êtes pas un enseignant.");
        }

        if (($post_data = catch_post(array('ids' => array('cours_id', 'evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($this->Evaluation_model->filtrer_evaluation($post_data) === TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enseignants > Effacer les filtes d'une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function effacer_filtres_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit("Aucun direct interdit");
        }

        if ( ! $this->est_enseignant)
        {
            exit("Vous n'êtes pas un enseignant.");
        }

        if (($post_data = catch_post(array('ids' => array('cours_id', 'evaluation_id')))) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }

        if ($this->Evaluation_model->effacer_filtres_evaluation($post_data) === TRUE)
        {
            echo json_encode(TRUE);
            return;
        }

        echo json_encode(FALSE);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Enseignaants > Planifier une évaluation
     *
     * ------------------------------------------------------------------------ */
    public function planifier_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        //
        // Extraire et valider les entrees
        //

        $post_data = $this->input->post(NULL, TRUE);

        if (verifier_ids($post_data, array('evaluation_id')) === FALSE)
        {
            echo json_encode(FALSE);
            return;
        }
        
        foreach($post_data as $k => $v)
        {
			switch($k)
			{
				case 'evaluation_reference' :
					$validation_rules = 'required';
                    break;

                case 'temps_limite' :
                    $valudation_rules = 'numeric';
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

        $exclam = '<i class="fa fa-exclamation-circle"></i> ';

        //
        // Verifier qu'un semestre est en vigueur.
        //

        if (empty($this->semestre) || $this->semestre == FALSE)
        {
            echo json_encode(array('erreur' => $exclam . "Il n'y a aucun semestre en vigueur."));
            return;
        }

        //
        // Verifier la validite des dates
        //
        // - Les champs presents dans le tableau de retour generent les erreurs.
        //

        if ( ! empty($post_data['debut_date']))
        {
            if ( ! $this->_date_valide($post_data['debut_date']))
            {
                echo json_encode(array('debut_date' => "La date est invalide."));
                return;
            }  
        }

        if ( ! empty($post_data['fin_date']))
        {
            if ( ! $this->_date_valide($post_data['fin_date']))
            {
                echo json_encode(array('fin_date' => "La date est invalide."));
                return;
            }  
        }

        //
        // Verifier la validite des heures
        //

        if ( ! empty($post_data['debut_heure']))
        {
            if ( ! $this->_heure_valide($post_data['debut_heure']))
            {
                echo json_encode(
                    array(
                        'debut_heure' => "L'heure est invalide.",
                        'erreur'      => $exclam . "L'heure est invalide."
                    )
                );
                return;
            }  
        }

        if ( ! empty($post_data['fin_heure']))
        {
            if ( ! $this->_heure_valide($post_data['fin_heure']))
            {
                echo json_encode(array('fin_heure' => "La heure est invalide."));
                return;
            }  
        }

        //
        // Verifier que les moments du debut et de la fin sont dans le futur.
        //
 
        $debut_date  = NULL;
        $debut_epoch = NULL;

        if ( ! empty($post_data['debut_date']) && ! empty($post_data['debut_heure']))
        {
            $debut_date  = $post_data['debut_date'] . ' ' . $post_data['debut_heure'] . ':00';
            $debut_epoch = date_epochize_plus($debut_date);

            if ($debut_epoch <= $this->now_epoch)
            {
                echo json_encode(
                    array(
                        'debut_date' => TRUE,
                        'erreur-debut-passe' => "La date du début de l'évaluation doit être dans le futur."
                    )
                );
                return;
            }

            if ($debut_epoch < $this->semestre['semestre_debut_epoch'])
            {
                echo json_encode(
                    array(
                        'erreur-debut' => "La date du début de l'évaluation doit être après celle du début du semestre."
                    )
                );
                return;
            }
        }

        $fin_date  = NULL;
        $fin_epoch = NULL;

        if ( ! empty($post_data['fin_date']) && ! empty($post_data['fin_heure']))
        {
            $fin_date  = $post_data['fin_date'] . ' ' . $post_data['fin_heure'] . ':00';
            $fin_epoch = date_epochize_plus($fin_date);

            if ($fin_epoch <= $this->now_epoch)
            {
                echo json_encode(
                    array(
                        'fin_date' => TRUE,
                        'erreur'   => "La date de fin de l'évaluation doit être dans le futur."
                    )
                );
                return;
            }

            if ($fin_epoch > $this->semestre['semestre_fin_epoch'])
            {
                echo json_encode(
                    array(
                        'erreur-fin' => "La date de fin de l'évaluation doit être avant celle de la fin du semestre."
                    )
                );
                return;
            }
        }

        //
        // Verifier que debut est avant fin
        //
    
        if  ( ! empty($debut_epoch) && ! empty($fin_epoch))
        {
            if ($debut_epoch >= $fin_epoch)
            {
                echo json_encode(
                    array(
                        'debut_date' => TRUE,
                        'fin_date'   => TRUE,
                        'erreur'     => "Le début de l'évaluation doit précéder sa fin.",
                    )
                ); 
                return;
            }
        }

        //
        // Verifier que le temps limite est superieur a 0
        //

        if ( ! empty($post_data['temps_limite']))
        {
            // @TODO
        }

		//
		// Planifier 
		//

		$r = $this->Evaluation_model->planifier_evaluation(
            array(
                'evaluation_id'        => $post_data['evaluation_id'],
                'evaluation_reference' => $post_data['evaluation_reference'],
                'debut_epoch'          => $debut_epoch,
                'fin_epoch'            => $fin_epoch,
                'temps_limite'         => $post_data['temps_limite']
            )
        );

        echo json_encode($r);
        return;
    }

    /* ------------------------------------------------------------------------
     *
     * (AJAX) Effacer la planification d'une evaluation
     *
     * ------------------------------------------------------------------------ */
    public function effacer_planification_evaluation()
    {
        if ( ! $this->input->is_ajax_request()) 
        {
            exit('No direct script access allowed');
        }

        if (($post_data = catch_post(array('ids' => array('evaluation_id')))) === FALSE)
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
				case 'evaluation_reference' :
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
		// Effacer la planification
		//

		if ($this->Evaluation_model->effacer_planification_evaluation($post_data) !== TRUE)
		{
			echo json_encode(FALSE);
			return;
		}

		echo json_encode(TRUE);
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Callbacks de la valiation des formes
     *
     * ------------------------------------------------------------------------ */

    function _date_valide($date)
    {
        $this->form_validation->set_message('_date_valide', 'La date est invalide.');

        preg_match('/(.*)-(.*)-(.*)/', $date, $matches);
        
        if (count($matches) != 4)
            return FALSE;

        $a = (int) $matches[1];
        $m = (int) $matches[2];
        $j = (int) $matches[3];

        if ($a < 2000 || $a > 2099)
            return FALSE;

        if ($m < 1 || $m > 12)
            return FALSE;

        if ($j < 1 || $j > 31)
            return FALSE;

        return TRUE;
    }

    function _heure_valide($heure)
    {
        $this->form_validation->set_message('_heure_valide', 'L\'heure est invalide.');
        
        preg_match('/(.*):(.*)/', $heure, $matches);

        if (count($matches) != 3)
            return FALSE;

        $h = (int) $matches[1];
        $m = (int) $matches[2];

        if ($h < 0 || $h > 23)
            return FALSE;

        if ($m < 0 || $m > 59)
            return FALSE;

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
	{
        $this->load->view('commons/header', $this->data);

		switch($page)
        {
            case '_page_accueil_www' :
            case '_non_connecte_www' :
				$this->load->view('bienvenue/bienvenue_www', $this->data);
                break;

			case '_non_connecte_sous_domaine' :
				$this->load->view('bienvenue/bienvenue_sous_domaine', $this->data);
                break;

            case '_enseignants_www' :
                $this->load->view('bienvenue/bienvenue_enseignants_www', $this->data);
                break;

			case '_enseignants_commun' :
				$this->load->view('bienvenue/bienvenue_enseignants_commun', $this->data);
                break;

            case '_enseignants_sous_domaine_sans_appartenance' :
				$this->load->view('bienvenue/bienvenue_enseignants_sous_domaine_sans_appartenance', $this->data);
                break;

			case '_etudiants_www' :
				$this->load->view('bienvenue/bienvenue_etudiants_www', $this->data);
                break;

			case '_etudiants_sous_domaine' :
				// $this->load->view('bienvenue/bienvenue_etudiants_sous_domaine', $this->data);
                $this->load->view('bienvenue/bienvenue_etudiants_sous_domaine_avec_menu_deroulant', $this->data);
                break;

			case 'aucun-semestre' :
				$this->load->view('bienvenue/bienvenue_aucun_semestre', $this->data);
				break;

			default :
				$this->load->view('bienvenue/bienvenue', $this->data);
				break;
		}

        $this->load->view('commons/footer');
    }
}
