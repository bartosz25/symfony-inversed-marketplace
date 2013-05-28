<?php $paramsTh = $routeParams; unset($paramsTh["how"]); ?>
                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate($routeName, array_merge($paramsTh, array('column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how))));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?> sort">Titre</a></th>
                      <th><a href="<?php echo $view['router']->generate($routeName, array_merge($paramsTh, array('column' => 'categorie', 'how' => $view['frontend']->getViewOrderRand($how))));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('categorie', $column);?> <?php echo $class['categorie'];?> sort">Catégorie</a></th>
                      <th><a href="<?php echo $view['router']->generate($routeName, array_merge($paramsTh, array('column' => 'ville', 'how' => $view['frontend']->getViewOrderRand($how))));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('ville', $column);?> <?php echo $class['ville'];?> sort">Ville</a></th>
                      <th class="rightTopRadius rightBorder"><a href="<?php echo $view['router']->generate($routeName, array_merge($paramsTh, array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how))));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date<br />début</a></th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($ads as $a => $ad) { ?>
                    <tr id="ad<?php echo $a;?>">
                      <td class="leftBorder"><a href="<?php echo $view['router']->generate('adsShowOne', array('url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad'], 'category' => $view['frontend']->makeUrl($ad['categoryUrl'])));?>"><?php echo $ad["adName"];?></a></td>
                      <td><?php echo $ad["categoryName"];?></td>
                      <td><?php echo $ad["cityName"];?></td>
                      <td><?php echo $ad["dateStart"];?></td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => $routeName,
'routeParams' => $routeParams)); ?>