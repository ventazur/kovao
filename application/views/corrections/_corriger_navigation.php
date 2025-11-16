<?
/* ------------------------------------------------------------------------
 *
 * CORRIGER - NAVIGATION (SIDEBAR)
 *
 * ------------------------------------------------------------------------ */ ?>
<nav class="sidebar">

    <div class="table-wrap">

        <table class="table" style="margin: 0">
            <thead>
                <tr>
                    <th colspan="2">
                        <a href="#top" style="display: block; text-decoration: none">
                            Navigation
                        </a>
                    </th>
                </tr>
            </thead>

            <tbody>

            <? $i = 1;

               foreach ($questions_a_corriger as $q) : 
                   
                   $pair = ($i % 2 == 0) ? TRUE : FALSE;
            ?>

                <?= $pair ? '' : '<tr>'; ?>

                    <td id="q<?= $q['question_id']; ?>box" style="text-align: center; width: 50%;" class="<?= $q['corrigee'] ? 'corrigee' : ''; ?>">

                        <a href="#question-q<?= $q['question_no']; ?>" style="display: block; text-decoration: none">
                            <div style="width: 100%; height: 100%">
                                <?= 'Q' . $q['question_no']; ?>
                            </div>
                        </a>
                    </td>

                <? // Nous sommes au dernier element du tableau ?>
                
                <? if (count($questions_a_corriger) == $i) : ?>

                    <td style="text-align: center; width: 50%;"></td>

                <? endif; ?>

                <?= $pair ? '<tr>' : ''; ?>

            <? $i++; endforeach; ?>

            </tbody>

        </table>

    </div>
</nav>
