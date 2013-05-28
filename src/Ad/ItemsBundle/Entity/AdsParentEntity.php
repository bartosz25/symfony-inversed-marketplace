<?php
namespace Ad\ItemsBundle\Entity;
use Database\MainEntity;

class AdsParentEntity extends MainEntity
{ 
  public $objetStates = array('peu importe', 'neuf', 'usagé');
  private $categoriesList = array();
  private $countriesList = array();
  private $citiesList = array();
  private $formFields = array();
  protected $tag1, $tag2, $tag3, $tag4, $tag5, $tag6, $tag7,
  $tag8, $tag9, $tag10;

  /**
   * Getters.
   */
  public function getTag1()
  {
    return $this->tag1;
  }
  public function getTag2()
  {
    return $this->tag2;
  }
  public function getTag3()
  {
    return $this->tag3;
  }
  public function getTag4()
  {
    return $this->tag4;
  }
  public function getTag5()
  {
    return $this->tag5;
  }
  public function getTag6()
  {
    return $this->tag6;
  }
  public function getTag7()
  {
    return $this->tag7;
  }
  public function getTag8()
  {
    return $this->tag8;
  }
  public function getTag9()
  {
    return $this->tag9;
  }
  public function getTag10()
  {
    return $this->tag10;
  }
  public function getFormFields()
  {
    return $this->formFields;
  }
  public function getObjetStates()
  {
    return $this->objetStates;
  }
  public function getNullObjectState()
  {
    return 0;
  }
  public function getNewObjectState()
  {
    return 1;
  }
  public function getUsedObjectState()
  {
    return 2;
  }
  public function getCountriesList()
  {
    $countries = array();
    foreach($this->countriesList as $c => $country)
    {
      $countries[$country['id_co']] = $country['countryName'];
    }
    return parent::makeSelectList($countries, '-- choissisez le pays --');
  }
  public function getCitiesList()
  {
    $cities = array(); 
    if(count($this->citiesList) > 0)
    {
      foreach($this->citiesList as $c => $city)
      {
        $cities[$city['id_ci']] = $city['cityName'];
      }
    }
    return parent::makeSelectList($cities, '-- choissisez la ville --');
  }
  public function getCategoriesList()
  {
    $categories = array();
    foreach($this->categoriesList as $p => $parent)
    {
      foreach($parent['children'] as $c => $category)
      {
        $categories[$category['id']] = $category['name'];
      }
    }
    return parent::makeSelectList($categories, '-- choissisez la catégorie --');
  }
  /**
   * Setters
   */
  public function setTag1($value)
  {
    $this->tag1 = $value;
  }
  public function setTag2($value)
  {
    $this->tag2 = $value;
  }
  public function setTag3($value)
  {
    $this->tag3 = $value;
  }
  public function setTag4($value)
  {
    $this->tag4 = $value;
  }
  public function setTag5($value)
  {
    $this->tag5 = $value;
  }
  public function setTag6($value)
  {
    $this->tag6 = $value;
  }
  public function setTag7($value)
  {
    $this->tag7 = $value;
  }
  public function setTag8($value)
  {
    $this->tag8 = $value;
  }
  public function setTag9($value)
  {
    $this->tag9 = $value;
  }
  public function setTag10($value)
  {
    $this->tag10 = $value;
  }
  public function setFormFields($value)
  {
    $this->formFields = $value;
  }
  public function setCategoriesList($categories)
  {
    $this->categoriesList = $categories;
  } 
  public function setCountriesList($countries)
  {
    $this->countriesList = $countries;
  }
  public function setCitiesList($cities)
  {
    $this->citiesList = $cities;
  }
  public function setFormFieldsData($params)
  {
    foreach($params as $p => $param)
    {
      $this->$param['codeName'] = $param['fieldValue'];
    }
    $this->setFormFields($params);
  }
}