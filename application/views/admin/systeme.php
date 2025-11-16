<?
/* ----------------------------------------------------------------------------
 *
 * Adninistration du systeme
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin">
<div class="container-fluid">

<div id="groupe-data" data-groupe_id="<?= $this->groupe['groupe_id']; ?>" class="d-none"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="row">
        <div class="col-8">
            <h3>
                Administration du système
            </h3>
        </div>
        <div class="col-4" style="text-align: right">
            <? if ($this->enseignant['privilege'] >= 90) : ?>
                <a href="<?= base_url() . 'admin/groupe'; ?>"  class="btn btn-outline-dark">
                    <i class="fa fa-cog" style="margin-right: 5px; color: dodgerblue"></i> Administration du groupe
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

    <div id="sous-menu" class="btn-group btn-block">

        <a class="btn btn-sm spinnable <?= $onglet == 'alertes' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/alertes'; ?>" style="width: 120px">
            Alertes
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'activite' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/activite'; ?>" style="width: 120px">
            Activité
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'activite2' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/activite2'; ?>" style="width: 120px">
            Activité ++
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'groupes' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/groupes'; ?>" style="width: 120px">
            Groupes
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'enseignants' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/enseignants'; ?>" style="width: 120px">
            Enseignants
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'etudiants' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/etudiants'; ?>" style="width: 120px">
            Étudiants
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'soumissions' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/soumissions'; ?>" style="width: 120px">
            Soumissions
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'consultations' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/consultations'; ?>" style="width: 120px">
            Consultations
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'parametres' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/parametres'; ?>" style="width: 120px">
            Paramètres
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

        <a class="btn btn-sm spinnable <?= $onglet == 'maintenance' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/systeme/maintenance'; ?>" style="width: 120px">
            Maintenance
            <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px; margin-right: -22px;"></i>
        </a>

    </div>

    <div class="tspace"></div>

    <? 
    /* ========================================================================
     *
     *  ALERTES
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'alertes') : ?>

        <div>
            <? $this->load->view('admin/alertes_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ACTIVITE
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'activite') : ?>

        <div>
            <? $this->load->view('admin/activite_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ACTIVITE ++
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'activite2') : ?>

        <div>
            <? $this->load->view('admin/activite2_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  GROUPES
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'groupes') : ?>

        <div>
            <? $this->load->view('admin/groupes_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  SOUMISSIONS
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'soumissions') : ?>

        <div>
            <? $this->load->view('admin/soumissions_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  CONSULTATIONS
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'consultations') : ?>

        <div>
            <? // $this->load->view('admin/consultations_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ENSEIGNANTS
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'enseignants') : ?>

        <div>
            <? $this->load->view('admin/enseignants_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ENSEIGNANT
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'enseignant') : ?>

        <div>
            <? $this->load->view('admin/enseignant_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  ETUDIANTS
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'etudiants') : ?>

        <div>
            <? $this->load->view('admin/etudiants_systeme', $this->data); ?>
        </div>

    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  PARAMETRES
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'parametres') : ?>

        <div>
            <? $this->load->view('admin/parametres_systeme', $this->data); ?>
        </div>
    
    <? endif; ?>

    <? 
    /* ========================================================================
     *
     *  MAINTENANCE
     *
     * ======================================================================== */ ?>

    <? if ($onglet == 'maintenance') : ?>

        <div>
            <? $this->load->view('admin/maintenance_systeme', $this->data); ?>
        </div>
    
    <? endif; ?>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-admin -->
