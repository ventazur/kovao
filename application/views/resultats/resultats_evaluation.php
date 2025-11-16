<?
/* ============================================================================
 *
 * RESULTATS D'UNE EVALUATION
 *
 * ============================================================================ */ ?>

<link href="<?= base_url() . 'assets/css/resultats4.css?' . $this->now_epoch; ?>" rel="stylesheet">
<script src="<?= base_url() . 'assets/js/resultats4.js?' . $this->now_epoch; ?>"></script>

<div id="resultats-evaluation">
<div class="container-fluid">

<?
 /* ---------------------------------------------------------------------------
  *
  * Titre
  *
  * --------------------------------------------------------------------------- */ ?>

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-9 col-xl-7">

        <? if ($lab) : ?>

            <h3>Résultats d'un laboratoire</h3>

        <? else : ?>

            <h3>Résultats d'une évaluation</h3>

        <? endif; ?>

    </div>
    <div class="col-sm-3 col-xl-3">
        <div class="float-sm-right mt-2 mt-sm-0">

            <button class="btn btn-outline-secondary" type="submit" name="action" onclick="history.back()" type="submit">
                <i class="fa fa-undo" style="margin-right: 3px"></i> 
                Retour aux résultats
            </button>

        </div>

    </div> <? // .col-md-3 ?>
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <? // .row ?>

<div class="hspace"></div>

<?
// --------------------------------------------------------------------
//
// Cours
//
// -------------------------------------------------------------------- */ ?>

<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <? 
            $cours    = array_pop($cours_raw); 
            $cours_id = $cours['cours_id'];
        ?>

        <div class="evaluation-cours-titre text-nowrap text-truncate">

            <?= $cours['cours_nom'] . ' (' . $cours['cours_code'] . ')'; ?>

        </div>

        <div class="resultats-cours-contenu">

        <?
        // ------------------------------------------
        //
        // Evaluations
        //
        // ------------------------------------------ */ ?>

        <? foreach($evaluations_eleves as $evaluation_id => $groupes) : ?>

            <? if ($evaluations[$evaluation_id]['cours_id'] != $cours_id) : continue; endif; ?>

            <div class="evaluation-titre text-nowrap text-truncate">

                <?= $evaluations[$evaluation_id]['evaluation_titre']; ?>

            </div>

            <div class="resultats-evaluation-contenu">

            <?
            // ------------------------------------------
            //
            // Groupes
            //
            // ------------------------------------------ */ ?>

            <?  // Tous les groupes confondus
                $soumission_ids_toutes = array(); 
                $soumission_references_toutes = array(); 
                $points_totaux_totaux = 0;

                $csv = NULL;
            ?>

            <? foreach($groupes as $cours_groupe => $groupe) : ?>

                <? $soumission_ids = array(); ?>
                <? $soumission_references = array(); ?>

                <? $nb_evaluations = 0; $points_totaux = 0; ?>

                <? $csv_groupe = NULL; ?>

                <div id="evaluation<?= $evaluation_id; ?>groupe<?= $cours_groupe; ?>" class="resultats-groupes expanded soumissions-liste-toggle">
                    <div class="row" style="font-family: Lato; font-weight: 300">
                        <div class="col-6 resultats-groupes-groupe">
                        <? if ($cours_groupe && $cours_groupe != 999) : ?>
                            Groupe <?= $cours_groupe; ?>
                        <? else : ?>
                            Groupe inconnu
                        <? endif; ?>
                        </div>
                        <div class="col-6 soumissions-liste-toggle-btn" style="text-align: right">
                            <span style="margin-right: 10px"><?= count($groupe); ?> soumission<?= count($groupe) > 1 ? 's' : ''; ?></span>
                            <span class="expand d-none"><i class="fa fa-plus-square-o fa-lg"></i></span>
                            <span class="collap"><i class="fa fa-minus-square-o fa-lg"></i></span>
                        </div>
                    </div>
                </div>

                <div class="soumissions soumissions-liste">
                    <table class="soumissions table table-sm" style="font-size: 0.9em;">
                        <thead>
                            <tr>
                                <? if ($lab) : ?>
                                    <th scope="col" class="d-none d-md-table-cell" style="width: 60px;">
                                        Place
                                        <? // <span class="tri-button" data-clef="clef_tri_place"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span> ?>
                                    </th>
                                <? endif; ?>
                                <th scope="col">
                                    Prénom et Nom 
                                    <? if ( ! $cours_groupe || $cours_groupe != 999) : ?>
                                        <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
                                    <? endif; ?>
                                </th>
                                <th style="width: 200px; text-align: right;" scope="col">
                                    Résultat 
                                    <span class="tri-button" data-clef="clef_tri_resultat" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                                </th>
                                <th scope="col" style="width: 190px; text-align: center">
                                    Date de remise 
                                    <span class="tri-button" data-clef="clef_tri_remise"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span>
                                </th>
                                <? if ( ! $lab) : ?>
                                    <th class="d-none d-md-table-cell" style="width: 120px; text-align: center" scole="col">
                                        Durée
                                        <span class="tri-button" data-clef="clef_tri_duree"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span>
                                    </th>
                                <? endif; ?>
                                <? if ($lab) : ?>
                                    <th class="d-none d-md-table-cell" style="width: 90px; text-align: center;" scole="col">
                                        Précorr.
                                        <? // <span class="tri-button" data-clef="clef_tri_precorrections"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span> ?>
                                    </th>
                                <? endif; ?>
                                <th scole="col" class="d-none d-md-table-cell" style="width: 120px; text-align: center">Référence</th>
                                <th class="d-none d-xl-table-cell" style="width: 90px; text-align: center" scope="col">
                                    Vues
                                    <? if ( ! $lab) : ?>
                                        <span class="tri-button" data-clef="clef_tri_vue" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                                    <? endif; ?>
                                </th>
                                <th class="d-none d-md-table-cell" style="width: 75px; text-align: center" scope="col">Visible</th>
                                <th class="d-none d-md-table-cell" style="width: 120px; text-align: right" scope="col">Opérations</th>
                                <th class="d-md-none" style="width: 5px"></th>
                            </tr>
                        </thead>

                        <?
                        // ------------------------------------------
                        //
                        // Soumissions
                        //
                        // ------------------------------------------ */ ?>

                        <tbody>

                        <? $eleves_remis_nda = array(); ?>

                        <? foreach($groupe as $soumission_id) :

                                $s = $soumissions[$soumission_id];

                                // Indexer les eleves ayant remis leur evaluation
                                $eleves_remis_nda[] = $s['numero_da'];

                                $soumission_ids[] = $s['soumission_id'];
                                $soumission_references[] = $s['soumission_reference'];
                                
                                // Ajustements
                                $ajustements = ! empty($s['ajustements_data']) ? unserialize($s['ajustements_data']) : array();
                                $points_obtenus = array_key_exists('total', $ajustements) ? $ajustements['total'] : $s['points_obtenus'];

                                $nb_evaluations++;
                                $points_totaux += $points_obtenus;

                                // Tous les groupes confondus
                                $soumission_ids_toutes[] = $s['soumission_id'];
                                $soumission_references_toutes[] = $s['soumission_reference'];
                                $points_totaux_totaux += $points_obtenus;
                                $points_total_evaluation = $s['points_evaluation'];

                                // Laboratoire
                                if ($lab) 
                                {
                                    $lab_data = json_decode($s['lab_data'], TRUE);
                                }

                                if ($s['evaluation_id'] != $evaluation_id) continue;

                                //
                                // Verifier s'il est possible de consulter l'evaluation corrigee
                                //

                                $permettre_visualisation     = ($s['permettre_visualisation'] && ($s['permettre_visualisation_expiration'] == 0 || $s['permettre_visualisation_expiration'] > $this->now_epoch)) ? TRUE : FALSE;
                                $permettre_voir_note         = ($s['permettre_visualisation'] && $s['permettre_visualisation_expiration'] != 0 && $s['permettre_visualisation_expiration'] < $this->now_epoch) ? TRUE : FALSE;
                                $permettre_visualisation_lim = ($permettre_visualisation && $s['permettre_visualisation_expiration'] != 0) ? "jusqu'au " . date_french_full($s['permettre_visualisation_expiration'], TRUE) : NULL;
                            
                                $csv        .= $s['prenom_nom'] . ';' . number_format($points_obtenus, 2, ',', '') . "\n";
                                $csv_groupe .= $s['prenom_nom'] . ';' . number_format($points_obtenus, 2, ',', '') . "\n";
                            ?>

                            <tr class="soumission-item <?= $s['non_terminee'] ? 'soumission-non-terminee' : ''; ?>"
                                data-clef_tri_place="<?= $lab_data['lab_place'] ?? 0; ?>"
                                data-clef_tri_nom="<?= (array_key_exists($s['numero_da'], $numeros_da) ? strtolower(strip_accents($numeros_da[$s['numero_da']])) : 'zzz'); ?>"
                                data-clef_tri_remise="<?= $s['soumission_epoch']; ?>"
                                data-clef_tri_resultat="<?= ($s['points_evaluation'] > 0 ? ($points_obtenus / $s['points_evaluation'] * 100) : 0); ?>"
                                data-clef_tri_duree="<?= $s['soumission_epoch'] - $s['soumission_debut_epoch']; ?>"
                                data-clef_tri_precorrections="<?= $lab_data['lab_precorrections']['compte'] ?? 0; ?>"
                                data-clef_tri_vue="<?= $s['vues']; ?>"
                                data-soumission_reference="<?= $s['soumission_reference']; ?>">

                                <? if ($lab) : ?>

                                    <td scope="row" class="d-none d-md-table-cell text-center">
                                        <?= $lab_data['lab_place']; ?>                                        
                                    </td>

                                <? endif; ?>

                                <td scope="row">

                                    <? if ($lab) : ?>

                                        <?
                                            $lab_partenaire2_nom = NULL;
                                            $lab_partenaire3_nom = NULL;

                                            // retrocompatibilite
                                            if (array_key_exists('lab_partenaire2', $lab_data) && ! empty($lab_data['lab_partenaire2']))
                                            {
                                                $lab_partenaire2_nom = $lab_data['lab_partenaire2'];
                                            }

                                            // retrocompatibilite
                                            if (array_key_exists('lab_partenaire3', $lab_data) && ! empty($lab_data['lab_partenaire3']))
                                            {
                                                $lab_partenaire3_nom = $lab_data['lab_partenaire3'];
                                            }

                                            if (array_key_exists('lab_partenaire2_nom', $lab_data) && ! empty($lab_data['lab_partenaire2_nom']))
                                            {
                                                $lab_partenaire2_nom = $lab_data['lab_partenaire2_nom'];
                                            }

                                            if (array_key_exists('lab_partenaire3_nom', $lab_data) && ! empty($lab_data['lab_partenaire3_nom']))
                                            {
                                                $lab_partenaire3_nom = $lab_data['lab_partenaire3_nom'];
                                            }
                                        ?>

                                        <div>
                                            <i class="bi bi-person-circle"></i>
                                            <?= $s['prenom_nom']; ?>
                                        </div>

                                        <? if ($lab_partenaire2_nom) : ?>
                                            <div>
                                                <i class="bi bi-person-fill"></i>
                                                <?= $lab_partenaire2_nom; ?>
                                            </div>
                                        <? endif ;?>
                                
                                        <? if ($lab_partenaire3_nom) : ?>
                                            <div>
                                                <i class="bi bi-person-fill"></i>
                                                <?= $lab_partenaire3_nom; ?>
                                            </div>
                                        <? endif ;?>

                                    <? else: ?>

                                        <? if ( ! empty($s['etudiant_id'])) : ?>
                                            <a style="cursor: pointer" href="<?= base_url() . 'etudiant/' . $s['etudiant_id']; ?>" target="_blank"
                                                data-trigger="hover" 
                                                data-toggle="popover" 
                                                data-html="true"
                                                data-placement="top"
                                                data-content="<?= (empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']) . ' : ' . $s['numero_da']; ?>">
                                                <?= $s['prenom_nom']; ?></a>
                                            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -2px; margin-left: 2px" width="16" height="16" fill="currentColor" class="bi-xs bi-person" viewBox="0 0 16 16">
                                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                            </svg>
                                        <? else : ?>
                                            <span style="cursor: pointer"
                                                data-trigger="hover" 
                                                data-toggle="popover" 
                                                data-html="true"
                                                data-placement="top"
                                                data-content="<?= (empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']) . ' : ' . $s['numero_da']; ?>">
                                                <?= $s['prenom_nom']; ?>
                                            </span>
                                        <? endif; ?>

                                    <? endif; // if lab ?>
                                </td>

                                <td style="text-align: right">
                                    <? if ($s['points_evaluation'] > 0) : ?>
                                        <?= my_number_format($points_obtenus) . ' / ' . my_number_format($s['points_evaluation']); ?>
                                        (<?= number_format($points_obtenus / $s['points_evaluation'] * 100); ?>%)
                                    <? endif; ?>
                                </td>

                                <td class="mono" style="text-align: center">

                                    <? if ($s['non_terminee']) : ?>
                                        <span style="color: crimson" data-toggle="tooltip" title="Cette évaluation a été terminée par l'enseignant.">
                                            <?= $s['soumission_date']; ?>
                                        </span>
                                    <? else : ?>
                                        <span>
                                            <?= $s['soumission_date']; ?>
                                        </span>
                                    <? endif; ?>
                                </td>

                                <?
                                /* --------------------------------------------
                                 *
                                 * Duree
                                 *
                                 * -------------------------------------------- */ ?>
                                <? if ( ! $lab) : ?>
                                    <td class="d-none d-md-table-cell mono" style="text-align: center">
                                        <? 
                                            $str = NULL;

                                            if (is_array($s['extra']))
                                            {
                                                if (array_key_exists('temps_redaction_str', $s['extra'])) 
                                                {
                                                    $str .= (empty($str) ? '' : '<br />');

                                                    $str .= 'Temps réel en rédaction : ' . $s['extra']['temps_redaction_str'];
                                                }

                                                if (array_key_exists('temps_ecoule_str', $s['extra'])) 
                                                {
                                                    $str .= (empty($str) ? '' : '<br />');

                                                    $str .= 'Temps écoulé  affiché : ' . $s['extra']['temps_ecoule_str'];
                                                }
                                            }
                                        ?>
                                        <span style="cursor: pointer"
                                            data-trigger="hover" 
                                            data-toggle="popover" 
                                            data-html="true"
                                            data-placement="top"
                                            data-content="<?= $str; ?>">
                                            <?= $s['duree']; ?>
                                        </span>
                                    </td>
                                <? endif; ?>

                                <?
                                /* --------------------------------------------
                                 *
                                 * Precorrections
                                 *
                                 * -------------------------------------------- */ ?>
                                <? if ($lab) : ?>
                                    <td class="d-none d-md-table-cell mono" style="text-align: center">
                                        <?= $lab_data['lab_precorrections']['compte'] ?? 0; ?>
                                    </td>
                                <? endif; ?>

                                <?
                                /* --------------------------------------------
                                 *
                                 * Reference
                                 *
                                 * -------------------------------------------- */ ?>
                                <td class="d-none d-md-table-cell mono" style="text-align: center;">
                                    <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>" target="_blank">
                                        <span style="cursor: pointer"
                                            data-trigger="hover" 
                                            data-toggle="popover" 
                                            data-html="true"
                                            data-placement="top"
                                            data-content="Empreinte : <?= $s['empreinte']; ?>">
                                            <?= $s['soumission_reference']; ?>
                                        </span>
                                    </a>
                                </td>

                                <td class="d-none d-xl-table-cell" style="text-align: center">

									<? if (empty($s['etudiant_id']) && $s['vues'] >= $this->config->item('corrections_max_vues')) : ?>
                                        <div class="remettre-a-zero" 
                                             style="font-size: 0.9em; font-weight: 300; color: crimson; cursor: pointer"
                                             data-soumission_id="<?= $s['soumission_id']; ?>"
                                             data-soumission_reference="<?= $s['soumission_reference']; ?>"
                                             data-toggle="modal" 
                                             data-target="#modal-reset-vues">
                                            <?= $s['vues']; ?>
                                        </div>
                                    <? else : ?>
                                        <?= $s['vues']; ?>
                                    <? endif; ?>

                                </td>

                                <td class="d-none d-md-table-cell" style="text-align: center; margin-bottom: -5px;">

                                    <div style="font-size: 0.9em; font-weight: 300; cursor: pointer"
                                        data-soumission_ids="<?= htmlspecialchars(serialize(array($s['soumission_id']))); ?>"
                                        data-soumission_references="<?= htmlspecialchars(serialize(array($s['soumission_reference']))); ?>"

										<? if ($s['permettre_visualisation_expiration'] != 0) : ?>

											data-date="<?= ! empty($s['permettre_visualisation_expiration']) ? date_humanize($s['permettre_visualisation_expiration']) : NULL; ?>"
											data-heure="<?= ! empty($s['permettre_visualisation_expiration']) ? hour_humanize($s['permettre_visualisation_expiration']) : NULL; ?>"

										<? endif; ?>

                                        data-toggle="modal" 
                                        data-target="#modal-soumission-visibilite">

                                        <? if ($permettre_visualisation) : ?>

                                            <svg viewBox="0 0 16 16" class="bi bi-eye-fill" fill="limegreen" xmlns="http://www.w3.org/2000/svg" 
                                                 data-toggle="popover" 
                                                 data-content="<?= $permettre_visualisation_lim; ?>">
                                              <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                              <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                            </svg>

                                        <? else : ?>

                                            <? if ($permettre_voir_note) : ?>

                                                <svg viewBox="0 0 16 16" class="bi bi-eye" fill="limegreen" xmlns="http://www.w3.org/2000/svg"
                                                 data-toggle="popover" 
                                                 data-content="L'étudiant peut seulement voir sa note.">
                                                  <path fill-rule="evenodd" d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.134 13.134 0 0 0 1.66 2.043C4.12 11.332 5.88 12.5 8 12.5c2.12 0 3.879-1.168 5.168-2.457A13.134 13.134 0 0 0 14.828 8a13.133 13.133 0 0 0-1.66-2.043C11.879 4.668 10.119 3.5 8 3.5c-2.12 0-3.879 1.168-5.168 2.457A13.133 13.133 0 0 0 1.172 8z"/>
                                                  <path fill-rule="evenodd" d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                </svg>

                                            <? else  : ?>

                                                <svg viewBox="0 0 16 16" class="bi bi-eye-slash" fill="crimson" xmlns="http://www.w3.org/2000/svg">
                                                  <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                                  <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                                  <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709z"/>
                                                  <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                </svg>

                                            <? endif; ?>

                                        <? endif; ?>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell" style="padding-top: 9px; text-align: right">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opérations</button>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item" data-toggle="modal" data-target="#modal-soumission-visibilite" href="#"
                                           data-soumission_ids="<?= htmlspecialchars(serialize(array($s['soumission_id']))); ?>"
                                           data-soumission_references="<?= htmlspecialchars(serialize(array($s['soumission_reference']))); ?>"

                                            <? if ($s['permettre_visualisation_expiration'] > $this->now_epoch) : ?>
                                                data-date="<?= ! empty($s['permettre_visualisation_expiration']) ? date_humanize($s['permettre_visualisation_expiration']) : NULL; ?>"
                                                data-heure="<?= ! empty($s['permettre_visualisation_expiration']) ? hour_humanize($s['permettre_visualisation_expiration']) : NULL; ?>"
                                            <? endif; ?>
                                            >

                                            <i class="fa fa-eye" style="margin-right: 5px"></i>
                                            Changer la visibilité
                                        </a>

                                        <? if ($s['corrections_manuelles']) : ?>

                                            <a class="dropdown-item" target="_blank" href="<?= base_url() . 'corrections/corriger/' . $s['soumission_reference']; ?>">
                                                <i class="fa fa-pencil-square" style="margin-right: 5px"></i> Recorriger
                                            </a>

                                        <? endif; ?>

                                        <a class="dropdown-item" target="_blank" href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>">
                                            <i class="fa fa-external-link" style="margin-right: 5px"></i> Consulter
                                        </a>

                                        <div role="separator" class="dropdown-divider"></div>

                                        <a class="dropdown-item" href="#" data-soumission_id="<?= $s['soumission_id']; ?>" data-toggle="modal" data-target="#modal-effacer-soumission">
                                            <i class="fa fa-trash" style="color: crimson; margin-right: 5px;"></i>
                                            <span style="color: crimson">Effacer cette soumission</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="d-md-none" style="vertical-align: middle">
                                    <a href="<?= base_url() . 'consulter/' . $s['soumission_reference']; ?>">
                                        <i class="fa fa-lg fa-angle-right"></i>
                                    </a>
                                </td>
                            </tr>

                        <? endforeach; // soumissions ?>

                        <? 
                        // 
                        // Ajouter les eleves n'ayant pas remis leur evaluation 
                        // 
                        ?>

                        <? if ( ! $lab && ! empty($eleves_nda[$s['cours_id']][$cours_groupe])) : ?>
                            
                            <? foreach($eleves_nda[$s['cours_id']][$cours_groupe] as $e) : ?>

                                <? if ( ! in_array($e['numero_da'], $eleves_remis_nda)) : ?>

                                    <tr class="soumission-item-manquant"
                                        data-clef_tri_nom="<?= mb_strtolower($e['nom']) . ', ' . mb_strtolower($e['prenom']); ?>"
                                        data-clef_tri_remise="9999999999"
                                        data-clef_tri_resultat="0"
                                        data-clef_tri_duree="9999999999"
                                        data-clef_tri_vue="0">

                                        <td style="color: crimson">
                                            <?= $e['prenom'] . ' ' . $e['nom']; ?>
                                        </td>
                                        <td style="text-align: right; color: #999">
                                            non remis
                                        </td>
                                        <td colspan="6">
                                        </td>
                                    </tr>

                                <? endif ?>

                            <? endforeach; ?>

                        <? endif; ?>

                        </tobdy>

                        <tfoot>
                            <tr>
                                <td colspan="<?= $lab ? 9 : 8; ?>">

                                    <div class="row"> 

                                        <div class="col-xs-12 col-md-6">

                                            <span class="btn btn-warning btn-sm">
                                                Moyenne :
                                                <? // Je prends simplement le dernier resultat pour determiner le nombre de points de l'evaluation ?>
                                                <span style="margin-left: 5px"><?= my_number_format($points_totaux / $nb_evaluations, 2, FALSE) . ' / ' . my_number_format($s['points_evaluation']); ?></span>
                                                <span style="margin-left: 5px">
                                                    <? if ($s['points_evaluation'] > 0) : ?>
                                                        (<?= number_format(($points_totaux/$nb_evaluations)/$s['points_evaluation']*100) . '%'; ?>)
                                                    <? else : ?>
                                                        (0%)
                                                    <? endif; ?>
                                                </span>
                                            </span>

                                            <? $requete = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 5)), 0, 5); ?>

                                            <a class="stats-evaluation btn btn-sm btn-outline-dark" 
                                                href="<?= base_url() . 'stats/evaluation/' . $evaluation_id . '/semestre/' . $semestre_id . '/groupe/' . $cours_groupe . '/req/' . $requete; ?>" 
                                                data-requete="<?= $requete; ?>"
                                                data-groupe_no="<?= $cours_groupe; ?>" 
                                                data-soumission_ids="<?= htmlspecialchars(serialize($soumission_ids)); ?>"
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Statistiques des évaluations du groupe">
                                                <i class="fa fa-stethoscope" aria-hidden="true" style="margin-right: 3px"></i>
                                                Statistiques
                                            </a>

                                            <div class="btn btn-sm btn-outline-dark copier-presse-papier" style="cursor: pointer;" 
                                                data-toggle="tooltip"
                                                data-title="Résultats en CSV dans le presse-papier"
                                                data-info="<?= $csv_groupe; ?>">
                                                <i class="fa fa-clipboard" style="margin-right: 3px"></i> 
                                                CSV
                                            </div>
                                            
                                        </div> <!-- .col-xs-12 -->
                            
                                        <div class="d-none d-lg-block col-md-6 text-sm-right">

                                            <div class="defilement-evaluation btn btn-sm btn-outline-dark" href=""
                                                data-groupe_no="<?= $cours_groupe; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Défilement des évaluations corrigées du groupe">
                                                <i class="fa fa-arrows-h" style="margin-right: 3px;"></i>
                                                Défilement
                                            </div>

											<div class="btn btn-sm btn-outline-dark"
												data-soumission_ids="<?= htmlspecialchars(serialize($soumission_ids)); ?>"
												data-toggle="modal" 
												data-target="#modal-soumissions-visibilite">
                                                <i class="fa fa-eye" style="margin-right: 3px"></i>
                                                Changer visiblité
											</div>

                                        </div> <!-- .col-md-6 -->

                                    </div> <!-- .row -->

                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div> <!-- .soumissions -->

            <? endforeach; // groupes ?>

            </div> <!-- .resultats-evaluation-contenu -->

        <? endforeach; // evaluations ?>

        <?
        /* --------------------------------------------------------------------
         *
         * Tous les groupes confondus, si plus d'un groupe
         *
         * -------------------------------------------------------------------- */ ?>

        <? if (count($groupes) > 1) : ?>

            <div class="resultats-tous-groupes">
                <div class="row" style="font-family: Lato; font-weight: 300">

                    <div class="col-6 resultats-groupes-groupe">
                        Tous les groupes
                    </div>

                    <div class="col-6 soumissions-liste-toggle-btn" style="text-align: right">
                        <span style="margin-right: 10px"><?= count($soumission_ids_toutes); ?> soumission<?= count($soumission_ids_toutes) > 1 ? 's' : ''; ?></span>
                    </div>
                </div>
            </div>

            <div class="soumissions" style="background: #f7f7f7; padding: 5px; border-color: #388E3C;">

                <div class="row">

                    <div class="col-8"> 

                        <span class="btn btn-warning btn-sm">
                            Moyenne cumulative :
                            <? // Je prends simplement le dernier resultat pour determiner le nombre de points de l'evaluation ?>
                            <span style="margin-left: 5px"><?= my_number_format($points_totaux_totaux / count($soumission_ids_toutes), 2, FALSE) . ' / ' . my_number_format($points_total_evaluation); ?></span>
                            <span style="margin-left: 5px">
                                <? if ($s['points_evaluation'] > 0) : ?>
                                    (<?= number_format(($points_totaux_totaux/count($soumission_ids_toutes))/$points_total_evaluation*100) . '%'; ?>)
                                <? else : ?>
                                    (0%)
                                <? endif; ?>
                            </span>
                        </span>
                    
                        <? $requete = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', 5)), 0, 5); ?>

                        <a class="stats-evaluation btn btn-sm btn-outline-dark" 
                            href="<?= base_url() . 'stats/evaluation/' . $evaluation_id . '/semestre/' . $semestre_id . '/req/' . $requete; ?>" 
                            data-requete="<?= $requete; ?>"
                            data-groupe_no="<?= NULL; ?>" 
                            data-soumission_ids="<?= htmlspecialchars(serialize($soumission_ids_toutes)); ?>"
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="Statistiques des évaluations du groupe">
                            <i class="fa fa-stethoscope" aria-hidden="true" style="margin-right: 3px"></i>
                            Statistiques cumulatives
                        </a>

                        <div class="btn btn-sm btn-outline-dark copier-presse-papier" style="cursor: pointer;" 
                            data-toggle="tooltip"
                            data-title="Résultats en CSV dans le presse-papier"
                            data-info="<?= $csv; ?>">
                            <i class="fa fa-clipboard" style="margin-right: 3px"></i> 
                            CSV
                        </div>
                    </div>

                    <div class="col-4" style="text-align: right">

                        <div class="defilement-evaluation-toutes btn btn-sm btn-outline-dark" href=""
                            data-groupe_no="<?= $cours_groupe; ?>" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="Défilement des évaluations corrigées du groupe">
                            <i class="fa fa-arrows-h" style="margin-right: 3px;"></i>
                            Défilement
                        </div>

                        <div class="btn btn-sm btn-outline-dark"
                            data-soumission_ids="<?= htmlspecialchars(serialize($soumission_ids_toutes)); ?>"
                            data-toggle="modal" 
                            data-target="#modal-soumissions-visibilite">
                            <i class="fa fa-eye" style="margin-right: 3px"></i>
                            Changer visiblité
                        </div>

                    </div> <!-- .col-8 -->

                </div> <!-- .row -->

            </div>
        <? endif; ?>

        </div> <!-- .resultats-cours-contenu -->

    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

<div class="space"></div>

<?
 /* ---------------------------------------------------------------------------
  *
  * Confirmite de l'évaluation
  *
  * --------------------------------------------------------------------------- */ ?>

<div id="conformite" class="d-none d-lg-block d-xl-block" style="margin-top: 20px">
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <div style="margin-top: 10px; margin-bottom: 20px;">
           <h4>
                Conformité de l'évaluation
            </h4>
        </div>

        <div id="conformite-evaluation">

            <div id="conformite-evaluation-titre">

                Registre des différents moniteurs

            </div>
            <div id="conformite-evaluation-contenu">

                Nombre d'évaluations uniques : <?= count($evaluations2_unique); ?> <i class="fa fa-info-circle" style="margin-left: 5px; color: #aaa" data-toggle="tooltip" data-title="les mêmes questions sans considérer l'ordre"></i><br />
                Nombre de soumissions uniques : <?= count($soumissions2_unique); ?> <i class="fa fa-info-circle" style="margin-left: 5px; color: #aaa" data-toggle="tooltip" data-title="les mêmes questions sans considérer l'ordre, et les mêmes réponses"></i><br />

                <div class="hspace"></div>

                Nombre d'adresses IP différentes : <?= count($adresse_ips) ?: 'n/d'; ?><br />

            </div>

        </div> <!-- #conformite-evaluation -->

        <?
        /* ------------------------------------------------------------
         *
         * Moniteurs
         *
         * ------------------------------------------------------------ */

        if (
            count($soumissions) != count($evaluations2_unique)  ||
            count($soumissions) != count($soumissions2_unique)  ||
            count($soumissions) != count($adresse_ips)          ||
            $meme_ip_louche                                     ||
            $aide_externe_louche
        ) :

        ?>

        <div id="conformite-evaluation-precisions">

            <?
            /* ------------------------------------------------------------
             *
             * (DESACTIVEE)
             * Evaluations :
             * Les memes questions dans le meme ordre 
             *
             * ------------------------------------------------------------ */ ?>

            <? if (1 == 2 && count($soumissions) != count($evaluations_unique)) : ?>

                <? $this->load->view('resultats/_conformite_evaluation_unique'); ?>   

            <? endif; // evaluation unique ?>

            <?
            /* ------------------------------------------------------------
             *
             * Evaluations :
             * Les memes questions sans considerer l'ordre des questions
             *
             * ------------------------------------------------------------ */ ?>

            <? if (count($soumissions) != count($evaluations2_unique)) : ?>

                <? $this->load->view('resultats/_conformite_evaluation_unique_sans_ordre'); ?>   

            <? endif; // exactement meme evaluation ?>

            <?
            /* ------------------------------------------------------------
             *
             * (DESACTIVEE)
             * Soumissions :
             * Les memes questions dans le meme ordre, et les memes reponses
             *
             * ------------------------------------------------------------ */ ?>

            <? if (1 == 2 && count($soumissions) != count($soumissions_unique)) : ?>

                <? $this->load->view('resultats/_conformite_soumission_unique'); ?>   

            <? endif; // soumission unique ?>

            <?
            /* ------------------------------------------------------------
             *
             * Soumissions :
             * Les memes questions sans considere l'ordre, et les memes reponses
             *
             * ------------------------------------------------------------ */ ?>

            <? if (count($soumissions) != count($soumissions2_unique)) : ?>

                <? $this->load->view('resultats/_conformite_soumission_unique_sans_ordre'); ?>   

            <? endif; // soumission unique ?>

            <?
            /* ------------------------------------------------------------
             *
             * La meme adresse IP
             *
             * ------------------------------------------------------------ */ ?>

            <? if (count($adresse_ips) > 0 && count($soumissions) != count($adresse_ips)) : ?>

                <? $this->load->view('resultats/_conformite_adresse_ip'); ?>   

            <? endif; // meme adresse IP ?>

            <?
            /* ------------------------------------------------------------
             *
             * La meme adresse IP : LOUCHE
             *
             * ------------------------------------------------------------ */ ?>

            <? if ($meme_ip_louche) : ?>

                <? $this->load->view('resultats/_conformite_adresse_ip_louche'); ?>   

            <? endif; // meme adresse IP ?>

            <?
            /* ------------------------------------------------------------
             *
             * Aide externe possible
             *
             * ------------------------------------------------------------ */ ?>

            <? if ($aide_externe_louche) : ?>

                <? $this->load->view('resultats/_conformite_aide_externe'); ?>   

            <? endif; ?>

            <?
            /* ------------------------------------------------------------
             *
             * (DESACTIVEE)
             * Le meme fureteur
             *
             * ------------------------------------------------------------ */ ?>

            <? if (1 == 2 && count($fureteurs_unique) > 0 && count($soumissions) != count($fureteurs_unique)) : ?>

                <? $this->load->view('resultats/_conformite_fureteur_unique'); ?>   

            <? endif; // meme fureteur ?>

            <?
            /* ------------------------------------------------------------
             *
             * (DEACTIVEE)
             * La meme adresse IP utilisee par plusieurs etudiants pendant l'evaluation
             *
             * ------------------------------------------------------------ */ ?>

            <? if (1 == 2 &&  ! empty($activite) && $activite_louche) : ?>

                <? $this->load->view('resultats/_conformite_adresse_ip_activite'); ?>   

            <? endif; // activite ?>

            <?
            /* ------------------------------------------------------------
             *
             * (DEACTIVEE)
             * Le meme ordinateur
             *
             * ------------------------------------------------------------ */ ?>

            <? if (1 == 2 && count($ordinateurs_unique) != 0 && (count($soumissions) != count($ordinateurs_unique))) : ?>

                <? $this->load->view('resultats/_conformite_ordinateur_unique'); ?>   

            <? endif; // le meme ordinateur ?> 

        </div> <!-- #conformite-evaluation -->

        <? endif; // count ?>

        <?
        /* ------------------------------------------------------------
         *
         * Les documents reproduits
         *
         * ------------------------------------------------------------ */ ?>

        <? if ( ! empty($documents_verification)) : ?>

            <div class="conformite-evaluation-precisions-titre documents">

                Ces étudiants ont téléversés des <strong>documents reproduits</strong> :

            </div>

            <div class="conformite-evaluation-precisions-contenu documents">

                <div class="conformite-explications">
                    <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
                    Ces étudiants ont téléversé un document qui a été utilisé dans une évaluation d'un autre étudiant.
                </div>

                <? foreach($documents_verification as $sref => $s) : ?> 

                    <? foreach($s as $d) : ?>

                        <span class="conformite-noms">
                            <?= $d['origine_soumission_prenom_nom']; ?>
                            <? // $etudiants[$e_id]['prenom'] . ' ' . $etudiants[$e_id]['nom']; ?>
                        </span>

                    <? endforeach; ?>
                                    
                <? endforeach; ?>

            </div> <!-- .conformite-evaluation-precision-contenu -->
    
        <? endif; // documents_verification ?>

    </div> <!-- .col-sm-12 col-xl-10 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->
</div> <!-- #conformite -->

</div> <!-- /.container -->
</div> <!-- #resultats -->

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: EFFACER UNE SOUMISSION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-effacer-soumission" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-trash"></i> Effacer une évaluation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">

				<?= form_open(NULL, 
						array('id' => 'modal-effacer-soumission-form'), 
						array('soumission_id' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 50px; padding-bottom: 40px">

						Êtes-vous certain de vouloir effacer cette évaluation ?
						<br/ ><br />
						<i class="fa fa-exclamation-circle" style="color: crimson"></i> Cette opération est <strong>irrévocable</strong>.

					</div>

				</form>
				
      		</div>

			<div class="modal-footer">
        		<div id="modal-effacer-soumission-sauvegarde" class="btn btn-danger"><i class="fa fa-trash"></i> Effacer</div>
        		<div class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: RESET VUES
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-reset-vues" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-undo" style="margin-right: 5px"></i> Remettre à zéro les vues</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-reset-vues-form'), 
						array('soumission_id' => NULL, 'soumission_reference' => NULL)
					); ?>

					<div class="form-group col-md-12" style="text-align: center; padding-top: 10px; padding-bottom: 0px">

						Êtes-vous certain de vouloir remettre à zéro le nombre de vues de cette évalution corrigée ?

					</div>

				</form>
      		</div>

			<div class="modal-footer">
                <div id="modal-reset-vues-sauvegarde" class="btn btn-danger spinnable">
                    <i class="fa fa-undo" style="margin-right: 5px;"></i> 
                    Remettre à zéro
                    <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                </div>
                <div class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times" style="margin-right: 5px;"></i> 
                    Annuler
                </div>
      		</div>

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: VISIBLITE D'UNE SOUMISSION
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-soumission-visibilite" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-eye" style="margin-right: 5px"></i> Visiblité d'une soumission</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-soumission-visibilite-form'), 
                        array(
                            'soumission_ids' => NULL, 
                            'soumission_references' => NULL
                        )
					); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <div>Cette soumission sera visible jusqu'au (<strong>exclusivement</strong>) :</div>

                        <div class="mt-3 form-group">
                            <label for="modal-planifier-evaluation-date">Date</label>
                            <input type="date" class="form-control col-4" name="date" id="modal-soumission-visibilite-date">
                        </div>

                        <div class="mt-3 form-group">
                            <label for="modal-soumission-visibilite-heure">Heure</label>
                            <input type="time" class="form-control col-4" name="heure" id="modal-soumission-visibilite-heure">
                        </div>

                        <div style="font-size: 0.85em; color: #777; margin-top: -8px;">
                            <i class="fa fa-info-circle" style="margin-right: 3px"></i> Heure au format 24h
                        </div>
                    </div>

                    <div class="hspace"></div>
                    
                    <div class="form-group col-md-12" style="font-size: 0.9em">
                        <i class="fa fa-exclamation-circle" style-"margin-right: 3px"></i>
                        Il est facultatif d'entrer une date et une heure.
                    </div>

				</form>
      		</div>

            <div class="modal-footer">
                <div class="col">
                    <div id="modal-soumission-rendre-visible" class="btn btn-success spinnable">
                        <i class="fa fa-eye" style="margin-right: 3px;"></i> 
                        Rendre visible
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                </div>

                <div class="col" style="text-align: right">
                    <div id="modal-soumission-rendre-invisible" class="btn btn-danger spinnable">
                        <i class="fa fa-eye-slash" style="margin-right: 3px;"></i> 
                        Rendre invisible 
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
      		</div> <!-- .modal-footer -->

    	</div>
  	</div>
</div>

<?
/* -------------------------------------------------------------------------
 *
 * MODAL: VISIBLITE DE SOUMISSIONS (PLUSIEURS)
 *
 * ------------------------------------------------------------------------- */ ?>	

<div id="modal-soumissions-visibilite" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">

      		<div class="modal-header">
        		<h5 class="modal-title"><i class="fa fa-eye" style="margin-right: 5px"></i> Visiblité de soumissions</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
        		</button>
      		</div>

      		<div class="modal-body">
				<?= form_open(NULL, 
						array('id' => 'modal-soumissions-visibilite-form'), 
                        array(
                            'soumission_ids' => NULL, 
                            'soumission_references' => NULL
                        )
					); ?>

					<div class="form-group col-md-12" style="padding-top: 10px; padding-bottom: 0px">

                        <div>Ces soumissions seront visibles jusqu'au (<strong>exclusivement</strong>) :</div>

                        <div class="mt-3 form-group">
                            <label for="modal-planifier-evaluation-date">Date</label>
                            <input type="date" class="form-control col-4" name="date" id="modal-soumissions-visibilite-date">
                        </div>

                        <div class="mt-3 form-group">
                            <label for="modal-soumission-visibilite-heure">Heure</label>
                            <input type="time" class="form-control col-4" name="heure" id="modal-soumissions-visibilite-heure">
                        </div>

                        <div style="font-size: 0.85em; color: #777; margin-top: -8px;">
                            <i class="fa fa-info-circle" style="margin-right: 3px"></i> Heure au format 24h
                        </div>
                    </div>

                    <div class="hspace"></div>
                    
                    <div class="form-group col-md-12" style="font-size: 0.9em">
                        <i class="fa fa-exclamation-circle" style-"margin-right: 3px"></i>
                        Il est facultatif d'entrer une date et une heure.
                    </div>

				</form>
      		</div>

            <div class="modal-footer">
                <div class="col">
                    <div id="modal-soumissions-rendre-visible" class="btn btn-success spinnable">
                        <i class="fa fa-eye" style="margin-right: 3px;"></i> 
                        Rendre visible
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                </div>

                <div class="col" style="text-align: right">
                    <div id="modal-soumissions-rendre-invisible" class="btn btn-danger spinnable">
                        <i class="fa fa-eye-slash" style="margin-right: 3px;"></i> 
                        Rendre invisible 
                        <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                    </div>
                </div>
      		</div> <!-- .modal-footer -->

    	</div>
  	</div>
</div>
