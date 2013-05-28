<?php

namespace Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException; 
use Security\SaltCellar;

class CheckPasswordValidator extends ConstraintValidator
{

  public function isValid($value, Constraint $constraint)
  {
    // first, salt password which will be checked after 
    $user = $constraint->em->getRepository('UserProfilesBundle:Users')->loadUserByUsername($constraint->login);
    $regDate = strtotime($user->getRegisteredDate()); 
    $cellar = new SaltCellar($constraint->saltData);
    $salt = $cellar->getSalt($user->getRegisteredDate());
    $passSalt = sha1($cellar->setHash(array('salt' => $salt, 'mdp' => $value, 'login' => $user->getLogin()), date('n', $regDate)));
    if((bool)($constraint->em->getRepository('UserProfilesBundle:Users')->checkPassword($constraint->login, $passSalt)))
    {
      return true;
    }
    $this->setMessage($constraint->message, array('{{value}}' => $value));
    return false;
  }

}
?>