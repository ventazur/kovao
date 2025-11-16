<?
/* ----------------------------------------------------------------------------
 *
 * Resultats des etudiants
 *
 * ---------------------------------------------------------------------------- */ ?>

<link href="<?= base_url() . 'assets/css/etu.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

<style>
    table#rangs-groupe-cours td {
        border: 0;
    }
</style>


<div id="resultats">
<div class="container-fluid">
        
<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <?
    /* --------------------------------------------------------------
     *
     * Classements
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! empty($rangs_semestres_cours)) : ?>

        <div style="font-size: 1.4em; font-weight: 400">
            Classements
        </div>

        <div class="space"></div>

        <table class="table" style="margin: 0; font-size: 0.9em; border-bottom: 1px solid #ddd;">
            <thead>
                <tr>
                    <th style="width: 100px">Semestre</th>
                    <th style="max-width: 500px">Cours</th>
                    <th style="text-align: center">
                        Rang-enseignant
                        <i class="fa fa-info-circle" style="margin-left: 3px; color: dodgerblue" data-toggle="tooltip" title="Votre rang parmi tous les étudiants de votre enseignant."></i>
                    </th>
                    <th style="text-align: center">
                        Rang-cours
                        <i class="fa fa-info-circle" style="margin-left: 3px; color: dodgerblue" data-toggle="tooltip" title="Votre rang parmi tous les étudiants de tous les enseignants."></i>
                    </th>
                </tr>
            </thead>
            <tbody>

            <? foreach($rangs_semestres_cours as $semestre_id => $rangs_cours) : ?>

                <? foreach($rangs_cours as $cours_id => $r) : ?>

                    <tr>
                        <td><?= $semestres[$semestre_id]['semestre_code']; ?></td>
                        <td>
                            <?= $cours[$cours_id]['cours_nom_court']; ?>
                            (<?= $cours[$cours_id]['cours_code']; ?>)
                        </td>

                        <td class="mono" style="text-align: center">
                            <?= $r['rang']  . ' / ' . $r['rang_max']; ?>
                        </td>

                        <td class="mono" style="text-align: center">
                            <?= $r['rang_complet']  . ' / ' . $r['rang_complet_max']; ?>
                        </td>
                    </tr>

                <? endforeach; ?>

            <? endforeach; ?>

            </tbody>
        </table>

        <div class="hspace"></div>

        <div style="font-size: 0.8em;">
            <i class="fa fa-info-circle" style="color: #999; margin-right: 3px"></i>
            Si ces classements ne reflètent pas la réalité, veuillez entrer les pondérations de vos évaluations.
        </div>

        <div class="tspace"></div>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------
     *
     * Resultats
     *
     * -------------------------------------------------------------- */ ?>

    <div style="font-size: 1.4em; font-weight: 400; color: crimson">
        Résultats
    </div>

    <div class="space"></div>

    <? if (empty($soumissions)) : ?>

        <i class="fa fa-exclamation-circle" style="color: crimson; margin-right: 5px"></i>
        Vous n'avez envoyé aucune évaluation dans ce groupe.

        <div class="hspace"></div>

    <? else : ?>

        <table id="resultats-etudiants-table" class="table" style="border-bottom: 1px solid #ddd; font-size: 0.9em;">
            <thead>
                <tr>
                    <th style="width: 100px; text-align: left;" class="d-none d-md-table-cell">Semestre</th>
                    <th style="width: 125px; text-align: left;" class="d-none d-xl-table-cell">Cours</th>
                    <th style="width: 125px; text-align: center;" class="d-table-cell d-xl-none">Cours</th>
                    <th>Évaluation</th>
                    <th style="width: 115px; text-align: center" class="d-none d-lg-table-cell">Remise</th>
                    <th style="width: 125px;" class="d-table-cell">Référence<br /><span style="color: #999">Empreinte</span></th>
                    <th style="width: 100px; text-align: right">Note</th>

                    <? if ($this->config->item('evaluation_montrer_ecart_moyenne') && $perf['_ecart_moy']) : ?>
                    
                        <th style="width: 95px; text-align: center" class="d-none d-xl-table-cell">
                            ΔMoy.<span style="cursor: default;" data-toggle="tooltip" title="L'écart à la moyenne"><i class="fa fa-info-circle" style="margin-left: 3px; color: dodgerblue"></i></span>
                        </th>

                    <? endif; ?>

                    <? if ($this->config->item('evaluation_ponderation')) : ?>

                        <th style="width: 100px; text-align: center" class="d-none d-xl-table-cell">
                            Pond.<span style="cursor: default;" data-toggle="tooltip" title="La pondération est la valeur relative de chaque évaluation sur la note finale du semestre"><i class="fa fa-info-circle" style="margin-left: 3px; color: dodgerblue"></i></span>
                        </th>

                    <? endif; ?>

                    <? if ($this->etudiant['montrer_rang_evaluation'] && $this->config->item('evaluation_montrer_rang') && $perf['_rang']) : ?>

                        <th style="width: 95px; text-align: right;">
                            Rang
                            <span style="cursor: default;" data-toggle="tooltip" title="Votre classement parmi tous les étudiants ayant fait cette évaluation">
                                <i class="fa fa-info-circle" style="margin-left: 3px; color: dodgerblue"></i>
                            </span>
                        </th>

                    <? endif; ?>
                </tr>
            </thead>
            <tbody>
                <? foreach($soumissions as $s) : 

                    $ajustements = array();

                    $points_obtenus = $s['points_obtenus'];

                    if ( ! empty($s['ajustements_data']))
                    {
                        $ajustements = unserialize($s['ajustements_data']);

                        if (array_key_exists('total', $ajustements))
                        {
                            $points_obtenus = $ajustements['total'];
                        }
                    }

                    //
                    // Verifier s'il est possible de consulter l'evaluation corrigeee
                    //

                    $permettre_visualisation = ($s['permettre_visualisation'] && ($s['permettre_visualisation_expiration'] == 0 || $s['permettre_visualisation_expiration'] > $this->now_epoch)) ? TRUE : FALSE;

                    //
                    // Extraire le cours id
                    //

                    $cours_id = $s['cours_id'];

                    //
                    // Label
                    //

                    $label = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id'];
                ?>

                    <tr>
                        <td class="d-none d-md-table-cell">
                            <span class="d-none d-sm-inline-block">
                                <?= $s['cours_data']['semestre_code']; ?>
                            </span>
                        </td>
                        <td class="d-none d-xl-table-cell" style="text-align: left">
                            <span style="cursor: default;" data-toggle="tooltip" title="<?= $cours[$cours_id]['cours_nom_court']; ?>">
                                <?= $s['cours_data']['cours_code']; ?>
                            </span>
                        </td>
                        <td class="d-table-cell d-xl-none" style="text-align: center">
                            <span style="cursor: default;" data-toggle="tooltip" title="<?= $s['cours_data']['cours_nom_court']; ?>">
                                <?= $s['cours_data']['cours_code_court']; ?>
                            </span>
                        </td>
                        <td>
                            <? // if ($permettre_visualisation && $semestres[$s['semestre_id']]['semestre_fin_epoch'] > $this->now_epoch) : ?>
                            <? if ($permettre_visualisation) : ?>

                                <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>" target="_blank">
                                    <?= $s['evaluation_data']['evaluation_titre']; ?>
                                </a>

                            <? else : ?>

                                <?= $s['evaluation_data']['evaluation_titre']; ?>

                            <? endif; ?>
                        </td>

                        <td class="mono d-none d-lg-table-cell" style="text-align: center">

                            <? if ($semestres[$s['semestre_id']]['semestre_fin_epoch'] > $this->now_epoch) : ?>
                    
                                <?= date_humanize($s['soumission_epoch']); ?>
                                <br />
                                <?= date('H:i:s', $s['soumission_epoch']); ?>

                            <? else : ?>

                                <?= date_humanize($s['soumission_epoch']); ?>

                            <? endif; ?>

                        </td>

                        <td class="mono d-table-cell">

                            <? // if ($permettre_visualisation && $semestres[$s['semestre_id']]['semestre_fin_epoch'] > $this->now_epoch) : ?>
                            <? if ($permettre_visualisation) : ?>

                                <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>" target="_blank">
                                    <?= $s['soumission_reference']; ?>
                                </a>

                            <? else : ?>

                                <?= $s['soumission_reference']; ?>

                            <? endif; ?>

                            <div style="color: #999">
                                <?= $s['empreinte']; ?>
                            </div>

                        </td>
                        <td class="mono" style="text-align: right;">

                            <? if ($s['permettre_visualisation']) : ?>

                                <? if ($s['points_evaluation'] > 0) : ?>
                                    
                                    <?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?><br />
                                    
                                    <span style="padding-left: 10px" 
                                        data-toggle="tooltip"
                                        data-title="<?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?>">
                                        (<?= number_format($points_obtenus / $s['points_evaluation'] * 100, 1, ',', ''); ?> %)
                                    </span>

                                <? else : ?>

                                    <div>
                                        <svg viewBox="0 0 16 16" class="bi-sm bi-x-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                            data-toggle="tooltip" data-title="note indisponible">
                                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </div>

                                <? endif; ?>

                            <? else : ?>

                                <div>
                                    <svg viewBox="0 0 16 16" class="bi-sm bi-x-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                        data-toggle="tooltip" data-title="note indisponible">
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>

                            <? endif; ?>

                        </td>

                        <? $label = 's' . $s['semestre_id'] . 'e' . $s['evaluation_id']; ?>

                        <? 
                        /* ------------------------------------------------
                         *
                         * L'ecart a la moyenne
                         *
                         * ------------------------------------------------ */ ?>

                        <? if ($this->config->item('evaluation_montrer_ecart_moyenne') && $perf['_ecart_moy']) : ?>

                            <td class="d-none d-xl-table-cell" style="text-align: center">

                                <? if ($s['points_evaluation'] > 0 && array_key_exists($label, $perf) && array_key_exists('ecart_moy', $perf[$label])) : ?>

                                    <? $ecart_moy = str_replace('.', ',', number_format($perf[$label]['ecart_moy'], 0)); ?>
                                        
                                    <? if ($ecart_moy > 0) : ?>

                                        <? if ($ecart_moy < 15) : ?>

                                            <svg viewBox="0 0 16 16" class="bi-sm bi-chevron-up" fill="limegreen" xmlns="http://www.w3.org/2000/svg"
                                                data-toggle="tooltip" data-html="true" title="au-dessus<br />de la moyenne">
                                                <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
                                            </svg>

                                        <? else : ?>

                                            <svg viewBox="0 0 16 16" class="bi-sm bi-chevron-double-up" fill="limegreen" xmlns="http://www.w3.org/2000/svg"
                                                data-toggle="tooltip" data-html="true" title="très au-dessus<br />de la moyenne">
                                                <path fill-rule="evenodd" d="M7.646 2.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 3.707 2.354 9.354a.5.5 0 1 1-.708-.708l6-6z"/>
                                                <path fill-rule="evenodd" d="M7.646 6.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 7.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
                                            </svg>

                                        <? endif; ?>
                                        <? /*
                                            <span style="color: #00C853;">
                                                +<?= $ecart_moy; ?>%
                                            </span>
                                        */ ?>

                                    <? elseif ($ecart_moy < 0) : ?>

                                        <? if (abs($ecart_moy) < 15) : ?>

                                            <svg viewBox="0 0 16 16" class="bi-sm bi-chevron-down" fill="crimson" xmlns="http://www.w3.org/2000/svg"
                                                data-toggle="tooltip" data-html="true" title="en-dessous<br />de la moyenne">
                                                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                            </svg>

                                        <? else : ?>

                                            <svg viewBox="0 0 16 16" class="bi-sm bi-chevron-double-down" fill="crimson" xmlns="http://www.w3.org/2000/svg"
                                                data-toggle="tooltip" data-html="true" title="très en-dessous<br />de la moyenne">
                                                <path fill-rule="evenodd" d="M1.646 6.646a.5.5 0 0 1 .708 0L8 12.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                                <path fill-rule="evenodd" d="M1.646 2.646a.5.5 0 0 1 .708 0L8 8.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                            </svg>


                                        <? endif; ?>

                                        <? /*
                                        <span style="color: crimson;">
                                            <?= $ecart_moy; ?>%
                                        </span>
                                        */ ?>

                                    <? else : ?>

                                        <span style="color: #666;">
                                            =
                                        </span>

                                    <? endif; ?>

                                <? endif; ?>

                            </td>
                        
                        <? endif; ?>

                        <? 
                        /* ------------------------------------------------
                         *
                         * La ponderation
                         *
                         * ------------------------------------------------ */ ?>

                        <? if ($this->config->item('evaluation_ponderation')) : ?>

                            <td class="mono d-none d-xl-table-cell" style="text-align: right;">

                                <? if ( ! array_key_exists($label, $ponderations) && ! array_key_exists($label, $mes_ponderations)) : ?>

                                    <? if ($s['points_evaluation'] > 0) : ?>

                                        <div class="btn btn-sm btn-outline-primary"
                                            data-toggle="modal" 
                                            data-target="#modal-ajuster-ponderation"
                                            data-evaluation_id="<?= $s['evaluation_id']; ?>"
                                            data-semestre_id="<?= $s['semestre_id']; ?>"
                                            data-ponderation=""
                                            data-evaluation_titre="<?= htmlentities($s['evaluation_data']['evaluation_titre']); ?>">
                                            <svg viewBox="0 0 18 18" style="margin-left: 1px" class="bi-xs bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                        </div>

                                    <? endif; ?>

                                <? else : ?>

                                    <? /* La ponderation officielle d'un enseignant */ ?>

                                    <? if (array_key_exists($label, $ponderations) && $ponderations[$label]['enseignant']) : ?>

                                        <span data-toggle="tooltip" data-title="pondération officielle">
                                            <?= number_format($ponderations[$label]['ponderation'], 2, ',', '') . ' %'; ?>
                                        </span>

                                    <? /* La ponderation officieuse des etudiants */ ?>

                                    <? elseif (array_key_exists($label, $ponderations) && $ponderations[$label]['etudiant']) : ?>
                                            
                                        <span data-toggle="tooltip" data-title="pondération entrée par les étudiants">
                                            <?= number_format($ponderations[$label]['ponderation'], 2, ',', '') . ' %'; ?>
                                        </span>

                                        <span style="cursor: pointer; color: dodgerblue"
                                            data-toggle="modal" 
                                            data-target="#modal-ajuster-ponderation"
                                            data-evaluation_id="<?= $s['evaluation_id']; ?>"
                                            data-semestre_id="<?= $s['semestre_id']; ?>"
                                            data-ponderation="<?= array_key_exists($label, $mes_ponderations) && $mes_ponderations[$label]['ponderation'] ? str_replace('.', ',', number_format($mes_ponderations[$label]['ponderation'], 2)) : NULL; ?>">
                                            <svg viewBox="0 0 18 18" class="bi-xs bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                        </span>
                            
                                    <? elseif (array_key_exists($label, $mes_ponderations)) : ?>

                                        <span data-toggle="tooltip" data-title="ma pondération">
                                            <?= number_format($mes_ponderations[$label]['ponderation'], 2, ',', '') . ' %'; ?>
                                        </span>

                                        <span style="cursor: pointer; color: dodgerblue"
                                            data-toggle="modal" 
                                            data-target="#modal-ajuster-ponderation"
                                            data-evaluation_id="<?= $s['evaluation_id']; ?>"
                                            data-semestre_id="<?= $s['semestre_id']; ?>"
                                            data-ponderation="<?= $mes_ponderations[$label]['ponderation'] ? str_replace('.', ',', number_format($mes_ponderations[$label]['ponderation'], 2)) : NULL; ?>">
                                            <svg viewBox="0 0 18 18" class="bi-xs bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                        </span>

                                    <? endif; ?>

                                <? endif; ?>

                            </td>

                        <? endif; // config 'evaluation_ponderation' ?>

                        <? 
                        /* ------------------------------------------------
                         *
                         * Le rang de l'etudiant
                         *
                         * ------------------------------------------------ */ ?>

                        <? if ($this->etudiant['montrer_rang_evaluation'] && $this->config->item('evaluation_montrer_rang') && $perf['_rang']) : ?>

                            <td class="mono" style="text-align: right">

                                <? if ($s['points_evaluation'] > 0 && array_key_exists($label, $perf) && array_key_exists('rang', $perf[$label])) : ?>

                                    <?= $perf[$label]['rang'] . ' / ' . $perf[$label]['rang_max']; ?>

                                <? endif; ?>
                            </td>

                        <? endif; ?>

                    </tr>

                <? endforeach; ?>
            </tbody>
        </table>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------------
     *
     * Ajouter une evaluation envoyee precedemment
     *
     * -------------------------------------------------------------------- */ ?>
    <?
    /*
     * Les etudiants doivent etre inscrits pour rediger une evaluation (depuis 2013-01-14).
     *
    <div class="d-none d-md-block">

        <div class="tspace"></div>

        <div style="font-family: Lato; font-weight: 400; font-size: 1em; background: #fff; color: #444; padding: 10px 15px 10px 15px; border: 1px solid #64B5F6;">
            <i class="fa fa-plus-circle" style="color: dodgerblue; margin-right: 5px"></i>
            Ajouter dans vos résultats une évaluation envoyée précédemment
        </div> 

        <div style="background: #f7f7f7; padding: 15px; border: 1px solid #64B5F6; border-top: 0;">
            <div>
                <form id="etudiants-resultats-ajouter-soumission-form" class="form-inline">
                    <input type="text" name="reference" id="etudiants-resultats-ajouter-soumission-reference" class="form-control form-control-sm mr-2" placeholder="Référence" required>
                    <input type="text" name="empreinte" id="etudiants-resultats-ajouter-soumission-empreinte" class="form-control form-control-sm mr-2" placeholder="Empreinte" required>

                    <button id="etudiants-resultats-ajouter-soumission" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus-circle" style="margin-right: 5px"></i>
                        Ajouter
                    </button>
                </form>
                <div id="etudiants-resultats-ajouter-soumission-erreur0" class="d-none" style="margin-top: 7px; font-size: 0.8em">	
                    <i class="fa fa-exclamation-circle"></i> Une erreur s'est produite. Veuillez recommencer.
                </div>
                <div id="etudiants-resultats-ajouter-soumission-erreur1" class="d-none" style="margin-top: 7px; font-size: 0.8em">	
                    <i class="fa fa-exclamation-circle"></i> Cette combinaison référence/empreinte est inexistante.
                </div>
                <div id="etudiants-resultats-ajouter-soumission-erreur2" class="d-none" style="margin-top: 7px; font-size: 0.8em">	
                    <i class="fa fa-exclamation-circle"></i> Cette soumission a déjà été associée au compte d'un étudiant.
                </div>
                <div id="etudiants-resultats-ajouter-soumission-erreur3" class="d-none" style="margin-top: 7px; font-size: 0.8em">	
                    <i class="fa fa-exclamation-circle"></i> La référence ou l'empreinte est manquante.
                </div>
                <div id="etudiants-resultats-ajouter-soumission-erreur4" class="d-none" style="margin-top: 7px; font-size: 0.8em">	
                    <i class="fa fa-exclamation-circle"></i> Cette évaluation n'appartient pas à ce groupe. Veuillez l'ajouter dans le groupe approprié.
                </div>
            </div>
        </div>

    </div>
    */ ?>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #resultats -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: AJUSTER LA PONDERATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-ajuster-ponderation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit" style="margin-right: 5px"></i> Ajuster la pondération</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding: 25px;">
				<?= form_open(NULL, 
						array('id' => 'modal-ajuster-ponderation-form'), 
                        array('evaluation_id' => NULL, 'semestre_id' => NULL)
					); ?>

                    <span id="modal-ajuster-ponderation-evaluation-titre" style="background: #E3F2FD; border-radius: 3px; padding: 5px; font-size: 0.9em">
                            Minitest du chapitre X
                    </span>

                    <div class="space"></div>

                    <div style="font-size: 0.85em" class="mb-3 mb-2">
                        La pondération permet d'évaluer votre rang dans votre groupe, et parmi les autres groupes.

                        <div class="hspace"></div>

                        Si votre enseignant a entré la pondération, celle-ci devient la pondération qui sera utilisée.<br />
                        Sinon, les étudiants peuvent entrer une pondération et si plusieurs pondérations concordent pour une même évaluation, alors cette pondération officieuse sera utilisée.
                        
                        <div class="hspace"></div>

                        La pondération de chaque évaluation est inscrite soit dans votre plan de cours, soit sur Colnet/Omnivox, ou vous pouvez la demander à votre enseignant(e).

                    </div>


                    Entrez la pondération de cette évaluation :

					<div class="form-row mt-3">
						<div class="col-md-3 mb-2">
							<div class="input-group">
								<input id="modal-ajuster-ponderation-ponderation" name="ponderation" type="text" class="form-control" style="text-align: right" required>
                                <div class="input-group-append mono">
									<span id="modal-corrections-changer-points-total" class="input-group-text" style="font-weight: 700">/ 100 %</span>
								</div>
							</div>
						</div>  
					</div>
				</form>
      		</div>

            <div class="modal-footer">

                <div id="modal-ajuster-ponderation-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save" style="margin-right: 5px;"></i> 
                    Sauvegarder la pondération
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>

                <div id="modal-effacer-ponderation-sauvegarde" class="btn btn-outline-danger spinnable">
                    <i class="fa fa-trash" style="margin-right: 5px;"></i> 
                    Effacer
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div> 

                <div class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-times" style="margin-right: 5px;"></i> 
                    Annuler
                </div>

      		</div>

    	</div>
  	</div>
</div>

