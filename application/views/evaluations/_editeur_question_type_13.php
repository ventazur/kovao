<?  
// ----------------------------------------------------------------
//
// REPONSE A DEVELOPPEMENT CHATGPT (TYPE 13)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-13">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
        Réponse
    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section">

        <div class="reponse-developpement" style="font-size: 0.9em">

            <i class="fa fa-exclamation-circle" style="color: orange; margin-right: 5px;"></i> 
            Cette question nécessite une réponse à développement.

        </div> <!-- /.reponse-developpement -->

    </div> <!-- /.editeur-section-sous-section -->

    <?
    /* ----------------------------------------------------------------
     *
     * Modele IA
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
        Modèle IA
    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section">

        <? if (empty($this->config->item('modeles_ia'))) : ?>

            <div style="font-size: 0.9em">

                <i class="fa fa-exclamation-circle"></i> Aucun modèle IA disponible.

            </div>

        <? else : ?>

            <select name="modele-ia" class="form-control">

                <? $i = 1; ?>

                <? foreach($this->config->item('modeles_ia') as $modele) : ?>
                    
                    <option value="<?= $modele['nom']; ?>" selected><?= $modele['desc']; ?> [<?= $modele['cout']; ?>]</option>

                <? endforeach; ?>

            </select>

        <? endif; ?>
    </div> <!-- /.editeur-section-sous-section -->

    <?
    /* ----------------------------------------------------------------
     *
     * Bareme de correction
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
        Barème
    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section">

        <div class="bareme-developpement" style="font-size: 0.9em">

            <i class="fa fa-exclamation-circle" style="color: orange; margin-right: 5px;"></i> 
            Cette question nécessite un barème sous forme de <i>prompt</i> pour ChatGPT.

        </div> <!-- /.bareme-developpement -->

        <? $this->load->view('evaluations/_editeur_question_type_13_bareme'); ?>

    </div> <!-- /.editeur-section-sous-section -->

</div> <!-- /.editeur-question-type-13 -->
