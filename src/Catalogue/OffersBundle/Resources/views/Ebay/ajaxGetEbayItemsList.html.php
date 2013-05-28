<script type="text/javascript">
<?php foreach($items as $i => $item) { ?>
urlsLoad[<?php echo $i;?>] = '<?php echo $view['router']->generate('ajaxGetEbayItem', array('item' => $i));?>';
<?php } ?>
</script>
<?php if($ebayConnectError) { ?>
  <?php echo $view->render('::frontend_ajax_error.html.php', array('text' => 'Une erreur de connexion s\'est produite. Veuillez réessayer plus tard ou <a href="#">signalez-nous ce dysfonctionnement</a>.'));?>
<?php } else { ?>
  <?php if(count($items) > 0) { ?>
    <?php foreach($items as $i => $item) { ?>
              <li id="bid<?php echo $i;?>" class="<?php if($item['data']['title'] == '') { ?>hidden loadBid<?php } ?> submitBid" >
                <div id="okBox<?php echo $i;?>" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => ''));?></div>
                <div id="errorBox<?php echo $i;?>" class="hidden bidErrors"><?php echo $view->render('::frontend_error_box.html.php', array('text' => ''));?></div>
                <form id="form<?php echo $i;?>" method="post" action="<?php echo $view['router']->generate('ajaxPostEbayItem', array('item' => $i));?>" id="itemsListForm">
                  <p><span id="name<?php echo $i;?>" class="bolder"><?php echo $item['data']['title'];?></span>  <br /><span class="smaller italic">Enchère numéro <?php echo $i;?></span></p>
                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox">
                        <label for="city<?php echo $i;?>">Ville :</label>
                      </div>
                      <div class="formBox">
                        <label for="cat<?php echo $i;?>">Catalogue</label>
                      </div>		
                      <div class="formLine fieldLine twoBoxes">
                        <div id="formcity<?php echo $i;?>Container" class="formBox noMarginLeft containerLine">
                          <select id="city<?php echo $i;?>" name="city<?php echo $i;?>">
                            <option>-- sélectionnez -- </option>
  <?php foreach($cities as $c => $city) { ?>
                            <option value="<?php echo $city['id_ci'];?>" <?php if($city['cityName'] == $item['data']['city']) { ?>selected="selected"<?php } ?>><?php echo $city['cityName'];?></option>
  <?php } ?>
                          </select>
                          <p id="error-city<?php echo $i;?>" class="errors hidden"></p>
                        </div>
                        <div id="formcat<?php echo $i;?>Container" class="formBox containerLine">
                          <select id="cat<?php echo $i;?>" name="cat<?php echo $i;?>">
                            <option>-- sélectionnez -- </option>
  <?php foreach($catalogues as $c => $catalogue) { ?>
                            <option value="<?php echo $catalogue['id_cat'];?>" <?php if($catalogue['id_cat'] == $item['catalogue']) { ?>selected="selected"<?php } ?> ><?php echo $catalogue['catalogueName'];?></option>
  <?php } ?>
                          </select>
                          <p id="error-cat<?php echo $i;?>" class="errors hidden"></p>
                        </div>
                      </div>		
                      <div class="formLine twoBoxes">
                        <div class="formBox">
                          <label for="tax<?php echo $i;?>">Taxe :</label>
                        </div>
                      </div>
                      <div class="formLine fieldLine twoBoxes">
                        <div id="formtax<?php echo $i;?>Container" class="formBox noMarginLeft containerLine">
                          <select id="tax<?php echo $i;?>" name="tax<?php echo $i;?>">
                            <option>-- sélectionnez -- </option>
  <?php foreach($taxes as $t => $tax) { ?>
                            <option value="<?php echo $t;?>" <?php if($t == $defaultTax){ ?>selected="selected"<?php } ?>><?php echo $tax;?></option>
  <?php } ?>
                          </select>
                          <p id="error-tax<?php echo $i;?>" class="errors hidden"></p>
                        </div>
                      </div>
                      <div id="fields<?php echo $i;?>"><?php echo $item['fields'];?></div>                  
                      <div class="formLine twoBoxes">
                        <a href="#" rel="#bid<?php echo $i;?>" class="deleteBid deleteItem floatLeft">supprimer</a>          
                      </div>
                  </fieldset>
                </form>
              </li>
    <script type="text/javascript">hideErrorMsgInit("#form<?php echo $i;?>", "");</script>
    <?php } ?>
  <?php } else { ?>
  <p>Page <?php echo $page;?> : Pas de nouvelles enchères à rajouter.</p>
  <?php } ?>
<?php } ?>