<?php
namespace Ad\QuestionsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController; 
use Ad\QuestionsBundle\Form\Write;
use Ad\QuestionsBundle\Form\Reply;
use Ad\QuestionsBundle\Entity\AdsQuestions;
use Message\MessagesBundle\Entity\MessagesContents;
use Message\MessagesBundle\Entity\Messages;
use Others\Pager;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates; 


class QuestionsController extends FrontController
{

  /**
   * Shows form to write the question.
   * @access public
   * @return Displayed template
   */
  public function writeAction(Request $request)
  {
    $attr = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $adqEnt = new AdsQuestions();
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 1 && $testResult == 0)
    {
      $attr = array('id' => (int)$request->attributes->get('elUser1'));
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attr = array('id' => (int)$request->attributes->get('user'));
    }
    // Check if ad is actives and user is not the author of this ad
    if($id == 0 || $this->enMan->getRepository('AdItemsBundle:Ads')->isCorrectAd($id, (int)$attr['id']) == false)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      return $this->redirect($this->generateUrl('badElement'));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 1), 200);
    }
    $this->enMan->getConnection()->beginTransaction();
    try
    {
      $url = array('id' => $id, 'url' => $request->attributes->get('url'), 'category' => $request->attributes->get('category'));
      if($flashSess->getFlash('formData') != null)
      {
        $data = $flashSess->getFlash('formData');
        $adqEnt->setQuestionTitle($data['Write']['questionTitle']);
	    $adqEnt->setQuestionText($data['Write']['questionText']);
      }
      AdsQuestions::setSessionToken($this->sessionTicket);
      $adqEnt->setTicket($this->sessionTicket);
      $formWrite = $this->createForm(new Write(), $adqEnt);
      if($request->getMethod() == 'POST') 
      {
        $formWrite->bindRequest($request);
        $data = $request->request->all('Write');
        if($formWrite->isValid())
        {
          $adRef = $this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $id);
          $autRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$attr['id']);
          // put new ad question
          $adqEnt->setQuestionAuthor($autRef);
          $adqEnt->setQuestionAd($adRef);
          $adqEnt->setQuestionTitle($data['Write']['questionTitle']);
	      $adqEnt->setQuestionText($data['Write']['questionText']);
	      $adqEnt->setQuestionDate('');
	      $adqEnt->setQuestionState(1);
          $this->enMan->persist($adqEnt);
          $this->enMan->flush();
          
          // update ads questions quantity
          $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
          ->set('a.adQuestions', 'a.adQuestions + 1')
          ->where('a.id_ad = ?1')
          ->setParameter(1, $id)
          ->getQuery();
          $p = $q->execute();

          // send new system message
          $vars = array('{USER}', '{URL}');
          $urls = array($this->user->getUser(), $this->generateUrl('adsQuestionRead', array('id' => $adqEnt->getIdAq())));
          $template = file_get_contents(rootDir.'messages/new_ad_question.message');
          // Set author entity
          $author = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $this->adminId);
          // Add messages
          $messageVals = array(
            'title' => "Une nouvelle question concernant votre annonce",
            'content' => str_replace($vars, $urls, $template),
            'type' => 2,
            'state' => 1
          );
          $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($author, $autRef, $messageVals);

          // increment counter
          $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'ques');

          // send e-mail notification
          $emtEnt = new EmailsTemplates;
          $template = file_get_contents(rootDir.'mails/new_ad_question.maildoc');
          $parsedTpl = str_replace($vars, $urls, $template);
          $message = \Swift_Message::newInstance()
          ->setSubject("Une nouvelle question concernant votre annonce")
          ->setFrom($this->from['mail'])
          ->setTo($attr['email'])
          ->setContentType("text/html")
          ->setBody($parsedTpl);
          $this->get('mailer')->send($emtEnt->getHeaderTemplate().$message.$emtEnt->getFooterTemplate());

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('sendSuccess', 1);
        }
        else
        {
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('formErrors', $this->getAllFormErrors($formWrite));
          $flashSess->setFlash('sendError', 1);
        }
        return $this->redirect($this->generateUrl('adsQuestion', $url));
      }
      return $this->render('AdQuestionsBundle:Questions:write.html.php', array('form' => $formWrite->createView(),
      'url' => $url,
      'isSuccess' => (int)$flashSess->getFlash('sendSuccess'), 'isError' => (int)$flashSess->getFlash('sendError'), 'formErrors' => (array)$flashSess->getFlash('formErrors')));
    }
    catch(Exception $e)
    {
      $this->enMan->getConnection()->rollback();
      $this->enMan->close();
      throw $e;
    }
  }

  /**
   * Shows messages list.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $column = $request->attributes->get('column');
    $how = $request->attributes->get('how');
    $isPartial = $this->checkIfPartial();
    $userAttr = $this->user->getAttributes();
    $adqEnt = new AdsQuestions();
    $questions = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')
    ->getQuestionsList(array('date' => $this->config['sql']['dateFormat'],'column' => $column, 'how' => $how, 
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('AdItemsBundle:Ads')->countAllQuestions((int)$userAttr['id']),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdQuestionsBundle:Questions:questionsTable.html.php', array('questions' => $questions, 'pager' => $pager->setPages(),
      'questionStates' => $adqEnt->questionStates, 'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('titre', 'date', 'etat', 'auteur')), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdQuestionsBundle:Questions:list.html.php', array('questions' => $questions, 'pager' => $pager->setPages(),
    'questionStates' => $adqEnt->questionStates,  'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('titre', 'date', 'etat', 'auteur')), 'how' => $how, 'column' => $column));
  }

  /**
   * Reads question.
   * @access public
   * @return Displayed template
   */
  public function readAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession(); 
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
    $question = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')->getQuestion(array('date' => $this->config['sql']['dateFormat']), $id, (int)$userAttr['id']);
    if(isset($question['id_aq']) && $question['id_aq'] == $id && $validCSRF === true)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $adqEnt = new AdsQuestions();
      // Update data if new question
      if($question['questionState'] == 1)
      {
        // set state to read
        $q = $this->enMan->createQueryBuilder()->update('Ad\QuestionsBundle\Entity\AdsQuestions', 'aq')
        ->set('aq.questionState', 0)
        ->where('aq.id_aq = ?1')
        ->setParameter(1, $id)
        ->getQuery();
        $p = $q->execute();
      }
      return $this->render('AdQuestionsBundle:Questions:read.html.php', array('question' => $question,
      'states' => $adqEnt->questionStates, 'ticket' => $this->sessionTicket,
      'messageSuccess' => (int)$flashSess->getFlash('messageSuccess')));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  } 

  /**
   * Delete message.
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
    $question = $this->enMan->getRepository('AdQuestionsBundle:AdsQuestions')->getQuestionWithReply($id, (int)$userAttr['id']);
    $ret = array('isError' => 1, 'message' => '');
    if($validCSRF === true && isset($question['id_aq']) && $question['id_aq'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // decrement replies and questions number
        $nr = 0;
        if($question['id_ar'] != '')
        {
          $nr = 1;
          // decrement counter
          $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'repl');
        }
        $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
        ->set('a.adQuestions', 'a.adQuestions - 1')
        ->set('a.adReplies', 'a.adReplies - '.$nr)
        ->where('a.id_ad = ?1')
        ->setParameter(1, $question['id_ad'])
        ->getQuery();
        $p = $q->execute();

        $q = $this->enMan->createQueryBuilder()->delete('Ad\QuestionsBundle\Entity\AdsQuestions', 'aq')
        ->where('aq.id_aq = ?1')
        ->setParameter(1, $id)
        ->getQuery();
        $p = $q->execute();

        // decrement counter
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'ques');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();

        $ret['message'] = "La question a été correctement supprimée";
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
      $ret['message'] = "Vous n'avez pas le droit de supprimer cette question";
      $ret['isError'] = 1;      
    }
	echo json_encode($ret);
	die();
  }

}