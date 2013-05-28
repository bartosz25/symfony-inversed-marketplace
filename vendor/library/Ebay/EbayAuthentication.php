<?php
namespace Ebay;

/**
 * Class to handle the connection to eBay web service.
 */
class EbayAuthentication extends EbayWebService
{

  const TEMPLATE = 'authentication.xml';

  public function setHeaders($data = array())
  {
    // parent::setHeaders(array("X-EBAY-API-CALL-NAME" => "GetItem"));
  }

  public function prepareInput()
  {
    $template = file_get_contents($this->templatePath.self::TEMPLATE);
    // $this->setInput(str_replace('[[ITEMID]]', '280770407979', $template));
  }

}