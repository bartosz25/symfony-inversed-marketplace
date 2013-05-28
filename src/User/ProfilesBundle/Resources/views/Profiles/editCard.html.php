<?php $view->extend('::frontend_base.html.php') ?>


<?php echo $view->render('::frontend_content_header.html.php', array('title' => 'Changer ma carte de visite')); ?>
              <div class="textContent">
<?php if($isSuccess == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Les informations ont été correctement sauvegardées.")); ?>
<?php } elseif($isSuccess != null && $isSuccess == 0) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des informations. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>
                <form id="EditCard" action="<?php echo $view['router']->generate('accountCard') ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['userEbayLogin'], "Identifiant eBay") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['userPrestashopStore'], "URL de la boutique Prestashop") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formuserEbayLoginContainer" class="formBox noMarginLeft  <?php echo $view['frontendForm']->setErrorClass($formErrors, 'userEbayLogin', 'boxError');?>">
                        <?php echo $view['form']->widget($form['userEbayLogin'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-userEbayLogin" class="errors">%s</p>', $formErrors, 'userEbayLogin');?>
                      </div>
                      <div id="formuserPrestashopStoreContainer" class="formBox  <?php echo $view['frontendForm']->setErrorClass($formErrors, 'userPrestashopStore', 'boxError');?>">
                        <?php echo $view['form']->widget($form['userPrestashopStore'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-userPrestashopStore" class="errors">%s</p>', $formErrors, 'userPrestashopStore');?>
                      </div>
                    </div>
                   <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['userProfile'], "Description de mon activité") ?>
                      </div>
                      <div id="formuserProfileContainer" class="formBox oneItem  <?php echo $view['frontendForm']->setErrorClass($formErrors, 'userProfile', 'boxError');?>">
                        <?php echo $view['form']->widget($form['userProfile']) ?>
                        <p id="cardCounter" class="charsCounter">Il vous restent <span>200</span> caractères.</p>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-userProfile" class="errors">%s</p>', $formErrors, 'userProfile');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['userType'], "Je suis un") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine oneBox ">
                      <div class="formBox floatLeft">
                      <?php echo $view['form']->widget($form['userType'], array('attr' => array('theme' => ':', "class" => "boxesInline"))) ?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('myAccount', array()), 'anchor' => 'Mon compte'))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('accountCard', array()), 'anchor' => 'Modifier ma carte de visite')); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/editCard.js'));?>