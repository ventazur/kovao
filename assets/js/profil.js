/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > profil.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Sauvegarder le profil
     *
     * ---------------------------------------------------------------- */
    $('#sauvegarder-profil').click(function()
    {
        var $spinner = $(this).find('.spinner');

        $spinner.removeClass('d-none');
    });

    /* ----------------------------------------------------------------
     *
     * Sauvegarder automatiquement les parametres checkbox
     *
     * ---------------------------------------------------------------- */
    if ($('#profil-enseignant-parametres, #profil-etudiant-parametres').length)
    {
        $(':checkbox').change(function(e)
        {
            $('#sauvegarder-profil').trigger('click');
        });
    }

});
