<?php $view->extend('::frontend_base.html.php') ?>

<h3>Liste des annonces</h3>
<ul>
<?php foreach($ads as $ad) { ?>
  <li><a href="<?php echo $view['router']->generate('adsShowOne', array('url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad'], 'category' => $view['frontend']->makeUrl($ad['categoryUrl'])));?>"><?php echo $ad['adName']; ?></a> </li>
<?php } ?>
</ul>
  
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'adsByCity',
'routeParams' => array('url' => $url, 'city' => $city))); ?>