<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - SPETROPHOTOMETRIE METHYL-ORANGE
 *
 * VERSION 2024-07-30
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

<script src="<?= base_url() . 'assets/js/lab/sn2_spectro_mo.js?' . date('U'); ?>"></script>

<div id="lab-tableaux-specifiques" 
	data-lab_prefix="<?= $lab_prefix; ?>">

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
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-titre">
        <div class="row no-gutters">

            <?= lab_f_tableau($tableau_no, $tableau_points); ?>

        </div> <!-- .row -->
    </div> <!-- .evaluation-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'c_conc'; ?>
    <? $champ2 = 'v_dil'; ?>

    <? $champ1_d = ajouter_d_champ($champ1); ?>
    <? $champ2_d = ajouter_d_champ($champ2); ?>

    <div class="evaluation-tableau-contenu">

        <div class="form-group row mt-1 mb-0" style="padding: 10px;">
            <label for="<?= $lab_prefix . '-' . $champ1; ?>" class="col-sm-6 col-form-label">
                <?= $lab_points['c_conc']['desc']; ?>
            </label>
            <div class="col-sm-6">

                <?
                /* ------------------------------------------------
                 *
                 * Concentration de la solution mere (c_conc)
                 *
                 * ------------------------------------------------ */ ?>

                <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ1, $champ1_d, @$traces['lab'], array('nsci' => TRUE, 'unites' => TRUE)); ?>

                <?= lab_montre_champ($montre_tags, $lab_points, $champ1, $champ1_d); ?>

            </div> <!-- .col -->
        </div> <!-- .form-group -->

        <div class="form-group row mt-0 mb-0" style="padding: 10px;">

            <label for="<?= $lab_prefix . '-' . $champ2; ?>" class="col-sm-6 col-form-label">
                <?= $lab_points['v_dil']['desc']; ?>
            </label>

            <div class="col-sm-6">

                <?
                /* ------------------------------------------------
                 *
                 * Volume dilue (v_dil)
                 *
                 * ------------------------------------------------ */ ?>

                <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ2, $champ2_d, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>

                <?= lab_montre_champ($montre_tags, $lab_points, $champ2, $champ2_d); ?>

            </div> <!-- .col -->
        </div> <!-- .form-group -->


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

    <div class="evaluation-tableau-titre">
        <div class="row no-gutters">

            <?= lab_f_tableau($tableau_no, $tableau_points); ?>

        </div> <!-- .row -->
    </div> <!-- .evaluation-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'v_conc'; ?>
    <? $champ2 = 'c_dil'; ?>
    <? $champ3 = 'absorb'; ?>
    <? $champ4 = 'a_absorb'; ?>

    <? $champ1_d = ajouter_d_champ($champ1); ?>
    <? $champ2_d = ajouter_d_champ($champ2); ?>
    <? $champ3_d = ajouter_d_champ($champ3); ?>
    <? $champ4_d = ajouter_d_champ($champ4); ?>

    <div class="evaluation-tableau-contenu">

        <table class="table mb-0" style="border-top: 0">
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
                        <label>Volume concentré</label>
                    </div>

                    <?= lab_f_champ2($lab_prefix, $lab_valeurs, NULL, $champ1_d, @$traces['lab'], array('nsci' => FALSE, 'unites' => TRUE)); ?>
                    <?= lab_montre_champ($montre_tags, $lab_points, NULL, $champ1_d); ?>

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

                        <? $champ = $champ1 . '-' . $i; ?>

                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ, NULL, @$traces['lab'], array('align' => 'center')); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, $champ, NULL); ?>

                    </td>
                    <td>
                        <? 
                        /* --------------------------------------------
                         *
                         * Concentration diluee (c_dil) (calcul)
                         *
                         * -------------------------------------------- */ ?>

                        <? $champ = $champ2 . '-' . $i; ?>
                        <? $champ_d = $champ2_d . '-' . $i; ?>

                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ, $champ_d, @$traces['lab']); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, $champ, $champ_d); ?>

                    </td>
                    <td>
                        <?
                        /* --------------------------------------------
                         *
                         * Absorbance des solutions etalons (aborb-n)
                         *
                         * -------------------------------------------- */ ?>

                        <? $champ = $champ3 . '-' . $i; ?>
                        <? $champ_d = $champ3_d . '-' . $i; ?>

                        <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ, $champ_d, @$traces['lab']); ?>
                        <?= lab_montre_champ($montre_tags, $lab_points, $champ, $champ_d); ?>

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

                    <? $champ = $champ4; ?>
                    <? $champ_d = $champ4_d; ?>

                    <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ, $champ_d, @$traces['lab']); ?>
                    <?= lab_montre_champ($montre_tags, $lab_points, $champ, $champ_d); ?>

                 </td>
            </tr>
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

    <div class="evaluation-tableau-titre">
        <div class="row no-gutters">

            <?= lab_f_tableau($tableau_no, $tableau_points); ?>

        </div> <!-- .row -->
    </div> <!-- .evaluation-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'droite_m'; ?>
    <? $champ2 = 'droite_b'; ?>

    <div class="evaluation-tableau-contenu">

        <div class="form-group row mt-1 mb-0" style="padding: 10px;">
            <label for="<?= $lab_prefix . '-' . $champ1; ?>" class="col-sm-6 col-form-label">
				Équation de votre droite
            </label>
            <div class="col-sm-6">
                <div class="input-group">

                    <?
                    /* ------------------------------------------------
                     *
                     * Equation de la droite
                     *
                     * ------------------------------------------------ */ ?>
                   
					<div class="input-group-prepend">
						<span class="input-group-text">y = </span>
  					</div> 

                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
						value="<?= $traces['lab'][$champ1] ?? NULL; ?>" placeholder="pente m">

                    <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">&#119909;</span>
                    <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">&plus;</span>

                    <input  type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
						value="<?= $traces['lab'][$champ2] ?? NULL; ?>" placeholder="ordonnée b">
                </div> <!-- .input-group -->

                <?= lab_montre_champ($montre_tags, $lab_points, $champ1, $champ2); ?>

            </div> <!-- .col -->
        </div> <!-- .form-group -->

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

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
	$tableau_points = $lab_points_tableaux[$tableau_no]['points'];
?>

<div class="evaluation-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-titre">
        <div class="row no-gutters">

            <?= lab_f_tableau($tableau_no, $tableau_points); ?>

        </div> <!-- .row -->
    </div> <!-- .evaluation-tableau-titre -->

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

    <div class="evaluation-tableau-contenu">

        <table class="table mb-0">
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

                    <? $champ = $champ1; ?>
                    <? $champ_d = $champ1_d; ?>

                    <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ, $champ_d, @$traces['lab']); ?>
                    <?= lab_montre_champ($montre_tags, $lab_points, $champ, $champ_d); ?>

                </td>

                <td class="text-center" style="vertical-align: middle;">
                    <?
                    /* --------------------------------------------
                     *
                     * Concentration admise (c_adm)
                     *
                     * ------------------------------------------- */ ?>

                    <? $champ = $champ2; ?>
                    <? $champ_d = $champ2_d; ?>

                    <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ, $champ_d, @$traces['lab']); ?>
                    <?= lab_montre_champ($montre_tags, $lab_points, $champ, $champ_d); ?>

                </td>

                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Incertitude relative (inc_rel)
                     *
                     * ------------------------------------------- */ ?>

                    <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ3, NULL, @$traces['lab']); ?>
                    <?= lab_montre_champ($montre_tags, $lab_points, $champ3); ?>
                </td>
                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Pourcentage d'ecart (p_ecart)
                     *
                     * ------------------------------------------- */ ?>

                    <?= lab_f_champ2($lab_prefix, $lab_valeurs, $champ4, NULL, @$traces['lab']); ?>
                    <?= lab_montre_champ($montre_tags, $lab_points, $champ4); ?>

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

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-spectro-mo -->
