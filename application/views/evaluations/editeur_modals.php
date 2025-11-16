<?
/* -------------------------------------------------------------------------
 *
 * MODALS
 *
 * ------------------------------------------------------------------------- */ ?>	

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: CHANGER LE RESPONSABLE D'UNE EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-changer-responsable" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-user"></i> Changer le responsable de cette évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-changer-responsable-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12" style="padding-top: 20px; padding-bottom: 0px">

					    Qui sera le nouveau responsable de cette évaluation?	

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="enseignant_id" class="custom-select" id="changer-responsable-select">
								<option selected>Choisissez l'enseignant ou l'enseignante...</option>
							</select>
						</div>

					</div>

					<div class="hspace"></div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-changer-responsable-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-user"></i> Changer le responsable
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: DUPLIQUER UNE EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-dupliquer-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-clone"></i> Dupliquer une évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-dupliquer-evaluation-form'), 
                        array('evaluation_id' => NULL, 'cours_id' => $cours['cours_id'])
                    ); 
                ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <span style="color: #777">
                            Cette opération créera une copie conforme de cette évaluation dans vos évaluations.
                        </span>

                        <div class="space"></div>

						Êtes-vous certain de vouloir dupliquer cette évaluation ?
						<br/ ><br />
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i> 
                        Vous serez transporté dans la nouvelle évaluation.
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-dupliquer-evaluation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-clone"></i> Dupliquer cette évaluation
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: COPIER UNE EVALUATION (D'UN COURS A UN AUTRE) (VERSION 2)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-copier-evaluation2" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-copy"></i> Copier une évaluation vers un autre cours</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-copier-evaluation-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <span style="color: #777">
                            Cette opération créera une copie conforme de cette évaluation pour un autre de vos cours.<br />
                            Ceci est pertinent lorsque vous voulez utiliser la même évaluation dans un autre cours.
                        </span>

                        <div class="space"></div>

                        <? if (count($groupes_copy) > 1) : ?>

                            Quel groupe ?

                            <div class="input-group mt-3 mb-4">
                                <select name="groupe_id" class="custom-select" id="copier-evaluation-groupe-id">
                                    <? foreach($groupes as $g_id => $g) : ?>
                                    <option value="<?= $g_id; ?>" <?= ($this->groupe_id == $g_id) ? 'selected' : ''; ?>>
                                        <?= ($g_id != 0 ? $g['ecole_nom'] . ' > ' : '') . $g['groupe_nom'] ?>
                                    </option>
                                    <? endforeach; ?>
                                </select>
                            </div>

                        <? else : ?>

                            Quel groupe ?

                            <div class="hspace"></div>

                            <span class="fa fa-square" style="color: dodgerblue; margin-right: 5px"></span>

                            <? if ($groupes[$groupes_copy[0]]['groupe_id'] != 0) : ?>
                                <span style="font-weight: bold"><?= $groupes[$groupes_copy[0]]['ecole_nom']; ?></span> >
                            <? endif; ?>

                            <span style="font-weight: bold"><?= $groupes[$groupes_copy[0]]['groupe_nom']; ?></span>

                            <input type="hidden" id="copier-evaluation-groupe-id" name="groupe_id" value="<?= $groupes_copy[0]; ?>" />

                            <div class="tspace"></div>

                        <? endif; ?>

                        Vers quel cours voulez-vous copier cette évaluation ?
    
                        <div class="input-group mt-3 mb-2">

                            <select name="cours_id" class="custom-select" id="copier-evaluation-cours-id">
                                <?  $i=0;
                                    foreach($cours_tous as $c_id => $c) : 
                                        if ($c['groupe_id'] != $groupes_copy[0]) continue;
                                        if ($c_id == $cours['cours_id']) continue;
                                        $i++;
                                ?>
                                    <option value="<?= $c_id; ?>" <?= $i == 1 ? 'selected' :''; ?>>
                                    <?= $c['cours_nom']; ?> (<?= $c['cours_code_court']; ?>)
                                    </option>
                                <? endforeach; ?>
                            </select>
                        </div>

					</div>

					<div class="hspace"></div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-copier-evaluation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-clone"></i> Copier
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: DUPLIQUER UNE QUESTION ET SES REPONSES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-dupliquer-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-clone"></i> Dupliquer une question</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-dupliquer-question-form'), 
						array('question_id' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Êtes-vous certain de vouloir dupliquer cette question ?
						<br/ ><br />
                        <i class="fa fa-exclamation-circle" style="color: darkorange"></i> 
                        La question et ses réponses seront dupliquées dans la même évaluation.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-dupliquer-question-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-clone"></i> Dupliquer cette question
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: IMPORTER UNE QUESTION VERS UNE EVALUATION PRIVEE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-importer-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-left"></i> Importer cette question vers une de vos évaluations</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-admin-importer-question-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'], 'question_id' => NULL)
					); ?>

					<div id="modal-importer-question-cours" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

						Quel est le cours de l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="cours_id" class="custom-select" id="modal-importer-question-cours-select">
								<option selected>Choisissez le cours...</option>
							</select>
                        </div>
    
                    </div>

					<div id="modal-importer-question-evaluation" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">
                        
						Quelle est l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="evaluation_id" class="custom-select" id="modal-importer-question-evaluations-select">
								<option selected>Choisissez l'évaluation...</option>
							</select>
						</div>

					</div>

                    <div class="form-group col-md-12">
                        <small class="form-text text-muted" style="padding-top: 10px">
                            ATTENTION :<br/ >
                            La question, ainsi que toute ses variables, seront importées dans cette évaluation.<br />
                            Si une variable identique existe déjà dans l'évaluation cible, il y a aura échec de la procédure.<br />
                            Le bloc associé ne sera pas importé.
                        </small>
                    </div>

                    <? // Afficher un message d'erreur, si la procedure echoue ?>

                    <div id="modal-importer-question-erreur" class="form-group col-md-12 d-none" style="padding-top: 10px; color: crimson">

                        <div class="erreur-titre">
                            <i class="fa fa-exclamation-circle"></i> Erreur <span class="erreur-code"></span> :
                        </div>

                        <div class="erreur-message"></div>
                        <div class="erreur-solution"></div>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-importer-question-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-left"></i> Importer 
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EXPORTER UNE QUESTION VERS UNE EVALUATION DU DEPARTEMENT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-exporter-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-right"></i> Exporter une question vers une évaluation du département</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-admin-exporter-question-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'], 'question_id' => NULL)
					); ?>

					<div id="modal-exporter-question-cours" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

						Quel est le cours de l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="cours_id" class="custom-select" id="modal-exporter-question-cours-select">
								<option selected>Choisissez le cours...</option>
							</select>
                        </div>
    
                    </div>

					<div id="modal-exporter-question-evaluation" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">
                        
						Quelle est l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="evaluation_id" class="custom-select" id="modal-exporter-question-evaluations-select">
								<option selected>Choisissez l'évaluation...</option>
							</select>
						</div>

					</div>

                    <div class="form-group col-md-12">
                        <small class="form-text text-muted" style="padding-top: 10px">
                            ATTENTION :<br/ >
                            La question, ainsi que toute ses variables, seront exportées dans cette évaluation.<br />
                            Si une variable identique existe déjà dans l'évaluation cible, il y a aura échec de la procédure.<br />
                            Le bloc associé ne sera pas exporté.
                        </small>
                    </div>

                    <? // Afficher un message d'erreur, si la procedure echoue ?>

                    <div id="modal-exporter-question-erreur" class="form-group col-md-12 d-none" style="padding-top: 10px; color: crimson">

                        <div class="erreur-titre">
                            <i class="fa fa-exclamation-circle"></i> Erreur <span class="erreur-code"></span> :
                        </div>

                        <div class="erreur-message"></div>
                        <div class="erreur-solution"></div>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-exporter-question-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-right"></i> Exporter cette question
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: COPIER UNE QUESTION DANS UNE AUTRE EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-copier-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"><i class="fa fa-copy"></i> Copier une question vers une autre évaluation</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-admin-copier-evaluation-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'], 'question_id' => NULL)
					); ?>

                    <div class="form-group col-md-12" style="padding-top: 10px">

                        <i class="fa fa-exclamation-circle" style="color: #bbb"></i>
                        <? if ($evaluation['public']) : ?>
                            L'évaluation cible est une évaluation du département.
                        <? else : ?>
                            L'évaluation cible est une de vos évaluations personnelles.
                        <? endif; ?>

                    </div>

					<div id="modal-copier-question-cours" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

						Quel est le cours de l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="cours_id" class="custom-select" id="modal-copier-question-cours-select">
								<option selected>Choisissez le cours...</option>
							</select>
                        </div>
    
                    </div>

					<div id="modal-copier-question-evaluation" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">
                        
						Quelle est l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="evaluation_id" class="custom-select" id="modal-copier-question-evaluations-select">
								<option selected>Choisissez l'évaluation...</option>
							</select>
						</div>

					</div>

                    <div class="form-group col-md-12">
                        <small class="form-text text-muted" style="padding-top: 10px">
                            ATTENTION :<br/ >
                            La question, ainsi que toute ses variables, seront copiées dans cette évaluation.<br />
                            Si une variable identique existe déjà dans l'évaluation cible, il y a aura échec de la procédure.<br />
                            Le bloc associé ne sera pas copié.
                        </small>
                    </div>

                    <? // Afficher un message d'erreur, si la procedure echoue ?>

                    <div id="modal-copier-question-erreur" class="form-group col-md-12 d-none" style="padding-top: 10px; color: crimson">

                        <div class="erreur-titre">
                            <i class="fa fa-exclamation-circle"></i> Erreur <span class="erreur-code"></span> :
                        </div>

                        <div class="erreur-message"></div>
                        <div class="erreur-solution"></div>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-copier-question-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-copy"></i> Copier 
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-evaluation" class="modal" tabindex="-1" role="dialog">
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
						array('id' => 'modal-effacer-evaluation-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 20px; padding-bottom: 10px">

						Êtes-vous certain de vouloir effacer cette évaluation ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-evaluation-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer cette évaluation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: IMPORTER L'EVALUATION DANS MES EVALUATIONS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-importer-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-left"></i> Importer cette évaluation dans ses évaluations</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-importer-evaluation-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Importer cette évaluation dans vos évaluations?

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-importer-evaluation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-left"></i> Importer
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EXPORTER L'EVALUATION DANS LES EVALUATIONS DU DEPARTEMENT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-exporter-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-right"></i> Exporter une évaluation vers les évaluations du département</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-exporter-evaluation-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Exporter cette évaluation dans les évaluations du département?

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-exporter-evaluation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-right"></i> Exporter cette évaluation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE VARIABLE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-variable" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter une nouvelle variable</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-variable-form'), 
						array('evaluation_id' => $evaluation_id)
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="form-group col-md-5">
						<label for="modal-variable-nom">Étiquette de la variable :</label>
						<select class="form-control" name="variable_nom" id="modal-variable-nom">
                        <? for($char = ord('A'); $char <= ord('Z'); ++$char) : ?>
                              <? if (array_key_exists(chr($char), $variables_presentes)) continue; ?>
                              <? if ($char == ord('E')) continue; ?>
							  <option><?= chr($char); ?></option>
						<? endfor; ?>
						</select>
					</div>

					<div class="hspace"></div>				

					<div style="margin-left: 15px; padding-bottom: 10px; color: #777">
						<i class="fa fa-info-circle"></i> Une valeur aléatoire sera choisie du minimum au maximum spécifié.
					</div>

					<div class="hspace"></div>				

                    <div class="form-inline">

                        <div class="form-group col-md-4">
                            <label for="modal-variable-minimum">Valeur minimum</label>
                            <input type="number" class="form-control mt-2" name="variable_minimum" id="modal-variable-minimum" value="1">
                        </div>

                        <div class="ml-5 form-group col-md-4">
                            <label for="modal-variable-maximum">Valeur maximum</label>
                            <input type="number" class="form-control mt-2" name="variable_maximum" id="modal-variable-maximum" value="999">
                        </div>

					</div> <!-- .form-inline -->

					<div class="space"></div>				

					<div class="form-group col-md-5">
						<label for="modal-variable-decimals">Décimale(s)</label>
						<input type="number" class="form-control col-md-4" name="variable_decimals" id="modal-variable-decimales" value="0">
                    </div>

					<div class="qspace"></div>				

                    <div class="form-inline">

                        <div class="form-group col-md-4">
                            <label for="modal-variable-ns">
                                Notation scientifique
                                <i class="fa fa-info-circle" style="margin-left: 5px" 
                                   data-toggle="tooltip" 
                                   data-title="Si oui, la valeur sera affichée en notation scientifique dans le texte de la question."></i>
                            </label>
                            <select class="form-control mt-2" id="modal-variable-ns" name="variable_ns" style="width: 125px">
                                <option value="0">non</option>
                                <option value="1">oui</option>
                            </select>
                        </div>

                        <div class="form-group ml-5 col-md-4">

                            <label for="modal-variable-cs">
                                Chiffres significatifs
                                <i class="fa fa-info-circle" style="margin-left: 5px" 
                                   data-toggle="tooltip" 
                                   data-title="Le nombre de chiffres significatifs qui sera conservé dans le texte de la question."></i>
                            </label>
                            <select class="form-control mt-2" id="modal-variable-cs" name="variable_cs" style="width: 125px">
                                <option value="0">auto</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>

                        </div>

					</div> <!-- .form-inline -->

                    <div class="space"></div>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Description :</label>
                        <input name="variable_desc" class="form-control" />
						<div class="invalid-feedback">
							Ce champ est requis.
                        </div>
                    </div>

				</form>
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-variable-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter cette variable
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE VARIABLE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-variable" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier la variable <var><span id="modal-modifier-variable-nom"></span></var></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, array('id' => 'modal-modifier-variable-form'),
                        array(        
                            'evaluation_id' => $evaluation_id,
                            'variable_id'   => NULL,
                            'variable_nom'  => NULL
                        ));
            ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

                    <div class="hspace"></div>				

                    <div class="form-inline">
                        <div class="form-group col-md-3">
                            <label for="modal-variable-minimum">Valeur minimum</label>
                            <input type="number" class="form-control" name="minimum" id="modal-variable-minimum" value="1">
                        </div>

                        <div class="form-group col-md-3 ml-5">
                            <label for="modal-variable-maximum">Valeur maximum</label>
                            <input type="number" class="form-control" name="maximum" id="modal-variable-maximum" value="999">
                        </div>
                    </div>

					<div class="space"></div>				

					<div class="form-group col-md-4">
						<label for="modal-variable-decimals">Décimale(s)</label>
						<input type="number" class="form-control" name="decimales" id="modal-variable-decimals" value="0">
					</div>

                    <div class="hspace"></div>				

                    <div class="form-inline">

                        <div class="form-group col-md-4">
                            <label for="modal-variable-ns">
                                Notation scientifique
                                <i class="fa fa-info-circle" style="margin-left: 5px" 
                                   data-toggle="tooltip" 
                                   data-title="Si oui, la valeur sera affichée en notation scientifique dans le texte de la question."></i>
                            </label>
                            <select class="form-control mt-2" id="modal-variable-ns" name="ns" style="width: 125px">
                                <option value="0">non</option>
                                <option value="1">oui</option>
                            </select>
                        </div>

                        <div class="form-group ml-5 col-md-4">

                            <label for="modal-variable-cs">
                                Chiffres significatifs
                                <i class="fa fa-info-circle" style="margin-left: 5px" 
                                   data-toggle="tooltip" 
                                   data-title="Le nombre de chiffres significatifs qui sera conservé dans le texte de la question."></i>
                            </label>
                            <select class="form-control mt-2" id="modal-variable-cs" name="cs" style="width: 125px">
                                <option value="0">auto</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>

                        </div>

					</div> <!-- .form-inline -->

                    <div class="space"></div>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Description :</label>
                        <input name="variable_desc" class="form-control" />
						<div class="invalid-feedback">
							Ce champ est requis.
                        </div>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-check" style="margin-left: 15px; margin-bottom: 10px">
                        <input type="checkbox" name="variable_effacement" id="modal-variable-effacement" class="form-check-input" />
						<label for="modal-variable-effacement" style="margin-left: 5px">Pour effacer cette variable, confirmez en cochant.</label>
					</div>

				</form>
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-modifier-variable-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer cette variable
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-variable-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-edit"></i> Modifier cette variable
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN BLOC
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-bloc" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un nouveau bloc</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
                        array('id' => 'modal-ajout-bloc-form', 'style' => 'padding: 15px'), 
						array('evaluation_id' => $evaluation_id)
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="form-group">
						<label for="modal-bloc-label">Étiquette du bloc :</label>
						<select class="form-control col-2" name="bloc_label" id="modal-bloc-label">
                        <? for($char = ord('A'); $char <= ord('Z'); ++$char) : ?>
                              <? if (in_array(chr($char), $bloc_labels)) continue; ?>
							  <option><?= chr($char); ?></option>
						<? endfor; ?>
						</select>
					</div>

					<div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-bloc_points">Point(s) alloué(s) pour chaque question : </label>
						<div class="input-group">
						    <input type="number" class="form-control col-2" name="bloc_points" id="modal-bloc_points" value="1">
       	 					<div class="input-group-append">
          						<div class="input-group-text">point(s)</div>
        					</div>
							<div class="invalid-feedback">
								Ce champ est requis.
                            </div>
                        </div>
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Toutes les questions doivent avoir le même nombre de points.
                        </small>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-bloc_nb_questions">Nombre de questions à choisir : </label>
                        <input type="number" class="form-control col-2" name="bloc_nb_questions" id="modal-bloc_nb-questions" value="0" disabled>
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Vous pourrez changer cette valeur après avoir ajouté des questions à ce bloc.
                        </small>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-question-titre">Description :</label>
                        <input name="bloc_desc" class="form-control" />
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-bloc-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter ce bloc
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UN BLOC
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-bloc" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier le bloc <span class="bloc-label-text-sm" id="modal-modifier-bloc-label">A</span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, array('id' => 'modal-modifier-bloc-form', 'style' => 'padding: 15px'),
                    array(        
                        'evaluation_id' => $evaluation_id,
                        'bloc_id'       => NULL,
                        'bloc_label'    => NULL
                    )
                );
            ?>
                    <div class="alert alert-danger d-none" role="alert">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="form-group">
						<label for="modal-variable-nom">Étiquette du bloc :</label>
						<select class="form-control col-2" name="bloc_label" id="modal-bloc-label-modification">
                        <? for($char = ord('A'); $char <= ord('Z'); ++$char) : ?>
                              <option value="<?= chr($char); ?>"><?= chr($char); ?></option>
						<? endfor; ?>
						</select>
					</div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-bloc-points">Point(s) alloué(s) pour chaque question : </label>
						<div class="input-group">
						    <input type="number" class="form-control col-2" name="bloc_points" id="modal-bloc-points" value="1">
       	 					<div class="input-group-append">
          						<div class="input-group-text">point(s)</div>
        					</div>
							<div class="invalid-feedback">
								Ce champ est requis.
                            </div>
                        </div>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-bloc-nb-questions">Nombre de question(s) à choisir : </label>
                        <input type="number" class="form-control col-2" name="bloc_nb_questions" id="modal-bloc-nb-questions" value="0">
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Ce nombre ne peut excéder le nombre de questions que contient ce bloc.
                        </small>
                    </div>

                    <div class="hspace"></div>				

					<div class="form-group">
						<label for="modal-bloc-desc">Description :</label>
                        <input type="text" name="bloc_desc" id="modal-bloc-desc" class="form-control" />
					</div>

                    <div class="hspace"></div>				

					<div class="form-check">
                        <input type="checkbox" name="bloc_effacement" id="modal-bloc-effacement" class="form-check-input" />
						<label for="modal-bloc-effacement" style="margin-left: 5px">Pour effacer ce bloc, confirmez en cochant.</label>
					</div>
					<div class="form-check">
                        <input type="checkbox" name="bloc_effacement_questions" id="modal-bloc-effacement-questions" class="form-check-input" />
						<label for="modal-bloc-effacement-questions" style="margin-left: 5px">Pour effacer ce bloc et toutes ses questions, confirmez en cochant.</label>
					</div>
                    
				</form>
      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-modifier-bloc-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer ce bloc
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-bloc-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-edit"></i> Modifier ce bloc
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div> <!-- .modal-footer -->

        </div> <!-- .modal-content -->

  	</div> <!-- .modal-dialog -->
</div> <!-- #modal-modifier-bloc -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: IMPORTER UN BLOC (ET SES QUESTIONS) VERS UNE EVALUATION DE VOS ÉVALUTIONS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-importer-bloc" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-left"></i> Importer un bloc vers une de vos évaluations</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-admin-importer-bloc-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'], 'bloc_id' => NULL)
					); ?>

					<div id="modal-importer-bloc-cours" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

						Quel est le cours de l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="cours_id" class="custom-select" id="modal-importer-bloc-cours-select">
								<option selected>Choisissez le cours...</option>
							</select>
                        </div>
    
                    </div>

					<div id="modal-importer-bloc-evaluation" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">
                        
						Quelle est l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="evaluation_id" class="custom-select" id="modal-importer-bloc-evaluations-select">
								<option selected>Choisissez l'évaluation...</option>
							</select>
						</div>

					</div>

                    <div class="form-group col-md-12">
                        <small class="form-text text-muted" style="padding-top: 10px">
                            ATTENTION :<br/ >
                            Le bloc ainsi que toutes ses questions et ses variables, seront importés dans l'évaluation cible.<br />
                            Si un bloc de même étiquette ou une variable identique existe déjà dans l'évaluation cible, il y a aura échec de la procédure.<br />
                        </small>
                    </div>

                    <? // Afficher un message d'erreur, si la procedure echoue ?>

                    <div id="modal-importer-bloc-erreur" class="form-group col-md-12 d-none" style="padding-top: 10px; color: crimson">

                        <div class="erreur-titre">
                            <i class="fa fa-exclamation-circle"></i> Erreur <span class="erreur-code"></span> :
                        </div>

                        <div class="erreur-message"></div>
                        <div class="erreur-solution"></div>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-importer-bloc-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-right"></i> Importer ce bloc
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: COPIER UN BLOC (ET SES QUESTIONS) VERS UNE EVALUATION DE MEME TYPE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-copier-bloc" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-right"></i> Copier un bloc vers une autre évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-admin-copier-bloc-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'], 'bloc_id' => NULL)
					); ?>

					<div id="modal-copier-bloc-cours" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

						Quel est le cours de l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="cours_id" class="custom-select" id="modal-copier-bloc-cours-select">
								<option selected>Choisissez le cours...</option>
							</select>
                        </div>
    
                    </div>

					<div id="modal-copier-bloc-evaluation" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">
                        
						Quelle est l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="evaluation_id" class="custom-select" id="modal-copier-bloc-evaluations-select">
								<option selected>Choisissez l'évaluation...</option>
							</select>
						</div>

					</div>

                    <div class="form-group col-md-12">
                        <small class="form-text text-muted" style="padding-top: 10px">
                            ATTENTION :<br/ >
                            Le bloc ainsi que toutes ses questions et ses variables, seront exportés dans l'évaluation cible.<br />
                            Si un bloc de même étiquette ou une variable identique existe déjà dans l'évaluation cible, il y a aura échec de l'opération.<br />
                        </small>
                    </div>

                    <? // Afficher un message d'erreur, si la procedure echoue ?>

                    <div id="modal-copier-bloc-erreur" class="form-group col-md-12 d-none" style="padding-top: 10px; color: crimson">

                        <div class="erreur-titre">
                            <i class="fa fa-exclamation-circle"></i> Erreur <span class="erreur-code"></span> :
                        </div>

                        <div class="erreur-message"></div>
                        <div class="erreur-solution"></div>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-copier-bloc-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-right"></i> Copier ce bloc
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EXPORTER UN BLOC (ET SES QUESTIONS) VERS UNE EVALUATION DU DEPARTEMENT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-exporter-bloc" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-arrow-circle-right"></i> Exporter un bloc vers une évaluation du département</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-admin-exporter-bloc-form'), 
                        array('evaluation_id' => $evaluation['evaluation_id'], 'bloc_id' => NULL)
					); ?>

					<div id="modal-exporter-bloc-cours" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

						Quel est le cours de l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="cours_id" class="custom-select" id="modal-exporter-bloc-cours-select">
								<option selected>Choisissez le cours...</option>
							</select>
                        </div>
    
                    </div>

					<div id="modal-exporter-bloc-evaluation" class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">
                        
						Quelle est l'évaluation cible ?

						<div class="input-group mb-3" style="margin-top: 20px;">

					  		<select name="evaluation_id" class="custom-select" id="modal-exporter-bloc-evaluations-select">
								<option selected>Choisissez l'évaluation...</option>
							</select>
						</div>

					</div>

                    <div class="form-group col-md-12">
                        <small class="form-text text-muted" style="padding-top: 10px">
                            ATTENTION :<br/ >
                            Le bloc ainsi que toutes ses questions et ses variables, seront exportés dans l'évaluation cible.<br />
                            Si un bloc de même étiquette ou une variable identique existe déjà dans l'évaluation cible, il y a aura échec de la procédure.<br />
                        </small>
                    </div>

                    <? // Afficher un message d'erreur, si la procedure echoue ?>

                    <div id="modal-exporter-bloc-erreur" class="form-group col-md-12 d-none" style="padding-top: 10px; color: crimson">

                        <div class="erreur-titre">
                            <i class="fa fa-exclamation-circle"></i> Erreur <span class="erreur-code"></span> :
                        </div>

                        <div class="erreur-message"></div>
                        <div class="erreur-solution"></div>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div id="modal-exporter-bloc-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-arrow-circle-right"></i> Exporter ce bloc
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: ASSIGNER A UN BLOC
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-assigner-bloc" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Assigner cette question à un bloc</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
                        array('id' => 'modal-assigner-bloc-form', 'style' => 'padding: 15px'), 
                        array(
                            'question_id' => NULL,
                            'evaluation_id' => $evaluation_id
                        )
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="form-group">
						<label for="modal-bloc-id">Choisissez le bloc :</label>
                        <select class="form-control" name="bloc_id" id="modal-bloc-id">
                            <? foreach($blocs as $bloc) : ?>
                                <option value="<?= $bloc['bloc_id']; ?>">
                                    <?= $bloc['bloc_label']; ?> <?= ( ! empty($bloc['bloc_desc']) ? ': ' . $bloc['bloc_desc'] : ''); ?>
                                </option>
						    <? endforeach; ?>
						</select>
					</div>

				</form>
      		</div>
      
			<div class="modal-footer">
                <div id="modal-desassigner-bloc-sauvegarde" class="btn btn-outline-danger spinnable">
                    <i class="fa fa-trash"></i> Désassigner de ce bloc
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div id="modal-assigner-bloc-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-plus-circle"></i> Assigner à ce bloc
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE QUESTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter une question</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-question-form'), 
						array('evaluation_id' => $evaluation_id)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Titre :</label>
						<textarea name="question_texte" class="form-control" id="modal-ajout-question-texte" rows="5"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

                    <div class="col-md-12 d-none d-lg-block" style="margin-top: -10px; text-align: center">
                        <div class="btn-group">
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="±">±</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sup>" data-symbol_end="</sup>" data-symbol="10<?= htmlentities('<sup>x</sup>'); ?>">10<sup>x</sup></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sub>" data-symbol_end="</sub>" data-symbol="N<?= htmlentities('<sub>2</sub>'); ?>">N<sub>x</sub></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<strong>" data-symbol_end="</strong>" data-symbol="<?= htmlentities('<strong>abc</strong>'); ?>"><strong>abc</strong></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<var>" data-symbol_end="</var>" data-symbol="<?= htmlentities('<var>A</var>'); ?>"><span style="color: crimson; font-weight: bold">A</span></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⟶'); ?>">⟶</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⇌'); ?>">⇌</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↔'); ?>">↔</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↑'); ?>">↑</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('×'); ?>">×</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('÷'); ?>">÷</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('π'); ?>">π</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('μ'); ?>">μ</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∞'); ?>">∞</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∈'); ?>">∈</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('½'); ?>">½</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('€'); ?>">€</div>
                        </div>
                        <div class="space"></div>				
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-type">Type de question :</label>
                        <select name="question_type" class="form-control" id="modal-ajout-question-type">
                            <? foreach(questions_types() as $q) : ?>
                                <option value="<?= $q['type']; ?>"><?= $q['desc']; ?></option>
                            <? endforeach; ?>
						</select>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Point(s) alloué(s) à cette question :</label>
						<div class="input-group">
        					<input name="question_points" type="text" class="form-control col-md-2 ax-points" id="modal-ajout-question-points" placeholder="" value="">
       	 					<div class="input-group-append">
          						<div class="input-group-text">point(s)</div>
        					</div>
							<div class="invalid-feedback">
								Ce champ est requis.
							</div>
      					</div>
					</div>
			
				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-question-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter cette question
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER LE TITRE DE L'EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-titre" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier le titre de l'évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-titre-form'), 
						array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-evaluation-titre" style="font-weight: bold">Titre de l'évaluation :</label>
						<div class="qspace"></div>
						<textarea name="evaluation_titre" class="form-control" id="modal-modifier-titre-evaluation-titre" rows="3"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>

						<div class="hspace"></div>

						<div style="color: #777">
							<i class="fa fa-exclamation-circle"></i> Maximum de 150 caractères
						</div>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-modifier-titre-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Sauvegarder les changements
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER / MODIFIER LES INSTRUCTIONS
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-instructions" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier les instructions</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-instructions-form'), 
						array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-evaluation-instructions" style="font-weight: bold">Instructions :</label>
						<div class="qspace"></div>
						<textarea name="instructions" class="form-control" id="modal-modifier-instructions-input" rows="5"><?= _html_edit($evaluation['instructions']); ?></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-modifier-instructions-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer les instructions
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-instructions-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-edit"></i> Modifier les instructions
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER / MODIFIER LA DESCRIPTION DE L'EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-description" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier la description</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-description-form'), 
						array('evaluation_id' => $evaluation['evaluation_id'])
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-evaluation-description" style="font-weight: bold">Description :</label>
						<div class="qspace"></div>
                        <textarea name="description" class="form-control" id="modal-modifier-description-input" rows="3"><?= _html_edit($evaluation['evaluation_desc']); ?></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-modifier-description-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer la description
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-description-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-edit"></i> Modifier la description
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE QUESTION (EDITEUR DE QUESTION)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-editeur-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier une question et ses options</h5>
				<button type="button" class="close" data-dismiss="modal" ria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-editeur-question-form'), 
						array('question_id' => NULL)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Titre :</label>
						<textarea name="question_texte" class="form-control" id="modal-editeur-question-texte" rows="5"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="col-md-12 d-none d-lg-block" style="margin-top: -10px; text-align: center">
                        <div class="btn-group">
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="±">±</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sup>" data-symbol_end="</sup>" data-symbol="10<?= htmlentities('<sup>x</sup>'); ?>">10<sup>x</sup></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sub>" data-symbol_end="</sub>" data-symbol="N<?= htmlentities('<sub>2</sub>'); ?>">N<sub>x</sub></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<strong>" data-symbol_end="</strong>" data-symbol="<?= htmlentities('<strong>abc</strong>'); ?>"><strong>abc</strong></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<var>" data-symbol_end="</var>" data-symbol="<?= htmlentities('<var>A</var>'); ?>"><span style="color: crimson; font-weight: bold">A</span></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⟶'); ?>">⟶</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⇌'); ?>">⇌</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↔'); ?>">↔</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↑'); ?>">↑</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('×'); ?>">×</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('÷'); ?>">÷</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('π'); ?>">π</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('μ'); ?>">μ</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∞'); ?>">∞</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∈'); ?>">∈</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('½'); ?>">½</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('€'); ?>">€</div>
                        </div>
					    <div class="space"></div>				
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-type">Type de question :</label>
						<select name="question_type" class="form-control" id="modal-editeur-question-type">
                            <? foreach(questions_types() as $q) : ?>
                                <option value="<?= $q['type']; ?>"><?= $q['desc']; ?></option>
                            <? endforeach; ?>
						</select>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Point(s) alloué(s) à cette question :</label>
						<div class="input-group">
        					<input name="question_points" type="text" class="form-control col-md-2 ax-points" id="modal-editeur-question-points" placeholder="" value="">
       	 					<div class="input-group-append">
          						<div class="input-group-text">point(s)</div>
        					</div>
							<div class="invalid-feedback">
								Ce champ est requis.
							</div>
      					</div>
					</div>
			
				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-editeur-question-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-edit"></i> Modifier cette question
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE REPONSE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-reponse" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter une réponse</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-reponse-form'), 
						array('question_id' => NULL, 'question_type' => NULL, 'equation' => 0)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Réponse :</label>
						<textarea name="reponse_texte" class="form-control" id="modal-ajout-reponse-texte"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="col-md-12 d-none d-lg-block" style="margin-top: -10px; text-align: center">
                        <div class="btn-group">
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="±">±</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sup>" data-symbol_end="</sup>" data-symbol="10<?= htmlentities('<sup>x</sup>'); ?>">10<sup>x</sup></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sub>" data-symbol_end="</sub>" data-symbol="N<?= htmlentities('<sub>2</sub>'); ?>">N<sub>x</sub></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<strong>" data-symbol_end="</strong>" data-symbol="<?= htmlentities('<strong>abc</strong>'); ?>"><strong>abc</strong></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('<symbol>long-arrow-right</symbol>'); ?>"><i class="fa fa-long-arrow-right"></i></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⟶'); ?>">⟶</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⇌'); ?>">⇌</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↔'); ?>">↔</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↑'); ?>">↑</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('×'); ?>">×</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('÷'); ?>">÷</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('π'); ?>">π</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('μ'); ?>">μ</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∞'); ?>">∞</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∈'); ?>">∈</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('½'); ?>">½</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('€'); ?>">€</div>
                        </div>
                        <div class="space"></div>				
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-reponse-type">Quel type de réponse ?</label>
						<select name="reponse_type" class="form-control" id="modal-ajout-reponse-type">
      						<option value="1">Réponse correcte</option>
							<option value="2">Réponse erronée</option>
						</select>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-reponse-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Ajouter cette réponse
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE REPONSE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-reponse" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier une réponse</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-reponse-form'), 
						array('question_id' => NULL, 'reponse_id' => NULL, 'equation' => 0)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Réponse :</label>
                        <textarea name="reponse_texte" class="form-control question-reponse" rows="3"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <div class="col-md-12 d-none d-lg-block" style="margin-top: -10px; text-align: center">
                        <div class="btn-group">
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="±">±</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sup>" data-symbol_end="</sup>" data-symbol="10<?= htmlentities('<sup>x</sup>'); ?>">10<sup>x</sup></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<sub>" data-symbol_end="</sub>" data-symbol="N<?= htmlentities('<sub>2</sub>'); ?>">N<sub>x</sub></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol_start="<strong>" data-symbol_end="</strong>" data-symbol="<?= htmlentities('<strong>abc</strong>'); ?>"><strong>abc</strong></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('<symbol>long-arrow-right</symbol>'); ?>"><i class="fa fa-long-arrow-right"></i></div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⟶'); ?>">⟶</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('⇌'); ?>">⇌</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↔'); ?>">↔</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('↑'); ?>">↑</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('×'); ?>">×</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('÷'); ?>">÷</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('π'); ?>">π</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('μ'); ?>">μ</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∞'); ?>">∞</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('∈'); ?>">∈</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('½'); ?>">½</div>
                            <div class="special-symbol btn btn-outline-light btn-sm" style="width: 42px; color: #777; border-color: #ccc" data-symbol="<?= htmlentities('€'); ?>">€</div>
                        </div>
                        <div class="space"></div>				
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-modifier-reponse-type">Quel type de réponse ?</label>
						<select name="reponse_correcte" class="form-control col-md-6" id="modal-modifier-reponse-type">
							<option value="0">Réponse erronée</option>
      						<option value="1">Réponse correcte</option>
						</select>
					</div>

				</form>
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-modifier-reponse-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer cette réponse
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-reponse-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Modifier cette réponse
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: DEFINIR UNE REPONSE CORRECTE (REPONSE NUMERIQUE ENTIERE)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-reponse-numerique-entiere" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Définir une réponse correcte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-reponse-numerique-entiere-form'), 
						array('question_id' => NULL, 'question_type' => 5, 'reponse_type' => 1, 'equation' => 0)
                    ); ?>

                    <div class="hspace"></div>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Réponse correcte :</label>
                        <div class="form-inline">
                            <input type="number" name="reponse_texte" class="form-control question-reponse-numerique-entiere col-md-2" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
                        </div>
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Doit être un nombre entier, positif ou négatif, sans virgule ni point.
                        </small>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Les unités :</label>
                        <input name="unites" class="form-control col-md-5" />
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Si les unités sont spécifiées, elles apparaîtront à côté de la case de réponse.
                        </small>
                    </div>

					<div class="hspace"></div>				

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-reponse-numerique-entiere-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Définir cette réponse correcte
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE REPONSE NUMERIQUE ENTIERE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-reponse-numerique-entiere" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier une réponse correcte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-reponse-numerique-entiere-form'), 
						array('question_id' => NULL, 'reponse_id' => NULL, 'reponse_type' => 1, 'reponse_correcte' => 1, 'question_type' => 5)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">La réponse correcte :</label>
                        <div class="form-inline">
                            <input type="number" name="reponse_texte" class="form-control col-md-2" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Doit être un nombre entier, positif ou négatif, sans virgule ni point.
                        </small>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Les unités :</label>
                        <input name="unites" class="form-control col-md-5" />
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Si les unités sont spécifiées, elles apparaîtront à côté de la case de réponse.
                        </small>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col-8" style="text-align: right; margin-right: -5px;">
                    <div id="modal-modifier-reponse-numerique-entiere-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Modifier cette réponse
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>

            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: DEFINIR UNE REPONSE CORRECTE (REPONSE NUMERIQUE)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-reponse-numerique" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Définir une réponse correcte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-reponse-numerique-form'), 
						array('question_id' => NULL, 'question_type' => 6, 'reponse_type' => 1, 'equation' => 0)
                    ); ?>

                    <div class="hspace"></div>

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Réponse correcte :</label>
                        <div class="form-inline">
                            <input type="text" name="reponse_texte" class="form-control question-reponse-numerique col-md-2" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
                        </div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Les unités :</label>
                        <input name="unites" class="form-control col-md-5" />
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Si les unités sont spécifiées, elles apparaîtront à côté de la case de réponse.
                        </small>
                    </div>

					<div class="hspace"></div>				

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-reponse-numerique-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Définir cette réponse correcte
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE REPONSE NUMERIQUE ENTIERE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-reponse-numerique" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier une réponse correcte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-reponse-numerique-form'), 
						array('question_id' => NULL, 'reponse_id' => NULL, 'reponse_type' => 1, 'reponse_correcte' => 1, 'question_type' => 6)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">La réponse correcte :</label>
                        <div class="form-inline">
                            <input type="text" name="reponse_texte" class="form-control col-md-2" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Les unités :</label>
                        <input name="unites" class="form-control col-md-5" />
                        <small>
                            <i class="fa fa-exclamation-circle" style="color: #aaa"></i> Si les unités sont spécifiées, elles apparaîtront à côté de la case de réponse.
                        </small>
                    </div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col-8" style="text-align: right; margin-right: -5px;">
                    <div id="modal-modifier-reponse-numerique-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Modifier cette réponse
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>

            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE TOLERANCE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-tolerance" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter une nouvelle tolérance</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-tolerance-form'), 
						array('question_id' => NULL)
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

                    <div class="hspace"></div>

                    <?
                    /* ------------------------------------------------
                     *
                     * Explications
                     *
                     * ------------------------------------------------ */ ?>

                    <div class="col-md-12" style="color: #777">
                        Les tolérances permettent d'accorder un certain nombre de points même si la réponse entrée par l'étudiant est plus ou moins éloignée de la réponse correcte.
                    </div>

                    <div class="space"></div>

                    <?
                    /* ------------------------------------------------
                     *
                     * Type de tolerance
                     *
                     * ------------------------------------------------ */ ?>

					<div class="form-group col-md-3">
						<label for="modal-tolerance-type">Type de tolérance :</label>
						<select class="form-control" name="type" id="modal-tolerance-type">
							  <option value="1">Absolue</option>
							  <option value="2">Relative</option>
						</select>
					</div>

					<div class="hspace"></div>				

                    <?
                    /* ------------------------------------------------
                     *
                     * Tolerance absolue
                     *
                     * ------------------------------------------------ */ ?>

                    <div id="modal-tolerance-absolue">

                        <div class="form-group col-md-5">
                            <label for="modal-tolerance-valeur">Tolérance absolue :</label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">&#177;</span>
                                </div>

                                <input type="text" class="form-control col-md-4" name="tolerance_absolue">

                            </div> <!-- .input-group -->
                        </div>

                    </div> <!-- /#modal-tolerance-absolue -->

                    <?
                    /* ------------------------------------------------
                     *
                     * Tolerance relative
                     *
                     * ------------------------------------------------ */ ?>

                    <div id="modal-tolerance-relative" class="d-none">

                        <div class="form-group col-md-5">
                            <label for="modal-tolerance-valeur">Tolérance relative :</label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">&#177;</span>
                                </div>

                                <input type="text" class="form-control col-md-4" name="tolerance_relative">

                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div> <!-- /.input-group -->
                        </div>

                    </div> <!-- /#modal-tolerance-relative -->

                    <div class="col-md-12" style="margin-top: -10px">
                        <small class="form-text text-muted" style="padding-top: 5px">
                        <i class="fa fa-exclamation-circle" style="color: #ccc"></i> Il ne peut y avoir deux tolérances identiques.
                        </small>
                    </div>

                    <div class="space"></div>				

                    <?
                    /* ------------------------------------------------
                     *
                     * Penalite
                     *
                     * ------------------------------------------------ */ ?>

                    <div class="form-group col-md-5">
                        <label for="modal-tolerance-penalite">Pénalité de points (%) :</label>
                        <div class="input-group">
                            <input type="number" class="form-control col-md-3" name="penalite" id="modal-tolerance-penalite">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
					</div>

				</form>
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-tolerance-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter cette tolérance
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE EQUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-equation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter une équation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-equation-form'), 
						array('question_id' => NULL, 'question_type' => NULL, 'equation' => 1)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Équation pour générer la réponse :</label>
                        <div class="form-inline">
                            R=<span style="padding-left: 5px"></span>
                            <input name="reponse_texte" class="form-control question-equation col-md-10" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 40px; color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
				 		Vous pouvez utiliser les variables définies dans l'évaluation.
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Quelles sont les unités ?</label>
                        <input name="unites" class="form-control col-md-5" />
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Combien de chiffres significatifs ?</label>
                        <input name="cs" class="form-control col-md-1" value="99" />
						<div class="invalid-feedback">
						</div>
                    </div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 20px; color: #777; font-size: 0.9em;">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                        Si ce champ est vide ou 0, le nombre de CS sera automatiquement ajusté aux variables.<br />
                        <i class="fa fa-info-circle" style="margin-right: 5px; color: #fff; background: #fff"></i>
                        Si ce champ est 99, les CS ne seront pas pris en compte.<br />
                    </div>

					<div class="hspace"></div>				

                    <div class="form-group col-md-12">
                        <div class="form-check">
                            <input name="notsci" class="form-check-input" type="checkbox">
                            <label class="form-check-label" for="questions-aleatoires">
                                Afficher en notation scientifique ?
                          </label>
                        </div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-ajout-equation-type">Quel type de réponse ?</label>
						<select name="reponse_type" class="form-control col-md-6" id="modal-ajout-equation-type">
							<option value="2">Réponse erronée</option>
      						<option value="1">Réponse correcte</option>
						</select>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-equation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Ajouter cette équation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE EQUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-equation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier une équation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-equation-form'), 
						array('question_id' => NULL, 'reponse_id' => NULL, 'equation' => 1)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Équation pour générer la réponse :</label>
                        <div class="form-inline">
                            R=<span style="padding-left: 5px"></span>
                            <input name="reponse_texte" class="form-control question-equation col-md-10" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 40px; color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i> Vous pouvez utiliser les variables définies dans l'évaluation.
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Quelles sont les unités ?</label>
                        <input name="unites" class="form-control col-md-5" />
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Combien de chiffres significatifs ?</label>
                        <input name="cs" class="form-control col-md-1" />
						<div class="invalid-feedback">
						</div>
                    </div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 20px; color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                        Si ce champ est vide ou 0, le nombre de CS sera automatiquement ajusté aux variables.<br />
                        <i class="fa fa-info-circle" style="margin-right: 5px; color: #fff; background: #fff;"></i>
                        Si ce champ est 99, les CS ne seront pas pris en compte.
                    </div>

					<div class="hspace"></div>				

                    <div class="form-group col-md-12">
                        <div class="form-check">
                            <input name="notsci" class="form-check-input" type="checkbox">
                            <label class="form-check-label" for="questions-aleatoires">
                                Afficher en notation scientifique ?
                          </label>
                        </div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-modifier-equation-type">Quel type de réponse ?</label>
						<select name="reponse_correcte" class="form-control col-md-6" id="modal-modifier-equation-type">
							<option value="0">Réponse erronée</option>
      						<option value="1">Réponse correcte</option>
						</select>
					</div>

				</form>
				
            </div> <!-- .modal-body -->

            <div class="modal-footer">
                <div class="col">
                    <div id="modal-modifier-equation-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer cette équation
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-equation-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Modifier cette équation
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE EQUATION CORRECTE (TYPE 9)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-equation-correcte" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajouter une équation pour la réponse correcte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-equation-correcte-form'), 
						array('question_id' => NULL, 'question_type' => NULL, 'equation' => 1, 'reponse_type' => 1)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Équation pour générer la réponse correcte :</label>
                        <div class="form-inline">
                            R=<span style="padding-left: 5px"></span>
                            <input name="reponse_texte" class="form-control question-equation col-md-10" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 40px; color: #777; font-size: 0.9em;">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
				 		Vous pouvez utiliser les variables définies dans l'évaluation.
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Quelles sont les unités ?</label>
                        <input name="unites" class="form-control col-md-5" />
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Combien de chiffres significatifs ?</label>
                        <input name="cs" class="form-control col-md-1" value="99" />
						<div class="invalid-feedback">
						</div>
                    </div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 20px; color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                        Si ce champ est vide ou 0, le nombre de CS sera automatiquement ajusté aux variables.<br />
                        <i class="fa fa-info-circle" style="margin-right: 5px; color: #fff; background: #fff"></i>
                        Si ce champ est 99, les CS ne seront pas pris en compte.
                    </div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-equation-correcte-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Ajouter cette équation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE EQUATION CORRECTE (TYPE 9)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-equation-correcte" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier l'équation de la réponse correcte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-equation-correcte-form'), 
						array('question_id' => NULL, 'reponse_id' => NULL, 'equation' => 1)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Équation pour générer la réponse :</label>
                        <div class="form-inline">
                            R=<span style="padding-left: 5px"></span>
                            <input name="reponse_texte" class="form-control question-equation col-md-10" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 40px; color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i> Vous pouvez utiliser les variables définies dans l'évaluation.
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Quelles sont les unités ?</label>
                        <input name="unites" class="form-control col-md-5" />
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">Combien de chiffres significatifs ?</label>
                        <input name="cs" class="form-control col-md-1" />
						<div class="invalid-feedback">
						</div>
                    </div>
                    <div style="margin-top: -5px; padding: 0 20px 20px 20px; color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                        Si ce champ est vide ou 0, le nombre de CS sera automatiquement ajusté aux variables.<br />
                        <i class="fa fa-info-circle" style="margin-right: 5px; color: #fff; background: #fff;"></i>
                        Si ce champ est 99, les CS ne seront pas pris en compte.
                    </div>

				</form>
				
            </div> <!-- .modal-body -->

            <div class="modal-footer">
                <div id="modal-modifier-equation-correcte-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Modifier cette équation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER UNE QUESTION ET SES REPONSES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer une question</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-question-form'), 
						array('question_id' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Êtes-vous certain de vouloir effacer cette question ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-question-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer cette question
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE REPONSE ACCEPTEE (REPONSE LITTERALE COURTE)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-reponse-litterale-courte" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle" style="margin-right: 5px"></i> Définir une réponse acceptée</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-reponse-litterale-courte-form'), 
						array('question_id' => NULL, 'question_type' => 7, 'reponse_type' => 1, 'equation' => 0)
                    ); ?>

                    <div class="hspace"></div>

					<div class="form-group col-md-12">
                        <label for="modal-question-titre">Réponse acceptée :</label>

                        <div class="hspace"></div>

                        <div class="form-inline">
                            <input type="text" name="reponse_texte" class="form-control question-reponse-litterale-courte col-md-12" />
                        </div>

						<div class="invalid-feedback">
							Ce champ est requis.
                        </div>
					</div>

					<div class="hspace"></div>				

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-reponse-litterale-courte-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Ajouter cette réponse
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE REPONSE LITTERALE COURTE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-reponse-litterale-courte" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier une réponse acceptée</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-reponse-litterale-courte-form'), 
						array('question_id' => NULL, 'reponse_id' => NULL, 'reponse_type' => 1, 'reponse_correcte' => 1, 'question_type' => 7)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-question-titre">La réponse acceptée :</label>
                        <div class="form-inline">
                            <input type="text" name="reponse_texte" class="form-control col-md-12" />
                        </div>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-effacer-reponse-litterale-courte-sauvegarde" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer cette réponse
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-reponse-litterale-courte-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Modifier cette réponse
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div>

    	</div>
  	</div>
</div>
<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER LES PARAMETRES D'UNE REPONSE LITTERALE COURTE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-reponse-litterale-courte-parametres" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier les paramètres</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-reponse-litterale-courte-parametres-form'), 
						array('question_id' => NULL)
                    ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="">Similarité :</label>
                        <div class="input-group">
                            <input type="number" name="reponse_similarite" class="form-control col-md-3" />
                            <div class="input-group-append">
                                <div class="input-group-text">%</div>
                            </div>
                        </div>
                        <div class="hspace"></div>
                        <small class="form-text text-muted">
                            <i class="fa fa-exclamation-circle" style="color: #777"></i> Le pourcentage de similarité entre deux réponses (le plus proche de 100%, le mieux).
                        </small>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

                    <? if (1 == 2) : ?>
                        <div class="form-group col-md-12">
                            <label for="">Variation :</label>
                            <input name="reponse_variation" class="form-control col-md-3" />
                            <div class="invalid-feedback">
                                Ce champ est requis.
                            </div>
                        </div>
                    <? endif; ?>

				</form>
				
            </div> <!-- .modal-body -->

            <div class="modal-footer">
                <div id="modal-modifier-reponse-litterale-courte-parametres-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Sauvegarder ces paramètres
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER LE DOCUMENT (IMAGE) D'UNE QUESTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-document" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer une image</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-document-form'), 
						array('doc_id' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Êtes-vous certain de vouloir effacer cette image ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-document-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER LE TITRE D'UN DOCUMENT (IMAGE)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-titre-document" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier le titre d'une image</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-titre-document-form'), 
						array('doc_id' => NULL)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-modifier-titre-document-document-titre">Titre de l'image :</label>
						<textarea name="doc_caption" class="form-control" id="modal-modifier-titre-document-document-titre" rows="3"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>

						<div style="color: #777">
							<i class="fa fa-exclamation-circle"></i> Maximum de 150 caractères
						</div>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-modifier-titre-document-sauvegarde" class="btn btn-success spinnbale">
                    <i class="fa fa-save"></i> Sauvegarder les changements
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: INFORMATION SUR LA NOTATION SCIENTIFIQUE
 *
 * ------------------------------------------------------------------------- */ ?>	
<div id="modal-info-notation-scientifique" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-info-circle" style="margin-right: 10px"></i>Notation scientifique</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="padding: 15px 20px 0px 20px">
			<div> Vous pouvez répondre avec la notation scientifique de ces quelques façons.</div>
			<div class="space"></div>
			<table style="width: 100%">
				<tr>
					<td>
						<div style="margin-left: 22px;"><strong>5,12×10<sup>-7</sup></strong> peut s'écrire :</div>
						<div class="space"></div>
						<ul>
							<li>5,12E-7 <span style="margin-left: 10px">(E = ×10)</span></li>
							<li>5,12x10^-7</li>
							<li>5,12 x 10^-7</li>
						</ul>
					</td>
					<td>
						<div style="margin-left: 22px;"><strong>6,90×10<sup>3</sup></strong> peut s'écrire :</div>
						<div class="space"></div>
						<ul>
							<li>6,90E3 <span style="margin-left: 10px">(E = ×10)</span></li>
							<li>6,90x10^3</li>
							<li>6,90 x 10^3</li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UNE GRILLE DE CORRECTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-grille-correction" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Créer une grille de correction</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, 
                    array('id' => 'modal-ajout-grille-correction-form'), 
                    array('evaluation_id' => $evaluation_id, 'question_id' => NULL)
                ); ?>

					<div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-ajout-grille-correction-affichage">Affichage de la grille :</label>
                        <select name="grille_affichage" class="form-control col-8" id="modal-ajout-grille-correction-affichage">
                            <option value="1">Afficher les éléments évalués</option>
                            <option value="0">Dissimuler les éléments évalués</option>
						</select>
					</div>

                    <div class="form-group col-md-12" style="font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 3px"></i>
                        Afficher, ou non, les éléments évalués lorsque l'étudiant consulte son évaluation corrigée.
                    </div>
                </form>
                
      		</div> <!-- .modal-body -->
      
			<div class="modal-footer">
                <div id="modal-ajout-grille-correction-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Créer cette grille
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: IMPORTER UNE GRILLE DE CORRECTION D'UNE AUTRE QUESTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-importer-grille-correction" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Importer une grille de correction</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, 
                    array('id' => 'modal-importer-grille-correction-form'), 
                    array('question_id' => NULL, 'evaluation_id' => $evaluation_id)
                ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-importer-grille-correction-question-id-origine"><strong>Question ID</strong> de la question qui contient la grille : </label>
                        <input type="text" class="form-control col-4" name="question_id_origine" id="modal-importer-grille-correction-question-id-origine" placeholder="Question ID">
                    
                        <div style="font-size: 0.9em; margin-top: 12px;">
                            <i class="fa fa-info-circle" style="margin-right: 3px; color: #999"></i>
                            Ce numéro se trouve dans l'éditeur, dans la section sur les informations de la question. 
                        </div>
                        <div style="font-size: 0.9em; margin-top: 3px;">
                            <i class="fa fa-info-circle" style="margin-right: 3px; color: #999"></i>
                            Vous ne pouvez importer qu'une grille de vos questions, ou une grille d'une question du groupe.
                        </div>

                        <div id="modal-importer-grille-correction-erreur" class="d-none" style="font-size: 0.9em; color: crimson; margin-top: 20px;">
                            <i class="fa fa-exclamation-circle" style="margin-right: 3px"></i>
                            <strong>Erreur</strong> : Vous ne pouvez pas importer cette grille.
                        </div>
                    </div>
                </form>

      		</div> <!-- .modal-body -->
      
			<div class="modal-footer">
                <div id="modal-importer-grille-correction-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Importer cette grille
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UNE GRILLE DE CORRECTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-grille-correction" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier une grille de correction</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, 
                    array('id' => 'modal-modifier-grille-correction-form'), 
                    array('question_id' => NULL, 'grille_id' => NULL)
                ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-modifier-grille-correction-affichage">Affichage de la grille :</label>
                        <select name="grille_affichage" class="form-control col-8" id="modal-modifier-grille-correction-affichage">
                            <option value="1">Afficher les éléments évalués</option>
                            <option value="0">Dissimuler les éléments évalués</option>
						</select>
					</div>

                    <div class="form-group col-md-12" style="font-size: 0.9em">
                        <li>
                            Afficher, ou non, les éléments évalués lorsque l'étudiant consulte son évaluation corrigée.
                        </li>

                    </div>

                    <div class="hspace"></div>				

					<div class="form-check" style="margin-left: 15px">
                        <input type="checkbox" name="grille_effacement" id="modal-bloc-effacement" class="form-check-input" />
						<label for="modal-bloc-effacement" style="margin-left: 5px">Pour effacer cette grille et tous ses éléments, confirmez en cochant.</label>
					</div>

                </form>
                
      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-effacer-grille-correction-sauvegarde" class="btn btn-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer cette grille et ses éléments
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-grille-correction-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Sauvegarder
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN ELEMENT (GRILLE DE CORRECTION)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-element" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un élément évalué</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, 
                    array('id' => 'modal-ajout-element-form'), 
                    array('question_id' => NULL, 'grille_id' => NULL)
                ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-ajout-element-desc">Description : </label>
                        <input type="text" class="form-control col-12" name="element_desc" id="modal-ajout-element-desc">
                    </div>

					<div class="form-group col-md-12">
						<label for="modal-ajout-element-type">Type d'élément :</label>
                        <select name="element_type" class="form-control col-4" id="modal-ajout-element-type">
                            <option value="1">Additif</option>
                            <option value="2">Déductif</option>
						</select>
                    </div>

                    <div class="form-group col-md-12" style="font-size: 0.9em">
                        <li>
                            Un élément <strong>additif</strong> sera ajouté aux points obtenus.
                        </li>
                        <li>
                            Un élément <strong>déductif</strong> sera soustrait aux points obtenus.
                        </li>
                    </div>

					<div class="form-group col-md-6">
						<label for="modal-ajout-element-ordre">Ordre de présentation :</label>
                            <input name="element_ordre" type="text" class="form-control col-md-3" 
                                   id="modal-ajout-element-ordre" value="99">
					</div>

					<div class="form-group col-md-6">
						<label for="modal-ajout-element-pourcent">Pourcentage alloué à cet élément :</label>
						<div class="input-group">
                            <input name="element_pourcent" type="text" class="form-control col-md-3 ax-points" style="text-align: right" 
                                   id="modal-ajout-element-pourcent" placeholder="">
       	 					<div class="input-group-append">
          						<div class="input-group-text">%</div>
        					</div>
      					</div>
                    </div>
                    
                    <div class="form-group col-md-12" style="margin-top: -5px; font-size: 0.9em;">
                        <i class="fa fa-exclamation-circle"></i>
                        La somme des pourcentages de tous les éléments additifs doit être de 100%.
                    </div>

                </form>

      		</div> <!-- .modal-body -->
      
			<div class="modal-footer">
                <div id="modal-ajout-element-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter cet élément
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER UN ELEMENT (GRILLE DE CORRECTION)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-element" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Modifier un élément évalué</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

            <?= form_open(NULL, 
                    array('id' => 'modal-modifier-element-form'), 
                    array('question_id' => NULL, 'grille_id' => NULL, 'element_id' => NULL)
                ); ?>

                    <div class="hspace"></div>				

					<div class="form-group col-md-12">
						<label for="modal-modifier-element-desc">Description : </label>
                        <input type="text" class="form-control col-12" name="element_desc" id="modal-modifier-element-desc">
                    </div>

					<div class="form-group col-md-12">
						<label for="modal-ajout-element-type">Type d'élément :</label>
                        <select name="element_type" class="form-control col-4" id="modal-ajout-element-type">
                            <option value="1">Additif</option>
                            <option value="2">Déductif</option>
						</select>
                    </div>

                    <div class="form-group col-md-12" style="font-size: 0.9em">
                        <li>
                            Un élément <strong>additif</strong> sera ajouté aux points obtenus.
                        </li>
                        <li>
                            Un élément <strong>déductif</strong> sera soustrait aux points obtenus.
                        </li>
                    </div>

					<div class="form-group col-md-6">
						<label for="modal-modifier-element-ordre">Ordre de présentation :</label>
                            <input name="element_ordre" type="text" class="form-control col-md-3" 
                                   id="modal-modifier-element-ordre" value="99">
					</div>

					<div class="form-group col-md-6">
						<label for="modal-modifier-element-pourcent">Pourcentage alloué à cet élément :</label>
						<div class="input-group">
                            <input name="element_pourcent" type="text" class="form-control col-md-3 ax-points" style="text-align: right" 
                                   id="modal-modifier-element-pourcent" placeholder="">
       	 					<div class="input-group-append">
          						<div class="input-group-text">%</div>
        					</div>
      					</div>
                    </div>

                    <div class="form-group col-md-12" style="margin-top: -5px; font-size: 0.9em;">
                        <i class="fa fa-exclamation-circle"></i>
                        La somme des pourcentages de tous les éléments additifs doit être de 100%.
                    </div>

                </form>

      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-effacer-element-sauvegarde" class="btn btn-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-modifier-element-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-edit"></i> Modifier
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
            </div>

    	</div>
  	</div>
</div>
