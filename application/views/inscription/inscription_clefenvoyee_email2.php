<!DOCTYPE html>
<html>
<head></head>
<body style="font-family: Arial; padding: 0; margin: 0;">
    <p style="margin: 0; background: #444444; padding: 12px; color: lightblue;">KOVAO</p>

    <p style="margin-top: 20px; font-weight: 600;">
        Confirmation de votre adresse courriel
    </p>

    <p>Veuillez cliquer sur le lien suivant pour continuer :</p>

    <p style="margin-top: 35px; margin-bottom: 35px">
        <span style="border: 2px solid #1565C0; border-radius: 10px; background: #f8f9fa; text-align: center; padding: 20px;">
            <a style="color: #1565C0;" href="<?= base_url() . 'inscription/confirmation/' . $clef; ?>">
                <?= base_url() . 'inscription/confirmation/' . $clef; ?>
            </a>
        </span>
    </p>

    <p>
        Vous avez <?= $this->config->item('inscription_expiration') / 3600; ?>h pour confirmer votre courriel, après quoi la procédure d'inscription deviendra caduque.<br />
        Si vous n'avez pas fait cette requête, nous nous en excusons et vous pouvez ignorer ce message.<br />
    </p>
</body>
</html>
