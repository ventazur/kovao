<!doctype html>
<html lang="en">

<head>
    <?
    /* ------------------------------------------------------------------------
     *
     * Meta
     *
     * ------------------------------------------------------------------------ */ ?>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre du site
     *
     * ------------------------------------------------------------------------ */ ?>

	<title>
		<?= $this->config->item('nom_du_site'); ?>

		<? if ($this->is_DEV) : ?>
			(dev)
		<? endif; ?>
	</title>

    <?
    /* ------------------------------------------------------------------------
     *
     * Les icons
     *
     * ------------------------------------------------------------------------ */ ?>

	<link rel="icon" href="<?= base_url(); ?>favicon-32x32.png" />
	<link rel="icon" href="<?= base_url(); ?>favicon.ico" type="image/x-icon" />

	<link rel="stylesheet" href="<?= base_url() . 'application/vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css'; ?>" />
    <? /* <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> */ ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Preconnect & DNS Prefetch
     *
     * ------------------------------------------------------------------------ */ ?>

    <link rel="dns-prefetch" href="https://fonts.googleapis.com/">

    <? /*
    <link rel="preconnect" href="https://ajax.googleapis.com/" crossorigin>
    <link rel="dns-prefetch" href="https://stackpath.bootstrapcdn.com/">
    <link rel="dns-prefetch" href="https://maxcdn.bootstrapcdn.com/">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net/">
    */ ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Les scripts (qui doivent etre charges avant les autres)
     *
     * ------------------------------------------------------------------------ */ ?>

    <script src="<?= base_url() . 'assets/js/vendors/jquery-3.5.1.min.js'; ?>"></script>

    <?
    /* ----------------------------------------------------------------------------
     *	
     * Les scripts (externes)
     *
     * ---------------------------------------------------------------------------- */ ?>

    <script src="<?= base_url() . 'assets/bundles/jquery-ui-1.12.1/jquery-ui.min.js'; ?>" defer></script>
    <script src="<?= base_url() . 'assets/js/vendors/datepicker-fr-CA.js'; ?>" defer></script>
    <script src="<?= base_url() . 'assets/bundles/popper-1.16.1/popper.min.js'; ?>" defer></script>
    <script src="<?= base_url() . 'assets/bundles/bootstrap-4.5.2/js/bootstrap.min.js'; ?>" defer></script>
    <script src="<?= base_url() . 'assets/js/vendors/autosize-4.0.0.min.js'; ?>" defer></script>
    <script src="<?= base_url() . 'assets/js/vendors/js-cookie-2.2.0.js'; ?>" defer></script>
    <script src="<?= base_url() . 'assets/js/vendors/underscore-1.9.1.min.js'; ?>" defer></script>

    <? 
    /* ----------------------------------------------------------------------------
     *
     * Google reCAPTCHA (version 3) 
     *
     * ----------------------------------------------------------------------------*/ ?>

    <? if ($this->uri->segment(1) == 'inscription') : ?>
        <script src="https://www.google.com/recaptcha/api.js?render=6LerZfQUAAAAAHMCPcZmcK_G8-UDCqCppLC3tZSI" defer></script>
    <? endif; ?>

    <?
    /* ----------------------------------------------------------------------------
     *	
     * Les scripts (internes)
     *
     * ---------------------------------------------------------------------------- */ ?>

    <script src="<?= base_url() . 'assets/js/site.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" defer></script>

    <? if ($this->uri->segment(1) !== NULL && file_exists('assets/js/' . $this->uri->segment(1) . '.js')) : ?>
        <script src="<?= base_url() . 'assets/js/' . $this->uri->segment(1) . '.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" defer></script>
    <? endif; ?>

    <? if ($this->uri->segment(1) !== NULL && in_array($this->uri->segment(1), array('evaluations', 'configuration'))) : ?>
        <script src="<?= base_url() . 'assets/js/documents.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" defer></script>
    <? endif; ?>

    <?
     /* ------------------------------------------------------------------------
      *	
      * Plugin de visibilite pour l'horloge du serveur
      *
      * ----------------------------------------------------------------------- */ ?>

    <? if ($this->uri->segment(1) == 'evaluation' && file_exists('assets/js/vendors/jquery.visible.min.js')) : ?>

        <script src="<?= base_url() . 'assets/js/vendors/jquery.visible.min.js'; ?>" defer></script>

    <? endif; ?>


    <?
    /* ------------------------------------------------------------------------
     *
     * Les fonts
     *
     * ------------------------------------------------------------------------ */ ?>

    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400|Ubuntu+Mono&display=swap" rel="stylesheet">

    <?
    /* ------------------------------------------------------------------------
     *
     * Les styles (externes)
     *
     * ------------------------------------------------------------------------ */ ?>

    <link href="<?= base_url() . 'assets/bundles/bootstrap-4.5.2/css/bootstrap.min.css'; ?>" rel="stylesheet">
    <link href="<?= base_url() . 'assets/css/vendors/font-awesome-4.7.0/css/font-awesome.min.css'; ?>" rel="stylesheet">
    <link href="<?= base_url() . 'assets/bundles/jquery-ui-1.12.1/themes/flick/jquery-ui.css'; ?>" rel="stylesheet">

    <?
    /* ------------------------------------------------------------------------
     *
     * Les styles (internes)
     *
     * ------------------------------------------------------------------------ */ ?>

    <link href="<?= base_url() . 'assets/css/site.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

    <? if ( ! empty($current_controller) && file_exists('assets/css/' . $current_controller . '.css')) : ?>

        <link href="<?= base_url() . 'assets/css/' . $current_controller . '.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

    <? endif; ?>

    <? if (@$current_controller == 'bienvenue' && $this->est_enseignant) : ?>

        <link href="<?= base_url() . 'assets/css/bienvenue_enseignants.css?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>" rel="stylesheet">

    <? endif; ?>

</head>

<?
/* ------------------------------------------------------------------------
 *
 * Les variables a exporter pour le javascript
 *
 * ------------------------------------------------------------------------ */ ?>

<script>
    var cct                 = "<?= $this->security->get_csrf_hash(); ?>";
    var logged_in           = "<?= $this->logged_in ? 1 : 0; ?>";
    var est_enseignant      = "<?= @$this->est_enseignant; ?>";
    var est_etudiant        = "<?= @$this->est_etudiant; ?>";
    var base_url            = "<?= base_url(); ?>";
    var current_url         = "<?= current_url(); ?>";
    var current_controller  = "<?= @$current_controller; ?>";
    var current_method      = "<?= @$current_method; ?>";
</script>

<body>

<? 
/* --------------------------------------------------------------------
 *
 * BARRE DE NAVIGATION
 *
 * -------------------------------------------------------------------- */ ?>

<? $this->load->view('commons/header_navigation'); ?>

<? 
/* --------------------------------------------------------------------
 *
 * BARRE DE STATUS
 *
 * -------------------------------------------------------------------- */ ?>

<? $this->data['barre_status'] = FALSE; ?>

<? 
/* --------------------------------------------------------------------
 *
 * BARRE DE STATUS NON PRESENTE
 *
 * -------------------------------------------------------------------- */ ?>

<? if ($this->uri->segment(1) == 'erreur') : ?>

    <div></div>

<? elseif ($this->sous_domaine == 'www' && @$current_controller == 'evaluation' && empty($current_method)) : ?>

    <div></div>

<? elseif ($this->sous_domaine == 'www' && ! $this->logged_in && @$current_controller != 'evaluation') : ?>

    <div></div>

<?
/* --------------------------------------------------------------------
 *
 * BARRE DE STATUS PRESENTE
 *
 * -------------------------------------------------------------------- */ ?>

<? elseif ($this->sous_domaine == 'www' && $this->logged_in && $this->est_etudiant) : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <? $this->load->view('commons/header_status_simple'); ?>

<? elseif ($this->uri->segment(1) == 'evaluation' && $this->uri->segment(2) != 'soumission') : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <? $this->load->view('commons/header_status_simple'); ?>

<? elseif ($this->uri->segment(1) == 'evaluation' && $this->uri->segment(2) == 'soumission') : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <? $this->load->view('commons/header_status_simple'); ?>

<? else : ?>

    <? $this->data['barre_status'] = TRUE; ?>

    <? $this->load->view('commons/header_status_simple'); ?>

<? endif; ?>

<? 
/* --------------------------------------------------------------------
 *
 * Controle de la marge du haut selon la presence ou l'absence de la barre de status
 *
 * -------------------------------------------------------------------- */ ?>

<? if ($this->data['barre_status']) : ?>

    <div style="margin-top: 130px"></div>

<? else : ?>

    <div style="margin-top: 100px"></div>

<? endif; ?>

<? 
/* --------------------------------------------------------------------
 *
 * Flash Data (Les messages d'interets generaux)
 *
 * -------------------------------------------------------------------- */ ?>

<? if ( ! empty($mg_message)) : ?>

    <div class="row">
        <div class="col-12">

            <div class="alert alert-<?= $mg_alert; ?> ml-3 mr-3" role="alert">
                <?= $mg_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-top: -2px">
                    <span aria-hidden="true" style="font-weight: 100">&times;</span>
                </button>
            </div>

            <div class="hspace"></div>

        </div> <!-- .col -->
    </div> <!-- .row -->

<? endif; ?>
