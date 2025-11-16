<style>
    #evaluation-soumission-contenu {
        position: relative;
        border: 1px solid #CFD8DC;
        background: #f8f9fa;
        font-family: Lato;
        font-weight: 300;
    }

    #evaluation-soumission-contenu img {
        display: none;
        width: 100px;
        position: absolute;
        right: 0px;
        margin-top: -25px;
        margin-right: -25px;
        opacity: 1;
    }

    #evaluation-soumission-contenu table {
        margin: 0; 
    }
    #evaluation-soumission-contenu tr td {
        border-top: 1px solid #eee;
    }

    #evaluation-soumission-contenu tr:first-child td {
        border-top: 0;
    }
</style>

<div id="soumission">
<div class="container">

    <h3><i class="fa fa-check-circle" style="color: limegreen"></i> Votre évaluation a bien été envoyée !</h3>

    <div class="space"></div>

    <div id="evaluation-soumission-contenu">

        <img src="<?= @$qr_image ?: NULL; ?>">

        <table id="evaluation-soumission-table" class="table">
          <tbody>
            <tr>
              <td>Date et heure d'envoi :</td>
              <td><?= date_humanize($this->now_epoch, TRUE); ?></td>
            </tr>
            <tr>
              <td>Prénom et Nom :</td>
              <td><?= $prenom_nom; ?></td>
            </tr>
            <tr>
              <td><?= empty($this->ecole['numero_da_nom']) ? 'Numéro DA' : $this->ecole['numero_da_nom']; ?></td>
              <td><?= $numero_da; ?></td>
            </tr>
            <tr>
              <td>Référence :</td>
              <td>
                <a href="<?= base_url() . 'consulter/' . $soumission_reference; ?>"><?= $soumission_reference; ?></a>
                <? if (@$permettre_visualisation && @$corrections_terminees) : ?>
                    <span style="color: crimson;">
                        <i class="fa fa-angle-left" style="padding-left: 10px; padding-right: 5px"></i> corrections disponibles
                    </span>
                <? endif; ?>
              </td>
            </tr>
            <tr>
              <td>Empreinte :</td>
              <td><?= $empreinte; ?></td>
            </tr>
          </tbody>
        </table>

    </div>

    <div class="tspace"></div>

    <i class="fa fa-exclamation-circle" style="color: crimson"></i> 
    <? if ($courriel_envoye) : ?>
       Un courriel vous a été envoyé avec ces informations. 
    <? else : ?>
        Veuillez noter la <strong>référence</strong> pour consulter votre évaluation lorsqu'elle sera corrigée.
    <? endif; ?>

    <div class="tspace"></div>

    <h5>Merci</h5>

</div> <!-- .container -->
</div> <!-- #soumission -->
