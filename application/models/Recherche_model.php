<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * RECHERCHE MODEL
 *
 * ============================================================================ */

class Recherche_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rechercher les resultats d'un etudiant (version 2, 2019/08/07)
     *
     * -------------------------------------------------------------------------------------------- */
    function rechercher2($search_query, $options = array())
    {
    	$options = array_merge(
        	array(
                'semestres' => FALSE,
                'etudiants' => FALSE,
           ),
           $options
        );

        $search_query = trim($search_query);

        $this->db->from   ('soumissions as s');
        $this->db->select ('s.*');

        $first_char = substr($search_query, 0, 1);

        if (is_numeric($first_char))
        {
            $this->db->where ('s.numero_da', $search_query);
        }
        else
        {
            $prenom_nom = strip_accents($search_query);

            $this->db->like('s.prenom_nom', $prenom_nom);
        }

        if ( ! $options['etudiants'])
        {
            $this->db->where ('s.enseignant_id', $this->enseignant_id);
        }

        if ( ! $options['semestres'])
        {
            $this->db->where('s.semestre_id', $this->enseignant['semestre_id']);
        }

        $this->db->order_by ('s.soumission_epoch', 'desc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;
                                                                                                                                                                                                                                  
        $soumissions_trouvees = $query->result_array();

        if (empty($soumissions_trouvees))
        {
            return FALSE;
        }

        return $soumissions_trouvees;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rechercher matricule
     *
     * -------------------------------------------------------------------------------------------- */
    function recherche_matricule($requete, $options = array())
    {
        // La requete est le matricule de l'etudiant.

    	$options = array_merge(
        	array(
           ),
           $options
        );

        //
        // Chercher des soumissions
        // en considerant le texte comme le nom de l'etudiant
        //

        $this->db->from   ('soumissions as s');
        $this->db->where  ('s.enseignant_id', $this->enseignant_id);
        $this->db->where  ('s.groupe_id', $this->groupe_id);
        $this->db->select ('s.soumission_id, s.soumission_epoch, s.soumission_reference, s.prenom_nom, s.numero_da, s.enseignant_id, s.cours_id, s.semestre_id' .
                           ', s.permettre_visualisation, s.corrections_terminees, s.corrections_manuelles, s.vues, s.points_obtenus, s.points_total, s.points_evaluation' . 
                           ', s.evaluation_data_gz');
        $this->db->like   ('s.numero_da', $requete);

        $this->db->order_by ('s.soumission_epoch', 'desc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }
                                                                                                                                                                                                                                  
        $soumissions = $query->result_array();

        return array(
            'soumissions' => $soumissions,
            'soumissions_compte' => count($soumissions)
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rechercher le texte (VERSION 3, 2020-07-15)
     *
     * -------------------------------------------------------------------------------------------- */
    function recherche_texte($requete, $options = array())
    {
    	$options = array_merge(
        	array(
           ),
           $options
        );

        //
        // Chercher des etudiants (de toutes les sessions)
        //

        $etudiants = array();

        /*
         * Ceci fonctionne mais donne trop de resultats et excede la memoire disponible.
         *
        $this->db->from     ('eleves as e, semestres as s, cours as c, enseignants as en');
        $this->db->select   ('e.*, s.semestre_id, s.semestre_code, c.cours_nom_court, c.cours_code_court, en.nom, en.prenom');
        $this->db->where    ('e.enseignant_id', $this->enseignant_id);
        $this->db->where    ('e.groupe_id', $this->groupe_id);
        $this->db->like     ('e.eleve_prenom', $requete);
        $this->db->or_like  ('e.eleve_nom', $requete);
        $this->db->where    ('e.semestre_id = s.semestre_id');
        $this->db->where    ('e.cours_id = c.cours_id');
        $this->db->where    ('e.enseignant_id = en.enseignant_id');
        $this->db->order_by ('s.semestre_id', 'desc');
        $this->db->order_by ('e.eleve_nom', 'asc');
        $this->db->order_by ('e.eleve_prenom', 'asc');
        
        $query = $this->db->get();

        $matricules_eleves = array();

        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $r)
            {
                if ($r['enseignant_id'] != $this->enseignant_id)
                    continue;

                if ($r['groupe_id'] != $this->groupe_id)
                    continue;

                $r['etudiant_id'] = NULL;
                    
                $etudiants[] = $r;

                $matricules_eleves[$r['numero_da']] = $r['eleve_id'];
            }

            $etudiants = array_keys_swap($etudiants, 'eleve_id');

            $this->db->from     ('etudiants_numero_da');
            $this->db->where_in ('numero_da', array_keys($matricules_eleves));
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                foreach ($query->result_array() as $r)
                {
                    if ( ! array_key_exists($r['numero_da'], $matricules_eleves))
                        continue;

                    $eleve_id = $matricules_eleves[$r['numero_da']];

                    $etudiants[$eleve_id]['etudiant_id'] = $r['etudiant_id'];
                }
            }
        }
        */

        //
        // Chercher des soumissions
        //
        // en considerant le texte comme le nom de l'etudiant
        //

        $soumissions = array();

        $this->db->from   ('soumissions as s');
        $this->db->where  ('s.enseignant_id', $this->enseignant_id);
        $this->db->where  ('s.semestre_id', $this->semestre_id);
        $this->db->where  ('s.groupe_id', $this->groupe_id);
        $this->db->select ('s.soumission_id, s.etudiant_id, s.soumission_epoch, s.soumission_reference, s.prenom_nom, s.numero_da, s.enseignant_id, s.cours_id, s.semestre_id' .
                           ', s.permettre_visualisation, s.corrections_terminees, s.corrections_manuelles, s.vues, s.points_obtenus, s.points_total, s.points_evaluation' . 
                           ', s.evaluation_data_gz, s.ajustements_data');
        $this->db->like   ('s.prenom_nom', $requete);

        $this->db->order_by ('s.soumission_epoch', 'desc');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $soumissions = $query->result_array();
        }

        //
        // Chercher des evaluations
        //
        // en considerant le texte comme le titre de l'evaluation
        //

        $evaluations = array(); 

        $this->db->from  ('evaluations as e');
        $this->db->where ('e.enseignant_id', $this->enseignant_id);
        $this->db->where ('e.public', 0);
        $this->db->where ('e.efface', 0);
        $this->db->like  ('e.evaluation_titre', $requete);

        $this->db->order_by ('e.evaluation_titre', 'asc');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $evaluations = $query->result_array();
        }

        return array(
            'etudiants'          => $etudiants,
            'soumissions'        => $soumissions,
            'soumissions_compte' => count($soumissions),
            'evaluations'        => $evaluations,
            'evaluations_compte' => count($evaluations)
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rechercher un etudiant (pour un enseignant)
     *
     * -------------------------------------------------------------------------------------------- */
    function recherche_etudiants_pour_enseignants($requete, $options = array())
    {
    	$options = array_merge(
        	array(
           ),
           $options
        );

        //
        // Chercher des etudiants (de toutes les sessions)
        //

        $etudiants = array();

		$this->db->from('etudiants as e');
		$this->db->like('e.prenom', $requete);
		$this->db->or_like('e.nom', $requete);

		$this->db->join('etudiants_numero_da', 'e.etudiant_id = etudiants_numero_da.etudiant_id');
		$this->db->where('etudiants_numero_da.groupe_id', $this->groupe_id);

        $this->db->order_by ('e.nom', 'asc');
        $this->db->order_by ('e.prenom', 'asc');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $etudiants = $query->result_array();
        }

        return array(
            'etudiants'          => $etudiants,
            'etudiants_compte'   => count($etudiants)
        );
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Rechercher un etudiant ADMIN
     *
     * -------------------------------------------------------------------------------------------- */
    function recherche_etudiants_admin($requete, $options = array())
    {
    	$options = array_merge(
        	array(
           ),
           $options
        );

        //
        // Chercher des etudiants (de toutes les sessions)
        //

        $etudiants = array();

        $this->db->from     ('etudiants as e');
        $this->db->like     ('e.prenom', $requete);
        $this->db->or_like  ('e.nom', $requete);
        $this->db->order_by ('e.etudiant_id', 'desc');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $etudiants = $query->result_array();
        }

        return array(
            'etudiants'          => $etudiants,
            'etudiants_compte'   => count($etudiants)
        );
    }
}
