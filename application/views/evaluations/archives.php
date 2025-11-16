<?
/* ----------------------------------------------------------------------------
 *
 * ARCHIVES DU GROUPE (EVALUATIONS ARCHIVEES)
 *
 * ----------------------------------------------------------------------------
 *
 * Version 1 (2023/06/07)
 *
 * ---------------------------------------------------------------------------- */ ?>

<link href="<?= base_url() . 'assets/css/evaluations2.css?' . $this->now_epoch; ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/evaluations2.js?' . $this->now_epoch; ?>"></script>

<div id="archives">

<div class="container-fluid">
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <?
        /* --------------------------------------------------------------------
         *
         * Titre
         *
         * -------------------------------------------------------------------- */ ?>

        <div class="row">

            <div class="col">

                <h3>Évaluations archivées du groupe</h3>

            </div>

            <div class="col" style="text-align: right">
            
                <a class="btn btn-outline-primary mt-2 mt-md-0" href="<?= base_url() . $current_controller . '/groupe'; ?>">
                    Évaluations du groupe
                </a>

            </div> <!-- col -->

        </div> <!-- row -->

        <?
        /* ------------------------------------------------------------------------
         *
         * Lister les cours
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="lister-cours">

            <? if ( ! empty($cours_evaluations_existent)) : ?>

                <? $this->load->view('evaluations/evaluations2_lister_cours'); ?>

            <? endif; ?>

        </div>

        <?
        /* ------------------------------------------------------------------------
         *
         * Lister les evaluations
         *
         * ------------------------------------------------------------------------ */ ?>

        <div id="lister-evaluations">

            <? if (empty($cours_evaluations_existent)) : ?>

                <div id="lister-evaluations-aucune" style="margin-top: 30px">

                    <i class="fa fa-exclamation-circle"></i>
                    Il n'y a aucune évaluation archivée.

                </div>

            <? else : ?>

                <? $this->load->view('evaluations/evaluations2_lister_evaluations'); ?>

            <? endif; ?>

        </div> <!-- #lister-evaluations -->


    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- /.container -->
</div> <!-- #mes-evaluations -->
