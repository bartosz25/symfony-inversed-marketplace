<script type="text/javascript">
var hasErrors = <?php if(count($formErrors) > 0 || (count($formErrors) == 0 && $edit)) { echo 'true'; } else { echo 'false';} ?>;
var urlGetCategories = "<?php echo $view['router']->generate('ajaxGetCategories') ?>";
var urlGetCities = "<?php echo $view['router']->generate('ajaxGetCities') ?>";
</script>
<?php if($isSuccess == 1 && $add) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'annonce a été correctement rajoutée."));?>
<?php } elseif($isSuccess == 1 && $edit) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'annonce a été correctement modifiée."));?>
<?php } elseif(count($formErrors)) { ?>
    <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde de l'annoce. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>
                <form id="AddAd" action="<?php if($add) { echo $view['router']->generate('adsAdd'); } else { echo $view['router']->generate('adsEdit', array('id' => $adId)); } ?>"  method="post"> 
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adName'], "Titre") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adCategory'], "Catégorie") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adName', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adName'], array("attr" => array("class" => "text"))); ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adName" class="errors">%s</p>', $formErrors, 'adName');?>
                      </div>
                      <div id="formadCategoryContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adCategory', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adCategory'], array("attr" => array("class" => "text"))) ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adCategory" class="errors">%s</p>', $formErrors, 'adCategory');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adText'], "Description") ?>
                      </div>
                      <div id="formadTextContainer" class="formBox oneItem <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adText', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adText']) ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adText" class="errors">%s</p>', $formErrors, 'adText');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adCountry'], "Pays") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adCity'], "Ville") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadCountryContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adCountry', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adCountry'], array("attr" => array("class" => "text"))); ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adCountry" class="errors">%s</p>', $formErrors, 'adCountry');?>
                      </div>
                      <div id="formadCityContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adCity', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adCity']) ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adCity" class="errors">%s</p>', $formErrors, 'adCity');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adMinOpinion'], "Opinion du vendeur") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adObjetState'], "Etat d'objet") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadMinOpinionContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adMinOpinion', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adMinOpinion'], array("attr" => array("class" => "text"))); ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adMinOpinion" class="errors">%s</p>', $formErrors, 'adMinOpinion');?>
                      </div>
                      <div id="formadObjetStateContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adObjetState', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adObjetState']) ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adObjetState" class="errors">%s</p>', $formErrors, 'adObjetState');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adSellerType'], "Type du vendeur") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adSellerGeo'], "Localisation du vendeur") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadSellerTypeContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adSellerType', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adSellerType'], array("attr" => array("class" => "text"))); ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adSellerType" class="errors">%s</p>', $formErrors, 'adSellerType');?>
                      </div>
                      <div id="formadSellerGeoContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adSellerGeo', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adSellerGeo']) ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adSellerGeo" class="errors">%s</p>', $formErrors, 'adSellerGeo');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adPayments'], "Payments acceptés") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adTax'], "Taxe") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadPaymentsContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adPayments', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adPayments'], array("attr" => array("class" => "text", "theme" => ":", "required" => true, 'onclick' => 'javascript: hideErrorMessagesChildren("#AddAd_adPayments input", false, "adPayments");'))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adPayments" class="errors">%s</p>', $formErrors, 'adPayments');?>
                      </div>
                      <div id="formadTaxContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adTax', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adTax']) ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adTax" class="errors">%s</p>', $formErrors, 'adTax');?>
                      </div>
                    </div>
<?php if($add) { ?>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adValidity'], "Validité") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['adAtHomePage'], "Distinguer sur l'accueil") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadValidityContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adValidity', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adValidity'], array("attr" => array("class" => "text"))); ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adValidity" class="errors">%s</p>', $formErrors, 'adValidity');?>
                      </div>
                      <div id="formadAtHomePageContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adAtHomePage', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adAtHomePage'], array('attr' => array('theme' => ':', "class" => "boxesInline", "req" => false))) ?>
                        <p class="italic smaller clear">Choix soumis à la validation de l'administrateur</p>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adAtHomePage" class="errors">%s</p>', $formErrors, 'adAtHomePage');?>
                      </div>
                    </div>
<?php } ?>
                    <div class="formLine twoBoxes">
                     <div class="formBox">
                        <?php  echo $view['form']->label($form['adBuyTo'], "Prix maximal (€, HT)") ?>
                      </div>
<?php if($add) { ?>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['tag1'], "Tags") ?>
                      </div>
<?php } ?>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formadBuyToContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'adBuyTo', 'boxError');?>">
                        <?php echo $view['form']->widget($form['adBuyTo'], array("attr" => array("class" => "text"))); ?><span class="mandatory">*</span>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-adBuyTo" class="errors">%s</p>', $formErrors, 'adBuyTo');?>
                      </div>
<?php if($add) { ?>
                      <div class="formBox">
  <?php echo $view['form']->widget($form['tag1'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag2'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag3'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag4'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag5'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag6'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag7'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag8'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag9'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
  <?php echo $view['form']->widget($form['tag10'], array("attr" => array("class" => "text verticalSmallSep"))) ?>
                      </div>
<?php } ?>
                    </div>
                    <div id="restForm">
                      <div id="loaderForm" class="hidden">Form loader...</div>
                      <div class="fields">
<?php echo $view->render('CategoryCategoriesBundle:Categories:getAjaxList.html.php', array('form' => $form, 'formErrors' => $formErrors, 'bidId' => $adId, 'fields' => $formFields)); ?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="<?php if($add) { ?>Ajouter<?php } else {?>Editer<?php } ?>" class="button" />
                    </div>
                  </fieldset>
                </form>