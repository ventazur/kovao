<div id="erreur">
<div class="container">

    <h3>
        <span style="color: darkorange">Oh non !</span> Une erreur est survenue !
    </h3>

    <div class="space"></div>

    <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

        Erreur : <strong>Connexion</strong>

        <div class="space"></div>

        Nous sommes désolés mais vous avez excédé le nombre de tentatives de connexion alloué.

        <div class="space"></div>

        Veuillez réessayer <?= $this->config->item('securite_tentatives_connexion_periode_blocage'); ?> minutes après votre dernière tentative.

    </div>

</div> <!-- .container -->
</div>
