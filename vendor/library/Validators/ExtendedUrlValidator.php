<?php

namespace Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\UrlValidator;

class ExtendedUrlValidator extends UrlValidator
{

  public function isValid($value, Constraint $constraint)
  {
    // first, check URL with Symfony method
    $result = parent::isValid($value, $constraint);
// HACK to local $value => prestashoptest/ 
// $result = true;
    if($result)
    {
      // we check only the URL : split passed value to get only the base URL
      // tested value : 'http://www.test.com'
      // FilterXSS changed / to &#x2F; , it's why we reconvert it
      $test = str_replace('&#x2F;', '/', $value);
      preg_match_all('/^(.*):\/\/(www.|)([A-Za-z0-9\-_\.]+)(\/|)(.*)$/i', $test, $matches);
      if(!isset($matches[1][0]) || !isset($matches[2][0]) || !isset($matches[3][0])) 
      {
        return false;
      }
      // if URL pattern is correct, check the server response (only 200 accepted)
      $req = curl_init();
      curl_setopt_array($req, array(CURLOPT_SSL_VERIFYPEER => false, CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_URL => $matches[1][0].'://'.$matches[2][0].$matches[3][0])
      ); 
      $result = curl_exec($req);
      $curlInfo = curl_getinfo($req);
      $result = (bool)($curlInfo['http_code'] == 200);
    }
    return $result;
  }

}
?>