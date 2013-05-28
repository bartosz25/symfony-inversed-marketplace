<?php $view->extend('::frontend_base.html.php') ?>
<?php echo $view->render('::frontend_content_header.html.php', array('title' => "Finir une annonce")); ?>
<script type="text/javascript">
var validAdUrl = "<?php echo $view['router']->generate('adsEnd', array('id' => $id));?>";
</script>
              <div class="textContent">
                <p>Aucune offre n'a pas été acceptée pour cette commande.</p>
<?php if(count($offers) > 0) { ?>
  <?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Veuillez patienter"));?>
  <?php echo $view->render('::frontend_ajax_error.html.php', array('text' => ''));?>
                <div id="successSaving" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => ''));?></div>
                <p class="bolder verticalSep">Vous pouvez accepter une offre et transformer votre annonce en commande.</p>
                <table class="item verticalSep" cellspacing="0">
                  <thead>
                    <tr>
                      <th class="leftTopRadius leftBorder">Offre</th>
                      <th>Prix TTC €</th>
                      <th class="rightTopRadius rightBorder">Action</th>
                    </tr>
                  </thead>
                  <tbody>
  <?php foreach($offers as $o => $offer) { ?>
                    <tr>
                      <td class="leftBorder"><?php echo $offer['offerName'];?></td>
                      <td><?php echo $offer['offerPrice'];?></td>
                      <td><a href="<?php echo $view['router']->generate('adAcceptOffer', array('offer' => $offer['id_of'], 'ad' => $id));?>?ticket=<?php echo $ticket;?>" class="acceptAd">accepter</a></td>
                    </tr>
  <?php } ?>
                  </tbody>
                </table>
                <p id="link" class="verticalSep centerText font14"><a href="<?php echo $view['router']->generate('adsEndWithoutOffers', array('id' => $id));?>">Je veux terminer l'annonce sans valider aucune offre</a></p>                
<?php } else { ?>
                <p id="link" class="verticalSep centerText font14"><a href="<?php echo $view['router']->generate('adsEndWithoutOffers', array('id' => $id));?>">Je veux terminer l'annonce quand même</a></p>                
<?php } ?>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array(array('url' => $view['router']->generate('adsMyList', array()), 'anchor' => "Mes annonces"))); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('adsEnd', array('id' => $id)), 'anchor' => "Finir une annonce")); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array('functions.js', 'users/endAdChooseOffer.js'));?>
<?php $view['slots']->set('css', array('list.css'));?>