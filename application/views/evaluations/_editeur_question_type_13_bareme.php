<?  
// ----------------------------------------------------------------
//
// REPONSE A DEVELOPPEMENT CHATGPT (TYPE 13) *** BAREME ***
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-13-bareme">

    <?
    /*
     * Choisoir un modele ChatGPT
     *
     */ ?>

    <div style="margin-top: 15px">

        <div class="btn btn-outline-primary"
            data-question_id="<?= $question_id; ?>" 
            data-question_type="<?= $q['question_type']; ?>"
            data-toggle="modal" 
            data-target="#modal-ajout-bareme-prompt">
            <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> 
            Ajouter un barême sous forme de prompt
        </div>

    </div>

</div> <!-- /.editeur-question-type-13-bareme -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJOUTER UN BAREME SOUS FORME DE PROMPT (TYPE 13)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-bareme-prompt" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un barème</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-bareme-prompt-form'),
						array('question_id' => NULL, 'question_type' => NULL)
                    ); ?>

                    <div class="qspace"></div>				

                    <div class="form-group col-md-12">

                        <div style="font-size: 0.9em">
                            <i class="fa fa-exclamation-circle" style="color: orange; margin-right: 5px;"></i> 
                            Votre barême doit être sous forme de <i>prompt</i> pour ChatGPT.
                        </div>
                    
                        <div class="space"></div>				

                        <textarea class="form-control" rows=10">
- Orthographe : 3 points
- Grammaire : 3 points
- Style : 2 points
- Cohérence : 2 points
                        </textarea>

						<div class="invalid-feedback">
							Ce champ est requis.
                        </div>

                    </div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-bareme-prompt-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Ajouter ce barème
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
 * MODAL: MODIFIER UN BAREME SOUS FORME DE PROMPT (TYPE 13)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-bareme-prompt" class="modal" tabindex="-1" role="dialog">
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
						array('id' => 'modal-modifier-bareme-prompt-form'), 
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
                <div id="modal-modifier-bareme-prompt-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Modifier cette équation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>
