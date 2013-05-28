<?php if($response['isError'] == 1) { ?>
Une erreur s'est produite :
<?php } ?>
<?php echo $response['message'];?>