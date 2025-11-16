/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > consulter.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Consultation
     *
     * ---------------------------------------------------------------- */

    if ($('#consulter-voir').length)
    {
        /* ----------------------------------------------------------------
         *
         * Ajuster les points de la soumission
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-changer-points-soumission-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-corrections-changer-points-soumission');        

            var points_obtenus         = $modal.find('input[name="points_obtenus"]').val();
            var nouveau_points_obtenus = $modal.find('input[name="nouveau_points_obtenus"]').val();
            var points                 = $modal.find('input[name="question_points"]').val();

            nouveau_points_obtenus = parseFloat(nouveau_points_obtenus);
            points = parseFloat(points);

            if (nouveau_points_obtenus > points)
            {
                $('#modal-corrections-changer-points-obtenus-soumission').addClass('is-invalid');
                $('#modal-corrections-changer-points-obtenus-soumission-invalide').removeClass('d-none');
                $('.spinnable').find('.spinner').addClass('d-none');

                return true;
            }

            if (nouveau_points_obtenus == points_obtenus)
            {
                $('#modal-corrections-changer-points-soumission').modal('toggle');
                $('.spinnable').find('.spinner').addClass('d-none');
                return true;
            }

        	$.post(base_url + 'consulter/changer_points_soumission', $('#modal-corrections-changer-points-soumission-form').serialize(),
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
         * Effacer l'ajustement des points d'une soumission
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-effacer-ajustement-soumission-sauvegarde').click(function(e)
        {
            var soumission_id = $('#soumission-data').data('soumission_id');

            $.post(base_url + 'consulter/effacer_ajustement_soumission', { ci_csrf_token: cct, soumission_id: soumission_id },
            function(data)
            {
                document.location.reload(true);
                return true;

            }, 'json');

        });

        /* ----------------------------------------------------------------
         *
         * Ajuster les points d'une question
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-changer-points').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); 
            var modal  = $(this);

            var question_id     = button.data('question_id');
            var points_obtenus  = button.data('points_obtenus');
            var points          = button.data('question_points');
            var ajustement      = button.data('ajustement');

            points = parseFloat(points);
            points_obtenus = parseFloat(points_obtenus);

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('input[name="points_obtenus"]').val(points_obtenus);
            modal.find('input[name="points"]').val(points);

            $('#modal-corrections-changer-points-question-id').html(question_id);
            $('#modal-corrections-changer-points-obtenus').val(points_obtenus);
            $('#modal-corrections-changer-points-total').html('/ ' + points);

            if (ajustement == 1)
            {
                $('#modal-corrections-effacer-ajustement-sauvegarde').removeClass('d-none');
                $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', question_id);
            }
            else
            {
                $('#modal-corrections-effacer-ajustement-sauvegarde').addClass('d-none');
                $('#modal-corrections-effacer-ajustement-sauvegarde').data('question_id', '');
            }

            // Nettoyer les messages d'erreur
            $('.spinnable').find('.spinner').addClass('d-none');
            $('#modal-corrections-changer-points-obtenus').removeClass('is-invalid');
            $('#modal-corrections-changer-points-obtenus-invalide').addClass('d-none');
        });

        /* ----------------------------------------------------------------
         *
         * Ajuster les points d'une question
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-changer-points-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-corrections-changer-points');        

            var points_obtenus         = $modal.find('input[name="points_obtenus"]').val();
            var nouveau_points_obtenus = $modal.find('input[name="nouveau_points_obtenus"]').val();
            var points                 = $modal.find('input[name="question_points"]').val();

            nouveau_points_obtenus = parseFloat(nouveau_points_obtenus);
            points = parseFloat(points);

            if (nouveau_points_obtenus > points)
            {
                $('#modal-corrections-changer-points-obtenus').addClass('is-invalid');
                $('#modal-corrections-changer-points-obtenus-invalide').removeClass('d-none');
                $('.spinnable').find('.spinner').addClass('d-none');

                return true;
            }

            /*
            if (nouveau_points_obtenus == points_obtenus)
            {
                $('#modal-corrections-changer-points').modal('toggle');
                return true;
            }
            */

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
            var soumission_id = $('#soumission-data').data('soumission_id');
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

        // ----------------------------------------------------------------
        //
        // Rotation d'une image
        // 
        // ----------------------------------------------------------------
        $('.files-list').on('click', '.file-rotation', function() 
        {
            var doc_id        = $(this).data('doc_id');
            var soumission_id = $('#soumission-data').data('soumission_id');
            var rotation      = $(this).data('rotation');
           
            var $row = $(this).parents('tr.file-data');

            $row.find('.file-operations').addClass('d-none');
            $row.find('.file-processing').removeClass('d-none');
            $row.find('.file-operations-group .file-processing-spinner').removeClass('d-none');

            $.post(base_url + 'consulter/rotation_image', 
                    { ci_csrf_token: cct, doc_id: doc_id, rotation: rotation, soumission_id: soumission_id },
            function(data) 
            {
                if (data != false) 
                {
                    var $image       = $('#file-' + doc_id).find('img.img-thumbnail');
                    var $image_lien  = $('#file-' + doc_id).find('a.img-original');

                    var img_tn_src = data['uri_tn'];
                    var img_src    = data['uri'];

                    $image.attr('src', img_tn_src + '?' + new Date().getTime());
                    $image_lien.attr('href', img_src + '?' + new Date().getTime());

                    $row.find('.file-operations').removeClass('d-none');
                    $row.find('.file-processing').addClass('d-none');
                    $row.find('.file-operations-group .file-processing-spinner').addClass('d-none');
                }
                else
                {
                    alert('Erreur BHKL6FH4. Un problème s\'est produit avec la rotation de l\'image.');

                    $row.find('.file-operations').removeClass('d-none');
                    $row.find('.file-processing').addClass('d-none');
                    $row.find('.file-operations-group .file-processing-spinner').addClass('d-none');
                }
            }, 'json');
        });

        if ($('.montrer-details').length)
        {
            $('.montrer-details').click(function()
            {
                $(this).next('.montrer-details-document').toggle();

            });
        }

        /* ----------------------------------------------------------------
         *
         * Laisser un commentaire a l'etudiant (pour une soumission)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-soumission').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);
    
            var commentaire = button.data('commentaire');

            modal.find('textarea[name="commentaire"]').val(commentaire);
        });

        /* ----------------------------------------------------------------
         *
         * Sauvegarder le commentaire (pour une soumission)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-soumission-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-laisser-commentaire-soumission');
            var $form = $('#modal-laisser-commentaire-soumission-form');

            var soumission_id        = $form.find('input[name="soumission_id"]').val();
            var soumission_reference = $form.find('input[name="soumission_reference"]').val();
            var commentaire          = $form.find('textarea[name="commentaire"]').val();

            $.post(base_url + 'corrections/sauvegarder_commentaire_soumission', 
                    { ci_csrf_token: cct, soumission_id: soumission_id, commentaire: commentaire },
            function(data) 
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }
                
                $modal.modal('hide');
                
            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Effacer le commentaire laisse a l'etudiant (pour une soumission)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-soumission-effacer').click(function(e)
        {
            var $modal = $('#modal-laisser-commentaire-soumission');
            var $form = $('#modal-laisser-commentaire-soumission-form');

            var soumission_id        = $form.find('input[name="soumission_id"]').val();
            var soumission_reference = $form.find('input[name="soumission_reference"]').val();

            $.post(base_url + 'corrections/effacer_commentaire_soumission', 
                    { ci_csrf_token: cct, soumission_id: soumission_id },
            function(data) 
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }

                $modal.modal('hide');

            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Laisser un commentaire a l'etudiant (pour une question)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);
       
            var question_id = button.data('question_id');
            var commentaire = button.data('commentaire');

            modal.find('input[name="question_id"]').val(question_id);
            modal.find('textarea[name="commentaire"]').val(commentaire);
        });

        /* ----------------------------------------------------------------
         *
         * Sauvegarder le commentaire (pour une question)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-laisser-commentaire');
            var $form = $('#modal-laisser-commentaire-form');

            var question_id          = $form.find('input[name="question_id"]').val();
            var soumission_id        = $form.find('input[name="soumission_id"]').val();
            var soumission_reference = $form.find('input[name="soumission_reference"]').val();
            var commentaire          = $form.find('textarea[name="commentaire"]').val();

            $.post(base_url + 'corrections/sauvegarder_commentaire', 
                    { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id, commentaire: commentaire },
            function(data) 
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }
                
                $modal.modal('hide');
                
            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Effacer le commentaire laisse a l'etudiant
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-effacer').click(function(e)
        {
            var $modal = $('#modal-laisser-commentaire');
            var $form = $('#modal-laisser-commentaire-form');

            var question_id          = $form.find('input[name="question_id"]').val();
            var soumission_id        = $form.find('input[name="soumission_id"]').val();
            var soumission_reference = $form.find('input[name="soumission_reference"]').val();

            $.post(base_url + 'corrections/effacer_commentaire', 
                    { ci_csrf_token: cct, soumission_id: soumission_id, question_id: question_id },
            function(data) 
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }

                $modal.modal('hide');

            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Laboratoires
         *
         * ---------------------------------------------------------------- */

        /* ----------------------------------------------------------------
         *
         * Ajuster les points d'un tableau
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-changer-points-tableau').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget); 
            var modal  = $(this);

            var tableau_no      = button.data('tableau_no');
            var points_obtenus  = button.data('points_obtenus');
            var points          = button.data('points');
            var ajustement      = button.data('ajustement');

            modal.find('input[name="tableau_no"]').val(tableau_no);
            modal.find('input[name="points_obtenus"]').val(points_obtenus);
            modal.find('input[name="points"]').val(points);

            $('#modal-corrections-changer-points-tableau-no').html(tableau_no);
            $('#modal-corrections-changer-points-obtenus-tableau').val(points_obtenus);
            $('#modal-corrections-changer-points-total-tableau').html('/ ' + points);

            if (ajustement == 1)
            {
                $('#modal-corrections-effacer-ajustement-tableau-sauvegarde').removeClass('d-none');
                $('#modal-corrections-effacer-ajustement-tableau-sauvegarde').data('tableau_no', tableau_no);
            }
            else
            {
                $('#modal-corrections-effacer-ajustement-tableau-sauvegarde').addClass('d-none');
                $('#modal-corrections-effacer-ajustement-tableau-sauvegarde').data('tableau_no', '');
            }

            // Nettoyer les messages d'erreur
            $('.spinnable').find('.spinner').addClass('d-none');
            $('#modal-corrections-changer-points-obtenus').removeClass('is-invalid');
            $('#modal-corrections-changer-points-obtenus-invalide').addClass('d-none');
        });

        /* ----------------------------------------------------------------
         *
         * Ajuster les points d'un tableau (action)
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-changer-points-tableau-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-corrections-changer-points');        

            var points_obtenus         = $modal.find('input[name="points_obtenus"]').val();
            var nouveau_points_obtenus = $modal.find('input[name="nouveau_points_obtenus"]').val();
            var points                 = $modal.find('input[name="points"]').val();

            nouveau_points_obtenus = parseFloat(nouveau_points_obtenus);
            points = parseFloat(points);

            if (nouveau_points_obtenus > points)
            {
                $('#modal-corrections-changer-points-obtenus').addClass('is-invalid');
                $('#modal-corrections-changer-points-obtenus-invalide').removeClass('d-none');
                $('.spinnable').find('.spinner').addClass('d-none');

                return true;
            }

            /*
            if (nouveau_points_obtenus == points_obtenus)
            {
                $('#modal-corrections-changer-points-tableau').modal('toggle');
                return true;
            }
            */

        	$.post(base_url + 'consulter/changer_points', $('#modal-corrections-changer-points-tableau-form').serialize(),
        	function(data)
            {
           		if (data == true) 
				{
                    document.location.reload(true);
					return true;
            	}
                else
                {
                    $('#modal-corrections-changer-points-tableau').modal('toggle');
                }
            }, 'json');
        });


        /* ----------------------------------------------------------------
         *
         * Effacer l'ajustement des points d'un tableau
         *
         * ---------------------------------------------------------------- */

        $('#modal-corrections-effacer-ajustement-tableau-sauvegarde').click(function(e)
        {
            var soumission_id = $('#soumission-data').data('soumission_id');
            var tableau_no   = $(this).data('tableau_no');

            if (tableau_no == '')
            {
                $('#modal-corrections-changer-points').modal('toggle');
            }

            $.post(base_url + 'consulter/effacer_ajustement', { ci_csrf_token: cct, soumission_id: soumission_id, tableau_no: tableau_no },
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
         * Laisser un commentaire a l'etudiant (pour un tableau)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-tableau').on('show.bs.modal', function(e) 
        {
            var button = $(e.relatedTarget);
            var modal = $(this);
       
            var tableau_no = button.data('tableau_no');
            var commentaire = button.data('commentaire');

            modal.find('input[name="tableau_no"]').val(tableau_no);
            modal.find('textarea[name="commentaire"]').val(commentaire);
        });

        /* ----------------------------------------------------------------
         *
         * Sauvegarder le commentaire (pour un tableau)
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-tableau-sauvegarde').click(function(e)
        {
            var $modal = $('#modal-laisser-commentaire-tableau');
            var $form = $('#modal-laisser-commentaire-tableau-form');

            var tableau_no           = $form.find('input[name="tableau_no"]').val();
            var soumission_id        = $form.find('input[name="soumission_id"]').val();
            var soumission_reference = $form.find('input[name="soumission_reference"]').val();
            var commentaire          = $form.find('textarea[name="commentaire"]').val();

            $.post(base_url + 'corrections/sauvegarder_commentaire', 
                    { ci_csrf_token: cct, soumission_id: soumission_id, tableau_no: tableau_no, commentaire: commentaire },
            function(data) 
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }
                
                $modal.modal('hide');
                
            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Effacer le commentaire laisse a l'etudiant
         *
         * ---------------------------------------------------------------- */
        $('#modal-laisser-commentaire-tableau-effacer').click(function(e)
        {
            var $modal = $('#modal-laisser-commentaire');
            var $form = $('#modal-laisser-commentaire-tableau-form');

            var tableau_no           = $form.find('input[name="tableau_no"]').val();
            var soumission_id        = $form.find('input[name="soumission_id"]').val();
            // var soumission_reference = $form.find('input[name="soumission_reference"]').val();

            $.post(base_url + 'corrections/effacer_commentaire', 
                    { ci_csrf_token: cct, soumission_id: soumission_id, tableau_no: tableau_no },
            function(data) 
            {
                if (data == true)
                {
                    document.location.reload(true);
                    return;
                }

                $modal.modal('hide');

            }, 'json');
        });

        /* ----------------------------------------------------------------
         *
         * Recorriger les tableaux
         *
         * ---------------------------------------------------------------- */
        $('#recorrection').click(function(e)
        {
            var soumission_id = $('#consulter-voir').data('soumission_id');

            $.post(base_url + 'laboratoire/recorriger_tableaux', 
                    { ci_csrf_token: cct, soumission_id: soumission_id },
            function(data) 
            {




            }, 'json');

        });

    } // if #consulter-voir
});
