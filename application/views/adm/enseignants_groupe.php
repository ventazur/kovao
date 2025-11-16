<? 
/* ------------------------------------------------------------------------
 *
 *  Admin > Groupe > Enseignants
 *
 * ------------------------------------------------------------------------ */ ?>

<h5>Les enseignants du groupe</h5>

<div class="space"></div>

<? if (empty($enseignants)) : ?>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun enseignant dans ce groupe        

<? else : ?>

    <table id="enseignants" class="table admin-table">

        <tr style="background: #fff">

            <th style="width: 80px; text-align: center">
                ID
                <span class="tri-button" data-clef="clef_tri_enseignant_id"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span>
            </th>
            <th>
                Nom
                <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
            </th>
            <th style="text-align: center">
                Niveau
                <span class="tri-button" data-clef="clef_tri_niveau" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="text-align: center">
                Évaluations
                <span class="tri-button" data-clef="clef_tri_evaluations" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="text-align: center">
                Évaluations<br />à remplir
                <span class="tri-button" data-clef="clef_tri_evaluations_remplir" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <? if ($semestre && is_array($semestre)) : ?>
                <th style="text-align: center">
                    Soumissions<br /><?= $semestre['semestre_code']; ?>
                    <span class="tri-button" data-clef="clef_tri_soumissions2" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                </th>
            <? endif; ?>
            <th style="text-align: center">
                Soumissions<br />totales
                <span class="tri-button" data-clef="clef_tri_soumissions" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 170px;">
                Dernière activité
                <span class="tri-button" data-clef="clef_tri_activite" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="text-align: center">
                Activité 
                <span class="tri-button" data-clef="clef_tri_compteur" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 20px">
                <svg viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                  <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
            </th>

        </tr>

        <? 
            $e_total = 0;
            $es_total = 0;
            $s_total = 0;
            $s_semestre = 0;

            foreach($enseignants as $e) : 
    
                if (array_key_exists($e['enseignant_id'], $soumissions_total))
                {
                    $s_total += $soumissions_total[$e['enseignant_id']];
                }    

                if (array_key_exists($e['enseignant_id'], $soumissions_semestre))
                {
                    $s_semestre += $soumissions_semestre[$e['enseignant_id']];
                }    
        ?>

            <tr data-clef_tri_enseignant_id="<?= $e['enseignant_id']; ?>"
                data-clef_tri_nom="<?= strtolower(strip_accents($e['nom'] . $e['prenom'])); ?>"
                data-clef_tri_niveau="<?= $e['niveau']; ?>"
                data-clef_tri_evaluations="<?= ( ! array_key_exists($e['enseignant_id'], $evaluations_enseignants) ? 0 : count($evaluations_enseignants[$e['enseignant_id']])); ?>"
                data-clef_tri_evaluations_remplir="<?= ( ! array_key_exists($e['enseignant_id'], $evaluations_selectionnees) ? 0 : count($evaluations_selectionnees[$e['enseignant_id']])); ?>"
                data-clef_tri_activite="<?= $e['derniere_activite_date']; ?>"
                data-clef_tri_compteur="<?= $e['activite_compteur']; ?>"
                data-clef_tri_soumissions2="<?= ( ! array_key_exists($e['enseignant_id'], $soumissions_semestre) ? 0 : $soumissions_semestre[$e['enseignant_id']]); ?>"
                data-clef_tri_soumissions="<?= ! array_key_exists($e['enseignant_id'], $soumissions_total) ? 0 : $soumissions_total[$e['enseignant_id']]; ?>">
                <td style="text-align: center"><?= $e['enseignant_id']; ?></td>
                <td style="padding-top: 15px">
                    <a href="<?= base_url() . $sous_dir . '/groupe/enseignant/' . $e['enseignant_id']; ?>">
                        <?= $e['prenom'] . ' ' . mb_strtoupper($e['nom']); ?>
                        <? if ($e['actif']) : ?>
                            <i class="fa fa-check-circle"></i>
                        <? else : ?>
                            <i class="fa fa-times-circle"></i>
                        <? endif; ?>
                    </a>
                </td>
                <td style="padding-top: 15px; text-align: center"><?= $e['niveau']; ?></td>

                <td style="text-align: center">
                    <? if ( ! array_key_exists($e['enseignant_id'], $evaluations_enseignants)) : ?>
                        0
                    <? else : ?>
                        <?= count($evaluations_enseignants[$e['enseignant_id']]); ?>
                        <? $e_total += count($evaluations_enseignants[$e['enseignant_id']]); ?>
                    <? endif; ?>
                </td>

                <? if ($semestre && is_array($semestre)) : ?>
                    <td style="text-align: center">
                        <? if ( ! array_key_exists($e['enseignant_id'], $evaluations_selectionnees)) : ?>
                            0
                        <? else : ?>
                            <?= count($evaluations_selectionnees[$e['enseignant_id']]); ?>
                            <? $es_total += count($evaluations_selectionnees[$e['enseignant_id']]); ?>
                        <? endif; ?>
                    </td>
                <? endif; ?>

                <td style="text-align: center"><?= ( ! array_key_exists($e['enseignant_id'], $soumissions_semestre) ? 0 : $soumissions_semestre[$e['enseignant_id']]); ?></td>
                <td style="text-align: center"><?= ( ! array_key_exists($e['enseignant_id'], $soumissions_total) ? 0 : $soumissions_total[$e['enseignant_id']]); ?></td>
                <td class="mono"><?= $e['derniere_activite_date']; ?></td>
                <td style="text-align: center"><?= $e['activite_compteur']; ?></td>
                <td>
                    <a target="_blank" href="<?= base_url() . 'admin/usurper/enseignant/' . $e['enseignant_id']; ?>">
                        <svg viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                          <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                    </a>
                </td>
            </tr>

        <? endforeach; ?>

        <tr style="background-color: #eee; font-weight: bold">    
            <td colspan="3"></td>
            <td style="text-align: center"><?= $e_total; ?></td>
            <td style="text-align: center"><?= $es_total; ?></td>
            <? if ($semestre && is_array($semestre)) : ?>
                <td style="text-align: center"><?= $s_semestre; ?></td>
            <? endif; ?>
            <td style="text-align: center"><?= $s_total; ?></td>
            <td colspan="3"></td>
        </tr>
    </table>

    <? if ( ! ($semestre && is_array($semestre))) : ?>
        <div class="hspace"></div>

        <span style="font-size: 0.9em;">
            <i class="fa fa-exclamation-circle"></i> Il n'y a aucun semestre en vigueur.
        </span>
    <? endif; ?>

<? endif; ?>
