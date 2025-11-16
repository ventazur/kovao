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
 * ENSEIGNANT MODEL
 *
 * ============================================================================ */

class Enseignant_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    //
    // Index des fonctions :
    //
    // function lister_enseignants ($options = array())
    // function lister_enseignants_tous ($options = array())
    // function extraire_profil_enseignant ($enseignant_id)
    // function extraire_enseignant ($enseignant_id, $options = array())
    // function extraire_enseignants ($options = array())
    // function extraire_enseignant_groupe ($enseignant_id)
    // function enseignants_evaluations_selectionnees ($groupe_id, $semestre_id, $options = array())
    // function extraire_cours_groupes ($options = array())
    // function nombre_evaluations_selectionnees ($options = array())
    // function editer_enseignant ($enseignant_id, $groupe_id = NULL, $post_data)
    // ...
    //

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des enseignants
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_enseignants($options = array())
    {
    	$options = array_merge(
        	array(
            	'ecole_id'       => NULL, // @TODO
                'groupe_id'      => ( ! array_key_exists('groupe_id', $options) ? $this->groupe_id : $options['groupe_id']),
                'cours_id'       => NULL,
                'enseignant_ids' => NULL,
                'test'           => FALSE,
                'actif'          => NULL  // actif dans le groupe
            ),
            $options
        );

        $this->db->from  ($this->enseignants_t . ' as e, ' . $this->enseignants_groupes_t . ' as eg');

        //
        // Chercher les enseignants par enseignant_id
        //
        if ($options['enseignant_ids'])
        {
            $this->db->where_in('e.enseignant_id', $options['enseignant_ids']);
        }

        //
        // Chercher les enseignants par groupe_id
        //
        elseif ($options['groupe_id'] !== NULL)
        {
            $this->db->where('eg.groupe_id', $options['groupe_id']);

            if ($options['actif'])
            {
                $this->db->where('eg.actif', 1);
            }
        }

        if ($options['test'] == FALSE)
        {
            $this->db->where('e.test', 0);
        }

        $this->db->where('eg.enseignant_id = e.enseignant_id');

        $this->db->where   ('e.actif', 1);
        $this->db->where   ('e.efface', 0);

        $this->db->order_by('e.nom', 'asc');
        $this->db->order_by('e.prenom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'enseignant_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste de tous les enseignants
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_enseignants_tous($options = array())
    {
    	$options = array_merge(
        	array(
                'enseignant_ids' => NULL,
                'actif'          => NULL,  // actif dans le groupe
            ),
            $options
        );

        $this->db->from  ($this->enseignants_t . ' as e');

        $this->db->select('e.enseignant_id, e.courriel, e.courriel_confirmation, e.nom, e.prenom, e.genre,
            e.privilege, e.inscription_date, e.inscription_epoch, e.derniere_activite_date, e.derniere_activite_epoch, e.activite_compteur, e.actif');

        //
        // Chercher les enseignants par enseignant_id
        //

        if ($options['enseignant_ids'])
        {
            $this->db->where_in('e.enseignant_id', $options['enseignant_ids']);
        }
        
        // $this->db->where   ('e.actif', 1);
        $this->db->where   ('e.efface', 0);

        $this->db->order_by('e.nom', 'asc');
        $this->db->order_by('e.prenom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'enseignant_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le profil d'un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_profil_enseignant($enseignant_id)
    {
        $this->db->from   ($this->enseignants_t . ' as e');
        $this->db->select ('e.enseignant_id, e.courriel, e.nom, e.prenom, e.genre,
                            e.cacher_evaluation, e.inscription_requise, e.montrer_rang, e.montrer_ecart_moy, e.permettre_fichiers_dangereux');
        $this->db->where  ('e.enseignant_id', $enseignant_id);
        $this->db->limit  (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $row = $query->row_array();

        return $row;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_enseignant($enseignant_id, $options = array())
    {
    	$options = array_merge(
            array(
                'inclure_motdepasse' => FALSE,
                'groupe_id'          => @$this->groupe_id,
            ),
            $options
       	);

        $select  = 'e.enseignant_id, e.courriel, e.nom, e.prenom, e.genre, e.inscription_epoch, e.actif, e.efface';
        $select .= ', eg.groupe_id, eg.semestre_id, eg.niveau';
        $select .= ', e.cacher_evaluation, e.inscription_requise, e.montrer_rang, e.montrer_ecart_moy, e.permettre_fichiers_dangereux';

        // Ceci est necessaire pour changer le profil de l'enseignant
        if ($options['inclure_motdepasse'])
        {
            $select .= ', e.password, e.salt';
        }

        $this->db->from     ($this->enseignants_t . ' as e, ' . $this->enseignants_groupes_t . ' as eg');
        $this->db->select   ($select);
        $this->db->where    ('e.enseignant_id', $enseignant_id);
        $this->db->where    ('eg.groupe_id', $options['groupe_id']);
        $this->db->where    ('eg.enseignant_id = e.enseignant_id');
        $this->db->limit    (1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire des enseignants
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_enseignants($options = array())
    {
    	$options = array_merge(
        	array(
            	'ecole_id'  => NULL,
                'groupe_id' => NULL
            ),
            $options
       	);

        if ($options['ecole_id'] && $options['groupe_id'] == NULL)
        {
            return FALSE;
        }

        $this->db->from ('enseignants as e');

        if ($options['ecole_id'])
        {
            $this->db->where('e.ecole_id', $options['ecole_id']);
        }

        if ($options['groupe_id'])
        {
            $this->db->where('e.groupe_id', $options['groupe_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return array_keys_swap($query->result_array(), 'enseignant_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les informations du groupe pour l'enseignant (groupe actuel)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_enseignant_groupe($enseignant_id)
    {
        $this->db->from  ($this->config->item('database_tables')['enseignants'] . '_groupes');
        $this->db->where ('enseignant_id', $enseignant_id);
        $this->db->where ('groupe_id', $this->groupe_id);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
            return array();
                                                                                                                                                                                                                                  
        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Enseignants ayant des evaluations selectionnees
     *
     * -------------------------------------------------------------------------------------------- */
    function enseignants_evaluations_selectionnees($groupe_id, $semestre_id, $options = array())
    {
    	$options = array_merge(
        	array(
                'cours_id'         => NULL,
                'enseignant_id'    => NULL,
                'cacher_cachees'   => FALSE, // Cacher les evaluations cachees par l'enseignant (pour les groupes ayant un sous-domaine).
                'cacher_bloquees'  => FALSE, // Cacher les evaluations bloquees par l'enseignant (pour les groupes ayant un sous-domaine).
                'respecter_date'   => FALSE  // Montrer seulement les evaluations qui peuvent etre remplies en respectant les dates planifiees.
            ),
            $options
        );

        $this->db->where('ree.semestre_id', $semestre_id);
        $this->db->from ('enseignants as en, 
                          rel_enseignants_evaluations as ree, 
                          rel_enseignants_cours as rec, 
                          evaluations as e, 
                          cours as c');

        $this->db->select('en.enseignant_id, en.courriel, en.nom, en.prenom, en.genre, c.cours_id, e.evaluation_id, ree.*');

        $this->db->where('ree.groupe_id',   $groupe_id);
        $this->db->where('ree.semestre_id', $semestre_id);
        $this->db->where('ree.efface',      0);

        if ($options['cacher_cachees'] && $this->groupe_id != 0)
        {
            $this->db->where('ree.cacher', 0);
        }

        if ($options['cacher_bloquees'] && $this->groupe_id != 0)
        {
            $this->db->where('ree.bloquer', 0);
        }

        if ($options['respecter_date'])
        {
            $this->db->where('ree.debut_epoch <', $this->now_epoch);
        }

        $this->db->where('ree.evaluation_id = e.evaluation_id');
        $this->db->where('e.cours_id = rec.cours_id');
        $this->db->where('e.cours_id = c.cours_id');

        // Ajout car ceci affichait les evaluations de cours non selectionnes lorsque, par exemple,
        // un enseignant selectionne un cours puis selectionne une evaluation, pour finalement deselectionne un cours
        // sans en faire de meme pour l'evaluation. (2019-03-29)
        $this->db->where('rec.enseignant_id = en.enseignant_id'); 
        $this->db->where('rec.cours_id = c.cours_id');

        $this->db->where('ree.enseignant_id = en.enseignant_id');

        if ( ! empty($options['enseignant_id']))
        {
            $this->db->where('ree.enseignant_id', $options['enseignant_id']);
        }

        $this->db->where('c.actif', 1);
        $this->db->where('e.actif', 1);
        $this->db->where('en.actif', 1);

        if ( ! empty($options['cours_id']))
        {
            $this->db->where('c.cours_id', $options['cours_id']);
        }

        $this->db->order_by('en.nom', 'asc');
        $this->db->order_by('en.prenom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array(); 
        }

        $enseignants = array_keys_swap($query->result_array(), 'evaluation_id'); // pour obtenir des valeurs uniques

        return $enseignants;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le ou les groupes (cours_groupe) d'un cours
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_cours_groupes($options = array())
    {
    	$options = array_merge(
        	array(
                'semestre_id' => $this->enseignant['semestre_id']
            ),
            $options
        );

        $this->db->select   ('cours_id, cours_groupe');
        $this->db->from     ('eleves');
        $this->db->where    ('enseignant_id', $this->enseignant_id);
        $this->db->where    ('semestre_id', $options['semestre_id']);
        $this->db->where    ('groupe_id', $this->groupe_id);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $resultats = array();

        foreach($query->result_array() as $r)
        {
            if ( ! array_key_exists($r['cours_id'], $resultats))
            {
                $resultats[$r['cours_id']] = array();
            }

            if ( ! in_array($r['cours_groupe'], $resultats[$r['cours_id']]))
            {
                $resultats[$r['cours_id']][] = $r['cours_groupe'];
            }
        }
            
        return $resultats;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Nombre d'evaluations a remplir
     *
     * -------------------------------------------------------------------------------------------- */
    function nombre_evaluations_selectionnees($options = array())
    {
    	$options = array_merge(
        	array(
                'groupe_id'      => $this->groupe_id,
                'semestre_id'    => NULL,
                'enseignant_id'  => NULL
            ),
            $options
        );

        $this->db->from     ('rel_enseignants_evaluations as ree');
        $this->db->where    ('ree.groupe_id', $options['groupe_id']);
        $this->db->where    ('ree.efface', 0);

        if ($options['semestre_id'])
        {
            $this->db->where ('ree.semestre_id', $options['semestre_id']);
        }

        if ($options['enseignant_id'])
        {
            $this->db->where ('ree.enseignant_id', $options['enseignant_id']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        $evaluations = array();

        foreach ($query->result_array() as $row)
        {
            $enseignant_id = $row['enseignant_id'];

            if ( ! array_key_exists($enseignant_id, $evaluations))
                $evaluations[$enseignant_id] = array();

            $evaluations[$enseignant_id][] = $row['evaluation_id'];
        }

        return $evaluations;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Editer un enseignant d'un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function editer_enseignant($enseignant_id, $groupe_id = NULL, $post_data = array())
    {
        //
        // Verifier que post_data contient des informations (php8.1, 2024/06/28)
        //

        if (empty($post_data))
        {
            return FALSE;
        }

        //
        // Un sysop *seulement* peut modifier les informations d'un usager (enseignant ou etudiant).
        //

        if (empty($groupe_id))
        {
            $groupe_id = $this->groupe_id;
        }

        // Verifier l'existence de cet enseignant dans la base de donnees.

        $this->db->from  ('enseignants as e, enseignants_groupes as eg');
        $this->db->where ('e.enseignant_id', $enseignant_id);
        $this->db->where ('e.enseignant_id = eg.enseignant_id');
        $this->db->where ('eg.groupe_id', $groupe_id);
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            // Cet enseignant est introuvable.
            return FALSE;
        }

		$row = $query->row_array();

        //
        // Extraire seulement les champs a changer
        //

		$champs_valide = array('nom', 'prenom', 'genre', 'niveau');

		$data = array();
		
		foreach($post_data as $k => $v)
		{
			if ( ! in_array($k, $champs_valide))
				continue;

			if ($v == $row[$k])
				continue;

			$data[$k] = $v;
		}

        // Toutes les valeurs sont identiques aux valeurs de la base de donnees

		if (empty($data))
        {
			return FALSE;
        }

        //
        // Est-ce qu'il faut changer le niveau ?
        //

        if (array_key_exists('niveau', $data))
        {
            $niveau = $data['niveau'];
            unset($data['niveau']);

            //
            // Il faut s'assurer que le niveau est inferieur ou egal a celui qui veut le changer.
            //

            if ($niveau <= $this->enseignant['niveau'])
            {
                $this->db->where ('enseignant_id', $enseignant_id);
                $this->db->where ('groupe_id', $groupe_id);
                $this->db->update('enseignants_groupes', array('niveau' => $niveau));
            }
        }

        //
        // Est-ce qu'il faut changer les autres informations ?
        //
        
		if ( ! empty($data))
        {
            // Il faut s'assurer que celui qui veut faire un changement est dans le meme groupe que l'enseignant,
            // sauf si c'est un administrateur de l'ecole.

            if (
                ($this->enseignant['niveau'] >= 75) ||
                ($this->enseignant['niveau'] >= 60 && $this->enseignant['ecole_id'] == $row['ecole_id'])   ||
                ($this->enseignant['niveau'] >= 40 && $this->enseignant['groupe_id'] == $row['groupe_id']) ||
                ($this->enseignant['enseignant_id'] == $enseignant_id)
            )
            {
                $this->db->where ('enseignant_id', $enseignant_id);
                $this->db->update('enseignants', $data);
            }
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Activer ou desactiver un enseignant (toggle)
     *
     * -------------------------------------------------------------------------------------------- */
    function activer_enseignant($enseignant_id)
    {
		if ( ! permis('admin_editer_enseignant'))
			return FALSE;

        // Verifier l'existence de cet enseignant dans la base de donnees.

        $this->db->select   ('eg.*');
        $this->db->from     ('enseignants e, enseignants_groupes as eg');
        $this->db->where    ('e.enseignant_id = eg.enseignant_id');
        $this->db->where    ('e.enseignant_id', $enseignant_id);
        $this->db->where    ('eg.groupe_id', $this->groupe_id);
        $this->db->limit    (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
            return FALSE;

        $row = $query->row_array();

		// Ne pas permettre d'activer ou de desactiver un enseignant avec un niveau egal ou superieur au demande.
	
		if ($this->enseignant['niveau'] <= $row['niveau'])
			return FALSE;

		$data = array();

		// Verifier s'il faut activer ou desactiver un enseignant (toggle)
		if ($row['actif'])
		{
			$data['actif'] = 0;
		}
		else
		{
			$data['actif'] = 1;
		}

		$this->db->where ('enseignant_id', $row['enseignant_id']);
        $this->db->where ('groupe_id', $this->groupe_id);
		$this->db->update('enseignants_groupes', $data);

		if ($this->db->affected_rows())
        {
			return TRUE;
        }

		return FALSE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le profil : identite
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_profil_identite($enseignant_id, $post_data)
    {
        $enseignant = $this->extraire_enseignant($enseignant_id);

        //
        // Permettre la modification des champs pertinents uniquement
        //

        $champs_pertinents = array(
            'nom', 'prenom', 'genre'
        );

        //
        // Exclure les champs
        //

        $data = array();

        foreach($post_data as $champ => $val)
        {
            if ( ! in_array($champ, $champs_pertinents))
            {
                continue;
            }
    
            if ($enseignant[$champ] == $val)
            {
                continue;
                
            }

            $data[$champ] = $val;
        }

        //
        // Faire les modifications appropriees
        //

        if (empty($data) || ! is_array($data))
        {
            return 'aucun';
        }

        $this->db->where ('enseignant_id', $enseignant_id);
        $this->db->update('enseignants', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le profil : mot-de-passe
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_profil_motdepasse($enseignant_id, $post_data)
    {
        $enseignant = $this->extraire_enseignant($enseignant_id,
            array(
                'inclure_motdepasse' => TRUE
            )
        );

        $champs_obligatoires = array(
            'password0', // mot-de-passe actuel
            'password1', // nouveau mot-de-passe
            'password2'  // confirmation du nouveau mot-de-passe
        );

        foreach($champs_obligatoires as $champ)
        {
            if ( ! array_key_exists($champ, $post_data) || empty($post_data[$champ]))
            {
                return FALSE;
            }
        }

        //
        // Verifier le mot-de-passe actuel
        //

        if ( ! $this->Auth_model->verifier_motdepasse($enseignant['salt'], $post_data['password0'], $enseignant['password']))
        {
            return 'mauvais_motdepasse';
        }     

        //
        // Faire les modifications au mot-de-passe
        //

        if ($this->Auth_model->editer_password('enseignant', $enseignant_id, $post_data['password1']) !== TRUE)
        {
            return FALSE;
        }

        log_alerte(
            array(
                'code'  => 'PRF8900',
                'desc'  => $enseignant['prenom'] . ' ' . $enseignant['nom'] . ' a changé son mot-de-passe.',
                'extra' => 'enseignant_id = ' . $enseignant['enseignant_id']
            )
        );

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier le profil : parametres
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_profil_parametres($enseignant_id, $post_data)
    {
        $enseignant = $this->extraire_enseignant($enseignant_id);

        //
        // Permettre la modification des champs pertinents uniquement
        //

        // ATTN : Il faut aussi ajouter un nouveau champ aux deux fonctions pour extraire un enseignant (plus-haut).

        $champs_pertinents = array(
            'cacher_evaluation', 'inscription_requise', 'montrer_rang', 'montrer_ecart_moy', 'permettre_fichiers_dangereux'
        );

        //
        // Initialiser les champs (standard behaviour is the value is only sent if the checkbox is checked)
        //
        // absent  : 0
        // present : on => 1
        //

        foreach($champs_pertinents as $champ)
        {
            if ( ! array_key_exists($champ, $post_data))
            {
                $post_data[$champ] = 0;
            }
            else
            {
                $post_data[$champ] = 1;
            }
        }

        //
        // Exclure les champs non pertinents
        //

        $data = array();

        foreach($post_data as $champ => $val)
        {
            if ( ! in_array($champ, $champs_pertinents))
            {
                continue;
            }
    
            if ($enseignant[$champ] == $val)
            {
                continue;
            }

            $data[$champ] = $val;
        }

        //
        // Faire les modifications appropriees
        //

        if (empty($data) || ! is_array($data))
        {
            return 'aucun';
        }

        $this->db->where ('enseignant_id', $enseignant_id);
        $this->db->update('enseignants', $data);

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Cacher une evaluation
     *
     * -------------------------------------------------------------------------------------------- */
    function cacher_evaluation_toggle($reference)
    {
        $this->db->from  ('rel_enseignants_evaluations as ree');
        $this->db->where ('ree.evaluation_reference', $reference);
        $this->db->where ('ree.groupe_id !=', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;
        
        $row = $query->row_array();

        if ($row['enseignant_id'] == $this->enseignant_id)
        {
            $data = array(
                'cacher' =>  $row['cacher'] ? 0 : 1 
            );

            $this->db->where  ('evaluation_reference', $reference);
            $this->db->update ('rel_enseignants_evaluations', $data);

            return TRUE;
        }

        return FALSE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les soumissions de l'enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_soumissions($enseignant_id)
    {
        $this->db->from   ('soumissions');
        $this->db->where  ('enseignant_id', $enseignant_id);
        $this->db->where  ('efface', 0);
        $this->db->order_by ('soumission_epoch', 'desc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
                                                                                                                                                                                                                                  
        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Derniere connexion
     *
     * -------------------------------------------------------------------------------------------- */
    function derniere_connexion($enseignant_id)
    {
        $this->db->from     ('activite');
        $this->db->where    ('enseignant_id', $enseignant_id);
        $this->db->order_by ('epoch', 'desc');
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
             return FALSE;
                                                                                                                                                                                                                                  
        return $query->row_array();
    } 

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les traces completes
     *
     * --------------------------------------------------------------------------------------------
     *
     * Ceci inclus les donnees (data = traces) ainsi que les autres informations.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_traces_completes($evaluation_reference, $evaluation_id, $session_id = NULL)
    {
        $this->db->from  ('enseignants_traces as et');
        $this->db->where ('et.enseignant_id', $this->enseignant_id);
        $this->db->where ('et.evaluation_id', $evaluation_id);
        $this->db->where ('et.efface', 0);
        $this->db->limit (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        return $query->row_array();
    }

}
