<?php
namespace Others;

class Tools
{

  /**
   * Normalize price vars.
   * @access public
   * @param string $val Value to normalize.
   * @return float Normalized price.
   */
  public static function normalizePrice($var)
  {
    return (float)str_replace(",", ".", $var);
  }

  public function prepareSearch($aliases, sfWebRequest $request, $options)
  {  
    // $this->where = array('left' => array(), 'right' => array());
	// $requestTail = array();
	// $i = 0; 
    // foreach($aliases as $a => $alias) 
	// {  
	  // $parameter = $request->getParameter($a); 
	  // switch($alias) 
	  // {
	    // case 'allOrNothing': 
		  // if($parameter != "") 
		  // {     
		    // $this->where['left'][$i] = $options['db'][$a]; 
		    // $this->where['right'][$i] = $parameter; 
			// $i++;
		  // }
		// break;
		// case 'perPage':
		  // // do nothing
		// break;
		// default: 
		  // if($parameter != "") 
		  // {  
		    // if($options['type'][$a] == 'LIKE')
			// {   
			  // $parameter = "%$parameter%";
			// }  
			// $this->where['left'][$i] = $alias; 
		    // $this->where['right'][$i] = $parameter; 
			// $i++;
		  // }
		// break;
	  // }
	  // $requestTail[$r] = $a.'='.$parameter;
	  // $r++;
	// }
	// $this->requestTail = implode('&' , $requestTail);
  } 
  
  public static function makeTree($rows, $config)
  {
    $final = array();
    foreach($rows as $r => $row)
    {
      if($row[$config['children']] == 0 || $row[$config['children']] == '')
      {
        $final[$row[$config['parent']]] = array('parent' => $row, 'children' => array());
      }
      else
      {
        $final[$row[$config['children']]]['children'][] = $row; 
      }
    }
    return $final;
  }

  public static function makeShortName($string, $cutLength, $maxLength)
  {
    if(mb_strlen($string, 'UTF-8') > $maxLength)
    {
      $string = trim(mb_substr($string, 0, $cutLength)).'...';
    }
    return $string;
  }

  /**
   * Gets start id to random function.
   * @access public
   * @param int $limit Number of elements to load.
   * @param int $max Count of all elements.
   * @return int Random offset integer.
   */
  public static function getStart($limit, $max)
  {
    // if $max lower than $limit, we take all items
    if($max <= $limit)
    {
      return 0;
    }
    // otherwise, we get a random number
    $result = rand(0, $max);
    if(($max-$result) < $limit)
    {
      return Tools::getStart($limit, $max);
    }
    return $result;
  }

  // TODO : set more dimensions (for now done with only 1 dimension array)  
  public function setMap($dimensions, $array, $key) 
  { 
    $map = array(); 
    foreach($array as $a => $row)
    { 
      $keyMap = $this->setKey($key, $row);
	  $i = count($map[$keyMap]);
	  $map[$keyMap][$i] = $row;
    } 
	return $map;
  } 
  
  private function setKey($key, $element = array())
  {
    $arrKey = explode('|', $key); 
	foreach($arrKey as $key)
	{
	  $element = $element[$key]; 
	}
	return $element;
  }
  
  public static function convertSize($from, $to, $oldSize) 
  {   
    $sizes = array(
	  'b' => array('kb' => 1024, 'mb' => 1024000, 'gb' => 1024000000),
	  'kb' => array('b' => 1024, 'mb' => 1000, 'gb' => 1000000),
	  'mb' => array('b' => 1024000, 'kb' => 1000, 'gb' => 1000),
	  'gb' => array('b' => 1024000000, 'kb' => 1000000,'mb' => 1000)
	);
	$newSize = $oldSize / $sizes[$from][$to];
	
    return round($newSize);
  }
  
  public function isValidMail($mail) 
  {
    return preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', trim($mail));
  }

  public static function removeNonAlpha($name)
  {

  }

}