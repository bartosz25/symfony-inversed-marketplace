<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Les meilleures ventes")); ?>
              <div class="textContent">
              <ol class="bestList">
<?php $last = count($offers)-1; foreach($offers as $o => $offer) { ?>
                <li <?php if($o == $last) {?>class="last"<?php } ?>><span class="name"><?php echo $offer["name_of"];?></span> <span class="gain"><span><?php echo $offer["price_of"];?>€</span> gagnés</span></li>
<?php } ?>
              </ol>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('bestOffers', array()), 'anchor' => "Les meilleures ventes")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>