<script type="text/javascript">
var hasErrors = <?php if(count($formErrors) > 0 || (count($formErrors) == 0 && $edit)) { echo 'true'; } else { echo 'false';} ?>;
var urlGetCategories = "<?php echo $view['router']->generate('ajaxGetCategories') ?>";
var urlGetCities = "<?php echo $view['router']->generate('ajaxGetCities') ?>";
var urlUpload = "<?php if($add) {  echo $view['router']->generate('offersImagesUpload', array('id' => $tmpId)).'?sid='.$randomId; } else { echo $view['router']->generate('offersImagesUploadNew', array('id' => $offerId)).'?sid='.$randomId; }?>";
var uploadExt = "<?php echo $configUploadify['ext'];?>";
var uploadDesc = "<?php echo $configUploadify['desc'];?>";
var uploadLimit = <?php echo $configUploadify['maxSize'];?>; 
</script>
<?php if($isSuccess == 1 && $add) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'offre a été correctement rajoutée."));?>
<?php } elseif($isSuccess == 1 && $edit) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'offre a été correctement modifiée."));?>
<?php } elseif(count($formErrors)) { ?>
    <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde de l'offre. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>
                <p class="addItem"><a href="<?php echo $view['router']->generate('ajaxImportPrestaWindow');?>" rel="#importPrestashop" title="Importer depuis Prestashop" class="importOffer">importer depuis Prestashop</a></p>
                <p class="addItem"><a href="<?php echo $view['router']->generate('ajaxImportEbayWindow');?>" rel="#importEbay" title="Importer depuis eBay" class="importOffer">importer depuis eBay</a></p>
                <div id="importPrestashop" class="hidden"></div>
                <div id="importEbay" class="hidden"></div>
                <div id="loaderContainer" class="hidden"><?php echo $view->render('::frontend_ajax_loader.html.php', array('id' => "loaderEbay", "hidden" => "", 'text' => "Chargement du formulaire"));?></div>
                <form id="AddOffer" enctype="multipart/form-data" action="<?php if($add) { echo $view['router']->generate('offersAdd'); } else { echo $view['router']->generate('offersEdit', array('id' => $offerId)); } ?>"  method="post"> 
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerName'], "Titre") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerCatalogue'], "Catalogue") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formofferNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerName', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerName'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerName" class="errors">%s</p>', $formErrors, 'offerName');?>
                      </div>
                      <div id="formofferCatalogueContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerCatalogue', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerCatalogue'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerCatalogue" class="errors">%s</p>', $formErrors, 'offerCatalogue');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerText'], "Description") ?>
                      </div>
                      <div id="formofferTextContainer" class="formBox oneItem <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerText', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerText']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerText" class="errors">%s</p>', $formErrors, 'offerText');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerCategory'], "Catégorie") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerCountry'], "Votre pays") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formofferCategoryContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerCategory', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerCategory'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerCategory" class="errors">%s</p>', $formErrors, 'offerCategory');?>
                        <p id="error_adAddOffer_offerCategory"class="errorsAjax hidden">La catégorie ne correspond pas à celle de l'annonce : <?php echo $adRow['category'];?></p>
                      </div>
                      <div id="formofferCountryContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerCountry', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerCountry']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerCountry" class="errors">%s</p>', $formErrors, 'offerCountry');?>
                        <p id="error_adAddOffer_offerCountry"class="errorsAjax hidden">Le pays ne correspond pas à celui de l'annonce :  <?php echo $adRow['country'];?></p>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerCity'], "Votre ville") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerObjetState'], "Etat d'objet") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formofferCityContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerCity', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerCity'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerCity" class="errors">%s</p>', $formErrors, 'offerCity');?>
                        <p id="error_adAddOffer_offerCity" class="errorsAjax hidden"><?php echo "La ville ne correspond pas à celle de l'annonce :  ".$adRow['city'];?></p>
                        <p id="error_adAddRegion"class="errorsAjax hidden"><?php echo  "La région ne correspond pas à celle de l'annonce  ".$adRow['region'];?></p>
                      </div>
                      <div id="formofferObjetStateContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerObjetState', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerObjetState']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerObjetState" class="errors">%s</p>', $formErrors, 'offerObjetState');?>
                        <p id="error_adAddOffer_offerObjetState"class="errorsAjax hidden">L'état ne correspond pas à celui de l'annonce : <?php echo $adRow['objectState'];?></p>
                     </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerPrice'], "Prix") ?>
                        <span id="error_adAddOffer_offerPrice" class="hidden">Le prix ne correspond pas à la tranche définie dans l'annonce <?php echo $adRow['priceFrom'];?> -  <?php echo $adRow['priceTo'];?></span>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['offerTax'], "Taxe") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formofferPriceContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerPrice', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerPrice'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerPrice" class="errors">%s</p>', $formErrors, 'offerPrice');?>
                      </div>
                      <div id="formofferTaxContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'offerTax', 'boxError');?>">
                        <?php echo $view['form']->widget($form['offerTax'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-offerTax" class="errors">%s</p>', $formErrors, 'offerTax');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['deliveryYN'], "Appliquer le prix de transport") ?>
                      </div>
<?php if($add) { ?>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['tag1'], "Tags") ?>
                      </div>
<?php } ?>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formdeliveryYNContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'deliveryYN', 'boxError');?>">
                        <?php echo $view['form']->widget($form['deliveryYN'], array('attr' => array('theme' => ':'))) ?>
                        <div id="deliveryZones" class="hidden">
<?php foreach($zones as $z => $zone) { ?>
<p class="verticalSep"><label for="zone<?php echo $zone['id'];?>" class="block"><?php echo $zone['name'];?></label> <input type="text" name="zone<?php echo $zone['id'];?>" id="zone<?php echo $zone['id'];?>" value="<?php if(isset($fees[$zone['id']])) { echo $fees[$zone['id']]; } ?>" class="text inputSmall" /> € TTC</p>
<?php } ?>
                        </div> 
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-deliveryYN" class="errors">%s</p>', $formErrors, 'deliveryYN');?>
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
<?php echo $view->render('CategoryCategoriesBundle:Categories:getAjaxList.html.php', array('form' => $form,  'bidId' => $randomId, 'fields' => $formFields, 'formErrors' => $formErrors)); ?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <span class="imitLabel">Ajouter des images</span>
                      </div>
<?php $classImg = 'hidden'; if(count($offerImages) > 0) { $classImg = ''; } ?>
				  <div id="addedImages" class="<?php echo $classImg;?>">
<?php echo $view->render('::frontend_ajax_loader.html.php', array("text" => "Suppression en cours", "id" => "deleteLoader"));?>
<?php echo $view->render('::frontend_ajax_loader.html.php', array("text" => "Transfert en cours", "id" => "uploadLoader"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array("text" => '', "id" => "errorAjaxImg"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array("text" => '', "id" => "errorDelImg"));?>
                        <p><b>Images ajoutées</b></p>
                        <ul id="containerImg" class="clear">
  <?php foreach($offerImages as $i => $image) { ?>
    <?php echo $view->render('CatalogueImagesBundle:Images:uploadTemporary.html.php', array("ticket" => $ticket, "route" => $route, "id" => $image['id_oi'], "dir" => $dir, "file" => $image['imageName']));?>
  <?php } ?>
                        </li>
                      </div>
 <input type="file" name="imageField" id="imageField" />
 <span class="smaller">Max 5 images de 1 mo chacune</span>

                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?><?php echo $view['form']->widget($form['offerAd']) ?><input id="tmpId" type="hidden" name="tmpId" value="<?php echo $tmpId; ?>" />
                      <input type="submit" name="send" value="<?php if($add) { ?>Ajouter<?php } else {?>Editer<?php } ?>" class="button" />
                    </div>
                  </fieldset>
                </form>