<div id="evaluation">
<div class="container">

	<div class="row no-gutters">

		<div class="col-sm-10 mb-3">
			<h4><?= $evaluation['evaluation_titre']; ?></h4>
		</div>
		
		<div class="col-sm-2 mb-3">
			<div class="float-sm-right"><h5> / <?= $points_evaluation; ?> point<?= $points_evaluation > 1 ? 's' :''; ?></h5></div>
		</div>

	</div>

    <div class="space"></div>

	<? 
	   //
       // initialiser les champs invisibles
       //
	   $hidden = array(
			'evaluation_id' 	=> $evaluation['evaluation_id'],
			'enseignant_id' 	=> $enseignant['enseignant_id'],
			'semestre_id' 		=> (@$previsualisation ? $enseignant['semestre_id'] : $semestre_id), // permet aux enseignants de tester
            'questions' 		=> count($questions),
            'questions_choisies' => $questions_choisies,
			'confirmation1_q' 	=> "J'ai bien vérifié toutes mes réponses.",
			'confirmation1'	 	=> NULL,
			'confirmation2_q' 	=> "Je suis bien informé que seul le premier envoi sera pris en compte.",
            'confirmation2' 	=> NULL,
            'session_id'        => $session_id,
            'soumission_debut_epoch' => (is_array($traces) && array_key_exists('soumission_debut_epoch', $traces)) ? $traces['soumission_debut_epoch'] : date('U')
   	   ); 
	
	   //
       // initialiser les champs des questions pour la soumission
       //
	   $i=0; 
	   foreach($questions as $q) 
	   {
	      $i++;
		  $hidden['question_' . $q['question_id']] = NULL;
	   }
	?>

    <?= form_open(base_url() . 'evaluation/soumission', array('id' => 'soumission-form'), $hidden); ?>

		<div class="form-row">
			<div class="col-sm-8 mb-4">
				<label for="evaluation-nom">Prénom et Nom</label>
                <input name="prenom_nom" type="text" class="form-control" id="evaluation-nom" placeholder="Entrez votre prénom et nom" value="<?= is_array($traces) && array_key_exists('nom', $traces) ? $traces['nom'] : ''; ?>" required>
				<div class="invalid-feedback">
					Ce champ est obligatoire.
				</div>
			</div>
			<div class="col-sm-4 mb-3">
				<label for="evaluation-numero-da">Numéro DA</label>
                <input name="numero_da" type="text" class="form-control" id="evaluation-numero-da" placeholder="Entrez votre numéro DA (9 chiffres)" value="<?= is_array($traces) && array_key_exists('numero_da', $traces) ? $traces['numero_da'] : ''; ?>" required>
				<div class="invalid-feedback">
					Ce champ est obligatoire.
				</div>
	        </div>
		</div> 

        <? /*
        <div style="text-align: center; margin-top: 15px; margin-bottom: 30px;">
            <i class="fa fa-exclamation-circle" style="color: darkorange"></i> Vous avez 2 heures pour compléter votre évaluation. Après quoi, vous devrez rafraîchir la page.
        </div>
        */ ?>

		<div class="mt-4 pt-2"></div>

		<? 
		/* --------------------------------------------
		 * 
		 * Instructions
		 *
		 * --------------------------------------------- */ ?>
		<? if ( ! empty($evaluation['instructions'])) : ?>

			<div id="instructions">

				<?= html_entity_decode($evaluation['instructions']); ?>

			</div>

			<div class="mt-4 pt-2"></div>

		<? endif; ?>

		<? 
		/* --------------------------------------------
		 * 
		 * Questions
		 *
		 * --------------------------------------------- */ ?>

		<? $i = 0; foreach($questions as $q) : $i++; $question_id = $q['question_id']; ?>

            <? 
            /* --------------------------------------------
             * 
             * Image
             *
             * --------------------------------------------- */ ?>

            <? if (array_key_exists($question_id, $images)) : ?>
    
                <div class="question-image" style="text-align: center; margin-top: 40px; margin-bottom: 40px">

                    <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

                    <? if ( ! empty($images[$question_id]['doc_caption'])) : ?>

                        <p style="margin-top: 20px"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></p>

                    <? endif; ?>

                </div>

            <? endif; ?>

			<div class="question">

				<div class="question-titre">

					<div class="row no-gutters">
						<div class="col-8">
							<div class="question-no" style="font-size: 1.1em; font-weight: bold">Question <?= $i; ?></div>
						</div>
						<div class="col-4">
							<div class="question-points float-right"><?= my_number_format($q['question_points']); ?> point<?= $q['question_points'] > 1 ? 's' : ''; ?></div>
						</div>
					</div>

				</div> <!-- /.question-titre -->

				<div class="question-texte">

					<div style="padding: 10px; font-size: 1.1em">
						<?= html_entity_decode(nl2br(filter_symbols($q['question_texte']))); ?>
					</div>
			
				</div> <!-- /.question-texte -->
			
				<? 
				/* --------------------------------------------
				 * 
				 * Question a choix unique
				 *
				* --------------------------------------------- */ ?>
				<? if ($q['question_type'] == 1) : ?>

					<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>">

					<?  $reponses_question = $reponses[$question_id]; ?>
                    <?  if ($q['reponses_aleatoires']) { shuffle($reponses_question); } ?>

					<? foreach($reponses_question as $r) : ?>

						<div class="question-reponse">

							<div class="form-check">
                            <input name="question_<?= $question_id; ?>" class="form-check-input" type="radio" value="<?= $r['reponse_id']; ?>" required <?= is_array($traces) && array_key_exists($question_id, $traces) && (@$traces[$question_id] == $r['reponse_id']) ? 'checked' : ''; ?>>
								<label class="form-check-label" style="margin-left: 7px">
									<?= filter_symbols($r['reponse_texte']); ?>
								</label>
							</div>

						</div> <!-- /.question-reponse -->

					<? endforeach; ?>

					</div> <!-- /.question-reponses -->

				<? endif; ?>

				<? 
				/* --------------------------------------------
				 * 
				 * Question a choix multiple
				 *
				* --------------------------------------------- */ ?>
				<? if ($q['question_type'] == 4) : ?>

					<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>">

						<div class="commentaire" style="padding: 10px 10px 10px 15px">

							<i class="fa fa-exclamation-circle" style="color: darkorange"></i> Aucune, une ou plusieurs réponses possibles

						</div>

						<?  $reponses_question = $reponses[$question_id]; ?>
                        <?  if ($q['reponses_aleatoires']) { shuffle($reponses_question); } ?>

						<? foreach($reponses_question as $r) : ?>

							<div class="question-reponse">

								<div class="form-check">
                                <input name="question_<?= $question_id; ?>[]" class="form-check-input" type="checkbox" value="<?= $r['reponse_id']; ?>" <?= is_array($traces) && array_key_exists($question_id, $traces) && in_array($r['reponse_id'], $traces[$question_id]) ? 'checked' : ''; ?>>
									<label class="form-check-label" style="margin-left: 7px">
										<?= filter_symbols($r['reponse_texte']); ?>
									</label>
								</div>

							</div> <!-- /.question-reponse -->

						<? endforeach; ?>

					</div> <!-- /.question-reponses -->

				<? endif; ?>

				<? 
				/* --------------------------------------------
				 * 
				 * Question a developpement
				 *
				 * --------------------------------------------- */ ?>

				<? if ($q['question_type'] == 2) : ?>

					<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>">

						<div class="form-group col-sm-12 mt-2 pl-4 pr-4">
							<label style="font-size: 0.9em; color: #999">Votre réponse à développement : </label>
                            <textarea name="question_<?= $question_id; ?>" class="form-control" rows="3" required><?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?></textarea>
							<div class="invalid-feedback">
								Ce champ est requis.
							</div>
						</div>
		
					</div>

				<? endif; ?>

				<? 
				/* --------------------------------------------
				 * 
				 * Question a coefficients variables
				 *
                 * --------------------------------------------- */ ?>

				<? if ($q['question_type'] == 3) : ?>

					<div class="question-data question-reponses" data-question_id="<?= $question_id; ?>">

					<?  $reponses_question = $reponses[$question_id]; ?>
					<?  shuffle($reponses_question); ?>

                    <? foreach($reponses_question as $r) : ?>

						<div class="question-reponse">

							<div class="form-check">
								<input name="question_<?= $question_id; ?>" class="form-check-input" type="radio" value="<?= $r['reponse_id']; ?>" required>
								<label class="form-check-label" style="margin-left: 7px">
                                    <?= $r['reponse_equation']; ?>
                                    <? if ($r['unites']) : ?>
                                        <?= $r['unites']; ?>
                                    <? endif; ?>
								</label>
							</div>

						</div> <!-- /.question-reponse -->

					<? endforeach; ?>

					</div> <!-- /.question-reponses -->


                <? endif; ?>

            </div> <? /* --- question --- */ ?>

		<? endforeach; ?>

        <div class="evaluation-soumission">

            <div class="row no-gutters">

                <div class="form-check">
                    <input name="confirmation1" id="confirmation1_q" class="confirmation form-check-input" type="checkbox" required>
                    <label class="form-check-label" for="confirmation1_q">
                        <?= $hidden['confirmation1_q']; ?>
                  </label>
                </div>

            </div>

            <div class="row no-gutters">

                <div class="form-check mt-3">
                    <input name="confirmation2" id="confirmation2_q" class="confirmation form-check-input" type="checkbox" required>
                    <label class="form-check-label" for="confirmation2_q">
                        <?= $hidden['confirmation2_q']; ?>
                  </label>
                </div>
            </div>

            <div class="row no-gutters mt-4">

                <div class="evaluation-soumission-bouton">

                    <? // Ceci pour regler le bogue #11 dans le KIT ?>
                    <? if ($previsualisation && empty($enseignant['semestre_id'])) : ?>

                        <button type="submit" class="btn btn-primary" disabled>
                            Envoyer votre évaluation
                        </button>
                        <i class="fa fa-exclamation-circle" style="margin-left: 10px; margin-right: 5px"></i> Vous devez avoir un semestre sélectionné pour tester l'envoie d'évaluation.

                    <? else : ?>

                        <button type="submit" class="btn btn-primary">
                            Envoyer votre évaluation <i id="soumettre-icon" class="fa fa-spin fa-spinner d-none" style="margin-left: 7px"></i>
                        </button>

                    <? endif; ?>
                </div>

            </div>

        </div>

	<? if ($previsualisation && ! empty($variables)) : ?>

		<div class="dspace"></div>

		<div class="row-fluid" style="border: 1px solid pink; border-radius: 3px">

			<div class="col-md-12" style="background: pink; padding: 10px; color: white">
				<span style="color: crimson">
					<i class="fa fa-exclamation-circle"></i>
					Information pour l'éditeur seulement
				</span>
			</div>

			<div class="col-md-12" style="padding: 10px">

				Nombre d'itération : <?= @$iteration; ?><br />
				<span style="color: #aaa">
					Le nombre d'itération permet de déterminer la qualité des variables et des équations choisies. Ce nombre devrait être de 1, ou de 2 à l'occasion. 
					Si ce nombre dépasse 4, il faut repenser les variables et les équations car des réponses identiques sont générées trop fréquemment.
				</span>

			</div>

		</div>
	
    <? endif; // previsualisation ?>


	</form>

</div> <!-- .container -->
</div> <!-- #evaluation -->
