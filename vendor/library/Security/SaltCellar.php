<?php

namespace Security;

class SaltCellar
{

  /**
   * Protected arrays used to salt a password.
   * @access protected
   * @type array
   */
  protected 
    $_years = array(),
    $_months = array(),
    $_days = array();

  /**
   * Constructor used to fill up protected $_years, $_months and $_days arrays.
   * @access public
   * @param array $data Data to protected arrays.
   * @return void
   */
  public function __construct($data)
  {
    $this->_years = $data['years'];
    $this->_months = $data['months'];
    $this->_days = $data['days'];
  }

  /**
   * Gets salt of passed date.
   * @access public
   * @params string $date Date.
   * @return string String with the salt.
   */
  public function getSalt($date) 
  {
    $date = strtotime($date);
    $year = date('Y', $date);
    $month = date('n', $date);
    $day = date('j', $date);
    
    return $this->_days[$day].$this->_months[$month].$this->_years[$year];
  }

  /**
   * Sets a hash used as an user password.
   * @access public
   * @params array $elements Array with elements used to generate the new password.
   * @params int $month Number representation of the current month.
   * @return string New password.
   */
  public function setHash($elements, $month) 
  {
    if($month%2 == 0) {
      $hash = $elements['mdp'].$elements['login'][0].$elements['salt'];
    }
    else {
      $hash = $elements['mdp'].$elements['login'][mb_strlen($elements['login'], 'UTF-8')-1].$elements['salt'];
    }
	return $hash;
  }
  
  /**
   * Getter for login letter used by setHash($elements, $month) to generate a new password.
   * @access public
   * @param string $login User login.
   * @param int $month Month of user registration.
   * @return string Bean used in the new password.
   */
  public function getSaltBean($login, $month) 
  {
    if($month%2 == 0) {
      $bean = $login[0];
    }
    else {
      $bean = $login[mb_strlen($elements['login'], 'UTF-8')-1];
    }
	return $bean;
  }

}
?>