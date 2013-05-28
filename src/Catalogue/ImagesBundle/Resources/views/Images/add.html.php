<?php //print_r($formErrors); die(); ?>
<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Ajouter une image")); ?>
              <div class="textContent">
<?php if($isSuccess == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'image a été correctement rajoutée."));?>
<?php } ?>
                <form id="AddImage" enctype="multipart/form-data" action="<?php echo $view['router']->generate('offersImagesAdd', array('id' => 0));?>"  method="post"> 
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <label for="imageName">Image</label>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['imageOffer'], "Offre") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formimageNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'imageName', 'boxError');?>">
                        <input type="file" name="imageName" id="imageName" srequired="true" />
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-imageName" class="errors">%s</p>', $formErrors, 'imageName');?>
                      </div>
                      <div id="formimageOfferContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'imageOffer', 'boxError');?>">
                        <?php echo $view['form']->widget($form['imageOffer']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-imageOffer" class="errors">%s</p>', $formErrors, 'imageOffer');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Ajouter" class="button" />
                    </div>
                  </fieldset>
                </form>
<?php //echo $view->render('AdItemsBundle:Items:addForm.html.php', array('isSuccess' => $isSuccess, 'adId' => $adId, 'edit' => $edit, 'add' => $add, 'formErrors' => $formErrors, 'form' => $form, 'formFields' => $formFields)); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('offersImagesList'), 'anchor' => "Les images de mes offres"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offersImagesAdd', array('id' => 0)), 'anchor' => "Ajouter une image")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/addImage.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>