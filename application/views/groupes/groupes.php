<?
/* ----------------------------------------------------------------------------
 *
 * Les groupes de l'enseignant
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="groupes">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Groupes</h4>

        <div class="space"></div>

        <div class="row">
            <div class="col">

                <? if (1 == 2 && $this->est_enseignant && $this->config->item('inscription_permise') && $this->config->item('inscription_permise_enseignant')) : ?>
                    <a class="btn btn-primary mb-2 m-sm-0" href="<?= base_url() . 'inviter'; ?>">
                        <i class="fa fa-user-plus" style="margin-right: 3px"></i>
                        Inviter un enseignant
                    </a>
                <? endif; ?>

                <? if ($this->est_enseignant && $permission_creer_groupe) : ?>
                    <a class="btn btn-outline-primary" href="<?= base_url() . 'groupes/creer'; ?>">
                        <i class="fa fa-plus-circle" style="margin-right: 3px"></i>
                        Créer un nouveau groupe
                    </a>
                <? endif; ?>

                <? if (1 == 2 && $this->enseignant['privilege'] >= 50) : ?>
                    <a class="btn btn-outline-secondary mt-2 m-sm-0" href="<?= base_url() . 'groupes/lister'; ?>">
                        <i class="fa fa-list" style="margin-right: 3px"></i>
                        Lister les groupes
                    </a>
                <? endif; ?>

            </div>

            <div class="col" style="text-align: right">
                <? if ($this->est_enseignant && $this->enseignant['privilege'] > 90) : ?>
                    <div class="btn-group">
                        <a class="btn btn-outline-secondary" href="<?= base_url() . 'adm/systeme'; ?>">
                            <i class="fa fa-cog" style="color: crimson;"></i>
                        </a>
                        <a class="btn btn-outline-secondary" href="<?= base_url() . 'adm/groupe'; ?>">
                            <i class="fa fa-cog" style="color: dodgerblue;"></i>
                        </a>
                    </div>
                <? endif; ?>
            </div>
        </div> <!-- .row -->

        <div class="tspace"></div>

        <i class="fa fa-lightbulb-o" style="color: dodgerblue; margin-right: 5px"></i> Pour ajouter un groupe, vous devez le <strong>créer</strong>, ou naviguer sur la page du groupe puis <strong>demander à le joindre</strong>.

        <div class="tspace"></div>

        <? foreach($groupes as $g_id => $g) : ?>

            <div class="groupe-item <?= $this->groupe_id == $g_id ? 'courant' : ''; ?>">
                <div class="row">

                    <div class="col-8">

                        <a class="btn" href="https://<?= $g['sous_domaine']; ?>.kovao.<?= ($this->is_DEV ? 'dev' : 'com'); ?>">
                            <? if ($g['groupe_id'] != 0) : ?>
                                <?= $g['ecole_nom']; ?> <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i>
                            <? endif; ?>

                            <?= $g['groupe_nom']; ?>

                            <? if ($this->groupe_id == $g_id) : ?>
                                <span class="badge badge-pill badge-dark" style="margin-left: 10px; padding-top: 4px;">ACTIF</span>
                            <? endif; ?>
                        </a>

                    </div>
                    <div class="col-4" style="text-align: right; padding-top: 4px">
            
                            <? if ($g_id == 0 || $g['niveau'] >= $this->config->item('niveaux')['admin_groupe']) : ?>
                                <a class="btn btn-sm btn-outline-danger mb-2 m-sm-0" href="https://<?= $g['sous_domaine']; ?>.kovao.<?= ($this->is_DEV ? 'dev' : 'com') . '/groupe/gerer'; ?>">
                                    Gérer
                                </a>
                            <? endif; ?>

                            <? if ($this->groupe_id != $g_id) : ?>
                                <a class="btn btn-sm btn-outline-primary" href="https://<?= $g['sous_domaine']; ?>.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>">
                                    Aller
                                    <i class="fa fa-angle-right" style="margin-left: 5px"></i>
                                </a>
                            <? endif; ?>

                    </div>
                </div> <!-- .row -->

            </div>

        <? endforeach; ?>

		<? if ( ! empty($demandes)) : ?>

		    <div class="space"></div>

			<h4>Demande<?= count($demandes) > 1 ? 's' : ''; ?> en attente d'approbation</h4>

			<div class="space"></div>
		
			<? foreach($demandes as $g) : ?>

				<div class="groupe-item" style="background: #ddd; border-color: #ddd;">
					<div class="row"`>

						<div class="col-8">

							<a class="btn" href="https://<?= $g['sous_domaine']; ?>.kovao.<?= ($this->is_DEV ? 'dev' : 'com'); ?>" style="color: #777">
								<? if ($g['groupe_id'] != 0) : ?>
									<?= $g['ecole_nom']; ?> <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i>
								<? endif; ?>

								<?= $g['groupe_nom']; ?>
							</a>

						</div>
						<div class="col-4" style="text-align: right">
					
							<a class="btn btn-primary" href="https://<?= $g['sous_domaine']; ?>.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>">Aller</a>

						</div>
					</div> <!-- .row -->
				</div>

			<? endforeach; ?>

		<? endif; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #groupes -->
