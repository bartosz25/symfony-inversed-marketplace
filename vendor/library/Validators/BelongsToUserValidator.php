<?php

namespace Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException; 

class BelongsToUserValidator extends ConstraintValidator
{

  public function isValid($value, Constraint $constraint)
  {
    $result = false;
    if($value == 0)
    {
      $result = true;
    }
    else
    {
      switch($constraint->what)
      {
        case 'address':
          $row = $constraint->em->getRepository('UserAddressesBundle:UsersAddresses')->getUserAddress((int)$value, $constraint->userId);
          if(isset($row[0]['id_ua']) && (int)$row[0]['id_ua'] > 0)
          {
            $result = true;
          }
        break;
      }
    }
    if(!$result)
    {
      $this->setMessage($constraint->message, array('{{value}}' => $value));
      return false;
    }
    return true;
  }

}
?>