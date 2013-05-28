<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Supprimer un catalogue")); ?>
              <div class="textContent">
                <div id="loadOffers" class="hidden"><?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Suppression des offres en cours", "hidden" => false, "id" => "loader1"));?></div>
                <div id="loadCatalogue" class="hidden"><?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Suppression du catalogue en cours", "hidden" => false, "id" => "loader2"));?></div>
<?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="okOffers" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => 'Des offres ont été correctement supprimées'));?></div>
                <div id="okCatalogue" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => 'Le catalogue a été correctement supprimé'));?></div>
                <p>Les offres doivent supprimées. Nombre d'offres qui seront supprimés : <b><?php echo $catalogue['catalogueOffers'];?></b> <?php if($inAd) { ?>dont <b><?php echo $offersAd;?></b> qui <?php if($offersAd == 1) { ?>a été choisie<?php } else { ?>ont été choisies<?php } ?> par les annonceurs.<?php } ?></p>
                <p>Nombre d'offres : <span id="allOff" class="bolder"><?php echo $catalogue['catalogueOffers'];?></span></p>
                <p>Nombre d'offres supprimées : <span id="delOff" class="bolder">0</span></p>
                <div id="questionDelete">
                  <p class="centerText question centeredBox">Voulez-vous continuer ?</p>
                  <p class="formLine centerText centeredBox">
                    <a href="#" class="buttonSmall button submit" id="confirmDelete">oui, je veux supprimer ces offres</a>
                    <a href="<?php echo $view['router']->generate('catalogueMyList', array());?>" class="buttonSmall btn cancel noFloat" id="denyDelete">non, je ne veux pas supprimer ces offres</a>
                  </p>
                </div><!-- questionDelete-->
  <?php if($catalogue['catalogueOffers'] > 0) { ?>
  <script type="text/javascript">
  var queues = <?php echo round($catalogue['catalogueOffers']/$perQueue);?>;
  var ticket = "<?php echo $ticket;?>";
  var deleted = 0;
  var allOffers = <?php echo $catalogue['catalogueOffers'];?>;
  var deleteQueueUrl = "<?php echo $view['router']->generate('offersDeleteQueue') ?>?ticket="+ticket;
  var catalogueId = "<?php echo $catalogue['id_cat'];?>";
  var deleteCatalogueUrl = "<?php echo $view['router']->generate('catalogueDelete', array('id' => $catalogue['id_cat'])); ?>?r=json&ticket="+ticket;
  </script>
  <?php } else { ?>
    <?php echo $view->render('::frontend_ok_box.html.php', array('text' => "Le catalogue a été correctement supprimé"));?>
  <?php } ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('catalogueMyList', array()), 'anchor' => "Mes catalogues" ))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('catalogueDelete', array('id' => $catalogue['id_cat'])), 'anchor' => "Supprimer un catalogue")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/deleteCatalogue.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array(''));?>