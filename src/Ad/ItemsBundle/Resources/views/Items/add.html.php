<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => $titleAction)); ?>
              <div class="textContent">
<?php echo $view->render('AdItemsBundle:Items:addForm.html.php', array('isSuccess' => $isSuccess, 'adId' => $adId, 'edit' => $edit, 'add' => $add, 'formErrors' => $formErrors, 'form' => $form, 'formFields' => $formFields)); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('adsMyList'), 'anchor' => "Mes annonces"))); ?>
<?php if($edit) { ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsEdit', array('id' => $adId)), 'anchor' => $titleAction)); ?>
<?php } elseif($add) { ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsAdd', array()), 'anchor' => $titleAction)); ?>
<?php } ?>
<?php $view['slots']->set('js', array('functions.js', 'users/addAd.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>