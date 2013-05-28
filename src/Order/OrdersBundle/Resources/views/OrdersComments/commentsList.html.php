<?php if(count($commentsList) > 0) {?>
  <?php echo $view->render('::frontend_ajax_loader.html.php', array('id' => 'loadComments', 'text' => "Chargement des commentaires"));?>
  <?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
  <p class="artTitle">Commentaires</p>
  <table>
    <thead>
      <tr>
        <th class="leftTopRadius leftBorder">Date</th>
        <th class="rightTopRadius rightBorder">Contenu</th>
      </tr>
    </thead>
    <tbody>
  <?php foreach($commentsList as $c => $comment) { ?>
      <tr><td class="leftBorder"><?php echo $comment['addedDate'];?></td><td><?php echo $comment['commentText'];?></td></tr>
  <?php } ?>
    </tbody>
  </table>
  <?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'ordersCommentsList',
  'routeParams' => array('id' => $id))); ?>
<?php } ?>