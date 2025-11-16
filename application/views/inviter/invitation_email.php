<!DOCTYPE html>
<html>

<head></head>

<body>

    <strong>Invitation à joindre la plateforme KOVAO !</strong>
    <br/><br />

    <? if (is_array($hote) && ! empty($hote)) : ?>

        <?= $hote['prenom'] . ' ' . $hote['nom']; ?> vous invite à joindre la plateforme KOVAO.
        <br />

    <? endif; ?>

    Veuillez cliquer le lien suivant pour vous inscrire :

    <br /><br />

    <a href="https://www.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>/inscription/enseignant/<?= $clef; ?>">
        https://www.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>/inscription/enseignant/<?= $clef; ?>
    </a>

    <br /><br />

    Vous avez 3 jours pour poursuivre avec votre inscription, après quoi la procédure deviendra caduque.<br />
    Si vous n'avez pas demandé d'invitation, nous nous en excusons et vous pouvez ignorer ce message.

</body>
</html>
