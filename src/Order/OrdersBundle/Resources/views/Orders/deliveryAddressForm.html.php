                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressFirstName'], "Prénom") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressLastName'], "Nom") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaddressFirstNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressFirstName', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressFirstName'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressFirstName" class="errors">%s</p>', $formErrors, 'addressFirstName');?>
                      </div>
                      <div id="formaddressLastNameContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressLastName', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressLastName'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressLastName" class="errors">%s</p>', $formErrors, 'addressLastName');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressStreet'], "Adresse") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressPostalCode'], "Code postal") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaddressStreetContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressStreet', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressStreet'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressStreet" class="errors">%s</p>', $formErrors, 'addressStreet');?>
                      </div>
                      <div id="formaddressPostalCodeContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressPostalCode', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressPostalCode'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressPostalCode" class="errors">%s</p>', $formErrors, 'addressPostalCode');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressCity'], "Ville") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressCountry'], "Pays") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaddressCityContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressCity', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressCity'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressCity" class="errors">%s</p>', $formErrors, 'addressCity');?>
                      </div>
                      <div id="formaddressCountryContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressCountry', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressCountry'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressCountry" class="errors">%s</p>', $formErrors, 'addressCountry');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form2['addressInfos'], "Infomations supplémentaires") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['orderPreferedDelivery'], "Moyen de livraison préféré") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaddressInfosContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressInfos', 'boxError');?>">
                        <?php echo $view['form']->widget($form2['addressInfos'], array('attr' => array('class' => 'addressConstructor text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressInfos" class="errors">%s</p>', $formErrors, 'addressInfos');?>
                      </div>
                      <div id="formorderPreferedDeliveryContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderPreferedDelivery', 'boxError');?>">
                        <?php echo $view['form']->widget($form['orderPreferedDelivery'], array('attr' => array('theme' => ':', "class" => "boxesInline"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderPreferedDelivery" class="errors">%s</p>', $formErrors, 'orderPreferedDelivery');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['orderPayment'], "Moyen de paiement préféré") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formorderPaymentContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderPayment', 'boxError');?>">
                        <?php echo $view['form']->widget($form['orderPayment']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderPayment" class="errors">%s</p>', $formErrors, 'orderPayment');?>
                      </div>
<?php echo $view['form']->widget($form2['addressId']) ?> 
<?php echo $view['form']->widget($form2['addressHash']) ?> 
<?php echo $view['form']->widget($form2['addressHashOld']) ?> 
<?php echo $view['form']->widget($form2['addressOldId']) ?>
                    </div>
                  </fieldset>