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
 * ECOLE MODEL
 *
 * ============================================================================ */

class Ecole_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();

		$this->ecoles_t = $this->config->item('database_tables')['ecoles'];
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Lister les ecoles
     *
     * -------------------------------------------------------------------------------------------- */
    function lister_ecoles($options = array())
    {
    	$options = array_merge(
        	array(
                'ecole_ids' => array(),
                'personnel' => TRUE
           ),
           $options
       	);

        $this->db->from  ($this->ecoles_t . ' as e');
        $this->db->where ('e.ecole_id !=', 0);

		if ( ! empty($options['ecole_ids']))
		{
			$this->db->where_in('e.ecole_id', $options['ecole_ids']);
		}

        $this->db->where    ('e.actif', 1);
        $this->db->order_by ('e.ecole_nom', 'asc');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $ecoles = array();

        if (array_key_exists('personnel', $options) && $options['personnel'])
        {
            $ecoles[] = $this->config->item('ecole_www');
        }
        
        $ecoles = array_merge($ecoles, $query->result_array());

        return array_keys_swap($ecoles, 'ecole_id');
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire une ecole
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ecole($options = array())
    {
    	$options = array_merge(
        	array(
                'ecole_id'      => NULL,
                'groupe_id'     => NULL,
                'evaluation_id' => NULL
           ),
           $options
       	);
    
        if ( ! $options['ecole_id'] && ! $options['groupe_id'] && ! $options['evaluation_id'])
        {
            $options['ecole_id'] = $this->ecole_id;
        }

        $from = 'ecoles as ec';

        if ($options['ecole_id'])
        {
            $this->db->where ('ec.ecole_id', $options['ecole_id']);
        }

        if ($options['groupe_id'])
        {
            $from .= ', groupes as g';

            $this->db->where('g.groupe_id', $options['groupe_id']);

            $this->db->where('ec.ecole_id = g.ecole_id');
        }

        if ($options['evaluation_id'])
        {
            $from .= ', evaluations as ev, cours as c, groupes as g';
            
            $this->db->where ('ev.evaluation_id', $options['evaluation_id']);

            $this->db->where ('ev.cours_id = c.cours_id');
            $this->db->where ('c.groupe_id = g.groupe_id');
            $this->db->where ('g.ecole_id = ec.ecole_id');
        }

        $this->db->from ($from);
        $this->db->select('ec.*');

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /* --------------------------------------------------------------------------------------------
     *
     * Extraire les adresses ips d'une ecole
     *
     * -------------------------------------------------------------------------------------------- */
    function extraire_ecole_ips($ecole_id)
    {
        $this->db->from ('ecoles_ips as ec');
        $this->db->where ('ec.ecole_id', $ecole_id);

        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $ips = array();

        foreach($query->result_array() as $r)
        {
            if ( ! in_array($r['adresse_ip'], $ips))
            {
                $ips[] = $r['adresse_ip'];
            }
        }

        return $ips;
	}
}
