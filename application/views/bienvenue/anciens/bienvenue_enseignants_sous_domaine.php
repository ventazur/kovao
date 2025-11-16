<div id="bienvenue-enseignants">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3>Enseignants</h3>

    <div class="hspace"></div>

    <div class="row">

    <div class="col-md-4 mt-2">
        <?
        // ------------------------------------------------------------------------ 
        //
        // RESULTATS
        //
        // ------------------------------------------------------------------------ 
        ?>
        <a class="box-link" href="<?= base_url() . 'resultats'; ?>">
            <div id="box-resultats" class="box-link">

                <div class="row">
                    <div class="col-10 mt-2">
                        <h5>
                            Résultats
                            <? if ( ! empty($enseignant['semestre_id'])) : ?>
                                <? if (@$resultats_cumules_session) : ?> 
                                    <span class="badge badge-success" style="margin-left: 5px"><?= $resultats_cumules_session; ?></span> 
                                <? else : ?>
                                    <span class="badge badge-success" style="margin-left: 5px">0</span> 
                                <? endif; ?>
                            <? endif; ?>
                        </h5>
                    </div>
                    <div class="col-2 mt-2">
                        <div class="float-right"><i class="fa fa-angle-right fa-lg"></i></div>
                    </div>
                </div>

            </div>
        </a>
    </div> <!-- .col-md-4 -->

    <div class="col-md-4 mt-2">
        <?
        // ------------------------------------------------------------------------ 
        //
        // CORRECTIONS
        //
        // ------------------------------------------------------------------------ 
        ?>
        <a class="box-link" href="<?= base_url() . 'corrections'; ?>">
            <div id="box-corrections" class="box-link">

                <div class="row">
                    <div class="col-10 mt-2">
                        <h5>
                            Corrections 
                            <? if ( ! empty($enseignant['semestre_id'])) : ?>
                                <? if (@$corrections_en_attente > 0) : ?> 
                                    <span class="badge badge-danger" style="margin-left: 5px"><?= $corrections_en_attente; ?></span> 
                                <? else : ?>
                                    <span class="badge badge-warning" style="margin-left: 5px">0</span> 
                                <? endif; ?>
                            <? endif; ?>
                        </h5>
                    </div>
                    <div class="col-2 mt-2">
                        <div class="float-right"><i class="fa fa-angle-right fa-lg"></i></div>
                    </div>
                </div>

            </div>
        </a>
    </div> <!-- .col-md-4 -->

    <div class="col-md-4 mt-2">
        <?
        // ------------------------------------------------------------------------ 
        //
        // ÉVALUATIONS
        //
        // ------------------------------------------------------------------------ 
        ?>
        <a class="box-link" href="<?= base_url() . 'evaluations'; ?>">
            <div id="box-evaluations" class="box-link">

                <div class="row">
                    <div class="col-10 mt-2">
                        <h5>Évaluations</h5>
                    </div>
                    <div class="col-2 mt-2">
                        <div class="float-right"><i class="fa fa-angle-right fa-lg"></i></div>
                    </div>
                </div>

            </div>
        </a>
    </div>

    </div> <? // .row ?>

    <? 
    // ---------------------------------------------------------------------
    // 
    // EVALUATIONS EN VIGUEUR
    //
    // --------------------------------------------------------------------- ?>

    <div class="evaluations-en-vigueur">

        <div class="tspace"></div>

        <div class="bienvenue-table-box">

            <table class="bienvenue-table">
        
                <thead>
                    <tr class="bienvenue-table-titre">
                        <th colspan="3">
                            <? if (count($evaluations) > 1) : ?>
                                Évaluations pouvant être remplies par vos étudiants
                            <? else : ?>
                                Évaluation pouvant être remplie par vos étudiants
                            <? endif; ?>
                        </th>
                    </tr>
        
                    <? if ( ! (empty($evaluations) || empty($cours_raw))) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th style="width: 80px; text-align: center;">Cours</th>
                            <th colspan="2">Titre de l'évaluation</th>
                        </tr>

                    <? endif; ?>
                </thead>

                <tbody style="font-size: 0.9em">

                <? if (empty($evaluations) || empty($cours_raw)) : ?>

                    <tr>
                        <td style="padding: 15px; font-family: Lato; font-weight: 300; font-size: 1.1em;">
                            <i class="fa fa-exclamation-circle"></i> Aucune évaluation sélectionnée dans la <a href="<?= base_url() . 'configuration'; ?>">configuration</a>
                        </td>
                    </tr>

                <? else : ?>

                    <? foreach($cours_raw as $cours_id => $c) : ?>

                        <? foreach($evaluations as $evaluation_id => $e) : ?>

                            <? if ($e['cours_id'] != $cours_id) continue; ?>

                            <tr style="background: #f7f7f7;">
                                <td style="width: 80px; color: #999; text-align: center">
                                    <?= $c['cours_code_court']; ?>
                                </td>
                                <td>
                                   <?= $e['evaluation_titre']; ?>
                                </td>
                                <td style="border-left: 0; text-align: right;">
                                    <div class="btn btn-sm btn-danger evaluation-terminee spinnable"  
                                            data-semestre_id="<?= $this->semestre_id; ?>"
                                            data-cours_id="<?= $cours_id; ?>"
                                            data-evaluation_id="<?= $evaluation_id; ?>">
                                            Terminer cette évaluation
                                            <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                    </div>
                                </td>
                            </tr>

                        <? endforeach; ?>

                    <? endforeach; ?>

                <? endif; // empty($evaluations) || empty($cours_raw) ?>
                
                </tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

    </div>

    <? 
    // ---------------------------------------------------------------------
    // 
    // ACTIVITE
    //
    // --------------------------------------------------------------------- ?>

    <div class="activite d-none d-md-block">

        <? 
        // ---------------------------------------------------------------------
        // 
        // DERNIERES SOUMISSIONS
        //
        // --------------------------------------------------------------------- ?>

        <div id="dernieres-soumissions-ferme" class="d-none bienvenue-fermee" style="margin-top: 25px; margin-bottom: 25px">

            <div class="row">
                <div class="col-8" style="padding-top: 3px;">
                    Les dernières soumissions de vos étudiants
                </div>
                <div class="col-4" style="text-align: right">
                    <div id="dernieres-soumissions-ouvrir" class="btn btn-sm btn-light">
                        <i class="fa fa-plus-square"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="dernieres-soumissions-ouvert" class="bienvenue-table-box-noborder" style="margin-top: 15px">

            <table class="bienvenue-table">

                <thead>
                    <tr class="bienvenue-table-titre" style="background: #fff; color: dodgerblue;">
                        <th colspan="6" style="padding-left: 0">
                            Les dernières soumissions de vos étudiants
                        </th>
                        <th style="text-align: right; padding-right: 0">
                            <div id="dernieres-soumissions-fermer" class="btn btn-sm btn-light">
                                <i class="fa fa-minus-square"></i>
                            </div>
                        </th>
                    </tr>
        
                    <? if ( ! empty($dernieres_soumissions)) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th>Date</th>
                            <th>Étudiant(e)</th>
                            <th style="text-align: center">Référence</th>
                            <th style="text-align: center">Cours</th>
                            <th style="text-align: center">Évaluation</th>
                            <th style="text-align: center">Durée</th>
                            <th style="text-align: right">Points (%)</th>
                        </tr>

                    <? endif; ?>

                </thead>

                <tbody style="font-size: 0.9em">

                    <? if (empty($dernieres_soumissions)) : ?>

                        <tr>
                            <td colspan="10" style="padding: 15px; font-family: Lato; font-size: 1.1em; font-weight: 300;">
                                <i class="fa fa-exclamation-circle"></i> 
                                Aucune soumission trouvée pour le semestre en vigueur
                            </td>
                        </tr>

                    <? else :

                        $ajd = date('Ymj');

                    ?>
                        <? foreach($dernieres_soumissions as $s) : 

                                $cours_data = (array) json_decode($s['cours_data']); 
                        ?>
                            <tr class="<?= date('Ymj', $s['soumission_epoch']) == $ajd ? 'ajd' : ''; ?>">
                                <td><?= $s['soumission_date']; ?></td>
                                <td><?= $s['prenom_nom']; ?></td>
                                <td style="text-align: center"><a href="<?= base_url() . 'corrections/voir/' . $s['soumission_reference']; ?>"><?= $s['soumission_reference']; ?></a></td>
                                <td style="text-align: center"><?= @$cours_data['cours_code_court']; ?></td>
                                <td style="text-align: center"><a href="<?= base_url() . 'evaluations/editeur/' . $s['evaluation_id']; ?>"><?= $s['evaluation_id']; ?></a></td>
                                <td style="text-align: center"><?= $s['duree']; ?></td>
                                <td style="text-align: right">
                                    <? if ( ! $s['corrections_terminees']) : ?>

                                        à corriger

                                    <? else : ?>

                                        <? if ($s['points_obtenus'] > 0) : ?>
                                            <?= my_number_format($s['points_obtenus']) . ' / ' . my_number_format($s['points_evaluation']); ?> 
                                            <span style="padding-left: 10px">(<?= number_format($s['points_obtenus'] / $s['points_evaluation'] * 100)?>%)</span>
                                        <? else : ?>
                                        0 / <?= number_format($s['points_evaluation']); ?> <span style="padding-left: 10px">(0%)</span>
                                        <? endif; ?>

                                    <? endif; ?>
                
                                </td>
                            </tr>
                        <? endforeach; ?>

                    <? endif; ?>

                <tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

        <? 
        // ---------------------------------------------------------------------
        // 
        // CORRECTIONS CONSULTEES
        //
        // --------------------------------------------------------------------- ?>

        <div id="corrections-consultees-ferme" class="d-none bienvenue-fermee" style="margin-top: 30px;">

            <div class="row">
                <div class="col-8" style="padding-top: 3px;">
                    Les dernières corrections consultées
                </div>
                <div class="col-4" style="text-align: right">
                    <div id="corrections-consultees-ouvrir" class="btn btn-sm btn-light">
                        <i class="fa fa-plus-square"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="corrections-consultees-ouvert" class="bienvenue-table-box-noborder" style="margin-top: 20px">

            <table class="bienvenue-table">

                <thead>
                    <tr class="bienvenue-table-titre" style="background: #fff; color: dodgerblue;">
                        <th colspan="6" style="padding-left: 0">
                            Les dernières corrections consultées
                        </th>
                        <th style="text-align: right; padding-right: 0">
                            <div id="corrections-consultees-fermer" class="btn btn-sm btn-light">
                                <i class="fa fa-minus-square"></i>
                            </div>
                        </th>
                    </tr>
        
                    <? if ( ! empty($corrections_consultees)) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th>Date consultée</th>
                            <th>Soumis par</th>
                            <th style="text-align: center">Référence</th>
                            <th style="text-align: center">Cours</th>
                            <th>Date soumis</th>
                            <th style="text-align: center">Vues</th>
                            <th style="text-align: right">Points (%)</th>
                        </tr>

                    <? endif; ?>

                </thead>

                <tbody style="font-size: 0.9em">

                    <? if (empty($corrections_consultees)) : ?>

                        <tr>
                            <td colspan="10"style="padding: 15px; font-family: Lato; font-size: 1.1em; font-weight: 300;">
                                <i class="fa fa-exclamation-circle"></i> 
                                Aucune correction consultée
                            </td>
                        </tr>

                    <? else :

                        $ajd = date('Ymj');

                    ?>
                        <? foreach($corrections_consultees as $c) : ?>

                            <tr class="<?= date('Ymj', $s['soumission_epoch']) == $ajd ? 'ajd' : ''; ?>">
                                <td><?= date_humanize($c['epoch'], TRUE); ?></td>
                                <td><?= $c['prenom_nom']; ?></td>
                                <td style="text-align: center"><a href="<?= base_url() . 'corrections/voir/' . $c['soumission_reference']; ?>"><?= $c['soumission_reference']; ?></a></td>
                                <td style="text-align: center"><?= $c['cours_code_court']; ?></td>
                                <td><?= date_humanize($c['soumission_epoch']); ?></td>
                                <td style="text-align: center"><?= $c['vues']; ?></td>
                                <td style="text-align: right">
                                    <?= my_number_format($c['points_obtenus']) . ' / ' . my_number_format($c['points_total']); ?> 
                                    <span class="" style="padding-left: 10px">(<?= number_format($c['points_obtenus'] / $c['points_total'] * 100)?>%)</span>
                                </td>
                            </tr>

                        <? endforeach; ?>

                    <? endif; ?>

                <tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

        <? 
        // ---------------------------------------------------------------------
        // 
        // CORRECTIONS CONSULTEES
        //
        // --------------------------------------------------------------------- ?>

        <div id="corrections-consultees" class="d-none">

        <? if (empty($corrections_consultees)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucune correction consultée

        <? else : ?>

            <table class="table bienvenue-table">
                <thead>
                    <tr>
                        <th>Date de consultation</th>
                        <th style="text-align: center">Référence</th>
                        <th style="text-align: center">Vues</th>
                        <th>Soumis par</th>
                        <th>Date soumis</th>
                        <th style="text-align: center">Cours</th>
                        <th style="text-align: center">Semestre</th>
                        <th style="text-align: right">Points (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($corrections_consultees as $c) : ?>
                        <tr>
                            <td><?= date_humanize($c['epoch'], TRUE); ?></td>
                            <td style="text-align: center"><a href="<?= base_url() . 'corrections/voir/' . $c['soumission_reference']; ?>"><?= $c['soumission_reference']; ?></a></td>
                            <td style="text-align: center"><?= $c['vues']; ?></td>
                            <td><?= $c['prenom_nom']; ?></td>
                            <td><?= date_humanize($c['soumission_epoch']); ?></td>
                            <td style="text-align: center"><?= $c['cours_code_court']; ?></td>
                            <td style="text-align: center"><?= $c['semestre_code']; ?></td>
                            <td style="text-align: right">
                                <?= my_number_format($c['points_obtenus']) . ' / ' . my_number_format($c['points_total']); ?> 
                                <span class="" style="padding-left: 10px">(<?= number_format($c['points_obtenus'] / $c['points_total'] * 100)?>%)</span>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        <? endif; ?>

    </div> <!-- #corrections-consultees -->

</div> <!-- .col-sm-12 -->
</div> <!-- .col-xl-1 -->

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
