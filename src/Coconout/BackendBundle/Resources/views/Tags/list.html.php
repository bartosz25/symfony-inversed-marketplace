<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 2) { ?>
<p><b>Tag was successfully deleted.</b></p>
<?php } ?>

<h1>Tags list.</h1>
<table id="tagsList">
  <thead>
    <tr>
      <th>ID</th>
      <th>Tag</th>
      <th>Ads</th>
      <th>Offers</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($tags as $t => $tag) { ?>
    <tr>
    <td><?php echo $tag['id_ta'];?></td>
    <td><?php echo $tag['tagName'];?></td>
    <td><?php echo $tag['tagAds'];?></td>
    <td><?php echo $tag['tagOffers'];?></td>
    <td><a href="<?php echo $view['router']->generate('tagsEdit', array('id' => $tag['id_ta']));?>">edit</a> | <a href="<?php echo $view['router']->generate('tagsDelete', array('id' => $tag['id_ta']));?>?ticket=<?php echo $ticket;?>">delete</a>
    </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'tagsList',
'routeParams' => array())); ?>