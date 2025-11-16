<?  
// ----------------------------------------------------------------
//
// REPONSE PAR DOCUMENTS (TYPE 10)
// 
// ---------------------------------------------------------------- ?>

<div class="editeur-question-type-10">

    <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
        Réponse
    </div>

    <div class="editeur-section-sous-section editeur-question-sous-section">

        <div class="reponse-developpement" style="font-size: 0.9em">

            <i class="fa fa-exclamation-circle" style="color: orange; margin-right: 5px;"></i> 
            Les étudiants devront téléverser des documents.

            <div class="hspace"></div>

            <? if ($this->config->item('permettre_fichiers_dangereux') && $enseignant['permettre_fichiers_dangereux']) : ?>

                <li>Formats acceptés : JPG, PNG, GIF, PDF, DOCx et XLSx.</li>

            <? else : ?>

                <li>Formats acceptés : JPG, PNG, GIF et PDF</li>

            <? endif; ?>

            <li>Nombre maximum : <?= $this->config->item('questions_types')[10]['docs_max']; ?> documents par question</li>
            <li>Taille maximale  : <?= $this->config->item('questions_types')[10]['taille_max']; ?> Mo par document</li>

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

</div> <!-- .editeur-question-type-10 -->
