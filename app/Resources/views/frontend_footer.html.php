            <div class="links">
              <ul>
<?php
// TODO : actuellement on a de petits numéros, après le lancement et les premières activités on va les augmenter
  $maxTags = count($tags)-1;
  $randTags = $view['frontend']->getUniqRandom($maxTags, 2);

  $maxOffers = count($offers)-1;
  $randOffers = $view['frontend']->getUniqRandom($maxOffers, 2);

  $maxAds = count($ads)-1;
  $randAds = $view['frontend']->getUniqRandom($maxAds, 2);
?>
                <li><a href="<?php echo $view['router']->generate('offersByTags', array('url' => $view['frontend']->makeUrl($tags[$randTags[0]]['name']), 'tag' => $tags[$randTags[0]]['id']));?>"><?php echo $tags[$randTags[0]]['name'];?></a></li>
                <li><a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offers[$randOffers[0]]['catalogueName']), 'offer' => $view['frontend']->makeUrl($offers[$randOffers[0]]['name']), 'catalogueId' => $offers[$randOffers[0]]['catalogueId'], 'offerId' => $offers[$randOffers[0]]['id']));?>"><?php echo $offers[$randOffers[0]]['name'];?></a></li>
                <li><a href="<?php echo $view['router']->generate('adsShowOne', array('category' => $ads[$randAds[0]]['category'], 'url' => $view['frontend']->makeUrl($ads[$randAds[0]]['name']), 'id' => $ads[$randAds[0]]['id']));?>"><?php echo $ads[$randAds[0]]['name'];?></a></li>
                <li><a href="<?php echo $view['router']->generate('offersByTags', array('url' => $view['frontend']->makeUrl($tags[$randTags[1]]['name']), 'tag' => $tags[$randTags[1]]['id']));?>"><?php echo $tags[$randTags[1]]['name'];?></a></li>
                <li><a href="<?php echo $view['router']->generate('adsShowOne', array('category' => $ads[$randAds[1]]['category'], 'url' => $view['frontend']->makeUrl($ads[$randAds[1]]['name']), 'id' => $ads[$randAds[1]]['id']));?>"><?php echo $ads[$randAds[1]]['name'];?></a></li>
                <li><a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offers[$randOffers[1]]['catalogueName']), 'offer' => $view['frontend']->makeUrl($offers[$randOffers[1]]['name']), 'catalogueId' => $offers[$randOffers[1]]['catalogueId'], 'offerId' => $offers[$randOffers[1]]['id']));?>"><?php echo $offers[$randOffers[1]]['name'];?></a></li>
              </ul>
            </div><!-- links-->
            <div class="navigation">
              <ul>
                <li><a href="<?php echo $view['router']->generate('adsAll');?>">Les annonces</a></li>
                <li><a href="<?php echo $view['router']->generate('offersAll');?>">Les offres</a></li>
                <li><a href="<?php echo $view['router']->generate('staticPages', array('page' => 'contact'));?>">Contact</a></li>
                <li><a href="<?php echo $view['router']->generate('staticPages', array('page' => 'relations_medias'));?>">Presse</a></li>
              </ul>
              <p>Copyright &copy; <a href="http://www.unemeilleureoffre.com">UneMeilleureOffre.com</a> 2012</p>
            </div><!-- navigation-->