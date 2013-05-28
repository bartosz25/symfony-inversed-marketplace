                  <table style="width:510px;" class="items" cellspacing="0">
                    <thead>
                      <tr>
                        <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('alertsList', array('type' => 'categories', 'column' => 'nom', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('nom', $column);?> <?php echo $class['nom'];?> sort">Catégorie</a></th>
                        <th><a href="<?php echo $view['router']->generate('alertsList', array('type' => 'categories', 'column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date d'abonnement</a></th>
                        <th class="rightTopRadius rightBorder">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach($elements as $e => $element) { ?>
                      <tr id="category<?php echo $e;?>">
                        <td class="leftBorder"><?php echo $element['categoryName'];?></td>
                        <td><?php echo $element['aboDate'];?></td>
                        <td><a href="<?php echo $view['router']->generate('alertDelete', array('type' => 'categories', 'id' => $element['id_ca']));?>?ticket=<?php echo $ticket;?>"  rel="#category<?php echo $e;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Désabonnement d\'une catégorie'); return false;">désabonner</a></td>
                      </tr>
                    <?php } ?>
                    </tbody>
                  </table>