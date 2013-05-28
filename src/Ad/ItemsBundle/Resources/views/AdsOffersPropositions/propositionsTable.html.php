                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('offerPropositions', array('column' => 'offre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('offre', $column);?> <?php echo $class['offre'];?> sort">Offre</a></th> 
                      <th><a href="<?php echo $view['router']->generate('offerPropositions', array('column' => 'annonce', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('annonce', $column);?> <?php echo $class['annonce'];?> sort">Annonce</a></th> 
                      <th class="rightTopRadius rightBorder">Action</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($propositions as $p => $proposition) { ?>
                    <tr id="proposition<?php echo $p;?>">
                      <td class="leftBorder"><?php echo $proposition['offerName'];?></td> 
                      <td><?php echo $proposition['adName'];?></td> 
                      <td><a href="<?php echo $view['router']->generate('offerPropositionsAction', array('offer' => $proposition['id_of'], 'ad' => $proposition['id_ad'], 'action' => 'accepter'));?>?ticket=<?php echo $ticket;?>" rel="#proposition<?php echo $p;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Acceptation d\'une proposition'); return false;">accepter</a> 
                        <a href="<?php echo $view['router']->generate('offerPropositionsAction', array('offer' => $proposition['id_of'], 'ad' => $proposition['id_ad'], 'action' => 'refuser'));?>?ticket=<?php echo $ticket;?>" rel="#proposition<?php echo $p;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Acceptation d\'une proposition'); return false;">refuser</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offerPropositions',
'routeParams' => array('how' => $how, 'column' => $column))); ?>