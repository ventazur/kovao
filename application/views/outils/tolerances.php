<div id="outil-tolerances">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Outils <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 7px"></i> Tolérances</h4>

        <div class="space"></div>

        <div clsss="col-12" style="background: #444; padding: 10px; border-radius: 3px 3px 0 0; font-family: Lato; font-weight: 300; color: #eee;">
            Détermination de la tolérance d'une question à réponse numérique
        </div>

        <div id="numerique" style="border: 1px solid #ccc; border-top: 0; border-radius: 0 0 3px 3px; padding: 20px; padding-top: 20px; background: #f7f7f7">

            <div id="alert-reponse" class="alert alert-danger d-none" role="alert" style="margin-top: 5px; margin-bottom: 20px;">
                <i class="fa fa-exclamation-circle" style="margin-right: 10px"></i> Vous devez entrer une réponse hypothétique.
            </div>

            <div id="alert-tolerances" class="alert alert-danger d-none" role="alert" style="margin-top: 5px; margin-bottom: 20px;">
                <i class="fa fa-exclamation-circle" style="color: darkred; margin-right: 10px"></i> Les valeurs de vos tolérances doivent être uniques.
            </div>

            <div>
                Entrez une réponse pour vérifier le pointage obtenu basé sur les tolérances définies :
            </div>

            <div class="space"></div>

			<?= form_open(NULL, 
					array('id' => 'form-tolerances')
				); ?>
				<table>

					<tr>
						<td colspan="2">
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="fa fa-check-circle" style="margin-right: 10px; color: limegreen"></i> Réponse : </div>
								</div>
								<input name="bonne_reponse" id="bonne-reponse" type="text" class="form-control" value="<?= @$reponse['reponse_texte']; ?>" style="text-align: right" required>
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
                                <input name="reponse" id="reponse" type="text" class="form-control" style="text-align: right" required>
							</div>
						</td>
					</tr>

					<tr height="20px"></td>

                    <?  $i = 0;
                    
                        foreach($tolerances as $t) : 

                            $i++;
                            $tolerance_id = $t['tolerance_id'];
                    ?>
                        <tr data-item="<?= $i; ?>">
                            <td width="100px">Tolérance <?= $i; ?> :</td>
                            <td width="100px">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">&#177;</div>
                                    </div>
                                    <input name="tolerance<?= $i; ?>" id="tolerance<?= $i; ?>" type="text" class="form-control tolerance" value="<?= str_replace('.', ',', @$t['tolerance']); ?>" style="text-align: right">
                                </div>
                            </td>
                            <td width="5px"></td>
                            <td width="100px">
                                <select name="type<?= $i; ?>" class="custom-select" id="type<?= $i; ?>">
                                    <option value="1">Absolue</option>
                                </select>
                            </td>
                            <td width="40px"></td>
                            <td width="100px">Pénalité <?= $i; ?> :</td>
                            <td width="100px">
                                <div class="input-group">
                                    <input name="penalite<?= $i; ?>" id="penalite<?= $i; ?>" type="number" class="form-control" value="<?= @$t['penalite']; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">%</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    
                        <tr height="10px"></tr>

                    <? endforeach; ?>

					<tr height="10px"></tr>

					<tr>
						<td width="140px"><strong>Pointage obtenu :</strong></td>
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

			</form>

        </div> <!-- #numerique -->

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outil-tolerances -->
