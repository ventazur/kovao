<? 
/* ----------------------------------------------------------------------------
 * 
 * IMAGE
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="question-image" style="text-align: center; margin-top: 20px; margin-bottom: 20px;">

    <? if ($images[$question_id]['s3']) : ?>

        <img src="<?= $this->config->item('s3_url', 'amazon') . 'evaluations/' . $images[$question_id]['doc_filename'] . '?' . $images[$question_id]['doc_sha256_file']; ?>" class="img-fluid" style="max-height: 500px"></img>

    <? else : ?>

        <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename'] . '?' . $images[$question_id]['doc_sha256_file']; ?>" class="img-fluid" style="max-height: 500px"></img>

    <? endif; ?>

    <? if ( ! empty($images[$question_id]['doc_caption'])) : ?>

        <p style="margin-top: 20px"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></p>

    <? endif; ?>

</div>

