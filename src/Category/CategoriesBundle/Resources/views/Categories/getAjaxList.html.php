<?php for($s=0; $s < count($fields); $s++) { ?>
  <div class="formLine twoBoxes">
    <div class="formBox noMarginLeft">
    <?php if(isset($fields[$s])) echo $view['form']->label($form[$fields[$s]['codeName']], $fields[$s]['labelForm']);?>
    </div>
    <div class="formBox">
    <?php if(isset($fields[$s+1])) echo $view['form']->label($form[$fields[$s+1]['codeName']], $fields[$s+1]['labelForm']);?>
    </div>
  </div>
  <div class="formLine fieldLine twoBoxes">
    <div <?php if(isset($fields[$s])) echo 'id="form'.$fields[$s]['codeName'].'Container"'; ?> class="formBox noMarginLeft  <?php echo $view['frontendForm']->setErrorClass($formErrors, $fields[$s]['codeName'], 'boxError');?>">
    <?php if(isset($fields[$s])) echo $view['form']->widget($form[$fields[$s]['codeName']], array('attr' => array('theme' => ':', 'id' => 'AddOffer_'.$fields[$s]['codeName'], 'class' => 'genElement text', 'onblur' => 'javascript: hideErrorMessages("'.$fields[$s]['codeName'].'", false);')));?>
    <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-'.$fields[$s]['codeName'].'" class="errors">%s</p>', $formErrors, $fields[$s]['codeName']);?>
    <!--<p id="error-<?php if(isset($fields[$s])) echo  $fields[$s]['codeName'].$bidId; ?>" class="errors hidden"></p>-->
    </div>
    <div <?php if(isset($fields[$s+1])) echo 'id="form'.$fields[$s+1]['codeName'].'Container"'; ?> class="formBox  <?php echo $view['frontendForm']->setErrorClass($formErrors, $fields[$s+1]['codeName'], 'boxError');?>">
    <?php if(isset($fields[$s+1])) echo $view['form']->widget($form[$fields[$s+1]['codeName']], array('attr' => array('id' => 'AddOffer_'.$fields[$s+1]['codeName'] , 'theme' => ':', 'class' => 'genElement text', 'onblur' => 'javascript: hideErrorMessages("'.$fields[$s+1]['codeName'].'", false);')));?>
    <?php echo $view['frontendForm']->displayErrorBlock('<p id="error-'.$fields[$s+1]['codeName'].'" class="errors">%s</p>', $formErrors, $fields[$s+1]['codeName']);?>
    <!--<p id="error-<?php if(isset($fields[$s+1])) echo  $fields[$s+1]['codeName'].$bidId; ?>" class="errors hidden"></p>-->
    </div>
  </div>
<?php $s++; } ?>