
<script src="<?= base_url() . 'assets/js/bienvenue_etudiants.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="bienvenue" data-groupe_id="<?= $this->groupe_id; ?>">
<div class="container-fluid">

<div id="semestre-data" class="d-none" data-semestre_id="<?= $this->semestre_id; ?>"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div style="font-size: 0.9em; color: #444; font-weight: 200; background: #f3f3f3; padding: 7px 10px 7px 10px; border-radius: 5px">
        <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 5px;" fill="crimson" class="bi-xs bi-exclamation-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
        </svg> 
        Vous n'êtes pas connecté.
    </div>

	<div class="tspace"></div>

    <?
    /* ---------------------------------------------------------------
     *
     * EVALUATION EN COURS DE REDACTION --- HIDDEN ---
     *
     * --------------------------------------------------------------- */ ?>

	<div id="evaluation-en-cours-redaction" class="d-none">
    <? if ( ! empty($evaluations_en_cours)) : ?>

        <h4>Évaluation<?= count($evaluations_en_cours) > 1 ? 's' : ''; ?> en cours de rédaction</h4>

        <div class="space"></div>

        <div class="evaluations-en-cours">

            <table class="table table-sm table-borderless" style="margin: 0;">
                <tbody>
                    <tr>
                        <td style="width: 75px; text-align: center">Cours</td>
                        <td>Titre</td>
                        <td class="d-none d-lg-table-cell" style="width: 175px">Débutée le</td>
                        <td style="width: 90px; text-align: center" class="d-none d-sm-table-cell">Effacer</td>
                        <td style="width: 90px; text-align: center">Continuer</td>
                    </tr>
                    <? foreach($evaluations_en_cours as $e) : ?>
                        <tr>
                            <td style="text-align: center; cursor: default" data-toggle="tooltip" data-placement="top" title="<?= $e['cours_nom']; ?>">
                                <?= $e['cours_code_court']; ?>
                            </td>
                            <td><?= $e['evaluation_titre']; ?></td>
                            <td class="d-none d-lg-table-cell"><?= $e['soumission_debut_date']; ?></td>
                            <td class="d-none d-sm-table-cell" style="text-align: center">
                                <span class="effacer-traces-redaction" style="cursor: pointer" data-evaluation_reference="<?= $e['evaluation_reference']; ?>">
                                    <i class="fa fa-trash fa-lg" style="color: crimson;"></i>
                                </span>
                            </td>
                            <td style="text-align: center">
                                <a href="<?= base_url() . 'evaluation/' . $e['evaluation_reference']; ?>">
                                    <i class="fa fa-arrow-circle-right fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        </div>

        <div class="dspace"></div>

    <? endif; ?>
	</div> <!-- #evaluation-en-cours-redaction -->

    <?
    /* ---------------------------------------------------------------
     *
     * CHOISISSEZ VOTRE EVALUATION --- HIDDEN ---
     *
     * --------------------------------------------------------------- */ ?>

	<div id="choisissez-votre-evaluation" class="d-none">
    <h4>Choisissez votre évaluation :</h4>

    <div class="space"></div>

    <?= form_open(); ?>

		<? 
		/* ---------------------------------------------------------------
		 * 
         * Quel cours ?
		 *
         * --------------------------------------------------------------- */ ?>

        <? if (empty($cours_ids)) : ?>

            <div style="font-weight: 300">
                <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                Aucune évaluation trouvée
            </div>
            
        <? else : ?>

            <div id="choisir-cours">

                <div class="form-group">
                    <label><i class="fa fa-angle-right"></i> Quel cours?</label>
                    <select id="choisir-cours-select" class="form-control">
                        <option value="0"></option>
                        <? foreach($cours_ids as $cours_id) : ?>
                            <option value="<?= $cours_id; ?>">
                                <?= $cours_raw[$cours_id]['cours_nom_court'] . ' (' . $cours_raw[$cours_id]['cours_code'] . ')'; ?>
                            </option>
                        <? endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="hspace"></div>

        <? endif; ?>

        <i id="choisir-cours-spinner" class="fa fa-spin fa-lg fa-circle-o-notch d-none" style="color: dodgerblue"></i>

		<? 
		/* ---------------------------------------------------------------
		 * 
         * Quel enseignant ?
		 *
         * --------------------------------------------------------------- */ ?>

		<div id="choisir-enseignant" class="d-none">

			<div class="form-group">
				<label><i class="fa fa-angle-right"></i> Quel enseignant?</label>
				<select id="choisir-enseignant-select" class="form-control">
				</select>
			</div>

			<div class="hspace"></div>

		</div>

        <i id="choisir-enseignant-spinner" class="fa fa-spin fa-lg fa-circle-o-notch d-none" style="color: dodgerblue"></i>

		<? 
		/* ---------------------------------------------------------------
		 * 
         * Quelle evaluation ?
		 *
         * --------------------------------------------------------------- */ ?>

		<div id="choisir-evaluation" class="d-none">

			<div class="form-group">
				<label><i class="fa fa-angle-right"></i> Quelle évaluation?</label>
				<select id="choisir-evaluation-select" class="form-control">
				</select>
			</div>

			<div class="space"></div>

		</div>

		<? 
		/* ---------------------------------------------------------------
		 * 
         * Aller a l'evaluation
		 *
         * --------------------------------------------------------------- */ ?>

        <div id="aller-evaluation" class="btn btn-primary d-none spinnable"
            data-evaluation_reference="">
            Aller à l'évaluation
            <i class="fa fa-angle-right" style="margin-left: 5px"></i>
            <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
        </div>

        <div id="aller-evaluation-temps-limite" class="btn btn-primary d-none" 
            data-toggle="modal" 
            data-target="#modal-etudiants-temps-limite"
            data-evaluation_reference="">
            Aller à l'évaluation
            <i class="fa fa-angle-right" style="margin-left: 5px"></i>
        </div>

    </form>
	</div> <!-- #choisissez-votre-evaluation -->

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AVERTISSEMENT DE TEMPS LIMITE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-etudiants-temps-limite" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="liste">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title" style="font-weight: 300">
                    Avertissement de temps limite
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, array('id' => 'modal-etudiants-temps-limite-form')); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="crimson" class="bi bi-xs bi-exclamation-circle" viewBox="0 0 18 18" style="margin-right: 5px">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                        </svg> 
                        Cette évaluation comporte un temps limite pour la terminer.
						<br/ ><br />
                        Voulez-vous débuter cette évaluation maintenant ?

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="modal-etudiants-temps-limite-debuter" data-evaluation_reference="" class="btn btn-primary spinnable">
                    Débuter
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-outline-danger" data-dismiss="modal">
                    Plus tard
                </div>
      		</div>
    	</div>
  	</div>
</div>
