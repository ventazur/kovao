<? 
/* ====================================================================
 *
 * BARRE DE NAVIGATION PRINCIPALE (TOUJOURS PRESENTE)
 *
 * ==================================================================== */ ?>

<? $bg_color = ($this->groupe_id == 0 ? '#0D47A1;' : '#333;'); ?>

<? if ($this->is_DEV) $bg_color = '#4A148C'; ?>
<? if ($this->domaine == 'kovao.ca') $bg_color = '#6c000e'; ?>

<nav id="navbar-kovao" class="navbar navbar-expand-md navbar-dark fixed-top" style="z-index: 5; background: <?= $bg_color; ?>">

<div id="navbar" class="container-fluid">

    <?
    /* ----------------------------------------------------------------
     *
     * KOVAO LOGO
     *
     * ---------------------------------------------------------------- */ ?>

    <? /* <a class="navbar-brand" href="https://www.<?= $this->config->item('domaine'); ?>"> */ ?>
	<a class="navbar-brand" href="<?= base_url(); ?>">

		<?= $this->config->item('nom_du_site'); ?>

        <? if (@$is_DEV) : ?>
            <span style="color: crimson; font-weight: 300; font-size: 0.9em"><sup>&beta;</sup></span>
		<? endif; ?>

    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-contenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar-contenu">

<? 
/* ========================================================
 *
 * NAVIGATION GAUCHE
 *
 * ======================================================== */ ?>

<ul class="navbar-nav mr-auto">

<li class="nav-item">

    <? if ($this->est_enseignant && ! $this->appartenance_groupe && $current_controller != 'erreur') : ?>

        <a class="nav-link" href="<?= base_url(); ?>">
            Accueil
        </a>

    <? else : ?>

        <a class="nav-link" href="<?= base_url(); ?>">
            Accueil
        </a>

    <? endif; ?>

</li>

<? 
/* ========================================================
 *
 * COMMUN NON CONNECTE
 *
 * ======================================================== */ ?>

<? if ( ! $this->logged_in) : ?>

	<!--
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'evaluation'; ?>">
            Évaluation
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'consulter'; ?>">
            Consulter
        </a>
	</li>
	-->

<? endif; ?>

<? 
/* ========================================================
 *
 * ETUDIANT
 *
 * ======================================================== */ ?>

<? if ($this->est_etudiant) : ?>

    <? if (1 == 2 && $this->current_controller != 'evaluation') : ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url() . 'evaluation'; ?>">
                Évaluation
            </a>
        </li>
    <? endif; ?>

    <? if ($this->current_controller != 'resultats') : ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url() . 'etu/resultats'; ?>">
                Résultats
            </a>
        </li>
    <? endif; ?>

<? endif; ?>

<? 
/* ========================================================
 *
 * ENSEIGNANT
 *
 * ======================================================== */ ?>

<? if ($this->est_enseignant && $this->appartenance_groupe) : ?>

    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Resultats
     *
     * ------------------------------------------------------------- */ ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'resultats'; ?>">
            Résultats
        </a>
    </li>

    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Corrections
     *
     * ------------------------------------------------------------- */ ?>

    <? if (@$corrections_en_attente > 0) : ?>

        <li class="nav-item">
            <a style="color: yellow; font-weight: 400" class="nav-link" href="<?= base_url() . 'corrections'; ?>">
                Corrections
            </a>
        </li>

    <? endif; ?>

    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Evaluations
     *
     * ------------------------------------------------------------- */ ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'evaluations'; ?>">
            Évaluations
        </a>
    </li>
    
    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Forums
     *
     * ------------------------------------------------------------- */ ?>

    <? if ($this->config->item('forums') && $this->groupe_id != 0) : ?>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url() . 'forums'; ?>">
                Forums
                <? if ($current_controller != 'forums' && @$forums_nouveaux_messages) : ?>
                    <sup><span class="badge" style="background: #FDD835; color: #444;"><?= @$forums_nouveaux_messages; ?></span></sup>
                <? endif; ?>
                <? if ($current_controller != 'forums' && @$forums_nouveaux_commentaires) : ?>
                    <sup><span class="badge" style="background: #E91E63; color: #fff;"><?= @$forums_nouveaux_commentaires; ?></span></sup>
                <? endif; ?>
            </a>
        </li>

    <? endif; ?>

    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Scrutins
     *
     * ------------------------------------------------------------- */ ?>

    <? if ($this->config->item('scrutins') && $this->groupe_id != 0) : ?>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url() . 'scrutins'; ?>">
                Scrutins
                <? if ($current_controller != 'scrutins' && $current_controller != 'scrutin' && @$scrutins_a_voter > 0) : ?>
                    <sup><span class="badge badge-warning"><?= @$scrutins_a_voter; ?></span></sup>
                <? endif; ?>
            </a>
        </li>

    <? endif; ?>

    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Outils
     *
     * ------------------------------------------------------------- */ ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'outils'; ?>">
            Outils
        </a>
    </li>

    <?
    /* -------------------------------------------------------------
     *
     * Enseignant : Recherche (DESACTIVE)
     *
     * ------------------------------------------------------------- */ ?>
    
    <? if (1 == 2) : ?>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url() . 'recherche'; ?>">
                <i class="fa fa-search fa-lg"></i>
            </a>
        </li>

    <? endif; ?>

<? endif; ?>

</ul>

<? 
/* ========================================================
 *
 * NAVIGATION DROITE
 *
 * ======================================================== */ ?>

<ul class="navbar-nav">

<? 
/* ========================================================
 *
 * ETUDIANT OU ENSEIGNANT
 *
 * ======================================================== */ ?>

<? if ($this->est_etudiant || ($this->est_enseignant && $this->appartenance_groupe)) : ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'profil'; ?>">
            Profil
        </a>
    </li>

<? endif; ?>

<? 
/* ========================================================
 *
 * ETUDIANT
 *
 * ======================================================== */ ?>

<? if ($this->est_etudiant) : ?>






<? endif; ?>

<? 
/* ========================================================
 *
 * ENSEIGNANT
 *
 * ======================================================== */ ?>

<? if ($this->est_enseignant) : ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url()  . 'groupes'; ?>">
            Groupes
        </a>
    </li>

    <? if ($this->appartenance_groupe) : ?>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url() . 'configuration'; ?>">
                Configuration
            </a>
        </li>

    <? endif; ?>

<? endif; ?>

<? 
/* ========================================================
 *
 * COMMUN NON CONNECTE
 *
 * ======================================================== */ ?>

<? if ( ! $this->logged_in) : ?>

   <? if ($this->config->item('inscription_permise')) : ?>

        <? if ($this->config->item('inscription_permise_etudiant')) : ?>

            <li id="nav-inscription" class="nav-item">
                <a class="nav-link" href="<?= base_url() . 'inscription/etudiant'; ?>">
                    Inscription
                </a>
            </li>

        <? elseif ($this->config->item('inscription_permise_enseignant')) : ?>

            <li id="nav-inscription-enseignant" class="nav-item">
                <a class="nav-link" href="https://<?= base_url() . 'inscription/enseignant'; ?>">
                    Inscription
                </a>
            </li>

        <? endif; ?>

    <? endif ;?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'connexion'; ?>">
            Connexion
        </a>
    </li>

<? endif; ?>

<? 
/* ========================================================
 *
 * COMMUN CONNECTE
 *
 * ======================================================== */ ?>

<? if ($this->logged_in) : ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url() . 'deconnexion'; ?>">
            <i class="fa fa-sign-out fa-lg"></i>
        </a>
    </li>

<? endif; ?>

</ul>

</div> <!-- .navbar-collapse -->

</div> <!-- #navbar .container-fluid -->

</nav>
