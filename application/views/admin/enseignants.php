<h5>Les enseignants <?= ! empty($enseignants) ? '(' . count($enseignants) . ')' : ''; ?></h5> 

<div class="space"></div>

<? if (empty($enseignants)) : ?>

    <i class="fa fa-exclamation-circle"></i> Aucune enseignant répertoriée

<? else : ?>

    <div id="enseignants">

        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Prénom, Nom</th>
                    <th style="text-align: center">Groupe</th>
                    <th style="text-align: center">Corrections</th>
                    <th style="text-align: center">Résultats (session)</th>
                    <th style="text-align: center">Résultats (total)</th>
                    <th style="text-align: center">Évaluations</th>
                    <th>Dernière activité</th>
                    <th style="text-align: center">Accès</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($enseignants as $e) : ?>

                    <? if ( ! isset($compteur)) { $compteur = 0; } else { $compteur += $e['activite_compteur']; } ?>

                    <tr style="<?= ! $e['actif'] ? 'background: pink' : ''; ?>">
                        <td>
                            <div data-toggle="tooltip" data-placement="top" title="<?= $e['courriel']; ?>" style="cursor: pointer">

                                <?= $e['prenom'] . ' ' . $e['nom']; ?> 

                                <span style="padding-left: 10px"></span>

                                <? if ($e['courriel_confirmation']) : ?>
                                    <i class="fa fa-envelope" style="color: limegreen"></i>
                                <? else : ?>
                                    <i class="fa fa-envelope" style="color: crimson"></i>
                                <? endif; ?>

                                <span style="padding-left: 5px"></span>

                                <? if ($e['actif']) : ?>
                                    <i class="fa fa-check-circle" style="color: limegreen"></i>
                                <? else : ?>
                                    <i class="fa fa-times-circle" style="color: crimson"></i>
                                <? endif; ?>

                            </div>
                        </td>

                        <td style="text-align: center"><a href="<?= base_url() . 'admin/groupe/' . $e['groupe_id']; ?>"><?= $e['groupe']; ?></a></td>
                        <td style="text-align: center"><?= $e['corrections_en_attente'] < 0 ? 0 : $e['corrections_en_attente']; ?></td>
                        <td style="text-align: center"><?= $e['resultats_cumules_session']; ?></td>
                        <td style="text-align: center"><?= $e['resultats_cumules_total']; ?></td>
                        <td style="text-align: center"><?= $e['evaluations_privees_total']; ?></td>
                        <td><?= $e['derniere_activite_date']; ?></td>
                        <td style="text-align: center"><?= $e['activite_compteur'] ?: ''; ?></td>
                    </tr>
                <? endforeach; ?>

                <tr style="background: #fff">
                    <td style="font-weight: bold">Totaux</td>
                    <td></td>
                    <td style="font-weight: bold; text-align: center"><?= $corrections_total; ?></td>
                    <td style="font-weight: bold; text-align: center"><?= $resultats_session; ?></td>
                    <td style="font-weight: bold; text-align: center"><?= $resultats_total; ?></td>
                    <td style="font-weight: bold; text-align: center"><?= $evaluations_total; ?></td>
                    <td></td>
                    <td style="font-weight: bold; text-align: center"><?= $compteur; ?></td>
                </tr>

            </tbody>

        </table>

    </div> <!-- #enseignants -->

<? endif; ?>
