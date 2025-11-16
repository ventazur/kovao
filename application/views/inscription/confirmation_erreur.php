<div id="confirmation">
<div class="container">

    <h3><i class="fa fa-exclamation-circle" style="margin-right: 5px;"></i> Erreur de la confirmation de votre courriel</h3>

    <div class="tspace"></div>

    <div style="padding: 20px; border: 1px solid crimson; border-radius: 3px;">

        <? if ( ! empty($no)) : ?>
            <strong><?= $no; ?></strong> : 
        <? endif; ?>

        <?= $message; ?>

    </div>

</div> <!-- .container -->
</div> <!-- #confirmation -->
