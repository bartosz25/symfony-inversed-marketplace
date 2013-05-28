<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="/css/styles.css" type="text/css" />
    <link rel="stylesheet" href="/css/jquery-ui-1.8.16.custom.css" type="text/css" />    
    <script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script> 
    <script type="text/javascript" src="/js/jquery-ui-1.8.16.custom.min.js"></script> 
  </head>
  <body>
   <div>
      <div style="float:left; margin-right:30px;"><ul>
        <li><b>Ads</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('adsList', array());?>">list</a></li>
            <li><a href="<?php echo $view['router']->generate('adsListNew', array());?>">not activated yet</a></li>
            <li><a href="<?php echo $view['router']->generate('questionsList', array());?>">questions list</a></li>
            <li><a href="<?php echo $view['router']->generate('repliesList', array());?>">replies list</a></li>
          </ul>
        </li>
        <li><b>Offers</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('offersList', array());?>">list</a></li>
            <li><a href="<?php echo $view['router']->generate('offersImgList', array());?>">images</a></li>
          </ul>
        </li>
        <li><b>Catalogues</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('cataloguesList', array());?>">list</a></li>
          </ul>
        </li>
        <li><b>Users</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('usersList', array());?>">list</a></li>
          </ul>
        </li>
        <li><b>Cache</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('cacheGenerate', array());?>">generate general</a></li>
            <li><a href="<?php echo $view['router']->generate('cacheGenerateSeo', array());?>">generate SEO</a></li>
            <li><a href="<?php echo $view['router']->generate('cacheClean', array());?>">clear site cache</a></li>
          </ul>
        </li>
        <li><b>Tags</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('tagsList', array());?>">list</a></li>
            <li><a href="<?php echo $view['router']->generate('tagsAdd', array());?>">add</a></li>
          </ul>
        </li>
        <li><b>Tests</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('accessTests', array());?>">access</a></li>
          </ul>
        </li>
        <li><b>Newsletters</b>
          <ul>
            <li><a href="<?php echo $view['router']->generate('newslettersList', array());?>?make=true">list</a></li>
            <li><a href="<?php echo $view['router']->generate('newslettersSend', array());?>?make=true">send</a></li>
          </ul>
        </li>
      </ul></div>
      <div style="float:left;"><?php $view['slots']->output('_content');?></div>
   </div>
   <hr />
  </body>
</html>