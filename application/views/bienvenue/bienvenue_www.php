<?
/* ----------------------------------------------------------------------------
 *
 * KOVAO - PAGE D'ACCUEIL PRINCIPALE
 *
 * ----------------------------------------------------------------------------
 *
 * version 2023-12-11
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>

    .chaque-ecole {
        margin-top: 25px; 
        padding: 20px; 
        border-radius: 3px; 
        background: #f3f3f3;
        font-weight: 300
    }

    .chaque-ecole:hover {
        background: #eee;
    }

</style>

<div id="bienvenue-www">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <? if ( 1 == 2 & ! $this->logged_in) : ?>
        <div style="font-size: 0.9em; color: #444; font-weight: 200; background: #f3f3f3; padding: 7px 10px 7px 10px; border-radius: 5px">
            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 5px;" fill="crimson" class="bi-xs bi-exclamation-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg> 
            Vous n'êtes pas connecté.
        </div>
        <div class="tspace"></div>
    <? endif; ?>

    <? if ( ! $this->logged_in) : ?>

        <h3 style="color: #1976D2; font-weight: 300">
            Bienvenue !
        </h3>

        <div class="hspace"></div>

    <? endif; ?>

    <div style="font-weight: 300">
        Veuillez choisir un groupe :

    </div>

    <? if (empty($ecole_ids)) : ?>

        <div class="space"></div>

        <i class="fa fa-exclamation-circle"></i> Aucune école

    <? else : ?>

        <? foreach($ecole_ids as $ecole_id) : ?>

            <? if ( ! array_key_exists($ecole_id, $ecoles)) continue; ?>
            
            <div class="chaque-ecole">

            <div class="row">

                <!-- image institutionnelle de chaque ecole -->

                <div class="col-1" style="min-width: 150px; min-height: 70px; text-align: center"> 
                    <? if ( ! empty($ecoles[$ecole_id]['image'])) : ?>
                        <img align="middle" width="120px" src="https://<?= $this->config->item('domaine'); ?>/assets/images/ecole_logo_<?= $ecoles[$ecole_id]['image']; ?>" />
                    <? endif; ?>
                </div>

                <div class="col">

                    <div style="font-size: 1.1em"><?= $ecoles[$ecole_id]['ecole_nom']; ?></div>

                    <? foreach($groupes as $groupe) : ?>

                        <? if ($groupe['ecole_id'] != $ecole_id) continue; ?>

                        <div style="margin-left: 25px; margin-top: 12px; font-weight: 300">
                            <i class="fa fa-angle-right" style="margin-right: 10px; color: #1565C0;"></i>
                            <a style="text-decoration: none" href="https://<?= $groupe['sous_domaine'] . '.' . $this->config->item('domaine'); ?>"> 
                                <?= $groupe['groupe_nom_court']; ?>
                            </a>
                        </div>

                    <? endforeach; ?>

                </div> <!-- .col -->
            </div> <!-- .row -->
            </div> <!-- .chaque-ecole -->

        <? endforeach; ?>

        </div>

    <? endif; ?>


</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
