<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Ad\ItemsBundle\Entity\AdsModified;

class AdsModifiedRepository extends EntityRepository 
{

  /**
   * Checks if $ad exists in the table.
   * @access public
   * @param int $ad Ad id.
   * @return boolean True if exists, false otherwise
   */
  public function ifExists($ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT am.modifiedText
    FROM AdItemsBundle:AdsModified am
    WHERE am.modifiedAd = :ad")
    ->setParameter('ad', $ad); 
    $row = $query->getResult();
    return (bool)(isset($row[0]['modifiedText']));
  }

  /**
   * Updates or inserts modifications of one ad.
   * @access public
   * @param int $ad Ad's id.
   * @param string $type Modification's type.
   * @return void.
   */
  public function adModified($ad, $type)
  {
    $admEnt = new AdsModified;
    $text = array();
    $query = $this->getEntityManager()
    ->createQuery("SELECT am.modifiedText, a.id_ad
    FROM AdItemsBundle:AdsModified am
    JOIN am.modifiedAd a
    WHERE a.id_ad = :ad")
    ->setParameter('ad', $ad); 
    $row = $query->getResult();
    $ifExists = $this->ifExists($ad);
    if($ifExists)
    {
      $text = (array)(unserialize($row[0]['modifiedText']));
    }
    $text[] = array('type' => $type, 'text' => $admEnt->getTypeLabel($type), 'date' => time());
    if($ifExists)
    {
      // make update
      $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\AdsModified', 'am')
      ->set('am.modifiedText', '?1')
      ->where('am.modifiedAd = ?2')
      ->setParameter(1, serialize($text))
      ->setParameter(2, $ad)
      ->getQuery()
      ->execute();
    }
    else
    {
      // $date = new \DateTime();
      // $date->add(new \DateInterval('P'.$admEnt->getFrequency().'D'));
      // insert new modified element
      $admEnt->setData(array('modifiedAd' => $this->getEntityManager()->getReference('Ad\ItemsBundle\Entity\Ads', $ad),
      'modifiedText' => serialize($text), 'modifiedFirstModif' => new \DateTime(),
      /*'modifiedSendDate' => $date,*/ 'modifiedLastUser' => 0
      ));
      $this->getEntityManager()->persist($admEnt);
      $this->getEntityManager()->flush();
    }
  }

  /**
   * Gets new informations for user.
   * @access public
   * @param int $user User's id.
   * @return boolean True if subsbscribed, false otherwise
   */
  public function getNewsByAd($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.adName, a.id_ad, am.modifiedText, c.id_ca, c.categoryUrl, c.categoryName
    FROM UserAlertsBundle:UsersAdsAlerts uaa
    JOIN uaa.alertAd am
    JOIN am.modifiedAd a
    JOIN a.adCategory c
    WHERE uaa.alertUser = :user")
    ->setParameter("user", $user); 
    return $query->getResult();
  }

}