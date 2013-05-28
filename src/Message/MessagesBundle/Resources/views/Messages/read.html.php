<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Lire une question")); ?>
              <div class="textContent article">
  <?php if($messageSuccess == 1) { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "La réponse a été correctement envoyée"));?>
  <?php } ?>
                <?php if($canReply) { ?><p class="addItem"><a href="<?php echo $view['router']->generate('messageWrite', array('id' => $message['id_me']));?>">répondre</a></p><?php } ?>
                <p class="artTitle"><?php echo $message['contentTitle'];?> <span class="smaller">[<?php echo $types[$message['contentType']];?>]</span></p>
                <p class="topLine smallInfos">Ecrite le <span><?php echo $message['messageDate'];?></span> par <span><?php echo $message['login'];?></span></p>
                <p class="verticalSep"><?php echo $message['contentText'];?></p>
                <?php if($canReply) { ?><p class="addItem"><a href="<?php echo $view['router']->generate('messageWrite', array('id' => $message['id_me']));?>">répondre</a></p><?php } ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('messagesList', array()), 'anchor' => "Mes messages"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('messageRead', array('id' => $message['id_me'])), 'anchor' => "Lire un message")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>