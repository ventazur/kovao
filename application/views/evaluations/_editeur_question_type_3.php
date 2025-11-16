<?  
// ----------------------------------------------------------------
//
// REPONSES A COEFFICIENTS VARIABLES (TYPE 3)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-3">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">

        Réponses générées par des équations

    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section" style="padding-top: 0">

        <?
        // ----------------------------------------------------------------------------
        //
        // Il doit y avoir des variables de definies pour utiliser ce type de question.
        // 
        // ---------------------------------------------------------------------------- ?>

        <? if ( ! $variables_presentes) : ?>

            <div style="font-size: 0.9em; padding-top: 25px; padding-bottom: 10px">
                <i class="fa fa-exclamation-circle" style="color: crimson;"></i> Vous devez créer des variables avant de créer des équations.
            </div>

        <? else : ?>

            <? if (empty($reponses[$question_id])) : ?>

                <div style="font-size: 0.9em; padding-top: 20px; padding-bottom: 10px;">
                    <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
                    Aucune équation définie
                </div>

            <? else : ?>

                <div class="reponses" data-question_id="<?= $question_id; ?>">

                    <? 
                        $j = 0; 
                        $reponse_correcte_presente = 0;

                        foreach($reponses[$question_id] as $r) : 

                        if ($r['question_id'] != $question_id)
                            continue;

                        $reponse_id = $r['reponse_id'];
                        $reponse_correcte_presente += $r['reponse_correcte'];

                        $j++;
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
                                    <?= @$r['reponse_texte']; ?>
                                </td>

                                <td style="text-align: right">

                                    <?
                                    /* ---------------------------------------
                                     *
                                     * Unites
                                     *
                                     * --------------------------------------- */ ?>

                                    <? if ($r['unites']) : ?>

                                        <?= @$r['unites']; ?>

                                    <? endif; ?>
                    
                                    <?
                                    /* ---------------------------------------
                                     *
                                     * Chiffres significatifs
                                     *
                                     * --------------------------------------- */ ?>

                                    <span style="margin-left: 10px">
                                        <? if (empty($r['cs']) || $r['cs'] == 0) : ?>

                                            (auto CS)

                                        <? elseif ($r['cs'] != 99) : ?>

                                            (<?= $r['cs']; ?> CS)

                                        <? else : ?>

                                            (<i class="fa fa-times" style="color: #999"></i> CS)

                                        <? endif; ?>
                                    </span>

                                    <?
                                    /* ---------------------------------------
                                     *
                                     * Notation scientifique
                                     *
                                     * --------------------------------------- */ ?>

                                    <? if ( ! empty($r['notsci'])) : ?>

                                        (NS)

                                    <? endif; ?>

                                </td>

                                <? if (in_array('modifier', $permissions_question)) : ?>

                                    <td style="width: 130px; text-align: right;">
                                        <div class="question-equation-sauvegarde btn btn-outline-primary" 
                                            data-toggle="modal" 
                                            data-target="#modal-modifier-equation"
                                            data-reponse="<?= htmlentities(json_encode($r)); ?>"
                                            data-question_id="<?= $r['question_id']; ?>"
                                            data-reponse_id="<?= $r['reponse_id']; ?>">
                                            <i class="fa fa-edit" style="margin-right: 5px"></i>
                                            Modifier
                                            <i class="question-equation-sauvegarde-action fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                                        </div>
                                    </td>
                                <? endif; ?>

                            </tr>

                        </table>

                    </div> <!-- .reponses-table-wrap -->

                    <? endforeach; ?>

                </div> <!-- .reponses -->

                <div style="margin-top: 15px; color: #777; font-size: 0.9em;">
                    <i class="fa fa-exclamation-circle" style="color: #777; margin-right: 5px;"></i> 
                    Les chiffres significatifs doivent être fixés manuellement dans le cas d'une addition ou d'une soustraction (ex. 40-1=40 si aucun CS fixé).
                </div>

                <? if (count($reponses[$question_id]) < 2) : ?>

                    <div style="font-size: 0.9em; padding-top: 25px; padding-bottom: 10px;">
                        <i class="fa fa-exclamation-circle" style="color: darkorange; margin-right: 5px"></i>
                        Cette question nécessite au moins deux réponses.
                    </div>

                <? endif; ?>


                <? if (in_array($q['question_type'], array(3)) && (count($reponses[$question_id]) > 1) && $reponse_correcte_presente == 0) : ?>

                    <div style="font-size: 0.9em; padding-top: 25px; padding-bottom: 10px;">
                        <i class="fa fa-exclamation-circle" style="color: limegreen; margin-right: 5px"></i>
                        Cette question nécessite au moins une réponse correcte.
                    </div>

                <? endif; ?>

            <? endif; // ! empty($reponses[$question_id]) ?>


            <? if (in_array('modifier', $permissions_question)) : ?>

               <?  
                // -------------------------------------------------------------
                //
                // Ajouter une equation
                // 
                // ------------------------------------------------------------- ?>

                <div class="ajouter-reponse" style="margin-top: 15px;">

                     <div class="btn btn-outline-primary" 
                        data-question_id="<?= $question_id; ?>" 
                        data-question_type="<?= $q['question_type']; ?>"
                        data-toggle="modal" 
                        data-target="#modal-ajout-equation">
                        <i class="fa fa-plus-circle"></i> Ajouter une équation</a>
                    </div>

                </div>

            <? else : ?>

                <? // Ne peut pas modifier uen question ?>

                <div class="hspace"></div>

            <? endif; ?>

        <? endif; // variables presentes ?>

    </div> <!-- /.editeur-section-sous-section -->

</div> <!-- /.editeur-question-type-3 -->
