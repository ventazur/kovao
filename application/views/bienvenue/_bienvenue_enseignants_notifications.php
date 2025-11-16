<? 
/* ----------------------------------------------------------------------------
 *
 * ADMINISTRATION > Notifications pour les alertes importantes
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if ($this->est_enseignant && $this->enseignant['privilege'] >= 90 && $admin_alertes > 0) : ?>

    <div class="alert" role="alert" style="background: crimson; color: #fff; font-family: Lato; font-weight: 300">
        <div class="row">
            <div class="col-8">
                <i class="fa fa-exclamation-circle" style="color: pink; margin-right: 7px"></i>
                Il y a 
                <span style="font-weight: 700; color: yellow; padding-left: 4px; padding-right: 4px;"><?= $admin_alertes; ?></span> 
                <?= $admin_alertes > 1 ? 'alertes importantes' : 'alerte importante'; ?> depuis les derniers 24h.
            </div>
            <div class="col-4" style="text-align: right">
                <a style="color: #fff;" href="<?= base_url() . 'adm/systeme/alertes/importance/4'; ?>">
                    <span style="text-decoration: underline">Voir les alertes</span>
                    <i class="fa fa-angle-right" style="margin-left: 7px"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="hspace"></div>

<? endif; ?>


<? 
/* ----------------------------------------------------------------------------
 *
 * Notification pour les enseignants en attente d'approbation
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if ($this->groupe_id != 0 && ! empty(@$enseignants_a_approuver)) : ?>

    <div class="alert alert-danger" role="alert">
        <div class="row">
            <div class="col-8">
                <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
                Il y a 
                <?= count($enseignants_approbation) ? 'un enseignant' : 'des enseignants'; ?> 
                en attente d'approbation.
            </div>
            <div class="col-4" style="text-align: right">
                <a style="color: darkred" href="https://<?= $this->sous_domaine; ?>.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>/groupe/gerer">
                    <span style="text-decoration: underline">Gestion du groupe</span>
                    <i class="fa fa-angle-right" style="margin-left: 7px"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="hspace"></div>

<? endif; ?>

<? if ($this->groupe_id == 0) : ?>

    <h3 style="font-family: Lato; font-weight: 300">Personnel</h3>
    <div class="hspace"></div>

<? endif; ?>


<? 
/* ----------------------------------------------------------------------------
 *
 * (DESACTIVE) Notifications pour les scrutins
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if (1 == 2 && $this->groupe_id != 0 && ! empty(@$scrutins_a_voter)) : ?>

    <div class="alert alert-warning" role="alert">
        <div class="row">
            <div class="col-8">
                <i class="fa fa-exclamation-circle" style="color: darkorange; margin-right: 7px"></i>
                Il y a 
                <?= $scrutins_a_voter > 1 ? 'plusieurs scrutins' : 'un scrutin'; ?> 
                qui requiert votre vote.
            </div>
            <div class="col-4" style="text-align: right">
                <? 
                    $url_scrutin = '';

                    if ($scrutins_a_voter == 1)
                    {
                        $url_scrutin = base_url() . 'scrutin/' . @$scrutin['scrutin_reference'];
                    }
                    else
                    {
                        $url_scrutin = base_url() . 'scrutins';
                    }
                ?>

                <a class="badge" style="background: darkorange; color: #fff;font-weight: 400; font-size: 0.85em; padding: 6px 10px 6px 10px" href="<?= $url_scrutin; ?>">
                    Je vais voter !
                    <i class="fa fa-angle-right" style="margin-left: 7px"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="hspace"></div>

<? endif; ?>
