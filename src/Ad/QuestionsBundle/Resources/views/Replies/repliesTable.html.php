                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('repliesList', array('column' => 'question_titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('question_titre', $column);?> <?php echo $class['question_titre'];?> sort">Question</a></th>
                      <th><a href="<?php echo $view['router']->generate('repliesList', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date</a></th>
                      <th><a href="<?php echo $view['router']->generate('repliesList', array('column' => 'annonce_nom', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('annonce_nom', $column);?> <?php echo $class['annonce_nom'];?> sort">Annonce</a></th>
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($replies as $r => $reply) { ?>
                    <tr id="reply<?php echo $r;?>">
                      <td class="leftBorder"><?php echo $reply['questionTitle'];?>
<p class="floatRight verticalSep more"><a href="#" rel="#fragment<?php echo $r;?>" class="smaller show">lire fragment</a></p>
<p id="fragment<?php echo $r;?>" class="hidden"><?php echo $reply['shortContent'];?></p>
                      </td>
                      <td><?php echo $reply['date'];?></td>
                      <td><?php echo $reply['adName'];?></td>
                      <td>
                        <a href="<?php echo $view['router']->generate('repliesEdit', array('id' => $reply['id_ar']));?>">éditer</a>
                        <a href="<?php echo $view['router']->generate('repliesDelete', array('id' => $reply['id_ar']));?>?ticket=<?php echo $ticket;?>" rel="#reply<?php echo $r;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une réponse'); return false;">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'repliesList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>