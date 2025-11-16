<?
/* ----------------------------------------------------------------------------
 *
 * Administration > Systeme > Parametres 
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-parametres">

    <h5>Paramètres dynamiques</h5> 

    <div class="space"></div>

    <div style="border: 1px solid #ddd; border-top: 0;">

    <? form_open(NULL, array(), array()); ?>

        <table class="table" style="margin: 0; font-size: 0.8em;">
            <thead>
                <tr>
                    <th style="width: 250px">Clef</th>
                    <th style="width: 150px">Valeur</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($parametres as $p) : ?>
                <tr>
                    <td style="vertical-align: middle"><?= $p['clef']; ?></td>
                    <td>
                        <? if ($p['type'] == 'number') : ?>

                            <input name="<?= $p['clef']; ?>" id="<?= $p['clef']; ?>" class="parametre force-change form-control" style="font-size: 0.9em;" type="number" value="<?= $p['valeur']; ?>">

                        <? elseif ($p['type'] == 'boolean') : ?>

                            <div class="custom-control custom-switch">
                                <input name="<?= $p['clef']; ?>" id="<?= $p['clef']; ?>" class="parametre custom-control-input" type="checkbox" <?= $p['valeur'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="<?= $p['clef']; ?>"></label>
                            </div>

                        <? elseif ($p['type'] == 'text') : ?>

                            <input name="<?= $p['clef']; ?>" id="<?= $p['clef']; ?>" class="parametre force-change form-control" style="font-size: 0.9em;" type="text" value="<?= $p['valeur']; ?>">

                        <? endif; ?>

                    </td>
                    <td style="vertical-align: middle"><?= $p['desc']; ?></td>
                </tr>
                <? endforeach; ?>
            </tbody>
        </table>

    </form>

    </div>

</div>
