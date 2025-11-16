<? 
/* ----------------------------------------------------------------------------
 *
 * Forums > Message
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="forums-lire">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3>
        <span style="color: 000; font-family: Lato; font-weight: 700"><span style="color: crimson">ƒ</span>orums</span>
    </h3>

    <? 
    /* -------------------------------------------------------------
     *
     * Le message
     *
     * ------------------------------------------------------------- */ ?>

    <div id="message">

        <?
        /* ------------------------------------------------------------
         *
         * Titre du message
         *
         * ------------------------------------------------------------ */ ?>

        <div class="message-lire-titre">

            <div class="row">

                <div class="col">
                    <?= _html_out($message['titre']); ?>
                </div>

                <div class="col" style="max-width: 280px; text-align: right">
                    <? if ($message_suivi) : ?>
                        <div id="ne-plus-suivre" class="btn btn-sm btn-outline-danger spinnable" data-message_id="<?= $message['message_id']; ?>">
                            <i class="fa fa-times-circle-o" style="margin-right: 3px"></i> 
                            Ne plus suivre ce message
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>
                    <? else : ?>
                        <div id="suivre" class="btn btn-sm btn-outline-primary spinnable"
                             data-message_id="<?= $message['message_id']; ?>"
                             data-toggle="popover"
                             data-content="Lorsqu'un commentaire s'ajoutera, vous recevrez une notification sur la page d'acueil.">
                            <i class="fa fa-map-marker" style="margin-right: 3px"></i> 
                            Suivre ce message
                            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                        </div>
                    <? endif; ?> 
                </div>

            </div>

        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Auteur du message
         *
         * ------------------------------------------------------------ */ ?>

        <div class="message-lire-auteur">

            par <?= $message['prenom'] . ' ' . $message['nom']; ?>, <?= fuzzy_date($message['ajout_epoch']); ?>

        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Contenu du message
         *
         * ------------------------------------------------------------ */ ?>
        <div class="message-lire-contenu">

            <?= _html_out(mynl2br($message['contenu'])); ?>

            <? if ($message['enseignant_id'] == $this->enseignant_id) : ?>
                <div class="message-lire-contenu-actions" style="font-size: 0.8em">
                    <div class="hspace"></div>
                    <a href="<?= base_url() . $current_controller . '/modifier/' . $message['message_id']; ?>">
                        <i class="fa fa-edit"></i>
                        Modifier votre message
                    </a>
                </div>
            <? endif; ?>
            
            <? if ($message['edite']) : ?>
                <div style="margin-top: 5px; font-size: 0.8em; color: #777;">
                    modifié <?= fuzzy_date($message['edite_epoch']); ?>
                
                </div>
            <? endif; ?>
        </div>

    </div> <!-- #message -->

    <? 
    /* -------------------------------------------------------------
     *
     * Les commentaires
     *
     * ------------------------------------------------------------- */ ?>

    <? if ($this->config->item('forums_commentaires') && ! $message['permettre_commentaires']) : ?>

        <div class="mt-4" style="font-size: 0.85em;">

            <i class="fa fa-exclamation-circle" style="color: #aaa"></i>
            Les commentaires ont été désactivés pour ce message.

        </div>
        
    <? elseif ($this->config->item('forums_commentaires')) : ?>

        <div id="commentaires" class="mt-4 <?= empty($commentaires) ? 'd-none' : ''; ?>">
        
            <div style="font-weight: 700; margin-bottom: 15px; color: #444;">
                <i class="fa fa-comment" style="margin-right: 3px"></i>
                Commentaire<?= count($commentaires) > 1 ? 's' : ''; ?> :
            </div>

            <div id="commentaires-lire">

                <? foreach($commentaires as $commentaire_id => $c) : ?>

                    <table style="margin-bottom: 10px; width: 100%">
                        <tbody>
                            <tr>
                                <td style="vertical-align: top; width: 15px;">
                                    <i class="fa fa-angle-right"></i>
                                </td>
                                <td class="commentaire-lire-contenu">
                                    <?= _html_out($c['commentaire_contenu']); ?>

                                    <? if (1 == 2 && $c['enseignant_id'] != $this->enseignant_id) : ?>
                                        <span style="font-size: 0.9em">
                                            <i class="fa fa-reply" style="margin-left: 5px;"></i>
                                        </span>
                                    <? endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="commentaire-lire-auteur">

                                    de <?= $c['prenom'] . ' ' . $c['nom']; ?>, <?= fuzzy_date($c['ajout_epoch']); ?>
    
                                    <? if (
                                            $c['enseignant_id'] == $this->enseignant_id &&
                                            ($c['ajout_epoch'] + $this->config->item('forums_commentaires_effacement_delai')) > $this->now_epoch
                                          ) : 
                                    ?>
                                
                                        | <a href="<?= base_url() . 'forums/effacer/commentaire/' . $c['commentaire_id']; ?>" style="color: crimson">effacer</a>

                                    <? endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                <? endforeach; ?>

            </div> <!-- #commentaires-lire -->

        </div> <!-- #commentaires -->

        <?
        /* ------------------------------------------------------------------------
         *
         * Ajouter un commentaire
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="nouveau-commentaire" class="mt-4">

            <?= form_open(base_url() . $current_controller . '/lire/' . $message['message_id'],
                    array(),
                    array()
                ); ?>

                <div class="form-group">
                    <label for="commentaire-contenu" style="font-weight: 700; width: 100%">
                        <i class="fa fa-comment"></i>
                        <sup><i class="fa fa-plus-circle" style="margin-left: -4px; margin-right: 3px; color: dodgerblue"></i></sup>
                        Ajouter un commentaire :
                    </label>
                    <textarea name="commentaire" class="form-control <?= @$errors['commentaire']; ?>" id="commentaire-contenu" rows="3" placeholder="Votre commentaire" value="<?= set_value('commentaire'); ?>" required></textarea>
                    <?= form_error('commentaire'); ?>
                </div>

                <button id="publier-commentaire" type="submit" class="btn btn-sm btn-primary spinnable">
                    <i class="fa fa-plus-circle" style="margin-right: 5px"></i>
                    Ajouter
                    <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
                </button>

            </form>

        </div> <!-- #nouveau-commentaire -->

    <? endif; ?>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #forums-lire -->
