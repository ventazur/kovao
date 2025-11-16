<?
/* ----------------------------------------------------------------------------
 *
 * Administration > Groupe > Soumissions
 *
 * ---------------------------------------------------------------------------- */ ?>

<h5>Les soumissions du groupe</h5>

<div class="space"></div>

<div id="soumissions-liste-btn" style="width: 150px" class="btn btn-sm btn-outline-primary active">
    Liste 
    <?= empty($soumissions) || ! is_array($soumissions) ? '' : '(' . count($soumissions) . ')'; ?> 
</div>

<div class="btn-group" role="group">
    <div id="soumissions-journees-btn" style="width: 200px" class="btn btn-sm btn-outline-primary">Les dernières journées</div>
    <div id="soumissions-meilleures-btn" style="width: 200px" class="btn btn-sm btn-outline-primary">Les meilleures journées</div>
</div>

<div class="space"></div>

<div id="soumissions">

<? if (empty($soumissions)) : ?>

    <div class="space"></div>

    <i class="fa fa-exclamation-circle"></i> Aucune soumission

<? else : ?>

    <div id="soumissions-liste">

        <div class="hspace"></div>

        <table class="table admin-table">

            <thead>
                <tr>
                    <th style="width: 180px">Date</th>
                    <th style="width: 180px">Enseignant</th>
                    <th style="width: 250px">Étudiant</th>
                    <th style="text-align: center">Cours</th>
                    <th style="text-align: center">Évaluation</th>
                    <th style="text-align: center">Référence</th>
                    <th style="width: 150px; text-align: center">Points</th>
                </tr>
            </thead>

            <tbody>
                <? 
                    $i = 0;                    

                    foreach($soumissions as $s) : 

                        if ($i > 250)
                            break;

                        $i++;

                        $cours_data = (array) json_decode(gzuncompress($s['cours_data_gz']), TRUE);
                ?>
                    <tr>
                        <td class="mono"><?= $s['soumission_date']; ?></td>
                        <td>
                            <a href="<?= base_url() . 'admin/enseignant/' . $s['enseignant_id']; ?>" target="_blank">
                                <?= substr(@$enseignants[$s['enseignant_id']]['prenom'], 0, 1) . '. ' . @$enseignants[$s['enseignant_id']]['nom']; ?>
                            </a>
                        </td>
                        <td>
                            <? if ( ! empty($s['etudiant_id'])) : ?>
                                <a href="<?= base_url() . 'admin/etudiant/' . $s['etudiant_id']; ?>" target="_blank"><?= $s['prenom_nom']; ?></a>

                                <a target="_blank" href="<?= base_url() . 'admin/usurper/etudiant/' . $s['etudiant_id']; ?>">
                                    <svg style="margin-left: 3px" viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                                      <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    </svg>
                                </a>
                            <? else : ?>
                                <?= $s['prenom_nom']; ?>
                            <? endif; ?>

                            <? if ( ! empty($s['courriel'])) : ?>
                                <i class="fa fa-send" style="color: dodgerblue"></i>
                            <? endif; ?>
                        </td>
                        <td style="text-align: center"><?= @$cours_data['cours_code_court']; ?></td>
                        <td style="text-align: center"><a href="<?= base_url() . 'evaluations/editeur/' . $s['evaluation_id']; ?>"><?= $s['evaluation_id']; ?></a></td>
                        <td style="text-align: center"><a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>"><?= $s['soumission_reference']; ?></a></td>
                        <td style="text-align: right">
                            <? if ( ! $s['corrections_terminees']) : ?>

                                à corriger

                            <? else : ?>

                                <? if ($s['points_evaluation'] > 0) : ?>
                                    <?= my_number_format($s['points_obtenus']) . ' / ' . my_number_format($s['points_evaluation']); ?>
                                    (<?= number_format($s['points_obtenus'] / $s['points_evaluation'] * 100); ?>%)
                                <? else : ?>
                                    0/0 (0%)
                                <? endif; ?>

                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>

    </div> <!-- #soumissions-liste -->

    <? 
    /* ----------------------------------------------------
     *
     * LES (DERNIERES) JOURNEES DE SOUMISSIONS
     *
     * ---------------------------------------------------- */ ?>

    <div id="soumissions-journees" class="d-none">

        <div class="hspace"></div>

        <? if (empty($dernieres_journees)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucune donnée pour les dernières journées de soumissions

        <? else : ?>

            <table class="table" style="border: 1px solid #ddd; font-size: 0.85em">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 50px">No</th>
                        <th style="width: 100px">Date</th>
                        <th style="width: 850px;">Nombre de soumissions</th>
                    </tr>
                </thead>
                <tbody>
                    <?  
                        $total = 0; 
                        $i = 0; 
                    
                        foreach($dernieres_journees as $date => $nombre) : 
                            
                            $i++; 
                            $total += $nombre; 
                    ?>
                        <tr>
                            <td style="text-align: center"><?= $i; ?></td> 
                            <td><?= $date; ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar <?= date_humanize(date('U')) == $date ? 'bg-danger' : ''; ?>" role="progressbar" style="width: <?= ($nombre/max($dernieres_journees)*100) . '%'; ?>" aria-valuenow="<?= $nombre; ?>" aria-valuemin="0" aria-valuemax="<?= max($dernieres_journees); ?>"><?= $nombre; ?></div>
                                </div>
                            </td>
                        </tr>
                    <? endforeach; ?>
                        <tr>
                            <td colspan="3" style="font-weight: bold">
                                Moyenne : <?= my_number_format($total/$i); ?> / jour
                            </td>
                        </tr>
                </tbody>
            </table>

        <? endif; ?>

    </div> <!-- #soumissions-journees -->

    <? 
    /* ----------------------------------------------------
     *
     * LES MEILLEURES JOURNEES DE SOUMISSIONS
     *
     * ---------------------------------------------------- */ ?>

    <div id="soumissions-meilleures" class="d-none">

        <div class="hspace"></div>

        <? if (empty($meilleures_journees)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucune donnée pour les meilleurs journées de soumissions

        <? else : ?>

            <table class="table" style="border: 1px solid #ddd; font-size: 0.85em">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 50px">No</th>
                        <th style="width: 100px">Date</th>
                        <th style="width: 850px;">Nombre de soumissions</th>
                    </tr>
                </thead>
                <tbody>
                    <? 
                        $i = 0; 
                            
                        foreach($meilleures_journees as $date => $nombre) : 
                            
                            $i++; 
                    ?>
                        <tr>
                            <td style="text-align: center"><?= $i; ?></td> 
                            <td><?= $date; ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar <?= date_humanize(date('U')) == $date ? 'bg-danger' : ''; ?>" role="progressbar" style="width: <?= ($nombre/max($meilleures_journees)*100) . '%'; ?>" aria-valuenow="<?= $nombre; ?>" aria-valuemin="0" aria-valuemax="<?= max($meilleures_journees); ?>"><?= $nombre; ?></div>
                                </div>
                            </td>
                        </tr>
                    <? endforeach; ?>

                </tbody>
            </table>

        <? endif; ?>

    </div> <!-- #soumissions-meilleures-journees -->

<? endif; ?>

</div> <!-- #soummissions -->
