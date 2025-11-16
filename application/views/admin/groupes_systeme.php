<?
/* ----------------------------------------------------------------------------
 *
 * Systeme > Groupes
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-systeme-groupes">

<h5>Les groupes</h5> 

<div class="space"></div>

<? if (empty($groupes) || ! is_array($groupes)) : ?>

    <i class="fa fa-exclamation-circle"></i> Il n'existe aucun groupe.

<? else : ?>

    <table class="table" style="font-size: 0.85em; border: 1px solid #ddd;">
        <thead>
            <tr>
                <th style="width: 170px">
                    Sous-domaine
                    <span class="tri-button" data-clef="clef_tri_sousdomaine"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
                </th>
                <th style="width: 250px">
                    École
                    <span class="tri-button" data-clef="clef_tri_nom"><i class="fa fa-sort-alpha-asc" style="margin-left: 5px"></i></span>
                </th>
                <th>Groupe</th>
                <th style="width: 60px; text-align: center">Actif</th>
                <th style="width: 100px; text-align: center">Enseignants</th>
                <th style="width: 170px; text-align: center">
                    Création
                    <span class="tri-button" data-clef="clef_tri_creation" data-ordre="desc"><i class="fa fa-sort-numeric-desc" style="margin-left: 5px"></i></span>
                </th>
                <th style="text-align: right">Opérations</th>
            </tr>
        </thead>
        <tbody>
            <? 
                foreach($ecoles as $e) : 

                    $ecole_id = $e['ecole_id'];
            ?>

                <? foreach($groupes as $g) : 

                    if ($g['ecole_id'] != $ecole_id)
                        continue;
                ?>
                    <tr data-clef_tri_creation="<?= $g['creation_epoch']; ?>" 
                        data-clef_tri_sousdomaine="<?= $g['sous_domaine']; ?>"
                        data-clef_tri_nom="<?= strtolower(strip_accents($e['ecole_nom'])) . strtolower(strip_accents($g['groupe_nom'])); ?>"
                        style="background: #f7f7f7">
                        <td>
                            <a target="_blank" href="https://<?= $g['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com'); ?>">
                                <?= $g['sous_domaine']; ?>
                            </a>
                        </td>
                        <td> 
                            <?= $e['ecole_nom']; ?>
                        </td>
                        <td>
                            <a href="https://<?= $g['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/admin/groupe'; ?>">
                                <?= $g['groupe_nom']; ?>
                            </a>
                        </td>
                        <td style="text-align: center">
                            <? if ($g['actif']) : ?>
                                <a href="<?= base_url() . 'admin/groupe_desactiver/' . $g['groupe_id']; ?>">
                                    <i class="fa fa-check-circle" style="color: limegreen"></i>
                                </a>
                            <? else : ?>
                                <a href="<?= base_url() . 'admin/groupe_activer/' . $g['groupe_id']; ?>">
                                    <i class="fa fa-times-circle" style="color: crimson"></i>
                                </a>
                            <? endif; ?>
                        </td>
                        <td style="text-align: center"></td>
                        <td class="mono" style="text-align: center"><?= $g['creation_date']; ?></td>
                        <td style="text-align: right">
                            <a href="https://<?= $g['sous_domaine'] . '.kovao.' . ($this->is_DEV ? 'dev' : 'com') . '/groupe/gerer'; ?>">Gérer</a>
                        </td>
                    </tr>

                <? endforeach; ?>

            <? endforeach; ?>
        </tbody>
    </table>

<? endif; ?>

</div> <!-- #admin-systeme-groupes -->
