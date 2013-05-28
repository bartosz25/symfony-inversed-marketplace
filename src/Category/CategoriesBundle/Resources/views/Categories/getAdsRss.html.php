<?php $view->extend('::rss_base.html.php') ?>
    <title>Annonces de la catégorie <?php echo $category[0]['categoryName'];?></title>
    <link><?php echo $view['router']->generate('adsByCategory', array('category' => $category[0]['categoryUrl']), true);?></link>
    <description>Retrouvez les dernières annonces de la catégorie <?php echo $category[0]['categoryName'];?>. </description>
    <?php foreach($ads as $a => $ad) { ?>
    <item>
      <title><?php echo $ad['adName'];?></title>
      <link><?php echo $view['router']->generate('adsShowOne', array('url' => $view['frontend']->makeUrl($ad['adName']), 'id' => $ad['id_ad'], 'category' => $category[0]['categoryUrl']), true);?></link>
      <description><?php echo $ad['shortDesc'];?></description>
    </item>
    <?php } ?>