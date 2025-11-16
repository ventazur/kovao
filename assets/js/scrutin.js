/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > scrutins.js
 *
 * ==================================================================== */
$(document).ready(function()
{
    /* ----------------------------------------------------------------
     *
     *  Scrutin
     *
     * ---------------------------------------------------------------- */
    if ($('#scrutin').length)
    {
        $('input[name="scrutin_choix_id"]').change(function()
        {
            var $sel = $(this);
            var $scrutin_choix = $sel.parents('.scrutin-choix');

            $('.scrutin-choix').removeClass('surligne');

            if ($sel.prop('checked'))
            {
                $scrutin_choix.addClass('surligne');
            }
        });
	}
});
