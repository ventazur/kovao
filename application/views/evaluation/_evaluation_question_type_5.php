<? 
/* ----------------------------------------------------------------
 * 
 * Question a reponse numerique entiere (TYPE 5)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <div class="commentaire" style="padding: 10px 10px 10px 15px">

        <span style="font-size: 0.9em; color: #888">
            <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
            Votre réponse doit être un entier positif ou négatif, ou zéro (aucune lettre, ni virgule, ni point).
        </span>

    </div>

    <?  
        if (count($reponses[$question_id]) == 1)
            $unites = dot_array_search($question_id . '.*.unites', $reponses) ?: NULL; 
        else
            $unites = NULL;
    ?> 

    <div class="question-reponse" style="margin-top: 5px;">

        <div class="form-inline">
            <div class="input-group" style="margin-right: 10px;">
                <div class="input-group-prepend">
                    <span class="input-group-text">Votre réponse :
                        
                        <?
                        /* ----------------------------------------------------
                        /*
                        /* Indication de l'enregistrement des traces
                        /*
                        /* ---------------------------------------------------- */ ?>

                        <span class="traces-enregistrees numerique">
                            <i class="fa fa-save" style="color: limegreen"></i>
                        </span>
                        <span class="traces-echecs numerique">
                            <i class="fa fa-save" style="color: crimson"></i>
                            <i class="fa fa-times" style="color: crimson"></i>
                        </span>

                    </span>

                 </div>
                 <input type="number" name="question_<?= $question_id; ?>" class="reponse-numerique form-control" value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>
                <? if ( ! empty($unites)) : ?>
                    <div class="input-group-append">
                        <span class="input-group-text"><?= $unites; ?></span>
                    </div>
                <? endif; ?>
            </div>

        </div>

    </div> <!-- /.question-reponse -->

</div> <!-- /.question-reponses -->
