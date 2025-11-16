<?
/* ----------------------------------------------------------------------------
 *
 * Maintenance
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="admin-systeme-maintenance">

<h5>Maintenance</h5> 

<?
/* ----------------------------------------------------------------------------
 *
 * Les styles specifiques
 *
 * ---------------------------------------------------------------------------- */ ?>

<style>
    i.soustitre {
        margin-right: 5px;
        color: #C5CAE9;
    }
    .lien, .lien-bientot {
        font-size: 0.9em;
    }
    .lien-bientot {
        color: #777;
    }
</style>

<?
/* ----------------------------------------------------------------------------
 *
 * Les documents
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-file soustitre"></i>
    Documents
</div>

<ul>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/detecter_documents_superflus'; ?>">Détecter les documents superflus (à effacer) [des évaluations]</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/detecter_documents_superflus_soumissions'; ?>">Détecter les documents superflus des étudiants (à effacer) [des soumissions]</a></li>
</ul>

<ul>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/detecter_documents_manquants_evaluations'; ?>">Détecter les documents manquants [des évaluations]</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/detecter_documents_manquants_soumissions'; ?>">Détecter les documents manquants [des soumissions]</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/detecter_documents_etudiants_manquants_soumissions'; ?>">Détecter les documents étudiants manquants [des soumissions] (@todo)</a></li>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * Les etudiants
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-user soustitre"></i>
    Étudiants
</div>

<ul>
    <li>
        <a class="lien spinnable" href="<?= base_url() . 'admin/etudiants_relies'; ?>">
            Détecter les étudiants reliés
            <span class="spinner d-none" style="margin-left: 7px"><i class="fa fa-circle-o-notch fa-spin"></i></span>
        </a>
    </li>
    <li>
        <a class="lien spinnable" href="<?= base_url() . 'admin/etudiants_connexions'; ?>">
            Le nombre d'étudiants différents qui se sont connectés à chaque jour
            <span class="spinner d-none" style="margin-left: 7px"><i class="fa fa-circle-o-notch fa-spin"></i></span>
        </a>
    </li>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * Les courriels
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-envelope soustitre"></i>
    Courriels 
</div>

<ul>
    <li><a class="lien" href="<?= base_url() . 'admin/test_messagerie'; ?>">Tester l'envoi selon la priorité et les limites</a></li>
    <li><a class="lien" href="<?= base_url() . 'admin/test_messagerie/amazon'; ?>">Tester l'envloi via Amazon</a></li>
    <li><a class="lien" href="<?= base_url() . 'admin/test_messagerie/mailgun'; ?>">Tester l'envoi via Mailgun</a></li>
    <li><a class="lien" href="<?= base_url() . 'admin/test_messagerie/mailjet'; ?>">Tester l'envoi via Mailjet</a></li>
    <li><a class="lien" href="<?= base_url() . 'admin/test_messagerie/sendgrid'; ?>">Tester l'envoi via Sendgrid (révoqué)</a></li>
    <li><a class="lien" href="<?= base_url() . 'admin/test_messagerie/sendinblue'; ?>">Tester l'envoi via SendinBlue</a></li>
</ul>

<ul>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/stats_messagerie'; ?>">Statistiques de messagerie pour tous les fournisseurs (local)</a>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/stats_messagerie_amazon'; ?>">Statistiques de messagerie pour Amazon</a>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/stats_messagerie_mailjet'; ?>">Statistiques de messagerie pour Mailjet</a>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * Les vues
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-window-restore soustitre"></i>
    Vues
</div>

<ul>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/vue/evaluation/soumission'; ?>">Confirmation d'envoi d'une soumission</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/vue/evaluation/evaluation_confirmation_email'; ?>">Courriel de confirmation d'envoi d'une evaluation (v1)</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/vue/evaluation/evaluation_confirmation_email2'; ?>">Courriel de confirmation d'envoi d'une evaluation (v2)</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/vue/evaluation/evaluation_confirmation_email3'; ?>">Courriel de confirmation d'envoi d'une evaluation (v3)</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/vue/evaluation/evaluation_confirmation_email4'; ?>">Courriel de confirmation d'envoi d'une evaluation (v4)</a></li>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/vue/evaluation/evaluation_terminee_enseignant'; ?>">Évaluation terminée par l'enseignant</a></li>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * Les tests
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-cog soustitre"></i>
    Unit Tests
</div>

<? 
 $methodes_tests = array(
     'cs_test'                                  => "Déterminer le nombre de chiffres significatifs d'une valeur",
     'cs_ajustement_test'                       => "Ajuster le nombre de chiffres significatifs d'une valeur",
     'nombre_decimales_test'                    => "Déterminer le nombre de décimales d'un nombre",
     'incertitude_ajustement_test'              => "Ajuster un nombre à son incertitude",
     'determiner_valeurs_variables_test'        => "Détermination des valeurs des variables alétatoires selon des conditions",
     'ns_format_test'                           => "Formatter en notation scientifique",
     'verifier_tags_test'                       => "Vérifier les tags dans les questions",
     'corriger_question_numerique_test'         => "Correction d'une question numérique",
     'corriger_question_litterale_courte3_test' => "Correction d'une question littérale courte (type 3) (v3)",
     'corriger_question_type_9_test'            => "Correction d'une question à réponse numérique par équation (type 9)",
     'lab_corriger_methode_extremes_test'       => "Correction de l'incertitude par la methode des extremes",
     'format_nombre_test'                       => "Formattage d'un nombre",
     'nsdec_test'                               => "Convertir une notation scientifique en notation décimale"
 );
?>

<ul>
    <? foreach($methodes_tests as $m => $m_desc) : ?>

        <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/tests/' . $m; ?>"><?= $m_desc; ?></a></li>

    <? endforeach; ?>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * La maintenance
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-cog soustitre"></i>
    Maintenance
</div>

<ul>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/reponses_multitypes'; ?>">Questions ayant des réponses de plusieurs question_types</a></li>
    <span style="font-size: 0.8em">Ces réponses causent des problèmes lorsqu'on change entre les types 1, 4, 11.</span>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * Le cache
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-cog soustitre"></i>
    KCache
</div>

<ul>
    <li><a class="lien" target="_blank" href="<?= base_url() . 'admin/voir_cache'; ?>">Visualiser le cache</a></li>
</ul>

<?
/* ----------------------------------------------------------------------------
 *
 * La version
 *
 * ---------------------------------------------------------------------------- */ ?>

<div class="mt-4 mb-3" style="font-family: Lato; font-weight: 300; font-size: 1.1em">
    <i class="fa fa-cog soustitre"></i>
    Version actuelle
</div>

<span style="font-size: 0.9em; font-weight: 300">
    Format écran :
        <div class="d-sm-none" style="display: inline">xs</div>
        <div class="d-none d-sm-inline d-md-none">sm</div>
        <div class="d-none d-md-inline d-lg-none">md</div>
        <div class="d-none d-lg-inline d-xl-none">lg</div>
        <div class="d-none d-xl-inline">xl</div><br />
    Environnement : <?= ENVIRONMENT; ?><br />
    Branch : <?= $this->current_branch ?? 'non disponible'; ?><br />
    Commit : <?= $this->current_commit ?? 'non disponible'; ?> [<?= get_current_commit_date(); ?>]<br />
    Database : <?= $this->db->database; ?><br />
    Kcache : <?= $this->config->item('cache_actif_dev') ? 'marche' : ( ! $this->is_DEV && $this->config->item('cache_actif') ? 'marche' : 'arrêt'); ?><br />
    Redis : <?= $this->cache_loaded ? 'marche' : 'arrêt'; ?><br />
    Sous-domaine : <?= $this->sous_domaine; ?></br >
    Connexion : <?= $this->logged_in ? 'connecté' : 'non-connecté';?><br />
    Groupe_id :  <?= $this->groupe_id; ?><br />
    Ecole_id : <?= $this->ecole_id; ?><br />
    Type : <?= @$this->usager['type'] ?: 'inconnu'; ?><br />
    <? if ($this->est_enseignant) : ?>
        Enseignant_id : <?= @$this->enseignant_id ?: 'inconnu'; ?><br />
    <? elseif ($this->est_etudiant) : ?>
        Etudiant_id : <?= @$this->etudiant_id ?: 'inconnu'; ?><br />
    <? endif; ?>
</div>

</div> <!-- #admin-systeme-maintenance -->
