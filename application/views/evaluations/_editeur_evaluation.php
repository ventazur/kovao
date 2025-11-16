<?  
// ------------------------------------------------------------------------
//
// EVALUATION : INFORMATION, OPERATIONS, TITRE ET OPTIONS 
// 
// ------------------------------------------------------------------------ ?>

<div id="editeur-evaluation-options" class="editeur-section">

    <a class="anchor" name="evaluation"></a>

    <div class="editeur-section-titre">
        <i class="fa fa-square" style="margin-right: 5px;"></i> 
        Évaluation

        <? if ($evaluation['archive']) : ?>
            archivée
        <? endif; ?>

    </div>

    <div id="editeur-evaluation-contenu" class="editeur-section-contenu">

        <? 
        // ----------------------------------------------------------------
        //
        // VERROUILLAGE
        //
        // (pour les evaluations du groupe seulement)
        //
        // ---------------------------------------------------------------- ?>

        <? if ($evaluation['cadenas']) : ?>

            <div id="cadenas">

                <i class="fa fa-lock fa-lg" style="margin-right: 10px"></i> Cette évaluation est verrouillée.

            </div>

        <? endif; ?>

        <? 
        // ----------------------------------------------------------------
        //
        // MES EVALUATIONS
        //
        // ACTIVER / DESACTIVER / ARCHIVER / DESARCHIVER
        //
        // ---------------------------------------------------------------- ?>

        <? if ( ! $evaluation['public']) : ?>

            <div id="editeur-evaluation-activation" 
                 style="<?= ( ! $evaluation['actif'] && ! $evaluation['public']) ? 'border: 1px solid pink; background: pink' : 'border: 1px solid #fff; background: #fff'; ?>">

                <div class="row no-gutters">
        
                    <div class="col-4 mt-2">

                    <? if ( ! $evaluation['actif']) : ?>
                        <i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette évaluation est
                        <span style="color: crimson; font-weight: bold">DÉSACTIVÉE</span>.<br />
                    <? else : ?>
                        <i class="fa fa-exclamation-circle" style="color: dodgerblue"></i> Cette évaluation est
                        <span style="color: dodgerblue; font-weight: bold">ACTIVÉE</span>.<br />
                    <? endif; ?>

                    </div>

                    <div class="col-8" style="text-align: right">

                        <? if ( ! $evaluation_a_remplir) : ?>

                            <? if ($evaluation['actif']) : ?>

                                <? if ( ! empty($this->semestre_id) && $this->enseignant['semestre_id'] == $this->semestre_id) : ?>
                                    <div id="mettre-en-ligne" class="btn btn-outline-dark spinnable">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 5px" fill="currentColor" class="bi-md bi-broadcast" viewBox="0 0 16 16">
                                            <path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707zm2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 0 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708zm5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708zm2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                                        </svg>
                                        Mettre en ligne
                                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                    </div>
                                <? endif; ?>

                            <? endif ; ?>

                            <?
                            /* ----------------------------------------
                             *
                             * Archiver ou desarchiver une evaluation
                             *
                             * ---------------------------------------- */ ?>

                            <? if ( ! $evaluation['archive']) : ?>

                                <div id="archiver-evaluation" class="btn btn-outline-info spinnable"
                                    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                    <i class="fa fa-archive" style="margin-right: 5px"></i> 
                                    Archiver cette évaluation
                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                </div>

                            <? else : ?>

                                <div id="desarchiver-evaluation" class="btn btn-info spinnable"
                                    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                    <i class="fa fa-archive" style="margin-right: 5px"></i> 
                                    Désarchiver cette évaluation
                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                </div>
                                
                            <? endif; ?>

                            <?
                            /* ----------------------------------------
                             *
                             * Activer ou desactiver une evaluation
                             *
                             * ---------------------------------------- */ ?>

                            <? if ($evaluation['actif']) : ?>

                                <div id="desactiver-evaluation" class="btn btn-outline-danger spinnable"
                                    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                    <i class="fa fa-times-circle" style="margin-right: 5px"></i> 
                                    Désactiver cette évaluation
                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                </div>

                            <? else : ?>

                                <div id="activer-evaluation" class="btn btn-outline-primary spinnable"
                                    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                    <i class="fa fa-check-circle" style="margin-right: 3px;"></i> 
                                    Activer cette évaluation
                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                </div>

                            <? endif; ?>

                        <? endif; // ! $evaluation_a_remplir ?>

                    </div> <? // .col-6 ?>

                </div> <? // .row ?>

            </div> <!-- /.editer-section-sous-section -->

            <div id="mettre-en-ligne-erreur" class="d-none" style="margin-top: -5px; margin-bottom: 25px; background: #FCE4EC; color: crimson; padding: 10px 15px 10px 15px; font-size: 0.9em">
                <div class="row">
                    <div class="col-10">
                        <i class="fa fa-times-circle" style="margin-right: 5px"></i>
                        <span></span>
                    </div>
                    <div class="col-2" style="color: #000; text-align: right; cursor: pointer">✕</div>
                </div>
            </div>

        <? 
        // ----------------------------------------------------------------
        //
        // EVALUATIONS DU GROUPE
        //
        // ARCHIVER / DESARCHIVER
        //
        // ---------------------------------------------------------------- ?>

        <? else : ?>

            <? if (permis('editeur')) : ?>

                <div id="editeur-evaluation-groupe" class="mb-3">

                    <div class="row no-gutters">
            
                        <div class="col-4 mt-2">

                        </div>

                        <div class="col-8" style="text-align: right">

                            <?
                            /* ----------------------------------------
                             *
                             * Archiver ou desarchiver une evaluation
                             *
                             * ---------------------------------------- */ ?>

                            <? if ( ! $evaluation['archive']) : ?>

                                <div id="archiver-evaluation" class="btn btn-outline-secondary spinnable"
                                    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                    <i class="bi bi-archive" style="margin-right: 5px"></i> 
                                    Archiver cette évaluation
                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                </div>

                            <? else : ?>

                                <div id="desarchiver-evaluation" class="btn btn-secondary spinnable"
                                    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                    <i class="bi bi-archive" style="margin-right: 5px"></i> 
                                    Désarchiver cette évaluation
                                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                                </div>
                                
                            <? endif; ?>

                        </div>

                    </div> <!-- .row .no-gutters -->


                </div> <!-- #editeur-evaluation-groupe -->

            <? endif; // if permis editeur ?> 

        <? endif; ?>

        <? 
        // ----------------------------------------------------------------
        //
        // INFORMATION
        //
        // ---------------------------------------------------------------- ?>

        <div class="editeur-section-sous-section-titre" style="margin-top: 0">
            Information
        </div>

        <div class="editeur-section-sous-section">

            <li>Cours : <?= $cours['cours_nom']; ?> (<?= $cours['cours_code']; ?>)</li>

            <? if ($evaluation['public'] || ($evaluation['enseignant_id'] != $this->enseignant_id)) : ?>
                <li>
                    Responsable de cette évaluation : <?=  @$evaluation['enseignant_prenom'] . ' ' . @$evaluation['enseignant_nom']; ?>
                </li>
            <? endif; ?>

            <? if ( ! empty($evaluation['ajout_epoch'])) : ?>
                <li>
                    Date de création : <?= date_french_full($evaluation['ajout_epoch']); ?>
                </li>
            <? endif; ?>

            <li>Évaluation ID : <?= $evaluation_id; ?></li>

            <div class="hspace"></div>

            <li>Nombre de questions : <?= count($questions); ?></li>
            <li>Nombre de questions à répondre par l'étudiant : <?= $nb_questions_reel; ?></li>

            <?  /* Ces options ne sont pas disponibles pour les evaluations du groupe. */ ?>

            <? $requete = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 5)), 0, 5); ?>

            <? if ( ! $evaluation['public']) : ?>

                <li>
                    <a href="<?= base_url() . 'stats/evaluation/' . $evaluation['evaluation_id'] . '/req/' . $requete; ?>">
                        Statistiques de cette évaluation
                        <i class="fa fa-stethoscope" style="margin-left: 3px"></i>
                    </a>
                </li>

                <?
                /* 
                 * Ceci pour eviter que si le compte d'un enseignant est compromis, l'intrus pourrait s'approprier
                 * rapidement toutes les questions et reponses des evaluations.
                 */ 
                ?>

                <li>
                    <a href="<?= base_url() . 'evaluations/sommaire/' . $evaluation['evaluation_id']; ?>" target="_blank">
                        Version sommaire imprimable
                        <i class="fa fa-print" style="margin-left: 3px"></i> 
                    </a>
                </li>

                <div class="hspace"></div>

            <? endif; ?>

        </div> <!-- /.editeur-section-sous-section -->

        <? 
        // --------------------------------------------------
        //
        // TITRE DE L'EVALUATION POUR EDITION
        //
        // -------------------------------------------------- ?>

        <div class="editeur-section-sous-section-titre">
            Titre de l'évaluation
        </div>

        <div class="editeur-section-sous-section" style="padding-bottom: 15px;">

            <p id="titre-evaluation-original" style="margin-top: 2px; padding: 30px 15px 30px 15px; margin-bottom: 0;">
                <?= html_entity_decode($evaluation['evaluation_titre']); ?>
            </p>

            <? if (in_array('modifier', $permissions)) : ?>

                <div class="titre-evaluation-sauvegarder">

                    <div class="btn btn-outline-primary" style="margin-top: 15px;"
                         data-toggle="modal" 
                         data-target="#modal-modifier-titre" 
                         data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                        <i class="fa fa-edit" style="margin-right: 5px"></i> 
                        Modifier le titre de l'évaluation
                    </div>

                </div>

            <? endif; ?>

        </div> <!-- /.editeur-section-sous-section -->

        <? 
        // ----------------------------------------------------------------
        //
        // OPERATIONS
        //
        // ---------------------------------------------------------------- ?>

        <div class="editeur-section-sous-section-titre">
            Opérations sur l'évaluation
        </div>

        <div class="editeur-section-sous-section">

            <div class="row">

                <div class="col">
                    <a class="btn btn-block btn-warning" target="_blank" href="<?= base_url() . 'evaluation/previsualisation/' . $evaluation['evaluation_id']; ?>">
                        <i class="fa fa-eye" style="margin-right: 5px"></i> 
                        Prévisualisation
                    </a>
                </div>

                <? if (in_array('ajouter_question', $permissions)) : ?>

                    <div class="col" style="">
                        <div class="btn  btn-block btn-primary" data-toggle="modal" data-target="#modal-ajout-question">
                            <i class="fa fa-plus-circle" style="margin-right: 5px"></i>
                            Ajouter question
                        </div>
                    </div>

                <? endif; ?>

                <? if (in_array('modifier', $permissions)) : ?>

                    <? if ($evaluation['public'] && in_array('changer_responsable', $permissions)) : ?>

                        <div class="col">
                            <div id="changer-responsable" class="btn btn-block btn-outline-primary" 
                                 data-toggle="modal" 
                                 data-target="#modal-changer-responsable" 
                                 data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                <i class="fa fa-user" style="margin-right: 5px"></i> 
                                Changer responsable
                            </div>
                        </div>

                    <? endif; ?>

                    <div class="col">
                        <div id="dupliquer-evaluation" class="btn btn-block btn-outline-primary" 
                            data-evaluation_id="<?= $evaluation_id; ?>"
                            data-toggle="modal" 
                            data-target="#modal-dupliquer-evaluation">
                            <i class="fa fa-clone" style="margin-right: 5px"></i> 
                            Dupliquer
                        </div>
                    </div>

                    <? if ( ! empty($groupes_copy)) : ?> 
                        <div class="col">
                            <div id="copier-evaluation2" class="btn btn-block btn-outline-primary" 
                                data-cours_id="<?= $evaluation['cours_id']; ?>"
                                data-groupe_id="<?= $this->groupe_id; ?>"
                                data-toggle="modal"
                                data-target="#modal-copier-evaluation2">
                                <i class="fa fa-copy" style="margin-right: 5px"></i> 
                                Copier vers cours
                            </div>
                        </div>
                    <? endif; ?>

                    <? if (in_array('effacer', $permissions)) : ?>
                        <div class="col">
                            <div id="effacer-evaluation" class="btn btn-block btn-outline-danger" 
                                data-evaluation_id="<?= $evaluation['evaluation_id']; ?>" 
                                data-toggle="modal" data-target="#modal-effacer-evaluation">
                                <i class="fa fa-trash" style="color: crimson; margin-right: 5px;"></i> 
                                Effacer
                            </div>
                        </div>
                    <? endif; ?>

                <? endif; // permissions ?> 

            </div> <!-- /.row -->

        </div> <!-- /.editeur-section-sous-section -->

        <? 
        // --------------------------------------------------
        //
        // ORDRE
        //
        // -------------------------------------------------- ?>

        <? if (in_array('modifier', $permissions)) : ?>

            <div class="editeur-section-sous-section-titre">
                Ordre de l'évaluation
            </div>

            <div class="editeur-section-sous-section" style="padding-bottom: 20px;">

                <p style="color: #777; font-size: 0.9em">
                    <i class="fa fa-info-circle" style="margin-right: 5px"></i> 
                    Le nombre dans ce champ sera utilisé pour déterminer l'ordre de présentation des évaluations (décimales permises).
                </p>

                <div class="ordre-evaluation">
                    <div class="form-inline">

                        <label for="ordre-evaluation" style="margin-right: 10px">Ordre de l'évaluation</label>
                        <input name="ordre" type="number" class="form-control col-sm-1" id="ordre-evaluation" value="<?= $evaluation['ordre'] ?: '0'; ?>" />

                        <? if ($this->enseignant['enseignant_id'] == $evaluation['enseignant_id'] || permis('editeur')) : ?>

                            <div id="ordre-evaluation-sauvegarde" class="btn btn-outline-primary" style="margin-left: 10px" data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                                <i class="fa fa-save" style="margin-right: 5px"></i>
                                Sauvegarder l'ordre
                                <i id="ordre-evaluation-sauvegarde-action" class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                            </div>

                        <? endif; ?>

                    </div> <!-- .form-inline -->

                </div> <!-- .ordre-evaluation -->

            </div> <!-- /.editeur-section-sous-section -->

        <? endif; ?>

        <? 
        // --------------------------------------------------
        //
        // OPTIONS
        //
        // -------------------------------------------------- ?>

        <? if (in_array('modifier', $permissions)) : ?>

            <div class="editeur-section-sous-section-titre">
                Options de l'évaluation
            </div>

            <div class="editeur-section-sous-section" style="padding-bottom: 20px">

                <div>
                    <div class="custom-control custom-switch">
                        <input name="questions_aleatoires" id="questions-aleatoires" class="custom-control-input" type="checkbox" <?= $evaluation['questions_aleatoires'] ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="questions-aleatoires">
                            Présenter les questions aléatoirement
                            <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                               data-trigger="hover" 
                               data-toggle="popover" 
                               data-html="true"
                               data-placement="top"
                               data-content="<span>L'ordre des questions sera généré aléatoirement, donc chaque étudiant verra un ordre différent.</span>"></i>
                        </label>
                    </div>

                    <? if ( ! $evaluation['public']) : ?>

                        <? 
                        /* 
                         * Toutes les evaluations exigent desormais aux etudiants d'etre inscrits, a moins d'etre change manuellement dans les parametres de l'evaluation suite a sa mise en ligne (depuis 2023-01-14).
                         *
                        <? if (array_key_exists('inscription_requise', $evaluation)) : ?>
                            <div class="custom-control custom-switch" style="margin-top: 10px">
                                <input name="evaluation_inscription_requise" id="evaluation-inscription-requise" class="custom-control-input" type="checkbox" <?= $evaluation['inscription_requise'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="evaluation-inscription-requise">
                                    Inscription requise des étudiants
                                    <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                                       data-trigger="hover" 
                                       data-toggle="popover" 
                                       data-html="true"
                                       data-placement="top"
                                       data-content="<span>Les étudiants devront s'inscrire et avoir un compte vérifié pour accéder l'évaluation. Cette mesure favorise à dissuader le plagiat et facilite la détection des abus.</span>"></i>
                                </label>
                            </div>
                        <? endif; ?>
                        */ ?>

                        <? if (array_key_exists('temps_en_redaction', $evaluation)) : ?>
                            <? /* pre 2023-01-14: <div id="temps-en-redaction" class="custom-control custom-switch <?= $evaluation['inscription_requise'] ? '' : 'd-none'; ?>" style="margin-top: 10px"> */ ?>
                            <div id="temps-en-redaction" class="custom-control custom-switch" style="margin-top: 10px">
                                <input name="evaluation_temps_en_redaction" id="evaluation-temps-en-redaction" class="custom-control-input" type="checkbox" <?= $evaluation['temps_en_redaction'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="evaluation-temps-en-redaction">
                                    Afficher le temps en rédaction
                                    <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                                       data-trigger="hover" 
                                       data-toggle="popover" 
                                       data-html="true"
                                       data-placement="top"
                                       data-content="<span>Le temps qui s'est écoulé depuis le début de la rédaction est affiché, et se met à jour à chaque seconde. L'inscription requise doit être activée pour que cette option soit fonctionnelle.</span>"></i>
                                </label>
                            </div>
                        <? endif; ?>

                        <? if (array_key_exists('formative', $evaluation)) : ?>
                            <div class="custom-control custom-switch" style="margin-top: 10px">
                                <input name="evaluation_formative" id="evaluation-formative" class="custom-control-input" type="checkbox" <?= $evaluation['formative'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="evaluation-formative">
                                    Évaluation formative
                                    <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                                       data-trigger="hover" 
                                       data-toggle="popover" 
                                       data-html="true"
                                       data-placement="top"
                                       data-content="<span>La correction sera disponible à l'étudiant immédiatement après la soumission de l'évaluation.<br /><strong>Attention</strong> : si l'évaluation comporte des questions à développement, les corrections ne seront pas disponibles tant que ces questions ne seront pas corrigées par l'enseignant.</span>"></i>
                                </label>
                            </div>
                        <? endif; ?>

                    <? endif; ?>

                    <? if ($evaluation['public']) : ?>

                        <div class="custom-control custom-switch" style="margin-top: 10px">
                            <input name="cadenas" id="evaluation-cadenas" class="custom-control-input" type="checkbox" <?= @$evaluation['cadenas'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="evaluation-cadenas">
                                Verrouiller cette évaluation
                                <i class="fa fa-info-circle" style="color: #222; margin-left: 5px"
                                   data-trigger="hover" 
                                   data-toggle="popover" 
                                   data-html="true"
                                   data-placement="top"
                                   data-content="<span>En verrouillant cette évaluation, les autres enseignants ne pourront ajouter ou modifier les questions. Par contre ils pourront continuer à exporter l'évaluation.</span>"></i>
                            </label>
                        </div>

                    <? endif; ?>
                </div>

            </div> <!-- /.editeur-section-sous-section -->

        <? endif; ?>

        <? 
        // --------------------------------------------------
        //
        // DESCRIPTION
        //
        // -------------------------------------------------- ?>

        <div id="evaluation-description" class="editeur-section-sous-section-titre">
            Description de l'évaluation
        </div>

        <div id="editeur-evaluation-description" class="editeur-section-sous-section" style="padding-bottom: 15px">

            <p style="color: #777; font-size: 0.9em">
                <i class="fa fa-info-circle" style="margin-right: 5px"></i> 
                Cette description est seulement visible par l'enseignant.
            </p>

            <div id="editeur-evaluation-description-contenu" class="<?= empty($evaluation['evaluation_desc']) ? 'description-vide' : ''; ?>">
                <? if (empty($evaluation['evaluation_desc'])) : ?>
                    Aucune description
                <? else : ?>
                    <?= _html_out($evaluation['evaluation_desc']); ?>
                <? endif; ?>
            </div>

            <? if (in_array('modifier', $permissions)) : ?>

                <div class="editeur-evaluation-description-sauvegarder">

                    <div class="btn btn-outline-primary" style="margin-top: 15px;"
                         data-toggle="modal" 
                         data-target="#modal-modifier-description">
                        <i class="fa fa-edit" style="margin-right: 5px"></i> 
                        Ajouter | Modifier la description
                    </div>

                </div>

            <? endif; // permission de modifier la description ?>

        </div> <!-- /.editeur-section-sous-section -->


    </div> <!-- .titre-evaluation -->

</div> <!-- /#editeur-evaluation-options -->
