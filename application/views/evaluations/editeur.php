<?
/* ----------------------------------------------------------------------------
 *
 * EDITEUR D'EVALUATION
 *
 * ---------------------------------------------------------------------------- */ ?>

<link href="<?= base_url() . 'assets/css/editeur.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/editeur.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="editeur-evaluation" class="<?= $evaluation['public'] ? 'evaluation' : 'mon-evaluation'; ?>">

<div class="container-fluid">
<div class="row">

<?  
// ------------------------------------------------------------------------
//
// DONNEES SUR L'EVALUATION
// 
// ------------------------------------------------------------------------ ?>

<div id="evaluation-data" class="d-none" 
    data-evaluation_id="<?= $evaluation_id; ?>" 
    data-evaluation_public="<?= $evaluation['public']; ?>"
    data-cours_id="<?= $cours['cours_id']; ?>" 
    data-groupe_id="<?= $groupe_id; ?>"
    data-evaluation_selectionnee="<?= $evaluation_a_remplir ? 1 : 0; ?>">
>
</div>

<?  
// ------------------------------------------------------------------------
//
// BARRE DE NAVIGATION
// 
// ------------------------------------------------------------------------ ?>

<div class="col-xl-2 d-none d-xl-block">

    <? $this->load->view('evaluations/_editeur_navigation'); ?>

</div> 

<?  
// ------------------------------------------------------------------------
//
// EDITEUR
// 
// ------------------------------------------------------------------------ ?>

<a class="anchor" name="top"></a>

<div id="editeur-evaluation-principal" class="col col-xl-10">

    <?  
    // ------------------------------------------------------------------------
    //
    // AVERTISSEMENT de ne pas utiliser sur un CELLULAIRE
    // 
    // ------------------------------------------------------------------------ ?>

    <div class="d-block d-md-none pas-de-cell">
        <i class="fa fa-exclamation-circle fa-2x"></i>
        <div style="margin-top: 10px"></div>
        Cette page n'est pas optimisée pour l'affichage sur un téléphone cellulaire ou une tablette.
    </div>

    <?  
    // ------------------------------------------------------------------------
    //
    // EN-TETES DE L'EDITEUR
    // 
    // ------------------------------------------------------------------------ ?>

	<div id="editeur-entete" class="row no-gutters">
        <div class="col-md-6">
            <div id="editeur-entete-titre">
                <i class="fa fa-edit" style="color: lightsteelblue; margin-right: 5px;"></i> 
                Éditeur d'évaluation
            </div>
		</div>
        <div class="col-md-6">
            <div id="editeur-entete-points" class="float-md-right">
				<span class="font-weight-bold"><?= my_number_format($pointage); ?></span> 
				<span class="font-weight-light">point<?= $pointage > 1 ? 's' : ''; ?> / évaluation</span>
			</div>
		</div>
    </div>

    <?  
    // ------------------------------------------------------------------------
    //
    // AVERTISSEMENT d'evaluation selectionnee pour les etudiants
    // 
    // ------------------------------------------------------------------------ ?>

    <? if ($evaluation_a_remplir) : ?>

        <div class="row no-gutters" style="margin-top: 25px; margin-bottom: -10px">
            <div class="col-md-12">
                <div class="alert alert-danger" style="background: crimson; border-color: crimson; color: #fff; font-family: Lato; border-radius: 0">
                    <i class="fa fa-exclamation-circle" style="color: #fff"></i>
                    <strong>NE FAITES AUCUN CHANGEMENT</strong>
                    <div class="hspace"></div>
                    <span style="font-weight: 300">

                        Cette évaluation est présentement sélectionnée pour être remplie par vos étudiants.<br />
                        Si vous voulez faire des changements au pointage, au type ou au nombre de questions, ou vous ajoutez ou enlevez des réponses, il est impératif de terminer l'évaluation sur la page d'accueil.
                        Pour tous les autres changements, il n'est généralement pas nécessaire de la terminer.<br />

                        <div class="hspace"></div>

                        <span style="font-weight: 400">Conséquences</span> :

                        <div class="hspace"></div>

                        <li> 
                            Si vous faites des changements sans la terminer, les étudiants ayant déjà chargé l'évaluation ne verront pas la dernière version de votre évaluation, et pourraient recevoir une correction inappropriée.<br />
                        </li>
                        <li>
                            Par contre, en la terminant (et même si vous la sélectionnez de nouveau), les étudiants l'ayant déjà chargée ne pourront plus l'envoyer et devront la recommencer car la référence de l'évaluation aura changé.</span>
                        </li>

                    </span>
                </div>
            </div>
        </div>

    <? endif; ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // TITRE DE L'EVALUATION
    // 
    // ------------------------------------------------------------------------ ?>

    <div id="evaluation-gros-titre<?= $evaluation['public'] ? '-groupe' : ''; ?>">

        <? if ($evaluation['lab']) : ?>
            <span style="background: #444; color: #fff; padding: 2px 7px 2px 7px; font-size: 0.65em; font-weight: 300; font-family: Lato; border-radius: 3px; margin-right: 5px">LAB</span>
        <? endif; ?>

        <?= html_entity_decode($evaluation['evaluation_titre']); ?>

        <svg style="margin-top: -3px; margin-left: 7px; color: #0D47A1; cursor: pointer" 
             data-toggle="modal" 
             data-target="#modal-modifier-titre" 
             data-evaluation_id="<?= $evaluation['evaluation_id']; ?>"
             xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
        </svg>

        <? if ( ! empty($evaluation['evaluation_desc'])) : ?>

            <div style="font-size: 0.6em; margin-top: 5px;">

                <?= _html_edit(strip_tags($evaluation['evaluation_desc'])); ?>

            </div>

        <? endif; ?>
    </div>

    <?  
    // ------------------------------------------------------------------------
    //
    // IMPORTATION & EXPORTATION
    // 
    // ------------------------------------------------------------------------ ?>

    <? if ($this->groupe_id != 0) : ?>

        <? if ($evaluation['public']) : ?>

            <? $this->load->view('evaluations/_editeur_importer'); ?>

        <? else : ?>

            <? $this->load->view('evaluations/_editeur_exporter'); ?>

        <? endif; ?>

    <? endif;?>

    <form>

    <?  
    // ------------------------------------------------------------------------
    //
    // EVALUATION : INFORMATION ET OPTIONS 
    // 
    // ------------------------------------------------------------------------ ?>

    <? $this->load->view('evaluations/_editeur_evaluation'); ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // VARIABLES
    // 
    // ------------------------------------------------------------------------ ?>
    
    <? $this->load->view('evaluations/_editeur_variables'); ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // BLOCS
    // 
    // ------------------------------------------------------------------------ ?>

    <? $this->load->view('evaluations/_editeur_blocs'); ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // INSTRUCTIONS
    // 
    // ------------------------------------------------------------------------ ?>

    <? $this->load->view('evaluations/_editeur_instructions'); ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // TABLEAUX
    // 
    // ------------------------------------------------------------------------ ?>

    <? if ($lab) : ?>

        <? $this->load->view('laboratoires/_editeur_tableaux_parametres'); ?>

        <? // tableaux_points = tableaux_champs ?>
        <? $this->load->view('laboratoires/_editeur_tableaux_points'); ?>

        <? $this->load->view('laboratoires/_editeur_tableaux_valeurs'); ?>

    <? endif; ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // DISCUSSION
    // 
    // ------------------------------------------------------------------------ ?>

    <? if ($lab && ! empty($questions)) : ?>
        
        <? $this->load->view('laboratoires/_editeur_discussion'); ?>

    <? endif; ?>

    <?  
    // ------------------------------------------------------------------------
    //
    // QUESTIONS
    // 
    // ------------------------------------------------------------------------ ?>
    
    <?  
        $i = 0; 

        foreach ($questions as $q) : 

            $i++;

            $question_id = $q['question_id'];

            $permissions_question = $permissions_questions[$question_id];

            // 
            // Ces variables sont necessaires pour les vues partielles (partials).
            //

            $partial['question_id'] = $question_id;
            $partial['q'] = $q;
            $partial['i'] = $i; 
            $partial['permissions_question'] = $permissions_question;
    ?>

        <?  
        // ------------------------------------------------------------------------
        //
        // IMAGE
        // 
        // ------------------------------------------------------------------------ ?>

        <? $this->load->view('evaluations/_editeur_image', $partial); ?>


        <?  
        // ------------------------------------------------------------------------
        //
        // QUESTION
        // 
        // ------------------------------------------------------------------------ ?>

        <? $this->load->view('evaluations/_editeur_question', $partial); ?>


            <?  
            // ----------------------------------------------------------------
            //
            // REPONSES (PAR TYPE DE QUESTION)
            // 
            // ---------------------------------------------------------------- ?>

			<? if ($q['question_type'] == 1 || $q['question_type'] == 4 || $q['question_type'] == 11) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSES A CHOIX UNIQUE    (TYPE 1)
                // REPONSES A CHOIX MULTIPLES (TYPE 4)
                // REPONSES A CHOIX MULTIPLES STRICTE (TYPE 11)
                // 
                // ---------------------------------------------------------------- ?>

                <? $this->load->view('evaluations/_editeur_question_type_1_4_11', $partial); ?>


			<? elseif ($q['question_type'] == 2) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSE A DEVELOPPEMENT (TYPE 2)
                // 
                // ---------------------------------------------------------------- ?>
            
                <? $this->load->view('evaluations/_editeur_question_type_2', $partial); ?>


            <? elseif ($q['question_type'] == 3) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSES A COEFFICIENTS VARIABLES (TYPE 3)
                // 
                // ---------------------------------------------------------------- ?>

                <? $this->load->view('evaluations/_editeur_question_type_3', $partial); ?>


            <? elseif ($q['question_type'] == 5) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSES NUMERIQUES ENTIERES (TYPE 5)
                // 
                // ---------------------------------------------------------------- ?>

                <? if ( ! $q['sondage']) : ?>

                    <? $this->load->view('evaluations/_editeur_question_type_5', $partial); ?>

                <? endif; ?>

            <? elseif ($q['question_type'] == 6) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSES NUMERIQUES (TYPE 6)
                // 
                // ---------------------------------------------------------------- ?>

                <? if ( ! $q['sondage']) : ?>

                    <? $this->load->view('evaluations/_editeur_question_type_6', $partial); ?>

                <? endif; ?>

            <? elseif ($q['question_type'] == 7) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSE LITTERALE COURTE (TYPE 7)
                // 
                // ---------------------------------------------------------------- ?>

                <? if ( ! $q['sondage']) : ?>

                    <? $this->load->view('evaluations/_editeur_question_type_7', $partial); ?>

                <? endif; ?>


            <? elseif ($q['question_type'] == 9) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSE NUMERIQUE PAR EQUATION (TYPE 9)
                // 
                // ---------------------------------------------------------------- ?>

                <? $this->load->view('evaluations/_editeur_question_type_9', $partial); ?>


            <? elseif ($q['question_type'] == 10) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSE PAR TELEVERSEMENT DE DOCUMENTS (TYPE 10)
                // 
                // ---------------------------------------------------------------- ?>

                <? $this->load->view('evaluations/_editeur_question_type_10', $partial); ?>

            <? elseif ($q['question_type'] == 12) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSE COURTE A DEVELOPPEMENT (TYPE 12)
                // 
                // ---------------------------------------------------------------- ?>

                <? $this->load->view('evaluations/_editeur_question_type_12', $partial); ?>

            <? elseif ($q['question_type'] == 13) : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // REPONSE A DEVELOPPEMENT (ChagGPT) (TYPE 13)
                // 
                // ---------------------------------------------------------------- ?>

                <? $this->load->view('evaluations/_editeur_question_type_13', $partial); ?>

            <? else : ?>

                <?  
                // ----------------------------------------------------------------
                //
                // QUESTION TYPE INCONNU
                // 
                // ---------------------------------------------------------------- ?>

                <div style="margin-top: 15px; padding: 20px 25px 20px 25px; border: 1px solid red; background: #FCE4EC;">
                
                    <i class="fa fa-exclamation-circle"></i> Ce type de question est inconnu. 

                </div>

            <? endif; ?>

        </div> <!-- /.editeur-question-section-contenu --> 
        </div> <!-- /.editeur-question-section (dans le parial de _editeur_question) -->
    
    <? endforeach; ?>

    </form>

</div> <? //.col col-xl-10 ?>

</div> <? // .row ?>
</div> <? // .container-fluid ?>

</div> <? // #editeur-evaluation ?>

<?
/* -------------------------------------------------------------------------
 *
 * MODALS
 *
 * ------------------------------------------------------------------------- */ ?>	

<? $this->load->view('evaluations/editeur_modals'); ?>
