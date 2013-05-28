<?php $view->extend('::frontend_base.html.php') ?>
<script type="text/javascript">
var hasErrors = <?php if(count($formErrors) > 0 || (count($formErrors) == 0 && $edit)) { echo 'true'; } else { echo 'false';} ?>;
var urlGetCities = "<?php echo $view['router']->generate('ajaxGetCities') ?>?ticket=<?php echo $ticket;?>";
</script>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
              <div class="textContent">
<?php if((int)$prestaCancel == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Les données de synchronisation ont été correctement supprimées. Vous pouvez relancer la synchronisation."));?>
<?php } elseif(count($formErrors) > 0) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde des modifications. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>
<?php echo $view->render('CatalogueOffersBundle:Prestashop:prestashopSteps.html.php', array('steps' => array(1 => false, 2 => false, 3 => false))); ?>
                <form id="syncPresta" method="post" action="<?php echo $view['router']->generate('synchronizePrestashop');?>?ticket=<?php echo $ticket;?>">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox noMarginLeft">
                        <?php echo $view['form']->label($form['syncSite'], "Adresse de l'API") ?><br /><small>Adresse complète, commençant par http://</small>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['syncKey'], "Clé API") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formsyncSiteContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'syncSite', 'boxError');?>">
                        <?php echo $view['form']->widget($form['syncSite'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-syncSite" class="errors">%s</p>', $formErrors, 'syncSite');?>
                      </div>
                      <div id="formsyncKeyContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'syncKey', 'boxError');?>">
                        <?php echo $view['form']->widget($form['syncKey'] , array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-syncKey" class="errors">%s</p>', $formErrors, 'syncKey');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox noMarginLeft">
                        <?php echo $view['form']->label($form['syncDefaultState'], "Etat des objets") ?> 
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['syncTax'], "Taxe des objets") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formsyncDefaultStateContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'syncDefaultState', 'boxError');?>">
                        <?php echo $view['form']->widget($form['syncDefaultState'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-syncDefaultState" class="errors">%s</p>', $formErrors, 'syncDefaultState');?>
                      </div>
                      <div id="formsyncTaxContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'syncTax', 'boxError');?>">
                        <?php echo $view['form']->widget($form['syncTax'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-syncTax" class="errors">%s</p>', $formErrors, 'syncTax');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['syncCountry'], "Pays des objets") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['syncCity'], "Ville des objets") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formsyncCountryContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'syncCountry', 'boxError');?>">
                        <?php echo $view['form']->widget($form['syncCountry']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-syncCountry" class="errors">%s</p>', $formErrors, 'syncCountry');?>
                      </div>
                      <div id="formsyncCityContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'syncCity', 'boxError');?>">
                        <?php echo $view['form']->widget($form['syncCity']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-syncCity" class="errors">%s</p>', $formErrors, 'syncCity');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
<?php if((int)$prestaCancel != 1) { ?>
                    <p class="floatLeft"><a href="<?php echo $view['router']->generate('synchronizeDeletePrestashop');?>?ticket=<?php echo $ticket;?>" class="deleteItem floatLeft">supprimer les données</a></p>
<?php } ?>

                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('synchronizePrestashop', array()), 'anchor' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/syncPres.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array(''));?>