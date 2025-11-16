/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > groupes.js
 *
 * ==================================================================== */

$(document).ready(function()
{
    if ($('#choisir-ecole-select').length)
    { 
        $('#choisir-ecole-select').change(function()
        {
            var ecole_id = $(this).val();

            if (ecole_id == 0 || typeof ecole_id == 'undefined')
            {
                return false;
            }

            window.location = current_url + '/' + ecole_id;
            return true;
        });
    }

});
