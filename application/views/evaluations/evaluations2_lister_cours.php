<?
/* ------------------------------------------------------------------------
 *
 * Lister les cours
 *
 * ------------------------------------------------------------------------ */ ?>

<?  
     // Le nombre de colonnes que doit avoir l'outil de filtrage
     // Ceci depend du nombre de cours

    $nb_colonnes = 5; 
    $nb_cours    = count($cours_evaluations_existent);
     
    if (($nb_cours + 1) % 2 == 0)
    {
        $nb_colonnes = 4;
    }
?>

<div id="lister-cours-titre">

    Filtrer par cours :

</div>

<div id="lister-cours-contenu">

    <div class="row no-gutters">

        <div id="cours-tous" class="col cours d-none d-md-block actif tous-les-cours">Tous les cours</div>
        <div id="cours-tous" class="col cours d-xs-bloc d-md-none actif tous-les-cours">Tous</div>

        <? $i = 1; foreach($cours_evaluations_existent as $c) : ?>

            <? if ($i == $nb_colonnes) : ?>

                </div>
                <div class="row no-gutters">

                <? $i = 0; ?>

            <? endif; ?>

            <div id="cours-<?= strtolower($cours_raw[$c]['cours_code_court']); ?>" class="col cours d-none d-md-block"
                 data-toggle="popover"
                 data-content="<?= $cours_raw[$c]['cours_nom_court']; ?>"
                 data-cours_md5="<?= md5($cours_raw[$c]['cours_code']); ?>"> 
                <?= $cours_raw[$c]['cours_code']; ?>
            </div>

            <div class="col cours d-xs-block d-md-none"
                data-cours_md5="<?= md5($cours_raw[$c]['cours_code']); ?>"> 
                <?= $cours_raw[$c]['cours_code_court']; ?>
            </div>

        <? $i++; endforeach; ?>

        <? for($j = $i; $j < $nb_colonnes; $j++) : ?>

            <div class="col cours-vide">
            </div>

        <? endfor; ?>

    </div> <!-- .row -->

</div> <!-- #lister-cours-contenu -->
