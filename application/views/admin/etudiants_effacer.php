
<?
/* ----------------------------------------------------------------------------
 *
 * Adninistration du systeme
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-etudiants-effacer">
<div class="container-fluid">

<div id="groupe-data" data-groupe_id="<?= $this->groupe['groupe_id']; ?>" class="d-none"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="row">
        <div class="col-8">
            <h4>
                Admin <i class="bi bi-chevron-right"></i> Étudiants <i class="bi bi-chevron-right"></i> Effacer les étudiants inactifs
            </h4>
		</div>
    </div>

	<div class="tspace"></div>

	<a class="btn btn-sm btn-primary" href="<?= base_url() . 'admin/etudiants_inactifs_effacer_action'; ?>" target="_blank">
		Effacer les étudiants inactifs [action]
	</a>

	<div class="tspace"></div>

	<? if (empty($rapports)) : ?>

		Aucun rapport

	<? else : ?>

		<table class="table" style="font-size: 0.9em; margin: 0">
	
			<tr>
				<td style="width: 80px">id</td>
				<td style="width: 175px">date</td>
				<td>data</td>
			</tr>

			<? foreach($rapports as $r) : ?>

				<tr>
					<td><?= $r['rapport_id']; ?>
					<td><?= $r['date']; ?>
					<td class="mono">
						<? $arr = json_decode($r['data'], TRUE)['g']; ?>
						<? foreach($arr as $k => $v) : ?>
							<?= $k . ' => ' . $v . '<br />'; ?>
						<? endforeach; ?>
					</td>
				</tr>

			<? endforeach; ?>

		</table>

	<? endif; ?>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-etudiants-effacer -->
