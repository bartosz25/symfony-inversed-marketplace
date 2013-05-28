<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Propositions d'achat")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement de offres"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('AdItemsBundle:AdsOffersPropositions:propositionsTable.html.php', array('ticket' => $ticket, 'propositions' => $propositions, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir effectuer cette opération ?",
'errorText' => "Une erreur s'est produite pendant l'exécution de cette opération. Veuillez réessayer.", 'okText' => "L'opération s'est correctement déroulée")); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offerPropositions', array()), 'anchor' => "Propositions d'achat")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/propositions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>