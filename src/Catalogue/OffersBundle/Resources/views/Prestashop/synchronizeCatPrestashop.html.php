<?php $view->extend('::frontend_base.html.php') ?>
<script type="text/javascript">
var catsLimit =  <?php echo count($categories); ?>;
var categories = new Array();
<?php foreach($categories as $cat => $category) { ?>
categories.push(<?php echo $category['id'];?>);
<?php } ?>
var prodsLimit =  <?php echo count($products); ?>;
var products = new Array();
<?php foreach($products as $pro => $product) { ?>
products.push(<?php echo $product['id'];?>);
<?php } ?>
var getPrestaProd = "<?php echo $view['router']->generate('ajaxGetPrestaProd'); ?>?ticket=<?php echo $ticket;?>";
</script>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
              <div class="textContent">
<?php echo $view->render('CatalogueOffersBundle:Prestashop:prestashopSteps.html.php', array('steps' => array(1 => true, 2 => true, 3 => false))); ?>
                <p class="bolder">Nombre de catégories trouvées : <?php echo count($categories); ?></p>
                <p class="bolder">Nombre de produits trouvés : <?php echo count($products); ?></p>
                <p>Les produits sélectionnés, déjà rajoutés dans vos catalogues, seront modifiés.</p>
                <ul class="verticalSep">
<?php $i = 1; foreach($products as $p => $product) { ?>
                  <li id="prod-<?php echo $p;?>" class="" style="width:170px;">Chargement du <span><?php echo ($i++); ?></span>e produit</li>
<?php } ?>
                </ul>
                <form method="post" action="<?php echo $view['router']->generate('synchronizeCatPrestashop');?>?ticket=<?php echo $ticket;?>">
                    <fieldset class="defaultForm">
                      <div class="formLine formChbox">
                        <div class="formBox oneLabel">
                          <b>Sélectionnez les produits à rajouter dans vos catalogues :</b>
                        </div>
                        <div  id="products"></div>
                      </div>
                      <div id="containerSubmit" class="formLine btnLine verticalSep hidden"><input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket;?>" />
                        <input type="submit" name="send" value="Ajouter sélectionnés" class="button" />
                      </div>
                    </fieldset>
                </form>
                <p class="floatLeft"><a href="<?php echo $view['router']->generate('synchronizeDeletePrestashop');?>?ticket=<?php echo $ticket;?>" class="deleteItem floatLeft">annuler la synchronisation</a></p>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('synchronizeCatPrestashop', array()), 'anchor' => "Synchronisation de mes catalogues avec ma boutique Prestashop")); ?>
<?php $view['slots']->set('js', array('functions.js', 'users/catPres.js'));?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('css', array('list.css'));?>
  