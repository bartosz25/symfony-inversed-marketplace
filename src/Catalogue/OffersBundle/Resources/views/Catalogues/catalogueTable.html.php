                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $catalogueUrl, 'id' => $catalogueId, 'column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?> sort">Titre</a></th>
                      <th><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $catalogueUrl, 'id' => $catalogueId, 'column' => 'categorie', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('categorie', $column);?> <?php echo $class['categorie'];?> sort">Catégorie</a></th>
                      <th><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $catalogueUrl, 'id' => $catalogueId, 'column' => 'prix', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('prix', $column);?> <?php echo $class['prix'];?> sort">Prix</a></th>
                      <th class="rightTopRadius rightBorder"><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $catalogueUrl, 'id' => $catalogueId, 'column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date<br />d'ajout</a></th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($offers as $o => $offer) { ?>
                    <tr id="offer<?php echo $o;?>">
                      <td class="leftBorder"><a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $catalogueUrl, 'catalogueId' => $catalogueId, 'offer' => $view['frontend']->makeUrl($offer['offerName']), 'offerId' => $offer['id_of']));?>"><?php echo $offer["offerName"];?></a></td>
                      <td><?php echo $offer["categoryName"];?></td>
                      <td><?php echo $offer["offerPrice"];?>€</td>
                      <td><?php echo $offer["addedDate"];?></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'catalogueShow',
'routeParams' => array('url' => $catalogueUrl, 'id' => $catalogueId, 'how' => $how, 'column' => $column))); ?>