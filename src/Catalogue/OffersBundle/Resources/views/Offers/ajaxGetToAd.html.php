<?php if($count == 0) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Vous n'avez pas d'offres correspondant aux critères de l'acheteur.", "id" => "noOffers")); ?>  
<?php } else { ?>
                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder">Titre</th>
                      <th class="rightTopRadius rightBorder">Action</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($offers as $o => $offer) { ?>
                    <tr id="offer<?php echo $o;?>">
                      <td class="leftBorder"><?php echo $offer["offerName"];?></td>
                      <td><a href="<?php echo $view['router']->generate('offerProposeSend', array('id' => $offer['id_of'], 'ad' => $ad)); ?>?ticket=<?php echo $ticket;?>">proposer</a></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
                <div id="pagerOffers">
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'ajaxGetOffersToAd',
'routeParams' => array("ad" => $ad))); ?>
                </div>
<?php } ?>