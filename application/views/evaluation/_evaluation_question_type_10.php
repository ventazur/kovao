<? 
/* ----------------------------------------------------------------
 * 
 * Question a repondre par televersement de documents (TYPE 10)
 *
 * ----------------------------------------------------------------- */ ?>

<div id="question-<?= $question_id; ?>" class="question-data question-reponses question-type-10" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

    <div class="question-reponse">

        <div class="form-check">
        <input name="question_<?= $question_id; ?>" class="repondre-oui form-check-input" type="radio" value="1" required <?= is_array($traces) && array_key_exists($question_id, $traces) && (@$traces[$question_id] == 1) ? 'checked' : ''; ?> required>
            <label class="form-check-label" style="margin-left: 7px">
                <?= $this->config->item('questions_types')[10]['val'][1]; ?>
            </label>
        </div>

    </div> <!-- /.question-reponse -->

    <div class="question-reponse">

        <div class="form-check">
        <input name="question_<?= $question_id; ?>" class="repondre-non form-check-input" type="radio" value="9" required <?= is_array($traces) && array_key_exists($question_id, $traces) && (@$traces[$question_id] == 9) ? 'checked' : ''; ?> required>
            <label class="form-check-label" style="margin-left: 7px">
                <?= $this->config->item('questions_types')[10]['val'][9]; ?>
            </label>
        </div>

    </div> <!-- /.question-reponse -->

    <?
    /* ------------------------------------------------------------------------
     *
     * DOCUMENTS UPLOADER
     *
     * ------------------------------------------------------------------------ */ ?>

    <? if ( ! $en_direct) : ?>

     <div class="documents-uploader <?= count($documents[$question_id]) >= 5 ? 'd-none' : ''; ?> <?= @$traces[$question_id] == 9 ? 'd-none' : ''; ?>" 
          style="margin-top: 15px; border-top: 1px solid #ddd;"> 

        <div class="alert alert-danger d-none" style="margin: 15px 15px 0 15px; color: crimson; padding-left: 15px; font-size: 0.9em;">
            <i class="fa fa-exclamation-circle" style="margin-right: 7px"></i>
            Vous n'avez téléversé aucun document.
        </div>

        <div style="font-size: 0.85em; color: #777; padding: 15px;"> 
            <i class="fa fa-info-circle" style="color: #777; margin-right: 5px;"></i>
            Vous pouvez téléverser jusqu'à 
            <?= $this->config->item('questions_types')[10]['docs_max']; ?> documents, 
            d'une taille maximale de
            <?= $this->config->item('questions_types')[10]['taille_max']; ?> Mo chacun, 
            et d'un des formats suivants : 
            
            <? if ($this->config->item('permettre_fichiers_dangereux') && $enseignant['permettre_fichiers_dangereux']) : ?>
                
                JPG, PNG, GIF, PDF, DOCx ou XLSx.

            <? else : ?>

                JPG, PNG, GIF, ou PDF.

            <? endif; ?>
        </div>

        <?
        /* ------------------------------------------------------------
         *
         * FILES FORM
         *
         * ------------------------------------------------------------ */ ?>
        <div class="documents-uploader-form" style="margin-bottom: 0">
        
            <div id="files-input-button-<?= $question_id; ?>" class="files-input-button col-md-12">
                <label class="btn btn-outline-primary btn-file">
                    <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> 
                     Sélectionner un ou plusieurs documents
                    <input id="files-input-<?= $question_id; ?>" class="files-input d-none" type="file" name="userfile[]" multiple="multiple">
                    <i id="file-reading-<?= $question_id; ?>" class="fa fa-spin fa-circle-o-notch image-upload-spinner d-none" style="margin-left: 10px; color: dodgerblue"></i>
                </label>
            </div>

            <div id="televersement-erreur-<?= $question_id; ?>" class="d-none" style="font-size: 0.85em; color: #777; padding: 10px 15px 0 15px; color: crimson"> 
                <i class="fa fa-exclamation-circle"></i>
                <span>Erreur</span>
            </div>

        </div> <!-- .documents-uploader-form -->

        <?
        /* ------------------------------------------------------------
         *
         * FILES PREVIEW
         *
         * ------------------------------------------------------------ */ ?>
        <div class="documents-uploader-file-preview">

            <div id="files-preview-<?= $question_id; ?>" class="files-preview col-md-12 d-none">

                <table style="width: 100%">
                    <tbody>


                    </tbody>
                </table>

            </div>

        </div> <!-- .documents-uploader-file-preview -->

        <?
        /* ------------------------------------------------------------
         * 
         * FILE PREVIEW TEMPLATE
         *
         * ------------------------------------------------------------ */ ?>
        <div id="file-preview-template-<?= $question_id; ?>" class="file-preview-template col-md-12 d-none">

            <table>
                <tbody>

                    <tr class="file-data">
                        <td style="width: 150px; max-width: 150px;">
                            <embed class="img-thumbnail" src="" />
                        </td>
                        <td class="d-none d-md-table-cell" style="text-align: left; padding: 15px; font-size: 0.9em">
                            <div class="btn" style="padding-left: 0; font-size: 0.9em;">
                                Nom : <span class="file-name"></span>
                            </div>
                            <div class="btn" style="padding-left: 0; font-size: 0.9em;">
                                (Taille : <span class="file-size"></span>)
                            </div>
                            <progress value="00" max="100" style="width: 100%"></progress>
                        </td>
                        <td style="width: 350px; text-align: right;">
                            <div class="file-upload btn btn-success">
                                <i class="fa fa-cloud-upload" style="margin-right: 5px"></i> 
                                Téléverser
                            </div>
                            <div class="file-cancel btn btn-danger">
                                <span class="d-md-none">
                                    <i class="fa fa-ban"></i> 
                                </span>
                                <span class="d-none d-md-inline">
                                    <i class="fa fa-ban" style="margin-right: 5px"></i> 
                                    Annuler
                                </span>
                            </div>
                            <i class="upload-spinner fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue; margin-left: 10px"></i>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div> <!-- #file-preview-template -->

    </div> <!-- #documents-uploader -->

    <? endif; ?>

    <?
    /* ----------------------------------------------------------------------- 
    *
    * DOCUMENTS MANAGER
    *
    * ----------------------------------------------------------------------- */ ?>
    <div id="documents-manager-<?= $question_id; ?>" class="documents-manager <?= empty($documents[$question_id]) ? 'd-none' : ''; ?>">

        <?
        /* -----------------------------------------------------------
         * 
         * FILES LIST
         *
         * ----------------------------------------------------------- */ ?>
        <div id="files-list-<?= $question_id; ?>" class="files-list col-md-12">

            <div class="files-list-title">
                <i class="fa fa-file-o" style="margin-right: 5px"></i>
                Vos documents téléversés :
            </div>

            <table class="table table-borderless" style="margin: 0">
                <tbody>

                <? if ( ! empty($documents[$question_id])) : ?>

                    <? foreach($documents[$question_id] as $d) : 

                            $doc_id = $d['doc_id'];
                    ?>

                    <tr class="file-data" id="file-<?= $doc_id; ?>" data-doc_id="<?= $doc_id; ?>">

                        <? 
                        /* ----------------------------------------------------
                         *
                         * DOCUMENT IMAGE
                         *
                         * --------------------------------------------------- */ ?>
                        <td style="width: 150px;">

                            <? if ($d['s3']) : ?>

                                <a class="img-original" target="_blank" href="<?= $this->config->item('s3_url', 'amazon') . 'soumissions/' . $d['doc_filename'] . '?' . $d['doc_sha256_file']; ?>" target="_blank">

                                    <? if ($this->config->item($d['doc_mime_type'], 'documents_mime_types_properties')['tn']) : ?>

                                        <img class="img-thumbnail center-block" src="<?= $this->config->item('s3_url', 'amazon') . 'soumissions/' . $d['doc_tn_filename'] . '?' . $d['doc_sha256_file']; ?>"></img>
        
                                    <? else : ?>

                                        <img class="img-thumbnail center-block" src="<?= base_url() . 'assets/images/' . $this->config->item($d['doc_mime_type'], 'documents_mime_types_properties')['tn_fichier']; ?>"></img>

                                    <? endif; ?>

                                </a>

                            <? else : ?>

                                <a class="img-original" target="_blank" href="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_filename'] . '?' . $d['doc_sha256_file']; ?>" target="_blank">

                                    <? if ($this->config->item($d['doc_mime_type'], 'documents_mime_types_properties')['tn']) : ?>

                                        <img class="img-thumbnail center-block" src="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_tn_filename'] . '?' . $d['doc_sha256_file']; ?>"></img>
        
                                    <? else : ?>

                                        <img class="img-thumbnail center-block" src="<?= base_url() . 'assets/images/' . $this->config->item($d['doc_mime_type'], 'documents_mime_types_properties')['tn_fichier']; ?>"></img>

                                    <? endif; ?>

                                </a>

                            <? endif; ?>
                        </td>

                        <? 
                        /* ----------------------------------------------------
                         *
                         * FILE OPERATIONS
                         *
                         * ---------------------------------------------------- */ ?>
                        <td class="file-operations-group" style="width: 450px; text-align: right;vertical-align: middle">

                           <? if ( ! $en_direct) : ?>

                               <? 
                               /* -------------------------------------------------
                                *
                                * IMAGE ROTATION (LEFT & RIGHT)
                                *
                                * ------------------------------------------------- */ ?>
                                <div class="btn-group image-rotation file-operations <?= $d['doc_is_image'] ? '' : 'd-none'; ?>" role="group" style="margin-right: 5px">
                                    <div class="file-rotation btn btn-outline-secondary" data-rotation="left" data-doc_id="<?= $d['doc_id']; ?>">
                                        <i class="fa fa-rotate-left icon-large"></i>
                                    </div>
                                    <div class="file-rotation btn btn-outline-secondary" data-rotation="right" data-doc_id="<?= $d['doc_id']; ?>">
                                        <i class="fa fa-rotate-right icon-large"></i>
                                    </div>
                                </div>

                               <? 
                               /* -------------------------------------------------
                                *
                                * VOIR
                                *
                                * ------------------------------------------------- */ ?>

                                <div class="btn-group file-operations" style="margin-right: 20px">

                                    <? if ($d['s3']) : ?>

                                        <a class="file-link btn btn-outline-primary" target="_blank" data-toggle="tooltip" data-title="Vous pouvez également cliquer sur l'image du document pour le voir."
                                           href="<?= $this->config->item('s3_url', 'amazon') . 'soumissions/' . $d['doc_filename'] . '?' . $d['doc_sha256_file']; ?>">
                                            Voir
                                        </a>

                                    <? else : ?>

                                        <a class="file-link btn btn-outline-primary" target="_blank" data-toggle="tooltip" data-title="Vous pouvez également cliquer sur l'image du document pour le voir."
                                           href="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_filename'] . '?' . $d['doc_sha256_file']; ?>">
                                            Voir
                                        </a>

                                    <? endif; ?>

                                </div>

                               <? 
                               /* -----------------------------------
                                *
                                * DELETE
                                *
                                * ----------------------------------- */ ?>
                                <div class="btn-group file-operations">
                                    <div class="file-delete btn btn-outline-danger" data-doc_id="<?= $d['doc_id']; ?>">
                                        <span class="d-md-none">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                        <span class="d-none d-md-inline">
                                            <i class="fa fa-trash" style="margin-right: 5px"></i>
                                            Effacer
                                        </span>
                                    </div>
                                </div>

                                <span class="file-processing-spinner d-none">
                                    <i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw" style="color: dodgerblue"></i>
                                </span>

                            <? endif; ?>
                        </td>
                    </tr>

                    <? endforeach; ?>

                <? endif; ?>

            </tbody>
            </table>
        </div> <!-- .files-list -->

        <?
        /* ----------------------------------------------------------- 
         *
         * FILE LIST TEMPLATE
         *
         * ----------------------------------------------------------- */ ?>
        <div id="file-list-template-<?= $question_id; ?>" class="file-list-template d-none">
            <table class="table" style="margin: 0">
                <tbody>

                <tr class="file-data" id="" data-doc_id="">

                        <? 
                        /* ----------------------------------------------------
                         *
                         * DOCUMENT IMAGE
                         *
                         * --------------------------------------------------- */ ?>
                        <td style="width: 150px;">
                            <a class="img-original" target="_blank" href="" target="_blank">
                                <img class="img-thumbnail center-block" src=""></img>
                            </a>
                        </td>

                        <? 
                        /* ----------------------------------------------------
                         *
                         * FILE OPERATIONS
                         *
                         * ---------------------------------------------------- */ ?>
                        <td style="width: 450px; text-align: right; vertical-align: middle">

                           <? 
                           /* -------------------------------------------------
                            *
                            * IMAGE ROTATION (LEFT & RIGHT)
                            *
                            * ------------------------------------------------- */ ?>
                            <div class="btn-group image-rotation file-operations d-none" role="group" style="margin-right: 5px">
                                <div class="file-rotation btn btn-outline-secondary" data-rotation="left">
                                    <i class="fa fa-rotate-left icon-large"></i>
                                </div>
                                <div class="file-rotation btn btn-outline-secondary" data-rotation="right">
                                    <i class="fa fa-rotate-right icon-large"></i>
                                </div>
                            </div>

                           <? 
                           /* -------------------------------------------------
                            *
                            * VOIR
                            *
                            * ------------------------------------------------- */ ?>

                            <div class="btn-group file-operations" style="margin-right: 20px">

                                <a class="file-link btn btn-outline-primary" target="_blank" data-toggle="tooltip" data-title="Vous pouvez également cliquer sur l'image du document pour le voir." href="">
                                    Voir
                                </a>

                            </div>

                           <? 
                           /* -----------------------------------
                            *
                            * DELETE
                            *
                            * ----------------------------------- */ ?>
                            <div class="btn-group file-operations">
                                <div class="file-delete btn btn-outline-danger">
                                    <span class="d-md-none">
                                        <i class="fa fa-trash"></i>
                                    </span>
                                    <span class="d-none d-md-inline">
                                        <i class="fa fa-trash" style="margin-right: 5px"></i>
                                        Effacer
                                    </span>
                                </div>
                            </div>

                            <span class="file-processing-spinner d-none">
                                <i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw" style="color: dodgerblue"></i>
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div> <!-- .file-list-template -->

    </div> <!-- .documents-manager -->

</div> <!-- .question-reponses -->
