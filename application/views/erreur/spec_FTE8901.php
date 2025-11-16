<div id="erreur">
<div class="container">

    <h3>
        <span style="color: darkorange">Oh non !</span> Une erreur est survenue !
    </h3>

    <div class="space"></div>

    <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

        Erreur : <strong>FTE8901</strong>

        <div class="space"></div>

        <i class="fa fa-exclamation-circle"></i>
        <span style="font-weight: 600">Votre nom n'a pas été trouvé dans la liste des étudiants de votre enseignant.</span>

        <div class="space"></div>

        Les causes les plus probables sont :

        <div class="space"></div>

        <ul>
            <li>
                Vous n'avez pas entré correctement votre <?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?> dans votre
                <a href="<?= base_url() . 'profil'; ?>">Profil</a>.
            </li>

            <div class="space"></div>

            <li>
                Cette évaluation a été mise en ligne par un enseignant qui n'est pas le vôtre.
            </li>

            <div class="space"></div>

            <li>
                Votre enseignant n'a peut-être pas encore ajouté ses listes d'étudiants.
            </li>
        </ul>

        <div class="qspace"></div>

        Nous sommes désolés des inconvénients.

    </div>

</div> <!-- .container -->
</div>
