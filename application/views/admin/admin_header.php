<div id="admin-admin">
<div class="container-fluid">

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h3>PARLA Administration du système</h3>

    <div class="space"></div>

    <div class="btn-group" role="group">
    <a class="btn btn-sm btn-outline-dark <?= $this->uri->segment(2) == '' || $this->uri->segment(2) == 'alertes' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/alertes'; ?>" style="width: 110px">Alertes</a>
        <a class="btn btn-sm btn-outline-dark <?= $this->uri->segment(2) == 'activite' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/activite'; ?>" style="width: 110px">Activité</a>
        <a class="btn btn-sm btn-outline-dark <?= $this->uri->segment(2) == 'soumissions' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/soumissions'; ?>" style="width: 110px">Soumissions</a>
        <a class="btn btn-sm btn-outline-dark <?= $this->uri->segment(2) == 'consultations' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/consultations'; ?>" style="width: 110px">Consultations</a>
        <a class="btn btn-sm btn-outline-dark <?= $this->uri->segment(2) == 'enseignants' ? 'active' : ''; ?>" href="<?= base_url() . 'admin/enseignants'; ?>" style="width: 110px">Enseignants</a>
    </div>

    <span style="padding-left: 15px"></span>

    <a class="btn btn-sm btn-outline-dark disabled" style="width: 110px" href="<?= base_url() . 'admin/parametres'; ?>">Paramètres</a>
    <a class="btn btn-sm btn-outline-dark" style="width: 110px" href="<?= base_url() . 'admin/ecoles'; ?>">Écoles</a>
    <a class="btn btn-sm btn-outline-dark" style="width: 110px" href="<?= base_url() . 'admin/groupes'; ?>">Groupes</a>

    <div class="dspace"></div>
