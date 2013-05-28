<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Offres pour mes annonces")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement de offres"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('AdItemsBundle:AdsOffers:offersTableAdsUser.html.php', array('ticket' => $ticket, 'offers' => $offers, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer cette offre ?",
'errorText' => "Une erreur s'est produite pendant la suppression de l'offre. Veuillez réessayer.", 'okText' => "L'offre a été supprimée")); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsShowOffersList', array()), 'anchor' => "Offres pour mes annonces")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>