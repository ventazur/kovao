/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > resultats2.js (version 2)
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
     * Ordonner une table selon les nom et prenoms des etudiants.
     *
     * ---------------------------------------------------------------- */

    $('.tri-nom-button').click(function(e)
    {
        var $sel = $(this);

        var $table = $(this).parents('table');

        var rows = $table.find('tbody tr').get();

        rows.sort(function(a, b) {
            var keyA = $(a).data('clef_tri_nom');
            var keyB = $(b).data('clef_tri_nom');
            if (keyB < keyA) return 1;
            if (keyB > keyA) return -1;
            return 0;
        });

        $.each(rows, function(index, row) {
            $table.children('tbody').append(row);
        });
    });

    $('.tri-nom-button').trigger('click');

    /* ----------------------------------------------------------------
     *
     * Ordonner une table selon les dates de remise
     *
     * ---------------------------------------------------------------- */

    $('.tri-remise-button').click(function(e)
    {
        var $sel = $(this);

        var $table = $(this).parents('table');

        var rows = $table.find('tbody tr').get();

        rows.sort(function(a, b) {
            var keyA = $(a).data('clef_tri_remise');
            var keyB = $(b).data('clef_tri_remise');
            if (keyB < keyA) return 1;
            if (keyB > keyA) return -1;
            return 0;
        });

        $.each(rows, function(index, row) {
            $table.children('tbody').append(row);
        });
    });

    /* ----------------------------------------------------------------
     *
     * Ordonner une table selon les resultats des etudiants
     *
     * ---------------------------------------------------------------- */

    $('.tri-resultat-button').click(function(e)
    {
        var $sel = $(this);

        var $table = $(this).parents('table');

        var rows = $table.find('tbody tr').get();

        rows.sort(function(a, b) {
            var keyA = $(a).data('clef_tri_resultat');
            var keyB = $(b).data('clef_tri_resultat');
            if (keyA < keyB) return 1;
            if (keyA > keyB) return -1;
            return 0;
        });

        $.each(rows, function(index, row) {
            $table.children('tbody').append(row);
        });
    });

    /* ----------------------------------------------------------------
     *
     * Fonctions pour aggrandir ou minimiser les fenetres
     *
     * ---------------------------------------------------------------- */

    function collapse_win($toggler, $sel, $sel_o)
    {
        $toggler.removeClass('expanded');
        $sel.addClass('d-none');
        $sel_o.find('.expand').removeClass('d-none');
        $sel_o.find('.collap').addClass('d-none');
    }

    function expand_win($toggler, $sel, $sel_o)
    {
        $toggler.addClass('expanded');
        $sel.removeClass('d-none');
        $sel_o.find('.expand').addClass('d-none');
        $sel_o.find('.collap').removeClass('d-none');
    }

    /* ----------------------------------------------------------------
     *
     * Montrer ou cacher un cours
     *
     * ---------------------------------------------------------------- */

    $('.cours-liste-toggle').click(function(e)
    {
        e.preventDefault();

        var $toggler = $(this);
        var $sel     = $toggler.next('.resultats-cours-contenu');
        var $sel_o   = $toggler.find('.cours-liste-toggle-btn');

        var toggler_id = $toggler.attr('id');

        if ($sel.hasClass('d-none'))
        {
            expand_win($toggler, $sel, $sel_o);
            localStorage.setItem(toggler_id, 'expanded');
        }
        else
        {
            collapse_win($toggler, $sel, $sel_o);
            localStorage.setItem(toggler_id, 'collapsed');
        }
    });

    //
    // Fixer les fenetres lors du chargement de la page
    //
    $('.cours-liste-toggle').each(function () 
    {   
        var toggler_id = $(this).attr('id');

        var $toggler   = $('#' + toggler_id);
        var $sel       = $toggler.next('.resultats-cours-contenu');
        var $sel_o     = $toggler.find('.cours-liste-toggle-btn');

        var state = localStorage.getItem(toggler_id);

        if (state == 'expanded')
        {
            expand_win($toggler, $sel, $sel_o);
        }
        else if (state == 'collapsed')
        {
           collapse_win($toggler, $sel, $sel_o);
        }

    });

    /* ----------------------------------------------------------------
     *
     * Montrer ou cacher un groupe.
     *
     * ---------------------------------------------------------------- */

    $('.soumissions-liste-toggle').click(function(e)
    {
        e.preventDefault();

        var $toggler = $(this);
        var $sel     = $toggler.next('.soumissions-liste');
        var $sel_o   = $toggler.find('.soumissions-liste-toggle-btn');
 
        var toggler_id = $toggler.attr('id');

        if ($sel.hasClass('d-none'))
        {
            expand_win($toggler, $sel, $sel_o);
            localStorage.setItem(toggler_id, 'expanded');
        }
        else
        {
            collapse_win($toggler, $sel, $sel_o);
            localStorage.setItem(toggler_id, 'collapsed');
        }
    });

    //
    // Fixer les fenetres lors du chargement de la page
    //
    $('.soumissions-liste-toggle').each(function () 
    {   
        var toggler_id = $(this).attr('id');

        var $toggler   = $('#' + toggler_id);
        var $sel       = $toggler.next('.soumissions-liste');
        var $sel_o     = $toggler.find('.soumissions-liste-toggle-btn');

        var state = localStorage.getItem(toggler_id);

        if (state == 'expanded')
        {
            expand_win($toggler, $sel, $sel_o);
        }
        else if (state == 'collapsed')
        {
           collapse_win($toggler, $sel, $sel_o);
        }
    });


    /* ----------------------------------------------------------------
     *
     * Rendre visible un groupe d'evaluations corrigees
     *
     * ---------------------------------------------------------------- */
    $('.rendre-visible, .rendre-invisible').click(function(e)
    {
        e.preventDefault();

        var soumission_ids = $(this).data('soumission_ids');
        var sans_reponses  = $(this).data('sans_reponses');

        if ($(this).hasClass('rendre-visible'))
        {
            var operation = 'visible';
        }
        else if ($(this).hasClass('rendre-invisible'))
        {
            var operation = 'invisible';
        }
        else
        {
            return false;
        }

       	$.post(base_url + 'resultats/changer_visibilite', 
                { ci_csrf_token: cct, soumission_ids: soumission_ids, sans_reponses: sans_reponses, operation: operation },
        function(data)
        {
            if (data == true)
            {
                document.location.reload(true);
                return;
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Effacer une soumission
     * modal
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
     * Statistiques d'une evaluation d'un groupe
     *
     * ---------------------------------------------------------------- */

    $('.stats-evaluation').click(function(e)
    {
        var $sel = $(this);

        var soumission_ids = $sel.data('soumission_ids');
        var groupe_no      = $sel.data('groupe_no');

       	$.post(base_url + 'stats/ecrire_session', 
                { ci_csrf_token: cct, soumission_ids: soumission_ids, groupe_no: groupe_no },
        function(data)
        {
            if (data == true)
            {
                window.location = base_url + 'stats/resultats/evaluation';
                return;
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Statistiques d'une evaluation d'un groupe
     *
     * ---------------------------------------------------------------- */

    $('.defilement-evaluation').click(function(e)
    {
        var $sel = $(this);

        // var soumission_references = $sel.data('soumission_references');
        
        var groupe_no = $sel.data('groupe_no');

        //
        // Extraire la liste de toutes les references des soumissions du groupe selon l'ordre actuel
        //

        var soumission_references = []; 

        $sel.parents('table.soumissions').find('tr').each(function() 
        {
            var sr = $(this).data('soumission_reference');

            if (typeof sr != 'undefined')
            {
                soumission_references.push(sr);
            }
        });

       	$.post(base_url + 'consulter/voir_defilement', 
                { ci_csrf_token: cct, soumission_references: soumission_references, groupe_no: groupe_no },
        function(data)
        {
            if (data != false)
            {
                window.location = base_url + data;
                return;
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Remettre a zero le nombre de vues
     *
     * ---------------------------------------------------------------- */

    if ($('.remettre-a-zero').length) 
    {
        $('#modal-reset-vues').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var soumission_id = button.data('soumission_id');
            var soumission_reference = button.data('soumission_reference');

            modal.find('input[name="soumission_id"]').val(soumission_id);
            modal.find('input[name="soumission_reference"]').val(soumission_reference);
        });

		$('#modal-reset-vues-sauvegarde').click(function(e)
		{
			e.preventDefault();

            var modal = $('#modal-reset-vues');

            var soumission_id        = modal.find('input[name="soumission_id"]').val();
            var soumission_reference = modal.find('input[name="soumission_reference"]').val();

        	$.post(base_url + 'resultats/remettre_zero_vues', { ci_csrf_token: cct, soumission_id: soumission_id, soumission_reference: soumission_reference },
        	function(data)
			{
                document.location.reload(true);
                return;
        	}, 'json');
		});
    }

});
