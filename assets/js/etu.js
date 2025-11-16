/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ============================================================================
 *
 * KOVAO.com > etu.js
 *
 * ============================================================================ */

$(document).ready(function()
{
 	/* ----------------------------------------------------------------
     *
     * Resultats
     *
     * ---------------------------------------------------------------- */

    if ($('#resultats').length)
    {
        /* ----------------------------------------------------------------
         *
         * Ponderation
         *
         * ---------------------------------------------------------------- */

        $('#modal-ajuster-ponderation').on('show.bs.modal', function (e) 
        {
            var modal  = $(this);
            var button = $(e.relatedTarget); // le button qui a amorce le modal

            var evaluation_id = button.data('evaluation_id'); 
            var semestre_id   = button.data('semestre_id');
            var ponderation   = button.data('ponderation');
            var evaluation_titre = button.data('evaluation_titre');

            modal.find('input[name="evaluation_id"]').val(evaluation_id);
            modal.find('input[name="semestre_id"]').val(semestre_id);
            modal.find('input[name="ponderation"]').val(ponderation);
            $('#modal-ajuster-ponderation-evaluation-titre').html(evaluation_titre);

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

            $.post(base_url + 'etu/resultats_ajuster_ponderation', { ci_csrf_token: cct, evaluation_id: evaluation_id, semestre_id: semestre_id, ponderation: ponderation },
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

            $.post(base_url + 'etu/resultats_effacer_ponderation', { ci_csrf_token: cct, evaluation_id: evaluation_id, semestre_id: semestre_id },
            function(data)
            {
                $modal.find('.spinner').addClass('d-none');

                document.location.reload(true);
                return true;

            }, 'json');
        });

        /* ------------------------------------------------------------------------
         *
         * Ajouter d'une ancienne soumission dans les resultats de l'etudiant
         *
         * ------------------------------------------------------------------------ */

        if ($('#etudiants-resultats-ajouter-soumission-form').length)
        {
            var baseid = '#etudiants-resultats-ajouter-soumission';

            $(baseid).click(function(e)
            {
                e.preventDefault();

                // Cacher les erreurs
                $(baseid + '-erreur0').addClass('d-none');
                $(baseid + '-erreur1').addClass('d-none');
                $(baseid + '-erreur2').addClass('d-none');
                $(baseid + '-erreur3').addClass('d-none');
                $(baseid + '-erreur4').addClass('d-none');

                var reference = $(baseid + '-reference').val();
                var empreinte = $(baseid + '-empreinte').val();

                $.post(base_url + 'etu/ajouter_soumission', 
                        { ci_csrf_token: cct, reference: reference, empreinte: empreinte },

                function(data)
                {
                    if (data === true)
                    {
                        document.location.reload(true);
                        return false;
                    }

                    if (data === false)
                    {
                        $(baseid + '-erreur' + '0').removeClass('d-none'); 
                        return false;
                    }

                    // Erreurs :
                    // 1 - Une combinaison inexistante
                    // 2 - Une soumission deja ajoutee dans le compte d'un etudiant
                    // 3 - La reference ou l'empreinte est manquante.
                    // 4 - La soumission n'appartient pas a ce groupe.

                    $(baseid + '-erreur' + data).removeClass('d-none'); 
                    return false;

                }, 'json');
            });
        }

    } // #resultats

});
