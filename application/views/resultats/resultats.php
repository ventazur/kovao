<div id="resultats">
<div class="container">
        
	<div class="row">
		<div class="col-sm-7">
			<h3>Résultats 

				<? if ( ! empty($enseignant['semestre_id']) || ! empty($semestre_id)) :  ?>

					<span style="color: limegreen"><?= $semestres[$semestre_id]['semestre_code']; ?></span>

				<? endif; ?>

			</h3>
		</div>
        <div class="col-sm-5">

            <div class="float-right">

                <? if ( ! empty($semestres) && count($semestres) > 1) : ?>
    
                    <div class="dropdown">	

                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Autres semestres
                        </button>

                        <? $semestres_r = array_reverse($semestres); ?>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                        <? foreach($semestres_r as $s) : ?>

                            <? if ($s['semestre_id'] == $semestre_id) continue; ?>

                            <a class="dropdown-item" href="<?= base_url() . 'resultats/semestre/' . $s['semestre_code']; ?>">
                                <?= $s['semestre_code']; ?>
                            </a>

                        <? endforeach; ?>
                        
                        </div>

                    </div>

                <? endif; ?>

            </div>

		</div> <? // .col-md-5 ?>
	</div> <? // .row ?>

    <div class="hspace"></div>

    <?
    // ------------------------------------------
    //
    // Aucun semestre selectionne par l'enseignant
    //
    // ------------------------------------------ */ ?>

    <? if (empty($enseignant['semestre_id']) && empty($semestre_id)) : ?>

		<div class="space"></div>
        <i class="fa fa-exclamation-circle"></i> Vous n'avez sélectionné aucun semestre.
        <span style="margin-right: 15px"></span>
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url() . 'configuration'; ?>">Configuration</a>

        </div>
        <? return; ?>

    <? endif; ?>

    <?
    // ------------------------------------------
    //
    // Aucun cours selectionne par l'enseignant
    //
    // ------------------------------------------ */ ?>

    <? if (empty($cours_raw)) : ?>

		<div class="space"></div>
        <i class="fa fa-exclamation-circle"></i> Vous n'avez sélectionné aucun cours pour ce semestre.
        <span style="margin-right: 15px"></span>
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url() . 'configuration'; ?>">Configuration</a>

    <? endif; ?>

    <?
    // ------------------------------------------
    //
    // Cours
    //
    // ------------------------------------------ */ ?>

    <? $soumissions_traitees = array(); // une liste de soumission_ids suite a leur affichage ?>

    <? foreach($cours_raw as $cours_id => $c) : ?>

		<div class="space"></div>

        <h4><?= $c['cours_nom'] . ' (' . $c['cours_code'] . ')'; ?></h4>

        <div class="space"></div>

        <table class="soumissions table table-bordered">
            <thead style="background-color: limegreen; color: #fff;">
                <tr>
                    <th scope="col">Prénom et Nom (Numéro DA)</th>
                    <th scope="col">Date de remise</th>
                    <th style="text-align: center" scole="col">Durée</th>
                    <th style="text-align: center" scope="col">Résultat</th>
                    <th style="text-align: center" scope="col">Vues</th>
                    <th style="text-align: right" scope="col">Opérations</th>
                </tr>
            </thead>

            <?
            // ------------------------------------------
            //
            // Evaluations
            //
            // ------------------------------------------ */ ?>

            <? foreach($evaluations_eleves as $evaluation_id => $groupes) : ?>

				<? if ($evaluations[$evaluation_id]['cours_id'] != $cours_id) : continue; endif; ?>

                <tr style="background-color: #444; color: #fff">
                    <td colspan="6">
                        <?= $evaluations[$evaluation_id]['evaluation_titre']; ?>
                    </td>
                </tr>
    
                <?
                // ------------------------------------------
                //
                // Groupes
                //
                // ------------------------------------------ */ ?>

                <? foreach($groupes as $cours_groupe => $groupe) : ?>

                    <? $rendre_visible = array(); ?>

                    <? $nb_evaluations = 0; $points_totaux = 0; ?>

                    <tr class="soumissions-liste-toggle" style="cursor: pointer">
                        <td colspan="6">
                            <div class="row">
                                <div class="col-8">
                                    <strong>
                                    <? if ($cours_groupe && $cours_groupe != 999) : ?>
                                        Groupe <?= $cours_groupe; ?>
                                    <? else : ?>
                                        Groupe inconnu
                                    <? endif; ?>
                                    </strong>
                                </div>
                                <div class="col-4 soumissions-liste-toggle-btn" style="text-align: right">
                                    <span style="margin-right: 10px"><?= count($groupe); ?> évaluation<?= count($groupe) > 1 ? 's' : ''; ?></span>
                                    <span class="collap d-none"><i class="fa fa-minus-square-o fa-lg"></i></span>
                                    <span class="expand"><i class="fa fa-plus-square-o fa-lg"></i></span>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <?
                    // ------------------------------------------
                    //
                    // Soumissions
                    //
                    // ------------------------------------------ */ ?>

                    <tbody class="soumissions-liste d-none">

                    <? foreach($groupe as $soumission_id) : ?> 

                        <? $s = $soumissions[$soumission_id]; ?>

                        <? $rendre_visible[] = $s['soumission_id']; ?>

                        <? $nb_evaluations++; $points_totaux += $s['points_obtenus']; ?>

                        <? if ($s['evaluation_id'] != $evaluation_id) continue; ?>

                        <tr>
                            <td scope="row"><?= $s['prenom_nom'] . ' (' . $s['numero_da'] . ')'; ?></td>
                            <td><?= $s['soumission_date']; ?></td>
                            <td style="text-align: center"><?= $s['duree']; ?></td>
                            <td style="text-align: right">
                                (<?= number_format($s['points_obtenus'] / $s['points_total'] * 100); ?>%)
                                <?= my_number_format($s['points_obtenus']) . ' / ' . my_number_format($s['points_total']); ?>
                            </td>
                            <td style="text-align: center"><?= $s['vues']; ?></td>
                            <td style="text-align: right">

                                <? if ($s['permettre_visualisation']) : ?>

                                    <div class="btn btn-light btn-sm active">
                                         <i class="fa fa-eye text-success"></i>
                                         Visible
                                    </div> 

                                <? else : ?>

                                    <div class="btn btn-light btn-sm active">
                                         <i class="fa fa-ban text-danger"></i>
                                         Invisible
                                    </div> 

                                <? endif; ?>

                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opérations</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?= base_url() . 'corrections/voir/' . $s['soumission_reference']; ?>">
                                        <i class="fa fa-external-link" style="margin-right: 5px"></i> Consulter
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url() . 'corrections/' . $s['soumission_id']; ?>">
                                        <i class="fa fa-wrench" style="margin-right: 5px"></i> Modifier la correction
                                    </a>
                                    <? if ($s['permettre_visualisation']) : ?>
                                        <a class="rendre-invisible dropdown-item" href="#" data-soumission_ids="<?= htmlspecialchars(serialize(array($s['soumission_id']))); ?>">
                                            <i class="fa fa-ban" style="margin-right: 5px"></i> Rendre invisible
                                        </a>
                                    <? else : ?>
                                        <a class="rendre-visible dropdown-item" href="#" data-soumission_ids="<?= htmlspecialchars(serialize(array($s['soumission_id']))); ?>">
                                            <i class="fa fa-eye" style="margin-right: 5px"></i> Rendre visible
                                        </a>
                                    <? endif; ?>

                                    <div role="separator" class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="#" data-soumission_id="<?= $s['soumission_id']; ?>" data-toggle="modal" data-target="#modal-effacer-soumission">
                                        <i class="fa fa-trash" style="color: crimson; margin-right: 5px;"></i>
                                        <span style="color: crimson">Effacer cette évaluation</span>
                                    </a>
                                </div>
                            </td>
                        </tr>

                    <? endforeach; // soumissions ?>

                    <tr>
                        <td colspan="6">

                            <div class="row"> 
                                <div class="col">
                                    <span class="btn btn-warning btn-sm">
                                        Moyenne :
                                        <? // Je prends simplement le dernier resultat pour determiner le nombre de points de l'evaluation ?>
                                        <span style="margin-left: 5px"><?= my_number_format($points_totaux / $nb_evaluations) . ' / ' . my_number_format($s['points_total']); ?></span>
                                        <span style="margin-left: 5px">(<?= number_format(($points_totaux/$nb_evaluations)/$s['points_total']*100) . '%'; ?>)</span>
                                    </span>
                                </div>
                    
                                <div class="col pull-right" style="text-align: right">
                                    <div class="rendre-visible btn btn-sm btn-outline-secondary" data-soumission_ids="<?= htmlspecialchars(serialize($rendre_visible)); ?>">
                                        <i class="fa fa-eye text-success" style="margin-right: 5px"></i>
                                        Rendre ce groupe visible
                                    </div>
                                    <div class="rendre-invisible btn btn-sm btn-outline-secondary" data-soumission_ids="<?= htmlspecialchars(serialize($rendre_visible)); ?>">
                                        <i class="fa fa-ban text-danger" style="margin-right: 5px"></i>
                                        Rendre ce groupe invisible
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
            
                </tbody>

                <? endforeach; // groupes ?>

            <? endforeach; // evaluations ?> 

            </table>

    <? endforeach; // cours ?> 

</div> <!-- /.container -->
</div> <!-- #resultats -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER UNE SOUMISSION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-soumission" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer une évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-soumission-form'), 
						array('soumission_id' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Êtes-vous certain de vouloir effacer cette évaluation ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>

			<div class="modal-footer">
        		<div id="modal-effacer-soumission-sauvegarde" class="btn btn-danger"><i class="fa fa-trash"></i> Effacer</div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>

    	</div>
  	</div>
</div>
