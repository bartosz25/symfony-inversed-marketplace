<?php
namespace Others;

/**
 * Class to handle the connection to Prestashop web service.
 */
class PrestashopWebService
{
  /**
   * Connection credentials.
   */
  private $credentials = array();

  /**
   * API's URL.
   */
  private $site = '';

  /**
   * API's response.
   */
  private $result = '';

  /**
   * Work mode.
   */
  private $debug = 0;

  /**
   * Array with returned data.
   */
  public $resArray = array();

  /**
   * Class constructor. It initializes connection data
   */
  public function __construct($data)
  {
    $this->credentials['key'] = $data['key'];
    // check if URL ends with one /
    $length = mb_strlen($data['site'], 'UTF-8');
    if($data['site'][$length-1] != '/')
    {
      $data['site'] = $data['site'].'/';
    }
    $this->site = $data['site'];
    // set debug mode
    $this->debug = $data['debug'];
  }


  /**
   * Connect to web service.
   */
  public function connect()
  {
    $req = curl_init();
    curl_setopt_array($req, array(CURLOPT_SSL_VERIFYPEER => false, CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_URL => $this->site,
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC, CURLOPT_USERPWD => $this->credentials['key'].':',
      CURLOPT_HTTPHEADER => array('Expect:' ) )
    ); 
    $this->result = curl_exec($req);
    $curlInfo = curl_getinfo($req);
    curl_close($req);
    if($this->debug == 1)
    {
      file_put_contents($_SERVER['DOCUMENT_ROOT'].'/ws_prestashop_response.xml', $this->result);
    }
    return (bool)($curlInfo['http_code'] == 200);
  }

  /**
   * Parses products response.
   */
  public function parseLinks()
  {
    $this->toXml();
    $children = $this->result->evaluate('/prestashop/api')->item(0)->childNodes;
    $this->resArray['links'] = array();
    foreach($children as $c => $child)
    {
      // accept strings only
      if($child->nodeName != '#text' && preg_match('/[(A-Za-z0-9_)+]/i', $child->nodeName))
      {
        $this->resArray['links'][$child->nodeName] = $child->getAttribute('xlink:href');
      }
    }
  }
 
  /**
   * Parses categories resource response.
   */
  public function parseCategories()
  {
    $this->toXml();
    $list = $this->result->evaluate('/prestashop/categories/category');
    $this->resArray['categories'] = array();
    foreach($list as $e => $element)
    {
      $this->resArray['categories'][$element->getAttribute('id')] = array('href' => $element->getAttribute('xlink:href'),
      'id' => $element->getAttribute('id'));
    }
  }

  /**
   * Parses tags response.
   */
  public function parseTags()
  {
    $this->toXml();
    $tags = $this->result->evaluate('/prestashop/tags/tag');
    $this->resArray['tags'] = array();
    foreach($tags as $t => $tag)
    {
      $this->resArray['tags'][] = array('id' => $tag->getAttribute('id'), 'href' => $tag->getAttribute('xlink:href'));
    }
  }

  /**
   * Parses category response.
   */
  public function parseCategory()
  {
    $this->toXml();
    $category = $this->result->evaluate('/prestashop/category')->item(0)->getElementsByTagName('id')->item(0)->textContent;
    $this->resArray['category'][(int)$category] = array();
    $products = $this->result->evaluate('.//associations/products/product');
    $categoryProducts = array();
    foreach($products as $p => $product)
    {
      $categoryProducts[] = array('href' => $product->getAttribute('xlink:href'), 'id' => $product->getElementsByTagName('id')->item(0)->textContent);
    }
    $this->resArray['category'][$category]['name'] = $this->result->evaluate('/prestashop/category/name/language[@id="2"]')->item(0)->textContent;
    $this->resArray['category'][$category]['products'] = $categoryProducts;
    $this->resArray['category'][$category]['relations'] = array('category' => 0, 'catalogue' => 0);
    $this->resArray['category'][$category]['productsQuantity'] = count($categoryProducts);
  }

  /**
   * Parses products response.
   */
  public function parseProduct()
  {
    $this->toXml();
    $product = $this->result->evaluate('/prestashop/product');
    $id = (int)$product->item(0)->getElementsByTagName('id')->item(0)->textContent;
    $this->resArray['product'] = array('href' => $this->site, 'id' => $id, 'price' => (float)$product->item(0)->getElementsByTagName('price')->item(0)->textContent,
    'name' => $product->item(0)->getElementsByTagName('name')->item(0)->getElementsByTagName('language')->item(0)->textContent, 'description' => $product->item(0)->getElementsByTagName('description')->item(0)->getElementsByTagName('language')->item(0)->textContent
    );
  }

  /**
   * Makes XML object.
   */
  public function toXml()
  {
    $domCl = new \DOMDocument();
    $domCl->loadXML($this->result);
    $this->result = new \DOMXPath($domCl);
  }

  /**
   * Sets new URL.
   */
  public function setSite($url)
  {
    $this->site = $url;
  }

}