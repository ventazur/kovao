<? 
/* ------------------------------------------------------------------------
 *
 *  Administration > Groupe > Evaluations
 *
 * ------------------------------------------------------------------------ */ ?>

<? 
/* ------------------------------------------------------------------------
 *
 * En redaction
 *
 * ------------------------------------------------------------------------ */ ?>

<h5>
    Les évaluations en rédaction
    <span style="font-weight: 300">
        (<?= count($etudiants_redaction); ?>)
    </span>
</h5>

<div class="space"></div>

<? if (empty($etudiants_redaction)) : ?>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucune évaluation présentement en rédaction

<? else : ?>

    <table id="evaluations" class="table admin-table">

        <tr style="background: #fff">
            <th style="width: 90px">Référence</th>
            <th style="width: 250px">Étudiant</th>
            <th>Titre de l'évaluation</th>
            <th style="width: 130px">Enseignant</th>
            <th style="width: 120px; text-align: center">Temps écoulé</th>
            <th style="width: 170px">Dernière activité</th>
        </tr>

        <? foreach($etudiants_redaction as $ee) : 

            $bg_actif   = '#FFECB3';
            $bg_inactif = '#F7F7F7';

            if (
                (array_key_exists('nom', $ee) && ! empty($ee['nom'])) ||
                (array_key_exists('prenom', $ee) && ! empty($ee['prenom']))
               )
            {
                $nom = $ee['prenom'] . ' ' . $ee['nom'];
            }
            else
            {
                $nom = 'Non-inscrit [' . substr($ee['session_id'], 0, 5) . ']';
            }
        ?>
            <tr style="background: <?= ($this->now_epoch - $ee['activite_epoch']) < 60*5 ? $bg_actif : $bg_inactif; ?>">
                <td class="mono" style="background: inherit">
                    <a href="<?= base_url() . 'evaluation/previsualisation/' . $ee['evaluation_id'] . '/etudiant'; ?>" target="_blank">
                        <?= $ee['evaluation_reference']; ?>
                    </a>
                </td>
                <td style="background: inherit">
                    <? if ($ee['etudiant_id']) : ?>
                        <a href="<?= base_url() . 'admin/etudiant/' . $ee['etudiant_id']; ?>"><?= ellipsize($nom, 30); ?></a>
                        <a style="margin-left: 3px" href="<?= base_url() . 'evaluation/endirect/reference/' . $ee['evaluation_reference'] . '/etudiant/' . $ee['etudiant_id']; ?>" target="_blank">
                            <svg data-toggle="tooltip" data-title="En direct" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi-xs bi-front" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm5 10v2a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2v5a2 2 0 0 1-2 2H5z"/>
                            </svg>
                        </a>
                    <? else : ?>
                        <?= ellipsize($nom, 30); ?>
                    <? endif; ?>
                </td>
                <td style="background: inherit">
                    <?= ellipsize($evaluations[$ee['evaluation_id']]['evaluation_titre'], 80); ?>
                </td>
                <td style="background: inherit">
                    <a href="<?= base_url() . 'admin/enseignant/' . $evaluations[$ee['evaluation_id']]['enseignant_id']; ?>">
                        <?= ellipsize(substr($evaluations[$ee['evaluation_id']]['prenom'], 0, 1) . '. ' . $evaluations[$ee['evaluation_id']]['nom'], 30); ?>
                    </a>
                </td>
                <td class="mono" style="background: inherit; text-align: center">
                    <span style="cursor: pointer" 
                          data-trigger="hover" 
                          data-toggle="popover" 
                          data-html="true"
                          data-placement="top"
                          data-content="Rédige depuis :<br /><?= date_humanize($ee['soumission_debut_epoch'], TRUE); ?>">
                        <?= calculer_duree($ee['soumission_debut_epoch'], $this->now_epoch); ?>
                    </span>
                </td>
                <td class="mono" style="background: inherit">
                    <span style="cursor: pointer" 
                          data-trigger="hover" 
                          data-toggle="popover" 
                          data-html="true"
                          data-placement="top"
                          data-content="Temps en activité :<br /><?= calculer_duree(0, $ee['secondes_en_redaction']); ?>">
                        <?= ! empty($ee['activite_epoch']) ? date_humanize($ee['activite_epoch'], TRUE) : 'non disponible'; ?>
                    </span>
                </td>
            </tr>
        <? endforeach; ?>

    </table>

<? endif; ?>

<div class="dspace"></div>

<? 
/* ------------------------------------------------------------------------
 *
 * Pouvant etre remplies
 *
 * ------------------------------------------------------------------------ */ ?>

<h5>
    Les évaluations du groupe pouvant être remplies 
    <span style="font-weight: 300"> 
        (<?= count($evaluations); ?>)
    </span>
</h5>

<div class="space"></div>

<? if (empty($evaluations)) : ?>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucune évaluation ne pouvant être remplie pour ce groupe

<? else : ?>

    <table id="evaluations" class="table admin-table">
        <tr style="background: #fff">
            <th style="width: 80px; text-align: center">Cours</th>
            <th style="width: 90px; text-align: center">Référence</th>
            <th>Titre de l'évaluation (Eval ID)</th>
            <th style="width: 130px">
                Enseignant
                <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 175px; text-align: center">
                Mise en ligne
                <span class="tri-button" data-clef="clef_tri_miseenligne" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
        </tr>

        <? 
            foreach($evaluations as $e) :
        ?>
            <tr data-clef_tri_nom="<?= strtolower(strip_accents($e['nom'] . $e['prenom'])); ?>"
                data-clef_tri_miseenligne="<?= $e['ajout_epoch']; ?>">
                <td style="text-align: center"><?= $e['cours_code_court']; ?></td>
                <td class="mono" style="text-align: center">
                    <a href="<?= base_url() . 'evaluation/previsualisation/' . $e['evaluation_id'] . '/etudiant'; ?>" target="_blank">
                        <?= $e['evaluation_reference']; ?>
                    </a>
                </td>
                <td>
                    <?= ellipsize($e['evaluation_titre'], 30); ?>
                    
                    (<a href="<?= base_url() . 'evaluations/editeur/'. $e['evaluation_id']; ?>" target="_blank"><?= $e['evaluation_id']; ?></a>)

                    <?
                    /* ----------------------------------------
                     *
                     * Inscription requise
                     *
                     * ---------------------------------------- */ ?>

                    <span style="margin-left: 5px"></span>

                    <? if ($e['inscription_requise']) : ?>

                        <svg data-toggle="tooltip" title="Inscription requise" xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px" fill="#FF6F00" class="bi-xs bi-person-check-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                            <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>

                    <? else : ?>

                        <svg data-toggle="tooltip" title="Inscription non requise" xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px" width="16" height="16" fill="#aaa" class="bi-xs bi-person-x-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z"/>
                        </svg>

                    <? endif; ?>

                    <span style="margin-right: 5px"></span>

                    <? if ($e['cacher']) : ?>
                        <span class="badge badge-pill badge-primary" style="font-weight: 300">C</span>
                    <? endif; ?>

                    <? if ($e['formative']) : ?>
                        <span class="badge badge-pill badge-dark" style="font-weight: 300">F</span>
                    <? endif; ?>
        
                    <? 
                    /* --------------------------------------------------------
                     *
                     * Filtres
                     *
                     * -------------------------------------------------------- */ ?>

                    <? if ($e['filtre_enseignant']) : ?>

                        <span class="badge badge-pill" style="background: #7986CB; color: #fff">
                            <i class="fa fa-sliders"></i>
                            <span style="font-weight: 300">E</span>
                        </span>

                    <? elseif ($e['filtre_cours']) : ?>

                        <span class="badge badge-pill" style="background: #7986CB; color: #fff">
                            <i class="fa fa-sliders"></i>
                            <span style="font-weight: 300">C</span>
                        </span>

                    <? elseif ($e['filtre_groupe']) : ?>

                        <span class="badge badge-pill" style="background: #7986CB; color: #fff">
                            <i class="fa fa-sliders"></i>
                            <span style="font-weight: 300">G (<?= $e['filtre_groupe']; ?>)</span>
                        </span>

                    <? elseif ($e['filtre_enseignant_autorisation']) : ?>

                        <span class="badge badge-pill" style="background: #7986CB; color: #fff">
                            <i class="fa fa-sliders"></i>
                            <span style="font-weight: 300">E＊</span>
                        </span>

                    <? elseif ($e['filtre_cours_autorisation']) : ?>

                        <span class="badge badge-pill" style="background: #7986CB; color: #fff">
                            <i class="fa fa-sliders"></i>
                            <span style="font-weight: 300">C＊</span>
                        </span>

                    <? elseif ($e['filtre_groupe_autorisation']) : ?>

                        <span class="badge badge-pill" style="background: #7986CB; color: #fff">
                            <i class="fa fa-sliders"></i>
                            <span style="font-weight: 300">G (<?= $e['filtre_groupe_autorisation']; ?>) ＊</span>
                        </span>

                    <? endif; ?>

                    <? 
                    /* --------------------------------------------------------
                     *
                     * Planification
                     *
                     * -------------------------------------------------------- */ ?>

                    <? if ($e['debut_epoch'] && $e['debut_epoch'] > $this->now_epoch) : ?>
                        <span class="badge badge-pill" style="padding-bottom: 3px; background: #4CAF50; color: #fff; font-weight: 300"
                            data-toggle="tooltip"
                            data-title="Début de l'évaluation">
                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 1px; height: 8px; width: 8px;" fill="currentColor" class="bi-arrow-bar-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L12.293 7.5H6.5A.5.5 0 0 0 6 8zm-2.5 7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5z"/>
                            </svg>
                            <?= date('Y-m-d H:i', $e['debut_epoch']); ?>
                        </span>
                    <? endif; ?>

                    <? if ($e['fin_epoch'] && $e['fin_epoch'] > $this->now_epoch) : ?>
                        <span class="badge badge-pill" style="padding-bottom: 3px; background: crimson; color: #fff; font-weight: 300"
                            data-toggle="tooltip"
                            data-title="Fin automatique de l'évaluation">
                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 1px; width: 8px; height: 8px;" fill="currentColor" class="bi-x-circle" viewBox="0 0 16 16">
                              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg> 
                            <?= date('Y-m-d H:i', $e['fin_epoch']); ?>
                        </span>

                    <? endif; ?>

                    <? if ( ! empty($e['temps_limite']) && $e['temps_limite'] > 0) : ?>
                        <span class="badge badge-pill" style="padding-bottom: 3px; background: #1565C0; color: #fff; font-weight: 300"
                            data-toggle="tooltip"
                            data-title="Temps limite">
                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-right: 0px; width: 10px; height: 10px" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                              <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                            </svg>
                                <?= $e['temps_limite']; ?> min<?= $e['temps_limite'] > 1 ? 's' : ''; ?>
                        </span>
                    <? endif; ?>

                </td>
                <td>
                    <a href="<?= base_url() . 'admin/enseignant/' . $e['enseignant_id']; ?>">
                        <?= ellipsize(substr($e['prenom'], 0, 1) . '. ' . $e['nom'], 30); ?>
                    </a>
                </td>
                <td class="mono" style="text-align: center">
                    <?= date_humanize($e['ajout_epoch'], TRUE); ?>
                </td>
            </tr>

        <? endforeach; ?>

    </table>
<? endif; ?>
