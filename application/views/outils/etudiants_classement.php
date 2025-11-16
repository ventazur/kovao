<?
/* ----------------------------------------------------------------------------
 *
 * Outils > Etudiants > Classement
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>
    table#semestres-cours td { }
</style>

<div id="etudiants-classement">

<div class="container-fluid">
        
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>
            Classement des étudiants par cours

            <? if (isset($semestre_id) && array_key_exists($semestre_id, $semestres) && isset($cours_id) && array_key_exists($cours_id, $cours)) : ?>

                <span style="font-weight: 300">
                    <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i> 
                    <?= $semestres[$semestre_id]['semestre_code']; ?>
                
                    <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i> 
                    <?= $cours[$cours_id]['cours_nom_court']; ?>
                </span>

            <? endif; ?>

        </h4>

		<div class="space"></div>

        <? if (empty($semestres_cours)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucun semestre trouvé

        <? else : ?>

            <? foreach($semestres_cours as $semestre_id => $cours_ids) : ?>

                <div style="margin-bottom: 30px">

                    <div style="font-size: 1em;">
                        <?= $semestres[$semestre_id]['semestre_nom']; ?>
                    </div>

                    <div class="hspace"></div>
                    
                    <div style="border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding-top: 5px; padding-bottom: 5px; background: #faf9f8">
                        <table id="semestres-cours" class="table table-sm table-borderless" style="margin: 0; font-size: 0.9em">
							<? foreach($cours_ids as $cours_id) : ?>
                                <tr>
                                    <td style="width: 100px"><?= $cours[$cours_id]['cours_code_court']; ?></td>
                                    <td>
                                        <a href="<?= base_url() . 'outils/etudiants/classement/semestre/' . $semestre_id . '/cours/' . $cours_id; ?>">
                                            <?= $cours[$cours_id]['cours_nom']; ?>
                                        </a>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    </div>

                </div>

            <? endforeach; ?>

        <? endif; ?>
        

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils-etudiants-relies -->
