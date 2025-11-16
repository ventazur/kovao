/*!
 * KOVAO - Open-source evaluation project
 * (c) 2018–2025 KOVAO Project – AGPL-3.0
 * FR : Forks autorisés sous autre nom, attribution à KOVAO requise.
 * EN : Forks allowed under another name, attribution to KOVAO required.
 */

/* ====================================================================
 *
 * KOVAO.com > evaluations2.js
 *
 * ==================================================================== */

$(document).ready(function()
{
	const currentPath = window.location.pathname;

    const match = currentPath.match(/\/cours\/([a-zA-Z0-9]{3})/);

    if (match) 
	{
        var code_cours_load = match[1]; // Extraire "xxx" (les 3 caractères)

		var $sel = $('#cours-' + code_cours_load);

		lister_evaluations_cours($sel, code_cours_load);
    }

    $('#lister-cours .cours').click(function()
    {
        var $sel = $(this);
            
		var code_cours = $(this).attr('id').split('-')[1] || '';

		if (code_cours == 'tous')
		{
			code_cours = '';
		}

		lister_evaluations_cours($sel, code_cours);
    });

	function lister_evaluations_cours($sel, code_cours)
	{
		//
		// mettre a jour l'URL selon le code du cours selectionne
		//

		if ($sel.hasClass('cours'))
		{
			let currentPath = window.location.pathname;

			if (code_cours) {
				// Cas : Ajouter ou mettre à jour /cours/xxx
				if (currentPath.includes('/cours/')) {
					// Remplacer le code_cours existant
					currentPath = currentPath.replace(/\/cours\/[a-zA-Z0-9]{3}/, `/cours/${code_cours}`);
				} else {
					// Ajouter /cours/xxx si absent
					currentPath += `/cours/${code_cours}`;
				}
			} else {
				// Cas : Supprimer /cours/xxx si le code_cours est vide
				currentPath = currentPath.replace(/\/cours\/[a-zA-Z0-9]{3}/, '');
			}

			// Modifier l'URL sans recharger la page
			window.history.pushState({}, '', currentPath);
		}

        $('#lister-cours .cours').removeClass('actif');

        // Tous les cours
        if ($sel.hasClass('tous-les-cours'))
        {
            $('.cours-md5').css('display', 'block');
            $sel.addClass('actif');
        }

        else
        {
            $('.cours-md5').css('display', 'none');

            var md5 = $sel.data('cours_md5');

            $('#' + md5).css('display', 'block');

            $sel.addClass('actif');
        }


	}



});
