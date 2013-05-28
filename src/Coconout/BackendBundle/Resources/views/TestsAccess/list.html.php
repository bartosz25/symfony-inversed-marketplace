<?php $view->extend('::backend_base.html.php') ?>

<?php if($result != '' && $result == 1) { ?>
Test result : ok.
<?php } elseif($result != '' && $result == 0) { ?>
Test result : error.
<?php } ?>

<h1>Access tests list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Last execution</th>
      <th>Last result</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($tests as $t => $test) { ?>
    <tr>
    <td><?php echo $test['id_ta'];?></td>
    <td><?php echo $test['testsLabel'];?></td>
    <td><?php echo $test['testsLastExecution'];?></td>
    <td><?php echo $ent->getResultLabel($test['testsLastResult']);?></td>
    <td><a href="<?php echo $view['router']->generate('accessTestsHistory', array('id' => $test['id_ta']));?>">see history</a> | <a href="<?php echo $view['router']->generate('accessTestsExecute', array('id' => $test['id_ta']));?>">execute</a></td>
    </tr>
  <?php } ?>
  </tbody>
</table>