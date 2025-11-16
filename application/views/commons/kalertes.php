<?
/* --------------------------------------------------------------------
 *
 * K ALERTES
 *
 * -------------------------------------------------------------------- */ ?>

<? if ( ! empty($kalertes)) : ?>

    <? foreach($kalertes as $ka) : ?>

        <? if ( ! array_key_exists('msg', $ka)) continue; ?>

        <div class="row">
            <div class="col-12">

                <div class="alert" role="alert" style="margin-left: 25px; margin-right: 25px; color: crimson; border: 1px solid crimson; background: #FFEBEE; font-family: Lato; font-weight: 300">
                    <i class="bi-exclamation-circle" style="margin-right: 8px"></i>
                    <?= $ka['msg']; ?>
                    <? /*
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true" style="color: crimson">&times;</span>
                    </button>
                    */ ?>
                </div>

                <div class="qspace"></div>
                <div class="hspace"></div>

            </div> <!-- .col -->
        </div> <!-- .row -->

    <? endforeach; ?>

<? endif; ?>
