<div id="groupes-creer">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h4><i class="fa fa-plus-circle" style="color: dodgerblue; margin-right: 5px"></i> Créer un nouveau groupe</h4>

    <div class="space"></div>

    <div id="creer-groupe-avertissement">

        Avant de soumettre un nouveau groupe, veuillez vous assurez que les conditions suivantes soient respectées :

        <div class="hspace"></div>

        <li>Le groupe doit représenter un corps enseignant d'une institution d'enseignement publique ou privée du Québec.</li>
        <li>La personne qui soumet ce nouveau groupe doit enseigner à cette institution, et dans cette discipline ou ce département.</li>
        <li><span style="font-weight: 400; color: crimson">Un groupe similaire ne doit pas déjà exister.</span></li>

    </div>

    <?= form_open(); ?>

        <div class="form-group no-gutters">
            <label>À quelle <strong>école</strong> enseignez-vous ?</label>
			<div class="col-md-4 mb-2">
                <select id="choisir-ecole-select" class="form-control">
                    <option value="0">Choisissez votre école dans cette liste :</option>
                    <? foreach($ecoles as $ecole) : ?>
                    <option value="<?= $ecole['ecole_id']; ?>">
                            <?= $ecole['ecole_nom']; ?>
                        </option>
                    <? endforeach; ?>
                </select>
            </div>
            <small class="form-text" style="margin-top: 15px">
                <i class="fa fa-exclamation-circle" style="color: crimson; margin-top: 7px; margin-right: 5px"></i> 
                Si votre école n'apparaît pas dans la liste, vous ne pouvez pas créer de groupe. Veuillez envoyer un courriel à 
                <span style="color: dodgerblue"><strong>info@kovao.com</strong></span> 
                pour demander l'ajout de votre école.
            </small>
        </div>

    </form>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #groupes-creer -->
