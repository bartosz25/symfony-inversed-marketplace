<?php $view->extend('::backend_base.html.php') ?>
 
<h1>Catalogues list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Owner</th>
      <th>Description</th>
      <th>Offers quantity</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($catalogues as $c => $catalogue) { ?>
    <tr>
    <td><?php echo $catalogue['id_cat'];?></td>
    <td><?php echo $catalogue['catalogueName'];?></td>
    <td><?php echo $catalogue['login'];?></td>
    <td><?php echo $catalogue['catalogueDesc'];?></td>
    <td><?php echo $catalogue['catalogueOffers'];?></td>
    <td><a href="<?php echo $view['router']->generate('cataloguesEdit', array('id' => $catalogue['id_cat']));?>">edit</a> | <a href="<?php echo $view['router']->generate('cataloguesDelete', array('id' => $catalogue['id_cat']));?>?ticket=<?php echo $ticket;?>">delete</a></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'cataloguesList',
'routeParams' => array())); ?>