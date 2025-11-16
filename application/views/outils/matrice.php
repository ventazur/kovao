<div id="matrice">
<div class="container-fluid">
        
<div class="row">
    <div class="col-sm-10 offset-sm-1">

    <? $labos = array_keys($equipes); ?>

    <table class="table">
        <thead>
            <tr>
                <th>Labos</th>
                <? for($i=1; $i <= 32; $i++) : ?>
                    <th class="text-center"><?= $i; ?>
                <? endfor; ?>
            </tr>
        </thead>

        <tbody>
            <? foreach($equipes as $labo => $e) : ?>
            <tr>
                <td class="text-center"><?= $labo; ?></td>
                <? foreach($e as $p1 => $p2) : ?>
                    <td class="text-center"><?= $p2 + 1; ?></td>
                <? endforeach; ?>
            </tr>
            <? endforeach; ?>
        </tbody>
    </table>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils -->
