<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Ecrire une opinion")); ?>
              <div class="textContent">
<?php if($isSuccess == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'opinion a été rajoutée."));?>
<?php } elseif(count($formErrors)) { ?>
    <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Une erreur s'est produite pendant la sauvegarde de l'opinion. ".$view['frontendForm']->checkInvalidTicket($formErrors, 'ticket'))); ?>
<?php } ?>
                <form id="Write" action="<?php echo $view['router']->generate('opinionWrite', array('id' => $id));?>"  method="post"> 
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['opinionTitle'], "Titre") ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['opinionText'], "Opinion") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formopinionTitleContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'opinionTitle', 'boxError');?>">
                        <?php echo $view['form']->widget($form['opinionTitle'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-opinionTitle" class="errors">%s</p>', $formErrors, 'opinionTitle');?>
                      </div>
                      <div id="formopinionTextContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'opinionText', 'boxError');?>">
                        <?php echo $view['form']->widget($form['opinionText'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-opinionText" class="errors">%s</p>', $formErrors, 'opinionText');?>
                      </div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['opinionNote'], "Note") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formopinionNoteContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'opinionNote', 'boxError');?>">
                        <?php echo $view['form']->widget($form['opinionNote'], array("attr" => array("class" => "text"))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-opinionNote" class="errors">%s</p>', $formErrors, 'opinionNote');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Ajouter" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('ordersList'), 'anchor' => "Mes commandes"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('opinionWrite', array('id' => $id)), 'anchor' => "Ecrire une opinion")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/writeComment.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>