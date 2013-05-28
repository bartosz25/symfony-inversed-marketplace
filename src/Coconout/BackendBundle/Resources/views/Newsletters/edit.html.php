<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1 && $add) { ?>
<p><b>L'annonce a été correctement rajoutée. Elle apparaîtra dès son acceptation par 
le modérateur.</b></p>
<?php } elseif($isSuccess == 1 && $edit) { ?>
<p><b>L'annonce a été correctement editée.</b></p>
<?php } else { ?>
<?php echo $view->render('AdItemsBundle:Items:addForm.html.php', array('formErrors' => $formErrors,
'edit' => $edit, 'add' => $add, 'form' => $form, 'formFields' => $formFields, 'adId' => $adId)); ?>
<?php } ?>