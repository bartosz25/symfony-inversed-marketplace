          <div class="bigPager">
            <ul>
  <?php if($pager['actual'] > 1) { ?>  
              <li class="icon"><a href="<?php echo $view['router']->generate($routeName, $routeParams);?>" title="La première page" class="prev"></a></li>
  <?php } else { ?>
              <li class="on"><a href="<?php echo $view['router']->generate($routeName, $routeParams);?>" title="La première page">1</a></li>
  <?php } ?>
  <?php foreach($pager['before'] as $b => $before) { ?>
    <?php if($before == 1) { ?>
              <li <?php if($before == $pager['actual']) {?>class="on"<?php } ?>><a href="<?php echo $view['router']->generate($routeName, $routeParams);?>"><?php echo $before;?></a></li>
    <?php } else { ?>
              <li <?php if($before == $pager['actual']) {?>class="on"<?php } ?>><a href="<?php echo $view['router']->generate($routeName, array_merge($routeParams, array('page' => $before)));?>"><?php echo $before;?></a></li>
    <?php } ?>
  <?php } ?>
  <?php if(count($pager['between']) > 0) { ?>
              <li class="more"><div class="buttonMore"><a href="#" class="showNextPages nextPages"></a></div>
                <div id="nextPages" class="hidden otherPages">
                  <ul>
    <?php foreach($pager['between'] as $be => $between) { ?>
                    <li><a href="<?php echo $view['router']->generate($routeName, array_merge($routeParams, array('page' => $between)));?>"><?php echo $between;?></a></li>
    <?php } ?>
                  </ul>
                </div><!-- otherPages -->
              </li>
  <?php } ?>
  <?php foreach($pager['after'] as $a => $after) { ?>
              <li <?php if($after == $pager['actual']) {?>class="on"<?php } ?>><a href="<?php echo $view['router']->generate($routeName,array_merge($routeParams, array('page' => $after)));?>"><?php echo $after;?></a></li>
  <?php } ?>
  <?php if($pager['last'] != $pager['actual']) { ?>
              <li class="icon"><a href="<?php echo $view['router']->generate($routeName,array_merge($routeParams, array('page' => $pager['last'])));?>" title="La dernière page" class="next"></a></li>
  <?php } ?>
            </ul>
            <form method="post" action="#"><div class="goPager">
              <label for="pageNr">Aller à la page : </label> <p><input type="text" name="pageNr" id="pageNr" /></p>
              <input type="submit" name="goToPage" class="btnOk" value="OK" onclick="javascript: pagerGoTo('<?php echo $view['router']->generate($routeName, array_merge($routeParams, array('page' => 0)));?>', '<?php echo $pager['last'];?>'); return false;" />
            </div></form><!-- goPager-->
          </div><!-- bigPager-->