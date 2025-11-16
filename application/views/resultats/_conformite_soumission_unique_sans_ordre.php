<?
/* ----------------------------------------------------------------------------
 * 
 * Resultats > Conformite > Soumissiosn2 (memes questions sans ordre, memes reponses)
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="conformite-evaluation-precisions-titre">

    Ces étudiants avaient la <strong>même soumission </strong> :

</div>

<div class="conformite-evaluation-precisions-contenu">

    <div class="conformite-explications">
        <i class="fa fa-info-circle" style="margin-right: 3px;"></i>
        Ces étudiants avaient les mêmes questions (sans considérer l'ordre de présentation de ces questions) et les mêmes réponses.
    </div>

    <table class="conformite-evaluation-precisions-table">

    <? foreach($soumissions2_unique as $ref => $soumission_ids) : ?> 

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

</div> <!-- .conformite-evaluation-precisions-contenu -->
