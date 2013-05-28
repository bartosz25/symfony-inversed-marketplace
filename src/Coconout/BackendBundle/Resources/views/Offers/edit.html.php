<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1) { ?>
<p><b>L'offre a été correctement editée.</b></p>
<?php } else { ?>
 <?php echo $view->render('CatalogueOffersBundle:Offers:addForm.html.php', array('isSuccess' => $isSuccess,
'edit' => true, 'add' => false, 'zones' => $zones, 'offerImages' => $offerImages, 'canAddImages' => $canAddImages,
'fees' => $fees, 'tmpId' => 0, 'dir' => $dir, 'offerId' => $offerId, 'formErrors' => $formErrors, 'form' => $form, 'formFields' => $formFields,
'adRow' => array('category' => '', 'country' => '', 'objectState' => '', 'city' => '', 'region' => '', 'priceFrom' => '', 'priceTo' => ''))); ?>
<?php } ?>