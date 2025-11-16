<?
/* ----------------------------------------------------------------------------
 *
 * Outil pour tester : 
 * Question a reponse numerique (TYPE 6)
 *
 * ---------------------------------------------------------------------------- */ ?>

<div id="outil-question-type-6-9">
<div class="container-fluid">
        
<div class="row">

<div class="d-none d-xl-block col-xl-1"></div>
<div class="col-sm-12 col-xl-10">

    <div class="outil-titre">
        <h4>
            Outils 
            <i class="fa fa-angle-right" style="margin-left: 10px; margin-right: 10px"></i> 
            <?= $this->config->item('questions_types')[$type]['desc']; ?>
        </h4>
    </div>

    <div id="outil-fenetre-titre">
        Tester la question et ses paramètres
    </div>

    <div id="outil-fenetre">

        <?  
        $attributes = array('id' => 'question_type_9_form');
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
            <? $texte = json_decode($question['question_texte']) ?: $question['question_texte']; ?>

            <?= nl2br($texte); ?>
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
        
            Réponses :

        </div>

        <div class="hspace"></div>

        <div id="reponses">

            <table>
                <tr>
                    <td>
                        <div class="input-group" style="width: 325px">

                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-check-circle" style="margin-right: 10px; color: limegreen"></i> Réponse : </div>
                            </div>

                            <input name="bonne_reponse" type="hidden" value="<?= $reponse['reponse_texte']; ?>">

                            <div id="bonne-reponse" type="text" class="form-control" value="<?= $reponse['reponse_texte']; ?>" style="text-align: right; background: #f7f7f7">
                                <?= $reponse['reponse_texte']; ?>
                            </div>

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <?= $reponse['unites'] ?: 'u'; ?>
                                </div>
                            </div>

                        </div> <!-- .input-group -->
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="input-group" style="width: 325px">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-times-circle" style="margin-right: 10px; color: crimson"></i>  Réponse : </div>
                            </div>

                            <input name="reponse" id="reponse" type="text" class="form-control" style="text-align: right" required>

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <?= $reponse['unites'] ?: 'u'; ?>
                                </div>
                            </div>

                        </div> <!-- .input-group -->
                    </td>
                </tr>
            </table>

            <div class="qspace"></div>

            <small>
                <i class="fa fa-exclamation-circle" style="margin-right: 5px; color: #bbb"></i> Veuillez entrer une réponse hypothétique, puis recalculez votre pointage.
            </small>


            <div class="space"></div>

        </div> <!-- #reponses -->

        <?
        /* --------------------------------------------------------------------
         *
         * Tolerances
         *
         * -------------------------------------------------------------------- */ ?>

        <div id="tolerances-titre">
        
            Tolérances :
    
        </div>

        <div id="tolerances">

            <div class="hspace"></div>


            <? if (empty($tolerances)) : ?> 

                <i class="fa fa-exclamation-circle" style="color: #bbb; margin-right: 5px"></i>
                Aucune tolérance définie

            <? else : ?>

                <table>
                    <tr style="font-family: Lato; font-weight: 300">
                        <td>Type</td> 
                        <td style="padding-left: 15px">Tolérance</td> 
                        <td style="padding-left: 15px">Pénalité</td> 
                        <td></td> 
                    </tr>
                <?  
                $i = 0;
            
                foreach($tolerances as $t) : 

                    $i++;
                    $tolerance_id = $t['tolerance_id'];
                ?>

                <tr class="tolerance"> 
                    <td style="width: 175px">
                        <select name="type<?= $i; ?>" class="custom-select tolerance-type" id="type<?= $i; ?>" data-type_orig="<?= $t['type']; ?>"; ?>>
                            <option value="1" <?= $t['type'] == 1 ? 'selected' : ''; ?>>Absolue</option>
                            <option value="2" <?= $t['type'] == 2 ? 'selected' : ''; ?>>Relative</option>
                        </select>
                    </td>

                    <td style="padding-left: 15px">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">±</div>
                            </div>
                            <input name="tolerance<?= $i; ?>" id="tolerance<?= $i; ?>" type="text" class="form-control tolerance" value="<?= str_replace('.', ',', @$t['tolerance']); ?>" style="width: 100px; text-align: right">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <? if ($t['type'] == 1) : ?>
                                        <?= $reponse['unites'] ?: 'u'; ?>
                                    <? else : ?>
                                        %
                                    <? endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td style="width: 135px; padding-left: 15px;">
                        <div class="input-group">
                            <input name="penalite<?= $i; ?>" id="penalite<?= $i; ?>" type="number" class="form-control" style="text-align: right" value="<?= @$t['penalite']; ?>">
                            <div class="input-group-append">
                                <div class="input-group-text" style="border-radius: 0 5px 5px 0">%</div>
                            </div>
                        <div>
                    </td>
                    
                    <td>
                        <div class="effacer-tolerance btn btn-outline-danger" style="margin-left: 25px">
                            <i class="fa fa-trash"></i>
                        </div>
                    </td>

                    <td>
                        <div style="margin-left: 15px">
                            <? if ($t['type'] == 2) : ?>
                                Absolue = 
                                <?= str_replace('.', ',', (str_replace(',', '.', $reponse['reponse_texte']) * str_replace(',', '.', $t['tolerance']) / 100)); ?>
                                <?= $reponse['unites'] ?: 'u'; ?>
                            <? endif; ?>
                        </div>
                    </td>
                </tr>
    
                <? endforeach; ?>

                </table>

                <?
                /* ---------------------------------------------------------
                 *
                 * Avertissement de ne pas utiliser deux types de tolerances
                 *
                 * --------------------------------------------------------- */ ?>

                <?
                   if (
                        (array_search(1, array_column($tolerances, 'type')) !== FALSE) &&
                        (array_search(2, array_column($tolerances, 'type')) !== FALSE)
                      ) :
                ?>
                    <div style="font-size: 0.85em; padding-top: 20px;color: crimson">
                        <i class="fa fa-exclamation-circle" style="color: dark-orange"></i>
                        Il est fortement déconseillé d'utiliser deux types de tolérances différentes (absolue et relative). Ceci pourrait mener à une correction imprévue.
                    </div>
                <? endif; ?>


            <? endif; ?>

            <div class="space"></div>

        </div> <!-- #tolerances -->

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

            <div class="qspace"></div>

        </div>

    </form>

    </div> <!-- #outil-fenetre -->

</div> <!-- .col-sm-12 .col-xl-10 -->
<div class="d-none d-xl-block col-xl-1"></div>

</div> <!-- .row -->

</div> <!-- .container-fluid -->
</div> <!-- #outil-tolerances -->
