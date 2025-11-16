<?
/* ----------------------------------------------------------------------------
 *
 * Corrections
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="corrections">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre
     *
     * ------------------------------------------------------------------------ */ ?>

    <h3>
        Corrections en attente

        <? if ( ! empty($enseignant['semestre_id'])) :  ?>

            <span style="color: dodgerblue"><?= $semestres[$enseignant['semestre_id']]['semestre_code']; ?></span>

        <? endif; ?>
    </h3>

    <?
    // ------------------------------------------
    //
    // Aucun semestre selectionne par l'enseignant
    //
    // ------------------------------------------ */ ?>

    <? if (empty($enseignant['semestre_id'])) : ?>

        <i class="fa fa-exclamation-circle"></i> Vous n'avez sélectionné aucun semestre.
        <span style="margin-right: 15px"></span>
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url() . 'configuration'; ?>">Configuration</a>

        <div class="space"></div>

    <? endif; ?>

    <?
    // ------------------------------------------
    //
    // Aucun cours selectionne par l'enseignant
    //
    // ------------------------------------------ */ ?>

    <? if (empty($cours_raw)) : ?>

        <i class="fa fa-exclamation-circle"></i> Aucun cours sélectionné pour ce semestre.
        <span style="margin-right: 15px"></span>
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url() . 'configuration'; ?>">Configuration</a>

        <div class="space"></div>

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Liste des des corrections par cours
     *
     * ------------------------------------------------------------------------ */ ?>

    <? $ec = 0; // evaluations compteur ?>

    <? foreach($cours_raw as $cours_id => $c) : ?>

        <? if ( ! $c['soumission_trouvee']) continue; ?>

        <div class="cours-nom">
            <?= $c['cours_nom'] . ' (' . $c['cours_code'] . ')'; ?>
        </div>

        <?
        // ------------------------------------------
        //
        // Evaluations
        //
        // ------------------------------------------ */ ?>

        <? foreach($evaluations_eleves as $evaluation_id => $groupes) : $ec++; ?>

            <? if ($evaluations[$evaluation_id]['cours_id'] != $cours_id) continue; ?>

            <div class="hspace"></div>

            <div class="cours-titre">
                <?= $evaluations[$evaluation_id]['evaluation_titre']; ?>
            </div>

            <div class="cours-soumissions">

                <table class="soumissions-en-attente">
                    <tbody>

                    <?
                    /* ------------------------------------------
                    /*
                    /* Groupes
                    /*
                    /* ------------------------------------------ */ ?>

                    <? foreach($groupes as $cours_groupe => $groupe) : ?>
                
                        <tr class="soumissions-groupe">
                            <td colspan="3">
                            
                                <? if ($cours_groupe) : ?>
                                    Groupe <?= $cours_groupe; ?>
                                <? else : ?>
                                    Aucun groupe 
                                <? endif; ?>

                            </td>
                            <td style="text-align: right; font-weight: 300">
                                <?= count($groupe); ?>
                                correction<?= count($groupe) > 1 ? 's' : ''; ?>
                            </td>
                                
                        </tr>

                        <tr class="soumissions-entete">
                            <td scope="col">Prénom et Nom</td>
                            <td scope="col" style="width: 200px; text-align: center">Date de remise</td>
                            <td scope="col" style="width: 150px; text-align: center">Status</td>
                            <td scole="col" style="text-align: center">Opérations</td>
                        </tr>

                        <?
                        /* --------------------------------------------
                        /*
                        /* Soumissions
                        /*
                        /* -------------------------------------------- */ ?>

                        <? foreach($groupe as $soumission_id) : ?> 

                            <? $s = $soumissions[$soumission_id]; ?>

                            <? if ($s['evaluation_id'] != $evaluation_id) continue; ?>

                            <tr class="soumissions-soumission">
                                <td scope="row">
                                    <? if ($s['etudiant_id']) : ?>
                                        <i class="bi bi-person-circle"></i>
                                        <a href="<?= base_url() . 'etudiant/' . $s['etudiant_id']; ?>" target="_blank"><?= $s['prenom_nom']; ?></a>

                                    <? else : ?>
                                        
                                        <?= $s['prenom_nom']; ?>

                                    <? endif; ?>
                                </td>
                                <td class="mono" style="text-align: center">
                                    <? if ($s['non_terminee']) : ?>
                                        <span data-toggle="tooltip" title="Cette évaluation a été terminée par l'enseignant." style="color: crimson">
                                    
                                            <?= $s['soumission_date']; ?>

                                        </span>

                                    <? else : ?>

                                        <?= $s['soumission_date']; ?>

                                    <? endif; ?>
                                </td>
                                <td style="text-align: center">
                                    <? if ($s['corrections_terminees']) : ?>

                                        <span style="color: limegreen">Corrigé</span>

                                    <? else : ?>

                                        <span style="color: crimson">À corriger</span>

                                    <? endif; ?>
                                </td>
                                <td style="text-align: right">
                                    <a href="<?= base_url() . 'corrections/corriger/' . $s['soumission_reference']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-pencil-square" style="margin-right: 3px"></i> Corriger
                                    </a>
                                    <div class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modal-effacer-soumission" data-soumission_id="<?= $s['soumission_id']; ?>">
                                        <i class="fa fa-trash" style="margin-right: 3px"></i> 
                                        Effacer
                                    </div> 

                                </td>
                            </tr>

                        <? endforeach; // soumissions ?>

                    <? endforeach; // groupes ?>

                    </tbody>
                </table>

            </div> <!-- .cours-soumissions -->

        <? endforeach; // evaluations ?>

    <? endforeach; // cours ?>

    <? if ($ec == 0) : ?>

        <div class="tspace"></div>

        <i class="fa fa-exclamation-circle"></i> Aucune évaluation à corriger

    <? endif; ?>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- /.container-fluid -->
</div> <!-- #corrections -->

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
