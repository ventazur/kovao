
<h5><?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></h5> 

<div class="space"></div>

<div class="groupe-section">

    <div class="groupe-section-titre">
        Évaluations à faire remplir
    </div>

    <div class="groupe-section-box">

        <? if (empty($evaluations_selectionnees) || ! is_array($evaluations_selectionnees)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucune évaluation à faire remplir

        <? else : ?>

            <? $c_premier = TRUE; ?>

            <? foreach($cours as $cours_id => $c) : ?>

                <? $c_entete = FALSE; ?>

                <? foreach($evaluations_selectionnees as $evaluation_id => $e) : ?>

                    <? if ($e['cours_id'] != $c['cours_id']) continue; ?>

                    <? if ( ! $c_entete) : ?>

                        <? if ( ! $c_premier) : ?><br /><? endif; ?>

                        <strong>
                            <?= $c['cours_code_court']; ?>
                        </strong>

                        <div class="space"></div>

                        <? $c_entete = TRUE; $c_premier = FALSE; ?>
                    <? endif; ?>

                    <a href="<?= base_url() . 'evaluation/' . $e['evaluation_reference']; ?>">
                        <?= $evaluations[$evaluation_id]['evaluation_titre']; ?>
                    </a>
                    
                    <? if ($e['cacher']) : ?>
                        (cachée)
                    <? endif; ?>
                    <br />
                
                <? endforeach; ?>

            <? endforeach; ?>

        <? endif; ?>
    </div>
</div>

<div class="space"></div>

<div class="groupe-section">

    <div class="groupe-section-titre">
        Évaluations
    </div>

    <div class="groupe-section-box">

        <? if (empty($evaluations) || ! is_array($evaluations)) : ?>

            <i class="fa fa-exclamation-circle"></i> Aucune évaluation

        <? else : ?>
                
            <? $c_premier = TRUE; ?>

            <? foreach($cours as $cours_id => $c) : ?>

                <? $c_entete = FALSE; ?>

                <? foreach($evaluations as $evaluation_id => $e) : ?>

                    <? if ($e['cours_id'] != $c['cours_id']) continue; ?>

                    <? if ( ! $c_entete) : ?>

                        <? if ( ! $c_premier) : ?><br /><? endif; ?>
            
                        <strong>
                            <?= $c['cours_code_court']; ?>
                        </strong>
    
                        <div class="space"></div>

                        <? $c_entete = TRUE; $c_premier = FALSE; ?>
                    <? endif; ?>

                    <a href="<?= base_url() . 'evaluations/editeur/' . $evaluation_id; ?>">
                        <?= $e['evaluation_titre']; ?>
                    </a>

                    <br />

                <? endforeach; ?>

            <? endforeach; ?>

        <? endif; ?>
    </div>
</div>

