                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
<?php if($showDeliveryForm) { ?>
                        <?php echo $view['form']->label($form['orderCarrier'], "Transporteur") ?>
<?php } else { ?>
                        <span class="imitLabel">Transporteur</span>
<?php } ?>
                      </div>
                      <div class="formBox">
<?php if($showDeliveryForm) { ?>
                        <?php echo $view['form']->label($form['orderDelivery'], "Prix de transport € TTC") ?>
<?php } else { ?>
                        <span class="imitLabel">Prix de transport € TTC</span>
<?php } ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formorderCarrierContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderCarrier', 'boxError');?>">
<?php if($showDeliveryForm) { ?>
                        <?php echo $view['form']->widget($form['orderCarrier'], array('attr' => array('class' => 'text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderCarrier" class="errors">%s</p>', $formErrors, 'orderCarrier');?>
<?php } elseif(isset($ad['orderCarrier']) && (int)$ad['orderCarrier'] > 0) { ?>
                        <?php echo $view['orders']->getCarrier((int)$ad['orderCarrier']);?>
<?php } ?>
                      </div>
                      <div id="formorderDeliveryContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderDelivery', 'boxError');?>">
<?php if($showDeliveryForm) { ?>
                        <?php echo $view['form']->widget($form['orderDelivery'], array('attr' => array('class' => 'text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderDelivery" class="errors">%s</p>', $formErrors, 'orderDelivery');?>
<?php } elseif(isset($ad['orderDelivery'])) { ?>
                        <?php echo $ad['orderDelivery'];?> 
<?php } ?>
                      </div>
                    </div>
<?php if($showCarrierRefForm) { ?>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['orderPackRef'], "Référence du colis") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formorderCarrierContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderPackRef', 'boxError');?>">
                        <?php echo $view['form']->widget($form['orderPackRef'], array('attr' => array('class' => 'text'))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderPackRef" class="errors">%s</p>', $formErrors, 'orderPackRef');?>                        
                      </div>
                    </div>
<?php } elseif(isset($ad['orderPackRef'])) { ?>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <span class="imitLabel">Référence du colis</span>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formorderCarrierContainer" class="formBox noMarginLeft">
                        <?php echo $ad['orderPackRef'];?>
                      </div>
                    </div>
<?php } ?>
                  </fieldset>