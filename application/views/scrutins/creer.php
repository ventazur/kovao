<div id="creer-scrutin">
<div class="container-fluid">

<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h4><i class="fa fa-plus-circle" style="margin-right: 7px; color: #ccc"></i>Créer un scrutin</h3>

    <div class="space"></div>

    <?= form_open(base_url() . $this->current_controller . '/' . $this->current_method, 
            array('id' => 'creer-scrutin-form'), 
            array('groupe_id' => $this->groupe_id, 'enseignant_id' => $this->enseignant_id)
        ); ?>

        <div class="form-group">
            <div class="pt-2 pl-3 mb-1" style="background: #3949AB; color: #fff; border-radius: 3px;">
                <label for="creer-evaluation-texte">
                    <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i>
                    Votre question
                </label>
            </div>
            <textarea name="scrutin_texte" type="text" class="form-control <?= @$errors['scrutin_texte']; ?>" placeholder="Entrez votre question" id="creer-scrutin-texte" rows="3"></textarea>
            <?= form_error('evaluation_titre'); ?>
            <small>
                <i class="fa fa-exclamation-circle" style="color: #aaa"></i> 
                Vous pourrez ajouter des fichiers et des images une fois le scrutin créé.
            </small>
            <small style="display: block">
                <i class="fa fa-exclamation-circle" style="color: #aaa"></i> 
                Le scrutin sera visible aux participants seulement une fois que vous laurez lancé (à la prochaine étape).
            </small>
        </div>

        <div class="space"></div>

        <div class="form">
            <div class="pt-2 pl-3 mb-1" style="background: #3949AB; color: #fff; border-radius: 3px">
                <label for="modal-variable-nom">
                    <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i>
                    À qui s'adresse ce scrutin ?
                </label>
            </div>
        
            <div style="padding: 15px; padding-top: 20px; border: 1px solid #ccc; border-radius: 3px">

			<div class="btn-group" role="group" aria-label="Basic example">
                <div id="selectionner-tous-enseignants" class="btn btn-sm btn-outline-primary"><i class="fa fa-check-square-o" style="margin-left: 3px; margin-right: 5px"></i> Sélectionner tous les participants</div>
                <div id="deselectionner-tous-enseignants" class="btn btn-sm btn-outline-primary"><i class="fa fa-square-o" style="margin-left: 3px; margin-right: 5px"></i> Désélectionner</div>
			</div>

			<div class="hspace"></div>

			<? foreach($enseignants as $e) : ?>

				<div class="form-check" style="padding-top: 7px;">

					<input name="participants[]" class="participants form-check-input" value="<?= $e['enseignant_id']; ?>" type="checkbox">
					<label class="form-check-label" style="margin-left: 7px">
						<?= $e['prenom'] . ' ' . $e['nom']; ?>
					</label>

				</div>

			<? endforeach; ?>

            </div>
        </div>

        <div class="tspace"></div>

        <div class="form">
            <div class="pt-2 pl-3 mb-1" style="background: #3949AB; color: #fff; border-radius: 3px">
                <label for="modal-variable-nom">
                    <i class="fa fa-square" style="margin-right: 10px; color: #9FA8DA"></i>
                    Est-ce un scrutin <strong>anonyme</strong> ?
                </label>
            </div>
            <select class="form-control col-1" name="anonyme">
                <option value="0">Non</option>
                <option value="1">Oui</option>
            </select>
        </div>

        <div class="dspace"></div>

        <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-plus-circle"></i> Créer ce scrutin</button>
    </form>


    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #creer-scrutin -->
