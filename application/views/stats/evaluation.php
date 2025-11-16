<?
/* ----------------------------------------------------------------------------
 *
 * Statistiques d'une evaluation
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>

    button.fauxlien 
    {
          background: none!important;
          border: none;
          padding: 0!important;
          color: #017BF3;
          text-decoration: none;
          cursor: pointer;
    }

    button.fauxlien:hover {
        color: #0356B3;
        text-decoration: underline;
    }

</style>

<div id="statistiques-evaluation">
<div class="container-fluid">

<div id="soumissions-data" data-soumission_ids="<?= htmlspecialchars(serialize($soumission_ids)); ?>" data-groupe_no="<?= $groupe_no; ?>"></div>

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <?
        /* --------------------------------------------------------------------
         *
         * En-tete
         *
         * -------------------------------------------------------------------- */ ?>

        <h3>Statistiques d'une évaluation</h3>

        <div class="space"></div>

        <?
        /* --------------------------------------------------------------------
         *
         * Titre de l'evaluation
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="stats-evaluation-titre">

            <?= $evaluation['evaluation_titre']; ?>

        </div>

        <?
        /* --------------------------------------------------------------------
         *
         * Information sur l'evaluation
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="stats-evaluation-info-titre">
    
            Information sur l'évaluation

        </div>

        <div id="stats-evaluation-info">
            <ul>

                <li>Cours : <?= $cours['cours_nom'] . ' (' . $cours['cours_code'] . ')'; ?></li>

                <li>Evaluation ID : <a href="<?= base_url() . 'evaluations/editeur/' . $evaluation_id; ?>"><?= $evaluation_id; ?></a></li>
        
                <? if ($groupe_no !== NULL) : ?>

                    <li>Groupe : <?= $groupe_no == 999 ? 'inconnu' : $groupe_no; ?></li>

                <? endif; ?>

                <li>Nombre d'évaluations corrigées : <?= $e_nombre ?: 0; ?></li>

                <? if ($e_points_totaux > 0) : ?>
                    <li>
                        <strong>Taux de réussite</strong> :
                        <?= number_format($e_points_obtenus, 1, ',', '') . ' / ' . number_format($e_points_totaux, 1, ',', ''); ?>
                        <span style="padding-left: 10px">(<?= number_format($e_points_obtenus / $e_points_totaux * 100); ?>%)</span>
                    </li>
                <? endif; ?>

            </ul>
        </div> 

        <?
        /* --------------------------------------------------------------------
         *
         * Cette évaluation n'a jamais été remplie par vos étudiants
         *
         * -------------------------------------------------------------------- */ ?>

        <? if (empty($soumissions)) : ?>

            <i class="fa fa-exclamation-circle"></i> Cette évaluation n'a jamais été remplie par vos étudiants.

        <? else : ?>

            <?
            /* --------------------------------------------------------------------
             *
             * Tableau des resultats par question
             *
             * -------------------------------------------------------------------- */ ?>

            <div id="stats-questions-titre">
        
                Les résultats obtenus par question

            </div>

            <div id="stats-questions">

                <table class="table questions" style="margin: 0; font-size: 0.9em">
                    <thead>
                        <tr>
                            <td style="width: 150px; text-align: center;">
                                Question ID 
                                <span class="tri-button" data-clef="clef_tri_question_ids"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span>
                            </td>
                            <td>Question</td>
                            <td style="width: 150px; text-align: center">
                                Apparitions
                              <span class="tri-button" data-clef="clef_tri_apparitions" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                            </td>
                            <td style="width: 150px; text-align: right">
                                Points cumulés
                                <span class="tri-button" data-clef="clef_tri_points" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                            </td>
                        </tr>
                    </thead>

                    <tbody>

                    <? 
                        $question_ids = array();

                        foreach($q_nombre as $question_id => $nombre) : 

                            $points_totaux  = $q_points_totaux[$question_id];
                            $points_obtenus = $q_points_obtenus[$question_id];

                            if ($points_totaux > 0)
                            {
                                $question_reussie = ($points_obtenus / $points_totaux) >= .6 ? TRUE : FALSE;
                            }
                            else
                            {
                                $question_ressie = FALSE;
                            }

                            $question_ids[] = $question_id;
                    ?>

                        <tr class="question"
                            data-question_id="<?= $question_id; ?>"
                            data-clef_tri_question_ids="<?= $question_id; ?>"
                            data-clef_tri_apparitions="<?= $nombre; ?>"
                            data-clef_tri_points="<?= $points_totaux > 0 ? ($points_obtenus/$points_totaux*100) : 0; ?>">

                            <td class="question-lien" style="text-align: center">
                                <? if ($origine == 'resultats') : ?>
                                    <?= form_open(base_url() . 'stats/resultats/question/' . $question_id . '/evaluation/' . $evaluation_id . '/semestre/' . $this->semestre_id . '/req/' . $requete); ?>
                                        <input class="ordre-questions" type="hidden" name="ordre" value="" />
                                        <button class="fauxlien"><?= $question_id; ?></button>
                                    </form>
                                <? else : ?>
                                    <?= form_open(base_url() . 'stats/evaluation/' . $evaluation_id . '/question/' . $question_id . '/req/' . $requete); ?>
                                        <input class="ordre-questions" type="hidden" name="ordre" value="" />
                                        <button class="fauxlien"><?= $question_id; ?></button>
                                    </form>
                                <? endif; ?>
                            </td>
                            <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width: 1px;">
                                <?= _html_edit($q_textes[$question_id]); ?>
                            </td>
                            <td style="text-align: center"><?= $nombre; ?></td>
                            <td style="text-align: right; color: <?= $question_reussie ? 'inherit' : 'crimson'; ?>">
                                <? if ( ! @$questions[$question_id]['sondage']) : ?>
                                    <? if ($points_totaux > 0) : ?>
                                    <?= my_number_format($points_obtenus) . ' / ' . my_number_format($points_totaux); ?> <span style="padding-left: 10px">(<?= number_format($points_obtenus / $points_totaux * 100); ?>%)</span>
                                    <? else : ?>
                                        0 / 0 (0%)
                                    <? endif; ?>
                                <? endif; ?>
                            </td>

                        </tr>

                    <? endforeach; ?>

                    </tbody>
                </table>

            </div> <!-- #stats-questions -->

        <? endif; // empty($soumissions) ?> 

        <div class="space"></div>

        <div class="row">
            <div class="col">
                <a class="btn btn-outline-secondary" href="<?= $stats_retour_resultats; ?>">
                    <i class="fa fa-undo" style="margin-right: 3px"></i> 
                    <? if ($stats_origine == 'editeur') : ?>
                        Retour à l'éditeur
                    <? else : ?>
                        Retour aux résultats de l'évaluation
                    <? endif; ?>
                </a>
            </div>
        </div>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container -->
</div> <!-- #question -->
