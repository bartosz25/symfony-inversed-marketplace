<?php
namespace Ad\ItemsBundle;

/**
 * Exception shown when ad isn't found
 */
class AdNotFoundException extends \Exception 
{

  public function __construct($message)
  {
    parent::__construct($message);
  }
  
  public function getParameters($enMan)
  {
    $ads = $enMan->getRepository('AdItemsBundle:Ads')->getAdsRand(10);
    return $ads;
  }

}