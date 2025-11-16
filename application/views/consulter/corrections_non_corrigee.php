<div id="resultats-aucun-semestre">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3>Évaluation envoyée</h3>

    <div class="space"></div>

    <div class="soumission-contenu">
        <table class="table">
            <tbody>
                <tr>
                    <td style="width: 200px">Cours : </td>
                    <td><?= $soumission['cours_data']['cours_nom']; ?> (<?= $soumission['cours_data']['cours_code']; ?>)</td>
                </td>
                <tr>
                    <td>Évaluation :</td>
                    <td><?= $soumission['evaluation_data']['evaluation_titre']; ?></td>
                </tr>
                <tr>
                    <td>Nom :</td>
                    <td><?= $soumission['prenom_nom']; ?></td>
                </tr>
                <tr>
                    <td>Date d'envoi :</td>
                    <td><?= date_french_full($soumission['soumission_epoch'], TRUE); ?></td>
                </tr>
                <tr>
                    <td>Documents :</td>
                    <td>
                        <? if (empty($soumission['documents_data'])) : ?>

                            Aucun document téléversé

                        <? elseif (count($soumission['documents_data']) == 1) : ?>

                            1 document téléversé

                        <? else : ?>
                            
                            <?= count($soumission['documents_data']); ?> documents téléversés

                        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Référence :</td>
                    <td class="mono"><?= $soumission['soumission_reference']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tspace"></div>

    <div style="border: 1px solid crimson; padding: 20px; font-weight: 300">

        <table>
            <tr>
                <td style="width: 30px; vertical-align: top"><i class="fa fa-exclamation-circle"></i></td>
                <td>
                    Cette évaluation n'est pas corrigée ou les corrections ne sont pas visibles.
                </td>
            </tr>
        </table>

    </div>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->
</div> <!-- .container-fluid -->
</div> <!-- #resultats-aucun-semestre -->
