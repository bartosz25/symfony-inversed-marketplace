<?php $view->extend('::frontend_base.html.php') ?>
 
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes alertes")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <form method="post" action="#">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox"><label for="typeAb">Type d'abonnement</label></div>
                      <div class="formBox">
                        <select name="typeAb" id="typeAb">
                          <option value="<?php echo $view['router']->generate('alertsList', array('type' => 'annonces'));?>" <?php if($type == "annonces") { ?>selected="selected"<?php } ?>>annonces</option>
                          <option value="<?php echo $view['router']->generate('alertsList', array('type' => 'categories'));?>" <?php if($type == "categories") { ?>selected="selected"<?php } ?>>catégories</option>
                        </select>
                      </div>
                  </fieldset>
                </form>
                <div class="clear"></div>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement d'alertes"));?>
                <div id="dynamicContent" class="clear">
<?php if($type == "annonces") { ?>
  <?php echo $view->render('UserAlertsBundle:UsersAlerts:tableAds.html.php',
  array('elements' => $elements, 'ticket' => $ticket, 'how' => $how, 'column' => $column, 'class' => $class));?>
<?php } elseif($type == "categories") { ?>
  <?php echo $view->render('UserAlertsBundle:UsersAlerts:tableCategories.html.php',
  array('elements' => $elements, 'ticket' => $ticket, 'how' => $how, 'column' => $column, 'class' => $class));?>
<?php } ?>
                </div>
              </div><!-- textContent-->
<?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer cette alerte ?",
'errorText' => "Une erreur s'est produite pendant la suppression de l'alerte. Veuillez réessayer.", 'okText' => "L'alerte a été correctement supprimée")); ?>

<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('alertsList', array('type' => $type, 'column' => $column, 'how' => $how)), 'anchor' => "Mes alertes")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/listAlerts.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>