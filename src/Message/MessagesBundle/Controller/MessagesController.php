<?php
namespace Message\MessagesBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController; 
use Message\MessagesBundle\Form\Write;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Others\Pager;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates;


class MessagesController extends FrontController
{

  /**
   * Write a message. 
   * @return Displayed template.
   */
  public function sendAction(Request $request)
  {
    $this->enMan->getConnection()->beginTransaction();
    try
    {
      $flashSess = $request->getSession();
	  $maxReceivers = $this->config['messages']['maxRecipers'];
	  $maxMessages = $this->config['messages']['maxMessages'];
      $mcoClass = new MessagesContents;
      MessagesContents::setSessionToken($this->sessionTicket);
      $mcoClass->setTicket($this->sessionTicket);
      $formWrite = $this->createForm(new Write(), $mcoClass);
      if($request->getMethod() == 'POST') 
      {
        $msgError = array();
        $data = $request->request->all('Write');
        $attr = $this->user->getAttributes();
        $messageId = (int)$request->attributes->get('id');
        if($messageId > 0)
        {
          $message = $this->enMan->getRepository('MessageMessagesBundle:Messages')->getMessage(array('date' => $this->config['sql']['dateFormat']), $messageId, (int)$attr['id']);
          if(isset($message[0]['id_me']) && $message[0]['id_me'] == $messageId)
          {
            $data['Write']['recipersList'] = $message[0]['id_us'].';';
          }
          else
          {
            return $this->redirect($this->generateUrl('badElement'));
          }
        }
        $formWrite->bindRequest($request);
        if($formWrite->isValid())
        {
          $users = explode(';' , $data['Write']['recipersList']);
          // Check if receivers quantity is lower than allowed
          unset($users[count($users)-1]);
          if(count($users) > 0 && count($users) <= $maxReceivers) 
          {
            $usersList = $this->enMan->getRepository('UserProfilesBundle:Users')->getUsersByIds($users);
            $usersPm = array();
            $usersError = array();
            foreach($usersList as $u => $user)
            {
              if($user['userMessages'] < $maxMessages)
              {
                $usersPm[$user['id_us']] = $user;
              }
              else
              {
                $usersError[$u] = $user['login'];
              }
            }
            // Set author entity
            $author = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$attr['id']);
            // Add messages
	        $mecEnt = new MessagesContents;
	        $mecEnt->setContentAuthor($author);
	        $mecEnt->setContentTitle($data['Write']['contentTitle']);
	        $mecEnt->setContentMessage($data['Write']['contentMessage']);
            $mecEnt->setContentDate(new \DateTime());
            $mecEnt->setContentType(0);
            $this->enMan->persist($mecEnt);
            $this->enMan->flush();

            // Mail variables
            $vars = array('{USER}', '{URL}', '{URL_PM}');
            $template = file_get_contents(rootDir.'mails/new_message.maildoc');

            $usIds = array();
	        $mesDb = new Messages;
	        $emtEnt = new EmailsTemplates;
            foreach($usersPm as $p => $receiver)
            {
              $mesClone = clone $mesDb;
              $mesClone->setMessageContent($mecEnt);
              $mesClone->setMessageReciper($this->enMan->getReference('User\ProfilesBundle\Entity\Users', $receiver['id_us']));
              $mesClone->setMessageState(1);
              $this->enMan->persist($mesClone);
              $this->enMan->flush();
              $usIds[] = $receiver['id_us'];
 
              $urls = array($this->user->getUser(), $this->generateUrl('messageRead', array('id' => $mesClone->getIdMe())), $this->generateUrl('messagesList', array()));
              $parsedTpl = str_replace($vars, $urls, $template);
              $message = \Swift_Message::newInstance()
              ->setSubject("Un nouveau message")
              ->setFrom($this->from['mail'])
              ->setTo($receiver['email'])
              ->setContentType("text/html")
              ->setBody($emtEnt->getHeaderTemplate().$parsedTpl.$emtEnt->getFooterTemplate());
              $this->get('mailer')->send($message);
            }
            if(count($usersPm) > 0)
            {
              // Update messages and new messages counters
              $qb = $this->enMan->createQueryBuilder();
              $qb->update('UserProfilesBundle:Users', 'u')
              ->set('u.userMessages', 'u.userMessages + 1')
             ->set('u.userNewMessages', 'u.userNewMessages + 1');
              $qb->add('where', $qb->expr()->in('u.id_us', $usIds));
              $query = $qb->getQuery()->execute();
            } 
            // Detect if some of choosen users didn't receive the message because of limit overtaking
            if(count($usersError) > 0)
            {
              $msgError[] = "Il n'a pas été envoyé à ".implode(",", $usersError)." car ils ont dépassé le nombre 
              maximal des messages.";
            }
            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            $flashSess->setFlash('successSend', 1);
            $flashSess->setFlash('formNotices', $msgError);
          }
          elseif(count($users) == 0)
          {
            // Not receivers specified
            $flashSess->setFlash('formData', $data);
            $flashSess->setFlash('messageError', 1);
            $flashSess->setFlash('messageErrors', array('Vous devez préciser les destinataires'));
          }
          else
          {
            // Too much receivers
            $flashSess->setFlash('formData', $data);
            $flashSess->setFlash('messageError', 1);
            $flashSess->setFlash('messageErrors', array('Vous avez dépassé le nombre de destinataires simultanés ('.$maxReceivers.' utilisateurs)'));
          }
        }
        else
        {
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('messageErrors', $this->getAllFormErrors($formWrite));
          $flashSess->setFlash('messageError', 1);
          return $this->redirect($this->generateUrl('messageWrite', array('id' => $messageId)));
        }
        // Make differents link (according to referrer)
        if((int)$data['Write']['isProfile'] > 0)
        {
          $userData = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser((int)$data['Write']['isProfile']);
          return $this->redirect($this->generateUrl('userProfile', array('url' => $userData['login'], 'id' => $userData['id_us'])));
        }
        else
        {
          if($messageId == 0)
          {
            return $this->redirect($this->generateUrl('messageWrite', array('id' => 0)));
          }
          else
          {
            return $this->redirect($this->generateUrl('messageRead', array('id' => $messageId)).'?ticket='.$this->sessionTicket);
          }
        }
      }
    }
    catch(Exception $e)
    {
      $this->enMan->getConnection()->rollback();
      $this->enMan->close();
      throw $e;
    }
  }

  /**
   * Shows form to write the message.
   * @access public
   * @return Displayed template
   */
  public function writeAction(Request $request)
  {
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $userAttr = $this->user->getAttributes();
    $flashSess = $request->getSession(); 
    $mesEnt = new Messages();
    $mecEnt = new MessagesContents;
    $id = (int)$request->attributes->get('id');
    $titleBox = "Ecrire un message";
    if($id > 0)
    {
      if($isTest == 0)
      {
        $userAttr = $this->user->getAttributes();
      }
      elseif($isTest == 1 && $testResult == 0)
      {
        $userAttr = array('id' => (int)$request->attributes->get('user'));
      }
      elseif($isTest == 1 && $testResult == 1)
      {
        $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      }
      $message = $this->enMan->getRepository('MessageMessagesBundle:Messages')->getMessage(array('date' => $this->config['sql']['dateFormat']), $id, (int)$userAttr['id']);
      if(isset($message[0]['id_me']) && $message[0]['id_me'] == $id && $message[0]['contentType'] != $mecEnt->getSystemMessage())
      {
        // access tests case
        if($isTest == 1)
        {
          return new Response(parent::testAccess($testResult, 1), 200);
        }
        $mecEnt->setContentTitle('Re : '.$message[0]['contentTitle']);
        $mecEnt->setRecipersList($message[0]['id_us'].';');
        $mecEnt->setRecipersLogins($message[0]['login'].';');
      }
      elseif((!isset($message[0]['id_me']) || $message[0]['id_me'] != $id) && $message[0]['contentType'] != $mecEnt->getSystemMessage())
      {
        // access tests case
        if($isTest == 1)
        {
          return new Response(parent::testAccess($testResult, 0), 200);
        }
        return $this->redirect($this->generateUrl('badElement'));
      }
      elseif($message[0]['contentType'] == $mecEnt->getSystemMessage())
      {
        return $this->render('MessageMessagesBundle:Messages:replyNotAllowed.html.php');
      }
      $titleBox = "Répondre à un message";
    }
    $logins = array();
    $ids = array();
    if($flashSess->getFlash('formData') != null)
    {
      $errorData = $flashSess->getFlash('formData');
      $mecEnt->setContentTitle($errorData['Write']['contentTitle']);
	  $mecEnt->setContentMessage($errorData['Write']['contentMessage']);
      if($id == 0)
      { 
        $mecEnt->setRecipersList($errorData['Write']['recipersList']);
        $mecEnt->setRecipersLogins($errorData['Write']['recipersLogins']);
        $logins = explode(';', $errorData['Write']['recipersLogins']);
        $ids = explode(';', $errorData['Write']['recipersList']);
      }
    }
    elseif($id > 0 && $flashSess->getFlash('formData') == null)
    {
      // get logins by author ID
      $logins = array($message[0]['login'], '');
      $ids = array($message[0]['id_us'], '');
    }
    $mecEnt->setIsProfile(0);
    MessagesContents::setSessionToken($this->sessionTicket);
    $mecEnt->setTicket($this->sessionTicket);
    $formAdd = $this->createForm(new Write(), $mecEnt);
    $formView = $formAdd->createView();
    return $this->render('MessageMessagesBundle:Messages:write.html.php', array('form' => $formAdd->createView(), 'maxRecipers' => $this->config['messages']['maxRecipers'],
    'messageSuccess' => (int)$flashSess->getFlash('successSend'), 'messageNotices' => (array)$flashSess->getFlash('formNotices'),
    'messageError' => (int)$flashSess->getFlash('messageError'), 'messageErrors' => (array)$flashSess->getFlash('messageErrors'),
    'logins' => $logins, 'titleBox' => $titleBox, 'ids' => $ids, 'messageId' => $id, 'isReply' => (bool)($id > 0)));
  }
 

  /**
   * Shows messages list.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $mesEnt = new Messages();
    $mecEnt = new MessagesContents();
    $messages = $this->enMan->getRepository('MessageMessagesBundle:Messages')
    ->getMessagesListByUser(array('date' => $this->config['sql']['dateFormat'], 'how' => $how, 'column' => $column,
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['messages'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('MessageMessagesBundle:Messages:messagesTable.html.php', array('messages' => $messages, 'page' => $page, 'pager' => $pager->setPages(),
      'aliases' => $mesEnt->messagesAliases, 'types' => $mecEnt->typesAliases, 'ticket' => $this->sessionTicket,
      'class' => $helper->getClassesBySorter($how, $column, array('titre', 'auteur', 'date', 'etat', 'type')), 'how' => $how, 'column' => $column));
    }
    return $this->render('MessageMessagesBundle:Messages:list.html.php', array('messages' => $messages, 'page' => $page, 'pager' => $pager->setPages(),
    'aliases' => $mesEnt->messagesAliases, 'types' => $mecEnt->typesAliases, 'ticket' => $this->sessionTicket,
    'class' => $helper->getClassesBySorter($how, $column, array('titre', 'auteur', 'date', 'etat', 'type')), 'how' => $how, 'column' => $column));
  }

  /**
   * Reads message.
   * @access public
   * @return Displayed template
   */
  public function readAction(Request $request)
  {
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession(); 
    $validCSRF = $this->validateCSRF();
    // $userAttr = $this->user->getAttributes();
    $mesEnt = new Messages();
    $mecEnt = new MessagesContents();
    if($isTest == 0)
    {
      $userAttr = $this->user->getAttributes();
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $validCSRF = true;
    }
    $message = $this->enMan->getRepository('MessageMessagesBundle:Messages')->getMessage(array('date' => $this->config['sql']['dateFormat']), $id, (int)$userAttr['id']);
    if($validCSRF === true && isset($message[0]['id_me']) && $message[0]['id_me'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // Update data if new message
      if($message[0]['messageState'] == 1)
      {
        // set state to read
        $q = $this->enMan->createQueryBuilder()->update('Message\MessagesBundle\Entity\Messages', 'm')
        ->set('m.messageState', 0)
        ->where('m.id_me = ?1')
        ->setParameter(1, $id)
        ->getQuery();
        $p = $q->execute();
        
        // decrement number of unread messages
        $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userNewMessages', 'u.userNewMessages - 1')
        ->where('u.id_us = ?1')
        ->setParameter(1, (int)$userAttr['id'])
        ->getQuery();
        $p = $q->execute();
      }
      return $this->render('MessageMessagesBundle:Messages:read.html.php', array('message' => $message[0],
      'aliases' => $mesEnt->messagesAliases, 'types' => $mecEnt->typesAliases,
      'messageSuccess' => (int)$flashSess->getFlash('successSend'), 'canReply' => (bool)($message[0]['contentType'] != $mecEnt->getSystemMessage())));
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
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $id = (int)$request->attributes->get('id');
    $userAttr = $this->user->getAttributes();
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
    $mesEnt = new Messages();
    $mecEnt = new MessagesContents();
    $message = $this->enMan->getRepository('MessageMessagesBundle:Messages')->getMessage(array('date' => $this->config['sql']['dateFormat']), $id, (int)$userAttr['id']);
    $ret = array('isError' => 1, 'message' => '');
    if($validCSRF === true && isset($message[0]['id_me']) && $message[0]['id_me'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // Update data if new message
        $userNewMsg = 0;
        if($message[0]['messageState'] == 1)
        {  
          // decrement number of unread messages
          $userNewMsg = 1;
        }
        // Decrement system or normal messages
        $field = 'userMessagesSystem';
        if($message[0]['contentType'] == 0)
        {
          $field = 'userMessages';
        }
        $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userNewMessages', 'u.userNewMessages - '.$userNewMsg.'')
        ->set('u.'.$field, 'u.'.$field.' - 1')
        ->where('u.id_us = ?1')
        ->setParameter(1, (int)$userAttr['id'])
        ->getQuery();
        $p = $q->execute();

        // Delete from messages
        $q = $this->enMan->createQueryBuilder()->delete('Message\MessagesBundle\Entity\Messages', 'm')
        ->where('m.id_me = ?1')
        ->setParameter(1, $id)
        ->getQuery();
        $p = $q->execute();

        // If content has no more message, delete content too
        if(!$this->enMan->getRepository('MessageMessagesBundle:Messages')->hasMessages($message[0]['id_mc']))
        {
          $q = $this->enMan->createQueryBuilder()->delete('Message\MessagesBundle\Entity\MessagesContents', 'mc')
          ->where('mc.id_mc = ?1')
          ->setParameter(1, $message[0]['id_mc'])
          ->getQuery();
          $p = $q->execute();
        }

        // commit SQL transaction
        // $this->enMan->getConnection()->commit();

        $ret['message'] = "Le message a été supprimé";
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
      $ret['message'] = "Vous n'avez pas le droit de supprimer ce message";
      $ret['isError'] = 1;
    }
	echo json_encode($ret);
	die();
  }

}