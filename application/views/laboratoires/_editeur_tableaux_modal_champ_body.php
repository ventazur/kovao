<?
/* --------------------------------------------------------------------
 * 
 * GENERATION DYNAMIQUE DU CONTENU DU MODAL POUR MODIFIER UN CHAMP
 *
 * -------------------------------------------------------------------- */ ?>

<?= 
form_open(NULL, array('id' => 'modal-tableaux-modifier-champ-form'),
    array(        
        'evaluation_id'  => $evaluation_id,
        'champ'          => $champ
    )
);
?>

<div class="alert alert-danger d-none" role="alert" style="margin: 15px">
    <i class="fa fa-exclamation-circle" style="color: crimson"></i>
    <span class="alert-msg"></span>
</div>

<div class="form-group row mt-4" style="padding-left: 15px; padding-right: 15px">

    <?
    /* ----------------------------------------------------------------
     *
     * Description du champ
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="col-sm-7">
        <input  type="text" class="form-control" 
                name="<?= $champ . '-desc'; ?>" 
                id="modal-<?= $champ . '-desc'; ?>" 
                value="<?= $lv[$champ]['desc']; ?>">
    </div> <!-- .col -->

    <?
    /* ----------------------------------------------------------------
     *
     * Valeurs du champ
     *
     * ---------------------------------------------------------------- */ ?>

    <div class="col-sm-5">
        <div class="input-group">

            <?
            /* --------------------------------------------------------
             *
             * Valeur
             *
             * --------------------------------------------------------- */ ?> 

            <input  type="text" class="form-control" 
                    name="<?= $champ . '-valeur'; ?>" 
                    id="modal-<?= $champ . '-valeur'; ?>" 
                    value="<?= str_replace('.', ',', $lv[$champ]['valeur']); ?>" style="text-align: right">

            <?
            /* --------------------------------------------------------
             *
             * Incertitude
             *
             * --------------------------------------------------------- */ ?> 

            <span class="input-group-text" style="margin-left: -1px; margin-right: -1px; border-radius: 0">±</span>
            <input  type="text" class="form-control" 
                    name="<?= $champ . '_d-valeur'; ?>"
                    id="<?= $champ . '_d-valeur'; ?>" 
                    value="<?= str_replace('.', ',', $lv[$champ . '_d']['valeur']); ?>" style="text-align: left">

            <?
            /* --------------------------------------------------------
             *
             * Notation scientifique
             *
             * --------------------------------------------------------- */ ?> 

            <input  type="text" class="form-control" 
                    name="<?= $champ .'-nsci'; ?>" 
                    id="<?= $champ . '-nsci'; ?>" 
                    value="<?= str_replace('.', ',', $lv[$champ]['nsci']); ?>" style="text-align: left">

            <?
            /* --------------------------------------------------------
             *
             * Unites
             *
             * --------------------------------------------------------- */ ?> 

            <input  type="text" class="form-control" 
                    name="<?= $champ . '-unites'; ?>" 
                    id="<?= $champ . '-unites'; ?>" 
                    value="<?= $lv[$champ]['unites']; ?>" style="text-align: left">

        </div> <!-- .input-group -->
    </div> <!-- .col -->

</div> <!-- .form-group -->

</form>
