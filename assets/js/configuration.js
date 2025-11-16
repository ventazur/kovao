/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > Configuration.js
 *
 * ==================================================================== */
$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Semestre >
     *
     * Selectionner ou deselectionner un semestre
     *
     * ---------------------------------------------------------------- */
    $('.choisir-semestre').click(function(e) 
    {
        e.preventDefault();

        var $semestre   = $(this);
        var semestre_id = $semestre.data('semestre_id');

        $.post(base_url + 'configuration/selection_semestre', { ci_csrf_token: cct, semestre_id : semestre_id },
        function(data)
        {
            if (data == true)
            {
                $semestre.find('i').removeClass('d-none');

                document.location.reload(true);
                return false;
            }
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Cours >
     *
     * Selectionner ou deselectionner un cours
     *
     * ---------------------------------------------------------------- */
    $('.choisir-cours').click(function() 
    {
        var $cours   = $(this);
        var cours_id = $cours.data('cours_id');
        var semestre_id = $('#semestre-data').data('semestre_id');

        $.post(base_url + 'configuration/selection_cours', { ci_csrf_token: cct, cours_id: cours_id, semestre_id: semestre_id },
        function(data)
        {
            if (data == true)
            {
                $cours.find('i').removeClass('d-none');

                document.location.reload(true);
                return false;
            }
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Evaluation >
     *
     * Selectionner ou deselectionner un evaluation
     *
     * ---------------------------------------------------------------- */
    $('#selection-evaluations').on('click', '.choisir-evaluation', function(e) 
    {
        e.preventDefault();

        $('.erreur-evaluation').addClass('d-none');

        var $evaluation   = $(this);

        var evaluation_id = $evaluation.data('evaluation_id');
		var cours_id      = $evaluation.data('cours_id');
        var semestre_id   = $('#semestre-data').data('semestre_id');

        var evaluation_selectionnee = $evaluation.data('evaluation_selectionnee');

        $evaluation.find('.evaluation-spinner').removeClass('d-none');

        $.post(base_url + 'configuration/selection_evaluation', 
                { ci_csrf_token: cct, evaluation_id: evaluation_id, cours_id: cours_id, semestre_id: semestre_id, evaluation_selectionnee : evaluation_selectionnee },
        function(data)
        {
            $evaluation.find('.evaluation-spinner').addClass('d-none');

            if (data == true)
            {
                if ($evaluation.hasClass('btn-dark'))
                {
                    $evaluation.data('evaluation_selectionnee', '0');
                    $evaluation.removeClass('btn-dark').addClass('btn-outline-primary');
                }
                else if ($evaluation.hasClass('btn-outline-primary'))
                {
                    $evaluation.data('evaluation_selectionnee', '1');
                    $evaluation.removeClass('btn-outline-primary').addClass('btn-dark');
                }
            }

            // Il y a une erreur dans l'evaluation. 

            if (data == 9)
            {
                $('#erreur' + evaluation_id).removeClass('d-none');
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Etudiants >
     *
     * Visualiser les listes d'etudiants 
     *
     * ---------------------------------------------------------------- */
	$('.liste-etudiants-btn').click(function(e)
	{
		var $liste = $(this);
		var $liste_wrap = $(this).parents('.liste-etudiants');

		if ( ! $liste.hasClass('active'))
		{
			$liste.addClass('active');

			$liste.css('border-radius', '3px 3px 0 0');
			$liste_wrap.find('table').removeClass('d-none');
		}
		else
		{
			$liste.removeClass('active');

			$liste.css('border-radius', '3px 3px 3px 3px');
			$liste_wrap.find('table').addClass('d-none');
		}

	});

    /* ----------------------------------------------------------------
     *
     * Etudiants >
     *
     * Associer/Dissocier le compte trouve a un etudiant de vos listes.
     *
     * ---------------------------------------------------------------- */
	$('.compte-trouve').click(function(e) 
	{
        var $sel = $(this);

        var etudiant_id  = $sel.data('etudiant_id');
        var cours_id     = $sel.data('cours_id');
        var semestre_id  = $sel.data('semestre_id');
        var cours_groupe = $sel.data('cours_groupe');
        var numero_da    = $sel.data('numero_da');

        if ($sel.hasClass('actif'))
        {
            // Dissocier

            $.post(base_url + 'configuration/dissocier_compte', 
                    { ci_csrf_token: cct, etudiant_id: etudiant_id, cours_id: cours_id, semestre_id: semestre_id, cours_groupe: cours_groupe, numero_da: numero_da },
            function(data)
            {
                if (data == true)
                {
                    $sel.removeClass('actif');
                }

            }, 'json');
        }
        else
        {
            // Associer

            $.post(base_url + 'configuration/associer_compte', 
                    { ci_csrf_token: cct, etudiant_id: etudiant_id, cours_id: cours_id, semestre_id: semestre_id, cours_groupe: cours_groupe, numero_da: numero_da },
            function(data)
            {
                if (data == true)
                {
                    $sel.addClass('actif');
                }

            }, 'json');
        }

        $sel.find('.spinner').addClass('d-none');

    });

    /* ----------------------------------------------------------------
     *
     * Etudiants >
     *
     * Ajouter un etudiant dans une liste
     *
     * ---------------------------------------------------------------- */
	$('#modal-ajouter-etudiant').on('show.bs.modal', function (e) 
	{
		var button = $(e.relatedTarget); // le button qui a amorce le modal

		var cours_id = button.data('cours_id');
        var cours_nom = button.data('cours_nom');
		var groupe = button.data('groupe');
		var semestre_id = $('#semestre-data').data('semestre_id');

		var modal = $(this);

		//
		// mettre a jour le modal avec les donnees de la question demandee
		// 

		modal.find('input[name="semestre_id"]').val(semestre_id);
		modal.find('input[name="cours_id"]').val(cours_id);
		modal.find('input[name="groupe"]').val(groupe);

        $('#modal-ajouter-etudiant-cours').html(cours_nom);
        $('#modal-ajouter-etudiant-groupe').html(groupe);

        // Enlever les erreurs precedentes

        $('#modal-ajouter-etudiant-form :input').each(function(index) 
        {
            $(this).removeClass('is-invalid');
        });
	});

	$('.modal').delegate('#modal-ajouter-etudiant-sauvegarde', 'click', function(e)
	{
		e.preventDefault();
	
		var $form = $('#modal-ajouter-etudiant-form');

		$.post(base_url + 'configuration/ajouter_etudiant_liste', $form.serialize(),
		function(data)
		{
			if (data == true)
			{
				document.location.reload(true);
				return false;
			}

            // Enlever les erreurs precedentes

            $('#modal-ajouter-etudiant-form :input').each(function(index) 
            {
                $(this).removeClass('is-invalid');
            });

            // Montrer les erreurs

            for (var property in data) 
            {
                if (data.hasOwnProperty(property)) 
                {
                    console.log('#modal-ajouter-etudiant-' + property.replace(/_/g, '-'));
                    var $field = $('#modal-ajouter-etudiant-' + property.replace(/_/g, '-'));

                    $field.addClass('is-invalid');
                }
            }

		}, 'json');
	});

    /* ----------------------------------------------------------------
     *
     * Etudiants > Modifier un etudiant
     *
     * ---------------------------------------------------------------- */
	$('#modal-modifier-etudiant').on('show.bs.modal', function (e) 
	{
		var button = $(e.relatedTarget); // le button qui a amorce le modal

		var semestre_id  = $('#semestre-data').data('semestre_id');
		var cours_id     = button.data('cours_id');
		var groupe       = button.data('groupe');
        var eleve_id     = button.data('eleve_id');
        var numero_da    = button.data('numero_da');
		var prenom_nom   = button.data('eleve_prenom_nom');
        var temps_supp   = button.data('temps_supp');

		var modal = $(this);

		modal.find('input[name="semestre_id"]').val(semestre_id);
		modal.find('input[name="cours_id"]').val(cours_id);
		modal.find('input[name="groupe"]').val(groupe);
		modal.find('input[name="numero_da"]').val(numero_da);
		modal.find('input[name="eleve_id"]').val(eleve_id);
        modal.find('input[name="temps_supp"]').val(temps_supp);

        $('#eleve-prenom-nom').html(prenom_nom);
	});

	$('.modal').delegate('#modal-modifier-etudiant-sauvegarde', 'click', function(e)
	{
		e.preventDefault();
	
		var $form = $('#modal-modifier-etudiant-form');

		$.post(base_url + 'configuration/modifier_etudiant_liste', $form.serialize(),
		function(data)
		{
            document.location.reload(true);
            return false;

		}, 'json');
	});

    /* ----------------------------------------------------------------
     *
     * Etudiants >
     *
     * Effacer un etudiant d'un groupe
     *
     * ---------------------------------------------------------------- */
	$('#modal-effacer-etudiant').on('show.bs.modal', function (e) 
	{
		var button = $(e.relatedTarget); // le button qui a amorce le modal

		var semestre_id  = $('#semestre-data').data('semestre_id');
		var cours_id     = button.data('cours_id');
		var groupe       = button.data('groupe');
        var eleve_id     = button.data('eleve_id');
        var numero_da    = button.data('numero_da');
		var prenom_nom   = button.data('eleve_prenom_nom');

		var modal = $(this);

		modal.find('input[name="semestre_id"]').val(semestre_id);
		modal.find('input[name="cours_id"]').val(cours_id);
		modal.find('input[name="groupe"]').val(groupe);
		modal.find('input[name="numero_da"]').val(numero_da);
		modal.find('input[name="eleve_id"]').val(eleve_id);

        $('#eleve-prenom-nom').html(prenom_nom);
	});

	$('.modal').delegate('#modal-effacer-etudiant-sauvegarde', 'click', function(e)
	{
		e.preventDefault();
	
		var $form = $('#modal-effacer-etudiant-form');

		$.post(base_url + 'configuration/effacer_etudiant_liste', $form.serialize(),
		function(data)
		{
            document.location.reload(true);
            return false;
		}, 'json');
	});

    /* ----------------------------------------------------------------
     *
     * Etudiants >
     *
     * Effacer une liste d'etudiants
     *
     * ---------------------------------------------------------------- */
	$('#modal-effacer-liste').on('show.bs.modal', function (e) 
	{
		var button = $(e.relatedTarget); // le button qui a amorce le modal
		var cours_id = button.data('cours_id');
		var groupe = button.data('groupe');
		var semestre_id = $('#semestre-data').data('semestre_id');

		var modal = $(this);

		//
		// mettre a jour le modal avec les donnees de la question demandee
		// 
		modal.find('input[name="semestre_id"]').val(semestre_id);
		modal.find('input[name="cours_id"]').val(cours_id);
		modal.find('input[name="groupe"]').val(groupe);
	});

	$('.modal').delegate('#modal-effacer-liste-sauvegarde', 'click', function(e)
	{
		e.preventDefault();
	
		var $form = $('#modal-effacer-liste-form');

		$.post(base_url + 'configuration/effacer_liste', $form.serialize(),
		function(data)
		{
			if (data == true)
			{
				document.location.reload(true);
				return false;
			}
			else
			{
				$('#modal-effacer-liste').modal('hide');
			}

		}, 'json');
	});

});
