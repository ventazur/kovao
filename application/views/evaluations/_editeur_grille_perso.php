<?  
// ----------------------------------------------------------------
//
// Grille de correction
// 
// ---------------------------------------------------------------- ?>

<? if ( ! array_key_exists($q['question_id'], $gc)) : ?>

    <? if (in_array('modifier', $permissions)) : ?>

        <div class="btn btn-outline-secondary creer-grille-correction" 
             style="margin-top: 15px" 
             data-question_id="<?= $q['question_id']; ?>" data-toggle="modal" data-target="#modal-ajout-grille-correction">
            <i class="fa fa-plus-circle" style="margin-right: 3px"></i> Créer une grille de correction
        </div>

        <div class="btn btn-outline-secondary importer-grille-correction" 
             style="margin-top: 15px" 
             data-question_id="<?= $q['question_id']; ?>" data-toggle="modal" data-target="#modal-importer-grille-correction">
            <i class="fa fa-plus-circle" style="margin-right: 3px"></i> Importer une grille d'une autre question
        </div>
    <? endif; ?>

<? else : ?>

    <a class="anchor" name="gc-question-<?= $q['question_id']; ?>"></a> 

    <div class="grille-correction editeur-section-sous-section-titre editeur-question-sous-section-titre">
        <div class="row">
            <div class="col">
            Grille de correction
            </div>
            <div class="col" style="text-align: right">
                <i class="fa fa-circle" style="margin-right: 5px; color: #9FA8DA"></i>
                <? if ($gc[$q['question_id']]['grille_affichage']) : ?>
                    Cette grille sera affichée.
                <? else : ?>
                    Cette grille ne sera <strong>pas</strong> affichée.
                <? endif; ?>
            </div>
        </div>
    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section">

        <?
            $calcul_pourcent = 0;
        ?>

        <? if ( ! array_key_exists('elements', $gc[$q['question_id']]) || count($gc[$q['question_id']]['elements']) < 1) : ?>

            <div style="font-size: 0.9em;">
                <i class="fa fa-exclamation-circle"></i>
                Aucun élément
            </div>

            <div class="qspace"></div>

        <? else : ?>

            <div class="grille-table2">

                <table style="width: 100%; font-size: 0.9em;">

                <? 
                    foreach($gc[$q['question_id']]['elements'] as $e) : 

                        if ($e['element_type'] == 1)
                        {
                            $calcul_pourcent += $e['element_pourcent'];
                        }
                ?>
                    <tr class="element-contenu">
                        <td style="width: 30px">
                            <i class="fa fa-square-o"></i>
                        </td>
                        <td style="width: 80px; text-align: center;">
                            <span style="color: #444">
                                <? if ($e['element_type'] == 1) : ?>
                                    <?= my_number_format($e['element_pourcent']); ?>%
                                <? else : ?>
                                    <span style="color: crimson">(<?= my_number_format($e['element_pourcent']); ?>%)</span>
                                <? endif; ?>
                            </span>
                        </td>
                        <td style="padding-left: 10px">
                                <? if ($e['element_type'] == 1) : ?>
                                    <?= $e['element_desc']; ?>
                                <? else : ?>
                                    <i class="fa fa-angle-right"></i>
                                    <span style="padding-left: 10px"><?= $e['element_desc']; ?></span>
                                <? endif; ?>
                        </td>
                        <td style="width: 70px; text-align: right">
                            <span class="badge badge-pill" style="background: #eee; font-size: 0.9em; font-weight: 300">
                                ordre : <?= $e['element_ordre']; ?>
                            </span>
                        </td>

                        <? if (in_array('modifier', $permissions)) : ?>

                            <td style="width: 160px; text-align: right;">

                                <div class="dupliquer-element btn btn-sm btn-outline-secondary"
                                     data-question_id="<?= $e['question_id']; ?>"
                                     data-grille_id="<?= $e['grille_id']; ?>"
                                     data-element_id="<?= $e['element_id']; ?>"
                                     data-toggle="tooltip"
                                     data-title="Dupliquer">
                                    <i class="fa fa-clone"></i>
                                    <i class="fa fa-circle-o-notch fa-spin d-none"></i>
                                </div>
                                     
                                <div class="btn btn-sm btn-outline-primary" 
                                     data-question_id="<?= $e['question_id']; ?>"
                                     data-grille_id="<?= $e['grille_id']; ?>"
                                     data-element_id="<?= $e['element_id']; ?>"
                                     data-element_desc="<?= htmlentities($e['element_desc']); ?>"
                                     data-element_type="<?= $e['element_type']; ?>"
                                     data-element_ordre="<?= $e['element_ordre']; ?>"
                                     data-element_pourcent="<?= $e['element_pourcent']; ?>"
                                     data-toggle="modal" 
                                     data-target="#modal-modifier-element">
                                    <i class="fa fa-edit"></i>
                                    Modifier
                                </div>
                            </td>

                        <? endif; ?> <? // permission de modifier ?> 
                    </tr>    

                <? endforeach; ?>

                <? if (in_array('modifier', $permissions)) : ?>

                    <tr style="height: 40px">
                        <td style="width: 30px">
                        <i class="fa fa-square" style="color: #ddd;"></i>
                        </td>
                        <td style="text-align: center; font-weight: 600">
                            <?= my_number_format($calcul_pourcent); ?>%
                        </td>
                        <td colspan="3" style="padding-left: 10px;">
                            <? if ($calcul_pourcent == 100) : ?>
                                <i class="fa fa-check-circle" style="color: limegreen; margin-right: 5px"></i>
                                    Cette grille est activée.
                            <? else : ?>
                                <i class="fa fa-times-circle" style="color: crimson; margin-right: 5px"></i>
                                    Le total des éléments additifs doit atteindre 100% pour que cette grille devienne active.
                            <? endif; ?>
                        </td>
                    </tr>

                <? endif; ?>

                </table>

            </div> <!-- .grille-table2 -->

        <? endif; ?>

        <? if (in_array('modifier', $permissions)) : ?>

            <div style="margin-top: 15px">

                <div class="row">
                    <div class="col">


                            <div class="btn btn-outline-primary"
                                 data-question_id="<?= $q['question_id']; ?>" 
                                 data-grille_id="<?= $gc[$q['question_id']]['grille_id']; ?>" 
                                 data-toggle="modal" 
                                 data-target="#modal-ajout-element">
                                <i class="fa fa-plus-circle"></i> Ajouter un élément
                            </div>

                            <div class="btn btn-outline-secondary"
                                 data-question_id="<?= $q['question_id']; ?>" 
                                 data-grille_id="<?= $gc[$q['question_id']]['grille_id']; ?>" 
                                 data-grille_affichage="<?= $gc[$q['question_id']]['grille_affichage']; ?>"
                                 data-toggle="modal" 
                                 data-target="#modal-modifier-grille-correction">
                                <i class="fa fa-edit"></i> Modifier les paramètres de la grille
                            </div>

                    </div>
                    <div class="col" style="text-align: right">
                        <? if (array_key_exists('redirect_corrections', $_SESSION) && @$_SESSION['redirect_corrections_question_id'] == $q['question_id']) : ?>

                            <a href="<?= base_url() . 'corrections/corriger/' . $_SESSION['redirect_corrections']; ?>" class="btn btn-warning">
                                <i class="fa fa-undo"></i> Retour à la correction
                            </a>

                        <? endif; ?>
                    </div>
                </div>
            </div>

        <? endif; ?> <? // permission de modifier ?>

    </div> <!-- /.editeur-section-sous-section -->

<? endif; ?>
