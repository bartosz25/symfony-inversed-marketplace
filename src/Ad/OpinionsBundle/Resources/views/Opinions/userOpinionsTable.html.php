                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('opinionsList', array('type' => $type, 'column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?>  sort">Titre</a></th> 
                      <th><a href="<?php echo $view['router']->generate('opinionsList', array('type' => $type, 'column' => 'commande', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('commande', $column);?> <?php echo $class['commande'];?>  sort">Commande</a></th> 
                      <th><a href="<?php echo $view['router']->generate('opinionsList', array('type' => $type, 'column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?><?php echo $class['date'];?>  sort">Date d'ajout</a></th> 
                      <th class="rightTopRadius rightBorder"><a href="<?php echo $view['router']->generate('opinionsList', array('type' => $type, 'column' => 'note', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('note', $column);?> <?php echo $class['note'];?>  sort">Note</a></th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($opinions as $o => $opinion) { ?>
                    <tr id="opinion<?php echo $o;?>">
                      <td class="leftBorder"><?php echo $opinion['opinionTitle'];?></td> 
                      <td><?php echo $opinion['adName'];?></td> 
                      <td><?php echo $opinion['addedDate'];?></td> 
                      <td><?php echo $opinion['opinionNote'];?></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'opinionsList',
'routeParams' => array('type' => $type, 'how' => $how, 'column' => $column))); ?>