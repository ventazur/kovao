/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > resultats4.js (version 4)
 *
 * ==================================================================== */

$(document).ready(function()
{
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
     * Ponderation
     *
     * ---------------------------------------------------------------- */

    $('.resultats-evaluation-titre-lien .ajuster-ponderation').click(function(e)
    {
        e.preventDefault();
    });

    $('#modal-ajuster-ponderation').on('show.bs.modal', function (e) 
    {
        var modal  = $(this);
        var button = $(e.relatedTarget); // le button qui a amorce le modal

        var evaluation_id = button.data('evaluation_id'); 
        var ponderation   = button.data('ponderation');

        modal.find('input[name="evaluation_id"]').val(evaluation_id);
        modal.find('input[name="ponderation"]').val(ponderation);

        $('#modal-effacer-ponderation-sauvegarde').removeClass('d-none');

        if (ponderation == '')
        {
            $('#modal-effacer-ponderation-sauvegarde').addClass('d-none');
        }
    });

    $('#modal-ajuster-ponderation').on('shown.bs.modal', function(e)
    {
        var modal = $(this);
        
        modal.find('input[name="ponderation"]').focus();
    });

    $('#modal-ajuster-ponderation-sauvegarde').click(function()
    {
        var $modal = $('#modal-ajuster-ponderation');        

        var evaluation_id = $modal.find('input[name="evaluation_id"]').val();
        var semestre_id   = $modal.find('input[name="semestre_id"]').val();
        var ponderation   = $modal.find('input[name="ponderation"]').val();

        $.post(base_url + 'resultats/ajuster_ponderation', { ci_csrf_token: cct, evaluation_id: evaluation_id, semestre_id: semestre_id, ponderation: ponderation },
        function(data)
        {
            $modal.find('.spinner').addClass('d-none');

            document.location.reload(true);
            return true;

        }, 'json');
    });

    $('#modal-effacer-ponderation-sauvegarde').click(function()
    {
        var $modal = $('#modal-ajuster-ponderation');        

        var evaluation_id = $modal.find('input[name="evaluation_id"]').val();
        var semestre_id   = $modal.find('input[name="semestre_id"]').val();

        $.post(base_url + 'resultats/effacer_ponderation', { ci_csrf_token: cct, evaluation_id: evaluation_id, semestre_id: semestre_id },
        function(data)
        {
            $modal.find('.spinner').addClass('d-none');

            document.location.reload(true);
            return true;

        }, 'json');
    });

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
            // localStorage.setItem(toggler_id, 'expanded');
        }
        else
        {
            collapse_win($toggler, $sel, $sel_o);
            // localStorage.setItem(toggler_id, 'collapsed');
        }
    });

    //
    // Fixer les fenetres lors du chargement de la page
    //
    
    /* Toujours ouvert au premier chargement
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
    */

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
            // localStorage.setItem(toggler_id, 'expanded');
        }
        else
        {
            collapse_win($toggler, $sel, $sel_o);
            // localStorage.setItem(toggler_id, 'collapsed');
        }
    });

    //
    // Fixer les fenetres lors du chargement de la page
    //

    /* Toujours ouvert au premier chargement 
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
    */

    /* ----------------------------------------------------------------
     *
     * Rendre visible ou invisible,
     *
     * - une soumission
     * - plusieurs soumissions (simultanement)
     *
     * ---------------------------------------------------------------- */

    $('#modal-soumission-visibilite, #modal-soumissions-visibilite').on('show.bs.modal', function (e) 
    {
        var button = $(e.relatedTarget); // le button qui a amorce le modal

        var soumission_ids = button.data('soumission_ids');
        var soumission_references = button.data('soumission_references');

        var date = null;
        var heure = null;

        if (button.data('date'))
        {
            date = button.data('date');
        }

        if (button.data('heure'))
        {
            heure = button.data('heure');
        }

        var modal = $(this);

        modal.find('input[name="soumission_ids"]').val(soumission_ids);
        modal.find('input[name="soumission_references"]').val(soumission_references);
        modal.find('input[name="date"]').val(date);
        modal.find('input[name="heure"]').val(heure);
    });

    //
    // Une soumission
    //

    $('.modal').delegate('#modal-soumission-rendre-visible', 'click', function(e)
    {
        e.preventDefault();

        var $form = $('#modal-soumission-visibilite-form');

       	$.post(base_url + 'resultats/rendre_visible', $form.serialize(),
        function(data)
        {
            document.location.reload(true);
            return;

        }, 'json');
    });

    $('.modal').delegate('#modal-soumission-rendre-invisible', 'click', function(e)
    {
        e.preventDefault();

        var $form = $('#modal-soumission-visibilite-form');

       	$.post(base_url + 'resultats/rendre_invisible', $form.serialize(),
        function(data)
        {
            document.location.reload(true);
            return;

        }, 'json');
    });

    //
    // Plusieurs soumissions
    //

    $('.modal').delegate('#modal-soumissions-rendre-visible', 'click', function(e)
    {
        e.preventDefault();

        var $form = $('#modal-soumissions-visibilite-form');

       	$.post(base_url + 'resultats/rendre_visible', $form.serialize(),
        function(data)
        {
            document.location.reload(true);
            return;

        }, 'json');
    });

    $('.modal').delegate('#modal-soumissions-rendre-invisible', 'click', function(e)
    {
        e.preventDefault();

        var $form = $('#modal-soumissions-visibilite-form');

       	$.post(base_url + 'resultats/rendre_invisible', $form.serialize(),
        function(data)
        {
            document.location.reload(true);
            return;

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
        e.preventDefault();

        var $sel = $(this);

        var soumission_ids = $sel.data('soumission_ids');
        var groupe_no      = $sel.data('groupe_no');
        var url            = $sel.attr('href');
        var requete        = $sel.data('requete');

       	$.post(base_url + 'stats/ecrire_session', 
                { ci_csrf_token: cct, soumission_ids: soumission_ids, groupe_no: groupe_no, requete: requete },
        function(data)
        {
            if (data == true)
            {
                // window.location = base_url + 'stats/resultats/evaluation';
                window.location = url;
                return;
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Defilement
     *
     * ---------------------------------------------------------------- */

    $('.defilement-evaluation').click(function(e)
    {
        var $sel = $(this);

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
                { ci_csrf_token: cct, soumission_references: soumission_references },
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
     * Defilement (toutes les evaluations)
     *
     * ---------------------------------------------------------------- */

    $('.defilement-evaluation-toutes').click(function(e)
    {
        var $sel = $(this);

        //
        // Extraire la liste de toutes les references des soumissions du groupe selon l'ordre actuel
        //

        var soumission_references = []; 

        $('table.soumissions').find('tr').each(function() 
        {
            var sr = $(this).data('soumission_reference');

            if (typeof sr != 'undefined')
            {
                soumission_references.push(sr);
            }
        });

       	$.post(base_url + 'consulter/voir_defilement', 
                { ci_csrf_token: cct, soumission_references: soumission_references },
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
