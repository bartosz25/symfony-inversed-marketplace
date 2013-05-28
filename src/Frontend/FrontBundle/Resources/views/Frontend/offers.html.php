          <div id="offersList<?php echo $page;?>">
            <div class="lastAdded">
              <div class="header">
                <p>Dernières offres</p>
                <ul>
                  <li class="left"><a href="<?php echo $view['router']->generate('ajaxGetIndexOffers', array('page' => $previous));?>" id="prevOffersLink<?php echo $previous;?>" rel="<?php echo $previous;?>" class="<?php echo $classes["navigation"]["previousA"];?> lastOffersLoad">6 précédentes</a><span id="prevOffersSpan<?php echo $previous;?>" class="<?php echo $classes["navigation"]["previousSpan"];?>">6 suivantes</span></li>
                  <li><a href="<?php echo $view['router']->generate('ajaxGetIndexOffers', array('page' => $next));?>" id="nextOffersLink<?php echo $next;?>" rel="<?php echo $next;?>" class="<?php echo $classes["navigation"]["nextA"];?> lastOffersLoad">6 suivantes</a><span id="nextOffersSpan<?php echo $next;?>" class="<?php echo $classes["navigation"]["nextSpan"];?>">6 suivantes</span></li>
                </ul>
              </div>
              <div class="offers">
                <div id="offersLoader<?php echo $page;?>" class="hidden desc loader">
                  <p>Chargement des offres...</p>
                </div><!-- offersLoader-->
                <div class="list">
<?php foreach($offers as $o => $offer) { ?>
                  <div class="offer <?php echo $classes["offers"][$o];?>" style="background: url(<?php echo $dir;?>/<?php echo $offer["id_of"];?>/medium_1.jpg) no-repeat top center;">
                    <a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer["id_cat"], 'offer' => $offer["id_cat"], 'offerId' => $offer["id_of"], 'offer' => $view['frontend']->makeUrl($offer['offerName'])));?>" class="name"><?php echo $offer["offerName"];?></a>
                    <a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $view['frontend']->makeUrl($offer['catalogueName']), 'id' => $offer['id_cat']));?>" class="catalogue"><?php echo $offer["catalogueName"];?></a>
                    <span class="price"><?php echo $offer["offerPrice"];?>€</span>
                  </div>
<?php } ?>
                </div>
              </div>
            </div><!-- lastAdded-->
            <ul class="pager offersPager">
              <li class="btn prev"><a href="<?php echo $view['router']->generate('ajaxGetIndexOffers', array('page' => $previous));?>" id="prevOffersLink<?php echo $previous;?>" rel="<?php echo $previous;?>" class="lastOffersLoad <?php echo $classes["navigation"]["previousA"];?>">6 précédentes</a><span id="prevOffersSpan<?php echo $previous;?>" class="<?php echo $classes["navigation"]["previousSpan"];?>">6 précédentes</span></li>
              <li class="btn next"><a href="<?php echo $view['router']->generate('ajaxGetIndexOffers', array('page' => $next));?>" id="nextOffersLink<?php echo $next;?>" rel="<?php echo $next;?>" class="lastOffersLoad <?php echo $classes["navigation"]["nextA"];?>">6 suivantes</a><span id="nextOffersSpan<?php echo $next;?>" class="<?php echo $classes["navigation"]["nextSpan"];?>">6 suivantes</span></li>
            </ul><!-- pager-->
          </div><!-- offersList<?php echo $page;?>-->