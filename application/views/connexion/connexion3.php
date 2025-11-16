<?
/* ----------------------------------------------------------------------------
 *
 * Connexion
 *
 * version 2 : 2020-06-14 (base sur Bootstrap)
 * version 3 : 2024-09-30
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="connexion">
<div class="container">

    <h3>Connexion</h3>

    <?= form_open(base_url() . 'connexion', array('class' => 'form-signin')); ?>

		<? if (isset($message_alerte) && ! empty($message_alerte)) : ?>

			<div class="alert alert-danger <?= ! empty($message_alerte) ? '' : 'd-none'; ?>" style="margin-bottom: 25px; font-size: 0.9em;" role="alert">
				<i class="fa fa-exclamation-circle"></i> <?= $message_alerte; ?>
			</div>

		<? endif; ?>

        <label for="courriel" class="sr-only">Courriel</label>
        <input name="email" type="email" class="form-control" id="courriel" placeholder="Courriel" value="<?= set_value('email'); ?>">

        <label for="motdepasse" class="sr-only">Mot-de-passe</label>
        <input name="password" type="password" class="form-control" id="motdepasse" placeholder="Mot-de-passe">

        <button id="se-connecter" class="btn btn-lg btn-primary btn-block" type="submit" style="font-size: 1em">
			Se connecter
			<i class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px" aria-hidden="true"></i>
        </button>

        <div class="mt-4" style="font-size: 0.8em">
            <div class="row">
                <div class="col" style="text-align: left">
                    <a href="<?= base_url() . $current_controller . '/oublie'; ?>">
                        J'ai oublié mon mot-de-passe.
                    </a>
                </div>
                <div class="col" style="text-align: right">
                    <a href="<?= base_url() . 'inscription/etudiant'; ?>">
                        Je veux m'inscrire.
                    </a>
                </div>
            </div>
        </div>
    </form>

</div> <!-- .container -->
</div> <!-- #connexion -->
