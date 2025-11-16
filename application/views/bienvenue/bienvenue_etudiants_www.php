<?
/* ----------------------------------------------------------------------------
 *
 * BIENVENUE ETUDIANTS WWW (SANS SOUS DOMAINE)
 *
 * ---------------------------------------------------------------------------- */ ?>

<script src="<?= base_url() . 'assets/js/bienvenue_etudiants.js?v=' . ($this->is_DEV ? $this->now_epoch : $this->current_commit); ?>"></script>

<div id="bienvenue-www">
<div class="container-fluid">

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h4>Trouver une évaluation</h4>

    <div class="tspace"></div>

    <?= form_open(); ?>
        
        <div class="input-group mb-2">
            <input id="vers-evaluation-query" name="vers-evaluation-query" type="text" class="form-control" 
                placeholder="Référence de l'évaluation (6 lettres)" style="border-color: dodgerblue; padding: 22px 15px 22px 15px">
            <div class="input-group-append">
                <span class="input-group-text" id="vers-evaluation" style="padding-left: 20px; padding-right: 20px; border-color: dodgerblue; background-color: dodgerblue; cursor: pointer">
                    <i class="fa fa-search" style="color: #fff"></i>
                </span>
            </div>
        </div>
        <div id="vers-evaluation-helper" class="d-none" style="font-family: Lato; font-weight: 300; font-size: 0.9em; margin-left: 10px; color: crimson;">
            <i class="fa fa-exclamation-circle" style="margin-right: 5px"></i>
            <span id="vers-evaluation-helper-msg"></span>
        </div>

    </form>

</div> <!-- .col-sm-12 col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #bienvenue -->
