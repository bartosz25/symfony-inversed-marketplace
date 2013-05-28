<?php $view->extend('::backend_base.html.php') ?>
<script type="text/javascript">
subscribers = new Array();
<?php foreach($subscribers as $s => $subscriber) { ?>
subscribers.push(<?php echo $subscriber['id_us'];?>);
<?php } ?>
$(document).ready(function() {
  sendMail(0);
});

function sendMail(i)
{
  var userId = subscribers[i];
  if(userId+'' != 'undefined')
  {
    $.ajax({
      type: "POST",
      url: $('#link'+userId).attr('href'),
      dataType: "json",
      success: function(ret)
      {
        var textError = "";
        if(ret.result == 1)
        {
          textError = "Envoyé";
        }
        else
        {
          textError = "Une erreur";
        }
        $('#state'+userId).html(textError);
        i++;
        sendMail(i);
      }
    });
  }
}
</script>
<h1>Newsletters list.</h1>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>User login</th>
      <th>State</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($subscribers as $s => $subscriber) { ?>
    <tr>
    <td><?php echo $subscriber['id_us'];?></td>
    <td><?php echo $subscriber['login'];?>
      <a href="<?php echo $view['router']->generate('newslettersSendMail', array('user' => $subscriber['id_us'])); ?>?ticket=<?php echo $ticket;?>" id="link<?php echo $subscriber['id_us'];?>"></a>
    </td>
    <td id="state<?php echo $subscriber['id_us'];?>">pas traité</td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $view->render('FrontendFrontBundle:Frontend:pager.html.php', array('pager' => $pager, 'routeName' => 'newslettersSend',
'routeParams' => array())); ?>