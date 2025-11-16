<div id="sommaire">

<div class="container-fluid">
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h4><?= $evaluation['evaluation_titre']; ?></h4>

    <div class="hspace"></div>

    <li>Évaluation ID : <?= $evaluation_id; ?></li>
    <li>Version imprimable générée : <?= date_humanize($this->now_epoch, TRUE); ?></li>

    <div class="space"></div>

    <? if ( ! empty($questions)) : ?>

        <? $i=0; foreach($questions as $question_id => $q) : $i++; ?>

            <div class="table-questions" style="margin-bottom: 10px; border: 1px solid #aaa; border-radius: 3px; padding: 10px;">
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td>
                                <strong>Question <?= $i; ?></strong>
                                <span style="font-size: 0.75em; padding: 2px 4px 2px 4px; border-radius: 3px; margin-left: 5px; background: #eee"><?= $question_id; ?></span>

                                <? if ( ! empty($q['bloc_id'])) : ?>
                                    <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px;"></i>
                                    <span style="font-size: 0.8em; border: 1px solid #222; padding: 2px 4px 2px 4px; border-radius: 3px; background: #222; color: #fff"><?= $blocs[$q['bloc_id']]['bloc_label']; ?></span>
                                <? endif; ?>
                            </td>
                            
                            <? if (array_key_exists($question_id, $images)) : ?>
                                <td rowspan="4" style="text-align: right">
                                    <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 200px; max-width: 500px"></img>
                                </td>
                            <? endif; ?>
                        </tr>
                        <tr>
                            <td><?= $this->config->item('questions_types')[$q['question_type']]['desc']; ?></td>
                        </tr>
                        <tr>    
                            <td style="padding-top: 5px; padding-bottom: 5px">
                                <? $texte = json_decode($q['question_texte']) ?: $q['question_texte']; ?>
        
                                <i class="fa fa-question-circle"></i> <?= $texte; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <? if ( ! empty($reponses[$question_id])) : ?>
                    
                                    <? foreach($reponses[$question_id] as $reponse_id => $r) : ?>

                                        <? if ($r['reponse_correcte']) : ?> 
                                            <strong>
                                        <? endif; ?>

                                        [<?= $r['reponse_texte']; ?>]

                                        <? if ($r['reponse_correcte']) : ?> 
                                            </strong>
                                        <? endif; ?>

                                    <? endforeach; ?>

                                <? else : ?>

                                    [aucune réponse définie]

                                <? endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <? endforeach; ?>

    <? endif; // ! empty($questions); ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->
</div> <!-- .container-fluid -->

</div> <!-- #resultats -->
