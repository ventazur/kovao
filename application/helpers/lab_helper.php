<?php

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
 * LAB HELPER
 *
 * ============================================================================ */

/* --------------------------------------------------------------------
 *
 * Titre des tableaux
 *
 * -------------------------------------------------------------------- */
function lab_tableau_titre($options = array())
{
    $CI =& get_instance();

    $champs_obligatoires    = array('tableau_no', 'v');
    $champs_obligatoires_e  = array('tableau_points');
    $champs_obligatoires_c  = array('tableau_data');

	$options = array_merge(
        array(
            // champs obligatoires
            
            'tableau_no'    => NULL,
            'v'             => 1,    // version

            // champs obligatoires (evaluation)

            'tableau_points' => array(),

            // champs obligatoires (consulter)

            'tableau_data' => array()
   		),
   		$options
    );

    // verifier les champs obligatoires

    foreach($champs_obligatoires as $c)
    {
        if ( ! array_key_exists($c, $options))
            return;

        if (empty($options[$c]))
            return;
    }
    
    $html = '';

    //
    // Evaluation
    //

    if ($CI->current_controller == 'evaluation')
    {
        foreach($champs_obligatoires_e as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }

	    extract($options);
        
        $html .= lab_f_tableau_complet($tableau_no, $tableau_points, $v);
    }

    //
    // Consulter
    //

    if ($CI->current_controller == 'consulter' || $CI->current_controller == 'corrections')
    {
        foreach($champs_obligatoires_c as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }

	    extract($options);

        $html .= lab_c_tableau($tableau_no, $tableau_data, $v);
    }

    return $html;
}

/* --------------------------------------------------------------------
 *
 * Evaluation > Titre des tableaux
 *
 * -------------------------------------------------------------------- */
function lab_f_tableau($tableau_no, $tableau_points, $v = 1)
{
	// $v : version

	$tableau_points_f = format_nombre($tableau_points);
	$tableau_points_s = $tableau_points > 1 ? 's' : '';

	$html = <<<EOD

	   <div class="col-8">
			Tableau $tableau_no

			<span id="est-sauvegarde$tableau_no" class="est-sauvegarde">
				<i class="bi bi-floppy"></i>
			</span>
			<span id="est-pas-sauvegarde$tableau_no" class="est-pas-sauvegarde">
				<i class="bi bi-floppy"></i> &times;
			</span>
		</div> <!-- .col-8 -->

		<div class="col-4">
			<div class="question-points float-right">
				<span id="tableau-points-obtenus-$tableau_no" class="tableau-points-obtenus d-none"></span>
				$tableau_points_f point$tableau_points_s
			</div>
		</div>

	EOD;

	return $html;
}

/* --------------------------------------------------------------------
 *
 * Evaluation > Titre des tableaux (COMPLET)
 *
 * --------------------------------------------------------------------
 *
 * La version 'complete' inclue les tags pour envelopper le tableau,
 * i.e. les 'div'.
 *
 * -------------------------------------------------------------------- */
function lab_f_tableau_complet($tableau_no, $tableau_points, $v = 1)
{
	// $v : version

	$tableau_points_f = format_nombre($tableau_points);
	$tableau_points_s = $tableau_points > 1 ? 's' : '';

	$html = <<<EOD

		<div class="evaluation-tableau-titre">
		<div class="row no-gutters">

	   	<div class="col-8">
			Tableau $tableau_no

			<span id="est-sauvegarde$tableau_no" class="est-sauvegarde">
				<i class="bi bi-floppy"></i>
			</span>
			<span id="est-pas-sauvegarde$tableau_no" class="est-pas-sauvegarde">
				<i class="bi bi-floppy"></i> &times;
			</span>
		</div> <!-- .col-8 -->

		<div class="col-4">
			<div class="question-points float-right">
				<span id="tableau-points-obtenus-$tableau_no" class="tableau-points-obtenus d-none"></span>
				$tableau_points_f point$tableau_points_s
			</div>
		</div>

		</div> <!-- .row -->
		</div> <!-- .evaluation-tableau-titre -->

	EOD;

	return $html;
}

/* --------------------------------------------------------------------
 *
 * Laboratoires - Les champs (V3)
 *
 * --------------------------------------------------------------------
 * 
 * table > tr > td avec valeur, incertitude sur valeur, nsci et unites
 *
 * -------------------------------------------------------------------- */
function lab_champs($options = array())
{
    $CI =& get_instance();

    $champs_obligatoires_e  = array('lab_prefix', 'lab_valeurs', 'traces');
    $champs_obligatoires_c  = array('lab_valeurs', 'lab_points_champs');

    $html = '';

    // verifier les champs obligatoires

    if ( ! array_key_exists('champ', $options) && ! array_key_exists('champ_d', $options))
        return;

    if (empty($options['champ']) && empty($options['champ_d']))
        return;

    if ($CI->current_controller == 'evaluation')
    {
        foreach($champs_obligatoires_e as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }

        extract($options);

        $html .= lab_f_champ3(
            array(
                'lab_prefix'    => $lab_prefix ?? NULL,
                'lab_valeurs'   => $lab_valeurs ?? array(),
                'traces'        => $traces ?? array(),

                // l'un ou l'autre obligatoire

                'champ'         => $champ ?? NULL,
                'champ_d'       => $champ_d ?? NULL,

                // facultatifs

                'nsci'	        => $nsci ?? FALSE, 
                'nsci_v'        => $nsci_v ?? NULL,  
                'unites'        => $unites ?? FALSE,
                'unites_v'      => $unites_v ?? NULL,  

                'type'          => $type ?? 'input',
                'select_choix'  => $select_choix ?? array(),

                'prepend'       => $prepend ?? NULL,
                'align'         => $align ?? 'right',
                'classes'       => $classes ?? NULL
            )
        );
    }

    if ($CI->current_controller == 'consulter' || $CI->current_controller == 'corrections')
    {
        foreach($champs_obligatoires_c as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }

        extract($options);

        $html .= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ ?? NULL, $champ_d ?? NULL, 
            array(
                'nsci'          => $nsci ?? FALSE,
                'nsci_v'        => $nsci_v ?? NULL,
                'unites'        => $unites ?? FALSE,
                'unites_v'      => $unites_v ?? NULL
            )
        );
    }

    return $html;
}

/* --------------------------------------------------------------------
 *
 * Laboratoires - Les champs pour l'evaluation (V3)
 *
 * --------------------------------------------------------------------
 * 
 * table > tr > td avec valeur, incertitude sur valeur, nsci et unites
 *
 * -------------------------------------------------------------------- */
function lab_f_champ3($options = array())
{
	$options = array_merge(
        array(

            // obligatoires

            'lab_prefix'    => NULL,
            'lab_valeurs'   => array(),
            'traces'        => array(),

            // l'un ou l'autre obligatoire

            'champ'         => NULL,
            'champ_d'       => NULL,

            // facultatifs

            'nsci'	        => FALSE,    // montrer ou ne pas montrer la nsci
            'nsci_v'        => NULL,     // specifier la valeur de nsci
            'unites'        => FALSE,    // montrer ou ne pas montrer les unites
            'unites_v'      => NULL,     // specifier la valeur de unites

            'type'          => 'input',
            'select_choix'  => array(),

            'prepend'       => NULL,
            'align'         => 'right',  // text align dans le input
            'classes'       => NULL      // classes a ajouter separees par des virgules (,)
   		),
   		$options
	);
	extract($options);

	$traces[$champ]   = $traces[$champ] ?? NULL;
	$traces[$champ_d] = $traces[$champ_d] ?? NULL;

    if ( ! empty($classes))
    {
        $classes = implode(' ', explode(',', str_replace(' ', '', $classes)));
    }

    $html = '<div class="input-group">';

	if (empty($champ_d) && ! $unites)
	    $align = 'center';

    if ($prepend)
    {
        $html .= '<div class="input-group-prepend"><div class="input-group-text">' . $prepend . '</div></div>';
    }
        
	if ($champ)
    {
        if ($type === 'select' && ! empty($select_choix))
        {
            $html .= '<select class="form-control text-' . $align . ' ' . $classes . '" name="' . $lab_prefix . '-' . $champ . '" id="' . $lab_prefix . '-' . $champ .'">';
            $html .= '<option value=""></option>';

            foreach($select_choix as $choix)
            {
                $html .= '<option value="' . $choix . '"' . (@$traces[$champ] == $choix ? ' selected' : '') . '>' . $choix . '</option>';
            }

            $html .= '</select>';
        }
        else
        {
            $html .= <<<EOD
                <input type="text" class="form-control text-$align $classes" name="$lab_prefix-$champ" id="$lab_prefix-$champ"
                    value="$traces[$champ]">
            EOD;
        }
	}

	if ($champ_d)
    {
        if ($champ)
        {
            $html .= '<span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>';
        }
        else
        {
            $html .= '<div class="input-group-prepend"><div class="input-group-text">±</div></div>';
        }

		$html .= <<<EOD
			<input type="text" class="form-control $classes" name="$lab_prefix-$champ_d" id="$lab_prefix-$champ_d"
				value="$traces[$champ_d]" style="text-align: left">
		EOD;
	}

	if ($nsci || $unites)
    {
		if (empty($champ))
        {
			$nsci_v   = empty($nsci_v) ? ($lab_valeurs[$champ_d]['nsci'] ?? NULL) : $nsci_v;
			$unites_v = empty($unites_v) ? ($lab_valeurs[$champ_d]['unites'] ?? NULL) : $unites_v;

            // Si 'unites_v' et 'nsci_v' sont nulles, alors
            // verifier si le champ (qui n'est pas une incertitude) possede ces informations.

			$champ_tmp = str_replace('_d', '', $champ_d);

            if (empty($nsci_v))
            {
			    $nsci_v = $lab_valeurs[$champ_tmp]['nsci'] ?? NULL;
            }

            if (empty($unites_v))
            {
                $unites_v = $lab_valeurs[$champ_tmp]['unites'] ?? NULL;
            }
		}
		else
        {
			$nsci_v   = empty($nsci_v) ? ($lab_valeurs[$champ]['nsci'] ?? NULL) : $nsci_v;
			$unites_v = empty($unites_v) ? ($lab_valeurs[$champ]['unites'] ?? NULL) : $unites_v;
		}

		if ($nsci_v || $unites_v)
		{
			$html .= '<div class="input-group-append">';
			$html .= '<div class="input-group-text">';

			if ($nsci && $nsci_v)
			{
				$html .= '&times;10<sup>' . $nsci_v . '</sup>';
			}

			if ($unites && $unites_v)
			{
				$html .= ($nsci ? '&nbsp;' : '') . $unites_v;
			}

			$html .= '</div></div>';
		}
	}

    $html .= <<<EOD
        </div> <!-- .input-group -->
    EOD;

	return $html;
}

/* --------------------------------------------------------------------
 *
 * Evaluation - Montrer les champs (V2)
 *
 * --------------------------------------------------------------------
 * 
 * table > tr > td avec valeur, incertitude sur valeur, nsci et unites
 *
 * -------------------------------------------------------------------- */
function lab_f_champ2($lab_prefix, $lab_valeurs, $champ, $champ_d, $traces = array(), $options = array())
{
	$options = array_merge(
        array(
            'align'    => 'right',  // text align dans le input
            'classes'  => NULL,     // classes a ajouter separees par des virgules (,)
            'nsci'	   => FALSE,    // montrer ou ne pas montrer la nsci
            'nsci_v'   => NULL,     // specifier la valeur de nsci
            'unites'   => FALSE,    // montrer ou ne pas montrer les unites
            'unites_v' => NULL      // specifier la valeur de unites
   		),
   		$options
	);
	extract($options);

	$traces[$champ]   = $traces[$champ] ?? NULL;
	$traces[$champ_d] = $traces[$champ_d] ?? NULL;

    if ( ! empty($classes))
    {
        $classes = implode(' ', explode(',', str_replace(' ', '', $classes)));
    }

	$html = '<div class="input-group">';

	if (empty($champ_d) && ! $unites)
	    $align = 'center';

	if ($champ)
	{
		$html .= <<<EOD
			<input type="text" class="form-control text-$align $classes" name="$lab_prefix-$champ" id="$lab_prefix-$champ"
				value="$traces[$champ]">
		EOD;
	}

	if ($champ_d)
	{
		if ($champ)
		{
			$html .= '<span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>';
		}
		else
		{
			$html .= '<div class="input-group-prepend"><div class="input-group-text">±</div></div>';
		}

		$html .= <<<EOD
			<input type="text" class="form-control $classes" name="$lab_prefix-$champ_d" id="$lab_prefix-$champ_d"
				value="$traces[$champ_d]" style="text-align: left">
		EOD;
	}

	if ($nsci || $unites)
    {
		if (empty($champ))
        {
			$nsci_v   = empty($nsci_v) ? ($lab_valeurs[$champ_d]['nsci'] ?? NULL) : $nsci_v;
			$unites_v = empty($unites_v) ? ($lab_valeurs[$champ_d]['unites'] ?? NULL) : $unites_v;

            // Si 'unites_v' et 'nsci_v' sont nulles, alors
            // verifier si le champ (qui n'est pas une incertitude) possede ces informations.

			$champ_tmp = str_replace('_d', '', $champ_d);

            if (empty($nsci_v))
            {
			    $nsci_v = $lab_valeurs[$champ_tmp]['nsci'] ?? NULL;
            }

            if (empty($unites_v))
            {
                $unites_v = $lab_valeurs[$champ_tmp]['unites'] ?? NULL;
            }
		}
		else
        {
			$nsci_v   = empty($nsci_v) ? ($lab_valeurs[$champ]['nsci'] ?? NULL) : $nsci_v;
			$unites_v = empty($unites_v) ? ($lab_valeurs[$champ]['unites'] ?? NULL) : $unites_v;
		}

		if ($nsci_v || $unites_v)
		{
			$html .= '<div class="input-group-append">';
			$html .= '<div class="input-group-text">';

			if ($nsci && $nsci_v)
			{
				$html .= '&times;10<sup>' . $nsci_v . '</sup>';
			}

			if ($unites && $unites_v)
			{
				$html .= ($nsci ? '&nbsp;' : '') . $unites_v;
			}

			$html .= '</div></div>';
		}
	}

	$html .= <<<EOD
		</div> <!-- .input-group -->
	EOD;

	return $html;
}

/* --------------------------------------------------------------------
 *
 * Laboratoires - Montrer les tags
 *
 * -------------------------------------------------------------------- */
function lab_tags($options = array())
{
    $CI =& get_instance();

    $champs_obligatoires    = array();
    $champs_obligatoires_e  = array('montre_tags', 'lab_points');
    $champs_obligatoires_c  = array('lab_points_champs');

	$options = array_merge(
        array(
            'champ'   => NULL,
            'champ_d' => NULL,
            'classes' => 'mt-1',
            'inline'  => FALSE,
   		),
   		$options
    );

    // verifier les champs obligatoires

    if ( ! empty($champs_obligatoires))
    {
        foreach($champs_obligatoires as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }
    }

    if ( ! array_key_exists('champ', $options) && ! array_key_exists('champ_d', $options))
        return;

    if (empty($options['champ']) && empty($options['champ_d']))
        return;

    $html = '';

    if ($CI->current_controller == 'evaluation')
    {
        foreach($champs_obligatoires_e as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }

        extract($options);

        $html .= lab_montre_champ($montre_tags, $lab_points, $champ, $champ_d, $options);
    }

    if ($CI->current_controller == 'consulter')
    {
        foreach($champs_obligatoires_c as $c)
        {
            if ( ! array_key_exists($c, $options))
                return;

            if (empty($options[$c]))
                return;
        }

        extract($options);

        $html .= lab_montrer_corr(
            $champ, 
            $lab_points_champs, 
            array(
                'classes' => $options['classes'] ?? NULL,
                'inline'  => $options['inline'] ?? FALSE
            )
        );
    }

    return $html;
}

/* --------------------------------------------------------------------
 *
 * Laboratoires - Montrer les tags
 *
 * -------------------------------------------------------------------- */
function lab_montre_champ($afficher, $lab_points, $champ, $champ_d = NULL, $options = array())
{
	$options = array_merge(
        array(
            // 'champ'   => NULL,
            // 'champ_d' => NULL,
            'classes' => 'mt-1',
            'inline'  => FALSE
   		),
   		$options
    );

    extract($options);

    //$classes = $options['classes'];
    //$inline  = $options['inline'];

	$html = '';

    //
    // inline
    //

    if ($inline)
    {
        $html = '<span ';
    }
    else
    {
        $html = '<div ';
    }

    //
    // afficher
    //

	if ($afficher)
    {
        $html .= 'class="tags text-center ' . $classes . '">';
	}
	else
	{
        $html .= 'class="tags text-center d-none ' . $classes . '">';
    }

	foreach(array($champ, $champ_d) as $c)
	{
		if (empty($c)) continue;

		$html .= '<span id="tag-' . $c . '" class="tag-champ">';

		$html .= montre_champ($lab_points, $c);

		$html .= ' <span class="points d-none"></span>';
		$html .= '</span>';
	}

    if ($inline)
    {
	    $html .= '</span>';
    }
    else
    {
	    $html .= '</div>';
    }

	return $html;
}

/* --------------------------------------------------------------------
 *
 * CONSULTER - CORRIGER
 *
 * -------------------------------------------------------------------- */

/* --------------------------------------------------------------------
 *
 * Consulter > Titre des tableaux
 *
 * -------------------------------------------------------------------- */
function lab_c_tableau($tableau_no, $tableau_data, $v = 1)
{
	// dans la vue :
 	// $tableau_data = array(
    //    'points_obtenus_ajustement' => $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0,
    //    'ajustement'                => $tableau_points_obtenus_ajustement ? TRUE : FALSE,
    //    'points_obtenus'            => $ajustement ? $tableau_points_obtenus_ajustement : $lab_points_tableaux[$tableau_no]['points_obtenus'],
    //    'points_totaux'             => $lab_points_tableaux[$tableau_no]['points'],
    //    'commentaires'              => $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL,
    //    'reussi'                    => ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $tableau_points) ? TRUE : FALSE
    // );

	// $v : version

	extract($tableau_data);

	$points_o  = format_nombre($points_obtenus);
	$points_t  = format_nombre($points_totaux); 
	$points_ts = $points_totaux > 1 ? 's' : '';

	$reussi_couleur = $reussi ? 'inherit' : 'crimson';
	$ajust_couleur  = $ajustement ? 'dodgerblue' : '#777';

	$commentaires_couleur = $commentaires ? 'dodgerblue' : '#777';
	$commentaires_icon = $commentaires ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots';

	$html = <<<EOD

		<div class="corriger-tableau-titre">	
			<div class="row no-gutters">

				<div class="col-8">
					Tableau $tableau_no
				</div>

			<div class="col-4">
				<div class="float-right font-weight-bold" style="color: $reussi_couleur">
					$points_o / $points_t point$points_ts
	EOD;

	/* --------------------------------------------------------
	 *
	 * Ajuste les points d'un tableau
	 * Laisser un commentaire a l'etudiant pour ce tableau
	 *
	 * -------------------------------------------------------- */

	if ($permettre_modifications)
	{
		$html .= <<<EOD

			<a href="#" style="text-decoration: none; margin-left: 5px"
				data-toggle="modal"
				data-target="#modal-corrections-changer-points-tableau"
				data-tableau_no="$tableau_no"
				data-ajustement="$ajustement"
				data-points_obtenus="$points_o"
				data-points="$points_t">

				<i class="bi bi-pencil-square" style="color: $ajust_couleur"></i>
			</a>

			<a href="#" style="text-decoration: none; margin-left: 5px"
				data-toggle="modal"
			   	data-target="#modal-laisser-commentaire-tableau"
			   	data-soumission_id="$soumission_id"
			   	data-tableau_no="$tableau_no"
			   	data-commentaire="$commentaires">

				<i class="bi $commentaires_icon" style="color: $commentaires_couleur"></i>
			</a>

		EOD;
	}

	$html .= <<<EOD

					</div>
				</div> <!-- .col -->
		
			</div> <!-- .row -->
		</div> <!-- .corriger-tableau-titre -->

	EOD;

	return $html;
}

/* --------------------------------------------------------------------
 *
 * Consulter / Corriger - Reponses
 *
 * -------------------------------------------------------------------- */
function lab_c_reponse($lab_valeurs, $points_champs, $champ, $champ_d, $options = array())
{
	$options = array_merge(
        array(
            'nsci'		=> FALSE,   // montrer ou ne pas montrer la nsci
            'nsci_v'    => NULL,    // specifier la valeur de nsci
            'unites'	=> FALSE,	// montrer ou ne pas montrer les unites
            'unites_v'  => NULL     // specifier la valeur des unites
   		),
   		$options
	);
    extract($options);

	$html = '';

	if ($champ && $champ_d && ($nsci || $unites))
	{
		$html .= '( ';
	}
	
	if ($champ)
	{
		$html .= $points_champs[$champ]['reponse'];
	}

	if ($champ_d)
	{
		if ($champ) $html .= ' ';

		$html .= '&pm; ' . $points_champs[$champ_d]['reponse'];
	}

	if ($champ && $champ_d && ($nsci || $unites))
	{
		$html .= ' )';
	}

	if ($nsci || $unites)
    {
        if (empty($champ))
        {
			$nsci_v   = empty($nsci_v) ? ($lab_valeurs[$champ_d]['nsci'] ?? NULL) : $nsci_v;
			$unites_v = empty($unites_v) ? ($lab_valeurs[$champ_d]['unites'] ?? NULL) : $unites_v;

            // Si 'unites_v' et 'nsci_v' sont nulles, alors
            // verifier si le champ (qui n'est pas une incertitude) possede ces informations.

			$champ_tmp = str_replace('_d', '', $champ_d);

            if (empty($nsci_v))
            {
			    $nsci_v = $lab_valeurs[$champ_tmp]['nsci'] ?? NULL;
            }

            if (empty($unites_v))
            {
                $unites_v = $lab_valeurs[$champ_tmp]['unites'] ?? NULL;
            }
		}
		else
        {
			$nsci_v   = empty($nsci_v) ? ($lab_valeurs[$champ]['nsci'] ?? NULL) : $nsci_v;
            $unites_v = empty($unites_v) ? ($lab_valeurs[$champ]['unites'] ?? NULL) : $unites_v;

            $champ_d_tmp = $champ . '_d';

            if (empty($nsci_v))
            {
                // Essayons de trouver la nsci de l'incertitude du champ
                $nsci_v = $lab_valeurs[$champ_d_tmp]['nsci'] ?? NULL;
            }

            if (empty($unites_v))
            {       
                // Essayons de trouver l'unites de l'incertitude du champ
                $unites_v = $lab_valeurs[$champ_d_tmp]['unites'] ?? NULL;
            }
        }

		if ($nsci_v || $unites_v)
		{
			if ($nsci && $nsci_v)
			{
				$html .= '&times;10<sup>' . $nsci_v . '</sup>';
			}

			if ($unites && $unites_v)
			{
				$html .= '&nbsp;' . $unites_v;
			}
		}

        // Si on veut seulement l'incertitude, alors les informations de nsci et d'unites sont dans le champ.
        /*
		if ( ! $champ)
		{
			$champ_tmp = enlever_d_champ($champ_d);
		}

		if ($nsci && isset($lab_valeurs[$champ_tmp]['nsci']) && $lab_valeurs[$champ_tmp]['nsci'])
		{
			$html .= ' &times;10<sup>' . $lab_valeurs[$champ_tmp]['nsci'] . '</sup>';
		}

		if ($unites && isset($lab_valeurs[$champ_tmp]['unites']) && $lab_valeurs[$champ_tmp]['unites'])
		{
			$html .= ' ' . $lab_valeurs[$champ_tmp]['unites'];
        }
         */
	}

	return $html;
}

/* ----------------------------------------------------------------------------
 *
 * Consulter / Corriger - Montrer la reponse d'un champ
 *
 * ---------------------------------------------------------------------------- */
function lab_montrer_reponse($champ, $points_champs)
{
    $reponse = $points_champs[$champ]['reponse'];

	// Ne pas changer les . en , si ce n'est pas ce que l'etudiant a entre.
    // if ($reponse != NULL)
    // {
    //    $reponse = str_replace('.', ',', $reponse);
    // }

    //
    // Precision
    //

    if (strpos($champ, 'precision') !== FALSE)
    {
        if ($reponse == NULL)
            $reponse = NULL;

        elseif ($reponse == 1)
            $reponse = 'précis';
        
        else 
            $reponse = 'non précis';
    }

    //
    // Exactitude
    //

    if (strpos($champ, 'exactitude') !== FALSE)
    {
        if ($reponse == NULL)
            $reponse = NULL;

        elseif ($reponse == 1)
            $reponse = 'exact';

        else
            $reponse = 'non exact';
    }

    //
    // Validite
    //

    if (strpos($champ, 'validite') !== FALSE)
    {
        if ($reponse == NULL)
            $reponse = NULL;

        elseif ($reponse == 1)
            $reponse = 'valide';

        else
            $reponse = 'non valide';
    }

    //
    // Reponse correcte
    //

    if (array_key_exists($champ, $points_champs) && $points_champs[$champ]['points'] == $points_champs[$champ]['points_obtenus']) 
    {
        $str = '<span class="lab-bonne-reponse">' . $reponse . '</span>';
    }

    //
    // Reponse incorecte
    //

    else
    {
        $str = '<span class="lab-mauvaise-reponse">' . $reponse . '</span>';
    }

    return $str;
}

/* ----------------------------------------------------------------------------
 *
 * Consulter - Montrer la correction d'un champ
 *
 * ---------------------------------------------------------------------------- */
function lab_montrer_corr($champ, $points_champs, $options = array())
{
    if ( ! is_array($options))
    {
        // retrocompatibilite pour A2024
        // avant il n'y avait pas $options, mais $classes
        $classes = $options;
        $montrer_corrections = TRUE;
        $inline = FALSE;
    }
    else
    {
        // a partir de H2025
        $options = array_merge(
            array(
                'classes' => 'mt-1',
                'inline'  => FALSE,
                'montrer_corrections' => TRUE
            ),
            $options
        );

        extract($options);
    }

    //
    // Ne pas montrer les corrections
    //

    if ( ! $montrer_corrections)
    {
        return;
    }

    $CI =& get_instance();

    $version_etudiante  = $CI->uri->segment(3) == 'etudiant' ? TRUE : FALSE;
    $montrer_champ_code = $CI->est_enseignant && ! $version_etudiante ? TRUE : FALSE;

    if ($CI->est_etudiant)
    {
        $montrer_champ_code = FALSE;
    }

    //
    // la str initiale
    //
    
    $str = '';

    //
    // ne pas montrer de correction si les points max sont de 0
    //

    /*
    if ($points_champs[$champ]['points'] == 0)
        return $str;
    */

    $wrap = FALSE; // wrap avec un div

    if ( ! empty($classes))
    {
        if (stripos($classes, 'mt') !== FALSE || stripos($classes, 'mb') !== FALSE)
        {
            $wrap = TRUE;
            $ext_classes = $classes;
            $classes = '';
        }
    }
        
    $points_tot = str_replace('.', ',', format_nombre($points_champs[$champ]['points']));
    $points_obt = str_replace('.', ',', format_nombre($points_champs[$champ]['points_obtenus']));

    $reponse_correcte = $points_champs[$champ]['reponse_correcte_ajustee'] ?? ($points_champs[$champ]['reponse_correcte'] ?? 0);
    $reponse          = $points_champs[$champ]['reponse'] ?? 0;  

    //
    // mauvaise reponse
    //

    if ($points_champs[$champ]['points'] != $points_champs[$champ]['points_obtenus']) 
    {
        // $deduct = ($points_champs[$champ]['points'] - $points_champs[$champ]['points_obtenus']);
        
        $str .= '<span class="lab-corr lab-corr-mauvaise ' . $classes . '">';

        if ($montrer_champ_code)
        {
            $str .= $champ . ' : ';
        }

        if ($reponse == NULL)
        {
            $str .= $points_obt . ' / ' . $points_tot . ' pt' . ($points_tot > 1 ? 's' : '');
            $str .= ' <i class="bi bi-x"></i>';
        }

        elseif ($reponse_correcte == $reponse)
        {
            $str .= $points_obt . ' / ' . $points_tot . ' pt' . ($points_tot > 1 ? 's' : '');
            $str .= ' CS';
        }
        else
        {
            $str .= $points_obt . ' / ' . $points_tot . ' pt' . ($points_tot > 1 ? 's' : '');
            $str .= ' <i class="bi bi-x"></i>';
        }

		if (array_key_exists('reponse_correcte_ajustee', $points_champs[$champ]) && ! empty($points_champs[$champ]['reponse_correcte_ajustee'])) 
		{
			// $str .= ' (R=' . str_replace('.', ',', $points_champs[$champ]['reponse_correcte_ajustee']) . ')';
			$str .= ' <span data-toogle="tooltip" data-placement="top" title="R=' . str_replace('.', ',', $points_champs[$champ]['reponse_correcte_ajustee']) . '">R=' . str_replace('.', ',', $points_champs[$champ]['reponse_correcte_ajustee']) . ')</span>';
		}
		else if (array_key_exists('reponse_correcte', $points_champs[$champ]) && ! empty($points_champs[$champ]['reponse_correcte']))
		{
			$str .= ' <span data-toogle="tooltip" data-placement="top" title="R=' . str_replace('.', ',', $points_champs[$champ]['reponse_correcte']) . '">R=' . str_replace('.', ',', $points_champs[$champ]['reponse_correcte']) . ')</span>';
		}

		$str .= '</span>';
    }

    //
    // champ non corrige car le champ ne comportait pas de points associes
    //

    elseif ($points_champs[$champ]['points'] == 0)
    {
        $str .= '<span class="lab-corr lab-corr-neutre ' . $classes . '">';

        if ($montrer_champ_code)
        {
            $str .= $champ . ' : ';
        }

        $str .= $points_obt . ' pt' . ($points_obt > 1 ? 's' : '');
        $str .= '</span>';
    }

    //
    // bonne reponse
    //

    else
    {
        $str .= '<span class="lab-corr lab-corr-bonne ' . $classes . '">';

        if ($montrer_champ_code)
        {
            $str .= $champ . ' : ';
        }

        $str .= $points_obt . ' pt' . ($points_obt > 1 ? 's' : '');
        $str .= ' <i class="bi bi-check-lg"></i></span>';
    }

    //
    // wrap
    //        

    if ($wrap)
    {
        if ($inline)
        {
            $str = '<span class="' . $ext_classes . '">' . $str . '</span>';

        }
        else
        {
            $str = '<div class="' . $ext_classes . '">' . $str . '</div>';
        }
    }

    return $str;
}

/* --------------------------------------------------------------------
 *
 * Consulter - Commentaires d'un tableau
 *
 * -------------------------------------------------------------------- */
function lab_c_commentaires($tableau_no, $tableau_data, $options = array())
{
    $options = array_merge(
        array(
            'montrer_commentaires' => TRUE
        ),
        $options
    );
    extract($options);

    //
    // Ne pas montrer les commentaires
    // (ex. lors de la correction manuelle de question)
    //

    if ( ! $montrer_commentaires)
    {
        return;
    }

    $genre = '';

    if (array_key_exists('cours_data', $tableau_data['soumission']))
    {
        $genre = $tableau_data['soumission']['cours_data']['enseignant_genre'] == 'F' ? 'e' : '';	
    }

    //
    // html initial
    //

	$html = '';

	if ($tableau_data['commentaires'])
	{
        $html .= '<div style="border-top: 1px solid #ddd; padding-top: 15px"></div>';

		$html .= <<<EOD
			<div class="corriger-tableau-commentaires">

				<div class="font-weight-bold" style="color: crimson">
					Commentaire de l'enseignant$genre :
				</div>

				<div class="hspace"></div>
		EOD;

		$html .= '<div>';

		$html .= _html_out($tableau_data['commentaires']);

		$html .= '</div>';
		$html .= '</div>';
	}

    //
    // html final
    //

    $html .= '';

	return $html;
}

/* --------------------------------------------------------------------
 *
 * Validite
 *
 * -------------------------------------------------------------------- */
function lab_validite($options = array())
{
	$CI =& get_instance();

	$traces = $options['traces'];
	$montre_tags = $options['montre_tags'];
	$lab_points = $options['lab_points'];
	$lab_points_champs = $options['lab_points_champs'];

	$html = '';
	
	$html .= <<<EOD
		<table class="table table-bordered mb-0" style="border: 0">
			<tbody>
				<tr>
					<td class="text-center">Précision</td>
					<td class="text-center">Exactitude</td>
					<td class="text-center">Validité</td>
				</tr>
				<tr>
	EOD;

	$champs = array(
		'precision'  => ['champ' => $options['precision'],  'on' => 'précis', 'off' => 'non précis'],
		'exactitude' => ['champ' => $options['exactitude'], 'on' => 'exact',  'off' => 'non exact'],
		'validite'   => ['champ' => $options['validite'],   'on' => 'valide', 'off' => 'non valide']
	);

	foreach($champs as $c_key => $c)
	{
		$champ = $c['champ'];
		$lab_prefix = $options['lab_prefix'];
		$prefix_champ = $lab_prefix . '-' . $champ;
		$prefix_champ_on  = $prefix_champ . '-1';
		$prefix_champ_off = $prefix_champ . '-0';

		// 
		// ON
		// 
		$html .= <<<EOD
			<td class="text-center">
				<div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
					<label class="btn btn-outline-primary no-margin" for="$prefix_champ_on" style="width: 125px; border-right: 0">
						<input type="radio" name="$prefix_champ" value="1" id="$prefix_champ_on" autocomplete="off"
		EOD;

		if ($CI->current_controller == 'evaluation' && array_key_exists($champ, $traces))
		{
			if ($traces[$champ] == 1)
				$html .= 'checked';
		}

		if ($CI->current_controller == 'consulter' && array_key_exists($champ, $lab_points_champs))
		{
			if ($lab_points_champs[$champ]['reponse'] == 1)
				$html .= 'checked';

			$html .= ' disabled';
		}

		$html .= '>' . $c['on'] .'</label>';

		// 
		// OFF
		//
		$html .= <<<EOD
			<label class="btn btn-outline-primary no-margin" for="$prefix_champ_off" style="width: 125px; margin-left: -5px">
				<input type="radio" name="$prefix_champ" value="0" id="$prefix_champ_off" autocomplete="off"
		EOD;

		if ($CI->current_controller == 'evaluation')
		{
			if (array_key_exists($champ, $traces))
			{
				if ($traces[$champ] == 0)
					$html .= 'checked';
			}
			else
			{
				$html .= 'checked';
			}
		}

		if ($CI->current_controller == 'consulter' && array_key_exists($champ, $lab_points_champs))
		{
			if ($lab_points_champs[$champ]['reponse'] == 0)
				$html .= 'checked disabled';

			$html .= ' disabled';
		}

		$html .= '>' . $c['off'] .'</label></div>';

		$html .= '<div class="mt-2">' . lab_tags(
					array(
						'champ' => $champ,
						// evaluation
						'montre_tags' => $montre_tags ?? FALSE,
						'lab_points'  => $lab_points ?? array(),
						// consulter
						'lab_points_champs' => $lab_points_champs ?? array()
					));

		$html .= <<<EOD
				</div>
			</td>
		EOD;
	}

	$html .= <<<EOD
				</tr>

			</tbody>
		</table>
	EOD; 

	echo $html;
} 


/* --------------------------------------------------------------------
 *
 * FONCTIONS OBSOLETE
 *
 * -------------------------------------------------------------------- */

/* --------------------------------------------------------------------
 *
 * lab form champ 
 *
 * --------------------------------------------------------------------
 * 
 * OBSOLETE
 *
 * Il faut la conserver pour les anciennes vues.
 *
 * -------------------------------------------------------------------- */
function lab_f_champ($lab_prefix, $champ, $champ_d = NULL, $options = array())
{
	$options = array_merge(
        array(
			'champv'	=> NULL,	// valeur du champ
			'champ_dv'  => NULL,	// valeur du champ_d
			'traces'	=> array(),
			'nsci'		=> 0,		// notation scientifique
			'u'			=> NULL		// unites
   		),
   		$options
	);
	extract($options);

	$traces[$champ]   = $traces[$champ] ?? NULL;
	$traces[$champ_d] = $traces[$champ_d] ?? NULL;

	$html = '<div class="input-group">';

	if ($champ)
	{
		$html .= <<<EOD
			<input type="text" class="form-control text-right" name="$lab_prefix-$champ" id="$lab_prefix-$champ"
				value="$traces[$champ]">
		EOD;
	}

	if ($champ_d)
	{
		if ($champ)
		{
			$html .= '<span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>';
		}
		else
		{
			$html .= '<div class="input-group-prepend"><div class="input-group-text">±</div></div>';
		}

		$html .= <<<EOD
			<input type="text" class="form-control" name="$lab_prefix-$champ_d" id="$lab_prefix-$champ_d"
				value="$traces[$champ_d]" style="text-align: left">
		EOD;
	}

	if ($options['nsci'] || $options['u'])
	{
		$html .= '<div class="input-group-append">';
		$html .= '<div class="input-group-text">';

		if ($options['nsci'])
		{
			$html .= '&times;10<sup>' . $options['nsci'] . '</sup>';
		}

		if ($options['u'])
		{
			$html .= ($options['nsci'] ? '&nbsp;' : '') . $options['u'];
		}

		$html .= '</div></div>';
	}

	$html .= <<<EOD
		</div> <!-- .input-group -->
	EOD;

	return $html;
}

