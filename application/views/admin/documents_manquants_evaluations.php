<?
/* ----------------------------------------------------------------------------
 *
 * Admin > Documents manquants des evaluations
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-documents-superflus">

<?
/* ----------------------------------------------------------------------------
 *
 * Les styles specifiques
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>
    h4 {
        font-weight: 300;
    }
</style>

<div class="container-fluid">
        
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Admin <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 10px"></i>Documents manquants des évaluations (<?= count($documents_manquants); ?>)</h4>

        <div class="space"></div>

        <? if (empty($documents_manquants)) : ?>

            <div style="font-family: Lato; font-weight: 300;">
                <i class="fa fa-exclamation-circle"></i> Aucun document manquant sur <?= $documents_verifies; ?> documents vérifiés
            </div>

        <? else : ?>

            <table class="table table-sm" style="font-size: 0.8em">
                <thead>
                    <tr>
                        <th style="width: 100px; text-align: center">Doc ID</th>
                        <th style="width: 100px; text-align: center">Groupe ID</th>
                        <th style="width: 180px">Date</th>
                        <th>Nom du fichier</th>
                        <th style="width: 100px">MIME</th>
                        <th style="width: 80px; text-align: center">Effacée</th>
                        <th style="width: 80px; text-align: center">Disque</th>
                        <th style="width: 80px; text-align: center">S3</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($documents_manquants as $d) : ?>
                        <tr>
                            <td style="text-align: center"><?= $d['doc_id']; ?></td>
                            <td style="text-align: center"><?= $d['groupe_id']; ?></td>
                            <td><?= $d['ajout_date']; ?></td>
                            <td><?= $d['doc_filename']; ?></td>
                            <td><?= $d['doc_mime_type']; ?></td>
                            <td style="text-align: center">
                                <? if ($d['efface']) : ?>

                                    <i class="fa fa-trash"></i>

                                <? else : ?>

                                    ×

                                <? endif; ?>
                            </td>
                            <td style="text-align: center">
                                <? if (file_exists(FCPATH . $this->config->item('documents_path') . $d['doc_filename'])) : ?>

                                    <a href="<?= base_url() . $this->config->item('documents_path') . $d['doc_filename']; ?>" target="_blank">
                                        <i class="fa fa-file-image-o"></i>
                                    </a>

                                <? else : ?>

                                    ×

                                <? endif; ?>
                            </td>
                            <td style="text-align: center">
                                <? if ($d['s3']) : ?>

                                    <a href="<?= $this->config->item('s3_url', 'amazon') . 'evaluations/' . $d['doc_filename']; ?>" target="_blank">
                                        <i class="fa fa-amazon"></i>
                                    </a>

                                <? else : ?>

                                    ×

                                <? endif; ?>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        <? endif; ?>


    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-documents-manquants-evaluations -->
