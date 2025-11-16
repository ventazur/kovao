<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - CRYOSCOPIE
 *
 * VERSION 2025-01-01
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Styles specifiques a ce laboratoire
 *
 * -------------------------------------------------------------------- */ ?>

<style>
    .masse-d-input {
        max-width: 100px;
    }

    .evaluation-tableau-contenu table td {
        vertical-align: middle;
    }

</style>

<?
/* --------------------------------------------------------------------
 *
 * Tableaux specifiques
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

<div class="evaluation-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau 1
     *
     * ---------------------------------------------------------------- */ ?>

    <?= lab_f_tableau_complet($tableau_no, $tableau_points); ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau 1
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

        <table class="table mb-0" style="border-top: 0">
            <tbody>

                <tr>
                    <td style="width: 40%; vertical-align: middle">
                        <div>Items à peser</div> 
                    </td>
                    <td class="text-center">
                        <div>Masse</div>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, 'masse_p_d', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'masse_p_d'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Bécher</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'becher', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE, 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'becher'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Bécher + sel inconnu</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'becher_sel', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE, 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'becher_sel'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Bécher + sel inconnu + H<sub>2</sub>O</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'becher_sel_eau', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE, 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'becher_sel_eau'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

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

        <table class="table mb-0" style="border-top: 0">
            <tbody>

                <tr>
                    <td style="width: 40%;vertical-align: middle">
                        <div>Substance</div> 
                    </td>
                    <td class="text-center">
                        <div>Masse</div>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, 'masse_c_d', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => 'g', 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'masse_c_d'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Eau</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'eau', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE, 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'eau'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Sel inconnu</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'sel', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => FALSE, 'align' => 'center')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'sel'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

<?
/* --------------------------------------------------------------------
 *
 * Tableau 3
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 3
 *
 * -------------------------------------------------------------------- */ ?>
<? 
	$tableau_no = 3;
    $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
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

        <table class="table mb-0" style="border-top: 0">
            <tbody>

                <tr>
                    <td style="width: 40%; vertical-align: middle">
                        <div>T<sub>cong</sub> de l'eau</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'tcong_eau', 'tcong_eau_d', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => '&deg;C')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'tcong_eau', 'tcong_eau_d'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>T<sub>cong</sub> de la solution</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'tcong_sln', 'tcong_sln_d', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => '&deg;C')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'tcong_sln', 'tcong_sln_d'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>&Delta;T<sub>cong</sub></div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'd_tcong', 'd_tcong_d', @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => '&deg;C', 'align' => 'right')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'd_tcong', 'd_tcong_d'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Molalité expérimentale <br />(conserver 2 CS)</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'b_exp', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => 'mol/kg', 'align' => 'right')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'b_exp'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Masse molaire expérimentale</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'mm_exp', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => 'g/mol', 'align' => 'right')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'mm_exp'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Masse molaire de référence</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'mm_ref', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => 'g/mol', 'align' => 'right')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'mm_ref'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>% d'écart sur la masse molaire</div> 
                    </td>       
                    <td>
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">
                                <?= lab_f_champ2($lab_prefix, $lab_valeurs, 'p_ecart', NULL, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE, 'unites_v' => '%', 'align' => 'right')); ?>
                                <?= lab_montre_champ($montre_tags, $lab_points, 'p_ecart'); ?>
                            </div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-spectro-mo -->
