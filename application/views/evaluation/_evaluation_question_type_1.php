<? 
/* ----------------------------------------------------------------------------
 * 
 * Question a choix unique (TYPE 1)
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

<?  $reponses_question = $reponses[$question_id]; ?>
<?  // if ($q['reponses_aleatoires']) { shuffle($reponses_question); } ?>

<? 
/* ----------------------------------------------------------------------------
 * 
 * Lorsqu'il y a plus de X reponses, la question passe d'un radio a un select.
 *
 * ---------------------------------------------------------------------------- */ ?>

<?  if (
          ($q['selecteur'] && count($reponses_question) >= $this->config->item('questions_types')[1]['selecteur_option']) 
          ||
          count($reponses_question) > ($this->config->item('questions_types')[1]['selecteur'] - 1)
       ) : ?>

    <div class="question-reponse">

        <select name="question_<?= $question_id; ?>" class="custom-select" style="margin-bottom: -5px" required>

            <option value="" <?= ! array_key_exists($question_id, $traces) ? 'selected' : ''; ?>>Choisissez votre réponse :</option>
            
            <? foreach($reponses_question as $r) : ?>
                <option value="<?= $r['reponse_id']; ?>" <?= array_key_exists($question_id, $traces) && $traces[$question_id] == $r['reponse_id'] ? 'selected' : ''; ?>>
                    <?= filter_symbols($r['reponse_texte']); ?>
                </option>
            <? endforeach; ?>

        </select>

        <?
        /* ----------------------------------------------------
        /*
        /* Indication de l'enregistrement des traces
        /*
        /* ---------------------------------------------------- */ ?>

        <div class="traces-enregistrees general" style="margin-top: 15px; margin-bottom: -15px">
            <i class="fa fa-save" style="color: limegreen"></i>
        </div>
        <div class="traces-echecs general" style="margin-top: 15px; margin-bottom: -15px;">
           <i class="fa fa-save" style="color: crimson"></i>
           <i class="fa fa-times" style="color: crimson"></i>
        </div>

    </div> <!-- /.question-reponse -->

<? else : ?>

    <? if (is_array($reponses_question) && ! empty($reponses_question)) : ?>

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
                        <?= filter_symbols($r['reponse_texte']); ?>
                    </label>
                </div>

            </div> <!-- /.question-reponse -->

        <? endforeach; ?>

    <? else : ?>

        <div style="font-size: 0.9em; margin-left: 15px; margin-top: 5px;">

            <i class="fa fa-exclamation-circle"></i> Aucune réponse définie

        </div>

    <? endif; ?>

<? endif; ?>

</div> <!-- /.question-reponses -->
