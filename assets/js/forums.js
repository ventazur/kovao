/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > forums.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Generer la previsualisation du message
     *
     * ---------------------------------------------------------------- */
    if ($('#forums-publier').length)
    {
        $('#message-contenu').keyup(function()
        {           
            var msg = $('#message-contenu').val().replace(/\r\n|\r|\n/g,"<br />");

            if (msg.length > 0)
            {       
                $('#previsualisation').removeClass('d-none');
            }
            else
            {
                $('#previsualisation').addClass('d-none');
            }

            $('#message-previsualisation').html(msg);
        }); 
    }

    /* ----------------------------------------------------------------
     *
     * Nouveau message
     *
     * ---------------------------------------------------------------- */
    
    if ($('#poster-message').length)
    {
        $('#poster-message').prop('disabled', true);

        $('#message-titre, #message-contenu').keyup(function()
        {
            if ($('#message-titre').val() != '' && $('#message-contenu').val() != '')
            {
                $('#poster-message').prop('disabled', false);
            }
            else
            {
                $('#poster-message').prop('disabled', true);
            }
        });
    }

    /* ----------------------------------------------------------------
     *
     * Suivre et ne plus suivre un message
     *
     * ---------------------------------------------------------------- */
    if ($('#forums-lire').length)
    {
        //
        // Suivre un message
        //

        $('#suivre').click(function()
        {
            var message_id = $(this).data('message_id');

        	$.post(base_url + 'forums/suivre_message', { ci_csrf_token: cct, message_id: message_id },
        	function()
        	{
                document.location.reload(true);
                return false;
            });
        });

        //
        // Ne plus suivre un message
        //

        $('#ne-plus-suivre').click(function()
        {
            var message_id = $(this).data('message_id');

        	$.post(base_url + 'forums/arret_suivre_message', { ci_csrf_token: cct, message_id: message_id },
        	function()
        	{
                document.location.reload(true);
                return false;
            });
        });
    }

    /* ----------------------------------------------------------------
     *
     * Commentaire
     *
     * ---------------------------------------------------------------- */

    $('#publier-commentaire').prop('disabled', true);

    $('#commentaire-contenu').keyup(function() 
    {
        if ($(this).val() != '') 
        {
            $('#publier-commentaire').prop('disabled', false);
        }
        else
        {
            $('#publier-commentaire').prop('disabled', true);
        }
    });
});
