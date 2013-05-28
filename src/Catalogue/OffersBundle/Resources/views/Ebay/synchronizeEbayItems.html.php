<?php $view->extend('::frontend_base.html.php') ?>
<script type="text/javascript">
var urlsListLoad = new Array();
<?php for($s = 1; $s <= 1; /*$maxPages;*/ $s++) { ?>
urlsListLoad[<?php echo $s;?>] =  '<?php echo $view['router']->generate('ajaxGetEbayItemsList', array('page' => $s));?>';
<?php } ?>
</script>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Synchronisation de mes catalogues avec mes enchères eBay")); ?>
              <div class="textContent">
<?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Chargement des enchères"));?>
<?php echo $view->render('::frontend_ajax_loader.html.php', array('id' => 'importLoader', 'text' => "Import des enchères en cours"));?>
<div id="errorTxt"><?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?></div>
                <p class="importAll verticalSep hidden"><a href="#" class="button submit">importer toutes les enchères</a></p>
                <p class="importAllErrors verticalSep hidden"><a href="#" class="button submit">importer le reste des enchères</a></p>
                <ul id="itemsList"></ul>
                <p class="importAll verticalSep hidden"><a href="#" class="button submit">importer toutes les enchères</a></p>
                <p class="importAllErrors verticalSep hidden"><a href="#" class="button submit">importer le reste des enchères</a></p>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('synchronizeEbayItems', array()), 'anchor' => "Synchronisation de mes catalogues avec mes enchères eBay")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/syncEbayItems.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array(''));?>