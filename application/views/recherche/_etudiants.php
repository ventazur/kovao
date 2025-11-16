<div>
<? if ( ! empty($soumissions) && is_array($soumissions)) : ?>

    <div id="resultats-table">
        <table class="table">

            <thead>
                <tr>
                    <th>Prénom et nom</th>
                    <th style="text-align: center">Date</th>
                    <th style="text-align: center">Semestre</th>
                    <th style="text-align: center">Cours</th>
                    <th style="text-align: center">Référence</th>
                    <th>Titre</th>
                    <th style="text-align: center">Note</th>
                </tr>
            </thead>

            <tbody>

            <? foreach($soumissions as $s) : 

                $cours = (array) json_decode(gzuncompress($s['cours_data_gz']));
                $evaluation = (array) json_decode(gzuncompress($s['evaluation_data_gz']));

                $cours_code_court = NULL;

                // pour assurer la comatibilite avec les anciennes soumissions
                if ( ! array_key_exists('cours_code_court', $cours))
                {
                    if (preg_match('/\-(.*)\-/', $cours['cours_code'], $matches))
                        $cours_code_court = $matches[1];
                }
                else
                {
                    $cours_code_court = $cours['cours_code_court'];
                }

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
                    <td class="nom-etudiant" style="cursor: pointer">
                        <span class="nom-etudiant-matricule" data-toggle="tooltip" data-placement="auto" title="<?= $s['numero_da'];?>" data-matricule="<?= $s['numero_da']; ?>">
                            <?= $s['prenom_nom']; ?>
                        </span>
                    </td>
                    <td style="text-align: center"><?= strstr($s['soumission_date'], ' ', TRUE); ?></td>
                    <td style="text-align: center"><?= (array_key_exists('semestre_code', $cours) ? trim($cours['semestre_code']) : ''); ?></td>
                    <td style="text-align: center"><?= $cours_code_court; ?></td>
                    <td style="text-align: center"><a href="<?= base_url() . 'consulter/'; ?><?= $s['soumission_reference']; ?>">
						<?= $s['soumission_reference']; ?>
					</a></td>
                    <td><?= @$evaluation['evaluation_titre']; ?></td>
                    <td style="text-align: right">
            
                        <? if ($s['points_evaluation'] > 0) : ?>
                            <?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?> 
                            <span style="padding-left: 10px">(<?= number_format($points_obtenus / $s['points_evaluation'] * 100)?>%)</span>
                        <? else : ?>
                            0/0 <span style="padding-left: 10px">(0%)</span>
                        <? endif; ?>

                    </td>
                </tr>

            <? endforeach; ?>

            </tbody>
        </table>
    </div> <!-- #resultats-table -->

    <div class="hspace"></div>    

    <div>
        <?= count($soumissions); ?> résultat<?= count($soumissions) > 1 ? 's' : ''; ?> trouvé<?= count($soumissions) > 1 ? 's' : ''; ?>
    </div>

<? endif; ?>
<div>
