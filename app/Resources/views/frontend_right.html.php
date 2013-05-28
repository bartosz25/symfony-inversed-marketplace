<?php foreach($blocks as $block) { ?>
  <?php echo $view->render('::'.$block.'.html.php', array('tags' => $tags));?>
<?php } ?>