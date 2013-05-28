<?php $view->extend('::frontend_base.html.php') ?>

<h3>Liste des annonces</h3>
<ul>
<?php foreach($regions as $region) { ?>
  <li><a href="<?php echo $view['router']->generate('adsByRegion', array('url' => $region['regionUrl']));?>">annonces <?php echo $region['regionName']; ?></a> (<?php echo $region['countryName']; ?>)</li>
<?php } ?>
</ul>

<h3>Liste des offres</h3>
<ul>
<?php foreach($regions as $region) { ?>
  <li><a href="<?php echo $view['router']->generate('offersByRegion', array('url' => $region['regionUrl']));?>">annonces <?php echo $region['regionName']; ?></a> (<?php echo $region['countryName']; ?>)</li>
<?php } ?>
</ul>