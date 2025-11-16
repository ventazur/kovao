
<script src="<?= base_url() . 'assets/js/bienvenue_enseignants.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="bienvenue-enseignants-sans-appartenance">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>
        <?= $this->ecole['ecole_nom']; ?> 
        <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px; color: #aaa"></i> 
        <?= $this->groupe['groupe_nom']; ?>
    </h4>

    <div class="tspace"></div>

	<?
	// ----------------------------------------------------------------------
	//
	// L'enseignant n'est PAS membre de ce groupe.
	// 
	// ---------------------------------------------------------------------- ?>

    <? if (empty($enseignant_groupe)) : ?>

        <div class="alert alert-secondary" style="border-radius: 0; font-weight: 300; background: #eee;">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i> 
            Vous n'êtes pas membre de ce groupe.
        </div>

        <?
        // ----------------------------------------------------------------------
        //
        // Ce groupe n'accepte PAS les nouveaux membres
        // 
        // ---------------------------------------------------------------------- ?>

        <? if ( ! $groupe['inscription_permise']) : ?>
        
            <div class="alert" style="border: 1px solid #ddd; border-radius: 0; font-weight: 300">
                <i class="fa fa-info-circle" style="margin-right: 5px; color: #ccc"></i>
                Ce groupe n'accepte pas les nouveaux membres.
            </div>

        <? endif; ?>

        <div class="hspace"></div>

        <div class="btn" style="margin-left: -10px; margin-right: -10px">Vous pouvez commencer dans votre groupe</div>
        <a class="btn btn-sm btn-primary" href="<?= 'https://www.kovao.' . ($this->is_DEV ? 'dev' : 'com'); ?>">
            Personnel
            <i style="margin-left: 5px" class="fa fa-angle-right"></i>
        </a>

	<?
	// ----------------------------------------------------------------------
	//
	// L'enseignant est membre de ce groupe mais un probleme est survenu.
	// 
	// ---------------------------------------------------------------------- ?>

    <? elseif(array_key_exists('actif', $enseignant_groupe) && ! $enseignant_groupe['actif']) : ?>

        <div class="tspace"></div>

        <div class="alert alert-dark" style="font-weight: 300">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i> 
            Votre accès à ce groupe a été désactivé.
        </div>

        <div class="space"></div>

        <strong>Solution :</strong>

        <div class="space"></div>

        Veuillez contacter l'administrateur de ce groupe pour rétablir votre accès.

    <? elseif(array_key_exists('niveau', $enseignant_groupe) && $enseignant_groupe['niveau'] < 1) : ?>

        <div class="tspace"></div>

        <div class="alert alert-dark" style="font-weight: 300">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i> 
            Votre niveau d'accès est inférieur au minimum requis pour accéder ce groupe.
        </div>

        <div class="space"></div>

        <strong>Solution :</strong>

        <div class="space"></div>

        Veuillez contacter l'administrateur de ce groupe pour rétablir votre niveau d'accès.

    <? else : ?>

        <div class="alert alert-danger" style="font-weight: 300">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i> 
            Une erreur <strong>ERR7109</strong> s'est produite.
        </div>

    <? endif; ?>

	<?
	// ----------------------------------------------------------------------
	//
	// Afficher la demande en cours
	// 
	// ---------------------------------------------------------------------- ?>

	<? if ( ! empty($demande)) : ?>

        <div class="tspace"></div>

		<? if (empty($demande['acceptee']) && empty($demande['refusee'])) : ?>

			<div class="alert alert-warning" style="font-weight: 300">
				<i class="fa fa-hourglass-half" style="margin-right: 5px; color: #777"></i>
                Votre demande est en attente d'approbation.
			</div>

		<? elseif ($demande['refusee']) : ?>

			<div class="alert alert-danger" style="font-weight: 300">
				<i class="fa fa-times-circle" style="margin-right: 5px; color: crimson"></i>
				Votre demande pour joindre ce groupe a été refusée.
			</div>

		<? endif; ?>

    <? else : ?>

	<? endif; ?>

	<?
	// ----------------------------------------------------------------------
	//
	// Formulaire pour joindre ce groupe
	// 
	// ---------------------------------------------------------------------- ?>

    <? if (empty($enseignant_groupe) && empty($demande) && $this->groupe['inscription_permise']) : ?>

    	<div class="tspace"></div>

        <div id="demande-joindre" style="border: 1px solid #BBDEFB; background-color: #E3F2FD; padding: 25px;">

            <span style="color: dodgerblue; font-weight: 700;">Joindre ce groupe</span>

            <div class="hspace"></div>

            <span style="font-weight: 300">
                Si vous demandez à joindre ce groupe, votre nom, prénom et courriel seront partagés avec ses membres.
                <br />
                L'administrateur du groupe devra ensuite vous approuver pour y avoir accès.
            </span>

            <div class="hspace"></div>

            <?= form_open(); ?>
                <? if ($this->groupe['inscription_code']) : ?>
                    <div class="row no-gutters">
                        <div class="form-group">
                            <label for="code-inscription">Code d'inscription : </label>
                            <input type="password" class="form-control <?= $errors['code-inscription'] ? 'is-invalid' : ''; ?>" name="code-inscription" id="code-inscription">
                            <?= form_error('code-inscription'); ?>
                            <? if (array_key_exists('code-erreur', $errors)) : ?>
                                <small style="color: crimson;">Le code d'inscription est invalide.</small>
                            <? endif; ?>
                        </div>
                    </div>
                <? endif; ?>

                <a class="btn btn-sm btn-primary mt-2 spinnable"
                href="https://<?= $this->sous_domaine; ?>.kovao.<?= ($this->is_DEV ? 'dev' : 'com'); ?>/groupes/joindre">
                    <i class="fa fa-plus-circle" style="margin-right: 5px"></i> 
                    Joindre	ce groupe
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </a>

            </form>

        </div> <!-- #demande-joindre --> 

	<? endif; ?>

</div> <!-- .col-sm-12 -->
</div> <!-- .col-xl-1 -->

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
