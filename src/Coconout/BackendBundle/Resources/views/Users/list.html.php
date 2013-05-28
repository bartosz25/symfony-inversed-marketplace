<?php $view->extend('::backend_base.html.php') ?>

<?php if($isSuccess == 2) { ?>
<p><b>User was successfully deleted.</b></p>
<?php } elseif($isSuccess == 3) { ?>
<p><b>Code was successfully sent.</b></p>
<?php } elseif($isSuccess == 4) { ?>
<p><b>User was successfully activated.</b></p>
<?php } ?>

<h1>Users list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Login</th>
      <th>E-mail</th>
      <th>Register date</th>
      <th>Last login</th>
      <th>Profile</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($users as $u => $user) { ?>
    <tr>
    <td><?php echo $user['id_us'];?></td>
    <td><?php echo $user['login'];?></td>
    <td><?php echo $user['email'];?></td>
    <td><?php echo $user['registeredDate'];?></td>
    <td><?php echo $user['lastLogin'];?></td>
    <td><?php echo $user['userProfile'];?></td>
    <td><?php if($user['userState'] != $deletedState) { ?><a href="<?php echo $view['router']->generate('usersEdit', array('id' => $user['id_us']));?>">edit</a> | <a href="<?php echo $view['router']->generate('usersDelete', array('id' => $user['id_us']));?>?ticket=<?php echo $ticket;?>">delete</a>
    <?php if($user['userState'] == $notActivatedState) { ?>| <a href="<?php echo $view['router']->generate('usersActivate', array('id' => $user['id_us']));?>?ticket=<?php echo $ticket;?>">activate</a> | <a href="<?php echo $view['router']->generate('usersSendCode', array('id' => $user['id_us']));?>?ticket=<?php echo $ticket;?>">send activation code</a><?php } } else { ?>Deleted user<?php } ?>
    </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'cataloguesList',
'routeParams' => array())); ?>