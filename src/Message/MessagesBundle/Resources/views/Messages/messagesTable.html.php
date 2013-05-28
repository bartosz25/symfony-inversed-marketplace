                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('messagesList', array('column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?> sort">Question</a></th>
                      <th><a href="<?php echo $view['router']->generate('messagesList', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date</a></th>
                      <th><a href="<?php echo $view['router']->generate('messagesList', array('column' => 'type', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('type', $column);?> <?php echo $class['type'];?> sort">Type</a></th>
                      <th><a href="<?php echo $view['router']->generate('messagesList', array('column' => 'auteur', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('auteur', $column);?> <?php echo $class['auteur'];?> sort">Auteur</a></th>
                      <th><a href="<?php echo $view['router']->generate('messagesList', array('column' => 'etat', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('etat', $column);?> <?php echo $class['etat'];?> sort">Etat</a></th>
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($messages as $m => $message) { ?>
                    <tr id="message<?php echo $m;?>">
                      <td class="leftBorder"><?php if($message['messageState'] == 1) { ?><b><?php } ?><?php echo $message['contentTitle'];?><?php if($message['messageState'] == 1) { ?></b><?php } ?>
                        <p class="floatRight verticalSep more"><a href="#" rel="#fragment<?php echo $m;?>" class="smaller show">lire fragment</a></p>
                        <p id="fragment<?php echo $m;?>" class="hidden clear"><?php echo strip_tags($message['shortContent']);?></p>
                      </td>
                      <td><?php echo $message['messageDate'];?></td>
                      <td><?php echo $types[$message['contentType']];?></td>
                      <td><?php echo $message['login'];?></td>
                      <td><?php echo $aliases[$message['messageState']];?></td>
                      <td>
                        <a href="<?php echo $view['router']->generate('messageRead', array('id' => $message['id_me']));?>?ticket=<?php echo $ticket;?>">lire</a>
                        <a href="<?php echo $view['router']->generate('messageDelete', array('id' => $message['id_me']));?>?ticket=<?php echo $ticket;?>" rel="#message<?php echo $m;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'un message'); return false;">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'messagesList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>