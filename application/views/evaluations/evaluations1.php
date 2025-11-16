<?
/* ----------------------------------------------------------------------------
 *
 * EVALUATIONS DU GROUPE
 *
 * ---------------------------------------------------------------------------- */ ?>

<link href="<?= base_url() . 'assets/css/evaluations1.css?' . $this->now_epoch; ?>" rel="stylesheet">

<div id="evaluations">

<div class="container-fluid">
<div class="row">

<?  
// ------------------------------------------------------------------------
//
// EVALUATION - BARRE DE NAVIGATION
// 
// ------------------------------------------------------------------------ ?>

<div class="col-xl-2 d-none d-xl-block">
    
        <nav class="sidebar <?= count($evaluations) > 5 || count($cours_evaluations_existent) > 3 ? '' : 'd-none'; ?>">

        <div class="d-none d-xl-block">

            <div class="sidebar-titre">
                Groupe
            </div>

            <ul class="nav flex-column">

                <div style="margin-top: 5px"></div>

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() . 'evaluations'; ?>" style="color: dodgerblue">
                        Mes évaluations
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="#top">
                        TOP
                    </a>
                </li>

                <? if ( ! empty($cours_evaluations_existent)) : ?>

                    <div style="border-top: 1px solid #ccc; margin-top: 5px; margin-bottom: 5px;"></div>

                    <? foreach($cours_evaluations_existent as $c) : ?>

                        <li class="nav-item">
                            <a class="nav-link active" href="#vers-<?= $cours_raw[$c]['cours_code']; ?>"><?= $cours_raw[$c]['cours_code']; ?></a>
                        </li>
                
                    <? endforeach; ?>

                <? endif; ?>

                <div style="margin-top: 5px"></div>

            </ul>

        </div>

        </nav>

</div> <!-- .col-xl-2 -->

<a class="anchor" name="top"></a>

<div class="col col-xl-10">

    <h3>Évaluations du groupe</h3>

    <div class="hspace"></div>
    <div class="space"></div>

    <a class="btn btn-primary" href="<?= base_url() . $current_controller . '/creer'; ?>" role="button"><i class="fa fa-plus-circle"></i> Créer une évaluation</a>

    <a class="btn btn-outline-secondary ml-0 ml-sm-2 mt-3 mt-sm-0" href="<?= base_url() . $current_controller; ?>"><i class="fa fa-user"></i> Mes évaluations</a>

    <div class="hspace"></div>

    <? foreach($cours_evaluations_existent as $c) : ?>

        <div class="hspace"></div>
        <div class="space"></div>

        <a class="anchor" name="vers-<?= $cours_raw[$c]['cours_code']; ?>"></a>

        <h5><?= $cours_raw[$c]['cours_nom']; ?> (<?= $cours_raw[$c]['cours_code']; ?>)</h5>

        <div class="space"></div>

        <? foreach($evaluations as $e) : 

            if ($e['cours_id'] != $c)
                continue;        
        ?>
            <a class="evaluation-liste" href="<?= base_url() . 'evaluations/editeur/' . $e['evaluation_id']; ?>">
                <div class="evaluation-liste row no-gutters">
                    <div class="col-8">
                        <span style="margin-right: 5px; color: #fff; padding: 3px 5px 3px 5px; border-radius: 3px;"><?= $cours_raw[$c]['cours_code_court']; ?></span>
                         <?= $e['evaluation_titre']; ?>
                    </div>
                    <div class="col-4" style="text-align: right"> 

                        <?  if ($e['cadenas']) : ?>
                            <span class="badge"><i class="fa fa-lock fa-lg" style="margin-right: 5px; color: crimson"></i></span>
                        <? endif; ?>

                        <? if ($e['enseignant_id'] == $this->enseignant_id) : ?>
                            <span class="badge badge-primary">
                                <?= ucfirst(@$enseignants[$e['enseignant_id']]['prenom']) . ' ' . ucfirst(mb_substr(@$enseignants[$e['enseignant_id']]['nom'], 0, 1)) . '.'; ?>
                            </span>
                        <? else : ?>
                            <span class="badge badge-dark">
                                <?= ucfirst(@$enseignants[$e['enseignant_id']]['prenom']) . ' ' . ucfirst(mb_substr(@$enseignants[$e['enseignant_id']]['nom'], 0, 1)) . '.'; ?>
                            </span>
                        <? endif; ?>

                        <? if ( 1 == 2 && ! $e['actif']) : ?>
                            <span class="badge badge-warning">Désactivée</span>
                        <? endif; ?>
                        <? if ( ! $e['public']) : ?>
                            <span class="badge badge-primary">Mon évaluation</span>
                        <? else : ?>
                            <span class="badge badge-warning">Département</span> 
                        <? endif; ?>
                        <i class="fa fa-arrow-circle-right fa-lg" style="margin-left: 10px; color: royalblue"></i>
                    </div>
				</div>
            </a>

        <? endforeach; ?> 

    <? endforeach; ?>

</div> <!-- .col-xl-10 -->

</div> <!-- .row -->
</div> <!-- .container-fluid -->

</div> <!-- #resultats -->
