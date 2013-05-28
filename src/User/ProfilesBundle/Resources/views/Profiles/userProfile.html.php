<?php $view->extend('::frontend_base.html.php') ?>
<?php if($messageSuccess == 1) { ?>
  <?php if(count($messageNotices) > 0) { $supplement = "Cependant :".implode('<br />', $messageNotices); } ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le message a été correctement envoyé.".$supplement));?>
<?php } ?>
<div id="okInviteBox" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'invitation a été correctement envoyée à cet utilisateur."));?></div>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $user['login'])); ?>
              <div class="userContent adContent offerContent">
                <div class="left">
                  <div class="infos">
                    <p>Type : <?php echo $types[$user['userType']];?></p>
                    <p>Moyenne : <?php echo ceil($user['average']);?></p>
                    <p class="bolder">Statistiques </p>
                    <p>Annonces : <?php echo $user['userAds'];?></p>
                    <p>Offres : <?php echo $user['userOffers'];?></p>
                    <p>Catalogues : <?php echo $user['userCatalogues'];?></p>
                  </div><!-- infos-->
                  <div class="buttons">
<?php if($isConnected) { ?>
  <?php $viewUser = $view->container->get('security.context')->getToken(); ?>
  <?php $attr = $viewUser->getAttributes(); ?>
  <?php if($attr['id'] != $user['id_us']) { ?>

                    <a href="<?php echo $view['router']->generate('contactsInvite', array('user' => $user['id_us'])); ?>?ticket=<?php echo $ticket;?>" id="inviteBtn" class="button adBtn">inviter aux contacts</a>
                    <p id="loaderInvite" class="smallLoader loader hidden">Veuillez patienter</p>
  <?php } ?>
<?php } ?>
                    <a href="#pmBox" class="clear verticalSep button adBtn">écrire un message</a>
                    <div class="clear desc"><?php echo $user['userProfile'];?></div><!-- desc-->
                  </div><!-- buttons-->
                  <div class="box">
                  </div><!-- box-->
                </div><!-- left-->
                <div class="offers">
                  <p class="header">Catalogues</p>
<?php if($user['userCatalogues'] > 0) { ?>
                  <ul>
  <?php foreach($catalogues as $catalogue) { ?>
                    <li><a href="<?php echo $view['router']->generate('catalogueShow', array('id' => $catalogue['id_cat'], 'url' => $view['frontend']->makeUrl($catalogue['catalogueName']))); ?>"><?php echo $catalogue['catalogueName'];?></a></li>
  <?php } ?>
                  </ul>
<?php } else { ?>
                  <p class="notice"><?php echo $user["login"];?> n'a pas rajouté des catalogues</p>
<?php } ?>
                </div><!-- offers-->
              </div><!-- adContent -->
<?php echo $view->render('::frontend_secondary_content_header.html.php', array('class' => 'verticalSep', 'title' => 'Ecrire un message')); ?>
              <div id="pmBox" class="textContent">
<?php if($isConnected) { ?>
  <?php echo $view->render('MessageMessagesBundle:Messages:form.html.php', array("messageId" => 0, "form" => $form, "messageError" => 1, 
  "logins" => $logins, "ids" => $ids, "isReply" => false, "maxRecipers" => $maxRecipers, "messageErrors" => $messageErrors));?>
<?php } else { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Seulement les utilisateurs connectés peuvent écrire des messages."));?>
<?php } ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('userProfile', array("id" => $user["id_us"], "url" => $view["frontend"]->makeUrl($user["login"]))), 'anchor' => $user["login"])); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'jquery.scrollTo.js', 'users/userProfile.js', 'users/writeMessage.js'));?>
<?php $view['slots']->set('css', array('list.css', 'jquery.fancybox-1.3.4.css'));?>