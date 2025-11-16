<div id="login">
<div class="container">

    <h3>Inscription</h3>

    <div class="space"></div>

    <p>
        <i class="fa fa-exclamation-circle" style="color: crimson"></i> Les inscriptions sont réservées aux <strong>enseignants</strong>.
    </p>

    <div class="space"></div>

    <?= form_open(base_url() . 'inscription',
            array(),
            array('status_se' => $status_se)
        ); ?>

		<div class="form-row">
			<div class="col-md-4 mb-2">
				<label for="validationServer01">Nom :</label>
				<input name="nom" type="text" class="form-control <?= $errors['nom']; ?>" "validationServer01" placeholder="Nom de famille" value="<?= set_value('nom'); ?>" required>
            	<?= form_error('nom'); ?>
			</div>

			<div class="col-md-4 mb-2">
				<label for="validationServer02">Prénom :</label>
			  	<input name="prenom" type="text" class="form-control <?= $errors['prenom']; ?>" id="validationServer02" placeholder="Prénom" value="<?= set_value('prenom'); ?>" required>
            	<?= form_error('prenom'); ?>
			</div>

        </div>

        <div class="space"></div>

        <div class="form-row">
            <div class="col-md-1 mb-2">
                <label>Genre : </label>
                <select name="genre" class="custom-select">
                    <option value="X" selected>X</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>
            </div>
        </div>

        <div class="space"></div>

        <div class="form-group">
            <label for="InputEmail1">Quel est votre courriel ?</label>
            <input name="email" type="email" class="form-control col-sm-5 <?= $errors['email']; ?>" id="InputEmail1" aria-describedby="emailHelp" placeholder="Courriel" value="<?= set_value('email'); ?>" required>
            <small class="form-text text-muted">
                <i class="fa fa-exclamation-circle" style="color: #aaa; margin-top: 7px"></i> Ceci doit être votre courriel de votre institution d'enseignement.
            </small>
            <?= form_error('email'); ?>
        </div>

        <div class="hspace"></div>

		<div class="form-row">

            <div class="col-md-4 mb-2">
                <label for="InputPassword1">Choisissez un mot de passe :</label>
                <input name="password1" type="password" class="form-control <?= $errors['password1']; ?>" id="InputPassword1" placeholder="Mot de passe" required>
                <?= form_error('password1'); ?>
            </div>

            <div class="col-md-4 mb-2">
                <label for="InputPassword2">Veuillez le confirmer :</label>
                <input name="password2" type="password" class="form-control <?= $errors['password2']; ?>" id="InputPassword2" placeholder="Confirmation du mot de passe" required>
                <?= form_error('password2'); ?>
            </div>

        </div>

        <? if ($this->config->item('inscription_code')) : ?>

            <div class="space"></div>

            <div class="form-group">
                <label for="InscriptionCode">Code d'inscription donné aux enseignants</label>
                <input name="code" type="text" class="form-control col-sm-5 <?= $errors['code']; ?>" id="InscriptionCode" placeholder="Code d'inscription" required>
                <?= form_error('code'); ?>
            </div>

        <? endif; ?>

        <div class="tspace"></div>

        <button id="demande-envoyer" type="submit" class="btn btn-primary">S'inscrire</button>
        <button id="demande-envoie-en-cours" type="submit" class="btn btn-primary disabled d-none">Inscription en cours....<i class="fa fa-circle-o-notch fa-spin" style="margin-left: 10px"></i></button>

    </form>

</div> <!-- .container -->
</div> <!-- #login -->
