<? 
/* ============================================================================
 *
 * LABORATOIRE - SN1 - VALIDITE
 *
 * VERSION 2024-08-12
 *
 *
 * ---> CONSULTER <---
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<style>
    .corriger-tableau table tr {
        vertical-align: middle;
    }

    .corriger-tableau table td {
        text-align: center;
    }

    .corriger-tableau table {
        margin-top: -10px;
    }
        
</style>

<? 
	$montre_tags = FALSE;
?>

<script src="<?= base_url() . 'assets/js/lab.js?' . date('U'); ?>"></script>

<style>
    #lab-tableaux-specifiques table td:first-child {
        border-left: 0;
    }

    #lab-tableaux-specifiques table td:last-child {
        border-right: 0;
    }

    #lab-tableaux-specifiques table tr:last-child td {
        border-bottom: 0;
    }
    #lab-tableaux-specifiques table td {
        vertical-align: middle;
    }
</style>


<div id="lab-tableaux-specifiques">

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

    $tableau_points_obtenus_ajustement = $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0;
    $ajustement = $tableau_points_obtenus_ajustement ? TRUE : FALSE;

    $tableau_points_obtenus = $ajustement ? $tableau_points_obtenus_ajustement : $lab_points_tableaux[$tableau_no]['points_obtenus'];
    $tableau_points = $lab_points_tableaux[$tableau_no]['points'];
    
    $tableau_commentaires = $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL;

    $tableau_reussi = ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $tableau_points) ? TRUE : FALSE;
?>

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-titre">

        <div class="row no-gutters">

            <div class="col-8">

                Tableau 1

            </div>
            <div class="col-4">
                <div class="float-right font-weight-bold" style="<?= $tableau_reussi ? '' : 'color: crimson'; ?>">

                    <?= format_nombre($tableau_points_obtenus) . ' / ' . format_nombre($tableau_points); ?>
                    point<?= $tableau_points > 1 ? 's' : ''; ?>

                    <?
                    /* --------------------------------------------------------
                     *
                     * Ajuste les points d'un tableau
                     * Laisser un commentaire a l'etudiant pour ce tableau
                     * 
                     * -------------------------------------------------------- */ ?>

                    <? if ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) : ?>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                            data-toggle="modal" 
                            data-target="#modal-corrections-changer-points-tableau" 
                            data-tableau_no="<?= $tableau_no; ?>"
                            data-ajustement="<?= $ajustement; ?>"
                            data-points_obtenus="<?= format_nombre($tableau_points_obtenus); ?>"
                            data-points="<?= format_nombre($tableau_points); ?>">

                            <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="<?= $ajustement ? 'dodgerblue' : '#aaa'; ?>" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                           data-toggle="modal" 
                           data-target="#modal-laisser-commentaire-tableau" 
                           data-soumission_id="<?= $soumission['soumission_id']; ?>"
                           data-tableau_no="<?= $tableau_no; ?>"
                           data-commentaire="<?= $tableau_commentaires; ?>">

                           <? if ( ! empty($tableau_commentaires)) : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots-fill" fill="dodgerblue" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm5 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>

                           <? else : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots" fill="#aaaaaa" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v11.586l2-2A2 2 0 0 1 4.414 11H14a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                    <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>

                            <? endif; ?>
                        </a>

                    <? endif; ?>
				</div>

            </div> <!-- .col-4 -->
        </div>

    </div> <!-- /.question-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?
    $champ1  = 'm_becher_vide_d';
    $champ2  = 'm_becher_d';
    $champ3  = 'm_becher_vide_p';
    $champ4  = 'm_becher_p-1';
    $champ5  = 'm_becher_p-2';
    $champ6  = 'm_becher_p-3';
    $champ7  = 'm_becher_vide_b';
    $champ8  = 'm_becher_b-1';
    $champ9  = 'm_becher_b-2';
    $champ10 = 'm_becher_b-3';
    $champ11 = 'temp';
    $champ12 = 'temp_d';
	$champ13 = 'rho';
	$champ14 = 'rho_d';
	$champ15 = 'rho_temp';
    ?>
    
    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0;">
            <tbody>
                <tr>
                    <td rowspan="2" class="text-left">Instruments</td>

					<?
					/* ------------------------------------------------
					 *
                     * Masse du becher vide
					 *
					 * ------------------------------------------------ */ ?>
                    <td rowspan="2" class="text-center" style="">
                        <div>Masse bécher vide</div>
                        <div>
                            (&pm; <?= lab_montrer_reponse($champ1, $lab_points_champs); ?> g)
                        </div>
                        <?= lab_montrer_corr($champ1, $lab_points_champs, 'mt-2 mb-2'); ?>
                    </td>

					<?
					/* ------------------------------------------------
					 *
                     * Masse du becher + H2O
					 *
					 * ------------------------------------------------ */ ?>
                    <td colspan="3" class="text-center">
                        <div>Masse bécher + H<sub>2</sub>O</div>
                        <div>
                            (&pm; <?= lab_montrer_reponse($champ2, $lab_points_champs); ?> g)
                        </div>
                        <?= lab_montrer_corr($champ2, $lab_points_champs, 'mt-2 mb-2'); ?>
                    </td>
                </tr>
                <tr>        
                    <td class="text-center">Essai 1</td>
                    <td class="text-center">Essai 2</td>
                    <td class="text-center">Essai 3</td>
                </tr> 
				<?
				/* ------------------------------------------------
				 *
				 * Pipette jaugee de 10 mL
				 *
				 * ------------------------------------------------ */ ?>
                <tr>
                    <td class="text-left">
                        <div>Pipette jaugée</div>
                        <div>de 10 mL</div>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ3, $lab_points_champs, 'mt-2'); ?>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ4, $lab_points_champs, 'mt-2'); ?>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ5, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ5, $lab_points_champs, 'mt-2'); ?>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ6, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ6, $lab_points_champs, 'mt-2'); ?>
                    </td>
                </tr>
				<?
				/* ------------------------------------------------
				 *
				 * Burette de 25 mL
				 *
				 * ------------------------------------------------ */ ?>
                <tr>
                    <td class="text-left">
                        <div>Burette</div>
                        <div>de 25 mL</div>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ7, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ7, $lab_points_champs, 'mt-2'); ?>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ8, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ8, $lab_points_champs, 'mt-2'); ?>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ9, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ9, $lab_points_champs, 'mt-2'); ?>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ10, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ10, $lab_points_champs, 'mt-2'); ?>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
					 * Temperature de l'eau (temp)
					 *
					 * ------------------------------------------------ */ ?>
                    <td colspan="5">
                        <div class="row">
                            <div class="col-sm text-left">
                                Température de l'eau
                            </div>
                            <div class="col-sm-8 text-right">
                                <?= lab_montrer_reponse($champ11, $lab_points_champs); ?>
                                &pm;
                                <?= lab_montrer_reponse($champ12, $lab_points_champs); ?> &deg;C
                                <div class="mt-2">
                                    <?= lab_montrer_corr($champ11, $lab_points_champs); ?>
                                    <?= lab_montrer_corr($champ12, $lab_points_champs, 'ml-2'); ?>
                                </div>
                            </div>
                        </div> <!-- .row -->
                    </td>
                </tr>

				<?
				/* ------------------------------------------------
				 *
				 * Masse volumique de l'eu (rho)
				 *
				 * ------------------------------------------------ */ ?>
                <tr>
                    <td colspan="5">
                        <div class="row">
                            <div class="col-sm text-left">
                                Masse volumique H<sub>2</sub>O à T&deg;C
                            </div>
                            <div class="col-sm-8 text-right">
                                <?= lab_montrer_reponse($champ13, $lab_points_champs); ?>
                                &pm;
                                <?= lab_montrer_reponse($champ14, $lab_points_champs); ?> g/mL 
                                à
                                <?= lab_montrer_reponse($champ15, $lab_points_champs); ?> &deg;C

                                <div class="mt-2">
                                    <?= lab_montrer_corr($champ13, $lab_points_champs); ?>
                                    <?= lab_montrer_corr($champ14, $lab_points_champs, 'ml-2'); ?>
                                    <?= lab_montrer_corr($champ15, $lab_points_champs, 'ml-2'); ?>
                                </div>
                            </div>
                        </div> <!-- .row -->
					</td>
				</tr>

            </tbody>

        </table>

    </div> <!-- .corriger-tableau-contenu --> 

</div> <!-- .corriger-tableau -->

<?
/* ------------------------------------------------------------
 *
 * Tableau 2
 *
 * ------------------------------------------------------------ */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 2
 *
 * -------------------------------------------------------------------- */ ?>

<? 
    $tableau_no = 2;

    $tableau_points_obtenus_ajustement = $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0;
    $ajustement = $tableau_points_obtenus_ajustement ? TRUE : FALSE;

    $tableau_points_obtenus = $ajustement ? $tableau_points_obtenus_ajustement : $lab_points_tableaux[$tableau_no]['points_obtenus'];
    $tableau_points = $lab_points_tableaux[$tableau_no]['points'];

    $tableau_commentaires = $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL;

    $tableau_reussi = ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $tableau_points) ? TRUE : FALSE;
?>

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-titre">

        <div class="row no-gutters">
            <div class="col-8">

                Tableau 2

            </div>
            <div class="col-4">
                <div class="float-right font-weight-bold" style="<?= $tableau_reussi ? '' : 'color: crimson'; ?>">

                    <?= format_nombre($tableau_points_obtenus) . ' / ' . format_nombre($tableau_points); ?>
                    point<?= $tableau_points > 1 ? 's' : ''; ?>

                    <?
                    /* --------------------------------------------------------
                     *
                     * Ajuste les points d'un tableau
                     * Laisser un commentaire a l'etudiant pour ce tableau
                     * 
                     * -------------------------------------------------------- */ ?>

                    <? if ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) : ?>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                            data-toggle="modal" 
                            data-target="#modal-corrections-changer-points-tableau" 
                            data-tableau_no="<?= $tableau_no; ?>"
                            data-ajustement="<?= $ajustement; ?>"
                            data-points_obtenus="<?= format_nombre($tableau_points_obtenus); ?>"
                            data-points="<?= format_nombre($tableau_points); ?>">

                            <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="<?= $ajustement ? 'dodgerblue' : '#aaa'; ?>" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                           data-toggle="modal" 
                           data-target="#modal-laisser-commentaire-tableau" 
                           data-soumission_id="<?= $soumission['soumission_id']; ?>"
                           data-tableau_no="<?= $tableau_no; ?>"
                           data-commentaire="<?= $tableau_commentaires; ?>">

                           <? if ( ! empty($tableau_commentaires)) : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots-fill" fill="dodgerblue" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm5 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>

                           <? else : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots" fill="#aaaaaa" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v11.586l2-2A2 2 0 0 1 4.414 11H14a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                    <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>

                            <? endif; ?>
                        </a>

                    <? endif; ?>

				</div>
            </div> <!-- .col-4 -->
        </div>

    </div> <!-- .corriger-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?
    	$champ1  = 'm_eau_d';
    	$champ2  = 'm_eau_p-1';
    	$champ3  = 'm_eau_p-2';
    	$champ4  = 'm_eau_p-3';
    	$champ5  = 'm_eau_moy_p';
    	$champ6  = 'm_eau_moy_p_d';
    	$champ7  = 'm_eau_b-1';
    	$champ8  = 'm_eau_b-2';
    	$champ9  = 'm_eau_b-3';
    	$champ10 = 'm_eau_moy_b';
    	$champ11 = 'm_eau_moy_b_d';
	?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>
                <tr>
                    <td class="text-left">Instruments</td>
                    <td colspan="3" class="text-center">
						Masse H<sub>2</sub>O
                        <div>
                            (&pm; <?= lab_montrer_reponse($champ1, $lab_points_champs); ?> g)
                        </div>
                        <?= lab_montrer_corr($champ1, $lab_points_champs, 'mt-2 mb-2'); ?>
					</td>
                    <td class="text-center">
                        <div>Masse moyenne H<sub>2</sub>O</div>
                        <div>(g)</div>
                    </td>
                </tr>
				<?
				/* ----------------------------------------------------
				 *
                 * Pipette jaugee de 10 mL
                 *
                 * ---------------------------------------------------- */ ?>
                <tr>
                    <td class="text-left">
						<div>Pipette jaugée</div>
						<div>de 10 mL</div>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ2, $lab_points_champs, 'mt-2'); ?>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ3, $lab_points_champs, 'mt-2'); ?>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ4, $lab_points_champs, 'mt-2'); ?>

					</td>
                    <td>
                        <?= lab_montrer_reponse($champ5, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ6, $lab_points_champs); ?>

                        <div class="mt-2">
                            <?= lab_montrer_corr($champ5, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ6, $lab_points_champs, 'ml-2'); ?>
                        </div>
					</td>
                </tr>
                <tr>
                    <td class="text-left">
						<div>Burette</div>
						<div>de 25 mL</div>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ7, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ7, $lab_points_champs, 'mt-2'); ?>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ8, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ8, $lab_points_champs, 'mt-2'); ?>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ9, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ9, $lab_points_champs, 'mt-2'); ?>

					</td>
                    <td>
                        <?= lab_montrer_reponse($champ10, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ11, $lab_points_champs); ?>

                        <div class="mt-2">
                            <?= lab_montrer_corr($champ10, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ11, $lab_points_champs, 'ml-2'); ?>
                        </div>
					</td>
                </tr>
            </tbody>

        </table>

    </div> <!-- .corriger-tableau-contenu --> 

</div> <!-- .corriger-tableau -->

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
    $tableau_points_obtenus_ajustement = $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0;
    $ajustement = $tableau_points_obtenus_ajustement ? TRUE : FALSE;

    $tableau_points_obtenus = $ajustement ? $tableau_points_obtenus_ajustement : $lab_points_tableaux[$tableau_no]['points_obtenus'];
    $tableau_points = $lab_points_tableaux[$tableau_no]['points'];

    $tableau_commentaires = $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL;

    $tableau_reussi = ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $tableau_points) ? TRUE : FALSE;
?>

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-titre">

        <div class="row no-gutters">

            <div class="col-8">

                Tableau 3

            </div>
            <div class="col-4">
                <div class="float-right font-weight-bold" style="<?= $tableau_reussi ? '' : 'color: crimson'; ?>">

                    <?= format_nombre($tableau_points_obtenus) . ' / ' . format_nombre($tableau_points); ?>
                    point<?= $tableau_points > 1 ? 's' : ''; ?>

                    <?
                    /* --------------------------------------------------------
                     *
                     * Ajuste les points d'un tableau
                     * Laisser un commentaire a l'etudiant pour ce tableau
                     * 
                     * -------------------------------------------------------- */ ?>

                    <? if ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) : ?>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                            data-toggle="modal" 
                            data-target="#modal-corrections-changer-points-tableau" 
                            data-tableau_no="<?= $tableau_no; ?>"
                            data-ajustement="<?= $ajustement; ?>"
                            data-points_obtenus="<?= format_nombre($tableau_points_obtenus); ?>"
                            data-points="<?= format_nombre($tableau_points); ?>">

                            <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="<?= $ajustement ? 'dodgerblue' : '#aaa'; ?>" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                           data-toggle="modal" 
                           data-target="#modal-laisser-commentaire-tableau" 
                           data-soumission_id="<?= $soumission['soumission_id']; ?>"
                           data-tableau_no="<?= $tableau_no; ?>"
                           data-commentaire="<?= $tableau_commentaires; ?>">

                           <? if ( ! empty($tableau_commentaires)) : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots-fill" fill="dodgerblue" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm5 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>

                           <? else : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots" fill="#aaaaaa" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v11.586l2-2A2 2 0 0 1 4.414 11H14a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                    <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>

                            <? endif; ?>
                        </a>

                    <? endif; ?>

				</div>
            </div> <!-- .col-4 -->
        </div>

    </div> <!-- /.question-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <?
    	$champ1  = 'v_theo_p';
    	$champ2  = 'v_theo_p_d';
    	$champ3  = 'v_exp_p';
    	$champ4  = 'v_exp_p_d';
    	$champ5  = 'inc_rel_p';
    	$champ6  = 'p_ecart_p';
    	$champ7  = 'v_theo_b';
    	$champ8  = 'v_theo_b_d';
    	$champ9  = 'v_exp_b';
    	$champ10 = 'v_exp_b_d';
		$champ11 = 'inc_rel_b';
    	$champ12 = 'p_ecart_b';
	?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>
                <tr>
                    <td style="vertical-align: middle">Instruments</td>

                    <td class="text-center" style="vertical-align: middle">
                        <div>Volume théorique H<sub>2</sub>O</div>
                        <div>(mL)</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle">
                        <div>Volume expérimental H<sub>2</sub>O</div>
                        <div>(mL)</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle">
                        <div>Incertitude relative</div>
                        <div>sur le volume expérimental</div>
                        <div>(%)</div>
                    </td>
                    <td class="text-center" style="vertical-align: middle">
                        <div>Pourcentage d'écart</div>
                        <div> entre le volume expérimental</div>
                        <div>et théorique</div>
                        <div>(%)</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>Pipette jaugée</div>
                        <div> de 10 mL</div>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>
                        <div class="mt-2">
                            <?= lab_montrer_corr($champ1, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ2, $lab_points_champs, 'ml-2'); ?>
                        </div>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>
                        <div class="mt-2">
                            <?= lab_montrer_corr($champ3, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ4, $lab_points_champs, 'ml-2'); ?>
                        </div>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ5, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ5, $lab_points_champs, 'mt-2'); ?>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ6, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ6, $lab_points_champs, 'mt-2'); ?>
					</td>
                </tr>
                <tr>
                    <td>
                        <div>Burette</div>
                        <div> de 25 mL</div>
                    </td>
                    <td>
                        <?= lab_montrer_reponse($champ7, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ8, $lab_points_champs); ?>
                        <div class="mt-2">
                            <?= lab_montrer_corr($champ7, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ8, $lab_points_champs, 'ml-2'); ?>
                        </div>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ9, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ10, $lab_points_champs); ?>
                        <div class="mt-2">
                            <?= lab_montrer_corr($champ9, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ10, $lab_points_champs, 'ml-2'); ?>
                        </div>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ11, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ11, $lab_points_champs, 'mt-2'); ?>
					</td>
                    <td>
                        <?= lab_montrer_reponse($champ12, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ12, $lab_points_champs, 'mt-2'); ?>
					</td>
                </tr>
            </tbody>

        </table>

    </div> <!-- .corriger-tableau-contenu --> 

</div> <!-- .corriger-tableau -->

<?
/* --------------------------------------------------------------------
 *
 * Tableau 4 : PRECISION - EXACTITUDE - VALIDITE
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

    $tableau_points_obtenus_ajustement = $lab_points_tableaux[$tableau_no]['points_obtenus_ajustement'] ?? 0;
    $ajustement = $tableau_points_obtenus_ajustement ? TRUE : FALSE;

    $tableau_points_obtenus = $ajustement ? $tableau_points_obtenus_ajustement : ($lab_points_tableaux[$tableau_no]['points_obtenus'] ?? 0);
    $tableau_points = $lab_points_tableaux[$tableau_no]['points'] ?? 0;

    $tableau_commentaires = $lab_points_tableaux[$tableau_no]['commentaires'] ?? NULL;

    $tableau_reussi = ($lab_points_tableaux[$tableau_no]['points_obtenus'] == $tableau_points) ? TRUE : FALSE;
?>

<div class="corriger-tableau" data-tableau_no="<?= $tableau_no; ?>">

    <?
    /* ----------------------------------------------------------------
     *
     * Titre du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-titre">

        <div class="row no-gutters">

            <div class="col-8">

                Tableau 4 : La validité des résultats

            </div>
            <div class="col-4">
                <div class="float-right font-weight-bold" style="<?= $tableau_reussi ? '' : 'color: crimson'; ?>">

                    <?= format_nombre($tableau_points_obtenus) . ' / ' . format_nombre($tableau_points); ?>
                    point<?= $tableau_points > 1 ? 's' : ''; ?>

                    <?
                    /* --------------------------------------------------------
                     *
                     * Ajuste les points d'un tableau
                     * Laisser un commentaire a l'etudiant pour ce tableau
                     * 
                     * -------------------------------------------------------- */ ?>

                    <? if ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) : ?>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                            data-toggle="modal" 
                            data-target="#modal-corrections-changer-points-tableau" 
                            data-tableau_no="<?= $tableau_no; ?>"
                            data-ajustement="<?= $ajustement; ?>"
                            data-points_obtenus="<?= format_nombre($tableau_points_obtenus); ?>"
                            data-points="<?= format_nombre($tableau_points); ?>">

                            <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="<?= $ajustement ? 'dodgerblue' : '#aaa'; ?>" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>

                        <a href="#" style="text-decoration: none; margin-left: 5px"
                           data-toggle="modal" 
                           data-target="#modal-laisser-commentaire-tableau" 
                           data-soumission_id="<?= $soumission['soumission_id']; ?>"
                           data-tableau_no="<?= $tableau_no; ?>"
                           data-commentaire="<?= $tableau_commentaires; ?>">

                           <? if ( ! empty($tableau_commentaires)) : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots-fill" fill="dodgerblue" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm5 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>

                           <? else : ?>

                                <svg viewBox="0 0 16 16" class="bi bi-chat-left-dots" fill="#aaaaaa" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v11.586l2-2A2 2 0 0 1 4.414 11H14a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                    <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>

                            <? endif; ?>
                        </a>

                    <? endif; ?>
				</div>
            </div> <!-- .col-4 -->
        </div>

    </div> <!-- /.question-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

	<?
		$champ1 = 'precision_p';
		$champ2 = 'exactitude_p';
		$champ3 = 'validite_p';
		$champ4 = 'precision_b';
		$champ5 = 'exactitude_b';
		$champ6 = 'validite_b';
	?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
			<tbody>
				<tr>
					<td></td>
					<td class="text-center">Précision</td>
					<td class="text-center">Exactitude</td>
					<td class="text-center">Validité</td>
				</tr>
				<tr>
					<td class="text-left">Pipette jaugée</td>

					<?
					/* ------------------------------------------------
					 *
                     * Precision - pipette jaugee
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ1, $lab_points_champs, 'mt-2'); ?>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Exactitude - pipette jaugee
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ2, $lab_points_champs, 'mt-2'); ?>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Validite - pipette jaugee
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ3, $lab_points_champs, 'mt-2'); ?>
					</td>
				</tr>

				<tr>
                    <td class="text-left">Burette</td>

					<?
					/* ------------------------------------------------
					 *
                     * Precision - pipette jaugee
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ4, $lab_points_champs, 'mt-2'); ?>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Exactitude - pipette jaugee
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ5, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ5, $lab_points_champs, 'mt-2'); ?>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Validite - burette
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ6, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ6, $lab_points_champs, 'mt-2'); ?>
					</td>
				</tr>
			</tbody>
		</table>

    </div> <!-- .corriger-tableau-contenu --> 

</div> <!-- .corriger-tableau -->

</div> <!-- #lab-tableaux-specifiques -->
