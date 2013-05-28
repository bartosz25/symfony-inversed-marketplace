<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\NotBlank;
use Database\MainEntity;

/**
 * @ORM\Table(name="catalogues")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\CataloguesRepository")
 */
class Catalogues extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_cat", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_cat; 

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $catalogueProp;

  /**
   * @ORM\Column(name="name_cat", type="string", length="100", nullable=false)
   */
  protected $catalogueName;

  /**
   * @ORM\Column(name="desc_cat", type="text", nullable=false)
   */
  protected $catalogueDesc;

  /**
   * @ORM\Column(name="offers_cat", type="integer", length="6", nullable=false)
   */
  protected $catalogueOffers = 0;

  /**
   * @ORM\Column(name="deleted_cat", type="integer", length="1", nullable=false)
   */
  protected $catalogueDeleted; // 0 - active, 1 - is deleted

  /**
   * Getters.
   */
  public function getIdCat()
  {
    return $this->id_cat;
  }
  public function getCatalogueName()
  {
    return $this->catalogueName;
  }
  public function getCatalogueDesc()
  {
    return $this->catalogueDesc;
  }
  public function getCatalogueOffers()
  {
    return $this->catalogueOffers;
  }
  public function getCatalogueDeleted()
  {
    return $this->catalogueDeleted;
  }
  /**
   * Setters
   */
  public function setId_cat($value)
  {
    $this->id_cat = $value;
  }
  public function setCatalogueName($value)
  {
    $this->catalogueName = $value;
  }
  public function setCatalogueProp($value)
  {
    $this->catalogueProp = $value;
  }
  public function setCatalogueDesc($value)
  {
    $this->catalogueDesc = $value;
  }
  public function setCatalogueOffers($value)
  {
    $this->catalogueOffers = $value;
  }
  public function setCatalogueDeleted($value)
  {
    $this->catalogueDeleted = $value;
  }

  /**
   * Sets data after submitting an addition form.
   * @access public
   * @param array $params Params list.
   * @return void
   */
  public function setDataAdded($params)
  {
    foreach($params as $p => $param)
    {
      $method = 'set'.ucfirst($p);
      $this->$method($param);
    }
  }

  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // name constraints
    $metadata->addPropertyConstraint('catalogueName', new NotBlank(array('message' => "Veuillez indiquer le nom du catalogue."
    , 'groups' => array('addCatalogue'))));
    $metadata->addPropertyConstraint('catalogueName', new MaxLength(array('limit' => 100, 'message' => "Le nom peut avoir au maximum 100 caractères."
    , 'groups' => array('addCatalogue'))));
    $metadata->addPropertyConstraint('catalogueDesc', new MaxLength(array('limit' => 200, 'message' => "La description peut avoir au maximum 200 caractères."
    , 'groups' => array('addCatalogue'))));
  }

}