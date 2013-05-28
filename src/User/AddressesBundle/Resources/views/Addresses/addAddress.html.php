<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => $titleBox)); ?>
              <div class="textContent">
<?php if($success == 1) { ?>
  <?php if($add == true) { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'adresse a été correctement ajoutée"));?>
  <?php } else { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'adresse a été correctement modifiée"));?>
  <?php } ?>
<?php } elseif($success == 0) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>
                <form id="addAddress" action="<?php echo $formUrl;?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressFirstName'], "Prénom") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressLastName'], "Nom") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaaddressFirstNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressFirstName', 'boxError');?>">
                        <?php echo $view['form']->widget($form['addressFirstName'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressFirstName" class="errors">%s</p>', $formErrors, 'addressFirstName');?>
                      </div>
                      <div id="formaaddressLastNameContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressLastName', 'boxError');?>">
                        <?php echo $view['form']->widget($form['addressLastName'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressLastName" class="errors">%s</p>', $formErrors, 'addressLastName');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressStreet'], "Adresse") ?>
                      </div>
                      <div id="formaaddressStreetContainer" class="formBox oneItem <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressStreet', 'boxError');?>">
                        <?php echo $view['form']->widget($form['addressStreet']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressStreet" class="errors">%s</p>', $formErrors, 'addressStreet');?>
                        <p id="streetCounter" class="charsCounter">Il vous restent <span>200</span> caractères.</p>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressPostalCode'], "Code postal") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressCity'], "Ville") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaddressPostalCodeContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressPostalCode', 'boxError');?>">
                        <?php echo $view['form']->widget($form['addressPostalCode'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressPostalCode" class="errors">%s</p>', $formErrors, 'addressPostalCode');?>
                      </div>
                      <div id="formaaddressCityContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressCity', 'boxError');?>">
                        <?php echo $view['form']->widget($form['addressCity'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressCity" class="errors">%s</p>', $formErrors, 'addressCity');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressCountry'], "Pays") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine">
                      <div id="formaaddressCountryContainer"  class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressCountry', 'boxError');?>">
                        <?php echo $view['form']->widget($form['addressCountry']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressCountry" class="errors">%s</p>', $formErrors, 'addressCountry');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['addressInfos'], "Infomations supplémentaires") ?>
                      </div>
                      <div class="formBox oneItem">
                        <?php echo $view['form']->widget($form['addressInfos']) ?>
                        <p id="infosCounter" class="charsCounter">Il vous restent <span>300</span> caractères.</p>
                      </div>
                    </div>
					
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('addressesList', array()), 'anchor' => "Mes adresses"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $formUrl, 'anchor' => $titleBox)); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php if($add) { ?>
  <?php $view['slots']->set('js', array('functions.js', 'users/addAddress.js'));?>
<?php } else { ?>
  <?php $view['slots']->set('js', array('functions.js', 'users/editAddress.js'));?>
<?php } ?>
<?php $view['slots']->set('css', array());?>