<?php
namespace Ebay;

/**
 * Class to handle the connection to eBay web service.
 */
class EbayGetItemsList extends EbayWebService
{

  const TEMPLATE = 'getItemsList.xml';
  public $insertedItems = array();
  public $dateFormat = 'Y-m-d H:i:s';
  public $userId = "";
  public $perPage = 50;
  public $page = 1;

  public function setHeaders($data = array())
  {
    parent::setHeaders(array("X-EBAY-API-CALL-NAME" => "GetSellerList"));
  }

  public function prepareInput()
  {
    $day = 24*60*60;
    $tplVals = array('[[STARTTIME]]', '[[ENDTIME]]', '[[USERID]]', '[[PERPAGE]]', '[[PAGE]]');
    $vals = array(date('Y-m-d H:i:s', (time()-(14*$day))), date('Y-m-d H:i:s', time()+(14*$day)), $this->userId, $this->perPage, $this->page);
    $template = file_get_contents($this->templatePath.self::TEMPLATE);
    $this->setInput(str_replace($tplVals, $vals, $template));
  }

  public function getItems($uniq = false)
  {
    $items = array();
    $this->toXml();
    $this->checkForError();
    foreach($this->result->ItemArray->Item as $node)
    {
      // add the item only when it doesn't exist in the user storage system
      if(!$uniq || ($uniq && !in_array((string)$node->ItemID[0], $this->insertedItems)))
      {
        $items[] = array('id' => (string)$node->ItemID[0], 'startTime' => date($this->dateFormat, strtotime((string)$node->ListingDetails[0]->StartTime)),
        'endTime' => date($this->dateFormat, strtotime((string)$node->ListingDetails[0]->EndTime)));
      }
    }
    return $items;
  }

}