<div id="scrutin-erreur-gabarit">
<div class="container">

    <div class="hspace"></div>

    <h3>
        <i class="fa fa-times-circle" style="color: crimson; margin-right: 7px"></i> 
        <?=  ! isset($titre) ? "Erreur avec le scrutin" : $titre; ?>
    </h3>

    <div class="space"></div>

    <?= ! isset($message) ? "Une erreur inconnue s'est produite lors de votre dernière opération." : $message; ?>

    <? if (isset($solution) && ! empty($solution)) : ?>
    
        <div class="space"></div>

        <strong>Solution</strong> :

        <div class="space"></div>

        <?= $solution; ?>

    <? endif; ?>

    <div class="tspace"></div>

    <a class="btn btn-sm btn-primary" href="<?= isset($scrutin_erreur_url) && ! empty($scrutin_erreur_url) ? $scrutin_erreur_url : base_url() . 'scrutins'; ?>">
        <i class="fa fa-undo" style="margin-right: 7px"></i>
        Retour aux scrutins
    </a>

</div> <!-- .container -->
</div> <!-- #scrutin-erreur-gabarit -->
