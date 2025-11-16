<?  
// ------------------------------------------------------------------------
//
// IMAGE
// 
// ------------------------------------------------------------------------ ?>

<div class="#editeur-image">

<? if ( ! array_key_exists($question_id, $images)) : ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // AJOUTER UNE IMAGE A CETTE QUESTION
    // 
    // ------------------------------------------------------------------------ ?>

    <? if (in_array('modifier', $permissions_question)) : ?>

        <div class="ajout-image" style="text-align: center; margin-top: 10px; margin-bottom: 40px;" data-question_id="<?= $question_id; ?>">

            <input type="file" name="imagefile" id="ajout-image-question-<?= $question_id; ?>" class="ajout-image-input custom-file-input">
            <label class="btn btn-outline-primary" for="ajout-image-question-<?= $question_id; ?>">

                <i class="fa fa-plus-circle"></i> Insérer une image liée à la question <?= $i; ?>

            </label>

            <i class="fa fa-spin fa-circle-o-notch image-upload-spinner d-none" style="margin-left: 10px; color: dodgerblue"></i>

        </div>

    <? else : ?>

        <div class="dspace"></div>

    <? endif; ?>

<? else : ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // MONTRER L'IMAGE DE CETTE QUESTION
    // 
    // ------------------------------------------------------------------------ ?>

    <div class="question-document" style="text-align: center; margin-top: 50px; margin-bottom: 40px">

        <? if ($images[$question_id]['s3']) : ?>
            
            <img src="<?= $this->config->item('s3_url', 'amazon') . 'evaluations/' . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

        <? else : ?>

            <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

        <? endif; ?>

        <div class="doc-caption" style="margin-top: 20px" data-doc_id="<?= $images[$question_id]['doc_id']; ?>">

            <p class="titre-document">

                <? if (empty($images[$question_id]['doc_caption'])) : ?>

                    <span class="titre"></span><i class="fa fa-exclamation-circle" style="color: darkorange"></i> Aucun titre pour cette image

                <? else : ?>

                    <span class="titre"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></span>

                <? endif; ?>

            </p>

        </div>

        <div class="operations-document" style="margin-top: 20px;">

            <? if (in_array('modifier', $permissions_question)) : ?>

                <div class="btn btn-outline-primary" data-doc_id="<?= $images[$question_id]['doc_id']; ?>" data-toggle="modal" data-target="#modal-modifier-titre-document">
                    <i class="fa fa-edit"></i> Modifier le titre de l'image
                </div>

                <div class="btn btn-outline-danger" data-doc_id="<?= $images[$question_id]['doc_id']; ?>"  data-toggle="modal" data-target="#modal-effacer-document">
                    <i class="fa fa-trash"></i> Effacer l'image
                </div>

            <? endif; ?>
        </div>
    </div>

<? endif; ?>

</div> <!-- /.editeur-image -->
