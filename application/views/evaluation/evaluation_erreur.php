<div id="evaluation-erreur">
<div class="container-fluid">

<div class="row">

<div class="col-xl-1 d-none d-xl-block"></div>

<div class="col-sm-12 col-xl-10">

	<div class="row">

		<div class="col-sm-10 mb-3">
			<h4><?= $evaluation['evaluation_titre']; ?></h4>
		</div>
		
		<div class="col-sm-2 mb-3">
		</div>

	</div>

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

                        Réponses problématiques avec CS :<br /><br />
                        <pre><? print_r($erreur['extra']['reponses_avec_cs']); ?></pre>

                    <? endif; ?>

                <? endif; ?>
            </div>

        <? endif; ?>
    </div>

</div> <!-- .row -->

</div> <!-- .col-sm-12 .mb-3 -->
<div class="col-xl-1 d-none d-xl-block">


</div> <!-- .container-fluid -->
</div> <!-- #evaluation-erreur -->
