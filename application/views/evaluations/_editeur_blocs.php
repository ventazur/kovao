
<?  
// ------------------------------------------------------------------------
//
// BLOCS
// 
// ------------------------------------------------------------------------ ?>

<div id="editeur-blocs" class="editeur-section">

	<a class="anchor" name="blocs"></a>

	<div id="blocs-evaluation">

        <div id="editeur-blocs-titre" class="editeur-section-titre">
            <i class="fa fa-cube" style="color: #fff; margin-right: 5px;"></i> 
            Blocs
        </div>

        <div id="editeur-blocs-contenu">

            <div style="color: #777; margin-bottom: 15px; font-size: 0.9em">
                <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                Ces blocs permettent de regrouper des questions et d'en choisir un certain nombre aléatoirement.
            </div>

            <? if ( ! empty($blocs)) : ?>

            <div class="bloc-wrap">

                <? // Quelques donnes sur les labels ?>
                <div id="bloc-labels" class="d-none"><?= json_encode($bloc_labels);?></div>

                <? foreach($blocs as $bloc) : ?>

                <div class="bloc">

                    <table>

                        <tr>
                            <td class="bloc-label" style="text-align: center; width: 45px; padding-right: 7px; border-right: 1px solid #ccc;">
                                <span class="bloc-label-text align-middle">
                                    <?= $bloc['bloc_label']; ?>
                                </span>
                            </td>
                            <td>
                                <? if ( ! empty($bloc['bloc_desc'])) : ?>
                                <span style="padding-left: 15px">
                                    <?= $bloc['bloc_desc']; ?><br />
                                </span>
                                <? endif; ?>
                                <span style="padding-left: 15px;">
                                    <strong><?= my_number_format($bloc['bloc_points']); ?></strong> point<?= ($bloc['bloc_points'] > 1 ? 's' : ''); ?>,
                                    <strong><?= $bloc['bloc_nb_questions']; ?></strong>/<?= $nb_questions_dans_blocs[$bloc['bloc_id']]; ?> question<?= ($nb_questions_dans_blocs[$bloc['bloc_id']] > 1 ? 's' : ''); ?> de ce bloc
                                    (<?= my_number_format($bloc['bloc_nb_questions'] * $bloc['bloc_points']); ?> point<?= ($bloc['bloc_nb_questions'] * $bloc['bloc_points']) > 1 ? 's' : '' ;?>)
                                </span>
                            </td>

                            <td class="bloc-data" style="text-align: right"
                                data-bloc_id="<?= $bloc['bloc_id']; ?>"
                                data-bloc_label="<?= $bloc['bloc_label']; ?>"
                                data-bloc_points="<?= $bloc['bloc_points']; ?>"
                                data-bloc_nb_questions="<?= $bloc['bloc_nb_questions']; ?>"
                                data-bloc_desc="<?= htmlentities($bloc['bloc_desc']); ?>">

                                <? if (count($cours_avec_evaluation) > 0) : ?>
                                    <div class="btn btn-outline-primary copier-bloc"
                                        data-toggle="modal" 
                                        data-target="#modal-copier-bloc">
                                        <i class="fa fa-copy"></i> Copier
                                    </div>
                                <? endif; ?>

                                <? if ( $this->groupe_id != 0 && ! $evaluation['public']) : ?>
                                    <div class="btn btn-outline-primary exporter-bloc"
                                        data-toggle="modal" 
                                        data-target="#modal-exporter-bloc">
                                        <i class="fa fa-arrow-circle-right"></i> Exporter
                                    </div>
                                <? endif; ?>

                                <? if ($evaluation['public']) : ?>
                                    <div class="btn btn-outline-primary importer-bloc"
                                        data-toggle="modal" 
                                        data-target="#modal-importer-bloc">
                                        <i class="fa fa-arrow-circle-left"></i> Importer
                                    </div>
                                <? endif; ?>


                                <? if (in_array('modifier', $permissions)) : ?>
                                        <div class="btn btn-outline-primary modifier-bloc" 
                                            data-toggle="modal" 
                                            data-target="#modal-modifier-bloc">
                                            <i class="fa fa-edit"></i> Modifier
                                        </div>
                                <? endif; ?>
                            </td>

                        </tr>

                    </table>

                </div> <!-- .bloc -->

               <? endforeach; ?>

            </div> <!-- .bloc-wrap -->

            <? endif; ?>

            <? if (in_array('modifier', $permissions)) : ?>

                <div class="btn btn-outline-primary mt-2 ajouter-bloc" data-toggle="modal" data-target="#modal-ajout-bloc">
                    <i class="fa fa-plus-circle"></i> Ajouter un bloc
                </div>

            <? else : ?>

                <? if (empty($blocs)) : ?>

                    <div style="margin-bottom: -15px"></div>

                <? else : ?>

                    <div style="margin-bottom: -10px"></div>

                <? endif; ?>

            <? endif; ?>

        </div> <!-- .titre-evaluation -->

    </div>

</div> <!-- /#editeur-blocs -->
