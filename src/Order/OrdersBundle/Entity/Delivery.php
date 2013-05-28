<?php
namespace Order\OrdersBundle\Entity;
  

/**
 * Simple class with delivery methods, carriers.
 */
class Delivery
{
  protected static $deliveryTypes = array(1 => 'livraison à domicile', 2 => 'livraison en point relais/bureau de poste');
  protected static $carriers = array(
  1 => array('name' => 'La Poste', 'tracking' => ''),
  2 => array('name' => 'UPS', 'tracking' => ''),
  3 => array('name' => 'Relais Colis', 'tracking' => ''), 
  4 => array('name' => 'Mondial Relay', 'tracking' => ''),
  5 => array('name' => 'FedEx', 'tracking' => ''), 
  6 => array('name' => 'DHL', 'tracking' => ''),
  7 => array('name' => 'TNT', 'tracking' => ''), 
  8 => array('name' => 'autre', 'tracking' => ''),
  9 => array('name' => 'sur place', 'tracking' => ''),
  );

  /**
   * Gets delivery types array.
   * @access public
   * @param boolean $onlyKeys If true, returns only keys from array $deliveryTypes
   * @return array
   */
  public static function getDeliveryTypes($onlyKeys = false)
  {
    if(!$onlyKeys)
    {
      return self::$deliveryTypes;
    }
    $types = array();
    foreach(self::$deliveryTypes as $d => $delivery)
    {
      $types[$d] = $d;
    }
    return $types;
  }

  /**
   * Gets delivery types labels.
   * @access public
   * @param array $methods Array with checked methods.
   * @return array
   */
  public static function getLabels($methods)
  {
    $labels = array();
    foreach($methods as $method)
    {
      $labels[] = self::$deliveryTypes[$method];
    }
    return $labels;
  }

  /**
   * Gets carriers array.
   * @access public
   * @param boolean $onlyKeys If true, returns only keys from array $carriers
   * @return array
   */
  public static function getCarriers($onlyKeys = false)
  {
    $types = array();
    if(!$onlyKeys)
    {
      foreach(self::$carriers as $c => $carrier)
      {
        $types[$c] = $carrier['name'];
      }
      return $types;
    }
    foreach(self::$carriers as $c => $carrier)
    {
      $types[$c] = $c;
    }
    return $types;
  }

  /**
   * Gets carrier.
   * @access public
   * @param int $carrier Carrier's id.
   * @return array
   */
  public static function getCarrier($carrier)
  {
    return self::$carriers[$carrier];
  }

}