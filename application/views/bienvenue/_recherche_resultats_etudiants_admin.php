<?
/* ----------------------------------------------------------------------------
 *
 * Resultats de la recherche > Etudiants
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if ( ! empty($etudiants)) : ?>

<div class="space-d"></div>

<div class="recherche-resultats-section">

    <div class="recherche-resultats-section-titre">

        <i class="fa fa-search" style="margin-right: 5px;"></i>
        Étudiants

    </div>

    <div class="recherche-resultats-section-contenu">

        <table class="table" style="font-size: 0.85em">
            <thead>
                <tr>
                    <th style="width: 125px">Etudiant ID</th>
                    <th>Prénom et Nom</th>
                    <th style="width: 180px">Inscription</th>
                    <th style="width: 220px">Dernière activité</th>
                    <th style="width: 350px">Courriel</th>

                </tr>
            </thead>
            <tbody>
                <? foreach($etudiants as $e) : ?>
                    <tr>
                        <td>
                            <a href="<?= base_url() . 'admin/etudiant/' . $e['etudiant_id']; ?>" target="_blank">
                                <?= $e['etudiant_id']; ?>
                            </a>
                        </td>
                        <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width: 1px;">
                            <?= $e['prenom'] . ' ' . $e['nom']; ?> 
                            <? if ($e['genre'] == 'F') : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#EC407A" class="bi bi-xs bi-gender-female" viewBox="0 0 16 16" style="">
                                  <path fill-rule="evenodd" d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5z"/>
								</svg>
							<? elseif ($e['genre'] == 'X') : ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-gender-ambiguous" viewBox="0 0 16 16">
									<path fill-rule="evenodd" d="M11.5 1a.5.5 0 0 1 0-1h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-3.45 3.45A4 4 0 0 1 8.5 10.97V13H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V14H6a.5.5 0 0 1 0-1h1.5v-2.03a4 4 0 1 1 3.471-6.648L14.293 1H11.5zm-.997 4.346a3 3 0 1 0-5.006 3.309 3 3 0 0 0 5.006-3.31z"/>
								</svg>
                            <? else : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0D47A1" class="bi bi-xs bi-gender-male" viewBox="0 0 16 16" style="">
                                  <path fill-rule="evenodd" d="M9.5 2a.5.5 0 0 1 0-1h5a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0V2.707L9.871 6.836a5 5 0 1 1-.707-.707L13.293 2H9.5zM6 6a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/>
                                </svg>
                            <? endif; ?>
                        </td>
                        <td class="mono"><?= $e['inscription_date']; ?></td>
                        <td class="mono"><?= $e['derniere_activite_date']; ?> [<?= $e['activite_compteur'] ?: 0; ?>]</td>
                        <td>
                            <?= $e['courriel']; ?>
                            <? if ($e['courriel_confirmation']) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="limegreen" class="bi bi-xs bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg> 
                            <? else : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="crimson" class="bi bi-xs bi-x-circle" viewBox="0 0 16 16">
                                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>

            </tbody>
        </table>


    </div>

</div>

<? endif; ?>
