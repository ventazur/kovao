/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > scrutins.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     *  Scrutin
     *
     * ---------------------------------------------------------------- */
    if ($('#scrutin').length)
    {
        $('input[name="scrutin_choix_id"]').change(function()
        {
            var $sel = $(this);
            var $scrutin_choix = $sel.parents('.scrutin-choix');

            $('.scrutin-choix').removeClass('surligne');

            if ($sel.prop('checked'))
            {
                $scrutin_choix.addClass('surligne');
            }
        });
	}

	if ($('.datepicker').length)
	{
		var date_originale = $('#date-echeance').val();

		$('input.datepicker').change(function() {

			var scrutin_id   = $('#scrutin-data').data('scrutin_id');
			var date_choisie = $(this).val();

			$('#datepicker-saved').addClass('d-none');
			$('#datepicker-failed').addClass('d-none');
			$('#datepicker-saving').removeClass('d-none');

        	$.post(base_url + 'scrutins/changer_date_echeance', { ci_csrf_token: cct, scrutin_id: scrutin_id, date_echeance: date_choisie },
        	function(data)
        	{
				$('#datepicker-saving').addClass('d-none');

				if (data == true)
				{
					date_originale = date_choisie;
					$('#datepicker-saved').removeClass('d-none');
					return false;
				}
					
				$('#date-echeance').val(date_originale);
				$('#datepicker-failed').removeClass('d-none');			

        	}, 'json');
		});

		$('#datepicker-clear').click(function(e) {
	
			e.preventDefault;

			$('#date-echeance').val('').change();
		});
    }

    /* ----------------------------------------------------------------
     *
     * CREER : Selectionner tous les enseignants
     *
     * ---------------------------------------------------------------- */
    if ($('#selectionner-tous-enseignants').length)
    {
        $('#selectionner-tous-enseignants').click(function()
        {
            $('.participants').prop('checked', true);
        });
    }

    /* ----------------------------------------------------------------
     *
     * CREER : Deselectionner tous les enseignants
     *
     * ---------------------------------------------------------------- */
    if ($('#deselectionner-tous-enseignants').length)
    {
        $('#deselectionner-tous-enseignants').click(function()
        {
            $('.participants').prop('checked', false);
        });
    }

    /* ----------------------------------------------------------------
     *
     * EDITEUR :
	 *
     * Spinner
     *
     * ---------------------------------------------------------------- */
    $('.spinnable').click(function()
    {
        $(this).find('.spinner').removeClass('d-none');
    });

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
     *
     * Modifier la question d'un scrutin
     *
     * ---------------------------------------------------------------- */
	if ($('#modal-modifier-question').length)
	{
        var question_texte = $('#modal-modifier-question-texte').val();

        //
        // sauvegarde des modifications a la questison
        //
    	$('.modal').delegate('#modal-modifier-question-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

			if ($('#modal-modifier-question-texte').val() == question_texte)
			{
				// aucun changement detecte, simplement fermer le modal
				$('#modal-modifier-question').modal('hide')
				return false;
			}

        	$.post(base_url + 'scrutins/modifier_question', $('#modal-modifier-question-form').serialize(),
        	function(data)
        	{
                document.location.reload(true);
                return false;

        	}, 'json');
    	});
	} // if #modal-modifier-question

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
     *
     * Ajout choix
     *
     * ---------------------------------------------------------------- */
	if ($('#modal-ajout-choix').length)
	{
    	$('.modal').delegate('#modal-ajout-choix-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'scrutins/ajout_choix', $('#modal-ajout-choix-form').serialize(),
        	function(data)
        	{
                document.location.reload(true);
                return false;

        	}, 'json');
    	});
    } // if #modal-ajout-choix

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
     *
     * Effacer choix
     *
     * ---------------------------------------------------------------- */
	if ($('.effacer-choix').length)
	{
    	$('.effacer-choix').click(function(e)
    	{
            e.preventDefault();

            var scrutin_id = $('#scrutin-data').data('scrutin_id');
            var choix_id = $(this).data('choix_id'); 

        	$.post(base_url + 'scrutins/effacer_choix', { ci_csrf_token: cct, scrutin_id: scrutin_id, choix_id: choix_id },
        	function(data)
        	{
                document.location.reload(true);
                return false;

        	}, 'json');
    	});
    } // if .effacer-choix

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
     *
     * Changer la participation d'un participant
     *
     * ---------------------------------------------------------------- */
	if ($('.participant-options').length)
	{
    	$('.participant-options .participant, .participant-options .non-participant').click(function(e)
    	{
            e.preventDefault();

            if ($(this).hasClass('active'))
                return false;

            if ($(this).hasClass('spinnable'))
            {
                $(this).css('background', '#3949AB');
                $(this).find('.fa-spin').removeClass('d-none');
            }

            var scrutin_id    = $('#scrutin-data').data('scrutin_id');
            var enseignant_id = $(this).parents('.participant-options').data('enseignant_id'); 

        	$.post(base_url + 'scrutins/changer_participation', 
                { ci_csrf_token: cct, scrutin_id: scrutin_id, enseignant_id: enseignant_id },
        	function(data)
        	{
                document.location.reload(true);
                return false;

        	}, 'json');
    	});
    } // if .participant-options

    /* ----------------------------------------------------------------
     *
     * EDITEUR :
	 *
     * Toggle entre code morin ou non
     *
     * ---------------------------------------------------------------- */
    if ($('#code-morin').length)
    {
        $('#code-morin').change(function() {

            var scrutin_id = $('#scrutin-data').data('scrutin_id');

        	$.post(base_url + 'scrutins/changer_code_morin', { ci_csrf_token: cct, scrutin_id: scrutin_id },
        	function()
        	{
                document.location.reload(true);
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * EDITEUR :
	 *
     * Toggle entre anonyme ou non
     *
     * ---------------------------------------------------------------- */
    if ($('#votes-anonymes').length)
    {
        $('#votes-anonymes').change(function() {

            var scrutin_id = $('#scrutin-data').data('scrutin_id');

        	$.post(base_url + 'scrutins/changer_anonyme', { ci_csrf_token: cct, scrutin_id: scrutin_id },
        	function()
        	{
                document.location.reload(true);
                return false;
            });
        });
    }

    /* ------------------------------------------------------------------------
	 *
     * EDITEUR :
     *
     * Lancer un scrutin
     *
     * ------------------------------------------------------------------------ */
	$('#modal-lancer-scrutin-sauvegarde').click(function(e)
	{
		e.preventDefault();

		var scrutin_id = $('#scrutin-data').data('scrutin_id');

        window.location = base_url + "scrutins/lancer/" + scrutin_id;
        return false;
    });

    /* ------------------------------------------------------------------------
	 *
     * EDITEUR :
     *
     * Ajouter un document
     *
     * ------------------------------------------------------------------------ */
    $('#ajout-document-input').change(function() 
	{
		var $sel = $(this);
		var file = this.files[0];

		$(this).parents('#ajout-document').find('.document-upload-spinner').removeClass('d-none');

        uploadFile(file, $sel);
    });

    /* ------------------------------------------------------------------------
	 *
     * EDITEUR :
     *
     * Routine d'envoie d'un fichier 
     *
     * ------------------------------------------------------------------------ */
    function uploadFile(file, $sel)
    {
        var data = new FormData();

		var scrutin_id = $('#scrutin-data').data('scrutin_id');
		
        data.append('ci_csrf_token', cct);
        data.append('upload_file', file);
		data.append('scrutin_id', scrutin_id);

        $.ajax({
            url: base_url + 'scrutins/upload',
            method: 'POST',
            data: data,
            success: function(file_data) 
            {
				document.location.reload(true);
				return true;
            },
            error: function (xhr, ajaxOptions, thrownError) 
            {
                console.log(xhr.status);
                console.log(thrownError);
            },

            // options to tell JQuery not to process data or worry about content-type
            cache: false,
            contentType: false,
            processData: false
        });
    }

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
     *
     * Modifier la description (caption) d'un document
     *
     * ---------------------------------------------------------------- */
	if ($('#modal-modifier-document-caption').length)
	{
        var doc_caption = $('#modal-modifier-question-texte').val();

        $('#modal-modifier-document-caption').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var scrutin_doc_id = button.data('scrutin_doc_id');
            var doc_caption = $.trim(button.parents('tr.document').find('span.document-caption').html());

            modal.find('input[name="scrutin_doc_id"]').val(scrutin_doc_id);
            modal.find('#modal-modifier-document-caption-texte').val(doc_caption);
        });

        //
        // sauvegarde des modifications a la questison
        //
    	$('.modal').delegate('#modal-modifier-document-caption-sauvegarde', 'click', function(e)
    	{
            e.preventDefault();

        	$.post(base_url + 'scrutins/modifier_document_caption', $('#modal-modifier-document-caption-form').serialize(),
        	function(data)
        	{
                document.location.reload(true);
                return false;

        	}, 'json');
    	});
	} // if #modal-modifier-document-caption

    /* ----------------------------------------------------------------
     *
     * EDITEUR : 
     *
     * Effacer un document
     *
     * ---------------------------------------------------------------- */
	if ($('.effacer-document').length)
	{
        $('#modal-effacer-document').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var scrutin_doc_id = button.data('scrutin_doc_id');

            //
            // mettre a jour le modal avec les donnees de la question demandee
            // 

            modal.find('input[name="scrutin_doc_id"]').val(scrutin_doc_id);
        });

    	$('#modal-effacer-document-sauvegarde').click(function(e)
    	{
            e.preventDefault();

            var scrutin_id     = $('#scrutin-data').data('scrutin_id');
            var scrutin_doc_id = $(this).data('scrutin_doc_id'); 

        	$.post(base_url + 'scrutins/effacer_document', $('#modal-effacer-document-form').serialize(),
        	function(data)
        	{
                document.location.reload(true);
                return false;

        	}, 'json');
    	});
    } // if .effacer-document

    /* ----------------------------------------------------------------
     *
     * EDITEUR :
	 *
     * Effacer un scrutin
     *
     * ---------------------------------------------------------------- */
    if ($('#modal-effacer-scrutin-sauvegarde').length)
    {
        $('#modal-effacer-scrutin-sauvegarde').click(function(e)
        {
            e.preventDefault();

            var scrutin_id = $(this).data('scrutin_id');

        	$.post(base_url + 'scrutins/effacer_scrutin', { ci_csrf_token: cct, scrutin_id: scrutin_id },
        	function()
        	{
                window.location = base_url + "scrutins/gerer";
                return false;
        	});
        });
    }

    /* ----------------------------------------------------------------
     *
     * GERER :
     *
     * Terminer un scrutin
     *
     * ---------------------------------------------------------------- */
    if ($('#modal-terminer-scrutin').length)
    {
        $('#modal-terminer-scrutin').on('show.bs.modal', function(e) 
        {
            console.log('ici');
            var button = $(e.relatedTarget);
            var modal  = $(this);

            var scrutin_reference = button.data('scrutin_reference');

            //
            // mettre a jour le modal avec les donnees de la question demandee
            // 

            modal.find('#terminer-scrutin-action').data('scrutin_reference', scrutin_reference);
        });

        $('#terminer-scrutin-action').click(function(e)
        {
            var scrutin_reference = $(this).data('scrutin_reference');
            var epoch = parseInt((new Date).getTime() / 1000);

            window.location = base_url + "scrutin/" + scrutin_reference + '/terminer/' + epoch;
        });
    }

    /* ----------------------------------------------------------------
     *
     * GERER :
     *
     * Effacer un scrutin
     *
     * ---------------------------------------------------------------- */
    
    if ($('.effacer-scrutin-lance').length)
    {
        $('#modal-effacer-scrutin-lance').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var scrutin_reference = button.data('scrutin_reference');
            var modal = $(this);

			modal.find('input[name="scrutin_reference"]').val(scrutin_reference);
        });

        $('#modal-effacer-scrutin-lance-sauvegarde').click(function(e)
        {
            e.preventDefault();

			$.post(base_url + 'scrutins/effacer_scrutin_lance', $('#modal-effacer-scrutin-lance-form').serialize(),
			function(data)
			{
				if (data == true)
				{
                    document.location.reload(true);
				}
				else
				{
					$('#modal-effacer-scrutin-lance').modal('hide');
				}

				return true;

			}, 'json');
        });
    }

});
