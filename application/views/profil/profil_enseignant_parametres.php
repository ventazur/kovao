<?
/* ----------------------------------------------------------------------------
 *
 * Profil enseignant > Parametres
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="profil-enseignant-parametres">

    <?= form_open(base_url() . 'profil/parametres',
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

        Modifier mes <span style="font-weight: 400">paramètres</span>

    </div> <!-- #contenu-titre -->

    <?
    /* ------------------------------------------------------------------------
     *
     * Contenu
     *
     * ------------------------------------------------------------------------ */ ?>

    <div id="profil-contenu" style="font-family: Lato; font-weight: 300">

    	<div class="custom-control custom-switch">
		  	<input type="checkbox" name="cacher_evaluation" class="custom-control-input" id="cacher-evaluation" <?= @$enseignant['cacher_evaluation'] ? 'checked' : '';?>>
          	<label class="custom-control-label" for="cacher-evaluation" style="font-weight: 400">Cacher initialement les évaluations</label>
			<br />
            <div class="profil-explication-parametre">
				Lorsque vous sélectionnez une évaluation pour être rédigée, elle sera initialement cachée. 
			    Ceci permet, par exemple, de planifier une date et une heure sans que celle-ci soit vue par les étudiants.<br />
				N'oubliez pas de la «décacher», à partir de la page d'accueil, une fois planifiée.
			</div>
		</div>

        <?
        /* 
         * Toutes les evaluations exigent desormais par defaut aux etudiants d'etre inscrits (depuis 2023-01-14).
         *
        
        <div class="tspace"></div>

        <div class="custom-control custom-switch">
            <input type="checkbox" name="inscription_requise" class="custom-control-input" id="inscription-requise" <?= @$enseignant['inscription_requise'] ? 'checked' : '';?>>
            <label class="custom-control-label" for="inscription-requise" style="font-weight: 400">Inscription requise pour toutes vos évaluations</label>
            <br />
            <div class="profil-explication-parametre">
                Ceci modifie la préférence indiquée dans chaque évaluation lors de la mise en ligne.
            </div>
        </div>
        */ ?>

        <? if ($this->config->item('evaluation_montrer_rang')) : ?>

            <div class="tspace"></div>

            <div class="custom-control custom-switch">
                <input type="checkbox" name="montrer_rang" class="custom-control-input" id="montrer-rang" <?= @$enseignant['montrer_rang'] ? 'checked' : '';?>>
                <label class="custom-control-label" for="montrer-rang" style="font-weight: 400">Montrer le rang de l'étudiant pour chacune de vos évaluations</label>
                <br />
                <div class="profil-explication-parametre">
                    Ceci ne dévoile en aucune façon les notes des étudiants.
                </div>
            </div>

        <? endif; ?>

        <? if ($this->config->item('evaluation_montrer_ecart_moyenne')) : ?>

            <div class="tspace"></div>

            <div class="custom-control custom-switch">
                <input type="checkbox" name="montrer_ecart_moy" class="custom-control-input" id="montrer-ecart-moy" <?= @$enseignant['montrer_ecart_moy'] ? 'checked' : '';?>>
                <label class="custom-control-label" for="montrer-ecart-moy" style="font-weight: 400">
                    Indiquer l'écart à la moyenne du résultat de l'étudiant sous forme de symbole pour chacune de vos évaluations
                </label>
                <br />
                <div class="profil-explication-parametre">
                    Donne une indication approximative si l'étudiant est autour de la moyenne, au-dessus, très au-dessus, en-dessous ou très en-dessous de la moyenne.<br />
                    Ceci ne dévoile en aucune façon les notes des étudiants, ni la moyenne de l'évaluation.
                </div>
            </div>

        <? endif; ?>

        <? if ($this->config->item('permettre_fichiers_dangereux')) : ?>

            <div class="tspace"></div>

            <div class="custom-control custom-switch">
                <input type="checkbox" name="permettre_fichiers_dangereux" class="custom-control-input" id="permettre-fichiers-dangereux" <?= @$enseignant['permettre_fichiers_dangereux'] ? 'checked' : '';?>>
                <label class="custom-control-label" for="permettre-fichiers-dangereux" style="font-weight: 400">
                    Permettre aux étudiants de téléverser des fichiers dangereux
                </label>
                <br />
                <div class="profil-explication-parametre">
                    Les fichiers dangereux sont les documents Word (.doc, .docx) et Excel (.xls, .xlsx) qui peuvent contenir des macros et exécuter du code arbitraire sur votre ordinateur.
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
                Sauvegarder vos paramètres
                <i class="spinner fa fa-circle-o-notch fa-spin d-none" style="margin-left: 2px"></i>
            </button>
        </div>

    </div> <!-- #profil-contenu-fin -->

    </form>

</div> <!-- #profil-enseignant-parametres -->
