<div id="outil-similarite">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Outils <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 7px"></i> Similarité</h4>

        <div class="space"></div>

        <div clsss="col-12" style="background: #444; padding: 10px; border-radius: 3px 3px 0 0; font-family: Lato; font-weight: 300; color: #eee;">
            Détermination de la similarité d'une réponse pour une question à réponse littérale courte
        </div>

        <div id="numerique" style="border: 1px solid #ccc; border-top: 0; border-radius: 0 0 3px 3px; padding: 20px; padding-top: 20px; background: #f7f7f7">

            <div id="alert-reponse" class="alert alert-danger d-none" role="alert" style="margin-top: 5px; margin-bottom: 20px;">
                <i class="fa fa-exclamation-circle" style="margin-right: 10px"></i> Vous devez entrer une réponse hypothétique.
            </div>

            <div>
                Entrez une réponse pour vérifier la similarité :
            </div>

            <div class="space"></div>

			<?= form_open(base_url() . 'outils/similarite', 
					array('id' => 'form-similarite')
				); ?>
				<table>

					<tr>
						<td colspan="2">
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="fa fa-check-circle" style="margin-right: 10px; color: limegreen"></i> Réponse : </div>
								</div>
								<input name="bonne_reponse" id="bonne-reponse" type="text" class="form-control" value="<?= @$reponse['reponse_texte']; ?>" required>
							</div>
						</td>
					</tr>

					<tr height="10px"></td>

					<tr>
						<td colspan="2">
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="fa fa-times-circle" style="margin-right: 10px; color: crimson"></i>  Réponse : </div>
								</div>
                                <input name="reponse" id="reponse" type="text" class="form-control" required>
							</div>
						</td>
					</tr>

					<tr height="30px"></td>
                    
                    <tr>
                        <td colspan="2">
                            <div class="form-row">
                                <div class="col input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa fa-cog" style="margin-right: 10px;"></i> Similarité : 
                                        </div>
                                    </div>
                                    <input name="similarite" id="similarite" type="number" class="form-control" value="<?= $similarite; ?>" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">%</div>
                                    </div>
                                </div>
                                <div class="col mt-2" style="visibility: hidden; padding-left: 10px; color: crimson">
                                    Similarité calculée : <span id="similarite-calculee">X</span> %
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px" colspan="2">
                            <small class="form-text text-muted">
                            </small>
                        </td>
                    </tr>

                    <? if (1 == 2) : ?>
                        <tr height="10px"></td>

                        <tr>
                            <td style="width: 150px">
                                Variation : 
                                <span id="variation" style="font-weight: bold"><?= @$variation; ?></span>
                            </td>
                            <td style="padding-left: 10px">
                                <small class="form-text text-muted">
                                    <i class="fa fa-exclamation-circle" style="color: #777"></i> La variation de caractères entre deux réponses (le plus proche de 0, le mieux).
                                </small>
                            </td>
                        </tr>
                    <? endif; ?>

                    <tr height="20px"></td>
                    
                    <tr>
                        <td width="150px"><strong>Pointage obtenu :</strong></td>
                        <td colspan="4">
                            <span class="badge badge-warning" style="padding: 10px; font-size: 0.9em;">
                                <span id="pointage" style="color: crimson">0</span> / 10
                            </span>
                        <td>
                    </tr>

				</table>

				<div class="tspace"></div>

			 	<div class="row no-gutters">
                    <button type="submit" id="calculer" class="btn btn-outline-primary mb-2 spinnable">
                        Recalculer votre pointage
                        <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                    </button>
                </div>

                <div class="space"></div>

                <div style="font-size: 0.9em">
                    <span style="color: crimson; font-weight: 600">Aide :</span>
                    <div class="hspace"></div>
                        Le pourcentage de similarité entre deux réponses est mieux lorsqu'il est le plus proche de 100% (100% signifie identique).<br />
                        La similarité choisie doit être supérieure ou égale à la similarité calculée pour que l'étudiant obtienne tous les points.<br />
                        Il n'y a pas de points partiels accordés.<br />
                        Les accents, les majuscules, les espaces, les virgules et les points ne sont pas pris en compte lors de la correction.<br />
                        ex. ABC est identique à A B C ou A, B, C.
                </div>
			</form>

        </div> <!-- #numerique -->

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outil-similarite -->
