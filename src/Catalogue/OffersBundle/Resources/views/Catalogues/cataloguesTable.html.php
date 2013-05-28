                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('catalogueMyList', array('column' => 'nom', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('nom', $column);?> <?php echo $class['nom'];?>  sort">Nom</a></th> 
                      <th><a href="<?php echo $view['router']->generate('catalogueMyList', array('column' => 'nombre_offres', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('nombre_offres', $column);?> <?php echo $class['nombre_offres'];?>  sort">Nombre d'offres</a></th> 
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($catalogues as $c => $catalogue) { ?>
                    <tr id="catalogue<?php echo $c;?>">
                      <td class="leftBorder"><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $view['frontend']->makeUrl($catalogue['catalogueName']), 'id' => $catalogue['id_cat']));?>" target="_blank"><?php echo $catalogue['catalogueName'];?></a></td> 
                      <td><?php echo $catalogue['catalogueOffers'];?></td> 
                      <td>
                        <a href="<?php echo $view['router']->generate('catalogueEdit', array('id' => $catalogue['id_cat']));?>">éditer</a>
                        <a href="<?php echo $view['router']->generate('catalogueDelete', array('id' => $catalogue['id_cat']));?>?ticket=<?php echo $ticket;?>">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'catalogueMyList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>