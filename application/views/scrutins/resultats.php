<?
/* ==================================================================
 *
 * SCRUTIN : RESULTATS
 *
 * ================================================================== */ ?>

<div id="scrutin">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>
            Résultats d'un scrutin
        </h4>

        <div class="space"></div>

        <? if ($scrutin['anonyme']) : ?>
            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Ce scrutin <?= $scrutin['termine'] || $scrutin['echeance_epoch'] < $this->now_epoch ? 'était' : 'est'; ?> <strong>anonyme</strong>.
            </div>
        <? endif; ?>

        <? if ( ! $scrutin['termine'] && $scrutin['echeance_epoch'] > $this->now_epoch): ?>

            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Ce scrutin 
                a débuté le <strong><?= date_french_full($scrutin['lance_epoch']); ?></strong> 
                et est en vigueur jusqu'au <strong><?= date_french_full($scrutin['echeance_epoch']); ?></strong>.
            </div>

        <? elseif ($scrutin['termine']) : ?>

            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Ce scrutin 
                a débuté le <strong><?= date_french_full($scrutin['lance_epoch']); ?></strong> 
                et est <span style="color: crimson; font-weight: 700">terminé</span> depuis le <strong><?= date_french_full($scrutin['termine_epoch']); ?></strong>.
            </div>

        <? else : ?>

            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Ce scrutin 
                a débuté le <strong><?= date_french_full($scrutin['lance_epoch']); ?></strong> 
                et est <span style="color: crimson; font-weight: 700">échu</span> depuis le <strong><?= date_french_full($scrutin['echeance_epoch']); ?></strong>.
            </div>

        <? endif; ?>

        <? if (1 == 2) : ?>
            <div style="font-size: 0.9em">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Ce scrutin a été créé par <?= $scrutin['prenom'] . ' ' . $scrutin['nom']; ?>.
            </div>
        <? endif; ?>

        <div class="tspace"></div>

        <div id="scrutin-question" style="font-size: 1em; padding: 20px; <?= ! empty($documents) ? 'border-radius: 3px 3px 0 0; border-bottom: 1px solid #9FA8DA;' : ''; ?>">
            <?= $scrutin['scrutin_texte']; ?>
        </div>

        <? if ( ! empty($documents)) : 

                $plusieurs = count($documents) > 1 ? 's' : '';
        ?>
            <? 
            $i=0;

            $mime_view = array(
                'application/pdf', 'image/jpg', 'image/jpeg', 'image/png'
            );

            foreach($documents as $d) : 

                $i++;
                $doc_url = base_url() . $this->config->item('documents_path') . $d['doc_filename'];
            ?>
                <div class="scrutin-document" style="<?= $i == 0 ? 'border-top: 0' : ''; ?>;">

                    <div class="row">
                        <div class="col-9 text-truncate">
                            <div style="padding-top: 4px">

                                <i class="fa fa-lg <?= determiner_file_icon($d['doc_mime_type']); ?>" style="margin-right: 10px;"></i>

                                <a <?= in_array($d['doc_mime_type'], $mime_view) ? 'href="' . $doc_url . '"' : ''; ?>>
                                    <? if (empty($d['doc_caption'])) : ?>
                                        Document <?= $scrutin_id . '.' . $i; ?>
                                    <? else : ?>
                                        <?= $d['doc_caption']; ?>
                                    <? endif; ?>
                                </a>
                            </div>
                        </div> <!-- .col-8 -->
                        <div class="col-3" style="text-align: right">
                            <a href="<?= $doc_url; ?>" class="btn btn-sm btn-outline-dark" download="Document<?= $scrutin_id . '_' . $i . '.' . determiner_extension($d['doc_filename']); ?>">
                                <i class="fa fa-download"></i> 
                            </a>
                        </div>

                    </div>

                </div>

            <? endforeach; ?>

        <? endif; ?>

        <div class="tspace"></div>

        <div id="scrutin-resultats-choix">

            <i class="fa fa-square" style="color: limegreen; margin-right: 10px"></i> 
            <strong>
            Les résultats par choix
            </strong>

            <div class="tspace"></div>

            <table class="table" style="margin: 0; font-size: 0.95em">
                <tbody>

                    <? foreach($choix as $c) : 

                        $scrutin_lance_choix_id = $c['scrutin_lance_choix_id'];

                        if ( ! array_key_exists($scrutin_lance_choix_id, $resultats))
                        {
                            $resultat = 0;
                        }
                        else
                        {
                            $resultat = $resultats[$scrutin_lance_choix_id];
                        }
                    ?>

                        <tr>
                            <td><?= $c['choix_texte']; ?></td>
                            <td style="text-align: left; width: 300px">
                                <div class="progress" style="margin-top: 4px">
                                    <div class="progress-bar" role="progressbar" style="background-color: limegreen; width: <?= $resultats_total > 0 ? ($resultat/$resultats_total * 100) : 0; ?>%"></div>
                                </div>
                            </td>
                            <td style="width: 125px">
                                <?= $resultat; ?> (<strong><?= $resultats_total > 0 ? (number_format($resultat/$resultats_total * 100)) : 0; ?>%</strong>)
                            </td>
                        </tr>
                    
                    <? endforeach; ?>
                </tbody>
            </table>

            <div class="tspace"></div>

            <div style="font-size: 0.9em;">
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Il y a eu <?= $resultats_total; ?> participant<?= $resultats_total > 1 ? 's' : ''; ?> sur <?= $participants_total; ?> qui <?= $resultats_total > 1 ? 'ont' : 'a'; ?> voté.<br />
                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                Le taux de participation est de <strong><?= number_format($resultats_total / $participants_total * 100); ?>%</strong>.
            </div>

        </div> <!-- #scrutin-choix -->

        <div class="tspace"></div>

        <div id="scrutin-resultats-participant">

            <i class="fa fa-square" style="color: #444; margin-right: 10px"></i> 
            <strong>
            Les résultats par participant
            </strong>

            <div class="tspace"></div>

            <table class="table" style="margin: 0; font-size: 0.95em;">
                <thead>
                    <tr>
                        <th style="width: 300px">
                            <?= $scrutin['anonyme'] ? 'Empreinte' : 'Participant'; ?>
                        </th>
                        <th>Choix</th>
                    </tr>
                </thead>
                <tbody>

                    <? if ($scrutin['anonyme']) : ?>

                        <? foreach($votes as $v) : ?>

                            <tr>
                                <td><?= substr($v['vote_sha256'], 0, 10); ?></td>
                                <td>
                                    <?= $choix[$v['scrutin_lance_choix_id']]['choix_texte']; ?>
                                </td>
                            </tr>

                        <? endforeach; ?>

                    <? else : ?>

                        <? foreach($enseignants as $e) : 

                            if ( ! array_key_exists($e['enseignant_id'], $participants)) continue;

                        ?>
                            <tr>
                                <td><?= $e['prenom'] . ' ' . $e['nom']; ?></td>
                                <td>
                                    <? if ( ! array_key_exists($e['enseignant_id'], $votes)) : ?>
                                        <i class="fa fa-times-rectangle-o" style="margin-right: 5px"></i>
                                    <? else : ?>
                                        <?= $choix[$votes[$e['enseignant_id']]['scrutin_lance_choix_id']]['choix_texte']; ?>
                                    <? endif; ?>
                                </td>
                            </tr>
                        
                        <? endforeach; ?>

                    <? endif; ?>
            </tbody>
        </table>

        </div> <!-- #scrutin-participants -->

        <? if ($scrutin['anonyme']) : ?>

            <div class="tspace"></div>

            <div id="scrutin-resultats-participant">

                <i class="fa fa-square" style="color: #444; margin-right: 10px"></i> 
                <strong>
                Les participants à ce scrutin
                </strong>

                <div class="space"></div>

                <? $i = 0; 
                   foreach($participants as $p) : 

                   $i++;
                ?>

                    <?= $p['prenom'] . ' ' . $p['nom'] . ($i < count($participants) ? ', ' : ''); ?> 

                <? endforeach; ?>

        <? endif; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils -->
