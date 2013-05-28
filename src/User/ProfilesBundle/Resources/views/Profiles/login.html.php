<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => 'Connexion')); ?>
              <div class="textContent">
<?php if(in_array('notCorrect', $formErrors)) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Les données d'identification ne sont pas correctes.")); ?>
<?php } ?>
<?php if($message != '') { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => $message)); ?>
<?php } ?>
<?php if($modifiedPassword == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le mot de passe a été changé. Reconnectez-vous en utilisant le nouveau mot de passe.")); ?>
<?php } ?>  

                <form action="<?php echo $view['router']->generate('loginDo', array()); ?>" method="post">
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <label for="username">Login</label>
                      </div>
                      <div class="formBox">
                        <label for="password">Mot de passe</label>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft">
                        <input type="text" name="username" id="username" class="text" />
                      </div>
                      <div class="formBox">
                        <input type="password" name="password" id="password" class="text" />
                      </div>
                    </div>
                    <div class="formLine btnLine">
                      <input type="submit" name="send" value="Se connecter" class="button" />
                    </div>
                  </fieldset>
                </form>
              </div><!-- textContent-->

<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('login', array()), 'anchor' => 'Connexion')); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array());?>