<?php $view->extend('::frontend_base.html.php') ?>
<script type="text/javascript">
var analytics = "<?php echo $view['router']->generate('ajaxAdsCounter', array('ad' => $ad['id_ad']));?>";
</script>
<?php if($offerAdded == 1) { ?>
  <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "L'offre a été correctement rajoutée."));?>
<?php } ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $ad["adName"])); ?>
              <div class="adContent">
                <div class="left">
                  <div class="infos">
                  <p class="user"><a href="<?php echo $view['router']->generate('userProfile', array('id' => $ad["id_us"], 'url' => $view['frontend']->makeUrl($ad["login"])));?>"><?php echo $ad['login'];?></a></p>
                  <p class="city"><span><?php echo $ad['cityName'];?></span></p>
                  <p class="category"><a href="<?php echo $view['router']->generate('adsByCategory', array('category' => $ad['categoryUrl']));?>"><?php echo $ad['categoryName'];?></a></p>
                  <p class="time"><span><?php echo $ad['startTime'];?> - <?php echo $ad['endTime'];?></span></p>
                </div><!-- infos-->
                <div class="buttons">
<?php if(isset($offers[0])) { ?>
                  <p class="best">La meilleure offre : <span class="price"><?php echo $offers[0]["offerPrice"];?></span> <span class="currency">€</span></p>
<?php } ?>
                  <p><a href="<?php echo $view['router']->generate('offerPropose', array('id' => $url['id']));?>" class="button offerBtn">J'ajoute l'offre</a></p>
                  <p><a href="<?php echo $view['router']->generate('adsQuestion', array('category' => $url['category'], 'url' => $url['url'], 'id' => $url['id']));?>" class="button adBtn" style="margin-left:24px;">Je pose une question</a></p>
                  <p class="centerText"><?php if($isSubscribed == 0) { ?><a href="<?php echo $view['router']->generate('adSubscribe', array('ad' => $ad['id_ad']));?>?ticket=<?php echo $ticket;?>" id="subscribe" class="black bolder">Je m'abonne <br /> à cette annonce</a><?php } elseif($isSubscribed == 1) { ?><a href="<?php echo $view['router']->generate('alertDelete', array('id' => $ad['id_ad'], 'type' => 'annonces'));?>?ticket=<?php echo $ticket;?>" id="subscribe" class="black bolder">Je me désabonne <br />de cette annonce</a><?php } ?></p>
                </div><!-- buttons-->
                <div id="resultSubscribe" class="hidden adMsgBox"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => ""));?></div>
                <div class="desc">
                  <p><?php echo $ad['adText'];?></p>
                </div><!-- desc-->
                <div class="box">
                  <p class="header">Les offres attendues :</p>
                  <table>
                    <tbody>
                      <tr>
                        <td style="width:110px;">Prix max :</td>
                        <td class="right"><span><?php echo $ad['adBuyTo'];?></span> €</td>
                      </tr>
                      <tr>
                        <td>Etat de l'objet :</td>
                        <td class="right"><?php echo $adsStates[$ad['adObjetState']];?></td>
                      </tr>
                      <tr>
                        <td>Localisation :</td>
                        <td class="right"><?php echo $view['items']->getLocalizationLabel($ad);?></td>
                      </tr>
                      <tr>
                        <td>Minimun d'appréciation :</td>
                        <td class="right"><?php echo  $usersAvg[$ad['adMinOpinion']];?></td>
                      </tr>
                      <tr>
                        <td>Type du vendeur&nbsp;:</td>
                        <td class="right"><?php echo $userType[$ad['adSellerType']];?></td>
                      </tr>
                      <tr>
                        <td>Payments acceptés :</td>
                        <td class="right"><?php echo $view['items']->getPaymentLabels($payments, $payLabels);?></td>
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
                <p class="header">Offres proposées :</p>
<?php if(count($offers) > 0) { ?>
                <ul>
  <?php foreach($offers as $o => $offer) { ?>
                  <li><?php echo ($o+1); ?>
                    <a href="<?php echo $view['router']->generate('offerShow', array('catalogueId' => $offer['id_cat'], 'catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'offerId' => $offer['id_of'], 'offer' => $view['frontend']->makeUrl($offer['offerName'])));?>" target="_blank"><?php echo $offer['offerName']; ?></a>
                    <p class="price"><?php echo $offer['offerPrice'];?><span>€</span></p>
                    <p class="user"><a href="#"><?php echo $offer["login"];?></a></p>
    <?php if($catAction && $offer['id_us'] == $userId) { ?>
                    <p class="verticalSep"><a href="<?php echo $view['router']->generate('offerRemoveFromAd', array('offer' => $offer['id_of'], 'ad' => $url['id']));?>?ticket=<?php echo $ticket;?>" class="smaller">retirer cette offre</a></p>
    <?php } elseif($connected && $userId == $ad['id_us'] && $ad['adOffer'] != $offer['id_of']) { ?>
                    <p class="verticalSep"><a href="<?php echo $view['router']->generate('adAcceptOffer', array('offer' => $offer['id_of'], 'ad' => $url['id']));?>?ticket=<?php echo $ticket;?>" class="smaller">accepter cette offre</a></p>
    <?php } ?>
    <?php if($ad['adOffer'] == $offer['id_of']) { ?>
                   <p>Offre validée.</p>
    <?php } ?>
                  </li>
  <?php } ?>
                </ul>
<?php } else { ?>
                <p class="notice">Aucune offre n'a pas été proposée. Soyez le premier à <a href="<?php echo $view['router']->generate('offerPropose', array('id' => $url['id']));?>">proposer votre offre pour cette annonce</a>.</p>
<?php } ?>
              </div><!-- offers-->
            </div><!-- adContent -->
<?php if(($allAds = count($ads)) > 0) { ?>
            <div class="list">
              <p class="header">Autres annonces <?php echo $ad["categoryName"];?></p>
              <ul>
  <?php foreach($ads as $o => $oneAd) { ?>
    <?php if($oneAd["id_ad"] != $ad["id_ad"]) { ?>
      <?php $class = ""; if($allAds == ($o + 1)) { $class = "last"; } ?>
                <li class="<?php echo $class;?>"><span class="title"><a href="<?php echo $view['router']->generate('adsShowOne', array('category' => $url["category"], 'url' => $view["frontend"]->makeUrl($oneAd["adName"]), 'id' => $oneAd["id_ad"]))?>"><?php echo $oneAd["adName"];?></a></span>
                  <span class="price"><?php echo $oneAd["adBuyTo"];?> €</span>
                  <span class="offers"><?php echo $oneAd["adOffers"];?> offre(s)</span>
                </li>
    <?php } ?>
  <?php } ?>
              </ul>
            </div><!-- list-->
<?php } ?>
<?php $view['slots']->set('breadcrumb', array(
array('url' => $view['router']->generate('adsAll', array()), 'anchor' => "Les annonces"),
array('url' => $view['router']->generate('adsByCategory', array('category' => $ad['categoryUrl'])), 'anchor' => "Annonces ".$ad['categoryName'])
)); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsShowOne', array('category' => $url["category"], 'url' => $url["url"], 'id' => $url["id"])), 'anchor' => $ad["adName"])); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('tags', $tags);?>
<?php $view['slots']->set('js', array('functions.js', 'users/showAd.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>