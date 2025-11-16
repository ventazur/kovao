<div id="scrutin-lance">
<div class="container">

    <h3>
        <i class="fa fa-check-circle" style="color: limegreen; margin-right: 7px"></i> 
        Scrutin lancé!
    </h3>

    <div class="tspace"></div>

    Votre scrutin a été lancé avec succès. Les participants peuvent maintenant y répondre.

    <div class="tspace"></div>

    <strong>Information sur le scrutin</strong> :

    <div class="space"></div>

    <table class="table" style="border-bottom: 1px solid #ddd;">
        <tbody>
            <tr>
                <td>Lien direct pour partager ce scrutin</td>
                <td>
                    <a href="<?= base_url() . 'scrutin/' . $scrutin_lance['scrutin_reference']; ?>">
                        <?= base_url() . 'scrutin/' . $scrutin_lance['scrutin_reference']; ?>
                    </a>
                </td>
            </tr>

            <tr>
                <td>Nombre de participants</td>
                <td><?= count($scrutin_lance['participants']); ?></td>
            </tr>

            <? if (1 == 2) : ?>
                <tr>
                    <td>Doit être proposé et appuyé</td>
                    <td><?= $scrutin_lance['code_morin'] ? 'Oui' : 'Non'; ?></td>
                </tr>
            <? endif; ?>

            <tr>
                <td>Anonyme</td>
                <td><?= $scrutin_lance['anonyme'] ? 'Oui' : 'Non'; ?></td>
            </tr>

            <tr>
                <td>Échéance</td>
                <td><?= $scrutin_lance['echeance_epoch'] ? date_french_full($scrutin_lance['echeance_epoch']) : 'Aucune'; ?></td>
            </tr>

        </tbody>
    </table>

    <div class="tspace"></div>

    <a class="btn btn-primary" href="<?= base_url() . 'scrutins'; ?>">
        <i class="fa fa-undo" style="margin-right: 5px"></i>
        Retour aux scrutins
    </a>


</div> <!-- .container -->
</div> <!-- #scrutin-lance -->
