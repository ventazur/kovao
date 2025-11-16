<div id="scrutins-gerer">
<div class="container-fluid">

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">
    
        <h3>Gérer vos scrutins</h3>

        <? if (empty($scrutins) && empty($scrutins_lances_en_vigueur)) : ?>

            <div class="tspace"></div>

            <i class="fa fa-exclamation-circle"></i> 
            Vous n'avez créé aucun scrutin.
            Veuillez <a href="<?= base_url() . 'scrutins/creer'; ?>">Créer</a> un scrutin.

        <? elseif ( ! empty($scrutins)) : ?>

            <div id="scrutins-en-prep" style="margin-top: 30px">

                <h5>Vos scrutins</h5>

                <? foreach($scrutins as $scrutin_id => $scrutin) : ?>

                    <div class="hspace"></div>

                    <a class="scrutin-item-link" href="<?= base_url() . 'scrutins/editeur/' . $scrutin_id; ?>">
                        <div class="scrutin-item">
                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td rowspan="2" style="width: 30px;">
                                            <i class="fa fa-square" style="color: #9FA8DA"></i> 
                                        </td>
                                        <td class="text-truncate">
                                            <?= $scrutin['scrutin_texte']; ?><br />
                                        </td>
                                        <td rowspan="2" style="text-align: right">
                                            <div class="btn btn-sm btn-primary">
                                                <?  if (in_array($scrutin_id, $scrutin_ids_lances)) : ?>
                                                    Lancé
                                                    <i class="fa fa-exclamation" style="margin-left: 5px"></i>
                                                <? else : ?>  
                                                    <i class="fa fa-edit" style="margin-right: 5px"></i> Éditer
                                                <? endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </a>
                <? endforeach; ?>

            </div> <!-- #scrutins-en-prep -->

        <? endif; ?>

        <? if ( ! empty($scrutins_lances_en_vigueur)) : ?>

            <div class="scrutins-en-vigueur" style="margin-top: 30px">

                <h5><i class="fa fa-send" style="margin-right: 5px; color: dodgerblue"></i> Vos scrutins lancés en vigueur</h5>

                <? foreach($scrutins_lances_en_vigueur as $s) : ?>

                    <div class="hspace"></div>

                    <div class="scrutin-item">
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td rowspan="2" style="width: 30px; vertical-align: top">
                                        <i class="fa fa-square" style="color: #9FA8DA"></i> 
                                    </td>
                                    <td class="text-truncate">
                                        <?= $s['scrutin_texte']; ?><br />
                                    </td>
                                    <td rowspan="2" style="text-align: right">
                                        <a class="btn btn-sm btn-success" href="<?= base_url() . 'scrutin/' . $s['scrutin_reference'] . '/resultats'; ?>">
                                            Résultats partiels
                                        </a>
                                        <div class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-terminer-scrutin" data-scrutin_reference="<?= $s['scrutin_reference']; ?>">
                                            Terminer le scrutin
                                        </div>
                                    </td>
                                </tr>
                                <tr>    
                                    <td style="font-size: 0.8em; padding-top: 3px">
                                        <span class="badge badge-warning">
                                            Échéance le <?= date_french_full($s['echeance_epoch']); ?>
                                        </span>
                                        <? if ($s['anonyme']) : ?>
                                            <span class="badge badge-dark">
                                                Anonyme
                                            </span>
                                        <? endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <? endforeach; ?>

            </div>

        <? endif; ?>


        <? if ( ! empty($scrutins_lances_termines)) : ?>

            <div class="tspace"></div>

            <div class="scrutins-termines">

                <h5><i class="fa fa-send" style="margin-right: 5px; color: lightblue"></i> Vos scrutins lancés terminés</h5>

                <? foreach($scrutins_lances_termines as $s) : ?>

                    <div class="hspace"></div>

                    <div class="scrutin-termine-item">
                        <table style="width: 100%">
                            <tbody>
            
                                <tr>
                                    <td rowspan="3" style="width: 30px; vertical-align: top">
                                        <i class="fa fa-square" style="color: #aaa"></i> 
                                    </td>
                                    <td class="text-truncate">
                                        <?= $s['scrutin_texte']; ?><br />
                                    </td>
                                    <td rowspan="3" style="text-align: right;">
                                        <? if ($s['votes'] == 0) : ?>

                                        <div class="effacer-scrutin-lance btn btn-sm btn-danger" style="margin-right: -7px" 
                                            data-toggle="modal" 
                                            data-target="#modal-effacer-scrutin-lance" 
                                            data-scrutin_reference="<?= $s['scrutin_reference']; ?>">
                                                <i class="fa fa-trash" style="margin-right: 5px"></i> Effacer ce scrutin
                                            </div>

                                        <? else : ?>

                                            <a class="scrutin-item-link" href="<?= base_url() . 'scrutin/' . $s['scrutin_reference'] . '/resultats'; ?>">
                                                <div class="btn btn-sm btn-success" style="margin-right: -7px">
                                                    Résultats finaux
                                                </div>
                                            </a>

                                        <? endif; ?>
                                    </td>
                                </tr>
                                <tr>    
                                    <td style="font-size: 0.8em; padding-top: 3px">
                                        <span class="badge badge-danger">
                                            <? if ($s['termine']) : ?>
                                                Terminé le <?= date_french_full($s['termine_epoch']); ?>
                                            <? else : ?>
                                                Terminé le <?= $s['echeance_epoch'] ? date_french_full($s['echeance_epoch']) : '[date inconnue]'; ?>
                                            <? endif; ?>
                                        </span>
                                        <? if ($s['anonyme']) : ?>
                                            <span class="badge badge-dark">
                                                Anonyme
                                            </span>
                                        <? endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 0.8em; padding-top: 3px">
                                        Taux de participation : <?= $s['votes'] . '/' . $s['participants']; ?> (<?= number_format($s['votes']/$s['participants']*100); ?>%)
                                    <td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <? endforeach; ?>

            </div> <!-- .scrutins-termins -->

        <? endif; ?>

    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #scrutins-gerer -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: TERMINER UN SCRUTIN
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-terminer-scrutin" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Terminer un scrutin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mt-3" style="text-align: center">
            <i class="fa fa-exclamation-circle"></i>
            Voulez-vous vraiment terminer ce scrutin ?
        </p>
      </div>
      <div class="modal-footer">
        <button id="terminer-scrutin-action" data-scrutin_reference="" type="button" class="btn btn-danger">Terminer</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
      </div>
    </div>
  </div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER UN SCRUTIN LANCE ET TERMINE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-scrutin-lance" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash" style="margin-right: 5px"></i> Effacer un scrutin terminé</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-scrutin-lance-form'), 
                        array('scrutin_reference' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 30px; padding-bottom: 20px">

						Êtes-vous certain de vouloir effacer ce scrutin ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-effacer-scrutin-lance-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer ce scrutin
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

