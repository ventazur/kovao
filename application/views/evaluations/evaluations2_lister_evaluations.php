<?
/* ------------------------------------------------------------------------
 *
 * Lister les evaluations
 *
 * ------------------------------------------------------------------------ */ ?>

<div id="lister-evaluations-contenu">

    <? foreach($cours_evaluations_existent as $c) : ?>

        <div id="<?= md5($cours_raw[$c]['cours_code']); ?>" class="cours-md5" style="display: block">

            <div class="tspace"></div>

            <h5><?= $cours_raw[$c]['cours_nom']; ?> (<?= $cours_raw[$c]['cours_code']; ?>)</h5>

            <div class="space"></div>

            <? foreach($evaluations as $e) : 

                if ($e['cours_id'] != $c)
                    continue;        
            ?>
                <?
                /* ----------------------------------------------------
                 *
                 * Evaluation
                 *
                 * ---------------------------------------------------- */ ?>

                <a class="evaluation-liste spinnable" href="<?= base_url() . 'evaluations/editeur/' . $e['evaluation_id']; ?>">

                    <div class="evaluation-liste row no-gutters">

                        <div class="col-8">

                            <table>
                                <tr>
                                    <td row-span="2">
                                        <span class="evaluation-titre-code-cours">
                                            <?= $cours_raw[$c]['cours_code_court']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $e['evaluation_titre']; ?>

                                        <? if ($e['lab']) : ?>
                                            <span style="margin-left: 5px; font-size: 0.8em; font-weight: 300; background: #444; color: #fff; border-radius: 3px; padding: 5px 8px 5px 8px">Laboratoire</span>
                                        <? endif; ?>
                                    </td>
                                <tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <? if ( ! $e['public'] && ! empty($e['evaluation_desc'])) : ?>

                                            <div style="color: #666; font-size: 0.8em"> 
                                                <?= strip_tags(_html_out($e['evaluation_desc'])); ?>
                                            </div>

                                        <? endif; ?>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div class="col-4" style="text-align: right"> 

                            <? if ($e['public']) : ?>

                                <? if ($e['cadenas']) : ?>
                                    <span class="badge"><i class="fa fa-lock fa-lg" style="margin-right: 5px; color: #222"></i></span>
                                <? endif; ?>

                                <? if ($e['archive']) : ?>
                                    <span class="badge" style="color: #FF5722; font-weight: 300">
                                        <?= ucfirst(@$enseignants[$e['enseignant_id']]['prenom']) . ' ' . ucfirst(@$enseignants[$e['enseignant_id']]['nom']); ?>
                                    </span>
                                <? else : ?>
                                    <span class="badge" style="color: #673AB7; font-weight: 300">
                                        <?= ucfirst(@$enseignants[$e['enseignant_id']]['prenom']) . ' ' . ucfirst(@$enseignants[$e['enseignant_id']]['nom']); ?>
                                    </span>
                                <? endif; ?>

                            <? endif; ?>

                            <? if ( ! $e['public']) : ?>

                                <? if ($e['formative']) : ?>
                                    <span class="badge" style="background: #64B5F6; color: #fff; font-weight: 300;">Formative</span>
                                <? endif; ?>

                                <? if ( ! $e['actif']) : ?>
                                    <span class="badge" style="background: crimson; color: #fff; font-weight: 300;">Désactivée</span>
                                <? endif; ?>

                                <? if ($e['archive']) : ?>
                                    <span class="badge" style="background: #00ACC1; color: #fff; font-weight: 300;">Archivée</span>
                                <? endif; ?>

                            <? else : ?>

                                <? if ($e['archive']) : ?>
                                    <span class="badge" style="background: #FF7043; color: #fff; font-weight: 300;">Archivée</span>
                                <? endif; ?>

                            <? endif; ?>

                            <i class="fa fa-angle-right fa-lg" style="margin-right: 5px; margin-left: 10px; color: #4527A0;"></i>
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>

                        </div>
                    </div>
                </a>

        <? endforeach; ?> 

        </div> <!-- #md5 -->

    <? endforeach; ?>

</div> <!-- #lister-evaluations-contenu -->
