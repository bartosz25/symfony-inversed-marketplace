<?php if($partial == 0) { ?>
<?php $view->extend('::frontend_base.html.php') ?>
ddddddeee
<?php if($error == 1) { ?>d
<p><b>Cette offre a déjà été proposée à l'annonce choisie. Attendez la réaction de l'auteur de l'offre ou 
envoyez-lui un message privé pour connaître sa réaction précise.</b></p>
<a href="<?php echo $view['router']->generate('offerProposeBuy', array('offer' => $id));?>">Choisir autre annonce.</a>
<?php } else { ?>
<script type="text/javascript">
$(document).ready(function() {
  // Check if choosen offer corresponds to ad criteria
  $('.checkChoosen').live('click', function() {
    $('#loaderCheck').show();
    $.ajax({
      type: "POST",
      url: "<?php echo $view['router']->generate('ajaxCheckAdOffer', array('offer' => $id)); ?>",
      data: {ad: $(this).val()},
      dataType: "JSON",
      success: function(result)
      {
        $('#compareResults span.icon').hide();
        $('#result'+result.result).show();
        $('#criteriaMessage').html(result.message);
      }
    }); 
  });
  $('.pager ul li a').live('click', function() {
    $.ajax({
      type: "GET",
      url: $(this).attr('href'),
      data: {partial: 1},
      success: function(result)
      {
        $('#adsList').html(result);
      }
    });    
    return false;    
  });
});
</script>

<?php if($csrfError == 1) { ?>
Votre session a expiré. Veuillez réessayer.
<?php } ?>
<div id="compareResults"><span id="loaderCheck" class="icon hidden">chargement...</span><span id="result0" class="icon hidden">ERROR</span>
<span id="result1" class="icon hidden">OK</span>
<span id="result2" class="icon hidden">WARNING</span>
<div id="criteriaMessage"></div></div>

<div id="adsList">
<?php } ?>
<?php } ?>

<form method="post" action="<?php echo $view['router']->generate('offerProposeBuy', array('offer' => $id));?>">
<?php foreach($ads as $a => $ad) { ?>
<p><input type="radio" name="adChoosen" value="<?php echo $ad['id_ad'];?>" id="ad_<?php echo $ad['id_ad'];?>" class="checkChoosen" /><label for="ad_<?php echo $ad['id_ad'];?>"><?php echo $ad['adName'];?></label></p>
<?php } ?> 
<p><input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket;?>" /><input type="submit" /></p>
</form>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'offerProposeBuy',
'routeParams' => array('offer' => $id))); ?>

<?php if($partial == 0) { ?>
</div>
<?php }?>