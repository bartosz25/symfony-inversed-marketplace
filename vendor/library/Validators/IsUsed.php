<?php

namespace Validators;

use Symfony\Component\Validator\Constraint;

class IsUsed extends Constraint
{
  public $message = 'This value is already used';
  public $field = '';
  public $type = ''; 
  public $what;
  public $em;
}
?>