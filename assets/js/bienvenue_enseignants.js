/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ============================================================================
 *
 * KOVAO.com > bienvenue_enseignants.js (Bienvenue aux enseignants)
 *
 * ============================================================================ */

$(document).ready(function()
{   
    // ------------------------------------------------------------------------
    //
    // AVEC SOUS DOMAINE : ENSEIGNANTS
    //
    // ------------------------------------------------------------------------

    if ($('#bienvenue-enseignants').length)
    {
        // ----------------------------------------------------------------
        //
        // Enseignants > Soumissions | Corrections
        //
        // ----------------------------------------------------------------

        //
        // Les etudiants en redaction
        //

        $('#etudiants-evaluations-ouvrir').click(function()
        {
            $('#etudiants-evaluations-ferme').addClass('d-none');
            $('#etudiants-evaluations-ouvert').removeClass('d-none');
        });

        $('#etudiants-evaluations-fermer').click(function()
        {
            $('#etudiants-evaluations-ferme').removeClass('d-none');
            $('#etudiants-evaluations-ouvert').addClass('d-none');
        });

        //
        // Les dernieres soumissions
        //

        $('#dernieres-soumissions-ouvrir').click(function()
        {
            $('#dernieres-soumissions-ferme').addClass('d-none');
            $('#dernieres-soumissions-ouvert').removeClass('d-none');
        });

        $('#dernieres-soumissions-fermer').click(function()
        {
            $('#dernieres-soumissions-ferme').removeClass('d-none');
            $('#dernieres-soumissions-ouvert').addClass('d-none');
        });

        //
        // Les dernieres corrections consultees
        //

        $('#corrections-consultees-ouvrir').click(function()
        {
            $('#corrections-consultees-ferme').addClass('d-none');
            $('#corrections-consultees-ouvert').removeClass('d-none');
        });

        $('#corrections-consultees-fermer').click(function()
        {
            $('#corrections-consultees-ferme').removeClass('d-none');
            $('#corrections-consultees-ouvert').addClass('d-none');
        });

        // ----------------------------------------------------------------
        //
        // Enseignants > Communiquer 
        //
        // ----------------------------------------------------------------

        $('#modal-communiquer-evaluation, #modal-communiquer-evaluation-etudiant').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal

            var modal = $(this);

            modal.find('.erreur-message').addClass('d-none');

            modal.find('input[name="etudiant_id"]').val('');
            modal.find('.etudiant_nom').html('');
                
            if (button.data('etudiant_id'))
            {
                modal.find('input[name="etudiant_id"]').val(button.data('etudiant_id'));
            }

            if (button.data('etudiant_nom'))
            {
                modal.find('.etudiant_nom').html(button.data('etudiant_nom'));
            }

            modal.find('input[name="evaluation_reference"]').val(button.data('evaluation_reference'));
            modal.find('input[name="evaluation_id"]').val(button.data('evaluation_id'));
            modal.find('span.evaluation-reference').html(button.data('evaluation_reference'));
        });

        $('#modal-communiquer-evaluation-sauvegarde, #modal-communiquer-evaluation-etudiant-sauvegarde').click(function(e)
        {
            e.preventDefault();

            var $modal      = $(this).parents('.modal');
            var $modal_form = $modal.find('form'); 

            $modal_form.find('.erreur-message').addClass('d-none');

            var evaluation_reference = $modal_form.find('input[name="evaluation_reference"]').val();
            var evaluation_id        = $modal_form.find('input[name="evaluation_id"]').val();
            var message              = $modal_form.find('textarea[name="message"]').val(); 
            var etudiant_id          = $modal_form.find('input[name="etudiant_id"]').val();

            if (message.length < 1)
            {
                $modal.modal('hide');
                $modal.find('.spinner').addClass('d-none');

                return false; 
            }

            $.post(base_url + 'bienvenue/communiquer', 
                    { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, message: message, etudiant_id: etudiant_id },
            function(data)
            {   
                if (typeof data === 'object' && 'message' in data)
                {
                    $modal_form.find('.erreur-message span').html(data['message']);
                    $modal_form.find('.erreur-message').removeClass('d-none');
                    $modal.find('.spinner').addClass('d-none');

                    return false;
                }

                document.location.reload(true);
                return false;

            }, 'json');
        });

        // ----------------------------------------------------------------
        //
        // Enseignants > Filtres
        //
        // ----------------------------------------------------------------

        $('#modal-filtres-evaluation').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal  = $(this);

            var $fe = modal.find('.filtre-etudiants');

            var href_complete = $fe.data('href_pre') + button.data('evaluation_reference');
           
            $fe.attr('href', href_complete); 
        });

        // ----------------------------------------------------------------
        //
        // Enseignants > Terminer une evaluation 
        // Enseignants > Terminer l'evaluation d'un etudiant
        //
        // ----------------------------------------------------------------

        $('#modal-terminer-evaluation, #modal-terminer-evaluation-etudiant').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);

            var modal = $(this);

            modal.find('input[name="evaluation_reference"]').val(button.data('evaluation_reference'));
            modal.find('span.evaluation-reference').html(button.data('evaluation_reference'));

            if (button.data('etudiant_id'))
            {
                modal.find('input[name="etudiant_id"]').val(button.data('etudiant_id'));
            }

            if (button.data('etudiant_nom'))
            {
                modal.find('span.etudiant_nom').html(button.data('etudiant_nom'));
            }
        });

        $('#terminer-evaluation-execution, #terminer-evaluation-etudiant-execution').click(function(e)
        {
            e.preventDefault();

            var $modal = $(this).parents('.modal');
            var $modal_form = $modal.find('form');

            var evaluation_reference = $modal_form.find('input[name="evaluation_reference"]').val();
            var etudiant_id          = $modal_form.find('input[name="etudiant_id"]').val();

            var enregistrer = 1;

            if ($modal_form.find('input[name="enregistrer_evaluation"]').length)
            { 
                if ( ! $modal_form.find('input[name="enregistrer_evaluation"][value="1"]').is(':checked'))
                {
                    enregistrer = 0;
                }
            }

        	$.post(base_url + 'evaluations/mettre_hors_ligne', 
                    { ci_csrf_token: cct, evaluation_reference: evaluation_reference, etudiant_id: etudiant_id, enregistrer: enregistrer },
        	function()
        	{   
                document.location.reload(true);
                return;
            });
        });

        /* ----------------------------------------------------------------
         *
         * Enseignant > Remettre a zero le nombre de vues
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

        /* ----------------------------------------------------------------
         *
         * Enseignant > (modal) Parametres
         *
         * ---------------------------------------------------------------- */

        $('#modal-parametres-evaluation').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);

            var inscription_requise  = button.data('inscription_requise');
            var cacher               = button.data('cacher');
            var bloquer              = button.data('bloquer');
            var evaluation_id        = button.data('evaluation_id');
            var evaluation_reference = button.data('evaluation_reference');
            
            modal.find('input[name="evaluation_id"]').val(evaluation_id);
            modal.find('input[name="evaluation_reference"]').val(evaluation_reference);

            if (inscription_requise)
            {
                modal.find('input[name="inscription_requise"]').prop('checked', true);
            }
            else
            {
                modal.find('input[name="inscription_requise"]').prop('checked', false);
            }

            if (cacher)
            {
                modal.find('input[name="cacher"]').prop('checked', true);
            }
            else
            {
                modal.find('input[name="cacher"]').prop('checked', false);
            }

            if (bloquer)
            {
                modal.find('input[name="bloquer"]').prop('checked', true);
            }
            else
            {
                modal.find('input[name="bloquer"]').prop('checked', false);
            }
        });

        $('.modal').delegate('#modal-parametres-evaluation-sauvegarde', 'click', function(e)
        {
            e.preventDefault();
        
            var $sel = $(this);
            var $form = $('#modal-parametres-evaluation-form');

            $.post(base_url + 'bienvenue/parametres_evaluation', $form.serialize(),
            function(data)
            {
                document.location.reload(true);
                return false;

            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Enseignant > (modal) Filtres 
         *
         * ---------------------------------------------------------------- */

        $('#modal-filtres-evaluation').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var params          = button.data('params');
            var cours_id        = button.data('cours_id');
            var cours_groupes   = button.data('cours_groupes');
            var evaluation_id   = button.data('evaluation_id');
            var evaluation_reference = button.data('evaluation_reference');

            modal.find('input[name="cours_id"]').val(cours_id);
            modal.find('input[name="evaluation_id"]').val(evaluation_id);
            modal.find('input[name="evaluation_reference"]').val(evaluation_reference);

            modal.find('#modal-cours-groupes').html('');
            modal.find('#modal-cours-groupes-autorisation').html('');

            if (Array.isArray(cours_groupes) && cours_groupes.length > 1)
            {
                $.each(cours_groupes, function(index, groupe)
                {
                    //
                    // Template SANS AUTORISATION
                    //

                    // modify template (in a var first)
                    var template = $('#cours-groupe-template').html();

                    // https://stackoverflow.com/questions/22143055/replacing-manipulating-element-in-html-string-using-jquery
                    var $template  = $('<div />', {html:template});
                    $template.find('.modal-cours-groupe').html(groupe);
                    $template.find('input').val('groupe_' + groupe);

                    var template_copy = $template.html();
                    $('#modal-cours-groupes').append(template_copy);

                    //
                    // Template AVEC AUTORISATION
                    //

                    var template = $('#cours-groupe-autorisation-template').html();

                    var $template  = $('<div />', {html:template});
                    $template.find('.modal-cours-groupe-autorisation').html(groupe);
                    $template.find('input').val('groupe_autorisation_' + groupe);

                    var template_copy = $template.html();
                    $('#modal-cours-groupes-autorisation').append(template_copy);
                });
            }

            // Definir la valeur actuelle ou par defaut

            if (params != false)
            {
                modal.find('input[name="filtre"][value="' + params + '"]').prop('checked', true);
            }

        });

        $('.modal').delegate('#modal-filtres-evaluation-sauvegarde', 'click', function(e)
        {
            e.preventDefault();
        
            var $sel = $(this);
            var $form = $('#modal-filtres-evaluation-form');

            if ($form.find('input[name="filtre"]').is(':checked'))
            {
                $.post(base_url + 'bienvenue/filtres_evaluation', $form.serialize(),
                function(data)
                {
                    document.location.reload(true);
                    return;

                }, 'json');
            }
            else 
            {
                // Aucune selection
                $sel.find('i.spinner').addClass('d-none');
            }

        });

        $('.modal').delegate('#modal-effacer-filtres-sauvegarde', 'click', function(e)
        {
            e.preventDefault();
        
            var $form = $('#modal-filtres-evaluation-form');

            $.post(base_url + 'bienvenue/effacer_filtres_evaluation', $form.serialize(),
            function(data)
            {
                document.location.reload(true);
                return;

            }, 'json');

        });

        /* ----------------------------------------------------------------
         *
         * Enseignant > (modal) Planifier une evaluation
         *
         * ---------------------------------------------------------------- */

        $('#modal-planifier-evaluation').on('show.bs.modal', function (e) 
        {
            var button = $(e.relatedTarget); // le button qui a amorce le modal
            var modal = $(this);

            var debut_date = button.data('debut_date');
            var debut_heure = button.data('debut_heure');
            var fin_date = button.data('fin_date');
            var fin_heure = button.data('fin_heure');
            var temps_limite = button.data('temps_limite');
            var evaluation_id = button.data('evaluation_id');
            var evaluation_reference = button.data('evaluation_reference');
            var cacher = button.data('cachee');

            modal.find('input[name="debut_date"]').val(debut_date);
            modal.find('input[name="debut_heure"]').val(debut_heure);
            modal.find('input[name="fin_date"]').val(fin_date);
            modal.find('input[name="fin_heure"]').val(fin_heure);
            modal.find('input[name="temps_limite"]').val(temps_limite);
            modal.find('input[name="evaluation_id"]').val(evaluation_id);
            modal.find('input[name="evaluation_reference"]').val(evaluation_reference);

            if ( ! (debut_date == '' && debut_heure == '') ||  ! (fin_date == '' && fin_heure == ''))
            {
                $('#modal-effacer-planification-sauvegarde').removeClass('d-none');
            }
            else
            {
                $('#modal-effacer-planification-sauvegarde').addClass('d-none');
            }

            $('#planifier-evaluation-cachee').addClass('d-none');

            if (cacher)
            {
                $('#planifier-evaluation-cachee').removeClass('d-none');
            }

            // Effacer les erreurs precedentes

            $('#modal-planifier-evaluation-form :input').each(function(index) 
            {
                $(this).removeClass('is-invalid');
            });

            modal.find('.planifier-evaluation-erreur').addClass('d-none');
        });

        $('.modal').delegate('#modal-planifier-evaluation-sauvegarde', 'click', function(e)
        {
            e.preventDefault();
        
            var $modal = $(this).parents('.modal');
            var $form  = $('#modal-planifier-evaluation-form');

            // Effacer les erreurs precedentes

            $('#modal-planifier-evaluation-form :input').each(function(index) 
            {
                $(this).removeClass('is-invalid');
            });

            $modal.find('.planifier-evaluation-erreur').addClass('d-none');

            $.post(base_url + 'bienvenue/planifier_evaluation', $form.serialize(),
            function(data)
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }
                else if (data == false)
                {
                    $modal.find('.spinner').addClass('d-none');
                    $modal.modal('hide');

                    return;
                }
                else if ($.isPlainObject(data)) 
                {
                    $modal.find('.spinner').addClass('d-none');

                    for (var property in data) 
                    {
                        if (data.hasOwnProperty(property)) 
                        {
                            var $field = $('#modal-planifier-evaluation-' + property.replace(/_/g, '-'));

                            $field.addClass('is-invalid');
                        }
                    }

                    if ('erreur' in data)
                    {
                        $('#planifier-evaluation-erreur').removeClass('d-none').html(data['erreur']);
                    }

                    if ('erreur-debut' in data)
                    {
                        $('#planifier-evaluation-erreur-debut').removeClass('d-none');
                    }

                    if ('erreur-debut-passe' in data)
                    {
                        $('#planifier-evaluation-erreur-debut-passe').removeClass('d-none');
                    }

                    if ('erreur-fin' in data)
                    {
                        $('#planifier-evaluation-erreur-fin').removeClass('d-none');
                    }
                }

            }, 'json');

        });

        $('.modal').delegate('#modal-effacer-planification-sauvegarde', 'click', function(e)
        {
            e.preventDefault();
        
            var $form = $('#modal-planifier-evaluation-form');

            $.post(base_url + 'bienvenue/effacer_planification_evaluation', $form.serialize(),
            function(data)
            {
                document.location.reload(true);
                return;

            }, 'json');

        });

    } // avec sous domaine : enseignants

});
