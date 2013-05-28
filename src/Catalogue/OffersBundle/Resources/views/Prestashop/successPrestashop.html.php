<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
              <div class="textContent">
<?php $textAdded = "1 produit a été rajouté à vos catalogues."; if($offersAdded == 0) { $textAdded = "Aucun produit n'a été rajouté à vos catalogue."; } elseif($offersAdded != 1) { $textAdded = "$offersAdded produits ont été rajoutés à vos catalogues.";} ?>
<?php $textEdited = "1 produit déjà rajouté a été modifié."; if($offersEdited == 0) { $textEdited = "Aucun produit déjà rajouté n'a pas été modifié."; } elseif($offersEdited != 1) { $textEdited = "$offersEdited produits déjà rajoutés ont été modifiés.";} ?>
<?php echo $view->render('::frontend_ok_box.html.php', array('text' => $textAdded.$textEdited));?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('synchronizeSuccessPrestashop', array()), 'anchor' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array('list.css'));?>