<?
/* ----------------------------------------------------------------------------
 *
 * Statistiques d'une question
 *
 * ---------------------------------------------------------------------------- */ ?>

<? $requete = $requete ?? NULL; ?>

<div id="statistiques-question">
<div class="container-fluid">

<div id="soumissions-data" data-soumission_ids="<?= htmlspecialchars(serialize($soumission_ids)); ?>" data-groupe_no="<?= $groupe_no; ?>"></div>

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <?
        /* --------------------------------------------------------------
         *
         * BARRE DE DEFILEMENT HAUT
         *
         * -------------------------------------------------------------- */ ?>

        <? if ( ! empty($defilement_prec) || ! empty($defilement_suiv)) : ?>

            <div class="defilement defilement-haut">
                <div class="row">

                    <div class="col-5">
                        <a class="btn btn-sm btn-outline-primary" style="width: 26px" data-toggle="tooltip" title="Retour aux statistiques de l'évaluation" href="<?= $stats_retour_resultats_stats; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: -3px; margin-top: -2px" fill="currentColor" class="bi-xxs bi-chevron-bar-left" viewBox="0 0 16 16">
                              <path fill-rule="evenodd" d="M11.854 3.646a.5.5 0 0 1 0 .708L8.207 8l3.647 3.646a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0zM4.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5z"/>
                            </svg>
                        </a>
                        <? if ( ! empty($defilement_prec)) : ?>
                            <a href="<?= $defilement_prec_prem_url; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                            <a href="<?= $defilement_prec_url; ?>" class="btn btn-sm btn-outline-primary"
                               style="background: dodgerblue; color: #fff; width: 125px">
                                <i class="fa fa-angle-left" style="margin-right: 8px"></i>
                                précédente
                            </a>
                    <? else : ?>
                        <div class="btn btn-sm" style="color: #1565C0; cursor: default">
                            Barre de défilement
                            <i class="fa fa-info-circle" style="margin-left: 5px"
                               data-trigger="hover" 
                               data-toggle="popover" 
                               data-html="true"
                               data-placement="top"
                               data-content="L'ordre de défilement est déterminé par l'ordre affiché dans les statistiques."></i>
                        </div>
                    <? endif; ?>
                    </div>
            
                    <div class="col-2" style="text-align: center">
                        <? if ($defilement_total > 1) : ?>
                            <div class="btn btn-sm" style="color: dodgerblue; background: #fff; padding-left: 15px; padding-right: 15px;">
                                <?= $defilement_index; ?> / <?= $defilement_total; ?>
                            </div>
                        <? endif; ?>
                    </div>

                    <div class="col-5" style="text-align: right">
                        <? if ( ! empty($defilement_suiv)) : ?>
                            <a href="<?= $defilement_suiv_url; ?>" class="btn btn-sm btn-outline-primary" 
                               style="background: dodgerblue; color: #fff; width: 125px">
                                suivante
                                <i class="fa fa-angle-right" style="margin-left: 9px"></i>
                            </a>
                            <a href="<?= $defilement_suiv_dern_url; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        <? endif; ?>
                    </div>

                </div> <!-- .row -->
            </div> <!-- .defilement -->

        <? endif; ?>

        <?
        /* --------------------------------------------------------------------
         *
         * En-tete
         *
         * -------------------------------------------------------------------- */ ?>

        <h3>Statistiques d'une question</h3>

        <div class="space"></div>

        <?
        /* --------------------------------------------------------------------
         *
         * Image
         *
         * -------------------------------------------------------------------- */ ?>

        <? if ( ! empty($images) && array_key_exists($question_id, $images)) : ?>

            <div style="text-align: center; margin-bottom: 15px;">

                <? if ($images[$question_id]['s3']) : ?>
                    
                    <img src="<?= $this->config->item('s3_url', 'amazon') . 'evaluations/' . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

                <? else : ?>

                    <? if (file_exists($this->config->item('documents_path') . $images[$question_id]['doc_filename'])) : ?>

                        <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

                        <? if ( ! empty($images[$question_id]['doc_caption'])) : ?>

                            <p style="margin-top: 20px"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></p>

                        <? endif; ?>

                    <? else : ?>

                        <img width="120px" src="<?= base_url() . 'assets/images/image_non_disponible.png'; ?>"></img>

                    <? endif; ?>

                <? endif; ?>
            </div>

        <? endif; ?>

        <?
        /* --------------------------------------------------------------------
         *
         * Titre de la question
         *
         * -------------------------------------------------------------------- */ ?>

        <span style="padding: 5px 15px 5px 15px; color: #fff; background: #5C6BC0; font-size: 0.9em; font-family: Lato; font-weight: 300">
            <?= $this->config->item('questions_types')[$question['question_type']]['desc']; ?>
        </span>
        <div id="stats-question-titre">

            <div><?= _html_out($question['question_texte']); ?></div> 

        </div>

        <?
        /* --------------------------------------------------------------------
         *
         * Information sur la question
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="stats-question-info-titre">
    
            Information sur la question

        </div>
        <div id="stats-question-info">
            <ul>

                <li>Cours : <?= $cours['cours_nom'] . ' (' . $cours['cours_code'] . ')'; ?></li>

                <li>Evaluation ID : <a href="<?= base_url() . 'evaluations/editeur/' . $evaluation_id; ?>"><?= $evaluation_id; ?></a></li>
        
                <li>Question ID : <?= $question_id; ?></li>

                <? if ($groupe_no !== NULL) : ?>

                    <li>Groupe : <?= $groupe_no == 999 ? 'inconnu' : $groupe_no; ?></li>

                <? endif; ?>

            </ul>
        </div>

        <?
        /* ----------------------------------------------------------------
         *
         * Reponse correcte
         *
         * ---------------------------------------------------------------- */ ?>

        <?  if ( ! in_array($question['question_type'], array(2, 3, 9, 10, 12)) && ! @$question['sondage']) : ?>

            <div id="stats-question-reponse-correcte-titre">

                Réponse correcte
                <i class="fa fa-check-square" style="margin-left: 7px; color: #1A237E"></i>

            </div>

            <div id="stats-question-reponse-correcte" class="rc-qtype-<?= $question['question_type']; ?>">

                <? if (is_array($reponse_correcte)) : ?>

                    <?= '<li>' . implode('</li><li>', $reponse_correcte) . '</li>'; ?>

                <? else : ?>

                    <?= $reponse_correcte; ?>

                <? endif; ?>

            </div>

        <? endif; ?>

        <?
        /* --------------------------------------------------------------------
         *
         * Cette question n'a jamais été repondue par vos étudiants
         *
         * -------------------------------------------------------------------- */ ?>

        <? if (empty($soumissions)) : ?>

            <i class="fa fa-exclamation-circle"></i> Cette question n'a jamais été demandée dans vos évaluations.

        <? else : ?>

            <?
            /* --------------------------------------------------------------------
             *
             * Tableau des resultats par question
             *
             * -------------------------------------------------------------------- */ ?>

            <div id="stats-reponses-titre">
        
                Les points obtenus à cette question

            </div>

            <div id="stats-reponses">
        
                <table class="table" style="margin: 0; font-size: 0.9em">
                    <thead>
                        <tr>
                            <td style="width: 300px">
                                Étudiant
                                <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
                            </td>
                            <td style="width: 100px; text-align: center">Semestre</td>
                            <td style="width: 100px; text-align: center">Référence</td>

                            <?
                            /* 
                             * Question a reponse numerique par equation (les reponses correctes sont differentes d'un etudiant a l'autre
                             *
                             */ ?>

                            <? if (in_array($question['question_type'], array(3, 9))) : ?>

                                <td style="text-align: center">Réponse correcte</td>

                            <? endif; ?>

                            <td style="text-align: center">Réponse</td>
                            <td style="width: 180px; text-align: right">
                                Points
                                <span class="tri-button" data-clef="clef_tri_points_q" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                            </td>
                            <td style="width: 180px; text-align: right">
                                Points soumission
                                <span class="tri-button" data-clef="clef_tri_points" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        <? 

                        $points_obtenus_totaux = 0; // la somme des points obtenus a cette question (pour tous les etudiants)
                        $points_totaux = 0;         // la somme des points de la question pour cette question (pour tous les etudiants)
                             
                        foreach($questions as $q) : 

                            $question_id = $q['question_id'];

                            $s = $soumissions[$q['soumission_id']];

                            $cours_data = json_decode(gzuncompress($s['cours_data_gz']), TRUE);

                            //
                            // Ajustements
                            //

                            $ajustements = ! empty($s['ajustements_data']) ? unserialize($s['ajustements_data']) : array();

                            $points_obtenus = array_key_exists('total', $ajustements) ? $ajustements['total'] : $s['points_obtenus'];

                            //
                            // Sondage
                            //

                            if ( ! array_key_exists('sondage', $q) || ! $q['sondage'])
                            {
                                $question_points_obtenus = array_key_exists($question_id, $ajustements) ? $ajustements[$question_id]['points_obtenus'] : $q['points_obtenus'];
                                $question_reussie        = ($question_points_obtenus == $q['question_points']) ? TRUE : FALSE;
                                $ajustement 		     = array_key_exists($question_id, $ajustements) ? 1 : 0;
                                $q['sondage']            = 0; // retrocompatibilite

                                $points_obtenus_totaux   += $question_points_obtenus;
                                $points_totaux           += $q['question_points'];
                            }
                            else
                            {
                                $ajustement = 0;
                                $question_reussie = TRUE;
                            }
                        ?>
                            <tr data-clef_tri_points_q="<?= ! $q['sondage'] ? $question_points_obtenus/$q['question_points']*100 : 0; ?>"
                                data-clef_tri_points="<?= ! $q['sondage'] ? $s['points_obtenus'] / $s['points_evaluation'] * 100 : 0; ?>"
                                data-clef_tri_nom="<?= (array_key_exists($s['numero_da'], $numeros_da) ? strtolower(strip_accents($numeros_da[$s['numero_da']])) : 'zzz'); ?>">

                                <td>
                                    <span style="cursor: pointer"
                                          data-trigger="hover"
                                          data-toggle="popover"
                                          data-html="true"
                                          data-placement="top"
                                          data-content="<?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?> : <?= $s['numero_da']; ?>">
                                        <?= $s['prenom_nom']; ?>
                                    </span>
                                
                                    <? if ( ! empty($s['etudiant_id'])) : ?>
                                        <i class="fa fa-user" style="margin-left: 3px; color: #aaa"></i>
                                    <? endif; ?>
                                </td>
                                <td style="text-align: center"><?= $cours_data['semestre_code'] ?? 'N/D'; ?></td>
                                <td class="mono" style="text-align: center"><a href="<?= base_url(). 'consulter/' . $s['soumission_reference']; ?>"><?= $s['soumission_reference']; ?></a></td>

                                <?
                                /* 
                                 * Question a reponse numerique par equation (les reponses correctes sont differentes d'un etudiant a l'autre
                                 *
                                 */ ?>

                                <? if (in_array($question['question_type'], array(3, 9))) : ?>

                                    <td style="text-align: center"><?= $q['reponse_correcte_texte']; ?></td>

                                <? endif; ?>
                                
                                <td style="text-align: center">
                                    <? if (in_array($question['question_type'], array(4, 11)) && is_array($q['reponse_repondue_texte'])) : ?>

                                        <span style="border: 1px solid #E8EAF6; padding: 5px 7px 5px 7px; border-radius: 3px; background: #E8EAF6; font-family: Lato; font-weight: 300; font-size: 0.95em; cursor: pointer"
                                              data-trigger="hover" 
                                              data-toggle="popover" 
                                              data-html="true"
                                              data-placement="top"
                                              data-content="<?= '<li>' . implode('</li><li>', $q['reponse_repondue_texte']) . '</li>'; ?>">
                                            Montrer les choix sélectionnés
                                            <i class="fa fa-angle-right" style="margin-left: 5px"></i>
                                        </span>

                                    <? elseif (in_array($question['question_type'], array(2, 12))) : ?>

                                        <?= $q['reponse_repondue']; ?>

                                    <? elseif (in_array($question['question_type'], array(10))) : ?>

                                        <? if ($q['reponse_repondue']) : ?>

                                            Un ou plusieurs documents

                                        <? else : ?>

                                            Cette question n'a pas été répondue.

                                        <? endif; ?>
                                        
                                    <? else : ?>

                                        <?= $q['reponse_repondue_texte']; ?>

                                    <? endif; ?>
                                </td>
                                <td style="text-align: right; color: <?= $question_reussie ? 'inherit' : 'crimson'; ?>">

                                    <? if ( ! $q['sondage']) : ?>

                                        <?= my_number_format($question_points_obtenus) . ' / ' . my_number_format($q['question_points']); ?> <span style="padding-left: 10px">(<?= number_format($question_points_obtenus / $q['question_points'] * 100)?>%)</span>

                                        <a href="#" data-toggle="modal" data-target="#modal-corrections-changer-points" 
                                            data-soumission_id="<?= $s['soumission_id']; ?>"
                                            data-soumission_reference="<?= $s['soumission_reference']; ?>"
                                            data-question_id="<?= $q['question_id']; ?>"
                                            data-ajustement="<?= $ajustement; ?>"
                                            data-points_obtenus="<?= my_number_format($question_points_obtenus); ?>"
                                            data-question_points="<?= my_number_format($q['question_points']); ?>">
                                            <i class="fa fa-edit" style="margin-left: 5px; color: <?= $ajustement ? 'dodgerblue' : '#aaa;'; ?>"></i>
                                        </a>

                                    <? endif; ?>

                                </td>
                                <td style="text-align: right">

                                    <? if ($s['points_evaluation'] > 0) : ?>

                                        <?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?> 
                                        <span style="padding-left: 10px">(<?= number_format($points_obtenus / $s['points_evaluation'] * 100)?>%)</span>

                                    <? endif; ?>
                                </td>
                            </tr>

                        <? endforeach; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Pourcentage moyen de la question
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if ( ! $q['sondage']) : ?>

                            <tr style="font-weight: 600">
                                <td colspan="9" style="border-top: 1px solid #3F51B5; background: #fff;">
                                    Pourcentage moyen de la question : 
                                    <span style="padding-left: 5px"><?= number_format($points_obtenus_totaux / $points_totaux * 100); ?>%</span>
                                </td>
                            </tr>

                        <? endif; ?>

                    </tbody>
                </table>

            </div> <!-- #stats-reponses -->

        <? endif; // empty($soumissions) ?>

        <?
        /* --------------------------------------------------------------
         *
         * BARRE DE DEFILEMENT BAS
         *
         * -------------------------------------------------------------- */ ?>

        <? if ( ! empty($defilement_prec) || ! empty($defilement_suiv)) : ?>

            <div class="defilement defilement-bas">
                <div class="row">

                    <div class="col-6">
                        <a class="btn btn-sm btn-outline-primary" style="width: 26px" data-toggle="tooltip" title="Retour aux statistiques de l'évaluation" href="<?= $stats_retour_resultats_stats; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: -3px; margin-top: -2px" fill="currentColor" class="bi-xxs bi-chevron-bar-left" viewBox="0 0 16 16">
                              <path fill-rule="evenodd" d="M11.854 3.646a.5.5 0 0 1 0 .708L8.207 8l3.647 3.646a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0zM4.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5z"/>
                            </svg>
                        </a>
                        <? if ( ! empty($defilement_prec)) : ?>
                            <a href="<?= $defilement_prec_prem_url; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                            <a href="<?= $defilement_prec_url; ?>" class="btn btn-sm btn-outline-primary"
                               style="background: dodgerblue; color: #fff; width: 125px">
                                <i class="fa fa-angle-left" style="margin-right: 8px"></i>
                                précédente
                            </a>
                        <? endif; ?>
                    </div>

                    <div class="col-6" style="text-align: right">
                        <? if ( ! empty($defilement_suiv)) : ?>
                            <a href="<?= $defilement_suiv_url; ?>" class="btn btn-sm btn-outline-primary" 
                               style="background: dodgerblue; color: #fff; width: 125px">
                                suivante
                                <i class="fa fa-angle-right" style="margin-left: 9px"></i>
                            </a>
                            <a href="<?= $defilement_suiv_dern_url; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        <? endif; ?>
                    </div>

                </div> <!-- .row -->
            </div> <!-- .defilement -->

        <? endif; ?>

        <div class="space"></div>

        <a class="btn btn-outline-secondary" href="<?= $_SESSION['stats_retour_resultats_stats' . $requete]; ?>">
            <i class="fa fa-undo" style="margin-right: 3px"></i> 
            Retour aux statistiques de l'évaluation
        </a>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container -->
</div> <!-- #statistiques-question -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: CHANGER LES POINTS D'UNE QUESTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-corrections-changer-points" class="modal" tabindex="0" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajuster les points obtenus à une question</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding-left: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-corrections-changer-points-form'), 
                        array(
                            'soumission_id'        => NULL,
                            'soumission_reference' => NULL,
                            'question_id'          => NULL,
                            'points_obtenus'       => NULL,
                            'points'               => NULL
                        )
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="hspace"></div>

					<label>Ajustement des points obtenus à la
                    <span class="badge badge-pill" style="background: #ddd; color: #222; font-size: 0.8em; font-weight: normal; margin-left: 2px">Question ID
						<span id="modal-corrections-changer-points-question-id"></span>
					</span>
					</label> :

					<div class="hspace"></div>

					<div class="form-row">
						<div class="col-md-3 mb-2">
							<div class="input-group">
								<input id="modal-corrections-changer-points-obtenus" name="nouveau_points_obtenus" type="text" class="form-control" style="text-align: right" required>
								<div class="input-group-append">
									<span id="modal-corrections-changer-points-total" class="input-group-text" style="font-weight: 700"></span>
								</div>
							</div>
						</div>  
					</div>

					<div class="hspace"></div>

					<div id="modal-corrections-changer-points-obtenus-invalide" style="font-size: 0.85em; color: crimson" class="d-none">
						<i class="fa fa-exclamation-circle"></i> Les points obtenus ne peuvent être supérieurs au pointage maximum alloué pour la question.
					</div>
					<div style="font-size: 0.85em">
						<i class="fa fa-info-circle" style="color: #aaa; margin-right: 5px"></i> Ceci n'affectera pas les autres soumissions.
					</div>

					<div class="hspace"></div>

				</form>
      		</div>
      
            <div class="modal-footer">
                    <div id="modal-corrections-changer-points-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Ajuster les points
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div id="modal-corrections-effacer-ajustement-sauvegarde" class="btn btn-danger" data-dismiss="modal">
                        <i class="fa fa-trash"></i> Effacer l'ajustement
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </div>
            </div>

    	</div>
  	</div>
</div>
