<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Les annonces {$tag}")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des annonces"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('AdItemsBundle:Items:adsTable.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how, "routeName" => $routeName, 'routeParams' => $routeParams));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsByTags', array('page' => $page, 'column' => $column, 'how' => $how, 'url' => $params['url'], 'tag' => $params['tag'])), 'anchor' => "Les annonces {$tag}")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>