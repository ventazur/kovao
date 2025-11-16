<?
/* --------------------------------------------------------------------
 *
 * TABLEAUX - CHAMPS (POINTS)
 *
 * -------------------------------------------------------------------- */ ?>

<style>
	tr.tr-hover:hover {
		background: #eee;
	}
</style>

<? 
     $types_styles = array(
        'standard'      => 'background: #E1F5FE; color: #777',
		'comparaison' 	=> 'background: #B2FF59', 
		'calcul' 		=> 'background: #FFFF8D', 
		'exactitude'	=> 'background: #ECEFF1',
		'precision'		=> 'background: #CFD8DC',
		'validite'		=> 'background: #90A4AE',
		'absorbance_d' 	=> 'background: #00E5FF'
	); 
?>

<?
/* ----------------------------------------------------------------
 * 
 * TABLEAU
 *
 * ---------------------------------------------------------------- */ ?>

<div id="editeur-tableaux-points" class="mt-4">

    <a class="anchor" name="tableaux-champs"></a>

    <div class="editeur-tableau-titre editeur-section-titre">

        <div class="row">

            <div class="col-sm-6">
                <i class="fa fa-table" style="color: #fff; margin-right: 5px"></i> 
                Tableaux : champs

            </div> <!-- /.col -->

            <div class="col-sm-6">


            </div> <!-- /.col -->

        </div> <!-- .row -->

    </div>    

    <div id="editeur-tableaux-contenu" class="editeur-section-contenu">

        <? if (empty($lab_points_tableaux)) : ?>

            <div class="mt-3" style="color: crimson; font-size: 0.9em">
                <i class="bi bi-exclamation-circle" style="margin-right: 5px"></i> Aucun champ trouvé
            </div>

        <? else : ?>

        <table class="table table-sm table-borderless" style="margin: 0; font-size: 0.9em">
            <? /*
            <thead>
                <tr>
                    <th>Tableau #</th>
                    <th>Champ</th>
                    <th>_d</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Points</th>
                    <th>EDIT</th>
                </tr>
            </thead>
            */ ?>
            <tbody>

            <? foreach(range(1, 99) as $no_tableau) : ?>

                <? if ( ! array_key_exists($no_tableau, $lab_points_tableaux)) continue; ?>

                <? // Determiner le pointage total du tableau ?>

                <?
                    $ce_tableau_points_totaux = 0;

                    foreach($lab_points_tableaux[$no_tableau] as $c => $c_arr) 
                    {
                        $ce_tableau_points_totaux += $c_arr['points'];
                    }

                    $ce_tableau_points_totaux = format_nombre($ce_tableau_points_totaux);
                ?>

                <tr>
                    <td colspan="10">
                        <div class="row mb-2 <?= $no_tableau == 1 ? '' : 'mt-3'; ?>" style="margin-left: -5px; margin-right: -5px; padding: 10px 0px 10px 0px; background: #C5CAE9; color: #303F9F; border-radius: 3px;">
                            <div class="col-10">
                                Tableau <?= $no_tableau; ?>
                            </div>
                            <div class="col-2 text-right">

                                <?= $ce_tableau_points_totaux; ?> 
                                point<?= $ce_tableau_points_totaux > 1 ? 's' : ''; ?>
        
                            </div>
                        </div>
                    </td>
                </tr>

                <? foreach($lab_points_tableaux[$no_tableau] as $c => $c_arr) : ?>

                    <tr class="tr-hover" style="height: 40px">

                        <? if ($evaluation['enseignant_id'] == $this->enseignant_id || permis('admin_lab')) : ?>
                            <td class="text-left">

                                <div class="modal-tableau-modifier-points btn btn-sm btn-light" data-toggle="modal" data-target="#modal-tableau-modifier-points" style="color: #007bff;"
                                    data-evaluation_id="<?= $evaluation_id; ?>"
                                    data-champ_nom="<?= $c; ?>"
                                    data-champ_type="<?= $c_arr['type'] ?? 'standard'; ?>"
                                    data-champ_points="<?= $c_arr['points']; ?>"
                                    data-champ_tolerance="<?= $c_arr['tolerance'] ?? 0; ?>"
                                    data-champ_cs="<?= $c_arr['cs'] ?? 0; ?>"
                                    data-champ_cspp="<?= $c_arr['cspp'] ?? 0; ?>"
                                    data-champ_est_incertitude="<?= $c_arr['est_incertitude'] ?? 0; ?>"
                                    data-champ_incertitude="<?= $c_arr['incertitude'] ?? NULL; ?>"
                                    data-tableau_no="<?= $c_arr['tableau']; ?>"
									data-champ_desc="<?= htmlentities($c_arr['desc']); ?>"
									data-champ_eq="<?= @htmlentities($c_arr['eq']); ?>"
									data-champ_eq_na="<?= @htmlentities($c_arr['eq_na']); ?>"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                            </td>
                        <? endif; ?>

                        <td>
                            <span class="tag-champ gros" style="margin-left: 0">
                                <?= $c; ?>
                            </span>
                        </td>
                
                        <td>

                            <? if ( ! empty($c_arr['est_incertitude'])) : ?>
                                <span class="ml-2" style="background: #4527A0; color: #fff; padding: 3px 5px 3px 5px; border-radius: 3px">
                                    <i class="bi bi-arrows-collapse-vertical"></i>
                                </span>
                            <? endif; ?>

                            <? if (empty($c_arr['est_incertitude']) && ! empty($c_arr['incertitude'])) : ?>
                                <span class="tag-champ gros" style="background: #9575CD; color: #fff">
                                    <?= $c_arr['incertitude']; ?>
                                </span>
                            <? endif; ?>

                        </td>

						<td style="font-family: Lato; font-weight: 300">

							<?= $c_arr['desc']; ?>

                            <? if ( ! empty($c_arr['type']) && $c_arr['type'] == 'calcul' && ! empty($evaluation['lab_corr_controller'])) : ?>

								<? $eq = NULL; $na = NULL; ?>

								<?
								/* -------------------------------------------------
								 * 
								 * Calcul - Equation
								 *
								 * ------------------------------------------------- */ ?>

								<? if ( ! empty($c_arr['eq'])) : ?>

									<? $eq_enseignant = TRUE; ?>
									<? $eq = $c_arr['eq']; ?>

								<? else : ?>

									<? $eq_enseignant = FALSE; ?>
									<? $eq = $this->Lab_model->extraire_equation($evaluation['lab_corr_controller'], $c); ?>

								<? endif; ?>

                                <? if ( ! empty($eq)) : ?>

                                    <div style="margin-top: 5px"> </div>
									<span class="mono" style="font-size: 0.9em; margin-top: 4px; color: #444; background: <?= $eq_enseignant ? '#FFAB91' : '#FFFF8D'; ?>; padding: 5px 8px 5px 8px; border-radius: 3px;">
                                        <?= $eq; ?>
                                    </span>

								<? endif; ?>

								<?
								/* -------------------------------------------------
								 * 
								 * Calcul - Equation - Nombres arrondies (na)
								 *
								 * ------------------------------------------------- */ ?>

								<? if ( ! empty($c_arr['eq_na'])) : ?>

									<? $na_enseignant = TRUE; ?>
									<? $na = $c_arr['eq_na']; ?>

								<? else : ?>

									<? $eq = $this->Lab_model->extraire_equation($evaluation['lab_corr_controller'], $c); ?>

									<? $na_enseignant = FALSE; ?>
									<? $na = $this->Lab_model->extraire_equation_na($evaluation['lab_corr_controller'], $c); ?>	

								<? endif; ?>

                                <? if ( ! empty($na)) : ?>

									<? if ( ! $na_enseignant) : ?>
							
										<? 
										 	foreach($na as &$na_i)
											{
												$na_i = '[' . $na_i . ']';
											}
										?>					
										<? $na = implode(', ', $na); ?>
									<? endif; ?>

                                    <div style="margin-top: 5px"> </div>
									<span class="mono" style="font-size: 0.9em; margin-top: 4px; color: #444; background: <?= $na_enseignant ? '#FFAB91' : '#FFFF8D'; ?>; padding: 5px 8px 5px 8px; border-radius: 3px;">
										<?= 'valeurs arrondies :'; ?>
                                        <?= $na; ?>
                                    </span>

								<? endif; ?>

							<? endif; ?>
                        </td>

                        <td class="text-left">
                            <? if ( ! empty($c_arr['type'])) : ?>

                                <span class="badge pill" style="<?= $types_styles[$c_arr['type']]; ?>; padding: 5px 7px 5px 7px; font-weight: 300; font-size: 0.8em">
                                    <i class="bi bi-<?= $this->config->item('lab_champs_types_icons')[$c_arr['type']]; ?>" style="margin-right: 3px; color: #000"></i>
                                    <?= $c_arr['type'] ?? 'erreur'; ?>

                                    <? if ($c_arr['type'] == 'comparaison' && ! empty($c_arr['tolerance'])) : ?>
                                       à <?= str_replace('.', ',', $c_arr['tolerance']); ?>%
                                    <? endif; ?>
                                </span>

                            <? endif; ?>
                        </td>
                        <td class="text-left">

                            <? if (in_array($c_arr['type'], array('precision', 'exactitude', 'validite'))) : ?>

                                &nbsp;

                            <? elseif (empty($c_arr['cs']) || (in_array($c_arr['type'], array('precision', 'exactitude', 'validite')))) : ?>

                                &nbsp;

                            <? else : ?>

                                <? if ($c_arr['cs'] < 10) : ?>
                                    <i class="bi bi-<?= $c_arr['cs']; ?>-circle"></i> CS
                                <? elseif ($c_arr['cs'] == 99) : ?>
                                    <i class="bi bi-circle-fill"></i> CS
                                <? else : ?>
                                    <?= $c_arr['cs'] . ' CS'; ?>
                                <? endif; ?>

                                (<span style="color: crimson">-<?= ($c_arr['cspp'] ?? 0);?>%</span>)

                            <? endif; ?>

                        </td>

                        <?
                        /* --------------------------------------------
                         *
                         * Points
                         *
                         * -------------------------------------------- */ ?>

                        <td class="text-right">

                            <? if ($c_arr['points'] == 0) : ?>
                                <span style="color: crimson">
                                    <?= format_nombre($c_arr['points']); ?> point<?= $c_arr['points'] > 1 ? 's' : ''; ?>
                                </span>
                            <? else : ?>
                                <?= format_nombre($c_arr['points']); ?> point<?= $c_arr['points'] > 1 ? 's' : ''; ?>
                            <? endif; ?>

                        </td>
                    </tr>

                <? endforeach; ?>

            <? endforeach; ?>

            </tbody>
        </table>

        <? endif; ?>

        <? if (permis('admin_lab')) : ?>
            <hr style="margin-top: 25px" />

            <div class="btn btn-outline-primary" id="tableau-ajouter-points" data-toggle="modal" data-target="#modal-tableau-ajouter-points">
                <i class="bi bi-plus-circle mr-1"></i>
                Ajouter un champ
            </div>
        <? endif; ?>

    </div>

</div> <!-- .editeur-tableau -->

<?
/* --------------------------------------------------------------------
 *
 * MODALS
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* -------------------------------------------------------------------------
 *
 * (modal) AJOUTER UN CHAMP (DES POINTS)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-tableau-ajouter-points" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle" style="margin-right: 5px"></i> 
                    Ajouter un champ
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div> <!-- .modal-header -->

            <div class="modal-body">

                <div class="alert alert-danger d-none" role="alert" style="margin: 15px">
                    <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                    <span class="alert-msg"></span>
                </div>

                <?= 
                form_open(
                    NULL, array('id' => 'modal-tableau-ajouter-points-form'),
                    array('evaluation_id'  => $evaluation_id)
                );
                ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Nom du champ / Type de champ
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-2">

					<div class="form-group col-md-6">
						<label for="tableau-ajouter-points-nom-champ">Nom du champ</label>
						<input name="nom_champ" class="form-control" id="tableau-ajouter-points-nom-champ" type="text" value="">
						<div class="mt-2 ml-1" style="font-size: 0.8em; color: #777;">
							Ajouter _d pour les incertitudes.
						</div>
					</div>
					<div class="form-group col-md-6">
						<label for="tableau-ajouter-points-type-champ">Type de champ</label>
						<div class="input-group">
							<select name="type" class="form-control" id="tableau-ajouter-points-type-champ">
								<option value="standard" selected>standard</option>
								<option value="comparaison">comparaison</option>
								<option value="calcul">calcul</option>
								<option value="precision">precision</option>
								<option value="exactitude">exactitude</option>
								<option value="validite">validite</option>
								<option value="absorbance_d">absorbance_d</option>
							</select>
						</div>
					</div>
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Tableau / Points
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-2">

					<div class="form-group col-md-4">
                        <label for="tableau-ajouter-points-tableau-champ">Tableau #</label>
                        <input name="tableau" class="form-control" id="tableau-ajouter-points-tableau-champ" type="number" value="1" min="1" max="99">
					</div>

                    <div class="form-group col-md-4">
                        <label for="tableau-ajouter-points-points-champ">Points</label>
                        <div class="input-group">
                            <input name="points" type="text" class="form-control" id="tableau-ajouter-points-points-champ" value="1">
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="tableau-ajouter-points-tolerance-champ">Tolérance (comparaison)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">à</div>
                            </div>
                            <input name="tolerance" type="text" class="form-control" id="tableau-ajouter-points-tolerance-champ" value="0">
                            <div class="input-group-append">
                                <div class="input-group-text">%</div>
                            </div>
                        </div>
                    </div>

                </div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * CS (Chiffres significatifs)
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-2">
					<div class="form-group col-md-4">
						<label for="field1">Chiffres significatifs</label>
						<div class="input-group">
							<input name="cs" class="form-control" id="tableau-ajouter-points-cs-champ" type="number" value="0" min="0" max="99">
							<div class="input-group-append">
								<div class="input-group-text">CS</div>
							</div>
						</div>
					</div>
					<div class="form-group col-md-4">
						<label for="field2">Pénalité pour CS</label>
						<div class="input-group">
                    		<input name="cspp" class="form-control" id="tableau-ajouter-points-cspp-champ" type="number" value="50" min="0" max="100">
							<div class="input-group-append">
								<div class="input-group-text">%</div>
							</div>
						</div>
					</div>
                    <div class="ml-2" style="font-size: 0.8em; color: #777;">
                        0 = ne pas considéré les chiffres significatifs
                        99 = <i class="bi bi-circle-fill"></i> = ajuster au nombre de décimales de son incertitude (ou de la valeur appropriée)
                    </div>
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Est incertitude ?
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-4">
					<div class="form-group col-md-6">
						<label>Ce champ est une incertitude ?</label>
						<div id="tableau-ajouter-points-est-incertitude" class="btn-group btn-group-toggle d-block" data-toggle="buttons">
							<label class="btn btn-outline-primary no-margin" style="width: 80px">
								<input type="radio" name="est_incertitude" value="1" id="tableau-ajouter-points-est-incertitude-1" autocomplete="off"> Oui
							</label>
							<label class="btn btn-outline-primary no-margin" style="width: 80px; margin-left: -5px;">
								<input type="radio" name="est_incertitude" value="0" id="tableau-ajouter-points-est-incertitude-0" autocomplete="off" checked>Non
							</label>
						</div>
					</div>
					<div class="form-group col-md-6">
						<label for="inputField">Si non, sélectionnez son incertitude :</label>
						<div class="input-group">
							<select name="incertitude" class="form-control" id="tableau-ajouter-points-champ-incertitude">
								<option value="">aucun</option>
								<? if ( ! empty($lab_points)) : ?>
									<? foreach($lab_points as $c => $c_arr) :  ?>
										<?= p($c); ?>
										<? $est_incertitude = $c_arr['est_incertitude'] ?? 0; 
										   if ( ! $est_incertitude) continue; 
										?>
										<option value="<?= $c; ?>"><?= $c; ?></option>
									<? endforeach; ?>
								<? endif; ?>
							</select>
						</div>
					</div>
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Description du champ
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-group col-md-12 mt-2">
                    <label for="tableau-ajouter-points-desc-champ">Description du champ</label>
                    <input name="desc" class="form-control" id="tableau-ajouter-points-desc-champ" value="" placeholder="Ma description">
                </div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Equation du calcul
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-group col-md-12 mt-4 calcul-details d-none">
                    <label for="tableau-modifier-ajouter-eq-champ">Calcul : Équation du calcul</label>
                    <input name="eq" class="form-control" id="tableau-ajouter-points-eq-champ" value="" placeholder="Mon équation">
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Valeurs arrondies (nombres arrondies)
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-group col-md-12 mt-3 calcul-details d-none">
                    <label for="tableau-ajouter-points-eq-na-champ">Calcul : Champs avec valeur arrondie dans l'équation (séparé par des virgules)</label>
                    <input name="eq_na" class="form-control" id="tableau-ajouter-points-eq-na-champ" value="" placeholder="Les champs arrondis">
				</div>

            </form>

      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">
                <div class="col">
                    <? /*
                    <div id="modal-modifier-champ-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer ce champ
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                        </div>
                    */ ?>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-tableau-ajouter-points-sauvegarde" class="btn btn-success spinnable">
                        <i class="bi bi-plus-circle" style="margin-right: 3px"></i> Ajouter les points
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>

            </div> <!-- .modal-footer -->

    	</div> <!-- .modal-content -->
  	</div> <!-- .modal-dialog -->
</div> <!-- .modal -->

<?
/* -------------------------------------------------------------------------
 *
 * (modal) MODIFIER UN CHAMP (DES POINTS)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-tableau-modifier-points" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="bi bi-pencil-square" style="margin-right: 5px"></i> Modifier les points d'un champ</span></var></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div> <!-- .modal-header -->

      		<div class="modal-body">

                <div class="alert alert-danger d-none" role="alert" style="margin: 15px">
                    <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                    <span class="alert-msg"></span>
                </div>

                <?= 
                form_open(
                    NULL, array('id' => 'modal-tableau-modifier-points-form'),
                    array(
						'evaluation_id'  => $evaluation_id,
						'nom_champ_origine' => NULL
					)
                );
                ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Nom du champ / Type de champ
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-2">

					<div class="form-group col-md-6">
						<label for="tableau-modifier-points-nom-champ">Nom du champ</label>
                        <input name="nom_champ" class="form-control" id="tableau-modifier-points-nom-champ" type="text" value="" <?= permis('admin_lab') ? '' : 'readonly'; ?>>
					</div>

					<div class="form-group col-md-6">
						<label for="tableau-modifier-points-type-champ">Type de champ</label>
						<div class="input-group">
                            <select name="type" class="form-control" id="tableau-modifier-points-type-champ" <?= permis('admin_lab') ? '' : 'disabled'; ?>>
								<option value="standard" selected>standard</option>
								<option value="comparaison">comparaison</option>
								<option value="calcul">calcul</option>
								<option value="precision">precision</option>
								<option value="exactitude">exactitude</option>
								<option value="validite">validite</option>
								<option value="absorbance_d">absorbance_d</option>
							</select>
						</div>
					</div>

                </div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Tableau / Points
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-row col-md-12 mt-2">

                    <? if (permis('admin_lab')) : ?>
                        <div class="form-group col-md-4">
                            <label for="tableau-modifier-points-tableau-champ">Tableau #</label>
                            <input name="tableau" class="form-control" id="tableau-modifier-points-tableau-champ" type="number" value="1" min="1" max="99">
                        </div>
                    <? endif; ?>

                    <div class="form-group col-md-4">
                        <label for="tableau-modifier-points-points-champ">Points</label>
                        <div class="input-group">
                            <input name="points" type="text" class="form-control" id="tableau-modifier-points-points-champ" value="">
                            <div class="input-group-append">
                                <div class="input-group-text">point(s)</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="tableau-modifier-points-tolerance-champ">Tolérance (comparaison)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">à</div>
                            </div>
                            <input name="tolerance" type="text" class="form-control" id="tableau-modifier-points-tolerance-champ" value="">
                            <div class="input-group-append">
                                <div class="input-group-text">%</div>
                            </div>
                        </div>
                    </div>

                </div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * CS (Chiffres significatifs)
                 *
                 * -------------------------------------------------------------------- */ ?>

				<div class="form-row col-md-12 mt-2">
					<div class="form-group col-md-6">
						<label for="field1">Chiffres significatifs</label>
						<div class="input-group">
							<input name="cs" class="form-control" id="tableau-modifier-points-cs-champ" type="number" value="" min="0" max="99">
							<div class="input-group-append">
								<div class="input-group-text">CS</div>
							</div>
						</div>
					</div>
					<div class="form-group col-md-6">
						<label for="field2">Pénalité pour CS</label>
						<div class="input-group">
                    		<input name="cspp" class="form-control" id="tableau-modifier-points-cspp-champ" type="number" value="" min="0" max="100">
							<div class="input-group-append">
								<div class="input-group-text">%</div>
							</div>
						</div>
					</div>
                    <div class="ml-2" style="font-size: 0.8em; color: #777;">
                        0 = ne pas considéré les chiffres significatifs<br />
                        99 = <i class="bi bi-circle-fill"></i> = ajuster au nombre de décimales de son incertitude (ou de la valeur appropriée)
                    </div>
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Est incertitude ?
                 *
                 * -------------------------------------------------------------------- */ ?>

                <? if (permis('admin_lab')) : ?>
                    <div class="form-row col-md-12 mt-4">
                        <div class="form-group col-md-6">
                            <label>Ce champ est une incertitude ?</label>
                            <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
                                <label class="btn btn-outline-primary no-margin" style="width: 80px">
                                    <input type="radio" name="est_incertitude" value="1" id="tableau-modifier-points-est-incertitude-1" autocomplete="off" > Oui
                                </label>
                                <label class="btn btn-outline-primary no-margin" style="width: 80px; margin-left: -5px;">
                                    <input type="radio" name="est_incertitude" value="0" id="tableau-modifier-points-est-incertitude-0" autocomplete="off"> Non
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputField">Si non, sélectionnez son incertitude :</label>
                            <div class="input-group">
                                <select name="incertitude" class="form-control" id="tableau-modifier-points-champ-incertitude">
                                    <option value="">aucun</option>
                                    <? if ( ! empty($lab_points)) : ?>
                                        <? foreach($lab_points as $c => $c_arr) :  ?>
                                            <? $est_incertitude = $c_arr['est_incertitude'] ?? 0; 
                                               if ( ! $est_incertitude) continue; 
                                            ?>
                                            <option value="<?= $c; ?>"><?= $c; ?></option>
                                        <? endforeach; ?>
                                    <? endif; ?>
                                </select>
                            </div>
                        </div> <!-- .form-group -->
                    </div>
                <? endif; ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Description du champ
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-group col-md-12 mt-3">
                    <label for="tableau-modifier-points-desc-champ">Description du champ</label>
                    <input name="desc" class="form-control" id="tableau-modifier-points-desc-champ" value="" placeholder="Ma description">
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Equation du calcul
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-group col-md-12 mt-4 calcul-details d-none">
                    <label for="tableau-modifier-points-eq-champ">Calcul : Équation du calcul</label>
                    <input name="eq" class="form-control" id="tableau-modifier-points-eq-champ" value="" placeholder="Mon équation">
				</div>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Valeurs arrondies (nombres arrondies)
                 *
                 * -------------------------------------------------------------------- */ ?>

                <div class="form-group col-md-12 mt-3 calcul-details d-none">
                    <label for="tableau-modifier-points-eq-na-champ">Calcul : Champs avec valeur arrondie dans l'équation (séparé par des virgules)</label>
                    <input name="eq_na" class="form-control" id="tableau-modifier-points-eq-na-champ" value="" placeholder="Les champs arrondis">
                </div>

            </form>

      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">

				<? if (permis('admin_lab')) : ?>

                    <div class="col">
                        <div id="modal-tableau-modifier-points-effacer" class="btn btn-outline-danger spinnable">
                            <i class="bi bi-trash" style="margin-right: 3px"></i> Effacer ce champ
                            <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                        </div>
                    </div> <!-- .col -->

				<? endif; ?>

				<div class="col text-right">
					<div id="modal-tableau-modifier-points-sauvegarde" class="btn btn-success spinnable">
						<i class="bi bi-floppy" style="margin-right: 3px"></i> Sauvegarder
						<i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
					</div>
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
				</div> <!-- .col -->

            </div> <!-- .modal-footer -->

    	</div> <!-- .modal-content -->
  	</div> <!-- .modal-dialog -->
</div> <!-- .modal -->
