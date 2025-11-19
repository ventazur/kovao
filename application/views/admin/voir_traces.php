<style>
	.table tr:first-child td {
		border-top: 0;
	}

</style>

<div class="container">

	<div class="row">
		<div class="col">
			Admin > Traces > <?= $traces['id']; ?>
		</div>	
	</div>

	<div class="row mt-3">
		<div class="col">
			<div style="border: 1px solid #ddd">
			<table class="table table-sm" style="margin-bottom: 0;">
				<tr>
					<td>Enseignant : </td>
					<td><?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></td>	
				</tr>
				<tr>
					<td>Étudiant :</td>
					<td><?= $etudiant['prenom'] . ' ' . $etudiant['nom']; ?></td>
				</tr>
				<tr>
					<td>Titre de l'évaluation :</td>
					<td><?= $evaluation['evaluation_titre']; ?></td>	
				</tr>
				<tr>
					<td>ID de l'évaluation :</td>
					<td><?= $evaluation['evaluation_id']; ?></td>	
				</tr>
				<tr>
					<td>Référence de la soumission :</td>
					<td><?= $traces['soumission_reference']; ?></td>	
				</tr>
			</table>
			</div>
		</div>
	</div>

	<div class="mt-3">
		<pre><? print_r($traces); ?></pre>
	</div>

</div>
