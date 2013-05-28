<?php
namespace Category\CategoriesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
/**
 * @ORM\Table(name="categories_modified")
 * @ORM\Entity(repositoryClass="Category\CategoriesBundle\Repository\CategoriesModifiedRepository")
 */
class CategoriesModified extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\Categories")
   * @ORM\JoinColumn(name="categories_id_ca", referencedColumnName="id_ca")
   */
  protected $modifiedCategory;

  /**
   * @ORM\Column(name="modifs_cm", type="text", nullable=false)
   */
  protected $modifiedText;

  /**
   * @ORM\Column(name="first_modif_cm", type="datetime", nullable=false)
   */
  protected $modifiedFirstModif;

  /**
   * @  ORM\Column(name="send_modif_cm", type="date", nullable=false)
   */
  // protected $modifiedSendDate;

  /**
   * @ORM\Column(name="last_user_cm", type="integer", length="11", nullable=false)
   */
  protected $modifiedLastUser;

  protected $typeLabels = array('add' => "Rajout de l'annonce {AD_NAME}", 'content' => "Modification de l'annonce {AD_NAME}", 
  'delete' => "Suppression de l'annonce {AD_NAME}");


  /**
   * Getters.
   */
  public function getModifiedCategory()
  {
    return $this->modifiedCategory;
  }
  public function getModifiedText()
  {
    return $this->modifiedText;
  }
  public function getModifiedFirstModif()
  {
    return $this->modifiedFirstModif;
  }
  // public function getModifiedSendDate()
  // {
    // return $this->modifiedSendDate;
  // }
  public function getModifiedLastUser()
  {
    return $this->modifiedLastUser;
  }
  public function getTypeLabel($type, $values)
  {
    return str_replace(array('{AD_NAME}'), $values, $this->typeLabels[$type]);
  }
  public function getFrequency()
  {
    return 3;
  }

  /**
   * Setters.
   */
  public function setModifiedCategory($value)
  {
    $this->modifiedCategory = $value;
  }
  public function setModifiedText($value)
  {
    $this->modifiedText = $value;
  }
  public function setModifiedFirstModif($value)
  {
    $this->modifiedFirstModif = $value;
  }
  // public function setModifiedSendDate($value)
  // {
    // $this->modifiedSendDate = $value;
  // } 
  public function setModifiedLastUser($value)
  {
    $this->modifiedLastUser = $value;
  }

}