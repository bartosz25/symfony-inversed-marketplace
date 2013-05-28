<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $offer['offerName'])); ?>
              <div class="adContent offerContent">
                <div class="left">
                  <div class="infos">
                    <p class="user"><a href="<?php echo $view['router']->generate('userProfile', array('id' => $offer["id_us"], 'url' => $view['frontend']->makeUrl($offer["login"])));?>"><?php echo $offer['login'];?></a></p>
                    <p class="city"><span><?php echo $offer['cityName'];?></span></p>
                    <p class="category last"><a href="<?php echo $view['router']->generate('offersByCategory', array('category' => $offer['categoryUrl']));?>"><?php echo $offer['categoryName'];?></a></p>
                  </div><!-- infos-->
                  <div class="buttons">
<?php if($canPropose) { ?>
                    <a href="<?php echo $view['router']->generate('offerProposeBuy', array('offer' => $offer['id_of']));?>?ticket=<?php echo $ticket;?>" class="button adBtn">J'achète cette offre</a>
<?php } ?>
                  </div><!-- buttons-->
                <div class="desc">
                  <p><?php echo $offer['offerText']; ?></p>
                </div><!-- desc-->
                <div class="box">
                  <p class="header">Les caractéristiques :</p>
                  <table>
                    <tbody>
                      <tr>
                        <td style="width:110px;">Prix :</td>
                        <td class="right"><span><?php echo $offer['offerPrice'];?></span>€</td>
                      </tr>
                      <tr>
                        <td>Etat de l'objet :</td>
                        <td class="right"><?php echo $adsStates[$offer['offerObjetState']];?></td>
                      </tr>
<?php foreach($fields as $f => $field) { ?>
                      <tr>
                        <td><?php echo $field['fullName']; ?> :</td>
                        <td class="right"><?php echo $field['fieldValue'];?></td>
                      </tr>
<?php } ?>
                    </tbody>
                  </table>
                </div><!-- box-->
                <div class="share">
                  <span>Je partage sur :</span> <a href="#" class="facebook"></a> <a href="#" class="twitter"></a>
                </div><!-- share-->
              </div><!-- left-->
              <div class="offers">
                <p class="header">Images de l'offre</p>
<?php if(count($images) > 0) { ?>
                <ul class="img">
  <?php foreach($images as $i => $image) { ?>
                  <li><a href="<?php echo $offersDir;?><?php echo $image['imageName'];?>" rel="offerImages"><img src="<?php echo $offersDir;?>small_<?php echo $image['imageName'];?>" /></a></li>
  <?php } ?>
                </ul>
<?php } else { ?>
                <p class="notice">Il n'y a pas d'images pour cette offre.</p>
<?php } ?>
              </div><!-- offers-->
            </div><!-- adContent -->
<?php if(($allOffers = count($offers)) > 0) { ?>
            <div class="list">
              <p class="header">Autres offres du catalogue <?php echo $offer["catalogueName"];?></p>
              <ul>
  <?php foreach($offers as $o => $oneOffer) { ?>
    <?php if($oneOffer["id_of"] != $offer["id_of"]) { ?>
      <?php $class = ""; if($allOffers == ($o + 1)) { $class = "last"; } ?>
                <li class="<?php echo $class;?>"><span class="title"><a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $oneOffer["catalogueUrl"], 'catalogueId' => $oneOffer['id_cat'], 'offer' => $view["frontend"]->makeUrl($oneOffer["offerName"]), 'offerId' => $oneOffer["id_of"]))?>"><?php echo $oneOffer["offerName"];?></a></span>
                  <span class="price"><?php echo $oneOffer["offerPrice"];?>€</span>
                </li>
    <?php } ?>
  <?php } ?>
              </ul>
            </div><!-- list-->
<?php } ?>
<?php $view['slots']->set('breadcrumb', array(
array('url' => $view['router']->generate('offersAll', array()), 'anchor' => "Les offres"),
array('url' => $view['router']->generate('offersByCategory', array('category' => $offer['categoryUrl'])), 'anchor' => "Offres ".$offer['categoryName']),
array('url' => $view['router']->generate('catalogueShow', array('url' => $view['frontend']->makeUrl($offer['catalogueName']), 'id' => $offer['id_cat'])), 'anchor' => $offer['catalogueName'])
)); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $offerUrl, 'offerId' => $offer['id_of'])), 'anchor' => $offer["offerName"])); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('tags', $tags);?>
<?php $view['slots']->set('js', array('functions.js', 'jquery.fancybox-1.3.4.min.js', 'showOffer.js'));?>
<?php $view['slots']->set('css', array('list.css', 'jquery.fancybox-1.3.4.css'));?>