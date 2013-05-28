<?php $view->extend('::frontend_base.html.php') ?>
          <div id="mainAds" class="mainAds">
            <div class="title">
              <span class="submit">Les annonces sur la une</span>
              <ul>
                <li class="actual"><a href="#" rel="#desc1" class="loadAdStar"><span class="hidden">1</span></a></li>
                <li><a href="#" rel="#desc2" class="loadAdStar"><span class="hidden">2</span></a></li>
                <li><a href="#" rel="#desc3" class="loadAdStar"><span class="hidden">3</span></a></li>
                <li><a href="#" rel="#desc4" class="loadAdStar"><span class="hidden">4</span></a></li>
                <li><a href="#" rel="#desc5" class="loadAdStar"><span class="hidden">5</span></a></li>
                <li><a href="#" rel="#desc6" class="loadAdStar"><span class="hidden">6</span></a></li>
                <li><a href="#" rel="#desc7" class="loadAdStar"><span class="hidden">7</span></a></li>
                <li><a href="#" rel="#desc8" class="loadAdStar"><span class="hidden">8</span></a></li>
              </ul>
            </div>
            <div id="mainAdLoader" class="hidden desc loader">
              <p>Chargement de l'annonce...</p>
            </div><!-- mainAdLoader-->
<?php foreach($ads as $a => $ad) {?>
            <div id="desc<?php echo ($a+1);?>" class="desc <?php echo $classes["ads"][$a];?>">
              <div class="infos">
                <p class="user"><a href="#"><?php echo $ad["login"];?></a></p>
                <p class="city"><span><?php echo $ad["cityName"];?></span></p>
                <p class="category"><a href="<?php echo $view['router']->generate('adsByCategory', array("category" => $ad["categoryUrl"]));?>"><?php echo $ad["categoryName"];?></a></p>
                <p class="share">
                  <span>Je partage cette annonce</span>
                  <a href="#" class="button facebook"><span class="hidden">Facebook</span></a>
                  <a href="#" class="button twitter"><span class="hidden">Twitter</span></a>
                </p>
              </div>
              <div class="text">
                <p class="title"><a href="<?php echo $view['router']->generate('adsShowOne', array('url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad'], 'category' => $view['frontend']->makeUrl($ad['categoryUrl'])));?>"><?php echo $ad["adName"];?></a></p>
                <p class="info"><?php echo $ad["shortDesc"];?>...</p>
                <p class="bottom">                
                  <span class="twoLines">Prix max</span>
                  <span class="orange price"><?php echo $ad["adBuyTo"];?></span>
                  <span class="orange days"><?php echo (int)$ad["daysToEnd"];?></span>
                  <span class="twoLines">jour(s) <br />restant</span>
                </p>
              </div>
            </div>
<?php } ?>
          </div><!-- mainAds-->
          <div id="offersList">
<?php echo $view->render('FrontendFrontBundle:Frontend:offers.html.php', array("offers" => $offers, "classes" => $classes,
"page" => 1, "dir" => $dir, "previous" => 0, "next" => $next, "classes" => $classes));?>
          </div>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('js', array('index.js')); ?>
<?php $view['slots']->set('blocks', array('counter', 'tags', 'ads_right'));?>
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array()); ?>