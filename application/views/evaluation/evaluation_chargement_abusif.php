<div id="evaluation-chargement-abusif">
<div class="container">

    <h3><i class="fa fa-times-circle" style="color: crimson"></i> Limite d'accès à cette évaluation excédée</h3>

    <? 
        if (empty($minutes_restantes)) 
        {
            $minutes_restantes = $this->config->item('evaluations_chargement_periode_blocage') ?: 60;
        }
    ?>

    <div class="dspace"></div>

    Vous avez fait trop de tentatives pour accéder à cette évaluation.<br />
    Cette évaluation est donc bloquée temporairement.<br />

    <div class="space"></div>

    Veuillez réessayer dans 
    <span style="color: crimson;"> <strong><?= $minutes_restantes; ?></strong> minute<?= $minutes_restantes > 1 ? 's' : ''; ?></span>.

</div> <!-- .container -->
</div> <!-- #soumission -->
