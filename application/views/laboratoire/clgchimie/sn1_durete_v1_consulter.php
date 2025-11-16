<? 
/* ============================================================================
 *
 * LABORATOIRE - SN1 - DURETE - CONSULTER
 *
 * VERSION 2024-09-24
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

<?
/* --------------------------------------------------------------------
 *
 * Les styles specifiques a ce laboratoire
 *
 * -------------------------------------------------------------------- */ ?>

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

<?
/* --------------------------------------------------------------------
 *
 * TABLEAUX SPECIFIQUES
 *
 * -------------------------------------------------------------------- */ ?>

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

    </div> <!-- .corriger-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Les champs du tableau 1
     *
     * ---------------------------------------------------------------- */ ?>

    <?
    $champ1  = 'v_eau';
    $champ2  = 'v_eau_d';
    $champ3  = 'c_edta';
    $champ4  = 'c_edta_d';
    ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Echantillon d'eau
					 *
                     * ------------------------------------------------ */ ?>

                    <td colspan"1">
                        <div>Échantillon d'eau</div>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Volume d'echantillon d'eau a analyser
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Volume d'échantillon d'eau à analyser (mL)</div>
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
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Concentration de la solution EDTA
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Concentration de la solution EDTA (&times;10<sup><?= $lab_valeurs['c_edta']['nsci']; ?></sup> mol/L)</div>
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
                </tr>

                <?
                 /* ---------------------------------------------------------------
                  *
                  * Commentaire laisse a l'etudiant par l'enseignant
                  *
                  * --------------------------------------------------------------- */ ?>

                <? if ( ! empty($tableau_commentaires)) : ?>

                    <tr>
                        <td>
                            <div class="corriger-tableau-commentaires">

                                <div class="font-weight-bold" style="color: crimson">
                                    Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
                                </div>

                                <div class="mt-2">
                                    <?= _html_out($tableau_commentaires); ?>
                                </div>
                            
                            </div>
                        </td>
                    </tr>

                <? endif; ?>

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

                Tableau <?= $tableau_no; ?>

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
     * Les champs du tableau 2
     *
     * ---------------------------------------------------------------- */ ?>

    <?
        $champ1  = 'v_edta_d';
        $champ2  = 'v_edta-1';
        $champ3  = 'v_edta-2';
        $champ4  = 'v_edta-3';
        $champ5  = 'v_edta-4';
    ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Essais realises
					 *
					 * ------------------------------------------------ */ ?>

                    <td colspan="2">
                        <div>Essais réalisés</div>
                    </td>
            
                    <td class="text-center">
                        <div>Volume EDTA</div>

                        <div>
                            (&pm; <?= lab_montrer_reponse($champ1, $lab_points_champs); ?> mL)
                        </div>
                        <?= lab_montrer_corr($champ1, $lab_points_champs, 'mt-2 mb-2'); ?>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Partenaire 1
					 *
					 * ------------------------------------------------ */ ?>

                    <td rowspan="2">
                        <div>Partenaire 1</div>
                    </td>
            
                    <td class="text-center">
                        Essai 1
                    </td>

                    <td>
                        <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>
                        <div>
                            <?= lab_montrer_corr($champ2, $lab_points_champs); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        Essai 2
                    </td>

                    <td>
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>
                        <div>
                            <?= lab_montrer_corr($champ3, $lab_points_champs); ?>
                        </div>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Partenaire 2
					 *
					 * ------------------------------------------------ */ ?>

                    <td rowspan="2" style="border-bottom: 0">
                        <div>Partenaire 2</div>
                    </td>
            
                    <td class="text-center">
                        Essai 3
                    </td>

                    <td>
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>
                        <div>
                            <?= lab_montrer_corr($champ4, $lab_points_champs); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        Essai 4
                    </td>

                    <td>
                        <?= lab_montrer_reponse($champ5, $lab_points_champs); ?>
                        <div>
                            <?= lab_montrer_corr($champ5, $lab_points_champs); ?>
                        </div>
                    </td>
                </tr>

                <?
                 /* ---------------------------------------------------------------
                  *
                  * Commentaire laisse a l'etudiant par l'enseignant
                  *
                  * --------------------------------------------------------------- */ ?>

                <? if ( ! empty($tableau_commentaires)) : ?>

                    <tr>
                        <td>
                            <div class="corriger-tableau-commentaires">

                                <div class="font-weight-bold" style="color: crimson">
                                    Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
                                </div>

                                <div class="mt-2">
                                    <?= _html_out($tableau_commentaires); ?>
                                </div>
                            
                            </div>
                        </td>
                    </tr>

                <? endif; ?>

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

                Tableau <?= $tableau_no; ?>

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
     * Les champs du tableau 3
     *
     * ---------------------------------------------------------------- */ ?>

    <?
        $champ1  = 'v_moy';
        $champ2  = 'v_moy_d';
        $champ3  = 'durete';
        $champ4  = 'durete_d';
        $champ5  = 'inc_rel';
        $champ6  = 'v_adm';
        $champ7  = 'v_adm_d';
        $champ8  = 'p_ecart';
    ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
            <tbody>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Moyenne des volumes EDTA
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Moyenne des volumes EDTA (mL)</div>
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
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Durete de l'eau analysee (ppm CaCO3)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Dureté de l'eau analysée (ppm CaCO<sub>3</sub>)</div>
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
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Incertitude de la durete (%)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Incertitude relative de la dureté (%)</div>
                    </td>
            
                    <td>
                        <?= lab_montrer_reponse($champ5, $lab_points_champs); ?>
                        <div>
                            <?= lab_montrer_corr($champ5, $lab_points_champs); ?>
                        </div>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Valeur admise de la durete (ppm CaCO3)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Valeur admise de la dureté (ppm CaCO<sub>3</sub>)</div>
                    </td>
            
                    <td>
                        <?= lab_montrer_reponse($champ6, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ7, $lab_points_champs); ?>
                        <div class="mt-2">
                            <?= lab_montrer_corr($champ6, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ7, $lab_points_champs, 'ml-2'); ?>
                        </div>
                    </td>
                </tr>

                <tr>
					<?
					/* ------------------------------------------------
					 *
                     * Pourcentage d'ecart (%)
					 *
					 * ------------------------------------------------ */ ?>

                    <td>
                        <div>Pourcentage d'écart entre la dureté expérimentale et admise (%)</div>
                    </td>
            
                    <td>
                        <?= lab_montrer_reponse($champ8, $lab_points_champs); ?>
                        <div>
                            <?= lab_montrer_corr($champ8, $lab_points_champs); ?>
                        </div>
                    </td>
                </tr>

                <?
                 /* ---------------------------------------------------------------
                  *
                  * Commentaire laisse a l'etudiant par l'enseignant
                  *
                  * --------------------------------------------------------------- */ ?>

                <? if ( ! empty($tableau_commentaires)) : ?>

                    <tr>
                        <td>
                            <div class="corriger-tableau-commentaires">

                                <div class="font-weight-bold" style="color: crimson">
                                    Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
                                </div>

                                <div class="mt-2">
                                    <?= _html_out($tableau_commentaires); ?>
                                </div>
                            
                            </div>
                        </td>
                    </tr>

                <? endif; ?>

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
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 4
 *
 * -------------------------------------------------------------------- */ ?>

<? 
	$tableau_no = 4;
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

                Tableau <?= $tableau_no; ?>

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
     * Les champs du tableau 4
     *
     * ---------------------------------------------------------------- */ ?>

    <?
        $champ1  = 'precision';
        $champ2  = 'exactitude';
        $champ3  = 'validite';
    ?>

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="corriger-tableau-contenu">

        <table class="table table-bordered mb-0" style="border: 0">
			<tbody>
				<tr>
					<td class="text-center">Précision</td>
					<td class="text-center">Exactitude</td>
					<td class="text-center">Validité</td>
				</tr>
				<tr>
					<?
					/* ------------------------------------------------
					 *
                     * Precision
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ1, $lab_points_champs, 'mt-2'); ?>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Exactitude
                     *
                     * ------------------------------------------------ */ ?>
                    <td class="text-center">
                        <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ2, $lab_points_champs, 'mt-2'); ?>
					</td>

					<?
					/* ------------------------------------------------
					 *
                     * Validite
                     *
                     * ------------------------------------------------ */ ?>
					<td class="text-center">
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>
                        <?= lab_montrer_corr($champ3, $lab_points_champs, 'mt-2'); ?>
					</td>
                </tr>

                <?
                 /* ---------------------------------------------------------------
                  *
                  * Commentaire laisse a l'etudiant par l'enseignant
                  *
                  * --------------------------------------------------------------- */ ?>

                <? if ( ! empty($tableau_commentaires)) : ?>

                    <tr>
                        <td>
                            <div class="corriger-tableau-commentaires">

                                <div class="font-weight-bold" style="color: crimson">
                                    Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
                                </div>

                                <div class="mt-2">
                                    <?= _html_out($tableau_commentaires); ?>
                                </div>
                            
                            </div>
                        </td>
                    </tr>

                <? endif; ?>

			</tbody>
		</table>

    </div> <!-- .corriger-tableau-contenu --> 

</div> <!-- .corriger-tableau -->

</div> <!-- #lab-tableaux-specifiques -->
