<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes questions")); ?>
              <div class="textContent">
                <div id="dynamicContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des questions"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
<?php echo $view->render('AdQuestionsBundle:Questions:questionsTable.html.php', array('ticket' => $ticket, 'questions' => $questions, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how, 'questionStates' => $questionStates));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer cette adresse ?",
'errorText' => "Une erreur s'est produite pendant la suppression de l'adresse. Veuillez réessayer.", 'okText' => "L'adresse a été correctement supprimée")); ?>
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsQuestionList', array()), 'anchor' => "Mes questions")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/listAddresses.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>