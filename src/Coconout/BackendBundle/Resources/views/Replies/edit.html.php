<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 1) { ?>
<p><b>La réponse a été correctement editée.</b></p>
<?php } else { ?>

<?php print_r($formErrors); ?>
<?php echo $view->render('AdQuestionsBundle:Replies:addForm.html.php', array('id' => $id,
'edit' => false, 'backoffice' => true, 'form' => $form)); ?>
<?php } ?>