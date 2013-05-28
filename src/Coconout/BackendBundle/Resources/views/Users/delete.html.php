<?php $view->extend('::backend_base.html.php') ?>
<script type="text/javascript">
var adLinks = new Array();
<?php foreach($ads as $a => $ad) { ?>
  adLinks.push('<?php echo $view['router']->generate('adsDelete', array('id' => (int)$ad['id_ad']));?>?ticket=<?php echo $ticket;?>');
<?php } ?>
var offerLinks = new Array();
<?php foreach($offers as $o => $offer) { ?>
  offerLinks.push('<?php echo $view['router']->generate('offersDelete', array('id' => (int)$offer['id_of']));?>?ticket=<?php echo $ticket;?>');
<?php } ?>
var toDelete = <?php echo (count($offers) + count($ads));?>;
$(document).ready(function() {
  deleteAd(0);
});
function deleteOffer(i)
{
  if(offerLinks[i]+'' != 'undefined')
  {
    $.ajax({
      type: "POST",
      url: offerLinks[i],
      data: {json: 1},
      dataType: "json",
      success: function(result)
      {
        if(result.success == 1)
        {
          $('#offer'+i).html("Offer successfully deleted");
          i++;
          toDelete--;
          deleteOffer(i);
        }
      }
    });
  }
  else if(toDelete == 0)
  {
    deleteUser();
  }
}

function deleteAd(i)
{
  if(adLinks[i]+'' != 'undefined')
  {
    $.ajax({
      type: "POST",
      url: adLinks[i]+"?json=1",
      data: {json: 1},
      dataType: "json",
      success: function(result)
      {
        if(result.success == 1)
        {
          $('#ad'+i).html("Ad successfully deleted");
          i++;
          toDelete--;
          deleteAd(i);
        }
      }
    }); 
  }
  else
  {
    deleteOffer(0);
  }
}

function deleteUser()
{
  $.ajax({
    type: "GET",
    url: "<?php echo $view['router']->generate('usersDeleteAccount', array('id' => $id));?>",
    dataType: "json",
    success: function(result)
    {
      if(result.success == 1)
      {
        alert("User successfully deleted");
        window.location.href = "<?php echo $view['router']->generate('usersList');?>"; 
      }
    }
  }); 
}
</script>
<p><b>Ads list:</b></p>
<ul>
<?php foreach($ads as $a => $ad) { ?>
<li><?php echo $ad['adName'];?> <span id="ad<?php echo $a;?>"></span></li>
<?php } ?>
</ul>
<br />
<br />
<p><b>Offers list:</b></p>
<ul>
<?php foreach($offers as $o => $offer) { ?>
<li><?php echo $offer['offerName'];?> <span id="offer<?php echo $o;?>"></span></li>
<?php } ?>
</ul>
 