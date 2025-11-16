<?  
// ------------------------------------------------------------------------
//
// INSTRUCTIONS
// 
// ------------------------------------------------------------------------ ?>

<div id="editeur-instructions">

    <a class="anchor" name="instructions"></a>

    <div id="editeur-instructions-titre" class="editeur-section-titre">
        <i class="fa fa-square" style="color: #fff; margin-right: 5px"></i> 
        Instructions
    </div>

    <div id="instructions-evaluation">

            <div style="color: #777; margin-bottom: 15px; font-size: 0.9em">
                <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                Ces instructions seront affichées dans le haut de l'évaluation.
            </div>

            <div id="instructions-evaluation-contenu" class="<?= (empty($evaluation['instructions']) ? 'empty' : ''); ?>">
                <? if (empty($evaluation['instructions'])) : ?>

                    Il n'y a pas d'instructions.

                <? else : ?>

                    <?= _html_out($evaluation['instructions']); ?>

                <? endif; ?>
            </div>

        <? if (in_array('modifier', $permissions)) : ?>

            <div class="btn btn-outline-primary mt-2 modifier-instructions" data-toggle="modal" data-target="#modal-modifier-instructions">
                <i class="fa fa-plus-circle"></i> Ajouter | <i class="fa fa-edit" style="margin-left: 5px"></i> Modifier les instructions
            </div>

        <? else : ?>

            <div style="margin-bottom: -10px"></div>

        <? endif; ?>

    </div> <!-- #instructions-evaluation -->

</div> <!-- #editeur-instructions -->
