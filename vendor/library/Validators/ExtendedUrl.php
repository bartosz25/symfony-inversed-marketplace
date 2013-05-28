<?php
namespace Validators;

// use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Url;

class ExtendedUrl extends Url
{
  public $message = 'This value is not a valid URL';
  public $protocols = array('http', 'https');
}
?>