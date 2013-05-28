<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1) { ?>
<p><b>Le tag a été correctement edité.</b></p>
<?php } else { ?>

<?php print_r($formErrors); ?>
<form action="<?php echo $view['router']->generate('questionsEdit', array('id' => $id)); ?>" method="post"> 
  <div><label for=""><?php echo $view['form']->label($form['questionTitle'], "Question title") ?></label><?php echo $view['form']->widget($form['questionTitle']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['questionText'], "Question description") ?></label><?php echo $view['form']->widget($form['questionText']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php } ?>