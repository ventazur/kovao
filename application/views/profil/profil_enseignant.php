<?
/* ----------------------------------------------------------------------------
 *
 * Profil enseignant
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-enseignant">

<div class="container-fluid">
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h3>Mon profil</h3>

    <?
    /* ------------------------------------------------------------------------
     *
     * Menu
     *
     * Identite | Mot-de-passe | Parametres
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-menu">

        <div class="row no-gutters">
            <a class="col spinnable <?= $methode == 'identite' ? 'actif' : ''; ?>" href="<?= base_url() . 'profil'; ?>">
                Identité
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 3px"></i>
            </a>
            <a class="col spinnable <?= $methode == 'motdepasse' ? 'actif' : ''; ?>" href="<?= base_url() . 'profil/motdepasse'; ?>">
                Mot-de-passe
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 3px"></i>
            </a>
            <a class="col spinnable <?= $methode == 'parametres' ? 'actif' : ''; ?>" href="<?= base_url() . 'profil/parametres'; ?>">
                Paramètres
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 3px"></i>
            </a>
            <div class="col d-none d-lg-block"></div>
            <div class="col d-none d-lg-block"></div>
            <div class="col d-none d-lg-block"></div>

        </div> <!-- .row -->

    </div>

    <?
    /* ------------------------------------------------------------------------
     *
     * Flash Messages
     *
     * ------------------------------------------------------------------------ */ ?>

    <? if ( ! empty($flash_message)) : ?>

        <div class="alert alert-<?= $flash_message['alert']; ?> alert-dismissible fade show mt-4" role="alert" style="margin-bottom: 25px;">
            <?= $flash_message['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

    <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Partials
     *
     * ------------------------------------------------------------------------ */ ?>

    <? $this->load->view('profil/profil_enseignant_' . $methode, $this->data); ?>


    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>
</div> <!-- .row -->

</div> <!-- /.container -->
</div> <!-- #profil-enseignant -->
