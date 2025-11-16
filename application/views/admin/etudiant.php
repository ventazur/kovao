<?
/* ====================================================================
 *
 * ADMIN > ETUDIANT
 *
 * ==================================================================== */ ?>

<style>

    table.table-sm {
        border-bottom: 1px solid #ddd;
    }

</style>

<div id="admin-etudiant">

<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">
    
    <div class="hspace"></div>

    <h5 style="text-transform: uppercase; color: <?= $etudiant['genre'] == 'F' ? '#E91E63;' : '#3F51B5;'; ?>"> 

        <span style="color: #fff; background: #eee; padding: 2px 5px 2px 5px; border-radius: 10px; font-size: 0.85em; margin-right: 10px">ADMIN</span>

        <?= $etudiant['prenom'] . ' ' . $etudiant['nom']; ?>

        <a target="_blank" href="<?= base_url() . 'admin/usurper/etudiant/' . $etudiant['etudiant_id']; ?>">
            <svg style="margin-top: -3px; margin-left: 10px" viewBox="0 0 16 16" class="bi-xs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
              <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
        </a>
        
    </h5>

    <?
    /* --------------------------------------------------------------
     *
     * Curriculum
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Curriculum
    </div>

    <div class="space"></div>

    <? if (empty($s_semestres)) : ?>

       <div style="font-size: 0.9em"> 
            <i class="fa fa-exclamation-circle"></i>
            Aucun cours trouvé
        </div>

        <div class="hspace"></div>

    <? else : ?>

        <table class="table" style="font-size: 0.85em; border-bottom: 1px solid #dfdfdf;">
            <thead>
                <tr>
                    <th style="width: 100px">École</th>
                    <th style="width: 200px">Groupe</th>
                    <th style="width: 100px">Semestre</th>
                    <th style="max-width: 500px">Cours</th>
                    <th>Enseignant</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($s_semestres as $semestre_id => $s) : ?>
                    <? foreach($s_cours[$semestre_id] as $cours_id => $c) : ?>
                        <tr>
                            <td>
                                <span data-toggle="tooltip" data-title="<?= $cours_data[$cours_id]['ecole']['ecole_nom']; ?>" style="cursor: pointer">
                                    <?= $cours_data[$cours_id]['ecole']['ecole_nom_court']; ?>
                                </span>
                            </td>
                            <td><?= $cours_data[$cours_id]['groupe']['groupe_nom_court']; ?></td>
                            <td><?= $c['semestre_code']; ?></td>
                            <td>
                                <?= $c['cours_nom_court']; ?>
                                (<?= $c['cours_code']; ?>)
                            </td>
                            <td><?= $c['enseignant_prenom'] . ' ' . $c['enseignant_nom']; ?></td>
                        </tr>
                    <? endforeach; ?>
                <? endforeach; ?>
            </tbody>
        </table>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------
     *
     * Classements
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! empty($rangs_semestres_cours)) : ?>

        <div class="space"></div>

        <div style="font-size: 1.2em; font-weight: 100">
            Classements
        </div>

        <div class="space"></div>

        <table class="table" style="margin: 0; font-size: 0.85em; border-bottom: 1px solid #ddd;">
            <thead>
                <tr>
                    <th style="width: 100px">École</th>
                    <th style="width: 200px">Groupe</th>
                    <th style="width: 100px">Semestre</th>
                    <th style="max-width: 500px">Cours</th>
                    <th style="text-align: center">
                        Rang-ens.
                        <i class="fa fa-info-circle" style="color: #aaa" data-toggle="tooltip" title="Le rang parmi tous les étudiants de votre enseignant."></i>
                    </th>
                    <th style="text-align: center">
                        Rang-cours
                        <i class="fa fa-info-circle" style="color: #aaa" data-toggle="tooltip" title="Le rang parmi tous les étudiants de tous les enseignants."></i>
                    </th>
                </tr>
            </thead>
            <tbody>

            <? foreach($rangs_semestres_cours as $semestre_id => $rangs_cours) : ?>

                <? foreach($rangs_cours as $cours_id => $r) : ?>

                    <tr>
                        <td>
                                <span data-toggle="tooltip" data-title="<?= $cours_data[$cours_id]['ecole']['ecole_nom']; ?>" style="cursor: pointer">
                                    <?= $cours_data[$cours_id]['ecole']['ecole_nom_court']; ?>
                                </span>
                        </td>
                        <td><?= $cours_data[$cours_id]['groupe']['groupe_nom_court']; ?></td>
                        <td><?= $semestres[$semestre_id]['semestre_code']; ?></td>
                        <td>
                            <?= $s_cours[$semestre_id][$cours_id]['cours_nom_court']; ?>
                            (<?= $s_cours[$semestre_id][$cours_id]['cours_code']; ?>)
                        </td>

                        <td style="text-align: center">
                            <?= $r['rang']  . ' / ' . $r['rang_max']; ?>
                        </td>

                        <td style="text-align: center">
                            <?= $r['rang_complet']  . ' / ' . $r['rang_complet_max']; ?>
                        </td>
                    </tr>

                <? endforeach; ?>

            <? endforeach; ?>

            </tbody>
        </table>

        <div class="space"></div>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------
     *
     * Soumissions
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! empty($s_semestres)) : ?>

        <div class="space"></div>

        <div style="font-size: 1.2em; font-weight: 100">
                Évaluations corrigées (<?= count($soumissions); ?>)
        </div>

        <div class="space"></div>

        <? if (empty($soumissions)) : ?>

           <div style="font-size: 0.9em"> 
                <i class="fa fa-exclamation-circle"></i>
                Aucune évaluation corrigée trouvée
            </div>

            <div class="hspace"></div>

        <? else : ?>

            <table class="table" style="font-size: 0.85em; border-bottom: 1px solid #dfdfdf;">
                <thead>
                    <tr>
                        <th style="width: 100px">Cours</th>
                        <th style="width: 100px">Semestre</th>
                        <th style="width: 120px; text-align: center">Reference</th>
                        <th>Titre de l'évaluation</th>
                        <th style="width: 150px; text-align: center">Date de remise</th>
                        <th style="width: 150px; text-align: right">Résultat</th>
                        <th style="width: 100px; text-align: right">Rang</th>
                    </tr>
                </thead>
                <tbody>
                    <? 
                        foreach($soumissions as $s) :

                            $sondage = $s['points_evaluation'] > 0 ? FALSE : TRUE;

                            $label = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id'];

                            if ( ! $sondage)
                            {
                                $points_obtenus = $s['points_obtenus'];

                                if ( ! empty($s['ajustments_data']) && array_key_exists('points_obtenus', $s['ajustements_data']))
                                {
                                    $points_obtenus = $s['ajustements_data']['points_obtenus'];
                                }

                                $points_evaluation = $s['points_evaluation'];

                                $pct = $points_obtenus / $points_evaluation * 100;
                            }
                    ?>
                    <tr>
                        <td><?= $s['cours_data']['cours_code_court']; ?></td>
                        <td><?= $s['cours_data']['semestre_code']; ?></td>

                        <td class="mono" style="text-align: center">
                            <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>" target="_blank">
                                <?= $s['soumission_reference']; ?>
                            </a>
                        </td>

                        <td><?= $s['evaluation_data']['evaluation_titre']; ?></td>
                        <td class="mono" style="text-align: center"><?= date('Y-m-d', $s['soumission_epoch']); ?></td>
                        <td style="text-align: right">
                            <? if ( ! $sondage) : ?>

                                <? if ( ! $s['corrections_terminees']) : ?>

                                    à corriger

                                <? else : ?>

                                    <?= my_number_format($points_obtenus); ?> / <?= my_number_format($points_evaluation); ?>
                                    <span style="margin-left: 5px">
                                        (<?= number_format($pct, 0, '', ''); ?>%)
                                    </span>

                                <? endif; ?>

                            <? endif; ?>
                        </td>
                        <td class="mono" style="text-align: right">

                            <? if ($s['points_evaluation'] > 0 && array_key_exists($label, $perf) && array_key_exists('rang', $perf[$label])) : ?>

                                <?= $perf[$label]['rang'] . ' / ' . $perf[$label]['rang_max']; ?>

                            <? endif; ?>
                        </td>
                    </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        <? endif; ?>

    <? endif ?>

    <?
    /* --------------------------------------------------------------
     *
     * Informations
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Informations
    </div>

    <div class="space"></div>

    <div style="font-size: 0.9em; font-weight: 300; line-height: 1.75em">
        Étudiant ID : <?= $etudiant['etudiant_id']; ?><br />
        <? /* Évaluations envoyées : <?= $evaluations_envoyees; ?><br /> */ ?>
        <?= $ecole['numero_da_nom']; ?> : <?= $etudiant['numero_da'] ? str_replace(' ', '[espace]', $etudiant['numero_da']) : 'NULL'; ?><br />
        Date d'inscription : <?= date_french_full($etudiant['inscription_epoch']); ?><br />
        Date de la dernière connexion : <?= ($derniere_connexion == FALSE ? 'n/d' : date_french_full($derniere_connexion['epoch'])); ?>
    </div>

    <?
    /* --------------------------------------------------------------
     *
     * Activite complete
     *
     * -------------------------------------------------------------- */ ?>

    <div class="tspace"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Activité complète (<?= count($activite); ?>)
    </div>

    <div class="space"></div>

    <? if (empty($activite)) : ?>

        <i class="fa fa-exclamation-circle"></i> Aucune activité

    <? else : ?>

        <table class="table table-sm" style="font-size: 0.85em; font-weight: 300; margin: 0;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Adresse IP</th>
                    <th>Plateforme</th>
                    <th>Fureteur ID</th>
                    <th>Unique ID</th>
                    <th>URI</th>
                </tr>
            </thead>
            <tbody>
                <? 
                    $uid = array();
                    $i = 0;

                    foreach($activite as $a) : 

                        $i++;
                ?>
                    <tr>
                        <td class="mono"><?= date_humanize($a['epoch'], TRUE); ?></td>
                        <td class="mono"><?= $a['adresse_ip']; ?></td>
                        <td><?= $a['plateforme'] . ', ' . $a['fureteur'] . ($a['mobile'] ? ' (' . $a['mobile'] . ')' : ''); ?></td>
                        <td class="mono"><?= substr($a['fureteur_id'], 0, 16); ?></td>
                        <td class="mono"><?= substr($a['unique_id'], 0, 16); ?></td>
                        <td class="mono">
                            <a href="<?= base_url() . (($a['uri'] != '/') ? $a['uri'] : ''); ?>" target="_blank">
                                <?= $a['uri']; ?>
                            </a>
                        </td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>

    <? endif; ?>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-etudiant -->
