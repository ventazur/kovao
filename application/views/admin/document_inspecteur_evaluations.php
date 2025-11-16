<div id="document_info">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="hspace"></div>

    <h5>
        <svg style="margin-top: -2px; margin-right: 7px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-break" viewBox="0 0 16 16">
            <path d="M0 10.5a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5zM12 0H4a2 2 0 0 0-2 2v7h1V2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v7h1V2a2 2 0 0 0-2-2zm2 12h-1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-2H2v2a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2z"/>
        </svg>
        Inspecteur de document
    </h5>

    <? if (empty($doc)) : ?>

        <div class="space"></div>

        <span style="font-weight: 300; font-size: 0.9em">
            <i class="fa fa-exclamation-circle"></i>
            Ce document est introuvable.
        </span>

    <? else : ?>

        <div class="space"></div>

        <div style="text-align: center">
        <img src="https://kovao.s3.amazonaws.com/<?= $type . '/' . $doc['doc_filename']; ?>" style="border: 3px solid #ddd; max-width: 600px; max-height: 300px; padding: 5px;"></img>
        </div>

        <div class="tspace"></div>
        
        <table class="table table" style="font-size: 0.8em; border-bottom: 1px solid #ddd;">
            <thead>
                <tr>
                    <th style="width: 100px">Doc ID</th>
                    <th>Nom du fichier</th>
                    <th>Hash</th>
                    <th style="width: 80px; text-align: center">Ens ID</th>
                    <th style="width: 100px; text-align: center">Question ID</th>
                    <th style="width: 200px">Date d'ajout</th>
                    <th style="width: 100px; text-align: center">Effacé</th>
                    <th style="width: 200px">Date d'effacement</th>
                </tr>
            </thead>

            <tbody>
                <? foreach($docs as $d) : ?>
                    <tr style="<?= $id == $d['doc_id'] ? 'background: #FFF9C4;' : ''; ?>">
                        <td>
                            <a href="<?= base_url() . 'admin/document/id/' . $d['doc_id'] . '/type/evaluations'; ?>">
                                <?= $d['doc_id']; ?>
                            </a>
                        </td>
                        <td>
                            <a href="https://kovao.s3.amazonaws.com/<?= $type . '/' . $d['doc_filename']; ?>" target="_blank">
                                <?= $d['doc_filename']; ?>
                            </a>
                        </td>
                        <td><?= substr($d['doc_sha256_file'], 0, 32); ?></td>
                        <td style="text-align: center">
                            <a href="<?= base_url() . 'admin/enseignant/' . $d['enseignant_id']; ?>">
                                <?= $d['enseignant_id']; ?>
                            </a>
                        </td>
                        <td style="text-align: center"><?= $d['question_id']; ?></td>
                        <td><?= $d['ajout_date']; ?></td>
                        <td style="text-align: center"><?= $d['efface'] ? 'oui' : 'non'; ?></td>
                        <td><?= $d['efface_date']; ?></td>
                    </tr>
                <? endforeach; ?>

            </tbody>
        </table>

        <?
        /* ------------------------------------------------------------------------
         *
         * Documents d'une evaluation (image)
         *
         * ------------------------------------------------------------------------ */ ?>

        <div class="space"></div>

        <h5>Les documents reliés à ces questions</h5>

        <? if (empty($docs_questions)) : ?>

            <div class="space"></div>

            <i class="fa fa-exclamation-circle"></i>
            Il n'y a aucun document relié à ces questions.

        <? else : ?>

            <div class="space"></div>

            <table class="table" style="font-size: 0.8em; border-bottom: 1px solid #ddd;">
                <thead>
                    <tr>
                        <th>Question ID</th>
                        <th>Eval ID</th>
                        <th>Doc ID</th>
                        <th>Nom du fichier</th>
                        <th>Hash</th>
                        <th style="width: 80px; text-align: center">Ens ID</th>
                        <th>Date d'ajout</th>
                        <th style="width: 100px; text-align: center">Effacé</th>
                        <th style="width: 200px">Date d'effacement</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($docs_questions as $d) : ?>
                        <tr style="background: <?= $id == $d['doc_id'] ? '#FFF9C4;' : ($d['doc_filename'] == $doc['doc_filename'] ? '#E3F2FD' : 'inherit'); ?>">
                            <td><?= $d['question_id']; ?></td>
                            <td>
                                <a href="<?= base_url() . 'evaluations/editeur/' . $d['evaluation_id']; ?>" target="_blank">
                                    <?= $d['evaluation_id']; ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= base_url() . 'admin/document/id/' . $d['doc_id'] . '/type/evaluations'; ?>">
                                    <?= $d['doc_id']; ?>
                                </a>
                            </td>
                            <td>
                                <a href="https://kovao.s3.amazonaws.com/<?= $type . '/' . $d['doc_filename']; ?>" target="_blank">
                                    <?= $d['doc_filename']; ?>
                                </a>
                            </td>
                            <td><?= substr($d['doc_sha256_file'], 0, 32); ?></td>
                            <td style="text-align: center">
                                <a href="<?= base_url() . 'admin/enseignant/' . $d['enseignant_id']; ?>">
                                    <?= $d['enseignant_id']; ?>
                                </a>
                            </td>
                            <td><?= $d['ajout_date']; ?></td>
                            <td style="text-align: center"><?= $d['efface'] ? 'oui' : 'non'; ?></td>
                            <td><?= $d['efface_date']; ?></td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * Les soumissioss reliés à ce document 
         *
         * ------------------------------------------------------------------------ */ ?>

        <div class="space"></div>

        <h5>Les soumissions reliées à ce document</h5>

        <? if (empty($soumissions)) : ?>

            <div class="space"></div>

            <i class="fa fa-exclamation-circle"></i>    
            Aucune soumission trouvée

        <? else : ?>

            <div class="space"></div>

            <table class="table" style="font-size: 0.8em; border-bottom: 1px solid #ddd;">
                <thead>
                    <tr>
                        <th style="width: 100px">ID</th>
                        <th style="width: 140px">Référence</th>
                        <th style="width: 300px">Nom de l'étudiant</th>
                        <th style="width: 100px; text-align: center">Etu ID</th>
                        <th style="width: 180px">Date</th>
                        <th>Question ID : Document (Doc ID)</th>
                        <th style="width: 80px; text-align: center">Trouvé</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                        foreach($soumissions as $s) : 

                            $trouves = array();
                            $trouve  = FALSE;

                            foreach($question_ids as $question_id) : 
                                
                                if (array_key_exists($question_id, $s['images_data']))
                                {
                                    $trouves[] = array(
                                       'status'         => TRUE,
                                       'question_id'    => $question_id,
                                       'doc_id'         => $s['images_data'][$question_id]['doc_id'],
                                       'doc_filename'   => $s['images_data'][$question_id]['doc_filename']
                                    );

                                    if ($s['images_data'][$question_id]['doc_filename'] == $doc['doc_filename'])
                                        $trouve = TRUE;
                                }

                            endforeach;
                    ?>
                        <tr>
                            <td><?= $s['soumission_id']; ?></td>
                            <td>
                                <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>" target="_blank">
                                    <?= $s['soumission_reference']; ?>
                                </a>
                                <a href="<?= base_url() . 'admin/voir_soumission/' . $s['soumission_reference']; ?>" target="_blank">
                                    (src)
                                </a>
                            </td>
                            <td><?= $s['prenom_nom']; ?></td>
                            <td style="text-align: center">
                                <? if ( ! empty($s['etudiant_id'])) : ?>
                                    <a href="<?= base_url() . 'admin/etudiant/' . $s['etudiant_id']; ?>">
                                        <?= $s['etudiant_id']; ?>
                                    </a>
                                <? else : ?>
                                    non-inscrit
                                <? endif; ?>
                            </td>
                            <td><?= $s['soumission_date']; ?></td>
                            <td>
                                <? if ( ! empty($trouves)) : ?>

                                    <? foreach($trouves as $t) : ?>

                                        <?= $t['question_id'] . ' : ' . $t['doc_filename'] . ' (' . $t['doc_id'] . ') '; ?>

                                    <? endforeach; ?>

                                <? endif; ?>

                            </td>
                            <td style="text-align: center; <?= $trouve ? 'background: limegreen; color: #fff;' : ''; ?>">
                                <?= $trouve ? 'OUI' : 'non'; ?>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        <? endif; ?>

    <? endif; // ! empty($doc) ?>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #document-info -->

