<div id="scrutin-vote-fait">
<div class="container">

    <h3><i class="fa fa-check-circle" style="color: limegreen"></i> Vote comptabilisé</h3>

    <div class="space"></div>

    Votre vote a été comptabilisé avec succès.<br />
    Vous pouvez consulter les résultats partiels dans l'onglet <a href="<?= base_url() . 'scrutins'; ?>">Scrutins</a>.

    <div class="space"></div>

    <? if (isset($empreinte) && ! empty($empreinte)) : ?>

        L'<strong>empreinte</strong> de confirmation du vote est <strong><?= $empreinte; ?></strong>.<br />
        Cette empreinte vous permettra de vérifier dans les résultats si votre vote a été enregistré correctement pour les scrutins anonymes.

        <div class="space"></div>

    <? endif; ?>

    Merci d'avoir voté !

</div> <!-- .container -->
</div> <!-- #scrutin-vote-fait -->
