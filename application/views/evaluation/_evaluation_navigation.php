<?
/* ------------------------------------------------------------------------
 *
 * EVALUATION - NAVIGATION (SIDEBAR)
 *
 * ------------------------------------------------------------------------ */ ?>
<nav class="sidebar">

    <? 
        $q_par_col = floor(count($questions) / 2); 
        $pair      = (count($questions) % 2 == 0) ? 0 : 1;
    ?>

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

            <? for($i = 1; $i <= ($q_par_col + $pair); $i++) : ?>

                <tr>
                    <td id="q<?= $i; ?>box" style="text-align: center; width: 50%;">

                        <a href="#q<?= $i; ?>" style="display: block; text-decoration: none">
                            <div style="width: 100%; height: 100%">
                                <?= 'Q' . $i; ?>
                            </div>
                        </a>

                    </td>
                    <td id="q<?= $i + $q_par_col + $pair; ?>box" style="text-align: center; width: 50%;">

                        <? if ( ! ($i > $q_par_col)) : ?>
                            <a href="#q<?= $i + $q_par_col + $pair; ?>" style="display: block; text-decoration: none">
                                <div style="width: 100%; height: 100%">
                                <?= 'Q' . ($i + $q_par_col + $pair); ?>
                                </div>
                            </a>
                        <? endif; ?>

                    </td>
                </tr> 

            <? endfor; ?>

            </tbody>

        </table>

    </div>
</nav>
