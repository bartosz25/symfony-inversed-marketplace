<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 2) { ?>
<p><b>Question was successfully deleted.</b></p>
<?php } ?>

<h1>Questions list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Question</th>
      <th>Ad</th>
      <th>Added</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($questions as $q => $question) { ?>
    <tr>
    <td><?php echo $question['id_aq'];?></td>
    <td><?php echo $question['questionTitle'];?></td>
    <td><?php echo $question['shortContent'];?></td>
    <td><?php echo $question['date'];?></td>
    <td><a href="<?php echo $view['router']->generate('questionsEdit', array('id' => $question['id_aq']));?>">edit</a> | <a href="<?php echo $view['router']->generate('questionsDelete', array('id' => $question['id_aq']));?>?ticket=<?php echo $ticket;?>">delete</a>
    </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'questionsList',
'routeParams' => array())); ?>