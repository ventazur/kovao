<!DOCTYPE html>
<html>
<head></head>
<body>
<strong>Un nouvel enseignant s'est inscrit à votre département !</strong>
<br/><br />
<?= $prenom . ' ' . $nom; ?> a confirmé son courriel avec succès.
<br />
Veuillez l'autoriser sans tarder une fois que vous aurez vérifié son identité.
<br /><br />
<a href="<?= base_url() . 'admin'; ?>"><?= base_url() . 'admin'; ?></a>
<br /><br />
Merci.
</body>
</html>
