<?
/* ----------------------------------------------------------------------------
 * 
 * Resultats > Conformite > Aide externe
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="conformite-evaluation-precisions-titre alerte2">

    Ces étudiants ont possiblement obtenu une <strong>aide externe</strong> :

</div>

<div class="conformite-evaluation-precisions-contenu alerte2">

    <div class="conformite-explications">
        <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
        Plusieurs adresses IPs se sont connectées sur le compte de l'étudiant pendant son évaluation.
    </div>

    <table class="conformite-evaluation-precisions-table">

    <tr>
        <td>
            <? foreach($activite['aide_externe'] as $etudiant_id => $adresse_ips) : ?> 

                <? if (count($adresse_ips) < 2) continue; ?>

                <span class="conformite-noms">
                    <?= $etudiants[$etudiant_id]['prenom'] . ' ' . $etudiants[$etudiant_id]['nom']; ?>
                    <span style="color: #777;"><?= '(' . count($adresse_ips) . ')'; ?></span>
                </span>
                                
            <? endforeach; ?>
        </td>
    </tr>

    </table>

</div> <!-- .conformite-evaluation-precisions-contenu -->
