<?
/* ============================================================================
 *
 * RESULTATS
 *
 * ----------------------------------------------------------------------------
 *
 * Historique des versions :
 *
 * - version 1 (2018)
 * - version 2 (2019) 
 * - version 3 (2020) Tentative avec une sidebar (skeleton), non fonctionnelle
 * - version 4 (2020)
 * - version 5 (2020) Ajouter la ponderation des evaluations pour les rangs
 *
 * ============================================================================ */ ?>

<link href="<?= base_url() . 'assets/css/resultats4.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/resultats4.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<? $resultats_totaux = 0; ?>

<div id="resultats">
<div class="container-fluid">

<?
/* ----------------------------------------------------------------------------
 *
 * En-tete
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-9 col-xl-7">

        <h3>Résultats 
            <? if ( ! empty($enseignant['semestre_id']) || ! empty($semestre_id)) :  ?>
                <span style="color: limegreen"><?= @$semestres[$semestre_id]['semestre_code']; ?></span>
            <? endif; ?>
        </h3>

    </div>
    <div class="col-sm-3 col-xl-3">
        <div class="float-sm-right mt-2 mt-sm-0">

            <? if ( ! empty($semestres) && count($semestres) > 1) : ?>

                <div class="dropdown">	

                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                        Autres semestres
                    </button>

                    <? $semestres_r = array_reverse($semestres); ?>

                    <div class="dropdown-menu">

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

    </div> <? // .col-md-3 ?>
    <div class="d-none d-xl-block col-xl-1"></div>
</div> <? // .row ?>

<div class="hspace"></div>

<?
/* ----------------------------------------------------------------------------
 *
 * Resultats
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

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

    <? foreach($cours as $cours_id => $c) : ?>

        <div id="cours<?= $cours_id; ?>" class="resultats-cours-titre cours-liste-toggle">

            <div class="row">
                <div class="col">
                    <?= $c['cours_nom'] . ' (' . $c['cours_code'] . ')'; ?>
                </div>
                <div class="col-2 cours-liste-toggle-btn mr-2 mr-md-0" style="text-align: right;">
                    <span style="margin-right: 28px; text-align: right; font-size: 0.85em">Pondérations</span>
                    <span class="expand d-none"><i class="fa fa-plus-square-o"></i></span>
                    <span class="collap"><i class="fa fa-minus-square-o"></i></span>
                </div>
            </div>

        </div>

        <div class="resultats-cours-contenu">

        <?
        // ------------------------------------------
        //
        // Evaluations
        //
        // ------------------------------------------ */ ?>

        <? foreach($evaluations as $e) : ?>

            <? if ($e['cours_id'] != $cours_id) : continue; endif; ?>
            
            <? $resultats_totaux += $e['count']; ?>

            <? $label = 's' . $semestre_id . 'e' . $e['evaluation_id']; ?>

            <a href="<?= base_url() . 'resultats/evaluation/' . $e['evaluation_id'] . '/semestre/' . $e['semestre_id']; ?>" class="resultats-evaluation-titre-lien spinnable">

                <div class="resultats-evaluation-titre">

                    <div class="row" style="height: 32px">
                        <div class="col">

                            <div class="btn btn-sm" style="margin-right: 5px; color: #bbb; font-size: 1em; font-weight: 300">
                                <?= $cours_raw[$cours_id]['cours_code_court']; ?>
                            </div>

                            <div class="btn btn-sm" style="font-size: 1em; font-weight: 400">
                                <? if ($e['lab'] ?? 0) : ?>
                                    <span style="margin-right: 5px; font-size: 0.8em; font-weight: 300; background: #444; color: #fff; border-radius: 3px; padding: 3px 4px 3px 4px">LAB</span>
                                <? endif; ?>
                                <span style="overflow-wrap: break-word;"><?= $e['evaluation_titre']; ?></span>
                            </div>

                            <div class="btn btn-sm" style="color: #aaa; font-weight: 300; font-size: 1em; margin-left: -10px">
                                (<?= $e['count']; ?>)
                            </div>

                            <? if ( ! empty($visibilites[$e['evaluation_id']])) : ?>

                                <? $toutes = (count($visibilites[$e['evaluation_id']]) == $e['count']) ? TRUE : FALSE; ?>

                                <? if ($toutes) : ?>

                                    <div class="btn btn-sm" style="color: #aaa; font-weight: 300; font-size: 1em; margin-left: -10px">
                                        <svg viewBox="0 0 16 16" class="bi bi-eye" fill="limegreen" xmlns="http://www.w3.org/2000/svg"
                                            data-toggle="popover" 
                                            data-content="Toutes les évaluations corrigées sont visibles.">
                                          <path fill-rule="evenodd" d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.134 13.134 0 0 0 1.66 2.043C4.12 11.332 5.88 12.5 8 12.5c2.12 0 3.879-1.168 5.168-2.457A13.134 13.134 0 0 0 14.828 8a13.133 13.133 0 0 0-1.66-2.043C11.879 4.668 10.119 3.5 8 3.5c-2.12 0-3.879 1.168-5.168 2.457A13.133 13.133 0 0 0 1.172 8z"/>
                                          <path fill-rule="evenodd" d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                        </svg>
                                    </div>

                                <? else : ?>

                                    <div class="btn btn-sm" style="color: #aaa; font-weight: 300; font-size: 1em; margin-left: -10px">
                                        <svg viewBox="0 0 16 16" class="bi bi-eye" fill="#888" xmlns="http://www.w3.org/2000/svg"
                                            data-toggle="popover" 
                                            data-content="Une ou plusieurs évaluations corrigées sont visibles.">
                                          <path fill-rule="evenodd" d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.134 13.134 0 0 0 1.66 2.043C4.12 11.332 5.88 12.5 8 12.5c2.12 0 3.879-1.168 5.168-2.457A13.134 13.134 0 0 0 14.828 8a13.133 13.133 0 0 0-1.66-2.043C11.879 4.668 10.119 3.5 8 3.5c-2.12 0-3.879 1.168-5.168 2.457A13.133 13.133 0 0 0 1.172 8z"/>
                                          <path fill-rule="evenodd" d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                        </svg>
                                    </div>

                                <? endif; ?>

                            <? endif; ?>
                        </div>

                        <div class="col-3" style="text-align: right">
                        
                            <?
                            /* ------------------------------------------------
                             *
                             * Ponderation des evaluations
                             *
                             * ------------------------------------------------ */ ?>

                            <? if ($this->config->item('evaluation_ponderation')) : ?>

                                <? 
                                    $p = array_key_exists($label, $ponderations) ? 
                                            number_format($ponderations[$label]['ponderation'], 2, ',', '') :
                                            NULL;
                                    
                                    $p_etudiant = array_key_exists($label, $ponderations) && $ponderations[$label]['etudiant'] ? 1 : 0;
                                ?>

                                <div class="btn btn-sm btn-light ajuster-ponderation stop-spinner" 
                                     style="margin-right: 15px; margin-top: 1px; border-color: #ddd; font-family: Lato; font-weight: 300"
                                    data-toggle="modal" 
                                    data-target="#modal-ajuster-ponderation"
                                    data-ponderation="<?= $p != NULL ? ($p_etudiant ? NULL : $p) : NULL; ?>"
                                    data-evaluation_id="<?= $e['evaluation_id']; ?>">
                                    <? if ($p !== NULL) : ?>
                                        <span style="font-weight: 400; color: <?= $ponderations[$label]['etudiant'] ? 'crimson' : 'inherit'; ?>" class="stop-spinner">
                                            <?=  $p . ' %'; ?>
                                        </span>
                                    <? else : ?>
                                        Pondération
                                    <? endif; ?>
                                    <i class="fa fa-edit stop-spinner" style="margin-left: 5px"></i>
                                </div>

                            <? endif; ?>

                            <div class="btn btn-sm" style="margin-right: -10px;">
                                <svg viewBox="0 0 16 16" class="bi-xs bi-chevron-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                  <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                            </div>

                        </div>

                    </div> <!-- .row -->

                </div>
            </a>

        <? endforeach; // evaluations ?>

        </div> <!-- .resultats-cours-contenu -->

    <? endforeach; // evaluations ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Le nombre de soumissions totales
     *
     * ------------------------------------------------------------------------ */ ?>

    <div style="margin-top: 20px; border-top: 2px solid #4CAF50; padding-top: 10px;">

        <?= $resultats_totaux; ?> soumission<?= $resultats_totaux > 1 ? 's' : ''; ?>

    </div>

    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>
</div> <!-- .row -->

</div> <!-- /.container -->
</div> <!-- #resultats -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJUSTER LA PONDERATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajuster-ponderation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit" style="margin-right: 5px"></i> Ajuster la pondération</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding: 25px;">
				<?= form_open(NULL, 
						array('id' => 'modal-ajuster-ponderation-form'), 
                        array('evaluation_id' => NULL, 'semestre_id' => $semestre_id)
					); ?>

                    <div style="font-size: 0.85em" class="mb-3 mb-2">
                        <i class="fa fa-info-circle" style="margin-right: 3px; color: #aaa"></i>
                        Les pondérations permettent aux étudiants de mieux évaluer leur performance.<br />
                        Les pondérations en <span style="color: crimson; font-weight: 600">rouge</span> ont été entrées par les étudiants.
                    </div>

                    Entrez la pondération de cette évaluation :

					<div class="form-row mt-3">
						<div class="col-md-3 mb-2">
							<div class="input-group">
								<input id="modal-ajuster-ponderation-ponderation" name="ponderation" type="text" class="form-control" style="text-align: right" required>
                                <div class="input-group-append">
									<span id="modal-corrections-changer-points-total" class="input-group-text" style="font-weight: 700">/ 100%</span>
								</div>
							</div>
						</div>  
					</div>
				</form>
      		</div>

            <div class="modal-footer">

                <div id="modal-ajuster-ponderation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save" style="margin-right: 5px;"></i> 
                    Ajuster la pondération
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>

                <div id="modal-effacer-ponderation-sauvegarde" class="btn btn-outline-danger spinnable">
                    <i class="fa fa-trash" style="margin-right: 5px;"></i> 
                    Effacer
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div> 

                <div class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-times" style="margin-right: 5px;"></i> 
                    Annuler
                </div>

      		</div>

    	</div>
  	</div>
</div>

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
                <div id="modal-effacer-soumission-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-trash"></i> Effacer
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
 * MODAL: RESET VUES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-reset-vues" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-undo" style="margin-right: 5px"></i> Remettre à zéro les vues</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-reset-vues-form'), 
						array('soumission_id' => NULL, 'soumission_reference' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

						Êtes-vous certain de vouloir remettre à zéro le nombre de vues de cette évalution corrigée ?

					</div>

				</form>
      		</div>

			<div class="modal-footer">
                <div id="modal-reset-vues-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-undo" style="margin-right: 5px;"></i> 
                    Remettre à zéro
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times" style="margin-right: 5px;"></i> 
                    Annuler
                </div>
      		</div>

    	</div>
  	</div>
</div>
