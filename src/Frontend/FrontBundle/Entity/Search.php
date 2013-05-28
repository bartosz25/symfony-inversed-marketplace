<?php
namespace Frontend\FrontBundle\Entity;

class Search
{

  protected $place;

  public function setPlace($place)
  {
    $this->place = (int)$place;
  }
  public function getPlace()
  {
    return $this->place;
  }
  
  public function isAd()
  {
    return (bool)($this->place == 1);
  }
  
  public function isOffer()
  {
    return (bool)($this->place == 2);
  }


}