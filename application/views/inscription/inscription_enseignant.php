<?
/* ====================================================================
 *
 * INSCRIPTION POUR LES ENSEIGNANTS
 *
 * ==================================================================== */ ?>

<link href="<?= base_url() . 'assets/css/inscription.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

<div id="inscription-enseignant">
<div class="container">

	<div class="row">

		<div class="col">

			<h3>
                <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -8px; width: 24px; height: 24px;" fill="#777" class="bi bi-person-badge" viewBox="0 0 16 16">
                  <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0h-7zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492V2.5z"/>
                </svg>
				Inscription pour <span style="color: crimson">enseignants</span>
			</h3>

		</div>

		<?
		/* ----------------------------------------------------------------
		 *
		 * Choix entre enseignant et etudiant
		 *
		 * ---------------------------------------------------------------- */ ?>
	
		<div class="col" style="text-align: right">	

			<div id="choix-enseignant-etudiant">
				<div class="btn-group mr-2" role="group">
					<a href="<?= base_url() . 'inscription/etudiant'; ?>" class="btn btn-outline-primary" style="width: 130px">Étudiant</a>
					<a class="btn btn-primary" style="width: 130px">Enseignant</a>
				</div>
			</div>

		</div>
	
	</div> <!--- .row -->

    <div class="space"></div>

	<div id="form-box">

        <?= form_open(base_url() . 'inscription/enseignant',
                array('id' => 'demande-inscription'),
				array('status_se' => $status_se)
			); ?>

            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

			<div class="form-row">
				<div class="col-md-4 mb-1">
					<label for="enseignant-nom">Nom :</label>
					<input name="nom" type="text" class="form-control <?= $errors['nom']; ?>" id="enseignant-nom" placeholder="Nom de famille" value="<?= set_value('nom'); ?>" required>
					<?= form_error('nom'); ?>
				</div>

				<div class="col-md-4 mb-1">
					<label for="enseignant-prenom">Prénom :</label>
					<input name="prenom" type="text" class="form-control <?= $errors['prenom']; ?>" id="enseignant-prenom" placeholder="Prénom" value="<?= set_value('prenom'); ?>" required>
					<?= form_error('prenom'); ?>
				</div>

			</div>

			<div class="hspace"></div>

			<div class="form-row d-none">
				<div class="col-md-2 mb-1">
					<label>Genre : </label>
					<select name="genre" class="custom-select">
						<option value="X" selected>X</option>
						<option value="M">Masculin</option>
						<option value="F">Féminin</option>
					</select>
				</div>
			</div>

			<div class="hspace"></div>

			<div class="form-group">
				<label for="enseignant-courriel">Votre courriel :</label>
				<input name="courriel" type="email" class="form-control col-sm-5 <?= $errors['courriel']; ?>" id="enseignant-courriel" placeholder="@clg.qc.ca" value="">
				<?= form_error('courriel'); ?>
			</div>

			<div class="form-row">

				<div class="col-md-4">
					<label for="enseignant-mdp1">Choisissez un mot de passe :</label>
					<input name="password1" type="password" class="form-control <?= $errors['password1']; ?>" id="enseignant-mdp1" placeholder="Mot de passe" required>
					<?= form_error('password1'); ?>
				</div>

				<div class="col-md-4">
					<label for="enseignant-mdp2">Veuillez le confirmer :</label>
					<input name="password2" type="password" class="form-control <?= $errors['password2']; ?>" id="enseignant-mdp2" placeholder="Confirmation du mot de passe" required>
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

			<button id="demande-envoyer" type="submit" class="btn btn-primary spinnable">S'inscrire comme enseignant</button>
            <button id="demande-envoie-en-cours" class="btn btn-primary disabled d-none">
				Inscription en cours....
                <i class="fa fa-circle-o-notch fa-spin" style="margin-left: 5px"></i>
            </button>

		</form>

	</div> <!-- #form-box -->

</div> <!-- .container -->
</div> <!-- #login -->
