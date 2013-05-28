<?php $view->extend('::frontend_base.html.php') ?>
<script type="text/javascript">
var catsLimit =  <?php echo count($categories); ?>;
var fromDb =  <?php echo (int)$alreadyDb; ?>;
var categories = new Array();
<?php foreach($categories as $cat => $category) { ?>
categories.push(<?php echo $category['id'];?>);
<?php } ?>
var getPresCatUrl = "<?php echo $view['router']->generate('ajaxGetPrestaCat'); ?>";
</script>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
              <div class="textContent">
<?php echo $view->render('CatalogueOffersBundle:Prestashop:prestashopSteps.html.php', array('steps' => array(1 => true, 2 => false, 3 => false))); ?>
<?php if(!$alreadyDb) { ?>
                  <ul style="width:200px;" class="clear">
<?php $i = 1; foreach($categories as $ca => $category) { ?>
                    <li id="cat-<?php echo $ca;?>" class="smallLoader">Chargement de la <span><?php echo ($i++); ?></span>e catégorie</li>
<?php } ?>
                  </ul> 
<?php } ?>
<?php if($alreadyDb) { ?>
                  <p><b>Les catégories reprises et mémorisées le <?php echo $dbData['dateOfSync'];?></b> . Si vous avez effectué les changements, 
                  vous pouvez <a href="<?php echo $view['router']->generate('synchronizeReloadPrestashop'); ?>?ticket=<?php echo $ticket;?>">réinitialiser la synchronisation</a>.</p>
<?php } ?>
                  <p class="verticalSep">Toutes les catégories de votre boutique doivent correspondre à une catégorie du site et à un de vos catalogues.</p>
                  <p class="verticalSep">Si vous ne précisez pas la catégorie du site ou le catalogue, les produits de cette catégorie de votre boutique
                  ne seront pas rajoutées.</p>
                  <p class="verticalSep">Les produits appartenant à plusieurs catégories seront rajoutés une fois, dans un seul catalogue.</p>
                  
                <div id="relations">
                  <form method="post" action="<?php echo $view['router']->generate('synchronizeMapPrestashop');?>?ticket=<?php echo $ticket;?>">
                    <fieldset class="defaultForm">
                      <table style="width:470px;" class="items" cellspacing="0">
                        <thead> 
                          <tr> 
                            <th class="leftTopRadius leftBorder">Catégorie sur votre boutique</th> 
                            <th>Catégorie sur le site</th> 
                            <th class="rightTopRadius rightBorder">Votre catalogue</th>
                          </tr> 
                        </thead>
                        <tbody>
<?php if($alreadyDb) { foreach($categories as $c => $category) { 
  $relCategory = "";
  if(isset($category['relations']['category'])) $relCategory = $category['relations']['category']; 
  $relCatalogue = "";
  if(isset($category['relations']['catalogue'])) $relCatalogue = $category['relations']['catalogue'];
?>
                          <tr>
                            <td class="leftBorder"><input type="hidden" name="store-<?php echo $category['id'];?>" /><b><?php echo $category['stats']['name'];?></b></td>
                            <td><?php echo $view->render('FrontendFrontBundle:Frontend:select.html.php', array(
                            'default' => $relCategory, 'elements' => $siteCategories, 'id' => 'id_ca', 'name' => 'categoryName', 'select' => 'category-'.$category['id'])); ?></td>
                            <td><?php echo $view->render('FrontendFrontBundle:Frontend:select.html.php', array(
                            'default' => $relCatalogue, 'elements' => $siteCatalogues, 'id' => 'id_cat', 'name' => 'catalogueName', 'select' => 'catalogue-'.$category['id'])); ?></td>
                          </tr>
<?php } } ?>
                        </tbody>
                      </table>
                      <div class="formLine btnLine verticalSep"><input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket;?>" />
                        <input type="submit" name="send" value="Synchroniser" class="button" />
                      </div>
                    </fieldset>
                  </form>
                  <p class="floatLeft"><a href="<?php echo $view['router']->generate('synchronizeDeletePrestashop');?>?ticket=<?php echo $ticket;?>" class="deleteItem floatLeft">annuler la synchronisation</a></p>
                  <div class="hidden">
                    <div id="selectCategories">
<?php echo $view->render('FrontendFrontBundle:Frontend:select.html.php', array(
'default' => '', 'ticket' => $ticket, 'elements' => $siteCategories, 'id' => 'id_ca', 'name' => 'categoryName', 'select' => 'test_categories')); ?>
                    </div>
                    <div id="selectCatalogues">
<?php echo $view->render('FrontendFrontBundle:Frontend:select.html.php', array(
'default' => '', 'ticket' => $ticket, 'elements' => $siteCatalogues, 'id' => 'id_cat', 'name' => 'catalogueName', 'select' => 'test_catalogues')); ?>
                    </div>
                  </div>
                </div><!-- relations-->
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('synchronizeMapPrestashop', array()), 'anchor' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/mapPres.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array('list.css'));?>