<?php $view->extend('::frontend_base.html.php') ?>


<h1>test register</h1>
<?php if($isSuccess == 1) { ?>
Enregistrement réussi.
<?php } else { ?>
<?php print_r($formErrors); ?>
<form action="<?php echo $view['router']->generate('register') ?>" method="post">
  <div><label for=""><?php echo $view['form']->label($form['login'], "Login") ?></label><?php echo $view['form']->widget($form['login']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['pass1'], "Mot de passe") ?></label><?php echo $view['form']->widget($form['pass1']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['pass2'], "Répétez le mot de passe") ?></label><?php echo $view['form']->widget($form['pass2']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['email'], "E-mail") ?></label><?php echo $view['form']->widget($form['email']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php } ?>