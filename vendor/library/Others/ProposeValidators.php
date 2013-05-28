<?php
namespace Others;

class ProposeValidators
{

  private $criteria, $userValues;

  private $labels = array('geo' => "localisation", 'opinion' => "minimum d'appréciation", 
  'seller' => "type du vendeur", 'object' => "l'état de l'objet", 'price' => "le prix");

  private $validators = array('geo' => "equal", 'opinion' => "equalOrBigger", 
  'seller' => "equal", 'object' => "equal", 'price' => "between"); 

  private $labelOptions = array(
    'seller' => array('accepted' => array(), 'given' => array())
  );

  /**
   * Array with not valid fields.
   * @access public
   * @var array
   */
  public $notValid;

  /**
   * Class constructor.
   * @access public
   * @param array $criteria Criteria list.
   * @param array $userValues User's values.
   * @return void
   */
  public function __construct($criteria, $userValues)
  {
    $this->criteria = $criteria;
    $this->userValues = $userValues;
  }

  /**
   * Validates $criteria by internal criteria's validators.
   * @access public
   * @return boolean True if all criteria are valid, else otherwise
   */
  public function validate()
  { //return true;
    foreach($this->criteria as $c => $criterion)
    {
      $validator = $this->validators[$c];
      if($criterion > 0 && !$this->$validator($criterion, $this->userValues[$c]))
      {
        $this->notValid[] = $this->labels[$c].' (attendu : '.$this->getLabelOptions($c, $criterion, 'accepted').', vous : '.$this->getLabelOptions($c, $this->userValues[$c], 'given').')';
      }
    }
    return (bool)(count($this->notValid) <= 0);
  }

  private function getLabelOptions($type, $value, $who)
  {
    return $this->labelOptions[$type][$who];
  }

  public function setLabelOptions($type, $who, $value)
  {
    $this->labelOptions[$type][$who] = $value['alias'];
  }

  private function between($criterion, $userValue)
  {
    return (bool)($userValue >= $criterion['from'] && $userValue <= $criterion['to']);
  }
 
  private function equalOrBigger($a, $b)
  {
    return (bool)($b >= $a);
  }

  private function equal($a, $b)
  {
    return (bool)($a == $b);
  }

}