<div id="bienvenue-www">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <? if (1 == 2) : ?>

        <div style="border: 2px solid #444; background: #f3f3f3; border-radius: 3px;">
            <table class="table" style="margin: 0; border: none;">
                <tbody>
                    <tr>
                        <td style="border: 0; text-align: center;">
                            <img src="<?= base_url() . 'assets/images/logo_clg.png'; ?>" style="width: 175px">
                        </td>
                        <td style="vertical-align: middle; border: 0; font-weight: 300">
                            Vous enseignez au <span style="font-weight: 600">Collège Lionel-Groulx</span> et aimeriez utiliser cette plateforme ?</span>
                            <div class="qspace"></div>
                            <div class="hspace"></div>
                            Veuillez envoyer un courriel à <span style="padding-left: 5px; padding-right: 5px; font-weight: 600; color: crimson">invitation@kovao.com</span> à partir de votre adresse institutionnelle, en précisant votre nom et votre domaine d'enseignement.<br />
                            Vous recevrez un lien d'invitation pour vous inscrire.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tspace"></div>
        <div class="qspace"></div>

    <? endif; ?>

    <h3>
        Trouver une évaluation !
    </h3>

    <div class="space"></div>

    <?= form_open(); ?>
        
        <div class="input-group mb-2">
            <input id="vers-evaluation-query" name="vers-evaluation-query" type="text" class="form-control" 
                placeholder="Référence de l'évaluation (6 lettres)">
            <div class="input-group-append">
                <span class="input-group-text" id="vers-evaluation" style="padding-left: 20px; padding-right: 20px; cursor: pointer">
                    <i class="fa fa-search" style="color: #fff"></i>
                </span>
            </div>
        </div>
        <div id="vers-evaluation-helper" class="d-none" style="font-family: Lato; font-weight: 300; font-size: 0.9em; margin-left: 10px; color: crimson;">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
            <span id="vers-evaluation-helper-msg"></span>
        </div>

    </form>

    <? if (1 == 2) : ?>
        <div style="margin-top: 50px; padding: 20px; background: #E8EAF6; border-radius: 5px;">

            <h5>Créez vos propres évaluations</h5>

            <div style="padding-top: 10px; padding-bottom: 20px; font-family: Lato; font-weight: 300; font-size: 1.1em; color: #444;">
                Vous voulez tester les connaissances de vos amis ? de vos étudiants ?<br />
                Inscrivez-vous pour débuter dès maintenant. Pssssst ! C'est gratuit !
            </div>

            <a id="inscrivez-vous" class="btn" href="<?= base_url() . 'inscription/enseignant'; ?>">
                Inscrivez-vous !
            </a>

        </div>
    <? endif; ?>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
