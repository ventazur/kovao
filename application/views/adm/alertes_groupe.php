<?
/* ----------------------------------------------------------------------------
 *
 * Administration > Groupe > Alertes
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-groupe-alertes">

    <h5>Les alertes du groupe</h5> 

    <div class="btn-toolbar mt-4 mb-4" role="toolbar">
      <div class="btn-group mr-2" role="group">
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/1'; ?>" class="btn btn-<?= $alertes_importance == 1 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">1</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/2'; ?>" class="btn btn-<?= $alertes_importance == 2 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">2</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/3'; ?>" class="btn btn-<?= $alertes_importance == 3 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">3</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/4'; ?>" class="btn btn-<?= $alertes_importance == 4 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">4</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/5'; ?>" class="btn btn-<?= $alertes_importance == 5 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">5</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/6'; ?>" class="btn btn-<?= $alertes_importance == 6 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">6</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/7'; ?>" class="btn btn-<?= $alertes_importance == 7 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">7</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/8'; ?>" class="btn btn-<?= $alertes_importance == 8 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">8</a>
        <a href="<?= base_url() . $sous_dir . '/groupe/alertes/importance/9'; ?>" class="btn btn-<?= $alertes_importance == 9 ? '' : 'outline-'; ?>primary" style="padding-left: 15px; padding-right: 15px">9</a>
      </div>
    </div>

    <? if (empty($alertes)) : ?>

        <div class="qspace"></div>

        <i class="fa fa-exclamation-circle"></i> 
        Aucune alerte trouvée

    <? else : ?>

        <div id="alertes">

            <table class="table admin-table" style="font-size: 0.85em">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center">Imp.</th>
                        <th style="width: 170px;">Date</th>
                        <th style="">Adresse IP</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th style="text-align: center">Extra</th>
                        <th>URI</th>
                    </tr>
                </thead>
                <tbody>

                    <? foreach($alertes as $a) : ?>
                        <tr style="<?= ($a['epoch'] + (24*60*60)) > date('U') ? 'background: #E3F2FD;' : ''; ?> <?= ($a['importance'] >= $this->config->item('alertes_importantes')) ? 'color: red;' : ''; ?>">
                            <td style="text-align: center"><?= $a['importance']; ?></td>
                            <td class="mono" style=""><?= $a['date']; ?></td>
                            <td class="mono" style="">
                                <?= $a['adresse_ip']; ?>
                            </td>
                            <td class="mono"><?= $a['code']; ?></td>
                            <td><?= $a['desc']; ?></td>
                            <td style="text-align: center">
                                <? if ( ! empty($a['extra'])) : ?>
                                    <div class="badge badge-light" data-toggle="popover" data-placement="top" data-html="true" data-content="<?= str_replace(',', ',<br />', htmlentities($a['extra'])); ?>" style="cursor: pointer">
                                        Extra
                                    </div>
                                <? endif; ?>
                            </td>
                            <td style="text-align: center">
                                <div class="badge badge-light" data-toggle="tooltip" data-placement="top" 
                                    title="<?= strtolower($a['uri']); ?>" style="cursor: pointer">
                                    URI
                                </div>
                            </td>
                        </tr>
                    <? endforeach; ?>

                </tbody>
            </table>

        </div> <!-- #alertes -->

    <? endif; ?>

</div>
