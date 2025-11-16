<?  
// ----------------------------------------------------------------
//
// REPONSE LITTERALE COURTE (TYPE 7)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-7">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">

        Réponse<?= count($reponses[$question_id]) > 1 ? 's' : ''; ?> acceptée<?= count($reponses[$question_id]) > 1 ? 's' : ''; ?>
        (<?= count($reponses[$question_id]); ?>)

    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section" style="padding-top: 0">

        <? if (empty($reponses[$question_id])) : ?>

            <div class="ajouter-reponse-litterale-courte" style="padding-top: 20px; padding-bottom: 5px;">

                 <div class="btn btn-outline-primary" 
                    data-question_id="<?= $question_id; ?>" 
                    data-question_type="<?= $q['question_type']; ?>"
                    data-toggle="modal" 
                    data-target="#modal-ajout-reponse-litterale-courte">
                    <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> 
                    Définir la réponse acceptée
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
                                            data-target="#modal-modifier-reponse-litterale-courte"
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

                <div class="ajouter-reponse-litterale-courte" style="padding-top: 15px; padding-bottom: 5px;">

                     <div class="btn btn-outline-primary" 
                        data-question_id="<?= $question_id; ?>" 
                        data-question_type="<?= $q['question_type']; ?>"
                        data-toggle="modal" 
                        data-target="#modal-ajout-reponse-litterale-courte">
                        <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> 
                        Définir une autre réponse acceptée
                    </div>

                </div>

                <? 
                // ----------------------------------------------
                //
                // SIMILARITE
                //
                // ---------------------------------------------- ?>

                <div class="similarite">

                    <div style="padding-top: 10px; font-family: Lato; font-weight: 400;">

                        Paramètres :

                    </div>

                    <div class="similarite-parametres" style="border: 1px solid #ddd; padding: 3px; margin-top: 10px; margin-bottom: 15px;">

                        <table style="width: 100%;">

                            <tr style="height: 45px">

                                <td style="padding-left: 10px; width: 175px;">
                                    Similarité
                                    = <?= $similarite[$question_id]['similarite']; ?>
                                    %
                                </td>

                                <? if (in_array('modifier', $permissions_question)) : ?>

                                    <td rowspan="2" style="text-align: right; padding-right: 2px">
                                        <div class="btn btn-outline-primary modifier-reponse-litterale-courte-parametres"
                                            data-toggle="modal"
                                            data-target="#modal-modifier-reponse-litterale-courte-parametres"
                                            data-question_id="<?= $question_id; ?>"
                                            data-reponse_id="<?= $reponse_id; ?>"
                                            data-similarite="<?= $similarite[$question_id]['similarite']; ?>">
                                            <i class="fa fa-edit" style="margin-right: 5px"></i> 
                                            Modifier la similarité
                                            <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                        </div>
                                    </td>

                                <? else : ?>

                                    <td rowspan="2" style="padding-left: 10px; width: 175px;"></td>

                                <? endif; ?>
                            </tr>

                        </table>

                    </div> <!-- .similarite-parametres -->

                    <? if ($this->enseignant['enseignant_id'] == $evaluation['enseignant_id'] ||
                           $this->enseignant['enseignant_id'] == $q['ajout_par_enseignant_id'] ||
                             permis('editeur')
                          ) : 
                    ?>
                            <a class="btn btn-outline-secondary" target="_blank" href="<?= base_url() . 'outils/question7/' . $question_id; ?>"> 
                                <i class="fa fa-cog"></i> Tester la question
                            </a>
                    <? endif; ?>

                </div> <!-- /.similarite -->

            </div> <!-- /.reponses -->

        <? endif; ?>

    </div> <!-- /.editeur-section-sous-section -->

</div> <!-- .editeur-question-type-7 -->
