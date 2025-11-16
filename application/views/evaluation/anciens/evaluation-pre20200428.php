<? 
/* ============================================================================
 *
 * EVALUATION
 *
 * ----------------------------------------------------------------------------
 *
 * La view lorsque l'evaluation est montree a l'etudiant.
 *
 * ============================================================================ */ ?>

<script>
    var verifier_numero_da_status = "<?= $this->config->item('verifier_numero_da') == 1 ? TRUE : FALSE; ?>";
    var pingSetting  = <?= (int) $this->config->item('ping_etudiant_evaluation'); ?>;
    var pingInterval = <?= (int) $this->config->item('ping_etudiant_evaluation_intervalle') * 1000; ?>;
</script>

<div id="evaluation-data" 
    data-etudiant_id="<?= $this->est_etudiant ? $this->etudiant_id : NULL; ?>" 
    data-evaluation_reference="<?= $evaluation_reference; ?>"
    data-evaluation_id="<?= $evaluation['evaluation_id']; ?>"></div>

<div id="evaluation">
<div class="container-fluid">

<div class="row">

<a class="anchor" name="top"></a>

<div class="col-xl-1 d-none d-xl-block">

    <?
    /* ------------------------------------------------------------------------
     *
     * NAVIGATION (SIDEBAR)
     *
     * ------------------------------------------------------------------------ */ ?>
    
    <? if ( ! empty($questions) && count($questions) > 9) : ?>

	<nav class="sidebar">

        <? 
            $q_par_col = floor(count($questions) / 2); 
            $pair      = (count($questions) % 2 == 0) ? 0 : 1;
        ?>
        <div class="table-wrap">

            <table class="table" style="margin: 0">
                <thead>
                    <tr>
                        <th colspan="2">
                            <a href="#top" style="display: block; text-decoration: none">
                                Navigation
                            </a>
                        </th>
                    </tr>
                </thead>

                <tbody>

                <? for($i = 1; $i <= ($q_par_col + $pair); $i++) : ?>

                    <tr>
                        <td id="q<?= $i; ?>box" style="text-align: center; width: 50%;">

                            <a href="#q<?= $i; ?>" style="display: block; text-decoration: none">
                                <div style="width: 100%; height: 100%">
                                    <?= 'Q' . $i; ?>
                                </div>
                            </a>

                        </td>
                        <td id="q<?= $i+ $q_par_col + $pair; ?>box" style="text-align: center; width: 50%;">

                            <? if ( ! ($i > $q_par_col)) : ?>
                                <a href="#q<?= $i + $q_par_col + $pair; ?>" style="display: block; text-decoration: none">
                                    <div style="width: 100%; height: 100%">
                                    <?= 'Q' . ($i + $q_par_col + $pair); ?>
                                    </div>
                                </a>
                            <? endif; ?>

                        </td>
                    </tr> 

                <? endfor; ?>

                </tbody>

            </table>

        </div>
    </nav>

    <? endif; // count($questions) > 10 ?>

</div> <? // /.col-x12 d-none d-xl-block ?>

<div class="col-sm-12 col-xl-10">

	<div class="row">

        <? if ( ! empty($erreur)) : ?>

            <div class="col-sm-12">

                <? if (@$erreur['status'] == 'WARNING') : ?>

                    <? $this->load->view('evaluation/_evaluation_avertissement', array('erreur' => $erreur)); ?>

                <? else : ?>

                    <? $this->load->view('evaluation/_evaluation_erreur', array('erreur' => $erreur)); ?>

                <? endif; ?>

            </div>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * INFORMATION SUR LE COURS
         *
         * ------------------------------------------------------------------------ */ ?>

        <? if (is_array($cours) && array_key_exists('cours_nom', $cours)) : ?>

            <div class="col-sm-12 mb-3 mt-sm-1">

                <div id="cours-info">

                    <table class="table table-sm table-borderless" style="margin: 0;">
                        <tr>
                            <td style="width: 90px; border-right: 1px solid inherit">
                                Cours
                            </td>
                            <td style="padding-left: 12px">
                                <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                                <?= $cours['cours_nom']; ?>
                            </td>
                        </tr>

                        <? if (is_array($evaluation_details) && 
                               array_key_exists('enseignant_prenom', $evaluation_details) && 
                               array_key_exists('enseignant_nom', $evaluation_details) &&
                               array_key_exists('enseignant_genre', $evaluation_details)) : ?>

                            <tr>
                                <td style="width: 90px; border-right: 1px solid inherit">
                                    Enseignant<?= $evaluation_details['enseignant_genre'] == 'F' ? 'e' : ''; ?>
                                </td>
                                <td style="padding-left: 12px">
                                    <i class="fa fa-angle-right" style="margin-right: 5px"></i>
                                    <?= $evaluation_details['enseignant_prenom'] . ' ' . $evaluation_details['enseignant_nom']; ?>
                                </td>
                            </tr>

                        <? endif; ?>
                    </table>

                </div>
            </div>

        <? endif; ?>

        <?
        /* ------------------------------------------------------------------------
         *
         * EST-CE QUE CETTE EVALUATION A DEJA ETE ENVOYEE ?
         *
         * ------------------------------------------------------------------------ */ ?>

        <? if ($soumission_deja_envoyee) : ?>

            <div class="col-sm-12 mb-3 mt-sm-1">

                <div class="alert alert-danger mt-3" style="margin-bottom: 0">
                    <i class="fa fa-exclamation-circle" style="color: crimson"></i>
                    Cette évaluation a déjà été envoyée. Vous ne pouvez pas la soumettre de nouveau.
                </div>

            </div>

        <? endif; ?>


        <?
        /* ------------------------------------------------------------------------
         *
         * TITRE ET POINTAGE
         *
         * ------------------------------------------------------------------------ */ ?>

		<div class="col-sm-10 mt-3 mb-3">
			<h4><?= $evaluation['evaluation_titre']; ?></h4>
		</div>
		
		<div class="col-sm-2 mt-3 mb-3">
			<div class="float-sm-right"><h5> / <?= $points_evaluation; ?> point<?= $points_evaluation > 1 ? 's' :''; ?></h5></div>
		</div>

	</div>

    <div class="hspace"></div>

	<? 
	   //
       // Initialiser les champs invisibles
       //
	   $hidden = array(
			'evaluation_id' 	     => $evaluation['evaluation_id'],
            'enseignant_id' 	     => $enseignant['enseignant_id'],
            'evaluation_reference'   => $evaluation_reference,
            'groupe_id'              => $this->groupe_id,
            'semestre_id' 		     => (@$previsualisation ? $enseignant['semestre_id'] : $semestre_id), // permet aux enseignants de tester
            'questions' 		     => count($questions),
            'questions_choisies'     => $questions_choisies,
            'variables_choisies'     => $variables_choisies,
			'confirmation1_q' 	     => "J'ai bien vérifié toutes mes réponses.",
			'confirmation1'	 	     => NULL,
			'confirmation2_q' 	     => "Je suis bien informé que seul le premier envoi sera pris en compte.",
            'confirmation2' 	     => NULL,
            'session_id'             => $session_id,
            'soumission_debut_epoch' => (is_array($traces) && array_key_exists('soumission_debut_epoch', $traces)) ? $traces['soumission_debut_epoch'] : $this->now_epoch
   	   ); 
	
	   //
       // Initialiser les champs des questions pour la soumission
       //
	   $i=0; 
	   foreach($questions as $q) 
	   {
	      $i++;
		  $hidden['question_' . $q['question_id']] = NULL;
	   }
	?>

    <?
    /* ------------------------------------------------------------------------
     *
     * FORMULAIRE D'EVALUATION
     *
     * ------------------------------------------------------------------------ */ ?>

    <?= form_open(base_url() . 'evaluation/soumission', array('id' => 'soumission-form'), $hidden); ?>

		<div class="form-row">
			<div class="col-sm-8">
				<label for="evaluation-nom">Prénom et Nom</label>
                <input name="prenom_nom" type="text" class="form-control" id="evaluation-nom" 
                    placeholder="Entrez votre prénom et nom" 
                    value="<?= $this->est_etudiant ? $this->etudiant['prenom'] . ' ' . $this->etudiant['nom'] : (is_array($traces) && array_key_exists('nom', $traces) && ! empty($traces['nom']) ? $traces['nom'] : ''); ?>" required>
				<div class="invalid-feedback d-none">
					Ce champ est obligatoire.
				</div>
			</div>
			<div class="col-sm-4 mt-3 mt-sm-0">
                <label for="evaluation-numero-da"><?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?></label>

                <input name="numero_da" type="text" class="form-control" id="evaluation-numero-da" 
                    placeholder="<?= empty($this->ecole['numero_da_desc']) ? 'Entrez votre numéro DA (9 chiffres)' : $this->ecole['numero_da_desc']; ?>"
                    value="<?= $this->est_etudiant && ! empty($this->etudiant['numero_da']) ? $this->etudiant['numero_da'] : (is_array($traces) && array_key_exists('numero_da', $traces) && ! empty($traces['numero_da']) ? $traces['numero_da'] : ''); ?>" required>

                <? if ($this->est_etudiant && empty($this->etudiant['numero_da'])) : ?>
                    <small class="form-text text-muted">
                        <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                        Vous pouvez entrer votre <?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?>
                        dans votre <a href="<?= base_url() . 'profil'; ?>">profil</a>.
                    </small>
                <? endif; ?>
				<div class="invalid-feedback d-none">
					Ce champ est obligatoire.
				</div>
	        </div>
		</div> 

        <div id="alerte-da" class="d-none alert alert-danger" style="margin-top: 30px; margin-bottom: 0px">
        
            <i class="fa fa-exclamation-circle" style="margin-right: 7px"></i>
            Votre <strong><?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?></strong> ne correspond pas à un étudiant de cet enseignant.

            <div class="mt-3">

                Erreurs possibles :

                <div class="space"></div>

                <div style="line-height: 24px">
                1. Vous avez mal entré votre <?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?>.</br >
                2. Vous avez sélectionné la mauvaise évaluation. Veuillez vérifier les renseignements ci-dessous :<br />
                    <div class="pl-4"><li>Enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?> : <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></li></div>
                    <div class="pl-4"><li>Cours : <?= $cours['cours_nom_court']; ?> (<?= $cours['cours_code']; ?>)</li></div>
                3. L'enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?> n'a pas entré sa liste d'étudiants dans le système. 
                   Ceci est à vérifier avec votre enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?>.
                </div>

                <div class="space"></div>

                Si vous pensez que ces erreurs ne s'appliquent pas à votre situation, vous pouvez les ignorer et continuer.<br />
                <strong>Cet avertisssement ne vous empêchera pas d'envoyer votre évaluation.</strong>
            </div>

        </div>

		<? 
		/* --------------------------------------------
		 * 
		 * Instructions
		 *
		 * --------------------------------------------- */ ?>
		<? if ( ! empty($evaluation['instructions'])) : ?>

            <div class="tspace"></div>

			<div id="instructions" style="margin-bottom: -10px">

				<?= html_entity_decode($evaluation['instructions']); ?>

			</div>

		<? endif; ?>

        <div class="dspace"></div>

		<? 
		/* --------------------------------------------
		 * 
		 * Questions
		 *
		 * --------------------------------------------- */ ?>

        <? 
            $i = 0; 

            foreach($questions as $q) : 
               
                $i++; 
                $question_id = $q['question_id']; 
        ?>

            <? 
            /* --------------------------------------------
             * 
             * Image
             *
             * --------------------------------------------- */ ?>

            <? if (array_key_exists($question_id, $images)) : ?>
    
                <div class="question-image" style="text-align: center; margin-top: 40px; margin-bottom: 20px;">

                    <img src="<?= base_url() . $this->config->item('documents_path') . $images[$question_id]['doc_filename']; ?>" class="img-fluid" style="max-height: 500px"></img>

                    <? if ( ! empty($images[$question_id]['doc_caption'])) : ?>

                        <p style="margin-top: 20px"><?= html_entity_decode($images[$question_id]['doc_caption']); ?></p>

                    <? endif; ?>

                </div>

            <? endif; ?>

            <a class="anchor" name="q<?= $i; ?>"></a>

			<div class="question">

				<div class="question-titre">

					<div class="row no-gutters">
						<div class="col-8">
                            <div class="question-no" style="font-size: 1.1em; font-weight: bold">
                                Question <?= $i; ?>
                                <? if ($previsualisation) : ?>
                                    <span class="badge badge-pill" style="background: #ddd; color: #888; font-size: 0.8em; font-weight: normal; margin-left: 10px">Question ID : <?= $question_id; ?></span>
                                <? endif;?>
                            </div>
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

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

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

				<? endif; // $q['question_type'] == 1 ?>

				<? 
				/* --------------------------------------------
				 * 
				 * Question a choix multiple
				 *
				* --------------------------------------------- */ ?>
				<? if ($q['question_type'] == 4) : ?>

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

						<div class="commentaire" style="padding: 10px 10px 10px 15px">

                            <span style="font-size: 0.9em; color: #888">
                                <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
                                Aucune, une ou plusieurs réponses possibles
                            </span>

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

				<? endif; // $q['question_type'] == 4 ?>

				<? 
				/* --------------------------------------------
				 * 
				 * Question a developpement
				 *
				 * --------------------------------------------- */ ?>

				<? if ($q['question_type'] == 2) : ?>

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

						<div class="form-group col-sm-12 mt-2 pl-4 pr-4">
							<label style="font-size: 0.9em; color: #888">Votre réponse à développement : </label>
                            <textarea name="question_<?= $question_id; ?>" class="form-control" rows="3" required><?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?></textarea>
							<div class="invalid-feedback">
								Ce champ est requis.
							</div>
						</div>
		
					</div>

				<? endif; // $q['question_type'] == 2 ?>

				<? 
				/* --------------------------------------------
				 * 
				 * Question a coefficients variables
				 *
                 * --------------------------------------------- */ ?>

				<? if ($q['question_type'] == 3) : ?>

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

					<?  $reponses_question = $reponses[$question_id]; ?>
                    <?  if ($q['reponses_aleatoires']) { shuffle($reponses_question); } ?>

                    <? foreach($reponses_question as $r) : ?>

						<div class="question-reponse">

							<div class="form-check">
                                <input name="question_<?= $question_id; ?>" class="form-check-input" type="radio" value="<?= $r['reponse_id']; ?>" required <?= is_array($traces) && array_key_exists($question_id, $traces) && (@$traces[$question_id] == $r['reponse_id']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" style="margin-left: 7px">
                                    <? if ($r['notsci']) : ?>
                                        <?= ns_format($r['reponse_equation']); ?>
                                    <? else : ?>
                                        <?= $r['reponse_equation']; ?>
                                    <? endif; ?>
                                    <? if ($r['unites']) : ?>
                                        <?= $r['unites']; ?>
                                    <? endif; ?>
								</label>
							</div>

						</div> <!-- /.question-reponse -->

					<? endforeach; ?>

					</div> <!-- /.question-reponses -->

                <? endif; // $q['question_type'] == 3 ?>

				<? 
				/* --------------------------------------------------------
				 * 
				 * Question a reponse numerique entiere (entier positif ou negatif)
				 *
				* --------------------------------------------------------- */ ?>
				<? if ($q['question_type'] == 5) : ?>

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

						<div class="commentaire" style="padding: 10px 10px 10px 15px">

                            <span style="font-size: 0.9em; color: #888">
                                <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
                                Votre réponse doit être un entier positif ou négatif, ou zéro (aucune lettre, ni virgule, ni point).
                            </span>

						</div>

                        <?  
                            if (count($reponses[$question_id]) == 1)
                                $unites = dot_array_search($question_id . '.*.unites', $reponses) ?: NULL; 
                            else
                                $unites = NULL;
                        ?> 

                        <div class="question-reponse" style="margin-top: 5px; margin-bottom: 5px;">

                            <div class="form-inline">
                                <div class="input-group" style="margin-right: 10px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Votre réponse :</span>
                                     </div>
                                     <input type="number" name="question_<?= $question_id; ?>" class="reponse-numerique form-control" value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>
                                    <? if ( ! empty($unites)) : ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?= $unites; ?></span>
                                        </div>
                                    <? endif; ?>
                                </div>

                            </div>

                        </div> <!-- /.question-reponse -->

					</div> <!-- /.question-reponses -->

                <? endif; // $q['question_type'] == 5 ?>

				<? 
				/* --------------------------------------------------------
				 * 
				 * Question a reponse numerique
				 *
				* --------------------------------------------------------- */ ?>
				<? if ($q['question_type'] == 6) : ?>

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

						<div class="commentaire" style="padding: 10px 10px 10px 15px">

                            <span style="font-size: 0.9em; color: #888">
                                <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
                                Votre pouvez utiliser une virgule ou un point pour les décimales. Aucune lettre permise.
                            </span>

						</div>

                        <?  
                            if (count(@$reponses[$question_id]) == 1)
                            {
                                $unites = dot_array_search($question_id . '.*.unites', $reponses) ?: NULL; 
                            }
                            else
                            {
                                $unites = NULL;
                            }
                        ?> 

                        <div class="question-reponse" style="margin-top: 5px; margin-bottom: 5px;">

                            <div class="form-inline">
                                <div class="input-group" style="margin-right: 10px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Votre réponse :</span>
                                     </div>
                                     <input type="text" name="question_<?= $question_id; ?>" class="reponse-numerique form-control" value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>
                                    <? if ( ! empty($unites)) : ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?= $unites; ?></span>
                                        </div>
                                    <? endif; ?>
                                </div>

                            </div>

                        </div> <!-- /.question-reponse -->

					</div> <!-- /.question-reponses -->

				<? endif; // $q['question_type'] == 6 ?>

				<? 
				/* --------------------------------------------------------
				 * 
				 * Question a reponse litterale courte
				 *
				* --------------------------------------------------------- */ ?>
				<? if ($q['question_type'] == 7) : ?>

                    <div class="question-data question-reponses" data-question_id="<?= $question_id; ?>" data-question_no="<?= $i; ?>">

						<div class="commentaire d-none" style="padding: 10px 10px 10px 15px">

                            <span style="font-size: 0.9em; color: #888">
                                <i class="fa fa-exclamation-circle" style="color: darkorange;"></i>
                                Vous devez entrer une lettre, un seul mot, ou plusieurs mots faisant partie d'une même idée (pas de phrase).
                            </span>

						</div>

                        <div class="question-reponse" style="margin-top: 5px; margin-bottom: 5px;">

                            <div class="form-inline">
                                <div class="input-group" style="margin-right: 10px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Votre réponse :</span>
                                     </div>
                                     <input type="text" name="question_<?= $question_id; ?>" style="width: 250px" class="reponse-litterale-courte form-control" value="<?= is_array($traces) && array_key_exists($question_id, $traces) ? $traces[$question_id] : ''; ?>" required>
                                </div>

                            </div>

                        </div> <!-- /.question-reponse -->

					</div> <!-- /.question-reponses -->

				<? endif; // $q['question_type'] == 7 ?>

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
            
            <? if ( ! $this->logged_in && $this->config->item('evaluation_confirmation_courriel')) : ?>
                <div class="row no-gutters">

                    <div class="col-sm-12 mt-3">
                        <label for="confirmation-courriel">Pour obtenir une confirmation d'envoi par courriel, entrez votre adresse :</label>
                        <input name="confirmation_courriel" type="text" class="form-control col-sm-6 col-xs-12" id="confirmation-courriel" 
                            placeholder="courriel">
                        <small class="form-text text-muted">
                            <i class="fa fa-exclamation-circle" style="color: #aaa; margin-top: 7px"></i> 
                            Ceci est facultatif, une page de confirmation s'affichera.
                        </small>
                    </div>
                </div>
            <? endif; ?>

            <div class="row no-gutters mt-4">

                <div class="evaluation-soumission-bouton">

                    <? // Ceci pour regler le bogue #11 dans le KIT ?>
                    <? if ($previsualisation && empty($enseignant['semestre_id'])) : ?>

                        <button type="submit" class="btn btn-primary" disabled>
                            Envoyer votre évaluation
                        </button>
                        <i class="fa fa-exclamation-circle" style="margin-left: 10px; margin-right: 5px"></i> Vous devez avoir un semestre sélectionné pour tester l'envoie d'évaluation.

                    <? else : ?>

                        <button id="envoyer-evaluation" type="submit" class="btn btn-primary">
                            Envoyer votre évaluation 
                            <i id="soumettre-icon" class="fa fa-spin fa-spinner d-none" style="margin-left: 7px"></i>
                        </button>

                    <? endif; ?>
                </div>

            </div>

        </div>

    </form>

    </div> <!-- .col .col-xl-10 -->
    <div class="col-xl-1 d-none d-xl-block">

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #evaluation -->
