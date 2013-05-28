<?php $view->extend('::frontend_base.html.php') ?>
 
<?php foreach($offers as $o => $offer) { ?>
<a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $view['frontend']->makeUrl($offer['offerName']), 'offerId' => $offer['id_of']));?>"><?php echo $offer['offerName'];?></a><br />
<?php } ?>
 
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersByCategory',
'routeParams' => array('category' => $category))); ?>