<?php $view->extend('::frontend_base.html.php') ?>
  
<?php if($isSuccess == 1) { ?>
Message a été correctement envoyé.  
<?php } else { ?>
  <?php if($isError == 1) { ?>Une erreur s'est produite pendant l'envoi du message : <?php print_r($messageErrors); } ?>
 
<form action="<?php echo $view['router']->generate('adsQuestion', array('id' => $url['id'], 'url' => $url['url'], 'category' => $url['category'])); ?>" method="post">

  <div><label for=""><?php echo $view['form']->label($form['questionTitle'], "Titre") ?></label><?php echo $view['form']->widget($form['questionTitle']) ?></div>
  <div><label for=""><?php echo $view['form']->label($form['questionText'], "Contenu") ?></label><?php echo $view['form']->widget($form['questionText']) ?></div>
  <?php echo $view['form']->widget($form['ticket']);?><input type="submit" />
</form>
<?php } ?>