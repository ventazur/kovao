<?
/* ----------------------------------------------------------------------------
 *
 * KOVAO - PAGE D'ACCUEIL PRINCIPALE DES ENSEIGNANTS (WWW)
 *
 * ----------------------------------------------------------------------------
 *
 * version 2023-12-11
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>

    .chaque-ecole {
        margin-top: 20px; 
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

    <div style="font-weight: 600">
        <i class="bi bi-exclamation-circle" style="color: crimson; margin-right: 5px"></i>
        Veuillez naviguer sur la page d'un groupe
    </div>

    <div class="tspace"></div>

    <?
    /* ------------------------------------------------------------------------
     *
     * VOS ECOLES ET DISCIPLINES
     *
     * ------------------------------------------------------------------------ */ ?>

    <div style="font-weight: 300">
        Vos groupes :
    </div>

        <div class="chaque-ecole">

        <div class="row">

            <!-- image institutionnelle de chaque ecole -->

            <div class="col-1" style="min-width: 150px; min-height: 70px; text-align: center;">
                <img align="middle" width="120px" style="padding-top: 10px" src="https://<?= $this->config->item('domaine'); ?>/assets/images/ecole_logo_perso.png" />
            </div> 
            <div class="col">
                <div style="font-size: 1.1em">Mon groupe personnel</div>

                <div style="margin-left: 25px; margin-top: 12px; font-weight: 300">
                    <i class="fa fa-angle-right" style="margin-right: 10px; color: #1565C0;"></i>
                    <a style="text-decoration: none" href="https://perso.<?= $this->config->item('domaine'); ?>"> 
                        Personnel
                    </a>
                </div>
            </div> <!-- .col -->
        
        </div> <!-- .row -->
        </div> <!-- .chaque-ecole --> 
                
        <? if ( ! empty($mes_ecoles_ids)) : ?>

            <? foreach($mes_ecoles as $ecole_id => $ecole) : ?>

                <div class="chaque-ecole">

                <div class="row">

                    <!-- image institutionnelle de chaque ecole -->

                    <div class="col-1" style="min-width: 150px; min-height: 70px; text-align: center"> 
                        <? if ( ! empty($ecole['image'])) : ?>
                            <img align="middle" width="120px" src="https://<?= $this->config->item('domaine'); ?>/assets/images/ecole_logo_<?= $ecole['image']; ?>" />
                        <? endif; ?>
                    </div>

                    <div class="col">

                        <div style="font-size: 1.1em"><?= $ecole['ecole_nom']; ?></div>

                        <? foreach($mes_groupes as $groupe) : ?>

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

        <? endif; ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * TOUTES LES AUTRES ECOLES ET DISCIPLINES
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="autres-ecoles" style="font-weight: 300; margin-top: 30px">

    Tous les autres groupes :

    <? if (empty($ecoles)) : ?>

        <div class="space"></div>

        <span style="font-weight: 300; font-weight: 0.9em">
            <i class="fa fa-exclamation-circle"></i> Aucune autre école
        </span>

    <? else : ?>

        <? foreach($ecoles as $ecole_id => $ecole) : ?>

            <div class="chaque-ecole">

            <div class="row">

                <!-- image institutionnelle de chaque ecole -->

                <div class="col-1" style="min-width: 150px; min-height: 70px; text-align: center"> 
                    <? if ( ! empty($ecole['image'])) : ?>
                        <img align="middle" width="120px" src="https://<?= $this->config->item('domaine'); ?>/assets/images/ecole_logo_<?= $ecole['image']; ?>" />
                    <? endif; ?>
                </div>

                <div class="col">

                    <div style="font-size: 1.1em"><?= $ecole['ecole_nom']; ?></div>

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

    <? endif; ?>


    </div> <!-- #autres-ecoles -->

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
