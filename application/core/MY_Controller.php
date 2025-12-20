<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * MAIN CONTROLLER
 *
 * ============================================================================ */

class MY_Controller extends CI_Controller
{
    public function __construct()
	{
        parent::__construct();

        // --------------------------------------------------------------------
        //
        // Forcer le protocol HTTPS.
        //
        // --------------------------------------------------------------------

		$this->config->config['base_url'] = str_replace('http://', 'https://', $this->config->config['base_url']);

		if (array_key_exists('SERVER_PORT', $_SERVER) && $_SERVER['SERVER_PORT'] != 443)
		{
			redirect($this->uri->uri_string());
			exit;
		}
		
        // --------------------------------------------------------------------
        //
        // Benchmarks
        //
        // --------------------------------------------------------------------

        if ($this->config->item('is_DEV'))
        {
            $this->benchmark->mark('code_start');
        }

        // --------------------------------------------------------------------
        //
        // Initialisation
        //
        // --------------------------------------------------------------------

        // Initialisation des variables globales

        $this->is_DEV       = $this->config->item('is_DEV');
        $this->domaine      = $this->config->item('domaine');
        $this->sous_domaine = $this->config->item('sous_domaine');
        $this->now_epoch    = date('U');
        $this->icons_bs     = base_url() . 'assets/icons/bootstrap-icons-1.1.0.svg';

        $this->current_commit     = git_commit_hash_horodatage();
        $this->current_controller = @strtolower($this->uri->segment(1));
        $this->current_method     = @strtolower($this->uri->segment(2));

		// Initialisation des variables pour les tables de la base de donnees.
		//
		// Les tables prendront la forme $this->enseignants_t

        if ( ! empty($this->config->item('database_tables'))) 
        {
            foreach($this->config->item('database_tables') as $clef => $table)
            {
                $this->{$clef . '_t'} = $table;
            }
        }

        // Initialisation du tableau de donnees pour l'affichage

        $this->data = array(
            'current_controller' => $this->current_controller,
            'current_method'     => $this->current_method
        );

        // Initialisation des parametres dynamiques

        $this->Settings_model->initialisation();

		// Initialisation de l'encryption 

        $this->encryption->initialize(
            $this->config->item('encryption_settings')
        );

        // Initialisation du groupe

        if ( ! empty($this->groupe = $this->Auth_model->determination_groupe($this->sous_domaine)))
        {
            $this->groupe_id = $this->groupe['groupe_id'];
        }
        else
        {
            // Ce groupe n'existe pas (ou le sous-domaine demande n'existe pas).
            // Rediriger a la page principale www

            header('Location: ' . $this->config->item('main_url'));
            exit; 
        }

        // Initialisation de l'ecole

        $this->ecole_id = $this->groupe['ecole_id'];
        $this->ecole    = $this->Auth_model->determination_ecole($this->ecole_id);

        // --------------------------------------------------------------------
        //
        // Authentification
        //
        // --------------------------------------------------------------------

        // Initialisation des flags representant le status de l'usager.

        $this->logged_in           = FALSE;
        $this->est_etudiant        = FALSE;
        $this->est_enseignant      = FALSE;
        $this->appartenance_groupe = FALSE;

		//
        // Verifier l'usurpation (DEV seulement)
        //
        // L'usurpation est un outil pour faciliter l'aide aux usagers avec les problemes qu'ils rencontrent.
		// Au lieu de tenter de reproduire un probleme, simplement se connecter avec leur compte (en sautant l'etape d'authentification) 
		// et constater le probleme directement.
		//

		$this->usurp = FALSE; // admin

		// if ($this->is_DEV && ($usurp = get_cookie('udata', TRUE)) !== NULL)
		if (($usurp = get_cookie('udata', TRUE)) !== NULL)
		{
			$this->usurp = unserialize($this->encryption->decrypt($usurp));

 			if ( ! is_array($this->usurp) 				        ||
                 ! array_key_exists('type',       $this->usurp) || 
                 ! array_key_exists('id',         $this->usurp) || 
                 ! array_key_exists('code',       $this->usurp) ||
                 ! array_key_exists('expiration', $this->usurp) ||
                 ! ($this->usurp['type'] == 'etudiant' || $this->usurp['type'] == 'enseignant') ||
                 ! ctype_digit($this->usurp['id'])
               )
            {
				delete_cookie('udata');
				$this->usurp = FALSE;
            }

            // Il est interdit d'usurper enseignant_id == 1
            // @TODO Verifier et interdire l'usurpation de tous les administrateurs.

            if ($this->usurp['type'] == 'enseignant' && $this->usurp['id'] == 1)
            {
				delete_cookie('udata');
				$this->usurp = FALSE;
            }

			// Verifier l'expiration

			if ($this->usurp['expiration'] > $this->now_epoch)
			{
				delete_cookie('udata');
				$this->usurp = FALSE;
			}

			// Verifier la clef d'usurpation

            if ($this->usurp['code'] !== hash('sha256', $this->input->ip_address() . $this->config->item('usurp_code')))
            {
				delete_cookie('udata');
				$this->usurp = FALSE;
            }
		}

        //
        // Authentifier l'usager
        //

        if (($this->usager = $this->Auth_model->connexion_cookie()) != FALSE)
        {
            //
            // SUCCESS de l'authentification
            //
            
            $this->logged_in = TRUE;

            if ($this->usager['type'] == 'etudiant')
            {
                $this->est_etudiant = TRUE;
                $this->etudiant     = $this->usager;
                $this->etudiant_id  = $this->usager['etudiant_id'];

                // Ceci permet de lire etudiant_id du client, en verifiant le hash.
                $this->etudiant_id_hash = hash('sha256', $this->config->item('salt1') . $this->usager['etudiant_id']);
            }

            elseif ($this->usager['type'] = 'enseignant')
            {
                $this->est_enseignant = TRUE;
                $this->enseignant     = $this->usager;
                $this->enseignant_id  = $this->usager['enseignant_id']; // (!) necessaire pour afficher les groupes

                // Ceci permet de lire enseignant_id du client, en verifiant le hash.
                $this->enseignant_id_hash = hash('sha256', $this->config->item('salt1') . $this->usager['enseignant_id']);
            }

            else
            {
                // Au lieu de generer une erreur, rendre l'usager non authentifie?

                generer_erreur('MYC111', "Le type d'usager n'a pu être déterminé.");
                return;
            }
        }
        else
        {
            //
            // ECHEC de l'authentification
            //
                
            // Ceci n'est probablement pas necessaire, mais assurons-nous que les cookies sont bien effaces.
            delete_all_cookies(); 
        }

		// --------------------------------------------------------------------------
        //
		// Groupe
		//
		// --------------------------------------------------------------------------

		//
        // Verifier que ce groupe est actif.
        //

        if ( ! $this->groupe['actif'])
        {
			if ( ! ($this->enseignant && $this->enseignant['privilege'] > 89))
			{
				$_SESSION['erreur_info'] = array(
					'code'    => 'GRP711',
					'message' => 'Le groupe <strong>' . strtolower($this->groupe['sous_domaine']) . '</strong> (groupe_id = 6) a été désactivé temporairement.'
				);

				redirect('https://www.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/erreur/spec/GRP711');
				exit;
			}
        }

        // --------------------------------------------------------------------
        //
        // Maintenance
        //
        // --------------------------------------------------------------------

        if ($this->config->item('maintenance'))
        {
            // Il y a plusieurs messages de maintenance :
            // 0 =
            // 1 =
            // 2 =

            if ($this->uri->segment(1) != NULL)
            {
                redirect(base_url());
                exit;
            }

            $this->output->cache(5);
            $this->load->view('maintenance' . $this->config->item('maintenance'));
            $this->output->_display();
            exit;
        }

        if ($this->config->item('maintenance_admin'))
        {
            // Les administrataeurs doivent s'etre prealablement connecte.
            // Les etudiants peuvent continuer leur evaluation et la soumettre.

            if (
                ! ($this->logged_in && $this->usager['privilege'] > 89)
                &&
                ! ($this->current_controller == 'evaluation' && $this->current_method == 'soumission')
            )
            {
                $this->output->cache(5);
                $this->load->view('maintenance');
                $this->output->_display();
                exit;
            }
        }

        // --------------------------------------------------------------------
        //
        // Semestres
        // 
        // --------------------------------------------------------------------
        //
        // Determination des semestres pour tous (connecte ou non connecte)
        // 
        // --------------------------------------------------------------------

        if ($this->groupe_id != 0)
        {
            // Lister tous les semestres existants

            $this->semestres = $this->Semestre_model->lister_semestres(
                array(
                    'groupe_id' => $this->groupe_id
                )
            );

            $this->semestre    = $this->Semestre_model->semestre_en_vigueur($this->groupe_id);
            $this->semestre_id = $this->semestre['semestre_id'] ?? 0; // Si le semestre_id == 0, c'est qu'il n'y a aucun semestre en vigueur defini pour le moment present. 
        }

        elseif ($this->groupe_id == 0 && $this->est_enseignant)
        {
            // Lister tous les semestres existants

            $this->semestres = $this->Semestre_model->lister_semestres(
                array(
                    'groupe_id'     => $this->groupe_id,
                    'enseignant_id' => $this->enseignant_id
                )
            );

            // Le semestre en vigueur selon la date actuelle
            // (et non celui choisi par l'enseignant car on ne sait pas encore si un enseignant est connecte)
            // Si aucun semestre en vigueur, $this->semestre retourne FALSE et $this->semestre_id retourne NULL.

            $this->semestre    = $this->Semestre_model->semestre_en_vigueur($this->groupe_id, $this->enseignant_id);
            $this->semestre_id = $this->semestre['semestre_id'] ?? 0; // Si le semestre_id == 0, c'est qu'il n'y a aucun semestre en vigueur defini pour le moment present. 
        }

        // --------------------------------------------------------------------
        //
        // Verifier l'appartenance au groupe 
        // Parametrage des comptes
        //
        // --------------------------------------------------------------------
        
        $this->appartenance_groupe = FALSE;
            
        if (
            $this->logged_in 
            && 
            $this->uri->segment(1) != 'erreur' // (!) important : verifier que la page demandee n'est pas une page d'erreur
           )
		{
            //
            // Verifier que l'etudiant ou l'enseignant appartient au groupe actuel
            //

            //
            // Etudiants et Enseignants 
            // Ils appartiennent toujours au groupe_id == 0 (perso).
            //

            if ($this->groupe_id == 0)
            {
                $this->appartenance_groupe = TRUE;
            }

            //
            // Eudiants et Enseignants
            //

            if ($this->groupe_id != 0 || $this->est_enseignant) // Les enseignants doivent extraire les parametres du groupe, meme si ce groupe_id == 0
			{
                if (($groupe = $this->Auth_model->appartenance_groupe($this->groupe_id, $this->usager)) !== FALSE)
				{
                    //
                    // L'etudiant ou l'enseignant appartient au groupe.
                    //

                    $this->appartenance_groupe = TRUE;
                }
                else
				{
                    //
                    // L'etudiant ou l'enseignant n'appartient pas au groupe.
                    //
                       
                    $this->appartenance_groupe = FALSE;

                    //
                    // Controler les access 
                    //

                    $methode = $this->uri->segment(1);

                    //
                    // Les methodes a rediriger dans le groupe personnel
                    // (car l'etudiant ou l'enseignant n'appartient pas a ce groupe)
                    //

                    $methodes_redirections = array('profil');

                    if (in_array($methode, $methodes_redirections))
                    {
                        $url = 'https://www.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/' . $methode;
                        redirect($url);
                        die;
                    }

                    //
                    // Les methodes permises sans appartenance au groupe.
                    //

                    $methodes_valides = array('groupes', 'deconnexion');

                    if ( ! ($methode == NULL || in_array($methode, $methodes_valides)))
                    {
                        $url = 'https://' . $this->sous_domaine . '.' . $this->domaine;
                        redirect($url);
                        exit;
                    }

                } // else

            } // else (est_etudiant && groupe_id == 0)

            //
            // Parametrage des comptes etudiants et enseignants selon les besoins
            //

            // --------------------------------------------------------
            //
            // Etudiant
            //
            // --------------------------------------------------------

            if ($this->est_etudiant)
            {
                $g = array(
                    'groupe_id'   => $this->groupe_id,
                    'semestre_id' => @$this->semestre_id ?: 0,
                );

                if ($this->groupe_id != 0)
                {
					$g['numero_da'] = $this->Etudiant_model->extraire_etudiant_numero_da();
                }

                $this->usager   = $this->usager + $g; 
                $this->etudiant = $this->etudiant + $g;

                $this->data = array_merge(
                    $this->data,
                    array(
                        'etudiant' => $this->usager,
                    )
                );
            } // est_etudiant

            // --------------------------------------------------------
            //
            // Enseignant
            //
            // --------------------------------------------------------

            if ($this->est_enseignant && is_array($groupe))
			{
                $g = array(
                    'groupe_id'   => $this->groupe_id,
                    'niveau'      => $groupe['niveau'],
                    'semestre_id' => array_key_exists($groupe['semestre_id'], $this->semestres) ? $groupe['semestre_id'] : NULL 
                );

                if ($this->groupe_id == 0)
                {
                    $g['niveau'] = 1;
                }

                $this->usager     = $this->usager + $g;
                $this->enseignant = $this->enseignant + $g; 

                $this->data['enseignant'] = $this->usager;

                //
                // Verifier s'il y a des corrections en attente
                //

                $this->data['corrections_en_attente'] = $this->Evaluation_model->corrections_en_attente(
                    array(
                        'enseignant_id' => $this->enseignant['enseignant_id'],
                        'groupe_id'     => $this->groupe_id,
                        'semestre_id'   => $this->enseignant['semestre_id']
                    )
                );

                //
                // Verifier s'il y a des scrutins a voter.
                //

                if ($this->config->item('scrutins') && $this->groupe_id)
                {
                    $this->data['scrutins_a_voter'] = $this->Vote_model->scrutins_a_voter(
                        array(
                            'enseignant_id' => $this->enseignant['enseignant_id']
                        )
                    );
                }

                //
                // Verifier s'il y a de nouveaux messages et commentaires dans les forums
                //

                if ($this->config->item('forums') && $this->groupe_id)
                {   
                    $this->data['forums_nouveaux_messages']     = $this->Forums_model->nouveaux_messages_compte();
                    $this->data['forums_nouveaux_commentaires'] = $this->Forums_model->nouveaux_commentaires_compte();
                }

            } // est_enseignant
            
        } // logged_in && ! erreur

		// --------------------------------------------------------------------
		//
		// Bloquer l'acces au site de developpement aux utilisateurs.
		//
		// --------------------------------------------------------------------

		/*
		if ($this->is_DEV && $this->logged_in)
		{
			$dev_controlleurs_permis = array('connexion', 'deconnexion');
			$dev_enseignants_permis  = array(1, 10);
			$dev_etudiants_permis    = array(1);

			if (
                ($this->est_enseignant && ! in_array($this->enseignant_id, $dev_enseignants_permis))
                    ||
                ($this->est_etudiant && ! in_array($this->etudiant_id, $dev_etudiants_permis))    
			   )
			{            
				if ( ! in_array($this->uri->segment(1), $dev_controlleurs_permis))
				{
					$html = file_get_contents(FCPATH . 'application/views/developpeurs.php');

					echo $html; 
					die;
				}
			}
		}
		*/

        // --------------------------------------------------------------------
        //
        // Enregistrer l'activite de l'usager
        //
        // --------------------------------------------------------------------

        $this->Admin_model->log_activite();

        // --------------------------------------------------------------------
        //
        // Administration > Alertes importantes sur la page d'accueil
        //
        // --------------------------------------------------------------------

        $this->data['admin_alertes'] = FALSE;

        if (
            $this->current_controller == ''       &&
            $this->est_enseignant                 && 
            $this->enseignant['privilege'] >= 90
           )
        {
            $this->data['admin_alertes'] = $this->Admin_model->compter_alertes(
                array(
                    'importance'  => $this->config->item('alertes_importantes'),
                    'apres_epoch' => $this->now_epoch - (60*60*24)
                )
            );
        }

        // --------------------------------------------------------------------
        //
        // Preparer les donnees pour l'affichage
        //
        // --------------------------------------------------------------------

        $this->data = array_merge($this->data, 
            array(
                'is_DEV'      => $this->is_DEV,
                'ecole'       => $this->ecole,
                'ecole_id'    => $this->ecole_id,
                'groupe'      => $this->groupe,
                'groupe_id'   => $this->groupe_id,
                'semestres'   => @$this->semestres ?: array()
            )
        );

        // --------------------------------------------------------------------
        //
		// Kalertes
		//
		// --------------------------------------------------------------------

        // Initialiser les kalertes
        
        $this->data['kalertes'] = array();

        // --------------------------------------------------------------------
        //
		// Donnees FLASH
		//
		// --------------------------------------------------------------------
		//
		// Ces donnees sont affichees une seule fois et disparaissent lorsque
		// la page est rechargee.
        //
        // --------------------------------------------------------------------

        if ($mg = $this->session->flashdata('message_general'))
        {
			$this->data['mg_message'] = $mg['message'];
            $this->data['mg_alert']   = $mg['alert'] ?? 'primary';
        }

        if ($this->is_DEV)
        {
            $this->benchmark->mark('my_controller');
        }
    }
}
