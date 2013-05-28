<?php
namespace User\FriendsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController; 
use User\FriendsBundle\Entity\UsersFriends;
use User\ProfilesBundle\Entity\Users;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Others\Pager;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class ContactsController extends FrontController
{

  /**
   * Invite action. 
   * @return JSON message.
   */
  public function inviteAction(Request $request)
  {
    if($this->validateCSRF() === true)
    {
      $this->enMan->getConnection()->beginTransaction();
      try
      {
	    // if they are already friends
	    // if user is active
        $attr = $this->user->getAttributes();
	    $id = (int)$request->attributes->get('user');
	    $userId = (int)$attr['id']; 
        // check if the users are already in contact and if the invited user has activated account
	    $areContacts = $this->enMan->getRepository('UserFriendsBundle:UsersFriends')->areContacts($id, $userId, false);
	    $isActiveMail = $this->enMan->getRepository('UserProfilesBundle:Users')->isActive($id); 
	    if($id > 0 && !$areContacts && $isActiveMail != '' && $userId != $id) 
	    {
          $user1Ref = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $userId);
          $user2Ref = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $id);
	      // add into database
          $usfEnt = new UsersFriends;
          $usfEnt->setUsersIdUs1($user1Ref);
          $usfEnt->setUsersIdUs2($user2Ref);
          $usfEnt->setFriendState(0);
          $this->enMan->persist($usfEnt);
          $this->enMan->flush();

          // send Private Message
          $vars = array('{URL_ACCEPT}', '{URL_DENY}');
	      $urls = array($this->generateUrl('contactConfirm', array('typeAction' => 'accepter', 'user2' => $id, 'user1' => $userId)),
	      $this->generateUrl('contactConfirm', array('typeAction' => 'refuser', 'user2' => $id, 'user1' => $userId)));
	      $tpl = file_get_contents(rootDir.'messages/invitation.message');
	      $parsedTpl = str_replace($vars, $urls, $tpl);
          $messageVals = array(
            'title' => $this->user->getUser()." vous invite à rejoindre ses amis",
            'content' => $parsedTpl,
            'type' => 1,
            'state' => 1
          );
          $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($user1Ref, $user2Ref, $messageVals);

	      // send e-mail notification
	      $emtEnt = new EmailsTemplates;
          $template = str_replace($vars, $urls, file_get_contents(rootDir.'mails/invitation.maildoc'));
          $message = \Swift_Message::newInstance()
          ->setSubject("Une nouvelle invitation")
          ->setFrom($this->from['mail'])
          ->setTo($isActiveMail)
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
          $this->get('mailer')->send($message);
          $ret['result'] = 1;
          $ret['message'] = "Votre invitation a été correctement envoyée.";
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
	    }
	    elseif($id > 0 && $isActiveMail == '')
	    {    
	      $ret['result'] = 0;
	      $ret['message'] = "Vous ne pouvez pas envoyer l'invitation à l'utilisateur inactif.";
  	    }
	    elseif($userId == $id)
 	    {
	      $ret['result'] = 0;
	      $ret['message'] = "Vous ne pouvez pas envoyer l'invitation à vous-même.";
	    } 
	    else
  	    {
	      $ret['result'] = 0;
	      $ret['message'] = "Une invitation a déjà été envoyée à cet utilisateur.";
  	    }
	    echo json_encode($ret);
	    die();
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    echo json_encode(array('result' => 0, "message" => "Votre session a expiré"));
    die();
  }

  /**
   * Accepts or denies a contact invitation.
   * @access public
   * @return JSON message.
   */
  public function acceptOrDenyAction(Request $request)
  {
    $user1 = (int)$request->attributes->get('user1');
    $user2 = (int)$request->attributes->get('user2');
    $attr = $this->user->getAttributes();
    $userId = (int)$attr['id']; 
    $action = $request->attributes->get('typeAction');
    $strict = true;
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userId =  (int)$request->attributes->get('user');
      $user1 = (int)$request->attributes->get('elUser1');
      $user2 = (int)$request->attributes->get('elUser2');
      $strict = false;
      $action = 'accepter';
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userId = (int)$request->attributes->get('elUser2');
      $user1 = (int)$request->attributes->get('elUser1');
      $user2 = (int)$request->attributes->get('elUser2');
      $strict = false;
      $action = 'accepter';
      $validCSRF = true;
    }
    $isInvitation = $this->enMan->getRepository('UserFriendsBundle:UsersFriends')->isInvitation($user1, $user2, $strict);   
    if($validCSRF === true && $isInvitation && $userId == $user2 && ($action == 'accepter' || $action == 'refuser')) 
	{
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try
      {	    
	    if($action == 'accepter') 
	    { 
	      // set status to accepted
          $q = $this->enMan->createQueryBuilder()->update('User\FriendsBundle\Entity\UsersFriends', 'uf')
          ->set('uf.friendState', 1)
          ->where('uf.users_id_us_1 = ?1 AND uf.users_id_us_2 = ?2')
          ->setParameter(1, $user1)
          ->setParameter(2, $user2)
          ->getQuery();
          $p = $q->execute();

 		  // increase friends quantity
          $q = $this->enMan->createQueryBuilder()->update('User\FriendsBundle\Entity\UsersFriends', 'uf')
          ->set('uf.friendState', 1)
          ->where('uf.users_id_us_1 = ?1 AND uf.users_id_us_2 = ?2')
          ->setParameter(1, $user1)
          ->setParameter(2, $user2)
          ->getQuery();
          $p = $q->execute();

          $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.userFriends', 'u.userFriends + 1')
          ->where('u.id_us = ?1 OR u.id_us = ?2')
          ->setParameter(1, $user1)
          ->setParameter(2, $user2)
          ->getQuery();
          $p = $q->execute();

		  // send e-mail notification
		  $user1Data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($user1);
		  $user2Data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($user2);
          $vars = array('{LOGIN}');
	      $urls = array($user2Data['login']);
          $template = str_replace($vars, $urls, file_get_contents(rootDir.'mails/invitation_accepted.maildoc'));
          $messageSwift = \Swift_Message::newInstance()
          ->setSubject("Invitation acceptée")
          ->setFrom($this->from['mail'])
          ->setTo($user1Data['email'])
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate()); 

		  $message = "L'invitation a été acceptée.";
		  $result = 1;
	    }
	    elseif($action == 'refuser')
	    {
          // delete invitation
          $q = $this->enMan->createQueryBuilder()->delete('User\FriendsBundle\Entity\UsersFriends', 'uf')
          ->where('uf.users_id_us_1 = ?1 AND uf.users_id_us_2 = ?2')
         ->setParameter(1, $user1)
         ->setParameter(2, $user2)
          ->getQuery();
          $p = $q->execute(); 

		  // send e-mail notification
		  $emtEnt = new EmailsTemplates;
		  $user1Data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($user1);
		  $user2Data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($user2);
          $vars = array('{LOGIN}');
	      $urls = array($user2Data['login']);
          $template = str_replace($vars, $urls, file_get_contents(rootDir.'mails/invitation_denied.maildoc'));
          $messageSwift = \Swift_Message::newInstance()
          ->setSubject("Invitation refusée")
          ->setFrom($this->from['mail'])
          ->setTo($user1Data['email'])
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate()); 

		  $message = "L'invitation a été refusée.";
		  $result = 1;
	    }
        $this->get('mailer')->send($messageSwift);
        // delete private message (if exists)
        $messageRow = $this->enMan->getRepository('MessageMessagesBundle:Messages')->getInviteMessage($user1, $user2);
        if(count($messageRow) > 0)
        {
          $q = $this->enMan->createQueryBuilder()->delete('Message\MessagesBundle\Entity\Messages', 'm')
          ->where('m.id_me = ?1')
          ->setParameter(1, $messageRow[0]['id_me'])
          ->getQuery();
          $p = $q->execute();
          $q = $this->enMan->createQueryBuilder()->delete('Message\MessagesBundle\Entity\MessagesContents', 'mc')
          ->where('mc.id_mc = ?1')
          ->setParameter(1, $messageRow[0]['id_mc'])
          ->getQuery();
          $p = $q->execute();
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
    }
	else 
	{
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
	  $result = 0;  
	  $message = "L'invitation que vous demandez, n'existe pas ou a déjà été traitée.";
    }
	if((int)$request->attributes->get('json') == 1) {
       $ret['result'] = $result;
	   $ret['message'] = $message;
	   $ret['id'] = $user1.'-'.$user2;
	   echo json_encode($ret);
	   die();
    }
    return $this->render('UserFriendsBundle:Contacts:acceptOrDeny.html.php', array('message' => $message, 'result' => $result));
  }
  
  /**
   * Show user's contacts to administrate.
   * @return Displayed template
   */
  public function showUserContactsAction(Request $request)
  {
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $isInsert = 0;
    if(isset($_GET['insert']))
    {
      $isInsert = (int)$_GET['insert'];
    }
    $page = (int)$request->attributes->get('page');
    $userAttr = $this->user->getAttributes();
    $users = $this->enMan->getRepository('UserFriendsBundle:UsersFriends')
    ->getUserContacts(array('column' => $column, 'how' => $how, 'login' => $this->user->getUsername(),
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'], 'between' => $this->config['pager']['between'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['friends'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('UserFriendsBundle:Contacts:showUserContacts.html.php', array('isInsert' => $isInsert, 'connected' => $userAttr['id'], 'users' => $users, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    return $this->render('UserFriendsBundle:Contacts:showUserContacts.html.php', array('isInsert' => $isInsert, 'connected' => $userAttr['id'], 'users' => $users, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
  }

  /**
   * Delete a contact. 
   * @return JSON message.
   */
  public function deleteAction(Request $request)
  {
	// if they are already friends
	// if user is active
    $attr = $this->user->getAttributes();
	$user1 = (int)$request->attributes->get('user1');
	$user2 = (int)$request->attributes->get('user2');
	$userId = (int)$attr['id'];
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userId =  (int)$request->attributes->get('user');
      $user1 = (int)$request->attributes->get('elUser1');
      $user2 = (int)$request->attributes->get('elUser2');
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userId = (int)$request->attributes->get('elUser1');
      $user1 = (int)$request->attributes->get('elUser1');
      $user2 = (int)$request->attributes->get('elUser2');
      $validCSRF = true;
    }
    $ret = array();
	$ret['isError'] = 1;
    // check if the users are already in contact and if the invited user has activated account
	$areContacts = $this->enMan->getRepository('UserFriendsBundle:UsersFriends')->areContacts($user1, $user2, true);
	if($validCSRF === true && $areContacts && ($userId == $user1 || $userId == $user2)) 
	{
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // delete contacts
        $q = $this->enMan->createQueryBuilder()->delete('User\FriendsBundle\Entity\UsersFriends', 'uf')
        ->where('uf.users_id_us_1 = ?1 AND uf.users_id_us_2 = ?2')
        ->setParameter(1, $user1)
        ->setParameter(2, $user2)
        ->getQuery();
        $p = $q->execute();

        $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userFriends', 'u.userFriends - 1')
        ->where('u.id_us = ?1 OR u.id_us = ?2')
        ->setParameter(1, $user1)
        ->setParameter(2, $user2)
        ->getQuery();
        $p = $q->execute();

		$ret['message'] = "Le contact a été correctement supprimé.";
		$ret['isError'] = 0; 
        // commit SQL transaction
        $this->enMan->getConnection()->commit();
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
	  $ret['message'] = "Une erreur s'est produite.";
  	}
	echo json_encode($ret);
	die();
  }

}