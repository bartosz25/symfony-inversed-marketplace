<?php
namespace Frontend\FrontBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\NotBlank;
use Validators\IsUsed;
use Database\MainEntity;

/**
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="Frontend\FrontBundle\Repository\TagsRepository")
 */
class Tags extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ta", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ta;
 
  /**
   * @ORM\Column(name="name_ta", type="string", length="100", nullable=false)
   */
  protected $tagName;

  /**
   * @ORM\Column(name="ads_ta", type="integer", length="11", nullable=false)
   */
  protected $tagAds;

  /**
   * @ORM\Column(name="offers_ta", type="integer", length="11", nullable=false)
   */
  protected $tagOffers;

  public static $em;

  /**
   * Setters.
   */
  public function setTagName($value)
  {
    $this->tagName = $value;
  }
  public function setTagAds($value)
  {
    $this->tagAds = $value;
  }
  public function setTagOffers($value)
  {
    $this->tagOffers = $value;
  }
  /**
   * Getters.
   */
  public function getIdTa()
  {
    return $this->id_ta;
  }
  public function getTagName()
  {
    return $this->tagName;
  }
  public function getTagAds()
  {
    return $this->tagAds;
  }
  public function getTagOffers()
  {
    return $this->tagOffers;
  }

  /**
   * Sets static Tags instance.
   * @access public
   * @param array $params Param's array.
   * @return void
   */
  public function setStaticTag($params)
  {
    foreach($params as $p => $param)
    {
      $this->$p = $param;
    }
  }

  /**
   * Resets static tag instance.
   * @access public
   * @param array $params Array with parameters.
   * @return void
   */
  public function resetStaticTag($params)
  {
    foreach($params as $p => $param)
    {
      $this->$param = null;
    }
  }
  
  /**
   * Validator constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // login constraints
    $metadata->addPropertyConstraint('tagName', new NotBlank(array('message' => "Veuillez indiquer le nom du tag."
    , 'groups' => array('editTag'))));
    $metadata->addPropertyConstraint('tagName', new MaxLength(array('limit' => 20, 'message' => "Le tag peut avoir au maximum 20 caractères."
    , 'groups' => array('editTag'))));
    $metadata->addPropertyConstraint('tagName', new IsUsed(array('em' => self::$em, 'what' => 'tag', 'type' => 'falseIfExists', 'field' => '', 'message' => "Ce tag existe déjà."
    , 'groups' => array('editTag'))));
  }


}