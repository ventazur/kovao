<? 
/* ============================================================================
 *
 * EVALUATION
 *
 * Version 2 (2020-04-28)
 *
 * ----------------------------------------------------------------------------
 *
 * L'evaluation montree a l'etudiant
 *
 * ============================================================================ */ ?>

<script src="<?= base_url() . 'assets/js/vendors/sha256.min.js'; ?>"></script>

<? if ($lab) : ?>
    <script src="<?= base_url() . 'assets/js/lab.js?' . date('U'); ?>" defer></script>
<? endif; ?>

<?
/* ----------------------------------------------------------------------------
 *
 * Donnees sur l'evaluation
 *
 * ---------------------------------------------------------------------------- */ ?>

<script>
    var verifier_numero_da_status = "<?= $this->config->item('verifier_numero_da') == 1 ? TRUE : FALSE; ?>";
    var pingSetting  = <?= (int) $this->config->item('ping_etudiant_evaluation'); ?>;
    var pingInterval = <?= (int) $this->config->item('ping_etudiant_evaluation_intervalle') * 1000; ?>;
    var inscription_requise = "1";
	var lab = <?= $evaluation['lab'] ?? 0; ?>;
    var en_direct = <?= $en_direct ? 1 : 0; ?>;
    var previsualisation = <?= $previsualisation ? 1 : 0; ?>;
    var utiliser_s3 = <?= $this->config->item('utiliser_s3') ? 1 : 0; ?>;
    var s3_url = "<?= $this->config->item('s3_url', 'amazon'); ?>";
    var documents_path_s = "<?= base_url() . $this->config->item('documents_path_s'); ?>";
    var documents_max = <?= $this->config->item('questions_types')[10]['docs_max']; ?>;
    var documents_filesize_max = <?= $this->config->item('questions_types')[10]['taille_max'] * 1E6; ?>;
    var traces = <?= @$this->traces ? 1 : 0; ?>;
    var app_version = "<?= $this->config->item('app_version'); ?>";
    var documents_mime_types = <?= $documents_mime_types; ?>;
</script>

<div id="evaluation-data" 
    data-etudiant_id="<?= $this->est_etudiant ? $this->etudiant_id : NULL; ?>" 
    data-evaluation_reference="<?= $evaluation_reference ?? 'previsual'; ?>"
    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>"></div>

<? if ($evaluation['lab']) : ?>

<div id="lab-data" data-lab_individuel="<?= json_decode($evaluation['lab_parametres'], TRUE)['individuel'] ?? NULL; ?>"></div>

<? endif; ?>

<?
/* ----------------------------------------------------------------------------
 *
 * Debut de la vue
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="evaluation">
<div class="container-fluid">

<div class="row">

    <a class="anchor" name="top"></a>

    <?
    /* --------------------------------------------------------------------
     *
     * NAVIGATION
     *
     * -------------------------------------------------------------------- */ ?>
        
    <div id="evaluation-navigation" class="col-xl-1 d-none d-xl-block">

        <? if ( ! empty($questions) && count($questions) > 3) : ?>

            <? $this->load->view('evaluation/_evaluation_navigation'); ?>

        <? endif; // count($questions) > 10 ?>

    </div> <!-- /#evaluation-navigation -->

    <?
    /* ------------------------------------------------------------------------
     *
     * EVALUATION
     *
     * ------------------------------------------------------------------------ */ ?>

    <div class="col-sm-12 col-xl-10">

        <div class="row">

            <?
            /* -----------------------------------------------------------------
             *
             * ERREUR DANS L'EVALUATION
             *
             * ----------------------------------------------------------------- */ ?>

            <? if ( ! empty($erreur)) : ?>

                <div class="col-sm-12">

                    <? if (@$erreur['status'] == 'WARNING') : ?>

                        <? $this->load->view('evaluation/_evaluation_avertissement', array('erreur' => $erreur)); ?>

                    <? else : ?>

                        <? $this->load->view('evaluation/_evaluation_erreur', array('erreur' => $erreur)); ?>

                    <? endif; ?>

                </div>

            <? endif; ?>

            <?
            /* ----------------------------------------------------------------
             *
             * INFORMATION SUR LE COURS
             *
             * ---------------------------------------------------------------- */ ?>

            <? if (is_array($cours) && array_key_exists('cours_nom', $cours)) : ?>

                <div class="col-sm-12 mb-3 mt-sm-1">

                    <div id="cours-info">

                        <table class="table table-sm table-borderless" style="margin: 0;">
                            <tr>
                                <td style="width: 90px; border-right: 1px solid inherit">

                                    Cours

                                </td>
                                <td style="padding-left: 12px">

                                    <i class="fa fa-angle-right" style="margin-right: 5px"></i>

                                    <? // Formattage pour les petits ecrans (xs) ?>
                                    <span class="d-none d-sm-inline">
                                        <?= $cours['cours_nom']; ?>
                                    </span>
                                    <? // Formattage pour les autres ecrans ?>
                                    <span class="d-inline d-sm-none">
                                        <?= $cours['cours_nom_court']; ?>
                                    </span>

                                </td>
                            </tr>

                                <? if 
                                    (
                                        is_array($evaluation_details) && 
                                        array_key_exists('enseignant_prenom', $evaluation_details) && 
                                        array_key_exists('enseignant_nom', $evaluation_details) &&
                                        array_key_exists('enseignant_genre', $evaluation_details)
                                    ) : ?>

                                <tr>
                                    <td style="width: 90px; border-right: 1px solid inherit">
                                        Enseignant<?= $evaluation_details['enseignant_genre'] == 'F' ? 'e' : ''; ?>
                                    </td>
                                    <td style="padding-left: 12px">
                                        <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                                        <?= $evaluation_details['enseignant_prenom'] . ' ' . $evaluation_details['enseignant_nom']; ?>
                                    </td>
                                </tr>

                            <? endif; ?>
                        </table>

                    </div>
                </div>

            <? endif; ?>

            <?
             /* ------------------------------------------------------------------------
              *
              * EN DIRECT (NOTICE)
              *
              * ------------------------------------------------------------------------ */ ?>

            <? if ($en_direct) : ?>

                <div class="col-sm-12 mt-3 mb-3">
            
                    <div style="border: 1px solid crimson; padding: 25px; background: #ffebee;">

                    <div class="row">
                        <div class="col-9">
                            <span style="color: crimson; font-weight: 600">
                                <i class="fa fa-exclamation-circle"></i>
                                ÉVALUATION EN DIRECT
                            </span>
                            </br >
                            <span style="color: crimson; font-family: Lato; font-weight: 300; font-size: 0.9em">
                                Vous ne pouvez faire aucune modification.
                                Cette page doit être rafraîchie pour voir les derniers changements.
                            </span>
                        </div>

                        <div class="col-3" style="text-align: right">
                            <a id="endirect-rafraichir" class="btn btn-danger" href="<?= current_url(); ?>" style="margin-top: 5px">
                                Rafraîchir cette page
                                <i class="fa fa-refresh" style="margin-left: 5px;"></i>
                            </a>
                        </div>
                    </div>

                    </div>
                </div>

            <? endif; ?> 

            <?
            /* ------------------------------------------------------------------------
             *
             * EST-CE QUE CETTE EVALUATION A DEJA ETE ENVOYEE ?
             *
             * ------------------------------------------------------------------------ */ ?>

            <? if ($soumission_deja_envoyee) : ?>

                <div class="col-sm-12 mb-3 mt-sm-1">

                    <div class="alert alert-danger mt-3" style="margin-bottom: 0">
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                        Cette évaluation a déjà été envoyée. Vous ne pouvez pas la soumettre de nouveau.
                    </div>

                </div>

            <? endif; ?>

            <?
             /* ------------------------------------------------------------------------
              *
              * TITRE ET POINTAGE
              *
              * ------------------------------------------------------------------------ */ ?>

            <div class="col-sm-10 mt-3 mb-xs-0 mb-sm-3">
                <h4><?= $evaluation['evaluation_titre']; ?></h4>
            </div>

            <div class="col-sm-2 mt-3 mb-xs-0 mb-sm-3">
                <div class="float-sm-right">
                    <? if ($points_evaluation > 0) : ?>
                        <h5> / <?= str_replace('.', ',', $points_evaluation); ?> point<?= $points_evaluation > 1 ? 's' :''; ?></h5>
                    <? endif; ?>
                </div>
            </div>

        </div> <!-- /.row -->

        <div class="hspace"></div>

        <? 
          //
          // Initialiser les champs invisibles
          //

          $hidden = array(
              'etudiant_id'             => $this->est_etudiant ? $this->etudiant_id : NULL,
              'evaluation_id' 	        => $evaluation['evaluation_id'],
              'enseignant_id' 	        => $evaluation['enseignant_id'],
              'evaluation_reference'    => $evaluation_reference,
              'groupe_id'               => $this->groupe_id,
              'semestre_id' 		    => (@$previsualisation ? $enseignant['semestre_id'] : $semestre_id), // permet aux enseignants de tester
              'lab'						=> $lab,
              'lab_vue_html'            => (isset($lab_vue_html) && ! empty($lab_vue_html) ? (htmlspecialchars($lab_vue_html, ENT_QUOTES, 'UTF-8')) : NULL),
              'questions' 		        => count($questions),
              'questions_choisies'      => $questions_choisies,
              'variables_choisies'      => $variables_choisies,
              'confirmation1_q' 	    => "J'ai bien vérifié toutes mes réponses.",
              'confirmation1'	 	    => NULL,
              'confirmation2_q' 	    => "Je suis bien informé que seul le premier envoi sera pris en compte.",
              'confirmation2' 	        => NULL,
              'session_id'              => $session_id,
              'soumission_debut_epoch'  => $soumission_debut_epoch,
              'previsualisation'        => (@$previsualisation ? 1 : 0), // permet aux enseignants de tester
              'temps_ecoule'            => NULL,
          ); 

            //
            // Initialiser les champs invisibles des questions pour la soumission
            //

            foreach($questions as $q) 
            {
                $hidden['question_' . $q['question_id']] = NULL;
            }
        ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * FORMULAIRE D'EVALUATION
         *
         * ------------------------------------------------------------------------ */ ?>

        <? if ( ! $en_direct) : ?>

            <?= form_open(base_url() . 'evaluation/soumission', array('id' => 'soumission-form'), $hidden); ?>

        <? endif; ?>

        <?
         /* ------------------------------------------------------------------------
          *
          * IDENTIFICATION
          *
          * ------------------------------------------------------------------------ */ ?>

        <? if ($lab) : ?>

            <? $this->load->view('evaluation/_evaluation_identification_lab'); ?>

        <? else : ?>

            <? $this->load->view('evaluation/_evaluation_identification'); ?>

        <? endif; ?>

        <?
          /* ------------------------------------------------------------------------
           *
           * INSTRUCTIONS
           *
           * ------------------------------------------------------------------------ */ ?>

        <? if ( ! empty($evaluation['instructions'])) : ?>

            <div class="tspace"></div>

            <div id="instructions" style="margin-top: 10px; margin-bottom: -10px">

                <?= _html_out($evaluation['instructions']); ?>

            </div>

        <? endif; ?>

        <div class="dspace"></div>

        <?
           /* ------------------------------------------------------------------------
            *
            * DUREE DE L'EXAMEN
            *
            * ------------------------------------------------------------------------ */ ?>

        <? if ((is_array($rel_evaluation) && $rel_evaluation['inscription_requise']) && ($this->est_etudiant || $this->est_enseignant)) : ?>

            <? if ($evaluation['temps_en_redaction'] || ( ! empty($evaluation['temps_limite']) && $evaluation['temps_limite'] > 0)) : ?>

                <div id="affichage-duree" class="row no-gutters" style="background: #E3F2FD">

                    <? 
                    /* --------------------------------------------------------
                     *
                     * Temps ecoule
                     *
                     * -------------------------------------------------------- */ ?>

                    <div class="col" style="text-align: left">

                        <svg viewBox="0 0 18 18" class="bi bi-clock-history" fill="dodgerblue" xmlns="http://www.w3.org/2000/svg" style="margin-right: 3px">
                          <path fill-rule="evenodd" d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                          <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                          <path fill-rule="evenodd" d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>

                        Temps écoulé :

                        <span id="duree-evaluation"
                              data-debut_epoch="<?= $soumission_debut_epoch; ?>"
                              data-maintenant_epoch="<?= $this->now_epoch; ?>"
                              data-duree_actuelle="<?= $this->now_epoch - $soumission_debut_epoch; ?>">
                            <? //  calculer_duree($this->now_epoch, $soumission_debut_epoch); ?>
						</span>

						<span id="duree-evaluation-minutes"></span>

                    </div> <!-- .col -->

                    <? 
                    /* --------------------------------------------------------
                     *
                     * Temps limite
                     *
                     * -------------------------------------------------------- */ ?>

                    <? if ( ! empty($evaluation['temps_limite']) && $evaluation['temps_limite'] > 0) : ?>

                        <? 
                            //
                            // Convertir les decimaux en secondes
                            //
                     
                            $temps_limite = floor($evaluation['temps_limite']);
                        ?>

                        <div class="col" style="text-align: center">

                            <svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" fill="crimson" class="bi bi-clock-fill" style="margin-right: 3px">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            </svg>

                            <span style="color: crimson">Temps limite</span> : 
							<span id="evaluation-temps-limite" data-temps_limite="<?= $temps_limite; ?>" style="font-weight: 400;">
                                <?= $temps_limite; ?> min<?= $temps_limite > 1 ? 's' : ''; ?>
							</span>
							<span id="evaluation-temps-limite-date">
								(fin à <?= hour_humanize($soumission_debut_epoch + ($temps_limite * 60)); ?>:00)
							</span>

                        </div> <!-- .col -->

                    <? endif; ?>

                    <? 
                    /* --------------------------------------------------------
                     *
                     * Date & Heure
                     *
                     * -------------------------------------------------------- */ ?>

                    <div class="col" style="text-align: right">

                        <svg viewBox="0 0 18 18" class="bi bi-clock" fill="dodgerblue" xmlns="http://www.w3.org/2000/svg" style="margin-right: 3px">
                          <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm8-7A8 8 0 1 1 0 8a8 8 0 0 1 16 0z"/>
                          <path fill-rule="evenodd" d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>

                        Date & Heure : 

                        <span id="heure-evaluation"
                              data-maintenant_epoch="<?= $this->now_epoch; ?>">
                            <? //  date_humanize($this->now_epoch, TRUE); ?>
                        </span>

                    </div> <!-- .col -->

                </div>

            <? endif; ?>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * Pour les laboratoires, verifier que l'on peut montrer toute l'evaluation.
         * Ceci doit etre apres que l'etudiant ait confirme ses partenaires de laboratoire.
         *
         * ------------------------------------------------------------------------ */ ?>

        <? $montrer_evaluation_toute = TRUE; ?>

        <? if ($lab) : ?>

            <? $lab_partenaires_confirmes = $traces['lab_partenaires_confirmes'] ?? FALSE; ?>

            <? if ( ! $lab_partenaires_confirmes) : ?>

                <? $montrer_evaluation_toute = FALSE; ?>

            <? endif; ?>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * TABLEAUX
         *
         * ------------------------------------------------------------------------ */ ?>

        <? if ($lab && $montrer_evaluation_toute) : ?>

            <div id="tableaux" class="evaluation-section-titre">

                <div class="row">

					<div class="col">
                        Tableaux des mesures et des résultats

                        <? if ($this->est_enseignant) : ?>
							<i id="toggle-tags" class="bi bi-tags" style="margin-left: 7px; cursor: pointer"></i>
                        <? endif; ?>
                    </div>

                    <div class="col text-right">
                        <?
                        /* --------------------------------------------------------
                         *
                         * Precorrection
                         *
                         * ------------------------------------------------------- */ ?>

                        <? if (isset($lab_parametres) && array_key_exists('precorrection', $lab_parametres) && $lab_parametres['precorrection']) : ?>

                            <? 
                                $precorrections_arr = precorrections_penalite(
                                    $traces['precorrections']['compte'] ?? 0, $lab_parametres['precorrection_essais'], $lab_parametres['precorrection_penalite']
                                );
                            ?>

                                <div id="precorrection-action" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" 
                                title="<?= '-' . str_replace('.', ',', $lab_parametres['precorrection_penalite']) . '% par précorrection lorsque le compteur tombe à zéro (0)'; ?>"
                                style="margin-right: 10px; font-size: 0.75em; font-weight: 300; padding-top: 2px; padding-bottom: 2px;">
                                PRÉCORRECTION

                                <? if ($lab_parametres['precorrection_penalite']) : ?>
                                    <span style="border: 1px solid #fff; border-radius: 10px; padding: 0 4px 0 4px; margin-left: 5px" id="precorrections-count">
                                        <?= $precorrections_arr['penalite_str']; ?>
                                    </span>
                                <? endif; ?>

                                <i class="fa fa-circle-o-notch spinner fa-spin d-none" style="margin-left: 5px"></i>
                                <i class="fa fa-check-circle termine" style="display: none; margin-left: 5px"></i>
                            </div>

                            <?
                            /* --------------------------------------------------------
                             *
                             * Precorrections a zero
                             *
                             * ------------------------------------------------------- */ ?>
                            <? if ($this->est_enseignant) : ?>
                                <div id="precorrections-reset" class="btn btn-sm"
                                    style="margin-right: 10px; font-size: 0.9em; padding: 0 3px 0 3px; background: #aaa">
                                    <i class="bi bi-arrow-counterclockwise" style="color: crimson"></i>
                                    <i class="fa fa-circle-o-notch spinner fa-spin d-none" style="margin-left: 5px"></i>
                                </div> 
                            <? endif; ?>

                        <? endif; ?>

                    </div> <!-- .col -->

                </div> <!-- .row -->

            </div> <!-- #tableaux -->

            <? if ( ! empty($lab_vue)) : ?>

                <? $this->load->view('laboratoire/' . $this->groupe['sous_domaine'] . '/' . $lab_vue); ?>

            <? endif; ?>

            <?      
            /* ------------------------------------------------------------------------
             *
             * DISCUSSION
             *
             * ------------------------------------------------------------------------ */ ?>

            <? if ( ! empty($questions)) :  ?>

                <div id="discussion" class="evaluation-section-titre">
                    Discussion
                </div>

            <? endif; ?>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * QUESTIONS
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="evaluation-questions" class="<?= ! $montrer_evaluation_toute ? 'd-none' : ''; ?>">

            <? 
            $i = 0; 

            foreach($questions as $q) : 

                $i++; 
                $question_id   = $q['question_id'];
                $question_type = $q['question_type'];

                $partial = array(
                    'i'             => $i,
                    'q'             => $q,
                    'question_id'   => $question_id
                );

                // Verifier que la vue existe

                if ( ! file_exists(VIEWPATH . 'evaluation/_evaluation_question_type_' . $question_type . '.php'))
                    continue;
            ?>

            <? 
            /* --------------------------------------------------------------------
             * 
             * IMAGE
             *
             * -------------------------------------------------------------------- */ ?>

            <? if (array_key_exists($question_id, $images)) : ?> 

                <div class="image-box<?= ($i == 1) ? '-question-1' : ''; ?>">

                    <? $this->load->view('evaluation/_evaluation_image', $partial); ?>

                </div>

            <? endif; ?>

            <? 
             /* --------------------------------------------------------------------
              * 
              * QUESTIONS
              *
              * -------------------------------------------------------------------- */ ?>

            <a class="anchor" name="q<?= $i; ?>"></a>

            <div class="question">

            <? 
              /* ----------------------------------------------------------------
               * 
               * Montrer le titre de la question
               *
               * ---------------------------------------------------------------- */ ?>

                <div class="question-titre">

                    <div class="row no-gutters">
                        <div class="col-8">
                            <div class="question-no">
                                Question <?= $i; ?>
                                <? if ($en_direct || ($previsualisation && ! @$previsualisation_etudiante)) : ?>
                                    <span class="badge badge-pill d-none d-sm-inline" style="background: #ddd; color: #888; font-size: 0.8em; font-weight: normal; margin-left: 10px">Question ID : <?= $question_id; ?></span>
                                <? endif;?>
                            </div>
                        </div>
                        <div class="col-4">
                            <? if ( ! $q['sondage']) : ?>
                                <div class="question-points float-right"><?= my_number_format($q['question_points']); ?> point<?= $q['question_points'] > 1 ? 's' : ''; ?></div>
                            <? endif; ?>
                        </div>
                    </div>

                </div> <!-- /.question-titre -->

                <div class="question-texte">

                    <div style="padding: 10px;">
                        <?= _html_out($q['question_texte']); ?>
                    </div>

                </div> 

                <? 
                   /* ----------------------------------------------------------------
                    * 
                    * Montrer les reponses de la question
                    *
                    * ----------------------------------------------------------------- */ ?>

                    <? $this->load->view('evaluation/_evaluation_question_type_' . $question_type, $partial); ?>

            </div> <!-- /.question -->

            <? endforeach; ?>

        </div> <!-- #evaluation-questions -->

        <?
        /* -------------------------------------------------------- 
         *
         * Soumission de l'evaluation
         *
         * -------------------------------------------------------- */ ?>

        <? if ( ! $montrer_evaluation_toute) : ?>

            <? // Enlever un spacing d'extra lorsque les actions de soumission ne sont pas montrees. ?>
            <div style="margin-bottom: -30px"></div>

        <? endif; ?>

        <div id="evaluation-soumission" class="<?= ! $montrer_evaluation_toute ? 'd-none' : ''; ?>">

            <div id="evaluation-soumission-titre" class="d-none">

                Veuillez envoyer votre évaluation :

            </div>

            <?
            /* -------------------------------------------------------- 
             *
             * Consigne avant de remettre l'evaluation
             *
             * -------------------------------------------------------- */ ?>

            <div id="evaluation-soumission-consignes">

                <?
                /* -------------------------------------------------------- 
                 *
                 * Demander si l'etudiant a bien verifie ses reponses
                 *
                 * -------------------------------------------------------- */ ?>

                <div class="row no-gutters">

                    <div class="form-check mt-2">
                        <input name="confirmation1" id="confirmation1_q" class="confirmation form-check-input" type="checkbox" required>
                        <label class="form-check-label" for="confirmation1_q" style="margin-left: 5px;">
                            <?= $hidden['confirmation1_q']; ?>
                        </label>
                    </div>

                </div>

                <?
                /* -------------------------------------------------------- 
                 *
                 * Demander la confirmation d'envoi unique seulement aux etudiants non inscrits.
                 *
                 * -------------------------------------------------------- */ ?>

                <? if ( ! $this->logged_in) : ?>

                    <? if (is_array($rel_evaluation) && array_key_exists('inscription_requise', $rel_evaluation) && $rel_evaluation['inscription_requise']) : ?>
                        <input class="d-none" type="checkbox" name="confirmation2" value="on" checked="checked" checked>
                    <? else : ?>
                        <div class="row no-gutters">
                            <div class="form-check mt-3">
                                <input name="confirmation2" id="confirmation2_q" class="confirmation form-check-input" type="checkbox" required>
                                <label class="form-check-label" for="confirmation2_q" style="margin-left: 5px">
                                    <?= $hidden['confirmation2_q']; ?>
                              </label>
                            </div>
                        </div>
                    <? endif; ?>

                    <? if ( ! $this->logged_in && $this->config->item('evaluation_confirmation_courriel')) : ?>
                        <div class="row no-gutters">
                            <div class="col-sm-12 mt-3">
                                <label for="confirmation-courriel">Pour obtenir une confirmation d'envoi par courriel, entrez votre adresse :</label>
                                <input name="confirmation_courriel" type="text" class="form-control col-sm-6 col-xs-12" id="confirmation-courriel" 
                                    placeholder="courriel">
                                <small class="form-text text-muted">
                                    <i class="fa fa-exclamation-circle" style="color: #aaa; margin-top: 7px"></i> 
                                    Ceci est facultatif, une page de confirmation s'affichera.
                                </small>
                            </div>
                        </div>
                    <? endif; ?>

                <? endif; ?>

            </div> <!-- #evaluation-soumission-consignes -->

            <?
            /* -------------------------------------------------------- 
             *
             * Envoyer l'evaluation (soumettre, ou remettre)
             *
             * -------------------------------------------------------- */ ?>

            <div id="evaluation-soumission-action" class="row no-gutters mt-4">

                <?
                /* 
                 * KIT #11 :
                 *
                 * (!) Une évaluation remplie lors d'une prévisualisation lorsqu'il n'y a aucun semestre sélectionné ne peut être envoyéee.
                 *     Il faut donc permettre seulement la prévisualisation.
                 */
                ?>

                <? if ($en_direct || ($previsualisation && empty($enseignant['semestre_id']))) : ?>

                    <button type="submit" class="btn btn-primary" disabled>
                        <? if ($lab) : ?>
                            Envoyer votre laboratoire
                        <? else : ?>
                            Envoyer votre évaluation
                        <? endif; ?>
                    </button>

                <? else : ?>

                    <button id="envoyer-evaluation" type="submit" class="btn btn-primary">
                        <? if ($lab) : ?>
                            Envoyer votre laboratoire
                        <? else : ?>
                            Envoyer votre évaluation
                        <? endif; ?>
                        <i class="fa fa-send" style="margin-left: 7px;"></i>
                        <i id="soumettre-icon" class="fa fa-spin fa-spinner d-none" style="margin-left: 7px"></i>
                    </button>

                <? endif; ?>

            </div> <!-- /#evaluation-soumission-action -->

        </div> <!-- /#evaluation-soumission -->

        </form>

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

            <div class="tspace"></div>

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

            <div class="tspace"></div>

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

    </div> <!-- .col .col-xl-10 -->
    <div class="col-xl-1 d-none d-xl-block"></div>

</div> <!-- .row -->
</div> <!-- .container-fluid -->
</div> <!-- #evaluation -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL : MESSAGE DE L'ENSEIGNANT
 *
 * ------------------------------------------------------------------------- */ ?>	
<div id="modal-message-enseignant" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header" style="background: crimson; color: #fff;">

                <h5 class="modal-title" style="font-weight: 300">
                    <svg style="margin-top: -2px; margin-right: 10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                      <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                    </svg>
                    Message de l'enseignant<?= $evaluation['enseignant_genre'] == 'F' ? 'e' : ''; ?>
                </h5>

            </div> <!-- .modal-header -->

            <div class="modal-body">

                <div style="padding: 20px;">
                    <span id="message-enseignant"></span> 
                </div>

            </div> <!-- .modal-body -->

            <div class="modal-footer">

                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fermer</button>

            </div> <!-- .modal-footer -->

        </div> <!-- .modal-content -->
    </div> <!-- .modal-dialog -->
</div> <!-- .modal -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: INFORMATION SUR LA NOTATION SCIENTIFIQUE
 *
 * ------------------------------------------------------------------------- */ ?>	
<div id="modal-info-notation-scientifique" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-info-circle" style="margin-right: 10px"></i>Notation scientifique</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> <!-- .modal-header -->

            <div class="modal-body">
                <div style="padding: 10px 10px 0 10px">
                    <div>Vous pouvez répondre avec la notation scientifique de ces quelques façons :</div>
                    <div class="space"></div>
                    <table style="width: 100%; margin: 0">
                        <tr>
                            <td>
                                <div style="margin-left: 22px;"><strong>5,12×10<sup>-7</sup></strong> peut s'écrire :</div>
                                <div class="space"></div>
                                <ul>
                                    <li>5,12x10^-7</li>
                                    <li>5,12E-7 <span style="margin-left: 10px">(E = ×10)</span></li>
                                    <li>5,12e-7 <span style="margin-left: 10px">(e = ×10)</span></li>
                                </ul>
                            </td>
                            <td>
                                <div style="margin-left: 22px;"><strong>6,97×10<sup>3</sup></strong> peut s'écrire :</div>
                                <div class="space"></div>
                                <ul>
                                    <li>6,97x10^3</li>
                                    <li>6,97E3 <span style="margin-left: 10px">(E = ×10)</span></li>
                                    <li>6,97e3 <span style="margin-left: 10px">(e = ×10)</span></li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <div style="font-size: 0.9em; margin-bottom: 10px;">
                        <i class="fa fa-exclamation-circle"></i>
                        Ne pas improviser une autre façon car l'application pourrait ne pas bien l'interpréter.
                    </div>
                </div>
          </div> <!-- .modal-body -->

          <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
          </div> <!-- .modal-footer -->

        </div> <!-- .modal-content -->
    </div> <!-- .modal-dialog -->
</div> <!-- .modal -->
