<?php
namespace Order\OrdersBundle\Entity;
  

/**
 * Simple class with tax values.
 */
class Tax
{
  protected static $taxes = array(
  1 => array('name' => 'TVA 19,6%', 'value' => 19.6),
  2 => array('name' => '5,5%', 'value' => 5.5)
  );

  /**
   * Gets taxes to select values (key => name)
   * @access public
   * @param boolean $onlyKeys If true, returns only keys from array $taxes
   * @return array
   */
  public static function getTaxesToSelect($onlyKeys = false)
  {
    $types = array();
    if(!$onlyKeys)
    {
      foreach(self::$taxes as $t => $tax)
      {
        $types[$t] = $tax['name'];
      }
      return $types;
    }
    foreach(self::$taxes as $t => $tax)
    {
      $types[$t] = $t;
    }
    return $types;
  }

  /**
   * Gets tax value.
   * @access public
   * @param array $methods Array with checked methods.
   * @return array
   */
  public static function getTaxValue($tax)
  {
    return self::$taxes[$tax]['value'];
  }

  /**
   * Retreives tax id by passed $value.
   * @access public
   * @param float $value Value.
   * @return int Tax id.
   */
  public static function getTaxByValue($value)
  {
    foreach(self::$taxes as $t => $tax)
    {
      if($tax['value'] == $value)
	  {
        return $t;
      }
    }
  }

  /**
   * Calculates price net of tax.
   * @access public
   * @param float $price Price to calculate.
   * @param float $tax Tax to calculate.
   * @return float Final price.
   */
  public static function getNetPrice($price, $tax)
  {
    $taxPrice = ((float)$price*(float)$tax)/100;
    return (float)$price - (float)$taxPrice;
  }

  /**
   * Gets default tax.
   * @access public
   * @return int Key for default tax.
   */
  public static function getDefaultTax()
  {
    return 1;
  }
}