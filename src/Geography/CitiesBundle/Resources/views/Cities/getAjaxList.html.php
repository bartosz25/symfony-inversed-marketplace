<?php foreach($cities as $city) { ?>
<option value="<?php echo $city['id_ci'];?>" region="<?php echo $city['id_re'];?>"><?php echo $city['cityName'];?></option>
<?php } ?>