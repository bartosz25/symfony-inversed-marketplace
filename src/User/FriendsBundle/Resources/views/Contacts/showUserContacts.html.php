<?php if($isInsert == 0) { ?>
  <?php $view->extend('::frontend_base.html.php') ?>

  <?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes contacts")); ?>
              <div class="textContent">
  <?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des contacts"));?>
  <?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
  <?php echo $view->render('UserFriendsBundle:Contacts:contactsTable.html.php', array('ticket' => $ticket, 'users' => $users, 'pager' => $pager, 'class' => $class,
  'connected' => $connected, 'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
  <?php echo $view->render('::frontend_delete_dialog.html.php', array('text' => "Etes-vous sûr de vouloir supprimer ce contact ?",
  'errorText' => "Une erreur s'est produite pendant la suppression du contact. Veuillez réessayer.", 'okText' => "Le contact a été correctement supprimé")); ?>
  <?php $view['slots']->set('breadcrumb', array()); ?>
  <?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('contactsList', array()), 'anchor' => "Mes contacts")); ?>
  <?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
  <?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
  <?php $view['slots']->set('js', array('functions.js'));?>
  <?php $view['slots']->set('css', array('list.css'));?>



<?php } else { ?>
<link rel="stylesheet" href="/css/list.css"  type="text/css" />
<div id="errorInsertContacts" class="hidden"><?php echo $view->render('::frontend_error_box.html.php', array('text' => ''));?></div>
<form method="post" id="formAllContacts" action="#">
  <div class="defaultForm">
    <div class="formLine formChbox">
      <div class="formBox oneLabel">Sélectionnez un ou plusieurs destinataires</div>
<?php foreach($users as $user) { ?>
<?php if($connected == $user['user1Id']) {
       $login = $user['user2Login'];
       $id = $user['user2Id'];
      }
      else {
      $login = $user['user1Login'];
      $id = $user['user1Id'];
      }
?>
  <div class="chbox"><span><input type="checkbox" id="login-<?php echo $id;?>" value="<?php echo $id;?>||<?php echo $login; ?>" /></span><label for="login-<?php echo $id;?>"><?php echo $login; ?></label></div>
<?php } ?>
<?php } ?>
    </div>
    <div class="formLine btnLine">
      <input type="submit" name="send" id="useContacts" onclick="javascript:insertAllContacts(); return false;" value="Utiliser ces contacts" class="button" />
    </div>
  </div>
</form>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'contactsList',
'routeParams' => array('insert' => '1'))); ?>