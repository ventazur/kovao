<?
/* ----------------------------------------------------------------------------
 *
 * Profil etudiant > Parametres
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-etudiant-parametres">

    <?= form_open(base_url() . 'profil/parametres',
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

        Modifier mes <span style="font-weight: 400">paramètres</span>

    </div> <!-- #contenu-titre -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu" style="font-family: Lato; font-weight: 300">

        <? if (1 == 2) : ?>

            <div class="custom-control custom-switch">
                <input type="checkbox" name="courriel_evaluation_envoyee" class="custom-control-input" id="courriel-evaluation-envoyee" <?= @$etudiant['courriel_evaluation_envoyee'] ? 'checked' : '';?>>
                <label class="custom-control-label" for="courriel-evaluation-envoyee" style="font-weight: 400">
                    Obtenir un courriel de confirmation après l'envoi de vos évaluations
                </label>
                <div class="profil-explication-parametre">
                    Après chaque évaluation envoyée, vous recevrez un courriel mentionnant le titre de l'évaluation, la date d'envoi,
                    la référence et l'empreinte.
                </div>
            </div>

            <div class="tspace"></div>

        <? endif; ?>

        <? if ($this->config->item('evaluation_montrer_rang_cours')) : ?>

            <div class="custom-control custom-switch">
                <input type="checkbox" name="montrer_rang_cours" class="custom-control-input" id="montrer-rang-cours" <?= @$etudiant['montrer_rang_cours'] ? 'checked' : '';?>>
                <label class="custom-control-label" for="montrer-rang-cours" style="font-weight: 400">
                    Montrer mon rang pour chaque cours
                </label>
                <div class="profil-explication-parametre">
                    Votre classement parmi tous les étudiants d'un même cours et d'un(e) même enseignant(e).
                </div>
            </div>

            <div class="tspace"></div>

        <? endif; ?>

        <div class="custom-control custom-switch">
            <input type="checkbox" name="montrer_rang_evaluation" class="custom-control-input" id="montrer-rang-evaluation" <?= @$etudiant['montrer_rang_evaluation'] ? 'checked' : '';?>>
            <label class="custom-control-label" for="montrer-rang-evaluation" style="font-weight: 400">
                Montrer mon rang pour chaque évaluation
            </label>
            <br />
            <div class="profil-explication-parametre">
                Votre classement parmi tous les étudiants ayant fait une même évaluation.<br />
                Si votre rang n'apparaît pas, il se pourrait que votre enseignant(e) ait désactivé cette option.
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
                Sauvegarder vos paramètres
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
            </button>
        </div>

    </div> <!-- #profil-contenu-fin -->

    </form>

</div> <!-- #profil-enseignant-parametres -->
