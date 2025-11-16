<div id="outil-empreinte">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Outils <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 7px"></i> Vérification d'une évaluation envoyée</h4>

        <div class="space"></div>

        <? if ( ! empty($alerte) && array_key_exists('status', $alerte)) : ?>

            <div class="hspace"></div>

            <? if ($alerte['status'] == 'erreur') : ?>

                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-circle" style="margin-right: 10px"></i> 
                    La référence <strong><?= $alerte['reference']; ?></strong> et l'empreinte <strong><?= $alerte['empreinte']; ?></strong>
                    ne correspondent pas à une combinaison valide.
                </div>

            <? endif; ?>

            <? if ($alerte['status'] == 'valide') : ?>

                <div class="alert alert-success" role="alert" style="border-radius: 0">
                    <i class="fa fa-check-circle" style="color: limegreen; margin-right: 10px"></i> 
                    La référence <strong><?= $alerte['reference']; ?></strong> et l'empreinte <strong><?= $alerte['empreinte']; ?></strong>
                    correspondent à une combinaison valide.
                </div>

                <? if ( ! empty($soumission)) : ?>

                    <div style="border: 1px solid #C3E6CB; font-family: Lato; font-weight: 300; margin-top: -17px; margin-bottom: 15px; padding: 10px; background: #f7f7f7">
                        <table class="table table-sm table-borderless" style="margin: 0;">
                            <tr>
                                <td style="width: 250px">Cours :</td>
                                <td><?= json_decode($soumission['cours_data'])->cours_nom; ?> (<?= json_decode($soumission['cours_data'])->cours_code; ?>)</td>
                            </tr>
                            <tr>
                                <td>Titre :</td>
                                <td><?= json_decode($soumission['evaluation_data'])->evaluation_titre; ?></td>
                            </tr>
                            <tr>
                                <td>Prénom et Nom :</td>
                                <td><?= $soumission['prenom_nom']; ?></td>
                            </tr>
                            <tr>
                                <td>Date de soumission :</td>
                                <td><?= $soumission['soumission_date']; ?></td>
                            </tr>
                            <tr>
                                <td>Référence :</td>
                                <td>
                                    <a href="<?= base_url() . 'consulter/' . $soumission['soumission_reference']; ?>">
                                        <?= $soumission['soumission_reference']; ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Empreinte :</td>
                                <td><?= $soumission['empreinte']; ?></td>
                            </tr>
                            <tr>
                                <td>Enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?></td>
                                <td><?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></td>
                            </tr>
                        </table>
                    </div>

                <? endif; ?>

            <? endif; ?>

            <div class="space"></div>

        <? endif; ?>

        <div clsss="col-12" style="background: #444; padding: 10px; border-radius: 3px 3px 0 0; font-family: Lato; font-weight: 300; color: #eee;">
            Vérification de la validité d'une référence et de son empreinte
        </div>

        <div id="numerique" style="border: 1px solid #ccc; border-top: 0; border-radius: 0 0 3px 3px; padding: 20px; padding-top: 20px; background: #f7f7f7">

            <div class="hspace"></div>

			<?= form_open(NULL, 
					array('id' => 'form-numerique')
				); ?>
				<table>

					<tr>
						<td colspan="2">
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">Référence : </div>
								</div>
								<input name="reference" id="reference" type="text" class="form-control" required>
							</div>
						</td>
					</tr>

					<tr height="10px"></td>

					<tr>
						<td colspan="2">
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">Empreinte : </div>
								</div>
                                <input name="empreinte" id="empreinte" type="text" class="form-control" required>
							</div>
						</td>
					</tr>

				</table>

				<div class="tspace"></div>

			 	<div class="row no-gutters">
                    <button type="submit" id="calculer" class="btn btn-outline-primary mb-2 spinnable">
                        Vérifier cette combinaison
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </button>
    			</div>

			</form>

        </div> <!-- #numerique -->

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outil-empreinte -->
