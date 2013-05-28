<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1) { ?>
<p><b>Le tag a été correctement edité.</b></p>
<?php } elseif($isSuccess == 2) { ?>
<p><b>Le tag a été correctement ajouté.</b></p>
<?php } else { ?>

<?php print_r($formErrors); ?>
<form action="<?php if($add) {  echo $view['router']->generate('tagsAdd', array()); } else { echo $view['router']->generate('tagsEdit', array('id' => $id)); } ?>" method="post"> 
  <div><label for=""><?php echo $view['form']->label($form['tagName'], "Tag name") ?></label><?php echo $view['form']->widget($form['tagName']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php } ?>