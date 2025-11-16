<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title>KOVAO : Confirmation d'envoi d'une évaluation</title>

    <? 
    /* ------------------------------------------------------------------------
     *
     * Les styles de base du courriel
     *
     * ------------------------------------------------------------------------ */ 
    ?>

    <? $this->load->view('email_styles'); ?>

</head>

<body class="">

    <span class="preheader"></span>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">

                    <div style="padding: 15px;background: #E8EAF6; color: #222; font-weight: 100">
                        KOVAO
                    </div>

                    <!-- START CENTERED WHITE CONTAINER -->
                    <table role="presentation" class="main">

                        <!-- START MAIN CONTENT AREA -->
                        <tr>
                            <td class="wrapper">
                                
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <p><?= $salutations; ?><?= isset($prenom_nom) && ! empty($prenom_nom) ? ' ' . $prenom_nom : ''; ?>,</p>
                                            <p>
                                            Ce courriel est pour confirmer que nous avons bien reçu votre évaluation <br />

                                                <? if (array_key_exists('evaluation_titre', $evaluation) && ! empty($evaluation['evaluation_titre'])) : ?>
                                                    <strong><?= $evaluation['evaluation_titre']; ?></strong>
                                                <? endif; ?>

                                                le <?= date_humanize(date('U'), TRUE); ?>.
                                            </p>
                                            <p>
                                                <div style="padding-top: 10px; padding-bottom: 10px;">
                                                    <div style="margin: auto; border: 1px solid #C5CAE9; border-radius: 3px; text-align: center; padding: 20px; width: 350px">
                                                        <strong>Référence</strong> : <?= $reference; ?><br />
                                                        <strong>Empreinte</strong> : <?= $empreinte; ?>
                                                    </div>      
                                                </div>
                                            </p>
                                            <p>
                                                Une fois corrigée, votre évaluation sera accessible au lien suivant :
                                            </p>

                                            <div style="padding-top: 10px; padding-bottom: 10px;">

                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                    <tbody>
                                                    <tr>
                                                        <td align="center">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                                <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <a href="<?= base_url() . 'consulter/' . $reference; ?>" target="_blank">
                                                                            Consulter mon évaluation corrigée
                                                                            <span style="margin-left: 5px">→</span>
                                                                        </a> 
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                            <p>
                                                Veuillez conserver ce courriel en cas de vérification. Merci
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- END MAIN CONTENT AREA -->

                    </table>
                    <!-- END CENTERED WHITE CONTAINER -->

                    <!-- START FOOTER -->
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <? /*
                                    Si vous n'êtes pas le destinataire de ce courriel, veuillez nous en excuser et vous
                                    <a href="https://www.kovao.com/courriel/desabonner/asdasdasdd">désabonner</a>.
                                    */ ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- END FOOTER -->

                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>

</body>
</html>
