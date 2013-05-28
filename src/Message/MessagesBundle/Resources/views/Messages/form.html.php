                <form id="writeMessage" action="<?php echo $view['router']->generate('messageSend', array('id' => $messageId)); ?>" method="post">
                  <fieldset class="defaultForm">
<?php if(!$isReply) { ?>
                    <div class="formLine twoBoxes">
                      <div class="formBox inlineElements">
                        <?php echo $view['form']->label($form['recipersList'], "Destinataires") ?><a href="<?php echo $view['router']->generate('contactsList', array());?>?insert=1" id="addressBook" rel="#addressBookContainer" class="addFromLink smaller">ajouter depuis la liste des contacts</a>
                      </div>
                      <ul id="otherRecipers" class="ulForm">
  <?php if($messageError == 1) { ?>
    <?php  unset($logins[count($logins)-1]);  foreach($logins as $l => $login) { ?>
                    <li id="rec<?php echo $ids[$l];?>"><?php echo $login;?>  <a href="#" rel="<?php echo $ids[$l];?>|<?php echo $login;?>" class="removeReceiver">supprimer</a></li>
    <?php } ?>
  <?php } ?> 
                      </ul><?php echo $view['form']->widget($form['recipersList']) ?>
                    </div>
<?php } ?>
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['contentTitle'], "Titre") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine">
                      <div id="formcontentTitleContainer" class="formBox oneItem noMarginTop noMarginLeft <?php echo $view['frontendForm']->setErrorClass($messageErrors, 'contentTitle', 'boxError');?>">
                        <?php echo $view['form']->widget($form['contentTitle'], array("attr" => array("class" => "text"))) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-contentTitle" class="errors">%s</p>', $messageErrors, 'contentTitle');?>
                      </div>
                    </div>
                    <div class="formLine">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['contentMessage'], "Contenu") ?>
                      </div>
                      <div id="formcontentMessageContainer" class="formBox oneItem <?php echo $view['frontendForm']->setErrorClass($messageErrors, 'contentMessage', 'boxError');?>">
                        <?php echo $view['form']->widget($form['contentMessage']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-contentMessage" class="errors">%s</p>', $messageErrors, 'contentMessage');?>
                      </div>
                    </div>
                    <div class="formLine btnLine"><?php echo $view['form']->widget($form['isProfile']) ?>
                      <?php echo $view['form']->widget($form['message']) ?>
                      <?php echo $view['form']->widget($form['recipersLogins']) ?>
                      <?php echo $view['form']->widget($form['ticket']);?>
                      <input type="hidden" name="form_receivers" id="form_receivers" value="<?php echo $maxRecipers;?>" />
                      <input type="submit" name="send" value="Envoyer" class="button" />
                    </div>
                  </fieldset>
                </form>
                <div id="addressBookContainer">
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des contacts"));?>
                  <div id="adressesList"></div>
                </div><!-- addressBookContainer -->