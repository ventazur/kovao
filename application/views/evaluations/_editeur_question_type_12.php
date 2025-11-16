<?  
// ----------------------------------------------------------------
//
// REPONSE A DEVELOPPEMENT COURT (TYPE 12)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-12">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
        Réponse
    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section">

        <div class="reponse-developpement" style="font-size: 0.9em">

            <i class="fa fa-exclamation-circle" style="color: orange; margin-right: 5px;"></i> 
            Cette question nécessite une réponse courte à développement.

        </div> <!-- /.reponse-developpement -->

    </div> <!-- /.editeur-section-sous-section -->

    <?
    /* ----------------------------------------------------------------
     *
     * Grille de correction
     *
     * ---------------------------------------------------------------- */ ?>

    <? if ( ! $q['sondage']) : ?>

        <? $this->load->view('evaluations/_editeur_grille_perso'); ?>

    <? endif; ?>

</div> <!-- /.editeur-question-type-2 -->
