<? 
/* ============================================================================
 *
 * LABORATOIRE - FORCES INTERMOLECULAIRES - CONSULTER
 *
 * VERSION 2024-11-11
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<? 
    if ( ! isset($montrer_tags))
    {
        $montre_tags = FALSE;
    }

    if ( ! isset($montrer_corrections))
    {
        $montrer_corrections = TRUE; 
    }
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

    $tableau_data = array(
        'points_obtenus_ajustement' => $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0,
        'ajustement'                => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? TRUE : FALSE,
        'points_obtenus'            => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] : $lab_points_tableaux[$tableau_no]['points_obtenus'],
        'points_totaux'             => $lab_points_tableaux[$tableau_no]['points'],
        'commentaires'              => $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL,
        'reussi'                    => ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $lab_points_tableaux[$tableau_no]['points']) ? TRUE : FALSE,
        'soumission_id'             => $soumission['soumission_id'],
        'soumission'                => $soumission,
        'permettre_modifications'   => ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) ? TRUE : FALSE
    );
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

    <?= lab_c_tableau($tableau_no, $tableau_data); ?>

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

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, 'dt1', array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montrer_corr('dt1', $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">T<sub>2</sub></div>

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, 'dt2', array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montrer_corr('dt2', $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">x<sub>1</sub></div>

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, 'dx1', array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montrer_corr('dx1', $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <div style="margin-bottom: 7px">x<sub>2</sub></div>

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, 'dx2', array('nsci' => FALSE, 'unites' => TRUE)); ?>
                        <?= lab_montrer_corr('dx2', $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                </tr>

                <? foreach($composes as $i => $nom) : ?>

                <tr>
                    <td><?= $nom; ?></td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_a . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_a . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_b . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_b . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_c . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_c . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_d . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_d . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_e . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_e . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_f . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_f . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
				</tr>

                <? endforeach; ?>

            </tbody>

        </table>

        <?
         /* ---------------------------------------------------------------
          *
          * Commentaire laisse a l'etudiant par l'enseignant
          *
          * --------------------------------------------------------------- */ ?>

        <?= lab_c_commentaires($tableau_no, $tableau_data, array('montrer_commentaires' => $montrer_commentaires ?? TRUE)); ?>

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

    $tableau_data = array(
        'points_obtenus_ajustement' => $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0,
        'ajustement'                => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? TRUE : FALSE,
        'points_obtenus'            => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] : $lab_points_tableaux[$tableau_no]['points_obtenus'],
        'points_totaux'             => $lab_points_tableaux[$tableau_no]['points'],
        'commentaires'              => $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL,
        'reussi'                    => ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $lab_points_tableaux[$tableau_no]['points']) ? TRUE : FALSE,
        'soumission_id'             => $soumission['soumission_id'],
        'soumission'                => $soumission,
        'permettre_modifications'   => ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) ? TRUE : FALSE
    );
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

    <?= lab_c_tableau($tableau_no, $tableau_data); ?>

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
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_a . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_a . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_b . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_b . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
                    <td class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ_c . $i, NULL, array('nsci' => FALSE, 'unites' => FALSE)); ?>
                        <?= lab_montrer_corr($champ_c . $i, $lab_points_champs, array('classes' => 'mt-0', 'montrer_corrections' => $montrer_corrections)); ?>
                    </td>
				</tr>

                <? endforeach; ?>

            </tbody>
        </table>

        <?
         /* ---------------------------------------------------------------
          *
          * Commentaire laisse a l'etudiant par l'enseignant
          *
          * --------------------------------------------------------------- */ ?>

        <?= lab_c_commentaires($tableau_no, $tableau_data, array('montrer_commentaires' => $montrer_commentaires ?? TRUE)); ?>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-tableaux-specifiques -->
