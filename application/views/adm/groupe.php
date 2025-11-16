<?
/* ============================================================================
 *
 * Administration d'un groupe
 *
 * ============================================================================ */ ?>

<link href="<?= base_url() . 'assets/css/admin.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/admin.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="admin">
<div class="container-fluid">

<div id="groupe-data" data-groupe_id="<?= $this->groupe['groupe_id']; ?>" class="d-none"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="row">
        <div class="col-8">
            <h3>
                Administration d'un groupe <sup style="color: crimson;">&beta;</sup>
            </h3>
        </div>
        <div class="col-4" style="text-align: right">
            <? if ($this->enseignant['privilege'] >= 90) : ?>
                <a href="<?= base_url() . $sous_dir . '/systeme'; ?>"  class="btn btn-outline-dark">
                    <i class="fa fa-cog" style="margin-right: 5px; color: crimson"></i> 
                    Administration du système
                </a>
            <? endif; ?>
        </div>
    </div>

    <div class="space"></div>
    
    <? if ( ! empty(@$flash_message)) : ?>

        <div class="alert alert-<?= $flash_message['alert']; ?> alert-dismissible fade show mt-4" role="alert" style="margin-bottom: 5px">
            <i class="fa fa-exclamation-circle"></i> <?= @$flash_message['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

    <? endif; ?>

    <div class="hspace"></div>

    <? 
    /* ========================================================================
     *
     *  SOUS-MENU
     *
     * ======================================================================== */ ?>

    <div style="padding: 8px 15px 8px 15px; border: 1px solid dodgerblue; border-bottom: 0; font-size: 1em; background: #f8f9fa;; color: #1565C0;">
        <?= $this->ecole['ecole_nom']; ?> 
        <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i> 
        <?= $this->groupe['groupe_nom']; ?>
    </div>

    <?
        $sous_menu_items = array(
            'alertes'       => 'Alertes',
            'activite'      => 'Activité',
            'enseignants'   => 'Enseignants',
            'evaluations'   => 'Évaluations',
            'soumissions'   => 'Soumissions',
            'consultations' => 'Consultations'
        );
    ?>

    <div id="sous-menu" class="btn-group btn-block" role="group">

        <? foreach ($sous_menu_items as $vue => $desc) : ?>

            <a class="btn btn-sm spinnable <?= $onglet == $vue ? 'active' : ''; ?>" href="<?= base_url() . $sous_dir . '/groupe/' . $vue; ?>" style="width: 121px">
                <?= $desc; ?>
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
            </a>

        <? endforeach; ?>

    </div>

    <div class="tspace"></div>

    <? 
    /* ========================================================================
     *
     *  VUE PRINCIPALE
     *
     * ======================================================================== */ ?>

    <div>
        <? $this->load->view($sous_dir . '/' . $onglet . '_groupe', $this->data); ?>
    </div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-admin -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER LE GROUPE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-groupe" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier ce groupe</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-groupe-form'), 
						array('groupe_id' => $adm_groupe_id)
					); ?>
					
					<div class="space"></div>				

                        </div>

					</div>

					<div class="hspace"></div>				
					<div class="space"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-groupe-nom">Niveau :</label>
                        <input name="groupe_nom" class="form-control" style="width: 100%" placeholder="Le nom du groupe">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>


                    <div class="hspace"></div>

					<div class="form-group col-md-12">
						<label for="modal-editer-groupe-code">Courriel :</label>
						<input name="enseignant_code" class="form-control col-md-8">
                        <small class="form-text text-muted">
                            ex. 202-NYA-05
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-editer-groupe-nom">URL du enseignant :</label>
						<input name="enseignant_url" class="form-control" id="modal-editer-groupe-url">
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
					<div id="modal-editer-groupe-sauvegarde" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Modifier ce groupe</div>
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN SEMESTRE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-semestre" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajouter un semestre</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajouter-semestre-form'), 
                        array('groupe_id' => $adm_groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-ajouter-semestre-nom">Le titre du semestre :</label>
						<input name="semestre_nom" class="form-control" id="modal-ajouter-semestre-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-ajouter-semestre-code">Le code du semestre :</label>
						<input name="semestre_code" class="form-control col-md-2" id="modal-ajouter-semestre-code">
                        <small class="form-text text-muted">
                            ex. H2018
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="modal-ajouter-semestre-debut-date">La date du <strong>début</strong> du semestre :</label>
                            <input name="semestre_debut_date" class="form-control datepicker" id="modal-ajouter-semestre-debut-date">
                            <small class="form-text text-muted">
                                ex. 2018-08-15
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-4 offset-md-1">
                            <label for="modal-ajouter-semestre-fin-date">La date de la <strong>fin</strong> du semestre :</label>
                            <input name="semestre_fin_date" class="form-control datepicker" id="modal-ajouter-semestre-fin-date">
                            <small class="form-text text-muted">
                                ex. 2018-12-31
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>
			
				</form>
				
      		</div>
      
			<div class="modal-footer">
        		<div id="modal-ajouter-semestre-sauvegarde" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Ajouter ce semestre</div>
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
						array('semestre_id' => NULL, 'groupe_id' => $adm_groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-editer-semestre-nom">Le titre du semestre :</label>
						<input name="semestre_nom" class="form-control" id="modal-editer-semestre-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group">
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

                    <div class="form-row">

                        <div class="form-group col-md-4">
                            <label for="modal-editer-semestre-debut-date">La date du <strong>début</strong> du semestre :</label>
                            <input name="semestre_debut_date" class="form-control datepicker" id="modal-editer-semestre-debut-date" value="">
                            <small class="form-text text-muted">
                                ex. 2018-08-15
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-4 offset-md-1">
                            <label for="modal-editer-semestre-fin-date">La date de la <strong>fin</strong> du semestre :</label>
                            <input name="semestre_fin_date" class="form-control datepicker" id="modal-editer-semestre-fin-date" value="">
                            <small class="form-text text-muted">
                                ex. 2018-12-31
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>

					<div id="modal-effacer-semestre-confirmation" class="form-group" style="margin-top: 15px">
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
                <div class="col-md-4" style="padding-left: 5px; text-align: left;">
                    <div id="modal-effacer-semestre-sauvegarde" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-trash"></i> Effacer ce semestre</div>
                </div>
                <div class="col-md-8" style="padding-right: 0; text-align: right">
                    <div id="modal-editer-semestre-sauvegarde" class="btn btn-success"><i class="fa fa-save"></i> Sauvegarder les modifications</div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN COURS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-cours" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajouter un cours</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajouter-cours-form'), 
						array('cours_id' => NULL, 'groupe_id' => $adm_groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-ajouter-cours-nom">Nom officiel du cours : </label>
						<input name="cours_nom" class="form-control" id="modal-ajouter-cours-nom">
                        <small class="form-text text-muted">
                            ex. Chimie générale : la matière
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-ajouter-cours-nom">Nom du cours :</label>
						<input name="cours_nom_court" class="form-control" id="modal-ajouter-cours-nom-court">
                        <small class="form-text text-muted">
                            ex. Chimie générale
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="hspace"></div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="modal-ajouter-cours-code">Code du cours :</label>
                            <input name="cours_code" class="form-control" id="modal-ajouter-cours-code">
                            <small class="form-text text-muted">
                                ex. 202-NYA-05
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-3 offset-md-1">
                            <label for="modal-ajouter-cours-code">Code abrégé du cours :</label>
                            <input name="cours_code_court" class="form-control" id="modal-ajouter-cours-code_court">
                            <small class="form-text text-muted">
                                ex. NYA
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-ajouter-cours-nom">URL du cours :</label>
						<input name="cours_url" class="form-control" id="modal-ajouter-cours-url">
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
					<div id="modal-ajouter-cours-sauvegarde" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Ajouter ce cours</div>
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER (MODIFIER) UN COURS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-cours" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Éditer un cours</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-cours-form'), 
						array('cours_id' => NULL, 'groupe_id' => $adm_groupe_id)
					); ?>

					<div class="form-group">
						<label for="modal-editer-cours-nom">Nom officiel du cours :</label>
						<input name="cours_nom" class="form-control" id="modal-editer-cours-nom">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-editer-cours-nom">Nom du cours :</label>
						<input name="cours_nom_court" class="form-control" id="modal-editer-cours-nom-court">
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="hspace"></div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="modal-editer-cours-code">Code du cours :</label>
                            <input name="cours_code" class="form-control" id="modal-editer-cours-code">
                            <small class="form-text text-muted">
                                ex. 202-NYA-05
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>

                        <div class="form-group col-md-3 offset-md-1">
                            <label for="modal-editer-cours-code-court">Code abrégé du cours :</label>
                            <input name="cours_code_court" class="form-control" id="modal-editer-cours-code-court">
                            <small class="form-text text-muted">
                                ex. NYA
                            </small>
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-editer-cours-nom">URL du cours :</label>
						<input name="cours_url" class="form-control" id="modal-editer-cours-url">
					</div>

					<div class="form-group" style="margin-top: 30px">
						<div class="form-check">
							<input type="checkbox" name="confirmation_effacer_cours" class="form-check-input">
							<label class="form-check-label">
								Pour effacer ce cours, confirmez en cochant.
								<br />(La seule raison valable pour effacer est si un cours vient d'être ajouté par mégarde.)
							</label>
						</div>
				  	</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
				<div class="col-4">
					<div id="modal-effacer-cours-sauvegarde" style="margin-left: 5px" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-trash"></i> Effacer ce cours</div>
				</div>
				<div class="col-8" style="text-align: right">
					<div id="modal-editer-cours-sauvegarde" class="btn btn-success"><i class="fa fa-save"></i> Sauvegarder les modifications</div>
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
				</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajouter-enseignant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un enseignant</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<div style="text-align: center; padding-top: 20px; padding-bottom: 20px">

					<i class="fa fa-exclamation-circle"></i> Veuillez passer par l'inscription pour ajouter un enseignant.

				</div>
				
      		</div>
      
            <div class="modal-footer">
					<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EDITER UN ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editer-enseignant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Éditer un enseignant</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editer-enseignant-form'), 
						array('enseignant_id' => NULL, 'groupe_id' => $adm_groupe_id)
					); ?>
					
					<div class="space"></div>				

					<div class="form-inline">
                        <div class="col-md-6">
							<div style="margin-bottom: 10px">Nom : </div>
                            <input name="nom" class="form-control" style="width: 100%" placeholder="Nom">
                        </div>
                        <div class="col-md-6">
							<div style="margin-bottom: 10px">Prénom : </div>
                            <input name="prenom" class="form-control" style="width: 100%" placeholder="Prénom">
                        </div>
					</div>

					<div class="space"></div>				

					<div class="form-group">
						<div class="col-md-3">
							<label>Genre : </label>
							<select name="genre" class="custom-select">
								<option value="M" selected>M</option>
								<option value="F">F</option>
							</select>
						</div>
					</div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Niveau :</label>
                            <select name="niveau" class="custom-select">
                                <? foreach($this->config->item('niveaux') as $k => $v) : ?>
                                    <? if ($v > $enseignant['niveau']) continue; ?>
                                    <? if ($v == 0) continue; ?>
                                    <option value="<?= $v; ?>"><?= $v; ?>: <?= $this->config->item($v, 'niveaux_desc'); ?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>

					<div class="form-group col-md-12">
						<label for="modal-editer-enseignant-code">Courriel :</label>
                        <input name="courriel" class="form-control col-md-8" <?= ($enseignant['niveau'] >= $this->config->item('niveaux')['sysop']) ? '' : 'disabled'; ?>>
					</div>

                    <? if ($enseignant['niveau'] >= $this->config->item('niveaux')['sysop']) : ?>

						<div class="form-inline" style="margin-top: 30px">
							<div class="col-md-6">
								<div style="margin-bottom: 10px">Mot-de-passe :</div>
								<input name="password" type="password" class="form-control" style="width: 100%" placeholder="Mot-de-passe">
							</div>
							<div class="col-md-6">
								<div style="margin-bottom: 10px">&nbsp;</div>
								<input name="password2" type="password" class="form-control" style="width: 100%" placeholder="Confirmation">
							</div>
						</div>

						<div class="tspace"></div>				

					<? endif; ?>

				</form>
				
      		</div>
      
            <div class="modal-footer">
				<div id="modal-editer-enseignant-sauvegarde" class="btn btn-success"><i class="fa fa-plus-circle"></i> Sauvegarder les changements</div>
				<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

        </div>
    </div>
</div>

