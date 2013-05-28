<?php $view->extend('::backend_base.html.php') ?>
<?php if($deleteSuccess == 1) { ?> 
The offer was successfully deleted.
<?php } ?>

<h1>Offers list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Author name</th>
      <th>Catalogue</th>
      <th>Category</th>
      <th>Source</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($offers as $o => $offer) { ?>
    <tr>
    <td><?php echo $offer['id_of'];?></td>
    <td><?php echo $offer['offerName'];?></td>
    <td><?php echo $offer['login'];?></td>
    <td><?php echo $offer['catalogueName'];?></td>
    <td><?php echo $offer['categoryName'];?></td>
    <td><?php echo $offer['offerExternalSystem'];?></td>
    <td><a href="<?php echo $view['router']->generate('offersEdit', array('id' => $offer['id_of']));?>">edit</a> | <a href="<?php echo $view['router']->generate('offersDelete', array('id' => $offer['id_of']));?>?ticket=<?php echo $ticket;?>">delete</a></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersList',
'routeParams' => array())); ?>