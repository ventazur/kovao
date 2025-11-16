<div id="erreur">
<div class="container">

    <h3>
        <span style="color: darkorange">Oh non !</span> Une erreur est survenue !
    </h3>

    <div class="space"></div>

    <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

        Erreur : <strong>EVPLN1</strong>

        <div class="space"></div>

        <span style="color: crimson; font-weight: 600">Cette évaluation sera disponible à un moment ultérieur.</span>

        <? if (isset($evaluation_debut_epoch) && !  empty($evaluation_debut_epoch)) : ?>

            <div class="space"></div>

            Vous pourrez accéder cette évaluation à partir de :

            <span style="background: #FDD835; padding: 6px 10px 6px 10px; border-radius: 5px; color: #00">

                <?= lcfirst(date_french_weekday($evaluation_debut_epoch, TRUE)) . ' le ' . date_french_full($evaluation_debut_epoch, TRUE); ?>

            </span>

        <? endif; ?>

    </div>

</div> <!-- .container -->
</div>
