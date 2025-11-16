<?
/* ============================================================================
 *
 * Forums
 *
 * ============================================================================ */ ?>

<div id="forums">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3>
        <span style="color: 000; font-family: Lato; font-weight: 700"><span style="color: crimson">ƒ</span>orums</span>
    </h3>

    <div class="space"></div>

    <a class="btn btn-sm btn-outline-primary" href="<?= base_url() . $current_controller . '/publier'; ?>">
        <i class="fa fa-plus-circle" style="margin-right: 5px"></i>
        Publier un nouveau message
    </a>

    <div class="tspace"></div>

    <div id="forums-contenu">

    <? if (empty($messages)) : ?>

        <div style="font-size: 0.9em" class="mb-3">
            <i class="fa fa-exclamation-circle"></i>
            Il n'y a aucune publication dans les forums.
        </div>

    <? else : ?>

        <?  $i = 0;

            $intervalle_max = $this->config->item('forums_intervalle_max');

            foreach($messages_ordre as $message_id) : 
            
                $m = $messages[$message_id];
                $i++;

                $bg_color = '';

                // Les messages non lus sont les messages non lus pas plus vieux que l'intervalle maximum,
                // excluant les messages de l'auteur.

                $m_non_lu = ! in_array($message_id, $message_ids_lus) ? TRUE : FALSE;
                $m_non_lu = $m_non_lu && (($m['ajout_epoch'] > ($this->now_epoch -  $intervalle_max))) ? TRUE : FALSE;
                $m_non_lu = $m_non_lu && ($this->enseignant_id != $m['enseignant_id'] ? TRUE : FALSE);

                // Les messages suivis avec de nouveaux commentaires

                $nc = in_array($message_id, $message_ids_nc) ? TRUE : FALSE; 

                if ($m_non_lu)
                    $bg_color = '#FFF9C4;';

                if ($nc)
                    $bg_color = '#FCE4EC;';

        ?>

        <a href="<?= base_url() . $current_controller . '/lire/' . $m['message_id']; ?>" style="text-decoration: none" class="spinnable">

        <div class="forums-table" style="background: <?= ($m_non_lu || $nc) ? $bg_color : ''; ?>">

            <table>
                <tbody>
                    <tr class="message-titre">
                        <td style="width: 20px;">
                            <i class="fa fa-angle-right" style="font-size: 0.9em; color: dodgerblue;"></i>
                        </td>
                        <td>
                            <a class="message-lien" href="<?= base_url() . $current_controller . '/lire/' . $m['message_id']; ?>">
                                <?= _html_out($m['titre']); ?>
                            </a>
                        </td>
                        <td rowspan="2" style="width: 60px; text-align: right; padding-right: 20px;">
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </td>
                    </tr>
                    <tr class="message-auteur">
                        <td></td>
                        <td>
                            par <?= $m['prenom'] . ' ' . $m['nom']; ?>, <?= fuzzy_date($m['ajout_epoch']); ?>

                            <? if ($this->config->item('forums_commentaires') && $m['permettre_commentaires']) : ?>
                                |
                                <? if (empty($commentaires[$m['message_id']])) : ?>
                                    aucun commentaire
                                <? else : ?>
                                    <?= $c = count($commentaires[$m['message_id']]); ?> commentaire<?= $c > 1 ? 's' : ''; ?>
                                <? endif; ?>
                            <? endif; ?>

                        </td>
                    </tr>
                </tbody>
            </table>

        </div> <!-- .forums-table -->

        </a>

        <? endforeach; ?>

    <? endif; ?>

    </div> <!-- #forums-contenu -->

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div>
