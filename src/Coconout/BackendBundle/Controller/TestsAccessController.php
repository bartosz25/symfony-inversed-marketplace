<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Coconout\BackendBundle\Entity\TestsAccess;
use Coconout\BackendBundle\Entity\TestsAccessHistory;
use Frontend\FrontBundle\Entity\EmailsTemplates;
// TODO : tests pour suppression d'une annonce, d'une offre et d'un catalogue

class TestsAccessController extends BackendController
{

  /**
   * List access tests.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $result = '';
	if(trim($flashSess->getFlash('resultTest')) != '')
    {
      $result = (int)$flashSess->getFlash('resultTest');
    }
    $tesDb = new TestsAccess();
    $tests = $this->enMan->getRepository('CoconoutBackendBundle:TestsAccess')->getAllTests();
    return $this->render('CoconoutBackendBundle:TestsAccess:list.html.php', array('tests' => $tests, 'ent' => $tesDb, 'result' => $result)); 
  }

  /**
   * Execute access test.
   * @access public
   * @return Displayed template.
   */
  public function executeAction(Request $request)
  {
    $flashSess = $request->getSession();
    $id = (int)$request->attributes->get('id');
    $testData = $this->enMan->getRepository('CoconoutBackendBundle:TestsAccess')->find($id);
    $config = $testData->getTestsConfig();//print_r($config);
    // get elements
    $method = $config['method'];
    $row = array('id' => 0, 'user1' => 0, 'user2' => 0);
    if($config['entity'] != '')
    {
      $row = $this->enMan->getRepository($config['entity'])->$method();//print_r($row);
    }

    // get other user id
    $userRow = $this->enMan->getRepository('UserProfilesBundle:Users')->getNotLike($row['user1'], $row['user2']);//print_r($userRow);

    // determine the final result (0 => if error returned, 1 => if true returned)
    $finalResult = rand(0, 1);
    $params = array(
      'id'  => $row['id'],
      'id2'  => (int)$row['id2'],
      'test'  => 1,
      'result'  => $finalResult,
      'elUser1'  => $row['user1'],
      'elUser2'  => $row['user2'],
      'user'  => $userRow[0]['id_us']
    );
// print_r($params);echo $config['action'];
    $response = $this->forward($config['action'], $params);   
 // var_dump($response->getContent()); 
  // die();

    // start SQL transaction
    $this->enMan->getConnection()->beginTransaction();
    try
    {
      // insert result into test_access_history table
      $tahDb = new TestsAccessHistory();
      $tahDb->setData(array('testsAccess' => $testData, 'historyParams' => serialize($params), 'historyDate' => '', 'historyResult' => $response->getContent(), 'historyTestedResult' => $finalResult));
      $this->enMan->persist($tahDb);
      $this->enMan->flush();
      // update test_access table with the last result
      $testData->setTestsLastExecution('');
      $testData->setTestsLastResult($tahDb->getHistoryResult());
      $this->enMan->persist($testData);
      $this->enMan->flush();

      $flashSess->setFlash('resultTest', $tahDb->getHistoryResult());
      if($tahDb->getHistoryResult() == 0)
      {
        // prevent admin about this
        $message = \Swift_Message::newInstance()
        ->setSubject("Error during access tests")
        ->setFrom($this->from['mail'])
        ->setTo($this->from['mail'])
        ->setContentType("text/html")
        ->setBody("Error occured for the test : ".$tahDb->getIdTah());
        $this->get('mailer')->send($message);
      }

      // commit SQL transaction
      $this->enMan->getConnection()->commit();
    }
    catch(Exception $e)
    {
      $this->enMan->getConnection()->rollback();
      $this->enMan->close();
      throw $e;
    }
    return $this->redirect($this->generateUrl('accessTests'));
  }

}