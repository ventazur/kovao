<div id="admin">
<div class="container-fluid">

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h3>Administration des écoles</h3>

    <div class="space"></div>

    <div class="btn btn-primary">
        <i class="fa fa-plus-circle" style="margin-right: 5px"></i> Ajouter une école
    </div>

    <div class="space"></div>

    <div id="admin-ecoles">

        <? if ( ! empty($ecoles)) : ?>

        <? foreach($ecoles as $ecole_id => $e) : ?>

            <a class="admin-ecole"  href="<?= base_url() . 'admin/ecole/' . $ecole_id; ?>">

                <div class="row">

                    <div class="col-md-11"> 
                        <i class="fa fa-graduation-cap" style="color: #aaa; margin-right: 7px"></i>
                        <?= $e['ecole_nom']; ?></span>
                    </div>

                    <div class="col-md-1" style="text-align: right">
                        <i class="fa fa-angle-right"></i>
                    </div>

                </div> <!-- .row -->

            </a>

        <? endforeach; ?>

        <? endif; // ! empty $ecoles; ?>

    </div> <!-- #admin-ecoles -->

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin -->
