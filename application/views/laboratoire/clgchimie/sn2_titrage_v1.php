<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - TITRAGRE AB
 *
 * VERSION 2025-04-09
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
 * Determiner le pointage du tableau
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
                    <td class="text-center" style="vertical-align: middle;">
                        Substance
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Volume titré</div> 
                        <div class="mt-3">
                            <?= lab_champs(
                                array(
                                    'lab_valeurs' => $lab_valeurs,
                                    'champ'       => NULL,
                                    'champ_d'     => 'vol_titre_d',
                                    'align'       => 'left',
                                    'unites'      => TRUE,
                                    'unites_v'    => 'mL',
                                    // evaluation
                                    'lab_prefix'  => $lab_prefix ?? NULL,
                                    'traces'      => $traces['lab'] ?? array('lab' => array()),
                                    // consultation
                                    'lab_points_champs' => $lab_points_champs ?? array(),
                                ));
                            ?>
                        </div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Volumes concordants de NaOH</div> 
                        <div class="mt-3">
                            <?= lab_champs(
                                array(
                                    'lab_valeurs' => $lab_valeurs,
                                    'champ'       => NULL,
                                    'champ_d'     => 'vol_naoh_d',
                                    'align'       => 'left',
                                    'unites'      => TRUE,
                                    'unites_v'    => 'mL',
                                    // evaluation
                                    'lab_prefix'  => $lab_prefix ?? NULL,
                                    'traces'      => $traces['lab'] ?? array('lab' => array()),
                                    // consultation
                                    'lab_points_champs' => $lab_points_champs ?? array(),
                                ));
                            ?>
                        </div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Volume moyen de NaOH</div> 
                        <div>(mL)</div>
                    </td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Concentration de l'inconnu
                        <div>(mol/L)</div>
                    </td>
                </tr>

                <tr>
                    <td rowspan="3">
                        HCl inconnu
                    </td>

                    <td rowspan="3"class="text-center">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'vol_titre',
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
                                        'champ' => 'vol_titre',
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
                                        'champ'       => 'vol_naoh_1',
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
                                        'champ' => 'vol_naoh_1',
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

                    <td rowspan="3" class="text-center" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'vol_moy',
                                        'champ_d'     => 'vol_moy_d',
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
                                        'champ' => 'vol_moy',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'vol_moy_d',
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

                    <td rowspan="3" class="text-center" style="border-bottom: 0">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'conc_inconnu',
                                        'champ_d'     => 'conc_inconnu_d',
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
                                        'champ' => 'conc_inconnu',
                                        // evaluation
                                        'montre_tags' => $montre_tags ?? FALSE,
                                        'lab_points'  => $lab_points ?? array(),
                                        // consulter
                                        'lab_points_champs' => $lab_points_champs ?? array()
                                    ));
                                ?>
                                <?= lab_tags(
                                    array(
                                        'champ' => 'conc_inconnu_d',
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
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'vol_naoh_2',
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
                                        'champ' => 'vol_naoh_2',
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
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div>
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'vol_naoh_3',
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
                                        'champ' => 'vol_naoh_3',
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
                    <td colspan="5" class="pt-3">
                        <div class="row">
                            <div class="col-auto pt-2">
                                Concentration de la solution titrante de NaOH
                            </div>
                            <div class="col-auto pt-1">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => 'conc_naoh',
                                        'champ_d'     => 'conc_naoh_d',
                                        'align'       => 'right',
                                        'unites'      => TRUE,
                                        'unites_v'    => 'mol/L',
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
                                            'champ' => 'conc_naoh',
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
                                            'champ' => 'conc_naoh_d',
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

    <? if ($this->current_controller == 'consulter') : ?>

        <?
         /* ---------------------------------------------------------------
          *
          * Commentaire laisse a l'etudiant par l'enseignant
          *
          * --------------------------------------------------------------- */ ?>

        <?= lab_c_commentaires($tableau_no, $tableau_data); ?>

    <? endif; ?>

</div> <!-- .evaluation-tableau -->

</div> <!-- #lab-tableau-specifiques -->
