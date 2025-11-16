<div id="recherche-etudiant">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Outils <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 7px"></i> Recherche d'un(e) étudiant(e)</h4>

        <script src="<?= base_url() . 'assets/js/outils.js?' . $this->now_epoch; ?>"></script>

        <div id="bienvenue-recherche" class="input-group mt-4">
            <input id="recherche-requete" type="text" class="form-control" placeholder="Rechercher par nom ou prénom" name="requete">
            <div class="input-group-append">
                <span class="input-group-text">
                    <span class="en-attente"><i class="fa fa-search"></i></span> 
                    <span class="en-precision d-none" style="cursor: pointer; padding-left: 1px; padding-right: 1px;">✕</span>
                    <span class="en-recherche d-none"><i class="fa fa-refresh fa-spin"></i></span>
                </span>
            </div>
        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Resultats de la recherche
         *
         * ------------------------------------------------------------ */ ?>

        <div id="recherche-resultats" class="mt-4">



        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Resultats de la recherche
         *
         * ------------------------------------------------------------ */ ?>
        <div id="recherche-aucun-resultat" class="mt-4 d-none" style="color: crimson">
            <i class="bi bi-exclamation-circle" style="margin-right: 3px"></i>
            Aucun résultat 
        </div>

    </div> <!-- .col -->
</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #recherche-etudiant -->
