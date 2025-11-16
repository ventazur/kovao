<?
/* ----------------------------------------------------------------------------
 *
 * Outil pour tester une question a reponse litterale courte (TYPE 7) 
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="outil-question-type-7">
<div class="container-fluid">
        
<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="outil-titre">
        <h4>
            Outils 
            <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 10px"></i> 
            <?= $this->config->item('questions_types')[7]['desc']; ?>
        </h4>
    </div>

    <div id="outil-fenetre-titre">
        Tester la question et ses paramètres
    </div>

    <div id="outil-fenetre">

        <?  
        $attributes = array('id' => 'question_type_7_form');
        echo form_open(NULL, $attributes);  
        ?>

        <?
        /* --------------------------------------------------------------------
         *
         * Question
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="question-texte-titre">
            Question :
        </div>

        <div class="hspace"></div>

        <div id="question-texte">
            <?= json_decode($question['question_texte']); ?>
            <input name="question_id" type="hidden" value="<?= $question['question_id']; ?>">
        </div>

        <div class="space"></div>

        <?
        /* --------------------------------------------------------------------
         *
         * Reponses
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="reponses-titre">
        
            Réponses acceptées :
    
        </div>

        <div class="hspace"></div>

        <div id="reponses">

            <table>

                <? foreach($reponses as $r) : ?>
                <tr>
                    <td>
                        <div class="input-group" style="width: 600px">

                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-check-circle" style="margin-right: 10px; color: limegreen"></i> Réponse : </div>
                            </div>

                            <input name="bonnes_reponses[]" type="hidden" value="<?= $r['reponse_texte']; ?>">

                            <div id="bonne-reponse" type="text" class="form-control" value="<?= $r['reponse_texte']; ?>" style="background: #f7f7f7">
                                <?= $r['reponse_texte']; ?>
                            </div>

                        </div> <!-- .input-group -->
                    </td>
                </tr>
                <? endforeach; ?>

            </table>

            <div class="space"></div>

        </div> <!-- #reponses -->

        <?
        /* --------------------------------------------------------------------
         *
         * Reponses hypothetiques
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="reponses-hypothetiques-titre">
            <span id="rh">Réponse hypothétique de l'étudiant :</span>
            <span id="ra" class="d-none">Réponses acceptables :</span>
        </div>

        <div class="hspace"></div>

        <div id="reponses-hypothetiques">

            <textarea name="reponses_hypothetiques" id="reponses-hypothetiques-textarea" class="form-control" rows="3" placeholder="Entrez une réponse par ligne"></textarea>

        </div> <!-- #reponses-hypothetiques -->

        <div style="margin-top: 15px; font-size: 0.9em; padding: 10px; border: 1px solid #ccc; border-radius: 3px;">
            Si vous entrez une seule réponse, la <strong>similarité calculée</strong> sera affichée.
            Si vous entrez plusieurs réponses acceptables (une réponse par ligne), la <strong>similarité suggérée</strong> sera affichée.
            <div class="hspace"></div>
            La <strong>similarité calculée</strong> permet de vérifier si la question est réussie, ou non, en la comparant avec la similarité que vous avez choisie. La valeur indique ce qu'aurait pu être la similarité minimum pour obtenir tous les points.
            La <strong>similiarité suggérée</strong> permet de vous guider dans votre choix de similarité en considérant la plus petite similarité possible pour accepter toutes les réponses acceptables en tant que réponse correcte.
        </div>

        <div class="space"></div>

        <?
        /* --------------------------------------------------------------------
         *
         * Similarite
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="similarite-titre">
        
            Similarité :
    
        </div>

        <div id="similarite">

            <div class="hspace"></div>

            <? if (empty($similarite)) : ?> 

                <i class="fa fa-exclamation-circle" style="color: #bbb; margin-right: 5px"></i>
                Aucune similarité définie

            <? else : ?>

                <table> 
                    <tr> 
                        <td style="width: 250px">
                            <div class="input-group">

                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa fa-flask" style="margin-right: 10px;"></i> Similarité : 
                                    </div>
                                </div>

                                <input name="similarite" id="similarite" type="number" class="form-control" value="<?= $similarite; ?>" style="text-align: right" required>

                                <div class="input-group-append">
                                    <div class="input-group-text">%</div>
                                </div>

                            </div>
                        </td> 

                        <td style="width: 300px">
                            <div style="margin-left: 15px; color: crimson">

                                <div id="similarite-calculee-wrap" class="d-none">
                                    Similarité calculée =
                                    <span id="similarite-calculee"></span> %
                                </div> 

                                <div id="similarite-suggeree-wrap" class="d-none">
                                    Similarité suggérée <
                                    <span id="similarite-suggeree"></span> %
                                </div> 

                            </div>
                        </td>
                    </tr> 
                </table>

            <? endif; ?>

            <div class="space"></div>

        </div> <!-- #similarite -->

        <?
        /* --------------------------------------------------------------------
         *
         * Pointage
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="pointage-titre">

            Pointage :

        </div>

        <div class="hspace"></div>

        <div id="pointage-contenu">

            <div class="btn btn-warning" style="width: 100px">
                <span id="pointage" style="color: crimson">?</span> / 10
            </div>

            <div id="calculer-pointage" class="btn btn-secondary spinnable" style="margin-left: 10px;">
                <i class="fa fa-bullseye" style="margin-right: 5px"></i>
                Recalculer votre pointage
                <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
            </div>

            <div class="btn btn-outline-secondary spinnable" onclick="document.location.reload(true);">
                <i class="fa fa-refresh" style="margin-right: 5px"></i>
                Rafraîchir
                <i class="fa fa-circle-o-notch fa-spin spinner d-none" style="margin-left: 5px"></i>
            </div>
            <div class="qspace"></div>

        </div>

    </form>

    </div> <!-- #outil-fenetre -->

</div> <!-- .col-sm-12 .col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outil-tolerances -->
