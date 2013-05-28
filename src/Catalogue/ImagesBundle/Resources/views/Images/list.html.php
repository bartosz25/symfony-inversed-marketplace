<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes offres")); ?>
              <div class="textContent">
                <p class="addItem"><a href="<?php echo $view['router']->generate('offersImagesAdd', array('id' => 0));?>">ajouter une image</a></p>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des offres"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('CatalogueImagesBundle:Images:userImagesTable.html.php', array('ticket' => $ticket, 'images' => $images, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how, 'id' => $id));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer cette image ?",
'errorText' => "Une erreur s'est produite pendant la suppression de l'image. Veuillez réessayer.", 'okText' => "L'image a été correctement supprimée")); ?>
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offersImagesList', array('how' => $how, 'column' => $column)), 'anchor' => "Mes adresses")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/listOffers.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>