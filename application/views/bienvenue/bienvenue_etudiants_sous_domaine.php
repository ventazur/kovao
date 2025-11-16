<?
/* ----------------------------------------------------------------------------
 *
 * BIENVENUE ETUDIANTS (SOUS DOMAINE)
 *
 * ---------------------------------------------------------------------------- */ ?>

<script src="<?= base_url() . 'assets/js/bienvenue_etudiants.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="bienvenue" data-groupe_id="<?= $this->groupe_id; ?>">
<div class="container-fluid">

<div id="semestre-data" class="d-none" data-semestre_id="<?= $this->semestre_id; ?>"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4 style="font-weight: 200; color: <?= $this->etudiant['genre'] == 'F' ? '#7B1FA2' : '#1976D2'; ?>">
        <? if (date('G') > 17 || date('G') < 5) : ?>
            Bonsoir 
        <? else : ?>
            Bonjour
        <? endif; ?>
        <?= $this->etudiant['prenom']; ?>
    </h4>

    <div class="space"></div>

    <? if ( $this->est_etudiant && array_key_exists('numero_da', $this->etudiant) && empty($this->etudiant['numero_da'])) : ?>

        <div class="mb-4">
            <i class="fa fa-lightbulb-o fa-lg" style="margin-right: 5px; color: dodgerblue;"></i>
            Veuillez entrer votre
            <strong><?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?></strong>
            dans votre <a href="<?= base_url() . 'profil'; ?>">Profil</a>.
        </div>

        <div class="hspace"></div>

    <? endif; ?>

    <?
    /* ---------------------------------------------------------------
     *
     * EVALUATION EN REDACTION
     *
     * --------------------------------------------------------------- */ ?>

    <? if ( ! empty($evaluations_en_cours)) : ?>

        <h4>Évaluation<?= count($evaluations_en_cours) > 1 ? 's' : ''; ?> en rédaction :</h4>

        <div class="space"></div>

        <div class="evaluations-en-cours">

            <table class="table table-sm table-borderless" style="margin: 0;">
                <tbody>
                    <tr>
                        <td style="width: 75px; text-align: center">Cours</td>
                        <td>Titre de l'évaluation</td>
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
                                <a class="btn btn-sm btn-primary" href="<?= base_url() . 'evaluation/' . $e['evaluation_reference']; ?>" 
                                   style="margin-top: -2px; font-family: Lato; font-weight: 300">
                                    Aller <i class="fa fa-angle-right fa-lg" style="margin-left: 7px"></i>
                                </a>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        </div>

        <div class="dspace"></div>

    <? endif; ?>

    <?
    /* ---------------------------------------------------------------
     *
     * EVALUATIONS À REDIGER (Anciennement: SUGGESTIONS D'EVALUATION)
     *
     * --------------------------------------------------------------- */ ?>

    <h4>Évaluation<?= $suggestions_compte > 1 ? 's' : ''; ?> à rédiger<?= $suggestions_compte > 1 ? 's' : ''; ?> :</h4>

    <div class="space"></div>

    <? if (empty($suggestions)) : ?>

        <div>
        
            <svg style="" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="crimson" class="bi bi-exclamation-circle" viewBox="0 0 20 20">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg>

            Aucune évaluation à rédiger pour l'instant

        </div>

    <? else : ?>

        <div class="suggestions">

            <table class="table table-sm table-borderless" style="margin: 0;">
                <tbody>
                    <tr>
                        <td style="width: 75px; text-align: center">Cours</td>
                        <td colspan="2">Titre de l'évaluation</td>
                    </tr>

                    <? foreach($suggestions as $cours_id => $c) : ?>

                        <? foreach($c as $evaluation_id => $e) : ?>

                            <tr>
                                <td style="text-align: center; cursor: default" data-toggle="tooltip" data-placement="top" title="<?= $e['cours_nom']; ?>">
                                    <?= $e['cours_code_court']; ?>
                                </td>
                                <td>
                                    <?= $e['evaluation_titre']; ?>
                                    <? if ($e['temps_limite'] > 0) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="crimson" class="bi bi-sm bi-clock" viewBox="0 0 18 18" style="margin-left: 5px;">
                                          <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                          <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                        </svg>
                                        <span style="color: crimson">
                                            <?= $e['temps_limite'] . ' minute' . ($e['temps_limite'] > 1 ? 's' : ''); ?> 
                                        </span>
                                    <? endif; ?>
                                </td>
                                <td style="width: 100px; text-align: right; padding-right: 7px">
                                    <a class="btn btn-sm btn-primary" href="<?= base_url() . 'evaluation/' . $e['evaluation_reference']; ?>" 
                                       style="margin-top: -2px; margin-right: 8px; font-family: Lato; font-weight: 300">
                                        Aller <i class="fa fa-angle-right" style="margin-left: 7px"></i>
                                    </a>
                                </td>
                            </tr>
                        <? endforeach; ?>

                    <? endforeach; ?>
                </tbody>
            </table>

        </div> <!-- .suggestion -->

        <div class="d-none" style="font-size: 0.85em; margin-top: 10px">
            <table>
                <tbody>
                    <tr>
                        <td style="width: 15px"><i class="fa fa-exclamation-circle"></i></td>
                        <td style="color: #777">
                            Vos évaluations à rédiger pourraient ne pas apparaître ci-haut si votre enseignante ou enseignant n'a pas importé ses listes d'élèves.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    <? endif; ?>

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
