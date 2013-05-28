                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder">Image</th> 
                      <th><a href="<?php echo $view['router']->generate('offersImagesList', array('column' => 'offre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('offre', $column);?> <?php echo $class['offre'];?>  sort">Offre</a></th> 
                      <th><a href="<?php echo $view['router']->generate('offersImagesList', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?>  sort">Date d'ajout</a></th> 
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($images as $i => $image) { ?>
                    <tr id="image<?php echo $i;?>">
                      <td class="leftBorder"><img src="/images/offers/<?php echo $image['id_of'];?>/small_<?php echo $image['imageName'];?>" /></td> 
                      <td><?php echo $image['offerName'];?></td> 
                      <td><?php echo $image['dateAdd'];?></td> 
                      <td><a href="<?php echo $view['router']->generate('offersImagesDelete', array('id' => $image['id_oi']));?>?ticket=<?php echo $ticket;?>" rel="#image<?php echo $i;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une image'); return false;">supprimer</a></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersImagesList',
'routeParams' => array('how' => $how, 'column' => $column, 'id' => $id))); ?>