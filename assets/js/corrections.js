/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > corrections.js
 *
 * ==================================================================== */
$(document).ready(function()
{
    // ----------------------------------------------------------------
    //
    // Activer les popovers
    //
    // ----------------------------------------------------------------

    $('[data-toggle="popover"]').popover()

    /* ----------------------------------------------------------------
     *
     * Ajustement des points au titre de la question
     *
     * ---------------------------------------------------------------- */
	function ajustement_points_titre(question_id, points)
	{
        if (typeof(points) === 'undefined') points = false;

		var $s = $('#points-obtenus-question-' + question_id);

		if (points === false)
		{
			$s.html('');		

            $('#q' + question_id + 'box').removeClass('corrigee');

			return false;
		}

        $('#q' + question_id + 'box').addClass('corrigee');

		if (points == 0 || points < 0)
		{
			$s.html('0 / ');		
			return false;
		}

		var points_str = Number(parseFloat(points).toFixed(2)).toString();

		points_str = points_str.replace(/^-/, '');
		points_str = points_str.replace(/\./g, ',');

		$s.html(points_str + ' / ');
	}

    /* ----------------------------------------------------------------
     *
     * Corriger
     *
     * ---------------------------------------------------------------- */

    $('.allouer-points').delegate('.points', 'click', function(e)
    {
        e.preventDefault();

        var $sel          = $(this);
        var soumission_id = $('#corriger').data('soumission_id');
        var question_id   = $sel.parents('.corriger-grille-correction').data('question_id');
        var points        = $sel.data('points');

		$sel.find('.points-pct').toggleClass('d-none');
		$sel.find('.spinner').toggleClass('d-none');

        $.post(base_url + 'corrections/allouer_points', 
            { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id, points: points },
        function(data)
        {
			if (data == 0)
			{
				ajustement_points_titre(question_id);
			}
			else
			{
				ajustement_points_titre(question_id, points);
			}

			var points_alloue = false;

			$sel.parents('.corriger-grille-correction').find('.points').each(function()
			{
				if ($(this).data('points') != points)
				{
					$(this).removeClass('alloue');
				}
				else
				{
					$(this).toggleClass('alloue');
				}

				if ($(this).hasClass('alloue'))
				{
					points_alloue = true;
				}
				
			});

			if (points_alloue)
			{
				$sel.parents('.corriger-grille-correction').find('.grille-correction-perso').addClass('d-none');

				if ($sel.hasClass('points-00'))
				{
					ajustement_points_titre(question_id, 0);
				}
			}
			else
			{
				$sel.parents('.corriger-grille-correction').find('.grille-correction-perso').removeClass('d-none');

				// Deselectionner tous les elements
				
				$('#grille-perso-question-' + question_id).find('table.elements').each(function()
				{
					$(this).removeClass('alloue');
					$(this).find('i.fa-square-o').removeClass('d-none');
					$(this).find('i.fa-check-square').addClass('d-none');
					$(this).find('i.fa-times-rectangle-o').addClass('d-none');
				});

				$('#grille-perso-points-obtenus-question-' + question_id).data('points', 0).html('0,00');
					
                ajustement_points_titre(question_id, false);
			}

			$sel.find('.points-pct').toggleClass('d-none');
			$sel.find('.spinner').toggleClass('d-none');

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Grille de correction perso
     *
     * ---------------------------------------------------------------- */
	if ($('.grille-correction-perso').length)
	{
		$('.grille-correction-perso-contenu table.elements').click(function(e)
		{
			var $sel = $(this);

			var soumission_id = $('#corriger').data('soumission_id');
			var question_id   = $sel.parents('.corriger-grille-correction').data('question_id');
			var grille_id 	  = $sel.data('grille_id');
			var element_id	  = $sel.data('element_id');

			var $gp				   = $('#grille-perso-question-' + question_id);
			var $gp_points_obtenus = $('#grille-perso-points-obtenus-question-' + question_id);
			var points_obtenus     = $gp_points_obtenus.data('points');

			//
			// Determiner les nouveaux points selon les selections.
			//

			if ($sel.hasClass('alloue'))
			{	
				$sel.removeClass('alloue');
			}
			else
			{
				$sel.addClass('alloue');
			}
		
			//
			// Changer le status du checkbox
			//
	
			$sel.find('i').toggleClass('d-none');

			//
			// Determiner les nouveaux points selon les elements selectionnes.
			// Extraire les selections.
			//

			var points = 0;
			var points_alloues = false;
			var elements_s = [];

			$gp.find('table.elements').each(function(e)
			{
				if ($(this).hasClass('alloue'))
				{	
					var p = $(this).data('points');
					
					if ($(this).hasClass('additif'))
					{
						points = points + parseFloat(p);
					}
				
					if ($(this).hasClass('deductif'))
					{
						points = points - parseFloat(p);
					}

					points_alloues = true;
					elements_s.push($(this).data('element_id'));
				}
			});	
			
			//
			// Mettre a jour l'affichage de la somme des points des elements
			//

			nouveaux_points 	 = points.toFixed(2);
			nouveaux_points_html = nouveaux_points.replace(/\./g, ',');

			$gp_points_obtenus.data('points', nouveaux_points).html(nouveaux_points_html);

			//
			// Mettre a jour l'affichage des points de la question
			//

			if (points_alloues)
			{
				ajustement_points_titre(question_id, points);
			}
			else
			{
				ajustement_points_titre(question_id);
			}

			//
			// Enregistrer dans la base de donnees
			//
			
			$.post(base_url + 'corrections/allouer_points_grille', 
				{ ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id, grille_id: grille_id, points: points, elements_s: elements_s },
			function(data)
			{


			}, 'json');

		});

        //
        // Configurer le modal
        //

        $('#modal-corriger-modifier-grille').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);
       
            var question_id = button.data('question_id');
            var anchor      = button.data('anchor');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="anchor"]').val(anchor);
        });

        //
        // Modifier une grille en cours de correction
        //

		$('#modal-corriger-modifier-grille-sauvegarde').click(function(e)
		{
            var soumission_id = $('#modal-corriger-modifier-grille-form').find('input[name="soumission_id"]').val();
            var soumission_reference = $('#modal-corriger-modifier-grille-form').find('input[name="soumission_reference"]').val();
            var evaluation_id = $('#modal-corriger-modifier-grille-form').find('input[name="evaluation_id"]').val();
            var question_id   = $('#modal-corriger-modifier-grille-form').find('input[name="question_id"]').val();
            var anchor        = $('#modal-corriger-modifier-grille-form').find('input[name="anchor"]').val();

            // Deselectionner tous les elements de la grille perso
            
            $('#grille-perso-question-' + question_id).find('table.elements').each(function()
            {
                $(this).removeClass('alloue');
                $(this).find('i.fa-square-o').removeClass('d-none');
                $(this).find('i.fa-check-square').addClass('d-none');
                $(this).find('i.fa-times-rectangle-o').addClass('d-none');
            });

            // Remettre les points a zero

            $('#grille-perso-points-obtenus-question-' + question_id).data('points', 0).html('0,00');
            ajustement_points_titre(question_id, 0);

            // Resetter la grille dans la base de donnees

        	$.post(base_url + 'corrections/reset_corrections_grille', $('#modal-corriger-modifier-grille-form').serialize(),
        	function(data)
			{
                if (data == true)
                {
                    window.location = base_url + 'evaluations/editeur/' + evaluation_id + anchor;
                    return;
                }
			}, 'json');
        });
	}	

    /* ----------------------------------------------------------------
     *
     * Allouer des points manuellement
     *
     * ---------------------------------------------------------------- */

    $('#modal-allouer-points-manuel').on('show.bs.modal', function(e) 
    {
        var button = $(e.relatedTarget); 
        var modal  = $(this);

        var question_no     = button.data('question_no');
        var question_id     = button.data('question_id');
        var question_points = button.data('question_points');

        modal.find('input[name="question_id"]').val(question_id);
        modal.find('input[name="question_points"]').val(question_points);

        $('#modal-allouer-points-manuel-question-id').html(question_id);
        $('#modal-allouer-points-manuel-question-no').html(question_no);
        $('#modal-allouer-points-manuel-total').html('/ ' + question_points);

        $('#modal-corrections-effacer-ajustement-sauvegarde').addClass('d-none');
        $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', '');

        // Nettoyer les messages d'erreur
        $('.spinnable').find('.spinner').addClass('d-none');
        $('#modal-allouer-points-manuel-obtenus').removeClass('is-invalid');
        $('#modal-allouer-points-manuel-obtenus-invalide').addClass('d-none');
    });

    $('#modal-allouer-points-manuel-sauvegarde').click(function(e)
    {
        var $modal = $('#modal-allouer-points-manuel');        

        var points_obtenus  = $modal.find('input[name="points_obtenus"]').val().replace(',','.');
        var question_points = $modal.find('input[name="question_points"]').val();

        points_obtenus  = parseFloat(points_obtenus);
        question_points = parseFloat(question_points);

        if (points_obtenus > question_points)
        {
            // Les points obtenus ne peuvent pas etre superieur au pointage de la question

            $('#modal-allouer-points-manuel-obtenus').addClass('is-invalid');
            $('#modal-allouer-points-manuel-obtenus-invalide').removeClass('d-none');
            $('.spinnable').find('.spinner').addClass('d-none');

            return true;
        }

        $.post(base_url + 'corrections/allouer_points_manuel', $('#modal-allouer-points-manuel-form').serialize(),
        function(data)
        {
            if (data == true) 
            {
                document.location.reload(true);
                return true;
            }
            else
            {
                $('#modal-allouer-points-manuel').modal('toggle');
            }
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * (modal) Effacer une soumission
     *
     * ---------------------------------------------------------------- */

    $('#modal-effacer-soumission').on('show.bs.modal', function (e) 
    {
        var button = $(e.relatedTarget); // le button qui a amorce le modal
        var soumission_id = button.data('soumission_id');
        var modal = $(this);

        //
        // mettre a jour le modal avec les donnees de la soumission demandee
        // 

        modal.find('input[name="soumission_id"]').val(soumission_id);
    });

    $('.modal').delegate('#modal-effacer-soumission-sauvegarde', 'click', function(e)
    {
        e.preventDefault();
    
        var $form = $('#modal-effacer-soumission-form');

        $.post(base_url + 'resultats/effacer_soumission', $form.serialize(),
        function(data)
        {
            if (data == true)
            {
                document.location.reload(true);
                return;
            }
            else
            {
                $('#modal-effacer-soumission').modal('hide');
            }

            return true;

        }, 'json');

    });

    /* ----------------------------------------------------------------
     *
     * Ajuster les points
     *
     * ---------------------------------------------------------------- */

    if ($('#corrections-voir').length)
    {
        $('#modal-corrections-changer-points').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); 
            var modal  = $(this);

            var question_id     = button.data('question_id');
            var points_obtenus  = button.data('points_obtenus');
            var question_points = button.data('question_points');
            var ajustement      = button.data('ajustement');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="points_obtenus"]').val(points_obtenus);
            modal.find('input[name="question_points"]').val(question_points);

            $('#modal-corrections-changer-points-question-id').html(question_id);
            $('#modal-corrections-changer-points-obtenus').val(points_obtenus);
            $('#modal-corrections-changer-points-total').html('/ ' + question_points);

            if (ajustement == 1)
            {
                $('#modal-corrections-effacer-ajustement-sauvegarde').removeClass('d-none');
                $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', question_id);
            }
            else
            {
                $('#modal-corrections-effacer-ajustement-sauvegarde').addClass('d-none');
                $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', '');
            }

            // Nettoyer les messages d'erreur
            $('.spinnable').find('.spinner').addClass('d-none');
            $('#modal-corrections-changer-points-obtenus').removeClass('is-invalid');
            $('#modal-corrections-changer-points-obtenus-invalide').addClass('d-none');
        });

        /* ----------------------------------------------------------------
         *
         * Ajuster (changer) les points d'une question
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-changer-points-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-corrections-changer-points');        

            var points_obtenus         = $modal.find('input[name="points_obtenus"]').val();
            var nouveau_points_obtenus = $modal.find('input[name="nouveau_points_obtenus"]').val();
            var question_points        = $modal.find('input[name="question_points"]').val();

            nouveau_points_obtenus = parseFloat(nouveau_points_obtenus);
            question_points = parseFloat(question_points);

            if (nouveau_points_obtenus > question_points)
            {
                $('#modal-corrections-changer-points-obtenus').addClass('is-invalid');
                $('#modal-corrections-changer-points-obtenus-invalide').removeClass('d-none');
                $('.spinnable').find('.spinner').addClass('d-none');

                return true;
            }

            if (nouveau_points_obtenus == points_obtenus)
            {
                $('#modal-corrections-changer-points').modal('toggle');
                return true;
            }

        	$.post(base_url + 'corrections/changer_points', $('#modal-corrections-changer-points-form').serialize(),
        	function(data)
            {
           		if (data == true) 
				{
                    document.location.reload(true);
					return true;
            	}
                else
                {
                    $('#modal-corrections-changer-points').modal('toggle');
                }
            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Effacer l'ajustement des points d'une questions
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-effacer-ajustement-sauvegarde').click(function(e)
        {
            var soumission_id = $('#soumission-data').data('soumission_id');
            var question_id   = $(this).data('question_id');

            if (question_id == '')
            {
                $('#modal-corrections-changer-points').modal('toggle');
            }

            $.post(base_url + 'corrections/effacer_ajustement', { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id },
            function(data)
            {
           		if (data == true) 
				{
                    document.location.reload(true);
					return true;
            	}
                else
                {
                    $('#modal-corrections-changer-points').modal('toggle');
                }

            }, 'json');

        });

    } // if #corrections-voir

    // ----------------------------------------------------------------
    //
    // Rotation d'une image
    // 
    // ----------------------------------------------------------------
    $('.files-list').on('click', '.file-rotation', function() 
    {
        var doc_id        = $(this).data('doc_id');
        var soumission_id = $('#corriger').data('soumission_id');
        var rotation      = $(this).data('rotation');
       
        var $row = $(this).parents('tr.file-data');

        $row.find('.file-operations').addClass('d-none');
        $row.find('.file-processing').removeClass('d-none');
        $row.find('.file-operations-group .file-processing-spinner').removeClass('d-none');

        $.post(base_url + 'corrections/rotation_image', 
                { ci_csrf_token: cct, doc_id: doc_id, rotation: rotation, soumission_id: soumission_id },
        function(data) 
        {
            if (data != false) 
            {
                var $image = $('#file-' + doc_id);

                $image.find('img.img-thumbnail').attr('src', data['uri_tn'] + '?' + data['doc_sha256_file']);
                $image.find('a.img-original').attr('href', data['uri'] + '?' + data['doc_sha256_file']);
                $image.find('a.file-link').attr('href', data['uri'] + '?' + data['doc_sha256_file']);

                $row.find('.file-operations').removeClass('d-none');
                $row.find('.file-processing').addClass('d-none');
                $row.find('.file-processing-spinner').addClass('d-none');
            }
            else
            {
                alert('Erreur BHKL6FH4. Un problème s\'est produit avec la rotation de l\'image.');

                $row.find('.file-operations').removeClass('d-none');
                $row.find('.file-processing').addClass('d-none');
                $row.find('.file-processing-spinner').addClass('d-none');
            }
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Laisser un commentaire a l'etudiant
     *
     * ---------------------------------------------------------------- */
    $('#modal-laisser-commentaire').on('show.bs.modal', function(e) 
    {
        var button = $(e.relatedTarget);
        var modal = $(this);
   
        var question_id = button.data('question_id');
        var commentaire = button.data('commentaire');

        modal.find('input[name="question_id"]').val(question_id);
        modal.find('textarea[name="commentaire"]').val(commentaire);
    });

    /* ----------------------------------------------------------------
     *
     * Sauvegarder le commentaire a l'etudiant
     *
     * ---------------------------------------------------------------- */
    $('#modal-laisser-commentaire-sauvegarde').click(function(e)
    {
        var $modal = $('#modal-laisser-commentaire');
        var $form = $('#modal-laisser-commentaire-form');

        var question_id          = $form.find('input[name="question_id"]').val();
        var soumission_id        = $form.find('input[name="soumission_id"]').val();
        var soumission_reference = $form.find('input[name="soumission_reference"]').val();
        var commentaire          = $form.find('textarea[name="commentaire"]').val();

        $.post(base_url + 'corrections/sauvegarder_commentaire', 
                { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id, commentaire: commentaire },
        function(data) 
        {
            if (data == true)
            {
                document.location.reload(true);
                return;
            }
            
            $modal.modal('hide');
            
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Effacer le commentaire laisse a l'etudiant
     *
     * ---------------------------------------------------------------- */
    $('#modal-laisser-commentaire-effacer').click(function(e)
    {
        var $modal = $('#modal-laisser-commentaire');
        var $form = $('#modal-laisser-commentaire-form');

        var question_id          = $form.find('input[name="question_id"]').val();
        var soumission_id        = $form.find('input[name="soumission_id"]').val();
        var soumission_reference = $form.find('input[name="soumission_reference"]').val();

        $.post(base_url + 'corrections/effacer_commentaire', 
                { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id },
        function(data) 
        {
            if (data == true)
            {
                document.location.reload(true);
                return;
            }

            $modal.modal('hide');

        }, 'json');

    });

});
