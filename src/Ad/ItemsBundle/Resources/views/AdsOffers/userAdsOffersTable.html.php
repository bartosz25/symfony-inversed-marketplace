                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('offersInAds', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?>  sort">Date</a></th> 
                      <th><a href="<?php echo $view['router']->generate('offersInAds', array('column' => 'offre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('offre', $column);?> <?php echo $class['offre'];?>  sort">Offre</a></th> 
                      <th><a href="<?php echo $view['router']->generate('offersInAds', array('column' => 'annonce', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('annonce', $column);?> <?php echo $class['annonce'];?>  sort">Annonce</a></th> 
                      <th class="rightTopRadius rightBorder">Action</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($offers as $o => $offer) { ?>
                    <tr id="offer<?php echo $o;?>">
                      <td class="leftBorder"><?php echo $offer['submitted'];?></td> 
                      <td><?php echo $offer['offerName'];?></td> 
                      <td><?php echo $offer['adName'];?></td> 
                      <td><a href="<?php echo $view['router']->generate('offerRemoveFromAd', array('offer' => $offer['id_of'], 'ad' => $offer['id_ad']));?>?ticket=<?php echo $ticket;?>" rel="#offer<?php echo $o;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une offre'); return false;">retirer cette offre</a></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offersInAds',
'routeParams' => array('how' => $how, 'column' => $column))); ?>