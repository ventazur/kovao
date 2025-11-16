<div id="activation">
<div class="container">

    <h3>Déconnexion</h3>

    <div class="space"></div>

    Êtes-vous certain<?= @$this->usager['genre'] == 'F' ? 'e' : ''; ?> de vouloir vous déconnecter?

    <div class="space"></div>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Si vous n'êtes pas sur un ordinateur public, vous n'avez pas à vous déconnecter à chaque fois.

    <div class="dspace"></div>

    <a class="btn btn-primary" href="<?= base_url() . 'deconnexion/confirmation'; ?>" role="button">Me déconnecter</a>

</div> <!-- .container -->
</div> <!-- #activation -->

