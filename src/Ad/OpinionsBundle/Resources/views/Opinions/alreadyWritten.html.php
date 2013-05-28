<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Ecrire une opinion")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_error_box.html.php', array('text' => "Le commentaire a déjà été écrit pour cette commande.")); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('ordersList'), 'anchor' => "Mes commandes"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('opinionWrite', array('id' => $id)), 'anchor' => "Ecrire une opinion")); ?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>