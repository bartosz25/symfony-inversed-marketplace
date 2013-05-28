                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('addressesList', array('column' => $column, 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="onSort <?php echo $class;?> sort">Intitulé</a></th> 
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($addresses as $a => $address) { ?>
                    <tr id="address<?php echo $a;?>">
                      <td class="leftBorder"><?php echo $address['addressFirstName'];?> <?php echo $address['addressLastName'];?>
                        <br /><?php echo $address['addressStreet'];?>, <?php echo $address['addressPostalCode'];?> <?php echo $address['addressCity'];?>
                      </td> 
                      <td>
                      <a href="<?php echo $view['router']->generate('addressEdit', array('id' => $address['id_ua']));?>">éditer</a>
                      <a href="<?php echo $view['router']->generate('addressDelete', array('id' => $address['id_ua']));?>?ticket=<?php echo $ticket;?>" rel="#address<?php echo $a;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une adresse'); return false;">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'addressesList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>