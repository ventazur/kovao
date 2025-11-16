<?
/* ----------------------------------------------------------------------------
 *
 * Outils > Etudiants relies
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="outils-etudiants-relies">

<div class="container-fluid">
        
<div class="row">

    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

        <h4>Outils <i class="fa fa-angle-right" style="margin-left: 7px; margin-right: 7px"></i> Étudiants reliés</h4>

        <div class="space"></div>

        <div>
            Les étudiants reliés sont ceux susceptibles de s'aider lors des évaluations parce qu'ils ont déjà partagé une même connexion internet.
        </div>

        <div class="space"></div>

        <? if (empty($etudiants_relies)) : ?>

            <div style="font-size: 0.9em">
                <i class="fa fa-exclamation-circle"></i> Aucun étudiant relié pour ce semestre
            </div>

        <? else : ?>

            <div style="border: 1px solid #5C6BC0">

                <table class="table table-borderless" style="margin: 0; font-size: 0.9em; border-top: 0">
                    <tr style="background: #E8EAF6">
                        <th style="width: 175px">Adresse IP</th>
                        <th>Étudiants <span style="font-weight: normal">(Etudiant ID)</span></th>
                    </tr>

                    <? foreach($etudiants_relies as $ip => $er) : ?>
                        <tr style="border-top: 1px solid #C5CAE9">
                            <td class="mono"><?= $ip; ?></td>
                            <td>
                                <? foreach($er as $e) : ?>

                                    <span style="margin-right: 7px; background: #E8EAF6; padding: 5px 7px 5px 7px; border-radius: 3px; font-size: 0.9em">
                                        <?= ucfirst($e['prenom']) . ' ' . ucfirst($e['nom']); ?> (<?= $e['etudiant_id']; ?>)
                                    </span>

                                <? endforeach; ?>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </table>

            </div>

            <div class="space"></div>

            <i class="fa fa-info-circle" style="margin-right: 5px; color: #9FA8DA"></i> Il est possible qu'un étudiant possède plus d'un compte.
        <? endif; ?>

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outils-etudiants-relies -->
