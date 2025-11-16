<? 
/* ============================================================================
 *
 * LABORATOIRE - COURS - NOM DU LABO
 *
 * VERSION 2024-11-11
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<? 
	$montre_tags = FALSE;

	if ($previsualisation && $this->uri->segment(4) != 'etudiant')
		$montre_tags = TRUE; 
?>

<?
/* --------------------------------------------------------------------
 *
 * Les styles specifiques a ce laboratoire
 *
 * -------------------------------------------------------------------- */ ?>

<style>
    #lab-tableaux-specifiques table td:first-child {
        border-left: 0;
    }

    #lab-tableaux-specifiques table td:last-child {
        border-right: 0;
    }

    #lab-tableaux-specifiques table tr:last-child td {
        border-bottom: 0;
    }
    #lab-tableaux-specifiques table td {
        vertical-align: middle;
    }
</style>

<?
/* --------------------------------------------------------------------
 *
 * TABLEAUX SPECIFIQUES
 *
 * -------------------------------------------------------------------- */ ?>

<div id="lab-tableaux-specifiques" data-lab_prefix="<?= $lab_prefix; ?>">

<?
/* --------------------------------------------------------------------
 *
 * Tableau 1
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 1
 *
 * -------------------------------------------------------------------- */ ?>

<? 
	$tableau_no = 1;
	$tableau_points = $lab_points_tableaux[$tableau_no]['points'];
?>

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 1
 *
 * ---------------------------------------------------------------- */ ?>

<?
$composes = array(
    1 => 'éthanol',
    2 => 'propan-1-ol',
    3 => 'butan-1-ol',
    4 => 'pentane',
    5 => 'méthanol',
    6 => 'hexane',
    7 => 'propan-2-ol'
);

$champ_a = 'mm_';
$champ_b = 'pe_';
$champ_c = 't1_';
$champ_d = 't2_';
$champ_e = 'x1_';
$champ_f = 'x2_';

?>

<div class="evaluation-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?= lab_f_tableau_complet($tableau_no, $tableau_points); ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
                    <td class="text-center">Nom du composé</td>
                    <td class="text-center">Masse molaire<div>(g/mol)</div></td>
                    <td class="text-center">Point d'ébullition<div>(<sup>o</sup>C)</div></td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">T<sub>1</sub></div>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, 'dt1', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, 'dt1'); ?>
                    </td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">T<sub>2</sub></div>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, 'dt2', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, 'dt2'); ?>
                    </td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">x<sub>1</sub></div>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, 'dx1', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, 'dx1'); ?>
                    </td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">x<sub>2</sub></div>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, 'dx2', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, 'dx2'); ?>
                    </td>
                </tr>

                <? foreach($composes as $i => $nom) : ?>

                <tr>
                        <td><?= $nom; ?></td>
                        <td>
                            <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_a . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE)); ?>
                            <?= lab_montre_champ($montre_tags, $lab_points, $champ_a . $i); ?>
                        </td>
                        <td>
                            <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_b . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE)); ?>
                            <?= lab_montre_champ($montre_tags, $lab_points, $champ_b . $i); ?>
                        </td>
                        <td>
                            <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_c . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE)); ?>
                            <?= lab_montre_champ($montre_tags, $lab_points, $champ_c . $i); ?>
                        </td>
                        <td>
                            <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_d . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE)); ?>
                            <?= lab_montre_champ($montre_tags, $lab_points, $champ_d . $i); ?>
                        </td>
                        <td>
                            <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_e . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE)); ?>
                            <?= lab_montre_champ($montre_tags, $lab_points, $champ_e . $i); ?>
                        </td>
                        <td>
                            <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_f . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE)); ?>
                            <?= lab_montre_champ($montre_tags, $lab_points, $champ_f . $i); ?>
                        </td>

				</tr>

                <? endforeach; ?>

            </tbody>

        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

<?
/* ------------------------------------------------------------
 *
 * Tableau 2
 *
 * ------------------------------------------------------------ */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 2
 *
 * -------------------------------------------------------------------- */ ?>

<? 
	$tableau_no = 2;
	$tableau_points = $lab_points_tableaux[$tableau_no]['points'];
?>

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 2
 *
 * ---------------------------------------------------------------- */ ?>

<?
$composes = array(
    1 => 'éthanol',
    2 => 'propan-1-ol',
    3 => 'butan-1-ol',
    4 => 'pentane',
    5 => 'méthanol',
    6 => 'hexane',
    7 => 'propan-2-ol'
);

$champ_a = 'dt_';
$champ_b = 'dx_';
$champ_c = 'v_moy_';
?>

<div class="evaluation-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?= lab_f_tableau_complet($tableau_no, $tableau_points); ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
                    <td class="text-center">Nom du composé</td>
                    <td class="text-center">| &Delta;T |<div>(<sup>o</sup>C)</div></td>
                    <td class="text-center">&Delta;x<div>(s)</div></td>
                    <td class="text-center">Vitesse moyenne<div>(<sup>o</sup>C/s)</div></td>
                </tr>

                <? foreach($composes as $i => $nom) : ?>

                <tr>
                    <td><?= $nom; ?></td>
                    <td>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_a . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, $champ_a . $i); ?>
                    </td>
                    <td>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_b . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, $champ_b . $i); ?>
                    </td>
                    <td>
                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ_c . $i, NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, $champ_c . $i); ?>
                    </td>
				</tr>

                <? endforeach; ?>

            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-tableaux-specifiques -->
