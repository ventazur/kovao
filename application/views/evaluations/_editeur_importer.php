<?  
// ------------------------------------------------------------------------
//
// IMPORTATION
// 
// ------------------------------------------------------------------------ ?>

<div id="editeur-importer">

    <div id="editeur-importer-titre">
        <i class="fa fa-caret-square-o-left" style="margin-right: 5px"></i>
        Importer
    </div>

    <div id="editeur-importer-contenu" class="<?= ($evaluation['public'] ? 'editeur-importer-groupe' : ''); ?>">

        <table style="width: 100%">
            <tr>
                <td>
                    <i class="fa fa-exclamation-circle" style="color: #000"></i>
                    Cette évaluation appartient au groupe. Veuillez l'importer pour y faire des changements.
                </td>
                <td style="text-align: right">

                    <div id="importer-evaluation" class="btn btn-outline-dark" 
                        data-evaluation_id="<?= $evaluation['evaluation_id']; ?>"
                        data-toggle="modal"
                        data-target="#modal-importer-evaluation">
                        <i class="fa fa-arrow-circle-left" style="margin-right: 5px"></i> 
                        Importer dans mes évaluations
                    </div>

                </td>
            </tr>

        </table>
    </div> <!-- /#editeur-importer-contenu -->

</div> <!-- /#editeur-importer -->
