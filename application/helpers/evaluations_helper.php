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
 * EVALUATIONS HELPER
 *
 * ============================================================================ */

 use jlawrence\eos\Parser;

/* ----------------------------------------------------------------------------
 *
 * DETERMINER LES VALEURS DES VARIABLES ALEATOIREMENT (VERSION 2)
 *
 * ----------------------------------------------------------------------------
 *
 * Cette fonction prend comme argument $variables qui provient de :
 * Evaluation_model->extraire_variables($evaluation_id)
 *
 * ---------------------------------------------------------------------------- */
function determiner_valeurs_variables($variables_raw)
{
    /*
     * $variables_raw :
     *

	 [A] => Array
        (
            [variable_id] => 386
            [evaluation_id] => 577
            [label] => A
            [minimum] => 1
            [maximum] => 9
            [decimales] => 0
            [modification_epoch] => 1589418752
            [efface] => 0
            [efface_epoch] => 
            [efface_date] => 
        )
	*/

    if (empty($variables_raw) || ! is_array($variables_raw))
    {
        return array();
    }

    $variables = array();

	//
    // Determiner la valeur des variables aleatoirement
	//

    foreach($variables_raw as $var => $specs)
    {
		if ($specs['minimum'] < 1 && $specs['maximum'] < 1 && $specs['decimales'] == 0)
		{
			preg_match('/0\.(0*)(.*)$/', $specs['minimum'], $m_min);
			preg_match('/0\.(0*)(.*)$/', $specs['maximum'], $m_max);

            // Pour eviter les divisions par zero
            if ($specs['minimum'] == 0)
            {
                $min_facteur = 0;
            }
            else
            {
                $min_facteur = $m_min[2] / $specs['minimum'];
            }

            // Pour eviter les divisions par zero
            if ($specs['maximum'] == 0)
            {
                $max_facteur = 0;
            }
            else
            {
                $max_facteur = $m_max[2] / $specs['maximum'];
            }

            $facteur = abs(max($min_facteur, $max_facteur));
            $facteur = $facteur * 10000;

            // debug :
            // echo '(' . $specs['minimum'] . ') ' . $specs['minimum'] * $facteur . ' <---> ' . $specs['maximum'] * $facteur . ' (' . $specs['maximum'] . ') facteur = ' . $facteur;

            $v = mt_rand($specs['minimum'] * $facteur, $specs['maximum'] * $facteur);

            // Pour eviter les divisions par zero
            if ($facteur == 0)
            {
                $v = 0;
            }
            else
            {
                $v = $v / $facteur;
            }

			if (strpos($v, 'E') || strpos($v, 'e'))
            {
				//
                // Enlever la notation scientifique
                // car elle cause des problemes avec jlawrence's EOS Parser
				//

                $a  = strstr(strtoupper($v), 'E', TRUE);
                $b  = substr(strstr(strtoupper($v), 'E', FALSE), 1);

                if ($b < 1)
                {
                    // Un nombre entre 0 et 1
                    $b = abs($b);	

                    $a = str_replace('.', '', $a);

                    $v = '0.' . str_repeat('0', $b - 1) . $a;
                }	
            }
		}
		else
        {
			$scale = pow(10, $specs['decimales']);

            $v = mt_rand($specs['minimum'] * $scale, $specs['maximum'] * $scale) / $scale;

			$v = sprintf('%.' . $specs['decimales'] . 'f', $v);
		}

        //
        // Utiliser seulement le nombre de CS defini car cela pourrait
        // cause des differences dans les calculs faits par l'etudiant.
        //

		// Avant le 2020-10-23

		/*
        if ( ! empty($specs['cs']))
        {
            $v = cs_ajustement($v, $specs['cs']);
        }
		*/

		// A partir du 2020-10-23

		if ( ! empty($specs['cs']))
		{
            $v = cs_ajustement($v, $specs['cs']);

			if (array_key_exists('ns', $specs) && $specs['ns'] == 1)
			{
                $v = ns_format($v, FALSE);
                // $v = cs_ajustement($v, $specs['cs']);
				$v = str_replace(',', '.', $v);
			}
        }
        else
        {
            if (array_key_exists('ns', $specs) && $specs['ns'] == 1)
            {
                $v = ns_format($v, FALSE);
                $v = str_replace(',', '.', $v);
            }
        }

        $variables = array_merge($variables, array($var => $v));
    }

	/* Return 
		Array
		(
			[A] => 3
		)
	*/

    return $variables;
}

/* ----------------------------------------------------------------------------
 *
 * REMPLACER LES VARIABLES DANS UNE QUESTION
 *
 * ----------------------------------------------------------------------------
 *
 * Cette fonction sert a remplacer les variables par leur valeur numerique
 * dans l'enonce de la question.
 *
 * (!) Il est necessaire de fournir les valeurs des variables ($variables) et
 *     les proprietes des variables ($variables_raw).
 *
 * ---------------------------------------------------------------------------- */
function remplacer_variables_question($question_texte, $variables, $variables_raw)
{
	//
	// Decoder si une question est au format JSON.
	//

	$question_texte = json_decode($question_texte) ?: $question_texte;

	//
	// 1. Traiter les variables pour l'affichage pour la NS et les CS.
	// 2. Remplacer les variables dans les valeurs numeriques dans la question
	//
	// NS : notation scientifique
	// CS : chiffres significations
	//

	foreach($variables as $var => $var_val)
	{
		$var_ns = $variables_raw[$var]['ns'];
		$var_cs = $variables_raw[$var]['cs'];

		if ($var_cs)
		{
			$var_val = cs_ajustement($var_val, $var_cs);
		}

		if ($var_ns)
		{
			$var_val = ns_format($var_val, $var_cs);
		}

		$var_val = str_replace('.', ',', $var_val);

		$question_texte = str_replace('<var>' . $var . '</var>', $var_val, $question_texte);
	}

	return $question_texte;
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A CHOIX UNIQUE (TYPE 1)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_1($options = array())
{
	$options = array_merge(
        array(
            'question_id'         => NULL,  // La question_id
            'sondage'             => FALSE, // La question est un sondage
            'reponse_repondue_id' => 0,     // La reponse_id de l'etudiant
			'reponses'            => NULL,  // Les reponses possibles
			'points'		      => NULL   // Le pointage de la question
   		),
   		$options
	);

    extract($options);

    //
    // Assurons-nous que des reponses possibles existent.
    //

    if (empty($reponses) || ! is_array($reponses))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571810', 
				'desc'  => "Les réponses d'une des questions sont introuvables.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID : ' . $question_id
            )
        );
        exit;
    }

    //
    // Cherchons la reponse correcte parmi toutes les reponses.
    //

    foreach($reponses as $r)
    {
        if ($r['reponse_correcte'])
        {
            $reponse_correcte_id    = $r['reponse_id'];
            $reponse_correcte_texte = $r['reponse_texte'];						
            break;
        }
    }

    //
    // Une reponse correcte n'a pu etre trouvee
    //

    if ( ! isset($reponse_correcte_id) || ! isset($reponse_correcte_texte))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571811', 
				'desc'  => "Une réponse correcte n'a pu être trouvée.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID : ' . $question_id
            )
        );
        exit;
    }

    //
    // Corriger la question
    //

    //
    // Extraire le texte de la reponse repondue
    //

    if (array_key_exists($reponse_repondue_id, $reponses))
    {
        $reponse_repondue_texte = $reponses[$reponse_repondue_id]['reponse_texte'];
    }

    //
    // Verifier si la reponse repondue est la reponse correcte
    //

    $pointage = array(
        'points_obtenus' => 0
    );

    //
    // Il peut y avoir plusieurs reponses correctes meme s'il s'agit d'une question a choix unique.
    //

    /*
    if ($reponse_repondue_id == $reponse_correcte_id) 
    {
        $pointage['points_obtenus'] = $points;
    }
    */
    
    //
    // Verifier si la question a ete repondue
    //

    if ($reponse_repondue_id == 0)
    {
        $question_non_repondue = TRUE;
    }
    else
    {
        if ($reponses[$reponse_repondue_id]['reponse_correcte'] == 1)
        {
            $pointage['points_obtenus'] = $points;
        }
    }
    
    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

	//
	// Le tableau a retourner
	//
    
    return array(
        'reponse_repondue_id'       => $reponse_repondue_id,
        'reponse_repondue_texte' 	=> $reponse_repondue_texte ?? NULL,
        'reponse_correcte_id'       => $reponse_correcte_id,
        'reponse_correcte_texte' 	=> $reponse_correcte_texte,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => $question_non_repondue ?? FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A DEVELOPPEMENT (TYPE 2)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_2($options = array())
{
	$options = array_merge(
        array(
            'question_id'      => NULL,  // La question_id
            'sondage'          => FALSE, // La question est un sondage
            'reponse_repondue' => NULL,  // La reponse de l'etudiant
			'points'		   => NULL   // Le pointage de la question
   		),
   		$options
	);

    extract($options);

	//
	// Traitement de la reponse de l'etudiant
	//

	if ($reponse_repondue !== NULL)
	{
		$reponse_repondue = trim($reponse_repondue);
		$reponse_repondue = htmlentities($reponse_repondue); // Est-ce vraiment necessaire ?
	}	

	//
	// Corriger cette question
	//

	$pointage = array(
		'points_obtenus' => 0
	);

	//
	// L'etudiant n'a pas repondu a cette question.
	//

	if (empty($reponse_repondue))
	{
		return array(
			'reponse_repondue'       	=> $reponse_repondue,
			'points_obtenus'		 	=> $pointage['points_obtenus'],
			'corrigee'                  => TRUE,
			'question_non_repondue'     => TRUE
		);
	}

	//
	// Sondage
	//

	if ($sondage)
	{
		return array(
			'reponse_repondue'       	=> $reponse_repondue,
			'points_obtenus'		 	=> $pointage['points_obtenus'],
			'corrigee'                  => TRUE,
			'question_non_repondue'     => FALSE
		);
	}

	//
	// Le tableau a retourner
	//
    
    return array(
        'reponse_repondue'       	=> $reponse_repondue,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => FALSE,
        'question_non_repondue'     => FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A CHOIX UNIQUE PAR EQUATIONS (TYPE 3)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_3($options = array())
{
	$options = array_merge(
        array(
            'question_id'         => NULL,  // La question_id
            'sondage'             => FALSE, // La question est un sondage
            'reponse_repondue_id' => 0,     // La reponse_id de l'etudiant
            'reponses'            => NULL,  // Les reponses possibles (ce sont des equations)
            'variables'           => [],    // Les variables
			'points'		      => NULL   // Le pointage de la question
   		),
   		$options
	);

    extract($options);

    //
    // Verifions que des REPONSES existent pour cette question.
    //

    if (empty($reponses) || ! is_array($reponses))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571810', 
				'desc'  => "Les réponses d'une des questions sont introuvables.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID : ' . $question_id
            )
        );
        exit;
    }

    //
    // Verifions que des VARIABLES existent pour cette question.
    //

    if (empty($variables) || ! is_array($variables))
    {
        generer_erreur2(
            array(
                'code'  => 'SET5571', 
                'desc'  => "Il n'a pas été possible d'extraire les variables de votre évaluation,"
                           . "alors qu'elles sont nécessaires pour la correction d'une question "
                           . "dont les réponses proviennent d'équations utilisant ces variables.",
                'extra' => 'Question ID : ' . $question_id
            )
        );
        return;
    }

    //
    // Determiner les reponses selon les equations
    //

    foreach($reponses as $reponse_id => $r)
    {
        if ($r['equation'])
        {
            //
            // Determiner le nombre de CS (chiffres significatifs) qu'aura la reponse,
            // base sur les variables contenues dans l'equation.
            //

            $plus_petit_cs = 999;

            foreach($variables as $var_l => $var_v)
            {
                if ( ! preg_match('/' . $var_l . '/', $r['reponse_texte']))
                {
                    continue;
                }

                if (cs($var_v) < $plus_petit_cs) 
                {
                    $plus_petit_cs = cs($var_v);
                }
            }

            //
            // Resoudre les equations
            //

            $r['reponse_texte'] = str_replace(',', '.', $r['reponse_texte']);

            //
            // Remplacer la notation scientifique car elle cause probleme avec JL's Parser.
            //

            foreach($variables as $var_l => &$var_v)
            {
                if (strpos($var_v, 'E-') !== FALSE)
                {
                    $var_v = number_format($var_v, 50);
                }
            }

            try 
            {
                $resolu = Parser::solve($r['reponse_texte'], $variables); 
            } 
            catch (Exception $e) 
            {
                generer_erreur(
                    'SET481234', 
                    "Il y a un problème avec une des équations pour générer les réponses de cette évaluation.<br />
                    Veuillez contacter votre enseignante ou enseignant pour régler cette erreur."
                );
                return;
            }

            // Est-ce que les chiffres significatifs (CS) doivent etre pris en compte?
            // Si 'cs' == 99, ne pas considérer les CS.

            if ($r['cs'] != 99)
            {
                if ( ! empty($r['cs']))
                {
                    $resolu = cs_ajustement($resolu, $r['cs']);
                }
                elseif ($plus_petit_cs < 999)
                {
                    $resolu = cs_ajustement($resolu, $plus_petit_cs);
                }
            }

            $resolu = str_replace('.', ',', $resolu);
            $reponses[$reponse_id]['reponse_texte'] = $resolu;
        }

        //
        // Ajouter les unites a cette reponse
        //

        if ( ! empty($r['unites']))
        {
            $reponses[$reponse_id]['reponse_texte'] = $reponses[$reponse_id]['reponse_texte'] . ' ' . $r['unites'];
        }

        //
        // Enregistrer la reponse correcte
        //

        if ($r['reponse_correcte'])
        {
            $reponse_correcte_id 	= $r['reponse_id'];
            $reponse_correcte_texte = $reponses[$reponse_id]['reponse_texte'];
        }

    } // foreach $reponses

    //
    // Une reponse correcte n'a pu etre trouvee
    //

    if ( ! isset($reponse_correcte_id) || ! isset($reponse_correcte_texte))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571811', 
				'desc'  => "Une réponse correcte n'a pu être trouvée.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID : ' . $question_id
            )
        );
        exit;
    }

    //
    // Corriger la question
    //

    //
    // Extraire le texte de la reponse repondue
    //

    if (array_key_exists($reponse_repondue_id, $reponses))
    {
        $reponse_repondue_texte = $reponses[$reponse_repondue_id]['reponse_texte'];
    }

    //
    // Verifier si la reponse repondue est la reponse correcte
    //

    $pointage = array(
        'points_obtenus' => 0
    );

    if ($reponse_repondue_id == $reponse_correcte_id) 
    {
        $pointage['points_obtenus'] = $points;
    }

    //
    // Verifier si la question a ete repondue
    //

    if ($reponse_repondue_id == 0)
    {
        $question_non_repondue = TRUE;
    }
    
    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

	//
	// Le tableau a retourner
	//
    
    return array(
        'reponse_repondue_id'       => $reponse_repondue_id,
        'reponse_repondue_texte' 	=> $reponse_repondue_texte ?? NULL,
        'reponse_correcte_id'       => $reponse_correcte_id,
        'reponse_correcte_texte' 	=> $reponse_correcte_texte,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => $question_non_repondue ?? FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A CHOIX MULTIPLES (TYPE 4)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_4($options)
{
	$options = array_merge(
        array(
            'question_id'            => NULL,     // La question_id
            'sondage'                => FALSE,    // La question est un sondage
            'reponses_repondues_ids' => array(),  // Les reponse_ids de l'etudiant
            'reponses'               => NULL,     // Les reponses possibles (ce sont des equations)
			'points'		         => NULL      // Le pointage de la question
   		),
   		$options
	);

    extract($options);

    //
    // Verifions que des REPONSES existent pour cette question.
    //

    if (empty($reponses) || ! is_array($reponses))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571810', 
				'desc'  => "Les réponses d'une des questions sont introuvables.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID : ' . $question_id
            )
        );
        exit;
    }

    //
    // Corriger la question
    //

    //
    // Etablir le nombre de points par reponse
    //

    $points_par_reponse = $points / count($reponses);
    
    //
    // Les points sont attribues pour les reponses choisies et non choisies.
    //

    $pointage['points_obtenus'] = 0;

    foreach($reponses as $reponse_id => $r)
    {
        $reponses_toutes_ids[]               = $reponse_id;
        $reponses_toutes_textes[$reponse_id] = $r['reponse_texte'];

        //
        // Si l'etudiant a choisi cette reponse, ajouter le texte de celle-ci.
        //

        if (in_array($reponse_id, $reponses_repondues_ids))
        {
            $reponses_repondues_textes[$reponse_id] = $r['reponse_texte'];
        }

        if ($r['reponse_correcte'])
        {
            if (in_array($reponse_id, $reponses_repondues_ids))
            {
                $pointage['points_obtenus'] += $points_par_reponse;
            }

            $reponses_correctes_ids[]               = $reponse_id;
            $reponses_correctes_textes[$reponse_id] = $r['reponse_texte'];
        }
        else
        {
            if ( ! in_array($reponse_id, $reponses_repondues_ids))
            {
                $pointage['points_obtenus'] += $points_par_reponse;
            }
        }
    }
    
    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

	//
	// Le tableau a retourner
	//

    // Les clefs de ce tableau ont ete conservees telles quelles pour etre compatible avec les vues,
    // meme si elles ne sont pas ideales pour bien comprendre ce qu'elles signifient.

    return array(
        'reponse_repondue'          => $reponses_repondues_ids,
        'reponse_repondue_texte' 	=> $reponses_repondues_textes ?? array(),
        'reponse_correcte'          => $reponses_correctes_ids,
        'reponse_correcte_texte' 	=> $reponses_correctes_textes,
        'reponse_toutes'            => $reponses_toutes_ids,
        'reponse_toutes_texte'      => $reponses_toutes_textes,
        'points_par_reponse'        => $points_par_reponse,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => FALSE	// Ne pas repondre equivaut a repondre pour cette question.
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A REPONSE NUMERIQUE ENTIERE (TYPE 5)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_5($options)
{
	$options = array_merge(
        array(
            'question_id'      => NULL,   // La question_id
            'sondage'          => FALSE,  // La question est un sondage
            'reponse_repondue' => NULL,   // Le reponse de l'etudiant a la position [0] du tableau des reponses
            'reponses'         => NULL,   // Les reponses possibles (ce sont des equations)
			'points'		   => NULL    // Le pointage de la question
   		),
   		$options
	);

    extract($options);

    //
    // Verifions que d'une seule REPONSE existe pour cette question.
    //

    if (empty($reponses) || ! is_array($reponses) || count($reponses) != 1)
    {
        generer_erreur(
            'GC213331', 
            "Il y a un problème avec la réponse définie pour cette question."
        );
        return;
    }

    $reponse = array();
    $reponse = array_values($reponses)[0];

    if (empty($reponse))
    {
        generer_erreur(
            'GC568855', 
            "La réponse numérique d'une des questions est introuvable."
        );
        return;
    }
    
    //
    // Enregistrer les reponses originales
    //

    $reponse_repondue_originale = $reponse_repondue;
    $reponse_correcte_originale = $reponse['reponse_texte'];

    //
    // Traiter la reponse correcte
    //

    $reponse_correcte = $reponse['reponse_texte'];
    $reponse_correcte = trim($reponse_correcte);
    $reponse_correcte = str_replace(',', '.', $reponse_correcte);

    //
    // Traiter la reponse de l'etudiant
    //

    if ($reponse_repondue !== NULL)
    {
        $reponse_repondue = trim($reponse_repondue);
        $reponse_repondue = str_replace(',', '.', $reponse_repondue);
    }

    //
    // Corriger la question
    //

    $pointage = array(
        'points_obtenus' => 0
    );

    if ($reponse_repondue === NULL)
    {
        $question_non_repondue = TRUE;
    }
    else
    {
        if ($reponse_repondue == $reponse_correcte)
        {
            $pointage['points_obtenus'] = $points;
        }
    }

    //
    // Ajouter les unites aux reponses
    //

    if (array_key_exists('unites', $reponse) && ! empty($reponse['unites']))
    {
        $reponse_correcte_originale .= ' ' . $reponse['unites'];

        if ($reponse_repondue !== NULL)
        {
            $reponse_repondue_originale .= ' ' . $reponse['unites'];
        }
    }
    
    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

	//
	// Le tableau a retourner
	//

    // Les clefs de ce tableau ont ete conservees telles quelles pour etre compatible avec les vues,
    // meme si elles ne sont pas ideales pour bien comprendre ce qu'elles signifient.

    return array(
        'reponse_repondue_texte' 	=> $reponse_repondue_originale,
        'reponse_correcte_texte' 	=> $reponse_correcte_originale,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => $question_non_repondue ?? FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A REPONSE NUMERIQUE (TYPE 6)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_6($options)
{
	$options = array_merge(
        array(
            'question_id'      => NULL,     // La question_id
            'sondage'          => FALSE,    // La question est un sondage
            'reponse_repondue' => NULL,     // Le reponse de l'etudiant
            'reponses'         => NULL,     // La reponse se trouve a la position [0] de ce tableau
            'tolerances'       => array(),  // Les tolerances
			'points'		   => NULL      // Le pointage de la question
   		),
   		$options
    );

    extract($options);

    //
    // Verifions que d'une seule REPONSE existe pour cette question.
    //

    if (empty($reponses) || ! is_array($reponses) || count($reponses) != 1)
    {
        generer_erreur(
            'GC213331', 
            "Il y a un problème avec la réponse définie pour cette question."
        );
        return;
    }

    $reponse = array();
    $reponse = array_values($reponses)[0];

    if (empty($reponse))
    {
        generer_erreur(
            'GC568855', 
            "La réponse numérique d'une des questions est introuvable."
        );
        return;
    }

    //
    // Enregistrer les reponses originales
    //

    $reponse_repondue_originale = $reponse_repondue;
    $reponse_correcte_originale = $reponse['reponse_texte'];

    //
    // Traiter la reponse correcte
    //

    $reponse_correcte = $reponse['reponse_texte'];
    $reponse_correcte = trim($reponse_correcte);
    $reponse_correcte = str_replace(',', '.', $reponse_correcte);

    //
    // Traiter la reponse de l'etudiant
    //

    if ($reponse_repondue !== NULL)
    {
        $reponse_repondue = trim($reponse_repondue);
        $reponse_repondue = str_replace(',', '.', $reponse_repondue);
    }

    //
    // Corriger la question
    //

    $pointage = array(
        'points_obtenus' => 0
    );

    if ($reponse_repondue === NULL)
    {
        $question_non_repondue = TRUE;
    }
    else
    {
        $pointage = corriger_question_numerique($reponse_repondue, $reponse_correcte, $points, $tolerances);
    }

    //
    // Ajouter les unites aux reponses originales
    //

    if (array_key_exists('unites', $reponse) && ! empty($reponse['unites']))
    {
        $reponse_correcte_originale .= ' ' . $reponse['unites'];

        if ($reponse_repondue !== NULL)
        {
            $reponse_repondue_originale .= ' ' . $reponse['unites'];
        }
    }
    
    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

    //
	// Le tableau a retourner
	//

    // Les clefs de ce tableau ont ete conservees telles quelles pour etre compatible avec les vues,
    // meme si elles ne sont pas ideales pour bien comprendre ce qu'elles signifient.

    return array(
        'reponse_repondue_texte' 	=> $reponse_repondue_originale,
        'reponse_correcte_texte' 	=> $reponse_correcte_originale,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => $question_non_repondue ?? FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A REPONSE LITTERALE COURTE (TYPE 7)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_7($options = array())
{
	$options = array_merge(
        array(
            'question_id'      => NULL,     // La question_id
            'sondage'          => FALSE,    // La question est un sondage
            'reponse_repondue' => NULL,     // Le reponse de l'etudiant
            'reponses'         => NULL,     // La reponse se trouve a la position [0] de ce tableau
            'similarite'       => array(),  // La similarite
			'points'		   => NULL      // Le pointage de la question
   		),
   		$options
    );

    extract($options);

    //
    // Verifions que des REPONSES existent pour cette question.
    //

    if (empty($reponses) || ! is_array($reponses))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571810', 
				'desc'  => "Les réponses d'une des questions sont introuvables.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID ' . $question_id
            )
        );
        exit;
    }

    //
    // Extraire les reponses acceptees
    //

    $reponses_acceptees = array_column($reponses, 'reponse_texte');

    //
    // Corriger la question
    //

    $pointage = array(
        'points_obtenus' => 0
    );

    if ($reponse_repondue === NULL)
    {
        $question_non_repondue  = TRUE;
        $reponse_correcte_texte = $reponses_acceptees[0];
    }
    else
    {
        $pointage = corriger_question_litterale_courte3(
            $reponse_repondue,                              // la reponse de l'etudiant
            $reponses_acceptees,                            // les reponses acceptees  
            $points,                                        // points totaux de la question
            $similarite['similarite']                       // similarite
        );

        $reponse_correcte_texte = $pointage['reponse_acceptable'];

        // $pointage :
        // Array
        // (
        // 	[correcte] =>					// Si la reponse est correcte, TRUE sinon FALSE
        //	[reponse] => mal				// La reponse de l'etudiant
        //	[reponse_acceptable] => maille  // La reponse la plus proche de la reponse de l'etudiant
        //	[points] => 1.00				// Les points de la question
        //	[similarite] => 22.2			// La similarite calculee (reelle)
        //	[points_obtenus] => 0  			// Les points alloues a cette question
        // )
    }
    
    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

    //
	// Le tableau a retourner
	//

    // Les clefs de ce tableau ont ete conservees telles quelles pour etre compatible avec les vues,
    // meme si elles ne sont pas ideales pour bien comprendre ce qu'elles signifient.

    return array(
        'reponse_repondue_texte' 	=> $reponse_repondue,
        'reponse_correcte_texte' 	=> $reponse_correcte_texte,
        'points_obtenus'		 	=> $pointage['points_obtenus'],
        'similarite_calculee'       => $similarite['similarite'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => $question_non_repondue ?? FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A REPONSE NUMERIQUE PAR EQUATION (TYPE 9)
 *
 * ----------------------------------------------------------------------------
 *
 * La correction de ce type de question est combine a la fonction pour corriger
 * les reponses numeriques.
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_9($options)
{
	$options = array_merge(
        array(
            'question_id'        => NULL,
            'sondage'            => FALSE,    // Ce type de question ne peut pas etre un sondage
            'reponse_repondue'   => NULL,
            'reponses'           => array(),
			'variables' 	     => [],       // Les variables sous la forme A => valeur
			'tolerances'	     => [],       // Les tolerances
			'points'		     => NULL,     // Le pointage de la question
            'resoudre_seulement' => FALSE,    // TRUE pour l'outil de verification

            // Ces parametres deviendront desuets
			'reponse_etudiant' => NULL,       // La reponse de l'etudiant
			'reponse_equation' => NULL,       // L'equation menant a la reponse correcte
			// 'cs'			   => 99,         // Le nombre de CS a conserver a la reponse (CECI EST SPECIFIE DANS LA REPONSE)
			'unites'		   => NULL        // Les unites pour l'affichage
   		),
   		$options
    );

	//
	// Les clefs des options deviendront des variables
	//

	extract($options);

    //
    // Assurons-nous qu'une reponse correcte existe.
    //

    if (empty($reponses) || ! is_array($reponses) || count($reponses) != 1)
    {
        generer_erreur(
            'GC213331', 
            "Il y a un problème avec la réponse définie pour cette question."
        );
        return;
    }

    //
    // Cette question doit contenir des variables.
    //

    if (empty($variables) || ! is_array($variables))
    {
        generer_erreur(
            'GC66572', 
            "Il n'a pas été possible d'extraire les variables de votre évaluation,
            alors qu'elles sont nécessaires pour la correction d'une question dont les réponses proviennent d'équations utilisant ces variables."
        );
        return;
    }

    //
    // Enregistrer les reponses originales
    //

    $reponse_repondue_originale = $reponse_repondue;
    
	//
    // Traiter la reponse de l'etudiant
    //

    if ($reponse_repondue !== NULL)
    {
        $reponse_repondue = trim($reponse_repondue);
        $reponse_repondue = str_replace(',', '.', $reponse_repondue);
    }

    //
    // Extraire la reponse
    //
    
    $reponse = array_values($reponses)[0];

	//
	// Verifier l'equation
    //

    $reponse_equation = $reponse['reponse_texte'];

	if (empty($reponse_equation))
	{
		generer_erreur2(
			array(
				'code' => 'GC213481', 
				'desc' => "Il y a un problème avec l'équation pour cette question."
			)
		);
		return;
	}

	//
	// Lorsque l'affichage en notation scientifique est demandee, le nombre est transforme au
	// format notation scientifique avec 'ns_format' (qui devient une string) dans le but de
	// determiner correctement les chiffres significatifs, mais alors il devient inutilisable
	// avec le Parser. Il faut s'assurer qu'il redevienne un float avant de resoudre l'equation. 
	// (2020-10-23)
	//

	foreach($variables as $var_l => &$var_v)
	{
        if (stripos($var_v, 'E-') !== FALSE)
        {
            $var_v = number_format($var_v, 50);
        }

		// Ceci est pour fixer un probleme avec php8 et le Parser. (2024-08-29)
		if (stripos($var_v, 'E') !== FALSE)
		{
			$var_v = (float) $var_v;
		}
	}

	//
	// Resoudre l'equation
    //

	try 
	{
        $resolu = Parser::solve(str_replace(',', '.', $reponse_equation), $variables); 
	} 
	catch (Exception $e) 
	{
		generer_erreur2(
			array(
				'code' => 'GC4812388', 
				'desc' => "Il y a un problème avec une des équations pour générer les réponses de cette évaluation.<br />
						   Veuillez contacter votre enseignante ou enseignant pour régler cette erreur."
			)
		);
		return;
	}

	//
	// Enregister la reponse brute sans consideration des CS.
	//

	$reponse_correcte_brute = $resolu;

    //
    // Les chiffres significatifs
    //
    
    $cs = $reponse['cs'];

	//
	// Determiner le nombre de chiffres significatifs qu'aura la reponse selon les variables
	//
	// (!) On tient seulement compte des regles de multiplication et de division pour la propagation des chiffres significatifs.
	// C'est-a-dire, on conserve autant de CS que le terme qui en possede le moins.
	//
	// On commence par une valeur arbitraire elevee (999) et on diminie selon si on trouve un nombre avec moins de CS.
	//

	$plus_petit_cs = 999;

	if (empty($cs) || $cs == 0)
	{
		foreach($variables as $var_l => $var_v)
		{
			if ( ! preg_match('/' . $var_l . '/', $reponse_equation))
				continue;

			if (cs($var_v) < $plus_petit_cs) 
			{
				$plus_petit_cs = cs($var_v);
			}
		}
    }


	//
	// Ajuster les chiffres significatifs de la reponse selon les directives de l'enseignant
	//
	// Est-ce que les chiffres significatifs (CS) doivent etre pris en compte?
	//
	// Si 'cs' == 99, alors ne pas considérer les CS.
	// Si 'cs' == 0,  alors c'est selon la variable ayant le moins de CS ($plus_petit_cs)
	//
	// Si $plus_petit_cs n'a pas ete determine, ne pas faire d'ajustement de CS.
    //

	if ($cs != 99)
	{
		if ( ! empty($cs))
		{
			$resolu = cs_ajustement($resolu, $cs);
        }

		elseif ($plus_petit_cs < 999)
		{
			$resolu = cs_ajustement($resolu, $plus_petit_cs);
        }
    }

	$reponse_correcte = $resolu;

	//
	// Lorsqu'on utilise l'outil pour tester une question de de type 9,
	// on ne veut pas effectuer la correction mais seulement retourner la reponse,
	// car la correction s'effectue apres que l'enseignant entre une reponse arbitraire.
	//

	if ($resoudre_seulement)
	{
		return array(
			'reponse_correcte' 		 => $reponse_correcte,
			'reponse_correcte_brute' => $reponse_correcte_brute
		);
	}

    //
    // Corriger la question
    //

    $pointage = array(
        'points_obtenus' => 0
    );

	$reponse_interpretee = NULL;

    if ($reponse_repondue === NULL)
    {
        $question_non_repondue = TRUE;
    }
    else
    {
        $pointage = corriger_question_numerique(
            $reponse_repondue,
            $resolu, 
            $points, 
            $tolerances
        );

		// Array
		// (
		//	[reponse] => 
		//	[reponse_interpretee] => 
		//	[reponse_correcte] => 1456
		//	[points_obtenus] => 0
		// )

		$reponse_interpretee = $pointage['reponse_interpretee'];
    }

    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

	//
	// Traiter les reponses pour l'affichage
	//
	
	if ( ! empty($reponse['unites']))
	{
        $reponse_correcte 		= str_replace(',', '.', $reponse_correcte);
		$reponse_correcte 	    = $reponse_correcte . ' ' . $reponse['unites'];
		$reponse_correcte_brute = $reponse_correcte_brute . ' ' . $reponse['unites']; 

		if ($reponse_repondue !== NULL)
		{
			$reponse_repondue_originale = $reponse_repondue_originale . ' ' . $reponse['unites'];
			$reponse_interpretee 		= $reponse_interpretee . ' ' . $reponse['unites'];
		}
	}

    //
	// Le tableau a retourner
	//

    return array(
        'reponse_repondue_texte' 	=> $reponse_repondue_originale, // La reponse de l'etudiant + unites
		'reponse_correcte'			=> $resolu,						// La reponse numerique (CS)
        'reponse_correcte_texte' 	=> $reponse_correcte,			// La reponse numerique (CS et ,) + unites
		'reponse_correcte_brute'    => $reponse_correcte_brute,		// La reponse numerique (CS)
		'reponse_interpretee_texte' => $reponse_interpretee,		// Ce que la function a compris de la reponse repondue de l'etudiant
		'reponse_equation'			=> $reponse_equation,			// L'equation qui a servi a calculer la reponse correcte
		'points_obtenus'		 	=> $pointage['points_obtenus'], // Les points obtenus par l'etudiant
        'corrigee'                  => TRUE,
        'question_non_repondue'     => $question_non_repondue ?? FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A REPONDRE PAR TELEVERSEMENT DE DOCUMNETS (TYPE 10)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_10($options)
{
   $CI =& get_instance();		

	$options = array_merge(
        array(
            'question_id'        => NULL,	// (*)
            'sondage'            => FALSE,	// (*)
            'reponse_repondue'   => NULL,
			'documents' 	     => [],	 	// (*) Les documents televerses par l'etudiant
			'points'		     => NULL    // Le pointage de la question
   		),
   		$options
    );

	//
	// Les clefs des options deviendront des variables
	//

	extract($options);

	//
	// Les reponses possibles
	//
	// 1 : J'ai televerse un ou plusieurs documents.
	// 9 : Je ne vais pas repondre a cette question.
	//

   	$reponses_textes = $CI->config->item('questions_types')[10]['val'];

	//
	// Enregistrer la reponse originale de l'etudiant
	//

	$reponse_repondue_originale = $reponse_repondue;

	//
	// Le nombre de documents trouves pour cette question
	//

	$documents_trouves = 0;

	//
	// Determinons le nombre de documents trouves pour cette question
	//

	if ( ! empty($documents) || ! is_array($documents)) 
	{
		foreach($documents as $d)
		{
			if ($d['question_id'] == $question_id)
			{
				$documents_trouves++;
			}
		}
	}

	//
	// L'etudiant n'a pas repondu a cette question
	//

	if (empty($reponse_repondue))
	{
		return array(
			'reponse_repondue'			 => 9,
			'reponse_repondue_originale' => $reponse_repondue_originale,
			'reponse_repondue_texte' 	 => $reponses_textes[9],
			'points_obtenus'		 	 => 0,
			'documents_trouves'		 	 => $documents_trouves,
			'corrigee'				 	 => TRUE,
			'question_non_repondue'  	 => TRUE 
		);
	}

	//
	// Corriger la questison
	//

	if ( ! $documents_trouves)
	{
		// L'etudiant a peut-etre coche qu'il va televerser des documents ($reponse_repondue == 1) mais il ne l'a pas fait.
		// Si c'est le cas, c'est qu'il n'a pas repondu a la question.
		// Sinon, c'est qu'il a coche qu'il n'allait pas faire cette question ($reponse_repondue == 9).

		return array(
			'reponse_repondue'			 => $reponse_repondue,
			'reponse_repondue_originale' => $reponse_repondue_originale,
			'reponse_repondue_texte' 	 => $reponses_textes[$reponse_repondue],
			'points_obtenus'		 	 => 0,
			'documents_trouves'		 	 => $documents_trouves,
			'corrigee'				 	 => TRUE,
			'question_non_repondue'  	 => $reponse_repondue == 1 ? TRUE : FALSE
		);
	}

	//
	// L'etudiant ne va pas faire cette quesiton
	//

	if ($reponse_repondue == 9)
	{
		return array(
			'reponse_repondue'			 => 9,
			'reponse_repondue_originale' => $reponse_repondue_originale,
			'reponse_repondue_texte' 	 => $reponses_textes[9],
			'points_obtenus'		 	 => 0,
			'documents_trouves'		 	 => $documents_trouves,
			'corrigee'				 	 => TRUE,
			'question_non_repondue'  	 => FALSE
		);
	}

	//
	// Cette question doit etre corrigee manuellement.
	//

    return array(
		'reponse_repondue'		 	 => 1,
		'reponse_repondue_originale' => $reponse_repondue_originale,
		'reponse_repondue_texte' 	 => $reponses_textes[1],
		'documents_trouves'			 => $documents_trouves,
		'points_obtenus'			 => 0,
		'corrigee'				 	 => ($sondage ? TRUE : FALSE),
		'question_non_repondue'  	 => FALSE
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A CHOIX MULTIPLES STRICTE (TYPE 11)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_11($options)
{
	$options = array_merge(
        array(
            'question_id'            => NULL,     // La question_id
            'sondage'                => FALSE,    // La question est un sondage
            'reponses_repondues_ids' => array(),  // Les reponse_ids de l'etudiant
            'reponses'               => NULL,     // Les reponses possibles (ce sont des equations)
			'points'		         => NULL      // Le pointage de la question
   		),
   		$options
	);

    extract($options);

    //
    // Verifions que des REPONSES existent pour cette question.
    //

    if (empty($reponses) || ! is_array($reponses))
    {
        generer_erreur2(
			array(
				'code'  => 'GC571810', 
				'desc'  => "Les réponses d'une des questions sont introuvables.<br />"
						   . "Veuillez contacter votre enseignante ou enseignant pour régler cette erreur.",
				'extra' => 'Question ID ' . $question_id
            )
        );
        exit;
    }

    //
    // Corriger la question
    //

    //
    // Les points sont attribues pour les reponses choisies et non choisies.
    //

    $pointage = array(
		'points_obtenus' => 0
	);

	$erreur_trouvee = FALSE;

    foreach($reponses as $reponse_id => $r)
    {
        $reponses_toutes_ids[]               = $reponse_id;
        $reponses_toutes_textes[$reponse_id] = $r['reponse_texte'];

        //
        // Si l'etudiant a choisi cette reponse, ajouter le texte de celle-ci.
        //

        if (in_array($reponse_id, $reponses_repondues_ids))
        {
            $reponses_repondues_textes[$reponse_id] = $r['reponse_texte'];
        }

        if ($r['reponse_correcte'])
        {
            if ( ! in_array($reponse_id, $reponses_repondues_ids))
            {
				$erreur_trouvee = TRUE;
            }

            $reponses_correctes_ids[]               = $reponse_id;
            $reponses_correctes_textes[$reponse_id] = $r['reponse_texte'];
        }
        else
        {
            if (in_array($reponse_id, $reponses_repondues_ids))
            {
				$erreur_trouvee = TRUE;
            }
        }
    }
    
	//
	// Determiner le pointage
	//

	if ($erreur_trouvee === FALSE)
	{
		$pointage['points_obtenus'] = $points;
	}

    //
    // Ne pas considerer les points s'il s'agit d'un sondage.
    //

    if ($sondage)
    {
        $pointage['points_obtenus'] = 0;
    }

	//
	// Le tableau a retourner
	//

    // Les clefs de ce tableau ont ete conservees telles quelles pour etre compatible avec les vues,
    // meme si elles ne sont pas ideales pour bien comprendre ce qu'elles signifient.

    return array(
        'reponse_repondue'          => $reponses_repondues_ids,
        'reponse_repondue_texte' 	=> $reponses_repondues_textes ?? array(),
        'reponse_correcte'          => $reponses_correctes_ids,
        'reponse_correcte_texte' 	=> $reponses_correctes_textes,
        'reponse_toutes'            => $reponses_toutes_ids,
        'reponse_toutes_texte'      => $reponses_toutes_textes,
		'points_obtenus'		 	=> $pointage['points_obtenus'],
        'corrigee'                  => TRUE,
        'question_non_repondue'     => FALSE	// Ne pas repondre equivaut a repondre pour ce type de question.
    );
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A DEVELOPPEMENT COURT (TYPE 12)
 *
 * ----------------------------------------------------------------------------
 *
 * Ce type de question est identique a la question a developpement (type 2) 
 * mais l'apparence sur le formulaire est differente.
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_12($options = array())
{
	return corriger_question_type_2($options);
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION NUMERIQUE
 *
 * ----------------------------------------------------------------------------
 *
 * Cette fonction retourne un tableau :
 *
 *  'reponse'          => La reponse de l'etudiant formattee 
 *  'reponse_correcte' => La reponse correcte formattee
 *  'points_obtenus'   => Les points obtenus a la question
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_numerique($reponse, $reponse_correcte, $points, $tolerances = array()) 
{
	//
	// Les arguments
	//
	// $reponse 		 : reponse de l'etudiant
	// $reponse_correcte : reponse correcte pour obtenir tous les points
	// $points			 : points totaux de la question
    // $tolerances		 : tolerances qui serviront a penaliser
	//

	$reponse_originale          = $reponse;
	$reponse_correcte_originale = $reponse_correcte;

	//
	// Enlever les espaces superflus avant et apres la reponse
	//

    $reponse = trim($reponse);

    //
    // Enlever tous les caracteres qui ne devraint pas de retrouver dans une reponse numerique.
    //
	// - Remplacer * par x (pour la multiplication)
	//

	$reponse = n_sci_fix($reponse);

	//
	// Enregister la reponse intepretee apres les changements a la chaine
	//

	$reponse_interpretee = $reponse;

	//
    // Formatage des reponses pour les calculs
	//

    $reponse = str_replace(',', '.', $reponse);

    $reponse_correcte = (float) trim(str_replace(',', '.', $reponse_correcte));

	//
    // Preparer le tableau a retourner
	//
	// - Il restera seulement a ajouter le pointage obtenu par l'etudiant.
    // 
    
    $resultat = array(
		'reponse'		   	  => $reponse_originale,
		'reponse_interpretee' => $reponse_interpretee,
		'reponse_correcte' 	  => $reponse_correcte_originale
    );

    //
    // Ceci permet de regler un probleme ou l'addition et la soustraction de nombres 
	// laisse une decimale arbitraire (a cause des floats).
    //

    $reponse_dec          = nombre_decimales($reponse);
    $reponse_correcte_dec = nombre_decimales($reponse_correcte);

	//
    // On determine le nombre maximal de decimales entre la reponse et la reponse correcte.
    // On conserve une decimale supplementaire pour eviter des problemes (lesquels?).
	//

    $max_dec = max($reponse_correcte_dec, $reponse_dec) + 1; 

	//
    // On determine la difference entre les deux reponses.
    // On arrondi au nombre de decimales defini par $max_dec.
	//
    
    $reponse = (float) $reponse;
	
    $diff = abs($reponse_correcte - $reponse);
    $diff = round($diff, $max_dec);

	//
    // On suppose une note parfaite avant de soustraire les penalites.
	//

    $pointage = $points;
    $diff     = (string) $diff; // (!) Il est important de forcer le type 'string' pour prevenir un bug.

	//
    // Si la difference est de zero, la reponse est correcte.
	//

    if ($diff == 0)
    {
        $resultat['points_obtenus'] = $pointage;

        return $resultat;
    }

	//
    // S'il y a une difference et qu'il n'y a pas de tolerance, alors la reponse est erronee.
	//

    if (empty($tolerances) || ! is_array($tolerances))
    {
        $resultat['points_obtenus'] = 0;

        return $resultat;
    }

	//
    // S'il y a une difference mais qu'il y a des tolerances, il faut verifier les points partiels.
	//

	//
	// Transformer les tolerances relatives en absolues.
	//
		
	foreach($tolerances as $i => $t)
	{
		if ($t['type'] == 1)
			continue;

		$v = $reponse_correcte * ($t['tolerance']/100); 

		$tolerances[$i]['type']      = 1;
		$tolerances[$i]['tolerance'] = $v;
	}

	//
	// Ordonner les tolerance en ordre croissant.
	//

	usort($tolerances, function($a, $b) 
	{
		return $a['tolerance'] <=> $b['tolerance'];
	});

	//
	// Determiner la penalite
	//
    // On suppose que la reponse de l'etudiant sera en dehors des tolerances definies, donc une penalite de 100% (pour simplifier).
    // Ensuite, on remplace par la tolerance trouvee.
	//

    $penalite = $points;

    foreach($tolerances as $t)
    {
        if ($diff <= $t['tolerance'])
        {
            $penalite = $t['penalite'] / 100 * $points;
            break;
        }
    }

	//
	// Determer le pointage
	//

    $pointage = $points - $penalite;

	//
	// Retourner le resultat
	//
	// Le tableau a retourer (deja rempli plus haut, saut les points obtenus) :
	//
    // $resultat = array(
 	// 		'reponse'		   	  => $reponse_originale,
	//		'reponse_interpretee' => $reponse_interpretee,
	//		'reponse_correcte' 	  => $reponse_correcte_originale
	//		'points_obtenus'	  => NULL
    // );
	//

    $resultat['points_obtenus'] = $pointage;

    return $resultat;
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION LITTERALE COURTE (VERSION 3)
 *
 * ----------------------------------------------------------------------------
 *
 * Cette fonction retourne un tableau :
 *
 *  'reponse'          => La reponse de l'etudiant formattee 
 *  'reponse_correcte' => La reponse correcte formattee
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_litterale_courte3($reponse, $reponses_acceptables, $points, $similarite)
{
    $CI =& get_instance();

    if (empty($reponse) || empty($reponses_acceptables) || empty($points))
    {
        return array(
            'correcte'           => FALSE,
            'reponse'            => $reponse ?: NULL,
            'reponse_acceptable' => $reponses_acceptables[0] ?: NULL,
            'points'             => $points,
            'similarite'         => $similarite ?: $CI->config->item('questions_types')[7]['similarite'],
            'points_obtenus'     => 0,
        ); 
    }

    if ( ! is_array($reponses_acceptables))
    {
        $reponses_acceptables = array($reponses_acceptables);
    }

    //
    // Preparation du tableau de retour
    //

    $corrections = array(
        'correcte'           => FALSE,
        'reponse'            => $reponse,
        'reponse_acceptable' => $reponses_acceptables[0],
        'points'             => $points,
        'similarite'         => 0, // a calculer
        'points_obtenus'     => 0  // a determiner
    );

    //
    // Aseptiser les reponses
    //

    $chars = array(
        ' '  => '',
        ','  => '',
        '.'  => '',
		'+'  => '',
        '_'  => '',
        '\'' => '',
        '"'  => '',
        '('  => '',
        ')'  => ''
    );

    $reponse = strtolower(enlever_accents(trim($reponse)));
    $reponse = strtr($reponse, $chars);

    $perc_max = 0;

    foreach($reponses_acceptables as $r)
    {
        $r_originale = $r;

        $r = strtolower(enlever_accents(trim($r)));
        $r = strtr($r, $chars);

        //  etudiant    enseignant
        if ($reponse == $r)
        {
            $corrections['correcte']           = TRUE;
            $corrections['reponse_acceptable'] = $r_originale;
            $corrections['similarite']         = 100;
            $corrections['points_obtenus']     = $points;

            $perc_max = 100;
            break;
        }

        // enseignant, etudiant
        similar_text($r, $reponse, $perc);

        if ($perc > $perc_max)
        {
            $perc_max = $perc;

            $corrections['reponse_acceptable'] = $r_originale;
            $corrections['similarite']         = number_format($perc, 1);
        }
    }

    if ($perc_max >= $similarite)
    {
        $corrections['correcte']       = TRUE;
        $corrections['similarite']     = number_format($perc_max, 1);
        $corrections['points_obtenus'] = $points;
    }

    return $corrections;
}

/* ----------------------------------------------------------------------------
 *
 * SUGGERER SIMILARITE
 *
 * ---------------------------------------------------------------------------- */
function suggerer_similarite($reponses, $reponses_acceptables)
{
    if (empty($reponses) || empty($reponses_acceptables))
    {
        return 0;
    }

    $chars = array(
        ' ' => '',
        ',' => '',
        '.' => '',
		'+' => '',
		'_' => ''
    );

    // Aseptiser les reponses hypothetiques

    $asep_reponses = array();

    foreach($reponses as $r)
    {
        $r = strtolower(enlever_accents(trim($r)));
        $r = strtr($r, $chars);

        $asep_reponses[] = $r;
    }

    // Aseptiser les reponses acceptables

    $asep_reponses_acceptables = array();

    foreach($reponses_acceptables as $r)
    {
        $r = strtolower(enlever_accents(trim($r)));
        $r = strtr($r, $chars);

        $asep_reponses_acceptables[] = $r;
    }

    $min_perc = 100;

    foreach($asep_reponses as $r)
    {
        foreach($asep_reponses_acceptables as $ra)
        {
            // enseignant, etudiant
            similar_text($ra, $r, $perc);

            if ($perc < $min_perc)
            {
                $min_perc = $perc;
            }
        }
    }

    return number_format($min_perc, 1);
}

/* ----------------------------------------------------------------------------
 *
 * GENERER UN TABLEAU (ARRAY) DES POINTS PAR TABLEAUX POUR LES LABORATOIRES
 *
 * ----------------------------------------------------------------------------
 * 
 *	Determinez les points par les tableaux
 *
 *	Array
 *
 *		[1] => (tableau 1)
 *    		'champ1' => 'points', 'points_obtenus'
 *    		'champ2' => 'points', 'points_obtenus'
 *    		'points_totaux' = n
 *    		'points_totaux_obtenus' = n
 *
 *		[lab_points_totaux] => 0
 *		[lab_points_totaux_obtenus] => 0
 *
 * ---------------------------------------------------------------------------- */
function generer_lab_points_tableaux($lab_points)
{
    /*
	$lab_points_tableaux = array(
		'lab_points_totaux' => 0,
		'lab_points_totaux_obtenus' => 0
	);
    */
	$lab_points_tableaux = array(
		'points_totaux' => 0,
		'points_totaux_obtenus' => 0
	);
	
    if (empty($lab_points) || ! is_array($lab_points))
    {
		return $lab_points_tableaux;
    }

    foreach($lab_points as $c => $c_arr)
    {
        $tableau_no = $c_arr['tableau'] ?? 0; // devrait exister et different de 0

        if ( ! array_key_exists($tableau_no, $lab_points_tableaux))
        {
            /*
            $lab_points_tableaux[$tableau_no] = array(
                'points_totaux' => 0,
                'points_totaux_obtenus' => 0
            );
            */
            $lab_points_tableaux[$tableau_no] = array(
                'points' => 0,
                'points_obtenus' => 0
            );
        }

        //
        // Est-ce vraiment pertinent d'inclure les champs ? (2024-08)
        //

        /*
        $lab_points_tableaux[$tableau_no][$c] = array(
            'points' => $c_arr['points'],
            'points_obtenus' => 0
        );
        
        $lab_points_tableaux[$tableau_no]['points_totaux'] += $c_arr['points'];
        $lab_points_tableaux['lab_points_totaux'] += $c_arr['points'];
        */

        $lab_points_tableaux[$tableau_no]['points'] += $c_arr['points'];
        $lab_points_tableaux['points_totaux'] += $c_arr['points'];
    }

	return $lab_points_tableaux;
}

/* ----------------------------------------------------------------------------
 *
 * COMPLEMENATER LAB_VALEURS
 *
 * ---------------------------------------------------------------------------- */
function complementer_lab_valeurs($lab_valeurs, $lab_points)
{
    if (empty($lab_valeurs) || ! is_array($lab_valeurs) || empty($lab_points) || ! is_array($lab_valeurs))
    {
        return $lab_valeurs;
    }

    foreach($lab_valeurs as $c => &$c_arr)
    {
        unset($lab_valeurs[$c]['a_incertitude']);

        if (empty($c_arr['valeur']))
        {
            unset($lab_valeurs[$c]);
            continue;
        }

        if ( ! array_key_exists($c, $lab_points))
            continue;

        $c_arr['tableau']		  = $lab_points[$c]['tableau'];
        $c_arr['desc'] 			  = $lab_points[$c]['desc'];
        $c_arr['est_incertitude'] = $lab_points[$c]['est_incertitude'];

        if ( ! $lab_points[$c]['est_incertitude'])
        {
            $c_arr['incertitude'] = $lab_points[$c]['incertitude'];
        }

        $champ_d = $lab_points[$c]['incertitude'];

        if ( ! empty($champ_d))
        {
            if (empty($lab_valeurs[$champ_d]['unites']))
                $lab_valeurs[$champ_d]['unites'] = $c_arr['unites'];

            if (empty($lab_valeurs[$champ_d]['nsci']))
                $lab_valeurs[$champ_d]['nsci'] = $c_arr['nsci'];
        }
    }

    foreach($lab_valeurs as &$s_arr)
    {
        ksort($s_arr);
    }

    return $lab_valeurs;
}

/* ----------------------------------------------------------------------------
 *
 * MONTRER LES DETAILS D'UN CHAMP DANS LES TABLEAUX
 *
 * ---------------------------------------------------------------------------- */
function montre_champ($lab_points, $champ)
{
    $CI =& get_instance();

    //
    // points
    //

	$points = $lab_points[$champ]['points'] ?? 0;
	$points = format_nombre($points);
	$points = '(' . $points . ' pt' . ($points > 1 ? 's' : '') . ')';
        
    //
    // type
    //

    $icons = $CI->config->item('lab_champs_types_icons');

    /*
	$icons = array(
		'standard' 		=> 'box-arrow-in-down',
		'comparaison' 	=> 'arrows',
		'calcul'		=> 'calculator',
        'precision'     => 'record-circle',
        'exactitude'    => 'bullseye',
        'validite'      => 'check2-circle',
		'absorbance_d' 	=> 'align-middle'
	);
    */

	$type = $lab_points[$champ]['type'] ?? 'standard';
	$type = '<i class="bi bi-' . $icons[$type] . ' . "></i>';

    return $champ . ' ' . $points . ' ' . $type;
}

/* ----------------------------------------------------------------------------
 *
 * INCERTITUDE ABSORBANCE MESUREE AVEC LE SPECTROPHOTOMETRE
 *
 * ----------------------------------------------------------------------------
 * 
 * Cette fonction retourne l'incertitude selon la valeur de l'absorbance mesuree,
 * avec le spectrophotometre Spectronic 20D+.
 *
 * ---------------------------------------------------------------------------- */
function incertitude_absorbance($mesure, $virgule = FALSE)
{	
	$mesure = str_replace(',', '.', $mesure);
	$mesure = (float) $mesure;

	if ($mesure < 0)
		return '0';

    if ($mesure == 0)
		return ($virgule ? '0,001' : '0.001');

	if ($mesure >= 0.001 && $mesure <= 0.061)
		return ($virgule ? '0,001' : '0.001');

	if ($mesure >= 0.062 && $mesure <= 0.207)
		return ($virgule ? '0,003' : '0.003');

	if ($mesure >= 0.208 && $mesure <= 0.316)
		return ($virgule ? '0,004' : '0.004');

	if ($mesure >= 0.317 && $mesure <= 0.403)
		return ($virgule ? '0,005' : '0.005');

	if ($mesure >= 0.404 && $mesure <= 0.476)
		return ($virgule ? '0,006' : '0.006');

	if ($mesure >= 0.477 && $mesure <= 0.538)
		return ($virgule ? '0,007' : '0.007');

	if ($mesure >= 0.539 && $mesure <= 0.592)
		return ($virgule ? '0,008' : '0.008');

	if ($mesure >= 0.593 && $mesure <= 0.640)
		return ($virgule ? '0,009' : '0.009');

	if ($mesure >= 0.641 && $mesure <= 0.839)
		return ($virgule ? '0,01' : '0.01');

	if ($mesure >= 0.840 && $mesure <= 1.060)
		return ($virgule ? '0,02' : '0.02');

	if ($mesure >= 1.061 && $mesure <= 1.206)
		return ($virgule ? '0,03' : '0.03');

	if ($mesure >= 1.207 && $mesure <= 1.314)
		return ($virgule ? '0,04' : '0.04');

	if ($mesure >= 1.315)
		return '0';
}

function ajouter_d_champ($champ) 
{
    // Vérifier si la chaîne se termine par "-x" où x est un nombre
    if (preg_match('/-\d+$/', $champ, $matches)) 
	{
        // Extraire la portion sans le "-x"
        $base = substr($champ, 0, strrpos($champ, $matches[0]));
        // Ajouter "_d" avant le "-x"
        return $base . '_d' . $matches[0];
    } 
	else 
	{
        // Ajouter "_d" à la fin de la chaîne
        return $champ . '_d';
    }
}

function enlever_d_champ($champ)
{
    // Vérifier si la chaîne se termine par "_d"
    if (substr($champ, -2) === '_d') 
	{
        // Enlever "_d" à la fin de la chaîne
        return substr($champ, 0, -2);
    }

    // Vérifier si la chaîne contient "_d-"
    if (strpos($champ, '_d-') !== FALSE) 
	{
        // Remplacer "_d-" par "-" dans la chaîne
        return str_replace('_d-', '-', $champ);
    }

    // Retourner la chaîne inchangée si aucune des conditions ci-dessus n'est remplie
    return $champ;
}

/* ----------------------------------------------------------------------------
 *
 * RECALCULER LES POINTS D'UNE SOUMISSION (EVALUATION ou LABORATOIRE)
 *
 * ----------------------------------------------------------------------------
 * 
 * Cette fonction est utilisee apres un ajustement ou un affacement  manuel 
 * des points a une question, ou a un tableau (laboratoire).
 *
 * ---------------------------------------------------------------------------- */
function calculer_points_soumission($soumission_id, $options = array())
{
	$options = array_merge(
        array(
            'soumission'              => array(),   // *OBLIGATOIRE* les donnes de la soumission
            'question_id'             => NULL,      // le id de la question en ajustement
            'tableau_no'              => NULL,      // le numero du tableau en ajustement
            'nouveau_points_obtenus'  => 0,         // les nouveaux points obtenus
            'points'                  => 0          // les points de la question ou du tableau
   		),
   		$options
	);

    extract($options);

    //
    // Verifier les champs obligatoires, et les proprietes des champs
    //

    if (empty($soumission))
        return FALSE;

    if (empty($question_id) && empty($tableau_no))
        return FALSE;

    if ( ! empty($nouveau_points_obtenus))
    {
        // C'est un ajustement des points

        if (empty($points))
            return FALSE;

        if ($nouveau_points_obtenus > $points)
            return FALSE;

        if ($nouveau_points_obtenus < 0)
            return FALSE;
    }

    //
    // Laboratoire
    //

    if ($soumission['lab'])
    {
        //
        // Extraire les donnees du laboratoire
        //

        $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);
        $lab_points_champs   = json_decode($soumission['lab_points_champs'], TRUE);

        $lab_data            = json_decode($soumission['lab_data'], TRUE);
        $lab_precorrections  = $lab_data['lab_precorrections'] ?? array();
    }

    //
    // Evaluation
    //

    else
    {
        //
        // Verifier que la question est corrigee et peut etre ajustee
        //

        if ( ! array_key_exists($question_id, $soumission['questions_data']) || ! $soumission['questions_data'][$question_id]['corrigee'])
        {
            return FALSE;
        }
    }

    //
    // Extraire les ajustements existants de la soumission
    //

    $ajustements = array();

    if ( ! empty($soumission['ajustements_data']))
    {
        $ajustements = unserialize($soumission['ajustements_data']);
    }

    // ------------------------------------------------------------
    //
    // Calculer les points de l'evaluation
    //
    // Il faut recalculer les points au complet, tableaux et questions, parce que
    // si l'enseignant ajoute un ajustement de soumission, et qu'il l'enleve, il faut etre
    // en mesure de determiner les points obtenus (et la difference ne peut plus etre suivie).
    // 
    // Il faut calculer les tableaux egalement car c'est la meme methode pour les
    // evaluations et les laboratoires, meme si celle-ci est utilisee seulement
    // pour les questions.
    //
    // ------------------------------------------------------------

    //
    // Les points obtenus de la soumission avant les derniers ajustements
    //

    $soumission_points_obtenus_actuels = $soumission['points_obtenus'];
    $soumission_points_obtenus = 0;

    //
    // Addition les points obtenus aux questions, en tenant compte des ajustements
    //

    foreach($soumission['questions_data'] as $q_id => $q)
    {
        // la question en ajustement

        if ( ! empty($question_id) && ! empty($nouveau_points_obtenus) && $question_id == $q_id)
        {
            $soumission_points_obtenus += $nouveau_points_obtenus;

            continue;
        }

        // les questions avec des ajustements existants

        if (array_key_exists($q_id, $ajustements))
        {
            $soumission_points_obtenus += $ajustements[$q_id]['points_obtenus'];

            continue;
        }

        // toutes les autres questions

        if ( ! $q['sondage'])
        {
            $soumission_points_obtenus += $q['points_obtenus'];
        }
    }

    //
    // Addition les points obtenus aux tableaux, en tenant compte des ajustements
    //

    if ($soumission['lab'])
    {
        $points_obtenus_tableaux = 0;
        $points_totaux_tableaux  = 0;

        foreach($lab_points_tableaux as $t_no => $t_arr)
        {
            $points_totaux_tableaux += $t_arr['points'];

            // le tableau en ajustement

            if ( ! empty($tableau_no) && ! empty($nouveau_points_obtenus) && $t_no == $tableau_no)
            {
                $points_obtenus_tableaux += $nouveau_points_obtenus;
                // $soumission_points_obtenus += $nouveau_points_obtenus;

                continue;
            }

            // les tableaux avec des ajustements existants

            if (array_key_exists('points_obtenus_ajustement', $t_arr))
            {
                $points_obtenus_tableaux += $t_arr['points_obtenus_ajustement'];
                // $soumission_points_obtenus += $t_arr['points_obtenus_ajustement'];

                continue;
            }

            // les autres tableaux

            $points_obtenus_tableaux += $t_arr['points_obtenus'];
            // $soumission_points_obtenus += $t_arr['points_obtenus'];
        }

        //
        // Enlever les penalites des precorrections excessives
        //

        if (array_key_exists('penalite', $lab_precorrections) && $lab_precorrections['penalite'] > 0)
        {
            $points_obtenus_tableaux = $points_obtenus_tableaux - ($points_totaux_tableaux * $lab_precorrections['penalite']/100);
        }

        $soumission_points_obtenus += $points_obtenus_tableaux;
    }

    return array(
        'soumission_points_obtenus' => $soumission_points_obtenus
    );
}

/* ----------------------------------------------------------------------------
 *
 * precorrections_penalite
 *
 * ----------------------------------------------------------------------------
 * 
 *
 * ---------------------------------------------------------------------------- */
function precorrections_penalite($compte, $limite_sans_penalite = 10, $penalite_par_compte = '0.5')
{
    $r = array(
            'compte'       => $compte,				 // comptage des essaies de precorrection
            'penalite'     => 0,          			 // penalite en pourcentage
            'penalite_str' => $limite_sans_penalite  // par defaut, montrer simplement le compte
    );

	if ($compte == 0)
	{
		return $r;
	}

	$p_restantes = ($limite_sans_penalite - $compte) > 0 ? ($limite_sans_penalite - $compte) : 0;
	$p_penalites = ($limite_sans_penalite - $compte) < 0 ? abs($limite_sans_penalite - $compte) : 0;
	$p_penalites_str = $p_restantes;
	
	if ($p_penalites > 0)
	{
		$p_penalites_str = str_replace('.', '.', '-' . $p_penalites * $penalite_par_compte . '%');
	}

	$r['penalite']     = $p_penalites;
	$r['penalite_str'] = $p_penalites_str;

    return $r;
}

/* ----------------------------------------------------------------------------
 *
 * Rendre compatible la notation scientifique
 *
 * ---------------------------------------------------------------------------- */
function n_sci_fix($nombre)
{
    if ($nombre == NULL)
    {
        $nombre = 0;
    }

	//
	// Enlever les espaces superflus avant et apres la reponse
	//

    $nombre = trim($nombre);
	
	//
	// Enlever les espaces superflus dans la reponse
	//

	if (strpos(' ', $nombre) !== FALSE)
		$nombre = str_replace(' ', '', $nombre);	

    //
    // Enlever tous les caracteres qui ne devraint pas de retrouver dans une reponse numerique.
    //
	// - Remplacer * par x (pour la multiplication)
	//

    $chaines = array(
        ' ' => '', 'e' => 'E',

        '(' => '', ')' => '', '%' => '', '&' => '', '*' => 'x', '$' => '', '@' => '', '!' => '', '#' => '', '_' => '', '+' => '', '|' => '',
        '~' => '', '?' => '', '<' => '', '>' => '', '[' => '',  ']' => '', '{' => '', '}' => '', ':' => '', ';' => '', '/' => '', '\\' => '', 

        'a' => '', 'b' => '', 'c' => '', 'd' => '', 'f' => '', 'g' => '', 'h' => '', 'i' => '', 'j' => '', 'k' => '', 'l' => '', 'm' => '', 
        'n' => '', 'o' => '', 'p' => '', 'q' => '', 'r' => '', 's' => '', 't' => '', 'u' => '', 'v' => '', 'w' => '', 'y' => '', 'z' => '',

        'A' => '', 'B' => '', 'C' => '', 'D' => '', 'F' => '', 'G' => '', 'H' => '', 'I' => '', 'J' => '', 'K' => '', 'L' => '', 'M' => '', 
        'N' => '', 'O' => '', 'P' => '', 'Q' => '', 'R' => '', 'S' => '', 'T' => '', 'U' => '', 'V' => '', 'W' => '', 'Y' => '', 'Z' => '',
    
        'à' => '', 'é' => '', 'è' => '', 'ê' => '', 'ç' => '', 'ô' => '', 'â' => '', 'ï' => '', 'î' => '', 'û' => '', 'ù' => '',
		'À' => '', 'É' => '', 'È' => '', 'Ê' => '', 'Ç' => '', 'Ô' => '', 'Â' => '', 'Ï' => '', 'Î' => '', 'Û' => '', 'Ù' => '',
		'•' => 'x'
    );

    $nombre = strtr($nombre, $chaines);

	//
    // Chaines de remplacement pour les calculs
	// 
	// Essayons de prevoir ce que les etudiants pourraient bien utilser pour 
	// representer la notation scientifique.
    //

    $chaines_calculs = array(
        'x10^'     => 'e',
        '×10^'     => 'e',
        'xE' 	   => 'e',
        'xe' 	   => 'e',
		'xE^'	   => 'e',
		'xe^'	   => 'e',
        'x10⁶'     => 'e6',
        'x10³'     => 'e3',
        'x10²'     => 'e2',
        'X10^'     => 'e',
        'XE' 	   => 'e',
        'Xe' 	   => 'e',
        'e10^'     => 'e',
        'E10^'     => 'e',
		'e^'	   => 'e',
		'E^'	   => 'e',
        'x10e'     => 'e',
        'x10e^'    => 'e',
        'x10E'     => 'e',
        'x10E^'    => 'e',
        'X10⁶'     => 'e6',
        'X10³'     => 'e3',
		'X10²'     => 'e2',
        'x10E'     => 'e'   // erreur d'une etudiante
    );

    $nombre = strtr($nombre, $chaines_calculs);

    return $nombre;
}

/* ----------------------------------------------------------------------------
 *
 * Liste d'eleves du groupe d'un etudiant
 *
 * ----------------------------------------------------------------------------
 *
 * Cette function sert a extraire la liste de tous les partenaires de laboratoire
 * potentiels d'un eleve (etudiant) afin qu'il puisse choisir ses partenaires
 * dans une liste de tous les eleves de son cours (et de son groupe), donc parmi
 * ceux presents physiquement dans la classe ou dans le laboratoire.
 * 
 * ---------------------------------------------------------------------------- */
function liste_eleves_groupe_etudiant($matricule, $opt = array()): array
{
	// matricule : l'etudiant est identifie par son matricule, car il n'a pas
    //             necessairement de compte KOVAO au moment de faire son laboratoire

    $CI =& get_instance();

	$opt = array_merge(
        array(
            'enseignant_id' => 0,
            'cours_id'      => 0,
			'semestre_id'   => $CI->semestre_id ?? 0
   		),
   		$opt
	);
	
	if (empty($opt['enseignant_id']) || empty($opt['cours_id']) || empty($opt['semestre_id']))
	{
		return array();
	}

	//
	// Extraire la liste des eleves de ce cours (tous les cours-groupes)
	//

  	$eleves_groupes = $CI->Cours_model->lister_eleves_laboratoire(
    	array(
          	'semestre_id'   => $opt['semestre_id'],
          	'enseignant_id' => $opt['enseignant_id'],
         	'cours_id'      => $opt['cours_id']
		)
	);

	//
	// Chercher l'eleve (matricule) dans la liste de tous les eleves,
 	// et determiner son cours-groupe
	//

	$eleve_cours_groupe = 0;

	if ( ! empty($eleves_groupes))
	{
		foreach($eleves_groupes as $e)
		{
			if ($e['numero_da'] == $matricule)
			{
				$eleve_cours_groupe = $e['cours_groupe'];
				break;
			}
		}
	}

	//
	// Extraire seulement les eleves de ce cours-groupe
	//

	if ( ! empty($eleve_cours_groupe))
	{
	    $lab_cours_groupe = $eleve_cours_groupe;
	
	    foreach($eleves_groupes as $e)
	    {
            if ($e['numero_da'] == $matricule)
                continue;

	   	    if ($e['cours_groupe'] == $eleve_cours_groupe)
	   	    {
	   		    $liste_eleves_cours[] = $e;
	   	    }
	    }
	
	    //
	    // Placer les eleves en ordre alpha de nom, puis de prenom
	    //
	
	    if (is_array($liste_eleves_cours) && ! empty($liste_eleves_cours))
	    {
	   	 usort($liste_eleves_cours, function($a, $b)
	   	 {
	   		 // Comparer par 'eleve_nom'
	   		 $nomCompare = strcmp($a['eleve_nom'], $b['eleve_nom']);
	
	   		 // Si les noms sont égaux, comparer par 'eleve_prenom'
	   		 if ($nomCompare === 0) {
	   			 return strcmp($a['eleve_prenom'], $b['eleve_prenom']);
	   		 }
	
	   		 return $nomCompare;
	   	 });
	
	   	 $liste_eleves_cours = array_keys_swap($liste_eleves_cours, 'eleve_id');
	    }
	}

    return array(
        'eleve_cours_groupe' => $eleve_cours_groupe,
        'liste_eleves_cours' => $liste_eleves_cours
    );
}

// ----------------------------------------------------------------------------
//
// LAB - CORRIGER LES COMPARAISONS
// 
// ----------------------------------------------------------------------------
function lab_corriger_comparaison($val_etudiant, $reponse, $points)
{



}

// ----------------------------------------------------------------------------
//
// LAB - CORRIGER LA METHODE DES EXTREMES 
// 
// ----------------------------------------------------------------------------
//
// Cette fonction determine l'incertitude sur une moyenne par la methode 
// des extermes.
//
// ----------------------------------------------------------------------------
function lab_corriger_methode_extremes($vals = array(), $vals_d = array())
{
	// $vals[0]   = valeur 0
	// $vals_d[0] = incertitude de la valeur 0

	if (empty($vals))
		return 0;

	$max = NULL;
	$min = NULL;

	foreach($vals as $i => $v)
	{
		$v_d = $vals_d[$i] ?? 0;

		// nettoyer les valeurs

		$v = trim($v);
		$v = str_replace(',', '.', $v);
		$v = str_replace(' ', '', $v);
		$v = n_sci_fix($v);

		$v = (float) $v;

		$v_d = trim($v_d);
		$v_d = str_replace(',', '.', $v_d);
		$v_d = str_replace(' ', '', $v_d);
		$v_d = n_sci_fix($v_d);

		$v_d = (float) $v_d;

		// calculs

		$ext_sup = $v + $v_d;

		$ext_inf = $v - $v_d;

		if ($max == NULL || $ext_sup > $max)
		{
			$max = $ext_sup;
		}

		if ($min == NULL || $ext_inf < $min)
		{
			$min = $ext_inf;
		}
	}

	$max_min = $max - $min;

	$incertitude = Parser::solve(($max - $min) / 2);

	return $incertitude;
}
