                <form id="addCatalogue" action="<?php if($add) { echo $view['router']->generate('catalogueAdd'); } elseif($backoffice) { echo $view['router']->generate('cataloguesEdit', array('id' => $catalogueId)); } else { echo $view['router']->generate('catalogueEdit', array('id' => $catalogueId)); } ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['catalogueName'], "Nom") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formcatalogueNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'catalogueName', 'boxError');?>">
                        <?php echo $view['form']->widget($form['catalogueName'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-catalogueName" class="errors">%s</p>', $formErrors, 'catalogueName');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['catalogueDesc'], "Description") ?>
                      </div>
                      <div id="formcatalogueDescContainer" class="formBox oneItem <?php echo $view['frontendForm']->setErrorClass($formErrors, 'catalogueDesc', 'boxError');?>">
                        <?php echo $view['form']->widget($form['catalogueDesc']) ?>
                        <p id="descCounter" class="charsCounter">Il vous restent <span>200</span> caractères.</p>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-catalogueDesc" class="errors">%s</p>', $formErrors, 'catalogueDesc');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
                  </fieldset>
                </form>