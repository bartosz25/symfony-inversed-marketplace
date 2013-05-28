<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Répondre à une question")); ?>
              <div class="textContent">
  <?php if($messageSuccess == 1) { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "La réponse a été correctement modifiée"));?>
  <?php } elseif($messageError == 1) { ?>
      <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
  <?php } ?>
  <?php echo $view->render('AdQuestionsBundle:Replies:addForm.html.php', array('id' => $id,
  'formErrors' => $formErrors, 'edit' => $edit, 'backoffice' => false, 'form' => $form)); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(
array('url' => $view['router']->generate('adsQuestionList', array()), 'anchor' => "Mes questions"),
array('url' => $view['router']->generate('repliesReply', array('id' => $id)).'?ticket='.$ticket, 'anchor' => "Lire une question"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('repliesReply', array('id' => $id)).'?ticket='.$ticket, 'anchor' => "Répondre à une question")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/replyQuestion.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>