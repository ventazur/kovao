/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > documents.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ------------------------------------------------------------------------
	 *
     * Configuration
     *
     * ------------------------------------------------------------------------ */
    if ($('#configuration').length)
    {
        /* ------------------------------------------------------------------------
         *
         * Afficher le nom du fichier de la liste d'eleves
         *
         * ------------------------------------------------------------------------ */
        $('#modal-ajouter-liste-eleves-fichier').change(function()
        {
            var $sel = $(this);
            var fichier = this.files[0];
            var nom_du_fichier = fichier.name;

            $('#liste-eleves-nom-fichier').empty().html(nom_du_fichier);

            $('#modal-ajouter-liste-eleves-fichier').parent('label').removeClass('btn-outline-danger').addClass('btn-outline-primary');
        });

        /* ------------------------------------------------------------------------
         *
         * Envoyer la liste d'eleves
         *
         * ------------------------------------------------------------------------ */
        $('#modal-ajouter-liste-eleves-sauvegarde').click(function(e)
        {
            e.preventDefault();

            var nom_du_fichier = $('#liste-eleves-nom-fichier').html();

            if (nom_du_fichier == '') {
                // Le fichier n'a pas ete selectionne.
                $('#modal-ajouter-liste-eleves-fichier').parent('label').removeClass('btn-outline-primary').addClass('btn-outline-danger');
                $(this).find('.spinner').addClass('d-none');
                return false;
            }

            var fichier = document.getElementById('modal-ajouter-liste-eleves-fichier').files[0];
            var semestre_id = $('#semestre-data').data('semestre_id');

            var cours_id = $('#modal-ajouter-liste-eleves-cours-id').val();
            var numero_groupe = $('#modal-ajouter-liste-eleves-numero-groupe').val();
            var plateforme = $('#modal-ajouter-liste-eleves-plateforme').val();

            if (numero_groupe == '' || numero_groupe < 1)
            {
                // Le numero du groupe est vide.
                $('#modal-ajouter-liste-eleves-numero-groupe').addClass('is-invalid');
                $(this).find('.spinner').addClass('d-none');
                return false;
            }

            var params = {
                'cours_id' : cours_id,
                'numero_groupe' : numero_groupe,
                'plateforme' : plateforme
            };

            uploadFile(fichier, $('#modal-ajouter-liste-eleves-fichier'), 'liste', semestre_id, params);
        });

        /* ------------------------------------------------------------------------
         *
         * Envoyer une liste d'etudiants
         *
         * ------------------------------------------------------------------------ */
        $('.ajout-liste-etudiants-input').change(function() 
        {
            var $sel = $(this);
            var file = this.files[0];

            var semestre_id = $('#semestre-data').data('semestre_id');

            // Montrer le spinner d'attente
            $sel.parents('.ajout-liste-etudiants').find('.image-upload-spinner').removeClass('d-none');

            uploadFile(file, $sel, 'liste', semestre_id);
        });

    } // #configuration
	
    /* ------------------------------------------------------------------------
	 *
     * Envoyer une image pour y etre associee a une question.
     *
     * ------------------------------------------------------------------------ */
    $('.ajout-image-input').change(function() 
	{
		var $sel = $(this);
		var file = this.files[0];

		var question_id = $sel.parents('.ajout-image').data('question_id');

		//
		// activer le spinner d'attente
		//
		$sel.parents('.ajout-image').find('.image-upload-spinner').removeClass('d-none');

        uploadFile(file, $sel, 'question', question_id);
    });

    /* ------------------------------------------------------------------------
	 *
     * Routine d'envoie d'un fichier 
     *
     * ------------------------------------------------------------------------ */
    function uploadFile(file, $sel, category, id, params = [])
    {
        var data = new FormData();
		
        data.append('upload_file', file);

        data.append('ci_csrf_token', cct);
		data.append('category', category);
		data.append('id', id);

        if ('plateforme' in params) 
        {
            data.append('cours_id', params.cours_id);
            data.append('numero_groupe', params.numero_groupe);
            data.append('plateforme', params.plateforme);
        }

        $.ajax({
            url: base_url + 'documents/upload',
            method: 'POST',
            data: data,
            success: function(file_data) {
                // var file_data = $.parseJSON(file_data);
                // return false;
				document.location.reload(true);
				return true;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // console.log(xhr.status);
                // console.log(thrownError);
            },

            // options to tell JQuery not to process data or worry about content-type
            cache: false,
            contentType: false,
            processData: false
        });
    }

});
