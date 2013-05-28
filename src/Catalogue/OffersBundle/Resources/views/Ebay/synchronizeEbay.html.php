<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Synchronisation de mes catalogues avec mes enchères eBay")); ?>
              <div class="textContent">
<?php if(count($formErrors) > 0) { ?>
    <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>  
<?php } ?>
                <form id="syncEbay" action="<?php echo $view['router']->generate('synchronizeEbay') ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['userEbayLogin'], "Identifiant eBay") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formuserEbayLoginContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'userEbayLogin', 'boxError');?>">
                        <?php echo $view['form']->widget($form['userEbayLogin'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-userEbayLogin" class="errors">%s</p>', $formErrors, 'userEbayLogin');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Synchroniser" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('synchronizeEbay', array()), 'anchor' => "Synchronisation de mes catalogues avec mes enchères eBay")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/syncEbay.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array(''));?>