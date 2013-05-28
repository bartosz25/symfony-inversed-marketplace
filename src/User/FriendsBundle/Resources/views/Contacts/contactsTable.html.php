                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('contactsList', array('column' => 'login', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="onSort <?php echo $class;?> sort">Login</a></th> 
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($users as $u => $user) { ?>
  <?php if($connected == $user['user1Id']) { $login = $user['user2Login']; } 
  else { $login = $user['user1Login']; } ?>
                    <tr id="contact<?php echo $u;?>">
                      <td class="leftBorder"><?php echo $login;?></td> 
                      <td>
                        <a href="<?php echo $view['router']->generate('contactDelete', array('user1' => $user['user1Id'], 'user2' => $user['user2Id']));?>?ticket=<?php echo $ticket;?>" rel="#contact<?php echo $u;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'un contact'); return false;">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'contactsList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>