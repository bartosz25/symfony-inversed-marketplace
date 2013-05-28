<?php $view->extend('::frontend_base.html.php') ?>
<?php foreach($users as $user) { ?>
<a href="<?php echo $view['router']->generate('userProfile', array('id' => $user['id_us'], 'url' => $view['frontend']->makeUrl($user['login']))); ?>"><?php echo $user['login'];?></a>
<?php } ?>

<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'usersList',
'routeParams' => array())); ?>