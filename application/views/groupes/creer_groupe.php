<div id="groupes-creer">
<div class="container-fluid">
        
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h4><i class="fa fa-plus-circle" style="color: dodgerblue; margin-right: 5px"></i> Créer un nouveau groupe</h4>

    <div class="space"></div>

    <?= form_open(base_url() . 'groupes/creer/' . $ecole['ecole_id'],
            array(),
            array()
        ); ?>

        <div id="creer-groupe-ecole-nom">
            <div class="row">
                <div class="col-6">
                    <?= $ecole['ecole_nom']; ?>
                </div>
                <div class="col-6" style="text-align: right">
                    <a class="btn btn-sm btn-outline-light" href="<?= base_url() . 'groupes/creer'; ?>">
                        Changer l'école
                    </a>
                </div>
            </div>
        </div>

        <div id="creer-groupe-ecole-win">

            <div id="creer-groupe-responsabilites" style="font-weight: 300">
                <i class="fa fa-exclamation-circle" style="color: #222; margin-right: 7px"></i>
                En créant ce groupe, vous en deviendrez son 
                <span style="font-weight: 600"><?= $this->enseignant['genre'] == 'F' ? 'administratrice' : 'administrateur'; ?></span>.
                Vous serez responsable d'accepter ou de refuser les nouveaux membres.
            </div>

            <? if (1 == 2) : ?>
                <div class="form-group no-gutters">
                    <label>Quelle est la <strong>dénomination</strong> de votre nouveau groupe ?</label>
                    <div class="col-md-2 mb-2">
                        <select id="choisir-denomination-select" name="denomination" class="form-control">
                            <option value="groupe">Groupe</option>
                            <option value="departement">Département</option>
                            <option value="discipline">Discipline</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>

            <div class="hspace"></div>

            <div class="form-group no-gutters">
                <label for="choisir-nom-groupe">Quel est le <strong>nom officiel</strong> du groupe ?</label>
                <div class="col-md-4">
                    <input name="nom-groupe" type="text" class="form-control <?= @$errors['nom-groupe']; ?>" placeholder="Nom du groupe" value="<?= set_value('nom-groupe'); ?>" required>
                    <?= form_error('nom-groupe'); ?>
                </div>
                <div class="form-text" style="font-size: 0.9em">
                    <i class="fa fa-lightbulb-o" style="color: dodgerblue; margin-top: 7px; margin-right: 5px"></i> 
                    Exemple :
                    <span style="color: dodgerblue;">Département de biologie</span>
                </div>
            </div>

            <div class="hspace"></div>

            <div class="form-group no-gutters">
                <label for="choisir-nom-court-groupe">Quel est le <strong>nom court</strong> du groupe ?</label>
                <div class="col-md-3">
                    <input name="nom-court-groupe" type="text" class="form-control <?= @$errors['nom-court-groupe']; ?>" placeholder="Nom court du groupe" value="<?= set_value('nom-court-groupe'); ?>" required>
                    <?= form_error('nom-court-groupe'); ?>
                </div>
                <div class="form-text" style="font-size: 0.9em">
                    <i class="fa fa-lightbulb-o" style="color: dodgerblue; margin-top: 7px; margin-right: 5px"></i> 
                    Exemple :
                    <span style="color: dodgerblue;">Biologie</span>
                </div>
            </div>

            <div class="hspace"></div>

            <div class="form-group no-gutters">
                <label for="creer-groupe-sous-domaine">Choisissez le <strong>sous-domaine</strong> par lequel votre groupe sera accessible :</label>
                <div class="col-md-4 mb-2">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">https://</div>
                        </div>
                        <input id="creer-groupe-sous-domaine" name="sous-domaine" type="text" class="form-control <?= @$errors['sous-domaine']; ?>" placeholder="sous-domaine" value="<?= set_value('sous-domaine'); ?>" required />
                        <div class="input-group-append">
                            <div class="input-group-text">kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?></div>
                        </div>
                        <?= form_error('sous-domaine'); ?>
                    </div>
                </div>
                <div class="form-text" style="font-size: 0.9em">
                    <i class="fa fa-lightbulb-o" style="color: dodgerblue; margin-top: 7px; margin-right: 5px"></i> 
                    Votre sous-domaine doit commencer par 
                    <span id="creer-groupe-ecole-code" style="color: dodgerblue; font-weight: 700"><?= strtolower($ecole['ecole_nom_court']); ?></span>, 
                    suivi d'une abbréviation représentative de votre groupe.<br />
                    Exemple : pour le département de biologie,
                    <span id="creer-groupe-ecole-code" style="color: dodgerblue;"><?= strtolower($ecole['ecole_nom_court']); ?>biologie</span> ou 
                    <span id="creer-groupe-ecole-code" style="color: dodgerblue;"><?= strtolower($ecole['ecole_nom_court']); ?>bio</span>.

                    Seulement les lettres sont permises, aucun chiffre ou autre caractère.
                </div>
            </div>

            <div class="hspace"></div>

            <button id="creation-groupe-envoyer" type="submit" class="btn btn-primary"><i class="fa fa-plus-circle" style="margin-right: 5px"></i> Créer ce groupe</button>
            <button id="creation-groupe-en-cours" type="submit" class="btn btn-primary disabled d-none">Création en cours....<i class="fa fa-circle-o-notch fa-spin" style="margin-left: 10px"></i></button>

        </div> <!-- #creer-groupe-ecole-win -->

    </div> <!-- #creer-groupe-suite -->

    </form>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #groupes-creer -->
