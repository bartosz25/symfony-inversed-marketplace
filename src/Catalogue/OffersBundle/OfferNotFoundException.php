<?php
namespace Catalogue\OffersBundle;

/**
 * Exception shown when ad isn't found
 */
class OfferNotFoundException extends \Exception 
{

  public function __construct($message)
  {
    parent::__construct($message);
  }
  
  public function getParameters($enMan)
  {
    $offers = $enMan->getRepository('CatalogueOffersBundle:Offers')->getOffersRand(10);
    return $offers;
  }

}