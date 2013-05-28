<?php
namespace User\AddressesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Database\MainEntity;

class UsersAddressesRepository extends EntityRepository
{

  /** 
   * Gets users addresses list.
   * @access public
   * @param array $options Options array.
   * @param int $user User's id.
   * @return array Users list.
   */
  public function getUserAddresses($options, $user)
  {
    $order = "ua.id_ua ASC";
    $columns = array("intitule" => array("ua.addressFirstName", "ua.addressLastName"));
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT ua.id_ua, ua.addressFirstName, ua.addressLastName, ua.addressCity, ua.addressPostalCode, ua.addressStreet,
    ua.addressInfos
    FROM UserAddressesBundle:UsersAddresses ua
    WHERE ua.addressUser = :user AND ua.addressState = 1
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }

  /** 
   * Gets user's address.
   * @access public
   * @param int $address Address's id.
   * @param int $user User's id.
   * @return array Users address data.
   */
  public function getUserAddress($address, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ua.id_ua, ua.addressFirstName, ua.addressLastName, ua.addressCity, ua.addressPostalCode, ua.addressStreet,
    ua.addressInfos, co.id_co, co.countryName
    FROM UserAddressesBundle:UsersAddresses ua
    JOIN ua.addressCountry co
    WHERE ua.id_ua = :address AND ua.addressUser = :user AND ua.addressState = 1")
    ->setParameter('user', (int)$user)
    ->setParameter('address', (int)$address);
    return $query->getResult();
  }

  /** 
   * Checks if address is used by one order.
   * @access public
   * @param int $address Address's id.
   * @return boolean True if is used, otherwise false.
   */
  public function isUsedInOrder($address)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(o.orderAd)
    FROM OrderOrdersBundle:Orders o
    WHERE o.orderBuyerAddress = :address")
    ->setParameter('address', $address);
    $result = $query->getSingleScalarResult();
    return (boolean)($result > 0);
  }

  /** 
   * Checks if address is used by one order.
   * @access public
   * @param int $address Address's id.
   * @param int $order Order's id.
   * @return boolean True if is used, otherwise false.
   */
  public function isUsedWithoutOneOrder($address, $order)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(o.orderAd)
    FROM OrderOrdersBundle:Orders o
    WHERE o.orderBuyerAddress = :address AND o.orderAd != :order")
    ->setParameter('address', $address)
    ->setParameter('order', $order);
    $result = $query->getSingleScalarResult();
    return (boolean)($result > 0);
  }

  /** 
   * Gets random address.
   * @access public
   * @return array Users address data.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ua.id_ua, ua.addressFirstName, ua.addressLastName, ua.addressCity, ua.addressPostalCode, ua.addressStreet,
    ua.addressInfos, u.id_us
    FROM UserAddressesBundle:UsersAddresses ua
    JOIN ua.addressUser u
    WHERE ua.addressState = 1");
    $row = $query->getResult();
    return array('id' => $row[0]['id_ua'], 'id2' => 0, 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }
}