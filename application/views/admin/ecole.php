<div id="admin">
<div class="container-fluid">

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h4 style="font-weight: 300"><i class="fa fa-graduation-cap"></i> <?= $adm_ecole['ecole_nom']; ?></h4>

    <div class="hspace"></div>
    <div class="space"></div>

    <div class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-modifier-ecole">
        <i class="fa fa-edit"></i> Modifier
    </div>
    <div class="btn btn-outline-secondary"><i class="fa fa-times"></i> Désactiver (X)</div>
    <div class="btn btn-outline-danger"><i class="fa fa-trash"></i> Effacer cette école (X)</div>

    <div class="dspace"></div>

    <div class="groupe-section-titre autoportant">

        <i class="fa fa-users" style="color: #444; margin-right: 7px"></i>
        Groupes

    </div>

    <? if (empty($adm_groupes)) : ?>

        <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun enseignant dans ce groupe        

    <? endif; ?>

    <div class="btn btn-primary" data-groupe_id="<?= $adm_ecole_id; ?>" data-toggle="modal" data-target="#modal-ajouter-semestre">
        <i class="fa fa-plus-circle"></i> Créer un groupe
    </div>

    <? if ( ! empty($adm_groupes)) : ?>

        <div class="hspace"></div>

        <? foreach($adm_groupes as $groupe_id => $g) : ?>

            <a class="admin-groupe"  href="<?= base_url() . 'admin/groupe/' . $groupe_id; ?>">

                <div class="row">

                    <div class="col-md-11"> 

                        <?= $g['groupe_nom']; ?>    

                    </div>
            
                    <div class="col-md-1" style="text-align: right">

                        <i class="fa fa-angle-right"></i>

                    </div>

                </div> <!-- .row -->

            </a>

        <? endforeach; ?>

    <? endif; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER LES INFORMATIONS D'UNE ECOLE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-ecole" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier une école</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-ecole-form'), 
						array('groupe_id' => NULL)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-modifier-ecole-nom">Le nom de l'école :</label>
                        <input name="semestre_nom" class="form-control" id="modal-modifier-ecole-nom" value="<?= $ecole['ecole_nom']; ?>">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-modifier-ecole-code">Le code de l'école :</label>
                        <input name="ecole_nom_court" class="form-control col-md-2" id="modal-modifier-ecole-code" value="<?= $ecole['ecole_nom_court']; ?>">
                        <small class="form-text text-muted">
                            <i class="fa fa-exclamation-circle" style="color: #aaa; margin-right: 5px"></i> Ce code servira pour déterminer les sous-domaines.
                        </small>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-modifier-ecole-url">Le lien vers la  page web de l'école :</label>
                        <input name="ecole_url" class="form-control" id="modal-modifier-ecole-url" placeholder="http://" value="<?= $ecole['ecole_url']; ?>">
					</div>
			
				</form>
				
      		</div>
      
			<div class="modal-footer">
        		<div id="modal-modifier-ecole-sauvegarde" class="btn btn-success"><i class="fa fa-save"></i> Modifier cette école</div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER SEMESTRE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-semestre" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Éditer un semestre</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-semestre-form'), 
						array('semestre_id' => NULL, 'groupe_id' => NULL)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-editer-semestre-nom">Le titre du semestre :</label>
						<input name="semestre_nom" class="form-control" id="modal-editer-semestre-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-semestre-code">Le code du semestre :</label>
						<input name="semestre_code" class="form-control col-md-2" id="modal-editer-semestre-code">
                        <small class="form-text text-muted">
                            ex. H2018
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-semestre-debut-date">La date du <strong>début</strong> du semestre :</label>
						<input name="semestre_debut_date" class="form-control col-md-2 datepicker" id="modal-editer-semestre-debut-date" value="">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-semestre-fin-date">La date de la <strong>fin</strong> du semestre :</label>
						<input name="semestre_fin_date" class="form-control col-md-2 datepicker" id="modal-editer-semestre-fin-date" value="">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="form-group col-md-12" style="margin-top: 30px">
						<div class="form-check">
							<input type="checkbox" name="confirmation_effacer_semestre" class="form-check-input">
							<label class="form-check-label" for="exampleCheck1">
								Pour effacer ce semestre, confirmez en cochant.
								<br />(La seule raison valable pour effacer est si un semestre vient d'être ajouté par mégarde.)
							</label>
						</div>
				  	</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
				<div class="col-4">
					<div id="modal-effacer-semestre-sauvegarde" style="margin-left: 5px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-trash"></i> Effacer semestre (X)</div>
				</div>
				<div class="col-8" style="text-align: right">
					<div id="modal-editer-semestre-sauvegarde" class="btn btn-success"><i class="fa fa-save"></i> Sauvegarder les modifications</div>
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
				</div>
            </div>

    	</div>
  	</div>
</div>
