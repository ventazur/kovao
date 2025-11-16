<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - SPETROPHOTOMETRIE METHYL-ORANGE
 *
 * VERSION 2024-07-30
 *
 * ---> CONSULTER <---
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<? 
	$montre_tags = FALSE;
?>

<div id="lab-tableaux-specifiques">

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

    <? $champ1   = 'c_conc'; ?>
    <? $champ1_d = 'c_conc_d'; ?>

    <? $champ2   = 'v_dil'; ?>
    <? $champ2_d = 'v_dil_d'; ?>

    <div class="corriger-tableau-contenu">

        <?
        /* ------------------------------------------------------------
         *
         * Concentration de la solution mere (c_conc)
         *
         * ------------------------------------------------------------- */ ?>

        <div class="row">
            <div class="col-sm-6">

                <?= $lab_points[$champ1]['desc']; ?>

            </div> <!-- .col -->
            <div class="col-sm-6 text-right">

                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ1, $champ1_d, array('nsci' => TRUE, 'unites' => TRUE)); ?>

                <?= lab_montrer_corr($champ1, $lab_points_champs, 'ml-2'); ?>
                <?= lab_montrer_corr($champ1_d, $lab_points_champs, ''); ?>

            </div> <!-- .col -->
        </div> <!-- .row -->

        <?
        /* ------------------------------------------------------------
         *
         * Volume dilue (v_dil)
         *
         * ------------------------------------------------------------- */ ?>
        <div class="row mt-3">
            <div class="col-sm-6">

                <?= $lab_points[$champ2]['desc']; ?>

            </div> <!-- .col -->
            <div class="col-sm-6 text-right">

                <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ2, $champ2_d, array('nsci' => TRUE, 'unites' => TRUE)); ?>

                <?= lab_montrer_corr($champ2, $lab_points_champs, 'ml-2'); ?>
                <?= lab_montrer_corr($champ2_d, $lab_points_champs, ''); ?>

            </div> <!-- .col -->
        </div> <!-- .row -->

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <?= lab_c_commentaires($tableau_no, $tableau_data); ?>

</div> <!-- .corriger-tableau -->

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

    <? $champ1   = 'v_conc'; ?>
    <? $champ1_d = 'v_conc_d'; ?>
    <? $champ2   = 'c_dil'; ?>
    <? $champ2_d = 'c_dil_d'; ?>
    <? $champ3   = 'absorb'; ?>
    <? $champ3_d = 'absorb_d'; ?>
    <? $champ4   = 'a_absorb'; ?>
    <? $champ4_d = 'a_absorb_d'; ?>

    <div class="corriger-tableau-contenu">

        <table class="table mb-0" style="margin-top: -10px">
            <tr>
                <td class="text-center">
					<div>Solutions</div> 
					<div>étalons</div>
				</td>
                <td>
                    <?
                    /* ------------------------------------------------
                     *
                     * Incertitude sur le volume concentre (v_conc_d)
                     *
                     * ------------------------------------------------ */ ?>

                    <div class="text-center">
                        Volume concentré
                    </div>

                    <div class="text-center">

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, NULL, $champ1_d, array('nsci' => TRUE, 'unites' => TRUE)); ?>
                        <?= lab_montrer_corr($champ1_d, $lab_points_champs, 'mt-1'); ?>

                    </div>

                </td>
                <td>
                    <div class="text-center">Concentration diluée</div>
                    <div class="text-center">(&times;10<sup>-6</sup> mol/L)</div>
                </td>
                <td class="text-center">
                    Absorbance
                </td>
            </tr>
            <tr>
                <td class="text-center">Blanc</td>
                <td class="text-center">0</td>
                <td class="text-center">0</td>
                <td class="text-center">0,000 &pm; 0,001</td>
            </tr>

            <? foreach(array(1, 2, 3, 4, 5) as $i) : ?>

                <tr>
                    <td class="text-center" style="vertical-align: middle"><?= $i; ?></td>
                    <td>
                        <? 
                        /* --------------------------------------------
                         *
                         * Volume concentre (v_conc)
                         *
                         * -------------------------------------------- */ ?>
                        <div class="text-center">

                            <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ1 . '-' . $i, NULL); ?>
                            <?= lab_montrer_corr($champ1 . '-' . $i, $lab_points_champs, 'mt-1'); ?>

                        </div>
                    </td>
                    <td>
                        <? 
                        /* --------------------------------------------
                         *
                         * Concentration diluee (c_dil) (calcul)
                         *
                         * -------------------------------------------- */ ?>
                        <div class="text-center">

                            <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ2 . '-' . $i, $champ2_d . '-' .  $i); ?>

                            <div class="mt-1">
                                <?= lab_montrer_corr($champ2 . '-' . $i, $lab_points_champs, ''); ?>
                                <?= lab_montrer_corr($champ2_d . '-' . $i, $lab_points_champs, ''); ?>
                            </div>

                        </div>
                    </td>
                    <td>
                        <?
                        /* --------------------------------------------
                         *
                         * Absorbance des solutions etalons (aborb-n)
                         *
                         * -------------------------------------------- */ ?>
                        <div class="text-center">

                            <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ3 . '-' . $i, $champ3_d . '-' .  $i); ?>

                            <div class="mt-1">
                                <?= lab_montrer_corr($champ3 . '-' . $i, $lab_points_champs, ''); ?>
                                <?= lab_montrer_corr($champ3_d . '-' . $i, $lab_points_champs, ''); ?>
                            </div>
                        </div>
                    </td>

                </tr>
            <? endforeach; ?>

            <?
            /* --------------------------------------------------------
             *
             * Tableau des resultats
             *
             * -------------------------------------------------------- */ ?>
            <tr>
                <td class="text-left" colspan=3" style="vertical-align: middle">
                    Inconnu A
                </td>
                <td>
                    <?
                    /* ------------------------------------------------
                     *
                     * Absorbance de l'inconnu A (a_absorb)
                     *
                     * ------------------------------------------------ */ ?>
                    <div class="text-center">

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ4, $champ4_d); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ4, $lab_points_champs, ''); ?>
                            <?= lab_montrer_corr($champ4_d, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                 </td>
            </tr>
        </table>

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <?= lab_c_commentaires($tableau_no, $tableau_data); ?>

</div> <!-- .corriger-tableau -->

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

    <? $champ1 = 'droite_m'; ?>
    <? $champ2 = 'droite_b'; ?>

    <div class="corriger-tableau-contenu">

        <?
        /* ------------------------------------------------------------
         *
         * Equation de la droite
         *
         * ------------------------------------------------------------- */ ?>
        <div class="row">
            <div class="col-sm-6">

                Équation de votre droite

            </div> <!-- .col -->
            <div class="col-sm-6 text-right">

                y = 
                <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>x + 
                <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>

                <?= lab_montrer_corr($champ1, $lab_points_champs, 'ml-2'); ?>
                <?= lab_montrer_corr($champ2, $lab_points_champs, ''); ?>
            </div> <!-- .col -->
        </div> <!-- .row -->

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <?= lab_c_commentaires($tableau_no, $tableau_data); ?>

</div> <!-- .corriger-tableau -->

<?
/* ------------------------------------------------------------
 *
 * Tableau 4
 *
 * ------------------------------------------------------------ */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 3
 *
 * -------------------------------------------------------------------- */ ?>
<? 
    $tableau_no = 4;

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

    <? $champ1 = 'c_exp'; ?>
    <? $champ2 = 'c_adm'; ?>
    <? $champ3 = 'inc_rel'; ?>
    <? $champ4 = 'p_ecart'; ?>

    <? $champ1_d = ajouter_d_champ($champ1); ?>
    <? $champ2_d = ajouter_d_champ($champ2); ?>
    <? $champ3_d = ajouter_d_champ($champ3); ?>
    <? $champ4_d = ajouter_d_champ($champ4); ?>

    <div class="corriger-tableau-contenu" style="padding-bottom: 10px">

        <table class="table mb-0" style="margin-top: -10px;">
            <tr>
                <td></td>
                <td>
                    <div class="text-center">Concentration expérimentale</div>
                    <div class="text-center">(&times;10<sup>-6</sup> mol/L)</div>
                </td>
                <td>
                    <div class="text-center">Concentration admise </div>
                    <div class="text-center">(&times;10<sup>-6</sup> mol/L)</div>
                </td>
                <td>
                    <div class="text-center">Incertitude relative</div>
                    <div class="text-center">(%)</div>
                </td>
                <td>
                    <div class="text-center">Pourcentage d'écart</div>
                    <div class="text-center">(%)</div>
                </td>
            </tr>

            <tr>
                <td style="vertical-align: middle; white-space: nowrap">Inconnu A</td>
                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Concentration experimentale (c_exp)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ1, $champ1_d); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ1, $lab_points_champs, ''); ?>
                            <?= lab_montrer_corr($champ1_d, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>

                <td class="text-center" style="vertical-align: middle;">
                    <?
                    /* --------------------------------------------
                     *
                     * Concentration admise (c_adm)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">

                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ2, $champ2_d); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ2, $lab_points_champs, ''); ?>
                            <?= lab_montrer_corr($champ2_d, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>

                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Incertitude relative (inc_rel)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ3, NULL); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ3, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>

                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Pourcentage d'ecart (p_ecart)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">
                        <?= lab_c_reponse($lab_valeurs, $lab_points_champs, $champ4, NULL); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ4, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="20">
                    <div class="mt-2" style="font-size: 0.7em; color: #999">
                        (<strong style="color: crimson">*</strong>) 
                        Pour l'incertitude de la concentration expérimentale, veuillez prendre l'incertitude de la solution étalon qui se rapproche le plus de votre valeur d'absorbance.
                    </div>
                </td>
            </tr>
        </table>

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <?= lab_c_commentaires($tableau_no, $tableau_data); ?>

</div> <!-- .corriger-tableau -->

</div> <!-- #lab-spectro-mo -->
