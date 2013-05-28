<?php $view->extend('::frontend_base.html.php') ?>

<?php echo $view->render('::frontend_content_header.html.php', array('title' => 'Mon compte')); ?>
              <div class="textContent">
                <p>Votre espace vous permet de gérer les annonces, les offres ainsi que d'autres parties de votre compte.</p>
                <p>Voici la liste complète des opérations que vous pouvez effectuer :</p>
                <ul class="nml">
<?php require_once(rootDir.'/cache/options.php');?>
<?php foreach($options as $option) { ?>
                  <li><a href="<?php echo $option['url'];?>" class="blue"><?php echo $option['name'];?></a></li>
<?php } ?>
                </ul>
              </div><!-- textContent-->
<?php $view['slots']->set('breadcrumb', array()); ?>
<?php $view['slots']->set('lastBread', array('url' => $view['router']->generate('myAccount', array()), 'anchor' => 'Mon compte')); ?>
<?php $view['slots']->set('menuLeft', array('type' => 'all')); ?>
<?php $view['slots']->set('blocks', array('tags', 'ads_right'));?>
<?php $view['slots']->set('js', array());?>