<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager;
use Ad\QuestionsBundle\Form\Reply;
use Ad\QuestionsBundle\Entity\AdsReplies; 

class RepliesController extends BackendController
{

  /**
   * List replies.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $replies = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')->getCompletList(array('date' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('repl'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Replies:list.html.php', array('replies' => $replies, 'pager' => $pager->setPages(), 'isSuccess' => $flashSess->getFlash('replyResult'),
    'ticket' => $this->sessionTicket));    
  }

  /**
   * Edit reply.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $adrEnt = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')->find($id); //new AdsQuestions();
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
	  $adrEnt->setReplyText($flashData['replyText']);
    }
    AdsReplies::setSessionToken($this->sessionTicket);
    $adrEnt->setTicket($this->sessionTicket);
    $formEdit = $this->createForm(new Reply(), $adrEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEdit->bindRequest($request);
      $data = $request->request->all('Reply');
      if($formEdit->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $this->enMan->createQueryBuilder()->update('Ad\QuestionsBundle\Entity\AdsReplies', 'ar')
          ->set('ar.replyText', '?1')
          ->where('ar.id_ar = ?2')
          ->setParameter(1, $data['Reply']['replyText'])
          ->setParameter(2, $id)
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
        $flashSess->setFlash('formData', $data['Reply']);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
      }
      return $this->redirect($this->generateUrl('repliesEdit', array('id' => $id)));
    }
    return $this->render('CoconoutBackendBundle:Replies:edit.html.php', array('form' => $formEdit->createView(), 'formErrors' => $flashSess->getFlash('formErrors'),
    'id' => $id, 'isSuccess' => $flashSess->getFlash('editSuccess')));
  }

  /**
   * Delete reply.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    if($this->validateCSRF() === true)
    {
      $id = (int)$request->attributes->get('id');
      $flashSess = $request->getSession();

      // get reply
      $reply = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')->getReplyOne($id);

      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // remove reply
        $this->enMan->createQueryBuilder()->delete('Ad\QuestionsBundle\Entity\AdsReplies', 'ar')
        ->where('ar.id_ar = ?1')
        ->setParameter(1, $id)
        ->getQuery()
        ->execute();

        // update questions and replies counters
        $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
        ->set('a.adReplies', 'a.adReplies - 1')
        ->where('a.id_ad = ?1')
        ->setParameter(1, $reply['id_ad'])
        ->getQuery()
        ->execute();

        // decrement stats counter
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'repl');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();
       $flashSess->setFlash('replyResult', 2);
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
      return $this->redirect($this->generateUrl('repliesList'));
    }
  }

}