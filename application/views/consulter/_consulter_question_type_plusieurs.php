<? 
/* ------------------------------------------------------------------------
 * 
 * Reponse repondue
 *
 * Plusieurs types de questions
 *
 * ------------------------------------------------------------------------ */ ?>

<div class="corriger-reponse-repondue">

    <div class="font-weight-bold">
        Votre réponse : 
    </div>

    <div class="hspace"></div>

    <? 
    /* --------------------------------------------------------
     * 
     * Question a developpement (TYPE 2)
     * Question a developppement court (TYPE 12)
     *
     * --------------------------------------------------------- */ ?>

    <? if (in_array($q['question_type'], array(2, 12))) : ?>

        <? if (@$q['question_non_repondue']) : ?>

            Vous n'avez pas répondu à cette question.

        <? else : ?>

            <div>
                <?= nl2br(filter_symbols($q['reponse_repondue'])); ?>
            </div>

        <? endif; ?>

    <? 
    /* --------------------------------------------------------
     * 
     * Question a choix multiples (TYPE 4)
     * Question a choix multiples stricte (TYPE 11)
     *
     * --------------------------------------------------------- */ ?>

    <? elseif ($q['question_type'] == 4 || $q['question_type'] == 11) : ?>

        <? if (empty($q['reponse_repondue'])) : ?>

            Vous n'avez rien répondu à cette question.

        <? else : ?>
    
            <? if (is_array($q['reponse_repondue'])) : ?>

                <? foreach($q['reponse_repondue'] as $r_id) : ?>

                    <div>
                        <?= filter_symbols($q['reponse_repondue_texte'][$r_id]); ?>
                    </div>

                <? endforeach; ?>

            <? endif; ?>

        <? endif; ?>

    <? else : ?>

        <? 
        /* --------------------------------------------------------
         * 
         * Les autres types de questions
         *
         * TYPES: 1, 3, 5, 6, 7, 9
         *
         * --------------------------------------------------------- */ ?>

        <div>
            <? if (@$q['question_non_repondue']) : ?>

                Vous n'avez pas répondu à cette question.

            <? else : ?>

                <?= filter_symbols($q['reponse_repondue_texte']); ?>

            <? endif; ?>
        </div>

    <? endif; ?>

</div> <!--- .corriger-reponse-repondue -->
