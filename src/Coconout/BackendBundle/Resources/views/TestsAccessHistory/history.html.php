<?php $view->extend('::backend_base.html.php') ?>

<h1>Access tests list <?php echo $testRow->getTestsLabel();?>.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Parameters</th>
      <th>Date of execution</th>
      <th>Final result</th>
      <th>Tested result</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($tests as $t => $test) { $params = unserialize($test['historyParams']); ?>
    <tr>
      <td><?php echo $test['id_tah'];?></td>
      <td><?php foreach($params as $p => $param) { ?>
        [<?php echo $p; ?>] => <?php echo $param;?> <br />
      <?php } ?></td>
      <td><?php echo $test['historyDate'];?></td>
      <td><?php echo $ent->getResultLabel($test['historyResult']);?></td>
      <td><?php echo $ent->getResultLabel($test['historyTestedResult']);?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>