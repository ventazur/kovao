<?  
// ------------------------------------------------------------------------
//
// EXPORTATION
// 
// ------------------------------------------------------------------------ ?>

<div id="editeur-exporter">

    <div id="editeur-exporter-titre">
        <i class="fa fa-share-alt-square" style="margin-right: 5px"></i>
        Partager
    </div>

    <div id="editeur-exporter-contenu" class="<?= ($evaluation['public'] ? 'editeur-exporter-groupe' : ''); ?>">

        <table style="width: 100%">
            <tr>
                <td>

                    <i class="fa fa-lg fa-smile-o" style="color: #00C853; margin-right: 5px"></i>
                    Partager une copie avec les autres enseignants du groupe.

                </td>
                <td style="text-align: right">

                    <div id="exporter-evaluation" class="btn btn-outline-dark" 
                        data-evaluation_id="<?= $evaluation['evaluation_id']; ?>"
                        data-toggle="modal"
                        data-target="#modal-exporter-evaluation">
                        <i class="fa fa-arrow-circle-right" style="margin-right: 5px;"></i> 
                        Partager avec le groupe
                    </div>

                    <div id="exporter-json" class="btn btn-outline-secondary" 
                        data-evaluation_id="<?= $evaluation['evaluation_id']; ?>">
                        <i class="fa fa-arrow-circle-down" style="margin-right: 5px;"></i> 
                        Exporter JSON
                    </div>

                </td>
            </tr>
        </table>
    </div> <!-- /#editeur-exporter-contenu -->

</div> <!-- /#editeur-exporter -->
