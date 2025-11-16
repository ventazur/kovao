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
 * COURS MODEL
 *
 * ============================================================================ */

class Cours_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire le cours
     *
     * -------------------------------------------------------------------------------------------- 
     *
     * - Verifier que le cours demande (cours_id) appartient bien au groupe de l'enseignant.
     *   (sans briser le site car cette fonction est utilisee a plusieurs endroits)
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_cours($options = array())
    {
    	$options = array_merge(
        	array(
                'cours_id'      => NULL,
                'evaluation_id' => NULL
           ),
           $options
       	);
    
        if ( ! $options['cours_id'] && ! $options['evaluation_id'])
        {
            return FALSE;
        }

        $from = 'cours as c';

        //
        // selon le cours_id
        //

        if ($options['cours_id'])
        {
            $this->db->where ('c.cours_id', $options['cours_id']);
        }

        //
        // selon l'evaluation_id
        //

        if ($options['evaluation_id'])
        {
            $from .= ', evaluations as ev';
        
            $this->db->where ('ev.evaluation_id', $options['evaluation_id']);

            $this->db->where ('ev.cours_id = c.cours_id');
        }

        $this->db->from   ($from);
        $this->db->select ('c.*');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire cours_id
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_cours_id($cours_code)
	{
        $this->db->from  ('cours as c');
		$this->db->where ('c.cours_code', $cours_code);
		$this->db->where ('c.groupe_id', $this->groupe_id);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array()['cours_id'];
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des cours
	 *
	 * --------------------------------------------------------------------------------------------
	 *
     * Cette fonction a ete reecrite au complet le 2020-11-20.
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_cours($options = array())
    {
        // @TODO Ajouter le cache car cette function est appelee a chaque requete par l'editeur.

    	$options = array_merge(
            array(
                'enseignant_id' => NULL,    // Lister tous les cours de l'enseignant (de tous ses groupes)
                'groupe_id'     => NULL,    // Lister tous les cours d'un groupe
                'cours_ids'     => NULL,    // Lister les cours dont les cours_ids sont specifies.
                'desuet'        => TRUE     // Inclure les cours desuets (les cours qui ne sont plus offerts)
           ),
           $options
       	);

        //
        // Ne pas extraire les cours desuets
        //

        if ($options['desuet'] == FALSE)
        {
            $this->db->where ('c.desuet', 0);
        }

        //
        // Si les options sont tous NULL, alors on ajuste les options pour
		// lister tous les cours de l'enseignant (incluant ceux de son groupe personnel)
		//

        if ($options['enseignant_id'] === NULL && $options['groupe_id'] === NULL && $options['cours_ids'] === NULL)
        {
            $options['enseignant_id'] = $this->enseignant_id;
        }

        //
        // Lister tous les cours de l'enseignant (de tous ses groupes)
        //
        // (!) Cette option est incompatible avec les options['groupe_id'] et $options['cours_id']
        //

        if ($options['enseignant_id'])
        {
            // Lister tous les groupes de l'enseignant (sans son groupe personnel)
            $groupe_ids = array_keys($this->Groupe_model->lister_groupes2());

            if (in_array(0, $groupe_ids))
                unset($groupe_ids[0]);

            $this->db->where_in ('c.groupe_id', $groupe_ids);
			$this->db->or_where ('c.enseignant_id', $this->enseignant_id);
        }
        else
        {
            //
            // Extraire les cours du groupe demande.
            // S'il s'agit du groupe personnel, verifier que les cours appartiennent a l'enseignant.
            //

            if ($options['groupe_id'] !== NULL)
            {
                $this->db->where('c.groupe_id', $options['groupe_id']);

                if ($options['groupe_id'] == 0)
                {
                    $this->db->where('c.enseignant_id', $this->enseignant_id);
                }       
            }

            //
            // Extraire les cours specifies par leur cour_id.
            //

            if ($options['cours_ids'] !== NULL)
            {
                $this->db->where_in ('c.cours_id', $options['cours_ids']);
            }
        }

        $this->db->from     ('cours as c');
        $this->db->order_by ('c.cours_code', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'cours_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des cours comportant au moins une evaluation
     *
     * >>> Il faut ignorer 'evaluation_id' car on ne peut pas copier une question vers une meme evaluation.
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_cours_avec_evaluation($options = array())
    {
    	$options = array_merge(
        	array(
                'evaluation_id' => NULL,
                'sans_cadenas'  => NULL,
                'public' => 0
           ),
           $options
       	);

        if (empty($options['evaluation_id']))
        {
            return array();
        }

        $evaluation_id = $options['evaluation_id']; 

        // Extraire les evaluations pour obtenir les cours_ids

        $this->db->from ('evaluations as e');

        if ( ! $options['public'])
        {
            $this->db->where ('e.enseignant_id', $this->enseignant_id);
        }

        $this->db->where ('e.groupe_id', $this->groupe_id); // Ajout 2019-06-09
        $this->db->where ('e.public', $options['public']);
        $this->db->where ('e.efface', 0);

        $this->db->where_not_in('e.evaluation_id', array($evaluation_id));

        if ($options['sans_cadenas'] == TRUE)
        {
            $this->db->where ('e.cadenas', 0);
        }

        $query = $this->db->get();
        
        if ( ! $query->num_rows() > 0)
        {
            return array();
        }

        $evaluations = $query->result_array();
        $evaluations = array_keys_swap($evaluations, 'evaluation_id');

        $cours_ids = array();

        foreach($evaluations as $e)
        {
            if ( ! in_array($e['cours_id'], $cours_ids))
                $cours_ids[] = $e['cours_id'];
        }

        $this->db->from     ('cours as c');
        $this->db->where    ('c.actif', 1);
        $this->db->where_in ('c.cours_id', $cours_ids);
        $this->db->order_by ('c.cours_code', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'cours_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste eleves
     *
     * --------------------------------------------------------------------------------------------
     *
     * Les eleves sont classes par cours, puis par groupe.
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_eleves($semestre_id, $options = array())
    {
    	$options = array_merge(
        	array(
                'cours_ids'    => NULL,
				'organisation' => 'groupe'
           ),
           $options
       	);

        $this->db->from ('eleves as e');

        if ($options['cours_ids'])
        {
            $this->db->where_in ('e.cours_id', $options['cours_ids']);
        }

        $this->db->where('e.semestre_id', $semestre_id);
        $this->db->where('e.enseignant_id', $this->enseignant_id);

        if ($options['organisation'] == 'groupe')
        {
            $this->db->order_by('e.cours_id', 'asc');
            $this->db->order_by('e.cours_groupe', 'asc');
        }

		$this->db->order_by('e.eleve_nom', 'asc');
		$this->db->order_by('e.eleve_prenom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

		$data = array();

		if ($options['organisation'] == 'groupe')
		{
			foreach ($query->result_array() as $row)
			{
				$cours_id = $row['cours_id'];
				$groupe   = $row['cours_groupe'];
				$eleve_id = $row['eleve_id'];

				if ( ! array_key_exists($cours_id, $data))
					$data[$cours_id] = array();

				if ( ! array_key_exists($groupe, $data[$cours_id]))
					$data[$cours_id][$groupe] = array();

				$data[$cours_id][$groupe][$eleve_id] = $row;
			}

			return $data;
		}

		return array_keys_swap($query->result_array(), 'eleve_id');
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Liste eleves d'un laboratoire
     *
     * --------------------------------------------------------------------------------------------
     *
     * Cette methode sert a determiner les partenaires de laboratoire.
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_eleves_laboratoire($options = array())
    {
    	$options = array_merge(
            array(
                'semestre_id'   => NULL,
                'enseignant_id' => NULL,
                'cours_id'      => NULL
           ),
           $options
       	);

        if (empty($options['enseignant_id']) || empty($options['semestre_id']) || empty($options['cours_id']))
            return array();

        $this->db->from  ('eleves as e');
        $this->db->where ('e.enseignant_id', $options['enseignant_id']);
        $this->db->where ('e.semestre_id', $options['semestre_id']);
        $this->db->where ('e.cours_id', $options['cours_id']);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste eleves d'une evaluation
     *
     * --------------------------------------------------------------------------------------------
     *
     * ATTN : Seulement les eleves ayant droit a du temps supplementaire
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_eleves_evaluation($options = array())
    {
    	$options = array_merge(
            array(
                'enseignant_id' => NULL,
                'semestre_id'   => NULL,
                'cours_id'      => NULL
           ),
           $options
       	);

        if (empty($options['enseignant_id']) || empty($options['semestre_id']) || empty($options['cours_id']))
            return array();

        $this->db->from  ('eleves as e');
        $this->db->where ('e.enseignant_id', $options['enseignant_id']);
        $this->db->where ('e.semestre_id', $options['semestre_id']);
        $this->db->where ('e.cours_id', $options['cours_id']);
        $this->db->where ('e.temps_supp >', 0);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste eleves pour les filtres
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_eleves_pour_filtres($semestre_id, $options = array())
    {
    	$options = array_merge(
        	array(
                'enseignant_id' => NULL,
                'cours_id'      => NULL,
                'groupe'        => NULL
           ),
           $options
        );

        //
        // Champs obligatoires
        //

        if (empty($options['enseignant_id']))
        {
            log_alerte(
                array(
                    'code'  => 'LEF2434',
                    'desc'  => 'Champ obligatoire manquant'
                )
            );

            return array();
        }

        $this->db->from  ('eleves as e');
        $this->db->where ('e.semestre_id', $semestre_id);
        $this->db->where ('e.enseignant_id', $options['enseignant_id']);

        $this->db->order_by ('e.eleve_nom', 'asc');
        $this->db->order_by ('e.eleve_prenom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

		$eleves = array();

        foreach ($query->result_array() as $row)
        {
            if ($options['cours_id'] && $options['groupe'])
            {
                if ($row['cours_id'] == $options['cours_id'] && $row['cours_groupe'] == $options['groupe'])
                {
                    $eleves[] = $row;
                } 

                continue;
            }

            elseif ($options['cours_id'])
            {
                if ($row['cours_id'] == $options['cours_id'])
                {
                    $eleves[] = $row;
                }

                continue;
            }

            $eleves[] = $row;
        }

        return $eleves;
	}

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire l'etudiant des listes d'eleves
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_eleve($numero_da)
    {
        $this->db->from  ('eleves as e');
        $this->db->where ('e.numero_da', $numero_da);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $query->result_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un etudiant a une liste
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_etudiant_liste($post_data)
    {
        $data = array(
            'enseignant_id' => $this->enseignant_id,
            'semestre_id'   => $post_data['semestre_id'],
            'groupe_id'     => $this->groupe_id,
            'cours_id'      => $post_data['cours_id'],
            'cours_groupe'  => $post_data['groupe'],
            'eleve_nom'     => $post_data['nom'],
            'eleve_prenom'  => $post_data['prenom'],
            'numero_da'     => $post_data['numero_da']
        );

		$this->db->trans_begin();

        $this->db->insert('eleves', $data);

		if ( ! $this->db->affected_rows())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

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
     * Modifier un etudiant d'une liste
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_etudiant_liste($post_data)
    {
        $this->db->trans_begin();

        $temps_supp = $post_data['temps_supp'];

        if ($temps_supp < 0 || $temps_supp > 100)
        {
            $temps_supp = 0;
        }

        $data = array(
            'temps_supp' => str_replace(',', '.', $temps_supp)
        );

        $this->db->where ('eleve_id', $post_data['eleve_id']);
        $this->db->update('eleves', $data);

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
     * Effacer un etudiant d'une liste
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_etudiant_liste($post_data)
    {
        $this->db->trans_begin();

        //
        // Effacer l'etudiant de la liste
        //

        $this->db->where ('eleve_id', $post_data['eleve_id']);
        $this->db->delete('eleves');

        //
        // Dissocier son compte
        //

        $this->db->from  ('etudiants_cours');

        $this->db->where ('enseignant_id', $this->enseignant_id);
        $this->db->where ('semestre_id', $post_data['semestre_id']);
        $this->db->where ('cours_id', $post_data['cours_id']);
        $this->db->where ('cours_groupe', $post_data['groupe']);
        $this->db->where ('numero_da', $post_data['numero_da']);
        $this->db->limit (1);

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $row = $query->row_array();

            $this->db->where  ('id', $row['id']);
            $this->db->delete ('etudiants_cours');
        }

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
     * Effacer liste des etudiants
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_liste_etudiants($semestre_id, $cours_id, $groupe)
    {
        //
        // Extaire tous les numeros DA
        //

        $this->db->from ('eleves');

		$this->db->where('enseignant_id', $this->enseignant['enseignant_id']);
		$this->db->where('semestre_id', $semestre_id);
		$this->db->where('cours_id', $cours_id);
        $this->db->where('cours_groupe', $groupe);

        $query = $this->db->get();

        $eleve_ids  = array();
        $numero_das = array();

        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $r)
            {
                if ( ! in_array($r['eleve_id'], $eleve_ids))
                {
                    $eleve_ids[] = $r['eleve_id'];
                }

                if ( ! in_array($r['numero_da'], $numero_das))
                {
                    $numero_das[] = $r['numero_da'];
                }
            }    
        }

		$this->db->trans_begin();

        //
        // Effacer les etudiants de la liste
        //

        if ( ! empty($eleve_ids))
        {
            $this->db->where_in('eleve_id', $eleve_ids);
            $this->db->delete('eleves');	
        }

        //
        // Dissocier tous les comptes
        //

        if ( ! empty($numero_das))
        {
            $this->db->where('enseignant_id', $this->enseignant['enseignant_id']);
            $this->db->where('semestre_id',   $semestre_id);
            $this->db->where('cours_id',      $cours_id);
            // $this->db->where('cours_groupe',  $groupe); // Afin de tenter de regler un bogue obscur (de France) KIT
            $this->db->where_in ('numero_da', $numero_das);

            $this->db->delete('etudiants_cours');
        }

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
     * Ajouter un cours
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_cours($groupe_id, $post_data)
    {
        //
        // Verifier l'existence de ce cours.
        // Il ne doit pas y avoir deux cours avec le meme code.
        //

        $this->db->from('cours');

        if (empty($groupe_id))
        {
            $this->db->where ('enseignant_id', $this->enseignant_id);
        }
        else
        {
            $this->db->where ('groupe_id', $groupe_id);
        }

        $this->db->where ('cours_code', $post_data['cours_code']);
        $this->db->limit (1);

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            return 'cours_existant';
        }
                                                                                                                                                                                                                                  
        //
        // Ajouter le cours
        //

        $data = array(
            'groupe_id' => $groupe_id
        );

        if (empty($groupe_id))
        {
            $data['enseignant_id'] = $this->enseignant_id;
        }

        foreach($post_data as $k => $v)
        {
            if (empty($v))
            {
                unset($post_data[$k]);
                continue;
            }

            $data[$k] = $v;
        }

        $this->db->insert('cours', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Editer un cours
     *
     * -------------------------------------------------------------------------------------------- */
    function editer_cours($cours_id, $post_data)
    {
        //
        // Verifier l'existence de ce cours
        //

        $this->db->from     ('cours');
        $this->db->where    ('cours_id', $cours_id);
        $this->db->where    ('groupe_id', $post_data['groupe_id']);
        $this->db->limit    (1);

        $query = $this->db->get();

        if ( ! $query->num_rows() > 0)
        {
            return FALSE;
        }

        $cours_data = $query->row_array();

        //
        // Determiner les champs modifies
        //

        $fields = array('cours_code', 'cours_code_court', 'cours_nom', 'cours_nom_court', 'cours_url', 'desuet');
        $data   = array();

        foreach($post_data as $k => $v)
        {
            if ( ! in_array($k, $fields))
                continue;

            if ($v !== $cours_data[$k])
                $data[$k] = $v;
        }

		//
		// Avant de mettre un cours desuet, 
		// il faut verifier qu'aucun enseignant donne ce groupe dans le semestre en vigueur
		//

		if (array_key_exists('desuet', $data) && $data['desuet'] == 1 && $this->semestre_id != 0)
		{
			$this->db->from  ('rel_enseignants_cours');
			$this->db->where ('groupe_id', $this->groupe_id);
			$this->db->where ('cours_id', $cours_id);
			$this->db->where ('semestre_id', $this->semestre_id);
			
			$this->db->limit (1);

			$query = $this->db->get();

			if ($query->num_rows() > 0)
			{
				unset($data['desuet']);
			}
		}	

        //
        // Editer le cours
        //

        // aucun changement detecte
        if (empty($data))
            return FALSE;

        $this->db->where('cours_id', $cours_id);
        $this->db->update('cours', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un cours
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_cours($cours_id, $groupe_id)
    {
        $this->db->where('cours_id', $cours_id);
        $this->db->where('groupe_id', $groupe_id);
        $this->db->delete('cours');

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Filtrer les cours qui ont une ou des evaluations pretes de ceux qui n'ent ont pas.
     *
     * --------------------------------------------------------------------------------------------
     *
     * Prend un liste de cours_ids et retourne seulement les cours_ids qui possedent au moins
     * une evaluation.
     *
     */ 
    function filter_cours_avec_evaluation($options = array())
    {
    	$options = array_merge(
        	array(
                'cours_ids' => NULL
           ),
           $options
       	);

        if (empty($options['cours_ids']))
        {
            return array();
        }

        $this->db->from ('evaluations as e');
        $this->db->where_in('e.cours_id', $options['cours_ids']);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $evaluations = $query->result_array();

        $cours_ids = array();

        foreach($evaluations as $e)
        {
            if ( ! in_array($e['cours_id'], $cours_ids))
            {
                $cours_ids[] = $e['cours_id'];
            }
        }

        return $cours_ids;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister les cours donnes par l'enseignant selectionne
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_cours_selectionnes($enseignant_id, $semestre_id)
    {
        $this->db->from('rel_enseignants_cours rec, cours as c');
        $this->db->where('rec.enseignant_id', $enseignant_id);
        $this->db->where('rec.semestre_id', $semestre_id);
        $this->db->where('rec.cours_id = c.cours_id');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return array_keys_swap($query->result_array(), 'cours_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     *  Selectionner ou deselectionner un cours
     *
     * -------------------------------------------------------------------------------------------- */
    function selection_cours($enseignant_id, $semestre_id, $cours_id)
    {
        if ( ! is_numeric($enseignant_id) || ! is_numeric($semestre_id) || ! is_numeric($cours_id))
        {
            return FALSE;
        }

        //
        // Determination de l'operation a effectuer
        // Est-ce une selection ou deselection ?
        //

        $this->db->from ('rel_enseignants_cours as rec');
        $this->db->where('rec.enseignant_id', $enseignant_id);
        $this->db->where('rec.semestre_id', $semestre_id);
        $this->db->where('rec.cours_id', $cours_id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows())
        {
            //
            // C'est une deselection.
            // 

            $this->db->trans_begin();

            // Deselectionner les evaluations pour ce cours/semestre.

            $this->db->where ('enseignant_id', $enseignant_id);
            $this->db->where ('semestre_id',   $semestre_id);
            $this->db->where ('cours_id',      $cours_id);            
            $this->db->delete('rel_enseignants_evaluations');

            // Deselectionner les cours pour ce semestre.

            $this->db->where ('enseignant_id', $enseignant_id);
            $this->db->where ('semestre_id',   $semestre_id);
            $this->db->where ('cours_id',      $cours_id);
            $this->db->delete('rel_enseignants_cours');

            if ( ! $this->db->affected_rows())
            {
                $this->db->trans_rollback();
                return FALSE;
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return FALSE;
            }

            $this->db->trans_commit();

            return TRUE;
        }

        //
        // Validation de la permission
        // Est-ce que le cours appartient bien au groupe de l'enseignant ?
        //

        $this->db->from ('cours as c');
        $this->db->where('c.cours_id', $cours_id);
        $this->db->where('c.groupe_id', $this->enseignant['groupe_id']);
        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        //
        // Executer la selection.
        //

        $this->db->trans_begin();

        $data = array(
            'enseignant_id' => $this->enseignant['enseignant_id'],
            'groupe_id'     => $this->enseignant['groupe_id'],
            'semestre_id'   => $semestre_id,
            'cours_id'      => $cours_id
        );

        $this->db->insert('rel_enseignants_cours', $data);

        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();

        return TRUE;
    }
}
