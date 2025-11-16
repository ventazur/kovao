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
 * TESTS : GENERAL HELPER
 *
 * -------------------------------------------------------------------------------------------- */

function sw_point_test() 
{
    $values = array(
        1 => array('0,2', '0.20', 2),
        2 => array('0.2', '0.20', 2),
        3 => array('56,15123', '56.15'),
        4 => array('58,55462', '58.555', 3),
        5 => array('745,634545', '746', 0),
        6 => array('ALLO', NULL),
        7 => array('889.145', '889.15')
    );

    foreach($values as $k => $arr)
    {
        $nombre_avant = $arr[0];
        $nombre_apres = $arr[1]; 
        $decimales    = isset($arr[2]) ? $arr[2] : NULL;

        if ($decimales === NULL)
        {
            $nombre = sw_point($nombre_avant);
        }
        else
        {
            $nombre = sw_point($nombre_avant, $decimales);
        }

        if ($nombre_apres !== $nombre)
        {
            echo $k . ' == FAILED (' . $nombre_apres . ' != ' . $nombre . ')<br />';
        }
        else
        {
            echo $k . ' == PASS! (' . $nombre . ')<br />';
        }
    }

    return;
}

function ns_format_test() 
{
    $data = array();

    $data[] = array('in' => '1',            'out' => '1×10<sup>0</sup>');
    $data[] = array('in' => '1,0',          'out' => '1,0×10<sup>0</sup>');
    $data[] = array('in' => '1E2',          'out' => '1×10<sup>2</sup>');
    $data[] = array('in' => '3,1e2',        'out' => '3,1×10<sup>2</sup>');
    $data[] = array('in' => '0.00960',      'out' => '9,60×10<sup>-3</sup>');
    $data[] = array('in' => '0.009601',     'out' => '9,601×10<sup>-3</sup>');
    $data[] = array('in' => '1.0E-2',       'out' => '1,0×10<sup>-2</sup>');
    $data[] = array('in' => '1',            'out' => '1×10<sup>0</sup>');
    $data[] = array('in' => '1,9',          'out' => '1,9×10<sup>0</sup>');
    $data[] = array('in' => '-1,9',         'out' => '-1,9×10<sup>0</sup>');
    $data[] = array('in' => '-0.0001',      'out' => '-1×10<sup>-4</sup>');
    $data[] = array('in' => '135612',       'out' => '1,35612×10<sup>5</sup>');
    $data[] = array('in' => '0.00000008',   'out' => '8×10<sup>-8</sup>');
    $data[] = array('in' => '0,015000',     'out' => '1,5000×10<sup>-2</sup>');

    foreach($data as $d)
    {
        $specs = array();

        $in  = $d['in'];
        $out = $d['out'];

        $str = ns_format($in);

        echo $in . ' == ' . $str;

        if ($str == $out)
        {
            echo ' <strong style="color: limegreen;">PASS !</strong><br />';
        }
        else
        {
            echo ' <strong style="color: crimson;">FAILED :(</strong><br />';
        }
    }

    return;
}

function cs_test()
{
    $values = array(
        '0' => 0,
        '0,0' => 0,
		'7E1' => 1,
		'8E-1' => 1,
		'56,7E-2' => 3,
        '45.67' => 4,
        '0.001' => 1,
        '0.1235' => 4,
        '45132' => 5,
        '0005523' => 4,
        '0.0096710' => 5,
        '12.0001' => 6,
        '400' => 3,
        '4000' => 4,
        '00600' => 3
    );

    foreach($values as $v => $vcs)
    {
        $cs = NULL;
        $cs = cs($v);

        echo $v . ' doit avoir ' . $vcs . ' CS et a été calculée comme ayant ' . $cs . ' CS.';

		if ($cs == $vcs)
		{
			echo ' <span style="color: limegreen">VRAI</span><br />';
		}
		else
		{
			echo ' <span style="color: crimson">FAUX</span><br />';
		}
    }
}

function cs_ajustement_test()
{
    $valeurs = array(
        '5,788'     => array(3, '5.79'),
        '430'       => array(2, '4.3E2'),
        '433'       => array(2, '4.3E2'),
		'433'		=> array(3, '433'),
		'0,999999'	=> array(3, '1.00'),
        '44,6'      => array(3, '44.6'),
        '1,0'       => array(3, '1.00'),
        '1e2'       => array(2, '1.0E2'),
        '1,0E-8'    => array(3, '1.00E-8'),
        '0.001E-2'  => array(3, '0.00100E-2'),
        '45.6'      => array(4, '45.60'),
        '89'        => array(5, '89.000'),
        '89'        => array(1, '9E1'),
        '945'       => array(2, '9.5E2'),
		'944'		=> array(2, '9.4E2'),
        '0.000167'  => array(2, '0.00017'),
        '0.0001661' => array(3, '0.000166'),
        '0.000166131123' => array(3, '0.000166'),
        '0.00096636408733352' => array(3, '0.000966'),
        '0.00096636408733354' => array(4, '0.0009664'),
        '0.0009600000'  => array(3, '0.000960'),
        '0.0982'    => array(2, '0.098'),
		'6'			=> array(3, '6.00'),
        '0.12300'   => array(7, '0.1230000'),
        '0.095'     => array(1, '0.1'),
        '0.679'     => array(1, '0.7'),
        '0.678'     => array(2, '0.68'),
        '0.672'     => array(2, '0.67'),
        '0.95'      => array(1, '1'),
        '0.00000008' => array(3, '8.00E-8'),
        '0.00001402' => array(3, '1.40E-5'),
        '0.00007098' => array(3, '7.10E-5'),
        '0.0001'       => array(1, '0.0001'),
        '0.00001'       => array(1, '1E-5'),
        '0.00001479'    => array(5, '1.4790E-5'),
        '4.563837'      => array(3, '4.56'),
        '71812'         => array(3, '7.18E4')
    );

    echo '<div style="font-size: 1.2em; line-height: 1.5">';

    foreach($valeurs as $val => $arr)
    {
        $cs_voulu  = $arr[0];
        $val_voulu = $arr[1];

        $nombre = cs_ajustement($val, $cs_voulu);

        echo $val . ' initial ajusté à ' . $cs_voulu . ' CS, devient ' . $nombre . ' obtenu ['; 

        if ((string) $nombre !== (string) $val_voulu)
        {
            echo '<span style="color: crimson">FAUX</span> : ' . $val_voulu . ']<br />';die;
        }
        else
        {
            echo '<span style="color: limegreen">OK</span>]<br />';
        }
    }

    echo '</div>';
}

function incertitude_ajustement_test()
{
	$valeurs = array(
		['n' => '0,785',    'i' => '0,1', 'r' => '0,8'],
		['n' => '0,53423',  'i' => '0,002', 'r' => '0,534'],
		['n' => '0,899999', 'i' => '0,002', 'r' => '0,900'],
		['n' => '0,999999', 'i' => '0,002', 'r' => '1,000'],
		['n' => '0.999999', 'i' => '0.002', 'r' => '1.000'],
		['n' => '15,6891',  'i' => '0,09', 'r' => '15,69'],
		['n' => '1067,1',   'i' => '10', 'r' => '1070'],
        ['n' => '568,9',    'i' => '1', 'r' => '569'],
        ['n' => '0,1',      'i' => '0,1', 'r' => '0,1'],
        ['n' => '0,07',     'i' => '0,1', 'r' => '0,1'],
        ['n' => '0,07',     'i' => '0,11', 'r' => '0,07']

	);

    echo '<div style="font-size: 1.2em; line-height: 1.5">';

    foreach($valeurs as $val => $arr)
    {
		$res = incertitude_ajustement($arr['n'], $arr['i']);

        echo $arr['n'] . ' &pm; ' . $arr['i'] . ' devrait être ajusté à ' . $arr['r'] . ' : [';

        if ((string) $res !== (string) $arr['r'])
        {
            echo '<span style="color: crimson">FAUX</span> : R=' . $res . ']<br />';die;
        }
        else
        {
            echo '<span style="color: limegreen">OK</span> : R=' . $res . ']<br />';
        }
    }

    echo '</div>';
}

// Test pour la version 2
function nombre_decimales_test()
{
	$valeurs = array(
		'105'		 => 0,
		'0,1'		 => 1,
		'1.999'      => 3,
		'0.0004'     => 4,
		'123'        => 0,
		'111,1123'   => 4,
        '0,00001'    => 5,
        '1E4'        => 0,
        '1,1E4'      => 0,
        '1,11111E4'  => 1,
        '1.112312E4' => 2,
        '1E0'        => 0,
        '11E1'       => 0,
        '11,1E1'     => 0,
        '111,11E1'   => 1,
        '0E0'        => 0,
        '0,111E0'    => 3,
        '1E-8'       => 8,
        '1,1E-8'     => 9,
        '0.1E-3'     => 4,
        '110E-3'     => 3,
        '1e4'        => 0,
        '1,1e4'      => 0,
        '1,11111e4'  => 1,
        '1.112312e4' => 2,
        '1e0'        => 0,
        '11e1'       => 0,
        '11,1e1'     => 0,
        '111,11e1'   => 1,
        '0e0'        => 0,
        '0,111e0'    => 3,
        '1e-8'       => 8,
        '1,1e-8'     => 9,
        '0.1e-3'     => 4,
        '110e-3'     => 3

	);

	foreach($valeurs as $v => $resultat)
	{
		$r = nombre_decimales2($v);

		echo $v . ' => ' . $r . ' (' . $resultat . ')';

		if ($r != $resultat)
		{
			echo ' <span style="color: crimson">ERREUR</span>' . '<br />';
		}
		else
		{
			echo ' <span style="color: limegreen">VRAI</span>' . '<br />';
		}
	}

	echo 'OK';
}

function nsdec_test()
{
	echo 'Convertir une notation scientifique en notation décimale' . "<br /><br />";

	$valeurs = array(
		'1.6E-3'	 => '0.0016',
		'100e1'		 => '1000',
		'100.1e1'	 => '1001',
		'100.10e1'	 => '1001.0',
		'9.9100E-1'  => '0.99100',
		'0.00100'	 => '0.00100',
		'0.566E2'	 => '56.6',
		'0.045'		 => '0.045',
		'1e-2'		 => '0.01'
	);

	foreach($valeurs as $v => $r)
	{
		$rep = nsdec($v);

		echo '[' . $v . '] devient [' . $rep . '], valeur attendue: ' . $r;

		if ($rep === $r)
		{
			echo ' ---> [<span style="color: limegreen">VRAI</span>] <--- <br />';
		}
		else
		{
			echo ' ---> [<span style="color: crimson">FAUX</span>] <--- <br />';
		}
	}
}

function verifier_tags_test()
{
	$strings = array();

	$strings[] = "Il faut <strong>croire</strong> que nous le pouvons.";
	$strings[] = "Il faut <strong>croire que nous le pouvons.";
	$strings[] = "Il faut croire</strong> que nous le pouvons.";
	$strings[] = "Il faut <strong><strong>croire</strong> que nous le pouvons.";
	$strings[] = "Il faut <sup><strong>croire</strong></sup> que nous le pouvons.";
    $strings[] = "Il faut <sup><strong>croire</strong></sup> que nous le pouvons<var>.";
    $strings[] = 'Il faut <a href="google.com">croire que nous le pouvons.';
    $strings[] = 'Il faut <a href="google.com">croire</a> que nous le pouvons.';
		
	foreach($strings as $index => $s)
    {
        echo htmlspecialchars($s);

		$s2 = verifier_tags($s);

		if ($s == $s2)
		{
			echo '<br />' . '[' . $index . '] OK : ' . $s2 . '<br/><br />';
		}
		else
		{
			echo '<br />' . '[' . $index . '] FAILED : ' . $s2 . '<br /><br />';
		}
	}
}

function my_number_format_test()
{
    $data = array(
        array('num' => 23.50, 'ret' => '23,5'),
        array('num' => 23.51, 'ret' => '23,51'),
        array('num' => 23.00, 'ret' => '23'),
        array('num' => 0,     'ret' => '0'),
        array('num' => '0.00',  'ret' => '0'),
        array('num' => '5,44',  'ret' => '5,44'),
        array('num' => 1.6666666,  'ret' => '1,67'),
        array('num' => 2.50,  'ret' => '2,50', 'strip_zeros' => FALSE),
    );

    foreach($data as $d) 
    {
        $strip_zeros = $d['strip_zeros'] ?? NULL;
        $decimals    = $d['decimals']    ?? NULL;

        if ($decimals === NULL && $strip_zeros === NULL)
        {
            $r = my_number_format($d['num']);
        }
        elseif ($decimals !== NULL && $strip_zeros === NULL)
        {
            $r = my_number_format($d['num'], $decimals);
        }
        elseif ($decimals === NULL && $strip_zeros !== NULL)
        {
            $r = my_number_format($d['num'], NULL, $strip_zeros);
        }
        else
        {
            $r = my_number_format($d['num'], $decimals, $strip_zeros);
        }

        echo $d['num'] . ' ===> ' . $r . ' (' . $d['ret'] . ') ';

        if ($r == $d['ret'])
        {
            echo 'SUCCESS<br />';
        }
        else
        {
            echo 'FAILED'; die;
        }
    }
}

function format_nombre_test()
{
	$data = array(
		['v' => '12.4', 	'r' => '12,4', 	'o' => array('virgule' => TRUE)],
		['v' => '12,60', 	'r' =>'12,6', 	'o' => array('virgule' => TRUE)],
		['v' => '0.001', 	'r' =>'0', 		'o' => array('virgule' => TRUE)],
		['v' => '3,00', 	'r' =>'3', 	'o' => array('virgule' => TRUE)],
		['v' => '03,03', 	'r' =>'3,03', 	'o' => array('virgule' => TRUE)],
		['v' => '03.03', 	'r' =>'3.03', 	'o' => array('virgule' => FALSE)],
		['v' => '10,00', 	'r' =>'10', 	'o' => array('virgule' => TRUE)],
		['v' => '10000.00', 'r' =>'10 000', 'o' => array('virgule' => TRUE)],
		['v' => '10000.00', 'r' =>'10000',  'o' => array('virgule' => TRUE, 'separateur_millier' => '')]
	);

	foreach($data as $d)
	{
		$r = format_nombre($d['v'], $d['o']);

		echo 'val=[' . $d['v'] . '] rep=[' . $d['r'] . '] ';
	
		if ($r == $d['r'])
		{
			echo '<span style="color: limegreen">OK</span>';
		}
		else
		{
			echo '<span style="color: crimson">FAILED</span> ==> attendu: ' . $r;
		}

		echo '<br />';
	}	

}

