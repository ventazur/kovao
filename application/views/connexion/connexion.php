<div id="login">
<div class="container">

    <h3>Connexion</h3>

    <div class="space"></div>

    <p class="d-none">
        <i class="fa fa-exclamation-circle" style="color: crimson"></i> Les étudiants n'ont pas besoin de se connecter pour remplir les évaluations.
    </p>

    <div class="alert alert-danger <?= ! empty($message_alerte) ? '' : 'd-none'; ?>" style="margin-bottom: 35px" role="alert">
        <i class="fa fa-exclamation-circle"></i> <?= $message_alerte; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <?= form_open(base_url() . 'connexion'); ?>

        <div class="form-group">
            <label for="InputEmail1">Courriel</label>
            <input name="email" type="email" class="form-control col-sm-5 <?= @$errors['email']; ?>" id="InputEmail1" aria-describedby="emailHelp" placeholder="Courriel" value="<?= set_value('email'); ?>">
            <?= form_error('email'); ?>
        </div>

        <div class="form-group mt-4">
            <label for="InputPassword1">Mot-de-passe</label>
            <input name="password" type="password" class="form-control col-sm-5 <?= @$errors['password']; ?>" id="InputPassword1" placeholder="Mot-de-passe">
            <?= form_error('password'); ?>
        </div>

        <div class="space"></div>

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <div class="mt-4">

        <div class="hspace"></div>

        <a href="<?= base_url() . $current_controller . '/oublie'; ?>">
            <i class="fa fa-info-circle"></i> J'ai oublié mon mot-de-passe.
        </a>

    </div>

</div> <!-- .container -->
</div> <!-- #login -->
