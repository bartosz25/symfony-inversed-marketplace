<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $catalogue["catalogueName"])); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des offres"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('CatalogueOffersBundle:Catalogues:catalogueTable.html.php', array('class' => $class, 'catalogueId' => $id, 'catalogueUrl' => $url,'offers' => $offers, 'page' => $page, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('offersAll', array()), 'anchor' => "Les offres")));?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('catalogueShow', array('page' => $page, 'url' => $url, 'id' => $id, 'column' => $column, 'how' => $how)), 'anchor' => $catalogue["catalogueName"])); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>