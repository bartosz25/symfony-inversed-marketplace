<?php
namespace Ebay;

/**
 * Class to handle the connection to eBay web service.
 */
class EbayGetItem extends EbayWebService
{

  const TEMPLATE = 'getItem.xml';
  private $itemId;

  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }

  public function setHeaders($data = array())
  {
    parent::setHeaders(array("X-EBAY-API-CALL-NAME" => "GetItem"));
  }

  public function prepareInput()
  {
    $template = file_get_contents($this->templatePath.self::TEMPLATE);
    $this->setInput(str_replace('[[ITEMID]]', $this->itemId, $template));
  }

// TODO : toutes ces données doivent être filtrées (pas de balises <script />, uniqument les balises
// acceptées par le site et convertis en bbcode
  public function getItem()
  {
    $item = array();
    $this->toXml();
    $this->checkForError();

    $currentPriceVal = $this->hasBuyItNow();
    // If -1, the Buy it now price isn't specified : we look for the normal current price
    if($currentPriceVal == -1)
    {
      $currentPrice = $this->result->Item->SellingStatus->CurrentPrice;
      $currentPriceVal = (string)$currentPrice;
      $attrs = (array)$currentPrice->attributes();
      if($attrs['@attributes']['currencyID'] != $this->currency)
      {
        $converted = $this->convertCurrentPrice();
        if($converted == -1)
        {
          throw new \Exception("The bid does not contain the right currency");
        }
        $currentPriceVal = $converted;
      }
    }
    return array('title' => (string)$this->result->Item->Title, 'desc' => (string)$this->result->Item->Description, 
      'objectState' => (string)$this->result->Item->ConditionID,
      'city' => (string)$this->result->Item->Location, 'country' => (string)$this->result->Item->Country,
      'category' => (string)$this->result->Item->PrimaryCategory->CategoryID, 
      'offersCount' => (string)$this->result->Item->SellingStatus->BidCount,
      'offerCurrency' => (string)$this->result->Item->Currency, 'offerPrice' => $currentPriceVal
    );
  }

  private function convertCurrentPrice()
  {
    $convertedPrice = $this->result->Item->SellingStatus->ConvertedCurrentPrice;
    $convertedPriceVal = (string)$convertedPrice;
    $attrs = (array)$convertedPrice->attributes();
    if($attrs['@attributes']['currencyID'] == $this->currency)
    {
      return $convertedPriceVal;
    }
    return -1;
  }

  private function hasBuyItNow()
  {
    $buyItNow = $this->result->Item->BuyItNowPrice;
    $price = -1;
    if((float)$buyItNow > 0)
    {
      $attrs = (array)$buyItNow->attributes();
      if($attrs['@attributes']['currencyID'] != $this->currency)
      {
        $converted = $this->result->Item->ConvertedBuyItNowPrice;
        $attrs = (array)$converted->attributes();
        if($attrs['@attributes']['currencyID'] == $this->currency)
        {
          $price = (string)$converted;
        }
      }
      else
      {
        $price = (string)$buyItNow;
      }
    }
    return $price;
  }

}