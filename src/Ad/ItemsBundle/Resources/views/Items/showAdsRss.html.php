<?php $view->extend('::rss_base.html.php') ?>
    <title><?php echo $rss['title'];?></title>
    <link><?php echo $rss['link'];?></link>
    <description><?php echo $rss['description'];?></description>
    <?php foreach($ads as $a => $ad) { ?>
    <item>
      <title><?php echo $ad['adName'];?></title>
      <link><?php echo $view['router']->generate('adsShowOne', array('url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad'], 'category' => $view['frontend']->makeUrl($ad['categoryUrl'])), true);?></link>
      <description><?php echo $ad['shortDesc'];?></description>
    </item>
    <?php } ?>