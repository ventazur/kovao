<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - SPETROPHOTOMETRIE METHYL-ORANGE
 *
 * VERSION 2025-01-01
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

    <?
    $champ1 = 'c_conc';
    $champ1_d = 'c_conc_d'; 

    $champ2 = 'v_dil';
    $champ2_d = 'v_dil_d';
    ?>

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

    <?
    $champ1 = 'v_conc';
    $champ1_d = 'v_conc_d';    

    $champ2 = 'c_dil';
    $champ2_d = 'c_dil_d';

    $champ3 = 'absorb';
    $champ3_d = 'absorb_d';

    $champ4 = 'a_absorb';
    $champ4_d = 'a_absorb_d';
    ?>

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
                    <div class="mt-2" style="font-size: 0.85em; color: #999">
                        (<strong style="color: crimson">*</strong>) 
                        Pour l'incertitude de la concentration expérimentale, veuillez prendre l'incertitude de la solution étalon qui se rapproche le plus de votre valeur d'absorbance.
                    </div>
                </td>
            </tr>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

<?
/* --------------------------------------------------------------------
 *
 * Tableau 5 : PRECISION - EXACTITUDE - VALIDITE
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 5
 *
 * -------------------------------------------------------------------- */ ?>
<? 
	$tableau_no = 5;
	$tableau_points = $lab_points_tableaux[$tableau_no]['points'];
?>

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 5
 *
 * ---------------------------------------------------------------- */ ?>

<?
$champ1  = 'precision';
$champ2  = 'exactitude';
$champ3  = 'validite';
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

            <div class="col-8">

                Tableau <?= $tableau_no; ?> : La validité de votre résultat expérimental

				<?
				/* ----------------------------------------------------
				 * 
                 * Indicateurs d'enregistrement des traces
				 *
                 * ---------------------------------------------------- */ ?>

				<span id="est-sauvegarde<?= $tableau_no; ?>" class="est-sauvegarde">
					<i class="bi bi-floppy"></i>
				</span>
				<span id="est-pas-sauvegarde<?= $tableau_no; ?>" class="est-pas-sauvegarde">
					<i class="bi bi-floppy"></i> &times;
				</span>
            </div>
            <div class="col-4">
                <div class="question-points float-right">
                    <span id="tableau-points-obtenus-<?= $tableau_no; ?>" class="tableau-points-obtenus d-none"></span>
					<?= format_nombre($tableau_points); ?> point<?= $tableau_points > 1 ? 's' : ''; ?>
				</div>
            </div>
        </div>

    </div> <!-- /.question-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

        <table class="table mb-0" style="border: 0">
			<tbody>
				<tr>
					<td class="text-center">Précision</td>
					<td class="text-center">Exactitude</td>
					<td class="text-center">Validité</td>
				</tr>
				<tr>
					<?
					/* ------------------------------------------------
					 *
                     * Precision
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
						<div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
                            <label class="btn btn-outline-primary no-margin" for="<?= $lab_prefix . '-' . $champ1; ?>-1" style="width: 125px; border-right: 0">
								<input type="radio" name="<?= $lab_prefix . '-' . $champ1; ?>" value="1" id="<?= $lab_prefix . '-' . $champ1; ?>-1" 
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ1, $traces['lab']) && $traces['lab'][$champ1] ? 'checked' : '') : NULL; ?>>
									précis
							</label>
                            <label class="btn btn-outline-primary no-margin" for="<?= $lab_prefix . '-' . $champ1; ?>-0" style="width: 125px; margin-left: -5px;">
								<input type="radio" name="<?= $lab_prefix . '-' . $champ1; ?>" value="0" id="<?= $lab_prefix . '-' . $champ1; ?>-0" 
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ1, $traces['lab']) && $traces['lab'][$champ1] ? '' : 'checked') : NULL; ?>>
									non précis
							</label>
						</div>

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ1; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ1); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Exactitude
                     *
                     * ------------------------------------------------ */ ?>
					<td class="text-center">
						<div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
							<label class="btn btn-outline-primary no-margin" style="width: 125px; border-right: 0">
								<input type="radio" name="<?= $lab_prefix . '-' . $champ2; ?>" value="1" id="<?= $lab_prefix . '-' . $champ2; ?>-1" 
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ2, $traces['lab']) && $traces['lab'][$champ2] ? 'checked' : '') : NULL; ?>>
									exact
							</label>
							<label class="btn btn-outline-primary no-margin" style="width: 125px; margin-left: -5px;">
								<input type="radio" name="<?= $lab_prefix . '-' . $champ2; ?>" value="0" id="<?= $lab_prefix . '-' . $champ2; ?>-0" 
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ2, $traces['lab']) && $traces['lab'][$champ2] ? '' : 'checked') : NULL; ?>>
									non exact
							</label>
						</div>

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ2; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ2); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Validite
                     *
                     * ------------------------------------------------ */ ?>
					<td class="text-center">
						<div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
							<label class="btn btn-outline-primary no-margin" style="width: 125px; border-right: 0">
								<input type="radio" name="<?= $lab_prefix . '-' . $champ3; ?>" value="1" id="<?= $lab_prefix . '-' . $champ3; ?>-1" 
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ3, $traces['lab']) && $traces['lab'][$champ3] ? 'checked' : '') : NULL; ?>>
									valide
							</label>
							<label class="btn btn-outline-primary no-margin" style="width: 125px; margin-left: -5px;">
								<input type="radio" name="<?= $lab_prefix . '-' . $champ3; ?>" value="0" id="<?= $lab_prefix . '-' . $champ3; ?>-0" 
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ3, $traces['lab']) && $traces['lab'][$champ3] ? '' : 'checked') : NULL; ?>>
									non valide
							</label>
						</div>

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ3; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ3); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
				</tr>

			</tbody>
		</table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-spectro-mo -->
