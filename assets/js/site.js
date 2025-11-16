/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > site.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    // ----------------------------------------------------------------
    //
    // Rafraichir la page et revenir au meme endroit
    //
    // ----------------------------------------------------------------

	// Enregistre la position de scroll avant le rechargement
	window.addEventListener('beforeunload', function () {
    	localStorage.setItem('scrollPosition', window.scrollY);
	});

	// Restaure la position de scroll après le rechargement
	window.addEventListener('load', function () {
    	const scrollPosition = localStorage.getItem('scrollPosition');
    	if (scrollPosition) {
        	window.scrollTo(0, parseInt(scrollPosition, 10));
        	localStorage.removeItem('scrollPosition'); // Nettoyage après usage
    	}
	});

    // ----------------------------------------------------------------
    //
    // Verifier le fonctionnement des cookies
    //
    // ----------------------------------------------------------------

    document.cookie = 'kovao_cookie_ok';

    if ( ! navigator.cookieEnabled || document.cookie.indexOf('kovao_cookie_ok') == -1)
    {
        window.location = base_url + "erreur/spec/cookie"; 
        return false;
    }

    /* ----------------------------------------------------------------
     *
     * Activer l'ajustement automatique de la grandeur des textarea
     *
     * ---------------------------------------------------------------- */

    if ($('textarea').length)
    {
        autosize($('textarea'));
    }

    /* ----------------------------------------------------------------
     *
     * Activer le spinner lorsque l'element est spinnable
     *
     * ---------------------------------------------------------------- */

    if ($('.spinnable').length)
    {
        $('.spinnable').click(function(e)
        {
            if ( ! $(e.target).hasClass('stop-spinner'))
            {
                $(this).find('.spinner').removeClass('d-none');
            }
        });
    }

    // ----------------------------------------------------------------------
    //
    // Activer les tooltips
    //
    // ----------------------------------------------------------------------
    
    if ($('[data-toggle="tooltip"]').length)
    {
  	    $('[data-toggle="tooltip"]').tooltip();
    }

    /* ----------------------------------------------------------------
     *
     * Activer les popovers
     *
     * ---------------------------------------------------------------- */
    
    if ($('[data-toggle="popover"]').length)
    {
        $('[data-toggle="popover"]').popover({
            placement: 'top',
            trigger: 'hover'
        });
    }

    /* ----------------------------------------------------------------
     *
     * Copier dans le presse-papier
     *
     * ---------------------------------------------------------------- */

    $('.copier-presse-papier').click(function(e)
    {
        e.preventDefault();

        var $sel = $(this);
        var info = $sel.data('info');

        function sleep(ms) 
        {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        async function copier()
        {
            $sel.find('i.fa-clipboard').css('color', 'dodgerblue');
            navigator.clipboard.writeText(info)
            await sleep(150);
            $sel.find('i.fa-clipboard').css('color', 'inherit');
        }

        copier();
    });

    // ----------------------------------------------------------------------
    //
    // Datepicker
    //
    // ----------------------------------------------------------------------

    if ($('input.datepicker').length)
    {
        $('body').on('focusin', 'input.datepicker', function() 
        {
            $(this).datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function(d, i) {
                    // make sure the value is not the same as before
                    // https://stackoverflow.com/questions/6471959/jquery-datepicker-onchange-event-help
                    if (d !== i.lastVal) {
                       $(this).change();
                    }
                },
            },
            $.datepicker.regional['fr-CA']);
        });

        // Francisation
        $.datepicker.regional['fr'] = {clearText: 'Effacer', clearStatus: '',
            closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
            prevText: '&lt;Préc', prevStatus: 'Voir le mois précédent',
            nextText: 'Suiv&gt;', nextStatus: 'Voir le mois suivant',
            currentText: 'Courant', currentStatus: 'Voir le mois courant',
            monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
            'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
            monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
            'Jul','Aoû','Sep','Oct','Nov','Déc'],
            monthStatus: 'Voir un autre mois', yearStatus: 'Voir une autre année',
            weekHeader: 'Sm', weekStatus: '',
            dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
            dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
            dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
            dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: 'Choisir le DD, MM d',
            dateFormat: 'dd/mm/yy', firstDay: 0, 
            initStatus: 'Choisir la date', isRTL: false};

        $.datepicker.setDefaults($.datepicker.regional['fr']);
    }

    /* ----------------------------------------------------------------
     *
     * Trier une table par colonne
     *
     * ---------------------------------------------------------------- */

    if ($('.tri-button').length)
    {
        $('.tri-button').click(function(e)
        {
            var $sel  = $(this);

            var clef  = 'tri'; // default
            var ordre = 'asc'; // default

            if ($sel.data('clef'))
            {
                var clef = $sel.data('clef');
            }

            if ($sel.data('ordre'))
            {
                var ordre = $sel.data('ordre');
            }

            var $table = $sel.parents('table');
            var rows   = $table.find('tbody tr').get();

            if (ordre == 'asc')
            {
                // Ordre croissant (asc)

                rows.sort(function(a, b) {
                    var keyA = $(a).data(clef);
                    var keyB = $(b).data(clef);
                    if (keyB < keyA) return 1;
                    if (keyB > keyA) return -1;
                    return 0;
                });
            }
            else
            {
                // Ordre decroissant (desc)

                rows.sort(function(a, b) {
                    var keyA = $(a).data(clef);
                    var keyB = $(b).data(clef);
                    if (keyA < keyB) return 1;
                    if (keyA > keyB) return -1;
                    return 0;
                });
            }

            $.each(rows, function(index, row) {
                $table.children('tbody').append(row);
            });
        });
    }

    // ----------------------------------------------------------------------
    //
	// Convertir les valeurs avec decimales, du francais a l'anglais
	//
	// nn,00$ -> nn.00$
	//
    // ----------------------------------------------------------------------
	function convertDecimalPoint(val, strip_off_decimals, add_decimals)
	{
        // 
        // Declaration des valeurs par default si non declaree
        //

        if (typeof(strip_off_decimals) === 'undefined') strip_off_decimals = false;
        if (typeof(add_decimals) === 'undefined') add_decimals = false;

		var output_val = null;

		var regex          = /^(-?[0-9]*)\,?\.?([0-9]*)/;
		var regex_matches  = val.match(regex);

		// no point or comma
		if (regex_matches[2] == '')
		{
			if (add_decimals)
            {
				return val + '.00';
            }

			return val;
		}

		if (regex_matches !== null)
		{
			output_val = regex_matches[1] + '.' + regex_matches[2];
		}

		if (isNaN(output_val))
		{
			output_val = 0.00
		}
		else
		{
			if (strip_off_decimals)
			{
                if (regex_matches[2] === '00') {
                    output_val = parseFloat(val).toFixed(0);
                }
                else {
                    output_val = parseFloat(val).toFixed(2);
                }
			}
			else
			{
				output_val = parseFloat(val).toFixed(2);
				// output_val = val.toFixed(2);
			}
		}

		return output_val;
	}

    // ----------------------------------------------------------------
    //
	// Convertir les valeurs avec decimales, de l'anglais au francais,
    // pour les elements ayant une classe 'ax-points'
    // 
    // ----------------------------------------------------------------

    $('body').on('focusout', ':input.ax-points', function() 
    {
        input_value = $(this).val();
        input_name  = $(this).attr('name');

        if ($(this).hasClass('ax-date') || input_value == '')
            return false;

        input_value = convertDecimalPoint(input_value, false, true);

        if ( ! isNaN(input_value))
            $(this).val(input_value);
    });

});
