/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > groupe.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * PARAMETRES DU GROUPE
     *
     * ---------------------------------------------------------------- */

    if ($('#groupe-parametres').length)
    {
        /* ----------------------------------------------------------------
         *
         * Inscription permise
         *
         * ---------------------------------------------------------------- */
        $('#inscription-permise-oui, #inscription-permise-non').click(function()
        {
            var $input = $(this).find('input');
            var groupe_id = $('#groupe-data').data('groupe_id');

            if ( ! $input.is(':checked'))
            {
                var permission = $input.val();

                $.post(base_url + 'groupe/groupe_inscription_permise_toggle', { ci_csrf_token: cct, groupe_id: groupe_id, permission: permission },
                function(data)
                {
                }, 'json');
            }
        });

        /* ----------------------------------------------------------------
         *
         * Inscription permise
         *
         * ---------------------------------------------------------------- */
        var code = $('#code-inscription').val();

        $('#code-inscription').on('change paste keyup', function()
        {
            $('#code-inscription-sauvegarder').removeClass('d-none');
        });

        $('#code-inscription-sauvegarder').click(function()
        {
            var $sauvegarder = $(this);
            var code = $('#code-inscription').val();
            var groupe_id = $('#groupe-data').data('groupe_id');

            $('#code-inscription-sauvegarder-spinner').removeClass('d-none');

            $.post(base_url + 'groupe/groupe_nouveau_code_inscription', { ci_csrf_token: cct, groupe_id: groupe_id, code: code },
            function(data)
            {
                $('#code-inscription-sauvegarder').addClass('d-none');
                $('#code-inscription-sauvegarder-spinner').addClass('d-none');

                $('#code-inscription-effacer').removeClass('d-none');

            }, 'json');
        });

        $('#code-inscription-effacer').click(function()
        {
            $effacer = $(this);
            var groupe_id = $('#groupe-data').data('groupe_id');

            $('#code-inscription-effacer-spinner').removeClass('d-none');

            $.post(base_url + 'groupe/groupe_effacer_code_inscription', { ci_csrf_token: cct, groupe_id: groupe_id },
            function(data)
            {
                $('#code-inscription-effacer').addClass('d-none');
                $('#code-inscription-effacer-spinner').addClass('d-none');

                $('#code-inscription').val('');
            }, 'json');
        });
    }

    /* ----------------------------------------------------------------
     *
     * SEMESTRES
     *
     * ---------------------------------------------------------------- */

    /* ----------------------------------------------------------------
     *
     * Ajouter un semestre (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajouter-semestre').length) 
    {
        $('#modal-ajouter-semestre').on('show.bs.modal', function (e) 
        {
            var modal = $(this);
            var button = $(e.relatedTarget); // le button qui a amorce le modal

            modal.find('.erreurs').addClass('d-none');
        });

    	$('.modal').delegate('#modal-ajouter-semestre-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

			var $form = $('#modal-ajouter-semestre-form');

        	$.post(base_url + 'groupe/ajouter_semestre', $form.serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload(true);
                    return;
            	}
            	else if ($.isPlainObject(data)) 
				{
					// effacer toutes les erreurs
					$form.find('.erreurs').addClass('d-none')

                    // afficher l'erreur
					$form.find('.erreur-' + data['erreur']).removeClass('d-none');
                }
        	}, 'json');
    	});

    } // if length #modal-ajouter-semestre

    /* ----------------------------------------------------------------
     *
     * Modifier (editer) un semestre nmodal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-editer-semestre').length) 
    {
        $('#modal-editer-semestre').on('show.bs.modal', function (e) 
        {
            var $form = $('#modal-editer-semestre-form');
            var modal = $(this);

            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var semestre_data = button.find('div.semestre-data');

            var semestre_id = semestre_data.data('semestre_id');
            var semestre_nom = semestre_data.data('semestre_nom');
            var semestre_code = semestre_data.data('semestre_code');
            var semestre_debut_date = semestre_data.data('semestre_debut_date');
            var semestre_fin_date = semestre_data.data('semestre_fin_date');

            modal.find('input[name="semestre_id"]').val(semestre_id);
            modal.find('input[name="semestre_code"]').val(semestre_code);
            modal.find('input[name="semestre_nom"]').val(semestre_nom);
            modal.find('input[name="semestre_code"]').val(semestre_code);
            modal.find('input[name="semestre_debut_date"]').val(semestre_debut_date);
            modal.find('input[name="semestre_fin_date"]').val(semestre_fin_date);

            // effacer les erreurs presentes
            $form.find('.erreurs').addClass('d-none')
        });

        //
        // modifier le semestre
        // 
        $('.modal').delegate('#modal-editer-semestre-sauvegarde', 'click', function(e)
        {
            e.preventDefault();

            var $form = $('#modal-editer-semestre-form');

            $.post(base_url + 'groupe/editer_semestre', $form.serialize(),
            function(data)
            {
                if (data == true) 
                {
                    document.location.reload(true);
                    return;
                }
                else if ($.isPlainObject(data)) 
                {
                    // afficher l'erreur
					$form.find('.erreur-' + data['erreur']).removeClass('d-none');
                }
            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Effacer un semestre
         *
         * ---------------------------------------------------------------- */
        $('.modal').delegate('#modal-effacer-semestre-sauvegarde', 'click', function(e)
        {
            e.preventDefault();

            var $form = $('#modal-editer-semestre-form');

            $.post(base_url + 'groupe/effacer_semestre', $form.serialize(),
            function(data) 
            {
                if (data == true) 
                {
                    document.location.reload(true);
                    return;
                }
                else if ($.isPlainObject(data)) 
                {
                    // afficher l'erreur
					$form.find('.erreur-' + data['erreur']).removeClass('d-none');
                }
			
            }, 'json');
        });

    } // if #modal-modifier-semestre length

    /* ----------------------------------------------------------------
     *
     * COURS
     *
     * ---------------------------------------------------------------- */

    /* ----------------------------------------------------------------
     *
     * Ajouter un cours (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-ajouter-cours').length) 
    {
        $('#modal-ajouter-cours').on('show.bs.modal', function (e) 
        {
            var $form = $('#modal-ajouter-cours-form');

            $form.find('.erreurs').addClass('d-none'); // effacer les erreurs presentes
        });

    	$('.modal').delegate('#modal-ajouter-cours-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

			var $form = $('#modal-ajouter-cours-form');

        	$.post(base_url + 'groupe/ajouter_cours', $form.serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload(true);
                    return;
            	}
            	else if ($.isPlainObject(data)) 
				{
					$form.find('.erreur-' + data['erreur']).removeClass('d-none');
                }
        	}, 'json');
    	});

    } // if length #modal-ajouter-cours

    /* ----------------------------------------------------------------
     *
     * Editer un cours (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-editer-cours').length) 
    {
        var modal = null;

        $('#modal-editer-cours').on('show.bs.modal', function (e) 
        {
            modal = $(this);

            var button = $(e.relatedTarget);
            var cours_data = button.find('div.cours-data');

            var cours_id = cours_data.data('cours_id');
            var cours_nom = cours_data.data('cours_nom');
            var cours_nom_court = cours_data.data('cours_nom_court');
            var cours_code = cours_data.data('cours_code');
            var cours_code_court = cours_data.data('cours_code_court');
            var cours_url = cours_data.data('cours_url');
            var desuet = cours_data.data('desuet');

            modal.find('input[name="cours_id"]').val(cours_id);
            modal.find('input[name="cours_code"]').val(cours_code);
            modal.find('input[name="cours_nom"]').val(cours_nom);
            modal.find('input[name="cours_nom_court"]').val(cours_nom_court);
            modal.find('input[name="cours_code"]').val(cours_code);
            modal.find('input[name="cours_code_court"]').val(cours_code_court);
            modal.find('input[name="cours_url"]').val(cours_url);

            if (desuet == true)
            {
                modal.find('input[name="desuet"]').prop('checked', true);
            }
            else
            {
                modal.find('input[name="desuet"]').prop('checked', false);
            }
        });

    	$('.modal').delegate('#modal-editer-cours-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

			var $form = $('#modal-editer-cours-form');

        	$.post(base_url + 'groupe/editer_cours', $form.serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload(true);
                    return;
            	}
            	else if ($.isPlainObject(data)) 
				{
					// Enlever les erreurs precedentes
					$('#modal-editer-cours-form :input').each(function(index) 
					{
						$(this).removeClass('is-invalid');
					});

                	// Prise en charge des erreurs
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
                    $('#modal-editer-cours-sauvegarde').find('.spinner').addClass('d-none'); 
                    modal.modal('hide');
                }
        	}, 'json');
    	});

        /* ----------------------------------------------------------------
         *
         * Effacer un cours
         *
         * ---------------------------------------------------------------- */

        $('#confirmation-effacer-cours').change(function()
        {
            if ($(this).is(':checked'))
            {
                $('#modal-effacer-cours-sauvegarde').removeClass('d-none');
            }
            else
            {
                $('#modal-effacer-cours-sauvegarde').addClass('d-none');
            }
        });

        $('.modal').delegate('#modal-effacer-cours-sauvegarde', 'click', function(e)
        {
            e.preventDefault();

            var $form = $('#modal-editer-cours-form');

            $.post(base_url + 'groupe/effacer_cours', $form.serialize(),
            function(data) 
            {
                if (data == true) 
                {
                    document.location.reload(true);
                    return;
                }
            }, 'json');
        });

    } // if #modal-modifier-cours length

    /* ----------------------------------------------------------------
     *
     * ENSEIGNANTS
     *
     * ---------------------------------------------------------------- */

    /* ----------------------------------------------------------------
     *
     * Montrer ou cacher les anciennes demandes
     *
     * ---------------------------------------------------------------- */
    if ($('.anciennes-demandes').length)
    {
        $('.anciennes-demandes.groupe-section-titre').click(function()
        {
            $('.anciennes-demandes.groupe-section-titre').toggle();
            $('.anciennes-demandes.groupe-section-box').toggle();
        });
    }

    /* ----------------------------------------------------------------
     *
     * Editer (modifier) un enseignant (modal)
     *
     * ---------------------------------------------------------------- */

    if ($('#modal-editer-enseignant').length) 
    {
        var modal = null;

        $('#modal-editer-enseignant').on('show.bs.modal', function (e) 
        {
            modal = $(this);

            var button = $(e.relatedTarget);
            var enseignant_id = button.data('enseignant_id');
			var nom = button.data('nom');
			var prenom = button.data('prenom');
			var niveau = button.data('niveau');
			var courriel = button.data('courriel');
			var genre = button.data('genre');

			modal.find('input[name="enseignant_id"]').val(enseignant_id);
            modal.find('input[name="nom"]').val(nom);
            modal.find('input[name="prenom"]').val(prenom);
            modal.find('select[name="niveau"]').val(niveau);
            modal.find('input[name="courriel"]').val(courriel);
			modal.find('select[name="genre"]').val(genre);
        });

    	$('.modal').delegate('#modal-editer-enseignant-sauvegarde', 'click', function(e)
    	{
        	e.preventDefault();

			var $form = $('#modal-editer-enseignant-form');

        	$.post(base_url + 'groupe/editer_enseignant', $form.serialize(),
        	function(data)
        	{
           		if (data == true) 
				{
                    document.location.reload(true);
                    return;
            	}
            	else
				{
					modal.modal('hide');
                }
        	}, 'json');
    	});

    } // if length #modal-editer-enseignant

    /* ----------------------------------------------------------------
     *
     * Activer ou desactiver un enseignant
     *
     * ---------------------------------------------------------------- */
	$('.activer-enseignant, .desactiver-enseignant').click(function()
	{
		var enseignant_id = $(this).data('enseignant_id');

		$('#activer-desactiver-spinner-' + enseignant_id).removeClass('d-none');

        $.post(base_url + 'groupe/activer_enseignant', { ci_csrf_token: cct, enseignant_id: enseignant_id },
		function(data)
		{
			if (data == true) 
			{
				document.location.reload(true);
				return;
			}

			$('#activer-desactiver-spinner-' + enseignant_id).addClass('d-none');

		}, 'json');
	});

    /* ----------------------------------------------------------------
     *
     * Changer responsable d'un groupe
     *
     * ---------------------------------------------------------------- */
    $('.changer-responsable').click(function()
    {
        var enseignant_id = $(this).data('enseignant_id');
        var groupe_id     = $(this).data('groupe_id');
        var $spinner      = $(this).find('.spinner');

        $spinner.removeClass('d-none');

        $.post(base_url + 'groupe/changer_responsable_groupe', { ci_csrf_token: cct, enseignant_id: enseignant_id, groupe_id: groupe_id },
		function(data)
		{
			if (data == true) 
			{
				document.location.reload(true);
				return;
			}

            $spinner.addClass('d-none');

		}, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Approuver ou desapprouver un enseignant en attente d'approbation
     *
     * ---------------------------------------------------------------- */
    if ($('.approuver-enseignant, .desapprouver-enseignant').length)
    {
        $('#approuver-enseignant').on('show.bs.modal', function(e) 
        {
            var button     = $(e.relatedTarget);
            var joindre_id = button.data('joindre_id');

            $('#approuver-enseignant-sauvegarder').data('joindre_id', joindre_id);
        })

        $('#approuver-enseignant-sauvegarder').click(function(e)
        {
            e.preventDefault();

            var joindre_id = $(this).data('joindre_id');

            $.post(base_url + 'groupe/approuver_enseignant', { ci_csrf_token: cct, joindre_id: joindre_id },
            function(data)
            {
                document.location.reload(true);
                return;
            }, 'json');
        });

        $('.desapprouver-enseignant').click(function(e)
        {
            e.preventDefault();

            var joindre_id = $(this).data('joindre_id');

            $.post(base_url + 'groupe/desapprouver_enseignant', { ci_csrf_token: cct, joindre_id: joindre_id },
            function(data)
            {
                if (data == true) 
                {
                    document.location.reload(true);
                    return;
                }
            }, 'json');
        });
    }


});
