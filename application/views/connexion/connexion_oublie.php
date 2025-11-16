<div id="login">
<div class="container">

    <h3>Demande de réinitialisation de votre mot-de-passe</h3>

    <? if ( ! empty($flash_message)) : ?>

        <div class="alert alert-<?= $flash_message['alert']; ?> alert-dismissible fade show" role="alert" style="margin-top: 30px; margin-bottom: 0px">
            <?= $flash_message['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

    <? else : ?>


    <? endif; ?>

    <div class="tspace"></div>

    <p>
        <i class="fa fa-info-circle" style="color: dodgerblue"></i> Vous recevrez un courriel avec un lien pour réinitialiser votre mot-de-passe.
    </p>

    <div class="space"></div>

    <?= form_open(base_url() . $current_controller . '/oublie'); ?>

        <div class="form-group">
            <input name="courriel" type="email" class="form-control col-sm-5 <?= @$errors['email']; ?>" id="InputEmail1" aria-describedby="emailHelp" placeholder="Votre courriel" value="<?= set_value('courriel'); ?>">
            <?= form_error('courriel'); ?>
        </div>

        <div class="space"></div>

        <button id="demande-envoyer" type="submit" class="btn btn-primary">Envoyer une demande de réinitilisation</button>
        <button id="demande-envoie-en-cours" type="submit" class="btn btn-primary disabled d-none">Le courriel est en cours d'envoi...<i class="fa fa-circle-o-notch fa-spin" style="margin-left: 10px"></i></button>
    </form>

</div> <!-- .container -->
</div> <!-- #login -->
