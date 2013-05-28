<?php
namespace Ad\QuestionsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Ad\QuestionsBundle\Form\Reply;
use Ad\QuestionsBundle\Entity\AdsQuestions; 
use Ad\QuestionsBundle\Entity\AdsReplies; 
use Message\MessagesBundle\Entity\MessagesContents;
use Message\MessagesBundle\Entity\Messages;
use Others\Pager;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class RepliesController extends FrontController
{

  /**
   * Replies to the question.
   * @access public
   * @return Displayed template
   */
  public function replyAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession(); 
    $userAttr = $this->user->getAttributes();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    // $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      // $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      // $validCSRF = true;
    }
    $question = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')->getQuestion(array('date' => $this->config['sql']['dateFormat']), $id, (int)$userAttr['id']);
    if(/*$validCSRF === true && */isset($question['id_aq']) && $question['id_aq'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $aqrEnt = new AdsReplies();
      if(($flashData = $flashSess->getFlash('formData')))
      {
        $aqrEnt->setReplyText($flashData['Reply']['replyText']);
      }
      AdsReplies::setSessionToken($this->sessionTicket);
      $aqrEnt->setTicket($this->sessionTicket);
      $replyForm = $this->createForm(new Reply(), $aqrEnt);
      if($request->getMethod() == 'POST')
      {
        $replyForm->bindRequest($request);
        $data = $request->request->all('Reply');
        if($replyForm->isValid())
        {
          // start SQL transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            // add reply to ad
            if($data['Reply']['replyType'] == 0)
            {
              $questionRef = $this->enMan->getReference('Ad\QuestionsBundle\Entity\AdsQuestions', $id);
              $aqrEnt->setReplyQuestion($questionRef);
              $aqrEnt->setReplyText($data['Reply']['replyText']);
              $aqrEnt->setReplyDate('');
              $this->enMan->persist($aqrEnt);
              $this->enMan->flush();

              $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
              ->set('a.adReplies', 'a.adReplies + 1')
              ->where('a.id_ad = ?1')
              ->setParameter(1, $question['id_ad'])
              ->getQuery();
              $p = $q->execute();

              // increment counter
              $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'repl');
            }
            elseif($data['Reply']['replyType'] == 1)
            {
              $helper = new FrontendHelper();
              $vars = array('{AD}', '{AD_URL}', '{QUESTION}', '{REPLY}');
              $values = array($question['adName'], $this->generateUrl('adsShowOne', array('category' => $question['categoryUrl'], 'url' => $helper->makeUrl($question['adName']), 'id' => $question['id_ad'])), $question['questionTitle'], $data['Reply']['replyText']);
              // Set author entity
              $author = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
              // Add messages
              $messageVals = array(
                'title' => "En réponse à votre question ".$question['questionTitle'],
                'content' => str_replace($vars, $values, file_get_contents(rootDir.'messages/question_reply.message')),
                'type' => 2,
                'state' => 1
              );
              $mesDb = $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($author, $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $question['id_us']), $messageVals);

              // Send e-mail notification
              $emtEnt = new EmailsTemplates;
              $vars = array('{USER}', '{URL}', '{URL_PM}');
              $template = file_get_contents(rootDir.'mails/new_message.maildoc');
              $urls = array($this->user->getUser(), $this->generateUrl('messageRead', array('id' => $mesDb->getIdMe())), $this->generateUrl('messagesList', array()));
              $parsedTpl = str_replace($vars, $urls, $template);
              $message = \Swift_Message::newInstance()
              ->setSubject("La réponse à votre question")
              ->setFrom($this->from['mail'])
              ->setTo($question['email'])
              ->setContentType("text/html")
              ->setBody($emtEnt->getHeaderTemplate().$parsedTpl.$emtEnt->getFooterTemplate());
              $this->get('mailer')->send($message);
            }

            $q = $this->enMan->createQueryBuilder()->update('Ad\QuestionsBundle\Entity\AdsQuestions', 'aq')
            ->set('aq.questionState', '2')
            ->where('aq.id_aq = ?1')
            ->setParameter(1, $id)
            ->getQuery();
            $p = $q->execute();

            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            $flashSess->setFlash('messageSuccess', 1);
            return $this->redirect($this->generateUrl('adsQuestionRead', array('id' => $id)).'?ticket='.$this->sessionTicket);
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
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('formErrors', $this->getAllFormErrors($replyForm));
          $flashSess->setFlash('messageError', 1);
        }
      }
      return $this->render('AdQuestionsBundle:Replies:reply.html.php', array('edit' => 0, 'form' => $replyForm->createView(), 'id' => $id,
      'messageSuccess' => 0, 'ticket' => $this->sessionTicket, 'formErrors' => (array)$flashSess->getFlash('formErrors', array()), 'messageError' => (int)$flashSess->getFlash('messageError', -1)));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Lists replies.
   * @access public
   * @return Displayed template
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $column = $request->attributes->get('column');
    $how = $request->attributes->get('how');
    $isPartial = $this->checkIfPartial();
    $userAttr = $this->user->getAttributes();
    $replies = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')
    ->getRepliesList(array('date' => $this->config['sql']['dateFormat'],'column' => $column, 'how' => $how, 
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('AdItemsBundle:Ads')->countAllReplies((int)$userAttr['id']),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdQuestionsBundle:Replies:repliesTable.html.php', array('replies' => $replies, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('question_titre', 'annonce_nom', 'date')), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdQuestionsBundle:Replies:list.html.php', array('replies' => $replies, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('question_titre', 'annonce_nom', 'date')), 'how' => $how, 'column' => $column));
  }

  /**
   * Edites a reply.
   * @access public
   * @return Displayed template
   */
  public function editAction(Request $request)
  {
    try
    {
      $id = (int)$request->attributes->get('id');
      $userAttr = $this->user->getAttributes();
      $flashSess = $request->getSession(); 
      $isTest = (int)$request->attributes->get('test');
      $testResult = (int)$request->attributes->get('result');
      if($isTest == 1 && $testResult == 0)
      {
        $userAttr = array('id' => (int)$request->attributes->get('user'));
      }
      elseif($isTest == 1 && $testResult == 1)
      {
        $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      }
      $reply = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')->getReply($id, (int)$userAttr['id']);
      if(isset($reply['id_ar']) && $reply['id_ar'] == $id)
      {
        // access tests case
        if($isTest == 1)
        {
          return new Response(parent::testAccess($testResult, 1), 200);
        }
        $aqrEnt = new AdsReplies();
        if(($flashData = $flashSess->getFlash('formData')))
        {
          $aqrEnt->setReplyText($flashData['Reply']['replyText']);
        }
        else
        {
          $aqrEnt->setReplyText($reply['replyText']);
        }
        AdsReplies::setSessionToken($this->sessionTicket);
        $aqrEnt->setTicket($this->sessionTicket);
        $replyForm = $this->createForm(new Reply(), $aqrEnt);
        if($request->getMethod() == 'POST')
        {
          $replyForm->bindRequest($request);
          $data = $request->request->all('Reply');
          if($replyForm->isValid())
          {
            // start SQL transaction
            $this->enMan->getConnection()->beginTransaction();
            // edit reply
            $q = $this->enMan->createQueryBuilder()->update('Ad\QuestionsBundle\Entity\AdsReplies', 'ar')
            ->set('ar.replyText', '?1')
            ->where('ar.id_ar = ?2')
            ->setParameter(1, $data['Reply']['replyText'])
            ->setParameter(2, $id)
            ->getQuery();
            $p = $q->execute();

            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            $flashSess->setFlash('messageSuccess', 1);
          }
          else
          {
            $flashSess->setFlash('formData', $data);
            $flashSess->setFlash('formErrors', $this->getAllFormErrors($replyForm));
            $flashSess->setFlash('messageError', 1);
          }
          return $this->redirect($this->generateUrl('repliesEdit', array('id' => $id)));
      }
      return $this->render('AdQuestionsBundle:Replies:reply.html.php', array('edit' => 1, 'form' => $replyForm->createView(), 'id' => $id,
      'ticket' => $this->sessionTicket, 'messageSuccess' => $flashSess->getFlash('messageSuccess'), 'formErrors' => (array)$flashSess->getFlash('formErrors'), 'messageError' => (int)$flashSess->getFlash('messageError')));        
      }      
    }
    catch(Exception $e)
    {
      $this->enMan->getConnection()->rollback();
      $this->enMan->close();
      throw $e;
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Deletes a reply.
   * @access public
   * @return JSON message.
   */
  public function deleteAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $userAttr = $this->user->getAttributes();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $validCSRF = true;
    }
    $reply = $this->enMan->getRepository('AdQuestionsBundle:AdsReplies')->getReply($id, (int)$userAttr['id']);
    $ret = array('isError' => 1, 'message' => '');
    if($validCSRF === true && isset($reply['id_ar']) && $reply['id_ar'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // decrement replies for this ad
        $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
        ->set('a.adReplies', 'a.adReplies - 1')
        ->where('a.id_ad = ?1')
        ->setParameter(1, $reply['id_ad'])
        ->getQuery();
        $p = $q->execute();

        $q = $this->enMan->createQueryBuilder()->delete('Ad\QuestionsBundle\Entity\AdsReplies', 'ar')
        ->where('ar.id_ar = ?1')
        ->setParameter(1, $id)
        ->getQuery();
        $p = $q->execute();

        // decrement counter
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'repl');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();

        $ret['message'] = "La réponse a été supprimée";
        $ret['isError'] = 0;
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
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      $ret['message'] = "Vous n'avez pas le droit de supprimer cette réponse";
      $ret['isError'] = 1;      
    }
    echo json_encode($ret);
    die();
  }

}