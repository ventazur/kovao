<div id="creer-evaluation">

<div class="container-fluid">
<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>Créer une évaluation</h3>

    <div class="hspace"></div>
    <div class="space"></div>

    <?= form_open(base_url() . 'evaluations/creer', 
            array('id' => 'creer-evaluation-form'), 
            array()
        ); ?>

        <div class="form-group">
            <label for="creer-evaluation-cours">Pour quel cours ?</label>
            <select name="evaluation_cours_id" class="form-control" id="creer-evaluation-cours">
                <? foreach($cours_raw as $cours_id => $c) : ?>
                    <option value="<?= $cours_id; ?>"><?= $c['cours_nom'] . ' (' . $c['cours_code'] . ')'; ?></option>
                <? endforeach; ?>
            </select>
        </div>

        <div class="hspace"></div>

        <div class="form-group">
            <label for="creer-evaluation-titre">Titre de l'évaluation :</label>
            <input name="evaluation_titre" type="text" class="form-control <?= @$errors['evaluation_titre']; ?>" id="creer-evaluation-titre" placeholder="">
            <?= form_error('evaluation_titre'); ?>
        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Les laboratoires sont faits sur mesure.
         *
         * ------------------------------------------------------------ */ ?>

		<? if (in_array($this->enseignant_id, [1, 5])) : ?>

		<div class="mt-4 mb-2">
			<span class="mr-3">
				Est-ce que cette évaluation est un laboratoire ?
			</span>

			<div class="btn-group btn-group-toggle" data-toggle="buttons">
				<label class="btn btn-sm btn-outline-primary" style="width: 70px">
					<input type="radio" name="est_laboratoire" id="option1" autocomplete="off" value="1" style="width: 100px"> oui
				</label>
				<label class="btn btn-sm btn-outline-primary" style="width: 70px">
					<input type="radio" name="est_laboratoire" id="option2" autocomplete="off" value="0" checked> non
				</label>
			</div>
		</div>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------
         *
         * Est-ce l'evaluation est publique (groupe) ou privee ?
         *
         * ------------------------------------------------------------ */ ?>

        <? if ($this->groupe_id != 0) : ?>

            <div class="hspace"></div>

            <div class="form">
                <label for="modal-variable-nom">Ajouter à quel endroit ?</label>
                <select class="form-control" name="public">
                    <option value="0">Mes évaluations</option>
                    <option value="1">Évaluations du département</option>
                </select>
            </div>

        <? endif; ?>

        <div class="dspace"></div>

        <button type="submit" class="btn btn-primary mb-2">Créer cette évaluation</button>

    </form>

    </div> <!-- .col .col-xl-10 -->
    <div class="col-xl-1 d-none d-xl-block">

</div> <!-- .row -->
</div> <!-- .container-fluid -->

</div> <!-- #creer-evaluation -->
