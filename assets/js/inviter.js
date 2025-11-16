/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.COM > inviter.js
 *
 * ==================================================================== */
$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     * Effacer les erreurs lors du focus
     *
     * ---------------------------------------------------------------- */
    $('#courriel').focusin(function()
    {
        $(this).removeClass('is-invalid');
    });

});
