<?
/* ----------------------------------------------------------------------------
 *
 * Profil enseignant > Identite
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-enseignant-identite">

    <?= form_open(base_url() . 'profil',
            array(),
            array('enseignant_id' => $enseignant['enseignant_id'])
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

        <div class="form-row">

            <div class="col-md-4 mb-2">
                <label>Nom :</label>
                <input name="nom" type="text" class="form-control <?= @$errors['nom']; ?>" placeholder="Nom de famille" value="<?= $enseignant['nom']; ?>" required>
                <?= form_error('nom'); ?>
            </div>

        </div>

        <div class="form-row">

            <div class="col-md-4 mt-2">
                <label>Prénom :</label>
                <input name="prenom" type="text" class="form-control <?= @$errors['prenom']; ?>" placeholder="Prénom" value="<?= $enseignant['prenom']; ?>" required>
                <?= form_error('prenom'); ?>
            </div>

        </div>

        <div class="hspace"></div>

        <div class="form-row d-none">
            <div class="col-md-1 mt-2 mb-2">
                <label>Genre : </label>
                <select name="genre" class="custom-select">
                    <option value="X" <?= $enseignant['genre'] == 'X' ? 'selected' : ''; ?>>X</option>
                    <option value="M" <?= $enseignant['genre'] == 'M' ? 'selected' : ''; ?>>M</option>
                    <option value="F" <?= $enseignant['genre'] == 'F' ? 'selected' : ''; ?>>F</option>
                </select>
            </div>
        </div>

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

</div> <!-- #profil-enseignant-identite -->

