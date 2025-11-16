<?  
// ----------------------------------------------------------------
//
// REPONSES NUMERIQUES (TYPE 6)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-6">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">

        Réponse correcte

    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section" style="padding-top: 0">

        <? if (empty($reponses[$question_id])) : ?>

            <div class="ajouter-reponse-numerique" style="padding-top: 20px; margin-bottom: 5px;">

                 <div class="btn btn-outline-primary" 
                    data-question_id="<?= $question_id; ?>" 
                    data-question_type="<?= $q['question_type']; ?>"
                    data-toggle="modal" 
                    data-target="#modal-ajout-reponse-numerique">
                    <i class="fa fa-plus-circle"></i> Définir la réponse correcte</a>
                </div>

            </div>

        <? else : ?>

            <div class="reponses" data-question_id="<?= $question_id; ?>">

                <? 
                foreach($reponses[$question_id] as $r) : 

                    if ($r['question_id'] != $question_id)
                        continue;

                    $reponse_id = $r['reponse_id'];
                ?>

                <div class="reponses-table-wrap">

                    <table class="reponses-table">

                        <tr>
                            <td class="reponse-label" style="text-align: center; width: 50px; padding-right: 8px; border-right: 1px solid #7986CB;">
                                <span class="align-middle">
                                    <? // Il n'y a qu'une reponse correcte possible ?>
                                    <? if ($r['reponse_correcte']) : ?>
                                        <i class="fa fa-check-circle" style="color: limegreen"></i>
                                    <? endif; ?>
                                </span>
                            </td>
                            <td style="padding-left: 15px; padding-right: 10px;">
                                <?= filter_symbols(@$r['reponse_texte']); ?>
                            </td>

                            <td style="text-align: right">
                                <? if ($r['unites']) : ?>
                                    <?= @$r['unites']; ?>
                                <? endif; ?>
                            </td>

                            <? if (in_array('modifier', $permissions_question)) : ?>

                                <td style="width: 130px; text-align: right;">
                                    <div class="question-reponse-sauvegarde btn btn-outline-primary"
                                        data-toggle="modal" 
                                        data-target="#modal-modifier-reponse-numerique"
                                        data-reponse="<?= htmlentities(json_encode($r)); ?>"
                                        data-question_id="<?= $r['question_id']; ?>"
                                        data-reponse_id="<?= $r['reponse_id']; ?>">
                                        <i class="fa fa-edit" style="margin-right: 5px"></i>
                                        Modifier
                                    </div>
                                </td>

                            <? endif; ?>

                        </tr>

                    </table>

                </div> <!-- .reponses-table-wrap -->

                <? endforeach; ?>

                <small class="form-text text-muted" style="padding-top: 5px">
                    <i class="fa fa-exclamation-circle" style="color: #ccc; margin-right: 5px"></i>
                    Les étudiants peuvent entrer les décimales à l'aide de point (.) ou de virgule (,) à leur convenance.
                </small>

                <? 
                // ----------------------------------------------
                //
                // TOLERANCES
                //
                // ---------------------------------------------- ?>

                <div class="tolerances">

                    <div style="padding-top: 15px; font-weight: 600;">

                        Tolérance<?= count($tolerances[$question_id]) > 1 ? 's' : ''; ?> :

                    </div>

                    <? if ( ! empty($tolerances[$question_id])) : ?>

                        <div class="editeur-tolerance" style="border: 1px solid #ddd; padding: 5px; margin-top: 10px; margin-bottom: 15px">

                            <table style="width: 100%;">

                                <? foreach($tolerances[$question_id] as $t) : ?>

                                    <tr style="height: 45px">
                                        <td style="width: 100px; padding-left: 10px">
                                            <? if ($t['type'] == 1) : ?>
                                                <span class="badge badge-light" style="padding: 8px">Absolue</span>
                                            <? else : ?>
                                                <span class="badge badge-light" style="padding: 8px">Relative</span>
                                            <? endif; ?>
                                        </td>
                                        <td style="width: 175px;">
                                            Tolérance : &#177; <?= str_replace('.', ',', $t['tolerance']); ?>
                                            <?= $t['type'] == 2 ? '%' : ''; ?>
                                        </td>
                                        <td style="width: 175px">
                                            Pénalité : <?= $t['penalite']; ?> %
                                        </td>

                                        <? if (in_array('modifier', $permissions_question)) : ?>

                                            <td style="text-align: right">
                                                <div class="btn btn-outline-danger effacer-tolerance spinnable"
                                                    data-question_id="<?= $question_id; ?>"
                                                    data-reponse_id="<?= $reponse_id; ?>"
                                                    data-tolerance_id="<?= $t['tolerance_id']; ?>">
                                                    <i class="fa fa-trash" style="margin-right: 5px"></i> 
                                                    Effacer
                                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                                </div>
                                            </td>

                                        <? else : ?>

                                            <td></td>

                                        <? endif; // permissions pour effacer une tolerance ?>
                                    </tr>

                                <? endforeach; ?>

                            </table>

                        </div>

                    <? endif; ?>

                    <?
                    /* ---------------------------------------------------------
                     *
                     * Avertissement de ne pas utiliser deux types de tolerances
                     *
                     * --------------------------------------------------------- */ ?>

                    <?
                       if (
                            (array_search(1, array_column($tolerances[$question_id], 'type')) !== FALSE) &&
                            (array_search(2, array_column($tolerances[$question_id], 'type')) !== FALSE)
                          ) :
                    ?>
                        <div style="font-size: 0.85em; padding-top: 5px; padding-bottom: 10px; color: crimson;">
                            <i class="fa fa-exclamation-circle" style="color: dark-orange"></i>
                            Il est fortement déconseillé d'utiliser deux types de tolérances différentes (absolue et relative). Ceci pourrait mener à une correction imprévue.
                        </div>
                    <? endif; ?>

                    <?
                    /* ---------------------------------------------------------
                     *
                     * Ajouter une tolerance
                     *
                     * --------------------------------------------------------- */ ?>

                    <? if ($this->enseignant['enseignant_id'] == $evaluation['enseignant_id'] ||
                           $this->enseignant['enseignant_id'] == $q['ajout_par_enseignant_id'] ||
                             permis('editeur')
                          ) : 
                    ?>
                        <div class="ajouter-tolerance" style="margin-top: 10px">

                             <div class="btn btn-outline-primary" 
                                data-reponse_id="<?= $reponse_id; ?>"
                                data-question_id="<?= $question_id; ?>"
                                data-toggle="modal" 
                                data-target="#modal-ajout-tolerance">
                                <i class="fa fa-plus-circle"></i> Ajouter une tolérance</a>
                            </div>

                            <a class="btn btn-outline-secondary" target="_blank" href="<?= base_url() . 'outils/question6/' . $question_id; ?>"> 
                                <i class="fa fa-cog"></i> Tester cette question
                            </a>

                            <div class="btn btn-light" data-toggle="modal" data-target="#modal-info-notation-scientifique" style="color: #555; border-color: #ddd">
                                <i class="fa fa-info-circle"></i>
                                Notation scientifique
                            </div>

                        </div>

                    <? endif; // permissions pour tolerances ?>

                </div> <!-- /.tolerances -->

            </div> <!-- /.reponses -->

        <? endif; ?>

    </div> <!-- /.editeur-section-sous-section -->

</div> <!-- /.editeur-question-type-6 -->
