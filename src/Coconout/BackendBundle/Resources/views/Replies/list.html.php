<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 2) { ?>
<p><b>Reply was successfully deleted.</b></p>
<?php } ?>

<h1>Replies list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Question</th>
      <th>Ad</th>
      <th>Short content</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($replies as $r => $reply) { ?>
    <tr>
    <td><?php echo $reply['id_ar'];?></td>
    <td><?php echo $reply['questionTitle'];?></td>
    <td><?php echo $reply['adName'];?></td>
    <td><?php echo $reply['shortContent'];?></td>
    <td><a href="<?php echo $view['router']->generate('repliesEdit', array('id' => $reply['id_ar']));?>">edit</a> | <a href="<?php echo $view['router']->generate('repliesDelete', array('id' => $reply['id_ar']));?>?ticket=<?php echo $ticket;?>">delete</a>
    </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'questionsList',
'routeParams' => array())); ?>