/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > Editeur.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Restaurer la position de défilement après le chargement de la page
     *
     * ---------------------------------------------------------------- */

	var scrollPosition = sessionStorage.getItem('scrollPosition');

	if (scrollPosition !== null) 
	{
		window.scrollTo(0, parseInt(scrollPosition, 10));
		sessionStorage.removeItem('scrollPosition'); // Nettoyer après utilisation
	}

    /* ----------------------------------------------------------------
     *
     * Remettre la position de la navbar a sa position avant le dernier
     * reload de la page.
     *
     * ---------------------------------------------------------------- */

    var scroll_evaluation_id = localStorage.getItem('evaluation_id');
    var scroll_pos = localStorage.getItem('scoll_pos');

    if (scroll_evaluation_id == $('#evaluation-data').data('evaluation_id'))
    {
        $('#editeur-sidebar').scrollTop(scroll_pos);
    }
    else
    {
        localStorage.removeItem('evaluation_id');
        localStorage.removeItem('scroll_pos');
    }

    /* ----------------------------------------------------------------
     *
     * Enregistrer la position du scroll de la barre de navigation
     *
     * ---------------------------------------------------------------- */
    $('#editeur-sidebar').scroll(function()
    {
        scroll_pos = $(this).scrollTop();
        localStorage.setItem('evaluation_id', $('#evaluation-data').data('evaluation_id'));
        localStorage.setItem('scoll_pos', scroll_pos);
    });

    /* ----------------------------------------------------------------
     *
     * Populer le select par des evaluations, pour l'importation, l'exportation et la copie.
     *
     * Parametres :
     *
     * $sel               le selector a modifier
     * cours_id           le cours qui comporte les evaluations
     * evaluation_id      l'evaluation qu'il faut enlever de la liste (si copie, utile)
     * evaluation_public  chercher les evaluations privees (0) ou du departement (1)
     *
     * ---------------------------------------------------------------- */
    function populer_select_evaluations($sel, cours_id, evaluation_id, evaluation_public)
    {
        $.post(base_url + 'evaluations/lister_evaluations', 
                { ci_csrf_token: cct, evaluation_id: evaluation_id, cours_id: cours_id, evaluation_public: evaluation_public },

        function(data)
        {
            if (data != false)
            {
                $sel.empty().html(data);
            }

        }, 'json');
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter des caracteres speciaux
     *
	 * Inspire de : https://stackoverflow.com/questions/1064089/inserting-a-text-where-cursor-is-using-javascript-jquery
     *
     * ---------------------------------------------------------------- */
    if ($('.special-symbol').length)
    {
		function getSelectedText() 
		{
			if (window.getSelection) 
			{
				return window.getSelection();
			} 
			if (window.document.getSelection) 
			{
				return window.document.getSelection();
			} 
			if (window.document.selection) 
			{
				return window.document.selection.createRange().text;
			}
			return "";  
		}

		$('.special-symbol').click(function() {

			var $sel         = $(this);

            var symbol       = $sel.data('symbol');
			var symbol_start = $sel.data('symbol_start');
			var symbol_end   = $sel.data('symbol_end');
	
            var $input = $sel.closest('form').find('textarea');
		
			var input_text = $input.val();
			var caretPos   = $input[0].selectionStart;

			var selectedText = getSelectedText();
	
			if (typeof(symbol_start) == 'undefined')
			{
				var nouveau_text = symbol;
				$input.val(input_text.substring(0, caretPos) + symbol + input_text.substring(caretPos));
			}
			else
			{
				if (selectedText == "")
				{
					if (typeof(symbol_start) == 'undefined')
					{
						var nouveau_text = symbol;
					}
					else
					{
						var nouveau_text = symbol_start;
						$input.val(input_text.substring(0, caretPos) + symbol + input_text.substring(caretPos));
					}
				}
				else
				{
					var nouveau_text = symbol_start + selectedText + symbol_end;
					var textBeforeSelection = $input.val().substring(0, $input[0].selectionStart);
					
					$input.val($input.val().replace(textBeforeSelection + selectedText, textBeforeSelection + symbol_start + selectedText + symbol_end));
				}
			}

            $input.focus().prop('selectionEnd', caretPos + nouveau_text.length);
		});
	}

	/* ================================================================
	 *
     * EVALUATIONS
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Expoter JSON - Exporter une evaluation en format JSON
     *
     * ---------------------------------------------------------------- */
    if ($('#exporter-json').length)
    {
        $('#exporter-json').click(function()
        {
            var $sel = $(this);

            var evaluation_id = $sel.data('evaluation_id');

        	$.post(base_url + 'evaluations/exporter_json', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                if (data != false)
                {
            		const blob = new Blob([data], { type: 'application/json' });

					const epoch = Math.floor(Date.now() / 1000);
					const filename = 'kovao_evaluation_' + evaluation_id + '_' + epoch + '.json';

					// Crée un lien de téléchargement temporaire
					const url = URL.createObjectURL(blob);
					const a = document.createElement('a');
					a.href = url;
					// a.download = 'data.json';
					a.download = filename;
					document.body.appendChild(a);
					a.click();
					document.body.removeChild(a);
					URL.revokeObjectURL(url); // Nettoie l'URL blob

					// window.location = base_url;
                    // document.location.reload();
                    // return false;
                }
            }, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Mettre en ligne une evaluation
     *
     * ---------------------------------------------------------------- */
    if ($('#mettre-en-ligne').length)
    {
        $('#mettre-en-ligne').click(function()
        {
            var $sel = $(this);

            var evaluation_id = $('#evaluation-data').data('evaluation_id');
            var evaluation_selectionnee = $('#evaluation-data').data('evaluation_selectionnee');

            if (evaluation_selectionnee == 1)
                return false;

        	$.post(base_url + 'evaluations/mettre_en_ligne', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                if (data == true)
                {
					window.location = base_url;
                    // document.location.reload();
                    return false;
                }
                else
                {
                    $sel.find('.spinner').addClass('d-none');

                    $('#mettre-en-ligne-erreur').removeClass('d-none').find('span').html(data['error_msg']);
                }

            }, 'json');
        });

        $('#mettre-en-ligne-erreur').click(function()
        {
            $(this).addClass('d-none');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Empecher les changements par les tiers (cadenas)
     *
     * ---------------------------------------------------------------- */
    if ($('#evaluation-cadenas').length)
    {
        $('#evaluation-cadenas').change(function() {

            var evaluation_id = $('#evaluation-data').data('evaluation_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_cadenas', { ci_csrf_token: cct, checked: checked, evaluation_id: evaluation_id },
        	function()
        	{
                document.location.reload();
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier le titre de l'evaluation
     *
     * ---------------------------------------------------------------- */

	if ($('#titre-evaluation-original').length)
	{
		var evaluation_titre = $('#titre-evaluation-original').html().trim();

        //
        // affichage du modal
        //
        $('#modal-modifier-titre').on('show.bs.modal', function (e) 
        {

            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            //
            // mettre a jour le modal avec les donnees de la question demandee
            // 

			modal.find('textarea').val(_.unescape(evaluation_titre));
        });

        //
        // sauvegarde des modifications au titre
        //
    	$('.modal').delegate('#modal-modifier-titre-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

			if ($('#modal-modifier-titre-evaluation-titre').val() == evaluation_titre)
			{
				// si aucun changement detecte, simplement fermer le modal
				$('#modal-modifier-titre').modal('hide')
				return true;
			}

        	$.post(base_url + 'evaluations/modifier_titre', $('#modal-modifier-titre-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}
            	else if ($.isPlainObject(data)) 
				{
					// remove previous errors
					$('#modal-editeur-question-form :input').each(function(index) 
					{
						$(this).removeClass('is-invalid');
					});

                	// errors
                	for (var property in data) 
					{
						if (data.hasOwnProperty(property)) 
						{
							console.log('#modal-editeur-' + property.replace(/_/g, '-'));
							var $field = $('#modal-editeur-' + property.replace(/_/g, '-'));

							$field.addClass('is-invalid');
						}
					}
                }
        	}, 'json');
    	});
	} // if #titre-evaluation length

    /* ----------------------------------------------------------------
     *
     * (Admin) Changer le responsable de l'evaluation
     *
     * ---------------------------------------------------------------- */

    if ($('#changer-responsable').length) 
    {
        $('#modal-changer-responsable').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);
            var evaluation_id = $('#evaluation-data').data('evaluation_id');

        	$.post(base_url + 'evaluations/lister_enseignants', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
			{
				data = JSON.parse(data);

				if (data != false)
				{
					$('#changer-responsable-select').empty().html(data);	
				}
				else
				{
					return e.preventDefault()
				}
			});
        });

        $('#modal-changer-responsable-sauvegarde').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'evaluations/changer_responsable', $('#modal-changer-responsable-form').serialize(),
        	function(data)
        	{
                document.location.reload();
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifiter l'ordre de presentation de l'evaluation
     *
     * ---------------------------------------------------------------- */

    if ($('#ordre-evaluation').length)
    {
        $('#ordre-evaluation-sauvegarde').click(function(e)
        {
            e.preventDefault();;
    
            var ordre = $('#ordre-evaluation').val();
            var evaluation_id = $(this).data('evaluation_id');

            $('#ordre-evaluation-sauvegarde-action').removeClass('d-none');

        	$.post(base_url + 'evaluations/changer_ordre_evaluation', { ci_csrf_token: cct, ordre: ordre, evaluation_id: evaluation_id },
        	function()
        	{
                $('#ordre-evaluation-sauvegarde-action').addClass('d-none');
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Dupliquer une evaluation
     *
     * ---------------------------------------------------------------- */

    $('#modal-dupliquer-evaluation').on('show.bs.modal', function (e) 
    {
        var button = $(e.relatedTarget);
        var evaluation_id = button.data('evaluation_id');
        var modal = $(this);

        modal.find('input[name="evaluation_id"]').val(evaluation_id);
    });

    //
    // Executer la duplication.
    //

    if ($('#dupliquer-evaluation').length) 
    {
        $('#modal-dupliquer-evaluation-sauvegarde').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'evaluations/dupliquer_evaluation', $('#modal-dupliquer-evaluation-form').serialize(),
        	function(data)
        	{
                // Redirection vers l'evaluation cible (data = evaluation_id).
              
				if (typeof data === 'object' && 'action' in data && data['action'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                window.location = base_url + "evaluations";
                return false;
        	}, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Copier une evaluation vers un autre cours
     *
     * ---------------------------------------------------------------- */

    if ($('#copier-evaluation2').length) 
    {
        /* Le groupe selectionne a ete change. */
        $('#copier-evaluation-groupe-id').change(function()
        {
            var cours_id_actuel = $('#evaluation-data').data('cours_id');
            var copier_evaluation_groupe_id  = $('#copier-evaluation-groupe-id').val();

        	$.post(base_url + 'evaluations/lister_cours', { ci_csrf_token: cct, groupe_id: copier_evaluation_groupe_id, cours_id: cours_id_actuel },
        	function(data)
			{
				data = JSON.parse(data);

				if (data != false)
				{
					$('#copier-evaluation-cours-id').empty().html(data);	
				}
				else
				{
					$('#copier-evaluation-cours-id').empty().html('');	
					return e.preventDefault()
				}
			});
        });
        
        $('#modal-copier-evaluation-sauvegarde').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'evaluations/importer_exporter_copier_evaluation_vers_cours', $('#modal-copier-evaluation-form').serialize(),
        	function(data)
        	{
                // Redirection vers l'evaluation cible (data = evaluation_id).
              
				if (typeof data === 'object' && 'action' in data && data['action'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

                /*
				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $erreur = $('#modal-copier-evaluation-erreur');

                    $erreur.find('.erreur-code').empty().html(data['code']);
                    $erreur.find('.erreur-message').empty().html(data['message']);
                    $erreur.find('.erreur-solution').empty().html(data['solution']);

                    $erreur.removeClass('d-none');

				    $('#modal-copier-evaluation-sauvegarde').find('.spinner').addClass('d-none');

                    return false;
				}
                */

                window.location = base_url + "evaluations";
                return false;
        	}, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Effacer l'evaluation
     *
     * ---------------------------------------------------------------- */

    if ($('#effacer-evaluation').length) 
    {
        var evaluation_id = $('#effacer-evaluation').data('evaluation_id');

        $('#modal-effacer-evaluation-sauvegarde').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'evaluations/effacer_evaluation', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                window.location = base_url + "evaluations";
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Archiver & desarchiver une evaluation
     *
     * ---------------------------------------------------------------- */

    if ($('#archiver-evaluation, #desarchiver-evaluation').length) 
    {
        $('#archiver-evaluation, #desarchiver-evaluation').click(function(e)
        {
            e.preventDefault();

            var evaluation_id = $(this).data('evaluation_id');

        	$.post(base_url + 'evaluations/archiver_desarchiver_evaluation', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                document.location.reload();
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Activer & desactiver une evaluation
     *
     * ---------------------------------------------------------------- */

    if ($('#activer-evaluation, #desactiver-evaluation').length) 
    {
        $('#activer-evaluation, #desactiver-evaluation').click(function(e)
        {
            e.preventDefault();

            var evaluation_id = $(this).data('evaluation_id');

        	$.post(base_url + 'evaluations/activer_desactiver_evaluation', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                document.location.reload();
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Importer une evaluation dans mes evaluations
     *
     * ---------------------------------------------------------------- */

    if ($('#importer-evaluation').length) 
    {
        var evaluation_id = $('#importer-evaluation').data('evaluation_id');

        $('#modal-importer-evaluation-sauvegarde').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'evaluations/importer_exporter_copier_evaluation_vers_cours', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                // Redirection vers l'evaluation cible (data = evaluation_id).
              
				if (typeof data === 'object' && 'action' in data && data['action'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

                /*
				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $erreur = $('#modal-copier-evaluation-erreur');

                    $erreur.find('.erreur-code').empty().html(data['code']);
                    $erreur.find('.erreur-message').empty().html(data['message']);
                    $erreur.find('.erreur-solution').empty().html(data['solution']);

                    $erreur.removeClass('d-none');

				    $('#modal-copier-evaluation-sauvegarde').find('.spinner').addClass('d-none');

                    return false;
				}
                */

                window.location = base_url + "evaluations";
                return false;
            }, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Exporter une evaluation dans les evaluations du departement
     *
     * ---------------------------------------------------------------- */

    if ($('#exporter-evaluation').length) 
    {
        var evaluation_id = $('#exporter-evaluation').data('evaluation_id');

        $('#modal-exporter-evaluation-sauvegarde').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'evaluations/importer_exporter_copier_evaluation_vers_cours', { ci_csrf_token: cct, evaluation_id: evaluation_id },
        	function(data)
        	{
                // Redirection vers l'evaluation cible (data = evaluation_id).
              
				if (typeof data === 'object' && 'action' in data && data['action'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

                /*
				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $erreur = $('#modal-copier-evaluation-erreur');

                    $erreur.find('.erreur-code').empty().html(data['code']);
                    $erreur.find('.erreur-message').empty().html(data['message']);
                    $erreur.find('.erreur-solution').empty().html(data['solution']);

                    $erreur.removeClass('d-none');

				    $('#modal-copier-evaluation-sauvegarde').find('.spinner').addClass('d-none');

                    return false;
				}
                */

                window.location = base_url + "evaluations";
                return false;
        	}, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier la description
     *
     * ---------------------------------------------------------------- */

	if ($('#evaluation-description').length)
	{
		var evaluation_description = null;

        //
        // affichage du modal
        //
        $('#modal-modifier-description').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			evaluation_description = $('#modal-modifier-description-input').val();
        });

        //
        // Sauvegarde de la description
        //
    	$('.modal').delegate('#modal-modifier-description-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

			if ($('#modal-modifier-description-input').val() != evaluation_description)
			{
				$.post(base_url + 'evaluations/modifier_description', $('#modal-modifier-description-form').serialize(),
				function(data)
				{
					document.location.reload();
					return true;
				}, 'json');
			}
			else
			{
				// Si aucun changement detecte, simplement fermer le modal
				$('#modal-modifier-description').modal('hide')
				return true;
			}
    	});

        //
        // Effacer la description
        //
    	$('.modal').delegate('#modal-modifier-description-effacer', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/effacer_description', $('#modal-modifier-description-form').serialize(),
        	function(data)
        	{
				document.location.reload();
				return true;
        	}, 'json');
    	});

	} // if #description-evaluation length

	/* ================================================================
	 *
     * QUESTIONS
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Copier une question d'une evaluation vers une autre evaluation
     * de meme categorie (privee ou publique). 
     *
     * ---------------------------------------------------------------- */

    if ($('.copier-question').length) 
    {
        var $copier_question                    = $('#modal-copier-question');
        var $copier_question_sauvegarde         = $('#modal-copier-question-sauvegarde');
        var $copier_question_select_cours       = $('#modal-copier-question-cours-select');
        var $copier_question_select_evaluations = $('#modal-copier-question-evaluations-select');   
        var $copier_question_erreur             = $('#modal-copier-question-erreur');

        $copier_question.on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal

			var question_id       = button.data('question_id');
        	var evaluation_id     = $('#evaluation-data').data('evaluation_id');
            var evaluation_public = $('#evaluation-data').data('evaluation_public');

            $copier_question.find('input[name="question_id"]').val(question_id);

            //
            // Populer le modal de cours et d'evaluations.
            // 

        	$.post(base_url + 'evaluations/lister_cours_avec_evaluation', { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_public: evaluation_public },
        	function(data)
        	{
				if (data != false)
				{
					var cours_id = $copier_question_select_cours.empty().html(data).val();

                    populer_select_evaluations($copier_question_select_evaluations, cours_id, evaluation_id, evaluation_public);
				}
        	}, 'json');
        });

		$copier_question_select_cours.change(function()
		{
			var cours_id 	      = $(this).val();
        	var evaluation_id     = $copier_question.find('input[name="evaluation_id"]').val();
            var evaluation_public = $('#evaluation-data').data('evaluation_public');

            populer_select_evaluations($copier_question_select_evaluations, cours_id, evaluation_id, evaluation_public);
		});

		$copier_question_sauvegarde.click(function()
		{
			var cours_id      = $copier_question_select_cours.val();
			var evaluation_id = $copier_question_select_evaluations.val();
			var question_id   = $copier_question.find('input[name="question_id"]').val();

        	$.post(base_url + 'evaluations/copier_question_vers_evaluation', { ci_csrf_token: cct, question_id: question_id, evaluation_id: evaluation_id, cours_id: cours_id },
        	function(data)
			{
                // Redirection vers l'evaluation cible (data = evaluation_id)

                if ($.isNumeric(data))
                {
				    window.location = base_url + 'evaluations/editeur/' + data;
                    return false;
                }

                // Erreur necessitant une redirection

				if (typeof data === 'object' && 'action' in data && data['action'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $copier_question_erreur.find('.erreur-code').empty().html(data['code']);
                    $copier_question_erreur.find('.erreur-message').empty().html(data['message']);
                    $copier_question_erreur.find('.erreur-solution').empty().html(data['solution']);

                    $copier_question_erreur.removeClass('d-none');

				    $copier_question_sauvegarde.find('.spinner').addClass('d-none');

                    return false;
				}
        	}, 'json');
		});
	}

    /* ----------------------------------------------------------------
     *
     * Importer/Exporter une question (variables)
     *
     * ---------------------------------------------------------------- */

    if ($('.exporter-question').length) 
    {
        var $impex_question                    = $('#modal-exporter-question');
        var $impex_question_sauvegarde         = $('#modal-exporter-question-sauvegarde'); // L'execution de la procedure
        var $impex_question_select_cours       = $('#modal-exporter-question-cours-select');
        var $impex_question_select_evaluations = $('#modal-exporter-question-evaluations-select');
        var $impex_question_erreur             = $('#modal-exporter-question-erreur'); // L'endroit ou afficher les erreurs

        var impex_question_evaluation_public   = 1; // exporter si 1
        var evaluation_id                      = $('#evaluation-data').data('evaluation_id');
    }

    if ($('.importer-question').length)
    {
        var $impex_question                    = $('#modal-importer-question');
        var $impex_question_sauvegarde         = $('#modal-importer-question-sauvegarde'); // L'execution de la procedure
        var $impex_question_select_cours       = $('#modal-importer-question-cours-select');
        var $impex_question_select_evaluations = $('#modal-importer-question-evaluations-select');
        var $impex_question_erreur             = $('#modal-importer-question-erreur'); // L'endroit ou afficher les erreurs

        var impex_question_evaluation_public   = 0; // importation si 0
        var evaluation_id                      = $('#evaluation-data').data('evaluation_id');
    }

    /* ----------------------------------------------------------------
     *
     * Importer/Exporter une question
     *
     * ---------------------------------------------------------------- */
    if ($('.importer-question, .exporter-question').length)
    {
        $impex_question.on('show.bs.modal', function(e) 
        {
            var button      = $(e.relatedTarget);
			var question_id = button.data('question_id');

            $impex_question.find('input[name="question_id"]').val(question_id);

            // Dissimuler les erreurs precedentes.

            $impex_question_erreur.addClass('d-none');

            //
            // Populer le modal de cours/evaluations pour une premiere fois.
            // 

        	$.post(base_url + 'evaluations/lister_cours_avec_evaluation',
                    { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_public: impex_question_evaluation_public },
        	function(data)
        	{
				if (data != false)
				{
					var cours_id = $impex_question_select_cours.empty().html(data).val();
                    
                    populer_select_evaluations($impex_question_select_evaluations, cours_id, evaluation_id, impex_question_evaluation_public);
				}
        	}, 'json');
        });

        //
        // Repopuler le modal de cours/evaluations suite a un changement.
        //

		$impex_question_select_cours.change(function()
		{
			var cours_id 	  = $impex_question_select_cours.val();
        	var evaluation_id = $('#evaluation-data').data('evaluation_id');

            populer_select_evaluations($impex_question_select_evaluations, cours_id, evaluation_id, impex_question_evaluation_public);
		});

		$impex_question_sauvegarde.click(function()
		{
			var cours_id      = $impex_question_select_cours.val();
			var evaluation_id = $impex_question_select_evaluations.val();
			var question_id   = $impex_question.find('input[name="question_id"]').val();

        	$.post(base_url + 'evaluations/copier_question_vers_evaluation', 
                    { ci_csrf_token: cct, question_id: question_id, evaluation_id: evaluation_id, cours_id: cours_id },
        	function(data)
			{
                // Redirection vers l'evaluation cible (data = evaluation_id).

                if ($.isNumeric(data))
                {
				    window.location = base_url + 'evaluations/editeur/' + data;
                    return false;
                }

                // Erreur necessitant une redirection.

				if (typeof data === 'object' && 'action' in data && data['action'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $impex_question_erreur.find('.erreur-code').empty().html(data['code']);
                    $impex_question_erreur.find('.erreur-message').empty().html(data['message']);
                    $impex_question_erreur.find('.erreur-solution').empty().html(data['solution']);

                    $impex_question_erreur.removeClass('d-none');

				    $impex_question_sauvegarde.find('.spinner').addClass('d-none');

                    return false;
				}

        	}, 'json');
		});
	}

    /* ----------------------------------------------------------------
     *
     * Permettre la presentation de l'ordre des questions aleatoirement
     *
     * ---------------------------------------------------------------- */
    if ($('#questions-aleatoires').length)
    {
        $('#questions-aleatoires').change(function() {

            var evaluation_id = $('#evaluation-data').data('evaluation_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_questions_aleatoires', { ci_csrf_token: cct, checked: checked, evaluation_id: evaluation_id },
        	function()
        	{
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Inscription requise
     *
     * ---------------------------------------------------------------- */
    if ($('#evaluation-inscription-requise').length)
    {
        $('#evaluation-inscription-requise').change(function() {

            var evaluation_id = $('#evaluation-data').data('evaluation_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_inscription_requise', { ci_csrf_token: cct, checked: checked, evaluation_id: evaluation_id },
        	function()
        	{
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Temps en redaction
     *
     * ---------------------------------------------------------------- */
    if ($('#evaluation-temps-en-redaction').length)
    {
        //
        // Ceci permet d'afficher ou de cacher cette option (temps en redaction)
        // selon si l'inscription requise est activee ou non.
        //

        $('input[name="evaluation_inscription_requise"]').change(function(e)
        {
            if ($(this).prop('checked') == true)
            {
                $('#temps-en-redaction').removeClass('d-none');
            }
            else
            {
                $('#temps-en-redaction').addClass('d-none');
            }
        });

        $('#evaluation-temps-en-redaction').change(function() {

            var evaluation_id = $('#evaluation-data').data('evaluation_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_temps_en_redaction', { ci_csrf_token: cct, checked: checked, evaluation_id: evaluation_id },
        	function()
        	{
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Permettre la presentation de l'ordre des questions aleatoirement
     *
     * ---------------------------------------------------------------- */
    if ($('#evaluation-formative').length)
    {
        $('#evaluation-formative').change(function() {

            var evaluation_id = $('#evaluation-data').data('evaluation_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_evaluation_formative', { ci_csrf_token: cct, checked: checked, evaluation_id: evaluation_id },
        	function()
        	{
                return true;
            });
        });
    }

	/* ================================================================
	 *
     * VARIABLES
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Ajouter une nouvelle variable (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-variable').length) 
    {
    	$('.modal').delegate('#modal-ajout-variable-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

        	$.post(base_url + 'evaluations/ajouter_variable', $('#modal-ajout-variable-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                if (data != true && data != false)
                {
                    $('#modal-ajout-variable').find('.alert span.alertmsg').html(data).parents('.alert').removeClass('d-none');
                }
        	}, 'json');
    	});

    } // if length #modal-ajout-variable

    /* ----------------------------------------------------------------
     *
     * Modifier une variable
     *
     * ---------------------------------------------------------------- */

    if ($('.modifier-variable').length) 
    {
        $('#modal-modifier-variable').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); // button qui amorce le modal
            var modal = $(this);

            var variable_id  = button.data('variable_id');
            var variable_nom = button.data('nom');
            var minimum      = button.data('minimum');
            var maximum      = button.data('maximum');
            var decimales    = button.data('decimales');
            var ns           = button.data('ns');
            var cs           = button.data('cs');
            var desc         = button.data('variable_desc');

            modal.find('#modal-modifier-variable-nom').html(variable_nom);
            modal.find('input[name="variable_id"]').val(variable_id);
            modal.find('input[name="variable_nom"]').val(variable_nom);
            modal.find('input[name="minimum"]').val(minimum);
            modal.find('input[name="maximum"]').val(maximum);
            modal.find('input[name="decimales"]').val(decimales);
            modal.find('select[name="ns"]').val(ns);
            modal.find('select[name="cs"]').val(cs);
            modal.find('input[name="variable_desc"]').val(desc);
        });

    	$('.modal').delegate('#modal-modifier-variable-sauvegarde', 'click', function(e)
    	{
        	$.post(base_url + 'evaluations/modifier_variable', $('#modal-modifier-variable-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                if (data != true && data != false)
                {
                    $('#modal-modifier-variable').find('.alert span.alertmsg').html(data).parents('.alert').removeClass('d-none');
                }

        	}, 'json');
    	});

    	$('.modal').delegate('#modal-modifier-variable-effacer', 'click', function(e)
    	{
        	$.post(base_url + 'evaluations/effacer_variable', $('#modal-modifier-variable-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                $('#modal-modifier-variable').modal('hide'); 

        	}, 'json');
        });
    } // if length

    /* ----------------------------------------------------------------
     *
     * Tester les variables - Rafraichir les valeurs
     *
     * ---------------------------------------------------------------- */

    if ($('#tester-variables-box').length)
    {
        $('#tester-variables-rafraichir').click(function(e)
        {
            document.location.reload();
            return true;
        });
    }

	/* ================================================================
	 *
     * BLOCS
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Ajouter un nouveau bloc
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-bloc').length) 
    {
    	$('.modal').delegate('#modal-ajout-bloc-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

        	$.post(base_url + 'evaluations/ajouter_bloc', $('#modal-ajout-bloc-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                if (data != true && data != false)
                {
                    $('#modal-ajout-bloc').find('.alert span.alertmsg').html(data).parents('.alert').removeClass('d-none');
                }
                else
                {
                    $('#modal-ajout-bloc').modal('hide'); 
                }
        	}, 'json');
    	});

    } // if length #modal-ajout-bloc

    /* ----------------------------------------------------------------
     *
     * Modifier un bloc
     *
     * ---------------------------------------------------------------- */

    if ($('.modifier-bloc').length) 
    {
        var bloc_labels = jQuery.parseJSON($('#bloc-labels').html());

        $('#modal-modifier-bloc').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); // button qui amorce le modal
            var modal = $(this);

            var $bloc = button.parents('.bloc-data');

            var bloc_id     = $bloc.data('bloc_id');
            var bloc_label  = $bloc.data('bloc_label');
            var bloc_points = $bloc.data('bloc_points');
            var bloc_desc   = $bloc.data('bloc_desc');
            var bloc_nb_questions = $bloc.data('bloc_nb_questions');

            modal.find('#modal-modifier-bloc-label').html(bloc_label);
            modal.find('input[name="bloc_id"]').val(bloc_id);
            modal.find('input[name="bloc_label"]').val(bloc_label);
            modal.find('input[name="bloc_points"]').val(bloc_points);
            modal.find('input[name="bloc_nb_questions"]').val(bloc_nb_questions);
            modal.find('input[name="bloc_desc"]').val(_.unescape(bloc_desc));

            $('#modal-bloc-label-modification option').each(function(index) {
           
                var label = $(this).val();

                if (jQuery.inArray(label, bloc_labels) != -1)
                {
                    $(this).addClass('d-none');

                    if (label == bloc_label)
                        $(this).removeClass('d-none');
                }
                else
                {
                    $(this).removeClass('d-none');
                }
            });

            $('#modal-bloc-label-modification').val(bloc_label);
        });

    	$('.modal').delegate('#modal-modifier-bloc-sauvegarde', 'click', function(e)
    	{
        	$.post(base_url + 'evaluations/modifier_bloc', $('#modal-modifier-bloc-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                if (data != true && data != false)
                {
                    $('#modal-modifier-bloc').find('.alert span.alertmsg').html(data).parents('.alert').removeClass('d-none');
                }
                else
                {
                    $('#modal-modifier-bloc').modal('hide'); 
                }

        	}, 'json');
    	});

    	$('.modal').delegate('#modal-modifier-bloc-effacer', 'click', function(e)
    	{
        	$.post(base_url + 'evaluations/effacer_bloc', $('#modal-modifier-bloc-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                $('#modal-modifier-bloc-effacer, #modal-modifier-bloc-sauvegarde').find('.spinner').addClass('d-none');
                $('#modal-modifier-bloc').modal('hide'); 

        	}, 'json');
        });
    } // if length

    /* ----------------------------------------------------------------
     *
     * Copier/Importer/Exporter un bloc (variables)
     *
     * ---------------------------------------------------------------- */

    if ($('.copier-bloc').length)
    {
        var $copier_bloc                    = $('#modal-copier-bloc');
        var $copier_bloc_sauvegarde         = $('#modal-copier-bloc-sauvegarde');
        var $copier_bloc_select_cours       = $('#modal-copier-bloc-cours-select');
        var $copier_bloc_select_evaluations = $('#modal-copier-bloc-evaluations-select');
        var $copier_bloc_erreur             = $('#modal-copier-bloc-erreur'); 

        var copier_bloc_evaluation_public   = $('#evaluation-data').data('evaluation_public');
        var evaluation_id                   = $('#evaluation-data').data('evaluation_id');
    }

    if ($('.importer-bloc').length)
    {
        var $impex_bloc                    = $('#modal-importer-bloc');
        var $impex_bloc_sauvegarde         = $('#modal-importer-bloc-sauvegarde');
        var $impex_bloc_select_cours       = $('#modal-importer-bloc-cours-select');
        var $impex_bloc_select_evaluations = $('#modal-importer-bloc-evaluations-select');
        var $impex_bloc_erreur             = $('#modal-importer-bloc-erreur'); 

        var impex_bloc_evaluation_public   = 0; // importation == 0
        var evaluation_id                  = $('#evaluation-data').data('evaluation_id');
    }

    if ($('.exporter-bloc').length)
    {
        var $impex_bloc                    = $('#modal-exporter-bloc');
        var $impex_bloc_sauvegarde         = $('#modal-exporter-bloc-sauvegarde');
        var $impex_bloc_select_cours       = $('#modal-exporter-bloc-cours-select');
        var $impex_bloc_select_evaluations = $('#modal-exporter-bloc-evaluations-select');
        var $impex_bloc_erreur             = $('#modal-exporter-bloc-erreur'); 

        var impex_bloc_evaluation_public   = 1; // exportation == 1
        var evaluation_id                  = $('#evaluation-data').data('evaluation_id');
    }

    /* ----------------------------------------------------------------
     *
     * Copier un bloc
     *
     * ---------------------------------------------------------------- */

    if ($('.copier-bloc').length)
    {
        $copier_bloc.on('show.bs.modal', function(e) 
        {
            var button  = $(e.relatedTarget); 
            var $bloc   = button.parents('.bloc-data');
			var bloc_id = $bloc.data('bloc_id');

            $copier_bloc.find('input[name="bloc_id"]').val(bloc_id);

            // Dissimuler les erreurs precedentes.

            $copier_bloc_erreur.addClass('d-none');

            //
            // Populer le select des cours.
            // 

        	$.post(base_url + 'evaluations/lister_cours_avec_evaluation', 
                    { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_public: copier_bloc_evaluation_public },
        	function(data)
        	{
				if (data != false)
				{
					var cours_id = $copier_bloc_select_cours.empty().html(data).val();

                    populer_select_evaluations($copier_bloc_select_evaluations, cours_id, evaluation_id, copier_bloc_evaluation_public);
				}

        	}, 'json');
        });

        //
        // Populer le select des evaluations suite a un changement du select des cours.
        // 

		$copier_bloc_select_cours.change(function()
		{
			var cours_id = $copier_bloc_select_cours.val();

            populer_select_evaluations($copier_bloc_select_evaluations, cours_id, evaluation_id, copier_bloc_evaluation_public);
		});

        //
        // Executer l'exportation du bloc.
        // 

		$copier_bloc_sauvegarde.click(function()
		{
			var bloc_id             = $copier_bloc.find('input[name="bloc_id"]').val();
			var evaluation_id_cible = $copier_bloc_select_evaluations.val();

        	$.post(base_url + 'evaluations/copier_bloc_vers_evaluation', 
                    { ci_csrf_token: cct, bloc_id: bloc_id, evaluation_id: evaluation_id, evaluation_id_cible: evaluation_id_cible, cours_id: cours_id },
        	function(data)
			{
                // Redirection vers l'evaluation cible (data = evaluation_id).

                if ($.isNumeric(data))
                {
				    window.location = base_url + 'evaluations/editeur/' + data;
                    return false;
                }

                // Erreur necessitant une redirection.

				if (typeof data === 'object' && 'status' in data && data['status'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $copier_bloc_erreur.find('.erreur-code').empty().html(data['code']);
                    $copier_bloc_erreur.find('.erreur-message').empty().html(data['message']);
                    $copier_bloc_erreur.find('.erreur-solution').empty().html(data['solution']);

                    $copier_bloc_erreur.removeClass('d-none');

				    $copier_bloc_sauvegarde.find('.spinner').addClass('d-none');

                    return false;
				}

        	}, 'json');
		});
	}

    /* ----------------------------------------------------------------
     *
     * Importer/Exporter un bloc
     *
     * ---------------------------------------------------------------- */

    if ($('.importer-bloc, .exporter-bloc').length)
    {
        $impex_bloc.on('show.bs.modal', function(e) 
        {
            var button  = $(e.relatedTarget); 
            var $bloc   = button.parents('.bloc-data');
			var bloc_id = $bloc.data('bloc_id');

            $impex_bloc.find('input[name="bloc_id"]').val(bloc_id);

            // Dissimuler les erreurs precedentes.

            $impex_bloc_erreur.addClass('d-none');

            //
            // Populer le select des cours.
            // 

        	$.post(base_url + 'evaluations/lister_cours_avec_evaluation', 
                    { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_public: impex_bloc_evaluation_public },
        	function(data)
        	{
				if (data != false)
				{
					var cours_id = $impex_bloc_select_cours.empty().html(data).val();

                    populer_select_evaluations($impex_bloc_select_evaluations, cours_id, evaluation_id, impex_bloc_evaluation_public);
				}

        	}, 'json');
        });

        //
        // Populer le select des evaluations suite a un changement du select des cours.
        // 

        var cours_id = null;

		$impex_bloc_select_cours.change(function()
		{
			cours_id = $impex_bloc_select_cours.val();
            populer_select_evaluations($impex_bloc_select_evaluations, cours_id, evaluation_id, impex_bloc_evaluation_public);
		});

        //
        // Executer l'exportation du bloc.
        // 

		$impex_bloc_sauvegarde.click(function()
		{
			var bloc_id             = $impex_bloc.find('input[name="bloc_id"]').val();
			var evaluation_id_cible = $impex_bloc_select_evaluations.val();

        	$.post(base_url + 'evaluations/copier_bloc_vers_evaluation', 
                    { ci_csrf_token: cct, bloc_id: bloc_id, evaluation_id: evaluation_id, evaluation_id_cible: evaluation_id_cible, cours_id: cours_id },
        	function(data)
			{
                // Redirection vers l'evaluation cible (data = evaluation_id).

                if ($.isNumeric(data))
                {
				    window.location = base_url + 'evaluations/editeur/' + data;
                    return false;
                }

                // Erreur necessitant une redirection.

				if (typeof data === 'object' && 'status' in data && data['status'] == 'redirect')
				{
					window.location = data['url'];
                    return false;
				}

                //
                // Erreur!
                //

				if (typeof data === 'object' && 'status' in data && data['status'] == 'error')
				{
                    $impex_bloc_erreur.find('.erreur-code').empty().html(data['code']);
                    $impex_bloc_erreur.find('.erreur-message').empty().html(data['message']);
                    $impex_bloc_erreur.find('.erreur-solution').empty().html(data['solution']);

                    $impex_bloc_erreur.removeClass('d-none');

				    $impex_bloc_sauvegarde.find('.spinner').addClass('d-none');

                    return false;
				}

        	}, 'json');
		});
	}

    /* ----------------------------------------------------------------
     *
     * Assigner un bloc
     *
     * ---------------------------------------------------------------- */

    if ($('.assigner-bloc').length) 
    {
        $('#modal-assigner-bloc').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); // button qui amorce le modal
            var modal = $(this);

            var bloc_id       = button.data('bloc_id');
            var question_id   = button.data('question_id'); 

            if (bloc_id != "")
            {
                modal.find('select[name="bloc_id"] option[value="' + bloc_id + '"]').attr('selected','selected');
                $('#modal-desassigner-bloc-sauvegarde').removeClass('d-none');
            }
            else
            {
                modal.find('select[name="bloc_id"] option:selected').removeAttr("selected");
                $('#modal-desassigner-bloc-sauvegarde').addClass('d-none');
            }

            modal.find('input[name="question_id"]').val(question_id);
        });

    	$('.modal').delegate('#modal-assigner-bloc-sauvegarde', 'click', function(e)
    	{
        	$.post(base_url + 'evaluations/assigner_bloc', $('#modal-assigner-bloc-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                if (data != true && data != false)
                {
                    $('#modal-assigner-bloc').find('.alert span.alertmsg').html(data).parents('.alert').removeClass('d-none');
                }

                $('#modal-assigner-bloc').modal('hide'); 

        	}, 'json');
    	});

    	$('.modal').delegate('#modal-desassigner-bloc-sauvegarde', 'click', function(e)
    	{
        	$.post(base_url + 'evaluations/desassigner_bloc', $('#modal-assigner-bloc-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                $('#modal-assigner-bloc').modal('hide'); 

        	}, 'json');
        });
    } // if length

	/* ================================================================
	 *
     * INSTRUCTIONS
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Modifier les instructions
     *
     * ---------------------------------------------------------------- */

	if ($('#instructions-evaluation').length)
	{
		var evaluation_instructions = null;

        //
        // affichage du modal
        //
        $('#modal-modifier-instructions').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			evaluation_instructions = $('#modal-modifier-instructions-input').val();
        });

        //
        // sauvegarde des instructions
        //
    	$('.modal').delegate('#modal-modifier-instructions-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

			if ($('#modal-modifier-instructions-input').val() != evaluation_instructions)
			{
				$.post(base_url + 'evaluations/modifier_instructions', $('#modal-modifier-instructions-form').serialize(),
				function(data)
				{
					document.location.reload();
					return true;
				}, 'json');
			}
			else
			{
				// si aucun changement detecte, simplement fermer le modal
				$('#modal-modifier-instructions').modal('hide')
				return true;
			}
    	});

        //
        // effacer les instructions
        //
    	$('.modal').delegate('#modal-modifier-instructions-effacer', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/effacer_instructions', $('#modal-modifier-instructions-form').serialize(),
        	function(data)
        	{
				document.location.reload();
				return true;
        	}, 'json');
    	});

	} // if #instructions-evaluation length

	/* ================================================================
	 *
     * QUESTIONS
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Ajouter une nouvelle question (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-question').length) 
    {
        //
        // ajouter la question
        //
    	$('.modal').delegate('#modal-ajout-question-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

			var $form = $('#modal-ajout-question-form');

        	$.post(base_url + 'evaluations/ajouter_question', $form.serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                	window.location = current_url;
					return true;
            	}
            	else if ($.isPlainObject(data)) 
				{
					// remove previous errors
					$('#modal-ajout-question-form :input').each(function(index) 
					{
						$(this).removeClass('is-invalid');
					});

                	// errors
                	for (var property in data) 
					{
						if (data.hasOwnProperty(property)) 
						{
							console.log('#modal-ajout-' + property.replace(/_/g, '-'));
							var $field = $('#modal-ajout-' + property.replace(/_/g, '-'));

							$field.addClass('is-invalid');
						}
					}
                }
        	}, 'json');
    	});

    } // if length #modal-ajout-question

    /* ----------------------------------------------------------------
     *
     * Modifier une question
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-editeur-question').length) 
    {
        $('#modal-editeur-question').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var question_id = button.data('question_id');
            var bloc_id = button.data('bloc_id');
            var modal = $(this);

            var $question_data  = $('#question-' + question_id + '-data');
            var question_texte  = $question_data.data('question_texte');
            var question_type   = $question_data.data('question_type');
            var question_points = $question_data.data('question_points');

            // var question_texte = $('<textarea />').html(question_texte).text();
            // var question_texte = $('<textarea />').html();

            modal.find('#modal-editeur-question-texte').val(_.unescape(question_texte));
            // modal.find('#modal-editeur-question-texte').val(question_texte);
			modal.find('input[name="question_id"]').val(question_id);
			modal.find('#modal-editeur-question-points').val(question_points);
			modal.find('#modal-editeur-question-type option[value="' + question_type + '"]').attr('selected', true);

            if (bloc_id != "")
                modal.find('input[name="question_points"]').prop('disabled', true);
            else
                modal.find('input[name="question_points"]').prop('disabled', false);
        });

        //
        // Sauvegarde des changements a la question
        //
    	$('.modal').delegate('#modal-editeur-question-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

        	$.post(base_url + 'evaluations/modifier_question', $('#modal-editeur-question-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}
            	else if ($.isPlainObject(data)) 
				{
					// remove previous errors
					$('#modal-editeur-question-form :input').each(function(index) 
					{
						$(this).removeClass('is-invalid');
					});

                	// errors
                	for (var property in data) 
					{
						if (data.hasOwnProperty(property)) 
						{
							console.log('#modal-editeur-' + property.replace(/_/g, '-'));
							var $field = $('#modal-editeur-' + property.replace(/_/g, '-'));

							$field.addClass('is-invalid');
						}
					}
                }
                else
                {
                    $('#modal-editeur-question').modal('hide');
                }

        	}, 'json');
    	});

        //
        // Change la reponse d'une question a choix unique/multiple de Vrai a Faux, ou de Faux a Vrai
        //
        $('.reponse-toggle').click(function(e)
        {
        	e.preventDefault();

            var question_id = $(this).data('question_id');
            var reponse_id = $(this).data('reponse_id');
            var reponse_correcte = $(this).data('reponse_correcte');

            $(this).addClass('fa-spin');

        	$.post(base_url + 'evaluations/reponse_toggle', 
                    { ci_csrf_token: cct, question_id: question_id, reponse_id: reponse_id, reponse_correcte: reponse_correcte },
        	function(data)
            {
                document.location.reload();
                return true;
            });

        });

    } // if length #modal-editeur-question

    /* ----------------------------------------------------------------
     *
     * Activer & desactiver une question
     *
     * ---------------------------------------------------------------- */

    if ($('.activer-question, .desactiver-question').length) 
    {
        $('.activer-question, .desactiver-question').click(function(e)
        {
            e.preventDefault();

            var question_id = $(this).data('question_id');

        	$.post(base_url + 'evaluations/activer_desactiver_question', { ci_csrf_token: cct, question_id: question_id },
        	function(data)
        	{
                document.location.reload();
                return;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
	 *
     * Modifier l'ordre de presentation des questions
     *
     * ---------------------------------------------------------------- */

    if ($('.ordre-question').length)
    {
        $('.ordre-question-sauvegarde').click(function(e)
        {
            e.preventDefault();;
    
            var $sel = $(this);

            var ordre = $(this).parents('.ordre-question').find('.ordre-question-input').val();
            var question_id = $(this).data('question_id');

            $sel.find('.ordre-question-sauvegarde-action').removeClass('d-none');

        	$.post(base_url + 'evaluations/changer_ordre_question', { ci_csrf_token: cct, ordre: ordre, question_id: question_id },
        	function()
        	{
                document.location.reload();
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier le titre d'une image (ou d'un document)
     *
     * ---------------------------------------------------------------- */

	if ($('.doc-caption').length)
	{
        //
        // affichage du modal
        //
        $('#modal-modifier-titre-document').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var doc_caption = button.parents('.question-document').find('.titre').html();
            var doc_id = button.data('doc_id');

            //
            // mettre a jour le modal avec les donnees de la question demandee
            // 

			modal.find('textarea').val(_.unescape(doc_caption));
            modal.find('input[name="doc_id"]').val(doc_id);
        });

        //
        // sauvegarde des modifications au titre
        //
    	$('.modal').delegate('#modal-modifier-titre-document-sauvegarde', 'click', function(e)
    	{
			var $form = $('#modal-modifier-titre-document-form');

            var doc_caption = $form.find('input[name="doc_caption"]').val();
            
			if ($('#modal-modifier-titre-document-document-titre').val() == doc_caption)
			{
				// si aucun changement detecte, simplement fermer le modal
				$('#modal-modifier-titre-document').modal('hide')
				return true;
			}

        	$.post(base_url + 'documents/modifier_caption', $form.serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}
        	}, 'json');
    	});

	} // if #titre-document length

    /* ----------------------------------------------------------------
     *
     * Dupliquer une question (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-dupliquer-question').length) 
    {
        $('#modal-dupliquer-question').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var question_id = button.data('question_id');
            var modal = $(this);

			modal.find('input[name="question_id"]').val(question_id);
        });

        //
        // Executer la duplication.
        //

    	$('.modal').delegate('#modal-dupliquer-question-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();
		
			$.post(base_url + 'evaluations/dupliquer_question', $('#modal-dupliquer-question-form').serialize(),
			function(data)
			{
				if (data == true)
				{
                    document.location.reload();
				}
				else
				{
					$('#modal-dupliquer-question').modal('hide');
				}

				return true;

			}, 'json');
		});
	} // if length modal-dupliquer-question

    /* ----------------------------------------------------------------
     *
     * Effacer une question (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-effacer-question').length) 
    {
        $('#modal-effacer-question').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var question_id = button.data('question_id');
            var modal = $(this);

			modal.find('input[name="question_id"]').val(question_id);
        });

		//
		// effacement
		//
    	$('.modal').delegate('#modal-effacer-question-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();
		
			$.post(base_url + 'evaluations/effacer_question', $('#modal-effacer-question-form').serialize(),
			function(data)
			{
				if (data == true)
				{
                    document.location.reload();
				}
				else
				{
					$('#modal-effacer-question').modal('hide');
				}

				return true;

			}, 'json');
		});
	} // if length modal-effacer-question

    /* ----------------------------------------------------------------
     *
     * Effacer une image (ou un document) (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-effacer-document').length) 
    {
        $('#modal-effacer-document').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var doc_id = button.data('doc_id');
            var modal = $(this);

			modal.find('input[name="doc_id"]').val(doc_id);
        });

		//
		// effacement
		//
    	$('.modal').delegate('#modal-effacer-document-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();
		
			$.post(base_url + 'evaluations/effacer_document', $('#modal-effacer-document-form').serialize(),
			function(data)
			{
				if (data == true)
				{
                    document.location.reload();
                    return;
				}
				else
				{
					$('#modal-effacer-document').modal('hide');
				}

				return true;

			}, 'json');
		});
    }

	/* ================================================================
	 *
     * REPONSES
     *
     * ================================================================ */

    /* ----------------------------------------------------------------
     *
     * Selectionner plusieurs reponses multiples pour effacement rapide.
     *
     * ---------------------------------------------------------------- */

    if ($('.choix-multiples-selection').length) 
    {
        $('.choix-multiples-selection').click(function (e)
        {
            var $section     = $(this).parents('.editeur-section-sous-section');
            var $rep         = $(this).parents('.reponses');
            var $effacer_btn = $section.find('.effacer-reponses-non-selection');
            var $effacer_btn_compte = $section.find('.effacer-reponses-non-selection-compte');

            if ($(this).prop('checked'))
            {
                $(this).parents('.reponses-table-wrap').addClass('selection-active');

            }
            else
            {
                $(this).parents('.reponses-table-wrap').removeClass('selection-active');
            }

            $effacer_btn.addClass('d-none');
            $effacer_btn_compte.addClass('d-none');
            
            var checked_count = 0;
            var rep_count = $rep.data('compte');

            $rep.find('.choix-multiples-selection').each(function(k, v)
            {
                if (v.checked)
                {
                    checked_count++;

                    if (checked_count > 1)
                    {
                        if ($effacer_btn.hasClass('d-none'))
                        {
                            $effacer_btn.removeClass('d-none');
                            $effacer_btn_compte.removeClass('d-none');
                        }
                    }
                }
            });

            $effacer_btn_compte.find('.compte').html(checked_count);
            $effacer_btn_compte.find('.rep-compte').html(rep_count - checked_count);
        });
    }

    $('.effacer-reponses-non-selection').click(function(e)
    {
        e.preventDefault();

        var $section = $(this).parents('.editeur-section-sous-section');
        var $rep     = $section.find('.reponses');

        var question_id   = $(this).data('question_id'); 
        var evaluation_id = $('#evaluation-data').data('evaluation_id');
        const reponse_ids = [];

        //
        // Determiner quelles sont les reponses selectionnees
        //

        $rep.find('.choix-multiples-selection').each(function(k, v)
        {
            if (v.checked)
            {
                reponse_ids.push($(this).val());
            }
        });

        if (reponse_ids.length > 0)
        {
        	$.post(base_url + 'evaluations/effacer_reponses_non_selectionnees', { ci_csrf_token: cct, evaluation_id: evaluation_id, question_id: question_id, reponse_ids: reponse_ids },
        	function(data)
        	{
                if (data == true)
                {
                    document.location.reload();
					return true;
                }

                return false;

            }, 'json');
        }
    });

    /* ----------------------------------------------------------------
     *
     * Ajouter une reponse (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-reponse').length) 
    {
        $('#modal-ajout-reponse').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var question_id = button.data('question_id');
            var question_type = button.data('question_type');
            var reponse_type_defaut = button.data('reponse_type_defaut');

            if (typeof reponse_type_defaut == 'undefined')
            {
                reponse_type_defaut = 2;
            }
            console.log('ici');

			modal.find('input[name="question_id"]').val(question_id);
			modal.find('input[name="question_type"]').val(question_type);
            modal.find('select[name="reponse_type"]').val(reponse_type_defaut);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier une reponse (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-modifier-reponse').length) 
    {
        $('#modal-modifier-reponse').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var question_id = button.data('question_id');
            var reponse = button.data('reponse');
            var reponse_id = button.data('reponse_id');
            var reponse_correcte = button.data('reponse_correcte');

			reponse = _.unescape(reponse);

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="reponse_id"]').val(reponse_id);
            modal.find('textarea[name="reponse_texte"]').val(_.unescape(reponse));

            modal.find('select[name="reponse_correcte"] option').attr('selected', false);
			modal.find('select[name="reponse_correcte"] option[value="' + reponse_correcte + '"]').attr('selected', true);
        });
    }

    /* ----------------------------------------------------------------
     *
     * EDITEUR :
	 *
     * La question est un sondage
     *
     * ---------------------------------------------------------------- */
    if ($('.question-sondage').length)
    {
        $('.question-sondage').change(function() {

            var question_id = $(this).data('question_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_question_sondage', { ci_csrf_token: cct, checked: checked, question_id: question_id },
        	function()
        	{
                document.location.reload();
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * EDITEUR :
	 *
     * Permettre la presentation de l'ordre des reponses aleatoirement
     *
     * ---------------------------------------------------------------- */
    if ($('.reponses-aleatoires').length)
    {
        $('.reponses-aleatoires').change(function() {

            var question_id = $(this).data('question_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_reponses_aleatoires', { ci_csrf_token: cct, checked: checked, question_id: question_id },
        	function()
        	{
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Activer le selecteur
     *
     * ---------------------------------------------------------------- */
    if ($('.selecteur').length)
    {
        $('.selecteur').change(function() {

            var question_id = $(this).data('question_id');
            var checked = false;

            if ($(this).is(':checked'))
            {
                checked = true;
            }

        	$.post(base_url + 'evaluations/changer_selecteur', { ci_csrf_token: cct, checked: checked, question_id: question_id },
        	function()
        	{
                return true;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter une reponse numerique entiere (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-reponse-numerique-entiere').length) 
    {
        $('#modal-ajout-reponse-numerique-entiere').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var question_id = button.data('question_id');

			modal.find('input[name="question_id"]').val(question_id);

            // var question_type = button.data('question_type'); // valeur == 5
			// modal.find('input[name="question_type"]').val(question_type);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier une reponse numerique entiere (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-modifier-reponse-numerique-entiere').length) 
    {
        $('#modal-modifier-reponse-numerique-entiere').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal  = $(this);

            var question_id = button.data('question_id');
            var reponse     = button.data('reponse');
            var reponse_id  = button.data('reponse_id');
        
            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="reponse_id"]').val(reponse_id);
            modal.find('input[name="reponse_texte"]').val(reponse['reponse_texte']);
            modal.find('input[name="unites"]').val(reponse['unites']);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter une reponse numerique (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-reponse-numerique').length) 
    {
        $('#modal-ajout-reponse-numerique').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var question_id = button.data('question_id');

			modal.find('input[name="question_id"]').val(question_id);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier une reponse numerique (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-modifier-reponse-numerique').length) 
    {
        $('#modal-modifier-reponse-numerique').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal  = $(this);

            var question_id = button.data('question_id');
            var reponse     = button.data('reponse');
            var reponse_id  = button.data('reponse_id');
        
            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="reponse_id"]').val(reponse_id);
            modal.find('input[name="reponse_texte"]').val(reponse['reponse_texte']);
            modal.find('input[name="unites"]').val(reponse['unites']);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter une tolerance (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-tolerance').length) 
    {
        $('#modal-ajout-tolerance').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal  = $(this);

            var question_id = button.data('question_id');
        
            modal.find('input[name="question_id"]').val(question_id);

            // Remettre la tolerance absolue par default si elle avait ete changee
            $('#modal-tolerance-type').val('1');
            $('#modal-tolerance-absolue').removeClass('d-none');
            $('#modal-tolerance-relative').addClass('d-none');
        });

        $('#modal-tolerance-type').change(function(e)
        {
            if ($('#modal-tolerance-type').val() == 1)
            {
                $('#modal-tolerance-absolue').removeClass('d-none');
                $('#modal-tolerance-relative').addClass('d-none');
            }
            else
            {
                $('#modal-tolerance-absolue').addClass('d-none');
                $('#modal-tolerance-relative').removeClass('d-none');
            }
        });

    	$('.modal').delegate('#modal-ajout-tolerance-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

        	$.post(base_url + 'evaluations/ajouter_tolerance', $('#modal-ajout-tolerance-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                $('#modal-ajout-tolerance').modal('hide'); 

        	}, 'json');
    	});

    } // if length #modal-ajout-tolerance

    /* ----------------------------------------------------------------
     *
     * Effacer une tolerance
     *
     * ---------------------------------------------------------------- */

    if ($('.effacer-tolerance').length) 
    {
        $('.effacer-tolerance').click(function(e)
        {
            e.preventDefault();

            var $sel = $(this);

            var question_id  = $sel.data('question_id');
            var tolerance_id = $sel.data('tolerance_id');

        	$.post(base_url + 'evaluations/effacer_tolerance', { ci_csrf_token: cct, question_id: question_id, tolerance_id: tolerance_id },
        	function(data)
        	{
                if (data == true) 
                {
                    document.location.reload();
                    return true;
                }

            }, 'json');

        });
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter une reponse litterale courte (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-reponse-litterale-courte').length) 
    {
        $('#modal-ajout-reponse-litterale-courte').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var question_id = button.data('question_id');

			modal.find('input[name="question_id"]').val(question_id);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier une reponse litterale courte (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-modifier-reponse-litterale-courte').length) 
    {
        $('#modal-modifier-reponse-litterale-courte').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal  = $(this);

            var question_id = button.data('question_id');
            var reponse     = button.data('reponse');
            var reponse_id  = button.data('reponse_id');
        
            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="reponse_id"]').val(reponse_id);
            modal.find('input[name="reponse_texte"]').val(reponse['reponse_texte']);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier les parametres d'une reponse litterale courte (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-modifier-reponse-litterale-courte-parametres').length) 
    {
        $('#modal-modifier-reponse-litterale-courte-parametres').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var question_id = button.data('question_id');
            var similarite  = button.data('similarite');

            modal.find('input[name="question_id"]').val(question_id);
			modal.find('input[name="reponse_similarite"]').val(similarite);
        });

    	$('.modal').delegate('#modal-modifier-reponse-litterale-courte-parametres-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

        	$.post(base_url + 'evaluations/modifier_similarite', $('#modal-modifier-reponse-litterale-courte-parametres-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                $('#modal-modifier-reponse-litterale-courte').modal('hide'); 

        	}, 'json');
    	});
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter une equation (TYPE 3) (modal) 
     * Ajouter une equation correcte (TYPE 9) (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-equation, #modal-ajout-equation-correcte').length) 
    {
        $('#modal-ajout-equation, #modal-ajout-equation-correcte').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var question_id = button.data('question_id');
            var question_type = button.data('question_type');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="question_type"]').val(question_type);
        });
    }

    /* ----------------------------------------------------------------
     *
     * Modifier une equation (TYPE 3) (modal)
     * Modifier une equation correcte (TYPE 9) (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-modifier-equation, #modal-modifier-equation-correcte').length) 
    {
        $('#modal-modifier-equation, #modal-modifier-equation-correcte').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var reponse = button.data('reponse');
            var reponse_id = button.data('reponse_id');
            var question_id = button.data('question_id');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="reponse_id"]').val(reponse_id);
            modal.find('input[name="reponse_texte"]').val(reponse['reponse_texte']);
            modal.find('input[name="unites"]').val(reponse['unites']);
			modal.find('input[name="cs"]').val(reponse['cs']);

            // Type 3
            if (button.hasClass('question-equation-sauvegarde'))
            {
                if (reponse['notsci'] == 1)
                    modal.find('input[name="notsci"]').prop('checked', true);

                modal.find('select[name="reponse_correcte"] option').attr('selected', false);
                modal.find('select[name="reponse_correcte"] option[value="' + reponse['reponse_correcte'] + '"]').attr('selected', true);
            }

            // Type 9
            if (button.hasClass('question-equation-correcte-sauvegarde'))
            {
                // rien de special pour l'instant
            }

        });
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter un bareme sous forme de prompt (TYPE 13) (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajout-bareme-prompt').length) 
    {
        $('#modal-ajout-bareme-prompt').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var question_id = button.data('question_id');
            var question_type = button.data('question_type');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="question_type"]').val(question_type);
            
            $(this).find('.spinner').addClass('d-none');
        });

        $('#modal-ajout-bareme-prompt-sauvegarde').click(function()
        {
            if ($('#modal-ajout-bareme-prompt textarea').val() == '')
            {   
                $('#modal-ajout-bareme-prompt').modal('hide');
                return;
            }

            $.post(base_url + 'evaluations/ajouter_bareme_prompt', $('#modal-ajout-bareme-prompt-form').serialize(),
            function(data)
            {
                if (data == true) 
                {
                    document.location.reload();
                    return true;
                }

            }, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * Ajouter une reponse ou une equation
     *
     * ---------------------------------------------------------------- */

    $('.modal').delegate(
            '#modal-ajout-reponse-sauvegarde'
             + ', #modal-ajout-reponse-numerique-entiere-sauvegarde'
             + ', #modal-ajout-reponse-numerique-sauvegarde' 
             + ', #modal-ajout-reponse-litterale-courte-sauvegarde'
             + ', #modal-ajout-equation-sauvegarde'
             + ', #modal-ajout-equation-correcte-sauvegarde',
        'click', function(e)
    {
        e.preventDefault();

        var reponse_modal = 'reponse';

        if ($(this).parents('#modal-ajout-equation').length)
        {
            reponse_modal = 'equation';
        }

        if ($(this).parents('#modal-ajout-equation-correcte').length)
        {
            reponse_modal = 'equation-correcte';
        }
        
        if ($(this).parents('#modal-ajout-reponse-numerique-entiere').length)
        {
            reponse_modal = 'reponse-numerique-entiere';
        }

        if ($(this).parents('#modal-ajout-reponse-numerique').length)
        {
            reponse_modal = 'reponse-numerique';
        }

        if ($(this).parents('#modal-ajout-reponse-litterale-courte').length)
        {
            reponse_modal = 'reponse-litterale-courte';
        }

        $.post(base_url + 'evaluations/ajouter_reponse', $('#modal-ajout-' + reponse_modal + '-form').serialize(),
        function(data)
        {
            if (data == true) 
            {
                document.location.reload();
                return true;
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Modifier une reponse ou une equation
     *
     * ---------------------------------------------------------------- */

    $('.modal').delegate(
            '#modal-modifier-reponse-sauvegarde'
            + ', #modal-modifier-reponse-numerique-entiere-sauvegarde'
            + ', #modal-modifier-reponse-numerique-sauvegarde'
            + ', #modal-modifier-reponse-litterale-courte-sauvegarde'
            + ', #modal-modifier-equation-sauvegarde'
            + ', #modal-modifier-equation-correcte-sauvegarde',
        'click', function(e)
    {
        e.preventDefault();

        var reponse_modal = 'reponse';

        if ($(this).parents('#modal-modifier-equation').length)
        {
            reponse_modal = 'equation';
        }

        if ($(this).parents('#modal-modifier-equation-correcte').length)
        {
            reponse_modal = 'equation-correcte';
        }

        if ($(this).parents('#modal-modifier-reponse-numerique-entiere').length)
        {
            reponse_modal = 'reponse-numerique-entiere';
        }

        if ($(this).parents('#modal-modifier-reponse-numerique').length)
        {
            reponse_modal = 'reponse-numerique';
        }

        if ($(this).parents('#modal-modifier-reponse-litterale-courte').length)
        {
            reponse_modal = 'reponse-litterale-courte';
        }

        $.post(base_url + 'evaluations/modifier_reponse', $('#modal-modifier-' + reponse_modal + '-form').serialize(),
        function(data)
        {
            if (data == true) 
            {
                document.location.reload();
                return true;
            }
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Changer le type d'une reponse
     *
     * ---------------------------------------------------------------- */

    if ($('.reponses').length) 
    {
		$('.reponses .reponse-type').click(function(e)
		{
			e.preventDefault();

			var $selection = $(this);
			var $reponses  = $(this).parents('.reponses');
			var $reponse   = $(this).parents('.reponse');

			var question_id  = $reponses.data('question_id');
			var reponse_id   = $reponse.data('reponse_id');
			var reponse_type = $(this).data('reponse_type');	

        	$.post(base_url + 'evaluations/changer_reponse_type', { ci_csrf_token: cct, reponse_id: reponse_id, reponse_type: reponse_type },
        	function(data)
        	{
				if (data == true)
				{
					// changer la selection courante
					$('#reponse-' + reponse_id + '-type span').each(function(index) 
					{
						if ( ! $(this).hasClass('reponse-' + reponse_type))
							$(this).addClass('d-none');
						else
							$(this).removeClass('d-none');
					});

					// changer les choix disponibles pour tenir compte de celui selectionne
					$('#reponse-' + reponse_id + '-type-dropdown div.reponse-type').each(function(index)
					{
						if ( ! $(this).hasClass('reponse-' + reponse_type))
							$(this).removeClass('d-none');
						else
							$(this).addClass('d-none');
					});
				}

			}, 'json');

		});
	} // if .reponses length

    /* ----------------------------------------------------------------
     *
     * Effacer une reponse ou une equation
     *
     * ---------------------------------------------------------------- */

    $('.modal').delegate('#modal-modifier-reponse-effacer, #modal-modifier-equation-effacer', 'click', function(e)
    {
        e.preventDefault();

        var modal = $(this).parents('.modal');

        var question_id = modal.find('input[name="question_id"]').val();
        var reponse_id = modal.find('input[name="reponse_id"]').val();

        $.post(base_url + 'evaluations/effacer_reponse', { ci_csrf_token: cct, question_id: question_id, reponse_id: reponse_id },
        function(data)
        {
            document.location.reload();
            return true;

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Effacer une reponse
     *
     * ---------------------------------------------------------------- */

    if ($('.reponses').length) 
    {
		$('.reponses .reponse-effacer').click(function(e)
		{
			e.preventDefault();

			var $selection = $(this);
			var $reponse   = $(this).parents('.reponse');
			var reponse_id = $reponse.data('reponse_id');

        	$.post(base_url + 'evaluations/effacer_reponse', { ci_csrf_token: cct, reponse_id: reponse_id },
        	function(data)
			{
				$reponse.addClass('d-none');

        	}, 'json');

		});
	} // if .reponses length

    /* ----------------------------------------------------------------
     *
     * Effacer une reponse acceptee 
     * d'une question a reponse litterale courte
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-effacer-reponse-litterale-courte-sauvegarde').length)
    {
        $('#modal-effacer-reponse-litterale-courte-sauvegarde').click(function(e)
        {
            var reponse_id = $(this).parents('#modal-modifier-reponse-litterale-courte').find('input[name="reponse_id"]').val();

        	$.post(base_url + 'evaluations/effacer_reponse', { ci_csrf_token: cct, reponse_id: reponse_id },
        	function(data)
			{
                document.location.reload();
                return true;
        	}, 'json');
        }); 
    }

    /* ----------------------------------------------------------------
     *
     * Grille de correction
     *
     * Creer une grille de correction
     *
     * ---------------------------------------------------------------- */

    if ($('.creer-grille-correction').length)
    {
        $('#modal-ajout-grille-correction').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			var question_id = button.data('question_id');

            modal.find('input[name="question_id"]').val(question_id);
        });

    	$('.modal').delegate('#modal-ajout-grille-correction-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/ajouter_grille_correction', $('#modal-ajout-grille-correction-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

        	}, 'json');
    	});
    }

    /* ----------------------------------------------------------------
     *
     * Grille de correction
     *
     * Importer une grille de correction
     *
     * ---------------------------------------------------------------- */

    if ($('.importer-grille-correction').length)
    {
        $('#modal-importer-grille-correction').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			var question_id = button.data('question_id');

            modal.find('input[name="question_id"]').val(question_id);
        });

    	$('.modal').delegate('#modal-importer-grille-correction-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/importer_grille_correction', $('#modal-importer-grille-correction-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

                $('#modal-importer-grille-correction-erreur').removeClass('d-none');

        	}, 'json');
    	});
    }

    if ($('.grille-correction').length)
    {
        $('#modal-modifier-grille-correction').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			var question_id      = button.data('question_id');
			var grille_id        = button.data('grille_id');
            var grille_type      = button.data('grille_type');
            var grille_affichage = button.data('grille_affichage');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="grille_id"]').val(grille_id);
            modal.find('select[name="grille_type"]').val(grille_type);
            modal.find('select[name="grille_affichage"]').val(grille_affichage);
        });

        //
        // Modifier une grille de correction
        //

    	$('.modal').delegate('#modal-modifier-grille-correction-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/modifier_grille_correction', $('#modal-modifier-grille-correction-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
					return true;
            	}

        	}, 'json');
    	});

        //
        // Effacer une grille de correction
        //

    	$('.modal').delegate('#modal-effacer-grille-correction-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/effacer_grille_correction', $('#modal-modifier-grille-correction-form').serialize(),
        	function(data)
        	{
                document.location.reload();
                return true;

        	}, 'json');
    	});

        //
        // Ajouter un element
        //

        $('#modal-ajout-element').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			var question_id      = button.data('question_id');
			var grille_id        = button.data('grille_id');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="grille_id"]').val(grille_id);
        });

    	$('.modal').delegate('#modal-ajout-element-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();
    
            var modal = '#' + $(this).parents('.modal').attr('id');

        	$.post(base_url + 'evaluations/ajouter_element_grille', $('#modal-ajout-element-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
                    return true;
                }

            	else if ($.isPlainObject(data)) 
				{
                    $(modal).find('.spinner').addClass('d-none');

					// remove previous errors
					$(modal + '-form :input').each(function(index) 
					{
						$(this).removeClass('is-invalid');
					});

                	// errors
                	for (var property in data) 
					{
						if (data.hasOwnProperty(property)) 
						{
							// console.log('#modal-ajout-' + property.replace(/_/g, '-'));
							var $field = $('#modal-ajout-' + property.replace(/_/g, '-'));

							$field.addClass('is-invalid');
						}
					}
                }

        	}, 'json');
    	});
        
        //
        // Modifier ou effacer un element
        //

        $('#modal-modifier-element').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

			var question_id      = button.data('question_id');
			var grille_id        = button.data('grille_id');
            var element_id       = button.data('element_id');
            var element_desc     = button.data('element_desc');
            var element_type     = button.data('element_type');
            var element_ordre    = button.data('element_ordre');
            var element_pourcent = button.data('element_pourcent');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="grille_id"]').val(grille_id);
            modal.find('input[name="element_id"]').val(element_id);

            modal.find('input[name="element_desc"]').val(element_desc);
            modal.find('select[name="element_type"]').val(element_type);
            modal.find('input[name="element_ordre"]').val(element_ordre);
            modal.find('input[name="element_pourcent"]').val(element_pourcent);
        });

        //
        // Dupliquer un element
        //

    	$('.dupliquer-element').click(function(e)
    	{
            e.preventDefault();

			var question_id      = $(this).data('question_id');
			var grille_id        = $(this).data('grille_id');
            var element_id       = $(this).data('element_id');

            $(this).find('i').toggleClass('d-none');

        	$.post(base_url + 'evaluations/dupliquer_element_grille',
                { ci_csrf_token: cct, question_id: question_id, grille_id: grille_id, element_id: element_id },
        	function(data)
        	{
                document.location.reload();
                return true;

        	}, 'json');
    	});

        //
        // Modifier un element
        //

    	$('.modal').delegate('#modal-modifier-element-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

            var modal = '#' + $(this).parents('.modal').attr('id');

        	$.post(base_url + 'evaluations/modifier_element_grille', $('#modal-modifier-element-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
                    return true;
                }

            	else if ($.isPlainObject(data)) 
				{
                    $(modal).find('.spinner').addClass('d-none');

					// remove previous errors
					$(modal + '-form :input').each(function(index) 
					{
						$(this).removeClass('is-invalid');
					});

                	// errors
                	for (var property in data) 
					{
						if (data.hasOwnProperty(property)) 
						{
							// console.log('#modal-modifier-' + property.replace(/_/g, '-'));
							var $field = $('#modal-modifier-' + property.replace(/_/g, '-'));

							$field.addClass('is-invalid');
						}
					}
                }

        	}, 'json');
    	});

        //
        // Effacer un element
        //

    	$('.modal').delegate('#modal-effacer-element-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'evaluations/effacer_element_grille', $('#modal-modifier-element-form').serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload();
                    return true;
                }

        	}, 'json');
    	});


    } /* .grille-correction */

	// ----------------------------------------------------------------
	//
	// Laboratoire
	//
	// ----------------------------------------------------------------

	// ----------------------------------------------------------------
	//
	// Tableau : parametres
	//
	// ----------------------------------------------------------------

    $('#individuel-activer').change(function()
    {
        var evaluation_id = $('#evaluation-data').data('evaluation_id');

        var individuel = 0;

        if ($(this).is(':checked'))
        {
            individuel = 1;
        }
        else
        {
            individuel = 0;
        }

		$.post(base_url + 'evaluations/changer_lab_individuel',
            {
				ci_csrf_token: cct,
				evaluation_id: evaluation_id,
                individuel:    individuel
            },
		function(data)
		{
            return false; 
        });
    });

    $('#tableaux-parametres-precorrections input').change(function()
    {
        var evaluation_id = $('#evaluation-data').data('evaluation_id');

        var precorrection_parametre     = $(this).attr('name');
        var precorrection_parametre_val = $(this).val();

        if (precorrection_parametre == 'precorrection')
        {
            if ($(this).is(':checked'))
            {
                precorrection_parametre_val = 1;
            }
            else
            {
                precorrection_parametre_val = 0;
            }
        }

		$.post(base_url + 'evaluations/changer_precorrection_parametres',
            {
				ci_csrf_token: cct,
				evaluation_id: evaluation_id,
                param:         precorrection_parametre,
                val:           precorrection_parametre_val
            },
		function(data)
		{
            return false; 

        });
    });

	// ----------------------------------------------------------------
	//
	// Tableau : ajouter un champ
	//
	// ----------------------------------------------------------------

	$('#modal-tableau-ajouter-champ-sauvegarde').click(function()
	{
        var lab_prefix = $('#tableau-ajouter-champ-data').data('lab_prefix');
        var champ_tmp  = $('#tableau-ajouter-champ-data').data('champ_tmp');
        var evaluation_id = $('#evaluation-data').data('evaluation_id');

        // ceci ne fonctionne pas (pour une raison que j'ignore)
        // console.log($('#modal-tableau-modifier-champ-form').serialize());

		$.post(base_url + 'evaluations/modal_tableau_ajouter_champ_sauvegarde',
            {
				ci_csrf_token: cct,
				evaluation_id: evaluation_id,
                nom_champ:     $('#' + lab_prefix + '-' + champ_tmp + '-champ').val(),
                champ_tmp:     champ_tmp,
                valeur:   	   $('#' + lab_prefix + '-' + champ_tmp + '-valeur').val(),
                nsci:     	   $('#' + lab_prefix + '-' + champ_tmp + '-nsci').val(),
               	unites:   	   $('#' + lab_prefix + '-' + champ_tmp + '-unites').val()
            },
		function(data)
		{
			if (data == true) 
			{
				// Sauvegarder la position de défilement avant le rechargement
				window.onbeforeunload = function() {
					sessionStorage.setItem('scrollPosition', window.scrollY);
				};

				document.location.reload();
				return true;
			}

		}, 'json');
	});

	// ----------------------------------------------------------------
	//
	// Tableau : modifier un champ
	//
	// ----------------------------------------------------------------

	$('.modal-tableau-modifier-champ').click(function()
	{
		var champ = $(this).data('champ');

		$.ajax({
			url: base_url + 'evaluations/modal_tableau_modifier_champ',
			method: 'POST',
			data: {
				ci_csrf_token: cct,
				evaluation_id: evaluation_id,
				champ: champ
			},
          	dataType: 'json',
			success: function(response) 
			{
				$('#modal-tableau-modifier-champ .modal-body').html(response.html);
				$('#modal-tableau-modifier-champ').modal('show');
			}
		});
	});

	$('#modal-tableau-modifier-champ-sauvegarde').click(function()
	{
        var lab_prefix = $('#tableau-modifier-champ-data').data('lab_prefix');
        var evaluation_id = $('#evaluation-data').data('evaluation_id');
        var champ = $('#tableau-modifier-champ-data').data('champ');
        
        var champ_tableau = champ + '-tableau';

        // ceci ne fonctionne pas (pour une raison que j'ignore)
        // console.log($('#modal-tableau-modifier-champ-form').serialize());

		$.post(base_url + 'evaluations/modal_tableau_modifier_champ_sauvegarde',
            {
				ci_csrf_token: 	 cct,
				evaluation_id:	 evaluation_id,
                nom_champ:    	 champ,
				est_incertitude: $('#tableau-modifier-champ-data').data('est_incertitude'),
                // 'tableau':  	 $('#' + lab_prefix + '-' + champ + '-tableau').val(),
                // 'desc':     	 $('#' + lab_prefix + '-' + champ + '-desc').val(),
                valeur:   	 	 $('#' + lab_prefix + '-' + champ + '-valeur').val(),
                nsci:     	 	 $('#' + lab_prefix + '-' + champ + '-nsci').val(),
                unites:   	 	 $('#' + lab_prefix + '-' + champ + '-unites').val(),
                points:   	 	 $('#' + lab_prefix + '-' + champ + '-points').val()
            },
		function(data)
		{
			if (data == true) 
			{
				// Sauvegarder la position de défilement avant le rechargement
				window.onbeforeunload = function() {
					sessionStorage.setItem('scrollPosition', window.scrollY);
				};

				document.location.reload();
				return true;
			}

		}, 'json');
	});

	$('#modal-tableau-effacer-champ').click(function()
	{
        var champ = $('#tableau-modifier-champ-data').data('champ');
        var champ_d = $('#tableau-modifier-champ-data').data('champ_d');
        
		$.post(base_url + 'evaluations/modal_tableau_effacer_champ',
            {
				ci_csrf_token: cct,
				evaluation_id: evaluation_id,
                champ:         champ,
                champ_d:       champ_d
            },
		function(data)
		{
			if (data == true) 
			{
				document.location.reload();
				return true;
			}

		}, 'json');
	});

	// ----------------------------------------------------------------
	//
	// Tableau : ajouter des points a un champ
	//
	// ----------------------------------------------------------------

	$('#modal-tableau-ajouter-points-sauvegarde').click(function()
	{
        var evaluation_id = $('#evaluation-data').data('evaluation_id');

		$.post(base_url + 'evaluations/modal_tableau_ajouter_points_sauvegarde', 
            { 
				ci_csrf_token:   cct,
				evaluation_id:   evaluation_id,
                nom_champ:       $('#tableau-ajouter-points-nom-champ').val(),
                type:       	 $('#tableau-ajouter-points-type-champ').val(),
                cs:         	 $('#tableau-ajouter-points-cs-champ').val(),	// cs = chiffres significatifs
                cspp:       	 $('#tableau-ajouter-points-cspp-champ').val(), 	// cspp = cs penalite pourcentage
                points:     	 $('#tableau-ajouter-points-points-champ').val(),
			    tolerance:       $('#tableau-ajouter-points-tolerance-champ').val(),
				est_incertitude: $('#tableau-ajouter-points-est-incertitude').find('[name="est_incertitude"]:checked').val(),
				incertitude:	 $('#tableau-ajouter-points-champ-incertitude').val(),
                tableau:    	 $('#tableau-ajouter-points-tableau-champ').val(),
                desc:       	 $('#tableau-ajouter-points-desc-champ').val()
            },
		function(data)
		{
			if (data == true) 
			{
				// Sauvegarder la position de défilement avant le rechargement
				window.onbeforeunload = function() {
					sessionStorage.setItem('scrollPosition', window.scrollY);
				};

				document.location.reload();
				return true;
			}

		}, 'json');
	});


	// ----------------------------------------------------------------
	//
	// Tableau : modifier des points a un champ
	//
	// ----------------------------------------------------------------

    if ($('div#modal-tableau-modifier-points').length)
    {
        //
        // affichage du modal
        //
        $('#modal-tableau-modifier-points').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            //
            // mettre a jour le modal avec les donnees de la question demandee
            // 

			$('#tableau-modifier-points-nom-champ').val(_.unescape(button.data('champ_nom')));
			$('#tableau-modifier-points-type-champ').val(_.unescape(button.data('champ_type')));
			$('#tableau-modifier-points-points-champ').val(_.unescape(button.data('champ_points')));
			$('#tableau-modifier-points-tolerance-champ').val(_.unescape(button.data('champ_tolerance')));
			$('#tableau-modifier-points-cs-champ').val(_.unescape(button.data('champ_cs')));
			$('#tableau-modifier-points-cspp-champ').val(_.unescape(button.data('champ_cspp')));
			$('#tableau-modifier-points-tableau-champ').val(_.unescape(button.data('tableau_no')));
			$('#tableau-modifier-points-desc-champ').val(_.unescape(button.data('champ_desc')));
			$('#tableau-modifier-points-eq-champ').val(_.unescape(button.data('champ_eq')));
			$('#tableau-modifier-points-eq-na-champ').val(_.unescape(button.data('champ_eq_na')));

            $('#modal-tableau-modifier-points-form').find('[name="nom_champ_origine"]').val(_.unescape(button.data('champ_nom')));
            $('#modal-tableau-modifier-points-form').find('[name="incertitude"]').val(button.data('champ_incertitude'));

            $('#tableau-modifier-points-est-incertitude-' + button.data('champ_est_incertitude')).prop('checked', 'true').trigger('click');

			if (button.data('champ_type') == 'calcul')
			{
				$('.calcul-details').removeClass('d-none');
			}
			else
			{
				$('.calcul-details').addClass('d-none');
			}
        });

		$('#tableau-ajouter-points-type-champ').on('change', function()
		{
			if ($(this).val() == 'calcul')
			{
				$('.calcul-details').removeClass('d-none');
			}
			else
			{
				$('.calcul-details').addClass('d-none');
			}
		});

		$('#tableau-modifier-points-type-champ').on('change', function()
		{
			if ($(this).val() == 'calcul')
			{
				$('.calcul-details').removeClass('d-none');
			}
			else
			{
				$('.calcul-details').addClass('d-none');
			}
		});

        $('#modal-tableau-modifier-points-sauvegarde').click(function()
        {
            var evaluation_id = $('#evaluation-data').data('evaluation_id');

            var $form = $('#modal-tableau-modifier-points-form');

            // extraire les donnes du formulaire rempli
            // var champ = $('#tableau-modifier-champ-nom

            $.post(base_url + 'evaluations/modal_tableau_modifier_points_sauvegarde', $form.serialize(),
            function(data)
            {
                if (data == true) 
                {
					// Sauvegarder la position de défilement avant le rechargement
					window.onbeforeunload = function() {
						sessionStorage.setItem('scrollPosition', window.scrollY);
					};

                    document.location.reload();
                    return true;
                }

            }, 'json');
        });

        $('#modal-tableau-modifier-points-effacer').click(function()
        {
            var $form = $('#modal-tableau-modifier-points-form');

            $.post(base_url + 'evaluations/modal_tableau_modifier_points_effacer', $form.serialize(),
            function(data)
            {
                if (data == true) 
                {
                    document.location.reload();
                    return true;
                }

            }, 'json');
        });
    }
});
