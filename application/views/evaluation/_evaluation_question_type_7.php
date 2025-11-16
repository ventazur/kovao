<? 
/* ----------------------------------------------------------------
 * 
 * Question a reponse litterale courte (TYPE 7)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <div class="commentaire d-none" style="padding: 10px 10px 10px 15px">

        <span style="font-size: 0.9em; color: #888">
            <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
            Vous devez entrer une lettre, un seul mot, ou plusieurs mots faisant partie d'une même idée (pas de phrase).
        </span>

    </div>

    <div class="question-reponse" style="margin-top: 5px;">

        <div class="form-inline">
            <div class="input-group col-12 col-lg-8" style="padding-left: 5px;  padding-right: 5px">
                <div class="input-group-prepend">
                    <span class="input-group-text">Votre réponse :

                        <?
                        /* ----------------------------------------------------
                        /*
                        /* Indication de l'enregistrement des traces
                        /*
                        /* ---------------------------------------------------- */ ?>

                        <span class="traces-enregistrees litterale">
                            <i class="fa fa-save" style="color: limegreen"></i>
                        </span>
                        <span class="traces-echecs litterale">
                            <i class="fa fa-save" style="color: crimson"></i>
                            <i class="fa fa-times" style="color: crimson"></i>
                        </span>

                    </span>
                 </div>

                 <input type="text" name="question_<?= $question_id; ?>" class="reponse-litterale-courte form-control" 
                    data-reponse_pre="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : NULL; ?>"
                    value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>
            </div>

        </div>

    </div> <!-- /.question-reponse -->

</div> <!-- /.question-reponses -->
