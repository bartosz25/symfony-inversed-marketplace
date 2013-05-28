<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Annonces ".$categoryRow['categoryName'])); ?>
              <div class="textContent">
               <p class="addItem"><?php if($isSubscribed == 0) { ?><a href="<?php echo $view['router']->generate('categorySubscribe', array('category' => $categoryRow['id_ca']));?>?ticket=<?php echo $ticket;?>" id="subscribe">s'abonner à cette catégorie</a><?php } elseif($isSubscribed == 1) { ?><a href="<?php echo $view['router']->generate('alertDelete', array('id' => $categoryRow['id_ca'], 'type' => 'categories'));?>?ticket=<?php echo $ticket;?>" id="subscribe">se désabonner de cette catégorie</a><?php } ?></p>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des annonces"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="resultSubscribe" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => ""));?></div>
                <div id="dynamicContent">
<?php echo $view->render('CategoryCategoriesBundle:Categories:adsTable.html.php', array('category' => $category, 'ads' => $ads, 'page' => $page, 'pager' => $pager, 'class' => $class,
'column' => $column, 'how' => $how));?>
                </div><!--dynamicContent-->
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('adsAll', array('page' => $page, 'column' => $column, 'how' => $how)), 'anchor' => "Les annonces"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsByCategory', array('page' => $page, 'column' => $column, 'how' => $how, 'category' => $category)), 'anchor' => "Annonces ".$categoryRow['categoryName'])); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/subscribeAd.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>