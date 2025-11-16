<?
/* ----------------------------------------------------------------------------
 *
 * Corrections
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="corrections">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre
     *
     * ------------------------------------------------------------------------ */ ?>

    <h3>Corrections</h3>

    <?
    // ------------------------------------------
    //
    // Aucun semestre selectionne par l'enseignant
    //
    // ------------------------------------------ */ ?>

    <? if (empty($enseignant['semestre_id'])) : ?>

        <div class="dspace"></div>

        <i class="fa fa-exclamation-circle"></i> Aucun semestre sélectionné
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url() . 'configuration'; ?>">Configuration</a>

    <? endif; ?>

    <?
    // ------------------------------------------
    //
    // Aucun cours selectionne par l'enseignant
    //
    // ------------------------------------------ */ ?>

    <? if ($enseignant['semestre_id'] && empty($cours_raw)) : ?>

        <div class="dspace"></div>

        <i class="fa fa-exclamation-circle"></i> Aucun cours sélectionné pour ce semestre
        <i class="fa fa-long-arrow-right"></i> <a href="<?= base_url() . 'configuration'; ?>">Configuration</a>

    <? endif; ?>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- /.container-fluid -->
</div> <!-- #corrections -->
