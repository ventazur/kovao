<?
/* ----------------------------------------------------------------------------
 * 
 * Resultats > Conformite > Evaluation plusieurs adresse IPs en activite 
 * (etudiants inscrits seulement)
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="conformite-evaluation-precisions-titre inscrits">

    Ces étudiants INSCRITS avaient la <strong>même adresse IP</strong> pendant leur évaluation :

</div>

<div class="conformite-evaluation-precisions-contenu inscrits">

    <div class="conformite-explications">
        <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
        Ces étudiants ont utilisé la même connexion Internet pendant leur évaluation, c'est-à-dire avant de la soumettre. 
        Ceci sert à détecter un étudiant qui aurait pu se connecter sur le compte d'un autre étudiant pour faire l'évaluation à sa place, avant de la soumettre.
    </div>

    <table class="conformite-evaluation-precisions-table">

    <? foreach($activite as $adresse_ip => $etudiant_ids) : ?> 

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

        <? foreach($etudiant_ids as $e_id) : ?>

            <span class="conformite-noms">
                <?= $etudiants[$e_id]['prenom'] . ' ' . $etudiants[$e_id]['nom']; ?>
            </span>

        <? endforeach; ?>
                <span style="color: #777;"><?= '(' . count($etudiant_ids) . ')'; ?></span>
            </td>
        </tr>
                        
    <? endforeach; ?>

    </table>

</div> <!-- .conformite-evaluation-precision-contenu -->

<? foreach($activite as $adresse_ip => $a) : ?>

    <? if (count($a) < 2) continue; ?>

<? endforeach; ?>
