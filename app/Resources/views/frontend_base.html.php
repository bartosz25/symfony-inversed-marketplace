<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/css/styles.css" type="text/css" />
    <link rel="stylesheet" href="/css/jquery-ui-1.8.16.custom.css" type="text/css" /> 
<?php foreach((array)$view['slots']->get("css") as $css) { ?>
    <link rel="stylesheet" href="/css/<?php echo $css;?>"  type="text/css" /> 
<?php } ?>   
    <!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="/css/ie7.css" /><![endif]-->
    <script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script> 
    <script type="text/javascript" src="/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="/js/commun.js"></script>
    <script type="text/javascript" src="/js/vars.js"></script>
<?php foreach((array)$view['slots']->get("js") as $script) { ?>
    <script type="text/javascript" src="/js/<?php echo $script;?>"></script>
<?php } ?>
<?php
require(rootDir.'cache/lists.php'); // static cache file with categories and cities list
require_once(rootDir.'cache/seo.php'); // static cache file with seo elements
$viewUser = $view->container->get('security.context')->getToken(); 
$isConnected = (bool)(isset($viewUser) && $viewUser instanceof Security\AuthenticationToken && $viewUser->isAuthenticated());
?>
  </head>
  <body>
    <div id="OVERLAY" class="hidden"></div><!-- OVERLAY-->
    <div id="SITE">
      <div id="CONTAINER">
        <div class="center">
          <div id="HEADER">
<?php echo $view->render('::frontend_header.html.php', array('categories' => (array)$categories, 'isConnected' => $isConnected, 'user' => $viewUser)); ?>
          </div><!-- HEADER-->
          <div id="CONTENT">
            <div class="leftCol">
<?php echo $view->render('::frontend_menu.html.php', array('categories' => $categories, 'slot' => $view['slots']->get("menuLeft"))); ?>
            </div><!-- leftCol-->
            <div class="content">
<?php echo $view->render('::frontend_breadcrumb.html.php', array('bread' => (array)$view['slots']->get("breadcrumb"), 'last' => (array)$view['slots']->get("lastBread"))); ?>
<?php $view['slots']->output('_content');?>
            </div><!-- content-->
            <div class="rightCol">
<?php echo $view->render('::frontend_right.html.php', array('blocks' => $view['slots']->get('blocks', array()), 'tags' => $view['slots']->get('tags', (array)$randTags)));?>
            </div><!-- rightCol-->
          </div><!-- CONTENT-->
          <div id="FOOTER">
<?php echo $view->render('::frontend_footer.html.php', array('tags' => $randTags, 'cats' => $randCats, 'offers' => $randOff, 'ads' => $randAds)); ?>
          </div><!-- FOOTER-->
        </div><!-- center-->
      </div><!-- CONTAINER-->
    </div><!-- SITE-->
    <div id="dialogErrorGen"></div><!-- dialogErrorGen-->
  </body>
</html>