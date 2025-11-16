<?
/* ----------------------------------------------------------------------------
 * 
 * Resultats > Conformite > Ordinateur unique
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="conformite-evaluation-precisions-titre">

    Ces étudiants utilisaient le <strong>même ordinateur </strong> :

</div>

<div class="conformite-evaluation-precisions-contenu">

    <div class="conformite-explications">
        <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
        Ces étudiants ont utilisé le même ordinateur pour faire leur évaluation. Il est possible qu'un étudiant faisait l'évaluation pour l'autre ou les autres. 
        <br />Attention, si les étudiants ont fait leur évaluation dans les laboratoires informatiques de l'école (on peut le savoir selon l'adresse IP), ils pourraient apparaître comme utilisant le même ordinateur.
    </div>

    <table class="conformite-evaluation-precisions-table">

    <? foreach($ordinateurs_unique as $ref => $soumission_ids) : ?> 

        <? if (count($soumission_ids) < 2) continue; ?>

        <tr>
            <td class="mono">
                <?= substr($ref, 0, 12); ?>
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

</div> <!-- .conformite-evaluation-precision-contenu -->
