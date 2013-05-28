<?php

namespace Validators;

use Symfony\Component\Validator\Constraint;

class CheckPassword extends Constraint
{
  public $message = 'This value is not filled up correctly';
  public $em;
  public $login;
  public $saltData;
}
?>