<?php $view->extend('::backend_base.html.php') ?>
<script type="text/javascript">
var links = new Array();
<?php foreach($directories as $d => $directory) { ?>
links.push("<?php echo $view['router']->generate('cacheCleanDirectory', array('directory' => $d));?>");
<?php } ?>
$(document).ready(function() {
  cleanOneDirectory(0);
});

function cleanOneDirectory(i)
{
  if(i == links.length) return false;
  $.ajax({
    type: "GET",
    url: links[i],
    dataType: "JSON",
    success: function(result)
    {
      if(result.continue == "0")
      {
        i++;
        cleanOneDirectory(i);
      }
      else
      {
        cleanOneDirectory(i);
      }
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert("Error");
    }
  });
}
</script>
<h1>Suppression du cache des offers et des annonces.</h1>
<ul>
<?php foreach($directories as $d => $directory) { ?>
  <li>
    <?php echo $d;?> <span id="state<?php echo $d;?>">en cours</span>
  </li>
<?php } ?>
</ul>