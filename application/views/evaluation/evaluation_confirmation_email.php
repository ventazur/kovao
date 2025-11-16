<!DOCTYPE html>
<html>
<head></head>
<body style="font-family: Arial">
<h3>Vous avez envoyé une évaluation</h3>
<div style="line-height: 24px">
<?= $prenom_nom; ?> a bien envoyé son évaluation le <?= date_humanize(date('U'), TRUE); ?>.
<br/ >
<? if (array_key_exists('evaluation_titre', $evaluation) && ! empty($evaluation['evaluation_titre'])) : ?>
Le titre de l'évaluation est : <strong><?= $evaluation['evaluation_titre']; ?></strong>
<? endif; ?>
<br />
Le code de référence est <a href="<?= base_url() . 'consulter/' . $reference; ?>"><strong><?= $reference; ?></strong></a>
et l'empreinte est <strong><?= $empreinte; ?></strong>.
<br />
</div>
<br />
Merci !
</body>
</html>
