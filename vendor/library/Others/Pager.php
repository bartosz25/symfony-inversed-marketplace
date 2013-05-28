<?php

/**
 * Pager class which facilitates your work with view part on MVC architecture.
 * 
 * @author : Bartosz KONIECZNY (http://www.bart-konieczny.com)
 * @version : 1.0 
 */
 
namespace Others;

class Pager {

  /**
   * Private variable with pager options.
   * @var array
   */
  private $options = array();

  /**
   * Private variable with the first element of getAfter() method.
   * @var array
   */
  private $firstAfter = 0;

  /**
   * Private variable with the last element of getBefore() method.
   * @var array
   */
  private $lastBefore = 0;

  /**
   * Class constructor. It initializes the options.
   * @access public
   * @param array $options Options list.
   * @return void
   */
  public function __construct($options = array())  
  {  
    if(!isset($options['between'])) $options['between'] = 6;
	$this->options = $options;
  }
  
  /**
   * Prepares the Pager for a view template. 
   * @access public
   * @return array List of parameters which may be used in the view part.
   */
  public function setPages()
  {
    return array('last' => $this->setLastPage(),
	'after' => $this->getAfter(),
	'before' => $this->getBefore(),
	'actual' => $this->options['page'],
	'next' => $this->setNext(),
	'previous' => $this->setPrevious(),
    'between' => $this->getBetween()
	);
  }
  
  /**
   * Gets number of pages before the actual page.
   * @access private
   * @return array List of pages.
   */
  private function getBefore()
  {
    if($this->options['page'] > 1) 
	{
      $result = array();
      $limit = $this->options['page']-$this->options['before'];
      if($limit < 0) $limit = 0;
      $page = $this->options['page'];
      $this->lastBefore = $page;
      while($page > 0 && $page > $limit) 
	  { 
        $result[$page] = $page;
        $page--;
      }
    return array_reverse($result);
    }
    else 
	{
      return array();
    }
  }
  
  /**
   * Gets number of pages after the actual page.
   * @access public
   * @return array List of pages.
   */
  private function getAfter()
  {  
    if($this->options['page'] < $this->last) 
	{
	  $result = array(); 
      $limit = $this->options['page'] + $this->options['after'] + 1;
      $page = $this->options['page']+1;   
      $this->firstAfter = $page; 
      while(($page < $limit && ($page <= $this->last))) 
	  { 
        $result[$page] = $page; 
        $page++;
      } 
      return $result;
    }
    else 
	{
      return array();
   }
  }
  
  /**
   * Sets the next page number. May be used for the anchors like "next", "next page" etc.
   * @access private
   * @return int Returns integer when the next page exists.
   */
  private function setNext()
  {
    if($this->options['page'] < $this->last-1)
	{
	  return $this->options['page'] + 1;
	}
  }

  /**
   * Sets the previous page number. May be used for the anchors like "previous", "previous page" etc.
   * @access private
   * @return int Returns integer when the previous page exists.
   */

  private function setPrevious()
  {
    if($this->options['page'] > 1)
	{
	  return $this->options['page'] - 1;
	}
  }
  
  /**
   * Gets number of pages which are between the last element of getBefore() and the first one from 
   * getAfter().
   * @access private
   * @return array List with numbers between the both elements.
   */

  private function getBetween()
  {
    $betweenPages = array();
    $difference = @round(($this->firstAfter - $this->lastBefore)/$this->options['between']);
    $espace = $this->firstAfter - $this->lastBefore;
    if($difference > 0 && $this->lastBefore > 0 && $espace > 1)
    {
      if($difference == 1) $difference++;
      for($i = $this->lastBefore; $i < round($this->firstAfter/$difference); $i++)
      {
        $number = $i * $difference;
        $betweenPages[$number] = $number;
      }
    }
    elseif($difference == 0 && $this->lastBefore > 0 && $espace > 1)
    {
      for($i = $this->lastBefore; $i < $this->firstAfter; $i++)
      {
        $betweenPages[$i] = $i;
      }
    }
    return $betweenPages;
  }

  /**
   * Sets the last page number. May be used for the anchors like "end", "the last page" etc.
   * @access private
   * @return int Returns integer with the laste page number.
   */

  private function setLastPage()
  {
    $this->last = ceil($this->options['all']/$this->options['perPage']);
    if($this->last == 0) 
	{
      $this->last = 1;
    }
    return $this->last;
  }

  public function setOption($key, $value)
  {
    $this->options[$key] = $value;
  }

}