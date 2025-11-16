<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - PREPARATION 1
 *
 * VERSION 2025-01-25
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<link href="<?= base_url() . 'assets/css/lab.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

<?
/* --------------------------------------------------------------------
 *
 * Styles specifiques a ce laboratoire
 *
 * -------------------------------------------------------------------- */ ?>

<style></style>

<?
/* --------------------------------------------------------------------
 *
 * Tableaux specifiques
 *
 * -------------------------------------------------------------------- */ ?>

<div id="lab-tableaux-specifiques" data-lab_prefix="<?= $lab_prefix; ?>">

<?
/* --------------------------------------------------------------------
 *
 * Tableau 1
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 1
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 1;

    if ($this->current_controller == 'evaluation')
    {
        $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    }

    if ($this->current_controller == 'consulter')
    {
        $tableau_data = array(
            'points_obtenus_ajustement' => $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0,
            'ajustement'                => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? TRUE : FALSE,
            'points_obtenus'            => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] : $lab_points_tableaux[$tableau_no]['points_obtenus'],
            'points_totaux'             => $lab_points_tableaux[$tableau_no]['points'],
            'commentaires'              => $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL,
            'reussi'                    => ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $lab_points_tableaux[$tableau_no]['points']) ? TRUE : FALSE,
            'soumission_id'             => $soumission['soumission_id'],
            'soumission'                => $soumission,
            'permettre_modifications'   => ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) ? TRUE : FALSE
        );
    }
?>

<div class="evaluation-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?= lab_tableau_titre(
        array(
            'tableau_no'     => $tableau_no,
            'tableau_points' => $tableau_points ?? array(),
            'tableau_data'   => $tableau_data ?? array()
        ));
    ?>

    <? // lab_f_tableau_complet($tableau_no, $tableau_points); ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

        <table class="table mb-0" style="border-top: 0">
            <tbody>

                <tr>
                    <td style="vertical-align: middle; min-width: 175px">
                        <div>Substance</div> 
                    </td>

                    <td class="text-center">
                        <div>Masse fiole vide</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'masse_fiole_vide_d' ,
                                        'unites'      => TRUE,
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'masse_fiole_vide_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <div>Masse de soluté</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'masse_solute_d' ,
                                        'unites'      => TRUE,
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'masse_solute_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <div>Masse fiole pleine</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'masse_fiole_pleine_d' ,
                                        'unites'      => TRUE,
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'masse_fiole_pleine_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <div>Température de la solution</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'temp_solution_d',
                                        'unites'      => TRUE,
                                        'unites_v'    => '&deg;C',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'temp_solution_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <div>Température de l'eau distillée</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'temp_eau_d',
                                        'unites'      => TRUE,
                                        'unites_v'    => '&deg;C',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'temp_eau_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        NaOH
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_fiole_vide',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'naoh_fiole_vide',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array('lab' => array()),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_masse',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'naoh_masse',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_fiole_pleine',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'naoh_fiole_pleine',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_temp',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'naoh_temp',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center" rowspan="2">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'temp_eau',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'temp_eau',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        NH<sub>4</sub>Cl    
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_fiole_vide',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_fiole_vide',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array('lab' => array()),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_masse',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_masse',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_fiole_pleine',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_fiole_pleine',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_temp',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_temp',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                </tr>

            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

<?
/* --------------------------------------------------------------------
 *
 * Tableau 2
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 1
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 2;

    if ($this->current_controller == 'evaluation')
    {
        $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    }

    if ($this->current_controller == 'consulter')
    {
        $tableau_data = array(
            'points_obtenus_ajustement' => $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0,
            'ajustement'                => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? TRUE : FALSE,
            'points_obtenus'            => isset($lab_points_tableaux[$tableau_no]['points_obtenus_ajustement']) ? $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] : $lab_points_tableaux[$tableau_no]['points_obtenus'],
            'points_totaux'             => $lab_points_tableaux[$tableau_no]['points'],
            'commentaires'              => $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL,
            'reussi'                    => ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $lab_points_tableaux[$tableau_no]['points']) ? TRUE : FALSE,
            'soumission_id'             => $soumission['soumission_id'],
            'soumission'                => $soumission,
            'permettre_modifications'   => ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) ? TRUE : FALSE
        );
    }
?>

<div class="evaluation-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?= lab_tableau_titre(
        array(
            'tableau_no'     => $tableau_no,
            'tableau_points' => $tableau_points ?? array(),
            'tableau_data'   => $tableau_data ?? array()
        ));
    ?>

    <? // lab_f_tableau_complet($tableau_no, $tableau_points); ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

        <table class="table mb-0" style="border-top: 0">
            <tbody>

                <tr>
                    <td style="vertical-align: middle; min-width: 175px">
                        <div>Substance</div> 
                    </td>

                    <td class="text-center">
                        <div>Masse de la solution</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'masse_solution_d' ,
                                        'unites'      => TRUE,
                                        'unites_v'    => 'g',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'masse_solution_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <div>Volume de la solution</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'volume_solution_d' ,
                                        'unites'      => TRUE,
                                        'unites_v'    => 'mL',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'volume_solution_d',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        <div>Masse volumique<br />(g/mL)</div>
                    </td>
                </tr>

                <tr>
                    <td>
                        NaOH
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_masse_solution',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'naoh_masse_solution',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array('lab' => array()),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_volume_solution',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'naoh_volume_solution',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'naoh_p',
                                        'champ_d'     => 'naoh_p_d',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <div class="mt-1">
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'naoh_p',
                                            'inline' => TRUE,
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array()
                                        ));
                                    ?>
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'naoh_p_d',
                                            'inline' => TRUE,
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array()
                                        ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        NH<sub>4</sub>Cl
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_masse_solution',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_masse_solution',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array('lab' => array()),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_volume_solution',
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_volume_solution',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_p',
                                        'champ_d'     => 'nh4cl_p_d',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                ?>
                                <div class="mt-1">
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'nh4cl_p',
                                            'inline' => TRUE,
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array()
                                        ));
                                    ?>
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'nh4cl_p_d',
                                            'inline' => TRUE,
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array()
                                        ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" class="pt-3">
                        <div class="row">
                            <div class="col-auto pt-2">
                                Masse volumique du NH<sub>4</sub>CI avec le densimètre
                            </div>
                            <div class="col-auto pt-1">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'nh4cl_p_densimetre',
                                        'unites'      => TRUE,
                                        'unites_v'    => '&pm; 0,001 g/mL',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'nh4cl_p_densimetre',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>


            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->


</div> <!-- #lab -->
