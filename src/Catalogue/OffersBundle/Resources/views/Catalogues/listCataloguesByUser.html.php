<?php $view->extend('::frontend_base.html.php') ?>

<?php foreach($catalogues as $c => $catalogue) { ?>
<p><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $view['frontend']->makeUrl($catalogue['catalogueName']), 'id' => $catalogue['id_cat']));?>"><?php echo $catalogue['catalogueName'];?></a></p>
<?php } ?>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'cataloguesByUser',
'routeParams' => array('id' => $userId))); ?>