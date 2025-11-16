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

/* --------------------------------------------------------------------------------------------
 *
 * PERMISSIONS HELPER
 *
 * --------------------------------------------------------------------------------------------
 *
 * Un helper pour gerer toutes les permissions a travers le site.
 *
 * -------------------------------------------------------------------------------------------- */

function permis($service)
{
    if (empty($service))
    {
        return FALSE;
    }

    $CI =& get_instance();

    if ( ! isset($CI->enseignant) || ! array_key_exists('niveau', $CI->enseignant))
    {
        return FALSE;
    }

    //
    // Extraire tous les niveaux existants
    //

    $niveaux = $CI->config->item('niveaux'); // Tous les niveaux configures

    //
    // Extraire le niveau d'un enseignant
    //

    $niveau  = $CI->enseignant['niveau'];    // Le niveau de l'enseignant

    if ($niveau < 1)
    {
        // L'enseignant ne peut avoir acces a ce groupe.
        return FALSE;
    }

    //
    // Permettre a l'administrateur d'avoir tous les acces.
    //

    if ($CI->enseignant['privilege'] > 89)
    {
        $niveau = 100;
    }
    
    // 
    // Determiner le niveau minimum de chaque categorie d'acces.
    //

    $niveau_minimum = 100;

    switch($service)
    {
        //
        // Administration
        //

        case 'admin' :
            $niveau_minimum = $niveaux['admin'];
            break;

        case 'devel' :
            $niveau_minimum = $niveaux['devel'];

        case 'admin_ecole' :
            $niveau_minimum = $niveaux['admin_ecole'];
            break;

        case 'admin_lab' :
            $niveau_minimum = $niveaux['admin_lab'];
            break;

        case 'admin_groupe' :
        case 'admin_editer_enseignant' :
            $niveau_minimum = $niveaux['admin_groupe'];
            break;

        //
        // Evaluations : Editeur
        //

        case 'editeur' :
        case 'editeur_effacer' :
            $niveau_minimum = $niveaux['admin_evaluations'];
        break;

        default:
            return FALSE;
    }

    //
    // Verifier si le niveau de l'enseignant rencontre le niveaum minimum
    // pour le service demande.
    //

    if ($niveau >= $niveau_minimum)
    {
        return TRUE;
    }

    return FALSE;
}
