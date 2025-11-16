<?  
// ----------------------------------------------------------------
//
// REPONSES NUMERIQUES ENTIERES (TYPE 5)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-5">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">

        Réponse correcte

    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section" style="padding-top: 0">

        <? if (empty($reponses[$question_id])) : ?>

            <div class="ajouter-reponse-numerique-entiere" style="padding-top: 20px; padding-bottom: 5px;">

                 <div class="btn btn-outline-primary" 
                    data-question_id="<?= $question_id; ?>" 
                    data-question_type="<?= $q['question_type']; ?>"
                    data-toggle="modal" 
                    data-target="#modal-ajout-reponse-numerique-entiere">
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
                                    <? if ($r['reponse_correcte']) : ?>
                                        <i class="fa fa-check-circle" style="color: limegreen"></i>
                                    <? else : ?>
                                        <i class="fa fa-times-circle" style="color: crimson"></i>
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
                                        data-target="#modal-modifier-reponse-numerique-entiere"
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

            </div> <!-- /.reponses -->

        <? endif; // empty($reponses[$question_id]) ?>

    </div> <!-- /.editeur-section-sous-section -->

</div> <!-- /.editeur-question-type-5 -->
