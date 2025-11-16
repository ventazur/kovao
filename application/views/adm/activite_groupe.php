
<h5>L'activité sommaire du groupe</h5>

<div class="space"></div>

<table class="table" style="border: 1px solid #ddd">
    <thead>
        <tr>
            <th style="text-align: center">5 min</th>
            <th style="text-align: center">15 min</th>
            <th style="text-align: center">1 h</th>
            <th style="text-align: center">6 h</th>
            <th style="text-align: center">12 h</th>
            <th style="text-align: center">24 h</th>
            <th style="text-align: center">3 j</th>
            <th style="text-align: center">7 j</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center"><?= $activite_temps['300']; ?></td>
            <td style="text-align: center"><?= $activite_temps['900']; ?></td>
            <td style="text-align: center"><?= $activite_temps['3600']; ?></td>
            <td style="text-align: center"><?= $activite_temps['21600']; ?></td>
            <td style="text-align: center"><?= $activite_temps['43200']; ?></td>
            <td style="text-align: center"><?= $activite_temps['86400']; ?></td>
            <td style="text-align: center"><?= $activite_temps['259200']; ?></td>
            <td style="text-align: center"><?= $activite_temps['604800']; ?></td>
        </tr>
    </tbody>
</table>

<div class="tspace"></div>

<h5>L'activité du groupe</h5> 

<div class="space"></div>

<div class="btn-group" role="group">
    <div id="activite-non-connectes-btn" style="width: 125px" class="btn btn-sm btn-outline-primary active" >Non-connectés</div>
    <div id="activite-etudiants-btn" style="width: 125px" class="btn btn-sm btn-outline-primary" >Étudiants</div>
    <div id="activite-enseignants-btn" style="width: 125px" class="btn btn-sm btn-outline-primary">Enseignants</div>
</div>

<div class="space"></div>

<?
/* --------------------------------------------------------------------
 *
 * Activite
 *
 * -------------------------------------------------------------------- */ ?>

<? $champs = array('non_connectes', 'etudiants', 'enseignants'); ?>

<? foreach($champs as $c) : ?>

    <div id="activite-<?= str_replace('_', '-', $c); ?>" class="<?= $c != 'non_connectes' ? 'd-none' : ''; ?>">

        <? if (empty($activite[$c])) : ?>

            <div class="hspace"></div>

            <i class="fa fa-exclamation-circle"></i> Aucune activité d'utilisateur non-connecté lors des 14 derniers jours

        <? else : ?>

            <table class="table admin-table">
                <thead>
                    <tr>
                        <th style="width: 175px">Date</th>
                        <th style="width: 130px">IP</th>
                        <th>Identité</th>
                        <th style="width: 120px; text-align: center">Unique ID</th>
                        <th style="width: 70px;">Réf.</th>
                        <th style="width: 250px">URI</th>
                    </tr>
                </thead>

                <tbody>

                <?  $i = 0; 

                        foreach($activite[$c] as $a) : 

                        if ($i > 500) break;

                        $i++;
                ?>

                    <tr>
                        <td class="mono"><?= date('Y-m-d H:i.s', $a['epoch']); ?></td>
                        <td class="mono"><?= $a['adresse_ip']; ?></td>
                        <td>
                            <? if ($c == 'etudiants') : ?>

                                <a target="_blank" href="<?= base_url() . 'admin/etudiant/' . $a['etudiant_id']; ?>">
                                    <?= $a['prenom'] . ' ' . $a['nom']; ?> 
                                </a>
                                (<?= $a['etudiant_id']; ?>)

                                <a target="_blank" href="<?= base_url() . 'admin/usurper/etudiant/' . $a['etudiant_id']; ?>">
                                    <svg style="margin-left: 5px" viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                                      <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    </svg>
                                </a>

                            <? elseif ($c == 'enseignants') : ?>

                                <?= $a['prenom'] . ' ' . $a['nom']; ?> (<?= $a['enseignant_id']; ?>)

                                <a target="_blank" href="<?= base_url() . 'admin/usurper/enseignant/' . $a['enseignant_id']; ?>">
                                    <svg style="margin-left: 5px" viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                                      <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    </svg>
                                </a>

                            <? elseif ($c == 'non_connectes' && ! empty($a['identite'])) : ?>

                                <?= explode(';', $a['identite'])[0]; ?>

                            <? endif; ?> 
                        </td>
                        <td class="mono" style="text-align: center">
                           <span data-toggle="tooltip" title="<?= $a['unique_id']; ?>">
                            <? if ( ! empty($a['unique_id'])) : ?>
                                <?= substr($a['unique_id'], 0, 10); ?>
                            <? endif; ?>
                            </span>
                        </td>
                        <td>
                            <? if ( ! empty($a['referencement'])) : ?>
                                <div class="badge" style="background: #ddd; color: #444" 
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="<?= htmlentities($a['referencement']); ?>" 
                                    style="cursor: pointer">
                                    Réf
                                </div>
                            <? endif; ?>
                        </td>
                        <td>
                            <? if ( ! empty($a['uri'])) : ?>
                                <? if (strlen($a['uri']) > 25) : ?>
                                    <a data-toggle="tooltip" title="<?= $a['uri']; ?>" href="<?= base_url() . $a['uri'];?>">
                                        <?= ellipsize($a['uri'], 25); ?>
                                    </a>
                                <? else : ?>
                                    <a href="<?= base_url() . $a['uri'];?>">
                                        <?= $a['uri']; ?>
                                    </a>
                                <? endif; ?>
                            <? endif; ?>
                        </td>
                    </tr>

                <? endforeach; ?>

                </tbody>

            </table>

    <? endif; ?>

    </div> <!-- #activite-non-connectes -->

<? endforeach; ?>
