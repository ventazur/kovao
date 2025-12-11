<?
/* ----------------------------------------------------------------------------
 *
 * CONSULTER UNE EVALUATION CORRIGEE
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="consulter-voir" data-soumission_id="<?= $soumission['soumission_id']; ?>">
<div class="container-fluid">

<div id="soumission-data" 
	data-soumission_id="<?= $soumission['soumission_id']; ?>"
    data-points_obtenus="<?= $soumission['points_obtenus']; ?>"
    data-points_total="<?= $soumission['points_evaluation']; ?>">
</div>

<? $lab = $soumission['lab']; ?>

<? if ($lab) : ?>

    <link href="<?= base_url() . 'assets/css/lab.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

    <?
        $lab_data       = json_decode($soumission['lab_data'], TRUE);
        $lab_valeurs    = json_decode($soumission['lab_valeurs'], TRUE);
        $lab_points     = json_decode($soumission['lab_points'], TRUE);

        $lab_points_champs   = json_decode($soumission['lab_points_champs'], TRUE);
        $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);
        $lab_vue        = $lab_data['lab_vue'] ?? NULL;

        $data_lab = array(
            'lab' => $lab,
            'lab_data' => $lab_data,
            'lab_valeurs' => $lab_valeurs,
            'lab_points'  => $lab_points,
            'lab_points_champs' => $lab_points_champs,
            'lab_points_tableaux' => $lab_points_tableaux
        );
    ?>

<? endif; ?>

<div class="row">

    <a class="anchor" name="top"></a>

    <?
    /* --------------------------------------------------------------------
     *
     * NAVIGATION
     *
     * -------------------------------------------------------------------- */ ?>
        
    <div id="consulter-navigation" class="col-xl-1 d-none d-xl-block">

        <? if ( ! empty($questions) && count($questions) > 1) : ?>

            <? $this->load->view('consulter/_consulter_navigation'); ?>

        <? endif; // count($questions) > 10 ?>

    </div> <!-- #consulter-navigation -->

    <div class="col-sm-12 col-xl-10">

    <?
    /* --------------------------------------------------------------
     *
     * BARRE DE DEFILEMENT HAUT
     *
     * -------------------------------------------------------------- */ ?>

    <? if ($this->uri->segment(3) == 'defilement' && ( ! empty($defilement_prec) || ! empty($defilement_suiv))) : ?>

        <div class="defilement defilement-haut">
            <div class="row">

                <div class="col-6">
                    <? if ( ! empty($defilement_prec)) : ?>
                        <a href="<?= base_url() . 'consulter/' . $defilement_prec_prem . '/defilement'; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-angle-double-left"></i>
                        </a>
                        <a href="<?= base_url() . 'consulter/' . $defilement_prec . '/defilement'; ?>" 
                           class="btn btn-sm btn-outline-primary" 
                           style="background: dodgerblue; color: #fff; width: 125px">
                            <i class="fa fa-angle-left" style="margin-right: 8px"></i>
                            précédente
                        </a>
                    <? else : ?>
                        <div class="btn btn-sm" style="color: #1565C0; cursor: default">
                            Barre de défilement
                            <i class="fa fa-info-circle" style="margin-left: 5px"
                               data-trigger="hover" 
                               data-toggle="popover" 
                               data-html="true"
                               data-placement="top"
                               data-content="L'ordre de défilement est déterminé par l'ordre affiché dans les résultats. "></i>
                        </div>
                    <? endif; ?>
                </div>

                <div class="col-6" style="text-align: right">
                    <? if ( ! empty($defilement_suiv)) : ?>
                        <a href="<?= base_url() . 'consulter/' . $defilement_suiv . '/defilement'; ?>" class="btn btn-sm btn-outline-primary" style="background: dodgerblue; color: #fff; width: 125px">
                            suivante
                            <i class="fa fa-angle-right" style="margin-left: 9px"></i>
                        </a>
                        <a href="<?= base_url() . 'consulter/' . $defilement_suiv_dern . '/defilement'; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-angle-double-right"></i>
                        </a>
                    <? endif; ?>
                </div>

            </div> <!-- .row -->
        </div> <!-- .defilement -->

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * EVALUATION CORRIGEE
     *
     * ------------------------------------------------------------------------ */ ?>

    <div class="row">

        <div class="col-8 mb-3">
            <? if ($soumission['corrections_terminees']) : ?>
                <h3>
                    <span style="color: crimson">
                        <? if ($lab) : ?>
                            Laboratoire corrigé
                        <? else : ?>
                            Évaluation corrigée
                        <? endif; ?>
                    </span>
                </h3>
            <? else : ?>
                <h3><span style="color: crimson; background: #ffcdd2; padding: 4px 8px 4px 8px; border-radius: 5px">Évaluation non corrigée</span></h3>
            <? endif; ?>
        </div>

        <div class="col-4 mt-1 mb-3" style="text-align: right;">

                <? 
                /* ------------------------------------------------------------
                 *
                 * Afficher les points de l'evaluation (soumission)
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if ($soumission['points_evaluation'] > 0) : ?>

                    <span id="soumission-points-obtenus">

                        <?= my_number_format($ajustements['total'] ?? $soumission['points_obtenus']) . ' / ' . my_number_format($soumission['points_evaluation']); ?> 

                        <span style="padding-left: 10px">(<?= number_format(($ajustements['total'] ?? $soumission['points_obtenus']) / $soumission['points_evaluation'] * 100)?>%)</span>
                        
                    </span>

                    <? if ( ! $version_etudiante && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) : ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Ajustement des points
                         *
                         * ---------------------------------------------------- */ ?>

                        <a href="#" style="margin-left: 8px; text-decoration: none" data-toggle="modal" data-target="#modal-corrections-changer-points-soumission" 
                            data-points_obtenus="<?= my_number_format($ajustements['total'] ?? $soumission['points_obtenus']); ?>"
                            data-question_points="<?= my_number_format($soumission['points_evaluation']); ?>">

                            <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="<?= array_key_exists('total', $ajustements) ? 'dodgerblue' : '#aaa'; ?>" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Laisser un commentaire a la soumission
                         *
                         * ---------------------------------------------------- */ ?>
                                   
                        <a href="#" style="text-decoration: none; margin-left: 5px;" 
                           data-toggle="modal" 
                           data-target="#modal-laisser-commentaire-soumission"
                           data-soumission_id="<?= $soumission['soumission_id']; ?>"
                           data-commentaire="<?= array_key_exists('total', $commentaires) ? _html_edit($commentaires['total']) : NULL; ?>">
                    
                            <? if (array_key_exists('total', $commentaires) && ! empty($commentaires['total'])) : ?>

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

                <? endif; ?>

        </div> <!-- .col-4 -->

    </div> <!-- .row -->

    <? 
    /* ------------------------------------------------------------------------
     * 
     * COMMENTAIRES DE L'ENSEIGNANT POUR LA SOUMISSION
     *
     * ------------------------------------------------------------------------ */ ?>

    <? if (array_key_exists('total', $commentaires) && ! empty($commentaires['total'])) : ?>

        <div style="margin-bottom: 30px; border: 1px solid crimson; background: #fff3f3">

            <div style="color: crimson; font-family: Lato; font-weight: 600; padding: 10px 12px 10px 12px">
                Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
            </div>

            <div style="font-family: Lato; padding: 0px 12px 10px 12px;">

                <?= _html_out($commentaires['total']); ?>

            </div>
        </div>

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * IDENTIFICATION DE L'EVALUATION
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="cours">

        <div class="row">

            <div class="col-sm-7">
                <label class="label-cours">
                    Cours :
                </label>
                <div class="font-weight-bold"><?= $cours['cours_nom'] . ' (' . $cours['cours_code'] . ')'; ?></div>
            </div>

            <div class="col-sm-2">
                <label class="label-cours">
                    Semestre :
                </label>
                <div class="font-weight-bold"><?= $cours['semestre_nom']; ?></div>
            </div>

            <div class="col-sm-3">
                <label class="label-cours">
                    Remise :
                </label>
                <div class="font-weight-bold">
                    <?= $soumission['soumission_date']; ?>

                    <? if ($soumission['non_terminee']) : ?>
                        <span data-toggle="tooltip" title="Cette évaluation a été terminée par l'enseignant.">
                            <svg style="margin-top: -2px; margin-left: 3px; margin-right: 3px;" viewBox="0 0 18 18" class="bi-xs bi-slash-circle" fill="crimson" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path fill-rule="evenodd" d="M11.354 4.646a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </span>
                    <? endif; ?>
                </div>
            </div>

            <div class="col-sm-9 mt-3">
                <label class="label-cours">
                    <? if ($lab) : ?>
                        Laboratoire :
                    <? else : ?>
                        Évaluation :
                    <? endif; ?>
                </label>
                <div class="font-weight-bold"><?= $evaluation['evaluation_titre']; ?></div>
            </div>


            <div class="col-sm-3 mt-3">
                <label class="label-cours">
                    Enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?> : 
                </label>
                <div class="font-weight-bold"><?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></div>
            </div>
                
        </div>

    </div> <!-- #cours -->

    <? 
    /* ------------------------------------------------------------------------
     * 
     * IDENTIFICATION DES ETUDIANTS
     *
     * ------------------------------------------------------------------------ */ ?>

    <? if ($lab) : ?>

        <? 
        /* ------------------------------------------------------------------------
         * 
         * IDENTIFICATION DES PARTENAIRES DE LABORATOIRE
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="identification-titre">
            <div class="row">
                <div class="col-6">
                    Identification des partenaires de laboratoire
                </div>
                <div class="col-6" style="text-align: right"> 
                    <? if ( ! $version_etudiante && $this->est_enseignant && empty($this->uri->segment(3))) : ?>
                        [<a style="font-size: 0.85em; color: #ffff" href="<?= current_url() . '/etudiant'; ?>">voir version étudiante</a>]
                    <? endif; ?>
                </div>
            </div>
        </div>

        <div id="identification">

            <div class="form-row">

                <?
                /* ----------------------------------------------------------------
                 *
                 * Numero de place
                 *
                 * ---------------------------------------------------------------- */ ?>

                <div class="col-md-2">
                    <label class="label-identification" for="lab-place"># de place</label>
                    <div class="font-weight-bold">
                        <?= $lab_data['lab_place']; ?>
                    </div>
                </div>

                <?
                /* ----------------------------------------------------------------
                 *
                 * Nom des partenaires
                 *
                 * ---------------------------------------------------------------- */ ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Partenaire 1 (principal)
                 *
                 * ------------------------------------------------------------ */ ?>

                <div class="col-md">
                    <label class="label-identification" for="evaluation-nom">
                        Partenaire 1
                    </label>
                    <div class="font-weight-bold">
                        <?= $soumission['prenom_nom']; ?>
                    </div>
                </div>

                <?
                /* ------------------------------------------------------------
                 *
                 * Partenaires de laboratoire
                 *
                 * Determinons les partenaires presents
                 *
                 * ------------------------------------------------------------ */ ?>

                <?
                    $lab_partenaire2 = FALSE;
                    $lab_partenaire3 = FALSE;

                    // retrocompatibilite avec les premiers essais 
                    if (array_key_exists('lab_partenaire2', $lab_data) && ! empty($lab_data['lab_partenaire2']))
                    {
                        $lab_partenaire2 = TRUE;
                        $lab_partenaire2_nom = $lab_data['lab_partenaire2'];
                    }

                    if (array_key_exists('lab_partenaire2_nom', $lab_data) && ! empty($lab_data['lab_partenaire2_nom']))
                    {
                        $lab_partenaire2 = TRUE;
                        $lab_partenaire2_nom = $lab_data['lab_partenaire2_nom'];
                    }

                    // retrocompatibilite avec les premiers essais 
                    if (array_key_exists('lab_partenaire3', $lab_data) && ! empty($lab_data['lab_partenaire3']))
                    {
                        $lab_partenaire2 = TRUE;
                        $lab_partenaire3_nom = $lab_data['lab_partenaire3'];
                    }

                    if (array_key_exists('lab_partenaire3_nom', $lab_data) && ! empty($lab_data['lab_partenaire3_nom']))
                    {
                        $lab_partenaire3 = TRUE;
                        $lab_partenaire3_nom = $lab_data['lab_partenaire3_nom'];
                    }
                ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Partenaire 2
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if ($lab_partenaire2) : ?>

                    <div class="col-md">
                        <label class="label-identification" for="lab-partenaire2">Partenaire 2</label>
                        <div class="font-weight-bold">

                            <span><?= $lab_data['lab_partenaire2_nom']; ?></span>

                        </div>
                    </div>

                <? endif; ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Partenaire 3 (facultatif)
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if ($lab_partenaire3) : ?>

                    <div class="col-md">
                        <label class="label-identification" for="lab-partenaire3">Partenaire 3</label>
                        <div class="font-weight-bold">

                            <span><?= $lab_data['lab_partenaire3_nom']; ?></span>

                        </div>
                    </div>

                <? endif; ?>

            </div>  <!-- .form-row -->

        </div> <!-- #identification-contenu -->

        <?
        /* --------------------------------------------------------------------
         *
         * Tableaux des mesures et des resultats
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="discussion" class="consulter-section-titre">
            <div class="row">
                <div class="col">
                    Tableaux des mesures et des résultats
                </div>

                <div class="col text-right">
                    <?
                    /* ------------------------------------------------
                     *
                     * Re-correction
                     *
                     * ------------------------------------------------ */ ?> 

                    <? if ( ! $version_etudiante && $this->est_enseignant && $this->enseignant_id == 1) : ?>

                        <div id="recorrection" class="btn btn-sm btn-light" style="padding-top: 1px; padding-bottom: 0px; font-size: 0.8em; margin-top: -2px;">Recorrection</div>

                    <? endif; ?>

                    <?
                    /* ------------------------------------------------
                     *
                     * Precorrection
                     *
                     * ------------------------------------------------ */ ?> 

                    <? if (array_key_exists('lab_precorrections', $data_lab['lab_data']) && ! empty($data_lab['lab_data']['lab_precorrections'])) : ?>

                        <? if ($data_lab['lab_data']['lab_precorrections']['penalite'] > 0) : ?>

                            <? $p_penalite = $data_lab['lab_data']['lab_precorrections']['penalite_pct'] ?? $data_lab['lab_data']['lab_precorrections']['penalite']; ?>

                            <span id="precorrections-penalite">
                                Pénalité <?= str_replace('.', ',', $p_penalite); ?>%
                                : <?= $data_lab['lab_data']['lab_precorrections']['compte']; ?> précorrections
                            </span>

                        <? endif; ?>

                    <? endif; ?>
                </div> <!-- .row -->
            </div>
        </div>

        <? if ( ! empty($lab_vue)) : ?>

            <? // avant 2025-01-21, la vue de l'evaluation et de consultation etaient differentes ?>

            <? if (file_exists(APPPATH . 'views/laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue . '_consulter.php')) : ?>

                <? $this->load->view('laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue . '_consulter', $data_lab); ?>

            <? else : ?>

                <? $this->load->view('laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue, $data_lab); ?>

            <? endif; ?>

        <? endif; ?>

        <? if ( ! empty($questions)) : ?>

            <?
            /* --------------------------------------------------------------------
             *
             * Discussion
             *
             * -------------------------------------------------------------------- */ ?>

            <div id="discussion" class="consulter-section-titre">
                Discussion
            </div>

        <? endif; ?>

    <? else : ?>

        <? 
        /* ------------------------------------------------------------------------
         * 
         * IDENTIFICATION DE L'ETUDIANT
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="identification-titre">

            <div class="row">

                <div class="col-6" style="text-align: left">
                    Identification de l'étudiant
                </div>

                <div class="col-6" style="text-align: right"> 
                    <? if ( ! $version_etudiante && $this->est_enseignant && empty($this->uri->segment(3))) : ?>
                        [<a style="font-size: 0.85em; color: #ffff" href="<?= current_url() . '/etudiant'; ?>">voir version étudiante</a>]
                    <? endif; ?>
                </div>

            </div>
        </div>

        <div id="identification">

            <div class="row">

                <div class="col-sm-9">
                    <label class="label-identification" for="evaluation-nom">
                        Prénom et Nom
                        <? if ( ! $version_etudiante && ! empty($soumission['etudiant_id'])) : ?>
                            <span style="font-weight: 300">(Étudiant ID)</span>
                        <? endif; ?>
                        :
                    </label>
                    <div class="font-weight-bold">
                        <?= $soumission['prenom_nom']; ?>
                        <? if ( ! $version_etudiante && ! empty($soumission['etudiant_id'])) : ?>

                            <? if ($this->est_enseignant && $this->enseignant['privilege'] > 89) : ?>
                                <span class="mono" style="font-weight: 300">
                                    (<a href="<?= base_url() . 'admin/etudiant/' . $soumission['etudiant_id']; ?>" target="_blank"><?= $soumission['etudiant_id']; ?></a>)
                                </span>
                            <? else : ?>
                                <span class="mono" style="font-weight: 300">(<?= $soumission['etudiant_id']; ?>)</span>
                            <? endif; ?>

                        <? endif; ?>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="label-identification" for="evaluation-numero-da">
                        <?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?> :
                    </label>
                    <div class="font-weight-bold"><?= $soumission['numero_da']; ?></div>
              </div>

            </div>

        </div> <!-- /#identification -->


    <? endif; // if lab ?>

    <? 
    /* ------------------------------------------------------------------------
     * 
     * QUESTIONS 
     *
     * ------------------------------------------------------------------------ */ ?>

    <? 
    $i = 0; 

    foreach($questions as $q) : 

		if ( ! array_key_exists('question_id', $q))
			continue;

        $question_id = $q['question_id']; 

        if ( ! array_key_exists('sondage', $q) || ! $q['sondage'])
        { 
            $question_points_obtenus = array_key_exists($question_id, $ajustements) ? $ajustements[$question_id]['points_obtenus'] : $q['points_obtenus'];
            $question_reussie        = ($question_points_obtenus == $q['question_points']) ? TRUE : FALSE;
            $ajustement 		     = array_key_exists($question_id, $ajustements) ? 1 : 0;
        }
        else
        {
            $ajustement  = 0;
            $question_reussie = TRUE;
        }

        $i++;

        $partials = array(
            'i' => $i,
            'q' => $q,
            'question_id' => $question_id
        );
    ?>

    <? 
    /* --------------------------------------------
     * 
     * Image de la question
     *
     * --------------------------------------------- */ ?>

    <? if (is_array($images) && array_key_exists($question_id, $images)) : ?>

        <div class="corriger-question-image<?= ($i == 1) ? '-question-1' : ''; ?>">

            <? if ($images[$question_id]['s3']) : ?>
                
                <img src="<?= $this->config->item('s3_url', 'amazon') . 'evaluations/' . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

            <? else : ?>

                <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

            <? endif; ?>

            <? if ( ! empty($images[$question_id]['doc_caption'])) : ?>

                <p style="margin-top: 20px"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></p>

            <? endif; ?>

        </div>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------------
     *
     * Question
     *
     * -------------------------------------------------------------------- */ ?>

    <a class="anchor" name="question-q<?= $i; ?>"></a>

    <div class="corriger-question">

        <?
        /* --------------------------------------------------------------------
         *
         * En-tete de la question
         *
         * -------------------------------------------------------------------- */ ?>

        <div class="corriger-question-titre">

            <div class="row">

                <div class="col-sm-8">

                    Question <?= $i; ?>

                    <? if ( ! $version_etudiante && $this->est_enseignant) : ?>
                        <span class="badge badge-pill" style="background: #ddd; color: #888; font-size: 0.8em; font-weight: normal; margin-left: 10px">Question ID <?= $question_id; ?></span>
                    <? endif; ?>

                    <? if ( ! $q['corrigee']) : ?>
                        <span style="color: crimson; margin-left: 10px;">NON CORRIGÉE</span>
                    <? endif; ?>

                </div>

                <div class="col-sm-4">
                    <?
                    /* ------------------------------------------------------------
                     *
                     * Afficher les points obtenus a une question
                     *
                     * ------------------------------------------------------------ */ ?>

                    <div class="float-right font-weight-bold" style="<?= ! $question_reussie ? 'color: crimson;' : ''; ?>">

                        <? if ( ! array_key_exists('sondage', $q) || ! $q['sondage']) : ?> 

                            <?= my_number_format($question_points_obtenus) . ' / ' . my_number_format($q['question_points']); ?> point<?= $q['question_points'] > 1 ? 's' : ''; ?>

                            <?
                            /* --------------------------------------------------------
                             *
                             * Permettre de changer (ajuster) les points d'une question
                             * 
                             * -------------------------------------------------------- */ ?>

                            <? if ( ! $version_etudiante && $q['corrigee'] && $this->est_enseignant && ($this->enseignant_id == $soumission['enseignant_id'] || $this->enseignant['privilege'] > 90)) : ?>

                                <a href="#" style="text-decoration: none; margin-left: 5px"
                                    data-toggle="modal" 
                                    data-target="#modal-corrections-changer-points" 
                                    data-question_id="<?= $question_id; ?>"
                                    data-ajustement="<?= $ajustement; ?>"
                                    data-points_obtenus="<?= my_number_format($question_points_obtenus); ?>"
                                    data-question_points="<?= my_number_format($q['question_points']); ?>">

                                    <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="<?= $ajustement ? 'dodgerblue' : '#aaa'; ?>" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </a>

                                <a href="#" style="text-decoration: none; margin-left: 5px"
                                   data-toggle="modal" 
                                   data-target="#modal-laisser-commentaire" 
                                   data-soumission_id="<?= $soumission['soumission_id']; ?>"
                                   data-question_id="<?= $question_id; ?>"
                                   data-commentaire="<?= $commentaires[$q['question_id']] ?? NULL; ?>">

                                   <? if (array_key_exists($q['question_id'], $commentaires) && ! empty($commentaires[$q['question_id']])) : ?>

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

                        <? else : ?>

                            <span style="color: #999">Sondage</span>

                        <? endif; ?>

                    </div>

                </div> <!-- .col-sm-4 -->
            </div> <!-- .row -->

        </div> <!-- .corriger-question-titre -->

        <?
        /* --------------------------------------------------------------------
         *
         * Texte de la question
         *
         * -------------------------------------------------------------------- */ ?>

        <div class="corriger-question-texte">

            <div><?= _html_out($q['question_texte']); ?></div> 

        </div>

        <? 
        /* ------------------------------------------------------------------------
         * 
         * Reponse repondue par l'etudiant
         *
         * ------------------------------------------------------------------------ */ ?>
        
        <? if ($q['question_type'] == 10) : ?>

            <? $this->load->view('consulter/_consulter_question_type_10', $partials); ?>

        <? else : ?>

            <? $this->load->view('consulter/_consulter_question_type_plusieurs', $partials); ?>

        <? endif; ?>

        <? 
        /* ------------------------------------------------------------------------
         * 
         * Reponse attendue
         *
         * Si la question n'est pas reussi
         *
         * SAUF (AVANT)
         *  　　La question est reussie mais c'est une question de type 6
         *      donc il se peut que la reponse repondue n'est pas exactement 
         *      identique a la reponse correcte du aux tolerances, alors
         *      afficher la reponse attendue dans ce cas
         *      ( ! $question_reussie || ($q['question_type'] == 6 && ($q['reponse_repondue_texte'] != $q['reponse_correcte_texte']))) && 
         *
         * ET
         *      La question est de type 2 et il n'y pas de reponse correcte
         *      car c'est une question a developpement
         *
         * ET
         *      La question de type 10 et il n'y a pas de reponse attendue
         *      car la reponse se trouve dans les documents televerses.
         *
         * ET (AVANT)
         *      La visualisation n'autorise pas d'afficher la reponse attendue
         *      permettre_visualisation == 2 (presentement inactif)
         *      et ce n'est pas un enseignant car on peut afficher la reponse
         *      attendue aux enseignants
         * 
         * ------------------------------------------------------------------------ */ ?>

        <?  if ( ! $question_reussie) : ?>
        
            <? 
            /* --------------------------------------------------------
             * 
             * Question a developpement
             * Question a developpement court
             * Question a repondre par televersement
             *
             * TYPES : 2, 10, 12
             *
             * --------------------------------------------------------- */ ?>

            <? if (in_array($q['question_type'], array(2, 10, 12))) : ?>

                <? $this->load->view('consulter/_consulter_grille_perso', $partials); ?>

            <? 
            /* --------------------------------------------------------
             * 
             * Question a choix multiples 
             *
             * TYPE : 4
             *
             * --------------------------------------------------------- */ ?>

            <? elseif ($q['question_type'] == 4) : ?>

                <div class="corriger-reponse-attendue">

                    <div class="font-weight-bold" style="color: crimson">
                        Réponse attendue : 
                    </div>

                    <div class="hspace"></div>

                    <? if (is_array($q['reponse_toutes'])) : ?>

                        <? foreach($q['reponse_toutes'] as $r_id) : ?>

                            <div>
                                <?= filter_symbols($q['reponse_toutes_texte'][$r_id]); ?>

                                <? if (is_array($q['reponse_correcte_texte']) && array_key_exists($r_id, $q['reponse_correcte_texte'])) : ?>

                                    <div class="badge" style="background: #ddd">
                                        <i class="fa fa-check" style="color: limegreen"></i>
                                        <? if (in_array($r_id, $q['reponse_repondue'])) : ?>
                                            +<?= sw_comma($q['points_par_reponse']); ?>
                                        <? endif; ?>
                                    </div>

                                <? else : ?>

                                    <div class="badge badge-light" style="background: #ddd">
                                        <i class="fa fa-times" style="color: crimson"></i>
                                        <? if ( ! in_array($r_id, $q['reponse_repondue'])) : ?>
                                            +<?= sw_comma($q['points_par_reponse']); ?>
                                        <? endif; ?>
                                    </div>

                                <? endif; ?>
                            </div>

                        <? endforeach; ?>

                    <? endif; ?>

                </div> <!-- .corriger-reponse-attendue -->
            <? 
            /* --------------------------------------------------------
             * 
             * Question a choix multiples stricte 
             *
             * TYPE : 11
             *
             * --------------------------------------------------------- */ ?>

            <? elseif ($q['question_type'] == 11) : ?>

                <div class="corriger-reponse-attendue">

                    <div class="font-weight-bold" style="color: crimson">
                        Réponse attendue (stricte) : 
                    </div>

                    <div class="hspace"></div>

                    <? if (is_array($q['reponse_toutes'])) : ?>

                        <? foreach($q['reponse_toutes'] as $r_id) : ?>

                            <div>
                                <?= filter_symbols($q['reponse_toutes_texte'][$r_id]); ?>
                                <? if (array_key_exists($r_id, $q['reponse_correcte_texte'])) : ?>

                                    <div class="badge" style="background: #ddd">
                                        <i class="fa fa-check" style="color: limegreen"></i>
                                    </div>

                                <? else : ?>

                                    <div class="badge badge-light" style="background: #ddd">
                                        <i class="fa fa-times" style="color: crimson"></i>
                                    </div>

                                <? endif; ?>
                            </div>

                        <? endforeach; ?>

                    <? endif; ?>

                </div> <!-- .corriger-reponse-attendue -->

            <? 
            /* --------------------------------------------------------
             * 
             * Question a choix unique (TYPE 1)
             * Question a choix unique par equation (TYPE 3)
             * Question a reponse numerique entiere (TYPE 5)
             * Question a reponse numerique (TYPE 6)
             * Question a reponse numerique par equation (TYPE 9)
             *
             * TYPES : 1, 3, 5, 6, 7, 9
             *
             * --------------------------------------------------------- */ ?>

            <? else : ?>

                <div class="corriger-reponse-attendue">

                    <div class="font-weight-bold" style="color: #43A047">
                        Réponse attendue : 
                    </div>

                    <div class="hspace"></div>

                    <div>
                        <?= filter_symbols($q['reponse_correcte_texte']); ?>
                    </div>

                </div> <!-- .corriger-reponse-attendue -->

            <? endif; ?>

        <? endif; // ! question_reussie ?>

        <?
         /* ---------------------------------------------------------------
          *
          * Commentaire laisse a l'etudiant par l'enseignant
          *
          * --------------------------------------------------------------- */ ?>

        <? if (array_key_exists($q['question_id'], $commentaires) && $commentaires[$q['question_id']] != NULL) : ?>

            <div class="corriger-commentaire">

                <div class="font-weight-bold" style="color: crimson">
                    Commentaire de l'enseignant<?= $soumission['cours_data']['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
                </div>

                <div class="hspace"></div>

                <div>
                    <?= _html_out($commentaires[$q['question_id']]); ?>
                </div>
            
            </div>

        <? endif; ?>

    </div> <!-- .corriger-question -->

    <? endforeach; //questions ?>

    <?
    /* --------------------------------------------------------------
     *
     * SECURITE
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! $version_etudiante) : ?>

        <?
        /* --------------------------------------------------------------
         *
         * Registre de l'activite de l'etudiant (VERSION 1)
         *
         * -------------------------------------------------------------- */ ?>

        <? if ( ! empty($activite)) : ?>

            <div id="securite-activite-titre">
                <i class="fa fa-shield" style="margin-right: 5px"></i>
                Registre de l'activité de cet étudiant pendant son évaluation
            </div>

            <div id="securite-activite-contenu">

                <table class="table table-sm" style="margin: 0; font-size: 0.85em">
                    <thead>
                        <tr>
                            <th style="width: 200px">Date</th>
                            <th style="width: 120px">Adresse IP</th>
                            <th style="width: 100px; text-align: center">Étudiant ID</th>
                            <th>Unique ID</th>
                            <th>Fureteur ID</th>
                            <th style="width: 225px">URI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach($activite as $a) : ?>
                            <tr>
                                <td class="mono"><?= date_humanize($a['epoch'], TRUE); ?></td>
                                <td class="mono"><?= @$a['adresse_ip']; ?></td>
                                <td class="mono" style="text-align: center"><?= @$a['etudiant_id']; ?></td>
                                <td class="mono"><?= substr($a['unique_id'], 0, 32); ?></td>
                                <td class="mono">
                                    <? if (array_key_exists($a['fureteur_id'], $fureteurs_desc)) : ?>

                                        <span data-toggle="popover" data-placement="top" data-content="<?= $fureteurs_desc[$a['fureteur_id']]; ?>" style="cursor: pointer">
                                            <?= substr(@$a['fureteur_id'], 0, 32); ?>
                                        </span>

                                    <? else : ?>

                                        <?= substr(@$a['fureteur_id'], 0, 32); ?>

                                    <? endif; ?>
                                </td>
                                <td class="mono"><?= $a['uri']; ?></td>
                            </tr>
                        <? endforeach; ?>
                    </tbody>
                </table>

            </div>

        <? endif; ?>

        <?
        /* --------------------------------------------------------------
         *
         * Registre de l'activite de l'etudiant (VERSION 2)
         *
         * -------------------------------------------------------------- */ ?>

        <? if ( ! empty($activite2)) : ?>

            <div id="securite-activite-titre">
                <i class="fa fa-shield" style="margin-right: 5px"></i>
                Registre de l'activité de cet étudiant pendant son évaluation
            </div>

            <div id="securite-activite-contenu">

                <table class="table table-sm" style="margin: 0; font-size: 0.85em">
                    <thead>
                        <tr>
                            <th style="width: 180px">Date</th>
                            <th style="width: 120px">Adresse IP</th>
                            <th style="width: 100px; text-align: center">Étudiant ID</th>
                            <th style="width: 100px">Fureteur ID</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach($activite2 as $a) : ?>
                            <tr>
                                <td class="mono"><?= date_humanize($a['epoch'], TRUE); ?></td>
                                <td class="mono"><?= @$a['adresse_ip']; ?></td>
                                <td class="mono" style="text-align: center">
                                    <a href="<?= base_url() . 'etudiant/' . $a['etudiant_id']; ?>" target="_blank">
                                        <?= @$a['etudiant_id']; ?>
                                    </a>
                                </td>
                                <td class="mono">
                                    <? if ( ! empty($a['plateforme']) && ! empty($a['fureteur'])) : ?>

                                        <span data-toggle="popover" data-content="<?= $a['plateforme'] . ', ' . $a['fureteur']; ?>" data-placement="top">
                                            <?= substr(@$a['fureteur_id'], 0, 12); ?>
                                        </span>

                                    <? else : ?>

                                        <? if (array_key_exists('fureteur_id', $a) && ! empty($a['fureteur_id'])) : ?>

                                            <?= substr(@$a['fureteur_id'], 0, 12); ?>

                                        <? endif; ?>

                                    <? endif; ?>
                                </td>
                                <td>
                                    <? /*
                                        <span data-toggle="popover" data-content="<?= $a['action']; ?>" data-placement="top">
                                            <?= $a['action']; ?>
                                        </span>
                                    */ ?>
                                    <?= $a['action']; ?>
                                </td>
                            </tr>
                        <? endforeach; ?>
                    </tbody>
                </table>

            </div>

        <? endif; ?>

    <? endif;  // ! $version_etudiante ?>

    <?
    /* --------------------------------------------------------------
     *
     * BARRE DE DEFILEMENT BAS
     *
     * -------------------------------------------------------------- */ ?>

    <? if ($this->uri->segment(3) == 'defilement' && ( ! empty($defilement_prec) || ! empty($defilement_suiv))) : ?>

        <div class="defilement defilement-bas">
            <div class="row">

                <div class="col-6">
                    <? if ( ! empty($defilement_prec)) : ?>
                        <a href="<?= base_url() . 'consulter/' . $defilement_prec_prem . '/defilement'; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-angle-double-left"></i>
                        </a>
                        <a href="<?= base_url() . 'consulter/' . $defilement_prec . '/defilement'; ?>" class="btn btn-sm btn-outline-primary"
                           style="background: dodgerblue; color: #fff; width: 125px">
                            <i class="fa fa-angle-left" style="margin-right: 8px"></i>
                            précédente
                        </a>
                    <? endif; ?>
                </div>

                <div class="col-6" style="text-align: right">
                    <? if ( ! empty($defilement_suiv)) : ?>
                        <a href="<?= base_url() . 'consulter/' . $defilement_suiv . '/defilement'; ?>" class="btn btn-sm btn-outline-primary" 
                           style="background: dodgerblue; color: #fff; width: 125px">
                            suivante
                            <i class="fa fa-angle-right" style="margin-left: 9px"></i>
                        </a>
                        <a href="<?= base_url() . 'consulter/' . $defilement_suiv_dern . '/defilement'; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-angle-double-right"></i>
                        </a>
                    <? endif; ?>
                </div>

            </div> <!-- .row -->
        </div> <!-- .defilement -->

        <a href="<?= $retour_resultats; ?>" class="btn btn-outline-secondary">
            <i class="fa fa-undo" style="margin-right: 3px"></i> Retour
        </a>

    <? endif; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #corrections-voir -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: CHANGER LES POINTS DE LA SOUMISSION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-corrections-changer-points-soumission" class="modal" tabindex="0" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajuster les points obtenus à cette évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding-left: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-corrections-changer-points-soumission-form'), 
                        array(
                            'soumission_id'         => $soumission['soumission_id'],
                            'soumission_reference'  => $soumission['soumission_reference'],
                            'points_obtenus'        => $soumission['points_obtenus'],
                            'soumission_points'     => $soumission['points_evaluation']
                        )
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="hspace"></div>

					Ajustement des points obtenus :

					<div class="space"></div>

					<div class="form-row">
						<div class="col-md-3 mb-2">
							<div class="input-group">
                                <input id="modal-corrections-changer-points-obtenus-soumission" name="nouveau_points_obtenus" i
                                       type="text" class="form-control" style="text-align: right" value="<?= array_key_exists('total', $ajustements) ? str_replace('.', ',', $ajustements['total']) : NULL; ?>" required>
								<div class="input-group-append">
                                    <span id="modal-corrections-changer-points-total-soumission" class="input-group-text" style="font-weight: 700">
                                        / <?= my_number_format($soumission['points_evaluation']); ?>
                                    </span>
								</div>
							</div>
                        </div>  
					</div>

					<div class="hspace"></div>

					<div id="modal-corrections-changer-points-obtenus-soumission-invalide" style="font-size: 0.85em; color: crimson" class="d-none">
						<i class="fa fa-exclamation-circle"></i> Les points obtenus ne peuvent être supérieurs au pointage maximum alloué pour la question.
                    </div>

					<div style="font-size: 0.85em; color: crimson">
						<i class="fa fa-exclamation-circle"></i> L'ajustement des points d'une évaluation a priorité sur les ajustements des points aux questions.
                    </div>

					<div class="hspace"></div>


				</form>
      		</div>
      
            <div class="modal-footer">
                <div id="modal-corrections-changer-points-soumission-sauvegarde" class="btn btn-success spinnable">
                    <i class="fa fa-save"></i> Ajuster les points
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div id="modal-corrections-effacer-ajustement-soumission-sauvegarde" class="btn btn-outline-danger" data-dismiss="modal">
                    <i class="fa fa-trash"></i> Effacer l'ajustement
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div> <!-- .modal-footer -->

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: CHANGER LES POINTS D'UNE QUESTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-corrections-changer-points" class="modal" tabindex="0" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajuster les points obtenus à une question</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding-left: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-corrections-changer-points-form'), 
                        array(
                            'soumission_id'   => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference'],
                            'question_id'     => NULL,
                            'points_obtenus'  => NULL,
                            'points'          => NULL
                        )
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="hspace"></div>

					<label>Ajustement des points obtenus à la
                    <span class="badge badge-pill" style="background: #ddd; color: #222; font-size: 0.8em; font-weight: normal; margin-left: 2px">Question ID
						<span id="modal-corrections-changer-points-question-id"></span>
					</span>
					</label> :

					<div class="hspace"></div>

					<div class="form-row">
						<div class="col-md-3 mb-2">
							<div class="input-group">
								<input id="modal-corrections-changer-points-obtenus" name="nouveau_points_obtenus" type="text" class="form-control" style="text-align: right" required>
								<div class="input-group-append">
									<span id="modal-corrections-changer-points-total" class="input-group-text" style="font-weight: 700"></span>
								</div>
							</div>
						</div>  
					</div>

					<div class="hspace"></div>

					<div id="modal-corrections-changer-points-obtenus-invalide" style="font-size: 0.85em; color: crimson" class="d-none">
						<i class="fa fa-exclamation-circle"></i> Les points obtenus ne peuvent être supérieurs au pointage maximum alloué pour la question.
					</div>

				</form>
      		</div>
      
			<div class="modal-footer">
				<div id="modal-corrections-changer-points-sauvegarde" class="btn btn-success spinnable">
					<i class="fa fa-save"></i> Ajuster les points
					<i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
				</div>
				<div id="modal-corrections-effacer-ajustement-sauvegarde" class="btn btn-danger" data-dismiss="modal">
					<i class="fa fa-trash"></i> Effacer l'ajustement</div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: LAISSER UN COMMENTAIRE A UN ETUDIANT (POUR UNE SOUMISSION)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-laisser-commentaire-soumission" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Laisser un commentaire à l'étudiant</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-laisser-commentaire-soumission-form'), 
                        array(
                            'soumission_id' => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference']
                        )
					); ?>

					<div class="form-group col-md-12">
                        <textarea name="commentaire" class="form-control" id="modal-laisser-commentaire-soumission" rows="5" placeholder="Votre commentaire"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-laisser-commentaire-soumission-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer ce commentaire
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-laisser-commentaire-soumission-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Sauvegarder
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: LAISSER UN COMMENTAIRE A UN ETUDIANT (POUR UNE QUESTION)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-laisser-commentaire" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Laisser un commentaire à l'étudiant pour cette question</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-laisser-commentaire-form'), 
                        array(
                            'soumission_id' => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference'],
                            'question_id' => NULL
                        )
					); ?>

					<div class="form-group col-md-12">
                        <textarea name="commentaire" class="form-control" id="modal-laisser-commentaire" rows="5" placeholder="Votre commentaire"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-laisser-commentaire-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer ce commentaire
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-laisser-commentaire-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Sauvegarder
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL DES LABORATOIRES
 *
 * ------------------------------------------------------------------------- */ ?>	

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: CHANGER LES POINTS D'UN TABLEAU
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-corrections-changer-points-tableau" class="modal" tabindex="0" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Ajuster les points obtenus à un tableau</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding-left: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-corrections-changer-points-tableau-form'), 
                        array(
                            'soumission_id'        => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference'],
                            'tableau_no'           => NULL,
                            'points_obtenus'       => NULL,
                            'points'               => NULL
                        )
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="hspace"></div>

					<label>Ajustement les points obtenus au tableau
                    <span class="badge badge-pill" style="background: #ddd; color: #222; font-size: 0.8em; font-weight: normal; margin-left: 2px">Tableau
						<span id="modal-corrections-changer-points-tableau-no"></span>
					</span>
					</label> :

					<div class="hspace"></div>

					<div class="form-row">
						<div class="col-md-3 mb-2">
							<div class="input-group">
								<input id="modal-corrections-changer-points-obtenus-tableau" name="nouveau_points_obtenus" type="text" class="form-control" style="text-align: right" required>
								<div class="input-group-append">
									<span id="modal-corrections-changer-points-total-tableau" class="input-group-text" style="font-weight: 700"></span>
								</div>
							</div>
						</div>  
					</div>

					<div class="hspace"></div>

					<div id="modal-corrections-changer-points-obtenus-invalide" style="font-size: 0.85em; color: crimson" class="d-none">
						<i class="fa fa-exclamation-circle"></i> Les points obtenus ne peuvent être supérieurs au pointage maximum alloué pour la question.
					</div>

				</form>
      		</div>
      
			<div class="modal-footer">
				<div id="modal-corrections-changer-points-tableau-sauvegarde" class="btn btn-success spinnable">
					<i class="fa fa-save"></i> Ajuster les points
					<i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
				</div>
				<div id="modal-corrections-effacer-ajustement-tableau-sauvegarde" class="btn btn-danger" data-dismiss="modal">
					<i class="fa fa-trash"></i> Effacer l'ajustement</div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: LAISSER UN COMMENTAIRE A UN ETUDIANT (POUR UN TABLEAU)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-laisser-commentaire-tableau" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Laisser un commentaire à l'étudiant pour ce tableau</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-laisser-commentaire-tableau-form'), 
                        array(
                            'soumission_id' => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference'],
                            'tableau_no' => NULL
                        )
					); ?>

					<div class="form-group col-md-12">
                        <textarea name="commentaire" class="form-control" id="modal-laisser-commentaire" rows="5" placeholder="Votre commentaire"></textarea>
						<div class="invalid-feedback">
							Ce champ est requis.
						</div>
					</div>

				</form>
				
      		</div>
      
            <div class="modal-footer">
                <div class="col">
                    <div id="modal-laisser-commentaire-tableau-effacer" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash"></i> Effacer ce commentaire
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-laisser-commentaire-tableau-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save"></i> Sauvegarder
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                </div>
      		</div>

    	</div>
  	</div>
</div>
