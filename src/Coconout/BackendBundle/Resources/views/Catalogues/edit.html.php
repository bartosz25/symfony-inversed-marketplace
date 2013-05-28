<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1 && $add) { ?>
<p><b>Le catalogue a été correctement ajouté.</b></p>
<?php } elseif($isSuccess == 1 && $edit) { ?>
<p><b>Le catalogue a été correctement edité.</b></p>
<?php } else { ?>
<?php print_r($formErrors); ?>
<?php echo $view->render('CatalogueOffersBundle:Catalogues:addForm.html.php', array('formErrors' => $formErrors,
'add' => false, 'backoffice' => true, 'catalogueId' => $catalogueId, 'form' => $form)); ?>
<?php } ?>