                  <fieldset class="defaultForm">
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Prénom et nom</span></div>
                      <div class="formBox"><span class="imitLabel">Adresse</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><span class="<?php echo $spanClass;?>"><?php echo $ad['addressFirstName'];?> <?php echo $ad['addressLastName'];?></span></div>
                      <div class="formBox"><span class="<?php echo $spanClass;?>"><?php echo $ad['addressStreet'];?>, <?php echo $ad['addressPostalCode'];?> <?php echo $ad['addressCity'];?> (<?php echo $ad['countryName'];?>)</span></div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Informations supplémentaires</span></div>
                      <div class="formBox"><span class="imitLabel">Moyen de livraison préféré</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><span class="<?php echo $spanClass;?> small grey"><?php if($ad['addressInfos'] != '') { ?><?php echo $ad['addressInfos']; } else { ?>-- pas d'informations supplémentaires --<?php } ?></span></div>
                      <div class="formBox"><span class="<?php echo $spanClass;?>"><?php echo $ad['preferedDeliveryLabels'];?></span></div>
                    </div>
                    <div class="formLine twoBoxes">
                      <div class="formBox"><span class="imitLabel">Mode de paiement</span></div>
                    </div>
                    <div class="formLine fieldLine twoBoxes">
                      <div class="formBox noMarginLeft"><span class="<?php echo $spanClass;?>"><?php echo $ad['orderPaymentLabel'];?></span></div>
                    </div>
                  </fieldset>