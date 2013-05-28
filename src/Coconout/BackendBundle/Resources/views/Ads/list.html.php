<?php $view->extend('::backend_base.html.php') ?>
<?php if($aorSuccess == 1) {?>
<p><b>Ad was successfully accepted.</b></p>
<?php } elseif($aorSuccess == 2) { ?>
<p><b>Ad was successfully denied.</b></p>
<?php } elseif($deleteSuccess == 1) { ?>
<p><b>Ad was successfully deleted.</b></p>
<?php } ?>
<h1>Ads list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Author name</th>
      <th>Category</th>
      <th>City</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($ads as $a => $ad) { ?>
    <tr>
    <td><?php echo $ad['id_ad'];?></td>
    <td><?php echo $ad['adName'];?></td>
    <td><?php echo $ad['login'];?></td>
    <td><?php echo $ad['categoryName'];?></td>
    <td><?php echo $ad['cityName'];?></td>
    <td>
      <?php if($ad['adState'] != $deletedState) { ?><a href="<?php echo $view['router']->generate('adsEdit', array('id' => $ad['id_ad']));?>">edit</a> <?php if($ad['adState'] != $notAcceptedState) { ?>| <a href="<?php echo $view['router']->generate('adsDelete', array('id' => $ad['id_ad']));?>?ticket=<?php echo $ticket;?>">delete</a><?php } } ?>
      <?php if($ad['adState'] == $notAcceptedState) { ?> | <a href="<?php echo $view['router']->generate('adsAcceptOrDeny', array('actionName' => 'accept', 'id' => $ad['id_ad']));?>?ticket=<?php echo $ticket;?>">accept</a> | <a href="<?php echo $view['router']->generate('adsAcceptOrDeny', array('actionName' => 'deny', 'id' => $ad['id_ad']));?>?ticket=<?php echo $ticket;?>">deny</a><?php } ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'adsList',
'routeParams' => array())); ?>