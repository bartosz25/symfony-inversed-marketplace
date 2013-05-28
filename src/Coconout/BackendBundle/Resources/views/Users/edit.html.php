<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1 && $edit) { ?>
<p><b>L'utilisateur a été correctement edité.</b></p>
<?php } else { ?>

<?php print_r($formErrors); ?>
<form action="<?php echo $view['router']->generate('usersEdit', array('id' => $id)); ?>" method="post"> 
  <div><label for=""><?php echo $view['form']->label($form['login'], "Login") ?></label><?php echo $view['form']->widget($form['login']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['email'], "E-mail") ?></label><?php echo $view['form']->widget($form['email']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['userProfile'], "Description") ?></label><?php echo $view['form']->widget($form['userProfile']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php } ?>