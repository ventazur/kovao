/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.COM > admin.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * STATS
     *
     * ---------------------------------------------------------------- */
	if (current_method == 'stats')
	{
		$('.btn-sm').click(function()
		{
			var $sel = $(this);
			var id = $sel.attr('id');

			if ($sel.hasClass('btn-dark'))
				return true;

			$('.btn-sm').each(function() {
				$(this).removeClass('btn-dark').addClass('btn-secondary');
			});

			$('.stats-contenu').each(function() {
				$(this).addClass('d-none');
			});

			$('#' + id).removeClass('btn-secondary').addClass('btn-dark');
			$('#' + id + '-contenu').removeClass('d-none');
		});	
	}

    /* ----------------------------------------------------------------
     *
     * ACTIVITE
     *
     * ---------------------------------------------------------------- */

	if ($('#activite-enseignants').length)
	{
        $('#activite-non-connectes-btn, #activite-etudiants-btn, #activite-enseignants-btn').click(function(e)
        {
			$('#activite-enseignants-btn').removeClass('active');
			$('#activite-etudiants-btn').removeClass('active');
            $('#activite-non-connectes-btn').removeClass('active');

			$('#activite-enseignants').addClass('d-none');
			$('#activite-etudiants').addClass('d-none');
            $('#activite-non-connectes').addClass('d-none');

            $(this).addClass('active');

            if ($(this).attr('id') == 'activite-non-connectes-btn')
            {
                $('#activite-non-connectes').removeClass('d-none');
            }

            if ($(this).attr('id') == 'activite-etudiants-btn')
            {
                $('#activite-etudiants').removeClass('d-none');
            }

            if ($(this).attr('id') == 'activite-enseignants-btn')
            {
                $('#activite-enseignants').removeClass('d-none');
            }
        });
	}

    /* ----------------------------------------------------------------
     *
     * HISTORIQUE D'ACTIVITE
     *
     * ---------------------------------------------------------------- */

    if ($('#historique-activite').length)
    {
        $('#historique-activite-btn .btn').click(function(e)
        {
            var btn = $(this);

            if (btn.hasClass('active'))
                return true;
            
            $('#historique-activite-btn .btn').removeClass('active');
            
            $('#historique-jour-d, #historique-jour, #historique-mois-d, #historique-mois').addClass('d-none');

            if ($(this).attr('id') == 'historique-jour-d-btn')
                $('#historique-jour-d').removeClass('d-none');

            if ($(this).attr('id') == 'historique-jour-btn')
                $('#historique-jour').removeClass('d-none');

            if ($(this).attr('id') == 'historique-mois-d-btn')
                $('#historique-mois-d').removeClass('d-none');

            if ($(this).attr('id') == 'historique-mois-btn')
                $('#historique-mois').removeClass('d-none');

            $(this).addClass('active');
        });
    }

    /* ----------------------------------------------------------------
     *
     * SOUMISSIONS
     *
     * ---------------------------------------------------------------- */

    if ($('#soumissions').length)
    {
        $('#soumissions-liste-btn').click(function()
        {
            $('#soumissions-journees-btn, #soumissions-meilleures-btn').removeClass('active');
            $('#soumissions-liste-btn').addClass('active');

            $('#soumissions-journees, #soumissions-meilleures').addClass('d-none');
            $('#soumissions-liste').removeClass('d-none');

        });

        $('#soumissions-journees-btn').click(function()
        {
            $('#soumissions-liste-btn, #soumissions-meilleures-btn').removeClass('active');
            $('#soumissions-journees-btn').addClass('active');

            $('#soumissions-liste, #soumissions-meilleures').addClass('d-none');
            $('#soumissions-journees').removeClass('d-none');
        });

        $('#soumissions-meilleures-btn').click(function()
        {
            $('#soumissions-liste-btn, #soumissions-journees-btn').removeClass('active');
            $('#soumissions-meilleures-btn').addClass('active');

            $('#soumissions-liste, #soumissions-journees').addClass('d-none');
            $('#soumissions-meilleures').removeClass('d-none');
        });
    }

    /* ----------------------------------------------------------------
     *
     * ACTIVITE
     *
     * ---------------------------------------------------------------- */

	if ($('#admin-parametres').length)
	{
        $('.parametre.force-change').keyup(function(e)
        {
            $(this).trigger('change');

        });

        $('.parametre').change(function(e)
        {
            var $sel = $(this);
            var clef = $sel.attr('name');
            var val  = null;

            if ($sel.hasClass('force-change'))
            {
                val = $sel.val();
            }
            else
            {
                if ($sel.is(':checked'))
                {
                    val = 'on';
                } 
                else
                {
                    val = 'off';
                }
            }
            
            $.post(base_url + 'admin/changer_parametre', 
                    { ci_csrf_token: cct, clef: clef, valeur: val },

            function(data)
            {
                if (data == true)
                {
                    $sel.parents('tr').css("background-color", "limegreen").animate({ backgroundColor: "inherit"}, 250);
                }
                else
                {
                    $sel.parents('tr').css("background-color", "pink").animate({ backgroundColor: "inherit"}, 250);
                }
            }, 'json');
        });
    }

});
