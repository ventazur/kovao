<? 
/* ============================================================================
 *
 * LABORATOIRE - SN1 - DURETE
 *
 * VERSION 2024-09-24
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
$champ1  = 'v_eau';
$champ2  = 'v_eau_d';
$champ3  = 'c_edta';
$champ4  = 'c_edta_d';
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

                Tableau 1

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

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Echantillon d'eau
					 *
                     * ------------------------------------------------ */ ?>

                    <td colspan"1">
                        <div>Échantillon d'eau</div>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Volume d'echantillon d'eau a analyser
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Volume d'échantillon d'eau à analyser (mL)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                                value="<?= $traces['lab'][$champ1] ?? NULL; ?>">

                            <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>

                            <input  type="text" class="form-control" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                                value="<?= $traces['lab'][$champ2] ?? NULL; ?>" style="text-align: left">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ1; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ1); ?>
                                <span class="points d-none"></span>
                            </span>
                            <span id="tag-<?= $champ2; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ2); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Concentration de la solution EDTA
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Concentration de la solution EDTA (&times;10<sup><?= $lab_valeurs['c_edta']['nsci']; ?></sup> mol/L)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ3; ?>" id="<?= $lab_prefix . '-' . $champ3; ?>" 
                                value="<?= $traces['lab'][$champ3] ?? NULL; ?>">

                            <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>

                            <input  type="text" class="form-control" name="<?= $lab_prefix . '-' . $champ4; ?>" id="<?= $lab_prefix . '-' . $champ4; ?>" 
                                value="<?= $traces['lab'][$champ4] ?? NULL; ?>" style="text-align: left">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ3; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ3); ?>
                                <span class="points d-none"></span>
                            </span>
                            <span id="tag-<?= $champ4; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ4); ?>
                                <span class="points d-none"></span>
                            </span>
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

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 2
 *
 * ---------------------------------------------------------------- */ ?>

<?
$champ1  = 'v_edta_d';
$champ2  = 'v_edta-1';
$champ3  = 'v_edta-2';
$champ4  = 'v_edta-3';
$champ5  = 'v_edta-4';

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

                Tableau 2

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

    </div> <!-- .evaluation-tableau-titre -->

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
					<?
					/* ------------------------------------------------
					 *
                     * Essais realises
					 *
					 * ------------------------------------------------ */ ?>

                    <td colspan="2">
                        <div>Essais réalisés</div>
                    </td>
            
                    <td class="text-center">
                        Volume EDTA 

                        <div class="input-group col-6" style="margin: auto">

                            <div class="input-group-prepend">
                                <div class="input-group-text">±</div>
                            </div>

                            <input  type="text" class="form-control" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                                value="<?= $traces['lab'][$champ1] ?? NULL; ?>" style="text-align: left">

                            <div class="input-group-append">
                                <div class="input-group-text">mL</div>
                            </div>
                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ1; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ1); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Partenaire 1
					 *
					 * ------------------------------------------------ */ ?>

                    <td rowspan="2">
                        <div>Partenaire 1</div>
                    </td>
            
                    <td class="text-center">
                        Essai 1
                    </td>

                    <td>
                        <div class="input-group col-6" style="margin: auto">

                            <input  type="text" class="form-control text-center" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                                value="<?= $traces['lab'][$champ2] ?? NULL; ?>">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ2; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ2); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        Essai 2
                    </td>

                    <td>
                        <div class="input-group col-6" style="margin: auto">

                            <input  type="text" class="form-control text-center" name="<?= $lab_prefix . '-' . $champ3; ?>" id="<?= $lab_prefix . '-' . $champ3; ?>" 
                                value="<?= $traces['lab'][$champ3] ?? NULL; ?>">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ3; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ3); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Partenaire 2
					 *
					 * ------------------------------------------------ */ ?>

                    <td rowspan="2" style="border-bottom: 0">
                        <div>Partenaire 2</div>
                    </td>
            
                    <td class="text-center">
                        Essai 3
                    </td>

                    <td>
                        <div class="input-group col-6" style="margin: auto">

                            <input type="text" class="form-control text-center" name="<?= $lab_prefix . '-' . $champ4; ?>" id="<?= $lab_prefix . '-' . $champ4; ?>" 
                                value="<?= $traces['lab'][$champ4] ?? NULL; ?>">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ4; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ4); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        Essai 4
                    </td>

                    <td>
                        <div class="input-group col-6" style="margin: auto">

                            <input  type="text" class="form-control text-center" name="<?= $lab_prefix . '-' . $champ5; ?>" id="<?= $lab_prefix . '-' . $champ5; ?>" 
                                value="<?= $traces['lab'][$champ5] ?? NULL; ?>">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ5; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ5); ?>
                                <span class="points d-none"></span>
                            </span>
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

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 3
 *
 * ---------------------------------------------------------------- */ ?>

<?
$champ1  = 'v_moy';
$champ2  = 'v_moy_d';
$champ3  = 'durete';
$champ4  = 'durete_d';
$champ5  = 'inc_rel';
$champ6  = 'v_adm';
$champ7  = 'v_adm_d';
$champ8  = 'p_ecart';
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

                Tableau 3

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

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Moyenne des volumes EDTA
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Moyenne des volumes EDTA (mL)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                                value="<?= $traces['lab'][$champ1] ?? NULL; ?>">

                            <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>

                            <input  type="text" class="form-control" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                                value="<?= $traces['lab'][$champ2] ?? NULL; ?>" style="text-align: left">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ1; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ1); ?>
                                <span class="points d-none"></span>
                            </span>
                            <span id="tag-<?= $champ2; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ2); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Durete de l'eau analysee (ppm CaCO3)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Dureté de l'eau analysée (ppm CaCO<sub>3</sub>)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ3; ?>" id="<?= $lab_prefix . '-' . $champ3; ?>" 
                                value="<?= $traces['lab'][$champ3] ?? NULL; ?>">

                            <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>

                            <input  type="text" class="form-control" name="<?= $lab_prefix . '-' . $champ4; ?>" id="<?= $lab_prefix . '-' . $champ4; ?>" 
                                value="<?= $traces['lab'][$champ4] ?? NULL; ?>" style="text-align: left">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ3; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ3); ?>
                                <span class="points d-none"></span>
                            </span>
                            <span id="tag-<?= $champ4; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ4); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Incertitude de la durete (%)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Incertitude relative de la dureté (%)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-center" name="<?= $lab_prefix . '-' . $champ5; ?>" id="<?= $lab_prefix . '-' . $champ5; ?>" 
                                value="<?= $traces['lab'][$champ5] ?? NULL; ?>">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ5; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ5); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Valeur admise de la durete (ppm CaCO3)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Valeur admise de la dureté (ppm CaCO<sub>3</sub>)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ6; ?>" id="<?= $lab_prefix . '-' . $champ6; ?>" 
                                value="<?= $traces['lab'][$champ6] ?? NULL; ?>">

                            <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>

                            <input  type="text" class="form-control" name="<?= $lab_prefix . '-' . $champ7; ?>" id="<?= $lab_prefix . '-' . $champ7; ?>" 
                                value="<?= $traces['lab'][$champ7] ?? NULL; ?>" style="text-align: left">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ6; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ6); ?>
                                <span class="points d-none"></span>
                            </span>
                            <span id="tag-<?= $champ7; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ7); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Pourcentage d'ecart (%)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Pourcentage d'écart entre la dureté expérimentale et admise (%)</div>
                    </td>
            
                    <td>
                        <div class="input-group">

                            <input type="text" class="form-control text-center" name="<?= $lab_prefix . '-' . $champ8; ?>" id="<?= $lab_prefix . '-' . $champ8; ?>" 
                                value="<?= $traces['lab'][$champ8] ?? NULL; ?>">

                        </div> <!-- .input-group -->

                        <?
                        //
                        // Donnees sur les champs
                        //
                        ?>
                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ8; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ8); ?>
                                <span class="points d-none"></span>
                            </span>
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
 * Tableau 4 : PRECISION - EXACTITUDE - VALIDITE
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 4
 *
 * -------------------------------------------------------------------- */ ?>
<? 
	$tableau_no = 4;
	$tableau_points = $lab_points_tableaux[$tableau_no]['points'];
?>

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 4
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

                Tableau 4 : La validité de votre résultat expérimental

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

        <table class="table table-bordered mb-0" style="border: 0">
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

</div> <!-- #lab-tableaux-specifiques -->
