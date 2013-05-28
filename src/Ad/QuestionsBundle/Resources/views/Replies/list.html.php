<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes réponses")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des réponses"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('AdQuestionsBundle:Replies:repliesTable.html.php', array('ticket' => $ticket, 'replies' => $replies, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer cette réponse ?",
'errorText' => "Une erreur s'est produite pendant la suppression de la réponse. Veuillez réessayer.", 'okText' => "La réponse a été correctement supprimée")); ?>
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('repliesList', array()), 'anchor' => "Mes réponses")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>