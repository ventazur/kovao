/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > Recherche.js
 *
 * ==================================================================== */

$(document).ready(function() 
{
    var min_len_livesearch = 3;

    $('#recherche-requete').focus(); 

    /* ----------------------------------------------------------------
     *
     * Recherche > Chercher par matricule (numero DA)
     *
     * Lorsqu'on clique sur le matricule d'un etudiant pour sortir
     * seulement les recherches avec ce matricule.
     *
     * ---------------------------------------------------------------- */

    $('#recherche-resultats').on('click', '.recherche-matricule', function()
    {
        var matricule = $(this).data('numero_da');

        $('#recherche-requete').val(matricule).trigger('keyup');
    });

    /* ----------------------------------------------------------------
     *
     * Recherche > Cacher les resultats au changement
     *
     * ---------------------------------------------------------------- */

    $('#recherche-requete').keydown(function()
    {
        $('#recherche-resultats-contenu').empty();

        /*
        $('.en-attente').addClass('d-none');
        $('.en-precision').addClass('d-none');
        $('.en-recherche').removeClass('d-none');
        */
    });

    /* ----------------------------------------------------------------
     *
     * Recherche > Status de la recherche
     *
     * ---------------------------------------------------------------- */

    function status_recherche(s)
    {
        // La barre de recheche est vide
        if (s == 'en_attente')
        {
            $('.en-attente').removeClass('d-none');
            $('.en-precision').addClass('d-none');
            $('.en-recherche').addClass('d-none');
        }
        
        // La barre de recherche contient des caracteres, possible precision a venir
        else if (s == 'en_precision')
        {
            $('.en-attente').addClass('d-none');
            $('.en-precision').removeClass('d-none');
            $('.en-recherche').addClass('d-none');
        }

        // La recherche est active
        else if (s == 'en_recherche')
        {
            $('.en-attente').addClass('d-none');
            $('.en-precision').addClass('d-none');
            $('.en-recherche').removeClass('d-none');
        }
    }

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
            $('#recherche-resultats-contenu').empty();

            status_recherche('en_attente');
        }
        else if (requete.length < min_len_livesearch) 
        {
            $('#recherche-resultats-contenu').empty();
            
            status_recherche('en_precision');
        }
        else 
        {
            status_recherche('en_recherche');

            $.post(base_url + 'recherche/recherche_en_direct', { ci_csrf_token: cct, requete: requete },
            function(data) 
            {
                console.log('[' + requete + ']');

                status_recherche('en_precision');

                $('#recherche-resultats-contenu').html(data);

            }, 'json');
        }
    }

    $('.en-precision').click(function()
    {
        $('#recherche-requete').val('');
        $('#recherche-resultats-contenu').empty();

        status_recherche('en_attente');
    });

});
