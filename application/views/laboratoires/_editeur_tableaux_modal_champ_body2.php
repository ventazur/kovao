<?
/* --------------------------------------------------------------------
 * 
 * GENERATION DYNAMIQUE DU CONTENU DU MODAL POUR MODIFIER UN CHAMP
 *
 * -------------------------------------------------------------------- */ ?>

<?= 
form_open(NULL, array('id' => 'modal-tableau-modifier-champ-form'),
    array(        
        'evaluation_id'   => $evaluation_id,
        'champ'           => $champ
    )
);
?>

<div class="alert alert-danger d-none" role="alert" style="margin: 15px">
    <i class="fa fa-exclamation-circle" style="color: crimson"></i>
    <span class="alert-msg"></span>
</div>

<div id="tableau-modifier-champ-data" class="d-none" 
    data-lab_prefix="<?= $lab_prefix; ?>"
    data-champ="<?= $champ; ?>"
    data-est_incertitude="<?= $lv[$champ]['est_incertitude'] ?? 0; ?>">
</div>

<?
/* --------------------------------------------------------------------
 *
 * Nom du champ
 *
 * -------------------------------------------------------------------- */ ?>

<div class="form-group col-md-12 mt-2">
    <label for="<?= $lab_prefix . '-' . $champ . '-champ'; ?>">Nom du champ</label>
    <input name="nom_champ" class="form-control col-md-6" id="<?= $lab_prefix . '-' . $champ . '-champ'; ?>" type="text" value="<?= $champ; ?>" disabled>
</div>

<?
/* --------------------------------------------------------------------
 *
 * Description du champ
 *
 * -------------------------------------------------------------------- */ ?>

<div class="form-group col-md-12 mt-4">
    <label for="<?= $lab_prefix . '-' . $champ . '-desc'; ?>">Description du champ</label>
    <input name="desc" class="form-control" id="<?= $lab_prefix . '-' . $champ . '-desc'; ?>" value="<?= $lv[$champ]['desc']; ?>" disabled>
</div>

<?
/* --------------------------------------------------------------------
 *
 * Valeur / Notation scientifique / Unites
 *
 * -------------------------------------------------------------------- */ ?>

<div class="form-row col-md-12 mt-4">

    <div class="form-group col-md-4">
        <label for="<?= $lab_prefix . '-' . $champ . '-valeur'; ?>">Valeur</label>

        <div class="input-group">
            <? if ($lv[$champ]['est_incertitude']) : ?>
                <div class="input-group-prepend">
                    <div class="input-group-text">&pm;</div>
                </div>
            <? endif; ?>
            <input name="valeur" type="text" class="form-control"
                id="<?= $lab_prefix . '-' . $champ . '-valeur'; ?>" value="<?= empty($lv[$champ]['valeur']) ? '' : str_replace('.', ',', $lv[$champ]['valeur']); ?>">
        </div>
    </div>

    <div class="form-group col-md-4">
        <label for="<?= $lab_prefix . '-' . $champ . '-nsci'; ?>">Notation scientifique</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">&times;10<span style="font-weight: 400; color: crimson"> <sup>n</sup></span>, &nbsp;<span style="color: crimson">n</span>&nbsp;=</div>
           </div>
            <input name="nsci" class="form-control" id="<?= $lab_prefix . '-' . $champ . '-nsci'; ?>" type="number" value="<?= $lv[$champ]['nsci'] ?? 0; ?>">
        </div>
        <div class="mt-1 ml-1" style="font-size: 0.8em; color: #777;">
            Si n = 0, aucune notation scientifique
        </div>
    </div>

    <div class="form-group col-md-4">
        <label for="<?= $lab_prefix . '-' . $champ . '-unites'; ?>">Unités</label>
        <input name="unites" class="form-control" id="<?= $lab_prefix . '-' . $champ . '-unites'; ?>" type="text" value="<?= $lv[$champ]['unites'] ?? NULL; ?>">
    </div>
</div>

</form>
