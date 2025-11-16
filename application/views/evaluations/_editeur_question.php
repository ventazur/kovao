<?  
// ------------------------------------------------------------------------
//
// QUESTION
// 
// ------------------------------------------------------------------------ ?>

<div class="editeur-question-section">

    <a class="anchor" name="question<?= $i ?>"></a>

    <?  
    // ----------------------------------------------------------------
    //
    // DONNEES SUR LA QUESTION
    // 
    // ---------------------------------------------------------------- ?>

    <div id="question-<?= $question_id; ?>-data" class="question-data d-none"
        data-question_id="<?= $question_id; ?>"
        data-question_texte="<?= htmlentities(_html_edit($q['question_texte'])); ?>"
        data-question_type="<?= $q['question_type']; ?>"
        data-question_points="<?= $q['question_points']; ?>">
    </div>

    <?  
    // ----------------------------------------------------------------
    //
    // BARRE D'EN-TETE
    // 
    // ---------------------------------------------------------------- ?>

    <div class="editeur-question-section-titre">

        <div class="row">
            
            <div class="col-sm-6">

                Question <?= $i; ?>

                <? if ( ! empty($q['bloc_id'])) : ?>
                    <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px;"></i> 
                    <span style="color: #222; font-family: Lato; font-weight: 400">BLOC</span> 
                    <span class="bloc-label-text-sm"><?= $blocs[$q['bloc_id']]['bloc_label']; ?></span>
                <? endif; ?>

            </div> <!-- /.col -->

            <div class="col-sm-6">

                <? if ( ! $q['sondage']) : ?>

                    <div style="margin-top: -4px; margin-bottom: -4px; text-align: right; font-weight: 700">
                        <? if ( ! empty($q['bloc_id'])) : ?>
                            <div class="btn btn-dark btn-sm" style="color: orange"
                                data-question_id="<?= $question_id; ?>" 
                                data-bloc_id="<?= @$q['bloc_id']; ?>"
                                data-toggle="modal" 
                                data-target="#modal-editeur-question">
                                <?= my_number_format($blocs[$q['bloc_id']]['bloc_points']); ?> point<?= $blocs[$q['bloc_id']]['bloc_points'] > 1 ? 's' : ''; ?>
                            </div>
                        <? else : ?>
                            <div class="btn btn-warning btn-sm"
                                data-question_id="<?= $question_id; ?>" 
                                data-bloc_id="<?= @$q['bloc_id']; ?>"
                                data-toggle="modal" 
                                data-target="#modal-editeur-question">
                                <?= my_number_format($q['question_points']); ?> point<?= $q['question_points'] > 1 ? 's' : ''; ?>
                            </div>
                        <? endif; ?>
                    </div>

                <? else : ?>

                    <div style="text-align: right; font-weight: 300; color: gold">

                        Sondage

                    </div>

                <? endif; ?>

            </div> <!-- /.col -->

        </div> <!-- /.row -->

    </div> <!-- /.editeur-question-section-titre -->

    <? 
    // ----------------------------------------------------------------
    //
    // CONTENU DE LA QUESTION
    //
    // ---------------------------------------------------------------- ?>

    <div class="editeur-question-section-contenu" style="<?= ( ! $q['actif']) ? 'background: #ECEFF1;' : ''; ?>">

        <? 
        // ----------------------------------------------------------------
        //
        // ACTIVATION et DESACTIVATION
        //
        // ---------------------------------------------------------------- ?>

        <div id="editeur-evaluation-activation" style="<?= ( ! $q['actif']) ? 'background: #ffebee;' : ''; ?>">

            <div class="row">

                <div class="col-6 mt-2">

                    <? if ( ! $q['actif']) : ?>
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette question est
                        <span style="color: crimson; font-weight: bold">DÉSACTIVÉE</span>.<br />
                    <? else : ?>
                        <i class="fa fa-exclamation-circle" style="color: dodgerblue"></i> Cette question est
                        <span style="color: dodgerblue; font-weight: bold">ACTIVÉE</span>.<br />
                    <? endif; ?>

                </div> <!-- /.col -->

                <div class="col-6" style="text-align: right">
            
                    <? if (in_array('modifier', $permissions)) : ?>

                        <? if ($q['actif']) : ?>
                            <div class="btn btn-outline-secondary desactiver-question spinnable" data-question_id="<?= $question_id; ?>">
                                <i class="fa fa-times-circle"></i> Désactiver cette question
                                <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                            </div>
                        <? else : ?>
                            <div class="btn btn-outline-primary activer-question spinnable" data-question_id="<?= $question_id; ?>">
                                <i class="fa fa-check-circle"></i> Activer cette question
                                <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                            </div>
                        <? endif; ?>

                    <? endif; ?>

                </div> <? // .col ?>

            </div> <? // .row ?>

        </div> <!-- /.editeur-evaluation-activation -->

        <? 
        // ----------------------------------------------------------------
        //
        // INFORMATION 
        //
        // ---------------------------------------------------------------- ?>

        <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
            Information
        </div>

        <div class="editeur-section-sous-section editeur-question-sous-section">

            <li>Question ID : <?= $q['question_id']; ?></li>
            <li>Type : <?= $this->config->item('questions_types')[$q['question_type']]['desc']; ?></li>

            <? if ($evaluation['public']) : ?>
                <li>
                    Responsable de cette question : <?= $q['enseignant_prenom'] . ' ' . $q['enseignant_nom']; ?>
                </li>
            <? endif; ?>

            <? $requete = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 5)), 0, 5); ?>

            <? if ( ! $evaluation['public']) : ?>
                <li>
                    <a href="<?= base_url() . 'stats/evaluation/' . $evaluation_id . '/question/' . $q['question_id'] . '/req/' . $requete; ?>">
                        Statistiques de cette question
                        <i class="fa fa-stethoscope" style="margin-left: 3px"></i>
                    </a>
                </li>
            <? endif; ?>

        </div> <!-- /.editeur-section-sous-section -->

        <? 
        // ----------------------------------------------------------------
        //
        // TITRE DE LA QUESTION
        //
        // ---------------------------------------------------------------- ?>

        <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
            Question
        </div>

        <div class="editeur-section-sous-section editeur-question-sous-section">

            <span style="padding: 5px 15px 5px 15px; color: #fff; background: #5C6BC0; font-size: 0.9em; font-family: Lato;">
                <?= $this->config->item('questions_types')[$q['question_type']]['desc']; ?>
            </span>

            <p style="margin-top: 2px; margin-bottom: 0; padding: 25px; border: 2px solid #5C6BC0; background: #FFFDE7">
                <?= _html_out($q['question_texte']); ?>
            </p>

            <? if (in_array('modifier', $permissions_question)) : ?>

                <div class="btn btn-outline-primary" style="margin-top: 15px"
                    data-question_id="<?= $question_id; ?>" 
                    data-bloc_id="<?= @$q['bloc_id']; ?>"
                    data-toggle="modal" 
                    data-target="#modal-editeur-question">
                    <i class="fa fa-edit" style="margin-right 5px"></i> 
                    Modifier le type, les points alloués et le titre de la question
                </div>

            <? endif; ?>

        </div> <!-- /.editeur-section-sous-section -->

        <? 
        // ----------------------------------------------------------------
        //
        // OPERATIONS SUR LA QUESTION
        //
        // ---------------------------------------------------------------- ?>

        <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
            Opérations sur la question
        </div>

        <div class="editeur-section-sous-section editeur-question-sous-section">

            <div class="row">

                <? if (in_array('modifier', $permissions_question)) : ?>

                    <? if ( ! empty($blocs) && ! $q['sondage']) : ?>

                        <div class="col">
                            <div class="btn btn-block btn-outline-primary assigner-bloc mt-2 mt-lg-0 ml-0 ml-lg-1"
                                data-question_id="<?= $question_id; ?>" 
                                data-bloc_id="<?= $q['bloc_id'] ?? NULL; ?>"
                                data-toggle="modal" 
                                data-target="#modal-assigner-bloc">
                                <i class="fa fa-cube" style="margin-right: 5px"></i> 
                                Assigner à un bloc
                            </div>
                        </div> <!-- /.col -->

                    <? endif; ?>

                    <div class="col">
                        <div class="dupliquer-question btn btn-block btn-outline-primary" 
                            data-evaluation_id="<?= $evaluation_id; ?>" 
                            data-question_id="<?= $question_id; ?>" 
                            data-toggle="modal" 
                            data-target="#modal-dupliquer-question">
                            <i class="fa fa-clone" style="margin-right: 5px"></i> 
                            Dupliquer
                        </div>
                    </div> <!-- /.col -->

                <? endif; ?>

                <? if ($cours_avec_evaluation) : ?>

                    <div class="col">
                        <div class="copier-question btn btn-block btn-outline-primary" 
                            data-evaluation_id="<?= $evaluation_id; ?>" 
                            data-question_id="<?= $question_id; ?>" 
                            data-toggle="modal" 
                            data-target="#modal-copier-question">
                            <i class="fa fa-copy" style="margin-right: 5px"></i> 
                            Copier vers évaluation
                        </div>
                    </div> <!-- /.col -->

                <? endif; ?>

                <? if ($this->groupe_id != 0) : ?>

                    <? if ($evaluation['public']) : ?>

                        <div class="col">
                            <div class="importer-question btn btn-block btn-outline-primary" 
                                data-evaluation_id="<?= $evaluation_id; ?>" 
                                data-question_id="<?= $question_id; ?>" 
                                data-toggle="modal" 
                                data-target="#modal-importer-question">
                                <i class="fa fa-arrow-circle-left" style="margin-right: 5px"></i> 
                                Importer
                            </div>
                        </div> <!-- /.col -->

                    <? else : ?>

                        <div class="col">
                            <div class="exporter-question btn btn-block btn-outline-primary" 
                                data-evaluation_id="<?= $evaluation_id; ?>" 
                                data-question_id="<?= $question_id; ?>" 
                                data-toggle="modal" 
                                data-target="#modal-exporter-question">
                                <i class="fa fa-arrow-circle-right" style="margin-right: 5px"></i> 
                                Exporter
                            </div>
                        </div> <!-- /.col -->
            
                    <? endif; ?>

                <? endif; ?>

                <? if (in_array('effacer', $permissions_question)) : ?>

                    <div class="col">
                        <div class="btn btn-block btn-outline-danger" 
                             data-question_id="<?= $question_id; ?>" 
                             data-toggle="modal" 
                             data-target="#modal-effacer-question">
                            <i class="fa fa-trash" style="margin-right: 5px"></i> 
                            Effacer
                        </div>
                    </div> <!-- /.col -->

                <? endif; ?>

            </div> <? // .row ?>

        </div> <!-- /.editeur-section-sous-section -->

        <? 
        // ----------------------------------------------------------------
        //
        // ORDRE
        //
        // ---------------------------------------------------------------- ?>

        <? if (in_array('modifier', $permissions_question)) : ?>

            <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
                Ordre de la question
            </div>

            <div class="editeur-section-sous-section editeur-question-sous-section">

                    <p style="color: #777; font-size: 0.9em">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i> 
                        Le nombre dans ce champ sera utilisé pour déterminer l'ordre d'apparition des questions (décimales permises).
                    </p>

                    <div class="ordre-question">
                        <div class="form-inline">
                            <label for="ordre-question" style="margin-right: 10px">Ordre de la question</label>
                            <input name="ordre" type="number" class="ordre-question-input form-control col-sm-1" value="<?= $q['ordre'] ?: '0'; ?>" />

                                <div class="ordre-question-sauvegarde btn btn-outline-primary" style="margin-left: 10px" data-question_id="<?= $q['question_id']; ?>">
                                    <i class="fa fa-save" style="margin-right: 5px"></i>
                                    Sauvegarder l'ordre
                                    <i class="ordre-question-sauvegarde-action fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                                </div>
                                <span class="d-none" style="margin-left: 15px; color: #999">ex. 1, 2, 3, ..., 1.5, 2.0, 2.5, ...</span>
                        </div>
                    </div> <!-- .ordre-question -->

            </div> <!-- /.editeur-section-sous-section -->

        <? endif; ?>

        <? 
        // ----------------------------------------------------------------
        //
        // OPTIONS
        //
        // ---------------------------------------------------------------- ?>

        <? if (in_array('modifier', $permissions_question)) : ?>

            <? $option_disponible = FALSE; ?>

            <div class="editeur-section-sous-section-titre editeur-question-sous-section-titre">
                Options
            </div>

            <div class="editeur-section-sous-section editeur-question-sous-section">

                <?
                /* ------------------------------------------------------------
                 *
                 * Sondage 
                 *
                 * (!) La question ne doit pas faire partie d'un bloc.
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if (empty($q['bloc_id']) && in_array($q['question_type'], array(1, 2, 4, 5, 6, 7, 10, 11, 12))) : ?>

                    <? $option_disponible = TRUE; ?>

                    <div>
                        <div class="custom-control custom-switch">
                            <input name="question_sondage" id="<?= $q['question_id']; ?>-question-sondage" class="question-sondage custom-control-input" type="checkbox" data-question_id="<?= $q['question_id']; ?>"  <?= @$q['sondage'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="<?= $q['question_id']; ?>-question-sondage">
                                Cette question est un sondage
                                <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                                   data-trigger="hover" 
                                   data-toggle="popover" 
                                   data-html="true"
                                   data-placement="top"
                                   data-content="<span>Cette question ne sera pas corrigée et aucun point n'y sera associé.</span>"></i>
                          </label>
                        </div>
                    </div>

                <? endif; ?>

                <?
                 /* -----------------------------------------------------------
                  *
                  * Reponses aleatoires
                  *
                  * ----------------------------------------------------------- */ ?>

                <? if (in_array($q['question_type'], array(1, 3, 4, 11))) : ?>

                    <? $option_disponible = TRUE; ?>

                    <div>
                        <div class="custom-control custom-switch mt-2">
                            <input name="reponses_aleatoires" id="<?= $q['question_id']; ?>-reponses-aleatoires" class="reponses-aleatoires custom-control-input" type="checkbox" data-question_id="<?= $q['question_id']; ?>"  <?= $q['reponses_aleatoires'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="<?= $q['question_id']; ?>-reponses-aleatoires">
                                Présenter les réponses aléatoirement
                          </label>
                        </div>
                    </div>

                <? endif; ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Activation du selecteur
                 * pour les questions a choix unique seulement (TYPE 1)
                 *
                 * ------------------------------------------------------------ */ ?>

                <? if (in_array($q['question_type'], array(1)) && count($reponses[$question_id]) >= $this->config->item('questions_types')[1]['selecteur_option']) : ?>

                    <? $option_disponible = TRUE; ?>

                    <div>
                        <div class="custom-control custom-switch mt-2">
                            <input name="choisir_secteur" id="<?= $q['question_id']; ?>-selecteur" class="selecteur custom-control-input" type="checkbox" data-question_id="<?= $q['question_id']; ?>"  <?= $q['selecteur'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="<?= $q['question_id']; ?>-selecteur">
                                Activer le sélecteur
                                <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                                   data-trigger="hover" 
                                   data-toggle="popover" 
                                   data-html="true"
                                   data-placement="top"
                                   data-content="<span>Le sélecteur permet d'avoir un grand nombre de choix de réponses sans occuper beaucoup d'espace. <strong>Attention</strong>, les codes de formatage ne pourront apparaître dans le sélecteur.</span>"></i>
                            </label>
                        </div>
                    </div>

                <? endif; ?>

                <?
                /* --------------------------------------------------------------------
                 *
                 * Aucune option disponible
                 *
                 * -------------------------------------------------------------------- */ ?>

                <? if ( ! $option_disponible) : ?> 

                    <span style="font-family: Lato; font-weight: 300">
                        Aucune option disponible
                    </span>

                <? endif; ?>

            </div> <!-- /.editeur-section-sous-section -->

        <? endif; // permission de modifier les options ?>
