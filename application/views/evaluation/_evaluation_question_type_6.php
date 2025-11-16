<? 
/* ----------------------------------------------------------------
 * 
 * Question a reponse numerique (TYPE 6)
 *
 * ----------------------------------------------------------------- */ ?>

<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <div class="commentaire" style="padding: 10px 10px 10px 15px">

        <span style="font-size: 0.9em; color: #888">
            <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
            Votre pouvez utiliser une virgule ou un point pour les décimales. Aucune lettre permise.
        </span>

    </div>

    <?  
        if (count(@$reponses[$question_id]) == 1)
        {
            $unites = dot_array_search($question_id . '.*.unites', $reponses) ?: NULL; 
        }
        else
        {
            $unites = NULL;
        }
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
                 <input type="text" name="question_<?= $question_id; ?>" class="reponse-numerique form-control" value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>
                <? if ( ! empty($unites)) : ?>
                    <div class="input-group-append">
                        <span class="input-group-text"><?= $unites; ?></span>
                    </div>
                <? endif; ?>
            </div>

            <div class="btn btn-light mt-2 mt-sm-0" 
                 data-toggle="modal" 
                 data-target="#modal-info-notation-scientifique" 
                 style="color: #555; border-color: #ddd">
                <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 3px;" fill="currentColor" class="bi-xs bi-info-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
                <span class="d-inline d-md-none">NS</span>
                <span class="d-none d-md-inline">Aide pour la notation scientifique</span>
            </div>

        </div> <!-- /.form-inline -->

    </div> <!-- /.question-reponse -->

</div> <!-- /.question-reponses -->
