<!DOCTYPE html>
<html>
<head></head>
<body>
<strong>Confirmation de votre adresse courriel</strong><br />
<br/>
Veuillez cliquer le lien suivant pour continuer :<br />
<br />
<a href="<?= base_url() . 'inscription/confirmation/' . $clef; ?>"><?= base_url() . 'inscription/confirmation/' . $clef; ?></a><br />
<br />
Vous avez <?= $this->config->item('inscription_expiration') / 3600; ?>h pour confirmer votre courriel, après quoi la procédure d'inscription deviendra caduque.<br />
Si vous n'avez pas fait cette requête, nous nous en excusons et vous pouvez ignorer ce message.<br />
<br />
Si vous éprouvez des problèmes, veuillez contacter <a href="info@kovao.com">info@kovao.com</a>.
</body>
</html>
