<?php
namespace Security;

class FilterXss
{
  /**
   * Characters considered by dangerous.
   * @access protected
   * @var array
   */
  protected $_dangerEnt = array('&' , '<' , '>' , '"' , "'", '/'); 
  /**
   * HTML entities corresponded to $_dangetEnt.
   * @access protected
   * @var array
   */
  protected $_secureEnt = array('&amp;' , '&lt;' , '&gt;', '&quot;', '&#x27;' , '&#x2F;');
  /**
   * Accepted and no filtered tags.
   * @access protected
   * @var array
   */
  protected $_whitelist = array();
  /**
   * List of RegEx patterns accepted on attributs.
   * @access protected
   * @var array
   */
  protected $_attrPatterns = array();
  /**
   * Filter mode.
   * @access protected
   * @var string
   */
  protected $_mode = "";
  /**
   * HTML attributes which have RegEx URL pattern.
   * @access protected
   * @var array
   */
  protected $_urlPatterns = array("href", "src");
  /**
   * Accepted tags RegEx pattern.
   * @access private
   * @var string
   */
  private $acceptedTagsPattern = "";
  /**
   * Strict mode for filter : strip all tags.
   */
  const STRICT_MODE = "STRICT";
  /**
   * Light mode for filter : accept some defined tags.
   */
  const LIGHT_MODE = "LIGHT";

  /**
   * Public constructor.
   * @access public
   * @param string $mode Filter mode : STRICT or LIGHT.
   * @param array $acceptedEnt Accepted tags.
   * @return void
   */
  public function __construct($mode, $acceptedEnt = array()) 
  {
    if($mode != self::STRICT_MODE && $mode != self::LIGHT_MODE)
    {
      throw new \Exception("Filter mode {$this->_mode} is unknown");
    }
    $this->_mode = $mode;
    $this->_whitelist = $acceptedEnt;
  }

  /**
   * Filter string or array input.
   * @access public
   * @param string|array $value Element to filter.
   * @return string|array Filtered element.
   */
  public function doFilterXss($value)
  {
    // first, replace all tags not defined as accepted
    $this->constructAcceptedTags();
    if(!is_array($value) && $this->_mode == self::STRICT_MODE) 
    {
      return $this->doStrictFilter($value);
    }
    elseif(!is_array($value) && $this->_mode == self::LIGHT_MODE) 
    {
      return $this->doLightFilter($value);
    }
    else 
    {
      return $this->filterArray($value);
    }
  }

  /**
   * Filter array.
   * @access private
   * @param array $array Array to filter.
   * @return array|string Filtered value.
   */
  private function filterArray($array)
  {
    foreach($array as $k => $value)
    {
      if(!is_array($value) && $this->_mode == self::STRICT_MODE) 
      {
        $values[$k] = $this->doStrictFilter($value);
      }
      elseif(!is_array($value) && $this->_mode == self::LIGHT_MODE) 
      {
        $values[$k] = $this->doLightFilter($value);
      }
      else 
      {
        $values[$k] = $this->filterArray($value);
      }
    }
    return $values;
  }

  /**
   * Normalize input string.
   * @access private
   * @param string $string String to normalize.
   * @return string Normalized string.
   */
  private function normalizeInput($string)
  {
    return urldecode(html_entity_decode((string)$string));
  }

  /**
   * Filter all tags from string.
   * @access private
   * @param string $string String to filter.
   * @return string Filtered string.
   */
  private function doStrictFilter($string)
  {
    // avoid double encoding exploits
    $string = $this->normalizeInput($string);
    return str_replace($this->_dangerEnt, $this->_secureEnt, strip_tags((string)$string));
  }

  /**
   * Filter tags from string (ignore accepted ones).
   * @access private
   * @param string $string String to filter.
   * @return string Filtered string.
   */
  private function doLightFilter($string)
  {
      // avoid double encoding exploits
      $string = $this->normalizeInput($string);
      $string = preg_replace("#(?".$this->acceptedTagsPattern."</?[a-zA-Z][a-zA-Z0-9]*[^<>]*>)#i", "", (string)$string);
      // secondly, get accepted tags and check theirs attributs : we consider the rest of tags as valable
      preg_match_all('#([^<]*)(<?[^>]*>?)#i', (string)$string, $tags);
      $finalString = "";
      foreach($tags[1] as $t => $text)
      {
        $finalString .= $text.$this->filterTag($tags[2][$t]);
      }
      return $finalString;
  }

  /**
   * Filter tag. For attributes we accept only alphanumerical characters or e-mail and url valid sequence.
   * @access private
   * @param string $tag Tag to filter.
   * @return string Filtered tag.
   */
  private function filterTag($tag)
  {
    // decompose tag as : tag name, attributes list
    // 2 : tag name
    // 3 : tag's attributes
    preg_match_all("#<(\/|)([a-z]+)(\/|(.*))>#i", $tag, $parts);
    $tagResult = "";
    if(isset($parts[2][0])) $tagResult = $parts[2][0];
    if(!in_array($tagResult, $this->_whitelist)) return "";
    // normalize attributes
    // 1 : attribute name
    // 3 : attribute value
    preg_match_all('#(\w+)\s*=\s*(?:(")(.*?)"|(\')(.*?)\')(\s\/|)#s', $parts[3][0], $attrs);
    $attributes = "";
    foreach($attrs[1] as $a => $attribute)
    {
      $attribute = strtolower($attribute);
      if(!isset($attrs[3][$a])) continue;
      $attributeValue = $attrs[3][$a];
      if(ctype_alnum($attribute) && (ctype_alnum($attributeValue) || (in_array($attribute, $this->_urlPatterns) && $this->isValidUrlAttr($attributeValue))))
      {
        $attributes .= ' '.$attribute.'="'.$attributeValue.'"';
      }
    }
    $closed = "";
    if(isset($attrs[6][1])) $closed = $attrs[6][1];
    return "<".$parts[1][0].$tagResult."".$attributes."".$closed.">";
  }

  /**
   * Checks if value is valid for href or src attribute.
   * @access private
   * @param string $value Value to check.
   * @return bool True if valid, false otherwise.
   */
  private function isValidUrlAttr($value)
  {
    return (bool)(preg_match("#(mailto|http|ftp|https)(://|:)(.*)#", $value));
  }

  /**
   * Constructs accepted tags RegEx pattern.
   * @access private
   * @return string Pattern with accepted tags.
   */
  private function constructAcceptedTags()
  {
     $result = "";
     foreach($this->_whitelist as $a => $accepted)
     {
       $result .= "(?!<(\/|)".$accepted."(.*|)>)";
     }
     $this->acceptedTagsPattern = $result;
  }

  /**
   * Setter and getter for $_whitelist property.
   */
  public function setWhitelist($list)
  {
    $this->_whitelist = $list;
  }
  public function getWhitelist()
  {
    return $this->_whitelist;
  }

  /**
   * Setter and getter for $_attrPatterns property.
   */
  public function setAttrsPatterns($patterns)
  {
    $this->_attrPatterns = $patterns;
  }
  public function getAttrsPatterns()
  {
    return $this->_attrPatterns;
  }
}
?>