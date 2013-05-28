<?php
namespace Coconout\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;


class TestsAccessRepository extends EntityRepository 
{

  /**
   * Gets all tests.
   * @access public
   * @return array Tests list.
   */
  public function getAllTests()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT t.id_ta, t.testsLabel, t.testsType, t.testsLastExecution, t.testsLastResult, t.testsConfig
    FROM CoconoutBackendBundle:TestsAccess t
    ORDER BY t.testsLabel ASC");
    return $query->getResult();
  }

}