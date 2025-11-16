<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>KOVAO.dev</title>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/flick/jquery-ui.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400" rel="stylesheet">

    </head>

    <body>

        <? 
        /* --------------------------------------------------------------------
         *
         * NAVIGATION GENERALE
         *
         * -------------------------------------------------------------------- */ ?>

        <nav id="navbar-kovao" class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" style="z-index: 5; border-bottom: 5px solid dodgerblue">

            <div id="navbar" class="container-fluid">

                <a class="navbar-brand" style="font-family: Lato; font-weight: 100; color: lightblue" href="https://www.kovao.com">
                    KOVAO
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-contenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div style="font-family: Lato; font-weight: 300; text-align: right">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="connexion">
                                Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="deconnexion">
                                Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>

            </div> <!-- .container -->
        </nav>

        <div id="erreur">
            <div class="container" style="margin-top: 110px">

                <h3>
                    <div style="color: dodgerblue;">
                        Vous n'êtes pas autorisé à accéder ce site de développement.
                        <i class="fa fa-frown-o" style="margin-left: 10px; color: mediumblue"></i> 
                    </div>
                </h3>

                <br />

                <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

                    Le site de développement est infesté de bogues monstrueux qui donnent la chair de poule même aux plus vaillants.<br />
                    Pour votre sécurité, nous sommes contraints d'y restreindre l'accès.

                </div>

            </div> <!-- .container -->
        </div> <!-- #erreur -->

    </body>
</html>
