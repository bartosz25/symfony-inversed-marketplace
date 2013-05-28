<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Proposer une offre")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_error_box.html.php', array('text' => "Vous ne rémplissez pas les conditions définies pour proposer votre offre. Les critères invalides sont les suivants : ".implode(',', $errors).".".$nextSent)); ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(
array('url' => $view['router']->generate('adsAll', array()), 'anchor' => "Les annonces"),
array('url' => $view['router']->generate('adsByCategory', array('category' => $ad['categoryUrl'])), 'anchor' => "Annonces ".$ad['categoryName']),
array('url' => $view['router']->generate('adsShowOne', array('id' => $ad['id_ad'], 'category' => $ad['categoryUrl'], 'url' => $view['frontend']->makeUrl($ad['adName']))), 'anchor' => $ad['adName'])
)); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('offerPropose', array('id' => $id)), 'anchor' => "Proposer une offre")); ?>
<?php $view['slots']->set('js', array('functions.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array());?>