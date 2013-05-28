<?php

namespace Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException; 

class IsUsedValidator extends ConstraintValidator
{

  public function isValid($value, Constraint $constraint)
  {
    $type = true;
    switch($constraint->what)
    {
      case 'register':
        if($constraint->type == 'trueIfExists')
        {
          $type = false;
          $result = $constraint->em->getRepository('UserProfilesBundle:Users')->isUsedActive($constraint->field, $value);
        }
        else
        {
          $result = $constraint->em->getRepository('UserProfilesBundle:Users')->isUsed($constraint->field, $value);
        }
      break;
      case 'code':
          $result = $constraint->em->getRepository('UserProfilesBundle:UsersCodes')->hasCode($value, 2);
          if($result)
          {
            $type = false;
          }
      break;
      case 'tag':
          $result = $constraint->em->getRepository('FrontendFrontBundle:Tags')->ifExists($value);
      break;
    }
    if($result == $type)
    {
      $this->setMessage($constraint->message, array('{{value}}' => $value));
      return false;
    }
    return true;
  }

}
?>