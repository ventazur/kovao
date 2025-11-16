<? 
/* ----------------------------------------------------------------
 * 
 * Question a developpement court (TYPE 12)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">


    <div class="question-reponse-label">

        Votre réponse :    

    </div>
    

    <div class="question-reponse" style="margin-top: 2px; margin-bottom: 5px;">

        <div class="form-inline">
            <div class="input-group col-12 col-lg-12" style="padding-left: 5px;  padding-right: 5px">
                <input type="text" name="question_<?= $question_id; ?>" class="form-control reponse-developpement-court" value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>

            </div>

            <?
            /* ----------------------------------------------------
            /*
            /* Indication de l'enregistrement des traces
            /*
            /* ---------------------------------------------------- */ ?>

            <div class="traces-enregistrees general" style="margin-top: 10px; margin-bottom: -15px">
                <i class="fa fa-save" style="color: limegreen"></i>
            </div>
            <div class="traces-echecs general" style="margin-top: 10px; margin-bottom: -15px;">
               <i class="fa fa-save" style="color: crimson"></i>
               <i class="fa fa-times" style="color: crimson"></i>
            </div>
        </div>

    </div> <!-- /.question-reponse -->

</div>
