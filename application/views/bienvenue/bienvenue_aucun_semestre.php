<?
/* ----------------------------------------------------------------------------
 *
 * Bienvenue > Aucun semestre actif
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="bienvenue-aucun-semestre">
<div class="container">

    <? if ($this->est_etudiant) : ?>

        <h4 style="color: <?= $this->etudiant['genre'] == 'F' ? '#7B1FA2' : '#1976D2'; ?>">
            <? if (date('G') > 17 || date('G') < 5) : ?>
                Bonsoir 
            <? else : ?>
                Bonjour
            <? endif; ?>
            <?= $this->etudiant['prenom']; ?>
        </h4>
    
    <? else : ?>

        <h3>Évaluations</h3>

    <? endif; ?>

    <div class="tspace"></div>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Il n'y a aucun semestre en vigueur.

    <div class="tspace"></div>

    Profitez de vos vacances !


</div> <!-- .container -->
</div> <!-- #bienvenue-aucun-semestre -->
