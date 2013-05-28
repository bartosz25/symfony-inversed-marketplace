                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('offersMyList', array('column' => 'nom', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('nom', $column);?> <?php echo $class['nom'];?>  sort">Nom</a></th> 
                      <th><a href="<?php echo $view['router']->generate('offersMyList', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?>  sort">Date d'ajout</a></th> 
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($offers as $o => $offer) { ?>
                    <tr id="offer<?php echo $o;?>">
                      <td class="leftBorder"><?php echo $offer['offerName'];?></td> 
                      <td><?php echo $offer['dateOffer'];?></td> 
                      <td>
                      <a href="<?php echo $view['router']->generate('offersEdit', array('id' => $offer['id_of']));?>">éditer</a>
                      <a href="<?php echo $view['router']->generate('offersDelete', array('id' => $offer['id_of']));?>?ticket=<?php echo $ticket;?>" rel="#offer<?php echo $o;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une offre'); return false;">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersMyList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>