<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Coconout\BackendBundle\Entity\TestsAccess;

class TestsAccessHistoryController extends BackendController
{

  /**
   * Gets history results.
   * @access public
   * @return Displayed template.
   */
  public function historyAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $tesDb = new TestsAccess();
    $tests = $this->enMan->getRepository('CoconoutBackendBundle:TestsAccessHistory')->getHistoryTests($id);
    return $this->render('CoconoutBackendBundle:TestsAccessHistory:history.html.php', array('tests' => $tests, 'ent' => $tesDb,
    'testRow' => $this->enMan->getRepository('CoconoutBackendBundle:TestsAccess')->find($id))); 
  }

}