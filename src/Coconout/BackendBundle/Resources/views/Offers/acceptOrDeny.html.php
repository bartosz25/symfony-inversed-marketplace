<?php $view->extend('::backend_base.html.php') ?>


<form id="AddAd" action="<?php echo $view['router']->generate('adsAcceptOrDeny', array('actionName' => $actionName, 'id' => $adId)); ?>" method="post"> 
  Message to author : 
  <textarea name="body" id="body"><?php echo $template;?></textarea>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>