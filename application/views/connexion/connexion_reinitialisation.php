<div id="login">
<div class="container">

    <h3>Réinitialisation de votre mot-de-passe</h3>

    <div class="space"></div>

    <?= form_open(base_url() . 'connexion/reinitialisation/' . $clef, '', array('status_se' => $status_se)); ?>

        <div class="hspace"></div>

		<div class="form-row">

            <div class="col-md-4 mb-2">
                <label for="InputPassword1">Choisissez un nouveau mot de passe :</label>
                <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="Mot de passe" required>
                <?= form_error('password1'); ?>
            </div>

            <div class="col-md-4 mb-2">
                <label for="InputPassword2">Veuillez le confirmer :</label>
                <input name="password2" type="password" class="form-control"  id="InputPassword2" placeholder="Confirmation du mot de passe" required>
                <?= form_error('password2'); ?>
            </div>

        </div>

        <div class="dspace"></div>

        <button type="submit" class="btn btn-primary">Réinitialiser</button>
        </form>

</div> <!-- .container -->
</div> <!-- #login -->
