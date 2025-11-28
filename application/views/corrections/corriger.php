<?
/* ----------------------------------------------------------------------------
 *
 * Corriger une soumission manuellement
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="corriger" data-soumission_id="<?= $soumission['soumission_id']; ?>">
<div class="container-fluid">

<div class="row">

    <a class="anchor" name="top"></a>

    <?
    /* --------------------------------------------------------------------
     *
     * NAVIGATION
     *
     * -------------------------------------------------------------------- */ ?>
        
    <div id="corriger-navigation" class="col-xl-1 d-none d-xl-block">

        <? if ( ! empty($questions_a_corriger) && count($questions_a_corriger) > 1) : ?>

            <? $this->load->view('corrections/_corriger_navigation'); ?>

        <? endif; // count($questions) > 10 ?>

    </div> <!-- /#evaluation-navigation -->


    <div class="col-sm-12 col-xl-10">

    <?
    /* --------------------------------------------------------------
     *
     * BARRE DE DEFILEMENT HAUT
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! empty($soumission_ref_p) || ! empty($soumission_ref_s)) : ?>

        <div class="defilement defilement-haut">
            <div class="row">

                <div class="col-5">
                    <? if ( ! empty($soumission_ref_p)) : ?>
                        <a href="<?= base_url() . 'corrections/corriger/' . $soumission_ref_p; ?>" class="btn btn-sm btn-outline-primary"
                           style="background: dodgerblue; color: #fff; width: 125px">
                            <i class="fa fa-angle-left" style="margin-right: 8px"></i>
                            précédente
                        </a>
                    <? else : ?>
                        <div class="btn btn-sm" style="color: #1565C0; cursor: default">
                            Barre de défilement
                        </div>
                    <? endif; ?>
                </div>

                <div class="col-2" style="text-align: center">
                    <? if (count($soumission_refs) > 1) : ?>
                        <div class="btn btn-sm" style="color: dodgerblue; background: #fff; padding-left: 15px; padding-right: 15px;">
                            <?= $soumission_ref_clef; ?> / <?= count($soumission_refs); ?>
                        </div>
                    <? endif; ?>
                </div>

                <div class="col-5" style="text-align: right">
                    <? if ( ! empty($soumission_ref_s)) : ?>
                        <a href="<?= base_url() . 'corrections/corriger/' . $soumission_ref_s; ?>" class="btn btn-sm btn-outline-primary"
                           style="background: dodgerblue; color: #fff; width: 125px">
                            suivante
                            <i class="fa fa-angle-right" style="margin-left: 9px"></i>
                        </a>
                    <? endif; ?>
                </div>

            </div> <!-- .row -->
        </div> <!-- .defilement -->

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * TITRE
     *
     * ------------------------------------------------------------------------ */ ?>

    <div class="row">
        <div class="col-8">
            <h3 style="font-weight: 400"><span style="color: crimson">Corriger</span> une évaluation</h3>
        </div>
        <div class="col-4" style="text-align: right">
            <a class="btn btn-sm btn-outline-secondary" href="<?= base_url() . 'consulter/' . $soumission['soumission_reference'] . '/noncorrigee'; ?>" target="_blank">
                voir l'évaluation complète
            </a>
        </div>
    </div>

    <div class="dspace"></div>

    <?
    /* ------------------------------------------------------------------------
     *
     * INFORMATION SUR L'EVALUATION
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="cours">

        <div class="row">

            <div class="col-sm-7">
                <label class="label-cours">Cours :</label>
                <div class="font-weight-bold"><?= $cours['cours_nom'] . ' (' . $cours['cours_code'] . ')'; ?></div>
            </div>

            <div class="col-sm-2">
                <label class="label-cours">Semestre :</label>
                <div class="font-weight-bold"><?= $cours['semestre_nom']; ?></div>
            </div>

            <div class="col-sm-3">
                <label class="label-cours">Remise :</label>
                <div class="font-weight-bold">
                    <?= $soumission['soumission_date']; ?>

                    <? if ($soumission['non_terminee']) : ?>
                        <span data-toggle="tooltip" title="Cette évaluation a été terminée par l'enseignant.">
                            <svg style="margin-left: 3px; margin-right: 3px;" viewBox="0 0 18 18" class="bi-xs bi-slash-circle" fill="crimson" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path fill-rule="evenodd" d="M11.354 4.646a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </span>
                    <? endif; ?>
                </div>
            </div>

            <div class="col-sm-9 mt-3">
                <label class="label-cours">Évaluation :</label>
                <div class="font-weight-bold"><?= $evaluation['evaluation_titre']; ?></div>
		  	</div>

			<div class="col-sm-3 mt-3">
                <label class="label-cours">Enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?> :</label>
                <div class="font-weight-bold"><?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></div>
			</div>

        </div>

    </div> <!-- #cours -->

    <? if (array_key_exists('lab', $soumission) && $soumission['lab']) : ?>

        <? 
            $lab_data = json_decode($soumission['lab_data'], TRUE); 

            $p_cols = 4;

            if (empty($lab_data['lab_partenaire3_nom']))
            {
                $p_cols = 6;
            }
            
            if (empty($lab_data['lab_partenaire2_nom']))
            {
                $p_cols = $p_cols == 4 ? 6 : 12;
            }
        ?>

        <? 
        /* ------------------------------------------------------------------------
         * 
         * IDENTIFICATION LABORATOIRE
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="identification-titre">

            Identification des partenaires de laboratoire

        </div> <!-- #identification-titre -->

        <div id="identification">

            <div class="row mb-2">

                <div class="col">
                    <label class="label-identification">Place :</label> 
                    <span class="font-weight-bold"><?= $lab_data['lab_place']; ?></span>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-<?= $p_cols; ?>">
                    <label class="label-identification">Partenaire 1 :</label>
                    <div class="font-weight-bold"><?= $soumission['prenom_nom']; ?></div>
                </div>

                <? if ( ! empty($lab_data['lab_partenaire2_nom'])) : ?>
                    <div class="col-sm-<?= $p_cols; ?>">
                        <label class="label-identification">Partenaire 2 :</label>
                        <div class="font-weight-bold"><?= $lab_data['lab_partenaire2_nom'] ?: '-'; ?></div>
                    </div>
                <? endif; ?>

                <? if ( ! empty($lab_data['lab_partenaire3_nom'])) : ?>
                    <div class="col-sm-<?= $p_cols; ?>">
                        <label class="label-identification">Partenaire 3 :</label>
                        <div class="font-weight-bold"><?= $lab_data['lab_partenaire3_nom'] ?: '-'; ?></div>
                    </div>
                <? endif; ?>

            </div> <!-- .row -->

        </div> <!-- #identification -->

    <? else : ?>

        <? 
        /* ------------------------------------------------------------------------
         * 
         * IDENTIFICATION
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="identification-titre">

            Identification de l'étudiant

        </div> <!-- #identification-titre -->

        <div id="identification">

            <div class="row">

                <div class="col-sm-9">
                    <label class="label-identification">Prénom et Nom :</label>
                    <div class="font-weight-bold"><?= $soumission['prenom_nom']; ?></div>
                </div>
                <div class="col-sm-3">
                    <label class="label-identification"><?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?> :</label>
                    <div class="font-weight-bold"><?= $soumission['numero_da']; ?></div>
                </div>

            </div> <!-- .row -->

        </div> <!-- #identification -->

    <? endif; ?>

    <?
    /* --------------------------------------------------------------------
     *
     * LABORATOIRE - TABLEAUX
     *
     * -------------------------------------------------------------------- */ ?>

    <? if ($soumission['lab']) : ?>

        <?
            $lab            = TRUE;
            $lab_data       = json_decode($soumission['lab_data'], TRUE);
            $lab_valeurs    = $soumission['lab_valeurs'] ? json_decode($soumission['lab_valeurs'], TRUE) : NULL;
            $lab_points     = $soumission['lab_points'] ? json_decode($soumission['lab_points'], TRUE) : NULL;

            $lab_points_champs   = json_decode($soumission['lab_points_champs'], TRUE);
            $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);
            $lab_vue        = $lab_data['lab_vue'] ?? NULL;

            $data_lab = array(
                'lab' => $lab,
                'lab_data' => $lab_data,
                'lab_valeurs' => $lab_valeurs,
                'lab_points'  => $lab_points,
                'lab_points_champs' => $lab_points_champs,
                'lab_points_tableaux' => $lab_points_tableaux,
                'lab_prefix'  => $lab_data['lab_prefix']
            );

        ?>

        <?
            /* RETIRE le 5 mars 2025
            $lab            = $soumission['lab'];
            $lab_data       = json_decode($soumission['lab_data'], TRUE);
            $lab_valeurs    = json_decode($soumission['lab_valeurs'], TRUE);

            $lab_points          = json_decode($soumission['lab_points'], TRUE);
            $lab_points_champs   = json_decode($soumission['lab_points_champs'], TRUE);
            $lab_points_tableaux = json_decode($soumission['lab_points_tableaux'], TRUE);

            $lab_prefix = $lab_data['lab_prefix'] ?? NULL;
            $lab_vue    = $lab_data['lab_vue'] ?? NULL;

            $partials = array(
                'version_etudiante' => 1,
                'montrer_commentaires' => FALSE,
                'montrer_corrections' => FALSE,
                'lab' => $lab,
                'lab_data' => $lab_data,
                'lab_valeurs' => $lab_valeurs,
                'lab_points' => $lab_points,
                'lab_points_champs' => $lab_points_champs,
                'lab_points_tableaux' => $lab_points_tableaux,
                'lab_prefix' => $lab_prefix
            );
            */
        ?>

        <link href="<?= base_url() . 'assets/css/consulter.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">
		<link href="<?= base_url() . 'assets/css/lab.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

		<? /* Ajout 2025-11-28 pour permettre la correction des evaluations en equipe sans tableau */ ?>
		<? if ( ! empty($lab_vue)) : ?>

			<? if (file_exists(APPPATH . 'views/laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue . '_consulter.php')) : ?>

				<? $this->load->view('laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue . '_consulter', $data_lab); ?>

			<? else : ?>

				<? $this->load->view('laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue, $data_lab); ?>

			<? endif; ?>

		<? endif; ?>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------------
     *
     * QUESTIONS A CORRIGER
     *
     * -------------------------------------------------------------------- */ ?>

    <? if (empty($questions_a_corriger)) : ?>

        <div style="text-align: center; margin-top: 50px; margin-bottom: 50px;">

            <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucune question à corriger pour cette évaluation.

        </div>

    <? else : ?>

        <? foreach($questions_a_corriger as $q) : 

            $i = $q['question_no'];
            $question_id = $q['question_id'];

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

            <div class="corriger-question-image">

                <? if ($images[$question_id]['s3']) : ?>
                    
                    <img src="<?= $this->config->item('s3_url', 'amazon') . 'evaluations/' . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

                <? else : ?>

                    <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

                <? endif; ?>

                <? if ( ! empty($images[$question_id]['doc_caption'])) : ?>

                    <p style="margin-top: 10px"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></p>

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

                    <div class="col-sm-8 font-weight-bold">
                        Question <?= $i; ?>
                    </div>
                    <div class="col-sm-4">
                        <div class="float-right">

                            <span id="points-obtenus-question-<?= $q['question_id']; ?>">
                                <? if ($q['corrigee']) : ?>
                                    <?= my_number_format($q['points_obtenus']) . ' / '; ?>
                                <? endif; ?>
                            </span>

                            <?= my_number_format($q['question_points']); ?> point<?= $q['question_points'] > 1 ? 's' : ''; ?>

                            <?
                            /* --------------------------------------------------------
                             *
                             * Allouer des points manuellement a une question
                             * 
                             * -------------------------------------------------------- */ ?>

                            <a href="#" style="margin-left: 5px"
                                data-toggle="modal" 
                                data-target="#modal-allouer-points-manuel" 
                                data-question_no="<?= $i; ?>"
                                data-question_id="<?= $question_id; ?>"
                                data-question_points="<?= my_number_format($q['question_points']); ?>">

                                <svg viewBox="0 0 16 16" class="bi bi-pencil-square" fill="#aaaaaa" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </a>

                            <?
                            /* --------------------------------------------------------------------
                             *
                             * Commentaire de l'enseignant pour cette question
                             *
                             * -------------------------------------------------------------------- */ ?>

                            <a href="#" style="margin-left: 5px"
                                 data-toggle="modal"
                                 data-target="#modal-laisser-commentaire"
                                 data-question_id="<?= $q['question_id']; ?>"
                                 data-commentaire="<?= array_key_exists($q['question_id'], $commentaires) ? _html_edit($commentaires[$q['question_id']]) : NULL; ?>">

                                <? if (array_key_exists($q['question_id'], $commentaires) && $commentaires[$q['question_id']] != NULL) : ?>

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

                        </div>
                    </div> 

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

            </div> <!-- .corriger-question -->

            <?
            /* --------------------------------------------------------------------
             *
             * Reponse repondue
             *
             * -------------------------------------------------------------------- */ ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Question a developpement (TYPE 2)
                 *
                 * -------------------------------------------------------------------- */ ?> 

                <? if ($q['question_type'] == 2) : ?>

                    <? $this->load->view('corrections/_corriger_question_type_2', $partials); ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Question a repondre par televersement de documents (TYPE 10)
                 *
                 * -------------------------------------------------------------------- */ ?> 

                <? elseif ($q['question_type'] == 10) : ?>

                    <? $this->load->view('corrections/_corriger_question_type_10', $partials); ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Question a developpement court (TYPE 12)
                 *
                 * -------------------------------------------------------------------- */ ?> 

                <? elseif ($q['question_type'] == 12) : ?>

                    <? $this->load->view('corrections/_corriger_question_type_12', $partials); ?>

                <? endif; ?>

            <?
            /* --------------------------------------------------------------------
             *
             * Grille de correction standard
             *
             * -------------------------------------------------------------------- */ ?>

            <div id="grille-standard-question-<?= $q['question_id']; ?>" class="corriger-grille-correction" data-question_id="<?= $q['question_id']; ?>">

                <? 
                    //
                    // Verifier si la grille perso a ete utilisee.
                    //

                    if ( ! array_key_exists('grille', $q)) 
                    {
                        $q['grille'] = FALSE;
                    }
                ?>

                <div class="row">
                    <div class="allouer-points col-sm-12">
                        <div style="text-align: center">
                            <div class="btn-group btn-block">

                                <? if ($q['question_points'] > 0) : ?>

                                    <div class="points btn points-100 <?= ! $q['grille'] && $q['points_obtenus'] == (string) $q['question_points'] && $q['corrigee'] ? 'alloue' : ''; ?>" 
                                         style="padding-top: 15px;" 
                                         data-points="<?= $q['question_points']; ?>">
                                        Réussie
                                        <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin"></i></span>
                                    </div>

                                    <div class="points btn points-90 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.90) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.90; ?>">
                                        <?= my_number_format($q['question_points'] * .90) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(90%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-80 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.80) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.80; ?>">
                                        <?= my_number_format($q['question_points'] * .80) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(80%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-75 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.75) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.75; ?>">
                                        <?= my_number_format($q['question_points'] * .75) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(75%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-70 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.70) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.70; ?>">
                                        <?= my_number_format($q['question_points'] * .70) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(70%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-60 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.60) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.60; ?>">
                                        <?= my_number_format($q['question_points'] * .60) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(60%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>

                                    <div class="points btn points-50 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.50) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.50; ?>">
                                        <?= my_number_format($q['question_points'] * .50) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(50%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-40 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.40) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.40; ?>">
                                        <?= my_number_format($q['question_points'] * .40) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(40%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-30 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.30) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.30; ?>">
                                        <?= my_number_format($q['question_points'] * .30) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(30%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-25 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.25) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.25; ?>">
                                        <?= my_number_format($q['question_points'] * .25) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(25%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-20 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.20) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.20; ?>">
                                        <?= my_number_format($q['question_points'] * .20) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(20%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>
                                    <div class="points btn points-10 <?= ! $q['grille'] && $q['points_obtenus'] == (string) ($q['question_points'] * 0.10) ? 'alloue' : ''; ?>" 
                                         data-points="<?= $q['question_points'] * 0.10; ?>">
                                        <?= my_number_format($q['question_points'] * .10) ?>
                                        <div style="min-width: 40px; text-align: center">
                                            <span class="points-pct" style="text-align: center">(10%)</span>
                                            <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
                                        </div>
                                    </div>

                                    <div class="points btn points-00 <?= ! $q['grille'] && $q['corrigee'] && $q['points_obtenus'] == '0' ? 'alloue' : ''; ?>" 
                                         style="padding-top: 15px;" 
                                         data-points="<? echo '0.00'; ?>">
                                        Erronée
                                        <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin"></i></span>
                                    </div>

                                <? else : ?>

                                    <div class="points btn points-100 <?= ! $q['grille'] && $q['points_obtenus'] == (string) $q['question_points'] && $q['corrigee'] ? 'alloue' : ''; ?>" 
                                         style="padding-top: 15px;" 
                                         data-points="<?= $q['question_points']; ?>">
                                        Réussie
                                        <span class="spinner d-none"><i class="fa fa-circle-o-notch fa-spin"></i></span>
                                    </div>

                                <? endif; ?>

                            </div>
                        </div>
                    </div> <!-- .allouer-points -->

                    <div class="hspace"></div>

                </div> <!-- .row -->

                <?
                /* --------------------------------------------------------------------
                 *
                 * Grille de correction perso
                 *
                 * -------------------------------------------------------------------- */ ?>

                <? if (
                        $q['question_points'] > 0                               && 
                        array_key_exists($q['question_id'], $gc)                &&
                        array_key_exists('pourcentage', $gc[$q['question_id']]) && 
                        $gc[$q['question_id']]['pourcentage'] == 100
                      ) : 
                ?>

                    <?  //
                        // Permet de choisir les elements selectionnes de la grille lors 
                        // d'une precedente correction.
                        //

                        if ($q['grille'])
                        {
                            // gc = grilles de correction (base de donnees)
                            // gd = elements de la grille tel qu'utilise pour la correction (soumission)

                            $gd = unserialize($soumission['grilles_data']);

                            if (array_key_exists($q['question_id'], $gd))
                            {
                                $gd = $gd[$q['question_id']];
                                $gd['elements'] = array_keys_swap($gd['elements'], 'element_id');
                            }
                            else
                            {
                                $gd = array();
                            }
                        }
                    ?>

                        <div id="grille-perso-question-<?= $q['question_id']; ?>" class="grille-correction-perso <?= ! $q['grille'] && $q['corrigee'] ? 'd-none' : ''; ?>">

                        <div class="grille-correction-perso-titre">
                            Grille de correction personnalisée
                        </div>

                        <div class="grille-correction-perso-contenu">

                            <? 
                            $points_elements = 0;

                            foreach($gc[$q['question_id']]['elements'] as $e) : 

                                $alloue = FALSE;

                                // Verifier que ce n'est pas element qui ne se retrouve dans la grille deja enregistree dans la soumission.
                                if ( ! empty($gd) && is_array($gd) && array_key_exists($e['element_id'], $gd['elements']))
                                {
                                    $alloue = ($q['grille'] && $gd['elements'][$e['element_id']]['selectionne'] ? TRUE : FALSE);
                                }

                                if ($alloue)
                                {
                                    if ($e['element_type'] == 1)
                                    {
                                        $points_elements = $points_elements + (number_format($e['element_pourcent']/100 * $q['question_points'], 2));
                                    }
                                    else
                                    {
                                        $points_elements = $points_elements - (number_format($e['element_pourcent']/100 * $q['question_points'], 2));
                                    }
                                }
                            ?>

                            <table class="elements <?= $alloue ? 'alloue' : ''; ?> <?= strip_accents($this->config->item('elements_types')[$e['element_type']]); ?>"
                                   data-points="<?= $e['element_pourcent'] / 100 * $q['question_points']; ?>" 
                                   data-grille_id="<?= $gc[$q['question_id']]['grille_id']; ?>"
                                   data-element_id="<?= $e['element_id']; ?>"
                                   data-element_type="<?= $e['element_type']; ?>">
                                    
                                    <tr class="<?= strip_accents($this->config->item('elements_types')[$e['element_type']]); ?>">
                                        <td style="width: 40px; text-align: center">
                                            <i class="fa fa-square-o <?= $alloue ? 'd-none' : ''; ?>" style="color: #000"></i>
                                            <? if ($e['element_type'] == 1) : ?>
                                                <i class="fa fa-check-square <?= $alloue ? '' : 'd-none'; ?>" style="color: #fff;"></i>
                                            <? else : ?>
                                                <i class="fa fa-times-rectangle-o <?= $alloue ? '' : 'd-none'; ?>" style="color: crimson;"></i>
                                            <? endif; ?>
                                        </td>
                                        <td style="padding-left: 10px">
                                            <? if ($e['element_type'] == 1) : ?>
                                                <?= $e['element_desc']; ?>
                                            <? else : ?>
                                                <i class="fa fa-angle-right" style="color: #000"></i>
                                                <i class="fa fa-angle-right d-none"></i>
                                                <span style="padding-left: 10px;">
                                                    <?= $e['element_desc']; ?>
                                                </span>
                                            <? endif; ?>
                                        </td>
                                        <td style="width: 125px; text-align: right; padding-right: 10px;">
                                            <? if ($e['element_type'] == 1) : ?>
                                                <span style="font-weight: 400"><?= str_replace('.', ',', number_format($e['element_pourcent']/100 * $q['question_points'], 2)); ?> pt</span>
                                            <? else : ?>
                                                <span style="font-weight: 400; color: crimson">-<?= str_replace('.', ',', number_format($e['element_pourcent']/100 * $q['question_points'], 2)); ?> pt</span>
                                            <? endif; ?>
                                        </td>

                                        <td style="width: 80px; text-align: right; padding-right: 10px;">
                                            <? if ($e['element_type'] == 1) : ?>
                                                <span style="padding-left: 10px"><?= '(' . my_number_format($e['element_pourcent']) . '%)'; ?></span>
                                            <? else : ?>
                                                <span style="padding-left: 10px; color: crimson"><?= '(' . my_number_format($e['element_pourcent']) . '%)'; ?></span>
                                            <? endif; ?>
                                        </td>
                                    </tr>

                                </table>

                            <? endforeach; ?>
                            
                            <table class="elements-points-alloues">
                                <tr>
                                    <td style="width: 40px; text-align: center">
                                        <i class="fa fa-square" style="color: #222"></i>
                                    </td>
                                    <td style="padding-left: 10px; font-weight: 600">
                                        Points alloués
                                    </td>
                                    <td style="width: 125px; text-align: right; padding-right: 32px; font-weight: 600;">
                                        <span id="grille-perso-points-obtenus-question-<?= $q['question_id']; ?>" 
                                              data-points="<?= $q['points_obtenus'] ? $q['points_obtenus'] : '0.00'; ?>">
                                            <?= number_format($points_elements, 2, ',', ''); ?>
                                        </span> pt
                                    </td>
                                    <td style="width: 60px;"></td>
                
                                </tr>
                            </table>

                            <div class="hspace"></div>
                        
                            <div class="corriger-modifier-grille btn btn-sm btn-outline-secondary"
                                 data-toggle="modal"
                                 data-target="#modal-corriger-modifier-grille"
                                 data-question_id="<?= $q['question_id']; ?>"
                                 data-anchor="#gc-question-<?= $q['question_id']; ?>">
                                
                                <i class="fa fa-edit" style="margin-right: 5px"></i>
                                Modifier cette grille
                            </div>

                        </div>

                    </div>

                <? endif; ?>

            </div> <!-- .corriger-grille-correction -->

            <?
            /* --------------------------------------------------------------------
             *
             * Commentaires pour l'etudiant
             *
             * -------------------------------------------------------------------- */ ?>
            
            <? if (array_key_exists($q['question_id'], $commentaires) && $commentaires[$q['question_id']] != NULL) : ?>

                <div class="commentaires-pour-etudiant">

                    <div class="commentaires-pour-etudiant-titre">

                        Commentaire de l'enseignant<?= $cours['enseignant_genre'] == 'F' ? 'e' : ''; ?> :
                    </div>

                    <div class="commentaires-pour-etudiant-contenu">

                        <div class="commentaire-texte">
                            <?= _html_out($commentaires[$q['question_id']]); ?>
                        </div>

                    </div>

                </div> <!-- .commentaires-pour-etudiant -->

            <? endif; ?>

        </div> <!-- .corriger-question -->

            <?
            /* --------------------------------------------------------------
             *
             * Defilemenet rapide a la meme question de l'evaluation suivante 
             * ou precedente
             *
             * -------------------------------------------------------------- */ ?>

            <? if ( ! empty($soumission_ref_p) || ! empty($soumission_ref_s)) : ?>
                <div style="margin-top: -25px; margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-6">
                            <? if ( ! empty($soumission_ref_p)) : ?>
                                <a href="<?= base_url() . 'corrections/corriger/' . $soumission_ref_p . '#question-q' . $i; ?>" data-toggle="tooltip" data-title="Même question de l'évaluation précédente">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xs bi-arrow-left" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                                    </svg>
                                </a>
                            <? endif; ?>
                        </div>
                        <div class="col-6" style="text-align: right">
                            <? if ( ! empty($soumission_ref_s)) : ?>
                                <a href="<?= base_url() . 'corrections/corriger/' . $soumission_ref_s . '#question-q' . $i; ?>" data-toggle="tooltip" data-title="Même question de l'évaluation suivante">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xs bi-arrow-right" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                </a>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            <? endif; ?>

        <? endforeach; //questions ?>

    <? endif; // ! empty($questions_a_corriger) ?>

    <?
    /* --------------------------------------------------------------
     *
     * BARRE DE DEFILEMENT BAS
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! empty($soumission_ref_p) || ! empty($soumission_ref_s)) : ?>

        <div class="defilement defilement-bas">
            <div class="row">

                <div class="col-6">
                    <? if ( ! empty($soumission_ref_p)) : ?>
                        <a href="<?= base_url() . 'corrections/corriger/' . $soumission_ref_p; ?>" class="btn btn-sm btn-outline-primary"
                           style="background: dodgerblue; color: #fff; width: 125px">
                            <i class="fa fa-angle-left" style="margin-right: 8px"></i>
                            précédente
                        </a>
                    <? endif; ?>
                </div>

                <div class="col-6" style="text-align: right">
                    <? if ( ! empty($soumission_ref_s)) : ?>
                        <a href="<?= base_url() . 'corrections/corriger/' . $soumission_ref_s; ?>" class="btn btn-sm btn-outline-primary"
                           style="background: dodgerblue; color: #fff; width: 125px">
                            suivante
                            <i class="fa fa-angle-right" style="margin-left: 9px"></i>
                        </a>
                    <? endif; ?>
                </div>

            </div> <!-- .row -->
        </div> <!-- .defilement -->

    <? endif; ?>

    <?
    /* --------------------------------------------------------------
     *
     * SECURITE
     *
     * -------------------------------------------------------------- */ ?>

    <?
    /* --------------------------------------------------------------
     *
     * Registre de l'activite de l'etudiant (VERSION 1)
     *
     * -------------------------------------------------------------- */ ?>

    <? if ( ! empty($activite)) : ?>

        <div class="dspace"></div>

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
                        <th style="width: 200px">Unique ID</th>
                        <th style="width: 200px">Fureteur ID</th>
                        <th>URI</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($activite as $a) : ?>
                        <tr>
                            <td class="mono"><?= date_humanize($a['epoch'], TRUE); ?></td>
                            <td class="mono"><?= @$a['adresse_ip']; ?></td>
                            <td class="mono" style="text-align: center"><?= @$a['etudiant_id']; ?></td>
                            <td class="mono"><?= substr($a['unique_id'], 0, 24); ?></td>
                            <td class="mono">
                                <? if (array_key_exists($a['fureteur_id'], $fureteurs_desc)) : ?>

                                    <span data-toggle="popover" data-placement="top" data-content="<?= $fureteurs_desc[$a['fureteur_id']]; ?>" style="cursor: pointer">
                                        <?= substr(@$a['fureteur_id'], 0, 24); ?>
                                    </span>

                                <? else : ?>

                                    <?= substr(@$a['fureteur_id'], 0, 24); ?>

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

        <div class="dspace"></div>

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

                                    <?= substr(@$a['fureteur_id'], 0, 12); ?>

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

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- /.container-fluid -->
</div> <!-- #corrections -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: ALLOUER UN POINTAGE MANUEL A UNE QUESTION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-allouer-points-manuel" class="modal" tabindex="0" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Allouer des points manuellement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body" style="padding-left: 25px">

				<?= form_open(NULL, 
						array('id' => 'modal-allouer-points-manuel-form'), 
                        array(
                            'soumission_id'        => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference'],
                            'question_id'          => NULL,
                            'question_points'      => NULL
                        )
                    ); ?>

                    <div class="alert alert-danger d-none" role="alert" style="margin: 15px; margin-bottom: 25px">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        <span class="alertmsg"></span>
                    </div>

					<div class="hspace"></div>

					<label>Allouer des points à la
                    <span class="badge badge-pill" style="background: #ddd; color: #222; font-size: 0.8em; font-weight: normal; margin-left: 2px">Question
						<span id="modal-allouer-points-manuel-question-no"></span>
					</span>
					</label> :

					<div class="hspace"></div>

					<div class="form-row">
						<div class="col-md-3 mb-2">
							<div class="input-group">
								<input id="modal-allouer-points-manuel-obtenus" name="points_obtenus" type="text" class="form-control" style="text-align: right" required>
								<div class="input-group-append">
									<span id="modal-allouer-points-manuel-total" class="input-group-text" style="font-weight: 700"></span>
								</div>
							</div>
						</div>  
					</div>

					<div class="hspace"></div>

					<div id="modal-allouer-points-manuel-obtenus-invalide" style="font-size: 0.85em; color: crimson" class="d-none">
						<i class="fa fa-exclamation-circle"></i> Les points obtenus ne peuvent être supérieurs au pointage maximum alloué pour la question.
					</div>

				</form>
      		</div>
      
			<div class="modal-footer">
				<div id="modal-allouer-points-manuel-sauvegarde" class="btn btn-success spinnable">
					<i class="fa fa-save"></i> Allouer les points
					<i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <? /*
                    <div id="modal-allouer-points-manual-effacer" class="btn btn-danger" data-dismiss="modal">
                        <i class="fa fa-trash"></i> Effacer les points
                    </div>
                */ ?>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
            </div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: LIEN VERS MODIFIER GRILLE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-corriger-modifier-grille" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">

    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-edit"></i> Modifier cette grille</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
                        array('id' => 'modal-corriger-modifier-grille-form'), 
                        array(
                            'soumission_id' => $soumission['soumission_id'],
                            'soumission_reference' => $soumission['soumission_reference'],
                            'evaluation_id' => $evaluation['evaluation_id'],
                            'question_id' => NULL,
                            'anchor' => NULL
                        )
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

					 En modifiant cette grille en cours de correction, vous perdrez vos sélections courantes.

					</div>

				</form>
				
      		</div> <!-- .modal-body -->
      
            <div class="modal-footer">

                <div id="modal-corriger-modifier-grille-sauvegarde" class="btn btn-outline-primary spinnable">
                    <i class="fa fa-edit"></i> Modifier cette grille
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>

            </div> <!-- .modal-footer -->

        </div> <!-- .modal-content -->

    </div> <!-- .modal-dialog -->

</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: LAISSER UN COMMENTAIRE A UN ETUDIANT POUR CETTE QUESTION
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
