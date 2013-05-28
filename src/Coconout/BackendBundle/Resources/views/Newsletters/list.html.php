<?php $view->extend('::backend_base.html.php') ?>
<?php if($sendSuccess == 1) {?>
<p><b>Newsletter was successfully send.</b></p>
<?php } ?>
<h1>Newsletters list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>User login</th>
      <th>Categories</th>
      <th>Ads</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($subscribers as $s => $subscriber) { ?>
    <tr>
    <td><?php echo $subscriber['id_us'];?></td>
    <td><?php echo $subscriber['login'];?></td>
    <td><?php echo $subscriber['aboCats'];?></td>
    <td><?php echo $subscriber['aboAds'];?></td>
    <td>see more</td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'newslettersList',
'routeParams' => array())); ?>