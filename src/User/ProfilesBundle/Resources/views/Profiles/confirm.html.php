<?php $view->extend('::frontend_base.html.php') ?>


<h1>confirm register</h1>
<?php if($codeValid) { ?>
Votre compte a été activé.
<?php } else { ?>
Le code d'activation est incorrect.
<?php } ?>