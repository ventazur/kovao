<div id="consulter">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>Consulter votre évaluation corrigée</h4>

    <div class="tspace"></div>

    <?= form_open(base_url() . 'consulter'); ?>

        <div class="input-group mb-2">
            <input id="input-reference" name="reference" type="text" class="form-control <?= $errors['reference'] ? 'is-invalid' : ''; ?>" 
                placeholder="Référence de la soumission (8 lettres)" style="border-color: <?= $errors['reference'] ? 'crimson;' : 'dodgerblue;'; ?> padding: 22px 15px 22px 15px">
            <div class="input-group-append">
                <button type="submit" class="input-group-text" id="vers-corrections" style="padding-left: 20px; padding-right: 20px; border-color: dodgerblue; background-color: dodgerblue; cursor: pointer">
                    <i class="fa fa-search" style="color: #fff"></i>
                </button>
            </div>
        </div>

        <div class="<?= $errors['reference'] ? '' : 'd-none'; ?>" style="color: crimson; font-size: 0.8em">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
            Cette référence est invalide.
        </div>

    </form>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #consulter -->
