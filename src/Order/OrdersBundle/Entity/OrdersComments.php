<?php
namespace Order\OrdersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Table(name="orders_comments")
 * @ORM\Entity(repositoryClass="Order\OrdersBundle\Repository\OrdersCommentsRepository")
 */
class OrdersComments extends MainEntity
{ 

  /**
   * @ORM\Id
   * @ORM\Column(name="id_oc", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_oc;

  /**
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $commentAd;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users") 
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $commentAuthor;

  /**
   * @ORM\Column(name="comment_oc", type="text", nullable=false)
   */
  protected $commentText;

  /**
   * @ORM\Column(name="date_oc", type="datetime", nullable=false)
   */
  protected $commentDate;

  /**
   * Getters.
   */
  public function getIdOc()
  {
    return $this->id_oc;
  }
  public function getCommentAd()
  {
    return $this->commentAd;
  }
  public function getCommentAuthor()
  {
    return $this->commentAuthor;
  }
  public function getCommentText()
  {
    return $this->commentText;
  }
  public function getCommentDate()
  {
    return $this->commentDate;
  }
  /**
   * Setters
   */
  public function setIdOc($value)
  {
    $this->id_oc = $value;
  }
  public function setCommentAd($value)
  {
    $this->commentAd = $value;
  }
  public function setCommentAuthor($value)
  {
    $this->commentAuthor = $value;
  }
  public function setCommentText($value)
  {
    $this->commentText = $value;
  }
  public function setCommentDate($value)
  {
    $this->commentDate = parent::getDate($value);
  }

}