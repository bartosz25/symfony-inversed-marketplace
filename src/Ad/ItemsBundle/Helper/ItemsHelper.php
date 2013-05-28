<?php
 
namespace Ad\ItemsBundle\Helper;
 
use Symfony\Component\Templating\Helper\Helper;
use Ad\ItemsBundle\Entity\Ads;

class ItemsHelper extends Helper
{

  /**
   * Gets localization label.
   * @access public
   * @param array $params List of parameters.
   * @return string Localization label (city, region, country)
   */
  public function getLocalizationLabel($params)
  {
    switch($params['adSellerGeo'])
    {
      case 0:
        return 'partout';
      break;
      case 1:
        return $params['countryName'];
      break;
      case 2:
        return $params['regionName'];
      break;
      case 3:
        return $params['cityName'];
      break;
    }
  }

  /**
   * Gets payments labels and create a string to display.
   * @access public
   * @param array $payments Payments array.
   * @param array $labels Labels array.
   * @return string String to display.
   */
  public function getPaymentLabels($payments, $labels)
  {
    $methods = array();
    foreach($payments as $payment)
    {
      $methods[] = $labels[$payment['payments_id_pa']];
    }
    return implode(',', $methods);
  }

  /**
   * Makes weeks validity for ad.
   * @access public
   * @return array List with weeks values.
   */
  public function getWeeks()
  {
    $weeks = array();
    $adsEnt = new Ads;
    foreach($adsEnt->getValidityTime() as $t => $time)
    {
      $weeks[$t] = time() + (7*24*60*60*$t);
    }
    return $weeks;
  }

  /**
   * Makes new "Our users have already economized XX€" block
   * @access public
   * @param float $amount Economized amount.
   * @param Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $render Engine used to render a .html.php template.
   * @return void
   */
  public function makeAmountBlock($amount, $render)
  {
    $maxLength = 7; // 7 numbers accepted on counter.html.php file
    $amount = ceil($amount);
    $final = array(0, 0, 0, 0, 0, 0, 0);
    $a = str_split($amount);
    $difference = 7 - ($length = (count($a)));
    for($i = 0; $i < $length; $i++)
    {
      $final[$i+$difference] = $a[$i];
    }
    file_put_contents(rootDir."/app/Resources/views/counter.html.php", $render->parseTemplate("::counter_template.html.php", array("amounts" => $final)));
  }

  public function getName()
  {
    return 'items';
  }

}