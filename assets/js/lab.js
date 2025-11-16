/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > Lab.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    // ----------------------------------------------------------------
    //
    // Verifier que le input est vraiment un nombre et qu'il ne contient pas
    // de caracteres illegaux.
    // 
    // ----------------------------------------------------------------

	function isValidNumber(input) 
	{
		if (typeof input !== "string") return false;

		let normalized = input.replace(",", ".");
		let numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

		return numberRegex.test(normalized);
	}


    /* ----------------------------------------------------------------
     *
     *  Montrer / Cacher les tags d'identification des champs
     *
     * ---------------------------------------------------------------- */

    $('#toggle-tags').click(function()
    {
        $('.tags').toggleClass('d-none');
		$('#precorrections-reset').toggleClass('d-none');

        reinit_indices();
    });

    /* ----------------------------------------------------------------
     *
     *  Choisir son partenaire avec select
	 *  Enregistrer les traces
     *
     * ---------------------------------------------------------------- */

	$('.lab-partenaire-select').change(function()
	{
		var $sel = $(this);

        var evaluation_reference = $('#evaluation-data').data('evaluation_reference');
        var evaluation_id        = $('#evaluation-data').data('evaluation_id');

		var eleve_id   	  = $sel.find('option:selected').val();
        var nom           = $sel.find('option:selected').data('nom');
		var matricule     = $sel.find('option:selected').data('matricule');
		var partenaire    = $sel.attr('name'); 
		var matricule_input_id = partenaire + '_matricule';

        $('#' + partenaire + '_nom').val(nom);

		$.post(base_url + 'evaluation/enregistrer_lab_select_identificaction_traces', 
			{ ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, partenaire: partenaire, eleve_id: eleve_id },
		function(data)
		{
			return true;
		}, 'json');
	});

    /* ----------------------------------------------------------------
     *
     * Traces des laboratoires
     *
     * ---------------------------------------------------------------- */

    /* ----------------------------------------------------------------
     *
     * Traces des partenaires de laboratoies
     * Traces des champs des tableaux
     *
     * ---------------------------------------------------------------- */

    $('#evaluation-identification-lab :input, #lab-tableaux-specifiques :input').change(function()
    {
        var $sel = $(this);

        var tableau_no = $sel.closest('.evaluation-tableau').data('tableau_no');

        var evaluation_reference = $('#evaluation-data').data('evaluation_reference');
        var evaluation_id        = $('#evaluation-data').data('evaluation_id');

        var input_name = $(this).attr('name');
        var champ_index = input_name.indexOf('-');

        // var lab_prefix = $('#lab-tableaux-specifiques').data('lab_prefix');
        var champ = input_name.substring(champ_index + 1);
        var champ_val = $(this).val();

        var champ_type = $(this).is('select') ? 'select' : 'input';

		//
		// Ceci empeche les etudiants d'entrer des valeurs erronees ou invalides dans les champs,
		// pouvant causer un probleme lors de la soumission de l'evaluation.
		//
        if ($(this).closest("#lab-tableaux-specifiques").length && champ_type == 'input' && champ_index > 0 && ! isValidNumber(champ_val))
        {
            $('#est-pas-sauvegarde' + tableau_no).show().fadeOut(2000);
			return false;
		}

        if ( ! en_direct)
        {
            $.post(base_url + 'evaluation/enregistrer_lab_input_traces', 
                { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, champ: champ, champ_val: champ_val, champ_type: champ_type },
            function(data)
            {
                if (data.res == true)
                {
                    if (data.champ_val != champ_val)
                    {
                        $sel.val(data.champ_val);
                    }

                    $('#est-sauvegarde' + tableau_no).show().fadeOut(2000);
                }
                else
                {
                    $('#est-pas-sauvegarde' + tableau_no).show().fadeOut(2000);
                }	
            }, 'json');
        }
    });

    /* ----------------------------------------------------------------
     *
     * Confirmer les partenaires de laboratoire
     * Confirmer le numero de place
     *
     * ---------------------------------------------------------------- */
    $('#lab-partenaires-confirmer-action').click(function()
    {
        var evaluation_reference = $('#evaluation-data').data('evaluation_reference');
        var evaluation_id        = $('#evaluation-data').data('evaluation_id');
		var lab_individuel		 = $('#lab-data').data('lab_individuel');

        $.post(base_url + 'evaluation/confirmer_lab_partenaires', 
            { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, lab_individuel: lab_individuel },
        function(data)
        {
            document.location.reload(true);
            return;
        });
    });

    /* ----------------------------------------------------------------
     *
     * Reinitialiser l'indice lorsque le champ change
     *
     * ---------------------------------------------------------------- */
    function reinit_indices()
    {
        $('.tag-champ').find('.points').html('').addClass('d-none');
        $('.tableau-points-obtenus').html('').addClass('d-none');
		$('#lab-tableaux-specifiques').find('input').css('background', '#FFFFFF');
		return true;
    }

    /* ----------------------------------------------------------------
     *
     * Reinitialiser l'indice lorsque le champ change
     *
     * ---------------------------------------------------------------- */

    $('#lab-tableaux-specifiques').on('change', 'input', function() 
    {
        reinit_indices();
    });

	/* ----------------------------------------------------------------
     *
     * Precorrection des tableaux
     * 
     * ---------------------------------------------------------------- */ 

	$('#precorrections-reset').click(function()
	{
		var $sel = $(this);
        var evaluation_id = $('#evaluation-data').data('evaluation_id');

		$sel.find('.spinner').removeClass('d-none');

        $.post(base_url + 'laboratoire/precorrections_reset',
            { ci_csrf_token: cct, evaluation_id: evaluation_id },
        function(data)
        {
			$sel.find('.spinner').addClass('d-none');

			if (data.res == true)
			{
				$('#precorrections-count').html(data.precorrection_essais);
			}
        }, 'json');
	});

	$('#precorrection-action').click(function()
	{
		$(this).find('.spinner').removeClass('d-none');

		precorriger_tableaux();

		$(this).find('.spinner').addClass('d-none');
		$(this).find('.termine').show().fadeOut(500);
	});

    function precorriger_tableaux()
    {
		//
		// Retablir le fond des champs d'une precorrection precedente
		// 

		$('#tab-tableaux-specifiques').find('input textarea').css('background', 'inherit');

        //
        // Les donnees de l'evaluation
        //

        var lab_prefix = $('#lab-tableaux-specifiques').data('lab_prefix');

        var evaluation_data = {
            evaluation_id: $('#evaluation-data').data('evaluation_id'),
			evaluation_reference: $('#evaluation-data').data('evaluation_reference'),
			lab_prefix: lab_prefix
        }

        //
        // Les valeurs des champs remplis
        //

		var champs_data = [];

		/*
		var $champs_remplis = $('#lab-tableaux-specifiques').find('input:not([readonly]), textarea:not([readonly])').filter(function() {
		   return $.trim($(this).val()) !== '';
		});

		var $champs_remplis = $('#lab-tableaux-specifiques').find('input:not([readonly]), textarea:not([readonly]), input[type="radio"]:checked').filter(function() {
    		return $.trim($(this).val()) !== '';
		});
		*/

		var $champs_remplis = $('#lab-tableaux-specifiques').find('input:not([readonly]):not([type="radio"]), input[type="radio"]:checked:not([readonly]), textarea:not([readonly])').filter(function() {
    		return $.trim($(this).val()) !== '';
		});

		$champs_remplis.each(function() {
			champs_data.push({
				name: $(this).attr('name'),
				val:  $(this).val()
			});
		});

        $.post(base_url + 'laboratoire/corriger_laboratoire' + '_ajax',
            { ci_csrf_token: cct, evaluation_data: evaluation_data, champs_data: champs_data },
        function(data)
        {
			if ('precorrections' in data)
			{
				$('#precorrections-count').html(data['precorrections']['penalite_str']);
			}

			if ('points_champs' in data) 
            {
				var points_champs = data.points_champs;
				
				Object.keys(points_champs).forEach(function(key) 
				{
					if (points_champs[key]['corrige'])
					{
                        if (points_champs[key]['points_obtenus'] != null)
                        {
                            let cpoints = points_champs[key]['points_obtenus'].toString().replace('.', ',');
                            $('#tag-' + key).find('.points').html(cpoints).removeClass('d-none');
                        }

						let $input = $('#' + lab_prefix + '-' + key);

						if (points_champs[key]['succes'] == true)
						{
							$input.css('background', '#E8F5E9');
						}
						else if (points_champs[key]['succes'] == false)
						{
							$input.css('background', '#FFEBEE');
						}
						else
						{
							$input.css('background', '#E8F5E9');
							// enlever le bleu 
                            // $input.css('background', '#E1F5FE');
						}

                        if ($input.is('select'))
                        {
							$input.css('background', '#E8F5E9');
							// enlever le bleu 
							// $input.css('background', '#E1F5FE');
                        }

					}
				});
			} 

			if ('points_bilan' in data && 'points_tableaux' in data.points_bilan) 
            {
                if ($('.tags').is(':visible'))
                {
                    var points_tableaux = data.points_bilan.points_tableaux;
                    
                    Object.keys(points_tableaux).forEach(function(key) 
                    {
                        if (points_tableaux[key]['points_obtenus'] != null)
                        {
                            let cpoints = points_tableaux[key]['points_obtenus'];
                            cpoints = cpoints.toString().replace('.', ',');
                            $('#tableau-points-obtenus-' + key).html(cpoints).removeClass('d-none');
                        }
                    });
                }
            } // if points_bilan

        }, 'json');
    }
});
