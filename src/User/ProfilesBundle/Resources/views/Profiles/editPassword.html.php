<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => 'Changer mon mot de passe')); ?>
              <div class="textContent">
<?php  if($isSuccess == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le mot de passe a été correctement modifié.")); ?>
<?php } elseif($isSuccess == 0) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>

                <form id="editPassword" action="<?php echo $view['router']->generate('accountPassword') ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['password'], "Ancien mot de passe") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['pass1'], "Nouveau mot de passe") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formpasswordContainer" class="formBox noMarginLeft  <?php echo $view['frontendForm']->setErrorClass($formErrors, 'password', 'boxError');?>">
                        <?php echo $view['form']->widget($form['password'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-password" class="errors">%s</p>', $formErrors, 'password');?>
                      </div>
                      <div id="formpass1Container" class="formBox  <?php echo $view['frontendForm']->setErrorClass($formErrors, 'pass1', 'boxError');?>">
                        <?php echo $view['form']->widget($form['pass1'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-pass1" class="errors">%s</p>', $formErrors, 'pass1');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['pass2'], "Répétez le nouveau mot de passe") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formpass2Container" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'pass2', 'boxError');?>">
                        <?php echo $view['form']->widget($form['pass2'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-pass2" class="errors">%s</p>', $formErrors, 'pass2');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('myAccount', array()), 'anchor' => 'Mon compte'))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('accountEmail', array()), 'anchor' => 'Changer mon mot de passe')); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/editPassword.js'));?>