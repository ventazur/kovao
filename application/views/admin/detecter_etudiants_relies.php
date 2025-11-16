<div id="etudiants-relies">

<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>Détection des étudiants reliés</h4>

    <div class="space"></div>

    <div id="etudiants-relies-liste" style="border: 1px solid #ddd; padding: 15px; background: #f8f9fa; font-size: 0.8em">

        <? if ( ! empty($etudiants_relies)) : ?>

            <?= p($etudiants_relies); ?>

        <? else : ?>

            <i class="fa fa-exclamation-circle"></i>
            Aucun étudiant relié

        <? endif; ?>

    </div>

    </div> <!-- #etudiants-relies-list -->

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->
</div> <!-- .container-fluid -->

</div> <!-- #etudiants-relies -->
