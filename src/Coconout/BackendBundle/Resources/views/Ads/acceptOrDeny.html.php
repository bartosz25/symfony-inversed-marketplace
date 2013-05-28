<?php $view->extend('::backend_base.html.php') ?>


<form id="AddAd" action="<?php echo $view['router']->generate('adsAcceptOrDeny', array('actionName' => $actionName, 'id' => $adId)); ?>?ticket=<?php echo $ticket;?>" method="post"> 
  Message to author : 
  <textarea name="body" id="body"><?php echo $template;?></textarea>
  <?php if($isHome) { ?>
  <p>Ad will be displayed at home page. Do you accept it ?
    <input type="radio" name="atHomePage" value="0" />no
    
    <input type="radio" name="atHomePage" value="1" />yes
  </p>
  <?php } else { ?>
  <input type="hidden" name="atHomePage" value="0" />
  <?php } ?>
  <p><a href="<?php echo $view['router']->generate('adsEdit', array('id' => $adId)); ?>" target="_blank">edit ad</a></p>
  <input type="submit" />
</form>