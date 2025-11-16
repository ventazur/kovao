<?
/* ----------------------------------------------------------------------------
 *
 * Outils
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="outils">

<?
/* ----------------------------------------------------------------------------
 *
 * Les styles specifiques
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>
    i.soustitre {
        margin-right: 5px;
        color: #C5CAE9;
    }
    .explication {
        font-size: 0.9em;
        color: #666;
    }
</style>


<div class="container-fluid">
        
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h3>Outils</h3>

        <?
        /* --------------------------------------------------------------------
         *
         * Recherches
         *
         * -------------------------------------------------------------------- */ ?>

        <div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
            <i class="fa fa-search soustitre"></i>
            Recherches
        </div>

        <ul>
            <li>
                <a href="<?= base_url() . $this->current_controller . '/recherche/etudiant'; ?>">
                    Rechercher un étudiant
                </a>
            </li>
        </ul>


        <?
        /* --------------------------------------------------------------------
         *
         * Evaluation
         *
         * -------------------------------------------------------------------- */ ?>

        <div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
            <i class="fa fa-square soustitre"></i>
            Évaluation
        </div>

        <ul>
            <li>
                <a href="<?= base_url() . $this->current_controller . '/empreinte'; ?>">
                Vérifier la validité d'une référence et de son empreinte
                </a>
            </li>
        </ul>

        <?
        /* --------------------------------------------------------------------
         *
         * Etudiants
         *
         * -------------------------------------------------------------------- */ ?>

        <? if ( ! empty($this->groupe_id) || ! empty($this->semestre_id)) : ?>

            <div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
                <i class="fa fa-user soustitre"></i>
                Étudiants
            </div>

            <ul>
                <? if ( ! empty($this->semestre_id)) : ?>
                    <li>
                        <a href="<?= base_url() . $this->current_controller . '/etudiants/relies'; ?>">
                            <span class="spinnable">
                                Détecter mes étudiants reliés 
                                <span class="spinner d-none" style="margin-left: 7px"><i class="fa fa-circle-o-notch fa-spin"></i></span>
                            </span>
                        </a>
                    </li>
                <? endif; ?>
            </ul>

        <? endif ; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils -->
