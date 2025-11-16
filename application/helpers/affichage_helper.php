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
 * AFFICHAGE HELPER
 *
 * ============================================================================ */

/* ----------------------------------------------------------------------------
 *
 * (HTML) TEXTE : IN
 *
 * ----------------------------------------------------------------------------
 *
 * Enregistrer le texte.
 *
 * ---------------------------------------------------------------------------- */
function _html_in($texte, $options = array())
{
	//
	// Options
	//

	$options = array_merge(
    	array(
            'strip_tags'   => FALSE,
            'htmlentities' => FALSE,
        	'json' 		   => FALSE
     	),
     	$options
  	);

	//
	// Preparer le texte pour l'enregistrement
	//

	$texte = trim($texte);

	if ($options['strip_tags'])
	{
		$texte = strip_tags($texte);
	}
	else
	{
		$texte = verifier_tags($texte);
    }

    if ($options['htmlentities'])
    {
        $texte = htmlentities($texte);
    }

    if ($options['json'])
    {
        $texte = json_encode($texte);
    }

    return $texte;
}

/* ----------------------------------------------------------------------------
 *
 * (HTML) TEXTE : EDIT
 *
 * ----------------------------------------------------------------------------
 *
 * Editer le texte.
 *
 * ---------------------------------------------------------------------------- */
function _html_edit($texte)
{
    if (empty($texte))
       return $texte;

    if (json_decode($texte) !== NULL)
    {
        $texte = json_decode($texte);
    }

    $texte = html_entity_decode($texte); 

    return $texte;
}

/* ----------------------------------------------------------------------------
 *
 * (HTML) TEXTE : OUT
 *
 * ----------------------------------------------------------------------------
 *
 * Montrer le texte a l'ecran
 *
 * ---------------------------------------------------------------------------- */
function _html_out($texte, $options = array())
{
	//
	// Options
	//

	$options = array_merge(
    	array(
			'limite' => FALSE, // Limite de caracteres (integer)
     	),
     	$options
  	);

	//
	// Preparation de la sortie
	//

    if ( ! empty($texte) && json_decode($texte) !== NULL)
    {
        $texte = json_decode($texte);
    }

    $texte = html_entity_decode($texte); 
    $texte = nl2br($texte);
	$texte = filter_symbols($texte);

    return $texte;
}
