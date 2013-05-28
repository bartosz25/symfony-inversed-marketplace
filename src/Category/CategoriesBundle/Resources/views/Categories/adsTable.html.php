                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('adsByCategory', array('column' => 'titre', 'category' => $category, 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?> sort">Titre</a></th>
                      <th><a href="<?php echo $view['router']->generate('adsByCategory', array('column' => 'ville', 'category' => $category, 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('ville', $column);?> <?php echo $class['ville'];?> sort">Ville</a></th>
                      <th><a href="<?php echo $view['router']->generate('adsByCategory', array('column' => 'fourchette-a', 'category' => $category, 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('fourchette-a', $column);?> <?php echo $class['fourchette-a'];?> sort">Prix max</a></th>
                      <th class="rightTopRadius rightBorder"><a href="<?php echo $view['router']->generate('adsByCategory', array('column' => 'date', 'category' => $category, 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date<br />début</a></th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($ads as $a => $ad) { ?>
                    <tr id="ad<?php echo $a;?>">
                      <td class="leftBorder"><a href="<?php echo $view['router']->generate('adsShowOne', array('url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad'], 'category' => $category));?>"><?php echo $ad["adName"];?></a></td>
                      <td><?php echo $ad["cityName"];?></td>
                      <!--<td><?php //echo $ad["adBuyFrom"];?>€</td>-->
                      <td><?php echo $ad["adBuyTo"];?>€</td>
                      <td><?php echo $ad["dateStart"];?></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'adsByCategory',
'routeParams' => array('category' => $category, 'how' => $how, 'column' => $column))); ?>