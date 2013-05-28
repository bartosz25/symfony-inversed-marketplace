<?php
namespace Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CsrfValidator extends ConstraintValidator
{

  public function isValid($value, Constraint $constraint)
  { //echo $value.'---------'.$constraint->sessionToken;die();
    $result = (bool)($value == $constraint->sessionToken && $value !== null && $constraint->sessionToken !== null);
    if(!$result)
    {
      $this->setMessage($constraint->message, array('{{value}}' => $value, '{{field}}' => $constraint->field));
      // $this->setMessage($constraint->message);
      return false;
    }
    return true;
  }

}