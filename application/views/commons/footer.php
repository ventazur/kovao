<? 
/* ========================================================================
 *
 * FOOTER
 *
 * ======================================================================== */ ?>

<?  
    if ($this->is_DEV)
    {
        $this->benchmark->mark('code_end'); 
        $page_load_time = round($this->benchmark->elapsed_time('code_start', 'code_end') * 1000, 0);
    }
?>

<div id="footer" style="padding-bottom: 30px;">

    <div class="container-fluid">

        <div class="row">

            <div class="col-10">

                <div>
                    <i class="fa fa-clock-o" style="margin-right: 5px"></i><strong>Horloge du serveur :</strong> <span id="server-time"><?= date_humanize(date('U'), TRUE); ?></span>
                </div>

                <? if ($this->est_enseignant) : ?>
                    <div class="mt-1">
                        <a target="_blank" href="https://docs.google.com/document/d/1gB2gdlvXzuszN6C3DhrJeLHWjMCv6p_3ZcliIVWIFuU/edit?usp=sharing"
                           style="color: #444">
                            <i class="fa fa-question-circle" style="margin-right: 5px;"></i><strong>Manuel d'utilisation</strong>
                        </a>
                    </div>
                <? endif; ?>

            </div>

            <div class="col-2" style="text-align: right">
                <div>
                    <span class="d-none">Développé au Québec </span>
                    <img src="<?= base_url() . 'assets/images/quebec.png'; ?>" style="width: 40px; vertical-align: middle; margin-left: 7px; border-radius: 2px"/>
                </div>
            </div>

        </div> <? // .row; ?>

        <?
        /* ------------------------------------------------------------
         * 
         * INFORMATION POUR LES DEVELOPPEURS
         *
         * ------------------------------------------------------------ */ ?>

        <? if ($this->est_enseignant && $this->is_DEV) : ?>

            <div class="row">
                <div class="col-12">

                        <div style="margin-top: 20px; color: #444; padding: 10px; background: #f7f7f7; border: 1px solid pink">
                            <span style="color: crimson; font-weight: bold">DEBUG</span><br />
                            <div style="margin-top: 15px"></div>
                            Adresse IP : <?= $this->input->ip_address(); ?><br />
                            Format écran :
                                <div class="d-sm-none" style="display: inline">xs</div>
                                <div class="d-none d-sm-inline d-md-none">sm</div>
                                <div class="d-none d-md-inline d-lg-none">md</div>
                                <div class="d-none d-lg-inline d-xl-none">lg</div>
                                <div class="d-none d-xl-inline">xl</div><br />
                            Environnement : <?= ENVIRONMENT; ?><br />
                            Database : <?= $this->db->database; ?><br />
                            Kcache : <?= $this->config->item('cache_actif_dev') ? 'marche' : ( ! $this->is_DEV && $this->config->item('cache_actif') ? 'marche' : 'arrêt'); ?><br />
                            Redis : <?= $this->cache_loaded ? 'marche' : 'arrêt'; ?><br />
                            Sous-domaine : <?= $this->sous_domaine; ?></br >
                            Connexion : <?= $this->logged_in ? 'connecté' : 'non-connecté';?><br />
                            Groupe_id :  <?= $this->groupe_id; ?><br />
                            Ecole_id : <?= $this->ecole_id; ?><br />
                            Type : <?= @$this->usager['type'] ?: 'inconnu'; ?><br />
                            <? if ($this->est_enseignant) : ?>
                                Enseignant_id : <?= @$this->enseignant_id ?: 'inconnu'; ?><br />
                            <? elseif ($this->est_etudiant) : ?>
                                Etudiant_id : <?= @$this->etudiant_id ?: 'inconnu'; ?><br />
                            <? endif; ?>
                            <? if (isset($page_load_time) && ! empty($page_load_time)) : ?>
                               Temps de chargement : <?= $page_load_time; ?>ms<br />
                               <? /*
                               Temps de chargement (MY_Controller) : <?= round($this->benchmark->elapsed_time('code_start', 'my_controller') * 1000, 0); ?>ms<br />
                               Temps de chargement (Breakpoint 1) : <?= round($this->benchmark->elapsed_time('code_start', 'code1') * 1000, 0); ?>ms<br />
                               Temps de chargement (Breakpoint 2) : <?= round($this->benchmark->elapsed_time('code_start', 'code2') * 1000, 0); ?>ms<br />
                               Temps de chargement (Breakpoint 3) : <?= round($this->benchmark->elapsed_time('code_start', 'code3') * 1000, 0); ?>ms<br />
                               */ ?>
                            <? endif; ?>

                            <? if ($this->enseignant_id == 1) : ?>

                                <?= p($_SERVER); ?>

                            <? endif; ?>
                        </div>
                </div>
            </div>

        <? endif; ?>

    </div> <? // .container-fluid ?>

</div> <!-- #footer -->

</body>
</html>
