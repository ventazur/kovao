<?
/* ----------------------------------------------------------------------------
 *
 * Etudiant
 *
 * ----------------------------------------------------------------------------- */ ?>

<div id="etudiant">

<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="hspace"></div>

    <h5 style="text-transform: uppercase; color: <?= $etudiant['genre'] == 'F' ? '#E91E63;' : '#3F51B5;'; ?>"> 
        <?= $etudiant['prenom'] . ' ' . $etudiant['nom']; ?>

        <? if ($this->enseignant['privilege'] > 89) : ?>
            <a href="<?= base_url() . 'admin/etudiant/' . $etudiant['etudiant_id']; ?>">
                <svg style="margin-top: -2px; margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ffcdd2" class="bi-xs bi-arrow-up-right-square-fill" viewBox="0 0 16 16">
                  <path d="M14 0a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12zM5.904 10.803L10 6.707v2.768a.5.5 0 0 0 1 0V5.5a.5.5 0 0 0-.5-.5H6.525a.5.5 0 1 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 .707.707z"/>
                </svg>
            </a>
        <? endif; ?>
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
                    <th>Groupe</th>
                    <th style="width: 100px">Semestre</th>
                    <th>Cours</th>
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
     * Soumissions
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
            Évaluations corrigées (<?= count($soumissions); ?>)
    </div>

    <div class="space"></div>

    <? if (empty($soumissions)) : ?>

       <div style="font-size: 0.9em"> 
            <i class="fa fa-exclamation-circle"></i>
            Aucune de vos évaluations corrigées trouvées
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
                </tr>
            </thead>
            <tbody>
                <? 
                    foreach($soumissions as $s) :

                        $sondage = $s['points_evaluation'] > 0 ? FALSE : TRUE;


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
                </tr>
                <? endforeach; ?>
            </tbody>
        </table>

    <? endif; ?>

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
        <? if (array_key_exists('numero_da', $etudiant) && ! empty($etudiant['numero_da'])) : ?>
            Numéro DA : <?= $etudiant['numero_da']; ?><br />
        <? endif; ?>

        Étudiant ID : <?= $etudiant['etudiant_id']; ?></br />
    
        Courriel : <?= preg_replace('/^[^@]+/', '***', $etudiant['courriel']); ?><br />
        
        <? /* Évaluations envoyées : <?= $evaluations_envoyees; ?><br /> */ ?>
        Date d'inscription : <?= date_french_full($etudiant['inscription_epoch']); ?><br />
        Date de la dernière connexion : <?= ($derniere_connexion == FALSE ? 'n/d' : date_french_full($derniere_connexion['epoch'])); ?>
    </div>

    <?
    /* --------------------------------------------------------------
     *
     * Activite
     *
     * -------------------------------------------------------------- */ ?>

    <div class="tspace"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Activité
    </div>

    <div class="space"></div>

    <? if (empty($activite)) : ?>

        <i class="fa fa-exclamation-circle"></i> Aucune activité

    <? else : ?>

        <table class="table table-sm" style="font-size: 0.85em; font-weight: 300; margin: 0; border-bottom: 1px solid #dfdfdf;">
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
                        <td class="mono"><?= $a['uri']; ?></td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>

    <? endif; ?>

</div> <!-- .col -->

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #etudiant -->
