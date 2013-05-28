<?php if(isset($last['url'])) { ?>
              <div class="bread">
                <ul>
                  <li class="noBackground"><a href="/">Accueil</a></li>
<?php foreach($bread as $line) { ?>
                  <li><a href="<?php echo $line['url'];?>"><?php echo $line['anchor'];?></a></li>
<?php } ?>
                  <li><h2><a href="<?php echo $last['url'];?>"><?php echo $last['anchor'];?></a></h2></li>
                </ul>
              </div><!-- bread-->
<?php } ?>