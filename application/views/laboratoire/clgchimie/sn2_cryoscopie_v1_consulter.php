<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - CRYOSCOPIE
 *
 * VERSION 2025-01-10
 *
 * ---> CONSULTER <--- 
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

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

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau 1
     *
     * ---------------------------------------------------------------- */ ?>

    <?= lab_c_tableau($tableau_no, $tableau_data); ?>

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

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, 'masse_p_d', array('nsci' => TRUE, 'unites' => TRUE)); ?>
                                <?= lab_montrer_corr('masse_p_d', $lab_points_champs, 'mt-1'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Bécher</div> 
                    </td>       
                    <td style="text-align: center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'becher', NULL, array('nsci' => TRUE, 'unites' => TRUE)); ?>
                                <?= lab_montrer_corr('becher', $lab_points_champs, 'mt-1'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Bécher + sel inconnu</div> 
                    </td>       
                    <td style="text-align: center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'becher_sel', NULL, array('nsci' => TRUE, 'unites' => TRUE)); ?>
                                <?= lab_montrer_corr('becher_sel', $lab_points_champs, 'mt-1'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Bécher + sel inconnu + H<sub>2</sub>O</div> 
                    </td>       
                    <td style="text-align: center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'becher_sel_eau', NULL, array('nsci' => TRUE, 'unites' => TRUE)); ?>
                                <?= lab_montrer_corr('becher_sel_eau', $lab_points_champs, 'mt-1'); ?>

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

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

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

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, 'masse_c_d', array('nsci' => TRUE, 'unites' => TRUE, 'unites_v' => 'g')); ?>
                                <?= lab_montrer_corr('masse_c_d', $lab_points_champs, 'mt-1'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Eau</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'eau', NULL, array('nsci' => TRUE, 'unites' => TRUE)); ?>
                                <?= lab_montrer_corr('eau', $lab_points_champs, 'mt-1'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Sel inconnu</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'sel', NULL, array('nsci' => TRUE, 'unites' => TRUE)); ?>
                                <?= lab_montrer_corr('sel', $lab_points_champs, 'mt-1'); ?>

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

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

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

        <table class="table mb-0" style="border-top: 0">
            <tbody>

                <tr>
                    <td style="width: 40%; vertical-align: middle">
                        <div>T<sub>cong</sub> de l'eau</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'tcong_eau', 'tcong_eau_d', array('nsci' => TRUE, 'unites' => TRUE)); ?>

                                <div>
                                    <?= lab_montrer_corr('tcong_eau', $lab_points_champs, ''); ?>
                                    <?= lab_montrer_corr('tcong_eau_d', $lab_points_champs, ''); ?>
                                </div>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>T<sub>cong</sub> de la solution</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'tcong_sln', 'tcong_sln_d', array('nsci' => TRUE, 'unites' => TRUE)); ?>

                                <div>
                                    <?= lab_montrer_corr('tcong_sln', $lab_points_champs, ''); ?>
                                    <?= lab_montrer_corr('tcong_sln_d', $lab_points_champs, ''); ?>
                                </div>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>&Delta;T<sub>cong</sub></div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'd_tcong', 'd_tcong_d', array('nsci' => TRUE, 'unites' => TRUE, 'unites_v' => '&deg;C')); ?>

                                <div>
                                    <?= lab_montrer_corr('d_tcong', $lab_points_champs, ''); ?>
                                    <?= lab_montrer_corr('d_tcong_d', $lab_points_champs, ''); ?>
                                </div>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Molalité expérimentale</div> 
                        <? /* <div>Molalité expérimentale<br />(conserver 2 CS)</div> */ ?>
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'b_exp', NULL, array('nsci' => TRUE, 'unites' => TRUE, 'unites_v' => 'mol/kg')); ?>
                                <?= lab_montrer_corr('b_exp', $lab_points_champs, 'mt-0'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Masse molaire expérimentale</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'mm_exp', NULL, array('nsci' => TRUE, 'unites' => TRUE, 'unites_v' => 'g/mol')); ?>
                                <?= lab_montrer_corr('mm_exp', $lab_points_champs, 'mt-0'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>Masse molaire de référence</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'mm_ref', NULL, array('nsci' => TRUE, 'unites' => TRUE, 'unites_v' => 'g/mol')); ?>
                                <?= lab_montrer_corr('mm_ref', $lab_points_champs, 'mt-0'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div>% d'écart sur la masse molaire</div> 
                    </td>       
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="col-xs-12 col-md-6 mt-2">

                                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'p_ecart', NULL, array('nsci' => TRUE, 'unites' => TRUE, 'unites_v' => '%')); ?>
                                <?= lab_montrer_corr('p_ecart', $lab_points_champs, 'mt-0'); ?>

                            </div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-spectro-mo -->
