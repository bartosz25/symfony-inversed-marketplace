<?php $view->extend('::rss_base.html.php') ?>
    <title>Offres de la catégorie <?php echo $category[0]['categoryName'];?></title>
    <link><?php echo $view['router']->generate('offersByCategory', array('category' => $category[0]['categoryUrl']), true);?></link>
    <description>Retrouvez les dernières offres de la catégorie <?php echo $category[0]['categoryName'];?>. </description>
    <?php foreach($offers as $o => $offer) { ?>
    <item>
      <title><?php echo $offer['offerName'];?></title>
      <link><?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $view['frontend']->makeUrl($offer['offerName']), 'offerId' => $offer['id_of']), true);?></link>
      <description><?php echo $offer['shortDesc'];?></description>
    </item>
    <?php } ?>