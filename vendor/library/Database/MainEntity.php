<?php
namespace Database;

/**
 * Main class entity with some of common methods used by
 * application's database entities.
 */
class MainEntity
{

  /**
   * CSRF protection vars.
   */
  protected $ticket;
  protected static $sessionToken;
  protected static $ticketMessage = "Votre session a expiré";
  

  /**
   * Sets CSRF protection token.
   * @access public
   * @param string $token Token's value.
   * @return void
   */
  public function setTicket($token)
  {
    $this->ticket = $token;
  }

  /**
   * Gets value of CSRF protection token.
   * @access public
   * @return string Token's value.
   */
  public function getTicket()
  {
    return $this->ticket;
  }

  /**
   * Sets session CSRF protection token.
   * @access public
   * @param string $token Token's value.
   * @return void
   */
  public static function setSessionToken($token)
  {
    self::$sessionToken = $token;
  }

  /**
   * Gets value of CSRF protection token from the session.
   * @access public
   * @return string Session's token value.
   */
  public static function getSessionToken()
  {
    return self::$sessionToken;
  }

  /**
   * Gets token message.
   * @access public
   * @return String Token's error message.
   */
  public static function getTicketMessage()
  {
    return self::$ticketMessage;
  }

  /**
   * Makes select list.
   * @access public
   * @param array $items Items array.
   * @param string $label Label shown at the first position of the select.
   * @return array List with select data.
   */
  public function makeSelectList($items, $label)
  {
    $options = array();
    if($label != '')
    {
      $options[0] = $label;
    }
    foreach($items as $i => $item)
    {
      $options[$i] = $item;
    }
    return $options;
  }

  /**
   * Sets data after submitting an addition form.
   * @access public
   * @param array $params Params list.
   * @return void
   */
  public function setData($params)
  {
    foreach($params as $p => $param)
    {
      $method = 'set'.ucfirst($p);
      $this->$method($param);
    }
  }

  /**
   * Gets date value. If empty, make today datetime.
   * @access public
   * @param Datetime $value Date value
   * @return Datetime value.
   */
  public function getDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    return $value;
  }

  /**
   * Makes an order clause into SQL statement.
   * @access public
   * @param array $columns Columns which may be ordered.
   * @param array $options Options which ordering column name.
   * @param string $default Default order query.
   * @return string Order query.
   */
  public static function makeOrderClause($columns, $options, $default)
  {
    if(isset($options['column']))
    {
      $query = array();
      if(array_key_exists($options['column'], $columns))
      {
        $howOrder = self::getOrderClause($options['how']);
        if(is_array($columns[$options['column']]))
        {
          foreach($columns[$options['column']] as $c => $clause)
          {
            $query[] = $clause.' '.$howOrder;
          }
        }
        else
        {
          $query[] = $columns[$options['column']].' '.$howOrder;
        }
      }
      return implode(',', $query);
    }
    return $default;
  }

  /**
   * Gets the ASC or DESC order clause. By default, it return ASC.
   * @access public
   * @param string $order Order clause (may be an alias)
   * @return string Order clause which can be used in the SQL/DQL query.
   */
  public static function getOrderClause($order)
  {
    $clause = "ASC";
    $mapping = array('asc' => "ASC", 'desc' => "DESC");
    if(isset($order) && $mapping[$order] != '') $clause = $mapping[$order];
    return $clause;
  }
}