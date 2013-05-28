<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => $titleBox)); ?>
              <div class="textContent">
  <?php if($isSuccess == 1 && $add) { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le catalogue a été correctement ajouté"));?>
  <?php } elseif($isSuccess == 1 && $edit) { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le catalogue a été correctement modifié"));?>
  <?php } elseif($isSuccess == 0) { ?>
    <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
  <?php } ?>
  <?php echo $view->render('CatalogueOffersBundle:Catalogues:addForm.html.php', array('formErrors' => $formErrors,
  'add' => $add, 'catalogueId' => $catalogueId, 'form' => $form, 'backoffice' => false)); ?>
              </div><!-- textContent-->
  <?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('catalogueMyList', array()), 'anchor' => "Mes catalogues" ))); ?>
  <?php if($add) { ?>
    <?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('catalogueAdd', array()), 'anchor' => $titleBox)); ?>
  <?php } elseif($edit) { ?>
    <?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('catalogueEdit', array('id' => $catalogueId)), 'anchor' => $titleBox)); ?>
  <?php } ?>
<?php $view['slots']->set('js', array('functions.js', 'users/adCatalogue.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array(''));?>