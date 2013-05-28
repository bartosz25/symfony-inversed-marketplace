<?php $view->extend('::frontend_base.html.php') ?>

<?php print_r($formErrors);?>
<h1>forgotten credentials</h1>
<?php if($isSuccess) { ?>
Les informations ont été envoyées.
<?php } else { ?>
<form action="<?php echo $view['router']->generate('forgottenCredentials') ?>" method="post">
  <div><label for=""><?php echo $view['form']->label($form['email'], "E-mail") ?></label><?php echo $view['form']->widget($form['email']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php } ?>