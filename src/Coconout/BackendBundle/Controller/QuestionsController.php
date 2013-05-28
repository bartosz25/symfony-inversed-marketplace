<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager;
use Ad\QuestionsBundle\Form\Write;
use Ad\QuestionsBundle\Entity\AdsQuestions;

class QuestionsController extends BackendController
{

  /**
   * List questions.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $questions = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')->getCompletList(array('date' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('ques'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Questions:list.html.php', array('questions' => $questions, 'pager' => $pager->setPages(), 'isSuccess' => $flashSess->getFlash('questionResult'),
    'ticket' => $this->sessionTicket)); 
  }

  /**
   * Edit question.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $adqEnt = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')->find($id); //new AdsQuestions();
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $adqEnt->setQuestionTitle($flashData['questionTitle']);
	  $adqEnt->setQuestionText($flashData['questionText']);
    }
    AdsQuestions::setSessionToken($this->sessionTicket);
    $adqEnt->setTicket($this->sessionTicket);
    $formEdit = $this->createForm(new Write(), $adqEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEdit->bindRequest($request);
      $data = $request->request->all('Write');
      if($formEdit->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $this->enMan->createQueryBuilder()->update('Ad\QuestionsBundle\Entity\AdsQuestions', 'aq')
          ->set('aq.questionTitle', '?1')
          ->set('aq.questionText', '?2')
          ->where('aq.id_aq = ?3')
          ->setParameter(1, $data['Write']['questionTitle'])
          ->setParameter(2, $data['Write']['questionText'])
          ->setParameter(3, $id)
          ->getQuery()
          ->execute();

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('editSuccess', 1);
        }
        catch(Exception $e)
        {
          $this->enMan->getConnection()->rollback();
          $this->enMan->close();
          throw $e;
        }
      }
      else
      {
        $flashSess->setFlash('formData', $data['Write']);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
      }
      return $this->redirect($this->generateUrl('questionsEdit', array('id' => $id)));
    }
    return $this->render('CoconoutBackendBundle:Questions:edit.html.php', array('form' => $formEdit->createView(), 'formErrors' => $flashSess->getFlash('formErrors'),
    'id' => $id, 'isSuccess' => $flashSess->getFlash('editSuccess')));
  }

  /**
   * Delete tag.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    if($this->validateCSRF() === true)
    {
      $id = (int)$request->attributes->get('id');
      $flashSess = $request->getSession();

      // count replies for this question
      $repliesCount = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')->countReplies($id);

      // get ad for this reply
      $adData = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')->getQuestionNormal($id);

      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // remove question
        $this->enMan->createQueryBuilder()->delete('Ad\QuestionsBundle\Entity\AdsQuestions', 'aq')
        ->where('aq.id_aq = ?1')
        ->setParameter(1, $id)
        ->getQuery()
        ->execute();

        // update questions and replies counters
        $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
        ->set('a.adQuestions', 'a.adQuestions - 1')
        ->set('a.adReplies', 'a.adReplies - '.$repliesCount)
        ->where('a.id_ad = ?1')
        ->setParameter(1, $adData['id_ad'])
        ->getQuery()
        ->execute();

        // decrement stats counter
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'ques');
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- '.$repliesCount, 'repl');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        $flashSess->setFlash('questionResult', 2);
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
      return $this->redirect($this->generateUrl('questionsList'));
    }
  }

}