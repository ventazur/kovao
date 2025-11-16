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
 * SETTINGS MODEL
 *
 * ================================================================================================ */

Class Settings_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();

        $this->parametres_table = 'parametres';
    }

    /* --------------------------------------------------------------------------------------------
	 *
	 * INITIALISATION
     * 
	 * -------------------------------------------------------------------------------------------- */
    function initialisation()
    {
        $parametres = $this->_chargement_parametres();

        return $this->_set_config_items($parametres);
    }

    /* --------------------------------------------------------------------------------------------
	 *
     * Chargement des parametres
     * 
	 * -------------------------------------------------------------------------------------------- */
    function _chargement_parametres()
    {
        $this->db->from($this->parametres_table);
        
        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        $parametres = $this->_arrayize($query->result_array());

        return $parametres;
    }

    /* --------------------------------------------------------------------------------------------
	 *
	 * SET CONFIG ITEMS
     * 
     * --------------------------------------------------------------------------------------------
	 *
     * Etablir les items de la configuration.
     *
	 * -------------------------------------------------------------------------------------------- */
	function _set_config_items($parametres)
	{
        foreach($parametres as $clef => $valeur)
        {
            $this->config->set_item($clef, $valeur);
        }

        return TRUE;
	}

    /* --------------------------------------------------------------------------------------------
	 *
	 * _ARRAYIZE
     * 
     * --------------------------------------------------------------------------------------------
     *
     * Transformer le resultat de la requete en un tableau.
	 *
	 * -------------------------------------------------------------------------------------------- */
    function _arrayize($parametres = array())
    {
        $resultats = array();

        foreach($parametres as $parametre)
        {
            $clef   = $parametre['clef'];
            $valeur = $parametre['valeur'];

            $resultats[$clef] = $valeur;
        }

        return $resultats;
    }
    
    /* --------------------------------------------------------------------------------------------
	 *
	 * Extraire les parametres
     * 
	 * -------------------------------------------------------------------------------------------- */
    function extraire_parametres()
    {
        $this->db->from($this->parametres_table);
        
        $query = $this->db->get();

        if ( ! $query->num_rows())
        {
            return array();
        }

        return $this->_arrayize($query->result_array());
    }

    /* --------------------------------------------------------------------------------------------
	 *
	 * Sauvegarder les parametres
     * 
     * --------------------------------------------------------------------------------------------
	 *
     * Mettre a jour les parametres.
     *
	 * -------------------------------------------------------------------------------------------- */
    function sauvegarder_parametres($post_data)
    {
        $data = array();

        foreach($post_data as $k => $v)
        {
            $data[] = array(
                'clef'   => $k,
                'valeur' => $v
            );
        }

        if ( ! empty($data))
        {
            $this->db->update_batch($this->parametres_table, $data, 'clef'); 
        }

        return;
    }
}
