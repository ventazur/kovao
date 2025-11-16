<div id="creer-evaluation-aucun-cours">

<div class="container-fluid">
<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>Créer une évaluation</h4>

    <div class="tspace"></div>

    <i class="fa fa-exclamation-circle"></i> Aucun cours trouvé

    <div class="tspace"></div>

    <? if ($this->groupe_id == 0) : ?>

        Vous devrez au préalable créer un semestre et un cours.
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url('groupe/gerer'); ?>">Gérer</a>

    <? elseif ($this->groupe['admin_enseignant_id'] == $this->enseignant_id) : ?>

        Vous devrez au préalable créer un semestre et un cours.
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url('groupe/gerer'); ?>">Gérer</a>

    <? else : ?>

        <strong>Solution :</strong>

        <div class="hspace"></div>

        Vous devez demander à l'administrateur de votre groupe de créer un semestre et un cours.<br />
        Ensuite vous pourrez créer une évaluation.

    <? endif; ?>

    </div> <!-- .col .col-xl-10 -->
    <div class="col-xl-1 d-none d-xl-block">

</div> <!-- .row -->
</div> <!-- .container-fluid -->

</div> <!-- #creer-evaluation-aucun-cours -->
