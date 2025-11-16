<?
/* ====================================================================
 *
 * ADMIN > ENSEIGNANT
 *
 * ==================================================================== */ ?>

<style>

    table.table-sm {
        border-bottom: 1px solid #ddd;
    }

</style>

<div id="admin-etudiant">

<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">
    
    <h5 style="text-transform: uppercase; color: <?= $enseignant['genre'] == 'F' ? '#E91E63;' : '#3F51B5;'; ?>"> 

        <span style="color: #fff; background: #eee; padding: 2px 5px 2px 5px; border-radius: 10px; font-size: 0.85em; margin-right: 5px">ADMIN</span>

        <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?>

        <a target="_blank" href="<?= base_url() . 'admin/usurper/enseignant/' . $enseignant['enseignant_id']; ?>">
            <svg style="margin-top: -3px; margin-left: 7px" viewBox="0 0 16 16" class="bi-xs bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
              <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
        </a>
        
    </h5>

    <?
    /* --------------------------------------------------------------
     *
     * Cours
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Cours
    </div>

    <div class="space"></div>

    <? if (empty($s_semestres_cours)) : ?>

       <div style="font-size: 0.9em"> 
            <i class="fa fa-exclamation-circle"></i>
            Aucun cours trouvé
        </div>

        <div class="hspace"></div>

    <? else : ?>

        <table class="table" style="font-size: 0.8em; border-bottom: 1px solid #ddd">
            <thead>
                <tr>
                    <th>Semestre</th>
                    <th>Cours</th>
                    <th style="width: 120px; text-align: center">Évaluations</th>
                    <th style="width: 120px; text-align: center">Soumissions</th>
                </tr>
            </thead>
            <tbody>

            <? foreach($s_semestres_cours as $sem_id => $cours_ids) : ?>

                <? foreach($cours_ids as $cours_id) : ?>

                    <tr>
                        <td><?= $s_semestres[$sem_id]['semestre_code']; ?></td>
                        <td>
                            <?= $s_cours[$cours_id]['cours_nom_court']; ?> (<?= $s_cours[$cours_id]['cours_code']; ?>)
                        </td>
                        <td style="text-align: center">
                            <?= count($s_evaluations[$sem_id . '_' . $cours_id]); ?>
                        </td>
                        <td style="text-align: center">
                            <?= $s_soumissions[$sem_id . '_' . $cours_id]; ?>
                        </td>
                    </tr>

                <? endforeach; ?>    

            <? endforeach; ?>

            </tbody>
        </table>

    <? endif; ?>

    <?
    /* --------------------------------------------------------------
     *
     * Evaluations en rédaction
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Évaluations en rédaction
    </div>

    <div class="space"></div>

    <? if (empty($evaluations_redaction)) : ?>

       <div style="font-size: 0.9em"> 
            <i class="fa fa-exclamation-circle"></i>
            Aucune évaluation en rédaction trouvée
        </div>

        <div class="hspace"></div>

    <? else : ?>

        <table class="table" style="margin: 0; font-size: 0.8em; border-bottom: 1px solid #ddd">
            <thead>
                <tr>
                    <th style="width: 150px">Domaine</th>
                    <th style="width: 100px">Semestre</th>
                    <th style="width: 150px">Cours</th>
                    <th>Titre de l'évaluation</th>
                    <th style="width: 180px">Mise en ligne</th>
                </tr>
            </thead>
            <tbody>

            <? foreach($evaluations_redaction as $er) : ?>

                <tr>
                    <td><?= $er['sous_domaine']; ?></td>
                    <td><?= $er['semestre_code']; ?></td>
                    <td><?= $er['cours_code_court']; ?></td>
                    <td><?= $er['evaluation_titre']; ?></td>
                    <td><?= date_humanize($er['ajout_epoch'], TRUE); ?></td>
                </tr>

            <? endforeach; ?>

            </tbody>
        </table>

    <? endif; ?>

    <div class="hspace"></div>


    <?
    /* --------------------------------------------------------------
     *
     * Evaluations
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Évaluations
    </div>

    <div class="space"></div>

    <div style="font-size: 0.8em">

        <? if (empty($evaluations) || empty($s_cours)) : ?>

          <i class="fa fa-exclamation-circle"></i>
          Aucune évaluation trouvée
    
          <div class="hspace"></div>

        <? else : ?>
      
          <? foreach($s_cours as $cours_id => $c) : ?>

            <? $premier = TRUE; ?>

            <? foreach($evaluations as $e) : ?>

              <? if ($e['cours_id'] != $cours_id) continue; ?>

              <? if ($premier) : ?>

                <span style="margin-right: 5px; font-weight: 600"><?= $c['cours_nom']; ?></span>
                (<?= $c['cours_code']; ?>)

                <div class="space"></div>

                <table class="table table-sm" style="margin-top: 0"> 
        
              <? endif; ?>

                  <tr>
                    <td style="width: 80px"><?= $c['cours_code_court']; ?></td>
                    <td style="width: 80px">
                      <a href="<?= base_url() . '/evaluations/editeur/' . $e['evaluation_id']; ?>" target="_blank">
                        <?= $e['evaluation_id']; ?>
                      </a>
                    </td>
                    <td><?= $e['evaluation_titre']; ?></td>
                    <td style="width: 80px"><?= ($e['public'] ? 'public' : ''); ?></td>
                    <td class="mono" style="width: 200px; text-align: right"><?= $e['ajout_date']; ?></td>
                  </tr>

              <? $premier = FALSE; ?>

            <? endforeach; ?>

            </table>

          <? endforeach; ?> 

        <? endif; ?>
    </div>


    <?
    /* --------------------------------------------------------------
     *
     * Informations
     *
     * -------------------------------------------------------------- */ ?>

    <div class="space"></div>

    <div style="font-size: 1.2em; font-weight: 100">
        Informations
    </div>

    <div class="space"></div>

    <div style="font-size: 0.9em; font-weight: 300; line-height: 1.75em">
        Enseignant ID : <?= $enseignant['enseignant_id']; ?></br />
        <? /* Évaluations envoyées : <?= $evaluations_envoyees; ?><br /> */ ?>
        Date d'inscription : <?= date_french_full($enseignant['inscription_epoch']); ?><br />
        Date de la dernière connexion : <?= ($derniere_connexion == FALSE ? 'n/d' : date_french_full($derniere_connexion['epoch'])); ?>
    </div>

</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #admin-etudiant -->
