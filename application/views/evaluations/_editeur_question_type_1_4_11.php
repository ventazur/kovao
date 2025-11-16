<?  
// ----------------------------------------------------------------
//
// REPONSES A CHOIX UNIQUE            (TYPE 1) 
// REPONSES A CHOIX MULTIPLES         (TYPE 4)
// REPOSNES A CHOIX MULTIPLES STRICTE (TYPE 11)
// 
// ---------------------------------------------------------------- ?>

<?
    //
    // Activer l'effacement multiples de reponses pour creer des questions rapidement.
    //

    $effacement_multiple = FALSE;

    if (in_array($q['question_type'], array(1, 4, 11)) && count($reponses[$question_id]) > 5)
    {
        $effacement_multiple = TRUE;
    }
?>

<div class="editeur-question-type-1-4-11">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">

        Réponses 
        <?= ! empty($reponses[$question_id]) && (count($reponses[$question_id]) > 3) ? '(' . count($reponses[$question_id]) . ')' : ''; ?>

    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section" style="padding-top: 0">

        <div class="reponses" data-question_id="<?= $question_id; ?>" data-compte="<?= count($reponses[$question_id]); ?>">

            <? if (empty($reponses[$question_id])) : ?>

                <div style="font-size: 0.9em; padding-top: 20px; padding-bottom: 10px;">
                    <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
                    Aucune réponse définie
                </div>

            <? else : ?>

                <? 
                $reponse_correcte_presente = 0;

                foreach($reponses[$question_id] as $r) : 

                    if ($r['question_id'] != $question_id)
                        continue;

                    $reponse_id = $r['reponse_id'];

                    $reponse_correcte_presente += $r['reponse_correcte'];
                ?>

                <div class="reponses-table-wrap">

                    <table class="reponses-table">

                        <tr>
                            <td class="reponse-label" style="text-align: center; width: 50px; padding-right: 8px; border-right: 1px solid #7986CB;">
                                <span class="align-middle">
                                    <? if ($r['reponse_correcte']) : ?>
                                        <i class="fa fa-check-circle reponse-toggle" style="color: limegreen; cursor: pointer"
											data-question_id="<?= $question_id; ?>"
											data-reponse_id="<?= $reponse_id; ?>"
											data-reponse_correcte="<?= $r['reponse_correcte']; ?>"></i>
                                    <? else : ?>
                                        <i class="fa fa-times-circle reponse-toggle" style="color: crimson; cursor: pointer"
											data-question_id="<?= $question_id; ?>"
											data-reponse_id="<?= $reponse_id; ?>"
											data-reponse_correcte="<?= $r['reponse_correcte']; ?>"></i>
                                    <? endif; ?>
                                </span>
                            </td>
                            <td class="reponse-texte" style="padding-left: 15px; padding-right: 10px;">
                                <?= filter_symbols(@$r['reponse_texte']); ?>
                            </td>

                            <? if (in_array('modifier', $permissions_question)) : ?>

                                <? if ($effacement_multiple) : ?>

                                    <td style="text-align: right">
                                        <input class="choix-multiples-selection" name="choix_multiples_selectionnes" type="checkbox" value="<?= $r['reponse_id']; ?>">
                                    </td>

                                <? endif; ?>

                                <td style="width: 130px; text-align: right;">
                                    <div class="question-reponse-sauvegarde btn btn-outline-primary"
                                        data-toggle="modal" 
                                        data-target="#modal-modifier-reponse"
                                        data-reponse="<?= htmlentities($r['reponse_texte']); ?>"
                                        data-reponse_correcte="<?= $r['reponse_correcte']; ?>"
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

                <? 
                /* ------------------------------------------------------------
                 *
                 * Avertissement qu'il faut au moins 2 reponses a cettre question
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if (count($reponses[$question_id]) < 2) : ?>

                    <div style="font-size: 0.9em; padding-top: 25px; padding-bottom: 10px;">
                        <i class="fa fa-exclamation-circle" style="color: darkorange; margin-right: 5px"></i>
                        Cette question nécessite au moins deux réponses.
                    </div>

                <? endif; ?>

                <? 
                /* ------------------------------------------------------------
                 *
                 * Avertissement qu'un selecteur sera affiche
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if ($q['question_type'] == 1 && count($reponses[$question_id]) > ($this->config->item('questions_types')[1]['selecteur'] - 1)) : ?>

                    <div style="font-size: 0.9em; padding-top: 25px; padding-bottom: 10px;">
                        <i class="fa fa-exclamation-circle" style="color: darkorange; margin-right: 5px"></i>
                        Lorsqu'il y a plus de 10 réponses, un sélecteur permettra de faire le choix.
                    </div>

                <? endif; ?>


                <? if (in_array($q['question_type'], array(1)) && (count($reponses[$question_id]) > 1) && $reponse_correcte_presente == 0) : ?>

                    <div style="font-size: 0.9em; padding-top: 25px; padding-bottom: 10px;">
                        <i class="fa fa-exclamation-circle" style="color: limegreen; margin-right: 5px"></i>
                        Cette question nécessite au moins une réponse correcte.
                    </div>

                <? endif; ?>
        
            <? endif; // ! empty($reponses[$question_id]) ?>

        </div> <!-- /.reponses -->


       <?  
        // -------------------------------------------------------------
        //
        // Ajouter une reponse
        // 
        // ------------------------------------------------------------- ?>

        <? if (in_array('modifier', $permissions_question)) : ?>

            <div class="ajouter-reponse" style="margin-top: 15px;">

                 <div class="btn btn-outline-primary" 
                    data-question_id="<?= $question_id; ?>" 
                    data-question_type="<?= $q['question_type']; ?>"
                    data-reponse_type_defaut="<?= $q['sondage'] ? 1 : 2; ?>"
                    data-toggle="modal" 
                    data-target="#modal-ajout-reponse">
                    <i class="fa fa-plus-circle" style="margin-right: 5px"></i> 
                    Ajouter une réponse</a>
                </div>

                <? if ($effacement_multiple) : ?>

                     <div class="d-none btn btn-outline-danger effacer-reponses-non-selection spinnable" style="margin-left: 5px"
                        data-question_id="<?= $question_id; ?>" 
                        data-question_type="<?= $q['question_type']; ?>">
                        <i class="fa fa-trash" style="margin-right: 5px"></i> Effacer les réponses <strong>non</strong> sélectionnées</a>
                        <i class="fa fa-spin fa-circle-o-notch spinner d-none" style="margin-left: 7px"></i>
                    </div>

                    <span class="d-none effacer-reponses-non-selection-compte" style="margin-left: 5px; font-family: Lato; font-weight: 300; font-size: 0.9em">
                        (<span class="compte" style="font-weight: 600">0</span>
                        sélectionnées,
                        <span class="rep-compte" style="font-weight: 700">0</span>
                        à effacer)
                    </span>

                <? endif ;?>

            </div> <!-- /.ajouter-reponse -->

        <? endif; ?>

    </div> <!-- /.editeur-section-sous-section -->

</div> <!-- /.editeur-question-type-1-4-11 -->
