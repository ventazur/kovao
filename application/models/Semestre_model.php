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

/* ================================================================================================
 *
 * SEMESTRE MODEL
 *
 * ================================================================================================ */

class Semestre_model extends CI_Model 
{
	function __construct()
	{
        parent::__construct();
		
		$this->semestres_t = $this->config->item('database_tables')['semestres'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Liste des semestres
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_semestres($options = array())
    {
    	$options = array_merge(
            array(
				'groupe_id'     => $options['groupe_id'] ?? $this->groupe_id,
                'enseignant_id' => NULL,
                'exclure_echus' => FALSE,   // Exclure les semestres echues
                //'debut_epoch'   => NULL     // Le semestre debutant a cet epoch ou apres
                'debut_epoch'   => date('U') - 3*365*86400  // Le semestre debutant a cet epoch ou apres
           ),
           $options
       	);

        //
        // Extraire les semestres
        //
        
        $this->db->from  ($this->semestres_t . ' as s');
		$this->db->where ('s.groupe_id', $options['groupe_id']);
        $this->db->where ('s.actif', 1);

		if ($options['groupe_id'] == 0)
		{
			$this->db->where('s.enseignant_id', $options['enseignant_id']);
		}

        //
        // Inclure seulement semestres debutant apres cette date (epoch)
        //

        if ( ! empty($options['debut_epoch']))
        {
            $this->db->where ('s.semestre_debut_epoch >=', $options['debut_epoch']);
        }

        $this->db->order_by('s.semestre_debut_epoch');
        $this->db->order_by('s.semestre_fin_epoch');

        $query = $this->db->get();

        if ( ! $query->num_rows())
		{
            return array();
		}

        $semestres = array_keys_swap($query->result_array(), 'semestre_id');

        //
        // Exclure les semestre echus
        //
        // - Ce sont les semestres qui sont terminees.
        // - Ne pas exclure le semestre actif de l'enseignant.
        //

        if ($options['exclure_echus'])
        {
            $semestre_id_actif = NULL;            

            if (array_key_exists('semestre_id', $this->enseignant) && ! empty($this->enseignant['semestre_id']))
            {
                $semestre_id_actif = $this->enseignant['semestre_id'];
            }

            foreach($semestres as $semestre_id => $s)
            {   
                if ( ! empty($semestre_id_actif) && $s['semestre_id'] == $semestre_id_actif)
                {
                    continue;
                }
                
                if ($s['semestre_fin_epoch'] < $this->now_epoch)
                {
                    unset($semestres[$semestre_id]);
                }
            } 
        }

        return $semestres;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire un semestre
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_semestre($options = array())
    {
    	$options = array_merge(
            array(
                'semestre_id' => NULL, // obligatoire
           ),
           $options
       	);

        if ( ! $options['semestre_id'])
		{
            return FALSE;
		}

        $this->db->from ($this->semestres_t . ' as s');
        $this->db->where('s.semestre_id', $options['semestre_id']);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire des semestres
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_semestres($options = array())
    {
    	$options = array_merge(
            array(
                'semestre_ids' => NULL, // obligatoire
           ),
           $options
       	);

        if ( ! $options['semestre_ids'])
		{
            return FALSE;
		}

        $this->db->from     ($this->semestres_t . ' as s');
        $this->db->where_in ('s.semestre_id', $options['semestre_ids']);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return array_keys_swap($query->result_array(), 'semestre_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire la relation semestres/cours
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_semestres_cours($enseignant_id, $options = array())
    {
    	$options = array_merge(
            array(
                'groupe_id'   => NULL,
                'debut_epoch' => NULL   // semestres debutant a cet epoch ou apres
           ),
           $options
       );

        $this->db->from     ('rel_enseignants_cours as rel, semestres as s');
        $this->db->where_in ('rel.enseignant_id', $enseignant_id);
        $this->db->where    ('rel.semestre_id = s.semestre_id');
        $this->db->where    ('s.semestre_debut_epoch <', $this->now_epoch);  // On ne considere pas les semestres qui debutent dans le futur
        $this->db->order_by ('s.semestre_debut_epoch', 'desc');    

        if ($options['groupe_id'])
        {
            $this->db->where ('rel.groupe_id', $options['groupe_id']);
        }

        if ($options['debut_epoch'])
        {
            $this->db->where ('s.semestre_debut_epoch >=', $options['debut_epoch']);
        }

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $semestres_cours = array();

        foreach($query->result_array() as $r)
        {
            if ( ! array_key_exists($r['semestre_id'], $semestres_cours))
            {
                $semestres_cours[$r['semestre_id']] = array();
            }

            if ( ! in_array($r['cours_id'], $semestres_cours[$r['semestre_id']]))
            {
                $semestres_cours[$r['semestre_id']][] = $r['cours_id'];
            }
        }

        return $semestres_cours;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire dernier semestre
     *
     * Trouver le semestre actif ou le dernier ayant ete actif (mais pas ceux dans le futur).
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_dernier_semestre()
    {
		if ($this->groupe_id == 0)
		{
			$this->db->from     ('semestres as s');
			$this->db->where    ('s.groupe_id', $this->groupe_id);
			$this->db->where    ('s.enseignant_id', $this->enseignant_id);
			$this->db->order_by ('s.semestre_debut_epoch', 'asc');
		}
		else
		{
			$this->db->from     ('semestres as s');
			$this->db->where    ('s.groupe_id', $this->groupe_id);
			$this->db->order_by ('s.semestre_debut_epoch', 'asc');
		}

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        $semestre_id = NULL;
        $semestres = array_keys_swap($query->result_array(), 'semestre_id');

        foreach($semestres as $s)
        {
            if ($semestre_id == NULL)
            {
                if ($s['semestre_debut_epoch'] < date('U'))
                {
                    $semestre_id = $s['semestre_id'];
                }

                continue;
            }

            if ($s['semestre_debut_epoch'] < date('U') && $s['semestre_debut_epoch'] > $semestres[$semestre_id]['semestre_debut_epoch'])
            {
                $semestre_id = $s['semestre_id'];
            }
        }

        return $semestre_id;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Ajouter un nouveau semestre
     *
     * -------------------------------------------------------------------------------------------- */
    function ajouter_semestre($groupe_id, $post_data)
    {
        $semestre_debut_epoch = date_epochize($post_data['semestre_debut_date']);
        $semestre_fin_epoch   = date_epochize($post_data['semestre_fin_date'], 'end');

		//
		// Verifier que la date du debut est anterieure a la date de fin du semestre.
		//

        if ($semestre_debut_epoch >= $semestre_fin_epoch)
        {
            return 'chronologie';
        }

		// Extraire tous les semestres

		$semestres = $this->lister_semestres(
			array(
				'groupe_id'     => $groupe_id,
				'enseignant_id' => $this->enseignant_id
			)
		);

		if ( ! empty($semestres) > 0)
		{
			foreach($semestres as $s)
			{
				// Verifier que le code du semestre est unique.
				
				if (strtolower($s['semestre_code']) == strtolower($post_data['semestre_code']))
				{
					return 'meme_code';
				}

				// Verifier que les dates ne recoupent pas un autre semestre.

				//     |----------|     |----------|     |----------|
				// A:                |----------|
				// B:                      |----------|

				if ($semestre_debut_epoch <= $s['semestre_fin_epoch'] && $semestre_fin_epoch >= $s['semestre_debut_epoch'])
				{
					return 'recoupe';
				}
			}
		}	

        //
        // Ajouter le semestre
        //
        
        $data = array(
            'groupe_id'            => $groupe_id,
			'enseignant_id'        => ($groupe_id == 0 ? $this->enseignant_id : NULL),
            'semestre_nom'         => $post_data['semestre_nom'],
            'semestre_code'        => $post_data['semestre_code'],
            'semestre_debut_date'  => $post_data['semestre_debut_date'],
            'semestre_debut_epoch' => $semestre_debut_epoch,
            'semestre_fin_date'    => $post_data['semestre_fin_date'],
            'semestre_fin_epoch'   => $semestre_fin_epoch
        );

        $this->db->insert('semestres', $data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Modifier un semestre
     *
     * -------------------------------------------------------------------------------------------- */
    function modifier_semestre($semestre_id, $post_data)
    {
        //
        // Verifier que ce semestre peut etre modifie par l'enseignant.
        //
		// (!) Niveau groupe d'au moins 40 requis
		// (!) Ceci n'est pas necessaire pour le groupe 0;

		if ($this->groupe_id != 0)
		{
			$groupe = $this->Groupe_model->extraire_groupe2(
				array(
					'groupe_id' 	=> $this->groupe_id,
					'enseignant_id' => $this->enseignant_id
				)
			);

			if (empty($groupe) || $groupe == FALSE || $groupe['niveau'] < $this->config->item('niveaux')['admin_groupe'])
			{
				return FALSE;
			}
		}

        $semestre = $this->extraire_semestre(array('semestre_id' => $semestre_id));

        if (empty($semestre))
        {
            return FALSE;
        }


        $semestre_debut_epoch = date_epochize($post_data['semestre_debut_date']);
        $semestre_fin_epoch   = date_epochize($post_data['semestre_fin_date'], 'end');

		//
		// Verifier que la date du debut est anterieure a la date de fin du semestre.
		//

        if ($semestre_debut_epoch >= $semestre_fin_epoch)
        {
            return 'chronologie';
        }

		// Extraire tous les semestres

		$semestres = $this->lister_semestres(array('groupe_id' => $this->groupe_id));

		if ( ! empty($semestres) > 0)
		{
			foreach($semestres as $s)
			{
				if ($s['semestre_id'] == $semestre_id)
					continue;

				// Verifier que les dates ne recoupent pas un autre semestre.

				//     |----------|     |----------|     |----------|
				// A:                |----------|
				// B:                      |----------|

				if ($semestre_debut_epoch <= $s['semestre_fin_epoch'] && $semestre_fin_epoch >= $s['semestre_debut_epoch'])
				{
					return 'recoupe';
				}
			}
		}	

        //
        // Conserver seulement les champs modifies. 
        //
        
        foreach($semestre as $k => $v)
        {
            if (array_key_exists($k, $post_data))
            {
                if ($post_data[$k] === $v)
                    unset($post_data[$k]);
            } 
        }

        if ( ! count($post_data))
		{
            return 'aucun_changement';
		}
        
		unset($post_data['groupe_id'], $post_data['semestre_id']);

        //
        // Refaire les date 'epoch' si necessaire.
        //

        if (array_key_exists('semestre_debut_date', $post_data))
		{
            $post_data['semestre_debut_epoch'] = $semestre_debut_epoch;
		}

        if (array_key_exists('semestre_fin_date', $post_data))
		{
            $post_data['semestre_fin_epoch'] = $semestre_fin_epoch;
		}

        $this->db->where ('semestre_id', $semestre_id);
        $this->db->update('semestres', $post_data);

        if ( ! $this->db->affected_rows())
        {
            return FALSE;
        }

        return TRUE;
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Effacer un semestre
     *
     * -------------------------------------------------------------------------------------------- */
    function effacer_semestre($semestre_id)
    {
        //
        // Verifier que ce semestre peut etre efface par l'enseignant.
        //
		// (!) Niveau groupe d'au moins 40 requise
		// (!) Ceci n'est pas necessaire pour le groupe 0;

		if ($this->groupe_id != 0)
		{
			$groupe = $this->Groupe_model->extraire_groupe2(
				array(
					'groupe_id' 	=> $this->groupe_id,
					'enseignant_id' => $this->enseignant_id
				)
			);

			if (empty($groupe) || $groupe == FALSE || $groupe['niveau'] < $this->config->item('niveaux')['admin_groupe'])
			{
				return FALSE;
			}
		}

		// Si le semestre contient des soumissions, refuser l'effacement.

        if ($this->Semestre_model->semestre_contient_soumissions($semestre_id))
        {
			return 'contient_soumissions';
		}

        $semestre = $this->extraire_semestre(array('semestre_id' => $semestre_id));

        $this->db->trans_begin();

        // Effacer les relations avec ce semestre
        // -> rel_enseignants_cours
        // -> rel_enseignants_evaluations

        $this->db->where('semestre_id', $semestre_id);
		$this->db->delete('rel_enseignants_cours');	

        $this->db->where('semestre_id', $semestre_id);
		$this->db->delete('rel_enseignants_evaluations');	

        $this->db->where('semestre_id', $semestre_id);
		$this->db->delete('semestres');	

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
     * Selectionner ou Deselectionner un semestre
     *
     * -------------------------------------------------------------------------------------------- */
    function selection_semestre($enseignant_id, $semestre_id)
    {
        if ( ! is_numeric($enseignant_id) || ! is_numeric($semestre_id))
        {
            return FALSE;
        }

		//
		// Verifier qu'une entree dans enseignant/groupe existe
        // et
        // Determination de l'operation a effectuer
        // Est-ce une selection ou deselection ?
        //

        $this->db->from  ('enseignants_groupes as eg');
        $this->db->where ('eg.enseignant_id', $enseignant_id);
		$this->db->where ('eg.groupe_id', $this->groupe_id);
        $this->db->limit (1);

        $query = $this->db->get();

		if ( ! $query->num_rows())
		{
			log_alerte(
				array(
					'code' => 'RQQ100',
					'desc' => "Entrée manquante dans enseignants_groupes pour l'enseignant_id " . $enseignant_id . " du groupe " . $this->groupe_id . "."
				)
			);

			generer_erreur('RQQ101', "Une information est manquante concernant votre compte. Le support technique a été avisé.");
			return;
		}

		$row = $query->row_array();

		$semestre_id_selectionne = $row['semestre_id'];

		$this->db->trans_begin();

		// Deselectionner toutes les evaluations se rapportant a ce semestre.

		if ( ! empty($semestre_id_selectionne))
		{
			$this->db->where ('enseignant_id', $enseignant_id);
			$this->db->where ('semestre_id',   $semestre_id_selectionne);
			$this->db->delete('rel_enseignants_evaluations');
		}

		//
		// C'est une deselection.
		//

		if ($semestre_id_selectionne == $semestre_id)
		{
			$data = array(
				'semestre_id' => NULL
			);

			// Remettre a NULL le semestre selectionne.

			$this->db->where ('enseignant_id', $enseignant_id);
			$this->db->where ('groupe_id', $this->groupe_id);
			$this->db->update('enseignants_groupes', $data);

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
        // C'est une selection.
        //

        //
        // Validation de la permission
        // Est-ce que le semestre appartient bien a ce groupe ? ou a cet enseignant s'il s'agit du groupe 0 ?
        //

		if ($this->groupe_id == 0)
		{
			$this->db->from  ('semestres as s');
			$this->db->where ('s.semestre_id',   $semestre_id);
			$this->db->where ('s.groupe_id',     $this->groupe_id);
			$this->db->where ('s.enseignant_id', $enseignant_id);
			$this->db->limit (1);
		}
		else
		{
			$this->db->from  ('semestres as s');
			$this->db->where ('s.semestre_id', $semestre_id);
			$this->db->where ('s.groupe_id',   $this->groupe_id);
			$this->db->limit (1);
		}

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        //
        // Executer la selection.
        //

        $data = array(
            'semestre_id' => $semestre_id,
        );

        $this->db->where  ('enseignant_id',       $enseignant_id);
		$this->db->where  ('groupe_id',           $this->groupe_id);
        $this->db->update ('enseignants_groupes', $data);

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
     * Semestre en vigueur
     *
     * -------------------------------------------------------------------------------------------- */
    function semestre_en_vigueur($groupe_id = NULL, $enseignant_id = NULL)
    {
        if ($groupe_id === NULL || ! is_numeric($groupe_id))
        {
            $groupe_id = $this->groupe_id;
        }

        $this->db->from ('semestres as s');

        $this->db->where('s.groupe_id', $groupe_id);

		if ($groupe_id == 0)
		{
        	$this->db->where('s.enseignant_id', $enseignant_id);
		}

        $this->db->where('s.semestre_fin_epoch >', $this->now_epoch);
        $this->db->where('s.semestre_debut_epoch <', $this->now_epoch);
        $this->db->where('s.actif', 1);

        $this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Est-ce que le semestre contient des soumissions ?
     *
     * -------------------------------------------------------------------------------------------- */
	function semestre_contient_soumissions($semestre_id)
	{
        $this->db->from ('soumissions as s');
        $this->db->where('s.groupe_id', $this->groupe_id);
        $this->db->where('s.semestre_id', $semestre_id);
        $this->db->where('s.efface', 0);
		$this->db->limit(1);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

		return TRUE;
	}

}
