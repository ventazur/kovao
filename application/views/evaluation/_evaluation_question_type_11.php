<? 
/* ----------------------------------------------------------------
 * 
 * Question a choix multiples stricte (TYPE 11)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <div class="commentaire" style="padding: 10px 10px 10px 15px">

        <span style="font-size: 0.9em; color: #888">
            <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
            Aucune, une ou plusieurs réponses possibles
        </span>

    </div>

    <?  $reponses_question = $reponses[$question_id]; ?>
    <?  // if ($q['reponses_aleatoires']) { shuffle($reponses_question); } ?>

    <? foreach($reponses_question as $r) : ?>

        <div class="question-reponse">

            <div class="form-check">
            <input name="question_<?= $question_id; ?>[]" class="form-check-input" type="checkbox" value="<?= $r['reponse_id']; ?>" <?= is_array($traces) && array_key_exists($question_id, $traces) && in_array($r['reponse_id'], $traces[$question_id]) ? 'checked' : ''; ?>>

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
                    <?= filter_symbols($r['reponse_texte']); ?>
                </label>
            </div>

        </div> <!-- /.question-reponse -->

    <? endforeach; ?>

</div> <!-- /.question-reponses -->
