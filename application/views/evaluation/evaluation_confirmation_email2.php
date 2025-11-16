<!DOCTYPE html>
<html>
<head></head>
<body style="font-family: Arial; padding: 0; margin: 0;">
    <p style="margin: 0; background: #444444; padding: 12px; color: lightblue;">KOVAO</p>

    <p style="margin-top: 20px;">
        Votre évaluation a été envoyée le <?= date_humanize(date('U'), TRUE); ?>.<br />
        <? if (array_key_exists('evaluation_titre', $evaluation) && ! empty($evaluation['evaluation_titre'])) : ?>
            Le titre de l'évaluation est : <?= $evaluation['evaluation_titre']; ?>
        <? endif; ?>
    </p>

    <p>
        <div style="border: 2px solid #1565C0; border-radius: 10px; background: #f8f9fa; text-align: center; padding: 20px; width: 350px">
            <strong>Référence</strong> : <?= $reference; ?><br />
            <strong>Empreinte</strong> : <?= $empreinte; ?>
        </div>
    </p>

    <p>
        Veuillez conserver ce courriel en cas de vérification.<br />
        Merci
    </p>
</body>
</html>
