<?
/* ------------------------------------------------------------------------
 *
 * BIENVENUE ENSEIGNANTS
 *
 * ------------------------------------------------------------------------ */ ?>

<script src="<?= base_url() . 'assets/js/bienvenue_enseignants.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="bienvenue-enseignants">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <?
    /* ------------------------------------------------------------------------
     *
     * NOTIFICATIONS
     *
     * ------------------------------------------------------------------------ */ ?>

    <? $this->load->view('bienvenue/_bienvenue_enseignants_notifications'); ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * K ALERTES
     *
     * ------------------------------------------------------------------------ */ ?>

    <? $this->load->view('commons/kalertes'); ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * RECHERCHE (ALPHA)
     *
     * ------------------------------------------------------------------------ */ ?>

    <? if ($this->groupe_id != 0) : ?>

        <div class="d-none d-md-block">

            <script src="<?= base_url() . 'assets/js/recherche.js?' . $this->now_epoch; ?>"></script>

            <div id="bienvenue-recherche" class="input-group">
                <input id="recherche-requete" type="text" class="form-control" placeholder="Rechercher" name="requete">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <span class="en-attente"><i class="fa fa-search"></i></span> 
                        <span class="en-precision d-none" style="cursor: pointer; padding-left: 1px; padding-right: 1px;">✕</span>
                        <span class="en-recherche d-none"><i class="fa fa-refresh fa-spin"></i></span>
                    </span>
                </div>
            </div>

            <?
            /* --------------------------------------------------------------------
             *
             * Resultats de la recherche
             *
             * -------------------------------------------------------------------- */ ?>

            <div id="recherche-resultats">

                <div id="recherche-resultats-contenu">
                </div>

            </div>

        </div>

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * LIENS RAPIDES (MATRICE)
     *
     * ------------------------------------------------------------------------ */ ?>

    <div class="row">

    <div class="col mt-2">

        <?
        // ------------------------------------------------------------------------ 
        //
        // RESULTATS
        //
        // ------------------------------------------------------------------------ ?>

        <a class="box-link" href="<?= base_url() . 'resultats'; ?>">
            <div id="box-resultats" class="box-link spinnable h-100">

                <div class="row">
                    <div class="col-xs-12 col-sm-10 mt-2">
                        <h5>
                            Résultats
                            <? if (@$resultats_cumules_session) : ?> 
                                <span class="badge badge-success" style="margin-left: 5px"><?= $resultats_cumules_session; ?></span> 
                            <? else : ?>
                                <span class="badge badge-success" style="margin-left: 5px">0</span> 
                            <? endif; ?>
                        </h5>
                    </div>
                    <div class="col-sm-2 d-none d-sm-block mt-2">
                        <div class="float-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>
                    </div>
                </div>

            </div>
        </a>
    </div> <!-- .col -->

    <div class="col mt-2">

        <?
        // ------------------------------------------------------------------------ 
        //
        // MES ÉVALUATIONS
        //
        // ------------------------------------------------------------------------ ?>

        <a class="box-link" href="<?= base_url() . 'evaluations'; ?>">
            <div id="box-evaluations" class="box-link spinnable h-100">

                <div class="row">
                    <div class="col-xs-12 col-sm-10 mt-2">
                        <h5>
                            Mes évaluations 
                        </h5>
                    </div>
                    <div class="col-sm-2 d-none mt-2 d-sm-block">
                        <div class="float-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>
                    </div>
                </div>

            </div>
        </a>
    </div>

    <div class="w-100"></div>

    <div class="col mt-2">

        <?
        // ------------------------------------------------------------------------ 
        //
        // CORRECTIONS
        //
        // ------------------------------------------------------------------------ ?>

        <a class="box-link" href="<?= base_url() . 'corrections'; ?>">
            <div id="box-corrections" class="box-link spinnable h-100">

                <div class="row">
                    <div class="col-xs-12 col-sm-10 mt-2">
                        <h5>
                            Corrections 
                            <? if (@$corrections_en_attente > 0) : ?> 
                                <span class="badge badge-danger" style="margin-left: 5px"><?= $corrections_en_attente; ?></span> 
                            <? else : ?>
                                <span class="badge badge-warning" style="margin-left: 5px">0</span> 
                            <? endif; ?>
                        </h5>
                    </div>
                    <div class="col-sm-2 d-none d-sm-block mt-2">
                        <div class="float-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>
                    </div>
                </div>

            </div>
        </a>
    </div> <!-- .col -->

    <div class="col mt-2">

        <?
        // ------------------------------------------------------------------------ 
        //
        // ÉVALUATIONS DU GROUPE
        //
        // ------------------------------------------------------------------------ ?>

        <? if ($this->groupe_id != 0) : ?>
            <a class="box-link" href="<?= base_url() . 'evaluations/groupe'; ?>">
                <div id="box-evaluations" class="box-link spinnable">

                    <div class="row">
                        <div class="col-xs-12 col-sm-10 mt-2">
                            <h5>
                                Évaluations du groupe
                            </h5>
                        </div>
                        <div class="col-sm-2 d-none d-sm-block mt-2">
                            <div class="float-right">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </a>
        <? endif; ?>
    </div>

    </div> <? // .row ?>

    <? 
    // ---------------------------------------------------------------------
    // 
    // EVALUATIONS EN VIGUEUR
    //
    // --------------------------------------------------------------------- ?>

    <div class="evaluations-en-vigueur">

        <div class="tspace"></div>

        <div class="bienvenue-table-box">

            <table class="bienvenue-table">
        
                <thead>
                    <tr class="bienvenue-table-titre" style="background: dodgerblue">
                        <th colspan="5">
                            <? if (count($evaluations) > 1) : ?>
                                Évaluations pouvant être remplies par vos étudiants
                            <? else : ?>
                                Évaluation pouvant être remplie par vos étudiants
                            <? endif; ?>
                        </th>
                    </tr>
        
                    <? if ( ! (empty($evaluations) || empty($cours_raw))) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th style="width: 70px; text-align: center;">Cours</th>
                            <th style="width: 100px;">
                                <span style="cursor: default;" data-toggle="tooltip" title="Le lien direct pour accéder l'évaluation.">Lien direct</span>
                            </th>
                            <th>Titre de l'évaluation</th>
                            <th style="min-width: 250px; text-align: right">Actions</th>
                        </tr>

                    <? endif; ?>
                </thead>

                <tbody style="font-size: 0.9em">

                <? if ( ! array_key_exists('semestre_id', $this->enseignant) || ! is_numeric($this->enseignant['semestre_id'])) : ?>

                    <tr>
                        <td style="padding: 15px; font-family: Lato; font-weight: 300; font-size: 1.1em;">
                            <i class="fa fa-exclamation-circle"></i> 
                            Aucun semestre sélectionné dans la <a href="<?= base_url() . 'configuration'; ?>">configuration.
                        </td>
                    </tr>

                <? elseif ($semestres[$this->enseignant['semestre_id']]['semestre_debut_epoch'] > $this->now_epoch || 
                           $semestres[$this->enseignant['semestre_id']]['semestre_fin_epoch'] < $this->now_epoch) : ?>

                    <tr>
                        <td style="padding: 15px; font-family: Lato; font-weight: 300; font-size: 1.1em;">
                            <i class="fa fa-exclamation-circle"></i> 
                            Le semestre sélectionné n'est pas en vigueur.
                        </td>
                    </tr>

                <? elseif (empty($evaluations) || empty($cours_raw)) : ?>

                    <tr>
                        <td style="padding: 15px; font-family: Lato; font-weight: 300; font-size: 1.1em;">
                            <i class="fa fa-exclamation-circle"></i> 
                            Aucune évaluation sélectionnée dans la <a href="<?= base_url() . 'configuration'; ?>">configuration</a> pour le semestre en vigueur.
                        </td>
                    </tr>

                <? else : ?>

                    <? foreach($cours_raw as $cours_id => $c) : ?>

                        <? foreach($evaluations as $evaluation_id => $e) : ?>

                            <? if ($e['cours_id'] != $cours_id) continue; ?>

                            <?
                                $eev = $evaluations_en_vigueur[$evaluation_id];
                                $debut_epoch = $eev['debut_epoch'] ?: 0;
                                $fin_epoch   = $eev['fin_epoch'] ?: 0;

                                $pev = FALSE;
                                    
                                if (($debut_epoch > $this->now_epoch) || ($fin_epoch > $this->now_epoch))
                                {
                                    $pev = TRUE; // pev = planification en vigueur
                                }
                            ?>

                            <?  //
                                // Preparation des filtres
                                //
                                                   
                                if ($eev['inscription_requise']) : 

                                    $cours_id = $eev['cours_id'];

                                    $cours_g = array();

                                    if (array_key_exists($cours_id, $cours_groupes))
                                    {
                                        $cours_g = $cours_groupes[$cours_id];
                                        sort($cours_g);
                                    }

                                    // Verifier si des parametres sont deja presents

                                    $params = FALSE;
                                    $params_desc = NULL;

                                    if ( ! empty($eev['filtre_enseignant']))
                                    {
                                        $params = 'enseignant';
                                        $params_desc = 'ens.';
                                        $params_tooltip = "Filtrer par enseignant";
                                    }

                                    elseif ( ! empty($eev['filtre_enseignant_autorisation']))
                                    {
                                        $params = 'enseignant_autorisation';
                                        $params_desc = 'ens.<sup>＊</sup>';
                                        $params_tooltip = "Filtrer par enseignant";
                                    }

                                    elseif ( ! empty($eev['filtre_cours']))
                                    {
                                        $params = 'cours';
                                        $params_desc = 'cours';
                                        $params_tooltip = "Filtrer par cours";
                                    }

                                    elseif ( ! empty($eev['filtre_cours_autorisation']))
                                    {
                                        $params = 'cours_autorisation';
                                        $params_desc = 'cours<sup>＊</sup>';
                                        $params_tooltip = "Filtrer par cours";
                                    }

                                    elseif ( ! empty($eev['filtre_groupe']))
                                    {
                                        $params = 'groupe_' . $eev['filtre_groupe'];
                                        $params_desc = 'gr ' . $eev['filtre_groupe'];
                                        $params_tooltip = "Filtrer par groupe";
                                    }

                                    elseif ( ! empty($eev['filtre_groupe_autorisation']))
                                    {
                                        $params = 'groupe_autorisation_' . $eev['filtre_groupe_autorisation'];
                                        $params_desc = 'gr ' . $eev['filtre_groupe_autorisation'] . '<sup>＊</sup>';
                                        $params_tooltip = "Filtrer par groupe";
                                    }

                                endif;
                            ?>

                            <tr style="background: <?= ($eev['bloquer'] || $debut_epoch > $this->now_epoch ? '#E8EAF6' : '#F7F7F7;'); ?>">
                                <td style="color: #999; text-align: center">
                                    <span style="cursor: default;" data-toggle="tooltip" title="<?= $c['cours_nom']; ?>">
                                        <?= $c['cours_code_court']; ?>
                                    </span>
                                </td>
                                <td class="mono">
                                    <? if ($evaluations_en_vigueur[$evaluation_id]['evaluation_reference'] != NULL) : ?> 
                                        <a target="_blank" href="<?= base_url() . 'evaluation/' . $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?>"><?= $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?></a>

                                        <span class="copier-presse-papier"
                                              data-toggle="tooltip" 
                                              data-title="Cliquer pour copier le lien direct dans le presse-papier."
                                              data-info="<?= base_url() . 'evaluation/' . $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?>">
                                            <i class="fa fa-clipboard" style="margin-left: 5px"></i> 
                                        </span>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <? 
                                        $e_size = 80; // ellipsize's size
                                        
                                        if ($debut_epoch || $fin_epoch)
                                            $e_size = 50;
                                        elseif ($debut_epoch && $fin_epoch)
                                            $e_size = 30;
                                    ?>
                                    
                                    <? // Indiquer s'il s'agit d'un laboratoire ?>

                                    <? if ($e['lab']) : ?>
                                        <span class="badge badge-pill" style="margin-right: 3px; padding-bottom: 3px; background: #444; color: #fff; font-weight: 300">
                                            LAB
                                        </span> 
                                    <? endif; ?>

                                    <a href="<?= base_url() . 'evaluations/editeur/' . $e['evaluation_id']; ?>" style="text-decoration: none; color: #444;"
                                        data-trigger="hover" 
                                        data-toggle="tooltip" 
                                        data-html="true"
                                        data-placement="top"
                                        data-title="<?= $e['evaluation_titre']; ?>">
                                        <?= ellipsize($e['evaluation_titre'], $e_size); ?>
                                    </a>
                                        
                                    <span style="padding-right: 5px"></span>

                                    <?
                                    /* ----------------------------------------
                                     *
                                     * Inscription requise
                                     *
                                     * ---------------------------------------- */ ?>

                                    <? if ($eev['inscription_requise']) : ?>

                                        <svg data-toggle="tooltip" title="Inscription requise" xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px" fill="#FF6F00" class="bi-xs bi-person-check-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                            <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                        </svg>

                                    <? else : ?>

                                        <svg data-toggle="tooltip" title="Inscription non requise" xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px" width="16" height="16" fill="#aaa" class="bi-xs bi-person-x-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z"/>
                                        </svg>

                                    <? endif; ?>

                                    <span style="padding-right: 5px"></span>

                                    <? if (array_key_exists('bloquer', $eev) && $eev['bloquer']) : ?>
                                        <span class="badge badge-pill" style="padding-bottom: 3px; background: #ffcdd2; color: crimson; font-weight: 300"
                                            data-toggle="tooltip"
                                            data-title="Cette évaluation est cachée est n'est pas accessible.">
                                            BLOQUÉE
                                        </span>
                                    <? endif ;?>
                                    
                                    <? if (array_key_exists('cacher', $eev) && $eev['cacher']) : ?>
                                        <span class="badge badge-pill" style="padding-bottom: 3px; background: dodgerblue; color: #fff; font-weight: 300"
                                            data-toggle="tooltip"
                                            data-title="Cette évaluation est cachée et accessible seulement par le lien direct.">
                                            CACHÉE
                                        </span>
                                    <? endif ;?>

                                    <? if (array_key_exists('formative', $e) && $e['formative']) : ?>
                                        <span class="badge badge-pill" style="padding-bottom: 3px; background: #B0BEC5; color: #000; font-weight: 300"
                                            data-toggle="tooltip"
                                            data-title="Formative">
                                            F
                                        </span>
                                    <? endif ;?>

                                    <? if ($debut_epoch && $debut_epoch > $this->now_epoch) : ?>
                                        <span class="badge badge-pill" style="padding-bottom: 3px; background: #4CAF50; color: #fff; font-weight: 300"
                                            data-toggle="tooltip"
                                            data-title="Début de l'évaluation">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -1px; margin-right: 1px; height: 10px; width: 10px;" fill="currentColor" class="bi-arrow-bar-right" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M6 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L12.293 7.5H6.5A.5.5 0 0 0 6 8zm-2.5 7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5z"/>
                                            </svg>
                                            <?= date('Y-m-d H:i', $eev['debut_epoch']); ?>
                                        </span>
                                    <? endif; ?>

                                    <? if ($fin_epoch && $fin_epoch > $this->now_epoch) : ?>
                                        <span class="badge badge-pill" style="padding-bottom: 3px; background: crimson; color: #fff; font-weight: 300"
                                            data-toggle="tooltip"
                                            data-title="Fin automatique de l'évaluation">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -1px; margin-right: 1px; width: 10px; height: 10px;" fill="currentColor" class="bi-x-circle" viewBox="0 0 16 16">
                                              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg> 
                                            <?= date('Y-m-d H:i', $fin_epoch); ?>
                                        </span>

                                    <? endif; ?>

                                    <? if ( ! empty($eev['temps_limite'])) : ?>
                                        <span class="badge badge-pill" style="padding-bottom: 3px; background: #1565C0; color: #fff; font-weight: 300"
                                            data-toggle="tooltip"
                                            data-title="Temps limite">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -1px; margin-right: 2px; width: 10px; height: 10px" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                              <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                            </svg>
                                                <?= $eev['temps_limite']; ?> min<?= $eev['temps_limite'] > 1 ? 's' : ''; ?>
                                        </span>
                                    <? endif; ?>

                                </td>

                                <td style="border-left: 0; text-align: right;">

                                    <?
                                    /* ----------------------------------------
                                     *
                                     * Envoyer un message aux etudiants en redaction
                                     *
                                     * ---------------------------------------- */ ?>

                                    <? if ($this->config->item('ping_etudiant_evaluation') && $etudiants_inscrits_redaction[$evaluations_en_vigueur[$evaluation_id]['evaluation_reference']] > 0) : ?>
                                        <span data-toggle="tooltip" title="Communiquer">
                                            <div class="btn btn-sm btn-outline-primary chat-evaluation"  
                                                data-toggle="modal" 
                                                data-target="#modal-communiquer-evaluation" 
                                                data-semestre_id="<?= $this->semestre_id; ?>"
                                                data-cours_id="<?= $cours_id; ?>"
                                                data-evaluation_id="<?= $evaluation_id; ?>"
                                                data-evaluation_reference="<?= $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?>">
                                                <svg style="margin-top: -2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xxs bi-chat" viewBox="0 0 16 16">
                                                  <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                                </svg>
                                            </div>
                                        </span>
                                    <? endif; ?>

                                    <?
                                    /* ----------------------------------------
                                     *
                                     * Parametres
                                     *
                                     * ---------------------------------------- */ ?>

                                    <span data-toggle="tooltip" title="Paramètres">
                                        <div class="btn btn-sm btn-outline-<?= $eev['cacher'] || $eev['bloquer'] ? 'dark' : 'primary'; ?> parametres-evaluation"  
                                            data-toggle="modal" 
                                            data-target="#modal-parametres-evaluation" 
                                            data-semestre_id="<?= $this->semestre_id; ?>"
                                            data-inscription_requise="<?= $eev['inscription_requise'] ?? 0; ?>"
                                            data-cacher="<?= $eev['cacher'] ?? 0; ?>"
                                            data-bloquer="<?= $eev['bloquer'] ?? 0; ?>"
                                            data-cours_id="<?= $cours_id; ?>"
                                            data-evaluation_id="<?= $evaluation_id; ?>"
                                            data-evaluation_reference="<?= $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?>">
                                            <svg style="margin-top: -2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xxs bi-gear" viewBox="0 0 16 16">
                                              <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                                              <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                                            </svg>
                                        </div>
                                    </span>

                                    <?
                                    /* ----------------------------------------
                                     *
                                     * Filtres
                                     *
                                     * ---------------------------------------- */ ?>

                                    <? if ($eev['inscription_requise']) :  ?>

                                        <span data-toggle="tooltip" title="<?= $params ? $params_tooltip : 'Filtrer'; ?>">
                                            <div class="btn btn-sm btn-outline-<?= ! $params ? 'primary' : 'dark'; ?>" 
                                                data-toggle="modal"
                                                data-target="#modal-filtres-evaluation"
                                                data-params="<?= $params; ?>"
                                                data-cours_id="<?= $cours_id; ?>"
                                                data-cours_groupes="<?= htmlentities(json_encode($cours_g)); ?>"
                                                data-evaluation_id="<?= $evaluation_id; ?>"
                                                data-evaluation_reference="<?= $eev['evaluation_reference']; ?>">

                                                <svg style="margin-top: -2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xxs bi-sliders" viewBox="0 0 16 16">
                                                  <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
                                                </svg>
                                                        
                                                <? if ($params) : ?>
                                                
                                                    <span style="font-weight: 400;">
                                                        <?= $params_desc; ?>
                                                    </span>

                                                <? endif; ?>
                                            </div>
                                        </span>

                                    <? endif; ?>

                                    <?
                                    /* ----------------------------------------
                                     *
                                     * Planifier une evaluation
                                     *
                                     * ---------------------------------------- */ ?>

                                    <span data-toggle="tooltip" title="Planifier"> 
                                        <div class="btn btn-sm btn-outline-<?= $pev ? 'dark' : 'primary'; ?>" 
                                            data-toggle="modal"
                                            data-target="#modal-planifier-evaluation"
                                            data-debut_date="<?= $debut_epoch > $this->now_epoch ? date_humanize($debut_epoch) : NULL; ?>"
                                            data-debut_heure="<?= $debut_epoch > $this->now_epoch ? hour_humanize($debut_epoch) : NULL; ?>"
                                            data-fin_date="<?= $fin_epoch > $this->now_epoch ? date_humanize($eev['fin_epoch']) : NULL; ?>"
                                            data-fin_heure="<?= $fin_epoch > $this->now_epoch ?  hour_humanize($eev['fin_epoch']) : NULL; ?>"
                                            data-temps_limite="<?= $eev['temps_limite']; ?>"
                                            data-cachee="<?= $eev['cacher']; ?>"
                                            data-evaluation_id="<?= $evaluation_id; ?>"
                                            data-evaluation_reference="<?= $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?>">

                                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px;" width="16" height="16" fill="currentColor" class="bi-xxs bi-clock" viewBox="0 0 16 16">
                                              <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                            </svg>

                                            <? if (1 == 2 && $pev) : ?>
                                                <span>
                                                    <? if ($debut_epoch && $debut_epoch > $this->now_epoch) : ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px" fill="currentColor" class="bi-xxs bi-arrow-bar-right" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M6 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L12.293 7.5H6.5A.5.5 0 0 0 6 8zm-2.5 7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5z"/>
                                                        </svg>
                                                        <?= date('Y-m-d H:i', $debut_epoch); ?>
                                                    <? endif; ?>

                                                    <? if ($fin_epoch && $fin_epoch > $this->now_epoch) : ?>
                                                        <? if ($debut_epoch && $debut_epoch > $this->now_epoch) : ?>
                                                            -
                                                        <? endif; ?>
                                                        <?= date('Y-m-d H:i', $fin_epoch); ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px" fill="currentColor" class="bi-xxs bi-arrow-bar-left" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M12.5 15a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5zM10 8a.5.5 0 0 1-.5.5H3.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L3.707 7.5H9.5a.5.5 0 0 1 .5.5z"/>
                                                        </svg>
                                                    <? endif; ?>
                                                </span>
                                            <? endif; ?>
                                        </div>
                                    </span>

                                    <span data-toggle="tooltip" title="Terminer">
                                        <div class="btn btn-sm btn-danger evaluation-terminee spinnable"  
                                            data-toggle="modal" 
                                            data-target="#modal-terminer-evaluation" 
                                            data-semestre_id="<?= $this->semestre_id; ?>"
                                            data-cours_id="<?= $cours_id; ?>"
                                            data-evaluation_id="<?= $evaluation_id; ?>"
                                            data-evaluation_reference="<?= $evaluations_en_vigueur[$evaluation_id]['evaluation_reference']; ?>">
                                            <svg style="margin-top: -2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xxs bi-x-circle" viewBox="0 0 16 16">
                                              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </div>
                                    </span>
                                </td>

                            </tr>

                        <? endforeach; ?>

                    <? endforeach; ?>

                <? endif; // empty($evaluations) || empty($cours_raw) ?>
                
                </tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

    </div>

    <? 
    // ---------------------------------------------------------------------
    // 
    // ACTIVITE
    //
    // --------------------------------------------------------------------- ?>

    <div class="activite d-none d-md-block">

        <? if ($this->config->item('ping_etudiant_evaluation')) : ?>

        <? 
        // ---------------------------------------------------------------------
        // 
        // LES ETUDIANTS PRESENTEMENT EN REDACTION D'EVALUATION
        //
        // --------------------------------------------------------------------- ?>
        
        <div id="etudiants-evaluations-ferme" class="d-none bienvenue-fermee" style="margin-top: 25px; margin-bottom: 25px">

            <div class="row">
                <div class="col-8" style="padding-top: 3px;">
                Les étudiants présentement en rédaction de vos évaluations
                </div>
                <div class="col-4" style="text-align: right">
                    <div id="etudiants-evaluations-ouvrir" class="btn btn-sm btn-light">
                        <i class="fa fa-plus-square"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="etudiants-evaluations-ouvert" class="bienvenue-table-box-noborder" style="margin-top: 15px">

            <table class="bienvenue-table">

                <thead>
                    <tr class="bienvenue-table-titre" style="background: #fff; color: dodgerblue;">
                        <th colspan="5" style="padding-left: 0">
                            Les étudiants présentement en rédaction de vos évaluations 
                            <span style="font-weight: 300">
                                (<?= count($etudiants_redaction); ?>)
                            </span>
                        </th>
                        <th style="text-align: right; padding-right: 0">
                            <div id="etudiants-evaluations-fermer" class="btn btn-sm btn-light">
                                <i class="fa fa-minus-square"></i>
                            </div>
                        </th>
                    </tr>

                    <? if ( ! empty($etudiants_redaction)) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th style="width: 90px">Référence</th>
                            <th style="width: 280px;">Étudiant(e)</th>
                            <th>Titre de l'évaluation</th>
                            <th style="width: 140px; text-align: center">Temps écoulé</th>
                            <th style="width: 175px;">Dernière activité</th>
                            <th style="width: 75px; text-align: right">Actions</th>
                        </tr>

                    <? endif; ?>

                </thead>

                <tbody style="font-size: 0.9em">

                    <? if (empty($etudiants_redaction)) : ?>

                        <tr>
                            <td colspan="6" style="padding: 15px; font-family: Lato; font-size: 1.1em; font-weight: 300;">
                                <i class="fa fa-exclamation-circle"></i> 
                                Aucun étudiant présentement en rédaction
                            </td>
                        </tr>

                    <? else : ?>

                        <? foreach($etudiants_redaction as $ee) : 

                            $bg_actif   = '#FFECB3';
                            $bg_inactif = '#F7F7F7';

                            if (
                                (array_key_exists('nom', $ee) && ! empty($ee['nom'])) ||
                                (array_key_exists('prenom', $ee) && ! empty($ee['prenom']))
                               )
                            {
                                $nom = $ee['prenom'] . ' ' . $ee['nom'];
                            }
                            else
                            {
                                $nom = 'Non-inscrit [' . substr($ee['session_id'], 0, 5) . ']';
                            }
                        ?>
                            <tr style="background: <?= ($this->now_epoch - $ee['activite_epoch']) < 60*5 ? $bg_actif : $bg_inactif; ?>">
                                <td class="mono" style="background: inherit">
                                    <?= $ee['evaluation_reference']; ?>
                                </td>
                                <td style="background: inherit">
                                    <? if ($ee['etudiant_id']) : ?>
                                        <i class="bi bi-person-circle"></i>
                                        <a href="<?= base_url() . 'etudiant/' . $ee['etudiant_id']; ?>" target="_blank"><?= ellipsize($nom, 30); ?></a>

                                        <a style="margin-left: 5px" href="<?= base_url() . 'evaluation/endirect/reference/' . $ee['evaluation_reference'] . '/etudiant/' . $ee['etudiant_id']; ?>" target="_blank">
                                            <svg data-toggle="tooltip" data-title="En direct" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xs bi-front" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm5 10v2a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2v5a2 2 0 0 1-2 2H5z"/>
                                            </svg>
                                        </a>

                                        <?
                                        /* ----------------------------------------------
                                         *
                                         * Afficher le nom des partenaires de laboratoire
                                         *
                                         * ---------------------------------------------- */ ?>

                                        <? if ($ee['lab']) : ?>

                                            <? if ( ! empty($ee['lab_etudiant2_nom'])) : ?>

                                                <br /><i class="bi bi-person"></i> <a href="<?= base_url() . 'etudiant/' . $ee['lab_etudiant2_id']; ?>" target="_blank"><?= ellipsize($ee['lab_etudiant2_nom'], 30); ?></a>

                                            <? endif; ?>
                
                                            <? if ( ! empty($ee['lab_etudiant3_nom'])) : ?>

                                                <br /><i class="bi bi-person"></i> <a href="<?= base_url() . 'etudiant/' . $ee['lab_etudiant3_id']; ?>" target="_blank"><?= ellipsize($ee['lab_etudiant3_nom'], 30); ?></a>

                                            <? endif; ?>

                                        <? endif; ?>

                                    <? else : ?>

                                        <?= ellipsize($nom, 30); ?>

                                    <? endif; ?>
                                </td>
                                <td style="background: inherit">

                                    <?= ellipsize($evaluations[$ee['evaluation_id']]['evaluation_titre'], 80); ?>

                                    <? if (array_key_exists('notifications', $ee) && count($ee['notifications']) > 0) : ?>
                                        <svg style="margin-top: -2px; margin-left: 3px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="dodgerblue" class="bi-xs bi-chat-text" viewBox="0 0 16 16">
                                              <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                              <path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8zm0 2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
                                        </svg>          
                                        <sup style="margin-left: -6px; color: #fff; font-size: 0.6em; padding: 1px 4px 1px 4px; border-radius: 3px; background: dodgerblue;"><?= count($ee['notifications']); ?></sup>
                                    <? endif; ?>

                                </td>
                                <td class="mono" style="background: inherit; text-align: center">
                                    <span style="cursor: pointer" 
                                          data-trigger="hover" 
                                          data-toggle="popover" 
                                          data-html="true"
                                          data-placement="top"
                                          data-content="Rédige depuis :<br /><?= date_humanize($ee['soumission_debut_epoch'], TRUE); ?>">
                                        <?= calculer_duree($ee['soumission_debut_epoch'], $this->now_epoch); ?>
                                    </span>
                                </td>
                                <td class="mono" style="background: inherit">
                                    <span style="cursor: pointer" 
                                          data-trigger="hover" 
                                          data-toggle="popover" 
                                          data-html="true"
                                          data-placement="top"
                                          data-content="Temps en activité :<br /><?= calculer_duree(0, $ee['secondes_en_redaction']); ?>">
                                        <?= ! empty($ee['activite_epoch']) ? date_humanize($ee['activite_epoch'], TRUE) : 'non disponible'; ?>
                                    </span>
                                </td>
                                <td style="text-align: right">

                                    <? if ( ! empty($ee['etudiant_id'])) : ?>

                                        <?
                                        /* ----------------------------------------
                                         *
                                         * Envoyer un message a cet etudiant specifique
                                         *
                                         * ---------------------------------------- */ ?>

                                         <span style="cursor: pointer"
                                                data-toggle="modal" 
                                                data-target="#modal-communiquer-evaluation-etudiant" 
                                                data-semestre_id="<?= $this->semestre_id; ?>"
                                                data-cours_id="<?= $cours_id; ?>"
                                                data-etudiant_id="<?= $ee['etudiant_id']; ?>"
                                                data-etudiant_nom="<?= $nom; ?>"
                                                data-evaluation_id="<?= $evaluation_id; ?>"
                                                data-evaluation_reference="<?= $ee['evaluation_reference']; ?>">
                                                <svg data-toggle="tooltip" title="Communiquer avec <?= $ee['genre'] == 'F' ? 'cette étudiante' : 'cet étudiant'; ?>"
                                                     style="margin-top: -2px; margin-right: 3px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="dodgerblue" class="bi-sm bi-chat" viewBox="0 0 16 16">
                                                  <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                                </svg>
                                            </div>
                                        </span>

                                        <?
                                        /* ----------------------------------------
                                         *
                                         * Terminer l'evaluation de cet etudiant specifique
                                         *
                                         * ---------------------------------------- */ ?>

                                        <span class="evaluation-terminee" style="cursor: pointer"  
                                            data-toggle="modal" 
                                            data-target="#modal-terminer-evaluation-etudiant" 
                                            data-semestre_id="<?= $this->semestre_id; ?>"
                                            data-cours_id="<?= $cours_id; ?>"
                                            data-etudiant_id="<?= $ee['etudiant_id']; ?>"
                                            data-etudiant_nom="<?= $nom; ?>"
                                            data-evaluation_id="<?= $evaluation_id; ?>"
                                            data-evaluation_reference="<?= $ee['evaluation_reference']; ?>">
                                            <svg data-toggle="tooltip" title="Terminer l'évaluation de <?= $ee['genre'] == 'F' ? 'cette étudiante' : 'cet étudiant'; ?>" 
                                                 xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="crimson" class="bi-sm bi-x-circle" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </span>

                                    <? endif; ?>
                                </td>
                            </tr>
                        <? endforeach; ?>

                    <? endif; ?>

                <tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

        <? endif; // config ?>

        <? 
        // ---------------------------------------------------------------------
        // 
        // DERNIERES SOUMISSIONS
        //
        // --------------------------------------------------------------------- ?>

        <div id="dernieres-soumissions-ferme" class="d-none bienvenue-fermee" style="margin-top: 25px; margin-bottom: 25px">

            <div class="row">
                <div class="col-8" style="padding-top: 3px;">
                    Les dernières soumissions de vos étudiants
                </div>
                <div class="col-4" style="text-align: right">
                    <div id="dernieres-soumissions-ouvrir" class="btn btn-sm btn-light">
                        <i class="fa fa-plus-square"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="dernieres-soumissions-ouvert" class="bienvenue-table-box-noborder" style="margin-top: 15px">

            <table class="bienvenue-table">

                <thead>
                    <tr class="bienvenue-table-titre" style="background: #fff; color: dodgerblue;">
                        <th colspan="6" style="padding-left: 0">
                            Les dernières soumissions de vos étudiants
                        </th>
                        <th style="text-align: right; padding-right: 0">
                            <div id="dernieres-soumissions-fermer" class="btn btn-sm btn-light">
                                <i class="fa fa-minus-square"></i>
                            </div>
                        </th>
                    </tr>
        
                    <? if ( ! empty($dernieres_soumissions)) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th style="width: 175px">Date remis</th>
                            <th>Étudiant(e)</th>
                            <th style="width: 90px; text-align: center">Référence</th>
                            <th style="width: 100px; text-align: center">Cours</th>
                            <th style="width: 100px; text-align: center">Évaluation</th>
                            <th style="width: 140px; text-align: center">Durée</th>
                            <th style="width: 175px; text-align: right">Points (%)</th>
                        </tr>

                    <? endif; ?>

                </thead>

                <tbody style="font-size: 0.9em">

                    <? if (empty($dernieres_soumissions)) : ?>

                        <tr>
                            <td colspan="10" style="padding: 15px; font-family: Lato; font-size: 1.1em; font-weight: 300;">
                                <i class="fa fa-exclamation-circle"></i> 
                                Aucune soumission trouvée pour le semestre en vigueur
                            </td>
                        </tr>

                    <? else :

                        $ajd = date('Ymj');

                    ?>
                        <? 
                            $evaluations_data = array();

                            foreach($dernieres_soumissions as $s) : 

                                $cours_data = (array) json_decode(gzuncompress($s['cours_data_gz'])); 

                                if ( ! array_key_exists($s['evaluation_id'], $evaluations_data))
                                {
                                    $evaluations_data[$s['evaluation_id']] = json_decode(gzuncompress($s['evaluation_data_gz']), TRUE);
                                }

                                // Ajustements
                                
                                $ajustements = ! empty($s['ajustements_data']) ? unserialize($s['ajustements_data']) : array();
                                $points_obtenus = array_key_exists('total', $ajustements) ? $ajustements['total'] : $s['points_obtenus'];

                        ?>
                            <tr class="<?= date('Ymj', $s['soumission_epoch']) == $ajd ? 'ajd' : ''; ?>">
                                <td class="mono"><?= $s['soumission_date']; ?></td>
                                <td>
                                    <? if ($s['etudiant_id']) : ?>
                                        <i class="bi bi-person"></i>
                                        <a href="<?= base_url() . 'etudiant/' . $s['etudiant_id']; ?>" target="_blank"><?= $s['prenom_nom']; ?></a>
                                    <? else : ?>
                                        <?= $s['prenom_nom']; ?>
                                    <? endif; ?>

                                    <? if ( ! empty($s['lab_etudiant2_id'])) : ?>
                                        <i class="bi bi-person"></i>
                                        <a href="<?= base_url() . 'etudiant/' . $s['lab_etudiant2_id']; ?>" target="_blank"><?= $s['lab_etudiant2_nom']; ?></a>
                                    <? endif; ?>

                                    <? if ( ! empty($s['lab_etudiant3_id'])) : ?>
                                        <i class="bi bi-person"></i>
                                        <a href="<?= base_url() . 'etudiant/' . $s['lab_etudiant3_id']; ?>" target="_blank"><?= $s['lab_etudiant3_nom']; ?></a>
                                    <? endif; ?>
                                </td>
                                <td class="mono" style="text-align: center"><a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>"><?= $s['soumission_reference']; ?></a></td>
                                <td style="text-align: center"><?= @$cours_data['cours_code_court']; ?></td>
                                <td style="text-align: center">
                                    
                                    <a href="<?= base_url() . 'resultats/evaluation/' . $s['evaluation_id'] . '/semestre/' . $s['semestre_id']; ?>"
                                       data-toggle="popover"
                                       data-content="<?= $evaluations_data[$s['evaluation_id']]['evaluation_titre']; ?>">
                                        <?= $s['evaluation_id']; ?>
                                    </a>

                                </td>
                                <td class="mono" style="text-align: center"><?= $s['duree']; ?></td>
                                <td style="text-align: right">

                                    <? if ( ! $s['corrections_terminees']) : ?>

                                        <a href="<?= base_url() . 'corrections/corriger/' . $s['soumission_reference']; ?>" style="color: crimson">
                                            à corriger
                                        </a>

                                    <? else : ?>

                                        <? if ($s['points_obtenus'] > 0) : ?>
                                            <?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?> 
                                            <span style="padding-left: 10px">(<?= number_format($points_obtenus / $s['points_evaluation'] * 100)?>%)</span>
                                        <? else : ?>
                                        0 / <?= number_format($s['points_evaluation']); ?> <span style="padding-left: 10px">(0%)</span>
                                        <? endif; ?>

                                    <? endif; ?>
                
                                </td>
                            </tr>
                        <? endforeach; ?>

                    <? endif; ?>

                <tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

        <? 
        // ---------------------------------------------------------------------
        // 
        // CORRECTIONS CONSULTEES
        //
        // --------------------------------------------------------------------- ?>

        <div id="corrections-consultees-ferme" class="d-none bienvenue-fermee" style="margin-top: 30px;">

            <div class="row">
                <div class="col-8" style="padding-top: 3px;">
                    Les dernières corrections consultées
                </div>
                <div class="col-4" style="text-align: right">
                    <div id="corrections-consultees-ouvrir" class="btn btn-sm btn-light">
                        <i class="fa fa-plus-square"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="corrections-consultees-ouvert" class="bienvenue-table-box-noborder" style="margin-top: 20px">

            <table class="bienvenue-table">

                <thead>
                    <tr class="bienvenue-table-titre" style="background: #fff; color: dodgerblue;">
                        <th colspan="6" style="padding-left: 0">
                            Les dernières corrections consultées
                        </th>
                        <th style="text-align: right; padding-right: 0">
                            <div id="corrections-consultees-fermer" class="btn btn-sm btn-light">
                                <i class="fa fa-minus-square"></i>
                            </div>
                        </th>
                    </tr>
        
                    <? if ( ! empty($corrections_consultees)) : ?>

                        <tr class="bienvenue-table-entete" style="font-size: 0.9em">
                            <th style="width: 175px">Date consultée</th>
                            <th>Remis par</th>
                            <th style="width: 90px; text-align: center">Référence</th>
                            <th style="width: 90px; text-align: center">Cours</th>
                            <th style="width: 130px; text-align: center">Date remis</th>
                            <th style="width: 90px; text-align: center">Vues</th>
                            <th style="width: 160px; text-align: right">Points (%)</th>
                        </tr>

                    <? endif; ?>

                </thead>

                <tbody style="font-size: 0.9em">

                    <? if (empty($corrections_consultees)) : ?>

                        <tr>
                            <td colspan="10"style="padding: 15px; font-family: Lato; font-size: 1.1em; font-weight: 300;">
                                <i class="fa fa-exclamation-circle"></i> 
                                Aucune correction consultée
                            </td>
                        </tr>

                    <? else :

                        $ajd = date('Ymj');

                    ?>
                        <? foreach($corrections_consultees as $c) : ?>

                            <tr class="<?= date('Ymj', $c['epoch']) == $ajd ? 'ajd' : ''; ?>">
                                <td class="mono"><?= date_humanize($c['epoch'], TRUE); ?></td>
                                <td>
                                    <i class="bi bi-person"></i>
                                    <a href="<?= base_url() . 'etudiant/' . $c['etudiant_id']; ?>" target="_blank"><?= $c['prenom_nom']; ?></a>
                                </td>
                                <td class="mono" style="text-align: center"><a href="<?= base_url() . 'consulter/' . $c['soumission_reference']; ?>"><?= $c['soumission_reference']; ?></a></td>
                                <td style="text-align: center"><?= $c['cours_code_court']; ?></td>
                                <td class="mono" style="text-align: center"><?= date_humanize($c['soumission_epoch']); ?></td>
                                <td style="text-align: center">
                                    <? if (empty($c['etudiant_id']) && $c['vues'] >= $this->config->item('corrections_max_vues')) : ?>
                                        <div class="remettre-a-zero badge badge-pill badge-danger" 
                                             style="font-size: 0.9em; font-weight: 300; cursor: pointer"
                                             data-soumission_id="<?= $c['soumission_id']; ?>"
                                             data-soumission_reference="<?= $c['soumission_reference']; ?>"
                                             data-toggle="modal" 
                                             data-target="#modal-reset-vues">
                                            <?= $c['vues']; ?>
                                        </div>
                                    <? else : ?>
                                        <?= $c['vues']; ?>
                                    <? endif; ?>
                                </td>
                                <td style="text-align: right">
                                    <? if ($c['points_evaluation'] > 0) : ?>

                                        <?= my_number_format($c['points_obtenus']) . ' / ' . my_number_format($c['points_evaluation']); ?> 
                                        <span style="padding-left: 10px">(<?= number_format($c['points_obtenus'] / $c['points_evaluation'] * 100)?>%)</span>

                                    <? else : ?>

                                        <?= my_number_format($c['points_obtenus']) . ' / ' . my_number_format(0); ?> 
                                        <span style="padding-left: 10px">(<?= number_format(0); ?>%)</span>

                                    <? endif; ?>
                                </td>
                            </tr>

                        <? endforeach; ?>

                    <? endif; ?>

                <tbody>

            </table>

        </div> <!-- .bienvenue-table-box -->

        <? 
        // ---------------------------------------------------------------------
        // 
        // CORRECTIONS CONSULTEES
        //
        // --------------------------------------------------------------------- ?>

        <div id="corrections-consultees" class="d-none">

        <? if (empty($corrections_consultees)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucune correction consultée

        <? else : ?>

            <table class="table bienvenue-table">
                <thead>
                    <tr>
                        <th>Date de consultation</th>
                        <th style="text-align: center">Référence</th>
                        <th style="text-align: center">Vues</th>
                        <th>Soumis par</th>
                        <th>Date soumis</th>
                        <th style="text-align: center">Cours</th>
                        <th style="text-align: center">Semestre</th>
                        <th style="text-align: right">Points (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($corrections_consultees as $c) : ?>
                        <tr>
                            <td><?= date_humanize($c['epoch'], TRUE); ?></td>
                            <td style="text-align: center"><a href="<?= base_url() . 'consulter/' . $c['soumission_reference']; ?>"><?= $c['soumission_reference']; ?></a></td>
                            <td style="text-align: center"><?= $c['vues']; ?></td>
                            <td><?= $c['prenom_nom']; ?></td>
                            <td><?= date_humanize($c['soumission_epoch']); ?></td>
                            <td style="text-align: center"><?= $c['cours_code_court']; ?></td>
                            <td style="text-align: center"><?= $c['semestre_code']; ?></td>
                            <td style="text-align: right">
                                <? if ($c['points_evaluation'] > 0) : ?>
                                    <?= my_number_format($c['points_obtenus']) . ' / ' . my_number_format($c['points_evaluation']); ?> 
                                    <span class="" style="padding-left: 10px">(<?= number_format($c['points_obtenus'] / $c['points_evaluation'] * 100)?>%)</span>
                                <? endif; ?>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>

        <? endif; ?>

    </div> <!-- #corrections-consultees -->

</div> <!-- .col-sm-12 -->
</div> <!-- .col-xl-1 -->

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue-enseignants -->

<? 
/* ----------------------------------------------------------------------------
 *
 * MODALS
 *
 * ---------------------------------------------------------------------------- */ ?>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: COMMUNIQUER
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-communiquer-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title">
                    <svg style="margin-top: -3px; margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="dodgerblue" class="bi-md bi-chat" viewBox="0 0 16 16">
                      <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                    </svg>
                    Communiquer
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-communiquer-evaluation-form'), 
                        array(
                            'evaluation_id'        => NULL, 
                            'evaluation_reference' => NULL
                        )); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <div class="form-group">
                            <label for="modal-communiquer-evaluation-message">
                                Envoyer un message aux étudiants en rédaction de l'évaluation 
                                <span class="evaluation-reference" style="color: dodgerblue; font-weight: 600"></span> :
                            </label>
                        
                            <div class="hspace"></div>

                            <textarea class="form-control" id="modal-communiquer-evaluation-message" name="message" rows="3" placeholder="Entrez votre message"></textarea>

                            <div class="erreur-message d-none" style="font-size: 0.85em; color: crimson; margin-top: 10px;">
                                <i class="fa fa-exclamation-circle" style="margin-right: 3px;"></i>
                                <span></span>
                            </div>

                        </div>
                        
                        <div style="font-size: 0.85em; color: #777">
                            <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
                            Maximum de 512 caractères
                        </div>

                        <div style="font-size: 0.85em; color: #777">
                            <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
                            Ce message apparaîtra aux étudiants dans les <?= $this->config->item('ping_etudiant_evaluation_intervalle'); ?> secondes ou moins.
                        </div>

                    </div>
				</form>

      		</div> <!-- .modal-body -->

            <div class="modal-footer">
                <div class="col-12" style="text-align: right">
                    <div id="modal-communiquer-evaluation-sauvegarde" class="btn btn-primary spinnable">
                        <svg style="margin-top: -2px; margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xs bi-chat-text-fill" viewBox="0 0 16 16">
                            <path d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM4.5 5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 2.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                        </svg>
                        Envoyer ce message
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times" style="margin-right: 5px;"></i> 
                        Annuler
                    </div>
                </div>
            </div> <!-- .modal-footer -->

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: COMMUNIQUER AVEC UN ETUDIANT
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-communiquer-evaluation-etudiant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title">
                    <svg style="margin-top: -3px; margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="dodgerblue" class="bi-md bi-chat" viewBox="0 0 16 16">
                      <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                    </svg>
                    Communiquer avec un étudiant
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-communiquer-evaluation-etudiant-form'), 
                        array(
                            'evaluation_id'        => NULL, 
                            'evaluation_reference' => NULL,
                            'etudiant_id'          => NULL
                        )); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <div class="form-group">
                            <label for="modal-communiquer-evaluation-etudiant-message">
                                Envoyer un message à <span class="etudiant_nom" style="color: dodgerblue; font-weight: 600"></span>
                                en rédaction de l'évaluation <span class="evaluation-reference" style="color: dodgerblue; font-weight: 600"></span> :
                            </label>
                        
                            <div class="hspace"></div>

                            <textarea class="form-control" id="modal-communiquer-evaluation-etudiant-message" name="message" rows="3" placeholder="Entrez votre message"></textarea>

                            <div class="erreur-message d-none" style="font-size: 0.85em; color: crimson; margin-top: 10px;">
                                <i class="fa fa-exclamation-circle" style="margin-right: 3px;"></i>
                                <span></span>
                            </div>

                        </div>
                        
                        <div style="font-size: 0.85em; color: #777">
                            <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
                            Maximum de 512 caractères
                        </div>

                        <div style="font-size: 0.85em; color: #777">
                            <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
                            Ce message apparaîtra aux étudiants dans les <?= $this->config->item('ping_etudiant_evaluation_intervalle'); ?> secondes ou moins.
                        </div>

                    </div>
				</form>

      		</div> <!-- .modal-body -->

            <div class="modal-footer">
                <div class="col-12" style="text-align: right">
                    <div id="modal-communiquer-evaluation-etudiant-sauvegarde" class="btn btn-primary spinnable">
                        <svg style="margin-top: -2px; margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xs bi-chat-text-fill" viewBox="0 0 16 16">
                            <path d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM4.5 5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 2.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                        </svg>
                        Envoyer ce message
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times" style="margin-right: 5px;"></i> 
                        Annuler
                    </div>
                </div>
            </div> <!-- .modal-footer -->

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: PARAMETRES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-parametres-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
                <h5 class="modal-title">
                    <svg style="margin-top: -2px; margin-right: 3px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-md bi-gear" viewBox="0 0 16 16">
                      <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                      <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                    </svg>
                    Paramètres de l'évaluation
                </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-parametres-evaluation-form'), 
                        array(
                            'evaluation_id'        => NULL, 
                            'evaluation_reference' => NULL
                        )); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <div class="custom-control custom-switch">
                        <input id="switch-inscription-requise" name="inscription_requise" class="custom-control-input" type="checkbox">
                            <label class="custom-control-label" for="switch-inscription-requise">
                                Inscription requise
                            </label>
                            <div style="margin-left: 2px; font-weight: 300; font-size: 0.85em">
                                Les étudiants doivent être inscrits pour accéder l'évaluation.
                            </div>
                        </div>

                        <div class="space"></div>

                        <? if ($this->sous_domaine != 'www') : ?>

                            <div class="custom-control custom-switch">
                                <input id="switch-cacher-evaluation" name="cacher" class="custom-control-input" type="checkbox">
                                <label class="custom-control-label" for="switch-cacher-evaluation">
                                    Cachée
                                </label>
                                <div style="margin-left: 2px; font-weight: 300; font-size: 0.85em">
                                    Cacher l'évaluation de la liste des évaluations accessibles par les étudiants.<br />
                                    Le lien direct sera alors requis pour accéder l'évaluation.
                                </div>
                            </div>

                            <div class="space"></div>

                        <? endif; ?>

                        <div class="custom-control custom-switch">
                            <input id="switch-bloquer-evaluation" name="bloquer" class="custom-control-input" type="checkbox">
                            <label class="custom-control-label" for="switch-bloquer-evaluation">
                                Bloquée
                            </label>
                            <div style="margin-left: 2px; font-weight: 300; font-size: 0.85em">
                                Cacher et Interdire l'accès à l'évaluation. Ceci n'affectera pas les étudiants déjà en rédaction.
                            </div>
                        </div>

                    </div>
				</form>

      		</div> <!-- .modal-body -->

            <div class="modal-footer">
                <div class="col-12" style="text-align: right">
                    <div id="modal-parametres-evaluation-sauvegarde" class="btn btn-primary spinnable">
                        <i class="fa fa-check" style="margin-right: 5px;"></i> 
                        Modifier les paramètres
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times" style="margin-right: 5px;"></i> 
                        Annuler
                    </div>
                </div>
            </div> <!-- .modal-footer -->

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: TERMINER EVALUATION (POUR TOUS LES ETUDIANTS)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-terminer-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header" style="background: #ffebee; color: crimson">
                <h5 class="modal-title">
                <svg style="margin-top: -3px; margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
                Terminer une évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-terminer-evaluation-form'), 
                        array(
                            'evaluation_reference' => NULL
                        ) 
                    ); 
                ?>

					<div class="form-group col-md-12" style="padding: 10px 25px 25px 25px; padding-bottom: 0">

                        Voulez-vous vraiment <b>terminer</b> l'évaluation 
                        <span class="evaluation-reference" style="font-weight: 600; color: dodgerblue"></span> ?
        
                        <div class="space"></div>

                        <div style="text-align: left; font-size: 0.9em; font-weight: 300">

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="enregistrer_evaluation" value="1" checked>
                                <label class="form-check-label">
                                    <span style="font-weight: 500">Enregistrer</span> et corriger les évaluations non terminées des étudiants inscrits
                              </label>
                            </div>

                            <div class="qspace"></div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="enregistrer_evaluation" value="0">
                                <label class="form-check-label">
                                    <span style="font-weight: 500">Ne pas enregistrer</span> les évaluations non terminées des étudiants
                                </label>
                            </div>

                        </div>

					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="terminer-evaluation-execution" class="btn btn-danger spinnable">
                    <i class="fa fa-times-circle"></i> Terminer cette évaluation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: TERMINER L'EVALUATION D'UN ETUDIANT / D'UNE ETUDIANTE
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-terminer-evaluation-etudiant" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header" style="background: #ffebee; color: crimson">
                <h5 class="modal-title">
                <svg style="margin-top: -3px; margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
                Terminer l'évaluation d'un étudiant</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-terminer-evaluation-etudiant-form'), 
                        array(
                            'evaluation_reference' => NULL,
                            'etudiant_id' => NULL,
                        ) 
                    ); 
                ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0">

                        Voulez-vous vraiment <b>terminer</b> l'évaluation 
                        <span class="evaluation-reference" style="font-weight: 600; color: dodgerblue"></span>
                        de 
                        <span class="etudiant_nom" style="font-weight: 600; color: dodgerblue"></span> ?
        
                        <div class="space"></div>

                        <div style="text-align: left; font-size: 0.9em; font-weight: 300">
                            
                            <i class="fa fa-exclamation-circle"></i>
                            L'évaluation sera <span style="font-weight: 600">enregistrée</span> et <span style="font-weight: 600">corrigée</span>.

                        </div>
					</div>

				</form>
				
      		</div>
      
			<div class="modal-footer">
                <div id="terminer-evaluation-etudiant-execution" class="btn btn-outline-danger spinnable">
                    <i class="fa fa-times-circle"></i> Terminer son évaluation
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
                <i class="fa fa-circle-o-notch fa-spin fa-lg d-none" style="color: dodgerblue"></i>
      		</div>
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: RESET VUES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-reset-vues" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-undo" style="margin-right: 5px"></i> Remettre à zéro les vues</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-reset-vues-form'), 
						array('soumission_id' => NULL, 'soumission_reference' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

						Êtes-vous certain de vouloir remettre à zéro le nombre de vues de cette évalution corrigée ?

					</div>

				</form>
      		</div>

			<div class="modal-footer">
                <div id="modal-reset-vues-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-undo" style="margin-right: 5px;"></i> 
                    Remettre à zéro
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times" style="margin-right: 5px;"></i> 
                    Annuler
                </div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: FILTRES (FILTRER LES ETUDIANTS)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-filtres-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-sliders" style="margin-right: 5px"></i> Filtrer les étudiants</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-filtres-evaluation-form'), 
                        array(
                            'cours_id' => NULL,
                            'evaluation_id' => NULL, 
                            'evaluation_reference' => NULL
                        )); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <div style="font-family: Lato; font-weight: 400">

                            <div style="font-weight: 300; font-size: 0.9em">
                                Ces paramètres filtreront les étudiants qui pourront accéder cette évaluation.<br />
                            </div>

                            <div class="space"></div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="filtre" value="enseignant">
                                <label class="form-check-label" for="filtre1">
                                    Seulement mes étudiants
                              </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="filtre" value="cours">
                                <label class="form-check-label">
                                    Seulement mes étudiants de ce cours
                                </label>
                            </div>

                            <div id="modal-cours-groupes"></div>

                            <div class="space"></div>

                            <div style="font-weight: 300; font-size: 0.9em">
                                Pour une sécurité augmentée :
                            </div>

                            <div class="space"></div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="filtre" value="enseignant_autorisation">
                                <label class="form-check-label" for="filtre1">
                                    Seulement mes étudiants autorisés 
                                    <sup><i class="fa fa-asterisk" style="color: crimson;"></i></sup>
                              </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="filtre" value="cours_autorisation">
                                <label class="form-check-label">
                                    Seulement mes étudiants autorisés de ce cours
                                    <sup><i class="fa fa-asterisk" style="color: crimson"></i></sup>
                                </label>
                            </div>

                            <div id="modal-cours-groupes-autorisation"></div>

                            <? /* TEMPLATES */ ?>

                            <div id="cours-groupe-template" class="d-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filtre" value="groupe_">
                                    <label class="form-check-label">
                                        Seulement mes étudiants de ce cours et du groupe 
                                        <span class="modal-cours-groupe" style="font-weight: 700"></span>
                                    </label>
                                </div>
                            </div>

                            <div id="cours-groupe-autorisation-template" class="d-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="filtre" value="groupe_autorisation_">
                                    <label class="form-check-label">
                                        Seulement mes étudiants autorisés de ce cours et du groupe 
                                        <span class="modal-cours-groupe-autorisation" style="font-weight: 700"></span>
                                        <sup><i class="fa fa-asterisk" style="color: crimson"></i></sup>
                                    </label>
                                </div>
                            </div>

                            <div class="hspace"></div>

                            <div style="font-weight: 300; font-size: 0.9em; margin-top: 5px;">
                                <i class="fa fa-asterisk" style="color: crimson; margin-right: 5px;"></i>
                                Vous devez autoriser les comptes de vos étudiants dans vos listes :
                                <a href="<?= base_url() . 'configuration'; ?>" target="_blank">Configuration</a>
                            </div>

                            <? if ($this->enseignant_id == 1) : ?>

                                <a class="filtre-etudiants btn btn-sm btn-primary" 
                                   style="margin-top: 25px; margin-bottom: -5px;"
                                   data-href_pre="<?= base_url() . 'evaluations/filtres/'; ?>"
                                   href="" 
                                   target="_blank">
                                    Filtres avancés (ALPHA)
                                    <i class="fa fa-angle-right" style="margin-left: 5px;"></i>
                                </a>

                            <? endif; ?>
                        </div>

                    </div> <!-- .form-group -->

				</form>

      		</div> <!-- .modal-body -->

            <div class="modal-footer">
                <div class="col">
                    <div id="modal-effacer-filtres-sauvegarde" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash" style="margin-right: 5px;"></i> 
                        Effacer le filtre
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col-8" style="text-align: right">
                    <div id="modal-filtres-evaluation-sauvegarde" class="btn btn-success spinnable">
                        <i class="fa fa-save" style="margin-right: 5px;"></i> 
                        Sauvegarder le filtre
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times" style="margin-right: 5px;"></i> 
                        Annuler
                    </div>
                </div>
            </div> <!-- .modal-footer -->
    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: PLANIFIER UNE EVALUATION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-planifier-evaluation" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-clock-o" style="margin-right: 5px"></i> Planifier une évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
            </div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-planifier-evaluation-form'), 
						array('evaluation_id' => NULL, 'evaluation_reference' => NULL)
					); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px;">

                        <div style="border: 1px solid #A5D6A7; padding: 25px; border-radius: 3px">

                            <div style="color: #2E7D32; font-weight: 200">
                                <strong>DÉBUT</strong> automatique de l'évaluation :
                            </div>
                                
                            <div class="form-row mt-3">

                                <div class="col">
                                    <label for="modal-planifier-evaluation-debut-date">Date</label>
                                    <input type="date" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', $this->semestre['semestre_fin_epoch']); ?>" class="form-control" name="debut_date" id="modal-planifier-evaluation-debut-date">
                                </div>

                                <div class="col">
                                    <label for="modal-planifier-evaluation-debut-heure">Heure <span style="color: #aaa;">(24h)</span></label>
                                    <input type="time" class="form-control" name="debut_heure" id="modal-planifier-evaluation-debut-heure">
                                </div>

                            </div>

                            <div id="planifier-evaluation-erreur-debut" class="planifier-evaluation-erreur d-none" style="margin-top: 20px; padding: 10px; background: #FFF59D; border-radius: 3px; color: crimson; font-size: 0.85em">
                                <i class="fa fa-exclamation-circle"></i>
                                Le début de l'évaluation ne peut pas être avant le début du semestre.
                            </div>

                            <div id="planifier-evaluation-erreur-debut-passe" class="planifier-evaluation-erreur d-none" style="margin-top: 20px; padding: 10px; background: #FFF59D; border-radius: 3px; color: crimson; font-size: 0.85em">
                                <i class="fa fa-exclamation-circle"></i>
                                Le début de l'évaluation ne peut pas être dans le passé.
                            </div>
                        </div>

                        <div class="mt-4" style="border: 1px solid #ffcdd2; padding: 25px; border-radius: 3px;">

                            <div style="color: crimson; font-weight: 200"><strong>FIN</strong> automatique de l'évaluation :</div>
                                
                            <div class="form-row mt-3">

                                <div class="col">
                                    <label for="modal-planifier-evaluation-fin-date">Date</label>
                                    <input type="date" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', $this->semestre['semestre_fin_epoch']); ?>" class="form-control" name="fin_date" id="modal-planifier-evaluation-fin-date">
                                </div>

                                <div class="col">
                                    <label for="modal-planifier-evaluation-fin-heure">Heure <span style="color: #aaa;">(24h)</span></label>
                                    <input type="time" class="form-control" name="fin_heure" id="modal-planifier-evaluation-fin-heure">
                                </div>

                            </div>

                            <div id="planifier-evaluation-erreur-fin" class="planifier-evaluation-erreur d-none" style="margin-top: 20px; padding: 10px; background: #FFF59D; border-radius: 3px; color: crimson; font-size: 0.85em">
                                <i class="fa fa-exclamation-circle"></i>
                                La fin de l'évaluation ne peut pas être après la fin du semestre.
                            </div>

                        </div>

                        <div class="mt-4" style="border: 1px solid #1976D2; padding: 25px; border-radius: 3px;">

                            <div class="form-row">

                                <div class="col mt-2">
                                    <label for="modal-planifier-evaluation-temps-limite" style="color: #1976D2; font-weight: 200">
                                        <strong>TEMPS LIMITE</strong> pour compléter l'évaluation :
                                    </label>
                                </div>

                                <div class="col">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="temps_limite" id="modal-planifier-evaluation-temps-limite" style="">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">minutes</span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
            
                        <div id="planifier-evaluation-erreur" class="planifier-evaluation-erreur d-none" style="margin-top: 25px; margin-bottom: -5px; padding: 10px; background: #FFF59D; border-radius: 3px; color: crimson; font-size: 0.85em">
                            ERREUR
                        </div>

                        <div id="" style="margin-top: 25px; margin-bottom: -10px; font-size: 0.85em">
                            <p style="margin-bottom: 0;">
                                <i class="fa fa-info-circle" style="color: #aaa; margin-right: 5px"></i> 
                                Les évaluations non terminées par les étudiants seront enregistrées.
                            </p>
                            <p style="margin-bottom: 0;">
                                <i class="fa fa-info-circle" style="color: #aaa; margin-right: 5px"></i> 
                                Le <span style="color: dodgerblue">temps limite</span> tient compte des <span style="color: dodgerblue">mesures particulières</span> de chaque étudiant (dans la Configuration).
                            </p>
                        </div>

                        <div id="planifier-evaluation-cachee" style="margin-top: 25px; font-size: 0.85em">
                            <p style="margin-bottom: 0;">
                                <i class="fa fa-exclamation-circle"></i> 
                                Votre évaluation est présentement cachée.
                            </p>
                            <p style="margin-left: 21px;">
                                N'oubliez pas de la « décacher » si vous voulez qu'elle apparaisse aux étudiants au moment planifié.
                            </p>
                        </div>

                        <div id="planifier-evaluation-erreur" class="planifier-evaluation-erreur d-none" style="padding: 10px; background: #FFF59D; border-radius: 3px; color: crimson; font-size: 0.85em">
                            ERREUR
                        </div>

                    </div>

				</form>

      		</div>

            <div class="modal-footer">
                <div class="col">
                    <div id="modal-effacer-planification-sauvegarde" class="btn btn-outline-danger spinnable">
                        <i class="fa fa-trash" style="margin-right: 5px;"></i> 
                        Effacer la planification
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
                <div class="col" style="text-align: right">
                    <div id="modal-planifier-evaluation-sauvegarde" class="btn btn-primary spinnable">
                        <i class="fa fa-check" style="margin-right: 5px;"></i> 
                        Planifier
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                    <div class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times" style="margin-right: 5px;"></i> 
                        Annuler
                    </div>
                </div>
      		</div>

    	</div>
  	</div>
</div>
