<div id="resultats-aucun-semestre">
<div class="container-fluid">

<div class="row">

    <div class="col-sm-1"></div>
    <div class="col-sm-7">
        <h3>Résultats 

            <? if ( ! empty($enseignant['semestre_id']) || ! empty($semestre_id)) :  ?>

                <span style="color: limegreen"><?= $this->semestres[$semestre_id]['semestre_code']; ?></span>

            <? endif; ?>

        </h3>
    </div>
    <div class="col-sm-3">

        <div class="float-sm-right mt-2 mt-sm-0">

            <? if ( ! empty($semestres) && count($semestres) > 1) : ?>

                <div class="dropdown">	

                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Autres semestres
                    </button>

                    <? $semestres_r = array_reverse($semestres); ?>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                    <? foreach($semestres_r as $s) : ?>

                        <? if ($s['semestre_id'] == $semestre_id) continue; ?>

                        <a class="dropdown-item" href="<?= base_url() . 'resultats/semestre/' . $s['semestre_code']; ?>">
                            <?= $s['semestre_code']; ?>
                        </a>

                    <? endforeach; ?>
                    
                    </div>

                </div>

            <? endif; ?>

        </div>

    </div> <? // .col-md-3 ?>
    <div class="col-sm-1"></div>
</div> <? // .row ?>

<div class="tspace"></div>

<div class="row">

    <div class="col-sm-1"></div>
    <div class="col-sm-10">

        <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun résultat trouvé pour ce semestre

    </div>

    <div class="col-sm-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #resultats-aucun-semestre -->
