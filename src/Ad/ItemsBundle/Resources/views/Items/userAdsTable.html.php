                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('adsMyList', array('column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?> sort">Titre</a></th> 
                      <th><a href="<?php echo $view['router']->generate('adsMyList', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?><?php echo $class['date'];?> sort">Date d'ajout</a></th> 
                      <th>Etat</th>
                      <th class="rightTopRadius rightBorder">Action</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($ads as $a => $ad) { ?>
                    <tr id="ad<?php echo $a;?>">
                      <td class="leftBorder"><?php echo $ad['adName'];?></td> 
                      <td><?php echo $ad['adStart'];?></td> 
                      <td><?php echo $states[$ad['adState']];?></td> 
                      <td>
  <?php if($ad['adState'] == 1) { ?>
                        <a href="<?php echo $view['router']->generate('adsEdit', array('id' => $ad['id_ad']));?>">éditer</a> <a href="<?php echo $view['router']->generate('adsDelete', array('id' => $ad['id_ad']));?>?ticket=<?php echo $ticket;?>" rel="#ad<?php echo $a;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une annonce'); return false;">supprimer</a>
                        <a href="<?php echo $view['router']->generate('adsEnd', array('id' => $ad['id_ad']));?>?ticket=<?php echo $ticket;?>">finir l'annonce</a>
  <?php } elseif($ad['adState'] == 2) { ?>
                        <a href="<?php echo $view['router']->generate('orderUpdateData', array('id' => $ad['id_ad']));?>">voir la commande</a>
  <?php } ?>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'adsMyList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>