/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > evaluation.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    // ----------------------------------------------------------------
	//
	// Evaluation confirmation
	//
	// Confirmation de l'etudiant qu'il est pret a debuter 
	// une evaluation comportant un temps limite.
	//
    // ----------------------------------------------------------------
	
	if ($('#evaluation-temps-limite-confirmation'))
	{
		$('#letsgo').click(function(e)
		{
			$('#letsgo').find('i').removeClass('d-none');
		});
	}

    // ----------------------------------------------------------------
    //
	// Bloque la fonctionnalite du fureteur de revenir a la page precedente.
	// Ceci pour eviter que les etudiants non inscrits ne perdent leurs 
    // reponses par megarde.
    //
    // ----------------------------------------------------------------

	window.history.pushState(null, "", window.location.href);        
	window.onpopstate = function() {
		window.history.pushState(null, "", window.location.href);
	};

    // ----------------------------------------------------------------
    //
    // Force a recharger la page lorsque celle-ci est relue de 
    // l'historique du fureteur.
    //
    // ----------------------------------------------------------------

    if (performance.navigation.type == 2) 
    {
        window.location = base_url;
        return false;
    }

    // ----------------------------------------------------------------
    //
    // Geolocalisation (INACTIF)
    //
    // ----------------------------------------------------------------

    if ($('#confirmation3_q').length)
    {
        var lat = null;
        var lon = null;

        function getLocation() 
        {
            if (navigator.geolocation) 
            {
                navigator.geolocation.getCurrentPosition(showPosition);
            } 
            else 
            {
                // "Refus de geolocaliser"
                lat = 0;
                lon = 0;
            }
        }

        function showPosition(position) 
        {
            // console.log("Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude);
            lat = position.coords.latitude;
            lon = position.coords.longitude;
        }

        $('#confirmation3_q').click(function(e)
        {
            getLocation();
        });
    }

    // ----------------------------------------------------------------
    //
    // Mettre a jour la barre de navigation au chargement
    //
    // ----------------------------------------------------------------

    $('.question-data').each(function(i)
    {
        var question_no  = $(this).data('question_no');
        var couleur_fond = '#E3F2FD';

        $(this).find(':input').each(function(j)
        {
            if ($(this).hasClass('reponse-numerique') && $(this).val() != '')
            {
                $('#q' + question_no + 'box').css('background-color', couleur_fond);
                return false;
            }

            else if ($(this).hasClass('reponse-developpement-court') && $(this).val() != '')
            {
                $('#q' + question_no + 'box').css('background-color', couleur_fond);
                return false;
            }

            else if ($(this).hasClass('reponse-litterale-courte') && $(this).val() != '')
            {
                $('#q' + question_no + 'box').css('background-color', couleur_fond);
                return false;
            }

            else if ($(this).is('select'))
            {
                if ($(this).val())
                {
                    $('#q' + question_no + 'box').css('background-color', couleur_fond);
                    return false;
                }
            }

            else if ($(this).is(':checked'))
            {
                $('#q' + question_no + 'box').css('background-color', couleur_fond);
                return false;
            }

            else if ($(this).is('textarea'))  
            {
                if ($.trim($(this).val())) 
                {
                    $('#q' + question_no + 'box').css('background-color', couleur_fond);
                    return false;
                }
            }
        });
    });

    // ----------------------------------------------------------------
    //
    // Informations
    //
    // ----------------------------------------------------------------

    var etudiant_id          = $('#evaluation-data').data('etudiant_id');
    var etudiant_session_id  = $('input[name="session_id"]').val();
    var evaluation_id        = $('input[name="evaluation_id"]').val();
    var evaluation_reference = $('input[name="evaluation_reference"]').val();

    // ----------------------------------------------------------------
    //
    // Rafraichir l'evaluation en direct
    //
    // ----------------------------------------------------------------

    if ($('#endirect-rafraichir').length)
    {
        $('#endirect-rafraichir').click(function()
        {
            $(this).find('i').addClass('fa-spin');
        });
    }

    // ----------------------------------------------------------------
    //
    // Soumettre l'evaluation
    //
    // ----------------------------------------------------------------

	$('#soumission-form').submit(function(e) 
    {
        e.preventDefault();

        var submit_form = true;

        //
        // Verifier que les questions de type 10 ont ete repondues, et si requis,
        // avec des documents televerses.
        //

        if ($('#soumission-form .question-type-10').length)
        {
            $('.question-type-10').each(function()
            {
                var q_id = $(this).data('question_id');

                if ($('input[name="question_' + q_id + '"]:checked').val() == 1)
                {
                    var q_no = $(this).data('question_no');

                    if ($(this).find('.files-list tr.file-data').length == 0)
                    {
                        $('#question-' + q_id).find('.alert').removeClass('d-none');

                        location.hash = "#q" + q_no;

                        submit_form = false;
                    }
                }
            });
        }

        if (submit_form)
        {
            //
            // Enregistrer ce que l'etudiant voit comme temps ecoule dans la soumission
            //

            if ($('#duree-evaluation').length)
            {
                var temps_ecoule = $('#duree-evaluation').html();
                $('#soumission-form').find('input[name="temps_ecoule"]').val(JSON.stringify(temps_ecoule)); 
            } 

            $('#envoyer-evaluation').attr('disabled', 'disabled');
            $('#soumettre-icon').removeClass('d-none');

            $('#soumission-form').unbind('submit').submit()
        }
	});

    // ----------------------------------------------------------------
    //
    // Verifie que le numero DA (ou matricule) correspond aux listes
    // des eleves de l'enseignant.
    //
    // ----------------------------------------------------------------
    
    var $alerte_da = $('#alerte-da');
    var $evaluation_numero_da = $('#evaluation-numero-da');
    
    // Attendre un certain temps apres le dernier caractere entre pour demarrer la verification du numero DA (750ms)
    $evaluation_numero_da.keyup(_.debounce(verifier_numero_da , 750));

    // Verifier le numero DA immediatement apres que le champ soit hors focus
    $evaluation_numero_da.focusout(function() 
    {
        verifier_numero_da();
    });

    // Rafraichir le status si le champ numero DA est entre a nouveau
    $evaluation_numero_da.on('focusin keydown', function(e)
    {
        // e.type is the type of event fired
        $alerte_da.addClass('d-none');
        $evaluation_numero_da.removeClass('is-invalid');
        $evaluation_numero_da.removeClass('is-valid');
    });

    // ----------------------------------------------------------------
    //
    // Verification du numero DA (matricule)
    //
    // ----------------------------------------------------------------

    function verifier_numero_da()
    {
        if (verifier_numero_da_status != true)
            return false;

        var numero_da     = $.trim($evaluation_numero_da.val());
        var enseignant_id = $('input[name="enseignant_id"]').val();
        var groupe_id     = $('input[name="groupe_id"]').val();

        if ( ! numero_da || ! enseignant_id || ! groupe_id || groupe_id == 0) 
            return false;
            
        if ( ! $alerte_da.hasClass('d-none') || $evaluation_numero_da.hasClass('is-valid'))
            return false;

        $.post(base_url + 'evaluation/verifier_numero_da', { ci_csrf_token: cct, enseignant_id: enseignant_id, numero_da: numero_da },
        function(data)
        {
            if (data == false)
            {
                $evaluation_numero_da.addClass('is-invalid');
                $alerte_da.removeClass('d-none');

                return false;
            }            

            $evaluation_numero_da.addClass('is-valid');

            return true;

        }, 'json');
    }

    // ----------------------------------------------------------------
    //
    // Enregistrer les reponses aux questions (traces) :
    //
    // L'etudiant peut ainsi revenir completer son evaluation
    // advenant un probleme de soumission ou simplement s'il ferme
    // la fenetre de son fureteur par megarde. Par contre, les tracaes des 
    // etudiants non inscrits sont lies a leur session_id donc si l'etudiant 
    // ferme completement son fureteur, les traces seoront perdues.
    // 
    // ----------------------------------------------------------------

    /*
     * Les etudiant non inscrits ne sont plus permis. (2024/08/03)
     *
	if (logged_in == 0 && traces)
	{
		$('#evaluation-nom').change(function() 
		{
			var $sel = $(this);
			var nom  = $(this).val();

            if ( ! previsualisation && ! en_direct)
            {
                $.post(base_url + 'evaluation/enregistrer_nom_traces', 
                    { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, nom: nom },
                function(data)
                {
                    return true;
                });
            }
		});

		$('#evaluation-numero-da').change(function() 
		{
			var $sel      = $(this);
			var numero_da = $(this).val();

            if ( ! previsualisation && ! en_direct)
            {
                $.post(base_url + 'evaluation/enregistrer_numero_da_traces', 
                    { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, numero_da: numero_da },
                function(data)
                {
                    return true;
                });
            }
		});
	}
    */

    // ----------------------------------------------------------------
    //
    // Traces
    //
    // ----------------------------------------------------------------

	if (typeof traces !== 'undefined')
    {
        // ----------------------------------------------------------------
        //
        // Traces des input numeriques
        //
        // ----------------------------------------------------------------
        $('input.reponse-numerique').change(function() {

            var $sel          = $(this);
            var question_id   = $sel.parents('div.question-data').data('question_id'); 
            var question_no   = $sel.parents('div.question-data').data('question_no');
            var reponse       = $sel.val();

            if (reponse != '')
                $('#q' + question_no + 'box').css('background-color', '#E3F2FD');
            else
                $('#q' + question_no + 'box').css('background-color', '#F8F9FA');

            // if ( ! previsualisation && ! en_direct)
            if ( ! en_direct)
            {
                $.ajax({
                    method: 'POST',
                    url: base_url + 'evaluation/enregistrer_reponse_numerique_traces', 
                    data: { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, question_id: question_id, reponse: reponse },
                })
                .done(function(data) 
                {
                    data = $.parseJSON(data);

                    if (data == true)
                    {
                        $sel.parent().find('.traces-enregistrees').show().fadeOut(500);
                    }
                    else
                    {
                        $sel.parent().find('.traces-echecs').show().fadeOut(500);
                    }	
                })
                .fail(function(data)
                {
                    $sel.parent().find('.traces-echecs').show().fadeOut(500);
                });
            }
        });

        // ----------------------------------------------------------------
        //
        // Traces des radios
        //
        // ----------------------------------------------------------------
        $(':radio, select').change(function() {

			// 
            // (!) Fixer un conflit avec les traces de l'identification d'un partenaire de laboratoire (dans lab.js)
            //

			if ($(this).hasClass('lab-partenaire-select'))
            {
				return false;
            }

            if ($('#evaluation-identification-lab').find($(this)).length) 
            {
                return false;
            }

            if ($('#lab-tableaux-specifiques').find($(this)).length) 
            {
                return false;
            }

            var $sel        = $(this);
            var question_id = $sel.parents('div.question-data').data('question_id'); 
            var reponse_id  = $sel.val();
            var question_no = $sel.parents('div.question-data').data('question_no');

            $('#q' + question_no + 'box').css('background-color', '#E3F2FD');

            // if ( ! previsualisation && ! en_direct)
            if ( ! en_direct)
            {
                $.ajax({
                    method: 'POST',
                    url: base_url + 'evaluation/enregistrer_reponse_radio_traces', 
                    data: { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, question_id: question_id, reponse_id: reponse_id },
                })
                .done(function(data) 
                {
                    data = $.parseJSON(data);

                    if (data == true)
                    {
                        $sel.parent().find('.traces-enregistrees').show().fadeOut(500);
                    }
                    else
                    {
                        $sel.parent().find('.traces-echecs').show().fadeOut(500);
                    }	
                })
                .fail(function(data)
                {
                    $sel.parent().find('.traces-echecs').show().fadeOut(500);
                });
            }
        });

        // ----------------------------------------------------------------
        //
        // Traces des checkbox
        //
        // ----------------------------------------------------------------
        $(':checkbox').change(function() {

            var $sel        = $(this);
            var question_id = $sel.parents('div.question-data').data('question_id'); 
            var reponse_id  = $sel.val();
            var question_no = $sel.parents('div.question-data').data('question_no');

            $('#q' + question_no + 'box').css('background-color', '#E3F2FD');

            if ($sel.hasClass('confirmation'))
                return true;

            // if ( ! previsualisation && ! en_direct)
            if ( ! en_direct)
            {
                $.ajax({
                    method: 'POST',
                    url: base_url + 'evaluation/enregistrer_reponse_checkbox_traces', 
                    data: { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, question_id: question_id, reponse_id: reponse_id },
                })
                .done(function(data) 
                {
                    data = $.parseJSON(data);

                    if (data == true)
                    {
                        $sel.parent().find('.traces-enregistrees').show().fadeOut(500);
                    }
                    else
                    {
                        $sel.parent().find('.traces-echecs').show().fadeOut(500);
                    }	
                })
                .fail(function(data)
                {
                    $sel.parent().find('.traces-echecs').show().fadeOut(500);
                });
            }
        });

        // ----------------------------------------------------------------
        //
        // Traces des input text specifiques a certaines questions
        //
        // ----------------------------------------------------------------
        $('input.reponse-litterale-courte, input.reponse-developpement-court').change(function() {

            var $sel = $(this);
            var question_id = $sel.parents('div.question-data').data('question_id'); 
            var reponse     = $sel.val();
            var reponse_pre = $sel.data('reponse_pre'); // reponse avant le changement
            var question_no = $sel.parents('div.question-data').data('question_no');

            $('#q' + question_no + 'box').css('background-color', '#E3F2FD');

            // if ( ! previsualisation && ! en_direct)
            if ( ! en_direct)
            {
                $.ajax({
                    method: 'POST',
                    url: base_url + 'evaluation/enregistrer_reponse_text_traces', 
                    data: { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, question_id: question_id, reponse: reponse },
                })
                .done(function(data) 
                {
                    data = $.parseJSON(data);

                    if (data == true)
                    {
                        // https://stackoverflow.com/questions/19156148/i-want-to-remove-double-quotes-from-a-string
                        var reponse_nouv = reponse.replace(/["]+/g, '');

                        $sel.data('reponse_pre', reponse_nouv);
                        $sel.val(reponse_nouv);

                        $sel.parents('.question-reponse').find('.traces-enregistrees').show().fadeOut(500);
                    }
                    else
                    {
                        $sel.val(reponse_pre);
                        $sel.parents('.question-reponse').find('.traces-echecs').show().fadeOut(500);
                    }	
                })
                .fail(function(data)
                {
                    $sel.val(reponse_pre);
                    $sel.parents('.question-reponse').find('.traces-echecs').show().fadeOut(500);
                });
            }
        });

        // ----------------------------------------------------------------
        //
        // Traces des textarea
        //
        // ----------------------------------------------------------------
        $('textarea').change(function() {

            var $sel = $(this);
            var question_id = $sel.parents('div.question-data').data('question_id'); 
            var reponse     = $sel.val();
            var question_no = $sel.parents('div.question-data').data('question_no');

            $('#q' + question_no + 'box').css('background-color', '#E3F2FD');

            // if ( ! previsualisation && ! en_direct)
            if ( ! en_direct)
            {
                $.ajax({
                    method: 'POST',
                    url: base_url + 'evaluation/enregistrer_reponse_textarea_traces', 
                    data: { ci_csrf_token: cct, evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, question_id: question_id, reponse: reponse },
                })
                .done(function(data) 
                {
                    data = $.parseJSON(data);

                    if (data == true)
                    {
                        $sel.parent().find('.traces-enregistrees').show().fadeOut(500);
                    }
                    else
                    {
                        $sel.parent().find('.traces-echecs').show().fadeOut(500);
                    }	
                })
                .fail(function(data)
                {
                    $sel.parent().find('.traces-echecs').show().fadeOut(500);
                });
            }
        });

    } // traces

    // ----------------------------------------------------------------
    //
    // Aller vers une evaluation selon sa reference
    //
    // ----------------------------------------------------------------

    if ($('#trouver-evaluation').length)
    {
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
    }

    // ----------------------------------------------------------------
    //
    // Documents
    // 
    // ----------------------------------------------------------------
    
    if ($('.question-type-10').length)
    {
        // ----------------------------------------------------------------
        //
        // Activer ou desactiver le selecteur de documents
        // 
        // ----------------------------------------------------------------

        $('.question-type-10').each(function()
        {
            if ($(this).find('.files-list tr.file-data').length != 0)
            {
                $(this).find('input.repondre-oui').click();
                $(this).find('input.repondre-non').attr('disabled', true);
            }

            /* Ceci est fait server-side.
            if ($(this).find('input.repondre-non').is(':checked'))
            {
                $(this).find('.documents-uploader').addClass('d-none');
            }
            */

        });

        $('.question-type-10 .question-reponse input').change(function()
        {
            var $input      = $(this);
            var $input_wrap = $(this).parents('.question-type-10');
            var $uploader   = $(this).parents('.question-type-10').find('.documents-uploader');

            // Oui, je vais televerser des documents.
            if ($input.val() == 1)
            {
                $input.parents('.question-type-10').find('.documents-uploader').removeClass('d-none');
            }

            // Non, je ne vais pas repondre a cette question.
            // if ($input.val() == 9)
            else
            {
                if ($input_wrap.find('.files-list tr.file-data').length == 0)
                {
                    $uploader.addClass('d-none');
                }
                else
                {
                    $input_wrap.find('.repondre-oui').prop("checked", true);
                }
            }
        });

        // ----------------------------------------------------------------
        //
        // Generateur de string au hasard
        // 
        // ----------------------------------------------------------------
        function generate_random_string(len)
        {
            var text = "";
            // var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            // var possible = "abcdefghijklmnopqrstuvwxyz";
            var possible = "abcdefghijkmnopqrstuvwxyz0123456789"; // minus l (small L)

            if (len == null)
                len = 5;

            for( var i=0; i < len; i++ )
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            return text;
        }

        /* ------------------------------------------------------------------------
         *
         * Verifier si la table des televersements est vide.
         *
         * ------------------------------------------------------------------------ */

        function isUploadTableEmpty(question_id) 
        {
            var $files_preview = $('#files-preview-' + question_id);

            if ($('#files-preview-' + question_id + ' table tbody tr').length)
            {
                if ($('#files-list-' + question_id + ' tr.file-data').length)
                {
                    $('#files-list-' + question_id).css('margin-top', '15px');
                }

                $files_preview.removeClass('d-none');
            }
            else
            {
                $files_preview.addClass('d-none');
            }
        }

        function showUpload(question_id) 
        {
            //
            // Verifier que le maximum de documents n'est pas atteint.
            // Si c'est le cas, enlever la possibilite de selectionner d'autres fichiers.
            //

            var doc_count = 0;

            $('#files-list-' + question_id).find('tr.file-data').each(function()
            {
                doc_count++;
            });

            //
            // Activer ou desactiver le choix de ne ps repondre
            // selon s'il y a des documents ou non dans la liste.
            //

            if (doc_count > 0)
            {
                $('#question-' + question_id).find('input.repondre-non').attr('disabled', true);
            }
            else
            {
                $('#question-' + question_id).find('input.repondre-non').attr('disabled', false);
            }

            //
            // Montrer ou cacher le selecteur de documents selon si le maximum de documents
            // permis a ete atteint ou non
            //

            if (doc_count >= documents_max)
            {
                $('#question-' + question_id).find('.documents-uploader').addClass('d-none');
            }
            else
            {
                $('#question-' + question_id).find('.documents-uploader').removeClass('d-none');
            }
        }

        function showFilesList(question_id)
        {
            if ($('#files-list-' + question_id + ' tr.file-data').length)
            {
                $('#documents-manager-' + question_id).removeClass('d-none');
            }

            else
            {
                $('#documents-manager-' + question_id).addClass('d-none');
            }
        }

        /* ------------------------------------------------------------------------
         *
         * Ajoute un document pour y etre associe a une reponse.
         *
         * ------------------------------------------------------------------------ */

        // Ceci est pour regler un probleme avec le 'onchange' qui empeche de televerser
        // (ou seulement selectionner) le meme fichier. 

        $('.files-input').click(function(e)
        { 
            var question_id = $(this).closest('.question-data').data('question_id');
            
            $('#files-preview-' + question_id).addClass('d-none');
            $('.files-input-' + question_id).val(''); 
        });

        $('.files-input').change(function() 
        {
            var question_id = $(this).parents('.question-data').data('question_id');

            $('#televersement-erreur-' + question_id).addClass('d-none');

            $('#files-preview-' + question_id + ' tbody').empty();
            $('#files-preview-' + question_id).addClass('d-none');
            $('#file-reading-' + question_id).removeClass('d-none');

            // Ceci fixe un bug si la touche ESC est appuyee pour canceller une selection, alors le spinner reste visible.
            if (this.files.length == 0)
            {
                $('#file-reading-' + question_id).addClass('d-none');
                return true;
            }

            // console.log(this.files);

            var fichiers = [];

            for (var i = 0; i < this.files.length; i++)
            {
                if (renderImage(this.files[i], i, this.files.length, question_id))
                {
                    fichiers.push(this.files[i].random_str);
                }
            }

            // Le document a ete selectionne
            
            if (fichiers.length)
            {
                $.post(base_url + 'evaluation/document_selection', 
                        { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, question_id: question_id, fichiers: fichiers });
            }
        });

        function renderImage(file, file_index, last_file_index, question_id) 
        {
            var reader        = new FileReader();
            var file_name     = file.name;
            var file_size     = file.size;
            var file_size_str = '';
            var random_str    = generate_random_string(10);
            var file_type     = file.type;

            file.random_str = random_str;

            //
            // Verifier si le format est supporte.
            //

            if ($.inArray(file.type, documents_mime_types) == -1)
            {
                var msg = "Ce format n'est pas supporté. Les formats permis sont JPG, PNG, GIF et PDF.";

                $('#televersement-erreur-' + question_id).removeClass('d-none').find('span').html(msg);

                $('#file-reading-' + question_id).addClass('d-none');

                return false;
            }

            //
            // Verifier la taille du fichier.
            //
            
            if (file_size > documents_filesize_max)
            {
                var msg = "Ce fichier est trop volumineux. La taille maximale permise est " + documents_filesize_max / 1e6 + " Mo.";

                $('#televersement-erreur-' + question_id).removeClass('d-none').find('span').html(msg);

                $('#file-reading-' + question_id).addClass('d-none');

                return false;
            }

            if (file_size > 1000000) 
            {
                file_size_str = (file_size / 1000000).toFixed(1);
                file_size_str = file_size_str + ' Mo';
            }
            else 
            {
                file_size_str = (file_size / 1000).toFixed(1);
                file_size_str = file_size_str + ' Ko';
            }

            reader.onload = function(event) 
            {
                file_url = event.target.result;

                // Modifie le template dans une variable
                // https://stackoverflow.com/questions/22143055/replacing-manipulating-element-in-html-string-using-jquery

                var template = $('#file-preview-template-' + question_id).html(); // all templates are the same

                var $template  = $('<div />', {html:template});
                $template.find('.file-data').attr('id', random_str);
                $template.find('.file-data').attr('data-random_str', random_str);
                $template.find('.file-data').attr('data-index', file_index);
                $template.find('embed').attr('src', file_url);
                $template.find('.file-size').html(file_size_str);
                $template.find('.file-name').html(file_name);

                if (file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                    file.type == 'application/msword')
                {
                    $template.find('.img-thumbnail').attr('src', base_url + 'assets/images/icon_doc.png');
                }

                if (file.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                    file.type == 'application/vnd.ms-excel')
                {
                    $template.find('.img-thumbnail').attr('src', base_url + 'assets/images/icon_xls.png');
                }

                var template_copy = $template.find('tbody').html();

                $('#files-preview-' + question_id + ' tbody').append(template_copy);

                isUploadTableEmpty(question_id);

                if ((file_index + 1) >= last_file_index)
                {
                    $('#file-reading-' + question_id).addClass('d-none');
                }
            }

            // when the file is read it triggers the onload event above
            reader.readAsDataURL(file);

            return true;
        }

        /* ------------------------------------------------------------------------
         *
         * Televerser un document pour y etre associe a une reponse.
         *
         * ------------------------------------------------------------------------ */
        $('.files-preview').on('click', '.file-upload', function() 
        {
            var question_id = $(this).closest('.question-data').data('question_id');
            var random_str  = $(this).closest('.file-data').data('random_str');

            // var files = document.getElementById('files-input-' + question_id).files;
            var files = $('#files-input-' + question_id).prop('files');

            var $row       = $(this).closest('.file-data'); 
            var file_id    = $row.attr('id');
            var file_index = $row.data('index');

            uploadFile(files[file_index], file_id, question_id, random_str);
        });

        function uploadFile(file, file_id, question_id, random_str)
        {
            var data = new FormData();

            var $progress = $('#' + random_str + ' progress');

            var data;

            data.append('ci_csrf_token', cct);
            data.append('question_id', question_id);
            data.append('etudiant_id', etudiant_id);
            data.append('etudiant_session_id', etudiant_session_id);
            data.append('evaluation_id', evaluation_id);
            data.append('evaluation_reference', evaluation_reference);
            data.append('inscription_requise', inscription_requise);
            data.append('random_str', random_str);
            data.append('upload_file', file);

            //
            // Barre de progression
            //

            function progressHandlingFunction(e) 
            {
                if (e.lengthComputable)
                {
                    $progress.attr({ value: e.loaded, max: e.total });
                }

                if (e.loaded >= e.total) 
                {
                    $progress.css('visibility','hidden');
                    $('#' + file_id + ' .file-cancel').remove();
                    // $('#' + file_id + ' .file-processing').removeClass('d-none');
                }
            }

            //
            // Avant de commencer le televersement, enlever le bouton pour le demarrer
            // car il a deja ete appuye.
            // 

            $('#' + file_id + ' .file-upload').remove();

            //
            // Demarrer le spinner.
            //

            $('#' + file_id + ' .upload-spinner').removeClass('d-none');

            //
            // Demarrer le televersement.
            //

            $.ajax({
                url: base_url + 'evaluation/televersement',
                method: 'POST',
                data: data,
                dataType: 'json',
                xhr: function() 
                {  
                    myXhr = $.ajaxSettings.xhr();
                    // check if upload property exists
                    if (myXhr.upload) 
                    { 
                        myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // for handling the progress of the upload
                    }
                    return myXhr;
                },
                success: function(file_data) 
                {
                    //
                    // L'inscription est requise pour televerser un document
                    //

                    if (file_data == 9)
                    {
                        document.location.reload(true);
                        return false;
                    }

                    // var file_data = $.parseJSON(file_data);

                    $('#question-' + question_id).find('.alert').addClass('d-none');

                    // remove row in the upload section
                    $('#' + file_id).remove();

                    /* json response:
                    {
                        "groupe_id":"1",
                        "question_id":"9684",
                        "etudiant_id":"1",
                        "etudiant_session_id":null,
                        "evaluation_id":"166",
                        "evaluation_reference":"ekpemy",
                        "doc_filename":"g1_1588988062_vnrcsx.jpeg",
                        "doc_sha256_file":"xxx",
                        "doc_filesize":245393,
                        "doc_is_image":1,
                        "doc_mime_type":"image\/jpeg",
                        "doc_size_h":1200,
                        "doc_size_w":1600,
                        "ajout_date":"2020-05-08 21:34:12",
                        "ajout_epoch":"1588988052",
                        "modif_date":"2020-05-08 21:34:12",
                        "modif_epoch":"1588988052",
                        "doc_tn_filename":"g1_1588988052_vnrcsx_tn.jpeg",
                        "doc_tn_sha256_file":"xxx",
                        "doc_tn_filesize":4511,
                        "doc_tn_is_image":1,
                        "doc_tn_size_h":113,
                        "doc_tn_size_w":150,
                        "doc_tn_mime_type":"image\/jpeg",
                        "doc_id"
                    }
                    */

                    //
                    // Modifier le template dans une variable.
                    //

                    var template = $('#file-list-template-' + question_id).html();
                    var $template  = $('<div />', {html:template});

                    $template.find('.file-data').attr('id', 'file-' + file_data['doc_id']);
                    $template.find('.file-delete').attr('data-doc_id', file_data['doc_id']);

                    if (file_data['doc_is_image'])
                    {
                        $template.find('.image-rotation').removeClass('d-none');
                        $template.find('.file-rotation').attr('data-doc_id', file_data['doc_id']);
                    }

                    // $template.find('.file-link').attr('href', documents_path_s + file_data['doc_filename']);
                    // $template.find('.file-download').attr('href', documents_path_s + file_data['doc_filename']);
                   
                    if (utiliser_s3)
                    {
                        $template.find('img').attr('src', s3_url + 'soumissions/' + file_data['doc_tn_filename'] + '?' + file_data['doc_sha256_file']);
                        $template.find('a.img-original').attr('href', s3_url + 'soumissions/' + file_data['doc_filename'] + '?' + file_data['doc_sha256_file']);
                        $template.find('a.file-link').attr('href', s3_url + 'soumissions/' + file_data['doc_filename'] + '?' + file_data['doc_sha256_file']);
                    }
                    else
                    {
                        $template.find('img').attr('src', documents_path_s + file_data['doc_tn_filename'] + '?' + file_data['doc_sha256_file']);
                        $template.find('a.img-original').attr('href', documents_path_s + file_data['doc_filename'] + '?' + file_data['doc_sha256_file']);
                        $template.find('a.file-link').attr('href', documents_path_s + file_data['doc_filename'] + '?' + file_data['doc_sha256_file']);
                    }

                    if (file_data['doc_mime_type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                        file_data['doc_mime_type'] == 'application/msword')
                    {
                        $template.find('.img-thumbnail').attr('src', base_url + 'assets/images/icon_doc.png');
                    }

                    if (file_data['doc_mime_type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                        file_data['doc_mime_type'] == 'application/vnd.ms-excel')
                    {
                        $template.find('.img-thumbnail').attr('src', base_url + 'assets/images/icon_xls.png');
                    }

                    var template_copy = $template.find('tbody').html();

                    //
                    // Fermer les preview des documents avant le televersement.
                    //

                    if ($('#files-preview-' + question_id + ' tr.file-data').length < 1)
                    {
                        $('#files-preview-' + question_id).addClass('d-none');
                    }

                    //
                    // L'etudiant veut repondre avec des documents.
                    //

                    $('#question-' + question_id).find('input.repondre-oui').click();

                    //
                    // Inserer le template dans le HTML.
                    //

                    $('#files-list-' + question_id + ' tbody').append(template_copy);

                    //
                    // Gerer les changements pour afficher les options pertinentes.
                    //
                    
                    showUpload(question_id);
                    showFilesList(question_id);

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

        // ----------------------------------------------------------------
        //
        // Annuler l'ajout d'un document
        // 
        // ----------------------------------------------------------------
        $('.files-preview').on('click', '.file-cancel', function() {

            var question_id = $(this).parents('.question-data').data('question_id');

            if (typeof myXhr != 'undefined') 
            {
                 myXhr.abort();
            }

            var random_str = $(this).parents('tr.file-data').data('random_str');

            $(this).parents('tr').remove();

            // Le document a ete annule
            $.post(base_url + 'evaluation/document_annulation', { ci_csrf_token: cct, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, question_id: question_id, random_str: random_str});

            //
            // Fermer les preview des documents avant le televersement.
            //

            if ($('#files-preview-' + question_id + ' tr.file-data').length < 1)
            {
                $('#files-preview-' + question_id).addClass('d-none');
            }

            showUpload(question_id);
            showFilesList(question_id);
        });

        // ----------------------------------------------------------------
        //
        // Effacer un document
        // 
        // ----------------------------------------------------------------
        $('.files-list').on('click', '.file-delete', function() 
        {
            var doc_id = $(this).data('doc_id');
            var question_id = $(this).parents('.question-data').data('question_id');

            $row = $('#file-' + doc_id);

            $row.find('.file-operations').addClass('d-none');
            $row.find('.file-processing').removeClass('d-none');
            $row.find('.file-operations-group .file-processing-spinner').removeClass('d-none');

            // console.log('doc_id = ' + doc_id + ', question_id = ' + question_id + ', evaluation_id = ' + evaluation_id + ', evaluation_reference = ' + evaluation_reference);

            $.post(base_url + 'evaluation/effacer_document_soumission', 
                    { ci_csrf_token: cct, doc_id: doc_id, question_id: question_id, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, etudiant_session_id: etudiant_session_id },
            function(data) 
            {
                if (data == true) 
                {
                    $row.remove();

                    showUpload(question_id);
                    showFilesList(question_id);
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
            var doc_id      = $(this).data('doc_id');
            var question_id = $(this).parents('.question-data').data('question_id');
            var rotation    = $(this).data('rotation');
            
            var $row = $(this).parents('tr.file-data');

            $row.find('.file-operations').addClass('d-none');
            $row.find('.file-processing').removeClass('d-none');
            $row.find('.file-processing-spinner').removeClass('d-none');

            $.post(base_url + 'evaluation/rotation_image', 
                    { ci_csrf_token: cct, doc_id: doc_id, rotation: rotation, question_id: question_id, evaluation_id: evaluation_id, evaluation_reference: evaluation_reference, etudiant_session_id: etudiant_session_id },
            function(data) 
            {
                if (data != false) 
                {
                    var $image  = $('#file-' + doc_id);

                    $image.find('img.img-thumbnail').attr('src', data['uri_tn'] + '?' + data['doc_sha256_file']);
                    $image.find('a.img-original').attr('href', data['uri'] + '?' + data['doc_sha256_file']);
                    $image.find('a.file-link').attr('href', data['uri'] + '?' + data['doc_sha256_file']);

                    $row.find('.file-operations').removeClass('d-none');
                    $row.find('.file-processing').addClass('d-none');
                    $row.find('.file-processing-spinner').addClass('d-none');
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

    } // if .question-type-10

    // ------------------------------------------------------------------------
    //
	// Ping
    //
    // ------------------------------------------------------------------------
    //
    // - Verifier si l'etudiant est en train de faire son evaluation.
    // - Verifier si l'evaluation est toujours en vigueur.
    // 
    // ------------------------------------------------------------------------

    var pingHandler = null;

    // L'intervalle entre chaque ping. Ceci est determine dans les parametres dynamiques.
    // Cette variable est initialisee dans la portion script de la vue, dans le cas contraire,
    // on assigne une valeur par defaut correspondant a 3 minutes (180s).

    if (typeof pingInterval == 'undefined' || isNaN(pingInterval)) 
    {
        pingInterval = 180e3;
    }

    if (typeof pingSetting !== 'undefined' && pingSetting != 0)
    {
		if ($('#evaluation').length && etudiant_id != '')
        {
            pingEtudiant();
        }
    }

    //
    // Cette fonction ping l'etudiant a un certain intervalle.
    //

    function pingEtudiant() 
    {
        var notification = true;

        // Ne pas verifier les nouvelles notifications si une notification
        // est presentement affichee a l'etudiant

        if ($('#modal-message-enseignant').hasClass('active'))
        {
            notification = false;
        }

        $.post(base_url + 'evaluation/ping_etudiant', 
                { ci_csrf_token: cct, etudiant_id: etudiant_id, session_id: etudiant_session_id, 
                  evaluation_reference: evaluation_reference, evaluation_id: evaluation_id, notification: notification },
        function(data)
        {
            // Si la version de l'application change pendant l'evaluation de l'etudiant, 
            // la page doit etre rafraichie pour mettre a jour le code js.

            /*
            if (typeof data == 'object' && 'app_version' in data && data['app_version'] != app_version)
            {
                document.location.reload(true);
                return false;
            }
            */

            if (typeof data == 'object' && 'terminee' in data && data['terminee'] == true)
            {
                window.location = base_url + "evaluation/terminee"; 
                return false;
            }

			else if (typeof data == 'object' && 'terminee_non_inscrit' in data && data['terminee_non_inscrit'] == true)
            {
                window.location = base_url + "evaluation/terminee/non_inscrit"; 
                return false;
            }

			else if (typeof data == 'object' && 'terminee_lab_partenaire' in data && data['terminee_lab_partenaire'] == true)
            {
                window.location = base_url + "evaluation/terminee/lab_partenaire"; 
                return false;
            }

			else if (typeof data == 'object' && 'terminee_abruptement' in data && data['terminee_abruptement'] == true)
            {
                window.location = base_url + "evaluation/terminee/abruptement"; 
                return false;
            }

            if (typeof data == 'object' && 'message' in data)
            {
                $('#message-enseignant').html(data['message']);
                $('#modal-message-enseignant').modal('show');
            }

            if (typeof data == 'object' && 'epoch' in data)
            {
                if ($('#affichage-duree').length)
                {
                    var temps_maintenant = Number(data['epoch']) + 2; // Il y a une seconde de retard.

                    $('#duree-evaluation').data('maintenant_epoch', temps_maintenant);
                    $('#heure-evaluation').data('maintenant_epoch', temps_maintenant);
                }
            }

            if (typeof data == 'object' && 'intervalle' in data)
            {
                pingInterval = data['intervalle'] * 1000;
            }

			// Verifier le temps limite

			if (
				typeof data == 'object' && 
				'temps_limite' in data && 
				'fin_epoch' in data && 
				'soumission_debut_epoch' in data
			   )
			{
				let temps_limite_a_changer = false;

				let temps_limite_possible = parseInt($('#evaluation-temps-limite').data('temps_limite'));

				// Il faut verifier si la fin de l'evaluation a ete planifiee.
				if (typeof data['fin_epoch'] !== 'undefined' && data['fin_epoch'] > 0)
				{
					// Le +60 est pour regler le cas limite.
					temps_limite_possible = Math.floor((data['fin_epoch'] - data['soumission_debut_epoch'] + 60) / 60);
				}

				if (temps_limite_possible < data['temps_limite'])
				{
					temps_limite_a_changer = true;					
				}
			
				if (temps_limite_possible > data['temps_limite'])
				{
					temps_limite_possible = data['temps_limite'];
					temps_limite_a_changer = true;					
				}	

				if (temps_limite_a_changer)
				{
					$('#evaluation-temps-limite').html(temps_limite_possible + ' mins');
					$('#evaluation-temps-limite').data('temps_limite', temps_limite_possible);

					let date_limite_epoch = Math.floor(parseInt(data['soumission_debut_epoch']) + (temps_limite_possible * 60));
					let date_limite = new Date(date_limite_epoch * 1000);

					let limite_heure	 = date_limite.getHours();
					let limite_minutes	 = '0' + date_limite.getMinutes();
					let limite_affichage = limite_heure + ':' + limite_minutes.substr(-2) + ':00';
					
					$('#evaluation-temps-limite-date').html('(fin à ' + limite_affichage + ')');
				}
			}

        }, 'json');
        
        pingHandler = setTimeout(pingEtudiant, pingInterval);
    }

    // ----------------------------------------------------------------
    //
    // Message de l'enseignant
    //
    // ----------------------------------------------------------------
   
    $('#modal-message-enseignant').on('show.bs.modal', function(e) {

        $('#modal-message-enseignant').addClass('active');

    });

    $('#modal-message-enseignant').on('hide.bs.modal', function(e) {

        $('#modal-message-enseignant').removeClass('active');

    });

    // ----------------------------------------------------------------
    //
    // Gestion dynamique de l'horloge et de la duree de l'examen
    //
    // ----------------------------------------------------------------
    //
	// - Temps ecoule depuis le debut de la redaction 
    // - Heure du serveur
    //
    // ----------------------------------------------------------------

    if ($('#affichage-duree').length)
    {
        var seconde_eval_interval = 1000;

        function rafraichirDuree() 
        {
            var temps_debut = $('#duree-evaluation').data('debut_epoch');

            var temps_maintenant = Number($('#duree-evaluation').data('maintenant_epoch'));
            var temps_maintenant_plus = temps_maintenant + 1;

            var temps_diff = temps_maintenant - temps_debut;
            var temps_str  = '';

            // get seconds
            var seconds = Math.round(temps_diff % 60);

            // remove seconds from the date
            temps_diff = Math.floor(temps_diff / 60);

            // get minutes
            var minutes = Math.round(temps_diff % 60);
            
            // remove minutes from the date
            temps_diff = Math.floor(temps_diff / 60);

            // get hours
            var hours = Math.round(temps_diff % 24);
            
            // remove hours from the date
            temps_diff = Math.floor(temps_diff / 24);
            
            // the rest of temps_diff is number of days
            var days = temps_diff ;

            if (days > 0)
                temps_str = days + 'j';

            if (hours > 0)
                temps_str += hours + 'h';

            if (minutes > 0)
			{
				if (hours > 0)
				{
					minutes = '0' + minutes;
					temps_str += minutes.substr(-2) + 'm';
				}
				else
				{
					temps_str += minutes + 'm';
				}
			}

			if (hours > 0 || minutes > 0)
			{
				seconds = '0' + seconds;
				temps_str += seconds.substr(-2) + 's';
			}
			else
			{
				temps_str += seconds + 's';
			}

            $('#duree-evaluation').data('maintenant_epoch', temps_maintenant_plus).html(temps_str);

			// minutes totales
			var minutes_tot = hours*60 + parseInt(minutes);

			// Il est inutle d'indiquer le temps ecoule sous 1 heure.
			if (minutes_tot > 59 && days < 1)
			{
				$('#duree-evaluation-minutes').html('(' + minutes_tot + ' mins)');
			}
			else
			{
				$('#duree-evaluation-minutes').html('');
			}

            dureeHandler = setTimeout(rafraichirDuree, seconde_eval_interval);
        }

        function rafraichirTemps()
        {
            var temps_maintenant = Number($('#heure-evaluation').data('maintenant_epoch')) * 1000;
            var temps_maintenant_plus = Number($('#heure-evaluation').data('maintenant_epoch')) + 1;

            var currentTime = new Date(temps_maintenant);

            var y = currentTime.getFullYear();
            var mo = currentTime.getMonth();
            var d = currentTime.getDate(); 

            var h = currentTime.getHours();
            var m = currentTime.getMinutes();
            var s = currentTime.getSeconds();

            // La numerotation commence a 0, donc 0 = janvier.
            mo = mo + 1; 

            if (mo < 10) 
            {
                mo = "0" + mo;
            }

            if (m < 10) 
            {
                m = "0" + m;
            }

            if (d < 10) 
            {
                d = "0" + d;
            }
            
            if (s < 10) 
            {
                s = "0" + s;
            }

            var server_time_str = y + '-' + mo + '-' + d + ' ' + h + ':' + m + ':' + s;

            $('#heure-evaluation').html(server_time_str);
            $('#heure-evaluation').data('maintenant_epoch', temps_maintenant_plus);

            tempsHandler = setTimeout(rafraichirTemps, seconde_eval_interval);
        }

        rafraichirDuree();

        rafraichirTemps();
    }

});
