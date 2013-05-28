<?php $view->extend('::frontend_base.html.php') ?>
<?php if($orderPage) { ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Mettre à jour une commande")); ?>
<?php } else { ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Finir une annonce")); ?>
<?php } ?>
              <div id="orderContent" class="textContent article">
<?php if(!$hasOffer) { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Vous ne pouvez pas déclencher cette commande. Votre annonce ne contient pas d'offre."));?>
<?php } else { ?>
  <?php if($success == 1 && $page == 'initOrder') { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le déclenchement de la commande s'est déroulé avec succès. Veuillez patienter la réponse de votre vendeur. Il va 
    vous communiquer ses coordonnées de payement ainsi que le montant total qui contiendra les frais de livraison. Vous en 
    serez prévenu par mail.<br /><br /> Cependant, si vous n'arrivez pas à avoir ces informations, veuillez contacter le vendeur directement via le messageries privées du site."));?>
    <?php } elseif($success == 1 && $page != 'initOrder') { ?>
        <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Les Informations ont été correctement renseignées.")); ?>
    <?php } else { ?>
    <script type="text/javascript">
    var errorStates = '<?php echo $errorStates;?>';
    </script>
<?php //print_r($formErrors);?>
                <p class="font14"><b>Etapes de la commande :</b></p>
                <ul class="prestaSteps">
  <?php echo $view['orders']->constructSteps($ad["orderState"], $orderStates);?>
                </ul>
  <?php if($stepDescription != "") { ?>
                <p class="font14"><b>Etape en cours :</b></p>
                <div class="noticeBox italic"><p><?php echo $stepDescription;?></p></div>
  <?php } ?>
  <?php if($lastState) { ?><p class="addItem floatLeft"><a href="<?php echo $view['router']->generate('opinionWrite', array('id' => $ad['id_ad']));?>">noter <?php echo $whoNotes;?></a></p><?php } ?>
                <ul id="fixedMenu" class="menuFixed">
                  <li class="label">Les composants de la commande:</li>
  <?php if($commentsList != null) { ?><li><a href="#COMMENTS" class="buttonLinkStyle floatLeft">Commentaires</a></li><?php } ?>
                  <li><a href="#AD" class="buttonLinkStyle floatLeft">Annonce</a></li>
                  <li><a href="#DELIVERY" class="buttonLinkStyle floatLeft">Adresse de livraison</a></li>
                  <li><a href="#ITEM" class="buttonLinkStyle floatLeft">Objet de la vente</a></li>
                  <li><a href="#DELIVERYMODE" class="buttonLinkStyle floatLeft">Mode de livraison</a></li>
                </ul>
                <div class="clear"></div>
    <?php if($commentsList != null) { ?>
                <div id="COMMENTS" class="verticalSep">
        <?php echo $view->render('OrderOrdersBundle:OrdersComments:commentsList.html.php', array('pager' => $pager, 'commentsList' => $commentsList, 'id' => $ad['id_ad'])); ?>
                <div class="clear "></div>
                </div>
    <?php } ?>
                <form method="post" action="<?php echo $formUrl;?>" id="formUpdateOrder">
                  <p id="AD" class="artTitle">Annonce</p>
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Titre</span></div>
                      <div class="formBox"><span class="imitLabel">Etat</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><a href="<?php echo $view['router']->generate('adsShowOne', array('category' => $view['frontend']->makeUrl($ad['categoryName']), 'url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad']));?>" title="Voir <?php echo $ad['adName'];?>" target="_blank" class="blue"><?php echo $ad['adName'];?></a></div>
                      <div class="formBox"><?php echo $states[$ad['adObjetState']];?></div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Appréciation du vendeur</span></div>
                      <div class="formBox"><span class="imitLabel">Type du vendeur</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><?php echo $ad['adMinOpinion'];?></div>
                      <div class="formBox"><?php echo $userTypes[$ad['adSellerType']];?></div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Prix max TTC</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><?php echo $ad['adBuyTo'];?> €</div>
                    </div>
                  </fieldset>
                  <p id="DELIVERY" class="artTitle">Adresse de livraison  
                    <?php if($showForm && $role == 'buyer') { ?><a href="<?php echo $view['router']->generate('addressesList');?>" id="addAddressBook" class="blue smallInfos">ajouter depuis carnet </a><?php } ?>
                  </p>
    <?php if(!$showForm || $role != 'buyer') { ?>
      <?php echo $view->render('OrderOrdersBundle:Orders:deliveryAddressBox.html.php', array('spanClass' => $spanClass, 'ad' => $ad)); ?>
    <?php } elseif($showForm && $role == 'buyer') { ?>
      <?php echo $view->render('OrderOrdersBundle:Orders:deliveryAddressForm.html.php', array('form' => $form, 'form2' => $form2, 'formErrors' => $formErrors)); ?>
    <?php } ?>
                  <p id="ITEM" class="artTitle">Objet de la vente</p>
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Nom</span></div>
                      <div class="formBox"><span class="imitLabel">Etat</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><a href="<?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $view['frontend']->makeUrl($offer['offerName']), 'offerId' => $offer['id_of']));?>" title="Voir <?php echo $offer['offerName'];?>" target="_blank" class="blue"><?php echo $offer['offerName'];?></a></div>
                      <div class="formBox"><?php echo $states[$offer['offerObjetState']];?></div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Catalogue</span></div>
                      <div class="formBox"><span class="imitLabel">Prix TTC</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><a href="<?php echo $view['router']->generate('catalogueShow', array('url' => $view['frontend']->makeUrl($offer['catalogueName']), 'id' => $offer['id_cat']));?>" title="Voir autres objets de ce catalogue" target="_blank" class="blue"><?php echo $offer['catalogueName'];?></a></div>
                      <div class="formBox"><?php echo $ad['adOfferPrice'];?> €</div>
                    </div>
                  </fieldset>
    <?php if($showForm && $role == 'seller' && $showFormPay) { ?>
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['paymentInfos'], "Informations sur le payement") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formaddressFirstNameContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'addressFirstName', 'boxError');?>">
                        <?php echo $view['form']->widget($form['paymentInfos']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-addressFirstName" class="errors">%s</p>', $formErrors, 'addressFirstName');?>
                      </div>
                      <p class="italic smaller">Les données ne sont pas stockées. Elles sont seulement envoyées à l'adresse e-mail de votre acheteur</p>
                    </div>
                  </fieldset>
    <?php } ?>
                  <p id="DELIVERYMODE" class="artTitle">Mode de livraison</p>
    <?php echo $view->render('OrderOrdersBundle:Orders:deliveryModeBox.html.php', array('showDeliveryForm' => (bool)($showForm && $role == 'seller' && $showFormPay), 
    'showCarrierRefForm' => (bool)($showForm && $role == 'seller' && !$showFormPay), 'form' => $form, 'formErrors' => $formErrors, 'ad' => $ad)); ?>
    <?php if($form !== null) { ?>
                  <fieldset id="orderState" class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <?php echo $view['form']->label($form['orderState'], 'Etat de la commande') ?>
                      </div>
                      <div class="formBox">
                        <?php echo $view['form']->label($form['orderComment'], "Commentaire") ?>
                      </div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div id="formorderStateContainer" class="formBox noMarginLeft <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderState', 'boxError');?>">
                        <?php echo $view['form']->widget($form['orderState']) ?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderState" class="errors">%s</p>', $formErrors, 'orderState');?>
                      </div>
                      <div id="formorderCommentContainer" class="formBox <?php echo $view['frontendForm']->setErrorClass($formErrors, 'orderComment', 'boxError');?>">
                        <?php echo $view['form']->widget($form['orderComment']) ;?>
                        <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-orderComment" class="errors">%s</p>', $formErrors, 'orderComment');?>
                      </div>
                    </div>
                  </fieldset>
                  <div class="formLine btnLine"><?php echo $view['form']->widget($form['ticket']);?>
                    <input type="submit" name="send" value="Actualiser" class="button" />
                  </div>
    <?php } ?>
                </form>
  <?php } ?>
                <div id="addressBook">
                  <?php echo $view->render('::frontend_ajax_loader.html.php', array('id' => 'addressLoader', 'text' => "Veuillez patienter"));?>
                  <div id="addressList"></div>
                </div>
<?php } ?>
              </div><!-- textContent-->
<?php if($orderPage) { ?>
  <?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('ordersList', array()), 'anchor' => "Mes commandes"))); ?>
  <?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('orderUpdateData', array('id' => $ad['id_ad'])), 'anchor' => "Mettre à jour une commande")); ?>
<?php } else { ?>
  <?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('adsMyList', array()), 'anchor' => "Mes annonces"))); ?>
  <?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsEnd', array('id' => $ad['id_ad'])), 'anchor' => "Finir une annonce")); ?>
<?php } ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/endAd.js', '2.3.0-crypto-sha1.js', 'jquery.stickyscroll.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>