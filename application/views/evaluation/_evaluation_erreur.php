<?
/* ============================================================================
 *
 * Une erreur a ete detectee dans votre evaluation
 *
 * ============================================================================ */ ?>

<div class="erreur">
    <div class="erreur-titre">
        <strong>ERREUR</strong> : <?= $erreur['code']; ?>
    </div>
    <div class="erreur-message">
        <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i> <?= $erreur['message']; ?>
    </div>
    <div class="erreur-solution">
        <span style="color: crimson; font-weight: bold">Solution : </span> <?= $erreur['solution']; ?>
    </div>

    <? if (array_key_exists('extra', $erreur)) : ?>
        <div class="erreur-extra">
            Question ID : <?= $erreur['extra']['question_id']; ?>

            <? if ($erreur['code'] == 'VIE1190' || $erreur['code'] === 'VIE1191') : ?>

                , Itérations : <?= $erreur['extra']['iteration']; ?> ,

                <br /><br />

                Plus le nombre d'itérations est élevé, moins il y a de chance que des réponses identiques soient générées.<br />
                Cette erreur n'apparaîtra pas lors de la présentation à l'étudiant, à moins qu'après 12 tentatives, aucune combinaison de réponses uniques n'a pu être générée.<br />
                Nous vous conseillons fortement de revoir vos variables et vos équations.

                <br /><br />

                 Réponses problématiques :

                <br /><br />

                <pre><? print_r($erreur['extra']['reponses']); ?></pre>

                <? if ($erreur['code'] == 'VIE1191') : ?>

                    Réponses problématiques suite à l'ajustement des CS :<br /><br />
                    <pre><? print_r($erreur['extra']['reponses_avec_cs']); ?></pre>

                <? endif; ?>

            <? endif; ?>
        </div>

    <? endif; ?>
</div>
