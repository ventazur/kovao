<?  
// ----------------------------------------------------------------
//
// REPONSE NUMERIQUE PAR EQUATION (TYPE 9)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-9">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">

        Équation pour générer la réponse correcte 

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
                <i class="fa fa-exclamation-circle" style="color: crimson;"></i> 
                Vous devez créer des variables avant de définir l'équation pour générer la réponse correcte.
            </div>
        <?
        // --------------------------------------------------------------------
        //
        // Definir la reponse correcte
        //
        // -------------------------------------------------------------------- */ ?>
            
        <? elseif (empty($reponses[$question_id])) : ?>

            <div class="ajouter-reponse" style="padding-top: 20px; padding-bottom: 5px;">

                 <div class="btn btn-outline-primary" 
                    data-question_id="<?= $question_id; ?>" 
                    data-question_type="<?= $q['question_type']; ?>"
                    data-toggle="modal" 
                    data-target="#modal-ajout-equation-correcte">
                    <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> 
                    Définir l'équation pour générer la réponse correcte
                </div>

            </div>

        <? else : ?>

            <?
            // --------------------------------------------------------------------
            //
            // Afficher la reponse correcte
            //
            // -------------------------------------------------------------------- */ ?>

            <div class="reponses" data-question_id="<?= $question_id; ?>">

                <? $j = 0; foreach($reponses[$question_id] as $r) : 

                    if ($r['question_id'] != $question_id)
                        continue;

                    $reponse_id = $r['reponse_id'];
                ?>

                <div class="reponses-table-wrap">

                    <table class="reponses-table">

                        <tr>
                            <td class="reponse-label" style="text-align: center; width: 40px; border-right: 1px solid #ddd;">
                                <span class="align-middle">
                                    <? if ($r['reponse_correcte']) : ?>
                                        <i class="fa fa-check-circle" style="color: limegreen"></i>
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
                                    <div class="question-equation-correcte-sauvegarde btn btn-outline-primary" 
                                        data-toggle="modal" 
                                        data-target="#modal-modifier-equation-correcte"
                                        data-reponse="<?= htmlentities(json_encode($r)); ?>"
                                        data-question_id="<?= $r['question_id']; ?>"
                                        data-reponse_id="<?= $r['reponse_id']; ?>">
                                        <i class="fa fa-edit" style="margin-right: 5px"></i>
                                        Modifier
                                        <i class="question-equation-correcte-sauvegarde-action fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                                    </div>
                                </td>
                            <? endif; ?>

                        </tr>

                    </table>

                </div> <!-- .reponses-table-wrap -->

                <? endforeach; ?>

                <div style="margin-top: 15px; color: #777; font-size: 0.9em">
                    <i class="fa fa-exclamation-circle" style="color: darkorange"></i> 
                    Les chiffres significatifs doivent être fixés manuellement dans le cas d'une addition ou d'une soustraction (ex. 40-1=40 si aucun CS fixé).
                </div>

                <div style="margin-top: 5px; color: #777; font-size: 0.9em">
                    <i class="fa fa-exclamation-circle" style="color: #ccc; margin-right: 5px"></i>
                    Les étudiants peuvent entrer les décimales à l'aide de point (.) ou de virgule (,) à leur convenance.
                </div>

                <? 
                // ----------------------------------------------
                //
                // TOLERANCES
                //
                // ---------------------------------------------- ?>

                <div class="space"></div>

                <div class="tolerances">

                    <label class="font-weight-bold">Tolérance<?= count($tolerances[$question_id]) > 1 ? 's' : ''; ?> : </label>

                    <? if ( ! empty($tolerances[$question_id])) : ?>

                        <div style="border: 1px solid #ddd; border-radius: 3px; padding: 5px; margin-top: 10px; margin-bottom: 20px;">

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
                        <div style="font-size: 0.85em; padding-top: 5px; padding-bottom: 10px; color: crimson">
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

                    <? if (
                            $this->enseignant['enseignant_id'] == $evaluation['enseignant_id']  ||
                            $this->enseignant['enseignant_id'] == $q['ajout_par_enseignant_id'] ||
                            permis('editeur')
                          ) : 
                    ?>
                        <div class="ajouter-tolerance" style="margin-top: 10px; margin-bottom: 5px;">

                             <div class="btn btn-outline-primary" 
                                data-reponse_id="<?= $reponse_id; ?>"
                                data-question_id="<?= $question_id; ?>"
                                data-toggle="modal" 
                                data-target="#modal-ajout-tolerance">
                                <i class="fa fa-plus-circle"></i> Ajouter une tolérance</a>
                            </div>

                            <a class="btn btn-outline-secondary" target="_blank" href="<?= base_url() . 'outils/question9/' . $question_id; ?>"> 
                                <i class="fa fa-cog"></i> Tester cette question
                            </a>

                            <div class="btn btn-light" data-toggle="modal" data-target="#modal-info-notation-scientifique" style="color: #555; border-color: #ddd">
                                <i class="fa fa-info-circle"></i>
                                Notation scientifique
                            </div>

                        </div>
                    <? endif; // permissions pour tolerances ?>

                </div> <!-- /.tolerances -->

            </div> <!-- .reponses -->

        <? endif; // ! empty($reponses[$question_id]) ?>

    </div> <!-- .editeur-section-sous-section -->

</div> <!-- .editeur-question-type-9 -->
