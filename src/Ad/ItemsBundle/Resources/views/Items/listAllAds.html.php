<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Les annonces")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des annonces"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php if($template == "ad") { ?>
  <?php echo $view->render('AdItemsBundle:Items:adsTable.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager, 'class' => $class,
  'column' => $column, 'how' => $how, "routeName" => 'search', 'routeParams' => $routeParams));?>
<?php } elseif($template == "offer") { ?>
  <?php echo $view->render('AdItemsBundle:Items:adsTable.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager, 'class' => $class,
  'column' => $column, 'how' => $how, "routeName" => 'adsAll', 'routeParams' => $routeParams));?>
<?php } ?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsAll', array('page' => $page, 'column' => $column, 'how' => $how)), 'anchor' => "Les annonces")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>