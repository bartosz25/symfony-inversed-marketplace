<?php
namespace Ebay;

/**
 * Class to handle the connection to eBay web service.
 */
class EbayWebService
{
  /**
   * Connection credentials.
   */
  private $credentials = array();

  /**
   * API's URL.
   */
  private $site = "https://api.ebay.com/ws/api.dll";

  /**
   * API's response.
   */
  public $result = '';

  /**
   * Work mode.
   */
  private $debug = 0;

  /**
   * Array with returned data.
   */
  public $resArray = array(); 

  /**
   * Default currency used by the webapp.
   */
  public $currency = "EUR";

  /**
   * cURL connection timeout (exprimed in seconds).
   */
  private $timeout = 15;

  /**
   * Class constructor. It initializes connection data
   */
  public function __construct($data)
  {
    // set debug mode
    $this->debug = $data['debug'];
    // eBay API credentians
    $this->apiCredentials = $data['credentials'];
    // eBay transactions currency
    if(isset($data['currency']) && $data['currency'] != '')
    {
      $this->currency = $data['currency'];
    }
    if(isset($data['timeout']) && $data['timeout'] != '')
    {
      $this->timeout = $data['timeout'];
    }
  }

  private $apiCredentials = array();

  private $headers = array();

  public $templatePath = '';
  private $templateInput;

  public function setHeaders($data = array())
  {
    $this->headers = array("Content-Type : application/x-www-form-urlencoded",
    "X-EBAY-API-CERT-NAME : ".$this->apiCredentials['cert'],
    "X-EBAY-API-APP-NAME : ".$this->apiCredentials['app'],
    "X-EBAY-API-DEV-NAME : ".$this->apiCredentials['dev'],
    "X-EBAY-API-COMPATIBILITY-LEVEL : 711",
    "X-EBAY-API-SITEID : 0");
    foreach($data as $k => $value)
    {
      $this->headers[] = $k." : ".$value;
    }
  }

  public function setInput($input)
  {
    $this->templateInput = $input;
    if($this->debug == 1)
    {
      file_put_contents($_SERVER['DOCUMENT_ROOT'].'/ws_ebay_input.xml', $input);
    }
  }

  /**
   * Connect to web service.
   */
  public function connect()
  {
    $req = curl_init();
    curl_setopt_array($req, array(
CURLOPT_SSL_VERIFYPEER => false, 
CURLOPT_SSL_VERIFYHOST => 0, 
      CURLOPT_TIMEOUT => $this->timeout, 
      CURLOPT_POSTFIELDS => $this->templateInput,
      CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $this->site,
      CURLOPT_HTTPHEADER => $this->headers)
    ); 
    $this->result = curl_exec($req);
    $curlInfo = curl_getinfo($req);
    curl_close($req);
    if($this->debug == 1)
    {
      file_put_contents($_SERVER['DOCUMENT_ROOT'].'/ws_ebay_response.xml', $this->result);
    }
    return (bool)($curlInfo['http_code'] == 200 && $this->result != '');
  }

  /**
   * Converts the string to SimpleXMLElement object.
   */
  public function toXml()
  {
    $this->result = simplexml_load_string($this->result);
  }

  /**
   * Checks if the response is OK.
   */
  public function checkForError()
  {
    if($this->result->Ack == "Failure")
    {
      throw new \Exception("Error in eBay API response : ".(string)$this->result->Errors->LongMessage);
    }
  }

  /**
   * Sets new URL.
   */
  public function setSite($url)
  {
    $this->site = $url;
  }

}