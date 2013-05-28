<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes catalogues")); ?>
              <div class="textContent">
                <p class="addItem"><a href="<?php echo $view['router']->generate('catalogueAdd');?>">ajouter un catalogue</a></p>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des catalogues"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('CatalogueOffersBundle:Catalogues:cataloguesTable.html.php', array('ticket' => $ticket, 'catalogues' => $catalogues, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer ce catalogue ?",
'errorText' => "Une erreur s'est produite pendant la suppression du catalogue. Veuillez réessayer.", 'okText' => "Le catalogue a été correctement supprimé")); ?>
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('catalogueMyList', array('how' => $how, 'column' => $column, 'page' => $page)), 'anchor' => "Mes catalogues")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>