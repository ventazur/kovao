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
 * ETUDIANT
 *
 * ============================================================================ */

class Etudiant extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();

        if ( ! $this->est_enseignant)
        {
            redirect(base_url());
            exit;
        }

        if ( ! $this->appartenance_groupe)
        {
            redirect(base_url());
            exit;
        }
	}

    /* ------------------------------------------------------------------------
     *
     * Remap
     *
     * ------------------------------------------------------------------------ */
    public function _remap($method = NULL)
    {
        //
        // La methode est un EtudiantID.
        //

        if (ctype_digit($method))
        {
            $this->_voir($method);
            return;
        }

        //
        // La methode est une fonction
        //

        $valid_methods = array();

        if (in_array($method, $valid_methods))
        { 
			$this->$method();
			return;
		}	

        //
        // La methode est uen fonction AJAX
        //

        $valid_ajax_methods = array();

        if (in_array($method, $valid_ajax_methods))
        {
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
		// Cette classe n'est pas accessible directement.
		return;
    }

    /* ------------------------------------------------------------------------
     *
     * Voir un etudiant
     *
     * ------------------------------------------------------------------------ */
	public function _voir($etudiant_id)
    {
        //
        // On ne peut seulement voir un de nos etudiants (ou anciens etudiants)
        //

        $mon_etudiant = FALSE; // Est-ce j'ai deja enseigne a cet etudiant?
        $mon_groupe   = FALSE; // Est-ce que cet etudiant a deja suivi un cours dans le groupe?
            
        $etudiant = $this->Etudiant_model->extraire_etudiant($etudiant_id);

        if ($etudiant == FALSE)
        {
            $this->_affichage('etudiant_introuvable');
            return;
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
            // 1. Verifier s'il s'agit d'un de mes etudiants.
            // 2. Etablir un tableau cours -> enseignant base sur ces soumissions.
            // 3. Extraire les informaions sur chaque cours
            // 4. Enlever les soumissions qui ne proviennent pas de cet enseignant (confidentialite).
            //

            foreach($soumissions as $s)
            {
                //
                // 1.
                //

                if ($s['enseignant_id'] == $this->enseignant_id)
                {
                    $mon_etudiant = TRUE;
                }

                if ($this->groupe_id != 0 && $s['groupe_id'] == $this->groupe_id)
                {
                    $mon_groupe = TRUE;
                } 

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

                //
                // 4.
                //

                if ($s['enseignant_id'] != $this->enseignant_id)
                {
                    unset($soumissions[$s['soumission_id']]);
                }
            }
        }

        arsort($s_semestres);

        //
        // Extraire les groupes et les ecoles
        //

        $this->data['etudiant']    = $etudiant;
        $this->data['soumissions'] = $soumissions;
        $this->data['s_semestres'] = $s_semestres;
        $this->data['s_cours']     = $s_cours;
        $this->data['cours_data']  = $cours_data;
        $this->data['activite']    = $this->Etudiant_model->extraire_etudiant_activite($etudiant_id);
        $this->data['evaluations_envoyees'] = $evaluations_envoyees;
        $this->data['derniere_connexion']   = $this->Etudiant_model->derniere_connexion($etudiant_id);

        if ( ! $mon_etudiant && ! $mon_groupe)
        {
            $this->_affichage('pas_mon_etudiant');
            return;
        }

        $this->_affichage();
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage($page = '')
    {
        $this->load->view('commons/header', $this->data);

        switch ($page) 
        {
            case 'etudiant_introuvable' :
            case 'pas_mon_etudiant' :
                $this->load->view('etudiant/' . $page);
                break;

            default:
                $this->load->view('etudiant/etudiant');
        }

        $this->load->view('commons/footer', $this->data);

    }
}
