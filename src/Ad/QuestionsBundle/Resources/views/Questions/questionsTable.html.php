                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr> 
                      <th class="leftTopRadius leftBorder"><a href="<?php echo $view['router']->generate('adsQuestionList', array('column' => 'titre', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('titre', $column);?> <?php echo $class['titre'];?> sort">Titre</a></th>
                      <th><a href="<?php echo $view['router']->generate('adsQuestionList', array('column' => 'date', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('date', $column);?> <?php echo $class['date'];?> sort">Date</a></th>
                      <th><a href="<?php echo $view['router']->generate('adsQuestionList', array('column' => 'etat', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('etat', $column);?> <?php echo $class['etat'];?> sort">Etat</a></th>
                      <th><a href="<?php echo $view['router']->generate('adsQuestionList', array('column' => 'auteur', 'how' => $view['frontend']->getViewOrderRand($how)));?>" rel="#dynamicContent" class="<?php echo $view['frontend']->getOnSort('auteur', $column);?> <?php echo $class['auteur'];?> sort">Auteur</a></th>
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($questions as $q => $question) { ?>
                    <tr id="question<?php echo $q;?>">
                      <td class="leftBorder"><?php if($question['questionState'] == 1) { ?><b><?php } ?><?php echo $question['questionTitle'];?><?php if($question['questionState'] == 1) { ?></b><?php } ?>
<p class="floatRight verticalSep more"><a href="#" rel="#fragment<?php echo $q;?>" class="smaller show">lire fragment</a></p>
<p id="fragment<?php echo $q;?>" class="hidden"><?php echo $question['shortContent'];?></p>
                      </td>
                      <td><?php echo $question['date'];?></td>
                      <td><?php echo $questionStates[$question['questionState']];?></td>
                      <td><?php echo $question['login'];?></td>
                      <td>
                        <a href="<?php echo $view['router']->generate('adsQuestionRead', array('id' => $question['id_aq']));?>?ticket=<?php echo $ticket;?>">lire</a>
                        <a href="<?php echo $view['router']->generate('adsQuestionDelete', array('id' => $question['id_aq']));?>?ticket=<?php echo $ticket;?>" rel="#question<?php echo $q;?>" onclick="javascript:openYesNoDialog('#deleteItem', this, 'Suppression d\'une question'); return false;">supprimer</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'adsQuestionList',
'routeParams' => array('how' => $how, 'column' => $column))); ?>