<?php
namespace Order\OrdersBundle\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Order\OrdersBundle\Entity\Delivery;
use Order\OrdersBundle\Entity\Orders;

class OrdersHelper extends Helper
{

  /** 
   * Get carrier name.
   * @param int $carrier Carrier's id.
   * @return String Carrier's name.
   */
  public function getCarrier($carrier)
  {
    if($carrier > 0)
    {
      $carrier = Delivery::getCarrier($carrier);
      return $carrier['name'];
    }
    return '';
  }

  /**
   * Checks if the order state is the last.
   * @access public
   * @param int $state Order state.
   * @return bool True if the last, false otherwise.
   */
  public function isTheLast($state)
  {
    $ordEnt = new Orders;
    $ordEnt->setOrderState($state);
    return $ordEnt->isTheLastState();
  }

  /**
   * Constructs steps path for order menu.
   * @access public
   * @param int $actualStep Actual step.
   * @param array $steps Order steps.
   * @return array Array with step and li's class.
   */
  public function constructSteps($actualStep, $steps)
  {
    $result = "";
    $last = count($steps)-1;
    $i = 0;
    $className = "on";
    foreach($steps as $s => $step)
    {
      if($i == $last) $className = "$className last";
      $result .= '<li class="'.$className.'"><span>'.$step.'</span></li>';
      if(strpos($s, $actualStep.";") !== false) $className = "";
      $i++;
    }
    return $result;
  }

    public function getName()
    {
        return 'orders';
    }
}