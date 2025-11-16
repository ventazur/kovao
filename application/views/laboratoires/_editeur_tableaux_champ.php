<?
/* --------------------------------------------------------------------
 *
 * TABLEAUX - EDITEUR DE CHAMP
 *
 * -------------------------------------------------------------------- */ ?>

<div class="laboratoire-specifique-champ mt-3">

    <div class="row">

        <?
        /* ------------------------------------------------------------
         *
         * Nom du champ
         *
         * ------------------------------------------------------------ */ ?>

        <div class="col-sm-2 text-left">
            <div class="btn">
				<? if ( ! empty($lv[$champ]['valeur'])) : ?>
					<span class="tag-champ" style="font-size: 0.8em; <?= $lv[$champ]['est_incertitude'] ? 'background: #9575CD; color: #fff' : ''; ?>"><?= $champ; ?></span>
				<? endif; ?>
            </div>
        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Description du champ
         *
         * ------------------------------------------------------------ */ ?>

        <div class="col-sm text-left">
            <div class="btn">
                <span style="font-family: Lato; font-weight: 300; font-size: 0.9em;"><?= $lab_points[$champ]['desc']; ?></span>
            </div>
        </div>

        <?
        /* ----------------------------------------------------
         *
         * Valeur du champ
         *
         * ---------------------------------------------------- */ ?>

        <div class="col-sm text-right">
            <div class="btn btn-light">

                <? if ( ! empty($lv[$champ]['valeur'])) : ?>
                       
                    <? if ($lv[$champ]['est_incertitude']) : ?>
                        <span>±</span>
                    <? endif; ?>

                    <span
                        id="<?= $lab_prefix . $champ; ?>"
                        data-name="<?= $lab_prefix . $champ; ?>"
                        data-champ="<?= $champ; ?>"
                        data-value="<?= str_replace('.', ',', $lv[$champ]['valeur']); ?>"
                    >
                        <?= str_replace('.', ',', $lv[$champ]['valeur']); ?>
                    </span>

                <? endif; ?>

            </div> <!-- .btn -->

            <? 
            /* --------------------------------------------------------
             *
             * Notation scientifique et unites
             *
             * ------------------------------------------------------- */ ?>

            <? if ( ! empty($lv[$champ]['unites'])) : ?>
                <div class="btn btn-light" disabled>
                    <?= ns_seul_format($lv[$champ]['nsci']); ?>
                    <?= $lv[$champ]['unites']; ?>
                </div>
            <? endif; ?>

        </div> <!-- .col -->

        <? if ($evaluation['enseignant_id'] == $this->enseignant_id || permis('admin_lab')) : ?>
            <div class="col-auto text-right">
                <div class="modal-tableau-modifier-champ btn btn-sm btn-light" data-toggle="modal" data-target="#modal-tableau-modifier-champ" style="color: #007bff"
                     data-evaluation_id="<?= $evaluation_id; ?>" data-champ="<?= $champ; ?>" data-champ_d="<?= ajouter_d_champ($champ); ?>">
                    <i class="bi bi-pencil-square"></i>
                </div>
            </div> <!-- .col -->
        <? endif; ?>

    </div> <!-- .row -->

</div> <!-- .laboratoire-specifique-champ -->

