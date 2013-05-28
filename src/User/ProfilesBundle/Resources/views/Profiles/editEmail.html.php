<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => 'Changer mon adresse e-mail')); ?>
              <div class="textContent">
<?php  if($isSuccess == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'adresse e-mail a été correctement modifiée.")); ?>
<?php } elseif($isSuccess == 0) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>

                <form id="editMail" action="<?php echo $view['router']->generate('accountEmail') ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['email'], "Nouvelle adresse e-mail") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formemailContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'email', 'boxError');?>">
                        <?php echo $view['form']->widget($form['email'], array('attr' => array('class' => 'text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-email" class="errors">%s</p>', $formErrors, 'email');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('myAccount', array()), 'anchor' => 'Mon compte'))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('accountEmail', array()), 'anchor' => 'Modifier mon adresse e-mail')); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/editUser.js'));?>