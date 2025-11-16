<? 
/* ============================================================================
 *
 * LABORATOIRE - SN2 - SPETROPHOTOMETRIE METHYL-ORANGE
 *
 * VERSION 2024-07-30
 *
 * ---> CONSULTER <---
 *
 * ----------------------------------------------------------------------------
 *
 * Le contenu specifique a ce laboratoire.
 *
 * ============================================================================ */ ?>

<? 
	$montre_tags = FALSE;
?>

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
            </div>
        </div>

    </div> <!-- /.tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'c_conc'; ?>
    <? $champ2 = 'v_dil'; ?>

    <? $champ1_d = ajouter_d_champ($champ1); ?>
    <? $champ2_d = ajouter_d_champ($champ2); ?>

    <div class="corriger-tableau-contenu">

        <?
        /* ------------------------------------------------------------
         *
         * Concentration de la solution mere (c_conc)
         *
         * ------------------------------------------------------------- */ ?>
        <div class="row">
            <div class="col-sm-6">

                <?= $lab_points[$champ1]['desc']; ?>

            </div> <!-- .col -->
            <div class="col-sm-6 text-right">
                (
                <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>

                &pm;
                
                <?= lab_montrer_reponse($champ1_d, $lab_points_champs); ?>
                )
                &times;10<sup>-6</sup> mol/L

                <?= lab_montrer_corr($champ1, $lab_points_champs, 'ml-2'); ?>
                <?= lab_montrer_corr($champ1_d, $lab_points_champs, ''); ?>
            </div> <!-- .col -->
        </div> <!-- .row -->

        <?
        /* ------------------------------------------------------------
         *
         * Volume dilue (v_dil)
         *
         * ------------------------------------------------------------- */ ?>
        <div class="row mt-3">
            <div class="col-sm-6">

                <?= $lab_points[$champ2]['desc']; ?>

            </div> <!-- .col -->
            <div class="col-sm-6 text-right" style="">

                (
                <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>

                &pm;
                
                <?= lab_montrer_reponse($champ2_d, $lab_points_champs); ?>
                )
                mL

                <?= lab_montrer_corr($champ2, $lab_points_champs, 'ml-2'); ?>
                <?= lab_montrer_corr($champ2_d, $lab_points_champs, ''); ?>

            </div> <!-- .col -->
        </div> <!-- .row -->


    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <? if ( ! empty($tableau_commentaires)) : ?>

        <div class="corriger-commentaire">

            <div class="font-weight-bold" style="color: crimson">
                Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
            </div>

            <div class="hspace"></div>

            <div>
                <?= _html_out($tableau_commentaires); ?>
            </div>
        
        </div>

    <? endif; ?>

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
            </div>
        </div>

    </div> <!-- /.tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'v_conc'; ?>
    <? $champ2 = 'c_dil'; ?>
    <? $champ3 = 'absorb'; ?>
    <? $champ4 = 'a_absorb'; ?>

    <? $champ1_d = ajouter_d_champ($champ1); ?>
    <? $champ2_d = ajouter_d_champ($champ2); ?>
    <? $champ3_d = ajouter_d_champ($champ3); ?>
    <? $champ4_d = ajouter_d_champ($champ4); ?>

    <div class="corriger-tableau-contenu">

        <table class="table mb-0" style="margin-top: -10px">
            <tr>
                <td class="text-center">
					<div>Solutions</div> 
					<div>étalons</div>
				</td>
                <td>
                    <?
                    /* ------------------------------------------------
                     *
                     * Incertitude sur le volume concentre (v_conc_d)
                     *
                     * ------------------------------------------------ */ ?>

                    <div class="text-center">
                        Volume concentré
                    </div>

                    <div class="text-center">
                        &pm;
                        <?= lab_montrer_reponse($champ1_d, $lab_points_champs); ?>
                        mL 

                        <?= lab_montrer_corr($champ1_d, $lab_points_champs, 'mt-1'); ?>
                    </div>

                </td>
                <td>
                    <div class="text-center">Concentration diluée</div>
                    <div class="text-center">(&times;10<sup>-6</sup> mol/L)</div>
                </td>
                <td class="text-center">
                    Absorbance
                </td>
            </tr>
            <tr>
                <td class="text-center">Blanc</td>
                <td class="text-center">0</td>
                <td class="text-center">0</td>
                <td class="text-center">0,000 &pm; 0,001</td>
            </tr>

            <? foreach(array(1, 2, 3, 4, 5) as $i) : ?>
                <tr>
                    <td class="text-center" style="vertical-align: middle"><?= $i; ?></td>
                    <td>
                        <? 
                        /* --------------------------------------------
                         *
                         * Volume concentre (v_conc)
                         *
                         * -------------------------------------------- */ ?>
                        <div class="text-center">
                            <?= lab_montrer_reponse($champ1 . '-' . $i, $lab_points_champs); ?>
                            <?= lab_montrer_corr($champ1 . '-' . $i, $lab_points_champs, 'mt-1'); ?>
                        </div>
                    </td>
                    <td>
                        <? 
                        /* --------------------------------------------
                         *
                         * Concentration diluee (c_dil) (calcul)
                         *
                         * -------------------------------------------- */ ?>
                        <div class="text-center">
                            <?= lab_montrer_reponse($champ2 . '-' . $i, $lab_points_champs); ?>
                            &pm;
                            <?= lab_montrer_reponse($champ2_d . '-' . $i, $lab_points_champs); ?>

                            <div class="mt-1">
                                <?= lab_montrer_corr($champ2 . '-' . $i, $lab_points_champs, ''); ?>
                                <?= lab_montrer_corr($champ2_d . '-' . $i, $lab_points_champs, ''); ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?
                        /* --------------------------------------------
                         *
                         * Absorbance des solutions etalons (aborb-n)
                         *
                         * -------------------------------------------- */ ?>
                        <div class="text-center">
                            <?= lab_montrer_reponse($champ3 . '-' . $i, $lab_points_champs); ?>
                            &pm;
                            <?= lab_montrer_reponse($champ3_d . '-' . $i, $lab_points_champs); ?>

                            <div class="mt-1">
                                <?= lab_montrer_corr($champ3 . '-' . $i, $lab_points_champs, ''); ?>
                                <?= lab_montrer_corr($champ3_d . '-' . $i, $lab_points_champs, ''); ?>
                            </div>
                        </div>
                    </td>

                </tr>
            <? endforeach; ?>

            <?
            /* --------------------------------------------------------
             *
             * Tableau des resultats
             *
             * -------------------------------------------------------- */ ?>
            <tr>
                <td class="text-left" colspan=3" style="vertical-align: middle">
                    Inconnu A
                </td>
                <td>
                    <?
                    /* ------------------------------------------------
                     *
                     * Absorbance de l'inconnu A (a_absorb)
                     *
                     * ------------------------------------------------ */ ?>
                    <div class="text-center">
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ4_d, $lab_points_champs); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ4, $lab_points_champs, ''); ?>
                            <?= lab_montrer_corr($champ4_d, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                 </td>
            </tr>
        </table>

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <? if ( ! empty($tableau_commentaires)) : ?>

        <div class="corriger-commentaire">

            <div class="font-weight-bold" style="color: crimson">
                Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
            </div>

            <div class="hspace"></div>

            <div>
                <?= _html_out($tableau_commentaires); ?>
            </div>
        
        </div>

    <? endif; ?>

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
            </div> <!-- .col -->
        </div> <!-- .row -->

    </div> <!-- .corriger-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'droite_m'; ?>
    <? $champ2 = 'droite_b'; ?>

    <div class="corriger-tableau-contenu">

        <?
        /* ------------------------------------------------------------
         *
         * Equation de la droite
         *
         * ------------------------------------------------------------- */ ?>
        <div class="row">
            <div class="col-sm-6">

                Équation de votre droite

            </div> <!-- .col -->
            <div class="col-sm-6 text-right">

                y = 
                <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>x + 
                <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>

                <?= lab_montrer_corr($champ1, $lab_points_champs, 'ml-2'); ?>
                <?= lab_montrer_corr($champ2, $lab_points_champs, ''); ?>
            </div> <!-- .col -->
        </div> <!-- .row -->

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <? if ( ! empty($tableau_commentaires)) : ?>

        <div class="corriger-commentaire">

            <div class="font-weight-bold" style="color: crimson">
                Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
            </div>

            <div class="hspace"></div>

            <div>
                <?= _html_out($tableau_commentaires); ?>
            </div>
        
        </div>

    <? endif; ?>

</div> <!-- .corriger-tableau -->

<?
/* ------------------------------------------------------------
 *
 * Tableau 4
 *
 * ------------------------------------------------------------ */ ?>

<?
/* --------------------------------------------------------------------
 *
 * Determiner le pointage du tableau 3
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

                Tableau 4

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

            </div> <!-- .col -->
        </div> <!-- .row -->

    </div> <!-- .corriger-tableau-titre -->

    <?
    /* ----------------------------------------------------------------
     *
     * Contenu du tableau
     *
     * ---------------------------------------------------------------- */ ?>

    <? $champ1 = 'c_exp'; ?>
    <? $champ2 = 'c_adm'; ?>
    <? $champ3 = 'inc_rel'; ?>
    <? $champ4 = 'p_ecart'; ?>

    <? $champ1_d = ajouter_d_champ($champ1); ?>
    <? $champ2_d = ajouter_d_champ($champ2); ?>
    <? $champ3_d = ajouter_d_champ($champ3); ?>
    <? $champ4_d = ajouter_d_champ($champ4); ?>

    <div class="corriger-tableau-contenu" style="padding-bottom: 10px">

        <table class="table mb-0" style="margin-top: -10px;">
            <tr>
                <td></td>
                <td>
                    <div class="text-center">Concentration expérimentale</div>
                    <div class="text-center">(&times;10<sup>-6</sup> mol/L)</div>
                </td>
                <td>
                    <div class="text-center">Concentration admise </div>
                    <div class="text-center">(&times;10<sup>-6</sup> mol/L)</div>
                </td>
                <td>
                    <div class="text-center">Incertitude relative</div>
                    <div class="text-center">(%)</div>
                </td>
                <td>
                    <div class="text-center">Pourcentage d'écart</div>
                    <div class="text-center">(%)</div>
                </td>
            </tr>

            <tr>
                <td style="vertical-align: middle; white-space: nowrap">Inconnu A</td>
                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Concentration experimentale (c_exp)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">
                        <?= lab_montrer_reponse($champ1, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ1_d, $lab_points_champs); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ1, $lab_points_champs, ''); ?>
                            <?= lab_montrer_corr($champ1_d, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>

                <td class="text-center" style="vertical-align: middle;">
                    <?
                    /* --------------------------------------------
                     *
                     * Concentration admise (c_adm)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">
                        <?= lab_montrer_reponse($champ2, $lab_points_champs); ?>
                        &pm;
                        <?= lab_montrer_reponse($champ2_d, $lab_points_champs); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ2, $lab_points_champs, ''); ?>
                            <?= lab_montrer_corr($champ2_d, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>

                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Incertitude relative (inc_rel)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">
                        <?= lab_montrer_reponse($champ3, $lab_points_champs); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ3, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>

                <td>
                    <?
                    /* --------------------------------------------
                     *
                     * Pourcentage d'ecart (p_ecart)
                     *
                     * ------------------------------------------- */ ?>
                    <div class="text-center">
                        <?= lab_montrer_reponse($champ4, $lab_points_champs); ?>

                        <div class="mt-1">
                            <?= lab_montrer_corr($champ4, $lab_points_champs, ''); ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="20">
                    <div class="mt-2" style="font-size: 0.7em; color: #999">
                        (<strong style="color: crimson">*</strong>) 
                        Pour l'incertitude de la concentration expérimentale, veuillez prendre l'incertitude de la solution étalon qui se rapproche le plus de votre valeur d'absorbance.
                    </div>
                </td>
            </tr>
        </table>

    </div> <!-- .corriger-tableau-contenu --> 

    <?
     /* ---------------------------------------------------------------
      *
      * Commentaire laisse a l'etudiant par l'enseignant
      *
      * --------------------------------------------------------------- */ ?>

    <? if ( ! empty($tableau_commentaires)) : ?>

        <div class="corriger-commentaire">

            <div class="font-weight-bold" style="color: crimson">
                Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
            </div>

            <div class="hspace"></div>

            <div>
                <?= _html_out($tableau_commentaires); ?>
            </div>
        
        </div>

    <? endif; ?>

</div> <!-- .corriger-tableau -->

</div> <!-- #lab-spectro-mo -->
