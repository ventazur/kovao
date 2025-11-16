/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > outils.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Recherche d'un etudiant ou une etudiante
     *
     * ---------------------------------------------------------------- */
    if ($('#recherche-etudiant').length)
    {
        var min_len_livesearch = 3;

        $('#recherche-requete').focus(); 

        /* ----------------------------------------------------------------
         *
         * Recherche > Cacher les resultats au changement
         *
         * ---------------------------------------------------------------- */

        $('#recherche-requete').keydown(function()
        {
            $('#recherche-resultats').empty();
            $('#recherche-aucun-resultat').addClass('d-none');
        });
        
        /* ----------------------------------------------------------------
         *
         * Recherche > En direct
         *
         * ---------------------------------------------------------------- */

        $('#recherche-requete').keyup(_.debounce(rechercher_requete , 500));

        function rechercher_requete()
        {
            var $sel = $('#recherche-requete');
            var requete = $sel.val();

            if (requete.length == 0)
            {
                $('#recherche-resultats').empty();
            }
            else if (requete.length < min_len_livesearch) 
            {
                $('#recherche-resultats').empty();
            }
            else 
            {
                $.post(base_url + 'outils/recherche_en_direct', { ci_csrf_token: cct, requete: requete },
                function(data) 
                {
                    if (data.length > 0)
                    {
                        $('#recherche-resultats').html(data);
                        $('#recherche-resultats').removeClass('d-none');
                    }
                    else
                    {
                        $('#recherche-aucun-resultat').removeClass('d-none');
                    }

                }, 'json');
            }
        } // fonction rechercher_requete
    }

    /* ----------------------------------------------------------------
     *
     * Question a reponse numerique              (TYPE 6)
     * Question a reponse numerique par equation (TYPE 9)
     *
     * ---------------------------------------------------------------- */
    if ($('#outil-question-type-6-9').length)
    {
        //
        // Empecher les changements de type de tolerance
        //
        
        $('.tolerance-type').change(function(e)
        {
            e.preventDefault();

            var type_orig = $(this).data('type_orig');

            if ($(this).val() != type_orig)
            {
                $(this).val(type_orig);
            }
        });

        //
        // Calculer le pointage
        //

        $('#calculer-pointage').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'outils/question9_calculer_pointage', $('#question_type_9_form').serialize(),
        	function(data)
        	{
                if (data.hasOwnProperty('reponse'))
                {
                    $('#reponse').addClass('is-invalid');
                    $('#pointage').html('?');
                    $('#calculer-pointage').find('.spinner').addClass('d-none');

                    return;
                }

                $('#reponse').removeClass('is-invalid');
                $('#calculer-pointage').find('.spinner').addClass('d-none');
   
                $('#pointage').html(data); 

            }, 'json');

        });

        //
        // Effacer une tolerance
        //
    
        $('.effacer-tolerance').click(function(e)
        {
            e.preventDefault();

            $(this).parents('tr').remove();

            var count_tolerances = 0;

            $('#tolerances tr.tolerance').each(function()
            {
                console.log($(this).html());
                count_tolerances++;
            });
            console.log(count_tolerances);

            if (count_tolerances == 0)
            {
                $('#tolerances-titre').remove();
                $('#tolerances').remove();
            }

        });
    }

    /* ----------------------------------------------------------------
     *
     * Question a reponse litterale courte (TYPE 7)
     *
     * ---------------------------------------------------------------- */
    if ($('#outil-question-type-7').length)
    {
        //
        // TYPE 7
        // Calculer le pointage
        //

        var $rh_textarea = $('#reponses-hypothetiques-textarea');
        var $rh = $('#rh');
        var $ra = $('#ra');

        // Pour me faire plaisir...

        $rh_textarea.keyup(function()
        {
            if ($rh_textarea.val().match(/\n/g)||[].length)
            {
                if ($ra.hasClass('d-none'))
                {
                    $ra.removeClass('d-none');
                    $rh.addClass('d-none');
                }
            }
            else
            {
                if ($rh.hasClass('d-none'))
                {
                    $rh.removeClass('d-none');
                    $ra.addClass('d-none');
                }
            }
        });

        $('#calculer-pointage').click(function(e)
        {
            e.preventDefault();

        	$.post(base_url + 'outils/question7_calculer_pointage', $('#question_type_7_form').serialize(),
        	function(data)
        	{
                $('#similarite-calculee-wrap').addClass('d-none');
                $('#similarite-suggeree-wrap').addClass('d-none');

                if (data.hasOwnProperty('reponses_hypothetiques'))
                {
                    $('#reponses_hypothetiques_textarea').addClass('is-invalid');
                    $('#pointage').html('?');
                    $('#calculer-pointage').find('.spinner').addClass('d-none');

                    return;
                }

                if (data.hasOwnProperty('similarite'))
                {
                    if (data.hasOwnProperty('correcte'))
                    {
                        // calculee
                        $('#pointage').html(data['points_obtenus']); 
                        $('#similarite-calculee').html(data['similarite']); 
                        $('#similarite-calculee-wrap').removeClass('d-none');
                    }
                    else
                    {
                        // suggeree
                        $('#pointage').html('?'); 
                        $('#similarite-suggeree').html(data['similarite']); 
                        $('#similarite-suggeree-wrap').removeClass('d-none');
                    }
                }

                $('#reponses_hypothetiques_textarea').removeClass('is-invalid');
                $('#calculer-pointage').find('.spinner').addClass('d-none');

            }, 'json');

        });
    }

    /* ----------------------------------------------------------------
     *
     * TOLERANCES 
     *
     * ---------------------------------------------------------------- */
    if ($('#outil-tolerances').length)
    {
        /* ----------------------------------------------------------------
         *
         * TOLERANCES > Calculer votre pointage
         *
         * ---------------------------------------------------------------- */
        $('#outil-tolerances #calculer').click(function(e)
        {
            e.preventDefault();

			var $form = $(this).parents('form');

            $.post(base_url + 'outils/tolerances_calculer_pointage', $form.serialize(),
            function(data)
            {
				$('.spinner').addClass('d-none');

                if (typeof data == 'object')
                {
                    if ('reponse' in data)
                    {
            			$form.find('input[name="reponse"]').addClass('is-invalid');
						$('#alert-reponse').removeClass('d-none');
                    }

                    if ('tolerances' in data)
                    {
            			$form.find('.tolerance').addClass('is-invalid');
						$('#alert-tolerances').removeClass('d-none');
                    }

                    return false;   
                }

				$('.alert').addClass('d-none');
				$(':input').removeClass('is-invalid');

                if ($.isNumeric(data))
                {
                    $('#pointage').empty().html(data);
                    return false;
                }

            }, 'json');
        });
    } // if #outil-tolerances

    /* ----------------------------------------------------------------
     *
     * SIMILARITE
     *
     * ---------------------------------------------------------------- */
    if ($('#outil-similarite').length)
    {
        /* ----------------------------------------------------------------
         *
         * SIMILARITE > Calculer votre pointage
         *
         * ---------------------------------------------------------------- */
        $('#outil-similarite #calculer').click(function(e)
        {
            e.preventDefault();

			var $form = $(this).parents('form');

            $.post(base_url + 'outils/similarite_calculer_pointage', $form.serialize(),
            function(data)
            {
				$('.spinner').addClass('d-none');

                if (typeof data == 'object')
                {
                    $('#similarite-calculee').html(data['similarite']);
                    $('#similarite-calculee').parent('div').css('visibility', 'visible');

                    $('#pointage').html(data['points_obtenus']);
                }

            }, 'json');
        });
    } // if #outils-similarite
});
