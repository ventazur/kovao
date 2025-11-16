<?  
// ----------------------------------------------------------------------------
//
// BARRE DE NAVIGATION DE L'EDITEUR D'EVALUATION
// 
// ---------------------------------------------------------------------------- ?>

<nav id="editeur-sidebar" class="sidebar">

<div class="d-none d-xl-block">

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre de la barre de navigation (desactivee)
     *
     * ------------------------------------------------------------------------ */ ?>

    <? if (1 == 2) : // depuis 2020-06-19 ?>

        <div class="sidebar-titre">
            <div class="row">

                <div class="col">
                    Éditeur
                </div>

                <div class="col float-right" style="text-align: right">
                    <? if ($evaluation['public']) : ?>
                        <a href="<?= base_url() . 'evaluations/groupe'; ?>">
                            <i class="fa fa-arrow-circle-up"></i>
                        </a>
                    <? else : ?>
                        <a href="<?= base_url() . 'evaluations'; ?>">
                            <i class="fa fa-arrow-circle-up"></i>
                        </a>
                    <? endif; ?>
                </div>

            </div> <!-- /.row -->
        </div> <!-- /.sidebar-titre -->

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Items du menu
     *
     * ------------------------------------------------------------------------ */ ?>

    <ul class="nav flex-column">

        <div class="qspace"></div>

        <li class="nav-item">
            <a class="nav-link" href="#top">
                TOP
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link active" href="#evaluation">
                Évaluation

                <? if ( ! $evaluation['public'] && ! $evaluation['actif']) : ?>
                    <i class="fa fa-times" style="color: crimson; padding-left: 5px"></i>
                <? endif; ?>

                <? if ($evaluation['public'] && $evaluation['cadenas']) : ?>
                    <i class="fa fa-lock" style="color: crimson; margin-left: 5px"></i>
                <? elseif ($evaluation['public'] && ! $evaluation['cadenas']) : ?>
                    <i class="fa fa-unlock" style="margin-left: 5px"></i>
                <? endif; ?> 
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link active" target="_blank" href="<?= base_url() . 'evaluation/previsualisation/' . $evaluation['evaluation_id']; ?>">
                Prévisualisation
            </a>
        </li>

        <? if (in_array('ajouter_question', $permissions)) : ?>
            <li class="nav-item">
                <a class="nav-link active" data-toggle="modal" href="#" data-target="#modal-ajout-question">
                    <i class="fa fa-plus-circle" style="margin-right: 3px; color: dodgerblue"></i>
                    <span style="color: dodgerblue; font-weight: 400">Ajouter une question</span>
                </a>
            </li>
        <? endif; ?>

        <li class="nav-item">
            <a class="nav-link active" href="#variables">Variables</a>
        </li>

        <li class="nav-item">
            <a class="nav-link active" href="#blocs">Blocs</a>
        </li>

        <li class="nav-item">
            <a class="nav-link active" href="#instructions">
                Instructions
                <? if ( ! empty($evaluation['instructions'])) : ?>
                    <i class="fa fa-bullhorn" style="color: #F9A825; margin-left: 5px"></i>
                <? endif ;?>
            </a>
        </li>

        <? if ($lab) : ?>

            <li class="nav-item">
                <a class="nav-link active" href="#tableaux-parametres">
                    Tableaux : paramètres
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#tableaux-champs">
                    Tableaux : champs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#tableaux-valeurs">
                    Tableaux : valeurs
                </a>
            </li>

        <? endif; ?>

        <?
        /* --------------------------------------------------------------------
         *
         * Liste des questions
         *
         * -------------------------------------------------------------------- */ ?>

        <? if ( ! empty($questions)) : ?>

            <div style="border-top: 1px solid #ccc; margin-top: 5px; margin-bottom: 5px;"></div>

            <? $i = 0; $points = 0;
                
                foreach ($questions as $q) : 

                    $i++;

                    if ($q['actif'])
                    {
                        $points += $q['question_points'];
                    }
                    
                    $question_id = $q['question_id'];
                    $permissions_question = $permissions_questions[$question_id];
            ?>

                <?
                /* ------------------------------------------------------------
                 *
                 * Les questions
                 *
                 * ------------------------------------------------------------ */ ?>

                <li class="nav-item">
                    <a class="nav-link active" href="#question<?= $i; ?>">

                        Q<?= $i ?> 

                        <span class="ml-1 mr-1"></span>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Question a developpement (Types 2 et 12)
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if (in_array($q['question_type'], array(2, 12))) : ?>

                            <i class="fa fa-comment-o mr-1"></i>

                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Question a televersement de documents
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if ($q['question_type'] == 10) : ?>

                            <i class="fa fa-file-o mr-1"></i>

                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Image
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if (array_key_exists($q['question_id'], $images)) : ?>

                            <i class="fa fa-picture-o mr-1"></i>

                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Points (ou points du bloc) / Bloc
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if ( ! empty($q['bloc_id'])) : ?>

                            <? if ( ! $q['sondage']) : ?>
                                <span class="mr-1">
                                    (<?= my_number_format($blocs[$q['bloc_id']]['bloc_points']); ?> pt<?= $blocs[$q['bloc_id']]['bloc_points'] > 1 ? 's' : ''; ?>)
                                </span>
                            <? endif; ?>

                            <i class="fa fa-angle-right mr-1"></i>

                            <span class="mr-1" style="color: orange; background: #222; padding: 1px 4px 1px 4px; font-size: 0.9em; border-radius: 3px; font-weight: bold;"><?= $blocs[$q['bloc_id']]['bloc_label']; ?></span>

                        <? else : ?>

                            <? if ( ! $q['sondage']) : ?>
                                <span class="mr-1">
                                    (<?= my_number_format($q['question_points']); ?> pt<?= $q['question_points'] > 1 ? 's' : ''; ?>)
                                </span>
                            <? endif; ?>

                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Variables
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if (array_key_exists($q['question_id'], $variables_par_question_id)) : ?>

                            <span>
                                <i class="fa fa-caret-right mr-1"></i>
                                <span class="mr-1" style="color: red; font-weight: bold"><?= $variables_par_question_id[$q['question_id']]; ?></span>
                            </span>

                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Nombre de reponses
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if (in_array( $questions[$question_id]['question_type'], array(1, 3, 4, 11))) : ?>

                            <span class="badge badge-pill mr-1" style="padding-left: 5px; padding-right: 5px; background: #CFD8DC; color: #777" 
                                  data-toggle="tooltip" 
                                  data-placement="top" 
                                  title="<?= count($reponses[$question_id]); ?> réponse<?= count($reponses[$question_id]) > 1 ? 's' :''; ?>">
                                <?= count($reponses[$question_id]); ?>
                            </span>

                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Sondage 
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if ($questions[$question_id]['sondage']) : ?>
                            <i class="fa fa-quote-right mr-1"></i>
                        <? endif; ?>

                        <?
                        /* ----------------------------------------------------
                         *
                         * Active / Inactive
                         *
                         * ---------------------------------------------------- */ ?>

                        <? if ( ! $q['actif']) : ?>
                            <i class="fa fa-times mr-1" style="color: crimson;"></i>
                        <? endif; ?>

                        <? if (
                                $evaluation['public'] && 
                                $evaluation['enseignant_id'] != $this->enseignant['enseignant_id'] &&
                                in_array('modifier', $permissions_question) &&
                                ! permis('editeur')
                              ) : 
                        ?>
                            <i class="fa fa-user" style="color: mediumslateblue"></i>
                        <? endif; ?>

                    </a>
                </li>
        
            <? endforeach; ?>

            <?
            /* ----------------------------------------------------------------
             *
             * Total des points de l'evaluation
             *
             * ---------------------------------------------------------------- */ ?>

            <li class="nav-item">
                <div style="border-top: 1px solid #ccc; color: #666; margin-top: 5px; padding: 7px 7px 7px 15px; background-color: #e3e3e3; font-weight: bold">
                    Total : <span style="padding-left: 5px"></span>
                        <?= str_replace('.', ',', $pointage); ?> 
                    points
                    <br />
                    <span style="">(<?= $nb_questions_reel; ?> question<?= $nb_questions_reel > 1 ? 's' :''; ?>)</span>
                </div>
            </li>

        <? endif; ?>

    </ul>

</div> <? // barre de navigation ?>

</nav> <? // /.sidebar ?>
