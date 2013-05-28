<?php if($slot['type'] == 'all') { ?>
              <ul id="mainMenu" class="menu">
  <?php $allCat = count($categories); $done = 1; foreach($categories as $p => $parent) { $allChild = count($parent['children'])-1; ?>
                <li <?php if($allCat == $done) {?>class="noBorder"<?php } ?>><a href="#" rel="#menuCat<?php echo $parent['parentId'];?>" class="mainCategory"><?php echo $parent['parentName'];?></a>
                  <ul id="menuCat<?php echo $parent['parentId'];?>" class="submenu hidden">
                    <li class="separator"><p></p></li>
    <?php foreach($parent['children'] as $c => $child) { ?>
                    <li <?php if($c == 0) { ?>class="first"<?php } elseif($c == $allChild) {?>class="last" <?php } ?>><a href="<?php echo $view['router']->generate('adsByCategory', array('category' => $child['url']));?>"><?php echo $child['name'];?></a></li>
    <?php } ?>
                  </ul>
                </li>
  <?php $done++; } ?>
              </ul>
<?php } else { ?>

<?php } ?>
<?php echo $view->render('::frontend_offers_box.html.php', array()); ?>
<?php echo $view->render('::ads_left.html.php', array()); ?>
