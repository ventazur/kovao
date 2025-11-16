<?
/* ----------------------------------------------------------------------------
 * 
 * Resultats > Conformite > Adresse IP louche
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="conformite-evaluation-precisions-titre alerte">

    Ces étudiants ont utilisé la <strong>même connexion internet</strong> :

</div>

<div class="conformite-evaluation-precisions-contenu alerte">

    <div class="conformite-explications">
        <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
        Plusieurs étudiants se sont connectés à partir de la même adresse IP.
    </div>

    <table class="conformite-evaluation-precisions-table">

        <? foreach($activite['meme_ip'] as $adresse_ip => $etudiant_ids) : ?> 

            <? if (count($etudiant_ids) < 2) continue; ?>

            <tr>
                <td class="mono">
                    <? if (in_array($adresse_ip, $ecole_ips)) : ?>
                        <?= $this->ecole['ecole_nom_court']; ?>
                        <i class="fa fa-info-circle" style="margin-left: 5px; color: #aaa" data-toggle="tooltip" data-title="Adresse IP de l'école"></i><br />
                    <? else : ?>
                        <?= $adresse_ip; ?>
                    <? endif; ?>
                </td>
                
                <td>
                    <? foreach($etudiant_ids as $etudiant_id) : ?>

                        <span class="conformite-noms">
                            <?= $etudiants[$etudiant_id]['prenom'] . ' ' . $etudiants[$etudiant_id]['nom']; ?>
                        </span>

                    <? endforeach; ?>

                    <span style="color: #777;"><?= '(' . count($etudiant_ids) . ')'; ?></span>
                </td>

            </tr>

        <? endforeach; ?>

    </table>

</div> <!-- .conformite-evaluation-precisions-contenu -->
