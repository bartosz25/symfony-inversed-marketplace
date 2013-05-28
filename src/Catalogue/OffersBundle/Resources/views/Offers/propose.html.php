<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Proposer une offre")); ?>
              <div class="textContent">
<?php if($result == 1) { ?>
<script type="text/javascript">
var params = {adCategory: '<?php echo (int)$ad['id_ca'];?>', adState: '<?php echo (int)$ad['adObjetState'];?>', 
      /*adFrom: '<?php echo (float)$ad['adBuyFrom'];?>',*/ adTo: '<?php echo (float)$ad['adBuyTo'];?>', 'adGeo' : '<?php echo (int)$ad['adSellerGeo'];?>',
      adCity: '<?php echo (int)$ad['id_ci'];?>', adRegion: '<?php echo (int)$ad['id_re'];?>', adCountry: '<?php echo (int)$ad['id_co'];?>'};
var relations = new Array();
relations['AddOffer_offerCategory'] = params.adCategory;
relations['AddOffer_offerObjetState'] = params.adState;
relations['AddOffer_offerCountry'] = params.adCountry;
relations['AddOffer_offerCity'] = params.adCity;
<?php $geoElement = ''; switch($ad['adSellerGeo']) {
case 1:
$geoElement = ', #AddOffer_offerCountry';
break;
case 3: 
$geoElement = ', #AddOffer_offerCity';
break;
} ?>
</script>
              <ul class="menuFixed">
                <li class="label">Ajouter une offre : </li>
                <li><a href="<?php echo $view['router']->generate('ajaxGetOffersToAd', array('ad' => $adId)); ?>" class="buttonLinkStyle floatLeft propose isAjax" id="catalogue">du catalogue</a></li>
                <li><a href="#" class="buttonLinkStyle floatLeft propose"  id="form">nouvelle</a></li>
              </ul>
              <div class="clear"></div>
              <div id="form_container" class="hidden">
<?php echo $view->render('CatalogueOffersBundle:Offers:addForm.html.php', array('isSuccess' => '', 'add' => true, 'edit' => false,
'formErrors' => array(), 'form' => $form, 'formFields' => $formFields, 'ad' => $adId, 'tmpId' => $tmpId, 'zones' => $zones,
'offerImages' => $offerImages, 'canAddImages' => $canAddImages, 'adRow' => $adRow, 'randomId' => $randomId, 'configUploadify' => $configUploadify)); ?>
              </div>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des offres", "id" => "loadOffers"));?>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
              <div id="catalogue_container" class="hidden"></div>
<?php } else { ?>
  <?php echo $view->render('::frontend_error_box.html.php', array('text' => "Vous ne rémplissez pas les conditions définies pour proposer votre offre. Les critères invalides sont les suivants : ".implode(',', $errors))); ?>
<?php } ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(
array('url' => $view['router']->generate('adsAll', array()), 'anchor' => "Les annonces"),
array('url' => $view['router']->generate('adsByCategory', array('category' => $ad['categoryUrl'])), 'anchor' => "Annonces ".$ad['categoryName']),
array('url' => $view['router']->generate('adsShowOne', array('id' => $ad['id_ad'], 'category' => $ad['categoryUrl'], 'url' => $view['frontend']->makeUrl($ad['adName']))), 'anchor' => $ad['adName'])
)); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offerPropose', array('id' => $id)), 'anchor' => "Proposer une offre")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/proposeOffer.js', 'swfobject.js', 'jquery.uploadify-2.1.4.min.js', 'users/addOffer.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array("list.css"));?>