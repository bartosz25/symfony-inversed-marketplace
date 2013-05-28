<?php $view->extend('::rss_base.html.php') ?>
    <title><?php echo $rss['title'];?></title>
    <link><?php echo $rss['link'];?></link>
    <description><?php echo $rss['description'];?></description>
    <?php foreach($offers as $o => $offer) { ?>
    <item>
      <title><?php echo $offer['offerName'];?></title>
      <link><?php echo $view['router']->generate('offerShow', array('catalogue' => $view['frontend']->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $view['frontend']->makeUrl($offer['offerName']), 'offerId' => $offer['id_of']), true);?></link>
      <description><?php echo $offer['shortDesc'];?></description>
    </item>
    <?php } ?>