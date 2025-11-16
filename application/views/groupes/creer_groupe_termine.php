<div id="groupes-creer=termine">
<div class="container-fluid">
        
<div class="row">
    <div class="d-none d-xl-block col-xl-1"></div>
    <div class="col-sm-12 col-xl-10">

    <h4><i class="fa fa-check" style="color: limegreen; margin-right: 5px"></i> Votre groupe a été créé !</h4>

    <div class="tspace"></div>

    <div>
        Voilà, tout est prêt !
    </div>

    <div class="hspace"></div>

    <div>
        Veuillez utiliser le lien officiel suivant pour accéder votre groupe :
        <a class="badge badge-primary" style="margin-left: 5px; font-size: 0.9em; padding: 8px 10px 8px 10px; font-weight: 300" 
           href="https://<?= $nouveau_groupe['sous-domaine']; ?>.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>">
            https://<?= $nouveau_groupe['sous-domaine']; ?>.kovao.<?= $this->is_DEV ? 'dev' : 'com'; ?>
        </a>
        </span>
    </div> 

    </div> <!-- .col-sm-12 -->
    <div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #groupes-creer -->
