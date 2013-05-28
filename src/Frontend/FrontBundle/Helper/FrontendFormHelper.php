<?php
 
namespace Frontend\FrontBundle\Helper;
 
use Symfony\Component\Templating\Helper\Helper;

class FrontendFormHelper extends Helper
{

  /**
   * Checks if CSRF ticket is invalid. 
   * @access public
   * @param array $messages Array with messages.
   * @return strint If invalid, it returns a ticket error message, empty string otherwise.
   */
  public function checkInvalidTicket($messages, $ticketKey)
  {
    $errorMsg = "";
    foreach($messages as $m => $msg)
    {
      if($m == $ticketKey)
      {
        if(!is_array($msg))
        {
          $errorMsg = $msg;
        }
        else
        {
          foreach($msg as $p => $msgPart)
          {
            $errorMsg .= $msgPart;
          }
        }
      }
    }
    return $errorMsg;
  }

  /**
   * Sets error class when necessary.
   * @access public
   * @param array $errors List with errors.
   * @param string $key Key to find in the $errors list.
   * @param string $class CSS class to show if $key found.
   * @return string CSS class or empty string.
   */
  public function setErrorClass($errors, $key, $class)
  {
    $finalClass = "";
    if(array_key_exists($key, $errors))
    {
      $finalClass = $class;
    }
    return $finalClass;
  }

  /**
   * Prepares error message to be displayed at the page.
   * @access public
   * @param string $block HTML container with the message.
   * @param array $messages Array with messages.
   * @param string $field Field of $messages array..
   * @return string Final message.
   */
  public function displayErrorBlock($block, $messages, $field)
  {
    if(count($messages) == 0) return "";
    $message = array();
    foreach($messages as $m => $msg)
    {
      if((string)$m == (string)$field)
      {
        if(is_array($msg)) //message))
        {
          foreach($msg as $p => $msgPart)
          {
            $message[] = $msgPart;
          }
        }
        else
        {
          $message[] = $msg;
        }
      }
    }
    return sprintf($block, implode('<br />', $message));
  }
 

  public function getName()
  {
      return 'frontendForm';
  }

}