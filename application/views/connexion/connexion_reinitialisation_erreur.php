<div id="clefenvoyee">
<div class="container">

    <h3><i class="fa fa-times-circle" style="color: crimson"></i> Erreur de réinitialisation de votre mot-de-passe</h3>

    <div class="tspace"></div>

    <? if ( ! empty($flash_message) && is_array($flash_message)) : ?>

        <?= $flash_message['message']; ?>

    <? else : ?>

        <strong>Dispositions inconnus</strong>

    <? endif; ?>

</div> <!-- .container -->
</div>
