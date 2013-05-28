<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Lire une question")); ?>
              <div class="textContent article">
  <?php if($messageSuccess == 1) { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "La réponse a été correctement envoyée"));?>
  <?php } ?>
                <p class="addItem"><a href="<?php echo $view['router']->generate('repliesReply', array('id' => $question['id_aq']));?>?ticket=<?php echo $ticket;?>">répondre</a></p>
                <p class="artTitle"><?php echo $question['questionTitle'];?> <span class="smaller">[question <?php echo $states[$question['questionState']];?>]</span></p>
                <p class="topLine smallInfos">Ecrite le <span><?php echo $question['date'];?></span> par <span><?php echo $question['login'];?></span></p>
                <p class="verticalSep"><?php echo $question['questionText'];?></p>
                <p class="addItem"><a href="<?php echo $view['router']->generate('repliesReply', array('id' => $question['id_aq']));?>?ticket=<?php echo $ticket;?>">répondre</a></p>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('adsQuestionList', array()), 'anchor' => "Mes questions"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsQuestionRead', array('id' => $question['id_aq'])), 'anchor' => "Lire une question")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/listAddresses.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>