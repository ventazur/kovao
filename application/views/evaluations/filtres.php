<div id="filtres">

<style>
    table {
        font-size: 0.8em;
    }

    #filtres-evaluation table {
        border: 1px solid #ddd;
    }

    #filtres-evaluation table tr td {
        border-top: 0;
        padding: 5px 10px 5px 10px;
    }
    
    #filtres-eleves table {
        border: 1px solid dodgerblue;
    } 

    #filtres-eleves table tr:first-child th {
        border-top: 0;
    }

    #filtres-eleves table thead tr th {
        border: 0;
        background-color: dodgerblue;
        color: #fff; 
    }

</style>

<div class="container-fluid">
<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>Filtres avancés</h4>

    <div class="space"></div>

    <div id="filtres-evaluation">
        <table class="table table-sm">
            <tbody>
                <tr>
                    <td>Cours</td>
                    <td><?= $cours['cours_nom']; ?> (<?= $cours['cours_code_court']; ?>)</td>
                </tr>
                <tr>
                    <td style="width: 200px">Titre de l'évaluation :</td>
                    <td style="font-weight: 600"><?= $evaluation['evaluation_titre']; ?></td>
                </tr>
                <tr>
                    <td>Référence de l'évaluation :</td>
                    <td><?= $evaluation_reference; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="hspace"></div>

    <h5 style="font-weight: 300">Étudiants</h5>

    <? if (empty($eleves)) : ?>

        <i class="fa fa-exclamation-circle"></i>
        Aucun élève trouvé

    <? else : ?>

        <div id="filtres-eleves">

            <? foreach($eleves[$cours_id] as $groupe_no => $groupe) : ?>

                <div class="hspace"></div>

                <table class="filtres-eleves table">
                    <thead>
                        <tr>
                            <th colspan="3">Groupe <?= $groupe_no; ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr style="background: #eee">
                            <td style="width: 100px; text-align: center; font-weight: 600">Sélection</td>
                            <td style="font-weight: 600">Nom de l'étudiant</td>
                            <td style="width: 120px; font-weight: 600">Numéro DA</td>
                        </tr>

                        <? foreach($groupe as $e) : ?>
                            <tr>
                                <td></td>
                                <td><?= $e['eleve_prenom'] . ' ' . $e['eleve_nom']; ?></td>
                                <td><?= $e['numero_da']; ?></td>
                            </tr>

                        <? endforeach; ?>
                    </tbody>
                </table>

            <? endforeach; ?>

        </div>

    <? endif; ?>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->
</div> <!-- .container-fluid -->

</div> <!-- #resultats -->
