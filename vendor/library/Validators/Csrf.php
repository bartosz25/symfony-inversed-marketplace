<?php
namespace Validators;

use Symfony\Component\Validator\Constraint;

/**
 * Validator for CSRF fields.
 */
class Csrf extends Constraint
{
  public $message = "Token value isn't correct";
  public $sessionToken;
  public $field;
}