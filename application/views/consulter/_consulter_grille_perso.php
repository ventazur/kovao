<? 
/* ----------------------------------------------------------------------------
 *
 * Grille de correction personnalisee
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if (array_key_exists('grille', $q) && $q['grille'] !== FALSE && array_key_exists($q['question_id'], $grilles)) : ?>

    <? if (($q['grille_affichage']) || ($this->est_enseignant && ! $version_etudiante)) : ?>

        <?  if (array_key_exists('elements', $grilles[$q['question_id']]) && ! empty($grilles[$q['question_id']]['elements'])) : ?>

            <div class="corriger-reponse-attendue">

                <div id="grille-perso-question-'<?= $q['question_id']; ?>" class="grille-perso">

                    <div class="font-weight-bold" style="color: crimson">
                        Grille de correction :
                        <? if ( ! $q['grille_affichage']) : ?>
                            <span style="font-weight: 300; color: #999">
                                (dissimulée)
                            </span>
                        <? endif; ?>
                    </div>

                    <div class="hspace"></div>

                    <? foreach ($grilles[$q['question_id']]['elements'] as $e) : ?>

                        <? if ($e['selectionne'] != 1 && $e['element_type'] == 2) continue; ?>

                        <div>
                            <? if ($e['element_type'] == 1) : ?>

                                <?= $e['element_desc']; ?>
            
                            <? else : ?>

                                <i class="fa fa-angle-right"></i>
                                <span style="padding-left: 10px"><?= $e['element_desc']; ?></span>

                            <? endif; ?>


                            <? if ($e['selectionne'] == 1) : ?>

                                <? if ($e['element_type'] == 1) : ?>

                                    <div class="badge" style="background: #ddd">
                                        <i class="fa fa-check" style="color: limegreen"></i>
                                        +<?= my_number_format($e['element_pourcent']/100 * $q['question_points']); ?>
                                    </div>

                                <? else : ?>

                                    <div class="badge badge-light" style="background: #ddd">
                                        <i class="fa fa-times" style="color: crimson"></i>
                                        -<?= my_number_format($e['element_pourcent']/100 * $q['question_points']); ?>
                                    </div>

                                <? endif; ?>

                            <? else : ?>

                                <? if ($e['element_type'] == 1) : ?>

                                    <div class="badge badge-light" style="background: #ddd">
                                        <i class="fa fa-times" style="color: crimson"></i>
                                        -<?= my_number_format($e['element_pourcent']/100 * $q['question_points']); ?>
                                    </div>

                                <? endif; ?>

                            <? endif; ?>
                        </div>
                
                    <? endforeach; ?>

                </div> <!-- .grille-perso -->

            </div> <!-- .corriger-reponse-attendue -->

        <? endif; ?>

    <? endif; ?>

<?endif; ?>
