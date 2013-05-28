<div id="deleteItem" class="hidden">
  <div id="ajaxDeleteSuccess" class="hidden"><?php echo $view->render('::frontend_ok_box.html.php', array('text' => $okText));?></div>
  <div id="ajaxDeleteError"><?php echo $view->render('::frontend_ajax_error.html.php', array('text' => $errorText));?></div>
  <div id="ajaxLoaderDelete"><?php echo $view->render('::frontend_ajax_loader.html.php', array('text' => "Veuillez patienter..."));?></div>
  <?php echo $text;?>
</div>