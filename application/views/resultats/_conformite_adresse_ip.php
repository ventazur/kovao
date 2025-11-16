<?
/* ----------------------------------------------------------------------------
 * 
 * Resultats > Conformite > La meme adresse IP
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="conformite-evaluation-precisions-titre alerte">

    Ces étudiants avaient la <strong>même adresse IP</strong> :

</div>

<div class="conformite-evaluation-precisions-contenu alerte">

    <div class="conformite-explications">
        <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
        Ces étudiants ont fait leur évaluation au même endroit avec la même connexion Internet.
    </div>

    <table class="conformite-evaluation-precisions-table">

    <? foreach($adresse_ips as $adresse_ip => $soumission_ids) : ?> 

        <? if (count($soumission_ids) < 2) continue; ?>

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
                <? foreach($soumission_ids as $s_id) : ?>

                    <span class="conformite-noms">
                        <?= $soumissions[$s_id]['prenom_nom']; ?>
                    </span>

                <? endforeach; ?>

                <span style="color: #777;"><?= '(' . count($soumission_ids) . ')'; ?></span>
            </td>
        </tr>
                        
    <? endforeach; ?>

    </table>

</div> <!-- .conformite-evaluation-precisions-contenu -->
