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
 * GROUPE MODEL
 *
 * ============================================================================ */

class Groupe_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();

		$this->groupes_t             = $this->config->item('database_tables')['groupes'];
		$this->enseignants_groupes_t = $this->config->item('database_tables')['enseignants_groupes'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister tous les groupes
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_groupes_tous($options = array())
    {
        $this->db->select   ('g.*');
        $this->db->from     ('groupes as g, ecoles as e');
        $this->db->where    ('g.groupe_id !=', 0);
        $this->db->where    ('g.ecole_id = e.ecole_id');
        $this->db->where    ('e.actif', 1);
        $this->db->where    ('e.efface', 0);
        $this->db->where    ('g.actif', 1);
        $this->db->where    ('g.efface', 0);
        $this->db->order_by ('g.groupe_nom', 'asc');
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }
        
        return array_keys_swap($query->result_array(), 'groupe_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister des groupes (v2 : summercode)
     *
     * --------------------------------------------------------------------------------------------
     *
     * Lister tous les groupes de l'enseignant
     *
     * -------------------------------------------------------------------------------------------- */
	function lister_groupes2($options = array())
    {
    	$options = array_merge(
            array(
                'inclure_personnel' => TRUE // inclure le groupe Personnel
           ),
           $options
       	);

		$ecoles_t	   = $this->config->item('database_tables')['ecoles'];
		$enseignants_t = $this->config->item('database_tables')['enseignants'];
		$groupes_t     = $this->config->item('database_tables')['groupes'];

		$enseignants_groupes_t = $enseignants_t . '_' . $groupes_t;

		$this->db->from  ($ecoles_t . ' as e, ' . $groupes_t . ' as g, ' . $enseignants_groupes_t . ' as eg');
		$this->db->select('g.*, eg.*, e.ecole_id, e.ecole_nom, e.ecole_nom_court');

		$this->db->where ('eg.enseignant_id', $this->enseignant_id);
		$this->db->where ('eg.groupe_id = g.groupe_id');
		$this->db->where ('g.ecole_id = e.ecole_id');

        if ($options['inclure_personnel'] === FALSE)
        {
            $this->db->where('g.groupe_id !=', 0);
        }

		$this->db->where ('e.actif', 1);
		$this->db->where ('e.efface', 0);
		$this->db->where ('g.actif', 1);
		$this->db->where ('g.efface', 0);
		$this->db->where ('eg.actif', 1);

        $query = $this->db->get();

        $groupes = array();

        if ($options['inclure_personnel'] === TRUE)
        {
            $groupes[] = $this->config->item('groupe_www');
        }

        if ( ! $query->num_rows())
        {
            return $groupes;
        }

		$groupes = array_merge($groupes, $query->result_array());

        return array_keys_swap($groupes, 'groupe_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Lister des groupes
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_groupes($options = array())
    {
    	$options = array_merge(
            array(
                'ecole_id'  => NULL,
                'ecole_ids' => NULL     // array de ecole ids
           ),
           $options
       	);

        $this->db->from ('groupes as g, ecoles as e');

        if ( ! empty($options['ecole_id']))
        {
            $this->db->where('e.ecole_id', $options['ecole_id']);
        }

        if ( empty($options['ecole_id']) && ! empty($options['ecole_ids']))
        {
            $this->db->where_in('e.ecole_id', $options['ecole_ids']);
        }

        $this->db->where('e.ecole_id = g.ecole_id');

        $this->db->order_by('e.ecole_nom', 'asc');
        $this->db->order_by('g.groupe_nom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'groupe_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des groupes (simple)
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_groupes_simplement()
    {
        $this->db->from  ('groupes as g');
        $this->db->where ('g.groupe_id !=', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'groupe_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Lister ecoles et groupes
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_ecoles_groupes($options = array())
	{
    	$options = array_merge(
           array(
           ),
           $options
       	);
		
		//
		// Extraire les ecoles
		// 

        $this->db->from  ('ecoles as e');
		$this->db->where ('e.ecole_id !=', 0);
		$this->db->where ('e.visible', 1);
        $this->db->where ('e.actif', 1);
		$this->db->where ('e.efface', 0);
		$this->db->order_by ('e.ecole_nom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

		$ecoles = $query->result_array();
		$ecoles = array_keys_swap($ecoles, 'ecole_id');

		//
		// Extraire les groupes
		//

        $this->db->from  ('groupes as g');
		$this->db->where ('g.groupe_id !=', 0);
		$this->db->where ('g.visible', 1);
        $this->db->where ('g.actif', 1);
		$this->db->where ('g.efface', 0);
		$this->db->order_by ('g.groupe_nom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

		$groupes = $query->result_array();

		$liste = array();

		foreach($ecoles as $ecole_id => $e)
		{
			$liste_groupes = array();

			foreach($groupes as $groupe_id => $g)
			{
				if ($g['ecole_id'] == $ecole_id)
					$liste_groupes[] = $g;
			}

			if ( ! empty($liste_groupes))
			{
				$liste[$ecole_id] = $e;
				$liste[$ecole_id]['groupes'] = $liste_groupes;
			}
		}

		return $liste;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le groupe (v2 : summercode)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_groupe2($options = array())
    {
    	$options = array_merge(
        	array(
                'groupe_id' 	=> NULL, // argument obligatoire
				'enseignant_id' => NULL
           ),
           $options
       	);

		if ($options['groupe_id'] === NULL)
		{
			return FALSE;
		}
        
		$from   = $this->groupes_t . ' as g';
        $select = 'g.*';
        
		$this->db->where ('g.groupe_id', $options['groupe_id']);

		if ($options['enseignant_id'])
		{
			$from   .= ', ' . $this->enseignants_groupes_t . ' as eg';
			$select .= ', eg.*'; 

			$this->db->where ('eg.enseignant_id', $options['enseignant_id']);
			$this->db->where ('g.groupe_id = eg.groupe_id');
		}

        $this->db->from ($from);
        $this->db->select($select);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le groupe
     *
     * --------------------------------------------------------------------------------------------
	 * 
     * Cette fonction semble extraire le groupe d'une evaluation.
     *
     * (!) La version 2 (extraire_groupe2) ne remplace pas cette fonction dans sa totalite.
     * (!) Ne pas remplacer cette fonction sans tests.
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_groupe($options = array())
    {
    	$options = array_merge(
        	array(
                'groupe_id'      => NULL,
                'evaluation_id'  => NULL,
                'extraire_admin' => NULL,
           ),
           $options
       );

        $from   = 'groupes as g';
        $select = 'g.*';

        if ($options['groupe_id'])
        {
            $this->db->where ('g.groupe_id', $options['groupe_id']);
        }

        if ($options['evaluation_id'])
        {
            $from .= ', evaluations as ev, cours as c';
        
            $this->db->where ('ev.evaluation_id', $options['evaluation_id']);

            $this->db->where ('ev.cours_id = c.cours_id');
            $this->db->where ('c.groupe_id = g.groupe_id');
        }

        if ($options['extraire_admin'])
        {
            $from .= ', enseignants as en';
            $select .= ', en.courriel as admin_courriel, en.nom as admin_nom, en.prenom as admin_prenom, en.genre as admin_genre, en.niveau as admin_niveau';
            $this->db->where('g.admin_enseignant_id = en.enseignant_id');
        }

        $this->db->from ($from);
        $this->db->select($select);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les groupes des enseignants
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_enseignants_groupes()
    {
        $this->db->from   ($this->enseignants_groupes_t . ' as eg');
        $this->db->where  ('eg.actif', 1);

        $query = $this->db->get();

        $enseignants = array();

        if ($query->num_rows() > 0)
        {
            $enseignants = $query->result_array();
        }
        else
        {
            return array();
        }

        $enseignants_groupes = array();

        foreach($enseignants as $e)
        {
            if ( ! array_key_exists($e['enseignant_id'], $enseignants_groupes))
                $enseignants_groupes[$e['enseignant_id']] = array();

            if ($e['groupe_id'] == 0)
                continue;

            if ( ! in_array($e['groupe_id'], $enseignants_groupes[$e['enseignant_id']]))
                $enseignants_groupes[$e['enseignant_id']][] = $e['groupe_id'];
        }

        return $enseignants_groupes;

    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les noms et prenoms a partir des numeros de da
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_noms_de_numeros_da($numeros_da = array())
    {
        if (empty($numeros_da))
            return array();

        $this->db->from     ('eleves as el');
        $this->db->where_in ('el.numero_da', $numeros_da);
        $this->db->where    ('el.groupe_id', $this->groupe_id);

        $query = $this->db->get();

        if ($query->num_rows() == 0)
             return array();

        $row = $query->row_array();

        $results = array();

        foreach ($query->result_array() as $row)
        {
           if ( ! array_key_exists($row['numero_da'], $results))
           {
               $string = $row['eleve_nom'] . ', ' . $row['eleve_prenom'];
               $results[$row['numero_da']] = $string;
           }
        }

        return $results;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le responsable du groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function changer_responsable_groupe($groupe_id, $enseignant_id)
    {
        //
        // Celui qui veut performer l'action doit etre un administrateur de groupe.
        //
        if ($this->enseignant['niveau'] < $this->config->item('admin_groupe', 'niveaux'))
            return FALSE;

        $enseignant = $this->Enseignant_model->extraire_enseignant($enseignant_id);

        //
        // Celui qui sera responsable du groupe doit deja etre un administrateur du groupe.
        //
        if ($enseignant['niveau'] < $this->config->item('admin_groupe', 'niveaux'))
            return FALSE;

        $this->db->where ('groupe_id', $groupe_id);
        $this->db->update('groupes',
            array(
                'admin_enseignant_id' => $enseignant['enseignant_id']
            )
        );

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer la permission de s'inscrire dans le groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function inscription_permise_toggle($groupe_id, $permission)
    {
        //
        // Celui qui veut performer l'action doit etre un administrateur de groupe.
        //
        if ($this->enseignant['niveau'] < $this->config->item('admin_groupe', 'niveaux'))
            return FALSE;

        $data = array(
            'inscription_permise' => ($permission == 'oui' ? 1 : 0)
        );

        $this->db->where ('groupe_id', $groupe_id);
        $this->db->update('groupes', $data);

        return TRUE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Changer le code d'inscription d'un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function nouveau_code_inscription($groupe_id, $code)
    {
        //
        // Celui qui veut performer l'action doit etre un administrateur de groupe.
        //
        if ($this->enseignant['niveau'] < $this->config->item('admin_groupe', 'niveaux'))
            return FALSE;

        $data = array(
            'inscription_code' => $code
        );

        $this->db->where ('groupe_id', $groupe_id);
        $this->db->update('groupes', $data);

        return TRUE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer le code d'inscription d'un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_code_inscription($groupe_id)
    {
        //
        // Celui qui veut performer l'action doit etre un administrateur de groupe.
        //

        if ($this->enseignant['niveau'] < $this->config->item('admin_groupe', 'niveaux'))
            return FALSE;

        $data = array(
            'inscription_code' => NULL
        );

        $this->db->where ('groupe_id', $groupe_id);
        $this->db->update('groupes', $data);

        return TRUE; 
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister enseignants en approbation (pour le groupe actuel)
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_enseignants_approbation()
	{
        $this->db->from  ('enseignants_groupes_demandes as d, enseignants as e');
		$this->db->where ('d.groupe_id', $this->groupe_id);
		$this->db->where ('d.enseignant_id = e.enseignant_id');
        $this->db->where ('d.demande_expiration >', $this->now_epoch);
		$this->db->where ('d.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
             return array();

        return $query->result_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire demande pour joindre un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_demande_joindre_groupe()
	{
        $this->db->from  ('enseignants_groupes_demandes as d');
		$this->db->where ('d.groupe_id', $this->groupe_id);
        $this->db->where ('d.enseignant_id', $this->enseignant_id);
		$this->db->where ('d.demande_expiration >', $this->now_epoch);
		$this->db->where ('d.efface', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
             return array();

        return $query->row_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire demandes (plusieurs) pour joindre un groupe pour cet enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_demandes_joindre_groupe()
	{
        $this->db->from     ('enseignants_groupes_demandes as d, ecoles as e, groupes as g');
		$this->db->where    ('d.enseignant_id', $this->enseignant_id);
		$this->db->where    ('d.groupe_id = g.groupe_id');
		$this->db->where    ('g.ecole_id = e.ecole_id');
		$this->db->where    ('d.demande_expiration >', $this->now_epoch);
		$this->db->where    ('d.acceptee', 0);
		$this->db->where    ('d.refusee', 0);
        $this->db->where    ('d.efface', 0);
        $this->db->order_by ('d.demande_epoch', 'desc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
             return array();

        return $query->result_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Demander a joindre un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function demande_joindre_groupe()
    {
        if ( ! $this->groupe['inscription_permise'])
        {
            generer_erreur('GRPJ03', "Ce groupe n'accepte pas les nouvelles inscriptions.");
            exit;
        }

		//
		// Verifier qu'une demande similaire n'existe pas.
		//

		$demande = $this->extraire_demande_joindre_groupe();

        if ( ! empty($demande))
        {
            generer_erreur('GRPJ03', "Une demande a déjà été faite pour joindre ce groupe.");
            exit;
        }

		$data = array(
			'groupe_id'          => $this->groupe_id,
			'enseignant_id'      => $this->enseignant_id,
			'demande_date'       => date_humanize($this->now_epoch, TRUE),
			'demande_epoch' 	 => $this->now_epoch,
			'demande_expiration' => ($this->now_epoch + 60*60*24*30) // 30 jours
		);	

		$this->db->insert('enseignants_groupes_demandes', $data);

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Approuver un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function approuver_enseignant($joindre_id) 
    {
		if ($this->enseignant['niveau'] < $this->config->item('niveaux')['admin_groupe'])
		{
			return FALSE;
		}

		// Verifier que cette demande correspond bien a ce groupe.

        $this->db->from  ('enseignants_groupes_demandes as d');
		$this->db->where ('d.joindre_id', $joindre_id);
		$this->db->where ('d.groupe_id', $this->groupe_id);
        $this->db->where ('d.demande_expiration >', $this->now_epoch);
        $this->db->where ('d.acceptee', 0);
		$this->db->where ('d.efface', 0);

        $query = $this->db->get();

        if ($query->num_rows() != 1)
            return FALSE;

        $row = $query->row_array();

        $this->db->trans_begin();

        $data = array(
            'acceptee' => 1,
            'refusee'  => 0
        );

        if ( ! $row['traitement'])
        {
            $data['traitement']       = 1;
            $data['traitement_epoch'] = $this->now_epoch;
            $data['traitement_date']  = date_humanize($this->now_epoch, TRUE);
        }

        // Mettre a jour l'acceptation.

        $this->db->where ('joindre_id', $joindre_id);
        $this->db->update('enseignants_groupes_demandes', $data);

		// Ajouter l'enseignant au groupe

		$data = array(
			'enseignant_id' => $row['enseignant_id'],
			'groupe_id'     => $this->groupe_id,
			'actif'			=> 1,
			'ajout_date'    => date_humanize($this->now_epoch, TRUE),
			'ajout_epoch'   => $this->now_epoch
		);

		$this->db->insert('enseignants_groupes', $data);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            return FALSE;
        }

        $this->db->trans_commit();

		return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Desapprouver (refuser) un enseignant
     *
     * -------------------------------------------------------------------------------------------- */
    function desapprouver_enseignant($joindre_id) 
    {
		if ($this->enseignant['niveau'] < $this->config->item('niveaux')['admin_groupe'])
		{
			return FALSE;
		}

		// Verifier que cette demande correspond bien a ce groupe.

        $this->db->from  ('enseignants_groupes_demandes as d');
		$this->db->where ('d.joindre_id', $joindre_id);
        $this->db->where ('d.demande_expiration >', $this->now_epoch);
        $this->db->where ('d.refusee', 0);
		$this->db->where ('d.efface', 0);

        $query = $this->db->get();

        if ($query->num_rows() != 1)
             return FALSE;

        $row = $query->row_array();

        // Verifier que cet enseignant n'a pas deja ete approuve par le passe.
        // Il n'est pas possible de refuser un enseignant approuve.

        if ($row['acceptee'])
        {
            return FALSE;
        }

        $this->db->trans_begin();

		// Mettre a jour le refus.

        $data = array(
            'acceptee'         => 0,
            'refusee'          => 1,
            'traitement'       => 1,
            'traitement_epoch' => $this->now_epoch,
            'traitement_date'  => date_humanize($this->now_epoch, TRUE),
            'traitement_expiration_epoch' => $this->now_epoch + 60*60*24*7,
            'traitement_expiration_date'  => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->where ('joindre_id', $joindre_id);
        $this->db->update('enseignants_groupes_demandes', $data);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

		return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Verifier l'existence d'un sous-dmaine
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * TRUE  = le sous-domaine est unique
     * FALSE = le sous-domaine existe deja 
     *
     * -------------------------------------------------------------------------------------------- */
    function verifier_sous_domaine_existant($sous_domaine)
    {
        $query = $this->db->get('groupes');
        
        if ( ! $query->num_rows() > 0)
        {
            return TRUE;
        }

        foreach ($query->result_array() as $row)
        {
            if ($row['sous_domaine'] == $sous_domaine)
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Permission de creer un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function permission_creer_groupe()
    {
        if ($this->enseignant_id == 1)
        {
            return TRUE;
        }

        if ($this->enseignant['privilege'] >= 90)
        {
            return TRUE;
        }
       
        $groupes = $this->lister_groupes();

        if ( ! empty($groupes) && is_array($groupes))
        {
            foreach($groupes as $g)
            {
                if ($g['groupe_id'] == 0)
                {
                    continue;
                }

                if ($g['creation_enseignant_id'] == $this->enseignant_id)
                {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Creer un groupe
     *
     * -------------------------------------------------------------------------------------------- */
    function creer_groupe($post_data = array())
    {
        if (empty($post_data) || ! is_array($post_data))
        {
            return FALSE;
        } 

        // Determiner une valeur par defaut pour la denomination

        if ( ! array_key_exists('denomination', $post_data))
        {
            $post_data['denomination'] = 'groupe';
        }

        $denomination_genres = array(
            'groupe'      => 'M',
            'departement' => 'M',
            'discipline'  => 'F'
        );

        $this->db->trans_begin();

        //
        // Créer le groupe
        //

        $data = array(
            'ecole_id'               => $post_data['ecole_id'],
            'groupe_nom'             => $post_data['nom-groupe'],
            'groupe_nom_court'       => $post_data['nom-court-groupe'],
            'denomination'           => $post_data['denomination'], 
            'denomination_genre'     => $denomination_genres[$post_data['denomination']],
            'sous_domaine'           => $post_data['sous-domaine'],
            'admin_enseignant_id'    => $this->enseignant_id,
            'creation_enseignant_id' => $this->enseignant_id,
            'creation_epoch'         => $this->now_epoch,
            'creation_date'          => date_humanize($this->now_epoch, TRUE),
            'inscription_permise'    => 1
        );

        $this->db->insert('groupes', $data);

        $groupe_id = $this->db->insert_id();

        if (empty($groupe_id) || ! is_numeric($groupe_id))
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        //
        // Associer l'enseignant au groupe
        //

        $data = array(
            'enseignant_id' => $this->enseignant_id,
            'groupe_id'     => $groupe_id,
            'niveau'        => 50,
            'ajout_epoch'   => $this->now_epoch,
            'ajout_date'    => date_humanize($this->now_epoch, TRUE)
        );

        $this->db->insert('enseignants_groupes', $data);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        log_alerte(
            array(
                'code'       => 'GRPNOUV1',
                'desc'       => 'Un nouveau groupe a été créé : ' . $post_data['groupe-nom'] . '(' . $post_data['sous-domaine'] . ').',
                'importance' => 6
            )
        );

        return TRUE;
    }

    /* ------------------------------------------------------------------------
     *
     * Activer un groupe
     *
     * ------------------------------------------------------------------------ */
    public function groupe_activer($groupe_id)
    {
        //
        // Verifier l'existence du groupe
        //

        $this->db->from  ('groupes');
        $this->db->where ('groupe_id', $groupe_id);
        $this->db->where ('actif', 0);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $groupe = $query->row_array();
        
        //
        // Verifier les permissions
        //

        if ( ! ($this->enseignant && $this->enseignant['privilege'] > 89))
        {
            if ($groupe['admin_enseignant_id'] != $this->enseignant_id)
            {
                return FALSE;
            }
        }

        //
        // Mettre a jour
        //

        $data = array(
            'actif' => 1
        );

        $this->db->where ('groupe_id', $groupe_id);
        $this->db->update('groupes', $data);

        return TRUE;
    }
    /* ------------------------------------------------------------------------
     *
     * Desactiver un groupe
     *
     * ------------------------------------------------------------------------ */
    public function groupe_desactiver($groupe_id)
    {
        //
        // Verifier l'existence du groupe
        //

        $this->db->from  ('groupes');
        $this->db->where ('groupe_id', $groupe_id);
        $this->db->where ('actif', 1);
        $this->db->where ('efface', 0);
        $this->db->limit (1);
        
        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $groupe = $query->row_array();
        
        //
        // Verifier les permissions
        //

        if ( ! ($this->enseignant && $this->enseignant['privilege'] > 89))
        {
            if ($groupe['admin_enseignant_id'] != $this->enseignant_id)
            {
                return FALSE;
            }
        }

        //
        // Mettre a jour
        //

        $data = array(
            'actif' => 0
        );

        $this->db->where ('groupe_id', $groupe_id);
        $this->db->update('groupes', $data);

        return TRUE;
    }

}
