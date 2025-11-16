<?
/* ----------------------------------------------------------------------------
 *
 * MES EVALUATIONS
 *
 * ----------------------------------------------------------------------------
 *
 * Version 2 (2020-06-19)
 *
 * ---------------------------------------------------------------------------- */ ?>

<link href="<?= base_url() . 'assets/css/evaluations2.css?' . $this->now_epoch; ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/evaluations2.js?' . $this->now_epoch; ?>"></script>

<div id="mes-evaluations">

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

            <div class="col-5">

                <h3>Mes évaluations</h3>

            </div>

            <div class="col" style="text-align: right">
            
                <a class="btn btn-primary" href="<?= base_url() . $current_controller . '/creer'; ?>" role="button">
                    <i class="fa fa-plus-circle" style="margin-right: 3px"></i> 
                    Créer une évaluation
                </a>

                <a class="btn btn-outline-secondary mt-2 mt-md-0" href="<?= base_url() . $current_controller . '/archives'; ?>">
                    <i class="bi bi-archive" style="margin-right: 3px"></i>
                    Mes archives
                </a>

                <? if ($this->groupe_id != 0) : ?>
                    <a class="btn btn-outline-dark mt-2 mt-md-0" href="<?= base_url() . $current_controller . '/groupe'; ?>">
                        <svg style="margin-top: -4px; margin-right: 3px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                        </svg>
                        Évaluations du groupe
                    </a>
                <? endif; ?>

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
                    Il n'y a aucune évaluation.

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
