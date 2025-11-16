<?
/* ----------------------------------------------------------------------------
 *
 * Profil etudiant > Compte
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-etudiant-compte">

    <?= form_open(base_url() . 'profil',
            array(),
            array('etudiant_id' => $etudiant['etudiant_id'])
        ); ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre du contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu-titre">

        Mon <span style="font-weight: 400">compte</span>

    </div> <!-- #contenu-titre -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu">

		<div style="font-family: Lato; font-weight: 300; font-size: 0.9em;">

		Votre compte et toutes vos données personnelles seront effacées après 2 ans d'inactivité.

        </div>

    </div> <!-- #profil-contenu -->

</div> <!-- #profil-etudiant-compte -->
