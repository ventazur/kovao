<?
/* ----------------------------------------------------------------------------
 *
 * Resultats de la recherche > Mes evaluations
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if ( ! empty($evaluations)) : ?>

<div class="recherche-resultats-section">

    <div class="recherche-resultats-section-titre">

        <i class="fa fa-search" style="margin-right: 5px;"></i>
        Mes évaluations

    </div>

    <div class="recherche-resultats-section-contenu">

        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center">Cours</th>
                    <th>Titre de l'évaluation</th>
                    <th style="width: 80px; text-align: right">Action</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($evaluations as $e) : 

                    // $evaluation = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                ?>
                    <tr>
                        <td style="text-align: center"><?= $cours[$e['cours_id']]['cours_code_court']; ?></td>
                        <td><?= $e['evaluation_titre']; ?></td>
                        <td style="text-align: right">
                            <a href="<?= base_url() . 'evaluations/editeur/' . $e['evaluation_id']; ?>">
                                Éditer<i class="fa fa-angle-right" style="margin-left: 5px"></i>
                            </a>

                        </td>
                    </tr>

                <? endforeach; ?>

            </tbody>
        </table>


    </div>

</div>

<? endif; ?>
