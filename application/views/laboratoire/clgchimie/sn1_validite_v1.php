<? 
/* ============================================================================
 *
 * LABORATOIRE - SN1 - VALIDITE
 *
 * VERSION 2025-09-17
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
					<td></td>
					<td></td>
                    <td class="text-center" style="vertical-align: middle;">
                        <div>Masse</div> 
                        <div class="mt-3">
                            <?= lab_champs(
                                array(
                                    'lab_valeurs' => $lab_valeurs,
                                    'champ'       => NULL,
                                    'champ_d'     => 'd_m',
                                    'align'       => 'left',
                                    'unites'      => TRUE,
                                    'unites_v'    => 'g',
                                    // evaluation
                                    'lab_prefix'  => $lab_prefix ?? NULL,
                                    'traces'      => $traces['lab'] ?? array('lab' => array()),
                                    // consultation
                                    'lab_points_champs' => $lab_points_champs ?? array(),
                                ));
                            ?>
							<?= lab_tags(
								array(
									'champ' => 'd_m',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array()
								));
							?>
                        </div>
                    </td>
				</tr>
					
				<tr>
					<td></td>
					<td style="vertical-align: middle;">
                        <div>Bécher vide</div> 
                    </td>
					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_becher_vide',
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
								'champ' => 'm_becher_vide',
								// evaluation
								'montre_tags' => $montre_tags ?? FALSE,
								'lab_points'  => $lab_points ?? array(),
								// consulter
								'lab_points_champs' => $lab_points_champs ?? array()
							));
						?>
					</td>
				<tr>

				<tr>
					<td rowspan="2">Partenaire 1</td>
					<td>Bécher + 1<sup>er</sup> volume d'eau</td>
					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_vol_1',
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
								'champ' => 'm_vol_1',
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
					<td>Bécher + 2<sup>e</sup> volume d'eau</td>
					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_vol_2',
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
								'champ' => 'm_vol_2',
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
					<td rowspan="2">Partenaire 2</td>
					<td>Bécher + 3<sup>e</sup> volume d'eau</td>
					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_vol_3',
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
								'champ' => 'm_vol_3',
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
					<td>Bécher + 4<sup>e</sup> volume d'eau</td>
					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_vol_4',
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
								'champ' => 'm_vol_4',
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
					<td>Température de l'eau</td>

					<td colspan="2" class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'temp_eau',
								'champ_d'     => 'd_temp_eau',
								'align'       => 'right',
								'unites'      => TRUE,
								'unites_v'    => '&deg;C',
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
									'champ' => 'temp_eau',
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
									'champ' => 'd_temp_eau',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array(),
									'inline' => TRUE
								));
							?>
						</div>
					</td>
				</tr>

				<tr>
					<td>Masse volumique H<sub>2</sub>O à cette température</td>
					<td colspan="2" class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'p_eau',
								'champ_d'     => 'd_p_eau',
								'align'       => 'right',
								'unites'      => TRUE,
								'unites_v'    => 'g/mL',
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
									'champ' => 'p_eau',
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
									'champ' => 'd_p_eau',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array(),
									'inline' => TRUE
								));
							?>
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

<?
/* --------------------------------------------------------------------
 *
 * Tableau 2
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau
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
			'tableau_titre'	 => "Évaluation de la validité de la masse d'eau prélevée à l'aide de la pipette jaugée de 10 mL.",
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
                        <div>Essai</div> 
					</td>

                    <td class="text-center" style="vertical-align: middle;">
						<div>Masse H<sub>2</sub>O ajouté</div> 
						<div class="mt-2">
                            <?= lab_champs(
                                array(
                                    'lab_valeurs' => $lab_valeurs,
                                    'champ'       => NULL,
                                    'champ_d'     => 'd_m_eau',
                                    'align'       => 'left',
                                    'unites'      => TRUE,
                                    'unites_v'    => 'g',
                                    // evaluation
                                    'lab_prefix'  => $lab_prefix ?? NULL,
                                    'traces'      => $traces['lab'] ?? array('lab' => array()),
                                    // consultation
                                    'lab_points_champs' => $lab_points_champs ?? array(),
                                ));
                            ?>
							<?= lab_tags(
								array(
									'champ' => 'd_m_eau',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array()
								));
							?>
					</td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Moyenne des masses de H<sub>2</sub>O ajoutées</div> 
						<div class="text-center">(g)</div>
					</td>

                    <td class="text-center" style="vertical-align: middle;">
						<div>Incertitude relative</div> 
						<div class="text-center">(%)</div>
					</td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Masse attendue<span style="color: crimson">*</span> de 10 mL H<sub>2</sub>O</div> 
						<div class="text-center">(g)</div>
					</td>

                    <td class="text-center" style="vertical-align: middle;">
                        <div>Pourcentage d'écart</div> 
						<div class="text-center">(%)</div>
					</td>
				</tr>

				<tr>
					<td class="text-center">1</td>

					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_eau_1',
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
								'champ' => 'm_eau_1',
								// evaluation
								'montre_tags' => $montre_tags ?? FALSE,
								'lab_points'  => $lab_points ?? array(),
								// consulter
								'lab_points_champs' => $lab_points_champs ?? array()
							));
						?>
					</td>

					<td rowspan="4" class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_eau_moy',
								'champ_d'     => 'd_m_eau_moy',
								'align'       => 'right',
								'unites'      => FALSE,
								'unites_v'    => '',
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
									'champ' => 'm_eau_moy',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array(),
									'inline' => FALSE
								));
							?>
							<?= lab_tags(
								array(
									'champ' => 'd_m_eau_moy',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array(),
									'inline' => FALSE
								));
							?>
						</div>
					</td>

					<td rowspan="4" class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'inc_rel',
								'champ_d'     => '',
								'align'       => 'left',
								'unites'      => FALSE,
								'unites_v'    => '',
								// evaluation
								'lab_prefix'  => $lab_prefix ?? NULL,
								'traces'      => $traces['lab'] ?? array('lab' => array()),
								// consultation
								'lab_points_champs' => $lab_points_champs ?? array(),
							));
						?>
						<?= lab_tags(
							array(
								'champ' => 'inc_rel',
								// evaluation
								'montre_tags' => $montre_tags ?? FALSE,
								'lab_points'  => $lab_points ?? array(),
								// consulter
								'lab_points_champs' => $lab_points_champs ?? array(),
								'inline' => FALSE
							));
						?>
					</td>

					<td rowspan="4" class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_eau_attendue',
								'champ_d'     => 'd_m_eau_attendue',
								'align'       => 'right',
								'unites'      => FALSE,
								'unites_v'    => '',
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
									'champ' => 'm_eau_attendue',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array()
								));
							?>
							<?= lab_tags(
								array(
									'champ' => 'd_m_eau_attendue',
									// evaluation
									'montre_tags' => $montre_tags ?? FALSE,
									'lab_points'  => $lab_points ?? array(),
									// consulter
									'lab_points_champs' => $lab_points_champs ?? array()
								));
							?>
						</div>
					</td>

					<td rowspan="4" class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'p_ecart',
								'champ_d'     => '',
								'align'       => 'left',
								'unites'      => FALSE,
								'unites_v'    => '',
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
								'lab_points_champs' => $lab_points_champs ?? array(),
								'inline' => FALSE
							));
						?>
					</td>
				</tr>

				<tr>
					<td class="text-center">2</td>

					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_eau_2',
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
								'champ' => 'm_eau_2',
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
					<td class="text-center">3</td>

					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_eau_3',
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
								'champ' => 'm_eau_3',
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
					<td class="text-center">4</td>

					<td class="text-center">
						<?= lab_champs(
							array(
								'lab_valeurs' => $lab_valeurs,
								'champ'       => 'm_eau_4',
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
								'champ' => 'm_eau_4',
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
					<td colspan="10">
						<span style="color: crimson">*</span> Masse attendue pour 10 mL d'eau : cette valeur est celle que vous auriez dû obtenir et doit être calculée à partir de la masse volumique de l'eau.
				</td>
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

<?
/* --------------------------------------------------------------------
 *
 * Tableau 3 : PRECISION - EXACTITUDE - VALIDITE
 *
 * -------------------------------------------------------------------- */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau
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

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="evaluation-tableau-contenu">

		<?= lab_validite(
				array(
					'precision' 	=> 'precision',
					'exactitude' 	=> 'exactitude',
					'validite' 		=>'validite',
					'lab_prefix'  	=> $lab_prefix ?? NULL,
					'traces'      	=> $traces['lab'] ?? array('lab' => array()),
					'montre_tags' 	=> $montre_tags ?? FALSE,
					'lab_points'  	=> $lab_points ?? array(),
					'lab_points_champs' => $lab_points_champs ?? array()
				)
			);
		?>


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

</div> <!-- #lab-tableaux-specifiques -->
