<div id="admin">
<div class="container-fluid">

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h3>Administration des groupes</h3>

    <div class="space"></div>

    <div class="btn btn-primary">
        <i class="fa fa-plus-circle" style="margin-right: 5px"></i> Créer un groupe
    </div>

    <div class="space"></div>

    <div id="admin-groupes">

        <? if ( ! empty($groupes)) : ?>

        <? foreach($groupes as $groupe_id => $g) : ?>

            <a class="admin-groupe"  href="<?= base_url() . 'admin/groupe/' . $groupe_id; ?>">

                <div class="row">

                    <div class="col-md-11"> 

                        <i class="fa fa-users" style="color: #aaa; margin-right: 7px"></i>
                        <?= $g['ecole_nom']; ?></span>
                        <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i> 
                        <?= $g['groupe_nom']; ?>    

                    </div>

            
                    <div class="col-md-1" style="text-align: right">

                        <i class="fa fa-angle-right"></i>

                    </div>

                </div> <!-- .row -->

            </a>

        <? endforeach; ?>

        <? endif; // ! empty $groupes; ?>

    </div> <!-- #admin-groupes -->

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin -->
