<?
/* ----------------------------------------------------------------------------
 *
 * Profil etudiant > Identite
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-etudiant-identite">

    <?= form_open(base_url() . 'profil',
            array(),
            array('etudiant_id' => $etudiant['etudiant_id'])
        ); ?>

    <?
    /* ------------------------------------------------------------------------
     *
     * Titre du contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu-titre">

        Modifier mon <span style="font-weight: 400">identité</span>

    </div> <!-- #contenu-titre -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu">

        <div style="font-family: Lato; font-weight: 300; font-size: 0.9em;">
            Étudiant ID : <?= $etudiant['etudiant_id']; ?>
        </div>

        <div class="form-row">
            <div class="col-md-4 mt-3">
                <label>Nom :</label>
                <input name="nom" type="text" class="form-control <?= @$errors['nom']; ?>" placeholder="Nom de famille" value="<?= $etudiant['nom']; ?>" required>
                <?= form_error('nom'); ?>
            </div>

        </div>

        <div class="form-row">
            <div class="col-md-4 mt-3">
                <label>Prénom :</label>
                <input name="prenom" type="text" class="form-control <?= @$errors['prenom']; ?>" placeholder="Prénom" value="<?= $etudiant['prenom']; ?>" required>
                <?= form_error('prenom'); ?>
            </div>
        </div>

        <div class="form-row d-none">
            <div class="col-md-1 mt-3">
                <label>Genre : </label>
                <select name="genre" class="custom-select">
                    <option value="X" <?= $etudiant['genre'] == 'X' ? 'selected' : ''; ?>>X</option>
                    <option value="M" <?= $etudiant['genre'] == 'M' ? 'selected' : ''; ?>>M</option>
                    <option value="F" <?= $etudiant['genre'] == 'F' ? 'selected' : ''; ?>>F</option>
                </select>
            </div>
        </div>

        <?
        /* --------------------------------------------------------------------
         *
         * Numero DA (Matricule) de l'ecole du groupe
         *
         * -------------------------------------------------------------------- */ ?>
    
        <? if ($this->groupe_id != 0) : ?>
            <div class="form-row">

                <div class="col-md-12 mt-3 mb-2">
                    <label><?= $this->ecole['numero_da_nom'] ?: 'Matricule'; ?></label>
                    <input name="numero_da" type="text" class="form-control col-md-3 <?= @$errors['numero_da']; ?>" placeholder="Votre numéro DA" value="<?= $etudiant['numero_da']; ?>">
                    <? if (empty($etudiant['numero_da']) && ! form_error('numero_da')) : ?>
                        <div style="font-size: 0.8em; color: crimson; margin-top: 8px;">
                            <i class="fa fa-exclamation-circle" style="margin-right: 3px"></i> 
                            Veuillez entrer votre <?= lcfirst($this->ecole['numero_da_nom'] ?: 'Matricule'); ?>
                            du <?= $this->ecole['ecole_nom']; ?>.
                        </div>
                    <? endif; ?>

                    <?= form_error('numero_da'); ?>
                </div>

            </div>
        <? endif; ?>
    
    </div> <!-- #profil-contenu -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Fin du contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu-fin">

        <div>
            <button type="submit" id="sauvegarder-profil" class="btn btn-success">
                <i class="fa fa-save" style="margin-right: 3px"></i> 
                Sauvegarder votre identité
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
            </button>
            <a href="<?= current_url(); ?>" class="btn btn-outline-danger mt-3 mt-sm-0">
                <i class="fa fa-times" style="margin-right: 3px"></i> 
                Annuler
            </a>
        </div>

    </div> <!-- #profil-contenu-fin -->

    </form>

</div> <!-- #profil-etudiant-identite -->

