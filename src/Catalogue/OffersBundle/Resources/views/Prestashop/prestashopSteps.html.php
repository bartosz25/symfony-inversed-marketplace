<p class="bolder">Etapes de synchronisation : </p>
<ul class="prestaSteps">
  <li <?php if($steps[1]) {?>class="on"<?php } ?>><?php if($steps[1]) {?><a href="<?php echo $view['router']->generate('synchronizePrestashop'); ?>"><?php } ?>Configuration du compte<?php if($steps[1]) {?></a><?php } ?></li>
  <li <?php if($steps[2]) {?>class="on"<?php } ?>><?php if($steps[2]) {?><a href="<?php echo $view['router']->generate('synchronizeMapPrestashop'); ?>"><?php } ?>Configuration des catégories<?php if($steps[2]) {?></a><?php } ?></li>
  <li class="last <?php if($steps[3]) {?>on<?php } ?>"><?php if($steps[3]) {?><a href="<?php echo $view['router']->generate('synchronizeCatPrestashop'); ?>"><?php } ?>Ajout des produits<?php if($steps[3]) {?></a><?php } ?></li>
</ul>