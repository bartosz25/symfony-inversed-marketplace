<select name="<?php echo $select;?>" id="<?php echo $select;?>" class="selectStyle selectSmall">
<?php foreach($elements as $e => $element) { ?>
  <option value="<?php echo $element[$id];?>" <?php if($default == $element[$id]) { ?>selected="selected"<?php } ?>><?php echo $element[$name];?></option>
<?php } ?>
</select>