<div id="evaluation-temps-limite-confirmation">
<div class="container">

    <h3>
        Évaluation
    </h3>

    <div class="space"></div>
    
    <span style="font-weight: 600; color: crimson">Cette évaluation comporte un temps limite.</span>

    <div class="space"></div>

	Dès que vous accédez cette évaluation, le temps limite de <strong><?= $temps_limite; ?> minutes</strong> commence à s'écouler.<br />
	Le temps continue de s'écouler même si vous quittez l'évaluation.

    <div class="space"></div>

	Assurez-vous d'être disponible pendant le temps complet alloué avant d'accéder cette évaluation.

    <div class="space"></div>
    <div class="space"></div>

	<a id="letsgo" href="<?= base_url() . 'evaluation/' . $evaluation_reference . '/go'; ?>" class="btn btn-primary">
		Je suis prêt, je veux commencer !
		<i class="fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
		
	</a>
	<a href="<?= base_url(); ?>" class="btn btn-outline-danger" style="margin-left: 5px">Je ne suis pas prêt.</a>

</div> <!-- .container -->
</div> <!-- #soumission -->
