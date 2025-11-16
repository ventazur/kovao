<div id="bienvenue-www">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>
        Test de messagerie 

        <? if ($envoi_reussi) : ?>

            <span style="color: green">RÉUSSI</span>
            
        <? else : ?>

            <span style="color: crimson">ÉCHEC</span>

        <? endif; ?>
    </h4>

    <div class="space"></div>

    Voici les informations du test :

    <div class="space"></div>

    Code de référence : <?= $string; ?><br />
    Date : <?= $date_human; ?>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
