<? 
/* ============================================================================
 *
 * LABORATOIRE - COURS - NOM DU LABO
 *
 * VERSION 2024-XX-XX
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

<script src="<?= base_url() . 'assets/js/lab.js?' . date('U'); ?>"></script>

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
$champ1  = '';
$champ2  = '';
$champ3  = '';
$champ4  = '';
$champ5  = '';
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
                     * Instruments
					 *
                     * ------------------------------------------------ */ ?>

                    <td rowspan="2">
                        <div>Instruments</div>

                    </td>

					<?
					/* ------------------------------------------------
					 *
                     * Masse du becher vide
					 *
					 * ------------------------------------------------ */ ?>

                    <td rowspan="2" class="text-center">
                        <div>Masse bécher vide</div>

                        <div class="input-group input-group-sm mt-2" style="max-width: 250px; margin: 0 auto">

                            <div class="input-group-prepend">
                                <div class="input-group-text">±</div>
                            </div>

                            <input  type="text" class="form-control form-control-sm text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                                value="<?= $traces['lab'][$champ1] ?? NULL; ?>">

                            <div class="input-group-append">
                                <div class="input-group-text">g</div>
                            </div>

                        </div> <!-- .input-group -->

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
                     * Masse du becher + H2O
					 *
                     * ------------------------------------------------ */ ?>

                    <td colspan="3" class="text-center">
                        <div>Masse bécher + H<sub>2</sub>O</div>

                        <div class="input-group input-group-sm mt-2 mb-2" style="max-width: 250px; margin: 0 auto">

                            <div class="input-group-prepend">
                                <div class="input-group-text">±</div>
                            </div>

                            <input  type="text" class="form-control form-control-sm text-right" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                                value="<?= $traces['lab'][$champ2] ?? NULL; ?>">

                            <div class="input-group-append">
                                <div class="input-group-text">g</div>
                            </div>
                        </div>

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ1; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ1); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ2; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ2); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

                    </td>
                </tr>

                <tr>        
                    <td class="text-center">Essai 1</td>
                    <td class="text-center">Essai 2</td>
                    <td class="text-center">Essai 3</td>
                </tr> 

				<?
				/* ------------------------------------------------
				 *
				 * Pipette jaugee de 10 mL
				 *
				 * ------------------------------------------------ */ ?>
                <tr>
                    <td>
                        <div>Pipette jaugée</div>
                        <div>de 10 mL</div>

                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ3; ?>" id="<?= $lab_prefix . '-' . $champ3; ?>" 
                            value="<?= $traces['lab'][$champ3] ?? NULL; ?>">
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
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ4; ?>" id="<?= $lab_prefix . '-' . $champ4; ?>" 
                            value="<?= $traces['lab'][$champ4] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ4; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ4); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ5; ?>" id="<?= $lab_prefix . '-' . $champ5; ?>" 
                            value="<?= $traces['lab'][$champ5] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ5; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ5); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ6; ?>" id="<?= $lab_prefix . '-' . $champ6; ?>" 
                            value="<?= $traces['lab'][$champ6] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ6; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ6); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                </tr>
				<?
				/* ------------------------------------------------
				 *
				 * Burette de 25 mL
				 *
				 * ------------------------------------------------ */ ?>
                <tr>
                    <td>
                        <div>Burette</div>
                        <div>de 25 mL</div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ7; ?>" id="<?= $lab_prefix . '-' . $champ7; ?>" 
                            value="<?= $traces['lab'][$champ7] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ7; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ7); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ8; ?>" id="<?= $lab_prefix . '-' . $champ8; ?>" 
                            value="<?= $traces['lab'][$champ8] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ8; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ8); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ9; ?>" id="<?= $lab_prefix . '-' . $champ9; ?>" 
                            value="<?= $traces['lab'][$champ9] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ9; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ9); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ10; ?>" id="<?= $lab_prefix . '-' . $champ10; ?>" 
                            value="<?= $traces['lab'][$champ10] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ10; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ10); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
					 * Temperature de l'eau (temp)
					 *
					 * ------------------------------------------------ */ ?>
                    <td colspan="5">

						<div class="form-group row" style="margin-bottom: 0">
							<label for="<?= $lab_prefix . '-' . $champ11; ?>" class="col-sm col-form-label">
                           		Température de l'eau
							</label>
							<div class="col-sm-8">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ11; ?>" id="<?= $lab_prefix . '-' . $champ11; ?>" 
                                        value="<?= $traces['lab'][$champ11] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ12; ?>" id="<?= $lab_prefix . '-' . $champ12; ?>" 
                                        value="<?= $traces['lab'][$champ12] ?? NULL; ?>">
									<div class="input-group-append">
										<span class="input-group-text">&deg;C</span>
									</div>
                                </div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

								<div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
									<span id="tag-<?= $champ11; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ11); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ12; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ12); ?>
										<span class="points d-none"></span>
									</span>
								</div>

							</div> <!-- .col -->
						</div> <!-- .form-group -->
                    </td>
                </tr>

				<?
				/* ------------------------------------------------
				 *
				 * Masse volumique de l'eu (rho)
				 *
				 * ------------------------------------------------ */ ?>
                <tr>
                    <td colspan="5">
						<div class="form-group row" style="margin-bottom: 0">
							<label for="<?= $lab_prefix . '-' . $champ13; ?>" class="col-sm col-form-label">
								Masse volumique H<sub>2</sub>O à T&deg;
							</label>
							<div class="col-sm-8">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ13; ?>" id="<?= $lab_prefix . '-' . $champ13; ?>" 
                                        value="<?= $traces['lab'][$champ13] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ14; ?>" id="<?= $lab_prefix . '-' . $champ14; ?>" 
                                        value="<?= $traces['lab'][$champ14] ?? NULL; ?>">
									<div class="input-group-append">
										<span class="input-group-text">g/mL</span>
									</div>
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text"> à </span>
									</div>
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ15; ?>" id="<?= $lab_prefix . '-' . $champ15; ?>" 
                                        value="<?= $traces['lab'][$champ15] ?? NULL; ?>">
									<div class="input-group-append"><span class="input-group-text">&deg;C</span></div>
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

								<div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
									<span id="tag-<?= $champ13; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ13); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ14; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ14); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ15; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ15); ?>
										<span class="points d-none"></span>
									</span>
                                </div>
							</div> <!-- .col -->
						</div> <!-- .form-group -->
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
$champ1  = '';
$champ2  = '';
$champ3  = '';
$champ4  = '';
$champ5  = '';
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
                    <td>Instruments</td>
                    <td colspan="3" class="text-center">
						Masse H<sub>2</sub>O
                        <div class="input-group input-group-sm mt-2 mb-2" style="max-width: 250px; margin: 0 auto">

                            <div class="input-group-prepend">
                                <div class="input-group-text">±</div>
                            </div>

                            <input  type="text" class="form-control form-control-sm text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                                value="<?= $traces['lab'][$champ1] ?? NULL; ?>">

                            <div class="input-group-append">
                                <div class="input-group-text">g</div>
                            </div>

                        </div> <!-- .input-group -->

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ1; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ1); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>

					</td>
                    <td class="text-center">
                        <div>Masse moyenne H<sub>2</sub>O</div>
                        <div>(g)</div>

                    </td>
                </tr>
				<?
				/* ----------------------------------------------------
				 *
                 * Pipette jaugee de 10 mL
                 *
                 * ---------------------------------------------------- */ ?>
                <tr>
                    <td>
						<div>Pipette jaugée</div>
                        <div>de 10 mL</div>

					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                            value="<?= $traces['lab'][$champ2] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ2; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ2); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ3; ?>" id="<?= $lab_prefix . '-' . $champ3; ?>" 
                            value="<?= $traces['lab'][$champ3] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ3; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ3); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ4; ?>" id="<?= $lab_prefix . '-' . $champ4; ?>" 
                            value="<?= $traces['lab'][$champ4] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ4; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ4); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
						<div class="form-group row" style="margin-bottom: 0">
							<div class="col-sm">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ5; ?>" id="<?= $lab_prefix . '-' . $champ5; ?>" 
                                        value="<?= $traces['lab'][$champ5] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ6; ?>" id="<?= $lab_prefix . '-' . $champ6; ?>" 
                                        value="<?= $traces['lab'][$champ6] ?? NULL; ?>">
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

								<div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
									<span id="tag-<?= $champ5; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ5); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ6; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ6); ?>
										<span class="points d-none"></span>
									</span>
								</div>

							</div> <!-- .col -->
						</div> <!-- .form-group -->
					</td>
                </tr>
                <tr>
                    <td>
						<div>Burette</div>
						<div>de 25 mL</div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ7; ?>" id="<?= $lab_prefix . '-' . $champ7; ?>" 
                            value="<?= $traces['lab'][$champ7] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ7; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ7); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ8; ?>" id="<?= $lab_prefix . '-' . $champ8; ?>" 
                            value="<?= $traces['lab'][$champ8] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ8; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ8); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ9; ?>" id="<?= $lab_prefix . '-' . $champ9; ?>" 
                            value="<?= $traces['lab'][$champ9] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ9; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ9); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
						<div class="form-group row" style="margin-bottom: 0">
							<div class="col-sm">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ10; ?>" id="<?= $lab_prefix . '-' . $champ10; ?>" 
                                        value="<?= $traces['lab'][$champ10] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ11; ?>" id="<?= $lab_prefix . '-' . $champ11; ?>" 
                                        value="<?= $traces['lab'][$champ11] ?? NULL; ?>">
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

								<div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
									<span id="tag-<?= $champ10; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ10); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ11; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ11); ?>
										<span class="points d-none"></span>
									</span>
								</div>

							</div> <!-- .col -->
						</div> <!-- .form-group -->
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
$champ1  = '';
$champ2  = '';
$champ3  = '';
$champ4  = '';
$champ5  = '';
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
                    <td style="vertical-align: middle">Instruments</td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>Volume théorique H<sub>2</sub>O</div>
                        <div>(mL)</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle">
                        <div>Volume expérimental H<sub>2</sub>O</div>
                        <div>(mL)</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle">
                        <div>Incertitude relative</div>
                        <div>sur le volume expérimental</div>
                        <div>(%)</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle">
                        <div>Pourcentage d'écart</div>
                        <div> entre le volume expérimental et théorique</div>
                        <div>(%)</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>Pipette jaugée</div>
                        <div> de 10 mL</div>
                    </td>
                    <td>
						<div class="form-group row" style="margin-bottom: 0">
							<div class="col-sm">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                                        value="<?= $traces['lab'][$champ1] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                                        value="<?= $traces['lab'][$champ2] ?? NULL; ?>">
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

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

							</div> <!-- .col -->
						</div> <!-- .form-group -->
					</td>
                    <td>
						<div class="form-group row" style="margin-bottom: 0">
							<div class="col-sm">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ3; ?>" id="<?= $lab_prefix . '-' . $champ3; ?>" 
                                        value="<?= $traces['lab'][$champ3] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ4; ?>" id="<?= $lab_prefix . '-' . $champ4; ?>" 
                                        value="<?= $traces['lab'][$champ4] ?? NULL; ?>">
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

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

							</div> <!-- .col -->
						</div> <!-- .form-group -->
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ5; ?>" id="<?= $lab_prefix . '-' . $champ5; ?>" 
                            value="<?= $traces['lab'][$champ5] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ5; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ5); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ6; ?>" id="<?= $lab_prefix . '-' . $champ6; ?>" 
                            value="<?= $traces['lab'][$champ6] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ6; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ6); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                </tr>
                <tr>
                    <td>
                        <div>Burette</div>
                        <div>de 25 mL</div>
                    </td>
                    <td>
						<div class="form-group row" style="margin-bottom: 0">
							<div class="col-sm">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ7; ?>" id="<?= $lab_prefix . '-' . $champ7; ?>" 
                                        value="<?= $traces['lab'][$champ7] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ8; ?>" id="<?= $lab_prefix . '-' . $champ8; ?>" 
                                        value="<?= $traces['lab'][$champ8] ?? NULL; ?>">
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

								<div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
									<span id="tag-<?= $champ7; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ7); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ8; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ8); ?>
										<span class="points d-none"></span>
									</span>
								</div>

							</div> <!-- .col -->
						</div> <!-- .form-group -->
					</td>
                    <td>
						<div class="form-group row" style="margin-bottom: 0">
							<div class="col-sm">
								<div class="input-group">
                                    <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ9; ?>" id="<?= $lab_prefix . '-' . $champ9; ?>" 
                                        value="<?= $traces['lab'][$champ9] ?? NULL; ?>">
									<div class="input-group-append input-group-prepend">
										<span class="input-group-text">&pm;</span>
									</div>
                                    <input type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ10; ?>" id="<?= $lab_prefix . '-' . $champ10; ?>" 
                                        value="<?= $traces['lab'][$champ10] ?? NULL; ?>">
								</div> <!-- .input-group -->

                                <?  /* Tags ------------------------------------ */ ?>

								<div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
									<span id="tag-<?= $champ9; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ9); ?>
										<span class="points d-none"></span>
									</span>
									<span id="tag-<?= $champ10; ?>" class="tag-champ">
										<?= montre_champ($lab_points, $champ10); ?>
										<span class="points d-none"></span>
									</span>
								</div>

							</div> <!-- .col -->
						</div> <!-- .form-group -->
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ11; ?>" id="<?= $lab_prefix . '-' . $champ11; ?>" 
                            value="<?= $traces['lab'][$champ11] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ11; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ11); ?>
                                <span class="points d-none"></span>
                            </span>
                        </div>
					</td>
                    <td>
                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ12; ?>" id="<?= $lab_prefix . '-' . $champ12; ?>" 
                            value="<?= $traces['lab'][$champ12] ?? NULL; ?>">

                        <?  /* Tags ------------------------------------ */ ?>

                        <div class="tags text-center mt-2 <?= $montre_tags ? '' : 'd-none'; ?>">
                            <span id="tag-<?= $champ12; ?>" class="tag-champ">
                                <?= montre_champ($lab_points, $champ12); ?>
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

                Tableau 4 : La validité des résultats

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
					<td></td>
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
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ1, $traces['lab']) && $traces['lab'][$champ1] ? '' : 'cbecked') : NULL; ?>>
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
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ2, $traces['lab']) && $traces['lab'][$champ2] ? '' : 'cbecked') : NULL; ?>>
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
                                    autocomplete="off" <?= array_key_exists('lab', $traces) ? (array_key_exists($champ3, $traces['lab']) && $traces['lab'][$champ3] ? '' : 'cbecked') : NULL; ?>>
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
