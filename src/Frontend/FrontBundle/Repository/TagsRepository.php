<?php
namespace Frontend\FrontBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Others\Tools;

class TagsRepository extends EntityRepository 
{

  /**
   * Gets tags by tags list from IN clause.
   * @access public
   * @param array $tags Tags array (words has to be between '').
   * @return array Tags list with id_ta as a key and tagName as the value
   */
  public function getTagsIn($tags)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 't.id_ta, t.tagName')
    ->add('from', 'FrontendFrontBundle:Tags t');
    $qb->add('where', $qb->expr()->in('t.tagName', $tags));
    $query = $qb->getQuery();
    $finalResult = array();
    foreach($query->getResult() as $result)
    {
      $finalResult[$result['id_ta']] = $result['tagName'];
    }
    return $finalResult;
  }

  /**
   * Test tags in query. Tests if query is safe.
   * @access public
   * @param array $tags Tags array (words has to be between '').
   * @return string Parsed query
   */
  public function testGetTagsIn($tags)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 't.id_ta, t.tagName')
    ->add('from', 'FrontendFrontBundle:Tags t');
    $qb->add('where', $qb->expr()->in('t.tagName', $tags));
    $query = $qb->getQuery();
    return $query->getSql();
  }

  /**
   * Gets tags by tags list from IN clause (ids).
   * @access public
   * @param array $tags Tags ids.
   * @return array Tags list with id_ta as a key and tagName as the value
   */
  public function getTagsInId($tags)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 't.id_ta, t.tagName, t.tagAds, t.tagOffers')
    ->add('from', 'FrontendFrontBundle:Tags t');
    $qb->add('where', $qb->expr()->in('t.id_ta', $tags));
    $query = $qb->getQuery();
    return $query->getResult();
  }

  /** 
   * Gets all tags.
   * @access public
   * @param array $options Options array.
   * @return array Tags list.
   */
  public function getCompletList($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT t.id_ta, t.tagName, t.tagAds, t.tagOffers
    FROM FrontendFrontBundle:Tags t
    ORDER BY t.tagName ASC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }

  /** 
   * Checks if $tag exists.
   * @access public
   * @param string $tag Tag name
   * @return boolean True if exists, false otherwise.
   */
  public function ifExists($tag)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT t.id_ta
    FROM FrontendFrontBundle:Tags t
    WHERE t.tagName = :tag")
    ->setParameter('tag', $tag);
    $row = $query->getResult();
    return (bool)isset($row[0]['id_ta']);
  }

  /** 
   * Update $field counter.
   * @access public
   * @param string $how How update (increment/decrement)
   * @param string $field Field to update
   * @param mixed Array for more tags, integer for one tag.
   * @return void
   */
  public function updateQuantity($how, $field, $tags)
  {
    if(is_array($tags))
    {
      $qb = $this->getEntityManager()->createQueryBuilder();
      $qb->update('FrontendFrontBundle:Tags', 't')
      ->set('t.'.$field, 't.'.$field.' '.$how) 
      ->add('where', $qb->expr()->in('t.id_ta', $tags))
      ->getQuery()
      ->getResult();
    }
    else
    {
      $this->getEntityManager()->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Tags', 't')
      ->set('t.'.$field, 't.'.$field.' '.$how)
      ->where('t.id_ta = ?1')
      ->setParameter(1, (int)$tags)
      ->getQuery()
      ->execute();
    }
  }

  /** 
   * Gets random offers.
   * @access public
   * @param int $limit Limit of tags to load.
   * @param int $max Number of all tags.
   * @return array Tags list.
   */
  public function getRandomTags($limit, $max)
  {
    $start = Tools::getStart($limit, $max);
    $query = $this->getEntityManager()
    ->createQuery("SELECT t.id_ta, t.tagName, t.tagAds, t.tagOffers
    FROM FrontendFrontBundle:Tags t
    ORDER BY t.tagName ASC")
    ->setMaxResults($limit)
    ->setFirstResult($start);
    return $query->getResult();
  }

}