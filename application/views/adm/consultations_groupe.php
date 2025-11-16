<?
/* ----------------------------------------------------------------------------
 *
 * Administration > Groupe > Consultations
 *
 * ---------------------------------------------------------------------------- */ ?>

<h5>Les corrections consultées <?= ! empty($consultations) ? '(' . count($consultations) . ')' : ''; ?></h5> 

<div class="space"></div>

<? if (empty($consultations)) : ?>

    <i class="fa fa-exclamation-circle"></i> Aucune correction consultée

<? else : ?>

    <div id="consultations">

        <table class="table admin-table">
            <thead>
                <tr>
                    <th style="width: 190px">Date consultée</th>
                    <th style="width: 100px; text-align: center">Référence</th>
                    <th style="text-align: center">Vues</th>
                    <th>Soumis par</th>
                    <th style="width: 130px; text-align: center">Date soumise</th>
                    <th>Enseignant</th>
                    <th style="text-align: center">Cours</th>
                    <th style="text-align: center">Semestre</th>
                    <th>Consulté par</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($consultations as $c) : ?>
                    <tr>
                        <td class="mono"><?= date_humanize($c['epoch'], TRUE); ?></td>
                        <td class="mono" style="text-align: center"><a href="<?= base_url() . 'consulter/' . $c['soumission_reference']; ?>"><?= $c['soumission_reference']; ?></a></td>
                        <td style="text-align: center"><?= $c['vues']; ?></td>
                        <td><?= $c['prenom_nom']; ?></td>
                        <td class="mono" style="text-align: center"><?= date_humanize($c['soumission_epoch']); ?></td>
                        <td><?= $c['enseignant']; ?></td>
                        <td style="text-align: center"><?= $c['cours_code_court']; ?></td>
                        <td style="text-align: center"><?= $c['semestre_code']; ?></td>
                        <td><?= @$c['identite_prenom_nom']; ?></td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>

    </div> <!-- #consultations -->

<? endif; ?>
