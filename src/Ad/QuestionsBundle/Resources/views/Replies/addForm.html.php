                <form id="responseForm" action="<?php if($backoffice) { echo $view['router']->generate('repliesEdit', array('id' => $id)); } elseif($edit == 0) { echo $view['router']->generate('repliesReply', array('id' => $id)); } else { echo $view['router']->generate('repliesEdit', array('id' => $id)); } ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['replyText'], "Contenu") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine">
                      <div id="formreplyText" class="formBox oneItem noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'replyText', 'boxError');?>">
                        <?php echo $view['form']->widget($form['replyText'], array('attr' => array('class' => 'bigger'))); ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-replyText" class="errors">%s</p>', $formErrors, 'replyText');?>
                      </div> 
                    </div>
<?php if($edit == 0 && $backoffice == false) { ?>
                    <div class="formLine formChbox">
                      <div class="formBox oneLabel">
                        <label>Type de réponse</label>
                      </div>
                      <?php echo $view['form']->widget($form['replyType'], array('attr' => array('theme' => array(0 => ':')))) ?>
                    </div>
<?php } ?>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                      <input type="submit" name="send" value="Sauvegarder" class="button" />
                    </div>
                  </fieldset>
                </form>