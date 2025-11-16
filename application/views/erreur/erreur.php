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

        <? if ( ! empty($erreur['url'])) : ?>

            <div class="space"></div>

            <a href="<?= $erreur['url']; ?>" class="btn btn-sm btn-primary">Retour <i class="fa fa-undo" style="margin-left: 5px"></i></a>
        <? endif; ?>

    </div>

</div> <!-- .container -->
</div>
