<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => $titleBox)); ?>
              <div class="textContent">
<?php if($messageSuccess == 1) { $supplement = ""; ?>
  <?php if(count($messageNotices) > 0) { $supplement = "Cependant :".implode('<br />', $messageNotices); } ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le message a été correctement envoyé.".$supplement));?>
<?php } elseif($messageError == 1) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant l'envoi du message. ".$view['frontendForm']->checkInvalidTicket($messageErrors, 'ticket'))); ?>
<?php } ?>
<?php if(!$isReply) { ?>
                <p class="addItem"><a href="#">écrire un message</a></p>
<?php } ?>
  <?php echo $view->render('MessageMessagesBundle:Messages:form.html.php', array("messageId" => $messageId, "form" => $form, "messageError" => $messageError, 
  "logins" => $logins, "ids" => $ids, "isReply" => $isReply, "maxRecipers" => $maxRecipers, "messageErrors" => $messageErrors));?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('messagesList', array()), 'anchor' => "Mes messages"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('messageSend', array('id' => $messageId)) , 'anchor' => $titleBox)); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/writeMessage.js'));?>
<?php $view['slots']->set('css', array('user.css'));?>