<div id="groupes-liste">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Liste des groupes</h4>

        <? if (empty($ecoles_groupes)) : ?>

            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i> Aucun groupe trouvé

        <? else : ?>

            <ul style="margin-bottom: 0px">

            <? foreach($ecoles_groupes as $e) : ?>
    
                <li style="margin-top: 35px">
                    <div style="font-size: 1.1em; font-weight: 400; font-family: Lato;">
                        <?= $e['ecole_nom']; ?>
                    </div>
                </li>

                <ul style="margin-top: 10px; padding-top: 15px; padding-bottom: 15px; border-radius: 3px; background: #f7f7f7;">

                <? foreach($e['groupes'] as $g) : ?>

                    <li style="padding-top: 5px; padding-bottom: 5px">
                        <a href="https://<?= $g['sous_domaine']; ?>.kovao.<?= ($this->is_DEV ? 'dev' : 'com'); ?>"> 
                            <?= $g['groupe_nom']; ?>
                        </a>
                    </li>

                <? endforeach; ?>

                </ul>

            <? endforeach; ?>

            </ul>

        <? endif; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #groupes-liste -->
