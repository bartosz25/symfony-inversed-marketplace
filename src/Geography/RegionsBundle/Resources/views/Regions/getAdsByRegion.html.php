<?php $view->extend('::frontend_base.html.php') ?>
 
<?php foreach($ads as $a => $ad) { ?>
<a href="<?php echo $view['router']->generate('adsShowOne', array('category' => $ad['categoryUrl'], 'url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad']));?>"><?php echo $ad['adName'];?></a><br />
<?php } ?>
 
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'adsByRegion',
'routeParams' => array('url' => $region['regionUrl']))); ?>