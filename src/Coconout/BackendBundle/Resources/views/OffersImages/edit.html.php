<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1) { ?>
<p><b>L'image a été correctement editée.</b></p>
<?php } else { ?>
<p>L'image actuelle : <img src="/images/offers/<?php echo $image['id_of'];?>/small_<?php echo $image['imageName'];?>" /></p>
<form id="AddImage" enctype="multipart/form-data" action="<?php echo $view['router']->generate('offersImgEdit', array('id' => $imageId));?>"  method="post"> 
  <input type="file" name="imageName" id="imageName" required="true" />
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form> 
<?php } ?>