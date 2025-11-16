<? 
/* ====================================================================
 *
 * BARRE DE STATUS (ECOLE / GROUPE / ENSEIGNANT)
 *
 * ==================================================================== */ ?>

<nav id="navbar-ecole" class="navbar navbar-expand-md navbar-light bg-light fixed-top" style="z-index: 4; margin-top: 61px;">

    <div id="navbar-2" class="container-fluid">

        <div class="text-nowrap text-truncate">

            <?
            /* --------------------------------------------------------
             *
             * ECOLE / GROUPE
             *
             * -------------------------------------------------------- */ ?>

            <? if ( ! empty($this->groupe_id)) : ?>

                <? if ( ! empty($ecole)) : ?>

                    <?= $ecole['ecole_nom']; ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>

                <? endif; ?>

                <? if ( ! empty($groupe)) : ?> 
                
                    <? if ($this->groupe_id != 0) : ?>
                        <?= $groupe['groupe_nom']; ?>

                        <? if ( ! $this->groupe['actif']) : ?>

                            <span style="margin-left: 5px; margin-right: 5px">|</span>
                            <span style="color: crimson; font-weight: 400">DÉSACTIVÉ</span>

                        <? endif; ?>
                    <? endif; ?>

                <? endif; ?>

            <? endif; ?>

            <? if ($this->est_enseignant && ! empty($this->enseignant)) : ?>

                <? if ( $this->groupe_id != 0) : ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                <? else : ?>
                    <i class="fa fa-square-o" style="margin-right: 5px"></i>
                <? endif; ?>

                <?= $this->enseignant['prenom'] . ' ' . $this->enseignant['nom']; ?>

                <? if (@$this->enseignant['semestre_id']) : ?>

                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                    <?= $semestres[$this->enseignant['semestre_id']]['semestre_code']; ?>

                <? endif; ?>

            <? elseif ($this->est_etudiant && ! empty($this->etudiant)) : ?>

                <? if ( $this->groupe_id != 0) : ?>
                    <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                <? else : ?>
                    <i class="fa fa-square-o" style="margin-right: 5px"></i>
                <? endif; ?>

                <?= $this->etudiant['prenom'] . ' ' . $this->etudiant['nom']; ?>

            <? endif; ?>

            <?
            /* -------------------------------------------------------
             *
             * Usurpation de l'identite par l'admin
             *
             * ------------------------------------------------------- */ ?>

            <? if (isset($this->usurp) && $this->usurp !== FALSE) : ?>
                <i class="fa fa-angle-right" style="margin-left: 5px; margin-right: 5px"></i>
                <a id="terminer-udata" href="<?= base_url() . 'deconnexion/terminer_usurpation'; ?>">
                    <i class="fa fa-lg fa-times-circle"></i>
                </a>
            <? endif; ?>
        </div>

    </div> <!-- #navbar-2 -->
</nav>
