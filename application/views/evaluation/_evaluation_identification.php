<?
/* ------------------------------------------------------------------------
 *
 * IDENTIFICATION
 *
 * ------------------------------------------------------------------------ */ ?>
<div id="identification-titre">
    Identification
</div>

<div id="identification-contenu">

    <div class="form-row">
        <div class="col-sm-8">
            <label for="evaluation-nom">Prénom et Nom</label>
            <input name="prenom_nom" type="text" class="form-control" id="evaluation-nom" 
                placeholder="Entrez votre prénom et nom" 
                value="<?= $this->est_etudiant ? $this->etudiant['prenom'] . ' ' . $this->etudiant['nom'] : (is_array($traces) && array_key_exists('nom', $traces) && ! empty($traces['nom']) ? $traces['nom'] : ''); ?>" required>
            <div class="invalid-feedback d-none">
                Ce champ est obligatoire.
            </div>
        </div>
        <div class="col-sm-4 mt-3 mt-sm-0">
            <label for="evaluation-numero-da"><?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?></label>

            <input name="numero_da" type="text" class="form-control" id="evaluation-numero-da" 
                placeholder="<?= empty($this->ecole['numero_da_desc']) ? 'Entrez votre numéro DA (9 chiffres)' : $this->ecole['numero_da_desc']; ?>"
                value="<?= $this->est_etudiant && ! empty($this->etudiant['numero_da']) ? $this->etudiant['numero_da'] : (is_array($traces) && array_key_exists('numero_da', $traces) && ! empty($traces['numero_da']) ? $traces['numero_da'] : ''); ?>" required <?= $this->est_etudiant ? 'disabled' : ''; ?>>

            <? if ($this->est_etudiant && empty($this->etudiant['numero_da'])) : ?>
                <small class="form-text text-muted">
                    <i class="fa fa-info-circle" style="margin-right: 5px"></i>
                    Vous pouvez entrer votre <?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?>
                    dans votre <a href="<?= base_url() . 'profil'; ?>">profil</a>.
                </small>
            <? endif; ?>
            <div class="invalid-feedback d-none">
                Ce champ est obligatoire.
            </div>
        </div>
    </div> 

    <div id="alerte-da" class="d-none alert alert-danger" style="border: 0; border-radius: 0; margin-top: 20px; margin-bottom: 0px">
    
        <i class="fa fa-exclamation-circle" style="margin-right: 7px"></i>
        Votre <strong><?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?></strong> ne correspond pas à un étudiant de cet enseignant.

        <div class="mt-3">

            Erreurs possibles :

            <div class="space"></div>

            <div style="line-height: 24px">
            1. Vous avez mal entré votre <?= empty($this->ecole['numero_da_nom']) ? 'numéro DA' : lcfirst($this->ecole['numero_da_nom']); ?>.</br >
            2. Vous avez sélectionné la mauvaise évaluation. Veuillez vérifier les renseignements ci-dessous :<br />
                <div class="pl-4"><li>Enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?> : <?= $enseignant['prenom'] . ' ' . $enseignant['nom']; ?></li></div>
                <div class="pl-4"><li>Cours : <?= $cours['cours_nom_court']; ?> (<?= $cours['cours_code']; ?>)</li></div>
            3. L'enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?> n'a pas entré sa liste d'étudiants dans le système. 
               Ceci est à vérifier avec votre enseignant<?= $enseignant['genre'] == 'F' ? 'e' : ''; ?>.
            </div>

            <div class="space"></div>

            Si vous pensez que ces erreurs ne s'appliquent pas à votre situation, vous pouvez les ignorer et continuer.<br />
            <strong>Cet avertisssement ne vous empêchera pas d'envoyer votre évaluation.</strong>
        </div>

    </div>

</div> <!-- #identification-contenu -->
