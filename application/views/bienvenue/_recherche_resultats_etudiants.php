<?
/* ----------------------------------------------------------------------------
 *
 * Resultats de la recherche > Etudiants
 *
 * ---------------------------------------------------------------------------- */ ?>

<? if ( ! empty($etudiants)) : ?>

<div class="recherche-resultats-section">

    <div class="recherche-resultats-section-titre">

        <i class="fa fa-search" style="margin-right: 5px;"></i>
        Étudiants

    </div>

    <div class="recherche-resultats-section-contenu">

        <table>
            <thead>
                <tr>
                    <th style="width: 100px">Etudiant ID</th>
                    <th>Prénom et Nom</th>
                    <th>Cours</th>
                    
                    <? if ($this->enseignant['privilege'] >= 90) : ?>
                        <th>Enseignant</th>
                    <? endif; ?>

                    <th style="width: 200px">Semestre</th>

                </tr>
            </thead>
            <tbody>
                <? 
                    foreach($etudiants as $e) : 
                ?>
                    <tr>
                        <td>
                            <? if ( ! empty($e['etudiant_id'])) : ?>
                                <? if ($this->enseignant['privilege'] >= 90) : ?>
                                    <a href="<?= base_url() . 'admin/etudiant/' . $e['etudiant_id']; ?>" target="_blank">
                                        <?= $e['etudiant_id']; ?>
                                    </a>
                                <? else : ?>
                                    <a href="<?= base_url() . 'etudiant/' . $e['etudiant_id']; ?>" target="_blank">
                                        <?= $e['etudiant_id']; ?>
                                    </a>
                                <? endif; ?>
                            <? else :  ?>
                                -
                            <? endif; ?>
                        </td>
                        <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width: 1px;">
                            <span data-toggle="popover" data-content="allo">
                                <?= $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?>
                            </span>
                        </td>
                        <td><?= $e['cours_nom_court'] . ' (' . $e['cours_code_court'] . ')'; ?></td>

                        <? if ($this->enseignant['privilege'] >= 90) : ?>
                            <td>
                                <?= $e['prenom'][0] . '. ' . $e['nom']; ?>
                            </td>
                        <? endif; ?>

                        <td><?= $e['semestre_code']; ?></td>
                    </tr>

                <? endforeach; ?>

            </tbody>
        </table>


    </div>

</div>

<? endif; ?>
