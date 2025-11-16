<div id="scrutins">
<div class="container-fluid">

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">
    
        <div class="row">
            <div class="col-6">
                <h3>Scrutins</h3>
            </div>

            <div class="col-6" style="text-align: right">
                <a href="<?= base_url() . 'scrutins/creer'; ?>" class="btn btn-primary">
                    <i class="fa fa-plus-circle" style="margin-right: 5px"></i> Créer un scrutin</a>
                <a href="<?= base_url() . 'scrutins/gerer'; ?>" class="btn btn-outline-primary">
                    <i class="fa fa-edit" style="margin-right: 3px"></i> Gérer vos scrutins
                </a> 
            </div>
        </div>

        <div class="tspace"></div>

        <? if (empty($scrutins)) : ?>

        <i class="fa fa-exclamation-circle"></i> Il n'y a aucun scrutin qui requiert votre vote.

        <? else : ?>

            <? $plusieurs = count($scrutins) > 1 ? 's' : ''; ?>

            Il y a <?= $plusieurs ? 'des' : 'un'; ?> scrutin<?= $plusieurs; ?> <strong>en attente de votre vote</strong> :

            <div class="space"></div>

            <? foreach($scrutins as $s) : ?>

            <a class="scrutin-item-link" href="<?= base_url() . 'scrutin/' . $s['scrutin_reference']; ?>">
                <div class="scrutin-item">
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="width: 30px; vertical-align: top">
                                    <i class="fa fa-square" style="color: #303F9F"></i> 
                                </td>
                                <td class="text-truncate">
                                    <?= $s['scrutin_texte']; ?><br />
                                </td>
                                <td rowspan="2" style="text-align: right">
                                    <div class="btn btn-sm btn-primary">
                                        Voter 
                                        <i class="fa fa-angle-right" style="margin-left: 7px"></i>
                                        <i class="fa fa-angle-right"></i>
                                        <i class="fa fa-angle-right"></i>
                                    </div>
                                </td>
                            </tr>
                            <tr>    
                                <td style="font-size: 0.8em; padding-top: 3px">
                                    Échéance le <?= date_french_full($s['echeance_epoch']); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </a>

            <? endforeach; ?>

        <? endif; ?>

        <div class="dspace"></div>

        <? 
            if ( ! empty($scrutins_participatifs)) : 

                $plusieurs = count($scrutins_participatifs) > 1 ? 's' : '';
        ?>

            <div id="scrutins-participatifs">

                <h5>Scrutin<?= $plusieurs; ?> répondu<?= $plusieurs; ?></h5>

                <? 
                foreach($scrutins_participatifs as $s) : 
            
                    $en_vigueur   = ($s['termine'] || $s['echeance_epoch'] < $this->now_epoch) ? FALSE : TRUE;                      
                    $resultats_url = base_url() . 'scrutin/' . $s['scrutin_reference'] . '/resultats';
                ?>
                    <div class="hspace"></div>

                    <a class="scrutin-item-link" href="<?= $resultats_url; ?>">
                        <div class="scrutin-<?= $en_vigueur ? '' : 'termine-'; ?>item">
                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td rowspan="2" style="width: 30px; vertical-align: top">
                                            <i class="fa fa-square" style="color: <?= $en_vigueur ? '#9FA8DA' : '#aaa'; ?>"></i> 
                                        </td>
                                        <td class="text-truncate">
                                            <?= $s['scrutin_texte']; ?><br />
                                        </td>
                                        <td rowspan="2" style="text-align: right">
                                            <div class="btn btn-sm" style="color: #fff; background: <?= $en_vigueur ? '#A5D6A7' : '#43A047'; ?>">
                                                Résultats <?= $en_vigueur ? 'partiels' : 'finaux'; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>    
                                        <td style="font-size: 0.8em; padding-top: 3px">
                                            <? if ( ! $en_vigueur) : ?>
                                                <span class="badge badge-danger">
                                                    <? if ($s['termine']) : ?>
                                                        Terminé le <?= date_french_full($s['termine_epoch']); ?>
                                                    <? else : ?>
                                                        Échu le <?= $s['echeance_epoch'] ? date_french_full($s['echeance_epoch']) : '[date inconnue]'; ?>
                                                    <? endif; ?>
                                                </span>
                                            <? else : ?>
                                                <span class="badge badge-warning">
                                                    Échéance le <?= date_french_full($s['echeance_epoch']); ?>
                                                </span>
                                            <? endif; ?>
                                            <? if ($s['anonyme']) : ?>
                                                <span class="badge badge-dark">
                                                    Anonyme
                                                </span>
                                            <? endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </a>

                <? endforeach; ?>

            </div>

        <? endif; ?>

    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #scrutins -->
