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
 * GENERAL HELPER
 *
 * ============================================================================ */

/* --------------------------------------------------------------------------------------------
 *
 * GET CURRENT COMMIT NUMBER
 *
 * -------------------------------------------------------------------------------------------- */
function get_current_commit_number()
{
    $count = shell_exec('./scripts/git_commit_number.sh');
    return (int) $count;
}

/* --------------------------------------------------------------------------------------------
 *
 * GET CURRENT BRANCH
 *
 * -------------------------------------------------------------------------------------------- */
function get_current_branch()
{
    $branch = shell_exec('./scripts/git_branch.sh');
    return trim($branch);
}

/* --------------------------------------------------------------------------------------------
 *
 * GET CURRENT COMMIT DATE
 *
 * -------------------------------------------------------------------------------------------- */
function get_current_commit_date()
{
    $date = shell_exec('./scripts/git_commit_date.sh');
    $date = new DateTime($date);

    $epoch = date_format($date, 'U');
    $date  = date_humanize($epoch);
    $date  = implode('', explode('-', $date));

    return $date;
}

/* --------------------------------------------------------------------------------------------
 *
 * GET COMMIT GENERATED HASH
 *
 * Les autres methodes ne fonctionnent pas.
 *
 * -------------------------------------------------------------------------------------------- */
function git_commit_hash_horodatage()
{
	$gitObjectsDir = FCPATH . '/.git/objects';

	// Vérifier si le répertoire existe
	if (is_dir($gitObjectsDir)) 
	{
    	// Obtenir le timestamp de la dernière modification du répertoire
    	$dirTimestamp = filemtime($gitObjectsDir);

    	$uniqueString = hash('sha256', $dirTimestamp);

    	return substr($uniqueString, 0, 12);
	} 
	else 
	{
		return date('U');
	}
}

/* --------------------------------------------------------------------------------------------
 *
 * GET MEMORY USAGE
 * https://www.php.net/manual/fr/function.memory-get-usage.php
 *
 * -------------------------------------------------------------------------------------------- */
function get_memory_usage()
{
    $size = memory_get_usage(TRUE);

    $unit = array('B','KB','MB','DB','TB','PB');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
} 

/* --------------------------------------------------------------------------------------------
 *
 * CATCH POST (ATTRITION)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction sera rendue desuette par attrition suite au remplacement par un simple:
 * $this->input->post()
 *
 * -------------------------------------------------------------------------------------------- */
function catch_post($options = array())
{
    $CI =& get_instance();

    //
    // default options
    //

    $options = array_merge(
        array(
            'ids' => array() // list of ids to test for validation (ids are unsigned 'int' or 'bigint')
        ), $options
    );

    if (($post_data = $CI->input->post(NULL, TRUE)) === NULL)
    {
        return FALSE;
    }

    //
    // variable validation for required ids
    //

    if ( ! empty($options['ids']))
    {
        foreach($options['ids'] as $id)
        {
            if ( ! array_key_exists($id, $post_data) ||
                 empty($post_data[$id])              ||
                 ! ctype_digit($post_data[$id])      ||
                 ($post_data[$id] < 0)
               )
            {
                return FALSE;
            }
        }
    }

    return $post_data;
}

/* ----------------------------------------------------------------------------
 *
 * VERIFIER IDS
 *
 * ----------------------------------------------------------------------------
 *
 * Ce helper sert a verifier si un ID est present et valide dans une requete POST.
 *
 * ---------------------------------------------------------------------------- */
function verifier_ids($post_data = array(), $ids = array(), $stricte = TRUE)
{
    // stricte : ID doit etre present.

    if (empty($ids))
    {
        return TRUE;
    }

    foreach($ids as $id)
    {
        if ( ! array_key_exists($id, $post_data))
        {
            if ($stricte) 
                return FALSE;

            continue;
        }

        if ( ! is_numeric($post_data[$id]) || ! ctype_digit($post_data[$id]))
		{
            return FALSE;
		}
    }

    return TRUE;
}

/* ----------------------------------------------------------------------------
 *
 * QUESTIONS TYPES
 *
 * ---------------------------------------------------------------------------
 *
 * Cette fonction sert a generer un tableau qui sera facile a iterer.
 *
 * ---------------------------------------------------------------------------- */
function questions_types()
{
    $CI =& get_instance();
    
    $types = $CI->config->item('questions_types');

    //
    // Enlever les questions dont l'enseignant n'a pas la permission.
    //

    foreach($types as $t => $q)
    {
        if ($CI->enseignant['privilege'] < $q['priv'])
        {
            unset($types[$t]);
        }
    }

    //
    // Ordonner les types de questions selon l'ordre defini.
    //

    $types_tous = array_column($types, 'ordre');
    array_multisort($types_tous, SORT_ASC, $types);

    return $types;
}

/* ----------------------------------------------------------------------------
 *
 * MY NUMBER FORMAT
 *
 * ----------------------------------------------------------------------------
 *
 * Version 1 
 * Cette fonction est encore tres utilisee. 
 *
 * ---------------------------------------------------------------------------- */
function my_number_format($number, $decimals = 2, $strip_zeros = TRUE)
{
    //
    // Assigner les valeurs par defaut dans le cas ou elles sont NULL.
    //

    if ($decimals === NULL)
    {
        $decimals = 2;
    }

    if ($strip_zeros === NULL)
    {
        $strip_zeros = TRUE;
    }

    //
    // Convertir le 'number' de francais a anglais si necessaire
    //

    $number = str_replace(',', '.', $number);

    if ($strip_zeros)
    {
		$tmp_number = $number;

		preg_match('/\.([0-9]?)([0-9]?)/', $number, $matches);

		if (empty($matches[2]))
		{
			if (empty($matches[1]))
        		return number_format($tmp_number, 0, ',', ' ');
			else
        		return number_format($tmp_number, 1, ',', ' ');
		}
		else
		{
        	return number_format($tmp_number, 2, ',', ' ');
		}
    }

	return number_format($number, $decimals, ',', ' ');
}

/* ----------------------------------------------------------------------------
 *
 * FORMAT (D'UN) NOMBRE (version 3)
 *
 * ----------------------------------------------------------------------------
 *
 * La version 2 de "my number format" (2024-08-06).
 * La version 3 de "my number format" (2024-10-18).
 *
 * ---------------------------------------------------------------------------- */
function format_nombre($nombre, $options = array())
{
	$options = array_merge(
		array(
			'decimales'     	 => 2,
			'virgule'			 => TRUE,
			'enlever_zeros' 	 => TRUE,
			'separateur_millier' => ' '    // version 3 seulement
	   ),
	   $options
	);

	// Retrocompatibilite

	$decimales 	        = $options['decimales'];
	$virgule 		    = $options['virgule'];
	$enlever_zeros      = $options['enlever_zeros'];
	$separateur_millier = $options['separateur_millier'];

	// Nettoyage du nombre

	$nombre = trim(str_replace([' ', ','], ['', '.'], $nombre));

	// Vérification si c'est bien un nombre

    if (!is_numeric($nombre)) 
	{
        return 0;
    }

	// Arrondir le nombre au nombre de decimales demande

    $arrondi = round($nombre, $decimales);
    
    // Vérifier si le nombre arrondi est un entier

    if (floor($arrondi) == $arrondi) 
	{
    	return number_format($arrondi, 0, '', $separateur_millier);
    }

	// Formatter le nombre avec le bon séparateur de décimales

    $nombre_formatte = number_format($arrondi, $decimales, '.', $separateur_millier);

	// Enlever les zéros inutiles si demande

    if ($enlever_zeros) 
	{
        $nombre_formatte = rtrim(rtrim($nombre_formatte, '0'), '.');
    }

	// Convertir le point en virgule si demande

	if ($virgule)
	{
		$nombre_formatte = str_replace('.', ',', $nombre_formatte);
	}

	return $nombre_formatte;
}

/* ----------------------------------------------------------------------------
 *
 * P (PRINT)
 *
 * ----------------------------------------------------------------------------
 *
 * Cette fonction permet d'imprimer lisiblement des informations de debuggage
 * sur la page web.
 *
 * ---------------------------------------------------------------------------- */
function p($var)
{
	echo '<div style="margin-top: 60px;"></div>';

    echo '<pre>';

    echo gettype($var) . '<br />';

    if (is_bool($var))
    {
        if ($var === TRUE)
            print_r('TRUE');
        elseif ($var === FALSE)
            print_r('FALSE');
        else
            print_r('unknown bool value');
    }
    else
        print_r($var);  // var_dump($array);

    echo '</pre>';
}

/* --------------------------------------------------------------------------------------------
 *
 * DATE HUMANIZE
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction prend pour entree une date au format unix (epoch) et la transforme
 * en format humain sous la forme AAAA-MM-JJ, avec zeros pour les mois et les jours plus
 * petits que 10.
 *
 * Si $precision = TRUE, l'heure est ajoutee au format HH:MM:SS avec zeros.
 *
 * -------------------------------------------------------------------------------------------- */
function date_humanize($epoch, $precision = FALSE, $temps_seulement = FALSE)
{
    if ($temps_seulement === TRUE)
        return date('H:i:s', $epoch);

    if ($precision === TRUE)
        return date('Y-m-d H:i:s', $epoch);

    return date('Y-m-d', $epoch);
}

/* --------------------------------------------------------------------------------------------
 *
 * HOUR HUMANIZE
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction affiche le temps (l'heure).
 *
 * Si $precision = TRUE, les secondes sont ajoutees.
 *
 * -------------------------------------------------------------------------------------------- */
function hour_humanize($epoch, $precision = FALSE)
{
    if ($precision === TRUE)
        return date('H:i:s', $epoch);

    return date('H:i', $epoch);
}

/* --------------------------------------------------------------------------------------------
 *
 * DATE EPOCHIZE
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction prend une date au format humain, AAAA-MM-JJ, et la transforme en format
 * unix (epoch). Si la periode est 'start', l'heure est consideree comme etant 00:00:00 (debut
 * de la journee) alors que si la periode est 'end', l'heure consideree est 23:23:59 (fin de la
 * journee).
 *
 * -------------------------------------------------------------------------------------------- */
function date_epochize($human_date, $period = 'start')
{
    if (empty($human_date))
    {
        return FALSE;
    }

    list($year, $month, $day) = explode('-', $human_date);

    $year   = (int) $year;
    $month  = (int) $month;
    $day    = (int) $day;

    if ($period === 'start')
        return mktime(0, 0, 0, $month, $day, $year);

    if ($period === 'end')
        return mktime(0, 0, -1, $month, $day + 1, $year);

	return FALSE;
}

/* --------------------------------------------------------------------------------------------
 *
 * DATE EPOCHIZE PLUS (DATE + HOUR, format: YYYY-MM-DD HH:mm)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction permet de generer une date au format unix (epoch) a partir d'une date au 
 * format humain, AAAA-MM-JJ, pour n'importe quelle heure (donc pas seulement 'start' ou 'end') 
 * dans la version originale.
 *
 * -------------------------------------------------------------------------------------------- */
function date_epochize_plus($human_date)
{
    if (empty($human_date))
    {
        return FALSE;
    }

    list($date, $hour) = explode(' ', $human_date);

    list($year, $month, $day) = explode('-', $date);
    list($h, $min)            = explode(':', $hour);

    $year  = (int) $year;
    $month = (int) $month;
    $day   = (int) $day;

    $h     = (int) $h;
    $min   = (int) $min;

    return mktime($h, $min, 0, $month, $day, $year);
}

/* -------------------------------------------------------------------------------------------- 
 *
 * DATE_FRENCH_DAY
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction ajoute un 'er' si c'est la premiere journee du mois.
 *
 * -------------------------------------------------------------------------------------------- */
function date_french_day($day)
{
    if ($day === '1' || $day === 1 || $day === '01')
        return $day = $day . 'er';
        
    return $day;
}

/* -------------------------------------------------------------------------------------------- 
 *
 * DATE FRENCH MONTH
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction traduit le nom du mois en francais, et ajoute une majuscule si demande.
 *
 * ATTENTION: Ne pas utiliser mb_strtoupper() pour obtenir le mois complet en majuscules.
 *			  Utiliser le 2e argument 'all' pour parvenir a cette fin.
 *
 * -------------------------------------------------------------------------------------------- */
function date_french_month($month = '', $capitalization = '', $convert_chars = TRUE)
{
	// Si $month est vide, assumer le mois courant.
    if ($month === '')
    {
        $month = date('n');
    }
    else 
	{
		// Convertir le mois pour elimiter la presence de zeros initiaux (par precaution).
        $month = date('n', mktime(0, 0, 0, $month, 1, 2000));
    } 

	$month_name = array(
		'1' => 'janvier',
		'2' => ($convert_chars ? 'f&eacute;vrier' : 'février'),
		'3' => 'mars',
		'4' => 'avril',
		'5' => 'mai',
		'6' => 'juin',
		'7' => 'juillet',
		'8' => ($convert_chars ? 'ao&ucirc;t' : 'août'),
		'9' => 'septembre',
		'10' => 'octobre',
		'11' => 'novembre',
		'12' => ($convert_chars ? 'd&eacute;cembre' : 'décembre')
	);

    $output = $month_name[$month];

    if (empty($capitalization))
        return $output;

	// En majustcule: 'all' pour toutes les lettres, 'first' pour la premiere lettre seulement.
    if ($capitalization === 'all') 
    {
        if (strpos($output, '&') === FALSE)
            $output = strtoupper($output);
        else {
            list($pre, $suf) = explode('&', $output);
            list($mid, $end) = explode(';', $suf);
            $pre = strtoupper($pre);
            $mid = ucfirst($mid);
            $end = strtoupper($end);
            
            $output = $pre . '&' . $mid . ';' .  $end;
        }
    }
    elseif ($capitalization === 'first')
    {
        $output = ucfirst($output);
    }

    return $output;
}

/* -------------------------------------------------------------------------------------------- 
 *
 * DATE FRENCH WEEKDAY
 *
 * --------------------------------------------------------------------------------------------
 *
 * Affiche le jour de la semaine.
 *
 * -------------------------------------------------------------------------------------------- */
function date_french_weekday($epoch, $capital = FALSE)
{
    $jour_semaine = date('w', $epoch);

    $jours = array(
        0 => 'dimanche',
        1 => 'lundi',
        2 => 'mardi',
        3 => 'mercredi',
        4 => 'jeudi',
        5 => 'vendredi',
        6 => 'samedi'
    );

    $jour = NULL;

    if (array_key_exists($jour_semaine, $jours))
    {
        $jour = $jours[$jour_semaine];

        if ($capital)
        {
            $jour = ucfirst($jour);
        }
    }
    
    return $jour;
}

/* -------------------------------------------------------------------------------------------- 
 *
 * DATE FRENCH FULL
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction affiche la date en francais a partir d'une date unix (epoch) et d'une heure
 * quelconque si presente.
 *
 * -------------------------------------------------------------------------------------------- */
function date_french_full($date_epoch, $time = FALSE, $convert_chars = TRUE)
{
    $day    = date_french_day(date('j', $date_epoch));
    $month  = date_french_month(date('n', $date_epoch), '', $convert_chars);
    $year   = date('Y', $date_epoch);

    $output = $day . ' ' . $month . ' ' . $year;

    if ($time)
    {
        $hour = date('G', $date_epoch);
        $min  = date('i', $date_epoch);

        $output .= ', ' . $hour . 'h' . $min;
    }

    return $output;
}

/* ------------------------------------------------------------------------------------------
 *
 * DELETE ALL COOKIES
 *
 * ------------------------------------------------------------------------------------------
 *
 * Cette fonction efface tous les temoins de connexion d'un client.
 *
 * ------------------------------------------------------------------------------------------ */
function delete_all_cookies()
{
    $CI =& get_instance();

	$CI->load->helper('cookie');

    $cookies = $CI->config->item('cookies');

    foreach ($cookies as $c_key => $c_name)
    {
		delete_cookie($c_name, '.kovao.' . ($CI->is_DEV ? 'dev' : 'com'));
	}

    return;
}

/* --------------------------------------------------------------------------------------------
 *
 * ARRAY KEYS SWAP
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction permet de remplacer l'index d'un tableau par la valeur d'un ID de l'un des
 * champs. Ce ID doit etre unique. Cette fonction fonctionne seulement sur les tableaux
 * multidimensionnels.
 *
 * Si $strict = TRUE, cette fonction retournera FALSE si une entree a deja ete assignee
 * par un meme ID, ceci dans le but d'eviter les ID non uniques. De plus, le ID doit etre
 * present dans chaque sous-tableau.
 *
 * Cette fonction est utilisee regulierement a travers le code, donc ne pas modifier.
 *
 * Version anglaise :
 *
 * This function replaces the keys of an array for any of the key's value of its subarray.
 * When strict = TRUE, it will return FALSE if the value was already assigned as a key.
 * The key must exists in every subarray.
 *
 * Example:
 *
 * Original Array
 *
 * [0] => ( [cat_id] = 55
 *          [cat_name] = 'Cars'
 *        ),
 * [1] => ( [cat_id] = 23
 *          [cat_name] = 'Trucks'
 *
 * Modified Array
*
 * [55] => ( [cat_id] = 55
 *          [cat_name] = 'Cars'
 *         ),
 * [23] => ( [cat_id] = 23
 *          [cat_name] = 'Trucks'
 *
 * --------------------------------------------------------------------------------------------  */
function array_keys_swap($arr, $key, $strict = FALSE)
{
    if ( ! is_array($arr))
        return FALSE;

    $arr_out = array();

    foreach($arr as $a)
    {
        if ( ! array_key_exists($key, $a))
            return FALSE;

        if ($strict)
        {
            if (array_key_exists($a[$key], $arr_out))
                return FALSE;
        }

        $arr_out[$a[$key]] = $a;
    }

    return $arr_out;
}

/* --------------------------------------------------------------------------------------------
 *
 * ARRAY SEARCH RECURSIVE
 *
 * --------------------------------------------------------------------------------------------
 *
 * Searches haystack for needle and returns an array of the key path if it is found in the 
 * (multidimensional) array, FALSE otherwise.
 *
 * -------------------------------------------------------------------------------------------- */
function array_search_recursive($needle, $haystack, $strict = FALSE, $path = array())
{
    if ( ! is_array($haystack))
        return false;

    foreach ($haystack as $key => $val)
    {
        if (is_array($val) && $subPath = array_search_recursive($needle, $val, $strict, $path))
        {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        }
        elseif (( ! $strict && $val == $needle) || ($strict && $val === $needle))
        {
            $path[] = $key;
            return $path;
        }
    }

   return FALSE;
}

/* --------------------------------------------------------------------------------------------
 *
 * ARRAY SEARCH MULTI
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction cherche pour une valeur (ex. 201969883) et retourne la clef du tableau (305).
 * Si une colonne est precisee (ex. 'etudiant_id'), alors la valeur de cette colonne sera retournee
 * au lieu de la clef.
 *
 * [305] => Array
 *    (
 *        [etudiant_id] => 305
 *        [nom] => Zuppel
 *        [prenom] => Nicolas
 *        [numero_da] => 201969883
 *    )
 *
 * -------------------------------------------------------------------------------------------- */
function array_search_multi($needle, $arr, $column = NULL)
{
    $keys = array();

    foreach($arr as $k => $v)
    {
        if (is_array($v))
        {
            if ( ! empty($column))
            {
                $deep_keys = array_search_multi($needle, $v, $column);

                if ( ! empty($deep_keys))
                {
                    $keys = array_merge($keys, $deep_keys);
                }
            }
            else
            {
                if (array_search_multi($needle, $v))
                {
                    $keys[] = $k;
                }
            }

            continue;
        }

        if ( ! empty($column))
        {
            if ($v == $needle)
            {
                if (array_key_exists($column, $arr))
                {
                    $keys[] = $arr[$column];
                }
				else
				{
					// column does not exists
					return FALSE;
				}
            }
        }
        else
        {
            if ($v == $needle)
            {
                return TRUE;
            }
        }
    }

    return $keys;
}

/* --------------------------------------------------------------------------------------------
 *
 * ARRAY COLUMN MULTI
 *
 * --------------------------------------------------------------------------------------------
 *
 * 
 *
 * -------------------------------------------------------------------------------------------- */
function array_column_multi($needle, $arr)
{
    $values = array();

    if ( ! is_array($arr))
    {
        return array();
    }

    foreach($arr as $k => $v)
    {
        if (is_array($v))
        {
            $deep_values = array_column_multi($needle, $v);

            if ( ! empty($deep_values))
            {
                $values = array_merge($values, $deep_values);
            }

            continue;
        }
        else
        {
            if ($k == $needle && ! in_array($v, $values))
            {
                $values[] = $v;
            }
        }
    }

    return $values;
}

/* --------------------------------------------------------------------------------------------
 *
 * SW_COMMA
 *
 * --------------------------------------------------------------------------------------------
 *
 * Changer les points (.) par des virgules (,) dans les nombres.
 *
 * -------------------------------------------------------------------------------------------- */
function sw_comma($nombre, $decimals = NULL)
{
	if (is_float($nombre))
	{
		return number_format($nombre, 2, ',', '');
	}

	return $nombre;
}

/* --------------------------------------------------------------------------------------------
 *
 * SW_POINT
 *
 * --------------------------------------------------------------------------------------------
 *
 * Changer les virgules (,) par des points (.) dans les nombres.
 *
 * -------------------------------------------------------------------------------------------- */
function sw_point($nombre, $decimales = 2)
{
    if (strpos($nombre, ',') !== FALSE)
    {
        $nombre = str_replace(',', '.', $nombre);
    }

    if ( ! (is_numeric($nombre) || is_float($nombre)))
    {
        return NULL;
    }

    return number_format($nombre, $decimales, '.', '');
}

/* --------------------------------------------------------------------------------------------
 *
 * MYNL2BR
 *
 * --------------------------------------------------------------------------------------------
 *
 * Transformer les symboles \r et \n par le correspond HTML pour affichage.
 *
 * -------------------------------------------------------------------------------------------- */
function mynl2br($str)
{
    if (empty($str))
    {
        return NULL;
    }

    $str = mb_ereg_replace("\r\n\r\n", '<div class="pspace"></div>', $str);
    $str = mb_ereg_replace("\r\n", '<br />', $str);

    $str = mb_ereg_replace("\n\n", '<div class="pspace"></div>', $str);
    $str = mb_ereg_replace("\n", '<br />', $str);

    return $str;
}

/* --------------------------------------------------------------------------------------------
 *
 * BR2NL
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction effectue le contraire de 'nl2br'.
 *
 * -------------------------------------------------------------------------------------------- */
function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

/* --------------------------------------------------------------------------------------------
 *
 * NS FORMAT (VERSION 2)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction transforme un nombre en notation scientifique.
 * Contrairement a la version 1, elle n'affecte aucunement le nombre de CS.
 * Pour ajuster le nombre de CS, il faut utiliser cs_ajustement().
 *
 * -------------------------------------------------------------------------------------------- */
function ns_format($nombre, $pretty = TRUE)
{
    $nombre = str_replace(',', '.', $nombre);

    $nombre_original = $nombre;

    $cs = cs($nombre);

    //
    // Verifier si le nombre est negatif
    //

    $negatif = FALSE;

    if ($nombre < 0)
    {
        $negatif = TRUE;
        $nombre = -$nombre;
    }

    //
    // Verifier que le nombre ne soit pas deja en notation scientifique.
    //

	if (strpos($nombre, 'E') !== FALSE || strpos($nombre, 'e') !== FALSE)
    {
        $nombre = str_replace('.', ',', $nombre);

		$ns_nombre = $nombre;

		preg_match('/(.*)[E|e](.*)/', $ns_nombre, $matches);

		$valeur   = $matches[1];
        $exposant = $matches[2];
	}
	else
    {
        $ns_nombre = sprintf("%E", $nombre);

		preg_match('/(.*)[E|e](.*)/', $ns_nombre, $matches);

		$valeur   = $matches[1];
        $exposant = $matches[2];

        {
            // Log du nombre
            $log = log10($valeur);

			// Arrondir le log pour determiner la portion entiere.
            $logIntegerPart = floor($log);

			// Soustraire la portion entiere de la valeur du log pour determiner la portion fractionnaire.
            $logFractionalPart = $log - $logIntegerPart;

			// Calculer la valeur de 10 a la $logFractionalPart.
            $valeur = pow(10, $logFractionalPart);

			// Arrondir $value a une nombre specifique de chiffres significatifs.
            /*
                $valeur = round($valeur, $cs - 1);
            */

			// La valeur juste
            $valeur = $valeur * pow(10, $logIntegerPart); 

            $surete = 0;
            while ($surete < 20) 
            {
                $surete++;

                if ($surete == 1 && strpos($valeur, '.') === FALSE)
                {
                    if ($cs > 1)
                    {
                        $valeur .= '.';
                    }
                }

                if (cs($valeur) < $cs)
                {
                    $valeur .= 0;
                }
                else
                {
                    break;
                }
            }
        }

        if (strpos($exposant, '+') !== FALSE)
        {
            $exposant = str_replace('+', '', $exposant);
        }
	}

    if ($negatif)
    {
        $valeur = -$valeur;
    }

    $valeur = str_replace('.', ',', $valeur);

	if ($pretty)
	{
		$str = $valeur . '×10<sup>' . $exposant . '</sup>';
	}
	else
	{
		$str = $valeur . 'E' . $exposant;
    }

	return $str;
}

/* --------------------------------------------------------------------------------------------
 *
 * NS SEUL FORMAT
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction transforme une valeur 1E-3 en x10-3 sans considerer la valeur.
 *
 * -------------------------------------------------------------------------------------------- */
function ns_seul_format($exposant, $pretty = TRUE)
{
	if (empty($exposant))
		return NULL;

	if ($pretty)
	{
		$str = '×10<sup>' . $exposant . '</sup>&nbsp;';
	}
	else
	{
		$str = 'E' . $exposant;
    }

	return $str;
}

/* --------------------------------------------------------------------------------------------
 *
 * CS (version 1)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction determine le nombre de chiffres significatifs d'une valeur.
 *
 * -------------------------------------------------------------------------------------------- */
function cs_v1($nombre)
{
    $nombre = (string) $nombre;

    $nombre = str_replace('.', ',', $nombre);

	//
	// Enlever la notation scienfique
	//

	if (($nombre_ns = stristr($nombre, 'e', TRUE)) !== FALSE)
	{
		$nombre = $nombre_ns;
	}

    //
    // Est-ce qu'une virgule est presente dans le nombre ?
    //

    $virgule_presente = (strpos($nombre, ',') === FALSE ? FALSE : TRUE);

    $virgule_trouvee = FALSE;
    $premier_chiffre_non_zero_trouve = FALSE;
    $cs = 0;

    if ($virgule_presente)
    {
        $chiffres = str_split($nombre);

        $pos = strpos($nombre, ',');
        $pre = substr($nombre, 0, $pos); // chiffres avant la virgule
        $suf = substr($nombre, $pos+1); // chiffres apres la virgule

        foreach($chiffres as $c)
        {
            if ( ! $premier_chiffre_non_zero_trouve && $c == 0)
            {
                continue;
            }
            else
            {
                $premier_chiffre_non_zero_trouve = TRUE;
            }

            if ($c == ',')
            {
                $virgule_trouvee = TRUE;
                continue;
            }

			$cs++;

            continue;
        }
    }

    else
    {
        $nombre_propre = ltrim($nombre, '0');

        $chiffres = str_split($nombre_propre);

        //
        // Est-ce que le nombre se termine par un zero ?
        //

        $dernier_chiffre_zero = (end($chiffres) == 0) ? TRUE : FALSE;

        if ( ! $dernier_chiffre_zero)
        {
            $cs = strlen($nombre_propre);
        }
        else
        {
            preg_match('/^([1-9]+?)([1-9]*)(0*)$/', $nombre_propre, $matches);

            $cs_all = strlen($nombre_propre);
            $zeros = strlen(@$matches[3]);

            $cs = $cs_all;

            /*
             * Ceci correspond à la vraie règle mais n'est pas appliquée.
             *
            $cs = (string) ($cs_all - $zeros);

            for($i = 1; $i <= $zeros; $i++)
            {
                $cs .= ',' . ($cs + $i); 
            }
            */
        }

    }

    return $cs;
}

/* --------------------------------------------------------------------------------------------
 *
 * CS
 *
 * --------------------------------------------------------------------------------------------
 *
 * Version 2 (2024-08-06)
 *
 * -------------------------------------------------------------------------------------------- */
function cs($nombre) 
{
    $nombre_str = (string) $nombre;

	$nombre_str = str_replace(',', '.', $nombre_str);

	if (stripos($nombre_str, 'e') !== FALSE)
	{
		$pattern = '/[eE][+-]?[0-9]+/';
		$nombre_str = preg_replace($pattern, '', $nombre_str);
	}

    // Retirer les zéros initiaux et le signe négatif
    $nombre_str = ltrim($nombre_str, '-');
    
    // Compter les chiffres significatifs
    $count = 0;
    $nonZeroFound = false;

    for ($i = 0; $i < strlen($nombre_str); $i++) 
	{
        if (ctype_digit($nombre_str[$i])) 
		{
            if ($nombre_str[$i] !== '0') 
			{
                $nonZeroFound = true;
                $count++;
            } 
			elseif ($nonZeroFound) 
			{
                $count++;
            }
        } 
		elseif ($nombre_str[$i] === '.') 
		{
            continue;
        } 
		else 
		{
            break;
        }
    }
    
    return $count;
}

/* --------------------------------------------------------------------------------------------
 *
 * CS AJUSTEMENT (VERSION 2)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction ajuste une valeur au nombre de chiffres significatifs demandes.
 *
 * Je me suis base sur une des reponses de StackOverflow :
 * https://stackoverflow.com/questions/5834537/how-to-round-down-to-the-nearest-significant-figure-in-php
 * J'ai ensuite traite des cas limites comme les zeros et les nombres negatifs.
 *
 * -------------------------------------------------------------------------------------------- */
function cs_ajustement($nombre, $cs)
{
    //
    // $nombre : la valeur a ajuster
    // $cs     : le nombre de CS a conserver
    //

    //
    // Si le nombre de CS est 0, on retourne simplement la valeur originale.
    // Donc $cs = 0 equivaut a ne pas faire d'ajustement.
    //

    if ($cs == 0)
    {
        return $nombre;
    }

    //
    // Si necessaire, convertir en notation avec point pour separer les decimales (anglaise) afin 
    // d'etre interprete correctement, i.e. les virgules deviennent des points (5,77 = 5.77).
    //

    $nombre_orig = $nombre;
    $virgule = FALSE;

    if (strpos($nombre, ',') !== FALSE)
    {
        $virgule = TRUE;
        $nombre = str_replace(',', '.', $nombre);
    }

    //
    // Si la valeur est zero, on traite chaque 0 comme un CS.
    //
    // Exemples :
    //
    // 0       = 1 CS
    // 0.0     = 2 CS
    // 0.00    = 3 CS
    // 0.000   = 4 CS
    // 000 = 0 = 1 CS
    //
    // @TODO
    //

 	if ($nombre == 0) 
   		return 0;

    //
    // Si la valeur est negative, il faut la traiter comme une valeur positive
    // pour la remettre en negatif plus tard.
    //

	$negatif = FALSE;

	if ($nombre < 0)
	{
		$negatif = TRUE;
		$nombre = -$nombre;
    }

    //
    // Convertir les nombres tres petits en notation scientifique
    // Ceci pour regler un bogue lorsque le nombre est plus petit que float ou double.
    //

    $limite_minimum = 0.0000000001 / (0.1 ** $cs);

    if ($nombre < $limite_minimum)
    {
        $nombre = ns_format($nombre, FALSE);
        $nombre = str_replace(',', '.', $nombre);
    }

    //
    // Verifier les nombres en notation scientifique (E ou e)
    //

    $ns = FALSE;
    
    if (strpos(strtoupper($nombre), 'E'))
    {
        $ns = TRUE;
        $a  = strstr(strtoupper($nombre), 'E', TRUE);
        $b  = substr(strstr(strtoupper($nombre), 'E', FALSE), 1);

        $nombre = $a;
    }

    // Log du nombre
    $log = log10($nombre);

	// Determiner la portion entiere du log.
	$logIntegerPart = floor($log);

	// Determiner la portion fractionnaire du log.
	$logFractionalPart = $log - $logIntegerPart;

	// Calculer la valeur de 10 a la portion fractionnaire.
	$value = pow(10, $logFractionalPart);

	// Arrondir au nombre de chiffres significatifs souhaites.
    $value = round($value, $cs - 1);

	// La valeur juste
    $value = $value * pow(10, $logIntegerPart); 

    // Verifier et retourner la valeur si elle passe le test.
    //
	// Si le nombre de CS est plus petit que le nombre de chiffres desire,
    // c'est parce que c'est un nombre plus grand que 1 et qui se termine par 0 (ex. 950).

    if (
        cs($value) == $cs ||
        (cs($value) >= $cs && $value > 1)
       )
    {
        if ($ns)
        {
            $value = $value . 'E' . $b;
        }

		if ($negatif)
            return -$value;

		// J'ai ajoute ce 'if' pour transformer les nombres entiers se terminant par 0 en notation scientifique,
		// afin de lever l'ambiguite sur les CS. (2025-03-12)

        if (strpos($value, '.') === FALSE && $value % 10 === 0 && strpos($value, 'e') === FALSE && strpos($value, 'E') === FALSE)
        {
            // on est en presence d'une nombre entier qui se termine par 0 sans notation scientifique

			$value = valeur_en_ns($value, $cs - 1);

			if (strpos($value, 'e0') !== FALSE)
			{
				return str_replace('e0', '', $value);
			}

			if (strpos($value, 'E0') !== FALSE)
			{
				return str_replace('E0', '', $value);
			}

			return valeur_en_ns($value, $cs - 1);
        }

		return $value;
    }

	// Il manque des zeros.
    // Determiner combien il faut en ajouter.

    $zeros = $cs - cs($value);

	if (strpos($value, '.') === FALSE)
    {
		$value = $value . '.';		
    }

    if (strpos(strtoupper($value), 'E'))
    {
        $ns = TRUE;
        $a  = strstr(strtoupper($value), 'E', TRUE);
        $b  = substr(strstr(strtoupper($value), 'E', FALSE), 1);

        if ($zeros < 0)
        {
            $a = (float) $a; 
        }
        else
        {
            for($i = 1; $i <= $zeros; $i++)
            {
                $a = $a . '0';	
            }
        }

        $value = $a;
    }
    else
    {
        for($i = 1; $i <= $zeros; $i++)
        {
            $value = $value . '0';	
        }
    }

    if ($ns)
    {
        $value = $value . 'E' . $b;
    }

	if ($negatif)
	{
		return -$value;
    }

	return $value;
}

/* --------------------------------------------------------------------------------------------
 *
 * VALEUR EN NS (2025-03-12)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Transformer une valeur en notation scientifique.
 *
 * -------------------------------------------------------------------------------------------- */

function valeur_en_ns($nombre, $decimales = 6) 
{
    if ($nombre == 0) 
	{
        return '0E0';
    }
    
    $exposant = floor(log10(abs($nombre)));
    $mantisse = $nombre / pow(10, $exposant);
    
	return sprintf("%.{$decimales}fE%d", $mantisse, $exposant);
}

/* --------------------------------------------------------------------------------------------
 *
 * INCERTITUDE AJUSTEMENT
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction ajuste le nombre a une certaine incertitude.
 *
 * -------------------------------------------------------------------------------------------- */

function incertitude_ajustement($nombre, $incertitude)
{
    $virgule = strpos($nombre, ',') !== false;
    $notation_sci = stripos($nombre, 'E') !== false;

    if ($virgule) {
        $nombre = str_replace(',', '.', $nombre);
        $incertitude = str_replace(',', '.', $incertitude);
    }

    // Déterminer le nombre de décimales de l'incertitude
    $nombre_decimales = nombre_decimales2($incertitude);

    // Convertir en float
    $nombre = (float) $nombre;
    $incertitude = (float) $incertitude;

    // Détecter si l'incertitude est un multiple de 10
    if ($incertitude >= 1) {
        $ordreGrandeur = floor(log10($incertitude)); // 10 → 1, 100 → 2
        $arrondi = -$ordreGrandeur;
    } else {
        $arrondi = $nombre_decimales; // Nombre de décimales pour une petite incertitude
    }

    // Arrondir le nombre
    $nombreArrondi = round($nombre, $arrondi);

    // Si le nombre était en notation scientifique, on le garde ainsi
    if ($notation_sci) {
        $nombreArrondi = sprintf('%.'.max(0, $arrondi).'E', $nombreArrondi);
    } else {
        // Sinon, on formate normalement
        $nombreArrondi = number_format($nombreArrondi, max(0, $arrondi), '.', '');
    }

    // Reformater avec une virgule si nécessaire
    if ($virgule) {
        $nombreArrondi = str_replace('.', ',', $nombreArrondi);
    }

    return $nombreArrondi;
}

/* --------------------------------------------------------------------------------------------
 *
 * create password
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonctione genere un mot-de-passe encode a partir d'un texte.
 *
 * -------------------------------------------------------------------------------------------- */
function create_password($password_plaintext, $version)
{
    $CI =& get_instance();
    $CI->load->helper('string');

    //
    // Version 1
    //
    
    if ($version == 1)
    {
        $salt            = random_string('sha1');
        $hashed_password = password_hash($salt . $password_plaintext, PASSWORD_BCRYPT);
        $cookie_password = password_hash($CI->agent->agent_string() . $CI->input->ip_address() . $hashed_password, PASSWORD_BCRYPT);
    }
    else
    {
        return FALSE;
    }

    return array(
        'salt'            => $salt,
        'hashed_password' => $hashed_password,
        'cookie_password' => $cookie_password
    );
}

/* --------------------------------------------------------------------------------------------
 *
 * calculer_duree
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction calcule la duree entre deux epochs et retourne une string avec la duree.
 *
 * -------------------------------------------------------------------------------------------- */
function calculer_duree($debut = NULL, $fin = NULL)
{
	if ($debut === NULL || $fin === NULL)
		return '';

	$dd = new \DateTime("@$debut");                                                                                                                                                          
	$df = new \DateTime("@$fin");

    $duree = array(
		'j' => $df->diff($dd)->format('%a'),
		'h' => $df->diff($dd)->format('%h'),
		'm' => $df->diff($dd)->format('%i'),
		's' => $df->diff($dd)->format('%s')
	);

    $duree_str = '';

	foreach($duree as $etiquette => $val)
	{
		if (empty($val))
			continue;

		$duree_str .= $val . $etiquette;
	}

	return $duree_str;
}

/* --------------------------------------------------------------------------------------------
 *
 * caculer_longue_duree
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction calcule la duree entre deux epochs et retourne une string avec la duree.
 *
 * -------------------------------------------------------------------------------------------- */
function calculer_longue_duree($debut = NULL, $fin = NULL)
{
	if ($debut === NULL || $fin === NULL)
		return '';

	$dd = new \DateTime("@$debut");                                                                                                                                                          
    $df = new \DateTime("@$fin");

    $intervale = date_diff($dd, $df);

    $fr = array(
        'y' => array('année',   'années'),
        'm' => array('mois',    'mois'),
        'd' => array('jours',   'jours'),
        'h' => array('heure',   'heures'),
        'i' => array('minute',  'minutes'),
        's' => array('seconde', 'secondes')
    );

    $duree_str = '';
        
	foreach($intervale as $etiquette => $val)
	{
		if (empty($val))
            continue;

        if ( ! array_key_exists($etiquette, $fr))
            continue;

        $duree_str .= $val . ' ';

        if ($val > 1)
        {
            $duree_str .= $fr[$etiquette][1];
        }
        else
        {
            $duree_str .= $fr[$etiquette][0];
        }

        if ($etiquette != 's')
        {
            $duree_str .= ', ';
        }
    }

    return $duree_str;
}

/* --------------------------------------------------------------------------------------------
 *
 * STRIP ACCENTS
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction enleve les accents d'une string.
 *
 * -------------------------------------------------------------------------------------------- */
function strip_accents($str)
{
    return utf8_encode(strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
}

/* --------------------------------------------------------------------------------------------
 *
 * filter_symbols
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction filtre les tags <symbol>STRING</symbol> puis remplacer par les tags de
 * FontAwesome.
 *
 * -------------------------------------------------------------------------------------------- */
function filter_symbols($str)
{
	return preg_replace('/\<symbol\>(.*)\<\/symbol\>/', '<i class="fa fa-$1"></i>', $str);
}

/* --------------------------------------------------------------------------------------------
 *
 * unfilter_symbol
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction fait le contraire de 'filter_symbols'.
 *
 * -------------------------------------------------------------------------------------------- */
function unfilter_symbols($str)
{
	return preg_replace('/<i class=\"fa fa-(.*)"\><\/i>/', '<symbol>$1</symbol>', $str);
}

/* --------------------------------------------------------------------------------------------
 *
 * generer_erreur
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction genere une erreur.
 * Une version 2 a ete cree et celle-ci permet une retrocompatibilite avec le code inchange.
 *
 * -------------------------------------------------------------------------------------------- */
function generer_erreur($code = '999', $message = NULL, $options = array())
{
    //
    // Transformons les parametres pour utiliser la nouvelle version de generer_erreur.
    //

    $data         = $options;
    $data['code'] = $code;
    $data['desc'] = $message;

    //
	// silence 
	//
    // Ne pas logger cette erreur.
    //

    if (array_key_exists('silence', $data))
    {
        if ($data['silence'] === TRUE)
        {
            $data['importance'] = 0;
        }

        unset($data['silence']);
    }

    generer_erreur2($data);

    return;
}

/* --------------------------------------------------------------------------------------------
 *
 * generer_erreur 2
 *
 * --------------------------------------------------------------------------------------------
 *
 * Version 2 (2020-09-01)
 *
 * -------------------------------------------------------------------------------------------- */
function generer_erreur2($data = array())
{
    //
    // Champs possibles
    //

    $champs_possibles = array(
        'code',         // Un identifiant unique pour retrouver l'emplacement dans le code ayant cause l'alerte.
        'desc',         // Une description de l'alerte.
        'url',          // Permet une redirection vers l'URL.
        'extra',        // Des informations en extra pertinentes au debuggage pour l'alerte.
        'importance',   // L'importance de l'alerte (0 = silencieux, 9 = urgence)
    );

    $CI =& get_instance();

    //
    // Initialisaer par default les champs obligatoires
    //

    if ( ! array_key_exists('code', $data) || empty($data['code']))
    {
        $data['code'] = 999;;
    }
    
    if ( ! array_key_exists('desc', $data) || empty($data['desc']))
    {
        $data['desc'] = 'Nous sommes désolés de cet inconvénient.';
	}

    //
    // Enregistrer dans la session flash
    //

	$CI->session->set_flashdata('erreur_code',    $data['code']);
    $CI->session->set_flashdata('erreur_message', $data['desc']);

    //
    // URL (redirection)
    //

    if (array_key_exists('url', $data) && ! empty($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL))
    {
        $CI->session->set_flashdata('erreur_url', $data['url']);
    }

    //
    // Extra
    //

    if ( ! array_key_exists('extra', $data))
    {
        $data['extra'] = NULL;
    }

    //
    // Importance
    //

    if ( ! array_key_exists('importance', $data))
    {
        $data['importance'] = $CI->config->item('alertes_importance');
    }

    //
    // Enregistrer l'alerte
    //

    if ($data['importance'] != 0)
    {
        log_alerte(
            array(
                'code'       => $data['code'],
                'desc'       => $data['desc'],
                'importance' => $data['importance'],
                'extra'      => $data['extra']
            )
        );
    }

    if ($CI->input->is_ajax_request())
    {
        echo json_encode(array('redirect' => 'erreur/code/' . $data['code']));
        return;
    }

    redirect(base_url() . 'erreur/code/' . $data['code']);
    exit;
}

/* --------------------------------------------------------------------------------------------
 *
 * log_alerte
 *
 * --------------------------------------------------------------------------------------------
 *
 * Enregistrer une erreur (ou une alerte) dans la base de donnees.
 *
 * -------------------------------------------------------------------------------------------- */
function log_alerte($data = array())
{
    //
    // Champs possibles
    //

    $champs_possibles = array(
        'code',                     // (!) Un identifiant unique pour retrouver l'emplacement dans le code ayant cause l'alerte.
        'desc',                     // (!) Une description de l'alerte.
        'importance',               // L'importance de l'alerte (0 = silencieux, 9 = urgence)
        'extra',                    // Des informations en extra pertinentes au debuggage.
        'enseignant_id_concerne'    // L'enseignant ID concerne par cette alerte.

        // (!) denote un champ obligatoire
    );

    //
    // Champs obligatoires
    //

    $champs_obligatoires = array('code', 'desc');

    foreach($champs_obligatoires as $c)
    {
        if ( ! array_key_exists($c, $data) || empty($data[$c]))
        {
            return;
        }
    }

    $CI =& get_instance();

    //
    // Importance
    //
    // L'importance sert a determer un ordre de priorite aux alertes, de 0 a 9.
    // 0 = silence
    // 1 = peu important
    // 9 = urgent
    // 

    if ( ! array_key_exists('importance', $data))
    {
        $data['importance'] = $CI->config->item('alertes_importance');
    }

    //
    // Si l'importance est de zero, l'alerte est silencieuse donc ne pas l'enregistrer.
    //

    if ($data['importance'] == 0)
    {
        return;
    }

    //
    // Enregistrement de l'erreur (ou l'alerte) dans la base de donnees
    //

    $data = array_merge(
        // Les valeurs par defaut
		array(
			'epoch'      => $CI->now_epoch,
			'date'		 => date_humanize($CI->now_epoch, TRUE),
            'adresse_ip' => $_SERVER['REMOTE_ADDR'],
            'groupe_id'  => $CI->groupe_id ?: NULL,
			'uri'		 => $_SERVER['REQUEST_URI']
	   ),
	   $data
	);

	$CI->db->insert('alertes', $data);

	return TRUE;
}

/* --------------------------------------------------------------------------------------------
 *
 * nombre_decimales
 *
 * --------------------------------------------------------------------------------------------
 *
 * Determiner le nombre de decimales d'une valeur.
 *
 * -------------------------------------------------------------------------------------------- */

function decim($valeur)
{
	$valeur = (string) $valeur;

    if (preg_match('/\./', $valeur))
    {
        return strcspn(strrev($valeur), '.');
    }
    else if (preg_match('/,/', $valeur))
    {
        return strcspn(strrev($valeur), ',');
    }
    else
    {
        return 0;
    }
}

function nombre_decimales($valeur)
{
	if (empty($valeur)) return 0;

    //
    // Notation scientifique  a x 10^b (aeb)
    // 

    if (preg_match('/(.+)[E|e](\-?)([0-9]+)/', $valeur, $matches))
    {
        $a = $matches[1];
        $b = $matches[3];

        $a = str_replace(',', '.', $a);

        if ( ! empty($matches[2]))
        {
            // Un nombre entre 0 et 1
            
            $c = decim($a);

            return ($b + $c);    
        }
        else
        {
            // Un nombre plus grand que 1

            $multip = '1' . str_repeat('0', $b);
            $valeur = $a * $multip;
        }
	}

	$valeur = trim($valeur);
    return decim($valeur);
}

//
// Cette version 2, amelioree, prend en compte les nombres > 1 sans chiffres decimaux.
// Elle a ete generee par ChatGPT.
//

function nombre_decimales2($nombre) 
{
	$nombre = trim($nombre);

    // Convertir en chaîne et normaliser le séparateur décimal
	$nombre = str_replace(',', '.', (string) $nombre);

    // Vérifier si le nombre est en notation scientifique
    if (stripos($nombre, 'E') !== false) {
        $parties = explode('E', strtoupper($nombre));
        $mantisse = $parties[0];
        $exposant = (int) $parties[1];

        // Compter les décimales dans la mantisse
        $decimales_mantisse = strpos($mantisse, '.') !== false ? strlen(explode('.', $mantisse)[1]) : 0;

        // Ajuster en fonction de l'exposant
        return max(0, $decimales_mantisse - $exposant);
    }

    // Vérifier si le nombre contient un point décimal
    if (strpos($nombre, '.') === false) {
        return 0;
    }

    // Supprimer les zéros inutiles à la fin
    $nombre = rtrim($nombre, '0');

    // Séparer partie entière et décimale
    $parties = explode('.', $nombre);

    return isset($parties[1]) ? strlen($parties[1]) : 0;
}

/* ----------------------------------------------------------------------------
 *
 * FLOAT COMPARE
 *
 * ---------------------------------------------------------------------------- */
function float_cmp($a, $b)
{
    // Si les deux sont exactement égaux (ex: même chaîne ou même binaire)
    if ($a === $b) return 0;

    // Si l'un est NaN ou infini, on sort proprement
    if (!is_finite($a) || !is_finite($b)) {
        return ($a <=> $b);
    }

    // Calcul d'une tolérance adaptée à l'ordre de grandeur des nombres
    $diff = abs($a - $b);
    $scale = max(abs($a), abs($b), 1.0);
    $epsilon = 1e-12 * $scale;

    if ($diff < $epsilon) return 0;
    return ($a < $b) ? -1 : 1;
}

/* --------------------------------------------------------------------------------------------
 *
 * NSDEC
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction convertit la notation scientifique en notation decimale.
 *
 * -------------------------------------------------------------------------------------------- */
function nsdec(string $scientific): string
{
    $scientific = trim(str_replace(',', '.', $scientific));

    // Si pas de "e" ou "E", on retourne directement
    if (stripos($scientific, 'e') === false) {
        return $scientific;
    }

    // Vérifier que c'est numérique
    if (!is_numeric($scientific)) {
        return '0';
    }

    // Séparer la base et l'exposant
	[$base, $exp] = preg_split('/e/i', $scientific);

    $exp = (int)$exp;

    // Séparer partie entière / décimale
    if (strpos($base, '.') !== false) {
        [$intPart, $decPart] = explode('.', $base);
	} 
	else {
        $intPart = $base;
        $decPart = '';
    }

    if ($exp > 0) {
        // Déplace la virgule vers la droite
        $number = $intPart . $decPart;

        // Si l'exposant dépasse la longueur de la partie décimale, on complète par des zéros
        if ($exp > strlen($decPart)) {
            $number .= str_repeat('0', $exp - strlen($decPart));
            $result = $number;
		} 
		else {
            // Sinon, on insère la virgule
            $pos = strlen($intPart) + $exp;
            $result = substr($number, 0, $pos) . '.' . substr($number, $pos);
        }
	} 
	elseif ($exp < 0) {
        // Déplace la virgule vers la gauche
        $exp = abs($exp);
        $number = str_pad($intPart, $exp + strlen($intPart), '0', STR_PAD_LEFT);
        $result = '0.' . str_repeat('0', $exp - strlen($intPart)) . $intPart . $decPart;
        // Nettoyer les éventuels zéros en trop
        $result = preg_replace('/^0+(\d)/', '0.$1', $result);
	} 
	else {
        $result = $base;
    }

    // Nettoyer les zéros de gauche
    $result = preg_replace('/^0+(\d)/', '$1', $result);

    // Supprimer un éventuel point final
    $result = rtrim($result, '.');

    // Si le nombre original avait un ".0" significatif
    if (preg_match('/\.\d*0+e/i', $scientific) && strpos($result, '.') === false) {
        $result .= '.0';
    }

    return $result;
}

function OLD_nsdec(string $scientific): string 
{
    if ( ! str_contains(strtolower($scientific), 'e')) 
	{
        return $scientific; // Pas de notation scientifique, on retourne tel quel
    }
	
	$scientific = str_replace(',', '.', $scientific);

	$cs = cs($scientific);

	if ( ! is_numeric($scientific))
	{
		return 0;
	}

    // Convertir en float et formatter en notation décimale avec haute précision
    $decimal = (string) ($scientific + 0); // Force la conversion en notation décimale

    // Vérifier si l'entrée avait des zéros significatifs après la virgule
    if (preg_match('/\.\d*0+$/', $scientific)) 
	{
        // Retrouver le nombre exact de décimales
        [$base] = explode('e', strtolower($scientific));
        $precision = strlen(explode('.', $base)[1] ?? '') ?? 0;
        $decimal = number_format((float)$scientific, $precision, '.', '');
    }

	if (strpos($decimal, '.'))
	{
		$decimal = cs_ajustement($decimal, $cs);	
	}

    return $decimal;
}

/* --------------------------------------------------------------------------------------------
 *
 * DETERMINER EXTENSION
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction determine l'extension d'un fichier.
 *
 * -------------------------------------------------------------------------------------------- */
function determiner_extension($nom_fichier)
{
    if (empty($nom_fichier))
        return NULL;

    if ( ! preg_match('/\.(.*)$/', $nom_fichier, $matches))
        return NULL;

    if (empty($matches[1]))
        return NULL;

    return $matches[1];
}

/* --------------------------------------------------------------------------------------------
 *
 * DETERMINER FILE ICON
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction determine quel icon de FontAwesome utiliser selon le type de fichier.
 *
 * -------------------------------------------------------------------------------------------- */
function determiner_file_icon($mime_type)
{
    switch($mime_type)
    {
        case 'application/pdf' :
            $file_icon = 'fa-file-pdf-o';
            break;
        case 'image/jpg' :
        case 'image/jpeg' :
        case 'image/gif' :
        case 'image/png' :
            $file_icon = 'fa-file-image-o';
            break;
        case 'video/mpeg' :
        case 'video/mpg' :
            $file_icon = 'fa-file-video-o';
            break;
        case 'application/msword' :
        case 'application/vnd.openxmlformats-officedocument.word' :
            $file_icon = 'fa-file-word-o'; 
            break;
        case 'application/vnd.ms-excel' :
        case 'application/vnd.ms-office' :
        case 'application/vnd.openxmlformats-officedocument.spre' :
            $file_icon = 'fa-file-excel-o';
            break;
        case 'application/vnd.ms-powerpoint' :
        case 'application/vnd.openxmlformats-officedocument.pres' :
            $file_icon = 'fa-file-powerpoint-o';
            break;
        default :
            $file_icon = 'fa-file-o';
    }

    return $file_icon;
}

/* --------------------------------------------------------------------------------------------
 *
 * TRUNCATE
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction permet de couper une string et d'y ajouter '...' apres un certain nombre de
 * caracteres.
 *
 * -------------------------------------------------------------------------------------------- */
function truncate($text, $chars = 25) 
{
    if (strlen($text) <= $chars) 
	{
        return $text;
    }

    $text = $text . ' ';
    $text = substr($text, 0, $chars);
    $text = substr($text, 0, strrpos($text, ' '));
    $text = $text . '...';

    return $text;
}

/* --------------------------------------------------------------------------------------------
 *
 * FUZZY DATE (FRANCAIS)
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonctione permet d'afficher la date de facon vague.
 *
 * -------------------------------------------------------------------------------------------- */
function fuzzy_date($epoch)
{
    $now = date('U');

    $diff = $now - $epoch;

	$numMins   = round($diff/60);
	$numHeures = round($numMins/60);
	$numJours  = round($numHeures/24);
	$numSems   = round($numJours/7);
	$numMois   = round($numSems/4.33);
	$numAns    = round($numMois/12);

    $fuzzy = array(
        29*60   => "il y a moins de 30 minutes",
        60*60   => "il y a moins d'une heure",
        120*60  => "il y a moins de deux heures",
        1440*60 => "il y a quelques heures"
    );

	$s = '';

	if (($diff < 60) || ($numMins <= 1)) 
	{
		$s = "il y a une minute";
	} 
	elseif ($numHeures == 0) 
	{
		$s = 'il y a ' . $numMins . " minutes";
	} 
	elseif ($numJours == 0) 
	{
		if ($numHeures > 1) 
		{
			$s = 'il y a ' . $numHeures . " heures";
		} 
		else 
		{
			$s = "il y a une heure";
		}
	} 
	elseif ($numSems == 0) 
	{
		if ($numJours > 1) 
		{
			$s = 'il y a ' . $numJours . " jours";
		} 
		else 
		{
			$s = "hier";
		}
	} 
	elseif ($numMois == 0) 
	{
		if ($numSems > 1) 
		{
			$s = 'il y a ' . $numSems . " semaines";
		} 
		else 
		{
			$s = "la semaine dernière";
		}
	} 
	else 
	{
		if ($numMois >= 1 && $numMois < 4) 
		{
			$s = 'il y a ' . $numMois . " mois";
		} 
		elseif ($numMois >= 4) 
		{
			$s = "il y a plusieurs mois";
		}
	}
    
	return $s;
}

/* --------------------------------------------------------------------------------------------
 *
 * ENLEVER_ACCENTS
 *
 * --------------------------------------------------------------------------------------------
 *
 * Cette fonction d'enlever les accents provient de Wordpress.
 *
 * -------------------------------------------------------------------------------------------- */
function enlever_accents($string) 
{
    if ( ! preg_match('/[\x80-\xff]/', $string))
        return $string;

	$chars = array(

	// Decompositions for Latin-1 Supplement

    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}

/* ----------------------------------------------------------------------------
 *
 * Decompresser les champs des soumissions
 *
 * ----------------------------------------------------------------------------
 *
 * 2020-04-12 : J'ai ajoute la possibilite de ne pas decompresser les champs
 *              qui ne se seraient pas correctement compresses car je veux conserver 
 *              les donnees advenant un probleme, ce qui semble s'etre produit
 *              aujourd'hui.
 *
 * ---------------------------------------------------------------------------- */
function decompresser_soumissions($soumissions = array())
{
    if (empty($soumissions) || ! is_array($soumissions))
    {
        return array();
    }

    $soumissions_out = array();

    $mdim_verifie = FALSE; // La multidimentionnalite du tableau a ete verifie

    foreach($soumissions as $soumission_id => $soumission)
    {
        if ( ! $mdim_verifie && ! is_array($soumission))
        {
            // Ce tableau n'est pas multidimentionnel.
            // Il ne contient qu'une seule soumission.

            $s_out = $soumissions;

            $s_out['evaluation_data'] = json_decode(@gzuncompress($soumissions['evaluation_data_gz']) ?: $soumissions['evaluation_data_gz'], TRUE);
            $s_out['cours_data']      = json_decode(@gzuncompress($soumissions['cours_data_gz'])      ?: $soumissions['cours_data_gz'], TRUE);
            $s_out['questions_data']  = json_decode(@gzuncompress($soumissions['questions_data_gz'])  ?: $soumissions['questions_data_gz'], TRUE);
            $s_out['images_data']     = empty($soumissions['images_data_gz']) ? NULL : json_decode(@gzuncompress($soumissions['images_data_gz']) ?: $soumissions['images_data_gz'], TRUE);
            $s_out['documents_data']  = empty($soumissions['documents_data_gz']) ? NULL : json_decode(@gzuncompress($soumissions['documents_data_gz']) ?: $soumissions['documents_data_gz'], TRUE);
            return $s_out;
        }

        $mdim_verifie = TRUE;

        $s_out = $soumission;

        $s_out['evaluation_data'] = json_decode(@gzuncompress($soumission['evaluation_data_gz']) ?: $soumission['evaluation_data_gz'], TRUE);
        $s_out['cours_data']      = json_decode(@gzuncompress($soumission['cours_data_gz'])      ?: $soumission['cours_data_gz'], TRUE);
        $s_out['questions_data']  = json_decode(@gzuncompress($soumission['questions_data_gz'])  ?: $soumission['questions_data_gz'], TRUE);
        $s_out['images_data']     = empty($soumission['images_data_gz']) ? NULL : json_decode(@gzuncompress($soumission['images_data_gz']) ?: $soumission['images_data_gz'], TRUE);
        $s_out['documents_data']  = empty($soumission['documents_data_gz']) ? NULL : json_decode(@gzuncompress($soumission['documents_data_gz']) ?: $soumission['documents_data_gz'], TRUE);

        if ($soumission_id == $soumission['soumission_id'])
        {
            $soumissions_out[$soumission_id] = $s_out;
        }
        else
        {
            $soumissions_out[] = $s_out;
        }

    }

    return $soumissions_out;
}

/* ----------------------------------------------------------------------------
 *
 * Verifier si la chaine est une chaine JSON.
 *
 * ---------------------------------------------------------------------------- */
function is_json($string) 
{
    json_decode($string);

    return (json_last_error() == JSON_ERROR_NONE);
} 

/* ----------------------------------------------------------------------------
 *
 * Shuffle un tableau en preservant l'association key => value.
 *
 * ---------------------------------------------------------------------------- */
function shuffle_assoc(&$array) 
{
	if ( ! (is_array($array) && ! empty($array)))
	{
		return TRUE;
	}

	$keys = array_keys($array);

	shuffle($keys);

	foreach($keys as $key) 
	{
		$new[$key] = $array[$key];
	}

	$array = $new;

	return TRUE;
}

/*
function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}
*/

/* ----------------------------------------------------------------------------
 *
 * Calculer la deviation standard
 *
 * ---------------------------------------------------------------------------- */
function std_dev($arr)
{ 
    $num_of_elements = count($arr); 
      
    $variance = 0.0; 
      
    // calculating mean using array_sum() method 
    $average = array_sum($arr)/$num_of_elements; 
      
    foreach($arr as $i) 
    { 
        // sum of squares of differences between  
        // all numbers and means. 
        $variance += pow(($i - $average), 2); 
    } 
      
    return (float)sqrt($variance/$num_of_elements); 
} 

/* ----------------------------------------------------------------------------
 *
 * Verifier les tags
 *
 * ----------------------------------------------------------------------------
 *
 * Version 2
 *
 * Cette methode sert a verifier si les tags HTML sont bien ouverts et fermes.
 * Utilisation des regex pour les tags plus complexes.
 *
 * Ceci dans le but d'eviter que les utilisateurs entrent des tags HTML incomplets.
 *
 * ---------------------------------------------------------------------------- */
function verifier_tags($str)
{
    $tags_permis = '<a><b><center><em><i><mark><pre><s><small><strong><sup><sub><tt><u><var>';
        
    //
    // Enlever tous les tags qui ne sont pas autorises.
    //

    $str = strip_tags($str, $tags_permis);

    //
    // Verifier les tags permis.
    //

	$tags = array(
        array('o' => '\<a.*?\>',    'c' => '\<\/a\>',   'type' => 'regex'),
		array('o' => '<b>', 		'c' => '</b>',      'type' => 'subs'),
		array('o' => '<center>', 	'c' => '</center>', 'type' => 'subs'),
		array('o' => '<em>', 		'c' => '</em>',     'type' => 'subs'),
		array('o' => '<i>', 		'c' => '</i>',      'type' => 'subs'),
		array('o' => '<mark>',		'c' => '</mark>',   'type' => 'subs'),
		array('o' => '<pre>', 		'c' => '</pre>',    'type' => 'subs'),
		array('o' => '<s>', 		'c' => '</s>',      'type' => 'subs'),
		array('o' => '<small>', 	'c' => '</small>',  'type' => 'subs'),
        array('o' => '<strong>', 	'c' => '</strong>', 'type' => 'subs'),
		array('o' => '<sub>', 		'c' => '</sub>',    'type' => 'subs'),
		array('o' => '<sup>', 		'c' => '</sup>',    'type' => 'subs'),
		array('o' => '<tt>', 		'c' => '</tt>',     'type' => 'subs'),
        array('o' => '<u>', 		'c' => '</u>',      'type' => 'subs'),
		array('o' => '<var>',		'c' => '</var>',    'type' => 'subs'),
	);

	foreach($tags as $t)
	{
		// $count_o : count opening tag
		// $count_c : count closing tag

		$ot = $t['o'];
		$ct = $t['c'];

        if ($t['type'] === 'regex')
        {
            //
            // TYPE : regex
            //

            preg_match_all('/' . $ot . '/', $str, $mo);
            preg_match_all('/' . $ct . '/', $str, $mc);

            if ( ! empty($mo[0]) || ! empty($mc[0]))
            {
                if (count($mo[0]) != count($mc[0]))
                {
                    // Enlever les tags invalides

                    foreach($mo as $m)
                    {
                        $str = str_replace($m, '', $str);
                    }

                    foreach($mc as $m)
                    {
                        $str = str_replace($m, '', $str);
                    }
                }
            }
        }
        else
        {       
            //
            // TYPE : substitutions (subs)
            //

            // Verifier a partir des targs ouverts

            if (($count_o = substr_count($str, $ot)) > 0)
            {
                $count_c = substr_count($str, $ct);

                if ($count_o != $count_c)
                {
                    $str = str_replace($ot, '', $str);
                    $str = str_replace($ct, '', $str);
                }
            }

            // Verifier a partir des tags fermes

            if (($count_c = substr_count($str, $ct)) > 0)
            {
                $count_o = substr_count($str, $ot);

                if ($count_o != $count_c)
                {
                    $str = str_replace($ot, '', $str);
                    $str = str_replace($ct, '', $str);
                }
            }
        }
	}
	
	return $str;
}

