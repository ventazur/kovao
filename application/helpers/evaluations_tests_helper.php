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
 * TESTS : EVALUATIONS HELPER
 *
 * -------------------------------------------------------------------------------------------- */

/* ----------------------------------------------------------------------------
 *
 * DETERMINER LES VALEURS DES VARIABLES ALEATOIREMENT (VERSION 2)
 *
 * ---------------------------------------------------------------------------- */
function determiner_valeurs_variables_test()
{
	$data = array();

	$data[] = array('A' => array('minimum' => '1',          'maximum' => '9',       'decimales' => 2, 'cs' => 0));
	$data[] = array('A' => array('minimum' => '0.015',      'maximum' => '0.0955',  'decimales' => 0));
	$data[] = array('A' => array('minimum' => '0.0015',     'maximum' => '0.0955',  'decimales' => 0));
	$data[] = array('A' => array('minimum' => '0.00000001', 'maximum' => '0.00001', 'decimales' => 0));
	$data[] = array('A' => array('minimum' => '0.00000001', 'maximum' => '0.00001', 'decimales' => 1));
	$data[] = array('A' => array('minimum' => '0.00000001', 'maximum' => '0.00001', 'decimales' => 10));
	$data[] = array('A' => array('minimum' => '0.00000001', 'maximum' => '0.01',    'decimales' => 10));
	$data[] = array('A' => array('minimum' => '0.00000001', 'maximum' => '0.00001', 'decimales' => 14)); // max = 14

	foreach($data as $d)
    {
        $s = $d['A'];

        $i=0;

        echo '<pre>min : ' . $s['minimum'] . ', max : ' . $s['maximum'] . ', decimals : ' . $s['decimales'] . '</pre><br />';

        while ($i < 20)
        {
            $r = determiner_valeurs_variables($d);

            $a = $r['A'];


            echo '[' . $a . ' : ';

            if ($a <= $s['maximum'] && $a >= $s['minimum'])
            {
                if (array_key_exists('cs', $s) && ! empty($s['cs']))
                {
                    if ($s['cs'] == cs($a))
                    {
                        echo 'OK';
                    }
                    else
                    {
                        echo 'FAILEDcs';
                    }
                }
                else
                {
                    echo 'OK';
                }
            }
            else
            {
                echo '<span style="color: crimson">FAILED</span>';
            }

            echo '] ';

            $i++;
        }

        echo '<br /><br />';
	}
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION A REPONSE NUMERIQUE PAR EQUATION (TYPE 9)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_type_9_test()
{
    $variables = array(
        'A' => '8.00',
        'B' => '9.13',
        'C' => '8.351E-5', 
        'D' => '1.7E-12',
        'F' => '0.00000000000000087',
        'G' => '9.998E-32',
        'H' => '2.96'
    );

    $data = array();

    $data[] = array(
        'reponse_repondue' => '219',
        'reponses'         => array(array('reponse_texte' => 'A*250/B', 'cs' => 3)),
    );

    $data[] = array(
        'reponse_repondue' => '-4.08',
        'reponses'         => array(array('reponse_texte' => 'log(C)', 'cs' => 3)),
    );

    $data[] = array(
        'reponse_repondue' => '4.08',
        'reponses'         => array(array('reponse_texte' => 'abs(log(C))', 'cs' => 3)),
    );

    $data[] = array(
        'reponse_repondue' => '11.77',
        'reponses'         => array(array('reponse_texte' => 'abs(log(D))', 'cs' => 4)),
    );

    $data[] = array(
        'reponse_repondue' => '15.1',
        'reponses'         => array(array('reponse_texte' => 'abs(log(F))', 'cs' => 3)),
    );

    $data[] = array(
        'reponse_repondue' => '31.000',
        'reponses'         => array(array('reponse_texte' => 'abs(log(G))', 'cs' => 5)),
    );

    $data[] = array(
        'reponse_repondue' => '9.120E-12',
        'reponses'         => array(array('reponse_texte' => '1E-14/(10^-H)', 'cs' => 4)),
    );

    foreach($data as $k => $d)
    {
        //
        // Ajuster les arguments
        //

        $d['variables'] = $variables;
        $d['points']    = 10;
        $d['reponse']['equation'] = TRUE;

        $a = corriger_question_type_9($d);

        echo '[' . $d['reponses'][0]['reponse_texte'] . '] : ';

        if ((string) $a['reponse_correcte_texte'] === (string) $d['reponse_repondue'])
        {
            echo 'YES';
        }
        else
        {
            echo '[' . $a['reponse_correcte_texte'] . '] != [' . $d['reponse_repondue'] . ']';
        }

        echo '<br />';
    }

}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION NUMERIQUE
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_numerique_test()
{
	$points = 10;

	$data = array();

	$data[] = array('r' => '3,3E0', 'rc' => '3,3', 'p' => 10, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3*10^0', 'rc' => '3,3', 'p' => 10, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '4.6', 'rc' => '5', 'p' => 9, 'tol' => array('tolerance' => 10, 'type' => 2, 'penalite' => 10));
	$data[] = array('r' => '4.4', 'rc' => '5', 'p' => 0, 'tol' => array('tolerance' => 10, 'type' => 2, 'penalite' => 10));
	$data[] = array('r' => '0', 'rc' => '0', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '0.00', 'rc' => '0', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '00000', 'rc' => '0', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '122.5', 'rc' => '123', 'p' => 9, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '122.49', 'rc' => '123', 'p' => 8, 'tol' => array('tolerance' => 1, 'type' => 1, 'penalite' => 20));
	$data[] = array('r' => '122.49', 'rc' => '123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '123', 'rc' => '123', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '122,8', 'rc' => '123', 'p' => 9, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '122.8', 'rc' => '123', 'p' => 9, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '0122.8', 'rc' => '123', 'p' => 9, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1,228E2', 'rc' => '123', 'p' => 9, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '0,001228E3', 'rc' => '123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '0,001228E5', 'rc' => '123', 'p' => 9, 'tol' => array('tolerance' => '0.5', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '---123', 'rc' => '123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '---123', 'rc' => '-123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '-123', 'rc' => '-123', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '--123', 'rc' => '-123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '-123', 'rc' => '123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '+123', 'rc' => '123', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '++123', 'rc' => '123', 'p' => 10, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1,1E-8', 'rc' => '1E-8', 'p' => 9, 'tol' => array('tolerance' => '1E-9', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1,1E-8', 'rc' => '1E-8', 'p' => 9, 'tol' => array('tolerance' => '1E-7', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => 'aaaa', 'rc' => '123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '', 'rc' => '123', 'p' => 0, 'tol' => array('tolerance' => 0.5, 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => ',001', 'rc' => '0,001', 'p' => 10, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '001', 'rc' => '0,001', 'p' => 0, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '0,001', 'rc' => '1E-3', 'p' => 10, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1e-3', 'rc' => '0,001', 'p' => 10, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1x10^-3', 'rc' => '0,001', 'p' => 10, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3x10^-3', 'rc' => '0,003', 'p' => 10, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1,1x10^-3', 'rc' => '0,001', 'p' => 9, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1,1 x10^ -3', 'rc' => '0,001', 'p' => 9, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1,1 x 10 ^ -3', 'rc' => '0,001', 'p' => 9, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '1.1x 10 ^ -3', 'rc' => '0,001', 'p' => 9, 'tol' => array('tolerance' => '1E-3', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3x10³', 'rc' => '3300', 'p' => 10, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3x10²', 'rc' => '330', 'p' => 10, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3x10²', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3*10^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3*10^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3e10^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3*10^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3e^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3*e^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));
	$data[] = array('r' => '3,3xe^2', 'rc' => '331', 'p' => 9, 'tol' => array('tolerance' => '1', 'type' => 1, 'penalite' => 10));

echo '
	<style>
		table { font-family: Arial; font-size: 0.95em; margin: auto; margin-top: 30px; width: 80%; }
		td { padding: 5px; text-align: center; border: 1px solid #ddd; }
	</style>
	Unit Test: <strong>corriger_question_numerique</strong>
	<table>
		<tr>
			<th>réponse</th>
			<th>réponse correcte</th>
			<th>tolérance</th>
			<th>réponse<br />retournée</th>
			<th>réponse correcte<br />retournée</th>
			<th>points<br />prévus</th>
			<th>points<br />obtenus</th>
		</tr>
';

	foreach($data as $test_no => $d)
	{
		$corrections = corriger_question_numerique($d['r'], $d['rc'], $points, array($d['tol']));

			echo '<tr style="' . ($corrections['points_obtenus'] != $d['p'] ? 'background: pink;' : 'background: #DCEDC8') . '">';
			echo '<td>' . $d['r'] . '</td>';
			echo '<td>' . $d['rc'] . '</td>';
			echo '<td>' . $d['tol']['tolerance'] . '</td>';
			echo '<td>' . $corrections['reponse'] . '</td>';
			echo '<td>' . $corrections['reponse_correcte'] . '</td>';
			echo '<td>' . $d['p'] . '</td>';
			echo '<td>' . $corrections['points_obtenus'] . '</td>';
			echo '</tr>';				
	}
}

/* ----------------------------------------------------------------------------
 *
 * CORRIGER QUESTION LITTERALE COURTE (VERSION 3)
 *
 * ---------------------------------------------------------------------------- */
function corriger_question_litterale_courte3_test()
{
	$points = 10;

	$data = array();

	$data[] = array('r' => 'rhombique alea', 'rc' => 'rhombique (aléa)', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'gâto', 'rc' => 'gâteau', 'sim' => 92, 'p' => 0);
	$data[] = array('r' => 'base', 'rc' => 'basique', 'sim' => 72, 'p' => 10);
	$data[] = array('r' => 'alphacentauri', 'rc' => 'alpha centauri', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'alpha centauri', 'rc' => 'Alpha Centauri', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'alpha+centauri', 'rc' => 'Alpha Centauri', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'alpha    centauri', 'rc' => 'Alpha Centauri', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'lauto', 'rc' => 'l\'auto', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'ephemere', 'rc' => 'éphémère', 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'betelgeuse', 'rc' => ['betelgeuse', 'etoile betelgeuse'], 'sim' => 92, 'p' => 10);
	$data[] = array('r' => 'G', 'rc' => ['H'], 'sim' => 100, 'p' => 0);

echo '
	<style>
		table { font-family: Arial; font-size: 0.95em; margin: auto; margin-top: 30px; width: 80%; }
		td { padding: 5px; text-align: center; border: 1px solid #ddd; }
		pre { font-family: Arial; }
	</style>
	Unit Test: <strong>corriger_question_litterale_courte3</strong> (version 3)
	<table>
		<tr>
			<th>réponse</th>
			<th>réponse correcte</th>
			<th>similarité</th>
			<th>similarité<br />calculée</th>
			<th>points<br />prévus</th>
			<th>points<br />obtenus</th>
		</tr>
';

	foreach($data as $test_no => $d)
	{
		$corrections = corriger_question_litterale_courte3($d['r'], $d['rc'], $points, $d['sim']);

			echo '<tr style="' . ($corrections['points_obtenus'] != $d['p'] ? 'background: pink;' : 'background: #DCEDC8') . '">';
			echo '<td><pre>' . $d['r'] . '</pre></td>';
			echo '<td>' . $corrections['reponse_acceptable'] . '</td>';
			echo '<td>' . $d['sim'] . '</td>';
			echo '<td>' . $corrections['similarite'] . '</td>';
			echo '<td>' . $d['p'] . '</td>';
			echo '<td>' . $corrections['points_obtenus'] . '</td>';
			echo '</tr>';				
	}
}

/* ----------------------------------------------------------------------------
 *
 * LAB CORRIGER METHODE DES EXTREMES 
 *
 * ---------------------------------------------------------------------------- */
function lab_corriger_methode_extremes_test()
{
	$data = array(
		0 => array([10, 10, 10], [1, 2, 2]),
		1 => array([10, 10, 10], [0.5, 0.6, 0.7]),
		2 => array([10.5, 10.4, 10.9], [NULL, 0.6, 0.6]),
		3 => array([11.2, 11.35, 11.24, 11.3], [0.06, 0.06, 0.06, 0.06]),
		4 => array([5.2, 5.8, 5.98], []),
		5 => array([11.2, 10.3, 10.52, '9,9'], ['0,06', 0.06, 0.06, 0.06]),
		6 => array([11.2, 10.3, 10.52, 9.9], ['a', 0.06, '0.06', 0.6])
	);

	$datar = array(
		0 => 2,
		1 => 0.7,
		2 => 0.85,
		3 => 0.135,
		4 => 0.39,
		5 => 0.71,
		6 => 0.95
	);

	echo "TEST de l'incertitude par la méthode des extrêmes<br /><br />";

	foreach($data as $i => $arr)
	{
		$inc = lab_corriger_methode_extremes($arr[0], $arr[1]);
		$rep = $datar[$i];

		echo "INC = [" . $inc . "], REP = [" . $rep . "] " . ($inc == $rep ? '<span style="color: limegreen">OK</span>' : '<span style="color: crimson">FAILED</span>') . "<br/>";

	}
}

