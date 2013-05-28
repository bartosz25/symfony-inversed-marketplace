<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes messages")); ?>
              <div class="textContent">
                <p class="addItem"><a href="<?php echo $view['router']->generate('messageWrite', array('id' => 0));?>">écrire un message</a></p>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des messages"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('MessageMessagesBundle:Messages:messagesTable.html.php', array('ticket' => $ticket, 'messages' => $messages, 'pager' => $pager, 'class' => $class,
'types' => $types, 'aliases' => $aliases, 'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer ce message ?",
'errorText' => "Une erreur s'est produite pendant la suppression du message. Veuillez réessayer.", 'okText' => "Le message a été correctement supprimé")); ?>
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('messagesList', array('page' => $page, 'column' => $column, 'how' => $how)), 'anchor' => "Mes messages")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>