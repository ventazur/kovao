<? 
/* ----------------------------------------------------------------
 * 
 * Question a choix unique par equations (TYPE 3)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <?  $reponses_question = $reponses[$question_id]; ?>
    <?  // if ($q['reponses_aleatoires']) { shuffle($reponses_question); } ?>

    <? foreach($reponses_question as $r) : ?>

        <div class="question-reponse">

            <div class="form-check">
                <input name="question_<?= $question_id; ?>" class="form-check-input" type="radio" value="<?= $r['reponse_id']; ?>" required <?= is_array($traces) && array_key_exists($question_id, $traces) && (@$traces[$question_id] == $r['reponse_id']) ? 'checked' : ''; ?>>

                <?
                /* ----------------------------------------------------
                /*
                /* Indication de l'enregistrement des traces
                /*
                /* ---------------------------------------------------- */ ?>

                <span class="traces-enregistrees general">
                    <i class="fa fa-save" style="color: limegreen"></i>
                </span>
                <span class="traces-echecs general">
                    <i class="fa fa-save" style="color: crimson"></i>
                    <i class="fa fa-times" style="color: crimson"></i>
                </span>

                <label class="form-check-label" style="margin-left: 7px">
                    <? if ($r['notsci']) : ?>
                        <?= ns_format($r['reponse_equation']); ?>
                    <? else : ?>
                        <?= $r['reponse_equation']; ?>
                    <? endif; ?>
                    <? if ($r['unites']) : ?>
                        <?= $r['unites']; ?>
                    <? endif; ?>
                </label>
            </div>

        </div> <!-- /.question-reponse -->

    <? endforeach; ?>

</div> <!-- /.question-reponses -->
