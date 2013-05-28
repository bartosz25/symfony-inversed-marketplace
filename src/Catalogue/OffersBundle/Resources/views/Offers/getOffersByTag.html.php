<?php $view->extend('::frontend_base.html.php') ?>

<h3>Liste des offres</h3>
<ul>
<?php foreach($offers as $offer) { ?>
  <li><a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $view['frontend']->makeUrl($offer['offerName']), 'offerId' => $offer['id_of']));?>"><?php echo $offer['offerName']; ?></a> </li>
<?php } ?>
</ul>
  
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersByTags',
'routeParams' => array('url' => $params['url'], 'tag' => $params['tag']))); ?>