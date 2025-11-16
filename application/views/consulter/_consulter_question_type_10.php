<? 
/* --------------------------------------------------------
* 
* Question a repondre par televersement de documents (TYPE 10)
*
* --------------------------------------------------------- */ ?>

<div class="corriger-reponse-repondue">

    <div class="font-weight-bold">
        Votre réponse : 
    </div>

    <div class="hspace"></div>

    <div>
        <? if (@$q['question_non_repondue']) : ?>

            Vous n'avez rien répondu à cette question.

        <? else : ?>

            <?= filter_symbols($q['reponse_repondue_texte']); ?>

        <? endif; ?>
    </div>

</div> <!--- .corriger-reponse-repondue -->

<?
/* --------------------------------------------------------------------
 *
 * Documents
 *
 * -------------------------------------------------------------------- */ ?>

<? if (empty($documents[$question_id])) : ?>

    <div style="font-size: 0.85em; padding: 0px 15px 20px 15px">

        <i class="fa fa-exclamation-circle" style="color: #aaa"></i>
        Aucun document trouvé pour cette question.

    </div>

<? else : ?>

    <div class="corriger-reponse-repondue-documents">

        <div class="corriger-reponse-repondue-documents-titre">

            <i class="fa fa-file-o" style="margin-right: 8px"></i>
            Document<?= count($documents[$question_id]) > 1 ? 's' : ''; ?> 
            téléversé<?= count($documents[$question_id]) > 1 ? 's' : ''; ?>
            (<?= count($documents[$question_id]); ?>) :

        </div>

        <div id="documents-manager-<?= $question_id; ?>" class="documents-manager">

            <?
            /* -----------------------------------------------------------
             * 
             * Liste des documents (files list)
             *
             * ----------------------------------------------------------- */ ?>
            <div id="files-list-<?= $question_id; ?>" class="files-list col-md-12">

                <table class="table table-borderless" style="margin: 0">
                    <tbody>

                    <? if ( ! empty($documents[$question_id])) : ?>

                        <? 
                            $j = 0; 

                            foreach($documents[$question_id] as $d) : 

                                ++$j;
                                $doc_id = $d['doc_id'];
                        ?>

                        <tr class="file-data" id="file-<?= $doc_id; ?>" data-doc_id="<?= $doc_id; ?>">

                            <? 
                            /* ----------------------------------------------------
                             *
                             * Image thumbnail du document
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

                                    <? if (file_exists(FCPATH . $this->config->item('documents_path_s') . $d['doc_filename'])) : ?>

                                        <a class="img-original" target="_blank" href="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_filename'] . '?' . $d['doc_sha256_file']; ?>" target="_blank">

                                            <? if ($this->config->item($d['doc_mime_type'], 'documents_mime_types_properties')['tn']) : ?>

                                                <img class="img-thumbnail center-block" src="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_tn_filename'] . '?' . $d['doc_sha256_file']; ?>"></img>
                
                                            <? else : ?>

                                                <img class="img-thumbnail center-block" src="<?= base_url() . 'assets/images/' . $this->config->item($d['doc_mime_type'], 'documents_mime_types_properties')['tn_fichier']; ?>"></img>

                                            <? endif; ?>

                                        </a>

                                    <? else : ?>

                                        <img class="img-thumbnail center-block" style="width: 100px" src="<?= base_url() . 'assets/images/image_non_disponible.png'; ?>"></img>

                                    <? endif; ?>

                                <? endif; ?>
                            </td>

                            <td style="vertical-align: middle">
                                <span style="font-size: 0.9em;">
                                    Format : <?= $d['doc_mime_type']; ?>

                                    <? if ($this->est_enseignant && $this->appartenance_groupe) : ?>

                                        <? if (count($documents_verification[$doc_id]) > 0) : ?>
                                            <div style="margin-top: 3px">

                                                Conformité : 

                                                <span style="color: crimson">
                                                    <? if (count($documents_verification[$doc_id]) > 1) : ?>
                                                        Plusieurs documents (<?= count($documents_verification); ?>) identiques ont été trouvés.
                                                    <? else : ?>
                                                        Un document identique a été trouvé.
                                                    <? endif; ?>
                                                </span>

                                                <? if ($this->enseignant['privilege'] > 89) : ?>

                                                    <span class="montrer-details">
                                                        Montrer les détails
                                                    </span>
                                                    <div class="montrer-details-document" style="display: none">
                                                        <?= p($documents_verification[$doc_id]); ?>
                                                    </div>

                                                <? endif; ?>
                                            </div>
                                        <? else : ?>
                                            <div>
                                                Conformité : Ce document est unique.
                                                <i class="fa fa-info-circle" 
                                                   style="color: #aaa; margin-left: 2px;"
                                                   data-toggle="popover"
                                                   data-content="Parmi tous les documents téléversés de la base de données (incluant tous les cours et tous les enseignants), ce document est unique."></i>
                                            </div>
                                        <? endif; ?>
                                    <? endif; ?>

                                </span>
                            </td>

                            <? 
                            /* ----------------------------------------------------
                             *
                             * Operations sur les documents
                             *
                             * ---------------------------------------------------- */ ?>
                            <td class="file-operations-group" style="width: 450px; text-align: right;vertical-align: middle">

                               <? 
                               /* -------------------------------------------------
                                *
                                * Rotation des images
                                *
                                * ------------------------------------------------- */ ?>
                                <? /* 
                                <? if ($this->est_enseignant && ($soumission['enseignant_id'] == $this->enseignant_id)) : ?>
                                    <div class="btn-group image-rotation file-operations <?= $d['doc_is_image'] ? '' : 'd-none'; ?>" role="group" style="margin-right: 10px">
                                        <div class="file-rotation btn btn-outline-secondary" data-rotation="left" data-doc_id="<?= $d['doc_id']; ?>">
                                            <i class="fa fa-rotate-left icon-large"></i>
                                        </div>
                                        <div class="file-rotation btn btn-outline-secondary" data-rotation="right" data-doc_id="<?= $d['doc_id']; ?>">
                                            <i class="fa fa-rotate-right icon-large"></i>
                                        </div>
                                    </div>
                                <? endif; ?>
                                */ ?>

                                <? 
                                /* -----------------------------------
                                 *
                                 * Voir
                                 *
                                 * ----------------------------------- */ ?>
                                <div class="btn-group file-operations" style="margin-right: 8px">

                                    <? if ($d['s3']) : ?>

                                        <a class="btn btn-outline-primary" target="_blank" data-toggle="tooltip" data-title="Vous pouvez également cliquer sur l'image du document pour le voir."
                                           href="<?= $this->config->item('s3_url', 'amazon') . 'soumissions/' . $d['doc_filename'] . '?' . $d['ajout_epoch']; ?>">
                                            Voir
                                        </a>

                                    <? else : ?>

                                        <a class="btn btn-outline-primary" target="_blank" data-toggle="tooltip" data-title="Vous pouvez également cliquer sur l'image du document pour le voir."
                                           href="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_filename'] . '?' . $d['doc_sha256_file']; ?>">
                                            Voir
                                        </a>

                                    <? endif; ?>

                                </div>

                                <? 
                                /* -----------------------------------
                                 *
                                 * Telechargement
                                 *
                                 * ----------------------------------- */ ?>

                                <span class="d-none">

                                    <? $nom_etudiant_fichier = str_replace(' ', '_', strip_accents($soumission['prenom_nom'])); ?>

                                    <div class="btn-group file-operations" style="margin-right: 8px">
                                        <a class="file-download btn btn-outline-primary" 
                                           href="<?= base_url() . $this->config->item('documents_path_s') . $d['doc_filename'] . '?' . $d['ajout_epoch']; ?>" 
                                            download="<?= $nom_etudiant_fichier . '_' . $soumission['soumission_reference'] . '_q' . $i . '_' . $j; ?>">
                                           <i class="fa fa-cloud-download fa-lg"></i> Télécharger
                                        </a>
                                    </div>

                                </span>
                            </td>
                        </tr>

                        <? endforeach; ?>

                    <? endif; ?>

                </tbody>
                </table>
            </div> <!-- .files-list -->

        </div> <!-- .documents-manager -->
    
    </div> <!-- .corriger-reponse-repondue-documents -->

<? endif; ?>
