<?
/* ----------------------------------------------------------------------------
 *
 * Forums > Publier
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="forums-publier">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>
        <i class="fa fa-plus-circle" style="margin-right: 5px; color: dodgerblue"></i>
        Publier un nouveau message dans les forums
    </h4>

    <?
    /* ------------------------------------------------------------------------
     *
     * Ecrire le nouveau message
     *
     * ------------------------------------------------------------------------ */ ?>

    <div class="space"></div>

        <?= form_open(base_url() . $current_controller . '/publier',
                array(),
                array()
            ); ?>
    
        <div id="message">

            <div class="form-group">
                <label for="message-titre">Titre :</label>
                <input id="message-titre" name="titre" type="text" class="form-control <?= @$errors['titre']; ?>" placeholder="Titre du message" value="<?= set_value('titre'); ?>" required>
                <?= form_error('titre'); ?>
            </div>

            <div class="form-group mt-3">
                <label for="message-contenu">Message :</label>
                <textarea id="message-contenu" name="message" class="form-control <?= @$errors['message']; ?>" rows="5" placeholder="Votre message" value="<?= set_value('message'); ?>" required></textarea>
                <?= form_error('message'); ?>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="permettre_commentaires" id="message-permettre-commentaires" checked>
                <label class="form-check-label" for="message-permettre-commentaires">
                    Permettre les commentaires
                </label>
            </div>

        </div> <!-- #message -->

        <div class="row mt-3">
            <div class="col-6">
                <button id="poster-message" type="submit" class="btn btn-sm btn-primary spinnable">
                    <i class="fa fa-plus-circle" style="margin-right: 5px"></i>
                    Publier votre message
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </button>
            </div>
            <div class="col-6" style="text-align: right">
                <a href="<?= base_url() . $current_controller; ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fa fa-times-circle" style="margin-right: 5px"></i>
                    Annuler
                </a>
            </div>
        </div>

    </form>

    <?
    /* ------------------------------------------------------------------------
     * 
     * Previsualisation du message
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="previsualisation" class="d-none">

        <div class="tspace"></div>

        <div style="font-family: Lato; font-weight: 300; font-size: 1em">Prévisualisation du message :</div>

        <div class="hspace"></div>

        <div id="message-previsualisation" style="padding: 15px; border: 1px solid #E8EAF6">

        </div>

    </div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div>
