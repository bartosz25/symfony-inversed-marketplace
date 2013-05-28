<li id="temporary<?php echo $id;?>" class="imageLine">
  <img src="<?php echo $dir;?>/small_<?php echo $file;?>" alt="" />
  <a href="<?php echo $view['router']->generate($route, array('id' => $id));?>?ticket=<?php echo $ticket;?>" class="floatLeft deleteItem" onclick="deleteImage('<?php echo $id;?>');return false;">supprimer</a>
</li>