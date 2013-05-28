                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('ordersList', array('column' => 'numero', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="onSort <?php echo $class['numero'];?> sort">Numéro</a></th> 
                      <th><a href="<?php echo $view['router']->generate('ordersList', array('column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="onSort <?php echo $class['titre'];?> sort">Annonce</a></th> 
                      <th><a href="<?php echo $view['router']->generate('ordersList', array('column' => 'etat', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="onSort <?php echo $class['etat'];?> sort">Etat</a></th> 
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($orders as $o => $order) { ?>
                    <tr id="order<?php echo $o;?>">
                      <td class="leftBorder"><?php echo $order['id_ad'];?></td> 
                      <td><?php echo $order['adName'];?></td> 
                      <td><?php echo $states[$order['orderState']];?></td>
                      <td><a href="<?php echo $view['router']->generate('orderUpdateData', array('id' => $order['id_ad']));?>">mettre à jour</a> 
  <?php if($view['orders']->isTheLast($order['orderState'])) { ?><a href="<?php echo $view['router']->generate('opinionWrite', array('id' => $order['id_ad']));?>">donner une opinion</a><?php } ?>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'ordersList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>