<?php $view->extend('::backend_base.html.php') ?>

<?php if($deleteSuccess == 2) { ?>
L'image n'a pas pu être correctement supprimée.
<?php } elseif($deleteSuccess == 1) { ?>
L'image a été correctement supprimée.
<?php } ?>

<h1>Offers list.</h1>
<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Image</th>
      <th>Offer</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach($images as $i => $image) { ?>
    <tr>
      <td><?php echo $image['id_oi'];?></td>
      <td><img src="/images/offers/<?php echo $image['id_of'];?>/small_<?php echo $image['imageName'];?>" /></td>
      <td><?php echo $image['offerName'];?></td>
      <td><a href="<?php echo $view['router']->generate('offersImgEdit', array('id' => $image['id_oi']));?>">edit</a> <a href="<?php echo $view['router']->generate('offersImgDelete', array('id' => $image['id_oi']));?>?ticket=<?php echo $ticket;?>">delete</a> </td>
    </tr>
<?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersImgList',
'routeParams' => array())); ?>