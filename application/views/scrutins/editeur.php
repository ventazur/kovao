<div id="editeur-scrutin">
<div class="container-fluid">

<div id="scrutin-data" class="d-none" data-scrutin_id="<?= $scrutin_id; ?>"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3><i class="fa fa-edit" style="color: #ccc; margin-right: 5px"></i> Éditeur de scrutin</h3>

    <div class="space"></div>

    <? 
    /* ---------------------------------------------------------------
     *
     * LA QUESTION
     *
     * --------------------------------------------------------------- */ ?>

    <div id="editeur-question">

        <div class="editeur-scrutin-section-titre">
            <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i> La question
        </div>

        <div class="editeur-scrutin-section">
            <?= $scrutin['scrutin_texte']; ?>
        </div>

        <div class="editeur-scrutin-section-options">
            <div class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-modifier-question">
                <i class="fa fa-edit" style="margin-right: 5px"></i> Modifier la question
            </div>
        </div>

    </div> <!-- #question -->

    <? 
    /* ---------------------------------------------------------------
     *
     * LES CHOIX
     *
     * --------------------------------------------------------------- */ ?>

    <div id="choix">

        <div class="tspace"></div>

        <div class="editeur-scrutin-section-titre">
            <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i> Les choix
        </div>

        <div id="choix-liste">

            <table class="table table-borderless table-sm">

                <? if (empty($choix)) : ?>

                    <tr>
                        <td style="padding: 20px 15px 20px 15px;">
                            <i class="fa fa-exclamation-circle" style="margin-right: 7px"></i> Aucun choix trouvé
                        </td>
                    </tr>

                    <? else : ?>

                        <? foreach($choix as $c) : ?> 

                            <tr>
                                <td style="padding-left: 15px; padding-top: 10px">
                                    <i class="fa fa-angle-right" style="margin-right: 7px"></i> <?= $c['choix_texte']; ?>
                                </td>
                                <td style="min-width: 150px; text-align: right">
                                    <div class="btn btn-sm btn-outline-danger effacer-choix" data-choix_id="<?= $c['choix_id']; ?>">
                                        <i class="fa fa-trash" style="margin-right: 7px"></i>Effacer ce choix
                                    </div>
                                </td>
                            </tr>

                        <? endforeach; ?>

                    <? endif ?>
            </table>
        </div>

        <div class="editeur-scrutin-section-options">
            <div class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-ajout-choix">
                <i class="fa fa-plus-circle" style="margin-right: 5px"></i> Ajouter un choix
            </div>
        </div>

    </div> <!-- #choix -->

    <? 
    /* ---------------------------------------------------------------
     *
     * LES PARTICIPANTS
     *
     * --------------------------------------------------------------- */ ?>

    <div id="participants">

        <div class="tspace"></div>

        <div class="editeur-scrutin-section-titre">
            <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i> Les participants
        </div>

        <div id="participants-liste">

            <? if (empty($enseignants)) : ?>

                <div class="editeur-scrutin-section" style="border-radius: 0 0 3px 3px;">

                    <i class="fa fa-exclamation-circle" style="margin-right: 7px;"></i> Aucun enseignant dans ce groupe

                </div>

            <? else : ?>

                <table class="table table-borderless table-sm">

                    <? $i = 0; foreach($enseignants as $e) : ?>

                        <? if (array_key_exists($e['enseignant_id'], $participants)) $i++; ?>

                        <tr class="participant">
                            <td class="participant-enseignant">
                                <i class="fa fa-user" style="margin-right: 7px; color: #ccc"></i> <?= $e['prenom'] . ' ' . $e['nom']; ?>
                            </td>
                            <td class="participant-options" data-enseignant_id="<?= $e['enseignant_id']; ?>">
                                <div class="btn-group">
                                    <div class="btn btn-sm btn-outline-primary spinnable participant <?= array_key_exists($e['enseignant_id'], $participants) ? 'active' : ''; ?>" style="width: 150px">
                                        Participant <i class="fa fa-spin fa-circle-o-notch d-none" style="margin-left: 5px;"></i> 
                                    </div>
                                    <div class="btn btn-sm btn-outline-primary spinnable non-participant <?= array_key_exists($e['enseignant_id'], $participants) ? '' : 'active'; ?>" style="width: 150px">
                                        Non participant <i class="fa fa-spin fa-circle-o-notch d-none" style="margin-left: 5px;"></i> 
                                    </div>
                                </div>
                            </td>
                        </tr>

                    <? endforeach; ?>

                    <tr class="participant">
                        <td colspan="2" class="participant-enseignant" style="padding-left: 35px; color: slateblue">
                            <?= $i; ?> participant<?= $i > 1 ? 's' : ''; ?>
                        </td>
                    </tr>
                </table>

            <? endif; // if empty enseignants ?>

        </div> <!-- .participants-liste -->

    </div> <!-- #participants -->

    <? 
    /* ---------------------------------------------------------------
     *
     * LES DOCUMENTS
     *
     * --------------------------------------------------------------- */ ?>

    <div id="documents" class="">

        <div class="tspace"></div>

        <div class="editeur-scrutin-section-titre">
            <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i> Les documents
        </div>

        <div id="documents-liste">

            <table class="table table-borderless table-sm">

                <? if (empty($documents)) : ?>

                    <tr>
                        <td style="padding: 20px 15px 20px 15px;">
                            <i class="fa fa-exclamation-circle" style="margin-right: 7px"></i> Aucun document trouvé
                        </td>
                    </tr>

                    <? else : ?>
        
                        <? $i = 0; 
    
                            $mime_view = array(
                                'application/pdf', 'image/jpg', 'image/jpeg', 'image/png'
                            );

                            foreach($documents as $d) : ?> 

                            <?  $i++;

                                $doc_url = base_url() . $this->config->item('documents_path') . $d['doc_filename']; 
                                $file_icon = determiner_file_icon($d['doc_mime_type']);
                            ?>

                            <tr class="document">
                                <td style="padding-left: 15px; padding-top: 12px; padding-bottom: 10px;">
                                    <div class="row">

                                        <div class="col-7">
                                            <i class="fa <?= $file_icon; ?> fa-lg" style="margin-right: 10px"></i>
                                            <a <?= in_array($d['doc_mime_type'], $mime_view) ? 'href="' . $doc_url . '"' : ''; ?>>
                                                <span class="document-caption">
                                                    <? if (empty($d['doc_caption'])) : ?>
                                                        Document <?= $scrutin_id . '.' . $i; ?>
                                                    <? else : ?>
                                                        <?= $d['doc_caption']; ?>
                                                    <? endif; ?>
                                                </span>
                                            </a>
                                            <div class="btn btn-sm btn-outline-secondary ajouter-document-caption" style="margin-left: 10px;" 
                                                data-scrutin_doc_id="<?= $d['scrutin_doc_id']; ?>" data-toggle="modal" data-target="#modal-modifier-document-caption">
                                                <i class="fa fa-pencil"></i>
                                                <span class="document-caption"></span>
                                            </div>
                                        </div> <!-- .col-7 -->

                                        <div class="col-5" style="text-align: right">
                                            <a class="btn btn-sm btn-outline-primary" href="<?= $doc_url; ?>" style="margin-right: 3px" download="Document<?= $scrutin_id . '_' . $i . '.' . determiner_extension($d['doc_filename']); ?>">
                                                <i class="fa fa-download" style="margin-right: 5px"></i> Télécharger
                                            </a>
                                                
                                            <div class="btn btn-sm btn-outline-danger effacer-document mt-2 mt-md-0" 
                                                data-scrutin_doc_id="<?= $d['scrutin_doc_id']; ?>" style="margin-right: 5px" data-toggle="modal" data-target="#modal-effacer-document">
                                                <i class="fa fa-trash" style="margin-right: 7px"></i>Effacer ce document
                                            </div>
                                        </div> <!-- .col-5 -->

                                    </div> <!--  .row -->
                                </td>
                            </tr>

                        <? endforeach; ?>

                    <? endif ?>
            </table>
        </div>

        <div class="editeur-scrutin-section-options">
            <div id="ajout-document" style="margin-bottom: -8px">
                <input type="file" name="docfile" id="ajout-document-input">
                <label class="btn btn-outline-primary" for="ajout-document-input">
                    <i class="fa fa-plus-circle"></i> Ajouter un document
                    <i class="fa fa-spin fa-circle-o-notch document-upload-spinner d-none" style="margin-left: 10px;"></i>
                </label>
            </div>
        </div>

    </div> <!-- #documents -->

    <? 
    /* ---------------------------------------------------------------
     *
     * LES OPTIONS
     *
     * --------------------------------------------------------------- */ ?>

    <div id="options">

        <div class="tspace"></div>

        <div class="editeur-scrutin-section-titre">
            <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i> Les options
        </div>

        <div class="editeur-scrutin-section" style="border-radius: 0 0 3px 3px">

            <? if ( 1 == 2) : ?>
                <div class="custom-control custom-switch">
                    <input name="code_morin" id="code-morin" class="custom-control-input" type="checkbox" <?= $scrutin['code_morin'] ? 'checked' : ''; ?>>
                    <label class="custom-control-label" for="code-morin">
                        <span style="margin-left: 10px">Ce scrutin requiert d'être proposé et appuyé.</spanA>
                    </label>
                </div>
                <small style="display: block; margin-top: 10px">
                    <i class="fa fa-exclamation-circle" style="color: #aaa"></i> 
                    Les participants seront convoqués pour trouver un proposeur et un appuyeur avant le début du scrutin.
                </small>

                <div class="space"></div>
            <? endif; ?>

            <div class="custom-control custom-switch">
                <input name="anonyme" id="votes-anonymes" class="custom-control-input" type="checkbox" <?= $scrutin['anonyme'] ? 'checked' : ''; ?>>
                <label class="custom-control-label" for="votes-anonymes">
                    <span style="margin-left: 10px">Les votes sont anonymes.</spanA>
                </label>
            </div>

            <? if (1 == 2) : ?>
                <div class="hspace"></div>

                <div class="custom-control custom-switch">
                    <input name="anonyme" id="resultats-partiels" class="custom-control-input" type="checkbox" disabled>
                    <label class="custom-control-label" for="resultats-partiels">
                        <span style="margin-left: 10px">[À faire] Afficher les résultats partiels aux participants après leur vote.</span>
                    </label>
                </div>
            <? endif; ?>

            <div class="space"></div>

            <div id="date-echeance-group" class="form-group form-inline" style="margin-bottom: 0px">
                <label for="date-echeance" style="margin-right: 10px">Date d'échéance</label>
                <div class="input-group">
                <input name="date_echeance" id="date-echeance" class="form-control form-control-sm datepicker" style="width: 100px" type="text" value="<?= $scrutin['echeance_date'] ?: ''; ?>">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-outline-primary" style="border: 1px solid #ccc" type="button" id="datepicker-clear">
                            <i class="fa fa-times" style="color: crimson"></i>
                        </button>
                    </div>
                </div>
                <i id="datepicker-saving" class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 10px; color: #aaa"></i>
                <i id="datepicker-saved"  class="fa fa-check d-none" style="margin-left: 10px; color: limegreen"></i>
                <i id="datepicker-failed" class="fa fa-exclamation-circle d-none" style="margin-left: 10px; color: crimson"></i>
            </div>
            <small style="display: block; margin-top: 10px">
                <i class="fa fa-exclamation-circle" style="color: #aaa"></i> 
                La date d'échéance ne peut excéder 90 jours et deviendra 90 jours si ce champ est laissé vide.
            </small>
        </div>

    <div>

    <div style="border-top: 3px solid #9FA8DA; margin-top: 30px; margin-bottom: 30px;"></div>
    
    <div class="row no-gutters">
        <div class="col-6">
            <a href="<?= base_url() . 'scrutins/previsualisation/' . $scrutin_id; ?>" class="btn btn-outline-primary mb-2">
                <i class="fa fa-eye" style="margin-right: 5px"></i> Prévisualiser ce scrutin
            </a>
            <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal-lancer-scrutin">
                <i class="fa fa-paper-plane" style="margin-right: 5px"></i> Lancer ce scrutin
            </button>
        </div>
        <div class="col-6" style="text-align: right">
            <button class="btn btn-danger mb-2" data-toggle="modal" data-target="#modal-effacer-scrutin">
                <i class="fa fa-trash" style="margin-right: 5px"></i> Effacer ce scrutin
            </button> 
        </div>
    </div>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #editeur-scrutin -->

<? 
/* ============================================================================
 *
 * MODALS
 *
 * ============================================================================ */ ?>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: MODIFIER LA QUESTION DU SCRUTIN
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-question" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier la question du scrutin</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-question-form'), 
						array('scrutin_id' => $scrutin_id)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-question" style="font-weight: bold">Question du scrutin :</label>
						<div class="qspace"></div>
                        <textarea name="scrutin_texte" id="modal-modifier-question-texte" class="form-control" rows="3"><?= $scrutin['scrutin_texte']; ?></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>

						<div class="hspace"></div>

						<div style="color: #777">
							<i class="fa fa-exclamation-circle"></i> Maximum de 250 caractères
						</div>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-modifier-question-sauvegarde" class="btn btn-success spinnable">
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
 * MODAL: AJOUTER UN CHOIX
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajout-choix" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un nouveau choix</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-ajout-choix-form'), 
						array('scrutin_id' => $scrutin_id)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-ajout-choix-texte" style="font-weight: bold">Choix :</label>
						<input name="choix_texte" class="form-control" id="modal-ajout-choix-texte"></input>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
                    </div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-ajout-choix-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-plus-circle"></i> Ajouter ce choix
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
 * MODAL: EFFACER SCRUTIN
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-scrutin" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer un scrutin</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-scrutin-form'),
                        array('scrutin_id' => $scrutin_id)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 30px; padding-bottom: 20px">

						Êtes-vous certain de vouloir effacer ce scrutin ?
                        <br/ ><br />
                        Les scrutins lancés à partir de ce scrutin ne seront pas effacés.
                        <br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-scrutin-sauvegarde" data-scrutin_id="<?= $scrutin_id; ?>" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer ce scrutin
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
 * MODAL: EFFACER document
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-document" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer un document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-document-form'), 
                        array('scrutin_id' => $scrutin_id, 'scrutin_doc_id' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 30px; padding-bottom: 20px">

						Êtes-vous certain de vouloir effacer ce document ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-document-sauvegarde" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Effacer ce document
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
 * MODAL: MODIFIER LA DESCRIPTION (CAPTION) D'UN DOCUMENT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-modifier-document-caption" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier la description d'un document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-modifier-document-caption-form'), 
						array('scrutin_id' => $scrutin_id, 'scrutin_doc_id' => NULL)
					); ?>

					<div class="form-group col-md-12">
						<label for="modal-modifier-document-caption-texte" style="font-weight: bold">Description du document :</label>
						<div class="qspace"></div>
                        <textarea name="doc_caption" id="modal-modifier-document-caption-texte" class="form-control" rows="3"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>

						<div class="hspace"></div>

						<div style="color: #777">
							<i class="fa fa-exclamation-circle"></i> Maximum de 100 caractères
						</div>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-modifier-document-caption-sauvegarde" class="btn btn-success spinnable">
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
 * MODAL: LANCER UN SCRUTIN
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-lancer-scrutin" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-paper-plane" style="margin-right: 10px"></i> Lancer un scrutin</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-lancer-scrutin-form'), 
                        array('scrutin_id' => $scrutin_id)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 30px; padding-bottom: 20px">

                        Êtes-vous certain de vouloir lancer ce scrutin ?

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-lancer-scrutin-sauvegarde" class="btn btn-primary spinnable">
                    <i class="fa fa-paper-plane" style="margin-right: 5px"></i> Lancer ce scrutin
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times" style="margin-right: 5px"></i> Annuler
                </div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

