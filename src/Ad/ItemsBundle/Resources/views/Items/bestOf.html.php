<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Les meilleurs achats")); ?>
              <div class="textContent">
              <ol class="bestList">
<?php $last = count($ads)-1; foreach($ads as $a => $ad) { ?>
                <li <?php if($a == $last) {?>class="last"<?php } ?>><span class="name"><?php echo $ad["name_ad"];?></span> <span class="gain"><span><?php echo $ad["gain"];?>€</span> d'économies</span></li>
<?php } ?>
              </ol>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('bestAds', array()), 'anchor' => "Les meilleurs achats")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>