<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $titleAction)); ?>
              <div class="textContent">
<?php echo $view->render('CatalogueOffersBundle:Offers:addForm.html.php', array('isSuccess' => $isSuccess, 'ticket' => $ticket, 'fees' => $fees, 'configUploadify' => $configUploadify,
'randomId' => $randomId, 'route' => $route, 'edit' => $edit, 'add' => $add, 'zones' => $zones, 'canAddImages' => $canAddImages, 'offerImages' => $offerImages, 'dir' => $dir, 'offerId' => $offerId, 'formErrors' => $formErrors, 'tmpId' => $tmpId, 'form' => $form, 'formFields' => $formFields,
'adRow' =>array('category' => '', 'country' => '', 'objectState' => '', 'city' => '', 'region' => '', 'priceFrom' => '', 'priceTo' => ''))); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('offersMyList'), 'anchor' => "Mes offres"))); ?>
<?php if($edit) { ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offersEdit', array('id' => $offerId)), 'anchor' => $titleAction)); ?>
<?php } elseif($add) { ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offersAdd', array()), 'anchor' => $titleAction)); ?>
<?php } ?>
<?php $view['slots']->set('js', array('functions.js', 'swfobject.js', 'jquery.uploadify-2.1.4.min.js', 'users/addOffer.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>