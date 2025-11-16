<div id="historique-activite">

<h5>L'historique de l'activité</h5>

<div class="space"></div>

<div id="historique-activite-btn">

    <div class="btn-group" role="group">
        <div id="historique-jour-d-btn" style="width: 200px" class="btn btn-sm btn-outline-primary active">Les 30 dernières journées</div>
        <div id="historique-jour-btn" style="width: 200px" class="btn btn-sm btn-outline-primary">Les 50 meilleures journées</div>
    </div>

    <div class="btn-group ml-4" role="group">
        <div id="historique-mois-d-btn" style="width: 200px" class="btn btn-sm btn-outline-primary">Les 12 derniers mois</div>
        <div id="historique-mois-btn" style="width: 200px" class="btn btn-sm btn-outline-primary">Les 15 meilleurs mois</div>
    </div>

</div>

<div class="space"></div>

<? 
/* ----------------------------------------------------
*
* LES 30 DERNIERES JOURNEES D'ACTIVITE
*
* ---------------------------------------------------- */ ?>

<div id="historique-jour-d">

    <div class="hspace"></div>

<? if (empty($historique['jour_d'])) : ?>

    <div class="hspace"></div>

    <i class="fa fa-exclamation-circle"></i> Aucune activité enregistrée

<? else : ?>

    <table class="table" style="border: 1px solid #ddd; font-size: 0.85em">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px">No</th>
                <th style="width: 100px">Date</th>
                <th style="width: 850px;">Activité</th>
            </tr>
        </thead>
        <tbody>
            <?  
                $max = $historique['jour_d_max'];

                $i = 0; 

                foreach($historique['jour_d'] as $h) : 

                    $nombre = $h['jour_total'];

                    $date = substr($h['jour'], 0, 4) . '-' . substr($h['jour'], 4, 2) . '-' . substr($h['jour'], 6, 2);
        
                    $i++; 
            ?>
                <tr>
                    <td style="text-align: center"><?= $i; ?></td> 
                    <td><?= $date; ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar <?= date_humanize(date('U')) == $date ? 'bg-danger' : ''; ?>" role="progressbar" style="width: <?= ($nombre/$max*100) . '%'; ?>" aria-valuenow="<?= $nombre; ?>" aria-valuemin="0" aria-valuemax="<?= $max; ?>"><?= $nombre; ?></div>
                        </div>
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>

<? endif; ?>

</div> <!-- #historique-jour-d -->

<? 
/* ----------------------------------------------------
*
* LES MEILLEURES JOURS D'ACTIVITE
*
* ---------------------------------------------------- */ ?>

<div id="historique-jour" class="d-none">

    <div class="hspace"></div>

<? if (empty($historique['jour'])) : ?>

    <div class="hspace"></div>

    <i class="fa fa-exclamation-circle"></i> Aucune activité enregistrée

<? else : ?>

    <table class="table" style="border: 1px solid #ddd; font-size: 0.85em">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px">No</th>
                <th style="width: 100px">Date</th>
                <th style="width: 850px;">Activité</th>
            </tr>
        </thead>
        <tbody>
            <?  
                $max = 0;
                $i = 0; 
            
                foreach($historique['jour'] as $h) : 

                    $nombre = $h['jour_total'];

                    if ($nombre < 500)
                        continue;

                    $date = substr($h['jour'], 0, 4) . '-' . substr($h['jour'], 4, 2) . '-' . substr($h['jour'], 6, 2);
        
                    if ($nombre > $max) 
                        $max = $nombre;

                    $i++; 
            ?>
                <tr>
                    <td style="text-align: center"><?= $i; ?></td> 
                    <td><?= $date; ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar <?= date_humanize(date('U')) == $date ? 'bg-danger' : ''; ?>" role="progressbar" style="width: <?= ($nombre/$max*100) . '%'; ?>" aria-valuenow="<?= $nombre; ?>" aria-valuemin="0" aria-valuemax="<?= $max; ?>"><?= $nombre; ?></div>
                        </div>
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>

<? endif; ?>

</div> <!-- #historique-jour -->

<? 
/* ----------------------------------------------------
*
* LES 12 DERNIERS MOIS D'ACTIVITE
*
* ---------------------------------------------------- */ ?>

<div id="historique-mois-d" class="d-none">

    <div class="hspace"></div>

<? if (empty($historique['mois_d'])) : ?>

    <div class="hspace"></div>

    <i class="fa fa-exclamation-circle"></i> Aucune activité enregistrée

<? else : ?>

    <table class="table" style="border: 1px solid #ddd; font-size: 0.85em">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px">No</th>
                <th style="width: 100px">Date</th>
                <th style="width: 850px;">Activité</th>
            </tr>
        </thead>
        <tbody>
            <?  
                $max = $historique['mois_d_max'];

                $i = 0; 

                foreach($historique['mois_d'] as $h) : 

                    $nombre = $h['mois_total'];

                    $date = substr($h['mois'], 0, 4) . '-' . substr($h['mois'], 4, 2);
        
                    $i++; 
            ?>
                <tr>
                    <td style="text-align: center"><?= $i; ?></td> 
                    <td><?= $date; ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar <?= date('Y-m') == $date ? 'bg-danger' : ''; ?>" role="progressbar" style="width: <?= ($nombre/$max*100) . '%'; ?>" aria-valuenow="<?= $nombre; ?>" aria-valuemin="0" aria-valuemax="<?= $max; ?>"><?= $nombre; ?></div>
                        </div>
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>

<? endif; ?>

</div> <!-- #historique-mois-d -->

<? 
/* ----------------------------------------------------
*
* LES MEILLEURES MOIS D'ACTIVITE
*
* ---------------------------------------------------- */ ?>

<div id="historique-mois" class="d-none">

    <div class="hspace"></div>

<? if (empty($historique['mois'])) : ?>

    <div class="hspace"></div>

    <i class="fa fa-exclamation-circle"></i> Aucune activité enregistrée

<? else : ?>

    <table class="table" style="border: 1px solid #ddd; font-size: 0.85em">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px">No</th>
                <th style="width: 100px">Date</th>
                <th style="width: 850px;">Activité</th>
            </tr>
        </thead>
        <tbody>
            <?  
                $max = 0;
                $i = 0; 
            
                foreach($historique['mois'] as $h) : 

                    $nombre = $h['mois_total'];

                    if ($nombre < 500)
                        continue;

                    $date = substr($h['mois'], 0, 4) . '-' . substr($h['mois'], 4, 2);
        
                    if ($nombre > $max) 
                        $max = $nombre;

                    $i++; 
            ?>
                <tr>
                    <td style="text-align: center"><?= $i; ?></td> 
                    <td><?= $date; ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar <?= date('Y-m') == $date ? 'bg-danger' : ''; ?>" role="progressbar" style="width: <?= ($nombre/$max*100) . '%'; ?>" aria-valuenow="<?= $nombre; ?>" aria-valuemin="0" aria-valuemax="<?= $max; ?>"><?= $nombre; ?></div>
                        </div>
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>

<? endif; ?>

</div> <!-- #historique-mois -->

</div> <!-- #historique-activite -->
