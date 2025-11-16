<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - CINETIQUE CHIMIQUE
 *
 * VERSION 2025-01-15
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

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

<?
/* ----------------------------------------------------------------
 *
 * Les champs du tableau 1
 *
 * ---------------------------------------------------------------- */ ?>

<?
$champ1  = 'temperature_d';
$champ2  = 'temperature_piece';
$champ3  = 'temperature_bain';
$champ4  = 'dv_calc_bain';
$champ5  = 'dv_calc_piece';
$champ6  = 'dv_graph_bain';
$champ7  = 'dv_graph_piece';
$champ8  = 'k_piece';
$champ9  = 'k_bain';
$champ10 = 'energie_activation';
$champ11 = 'unites_k';
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
                        <div>Réaction</div> 
                    </td>

                    <td class="text-center">
                        <div>Température de la réaction</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ_d'     => $champ1,
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
                                        'champ' => $champ1,
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
                        Temps de demi-vie calculé (s)
                    </td>

                    <td class="text-center">
                        Temps de demi-vie graphique (s)
                    </td>

                    <td class="text-center">
                        <div>Constante de vitesse</div>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => $champ11,
                                        'align'       => 'center',
                                        // evaluation
                                        'lab_prefix'  => $lab_prefix ?? NULL,
                                        'traces'      => $traces['lab'] ?? array('lab' => array()),
                                        'type'        => 'select',
                                        'select_choix' => array('mol/L⋅s', 's-1', 'L/mol⋅s', 'L2/mol2⋅s'),
                                        // consultation
                                        'lab_points_champs' => $lab_points_champs ?? array(),
                                    ));
                                ?>

                                <?= lab_tags(
                                    array(
                                        'champ' => $champ11,
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
                        Énergie d'activation <br />(kJ/mol)
                    </td>
                </tr>

                <tr>
                    <td>
                        Température pièce
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => $champ2,
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
                                        'champ' => $champ2,
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
                                        'champ'       => $champ5,
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
                                        'champ' => $champ5,
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
                                        'champ'       => $champ7,
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
                                        'champ' => $champ7,
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
                                        'champ'       => $champ8,
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
                                        'champ' => $champ8,
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
                                        'champ'       => $champ10,
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
                                        'champ' => $champ10,
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
                        Bain thermostaté
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <?= lab_champs(
                                    array(
                                        'lab_valeurs' => $lab_valeurs,
                                        'champ'       => $champ3,
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
                                        'champ' => $champ3,
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
                                        'champ'       => $champ4,
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
                                        'champ' => $champ4,
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
                                        'champ'       => $champ6,
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
                                        'champ' => $champ6,
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
                                        'champ'       => $champ9,
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
                                        'champ' => $champ9,
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
