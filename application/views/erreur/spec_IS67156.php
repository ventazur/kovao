<div id="erreur">
<div class="container">

    <h3>
        <span style="color: darkorange">Oh non !</span> Une erreur est survenue !
    </h3>

    <div class="space"></div>

    <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

        Erreur : <strong>IS67156</strong>

        <div class="space"></div>

        <span style="color: tomato; font-weight: 600">Cette adresse courriel est présentement en procédure d'inscription.</span>

        <div class="space"></div>

        Pour continuer, <span style="background: gold; border-radius: 3px; padding-left: 3px; padding-right: 3px;">il faut obligatoirement cliquer sur le lien de confirmation</span> qui a été envoyé à votre adresse courriel.

        <div class="space"></div>

        <strong>Solutions :</strong>

        <div class="space"></div>

        Vous n'avez rien reçu ?

        <div class="space"></div>

        <ul>
            <li>
                Veuillez vérifier vos <span style="color: crimson; font-weight: 600;">pourriels</span> (aussi appelé <span style="color: crimson; font-weight: 600">spams</span>).<br />
                Dans la grande majorité des cas, le courriel a été classé par erreur dans vos courriers indésirables.
            </li>

            <div class="hspace"></div>
            
            <li>
                S'il n'y a vraiment rien dans vos pourriels, vous pourrez recommencer après <?= $this->config->item('inscription_expiration') / 3600; ?>h d'attente.
            </li>

            <div class="hspace"></div>
            
            <li>
                Finalement, si ça ne fonctionne toujours pas après avoir recommencé, veuillez essayer avec une autre adresse courriel.<br />
                L'adresse courriel de votre collège est souvent utilisée.
            </li>
        </ul>

        </ul>
        Si le problème persiste, veuillez contacter <a href="mailto:info@kovao.com">info@kovao.com</a>

    </div>

</div> <!-- .container -->
</div>
