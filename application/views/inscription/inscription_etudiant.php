<?
/* ====================================================================
 *
 * INSCRIPTION POUR LES ETUDIANTS
 *
 * ==================================================================== */ ?>

<link href="<?= base_url() . 'assets/css/inscription.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

<div id="inscription-etudiant">
<div class="container">

	<div class="row">

		<div class="col">

			<h3>
                <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; width: 24px; height: 24px;" fill="#777" class="bi bi-person-fill" viewBox="0 0 16 16">
                  <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
                Inscription pour <span style="color: dodgerblue">étudiants</span>
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
					<a href="" class="btn btn-primary" style="width: 130px">Étudiant</a>
					<a href="<?= base_url() . 'inscription/enseignant'; ?>" class="btn btn-outline-primary" style="width: 130px">Enseignant</a>
				</div>
			</div>

		</div>
	
	</div> <!--- .row -->

    <div class="space"></div>

	<div id="form-box">

        <?= form_open(base_url() . 'inscription/etudiant',
                array('id' => 'demande-inscription'),
                array('status_se' => $status_se)
            ); ?>

            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

            <div class="form-row">
                <div class="col-md-4 mb-1">
                    <label for="validationServer01">Nom :</label>
                    <input name="nom" type="text" class="form-control <?= $errors['nom']; ?>" "validationServer01" placeholder="Nom de famille" value="<?= set_value('nom'); ?>" required>
                    <?= form_error('nom'); ?>
                </div>

                <div class="col-md-4 mb-1">
                    <label for="validationServer02">Prénom :</label>
                    <input name="prenom" type="text" class="form-control <?= $errors['prenom']; ?>" id="validationServer02" placeholder="Prénom" value="<?= set_value('prenom'); ?>" required>
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

            <?
            /* --------------------------------------------------------------------
             *
             * Numero DA (Matricule) de l'ecole du groupe
             *
             * -------------------------------------------------------------------- */ ?>
        
            <? if ($this->groupe_id != 0) : ?>
                <div class="form-row">

                    <div class="col-md-12 mb-1">
                        <label><?= $this->ecole['numero_da_nom'] ?: 'Matricule'; ?> du <?= $this->ecole['ecole_nom']; ?> :</label>
                        <input name="numero_da" type="text" class="form-control col-md-3 <?= @$errors['numero_da']; ?>" 
                               placeholder="<?= ucfirst($this->ecole['numero_da_nom'] ?: 'Matricule'); ?>" value="<?= set_value('numero_da'); ?>" required>
                        <?= form_error('numero_da'); ?>
                    </div>

                </div>
            <? endif; ?>

            <div class="hspace"></div>

            <div class="form-group">
                <label for="inscription-etudiant-courriel">Votre courriel :</label>
                <input name="courriel" type="email" class="form-control col-sm-5 <?= $errors['courriel']; ?>" id="inscription-etudiant-courriel" aria-describedby="emailHelp" placeholder="Courriel" value="<?= set_value('email'); ?>" required>
                <?= form_error('courriel'); ?>
            </div>

            <div class="form-row">

                <div class="col-md-4 mb-1">
                    <label for="InputPassword1">Choisissez un mot de passe :</label>
                    <input name="password1" type="password" class="form-control <?= $errors['password1']; ?>" id="InputPassword1" placeholder="Mot de passe" required>
                    <?= form_error('password1'); ?>
                </div>

                <div class="col-md-4 mb-1">
                    <label for="InputPassword2">Veuillez le confirmer :</label>
                    <input name="password2" type="password" class="form-control <?= $errors['password2']; ?>" id="InputPassword2" placeholder="Confirmation du mot de passe" required>
                    <?= form_error('password2'); ?>
                </div>

            </div>

            <div class="space"></div>

            <button id="demande-envoyer" type="submit" class="btn btn-primary">S'inscrire</button>
            <button id="demande-envoie-en-cours" class="btn btn-primary disabled d-none">
                Inscription en cours....
                <i class="fa fa-circle-o-notch fa-spin" style="margin-left: 5px"></i>
            </button>

        </form>

    </div> <!-- #form-box -->

</div> <!-- .container -->
</div> <!-- #login -->
