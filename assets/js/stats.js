/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > stats.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Ajuster les points
     *
     * ---------------------------------------------------------------- */

    $('#modal-corrections-changer-points').on('show.bs.modal', function(e) 
    {
        var button = $(e.relatedTarget); 
        var modal  = $(this);

        var soumission_id        = button.data('soumission_id');
        var soumission_reference = button.data('soumission_reference');
        var question_id          = button.data('question_id');
        var points_obtenus       = button.data('points_obtenus');
        var question_points      = button.data('question_points');
        var ajustement           = button.data('ajustement');

        modal.find('input[name="soumission_id"]').val(soumission_id);
        modal.find('input[name="soumission_reference"]').val(soumission_reference);
        modal.find('input[name="question_id"]').val(question_id);
        modal.find('input[name="points_obtenus"]').val(points_obtenus);
        modal.find('input[name="points"]').val(question_points);

        $('#modal-corrections-changer-points-question-id').html(question_id);
        $('#modal-corrections-changer-points-obtenus').val(points_obtenus);
        $('#modal-corrections-changer-points-total').html('/ ' + question_points);

        if (ajustement == 1)
        {
            $('#modal-corrections-effacer-ajustement-sauvegarde').removeClass('d-none');
            $('#modal-corrections-effacer-ajustement-sauvegarde').data('soumission_id', soumission_id);
            $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', question_id);
        }
        else
        {
            $('#modal-corrections-effacer-ajustement-sauvegarde').addClass('d-none');
            $('#modal-corrections-effacer-ajustement-sauvegarde').data('soumission_id', '');
            $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', '');
        }

        // Nettoyer les messages d'erreur
        $('.spinnable').find('.spinner').addClass('d-none');
        $('#modal-corrections-changer-points-obtenus').removeClass('is-invalid');
        $('#modal-corrections-changer-points-obtenus-invalide').addClass('d-none');
    });

    /* ----------------------------------------------------------------
     *
     * Ajuster (changer) les points d'une question
     *
     * ---------------------------------------------------------------- */

    $('#modal-corrections-changer-points-sauvegarde').click(function(e)
    {
        var $modal = $('#modal-corrections-changer-points');        

        var points_obtenus         = $modal.find('input[name="points_obtenus"]').val();
        var nouveau_points_obtenus = $modal.find('input[name="nouveau_points_obtenus"]').val();
        var question_points        = $modal.find('input[name="question_points"]').val();

        $.post(base_url + 'consulter/changer_points', $('#modal-corrections-changer-points-form').serialize(),
        function(data)
        {
            if (data == true) 
            {
                document.location.reload(true);
                return true;
            }
            else
            {
                $('#modal-corrections-changer-points').modal('toggle');
            }
        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Effacer l'ajustement des points d'une questions
     *
     * ---------------------------------------------------------------- */

    $('#modal-corrections-effacer-ajustement-sauvegarde').click(function(e)
    {
        var soumission_id = $(this).data('soumission_id');
        var question_id   = $(this).data('question_id');

        if (question_id == '')
        {
            $('#modal-corrections-changer-points').modal('toggle');
        }

        $.post(base_url + 'consulter/effacer_ajustement', { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id },
        function(data)
        {
            if (data == true) 
            {
                document.location.reload(true);
                return true;
            }
            else
            {
                $('#modal-corrections-changer-points').modal('toggle');
            }

        }, 'json');

    });

    /* ----------------------------------------------------------------
     *
     * Defilement des questions
     *
     * ---------------------------------------------------------------- */

    $('#defilement-questions').click(function(e)
    {
        var $sel = $(this);

        //
        // Extraire la liste de toutes les question_ids selon l'ordre actuel
        //

        // Anciennement :
        // var question_ids = $sel.data('question_ids');

        var question_ids = []; 

        $('table.questions').find('tr').each(function() 
        {
            var q_id = $(this).data('question_id');

            if (typeof q_id != 'undefined')
            {
                question_ids.push(q_id);
            }
        });

       	$.post(base_url + 'stats/defilement_questions', { ci_csrf_token: cct, question_ids: question_ids },
        function(data)
        {
            if (data != false)
            {
                window.location = data;
                return;
            }

        }, 'json');
    });

    /* ----------------------------------------------------------------
     *
     * Changer l'ordre de defilement des questions
     *
     * ---------------------------------------------------------------- */
    if ($('#statistiques-evaluation').length)
    {
        $('.tri-button').click(function(e)
        {
            var ordre_question_ids = [];

            $('tr.question').each(function(i, elem)
            {
                ordre_question_ids.push(elem.dataset.question_id);
            });

            var ordre_question_ids_json = JSON.stringify(ordre_question_ids);

            $('td.question-lien input.ordre-questions').each(function(i, elem)
            {
                elem.value = ordre_question_ids_json;
            }); 


            /*
            var $tri = $(this);

            $('td.question-lien > .stats-question').each(function(i, elem)
            {
                var url = elem.dataset.url;

                if ($tri.data('clef') == 'clef_tri_apparitions')
                {
                    elem.href = url + '/ordre/apparition';
                }

                if ($tri.data('clef') == 'clef_tri_question_ids')
                {
                    elem.href = url + '/ordre/question';
                }
                
                if ($tri.data('clef') == 'clef_tri_points')
                {
                    elem.href = url + '/ordre/points';
                }
            });
            */
        });
    }

});
