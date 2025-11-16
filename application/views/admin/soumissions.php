
<h5>Les soumissions <?= ! empty($soumissions) ? '(' . count($soumissions) . ')' : ''; ?></h5>

<div class="space"></div>

    <div id="soumissions-liste-btn" style="width: 100px" class="btn btn-sm btn-outline-primary active">Liste</div>
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
                    <th style="text-align: center">ID</th>
                    <th>Date</th>
                    <th style="text-align: center">Référence</th>
                    <th>Enseignant</th>
                    <th>Étudiant</th>
                    <th style="text-align: center">Cours</th>
                    <th style="text-align: center">Évaluation</th>
                    <th style="text-align: center">Visible</th>
                    <th style="text-align: center">Durée</th>
                    <th style="text-align: center">Points</th>
                </tr>
            </thead>

            <tbody>
                <? 
                    $i = 0;                    

                    foreach($soumissions as $s) : 

                        if ($i > 250)
                            break;

                        $i++;

                        $cours_data = (array) json_decode($s['cours_data']);
                ?>
                    <tr>
                        <td style="text-align: center"><?= $s['soumission_id']; ?></td>
                        <td><?= $s['soumission_date']; ?></td>
                        <td style="text-align: center"><a href="<?= base_url() . 'corrections/voir/' . $s['soumission_reference']; ?>"><?= $s['soumission_reference']; ?></a></td>
                        <td><?= $enseignants[$s['enseignant_id']]['prenom'] . ' ' . $enseignants[$s['enseignant_id']]['nom']; ?></td>
                        <td><?= $s['prenom_nom']; ?></td>
                        <td style="text-align: center"><?= @$cours_data['cours_code_court']; ?></td>
                        <td style="text-align: center"><a href="<?= base_url() . 'evaluations/editeur/' . $s['evaluation_id']; ?>"><?= $s['evaluation_id']; ?></a></td>
                        <td style="text-align: center"><?= $s['permettre_visualisation'] ? 'oui' : 'non'; ?></td>
                        <td style="text-align: center"><?= $s['duree']; ?></td>
                        <td style="text-align: right">
                            <? if ( ! $s['corrections_terminees']) : ?>

                                à corriger

                            <? else : ?>

                                <? if ($s['points_total'] > 0) : ?>
                                    <?= my_number_format($s['points_obtenus']) . ' / ' . my_number_format($s['points_total']); ?>
                                    (<?= number_format($s['points_obtenus'] / $s['points_total'] * 100); ?>%)
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
