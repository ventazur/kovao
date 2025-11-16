<?
/* --------------------------------------------------------------------
 *
 * TABLEAUX - PARAMETRES
 *
 * -------------------------------------------------------------------- */ ?>

<div id="editeur-tableaux-parametres" class="mt-4 mb-4">

    <a class="anchor" name="tableaux-parametres"></a>

    <div id="editeur-tableaux-parametres-titre" class="editeur-section-titre">
        <i class="fa fa-table" style="color: #fff; margin-right: 5px"></i> 
        Tableaux : paramètres
    </div>

    <div id="editeur-tableaux-parametres-contenu" class="editeur-section-contenu">

        <?
        /* ------------------------------------------------------------
         *
         * Options
         *
         * ------------------------------------------------------------ */ ?>

        <div id="tableaux-parametres-options">

            <div class="editeur-section-sous-section-titre mt-0">
                Options
            </div>

            <div class="editeur-section-sous-section" style="padding-bottom: 20px">
                <div class="custom-control custom-switch mt-2">
                    <input name="individuel" id="individuel-activer" class="custom-control-input" type="checkbox" <?= array_key_exists('individuel', $lab_parametres) ? ($lab_parametres['individuel'] ? 'checked' : '') : '' ?> />
                    <label class="custom-control-label" for="individuel-activer">
                        Individuel
                    </label>
                </div>
            </div>

        </div>

        <?
        /* ------------------------------------------------------------
         *
         * Precorrections
         *
         * ------------------------------------------------------------ */ ?>

        <div id="tableaux-parametres-precorrections">

            <div class="editeur-section-sous-section-titre mt-3">
                Précorrections
            </div>

            <div class="editeur-section-sous-section" style="padding-bottom: 20px">
                <div class="custom-control custom-switch mt-2">
                    <input name="precorrection" id="precorrection-activer" class="custom-control-input" type="checkbox" <?= array_key_exists('precorrection', $lab_parametres) ? ($lab_parametres['precorrection'] ? 'checked' : '') : 'checked' ?> />
                    <label class="custom-control-label" for="precorrection-activer">
                        Précorrections
                    </label>
                </div>

                <div class="form-inline mt-3">
                    <label for="precorrection-essais" style="margin-right: 10px">Nombre de précorrections sans pénalité</label>
                    <input name="precorrection_essais" type="number" class="form-control col-sm-2" id="precorrection-essais" value="<?= $lab_parametres['precorrection_essais'] ?? 10; ?>" />
                </div> <!-- .form-inline -->

                <div class="form-inline mt-3">
                    <label for="precorrection-penalite" style="margin-right: 10px">Pénalité pour chaque précorrection supplémentaire</label>
                    <div class="input-group">
                        <input name="precorrection_penalite" type="text" class="form-control col-sm-4" id="precorrection-penalite" value="<?= $lab_parametres['precorrection_penalite'] ? str_replace('.', ',', $lab_parametres['precorrection_penalite']) :  '0,5'; ?>" />
                        <div class="input-group-append">
                            <div class="input-group-text">%</div>
                        </div>
                    </div>
                </div> <!-- .form-inline -->

                <div class="form-inline mt-3">
                    <label for="precorrection-essais" style="margin-right: 10px">Vue</label>
                    <input name="precorrection_essais" type="text" class="form-control col-sm-2" value="<?= $evaluation['lab_vue']; ?>" disabled />
                </div> <!-- .form-inline -->
                
            </div>

        </div>

    </div> <!-- #editeur-tableaux-parametres-contenu -->

</div> <!-- #editeur-tableaux-parametres -->
