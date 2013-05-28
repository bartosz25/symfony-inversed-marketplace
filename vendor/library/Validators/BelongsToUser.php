<?php
namespace Validators;

use Symfony\Component\Validator\Constraint;

class BelongsToUser extends Constraint
{
  public $message = 'This value is already used'; 
  public $what;
  public $em;
  public $userId;
}
?>