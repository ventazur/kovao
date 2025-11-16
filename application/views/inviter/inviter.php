<div id="groupe-inviter">
<div class="container-fluid">

<div id="groupe-data" data-groupe_id="<?= $this->groupe['groupe_id']; ?>" class="d-none"></div>

<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <h3>
        <i class="fa fa-user-plus" style="margin-right: 10px; color: dodgerblue"></i>
        Inviter une enseignante ou un enseignant sur <span style="font-family: Lato; font-weight: 300;">KOVAO</span>
    </h3> 

    <div class="space"></div>

    <div style="line-height: 1.75">
    <li>Assurez-vous que cette personne soit une enseignante ou un enseignant d'une institution publique ou privée du Québec.</li>
    <li><span style="color: crimson">Inviter un non enseignant, par exemple un étudiant, pourrait vous mériter d'être banni définitivement du site.</span></li>
    <li>Nous enverrons un lien par courriel à votre invité pour lui permettre de s'inscrire.</li>
    <li>Ce lien expirera après 3 jours.</li>
    <li>Si votre invité mentionne qu'il n'a rien reçu, demandez-lui de <span style="color: crimson"><strong>vérifier ses pourriels.</strong></span></li>
    <li>Une fois inscrit, votre invité pourra créer un nouveau groupe, ou joindre les groupes de son choix.</li>
    </div>

    <div class="tspace"></div>

    <?= form_open(base_url() . 'inviter',
            array(),
            array()
        ); ?>

        <div class="form-group">
            <label for="InputEmail1">Entrez l'adresse courriel de l'enseignante ou l'enseignant :</label>
            <input id="courriel" name="courriel" type="email" class="form-control col-sm-4 <?= $errors['courriel']; ?>" id="InputEmail1" aria-describedby="emailHelp" value="<?= set_value('courriel'); ?>" placeholder="Courriel" required>
            <?= form_error('courriel'); ?>
        </div>

        <div class="space"></div>

        <button id="demande-envoyer" type="submit" class="btn btn-primary spinnable">
            Inviter
            <i class="fa fa-paper-plane-o" style="margin-left: 5px"></i>
            <i class="fa fa-circle-o-notch fa-spin d-none spinner" style="margin-left: 10px"></i>
        </button>

    </form>


</div> <!-- .col-sm-12 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #groupe-inviter -->
