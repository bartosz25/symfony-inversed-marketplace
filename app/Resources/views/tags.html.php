              <div class="smallBox">
                <p class="header">Tags</p>
                  <ul class="tagsList">
<?php if(isset($tags[0]["id"])) {  
  $tags = $view['frontend']->transformTags($tags, 10);
} ?>
<?php foreach($tags as $t => $tag) { ?>
                    <li><a href="<?php echo $view['router']->generate('adsByTags', array('tag' => $tag['id_ta'], 'url' => $view['frontend']->makeUrl($tag['tagName'])));?>"><?php echo $tag['tagName'];?></a></li>
<?php } ?>
                  </ul>
              </div><!-- smallBox-->