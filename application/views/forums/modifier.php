<?
/* ----------------------------------------------------------------------------
 *
 * Forums > Modifier 
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="forums-modifier">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>
        <i class="fa fa-plus-circle" style="margin-right: 5px; color: dodgerblue"></i>
        Modifier votre message
    </h4>

    <?
    /* ------------------------------------------------------------------------
     *
     * Modifier le message
     *
     * ------------------------------------------------------------------------ */ ?>

    <div class="space"></div>

    <?= form_open(base_url() . $current_controller . '/modifier/' . $message['message_id'],
            array(),
            array()
        ); ?>

    <div id="message">

		<div class="form-group">
            <label for="validationServer01">Titre :</label>
            <input name="titre" type="text" class="form-control <?= @$errors['titre']; ?>" "validationServer01" placeholder="Votre titre" value="<?= $message['titre']; ?>" required>
            <?= form_error('titre'); ?>
        </div>

        <div class="form-group mt-4">
            <label for="InputMessage">Message :</label>
            <textarea name="message" class="form-control <?= @$errors['message']; ?>" id="InputMessage" rows="5" placeholder="Votre message" value="<?= set_value('message'); ?>" required><?= _html_edit($message['contenu']); ?></textarea>
            <?= form_error('message'); ?>
        </div>

        <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="permettre_commentaires" id="message-permettre-commentaires" <?= $message['permettre_commentaires'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="message-permettre-commentaires">
                Permettre les commentaires
            </label>
        </div>

    </div>

        <div class="row mt-3">
            <div class="col-6">

                <button id="modifier-message" type="submit" class="btn btn-sm btn-primary spinnable">
                    <i class="fa fa-save" style="margin-right: 3px"></i>
                    Sauvegarder vos changements
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </button>

                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url() . 'forums/lire/' . $message['message_id']; ?>">
                    <i class="fa fa-times-circle" style="margin-righ: 5px"></i>
                    Annuler
                </a>
                
            </div>
            <div class="col-6" style="text-align: right">

                <? if (($message['ajout_epoch'] + $this->config->item('forums_effacement_delai')) > $this->now_epoch) : ?>

                    <a href="<?= base_url() . $current_controller . '/effacer/message/' . $message['message_id']; ?>" class="btn btn-sm btn-outline-danger">
                        <i class="fa fa-trash" style="margin-right: 5px"></i>
                        Effacer ce message
                    </a>

                <? endif; ?>

            </div>
        </div>
    </form>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div>
