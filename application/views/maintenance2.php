<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>KOVAO.com</title>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/flick/jquery-ui.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400" rel="stylesheet">
        <link href="assets/css/site.css<?= '?' . date('U'); ?>" rel="stylesheet">

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

                <a class="navbar-brand" style="font-family: Lato; font-weight: 100; color: lightblue">KOVAO</a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-contenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbar-contenu">

                </div> <!-- .navbar-collapse -->

            </div> <!-- .container -->
        </nav>

        <div id="erreur">
            <div class="container" style="margin-top: 110px">

                <h3>
                    <div style="color: dodgerblue;">
                        Le site est présentement en maintenance
                        <i class="fa fa-wrench" style="margin-left: 10px; color: mediumblue"></i> 
                    </div>
                </h3>

                <br />

                <div style="padding: 40px; border: 1px solid #ddd; border-radius: 3px">

                    Nous vous reviendrons pour la prochaine session. <br /><br />
                    Bonnes vacances !

                </div>

            </div> <!-- .container -->
        </div> <!-- #erreur -->

    </body>
</html>
