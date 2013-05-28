<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mes commentaires")); ?>
              <div class="textContent">
                <fieldset class="formBeforeTable"><form method="post" action="#">
                  <p><label for="type" class="imitLabel">Afficher les commantaires que j'ai </label>
                  <select name="type" id="type" class="selectStyle">
                    <option value="<?php echo $view['router']->generate('opinionsList', array('type' => 'ecrits', 'how' => $how, 'column' => $column));?>" <?php if($type == "ecrits") { ?>selected="selected"<?php } ?>>écrits</option>
                    <option value="<?php echo $view['router']->generate('opinionsList', array('type' => 'recus', 'how' => $how, 'column' => $column));?>" <?php if($type == "recus") { ?>selected="selected"<?php } ?>>reçus</option>
                  </select></p>
                </form></fieldset>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement de commentaires"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="dynamicContent">
<?php echo $view->render('AdOpinionsBundle:Opinions:userOpinionsTable.html.php', array('ticket' => $ticket, 'type' => $type, 'opinions' => $opinions, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('opinionsList', array('type' => $type)), 'anchor' => "Mes commentaires")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/listComments.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>