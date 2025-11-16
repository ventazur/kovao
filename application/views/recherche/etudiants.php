<div id="recherche">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Recherche d'un étudiant</h4>

        <div class="space"></div>

        <?= form_open('',
                array(),
                array('ecole_id' => $ecole['ecole_id'], 'groupe_id' => $groupe['groupe_id'], 'enseignant_id' => $enseignant['enseignant_id'])
            ); ?>

            <div class="form-row">

                <div class="input-group mb-2" style="margin-left: 5px">
                    <input id="search-query" name="search-query" type="text" class="form-control" placeholder="Entrez le nom ou numéro DA de l'étudiant">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2"><i class="fa fa-search" style="color: dodgerblue"></i></span>
                    </div>
                </div>

                <div id="form-options">
					<div class="form-check form-check-inline" style="margin-top: 5px">
                        <div class="custom-control custom-switch" style="margin-top: 2px">
                            <input class="custom-control-input" type="checkbox" id="checkbox-semestres" name="tous_semestres">
                            <label class="custom-control-label" for="checkbox-semestres"></label>
                        </div>
					    <label class="form-check-label">Tous les semestres</label>
					</div>
					<? if ($this->enseignant['privilege'] >= 90) : ?>
                        <div class="form-check form-check-inline">
                            <div class="custom-control custom-switch" style="margin-top: 2px">
                                <input class="custom-control-input" type="checkbox" id="checkbox-etudiants" name="tous_etudiants">
                                <label class="custom-control-label" for="checkbox-etudiants"></label>
                            </div>
						    <label class="form-check-label">Tous les étudiants</label>
						</div>
					<? endif; ?>
                </div>

            </div>

        </form>

        <div id="resultats" class="resultats d-none" style="margin-top: 10px;">

            <div class="tspace"></div>

            <h5>Résultats de votre recherche</h5>

            <div id="resultats-trop" class="resultats d-none" style="padding-top: 20px">

                <i class="fa fa-exclamation-circle" style="margin-right: 7px; color: darkorange"></i>
                Trop de résultats trouvés. Veuillez affiner votre recherche.

            </div>

            <div id="resultats-aucun" class="resultats d-none" style="padding-top: 20px">

                <i class="fa fa-exclamation-circle" style="margin-right: 7px; color: crimson"></i>
                Aucun résultat trouvé. <i class="fa fa-frown-o"></i>

            </div>

            <div id="caracteres-interdits" class="resultats d-none" style="padding-top: 20px">

                <i class="fa fa-exclamation-circle" style="margin-right: 7px; color: crimson"></i>
                Les caractères spéciaux sont interdits. Vous ne pouvez entrer que des lettres, des chiffres, des tirets et des espaces.

            </div>

            <div id="resultats-montrer" class="resultats d-none" style="padding-top: 20px">
            </div>

        </div>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #recherche -->
