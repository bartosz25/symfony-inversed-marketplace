<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $titleAction)); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant l'ajout de l'offre. ".$message)); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('offersMyList'), 'anchor' => "Mes offres"))); ?>
<?php if($edit) { ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offersEdit', array('id' => $offerId)), 'anchor' => $titleAction)); ?>
<?php } elseif($add) { ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offersAdd', array()), 'anchor' => $titleAction)); ?>
<?php } ?>
<?php $view['slots']->set('js', array('functions.js', 'swfobject.js', 'jquery.uploadify-2.1.4.min.js', 'users/addOffer.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>