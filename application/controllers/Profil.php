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
 * PROFIL
 *
 * ============================================================================ */

class Profil extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->logged_in)
        {
            redirect(base_url());
            exit;
        }

		//
		// Configuration de la validation
		//

        $this->form_validation->set_message('required', 'Ce champ est obligatoire.');
        $this->form_validation->set_message('min_length', 'Ce champ ne comporte pas assez de caractères.');
        $this->form_validation->set_message('max_length', 'Ce champ ne comporte pas assez de caractères.');
        $this->form_validation->set_message('alpha_numeric', 'Ce champ doit comporter seulement des lettres et/ou des chiffres.');
        $this->form_validation->set_message('numeric', 'Ce champ doit comporter seulement des chiffres.');
        $this->form_validation->set_message('matches', 'Le mot-de-passe et sa confirmation ne concordent pas.');


        $this->data['current_controller'] = strtolower(__CLASS__);
    }

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
	public function _remap()
    {
        //
        // Etudiant
        //

        if ($this->est_etudiant)
        {
            $methode = $this->uri->rsegment(2);

            // La methode par defaut
            if ($methode == 'index')
            {
                $methode = 'identite';
            }

            $methodes_valides = array('compte', 'identite', 'motdepasse', 'parametres');

            if ( ! in_array($methode, $methodes_valides))
            {
                redirect(base_url());
                exit;
            }

            $this->data['methode'] = $methode;

            switch($methode)
            {
                case 'compte' :
                    $this->_profil_etudiant_compte();
					break;

                 case 'identite' :
                    $this->_profil_etudiant_identite();
                    break;

                case 'motdepasse' :
                    $this->_profil_etudiant_motdepasse();
                    break;

                case 'parametres' :
                    $this->_profil_etudiant_parametres();
					break;
           }

            $this->_affichage('etudiant');

            return;
        }

        //
        // Enseignant
        //

        elseif ($this->est_enseignant)
        {
            $methode = $this->uri->rsegment(2);

            // La methode par defaut
            if ($methode == 'index')
            {
                $methode = 'identite';
            }

            $methodes_valides = array('identite', 'motdepasse', 'parametres');

            if ( ! in_array($methode, $methodes_valides))
            {
                redirect(base_url());
                exit;
            }

            $this->data['methode'] = $methode;

            switch($methode)
            {
                case 'identite' :
                    $this->_profil_enseignant_identite();
                    break;

                case 'motdepasse' :
                    $this->_profil_enseignant_motdepasse();
                    break;

                case 'parametres' :
                    $this->_profil_enseignant_parametres();
                    break;
            }

            $this->_affichage('enseignant');

            return;
        }

        redirect(base_url());
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
        redirect(base_url());
        exit;
    }

    /* ------------------------------------------------------------------------
     *
     * Etudiant > Identite
     *
     * ------------------------------------------------------------------------ */
	public function _profil_etudiant_identite()
    {
        //
        // Extraire le profil de l'etudiant
        //

        $etudiant = $this->Etudiant_model->extraire_profil_etudiant($this->etudiant_id);

		$post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

        //
        // Definition des messages d'erreur
        //

		$this->form_validation->set_rules('nom', 'Nom', 'trim|required|min_length[2]|max_length[75]');
        $this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('numero_da', $this->ecole['numero_da_nom'] ?: 'Matricule', 'trim|numeric|min_length[3]');

		//
		// Validation du formulaire (form)
		// 

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
            //

            $this->data['etudiant'] = $etudiant;
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            if ($post_data['etudiant_id'] != $etudiant['etudiant_id'])
            {
                generer_erreur('SP45123a', "L'etudiant ne peut modifier que son profil.");
                die;
            }

            $result = $this->Etudiant_model->modifier_profil_identite($etudiant['etudiant_id'], $post_data);

            //
            // Retourner un message selon le resultat de l'operation
            //

            if ($result === TRUE)
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => 'Votre profil a été changé.',
                        'alert'   => 'success'
                    )
                );

                redirect(current_url());
                exit;
            }

            elseif ($result == 'aucun')
            {
                $this->session->set_flashdata('flash_message', 
                    array(
                        'message' => 'Aucun changement détecté',
                        'alert'   => 'primary'
                    )
                );

                redirect(current_url());
                exit;
            }

            else
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => "Une erreur s'est produite.",
                        'alert'   => 'danger'
                    )
                );

                redirect(current_url());
                exit;
            }
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Etudiant > Mot-de-passe
     *
     * ------------------------------------------------------------------------ */
	public function _profil_etudiant_motdepasse()
    {
        $etudiant = $this->Etudiant_model->extraire_profil_etudiant($this->etudiant_id);

		$post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

        //
        // Definition des messages d'erreur
        //

        $this->form_validation->set_rules('password0', 'Mot de passe', 'required|min_length[8]|alpha_numeric');
        $this->form_validation->set_rules('password1', 'Mot de passe', 'required|min_length[8]|max_length[50]|alpha_numeric');
        $this->form_validation->set_rules('password2', 'Confirmation', 'required|matches[password1]');

		//
		// Validation du formulaire (form)
		// 

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
			//
            
            $this->data['etudiant'] = $etudiant;
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            if ($post_data['etudiant_id'] != $etudiant['etudiant_id'])
            {
                generer_erreur('SP45123b', "L'etudiant ne peut modifier que son profil.");
                die;
            }

            $result = $this->Etudiant_model->modifier_profil_motdepasse($etudiant['etudiant_id'], $post_data);

            //
            // Retourner un message selon le resultat de l'operation
            //

            if ($result === TRUE)
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => 'Votre mot-de-passe a été changé.',
                        'alert'   => 'success'
                    )
                );

                redirect(current_url());
                exit;
            }

			elseif ($result == 'mauvais_motdepasse')
			{
				$this->session->set_flashdata('flash_message',
					array(
						'message' => 'Votre mot-de-passe actuel est incorrect.',
						'alert'   => 'danger'
					)
				);
			
				redirect(current_url());
				exit;
			}

			elseif ($result == 'mauvaise_confirmation')
			{
				$this->session->set_flashdata('flash_message',
					array(
						'message' => 'Le mot-de-passe et sa confirmation ne correspondent pas.',
						'alert'   => 'danger'
					)
				);
			
				redirect(current_url());
				exit;
			}

            else
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => "Une erreur s'est produite.",
                        'alert'   => 'danger'
                    )
                );

                redirect(current_url());
                exit;
            }

        }
    }

    /* ------------------------------------------------------------------------
     *
     * Profil de l'etudiant > Parametres
     *
     * ------------------------------------------------------------------------ */
	public function _profil_etudiant_parametres()
    {
        //
        // Extraire le profil de l'etudiant
        //

        //
        // (!) Lorsqu'on ajoute un nouveau parametre, il faut modifier le select a 3 endroits :
        //
        // - Enseignant_model > extraire_profil_enseignant
        // - Enseignant_model > extraire_enseignant
        // - Auth_model       > connexion_cookie 
        //

        $etudiant = $this->Etudiant_model->extraire_profil_etudiant($this->etudiant_id);
        
        //
		// Extraire les donnees du _POST
		// 
        // (!) Les checkbox non selectionnees n'apparaissent pas dans le $_POST.
        //

        $post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

		//
		// Validation du formulaire (form)
		// 

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

       	if (empty($post_data))
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
            //

            $this->data['etudiant'] = $etudiant;

        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
            //
           
            if ($post_data['etudiant_id'] != $etudiant['etudiant_id'])
            {
                generer_erreur('SP45123c', "L'etudiant ne peut modifier que son profil.");
                die;
            }

            unset($post_data['etudiant_id']);

            $result = $this->Etudiant_model->modifier_profil_parametres($etudiant['etudiant_id'], $post_data);

            //
            // Retourner un message selon le resultat de l'operation
            //

            if ($result === TRUE)
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => 'Votre profil a été changé.',
                        'alert'   => 'success'
                    )
                );

                redirect(current_url());
                exit;
            }

            elseif ($result == 'aucun')
            {
                $this->session->set_flashdata('flash_message', 
                    array(
                        'message' => 'Aucun changement détecté',
                        'alert'   => 'primary'
                    )
                );

                redirect(current_url());
                exit;
            }

            else
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => "Une erreur s'est produite.",
                        'alert'   => 'danger'
                    )
                );

                redirect(current_url());
                exit;
            }
        }

        $this->data['etudiant'] = $etudiant;
    }


    /* ------------------------------------------------------------------------
     *
     * Profil de l'enseignant > Identite
     *
     * ------------------------------------------------------------------------ */
	public function _profil_enseignant_identite()
    {
        //
        // Extraire le profil de l'enseignant
        //

        $enseignant = $this->Enseignant_model->extraire_profil_enseignant($this->enseignant_id);

		$post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

        //
        // Definition des messages d'erreur
        //

		$this->form_validation->set_rules('nom', 'Nom', 'trim|required|min_length[2]|max_length[75]');
		$this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|min_length[2]|max_length[50]');

		//
		// Validation du formulaire (form)
		// 

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
            //

            $this->data['enseignant'] = $enseignant;

        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            if ($post_data['enseignant_id'] != $enseignant['enseignant_id'])
            {
                generer_erreur('SP45123d', "L'enseignant ne peut modifier que son profil.");
                die;
            }

            $result = $this->Enseignant_model->modifier_profil_identite($enseignant['enseignant_id'], $post_data);

            //
            // Retourner un message selon le resultat de l'operation
            //

            if ($result === TRUE)
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => 'Votre profil a été changé.',
                        'alert'   => 'success'
                    )
                );

                redirect(current_url());
                exit;
            }

            elseif ($result == 'aucun')
            {
                $this->session->set_flashdata('flash_message', 
                    array(
                        'message' => 'Aucun changement détecté',
                        'alert'   => 'primary'
                    )
                );

                redirect(current_url());
                exit;
            }

            else
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => "Une erreur s'est produite.",
                        'alert'   => 'danger'
                    )
                );

                redirect(current_url());
                exit;
            }
        }
    }

    /* ------------------------------------------------------------------------
     *
     * Profil de l'enseignant > Mot-de-passe
     *
     * ------------------------------------------------------------------------ */
	public function _profil_enseignant_motdepasse()
    {
        $enseignant = $this->Enseignant_model->extraire_profil_enseignant($this->enseignant_id);

		$post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

        //
        // Definition des messages d'erreur
        //

        $this->form_validation->set_rules('password0', 'Mot de passe', 'required|min_length[8]|alpha_numeric');
        $this->form_validation->set_rules('password1', 'Mot de passe', 'required|min_length[8]|max_length[50]|alpha_numeric');
        $this->form_validation->set_rules('password2', 'Confirmation', 'required|matches[password1]');

		//
		// Validation du formulaire (form)
		// 

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

       	if ($this->form_validation->run() == FALSE)
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
			//
            
            $this->data['enseignant'] = $enseignant;
        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
			//

            if ($post_data['enseignant_id'] != $enseignant['enseignant_id'])
            {
                generer_erreur('SP45123e', "L'enseignant ne peut modifier que son profil.");
                die;
            }

            $result = $this->Enseignant_model->modifier_profil_motdepasse($enseignant['enseignant_id'], $post_data);

            //
            // Retourner un message selon le resultat de l'operation
            //

            if ($result === TRUE)
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => 'Votre mot-de-passe a été changé.',
                        'alert'   => 'success'
                    )
                );

                redirect(current_url());
                exit;
            }

			elseif ($result == 'mauvais_motdepasse')
			{
				$this->session->set_flashdata('flash_message',
					array(
						'message' => 'Votre mot-de-passe actuel est incorrect.',
						'alert'   => 'danger'
					)
				);
			
				redirect(current_url());
				exit;
			}

			elseif ($result == 'mauvaise_confirmation')
			{
				$this->session->set_flashdata('flash_message',
					array(
						'message' => 'Le mot-de-passe et sa confirmation ne correspondent pas.',
						'alert'   => 'danger'
					)
				);
			
				redirect(current_url());
				exit;
			}

            else
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => "Une erreur s'est produite.",
                        'alert'   => 'danger'
                    )
                );

                redirect(current_url());
                exit;
            }

        }
    }

    /* ------------------------------------------------------------------------
     *
     * Profil de l'enseignant > Parametres
     *
     * ------------------------------------------------------------------------ */
	public function _profil_enseignant_parametres()
    {
        //
        // Extraire le profil de l'enseignant
        //

        //
        // (!) Lorsqu'on ajoute un nouveau parametre, il faut modifier le select a 3 endroits :
        //
        // - Enseignant_model > extraire_profil_enseignant
        // - Enseignant_model > extraire_enseignant
        // - Auth_model       > connexion_cookie 
        //

        $enseignant = $this->Enseignant_model->extraire_profil_enseignant($this->enseignant_id);

		//
		// Extraire les donnees du _POST
		// 

		$post_data = $this->input->post(NULL, TRUE);

		$this->form_validation->set_error_delimiters('<div style="margin-top: 7px; color:crimson; font-size: 0.8em"><i class="fa fa-exclamation-circle"></i> ', '</div>');

        //
        // Definition des messages d'erreur
        //

		/*
		$this->form_validation->set_rules('nom', 'Nom', 'trim|required|min_length[2]|max_length[75]');
		$this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|min_length[2]|max_length[50]');
		*/

		//
		// Validation du formulaire (form)
		// 

        $this->data['flash_message'] = $this->session->flashdata('flash_message');

       	if (empty($post_data))
        {
            //
            // Premier chargement OU le formulaire comporte des erreurs
            //

            $this->data['enseignant'] = $enseignant;

        }
        else
        {
			//
			// Le formulaire a ete rempli correctement.
            //
           
            if ($post_data['enseignant_id'] != $enseignant['enseignant_id'])
            {
                generer_erreur('SP45123f', "L'enseignant ne peut modifier que son profil.");
                die;
            }

            unset($post_data['enseignant_id']);

            $result = $this->Enseignant_model->modifier_profil_parametres($enseignant['enseignant_id'], $post_data);

            //
            // Retourner un message selon le resultat de l'operation
            //

            if ($result === TRUE)
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => 'Votre profil a été changé.',
                        'alert'   => 'success'
                    )
                );

                redirect(current_url());
                exit;
            }

            elseif ($result == 'aucun')
            {
                $this->session->set_flashdata('flash_message', 
                    array(
                        'message' => 'Aucun changement détecté',
                        'alert'   => 'primary'
                    )
                );

                redirect(current_url());
                exit;
            }

            else
            {
                $this->session->set_flashdata('flash_message',
                    array(
                        'message' => "Une erreur s'est produite.",
                        'alert'   => 'danger'
                    )
                );

                redirect(current_url());
                exit;
            }
        }

        $this->data['enseignant'] = $enseignant;
	}

    /* ------------------------------------------------------------------------
     *
     * Profil de l'etudiant > Compte
     *
     * ------------------------------------------------------------------------ */
	public function _profil_etudiant_compte()
    {
		$etudiant = $this->Etudiant_model->extraire_profil_etudiant($this->etudiant_id);

        $this->data['etudiant'] = $etudiant;
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
	public function _affichage($page = '')
    {
        if (empty($page))
        {
            redirect(base_url());
            exit;
        }

        $this->load->view('commons/header', $this->data);

        switch($page)
        {
            case 'etudiant' :
                $this->load->view('profil/profil_etudiant', $this->data);
                break;

            case 'enseignant' :
                $this->load->view('profil/profil_enseignant', $this->data);
                break;
        }

        $this->load->view('commons/footer');
    }
}
