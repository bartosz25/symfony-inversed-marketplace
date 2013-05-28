<?php $view->extend('::frontend_base.html.php') ?>

<h1>Annonces</h1>
<?php foreach($categories as $c => $category) { ?>
<a href="<?php echo $view['router']->generate('adsByCategory', array('category' => $category['categoryUrl']));?>"><?php echo $category['categoryName'];?></a><br />
<a href="<?php echo $view['router']->generate('rssAdsByCategoryList', array('category' => $category['categoryUrl']));?>">RSS : <?php echo $category['categoryName'];?></a><br />
<?php } ?>

<h1>Offres</h1>
<?php foreach($categories as $c => $category) { ?>
<a href="<?php echo $view['router']->generate('offersByCategory', array('category' => $category['categoryUrl']));?>"><?php echo $category['categoryName'];?></a><br />
<a href="<?php echo $view['router']->generate('rssOffersByCategoryList', array('category' => $category['categoryUrl']));?>">RSS : <?php echo $category['categoryName'];?></a><br />
<?php } ?>

<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'categoriesList',
'routeParams' => array())); ?>



<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('counter', 'tags', 'ads_right'));?>