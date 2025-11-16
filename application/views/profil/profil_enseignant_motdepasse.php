<?
/* ----------------------------------------------------------------------------
 *
 * Profil enseignant > Mot-de-passe
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-enseignant-motdepasse">

    <?= form_open(base_url() . 'profil/motdepasse',
            array(),
            array('enseignant_id' => $enseignant['enseignant_id'])
        ); ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre du contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu-titre">

        Modifier mon <span style="font-weight: 400">mot-de-passe</span>

    </div> <!-- #contenu-titre -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu">

        <div class="form-row">

            <div class="col-md-4">
                <label for="InputPassword0">Mot de passe actuel :</label>
                <input name="password0" type="password" class="form-control <?= @$errors['password0']; ?>" id="InputPassword0" placeholder="Mot-de-passe actuel">
                <?= form_error('password0'); ?>
            </div>

        </div>

        <div class="form-row">

            <div class="col-md-4 mt-5">
                <label for="InputPassword1">Choisissez un nouveau mot de passe :</label>
                <input name="password1" type="password" class="form-control <?= @$errors['password1']; ?>" id="InputPassword1" placeholder="Nouveau mot de passe">
                <?= form_error('password1'); ?>
            </div>

        </div>

        <div class="form-row">

            <div class="col-md-4 mt-3 mb-2">
                <label for="InputPassword2">Veuillez le confirmer :</label>
                <input name="password2" type="password" class="form-control <?= @$errors['password2']; ?>" id="InputPassword2" placeholder="Confirmation du mot de passe">
                <?= form_error('password2'); ?>
            </div>

        </div>

    </div> <!-- #profil-contenu -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Fin du contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu-fin">

        <div>
            <button type="submit" id="sauvegarder-profil" class="btn btn-success">
                <i class="fa fa-save" style="margin-right: 3px"></i> 
                Changer votre mot-de-passe
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
            </button>
            <a href="<?= current_url(); ?>" class="btn btn-outline-danger mt-3 mt-sm-0">
                <i class="fa fa-times" style="margin-right: 3px"></i> 
                Annuler
            </a>
        </div>

    </div> <!-- #profil-contenu-fin -->
    
    </form>

</div> <!-- #profil-enseignant-motdepasse -->
