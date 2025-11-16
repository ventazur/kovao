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
                <div class="col-1 cours-liste-toggle-btn mr-2 mr-md-0" style="text-align: right;">
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
    
            <a href="<?= base_url() . 'resultats/evaluation/' . $e['evaluation_id'] . '/semestre/' . $e['semestre_id']; ?>" class="resultats-evaluation-titre-lien spinnable">

                <div class="resultats-evaluation-titre">

                    <div class="row">
                        <div class="col">

                            <span class="d-none d-sm-inline" style="margin-right: 10px; color: #bbb; font-weight: 300"><?= $cours_raw[$cours_id]['cours_code_court']; ?></span>

                            <?= $e['evaluation_titre']; ?>

                            <span style="color: #aaa; margin-left: 5px; font-weight: 300">
                                (<?= $e['count']; ?>)
                            </span>

                        </div>
                        <div class="col-1" style="text-align: right">

                            <i class="fa fa-angle-right"></i>
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>

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
