<?
/* ============================================================================
 *
 * Adninistration du systeme
 *
 * ============================================================================ */ ?>

<link href="<?= base_url() . 'assets/css/admin.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/admin.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="admin">
<div class="container-fluid">

<div id="groupe-data" data-groupe_id="<?= $this->groupe['groupe_id']; ?>" class="d-none"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="row">
        <div class="col-8">
            <h3>
                Administration du système <sup style="color: crimson;">&beta;</sup>
            </h3>
        </div>
        <div class="col-4" style="text-align: right">
            <? if ($this->enseignant['privilege'] >= 90) : ?>
                <a href="<?= base_url() . $sous_dir . '/groupe'; ?>"  class="btn btn-outline-dark">
                    <i class="fa fa-cog" style="margin-right: 5px; color: dodgerblue"></i> 
                    Administration du groupe
                </a>
            <? endif; ?>
        </div>
    </div>

    <div class="tspace"></div>
    
    <? 
    /* ========================================================================
     *
     *  SOUS MENU
     *
     * ======================================================================== */ ?>

    <?
        $sous_menu_items = array(
            'alertes'       => 'Alertes',
            'activite'      => 'Activité',
//            'activite2'     => 'Activité++',
            'groupes'       => 'Groupes',
            'enseignants'   => 'Enseignants',
            'etudiants'     => 'Étudiants',
            'soumissions'   => 'Soumissions',
            'consultations' => 'Consultations',
            'parametres'    => 'Paramètres',
            'maintenance'   => 'Maintenance'
        );
    ?>

    <div id="sous-menu" class="btn-group btn-block">

        <? foreach ($sous_menu_items as $vue => $desc) : ?>

            <a class="btn btn-sm spinnable <?= $onglet == $vue ? 'active' : ''; ?>" href="<?= base_url() . $sous_dir . '/systeme/' . $vue; ?>" style="width: 120px">
                <?= $desc; ?>
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
            </a>

        <? endforeach; ?>

    </div>

    <div class="tspace"></div>

    <? 
    /* ========================================================================
     *
     *  VUE PRINCIPALE
     *
     * ======================================================================== */ ?>

    <div>
        <? $this->load->view($sous_dir . '/' . $onglet . '_systeme', $this->data); ?>
    </div>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-admin -->
