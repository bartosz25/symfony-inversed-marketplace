<?php $view->extend('::frontend_base.html.php') ?>

<h3>Liste des annonces</h3>
<ul>
<?php foreach($cities as $city) { ?>
  <li><a href="<?php echo $view['router']->generate('adsByCity', array('url' => $city['regionUrl'], 'city' => $view['frontend']->makeUrl($city['cityName'])));?>">annonces <?php echo $city['cityName']; ?></a> </li>
<?php } ?>
</ul>

<h3>Liste des offres</h3>
<ul>
<?php foreach($cities as $city) { ?>
  <li><a href="<?php echo $view['router']->generate('offersByCity', array('url' => $city['regionUrl'], 'city' => $view['frontend']->makeUrl($city['cityName'])));?>">offres <?php echo $city['cityName']; ?></a> </li>
<?php } ?>
</ul>

<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'citiesList',
'routeParams' => array())); ?>