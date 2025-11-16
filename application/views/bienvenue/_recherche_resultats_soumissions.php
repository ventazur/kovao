<?
/* ----------------------------------------------------------------------------
 *
 * Resultats de la recherche > Soumissions
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if ( ! empty($soumissions)) : ?>

<div class="recherche-resultats-section">

    <div class="recherche-resultats-section-titre">

        <i class="fa fa-search" style="margin-right: 5px;"></i>
        Soumissions

    </div>

    <div class="recherche-resultats-section-contenu">

        <table>
            <thead>
                <tr>
                    <th style="width: 200px">Prénom et Nom</th>
                    <th style="width: 50px; text-align: center">Cours</th>
                    <th>Titre de l'évaluation</th>
                    <th style="width: 100px; text-align: center;">Remise</th>
                    <th style="width: 100px; text-align: center">Référence</th>
                    <th style="width: 150px; text-align: center">Points</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($soumissions as $s) : 

                    $evaluation = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);

                    //
                    // Ajustements
                    //

                    $points_obtenus = $s['points_obtenus'];

                    if ( ! empty($s['ajustements_data']))
                    {
                        $ajustements = unserialize($s['ajustements_data']);

                        if (array_key_exists('total', $ajustements))
                        {
                            $points_obtenus = $ajustements['total'];
                        } 
                    }
                ?>
                    <tr>
                        <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width: 1px;">
                            <? if ( ! empty($s['etudiant_id'])) : ?>
                                <a href="<?= base_url() . 'etudiant/' . $s['etudiant_id']; ?>">
                                    <span data-toggle="popover" data-content="allo">
                                        <?= $s['prenom_nom']; ?>
                                    </span>
                                </a>
                            <? else : ?>
                                <a href="#" class="recherche-matricule" data-numero_da="<?= $s['numero_da']; ?>">
                                    <span data-toggle="popover" data-content="allo">
                                        <?= $s['prenom_nom']; ?>
                                    </span>
                                </a>
                            <? endif; ?>
                        </td>
                        <td style="text-align: center"><?= $cours[$s['cours_id']]['cours_code_court']; ?></td>
                        <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width: 1px;">
                            <?= $evaluation['evaluation_titre']; ?>
                        </td>
                        <td class="mono" style="text-align: center"><?= date_humanize($s['soumission_epoch']); ?></td>
                        <td class="mono" style="text-align: center">
                            <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>">
                                <?= $s['soumission_reference']; ?>
                            </a>
                        </td>
                        <td style="text-align: right">
                            <? if ( ! $s['corrections_terminees']) : ?>

                                <a href="<?= base_url() . 'corrections/corriger/' . $s['soumission_reference']; ?>" style="color: crimson">
                                    à corriger
                                </a>

                            <? else : ?>

                                <? if ($points_obtenus > 0) : ?>
                                    <?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?> 
                                    <span style="padding-left: 10px">(<?= number_format($points_obtenus / $s['points_evaluation'] * 100)?>%)</span>
                                <? else : ?>
                                0 / <?= number_format($s['points_evaluation']); ?> <span style="padding-left: 10px">(0%)</span>
                                <? endif; ?>

                            <? endif; ?>
                        </td>
                    </tr>

                <? endforeach; ?>

            </tbody>
        </table>

    </div>

</div>

<? endif; ?>
