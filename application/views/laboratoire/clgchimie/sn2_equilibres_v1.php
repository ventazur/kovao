<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - EQUILIBRES CHIMIQUES
 *
 * VERSION 2025-01-21
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

    if (in_array($this->current_controller, array('consulter', 'corrections')))
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

        <table class="table table-bordered mb-0" style="border-top: 0; border-bottom: 0; border-left: 0; border-right: 0">
            <tbody>

                <tr>
                    <td style="vertical-align: middle; min-width: 100px; max-width: 110px">
                        <div>Numéro</div> 
                    </td>

                    <td class="text-center" style="min-width: 165px">
                        <div>Volume de solution mère de KSCN</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => 'kscn_mere_vol_d',
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
                                        'champ' => 'kscn_mere_vol_d',
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

                    <td class="text-center" style="min-width: 195px">
                        Concentration de la solution mère de KSCN
                        <br />
                        (&times;10<sup>-3</sup> mol/L)
                    </td>

                    <td class="text-center"i style="min-width: 195px">
                        Volume des solutions étalons de FeSCN<sup>2+</sup>
                        <br />
                        (mL)
                    </td>

                    <td class="text-center" style="max-width: 150px">
                        Concentration des solutions étalons de FeSCN<sup>2+</sup>
                        <br />
                        (&times;10<sup>-4</sup> mol/L)
                    </td>

                    <td class="text-center" style="min-width: 180px">
                        Absorbance
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'absorb_onde',
                                        'unites'      => TRUE,
                                        'align'       => 'left',
                                        'prepend'     => '&lambda; =',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                
                                <?= lab_tags(
                                    array(
                                        'champ' => 'absorb_onde',
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
                        Étalon 1
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kscn_mere_vol_1',
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
                                        'champ' => 'kscn_mere_vol_1',
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
                    <td class="text-center" rowspan="5" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kscn_mere_conc',
                                        'champ_d'     => 'kscn_mere_conc_d',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'kscn_mere_conc',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'kscn_mere_conc_d',
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

                    <td class="text-center" rowspan="5" style="border-bottom: 0">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_etalons_vol',
                                'champ_d'     => 'fescn_etalons_vol_d',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'fescn_etalons_vol',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'fescn_etalons_vol_d',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>
                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_etalon_1_conc',
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
                                'champ' => 'fescn_etalon_1_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'absorb_etalon_1',
                                'champ_d'     => 'absorb_etalon_1_d',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_1',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_1_d',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                </tr>

                <tr>
                    <td>
                        Étalon 2
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kscn_mere_vol_2',
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
                                        'champ' => 'kscn_mere_vol_2',
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
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_etalon_2_conc',
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
                                'champ' => 'fescn_etalon_2_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'absorb_etalon_2',
                                'champ_d'     => 'absorb_etalon_2_d',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_2',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_2_d',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                </tr>

                <tr>
                    <td>
                        Étalon 3
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kscn_mere_vol_3',
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
                                        'champ' => 'kscn_mere_vol_3',
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
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_etalon_3_conc',
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
                                'champ' => 'fescn_etalon_3_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'absorb_etalon_3',
                                'champ_d'     => 'absorb_etalon_3_d',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_3',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_3_d',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                </tr>

                <tr>
                    <td>
                        Étalon 4
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kscn_mere_vol_4',
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
                                        'champ' => 'kscn_mere_vol_4',
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
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_etalon_4_conc',
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
                                'champ' => 'fescn_etalon_4_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'absorb_etalon_4',
                                'champ_d'     => 'absorb_etalon_4_d',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_4',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_4_d',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                </tr>

                <tr>
                    <td>
                        Étalon 5
                    </td>
                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'kscn_mere_vol_5',
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
                                'champ' => 'kscn_mere_vol_5',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array('lab' => array()),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_etalon_5_conc',
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
                                'champ' => 'fescn_etalon_5_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'absorb_etalon_5',
                                'champ_d'     => 'absorb_etalon_5_d',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_5',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorb_etalon_5_d',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                </tr>

                <tr>
                    <td colspan="6" class="pt-3">
                        <div class="row">
                            <div class="col-auto pt-2">
                                Volume de FeCl<sub>3</sub> dans chaque solution étalon
                            </div>
                            <div class="col-auto pt-1">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fecl3_vol',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                        'unites'      => TRUE
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_vol',
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
 * Determiner le pointage du tableau 2
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 2;

    if ($this->current_controller == 'evaluation')
    {
        $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    }

    if (in_array($this->current_controller, array('consulter', 'corrections')))
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

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 2
 *
 * ---------------------------------------------------------------- */ ?>

<?
$champ1  = 'droite_m';
$champ2  = 'droite_b';
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

        <div class="form-group row mt-1 mb-0" style="padding: 10px;">
            <label for="<?= $lab_prefix . '-' . $champ1; ?>" class="col-sm-6 col-form-label">
				Équation de votre droite
            </label>
            <div class="col-sm-6">


                <? if ($current_controller == 'evaluation') : ?>

                    <div class="input-group">
                        <?
                        /* ------------------------------------------------
                         *
                         * Equation de la droite
                         *
                         * ------------------------------------------------ */ ?>

                        <div class="input-group-prepend">
                            <span class="input-group-text">y = </span>
                        </div> 

                        <input type="text" class="form-control text-right" name="<?= $lab_prefix . '-' . $champ1; ?>" id="<?= $lab_prefix . '-' . $champ1; ?>" 
                            value="<?= $traces['lab'][$champ1] ?? NULL; ?>" placeholder="pente m">

                        <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">&#119909;</span>
                        <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">&plus;</span>

                        <input  type="text" class="form-control text-left" name="<?= $lab_prefix . '-' . $champ2; ?>" id="<?= $lab_prefix . '-' . $champ2; ?>" 
                            value="<?= $traces['lab'][$champ2] ?? NULL; ?>" placeholder="ordonnée b">
                    </div> <!-- .input-group -->

                <? endif; ?>

                <? if (in_array($this->current_controller, array('consulter', 'corrections'))) : ?>

                    <div class="text-center">

                    y = <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'droite_m', NULL); ?><i>x</i> + <?= lab_c_reponse($lab_valeurs, $lab_points_champs, 'droite_b', NULL); ?>

                    </div>

                <? endif; ?>

                <div class="text-center mt-1">
                    <?= lab_tags(
                        array(
                            'champ' => 'droite_m',
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
                            'champ' => 'droite_b',
                            'inline' => TRUE,
                            // evaluation
                            'montre_tags' => $montre_tags ?? FALSE,
                            'lab_points'  => $lab_points ?? array(),
                            // consulter
                            'lab_points_champs' => $lab_points_champs ?? array()
                        ));
                    ?>
                </div>
            </div> <!-- .col -->

        </div> <!-- .form-group -->

    </div>

</div> <!-- .evaluation-tableau --> 

<?
/* --------------------------------------------------------------------
 *
 * Tableau 3
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 3
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 3;

    if ($this->current_controller == 'evaluation')
    {
        $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    }

    if (in_array($this->current_controller, array('consulter', 'corrections')))
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

        <table class="table table-bordered mb-0" style="border-top: 0; border-bottom: 0; border-left: 0; border-right: 0">
            <tbody>

                <tr>
                    <td class="text-center" style="vertical-align: middle;">
                        <div>Volume de la solution mère de FeCl<sub>3</sub></div> 
                        <div>(mL)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Concentration de la solution mère de FeCl<sub>3</sub></div> 
                        <div>(mol/L)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Volume de la solution diluée de FeCl<sub>3</sub></div> 
                        <div>(mL)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Concentration de la solution diluée de FeCl<sub>3</sub></div> 
                        <div>(&times;10<sup>-3</sup> mol/L)</div>
                    </td>
                </tr>

                <tr>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fecl3_mere_vol',
                                        'champ_d'     => 'fecl3_mere_vol_d',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_mere_vol',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_mere_vol_d',
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
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fecl3_mere_conc',
                                        'champ_d'     => 'fecl3_mere_conc_d',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_mere_conc',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_mere_conc_d',
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

                    <td class="text-center" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fecl3_diluee_vol',
                                        'champ_d'     => 'fecl3_diluee_vol_d',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_diluee_vol',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_diluee_vol_d',
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

                    <td class="text-center" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fecl3_diluee_conc',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'fecl3_diluee_conc',
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
                    <td colspan="4" class="pt-3">
                        <div class="row">
                            <div class="col-auto pt-2">
                                Volume de FeCl<sub>3</sub> diluée dans l'éprouvette
                            </div>
                            <div class="col-auto pt-1">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fecl3_eprouvette_vol',
                                        'champ_d'     => 'fecl3_eprouvette_vol_d',
                                        'align'       => 'right',
                                        'unites'      => TRUE,
                                        'unites_v'    => 'mL',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                ?>
                                <div class="mt-1 text-center">
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'fecl3_eprouvette_vol',
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array(),
                                            'inline' => TRUE
                                        ));
                                    ?>
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'fecl3_eprouvette_vol_d',
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array(),
                                            'inline' => TRUE
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
                                Volume de KSCN (SCN<sup>-</sup>) dans l'éprouvette
                            </div>
                            <div class="col-auto pt-1">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kscn_eprouvette_vol',
                                        'champ_d'     => 'kscn_eprouvette_vol_d',
                                        'align'       => 'right',
                                        'unites'      => TRUE,
                                        'unites_v'    => 'mL',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                ?>
                                <div class="mt-1 text-center">
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'kscn_eprouvette_vol',
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array(),
                                            'inline' => TRUE
                                        ));
                                    ?>
                                    <?= lab_tags(
                                        array(
                                            'champ' => 'kscn_eprouvette_vol_d',
                                            // evaluation
                                            'montre_tags' => $montre_tags ?? FALSE,
                                            'lab_points'  => $lab_points ?? array(),
                                            // consulter
                                            'lab_points_champs' => $lab_points_champs ?? array(),
                                            'inline' => TRUE
                                        ));
                                    ?>
                                </div>
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
 * Tableau 4
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 4
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 4;

    if ($this->current_controller == 'evaluation')
    {
        $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    }

    if (in_array($this->current_controller, array('consulter', 'corrections')))
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

        <table class="table table-bordered mb-0" style="border-top: 0; border-bottom: 0; border-left: 0; border-right: 0">
            <tbody>

                <tr>
                    <td class="text-center" style="vertical-align: middle">
                        <div>[Fe<sup>3+</sup>]<sub>o</sub></div> 
                        <div>(&times;10<sup>-3</sup> mol/L)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>[SCN<sup>-</sup>]<sub>o</sub></div> 
                        <div>(&times;10<sup>-4</sup> mol/L)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>Absorbance</div> 
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>[FeSCN<sup>2+</sup>]<sub>eq</sub></div> 
                        <div>(&times;10<sup>-4</sup> mol/L)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>[Fe<sup>3+</sup>]<sub>eq</sub></div> 
                        <div>(&times;10<sup>-3</sup> mol/L)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>[SCN<sup>-</sup>]<sub>eq</sub></div> 
                        <div>(&times;10<sup>-4</sup> mol/L)</div>
                    </td>
                </tr>

                <tr>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'fe_initial_conc',
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
                                        'champ' => 'fe_initial_conc',
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
                    <td class="text-center" rowspan="5" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'scn_initial_conc',
                                        'align'       => 'right',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                 ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'scn_initial_conc',
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

                    <td class="text-center" style="border-bottom: 0">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'absorbance',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'absorbance',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>
                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fescn_eq_conc',
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
                                'champ' => 'fescn_eq_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'fe_eq_conc',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'fe_eq_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                    <td class="text-center">
                        <?= lab_champs(
                            array(
                                'lab_valeurs' => $lab_valeurs,
                                'champ'       => 'scn_eq_conc',
                                'align'       => 'right',
                                // evaluation
                                'lab_prefix'  => $lab_prefix ?? NULL,
                                'traces'      => $traces['lab'] ?? array('lab' => array()),
                                // consultation
                                'lab_points_champs' => $lab_points_champs ?? array(),
                            ));
                         ?>
                        <?= lab_tags(
                            array(
                                'champ' => 'scn_eq_conc',
                                // evaluation
                                'montre_tags' => $montre_tags ?? FALSE,
                                'lab_points'  => $lab_points ?? array(),
                                // consulter
                                'lab_points_champs' => $lab_points_champs ?? array()
                            ));
                        ?>
                    </td>

                </tr>


            </tbody>
        </table>

    </div> <!-- .evaluation-tableau-contenu --> 

</div> <!-- .evaluation-tableau -->

<?
/* --------------------------------------------------------------------
 *
 * Tableau 5
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 5
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 5;

    if ($this->current_controller == 'evaluation')
    {
        $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    }

    if (in_array($this->current_controller, array('consulter', 'corrections')))
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

        <table class="table table-bordered mb-0" style="border-top: 0; border-bottom: 0; border-left: 0; border-right: 0">
            <tbody>

                <tr>
                    <td class="text-center" style="vertical-align: middle">
                        <div>K<sub>c</sub> obtenue</div> 
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>K<sub>c</sub> classe</div> 
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>Pourcentage d'écart</div> 
                        <div>(%)</div>
                    </td>
                </tr>

                <tr>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kc',
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
                                        'champ' => 'kc',
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
                    <td class="text-center" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'kc_classe',
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
                                        'champ' => 'kc_classe',
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

                    <td class="text-center" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'p_ecart',
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
                                        'champ' => 'p_ecart',
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
