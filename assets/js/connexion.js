/*!
 * KOVAO - Open-source evaluation project
 * © 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.COM > connexion.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Empecher d'envoyer deux fois une demande de reinitialisation
     *
     * ---------------------------------------------------------------- */

    $('#demande-envoyer').click(function(e)
    {
        $('#demande-envoyer').addClass('d-none');
        $('#demande-envoie-en-cours').removeClass('d-none');
    });

    /* ----------------------------------------------------------------
     *
     * 
     *
     * ---------------------------------------------------------------- */

	$('#se-connecter').click(function()
	{
		$(this).find('i').removeClass('d-none');
	});
});
