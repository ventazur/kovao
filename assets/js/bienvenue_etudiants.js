/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ============================================================================
 *
 * KOVAO.com > bienvenue.js (Bienvenue aux etudiants)
 *
 * ============================================================================ */

$(document).ready(function()
{   
    // ------------------------------------------------------------------------
    //
    // SANS SOUS DOMAINE
    //
    // ------------------------------------------------------------------------

    if ($('#bienvenue-www').length)
    {
        // ----------------------------------------------------------------
        //
        // Etudiant > Aller vers une evaluation selon sa reference
        //
        // ----------------------------------------------------------------
        //
        // Note : Une fonction identique se trouve dans evaluation.js.
        //
        // ----------------------------------------------------------------

        var $helper     = $('#vers-evaluation-helper');
        var $helper_msg = $('#vers-evaluation-helper-msg');

        $('#vers-evaluation-query').focusin(function() 
        {
            $helper.addClass('d-none');
            $helper_msg.html('');
        });

        $('#vers-evaluation').click(function(e)
        {
            e.preventDefault();

            var ref = $('#vers-evaluation-query').val();

            if (ref == null || ref == '')
            {
                $helper_msg.html('Vous devez entrer une référence.');
                $helper.removeClass('d-none');

                return false;
            }

            var teststr = /^[a-z]{6}$/i.test(ref);
            
            if ( ! teststr)  
            {
                $helper_msg.html('Cette référence est invalide.');
                $helper.removeClass('d-none');

                return false;
            }
            
            $.post(base_url + 'bienvenue/aller_vers_evaluation', { ci_csrf_token: cct, ref: ref },
            function(data)
            {
                if (data == false) 
                {
                    $helper_msg.html('Cette référence est invalide.');
                    $helper.removeClass('d-none');

                    return false;
            	}

                window.location.replace(data);    
                return true;
                    
            }, 'json');
        });
    
    } // sans sous domaine


    // ------------------------------------------------------------------------
    //
    // AVEC SOUS DOMAINE : ETUDIANTS
    //
    // ------------------------------------------------------------------------
    
    if ($('#bienvenue').length)
    {
        // ----------------------------------------------------------------
        //
        // Etudiant > Temps limite
        //
        // ----------------------------------------------------------------

        $('#modal-etudiants-temps-limite').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal

            var modal = $(this);
            var evaluation_reference = button.data('evaluation_reference');
            
            $('#modal-etudiants-temps-limite-debuter').data('evaluation_reference', evaluation_reference);
        });

        $('#modal-etudiants-temps-limite-debuter').click(function(e)
        {
            var evaluation_reference = $(this).data('evaluation_reference');

            var redirect_url = base_url + 'evaluation/' + evaluation_reference;

            window.location = redirect_url;
            return false;
        });

        // ----------------------------------------------------------------
        //
        // Etudiant > Choisir un cours
        //
        // ----------------------------------------------------------------

        $('#bienvenue').delegate('#choisir-cours select', 'change', function(e)
        {
            $('#choisir-enseignant').addClass('d-none');
            $('#choisir-evaluation').addClass('d-none');
            $('#aller-evaluation').addClass('d-none');
            $('#aller-evaluation-temps-limite').addClass('d-none');

            $('#choisir-enseignant-select').empty();                
            $('#choisir-evaluation-select').empty();

            var cours_id = $('#choisir-cours-select').val(); 

            if (cours_id == 0 || cours_id == '0' || cours_id == null)
            {
                window.location = current_url;
                return false;
            }

            $('#choisir-cours-spinner').removeClass('d-none');

            $.post(base_url + 'bienvenue/choisir_cours', { ci_csrf_token: cct, cours_id: cours_id },
            function(data)
            {
                if (data != true) 
                {
                    $('#choisir-cours-spinner').addClass('d-none');

                    $html = '<option value="0"></option>';
                    $('#choisir-enseignant-select').append($html);

                    $.each(data, function(index, arr) 
                    {
                        $html = '<option value="' + arr.enseignant_id + '">' + arr.prenom + ' ' + arr.nom + '</option>';

                        $('#choisir-enseignant-select').append($html);
                    });
                   
                    $('#choisir-enseignant').removeClass('d-none');
                }
            }, 'json');
        });

        // ----------------------------------------------------------------
        //
        // Etudiant > Choisir un enseignant ou une enseignante
        //
        // ----------------------------------------------------------------

        $('#bienvenue').delegate('#choisir-enseignant select', 'change', function(e)
        {
            $('#choisir-evaluation').addClass('d-none');
            $('#aller-evaluation').addClass('d-none');
            $('#aller-evaluation-temps-limite').addClass('d-none');

            $('#choisir-evaluation-select').empty();

            var semestre_id   = $('#semestre-data').data('semestre_id');
            var cours_id      = $('#choisir-cours-select').val(); 
            var enseignant_id = $('#choisir-enseignant-select').val(); 

            if (enseignant_id == 0 || enseignant_id == '0' || enseignant_id == null)
            {
                window.location = current_url;
                return false;
            }

            $('#choisir-enseignant-spinner').removeClass('d-none');

            $.post(base_url + 'bienvenue/choisir_enseignant', { ci_csrf_token: cct, semestre_id: semestre_id, cours_id: cours_id, enseignant_id: enseignant_id },
            function(data)
            {
                if (data != true) 
                {
                    $('#choisir-enseignant-spinner').addClass('d-none');

                    $html = '<option value="0"></option>';
                    $('#choisir-evaluation-select').append($html);

                    $.each(data, function(index, arr) 
                    {
                        var en_redaction = 0;
                        var temps_limite = 0;

                        if ('en_redaction' in arr) {
                            en_redaction = arr.en_redaction;
                        }

                        if ('temps_limite' in arr) {
                            temps_limite = arr.temps_limite;
                        }

                        $html = '<option value="' + arr.evaluation_reference + '" data-temps_limite="' + temps_limite + '" data-en_redaction="' + en_redaction + '">' + arr.evaluation_titre + '</option>';
                        // $html = '<option value="' + arr.evaluation_reference + '">' + arr.evaluation_titre + '</option>';

                        $('#choisir-evaluation-select').append($html);
                    });
                   
                    $('#choisir-evaluation').removeClass('d-none');
                }
            }, 'json');
        });

        // ----------------------------------------------------------------
        //
        // Etudiant > Choisir une evaluation
        //
        // ----------------------------------------------------------------

        $('#bienvenue').delegate('#choisir-evaluation select', 'change', function(e)
        {
            $('#aller-evaluation').addClass('d-none');
            $('#aller-evaluation-temps-limite').addClass('d-none');

            var evaluation_reference = $('#choisir-evaluation-select').val(); 

            if (evaluation_reference == null || evaluation_reference.length != 6)
            {
                window.location = current_url;
                return false;
            }

            var temps_limite = $(this).find(':selected').data('temps_limite');
            var en_redaction = $(this).find(':selected').data('en_redaction');

            /*
             * Ceci sert a avoir une fenetre popup avant de debuter une evaluation qui comporte un temps limite.
             *
            if (temps_limite != null && temps_limite > 0 && en_redaction < 1)
            {
                console.log('limite trouvee');
                $('#aller-evaluation-temps-limite').data('evaluation_reference', evaluation_reference);
                $('#aller-evaluation-temps-limite').removeClass('d-none');
            }
            else
            {
                console.log('la');
                $('#aller-evaluation').data('evaluation_reference', evaluation_reference);
                $('#aller-evaluation').removeClass('d-none');
            }
            */

            $('#aller-evaluation').data('evaluation_reference', evaluation_reference);
            $('#aller-evaluation').removeClass('d-none');
       });

        // ----------------------------------------------------------------
        //
        // Etudiant > Aller a l'evaluation
        //
        // ----------------------------------------------------------------

        $('#bienvenue').delegate('#aller-evaluation', 'click', function(e)
        {
            e.preventDefault();

            var evaluation_reference = $(this).data('evaluation_reference');

            // var evaluation_reference = $('#choisir-evaluation-select').val(); 

            if (evaluation_reference == null || evaluation_reference.length != 6)
            {
                window.location = current_url;
                return false;
            }

            window.location = base_url + 'evaluation/' + evaluation_reference;
            return true;
        }); 

        // ----------------------------------------------------------------
        //
        // Etudiant > Effacer une evaluation en redaction
        //
        // ----------------------------------------------------------------

        if ($('.effacer-traces-redaction').length)
        {
            $('.effacer-traces-redaction').click(function(e)
            {
                e.preventDefault();

                var $sel = $(this);
                var evaluation_reference = $sel.data('evaluation_reference');

                if (evaluation_reference.length != 6)
                    return false;

                $.post(base_url + 'bienvenue/effacer_traces_redaction', { ci_csrf_token: cct, evaluation_reference: evaluation_reference },
                function(data)
                {
                    if (data == false) 
                    {
                        return false;
                    }

                    document.location.reload(true);
                    return true;
                        
                }, 'json');

            });
        }

    } // avec sous domaine : etudiants

});
