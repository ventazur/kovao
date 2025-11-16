<?  
// ------------------------------------------------------------------------
//
// VARIABLES
// 
// ------------------------------------------------------------------------ ?>

<div id="editeur-variables" class="editeur-section">

	<a class="anchor" name="variables"></a>

	<div id="variables-evaluation">

        <div id="editeur-variables-titre" class="editeur-section-titre">

            <i class="fa fa-square" style="color: #fff; margin-right: 5px"></i>
            Variables

        </div>

        <div id="editeur-variables-contenu">

            <div style="color: #777; margin-bottom: 15px; font-size: 0.9em">
                <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                Ces variables sont nécessaires pour générer des réponses avec des équations.
            </div>

            <?  
            // ------------------------------------------------------------------------
            //
            // EVALUATION - DEFINITION DES VARIABLES
            // 
            // ------------------------------------------------------------------------ ?>

            <? if ( ! empty($variables_presentes)) : ?>

            <div class="variables-wrap">
        
               <? foreach($variables_presentes as $var) : ?>

                <div class="variable">

                    <table>

                        <tr>
                            <td class="variable-label" style="text-align: center; width: 45px; padding-right: 7px; border-right: 1px solid #FF8A65;">
                                <span class="variable-label-text align-middle">
                                    <?= $var['label']; ?>
                                </span>
                            </td>
                            <td>
                                <? if ( ! empty($var['variable_desc'])) : ?>
                                    <span style="padding-left: 15px">
                                        <?= json_decode($var['variable_desc']); ?><br />
                                    </span>
                                <? endif; ?>
                                <span style="padding-left: 15px;">
                                    minimum : <strong><?= $var['minimum']; ?></strong>,
                                    maximum : <strong><?= $var['maximum']; ?></strong>,
                                    décimale<?= $var['decimales'] > 1 ? 's' : ''; ?> : <strong><?= $var['decimales']; ?></strong>,
                                    <span data-toggle="tooltip" data-title="notation scientifique">ns</span> : <strong><?= $var['ns'] ? 'oui' : 'non'; ?></strong>,
                                    <span data-toggle="tooltip" data-title="chiffres significatifs">cs</span> : <strong><?= $var['cs'] ?: 'auto'; ?></strong>
                                </span>
                            </td>

                            <? if (in_array('modifier', $permissions)) : ?>

                                <td style="text-align: right">
                                    <div class="btn btn-outline-primary modifier-variable" 
                                        data-variable_id="<?= $var['variable_id']; ?>"
                                        data-nom="<?= $var['label']; ?>"
                                        data-minimum="<?= $var['minimum']; ?>"
                                        data-maximum="<?= $var['maximum']; ?>"
                                        data-decimales="<?= $var['decimales']; ?>"
                                        data-ns="<?= $var['ns']; ?>"
                                        data-cs="<?= $var['cs']; ?>"
                                        data-variable_desc="<?= (empty($var['variable_desc']) ? NULL : json_decode($var['variable_desc'])); ?>"
                                        data-toggle="modal" 
                                        data-target="#modal-modifier-variable">
                                        <i class="fa fa-edit"></i> Modifier la variable <?= $var['label']; ?>
                                    </div>
                                </td>

                            <? endif; ?>

                        </tr>

                    </table>

                </div> <!-- .bloc -->

               <? endforeach; ?>

            </div> <!-- .variables-wrap -->

            <? endif; ?>

            <? if (in_array('modifier', $permissions)) : ?>

                <div class="btn btn-outline-primary mt-2 ajouter-variable" data-toggle="modal" data-target="#modal-ajout-variable">
                    <i class="fa fa-plus-circle" style="margin-right: 5px"></i> Ajouter une variable
                </div>

                <? if (count($variables_presentes) > 0) : ?>

                    <div id="tester-variables-box">

                        <div id="tester-variables-box-titre">
                            Générateur de variables aléatoires
                        </div>
                
                        <div id="tester-variables-box-table">

                            <table>
                                <? foreach($variables_presentes as $var) : ?>
                                    <tr id="tester-variable-<?= $var['label']; ?>">
                                        <td class="variable-label" style="color: crimson">
                                            <?= $var['label']; ?>
                                        </td>
                                        <td class="variable-val">
                                            =
                                            <? if ($var['ns']) : ?>

                                                <?
                                                    if ($var['cs'])
                                                    {
                                                        $n = cs_ajustement($variables_generees[$var['label']], $var['cs']);
                                                    }
                                                    else
                                                    {
                                                        $n = ns_format($variables_generees[$var['label']]);
                                                    }

                                                    echo $n;
                                                ?>

                                            <? else : ?>
                                                <?= str_replace('.', ',', $variables_generees[$var['label']]); ?>
                                            <? endif; ?>
                                        </td>
                                    </tr> 
                                <? endforeach; ?>
                            </table>

                            <div id="tester-variables-rafraichir" class="btn btn-sm btn-outline-secondary spinnable" style="font-family: Lato; font-weight: 300; margin-top: 15px">
                                <i class="fa fa-refresh" style="margin-right: 5px; color: #999"></i>
                                 Générer de nouvelles valeurs 
                                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 5px"></i>
                            </div>

                        </div> <!-- #tester-variables-box-table -->
    
                    </div> <!-- #tester-variables-box -->

                <? endif; ?>

            <? else : ?>

                <? if (empty($variables_presentes)) : ?>
                    <div style="margin-bottom: -15px"></div>
                <? else : ?>
                    <div style="margin-bottom: -10px"></div>
                <? endif; ?>

            <? endif; ?>

        </div> <!-- .titre-evaluation -->
    </div> <!-- #variables-evaluation -->

</div> <!-- /#editeur-variables -->
