<?
/* ----------------------------------------------------------------------------
 *
 * Administration > Systeme > Etudiants
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-system-etudiants-recherche" class="mb-4">

        <script src="<?= base_url() . 'assets/js/recherche_admin.js?' . $this->now_epoch; ?>"></script>

        <div id="bienvenue-recherche" class="input-group">
            <input id="recherche-requete" type="text" class="form-control" placeholder="Rechercher un étudiant" name="requete">
            <div class="input-group-append">
                <span class="input-group-text">
                    <span class="en-attente"><i class="fa fa-search"></i></span> 
                    <span class="en-precision d-none" style="cursor: pointer; padding-left: 1px; padding-right: 1px;">✕</span>
                    <span class="en-recherche d-none"><i class="fa fa-refresh fa-spin"></i></span>
                </span>
            </div>
        </div>

        <?
        /* --------------------------------------------------------------------
         *
         * Resultats de la recherche
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="recherche-resultats">

            <div id="recherche-resultats-contenu">
            </div>

        </div>
</div>

<div id="admin-systeme-etudiants">

<h5>Les derniers étudiants inscrits (<?= count($etudiants) > 0 ? count($etudiants) - 1 : 0; ?>)</h5> 

<div class="space"></div>

<? if (empty($etudiants)) : ?>

    <div class="space"></div>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> Aucun étudiant inscrit

<? else : ?>

    <table id="etudiants" class="table admin-table" style="font-size: 0.85em">

        <tr style="background: inherit">
            <th style="width: 80px; text-align: center">
                ID
                <span class="tri-button" data-clef="clef_tri_etudiant_id"><i class="fa fa-sort-numeric-asc" style="margin-left: 5px"></i></span>
            </th>
            <th>
                Nom
                <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 130px; text-align: center">
                Soumissions
                <span class="tri-button" data-clef="clef_tri_soumissions" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 180px">
                Date d'inscription
                <span class="tri-button" data-clef="clef_tri_inscription" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 180px">
                Dernière activité
                <span class="tri-button" data-clef="clef_tri_activite" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 120px; text-align: center">
                Activité
                <span class="tri-button" data-clef="clef_tri_compteurs" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
            </th>
            <th style="width: 20px">
                <svg viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                  <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
            </td>
        </tr>

        <? 
            $activite_totale = 0;

            foreach($etudiants as $e) : 

                if ($e['test']) continue;

                $activite_totale += $e['activite_compteur']; 
        ?>

            <tr data-clef_tri_etudiant_id="<?= $e['etudiant_id']; ?>"
                data-clef_tri_nom="<?= strip_accents($e['nom'] . $e['prenom']); ?>"
                data-clef_tri_inscription="<?= $e['inscription_epoch']; ?>"
                data-clef_tri_activite="<?= $e['derniere_activite_epoch']; ?>"
                data-clef_tri_soumissions="<?= array_key_exists($e['etudiant_id'], $etudiants_soumissions) ? $etudiants_soumissions[$e['etudiant_id']] : 0; ?>"
                data-clef_tri_compteurs="<?= $e['activite_compteur']; ?>">
                <td style="text-align: center"><?= $e['etudiant_id']; ?></td>
                <td>
                    <a href="<?= base_url() . 'admin/etudiant/' . $e['etudiant_id']; ?>"><?= $e['prenom'] . ' ' . mb_strtoupper($e['nom']); ?></a>

                    <span data-toggle="tooltip" title="<?= $e['courriel']; ?>" style="margin-left: 5px; cursor: pointer">
                        <i class="fa fa-envelope-o"></i>
                    </span>

                    <? if ($e['actif']) : ?>
                        <i class="fa fa-check-circle" style="margin-left: 5px; color: limegreen"></i>
                    <? else : ?>

                    <? endif; ?>
                </td>
                <td style="text-align: center"><?= array_key_exists($e['etudiant_id'], $etudiants_soumissions) ? $etudiants_soumissions[$e['etudiant_id']] : 0; ?></td>
                <td class="mono"><?= $e['inscription_date']; ?></td>
                <td class="mono"><?= empty($e['derniere_activite_epoch']) ? '' : date_humanize($e['derniere_activite_epoch'], TRUE); ?></td>
                <td style="text-align: center"><?= $e['activite_compteur']; ?></td>
                <td style="text-align: right">
                    <a target="_blank" href="<?= base_url() . 'admin/usurper/etudiant/' . $e['etudiant_id']; ?>">
                        <svg viewBox="0 0 16 16" class="bi-xxs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                          <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                    </a>
                </td>
            </tr>

        <? endforeach; ?>
    </table>

<? endif; ?>

    <div class="space"></div>

    Activité totale des étudiants : <?= $activite_totale; ?>

</div> <!-- #admin-systeme-etudiants -->
