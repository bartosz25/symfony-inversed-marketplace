<?php echo $view->render('::frontend_ajax_error.html.php', array('id' => 'errorChoiceAddress', 'text' => "Une erreur s'est produite. Veuillez réessayer."));?>
                <table style="width:510px;" class="items" cellspacing="0">
                  <thead> 
                    <tr>
                      <th class="leftTopRadius leftBorder">Intitulé</th>
                      <th class="rightTopRadius rightBorder">Actions</th>
                    </tr> 
                  </thead>
                  <tbody>
<?php foreach($addresses as $a => $address) { ?>
                    <tr>
                      <td class="leftBorder"><?php echo $address['addressFirstName'];?> <?php echo $address['addressLastName'];?>
                        <br /><?php echo $address['addressStreet'];?>, <?php echo $address['addressPostalCode'];?> <?php echo $address['addressCity'];?></td>
                      <td>
                        <a href="<?php echo $view['router']->generate('addressChoose', array('id' => $address['id_ua']));?>" onclick="chooseAddress(this); return false;" class="chooseAddress">choisir cette adresse</a>
                      </td>
                    </tr>
<?php } ?>
                  </tbody>
                </table>
                <div id="pagerAddresses">
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'addressesList',
'routeParams' => array())); ?>
                </div>