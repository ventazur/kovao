<!doctype html>
<html lang="en">
    <head>
        <? if ( ! $this->is_DEV) : ?>

            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-71668883-1"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', 'UA-71668883-1');
            </script>

        <? endif; ?>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>KOVAO.<?= $this->is_DEV ? 'dev' : 'com'; ?></title>

        <? // <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous"> ?>
        <? // <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> ?>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/flick/jquery-ui.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400" rel="stylesheet">
        <link href="<?= base_url(); ?>assets/css/site.css?<?= $this->now_epoch; ?>" rel="stylesheet">

        <? if ( ! empty($current_controller) && file_exists('assets/css/' . $current_controller . '.css')) : ?>

            <link href="<?= base_url() . 'assets/css/' . $current_controller . '.css?' . $this->now_epoch; ?>" rel="stylesheet">

        <? endif; ?>

        <? if (@$current_controller == 'bienvenue' && $this->logged_in && $this->est_enseignant) : ?>

            <link href="<?= base_url(); ?>assets/css/bienvenue_enseignants.css?<?= $this->now_epoch; ?>" rel="stylesheet">

        <? endif; ?>

        <? if (@$current_controller == 'scrutins') : ?>

            <link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet">

        <? endif; ?>

        <? if (@$current_controller == 'evaluations' && @$current_method == 'editeur') : ?>

            <link href="<?= base_url(); ?>assets/css/editeur.css?<?= $this->now_epoch; ?>" rel="stylesheet">

        <? endif; ?>

    </head>

<script>
    var cct                 = "<?= $this->security->get_csrf_hash(); ?>";
    var logged_in           = "<?= $this->logged_in ? 1 : 0; ?>";
    var est_enseignant      = "<?= @$this->est_enseignant; ?>";
    var est_etudiant        = "<?= @$this->est_etudiant; ?>";
    var base_url            = "<?= base_url(); ?>";
    var current_url         = "<?= current_url(); ?>";
    var current_controller  = "<?= @$current_controller; ?>";
    var current_method      = "<?= @$current_method; ?>";
</script>

    <body>

        <? 
        /* --------------------------------------------------------------------
         *
         * NAVIGATION GENERALE (TOUJOURS PRESENTE)
         *
         * -------------------------------------------------------------------- */ ?>

        <? $bg_color = ($this->groupe_id == 0 ? '#0D47A1;' : '#333;'); ?>

        <nav id="navbar-kovao" class="navbar navbar-expand-md navbar-dark fixed-top" style="z-index: 5; background: <?= $bg_color; ?>">
        <div id="navbar" class="container-fluid">

            <a class="navbar-brand" href="https://<?= $this->config->item('sous_domaine') . '.' .  $this->config->item('domaine'); ?>">
                KOVAO
                <? if (@$is_DEV) : ?>
                    <span style="color: crimson; font-weight: 300; font-size: 0.9em"><sup>DEV</sup></span>
                <? endif; ?>
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-contenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar-contenu">

                <ul class="navbar-nav mr-auto">

                    <? 
                    /* ========================================================
                     *
                     * NAVIGATION GAUCHE
                     *
                     * ======================================================== */ ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url(); ?>">
                            Accueil
                        </a>
                    </li>

                    <? if ( ! $this->est_enseignant) : ?>

                        <? if ( ! ($this->current_controller == 'evaluation' && ! empty($this->current_method))) : ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url() . 'evaluation'; ?>">
                                    Évaluation
                                </a>
                            </li>

                        <? endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() . 'consulter'; ?>">
                                Consulter
                            </a>
                        </li>

                    <? endif; // ! est_enseignant ?>  

                    <? if ($this->logged_in) : ?>

                        <? if ($this->est_enseignant) : ?>

                            <? if ($this->appartenance_groupe) : ?>

                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url() . 'resultats'; ?>">
                                        Résultats
                                    </a>
                                </li>

                            <? endif; ?>

                            <? if (@$corrections_en_attente > 0) : ?>

                                <li class="nav-item">
                                    <a style="color: yellow" class="nav-link" href="<?= base_url() . 'corrections'; ?>">Corrections</a>
                                </li>

                            <? endif; ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url() . 'evaluations'; ?>">Évaluations</a>
                            </li>

                            <? if (1 == 2 && $this->enseignant_id == 1) : ?>
                                <? if ($this->current_controller != 'votes') : ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= base_url() . 'votes'; ?>">Votes</a>
                                    </li>
                                <? endif; ?>
                            <? endif; ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url() . 'recherche'; ?>">
                                    <i class="fa fa-search fa-lg"></i>
                                </a>
                            </li>

                        <? endif; // est_enseignant ?>

                    <? endif; // logged_in ?>

                </ul>

                <ul class="navbar-nav">

                    <? 
                    /* ========================================================
                     *
                     * NAVIGATION DROITE
                     *
                     * ======================================================== */ ?>

                    <? if ( ! $this->logged_in) : ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() . 'connexion'; ?>">Connexion</a>
                        </li>

                        <? if ($this->config->item('inscription_permise') && $this->config->item('inscription_permise_etudiant')) : ?>

                            <li class="nav-item">
                                <a class="nav-link" href="https://<?= $this->sous_domaine; ?>.kovao.<?= ($this->is_DEV ? 'dev' : 'com') . '/inscription'; ?>">Inscription</a>
                            </li>

                        <? endif; ?>

                    <? endif; ?> 

                    <? if ($this->logged_in) : ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() . 'profil'; ?>">Profil</a>
                        </li>

                        <? if ($this->est_enseignant) : ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url()  . 'groupes'; ?>">Groupes</a>
                            </li>

                            <? if ($this->appartenance_groupe) : ?>

                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url() . 'configuration'; ?>">Configuration</a>
                                </li>

                            <? endif; ?>

                            <? if (1 == 2) : ?>
                                <li class="nav-item">
                                    <a class="nav-link" target="_blank" href="https://docs.google.com/document/d/1gB2gdlvXzuszN6C3DhrJeLHWjMCv6p_3ZcliIVWIFuU/edit?usp=sharing">
                                        <i class="fa fa-question-circle fa-lg"></i>
                                    </a>
                                </li>
                            <? endif; ?>

                        <? endif; // est_enseignant ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() . 'deconnexion'; ?>">
                                <i class="fa fa-sign-out fa-lg"></i>
                            </a>
                        </li>
    
                    <? endif; // logged_in ?> 

                </ul>

            </div> <!-- .navbar-collapse -->

        </div> <!-- #navbar .container-fluid -->
        </nav>

        <? 
        /* --------------------------------------------------------------------
         *
         * NAVIGATION ECOLE / GROUPE / ENSEIGNANT
         *
         * -------------------------------------------------------------------- */ ?>

        <? $nav2 = FALSE; // Navigation 2 presente? ?>

        <? if ($this->uri->segment(1) == 'erreur') : ?>

            <div></div>
        
        <? elseif ($this->sous_domaine == 'www' && @$current_controller == 'evaluation' && empty($current_method)) : ?>

            <div></div>

        <? elseif ($this->sous_domaine == 'www' && ! $this->logged_in && @$current_controller != 'evaluation') : ?>

            <div></div>

        <? elseif ($this->sous_domaine == 'www' && $this->logged_in && $this->est_etudiant) : ?>

            <div></div>

        <? elseif ($this->uri->segment(1) == 'evaluation' && $this->uri->segment(2) != 'soumission') : ?>

            <? $nav2 = TRUE; ?>

            <nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

                <div id="navbar-2" class="container-fluid">

                    <div class="text-nowrap text-truncate">

                        <? if ( ! empty($ecole) && ! empty($this->groupe_id)) : ?>

                            <?= $ecole['ecole_nom']; ?>
                            <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                        <? endif; ?>

                        <? if ( ! empty($groupe)) : ?> 
                        
                            <? if ($this->groupe_id != 0) : ?>
                                <?= $groupe['groupe_nom']; ?>
                            <? endif; ?>

                        <? endif; ?>

                        <? if ( ! empty($enseignant)) : ?>

                            <? if ( $this->groupe_id != 0) : ?>
                                <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                            <? else : ?>
                                <i class="fa fa-square-o" style="margin-right: 5px"></i>
                            <? endif; ?>
                            <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></a>

                        <? else : ?>

                            <? // C'est un etudiant. ?>
                        
                            <? if ( ! empty($evaluation_details)) : ?>

                                <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                                <?= $evaluation_details['enseignant_prenom'] . ' ' . $evaluation_details['enseignant_nom']; ?></a>

                            <? endif; ?>

                        <? endif; ?>

                    </div>

                </div> <!-- #navbar-2 -->
            </nav>

        <? elseif ($this->uri->segment(1) == 'evaluation' && $this->uri->segment(2) == 'soumission') : ?>

            <? $nav2 = TRUE; ?>

            <nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

                <div id="navbar-2" class="container-fluid">

                    <div class="text-nowrap text-truncate">

                        <? if ( ! empty($ecole) && ! empty($this->groupe_id)) : ?>

                            <?= $ecole['ecole_nom']; ?>
                            <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                        <? endif; ?>

                        <? if ( ! empty($groupe)) : ?> 
                        
                            <? if ($this->groupe_id != 0) : ?>
                                <?= $groupe['groupe_nom']; ?>
                            <? endif; ?>

                        <? endif; ?>

						<? if ( $this->groupe_id != 0) : ?>
							<i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
						<? else : ?>
							<i class="fa fa-square-o" style="margin-right: 5px"></i>
						<? endif; ?>
						<?= @$enseignant['prenom'] . ' ' . @$enseignant['nom']; ?></a>

                    </div>

                </div> <!-- #navbar-2 -->
            </nav>

        <? else : ?>

            <? $nav2 = TRUE; ?>

            <nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

                <div id="navbar-2" class="container-fluid">

                    <div class="text-nowrap text-truncate">

                        <? if ( ! empty($ecole) && $this->groupe_id != 0) : ?>

                            <?= $ecole['ecole_nom']; ?>
                            <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                        <? endif; ?>

                        <? if ( ! empty($groupe) && $this->groupe_id != 0) : ?> 

                            <?= $groupe['groupe_nom']; ?>

                        <? endif; ?>

                        <? if ( ! empty($enseignant) && $this->appartenance_groupe) : ?>

                            <? // C'est un enseignant. ?>

                            <? if ( $this->groupe_id != 0) : ?>
                                <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                            <? else : ?>
                                <i class="fa fa-square-o" style="margin-right: 5px"></i>
                            <? endif; ?>
                            <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></a>

                            <? if ( ! empty($enseignant['semestre_id'])) : ?>

                                <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                                <?= @$this->semestres[$enseignant['semestre_id']]['semestre_code']; ?>

                            <? endif; ?>

                        <? endif; ?>

                    </div>

                </div> <!-- #navbar-2 -->
            </nav>

        <? endif; ?>

        <? 
        /* --------------------------------------------------------------------
         *
         * PAGE PRINCIPALE
         *
         * -------------------------------------------------------------------- */ ?>

        <? if ($nav2) : ?>

            <div style="margin-top: 130px"></div>

        <? else : ?>

            <div style="margin-top: 100px"></div>

        <? endif; ?>

        <? 
        /* --------------------------------------------------------------------
         *
         * FLASH DATA (MESSAGE GENERAL)
         *
         * -------------------------------------------------------------------- */ ?>

        <? if ( ! empty($mg_message)) : ?>

            <div class="row">
                <div class="col-12">

                    <div class="alert alert-<?= $mg_alert; ?>" role="alert" style="margin-left: 25px; margin-right: 25px">
                        <?= $mg_message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="hspace"></div>

                </div> <!-- .col -->
            </div> <!-- .row -->

        <? endif; ?>
