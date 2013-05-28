<?php
namespace Coconout\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;


class TestsAccessHistoryRepository extends EntityRepository 
{

  /**
   * Gets all tests.
   * @access public
   * @return array Tests list.
   */
  public function getHistoryTests($test)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT tah.id_tah, tah.historyParams, tah.historyDate, tah.historyResult, tah.historyTestedResult
    FROM CoconoutBackendBundle:TestsAccessHistory tah
    WHERE tah.testsAccess = :test
    ORDER BY tah.id_tah DESC")
    ->setParameter('test', $test);
    return $query->getResult();
  }

}