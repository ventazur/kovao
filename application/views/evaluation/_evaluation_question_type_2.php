
<? 
/* ----------------------------------------------------------------
 * 
 * Question a developpement (TYPE 2)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <div class="form-group col-sm-12 mt-2 pl-4 pr-4" style="margin-bottom: 10px">
        <label style="font-size: 0.9em; color: #888">Votre réponse à développement : </label>
        <textarea name="question_<?= $question_id; ?>" class="form-control" rows="3" required><?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?></textarea>

        <div class="invalid-feedback">
            Ce champ est requis.
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

</div>
