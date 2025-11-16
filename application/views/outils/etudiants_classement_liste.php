<?
/* ----------------------------------------------------------------------------
 *
 * Outils > Etudiants > Classement > Liste
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>
    table#classement tr:hover td { 
        background: #E3F2FD;
    }

    table#classement th {
        font-family: Lato;
        font-weight: 300;
    }

    table#classement tr > td {
        background: #f7f7f7;
    }


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

        <? if (empty($classement)) : ?>

            <div style="font-famiy: Lato; font-weight: 300">
                <i class="fa fa-exclamation-circle"></i>
                Vous devez entrer les pondérations de vos évaluations pour que le classement soit calculé.
            </div>

        <? else : ?>

            <div style="font-size: 0.9em; font-famiy: Lato; font-weight: 300">
                La cote K est basée sur la cote Z. Elle tient seulement compte des évaluations dont la pondération est connue.<br />
                Si ce classement ne réflète pas la réalité, veuillez entrer la pondération de vos évaluations dans les résultats.
            </div>

            <div class="tspace"></div>

            <div style="border: 1px solid #ddd; border-top: 0">
                <table id="classement" class="table table-sm" style="margin: 0; font-size: 0.9em">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 100px; text-align: center">Rang</th>
                            <th>Étudiant</th>
                            <th style="width: 100px; text-align: center">Cote K</th>
                            <? /* <th>Enseignant</th> */ ?>
                        </tr>
                    </thead>
                    <tbody>
                        <? 
                            $rang_precedent = NULL;
     
                            foreach($classement as $etudiant_id => $etu) : 
                        ?>
                            <tr class="<?= $etu['note'] < 60 ? 'echec' : ''; ?>">
                                <td style="text-align: center">
                                    <?= $etu['rang']; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 300">
                                        <? if ($etu['enseignant_id'] == $this->enseignant_id) : ?>

                                            <a href="<?= base_url() . 'etudiant/' . $etudiant_id; ?>" target="_blank"><?= $etu['etudiant_prenom'] . ' ' . $etu['etudiant_nom']; ?></a>

                                            <svg style="margin-left: 5px; margin-top: -2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#FBC02D" class="bi-xs bi-star" viewBox="0 0 16 16">
                                              <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.523-3.356c.329-.314.158-.888-.283-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767l-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288l1.847-3.658 1.846 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.564.564 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/>
                                            </svg>

                                        <? elseif ($this->enseignant['privilege'] > 89) : ?>

                                            <a href="<?= base_url() . 'etudiant/' . $etudiant_id; ?>" target="_blank"><?= $etu['etudiant_prenom'] . ' ' . $etu['etudiant_nom']; ?></a>

                                        <? else : ?>

                                            <?= $etu['etudiant_prenom'] . ' ' . $etu['etudiant_nom']; ?>

                                        <? endif; ?>
                                    </span>
                                </td>
                                <td class="mono" style="text-align: center">
                                    <?= number_format($etu['note'], 4, ',', ''); ?>
                                </td>
                            </tr>

                            <? $rang_precedent = $etu['rang']; ?>
            
                        <? endforeach; ?>
                    </tbody>
                </table>
            </div>

        <? endif; ?>
        

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils-etudiants-relies -->
