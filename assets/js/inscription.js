/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.COM > inscription.js
 *
 * ==================================================================== */
$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Inscription
     *
     * ---------------------------------------------------------------- */
    
    $('#demande-inscription').submit(function(e)
    {
        e.preventDefault();

        /* --------------------------------------------------------
         *
         * Google reCAPTCHA v3
         *
         * -------------------------------------------------------- */
        grecaptcha.ready(function() 
        {
            grecaptcha.execute('6LerZfQUAAAAAHMCPcZmcK_G8-UDCqCppLC3tZSI', {action: 'inscription'}).then(function(token) 
            {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;

            }).then(function()
            {
                $('#demande-envoyer').addClass('d-none');
                $('#demande-envoie-en-cours').removeClass('d-none');

                $('#demande-inscription').unbind('submit').submit();
                return false;
            });
        });
    });

    /* ----------------------------------------------------------------
     *
     * Inscription des etudiants
     *
     * ---------------------------------------------------------------- */

    if ($('#inscription-etudiant').length)
    {

    }

    /* ----------------------------------------------------------------
     *
     * Inscription des enseignants
     *
     * ---------------------------------------------------------------- */

    if ($('#inscription-enseignant').length)
    {

    }
});
