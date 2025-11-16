<div id="erreur">
<div class="container">

    <h3>
        <span style="color: darkorange">Oh non !</span> Une erreur est survenue !
    </h3>

    <div class="space"></div>

    <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

        Erreur : <strong><?= @$erreur['code']; ?></strong>

        <div class="space"></div>

        <?= @$erreur['message']; ?>

        <? if (array_key_exists('solution', @$erreur)) : ?>

            <br /><br />

            <strong>Solution :</strong>

            <br /><br />

            <?= @$erreur['solution']; ?>

        <? endif; ?>

        <br /><br />

        <button class="btn btn-outline-primary btn-sm" onclick="history.back()" style="margin-top: 3px">
            <i class="fa fa-angle-up" style="margin-right: 10px"></i>Retour
        </button>
    </div>

</div> <!-- .container -->
</div>
