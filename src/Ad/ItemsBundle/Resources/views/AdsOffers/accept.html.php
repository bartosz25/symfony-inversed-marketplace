<?php $view->extend('::frontend_base.html.php') ?>
<form method="post" action="<?php echo $view['router']->generate('adAcceptOffer', array('offer' => $offer, 'ad' => $ad));?>">
  <div><label>Type d'acceptation</label> <select name="type">
    <option>-- sélectionnez le type --</option>
    <option value="final">définitive (annonce sera finie maintenant)</option>
    <option value="normal">normale (annonce sera finie le <?php echo $data['dateEnd'];?>)</option>
  </select></div>
  <div><input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket;?>" /><input type="submit" /></div>
</form>