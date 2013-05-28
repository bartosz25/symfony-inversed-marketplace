<?php $view->extend('::frontend_base.html.php') ?>
<?php if($isSuccess) { ?>
Le mot de passe a été changé.
<?php } else { ?>
<?php if($result) {  ?>
<?php print_r($formErrors);?>
<h1>forgotten credentials</h1>
<form action="<?php echo $view['router']->generate('forgottenConfirm', array('code' => $code)); ?>" method="post">
  <div><label for=""><?php echo $view['form']->label($form['pass1'], "Mot de passe") ?></label><?php echo $view['form']->widget($form['pass1']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['pass2'], "Répétez le mot de passe") ?></label><?php echo $view['form']->widget($form['pass2']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php }  else { ?>
Le code est incorrect.
<?php } ?>
<?php } ?>