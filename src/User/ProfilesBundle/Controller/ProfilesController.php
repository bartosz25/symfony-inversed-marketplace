<?php
namespace User\ProfilesBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController; 
use User\ProfilesBundle\Form\Register;
use User\ProfilesBundle\Form\Forgotten;
use User\ProfilesBundle\Form\ForgottenNew;
use User\ProfilesBundle\Form\EditPassword;
use User\ProfilesBundle\Form\EditEmail;
use User\ProfilesBundle\Form\EditCard;
use User\ProfilesBundle\Form\Login;
use User\ProfilesBundle\Entity\Users;
use User\ProfilesBundle\Entity\UsersCodes;
use Message\MessagesBundle\Form\Write;
use Message\MessagesBundle\Entity\MessagesContents;
use Security\SaltCellar;
use Security\Authentication;
use Others\Pager;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class ProfilesController extends FrontController
{

  /**
   * Register action. 
   * @return Displayed template.
   */
  public function registerAction(Request $request)
  {
    parent::checkPage('User\ProfilesBundle\Controller\ProfilesController::registerAction');
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $usersEnt = new Users();
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    Users::$em = $this->enMan;
    Users::$isText = $this->isTest;
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $usersEnt->setLogin($flashData['Register']['login']);
      $usersEnt->setEmail($flashData['Register']['email']);
    }
    $formRegister = $this->createForm(new Register(), $usersEnt);
    $formRegister->setData($usersEnt);
    if($request->getMethod() == 'POST') {
      $formRegister->bindRequest($request);
      if($formRegister->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $data = $request->request->all('Register');
          // make salt password
          $cellar = new SaltCellar($this->saltData);
          $salt = $cellar->getSalt(date('Y-m-d'));
          $passSalt = $cellar->setHash(array('salt' => $salt, 'mdp' => $data['Register']['pass1'], 'login' => $data['Register']['login']), date('n'));
          // set new user 
          $usersEnt->setRegisterUser(array('login' => $data['Register']['login'], 'password' => sha1($passSalt),
          'email' => $data['Register']['email'], 'registeredDate' => new \DateTime(), 
          'lastLogin' => new \DateTime(), 'userIp' => '', 'userType' => 0, 'aboAds' => 0, 'userNotes' => 0, 'userNotesQuantity' => 0, 'aboCats' => 0,
          'userFriends' => 0, 'userMessagesSystem' => 0, 'userMessages' => 0, 'userNewMessages' => 0, 'userAds' => 0, 'userOffers' => 0, 'userCatalogues' => 0, 
          'userOrders' => 0, 'userEbayLogin' => '', 'userPrestashopStore' => '', 'userAddresses' => 0, 'userActivityType' => 0, 'userState' => 0, 'userProfile' => ''
          ));
          $this->enMan->persist($usersEnt);
          $this->enMan->flush();
          // set new user code
          $activateCode = sha1(time().$_SERVER['REMOTE_ADDR'].rand(0,11111));
          $codesEnt = new UsersCodes();
          $codesEnt->setNewCode(array('user' => $usersEnt, 'code' => $activateCode, 'type' => 1));
          $this->enMan->persist($codesEnt);
          $this->enMan->flush();
          
          // get e-mail template, parse and send it
          $emtEnt = new EmailsTemplates;
          $tplVals = array('{URL}');
          $realVals = array($this->generateUrl('registerConfirm', array('code' => $activateCode), true));
          $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/register.maildoc'));
          $message = \Swift_Message::newInstance()
          ->setSubject("Confirmation de l'enregistrement")
          ->setFrom($this->from['mail'])
          ->setTo($data['Register']['email'])
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
          $this->get('mailer')->send($message);
          $flashSess->setFlash('registerSuccess', 1);
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          if($this->isTest)
          {
            return new Response('registered_successfully');
          }
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
        $flashSess->setFlash('formData', $request->request->all('Register'));
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formRegister));
      }
      return $this->redirect($this->generateUrl('register'));
    }
    return $this->render('UserProfilesBundle:Profiles:register.html.php', array('form' => $formRegister->createView(), 'formErrors' => $flashSess->getFlash('formErrors'),
    'isSuccess' => $flashSess->getFlash('registerSuccess')));
  }

  /**
   * Confirm register action. 
   * @return Displayed template.
   */
  public function confirmAction(Request $request)
  {
    parent::checkPage('User\ProfilesBundle\Controller\ProfilesController::confirmAction');
    $code = $request->attributes->get('code');
    if(ctype_alnum($code))
    {
      $codeRow = $this->enMan->getRepository('UserProfilesBundle:UsersCodes')->getUser($code, 1);
      if(($result = count($codeRow) > 0))
      {
        // make user account to active
        $qb = $this->enMan->createQueryBuilder();
        $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userState', 1)
        ->where('u.id_us = ?1')
        ->setParameter(1, (int)$codeRow[0]['id_us'])
        ->getQuery();
        $p = $q->execute();
        // remove activation code
        $q = $qb->delete('User\ProfilesBundle\Entity\UsersCodes', 'uc')
        ->where('uc.user = ?1')
        ->setParameter(1, (int)$codeRow[0]['id_us'])
        ->getQuery();
        $p = $q->execute();
        // update stats
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'user');
        // send activation mail
        $emtEnt = new EmailsTemplates;
        $template = file_get_contents(rootDir.'mails/activated.maildoc');
        $message = \Swift_Message::newInstance()
        ->setSubject("Activation du compte")
        ->setFrom($this->from['mail'])
        ->setTo($codeRow[0]['email'])
        ->setContentType("text/html") 
        ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
        $this->get('mailer')->send($message);
      }
    }
    return $this->render('UserProfilesBundle:Profiles:confirm.html.php', array('codeValid' => (bool)$result));
  }

  /**
   * Send a new password.
   * @return Displayed template.
   */
  public function forgottenAction(Request $request)
  {
    parent::checkPage('User\ProfilesBundle\Controller\ProfilesController::forgottenAction');
    $flashSess = $request->getSession();
    $usersEnt = new Users();
    Users::$em = $this->enMan;
    $postData = $flashSess->getFlash('formData');
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $usersEnt->setEmail($flashData['Forgotten']['email']);
    }
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    $formForgotten = $this->createForm(new Forgotten(), $usersEnt);
    if($request->getMethod() == 'POST') {
      $formForgotten->bindRequest($request);
      if($formForgotten->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $data = $request->request->all('Forgotten');
          // get user by mail
          $userRow = $this->enMan->getRepository('User\ProfilesBundle\Entity\Users')->findOneBy(array('email' => $data['Forgotten']['email'], 'userState' => 1));
          // insert recovery code
          $recoveryCode = sha1(time().rand(0,111111).$userRow->getLogin());
          // set new user code
          $codesEnt = new UsersCodes();
          $codesEnt->setNewCode(array('user' => $userRow, 'code' => $recoveryCode, 'type' => 2));
          $this->enMan->persist($codesEnt);
          $this->enMan->flush();
          // get e-mail template, parse and send it
          $emtEnt = new EmailsTemplates;
          $tplVals = array('{URL}');
          $realVals = array($this->generateUrl('forgottenConfirm', array('code' => $recoveryCode), true));
          $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/forgotten.maildoc'));
          $message = \Swift_Message::newInstance()
          ->setSubject("Rappel des identifiants")
          ->setFrom($this->from['mail'])
          ->setTo($userRow->getEmail())
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
          $this->get('mailer')->send($message);
          $flashSess->setFlash('forgottenSuccess', 1);
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
        $flashSess->setFlash('formData', $request->request->all('Forgotten'));
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formForgotten));
      }
      return $this->redirect($this->generateUrl('forgottenCredentials'));
    }
    return $this->render('UserProfilesBundle:Profiles:forgotten.html.php', array('form' => $formForgotten->createView(),
    'formErrors' => $flashSess->getFlash('formErrors'), 'isSuccess' => $flashSess->getFlash('forgottenSuccess')));
  }

  /**
   * Send a new password.
   * @return Displayed template.
   */
  public function forgottenNewAction(Request $request)
  {
    parent::checkPage('User\ProfilesBundle\Controller\ProfilesController::forgottenNewAction');
    $code = $request->attributes->get('code');
    $flashSess = $request->getSession();
    $usersEnt = new Users();
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    $formForgotten = $this->createForm(new ForgottenNew(), $usersEnt);
    if(ctype_alnum($code))
    {
      $codeRow = $this->enMan->getRepository('UserProfilesBundle:UsersCodes')->getUser($code, 2);
      if(($result = count($codeRow) > 0))
      {
        if($request->getMethod() == 'POST') {
          $formForgotten->bindRequest($request);
          if($formForgotten->isValid())
          {
            // start SQL transaction
            $this->enMan->getConnection()->beginTransaction();
            try
            {
              $time = strtotime($codeRow[0]['registeredDate']);
              $data = $request->request->all('ForgottenNew'); 
              // make salt password
              $cellar = new SaltCellar($this->saltData);
              $salt = $cellar->getSalt(date('Y-m-d', $time));
              $passSalt = $cellar->setHash(array('salt' => $salt, 'mdp' => $data['ForgottenNew']['pass1'], 'login' => $codeRow[0]['login']), date('n', $time));
              $qb = $this->enMan->createQueryBuilder();
              // remove activation code
              $q = $qb->delete('User\ProfilesBundle\Entity\UsersCodes', 'uc')
              ->where('uc.user = ?1')
              ->setParameter(1, (int)$codeRow[0]['id_us'])
              ->getQuery();
              $p = $q->execute();
              // set new password
              $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
              ->set('u.password', '?1')
              ->where('u.id_us = ?2')
              ->setParameter(1, sha1($passSalt))
              ->setParameter(2, (int)$codeRow[0]['id_us'])
              ->getQuery();
              $p = $q->execute(); 
              $flashSess->setFlash('forgottenNewSuccess', 1);
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
            $flashSess->setFlash('formData', $request->request->all('ForgottenNew'));
            $flashSess->setFlash('formErrors', $this->getAllFormErrors($formForgotten));
          }
          return $this->redirect($this->generateUrl('forgottenConfirm', array('code' => $code)));
        }
      }
    }
    return $this->render('UserProfilesBundle:Profiles:forgottenNew.html.php', array('form' => $formForgotten->createView(),
    'code' => $code, 'formErrors' => $flashSess->getFlash('formErrors'), 'isSuccess' => $flashSess->getFlash('forgottenNewSuccess'), 'result' => $result));
  }

  /**
   * Login the user.
   * @return Displayed template.
   */
  public function loginAction(Request $request)
  {
    parent::checkPage('User\ProfilesBundle\Controller\ProfilesController::loginAction');
    $flashSess = $request->getSession();
    $all = $flashSess->all();
    $loginErrors = array();
    if(isset($all['_security.last_error']) && $all['_security.last_error']->getMessage() != null)
    {
      $loginErrors[] = 'notCorrect';
    }
    $modifiedPassword = (int)$flashSess->get('editPasswordSuccess', -1);
    $flashSess->remove('editPasswordSuccess');
    $errorTxt = $flashSess->get('autherror');
    $flashSess->remove('autherror'); 
    $message = '';
    return $this->render('UserProfilesBundle:Profiles:login.html.php', array( 
    'modifiedPassword' => $modifiedPassword, 'message' => $errorTxt,
    'formErrors' => $loginErrors));
  }

  /**
   * Logout the user.
   * @return Displayed template.
   */
  public function logoutAction(Request $request)
  {
    $validCSRF = $this->validateCSRF();
    if($validCSRF === true)
    {
      $attributes = $this->user->getAttributes();
      // update last login time
      $this->enMan->getRepository('User\ProfilesBundle\Entity\Users')->updateLastLogin((int)$attributes['id']);
      $request->getSession()->invalidate();
      $this->container->get('security.context')->setToken($this->user->setLogout());
      return $this->redirect($this->generateUrl('login', array()));
    }
    return $this->redirect($this->generateUrl('badElement', array()));
  }

  /**
   * Displays account landing page.
   * @return Displayed template.
   */
  public function accountAction(Request $request) 
  {
    return $this->render('UserProfilesBundle:Profiles:account.html.php', array('user' => $this->container->get('security.context')->getToken()));
  }

  /**
   * Edit user's password. If the request is submitted and executed correctly, the user is redirected to
   * login page.
   * @return Displayed template or redirection to login page.
   */
  public function editPasswordAction(Request $request) 
  {
    $userLogin = $this->container->get('security.context')->getToken()->getUser();
    $flashSess = $request->getSession();
    $usersEnt = new Users();
    Users::$em = $this->enMan;
    Users::$staticLogin = $userLogin;
    Users::$saltData = $this->saltData;
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    $formEditPassword = $this->createForm(new EditPassword(), $usersEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEditPassword->bindRequest($request);
      if($formEditPassword->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $data = $request->request->all('EditPassword');
          // get user by login
          $userRow = $this->enMan->getRepository('User\ProfilesBundle\Entity\Users')->findBy(array('login' => $userLogin));
          $regDate = strtotime($userRow[0]->getRegisteredDate()); 
          // make salt password
          $cellar = new SaltCellar($this->saltData);
          $salt = $cellar->getSalt($userRow[0]->getRegisteredDate());
          $passSalt = sha1($cellar->setHash(array('salt' => $salt, 'mdp' => $data['EditPassword']['pass1'], 'login' => $userRow[0]->getLogin()), date('n', $regDate)));
          $qb = $this->enMan->createQueryBuilder();
          // set new password
          $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.password', '?1')
          ->where('u.id_us = ?2')
          ->setParameter(1, $passSalt)
          ->setParameter(2, (int)$userRow[0]->getIdUs())
          ->getQuery();
          $p = $q->execute();
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $request->getSession()->invalidate();
          $this->container->get('security.context')->setToken(null);
          // if password was changed, redirect to login page, notify about the changement and logout the user
          $flashSess->set('editPasswordSuccess', 1);
          return $this->redirect($this->generateUrl('login', array()));
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
        $flashSess->setFlash('formData', $request->request->all('EditPassword'));
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEditPassword));
        $flashSess->setFlash('editPasswordSuccess', 0);
      }
      return $this->redirect($this->generateUrl('accountPassword', array()));
    }
    return $this->render('UserProfilesBundle:Profiles:editPassword.html.php', array('user' => $this->container->get('security.context')->getToken(),
    'form' => $formEditPassword->createView(), 'formErrors' => $flashSess->getFlash('formErrors', array()), 'isSuccess' => $flashSess->getFlash('editPasswordSuccess', -1)));
  }

  /**
   * Edit user's e-mail.
   * @return Displayed template or redirection.
   */
  public function editEmailAction(Request $request) 
  {
    $flashSess = $request->getSession();
    $usersEnt = new Users();
    Users::$em = $this->enMan;
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    if(count($userData = $flashSess->getFlash('formData')) > 0)
    {
      $usersEnt->setEmail($userData['EditEmail']['email']);
    }
    else
    {
      $userAttr = $this->user->getAttributes();
      $usersEnt->setEmail($userAttr['email']);
    }
    $formEditEmail = $this->createForm(new EditEmail(), $usersEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEditEmail->bindRequest($request);
      if($formEditEmail->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $userAttr = $this->user->getAttributes();
          $data = $request->request->all('EditEmail');
          $qb = $this->enMan->createQueryBuilder();
          // set new password
          $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.email', '?1')
          ->where('u.id_us = ?2')
          ->setParameter(1, $data['EditEmail']['email'])
          ->setParameter(2, (int)$userAttr['id'])
          ->getQuery();
          $p = $q->execute();
          // commit SQL transaction
          $this->enMan->getConnection()->commit(); 
          $flashSess->setFlash('editEmailSuccess', 1);   
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
        $flashSess->setFlash('formData', $request->request->all('EditEmail'));
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEditEmail));
        $flashSess->setFlash('editEmailSuccess', 0);
      }
      return $this->redirect($this->generateUrl('accountEmail', array()));
    }
    return $this->render('UserProfilesBundle:Profiles:editEmail.html.php', array('user' => $this->container->get('security.context')->getToken(),
    'form' => $formEditEmail->createView(), 'formErrors' => $flashSess->getFlash('formErrors', array()), 'isSuccess' => $flashSess->getFlash('editEmailSuccess', -1)));
  }

  /**
   * Edit user's card.
   * @return Displayed template or redirection.
   */
  public function editCardAction(Request $request) 
  {
    $flashSess = $request->getSession();
    $userAttr = $this->user->getAttributes();
    $data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser((int)$userAttr['id']);
    $usersEnt = new Users();
    $usersEnt->setUserProfile($data['userProfile']);
    $usersEnt->setUserType($data['userType']);
    $usersEnt->setUserEbayLogin($data['userEbayLogin']);
    $usersEnt->setUserPrestashopStore($data['userPrestashopStore']);
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    $formEditCard = $this->createForm(new EditCard(), $usersEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEditCard->bindRequest($request);
      if($formEditCard->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $userAttr = $this->user->getAttributes();
          $data = $request->request->all('EditCard');
          $qb = $this->enMan->createQueryBuilder();
          // set new password
          $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.userProfile', '?1')
          ->set('u.userType', '?2')
          ->set('u.userEbayLogin', '?3')
          ->set('u.userPrestashopStore', '?4')
          ->where('u.id_us = ?5')
          ->setParameter(1, $data['EditCard']['userProfile'])
          ->setParameter(2, (int)$data['EditCard']['userType'])
          ->setParameter(3, $data['EditCard']['userEbayLogin'])
          ->setParameter(4, $data['EditCard']['userPrestashopStore'])
          ->setParameter(5, (int)$userAttr['id'])
          ->getQuery();
          $p = $q->execute();
          // commit SQL transaction
          $this->enMan->getConnection()->commit(); 
          $flashSess->setFlash('editCardSuccess', 1);   
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
        $flashSess->setFlash('formData', $request->request->all('EditCard'));
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEditCard));
        $flashSess->setFlash('editCardSuccess', 0);
      }
      return $this->redirect($this->generateUrl('accountCard', array()));
    }

    return $this->render('UserProfilesBundle:Profiles:editCard.html.php', array('user' => $this->container->get('security.context')->getToken(),
    'form' => $formEditCard->createView(), 'formErrors' => $flashSess->getFlash('formErrors', array()), 'isSuccess' => $flashSess->getFlash('editCardSuccess', -1)));
  }

  /**
   * Show access denied page.
   * @return Displayed template.
   */
  public function deniedAccessAction(Request $request)
  {
    $response = $this->render('UserProfilesBundle:Profiles:deniedAccess.html.php', array());
    $response->setStatusCode(404);
    return $response;
  }

  /**
   * Show users list.
   * @return Displayed template.
   */
  public function usersListAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $users = $this->enMan->getRepository('UserProfilesBundle:Users')
    ->getUsersList(array(
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)));
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('user'),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('UserProfilesBundle:Profiles:usersList.html.php', array('users' => $users, 'pager' => $pager->setPages()));
  }

  /**
   * Show user profile.
   * @return Displayed template.
   */
  public function showProfileAction(Request $request)
  {
    $flashSess = $request->getSession();
    $id = (int)$request->attributes->get('id');
    $user = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($id);
    $useEnt = new Users;
    $catalogues = array();
    if((int)$user['userCatalogues'] > 0)
    {
      $catalogues = $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array('start' => 0,
      'maxResults' => $this->config['pager']['maxResults']), $id);
    }
    $formView = null;
    $logins = array();
    $ids = array();
    if(($isConnected = parent::checkIfConnected()))
    {
      $userAttr = $this->user->getAttributes();
      if($user["id_us"] != $userAttr["id"])
      {
 	    $logins = array($user['login'], '');
        $ids = array($user['id_us'], '');
      }
// TODO : gestion du formulaire après la soumission
      $mecEnt = new MessagesContents;
      if($flashSess->getFlash('formData') != null)
      {
        $errorData = $flashSess->getFlash('formData');
        $mecEnt->setRecipersList($errorData['Write']['recipersList']);
        $mecEnt->setRecipersLogins($errorData['Write']['recipersLogins']);
	    $mecEnt->setContentTitle($errorData['Write']['contentTitle']);
	    $mecEnt->setContentMessage($errorData['Write']['contentMessage']);
        $logins = explode(';', $errorData['Write']['recipersLogins']);
        $ids = explode(';', $errorData['Write']['recipersList']);
      }
      else
      {
        $mecEnt->setRecipersList($user['id_us'].';');
        $mecEnt->setRecipersLogins($user['login'].';');
      }
      $mecEnt->setIsProfile($user['id_us']);
      $formAdd = $this->createForm(new Write(), $mecEnt);
      $formView = $formAdd->createView();
    }
    return $this->render('UserProfilesBundle:Profiles:userProfile.html.php', array('user' => $user, 'types' => $useEnt->getUserTypesAliases(), 'averages' => $useEnt->getAveragesAliases(),
    'catalogues' => $catalogues, 'isConnected' => $isConnected, 'form' => $formView, 'maxRecipers' => $this->config['messages']['maxRecipers'],
    'messageSuccess' => (int)$flashSess->getFlash('successSend'), 'messageNotices' => (array)$flashSess->getFlash('formNotices'),
    'messageError' => (int)$flashSess->getFlash('messageError'), 'messageErrors' => (array)$flashSess->getFlash('messageErrors'),
    'logins' => $logins, 'ids' => $ids, "ticket" => $this->sessionTicket));
  }

  /**
   * Page when user tries to get an element which isn't yours.
   * @access public
   * @return Displayed template.
   */
  public function badElementAction(Request $request)
  {
    $response = $this->render('UserProfilesBundle:Profiles:badElement.html.php', array());
    $response->setStatusCode(404);
    return $response;
  }

}