<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager; 
use User\ProfilesBundle\Entity\Users;
use User\ProfilesBundle\Form\EditUser;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class UsersController extends BackendController
{

  /**
   * List users.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $useEnt = new Users();
    $users = $this->enMan->getRepository('UserProfilesBundle:Users')->getCompletList(array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('user'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Users:list.html.php', array('users' => $users, 'pager' => $pager->setPages(), 'isSuccess' => $flashSess->getFlash('userResult'),
    'deletedState' => $useEnt->getDeletedState(), 'notActivatedState' => $useEnt->getNotActivatedState(),
    'ticket' => $this->sessionTicket));  
  }

  /**
   * Edit user description fields.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $userData = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($id);
    $usersEnt = new Users();
    Users::$em = $this->enMan;
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $usersEnt->setLogin($flashData['login']);
      $usersEnt->setEmail($flashData['email']);
      $usersEnt->setUserProfile($flashData['userProfile']);
    }
    else
    {
      $usersEnt->setLogin($userData['login']);
      $usersEnt->setEmail($userData['email']);
      $usersEnt->setUserProfile($userData['userProfile']);
    }
    Users::setSessionToken($this->sessionTicket);
    $uasEnt->setTicket($this->sessionTicket);
    $formEdit = $this->createForm(new EditUser(), $usersEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEdit->bindRequest($request);
      $data = $request->request->all('EditUser');
      if($formEdit->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.login', '?1')
          ->set('u.email', '?2')
          ->set('u.userProfile', '?3')
          ->where('u.id_us = ?4')
          ->setParameter(1, $data['EditUser']['login'])
          ->setParameter(2, $data['EditUser']['email'])
          ->setParameter(3, $data['EditUser']['userProfile'])
          ->setParameter(4, $id)
          ->getQuery()
          ->execute(); 
          
          // remove cache files
          $this->cacheManager->cleanDirCache('users/');

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('isSuccess', 1);
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
        $flashSess->setFlash('formData', $data['EditUser']);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
      }
      return $this->redirect($this->generateUrl('usersEdit', array('id' => $id)));
    }
    return $this->render('CoconoutBackendBundle:Users:edit.html.php', array('form' => $formEdit->createView(), 'formErrors' => $flashSess->getFlash('formErrors'),
    'id' => $id, 'isSuccess' => $flashSess->getFlash('editSuccess')));
  }

  /**
   * Delete user.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $validCSRF = $this->validateCSRF();
    $flashSess = $request->getSession();
    if($validCSRF === true)
    {
      // first, get user's offers and ads
      $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdsListByUser(array('maxResults' => 1000000, 'start' => 0), $id);
      $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOffersListByUser(array('maxResults' => 1000000, 'start' => 0), $id);

      return $this->render('CoconoutBackendBundle:Users:delete.html.php', array('ads' => $ads, 'offers' => $offers, 'id' => $id,
      'ticket' => $this->sessionTicket));
    }
  }


  /**
   * Delete user's account.
   * @access public
   * @return Displayed template.
   */
  public function deleteAccountAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $validCSRF = $this->validateCSRF();
    if($validCSRF === true)
    {
      $useEnt = new Users();
      $userData = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($id);
      $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
      ->set('u.userState', $useEnt->getDeletedState())
      ->set('u.login', $this->config['deleted']['userDeleted'])
      ->where('u.id_us = ?1')
      ->setParameter(1, $id)
      ->getQuery()
      ->execute();
      // update stats
      $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'user');

      // remove cache files
      $this->cacheManager->cleanDirCache('users/');
 
      // send e-mail
      $templateMail = file_get_contents(rootDir.'mails/user_ban.maildoc');
      $emtEnt = new EmailsTemplates;
      $message = \Swift_Message::newInstance()
      ->setSubject("Votre compte a été supprimé")
      ->setFrom($this->from['mail'])
      ->setContentType("text/html")
      ->setTo($userData['email'])
      ->setBody($emtEnt->getHeaderTemplate().$templateMail.$emtEnt->getFooterTemplate());
      $this->get('mailer')->send($message);
      echo json_encode(array('success' => 1));
      die();
    }
  }

  /**
   * Send activation code.
   * @access public
   * @return Displayed template.
   */
  public function sendCodesAction(Request $request)
  {
    if($this->validateCSRF() === true)
    {
      $id = (int)$request->attributes->get('id');
      $flashSess = $request->getSession();
      $userData = $this->enMan->getRepository('UserProfilesBundle:UsersCodes')->getUserWithCode($id);
      // avoid backend.php in the URL, set app prefix
      $this->setBaseUrl($this->getRouteUrl()); 
      // send e-mail
      $emtEnt = new EmailsTemplates;
      $tplVals = array('{URL}');
      $realVals = array($this->generateUrl('registerConfirm', array('code' => $userData['code']), true));
      $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/register.maildoc'));
      $message = \Swift_Message::newInstance()
      ->setSubject("Confirmation de l'enregistrement")
      ->setFrom($this->from['mail'])
      ->setTo($userData['email'])
      ->setContentType("text/html")
      ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
      $this->get('mailer')->send($message);
      $flashSess->setFlash('userResult', 3);
      // return to coconout.php in the URL
      $this->setBaseUrl($this->getBackendUrl()); 
      return $this->redirect($this->generateUrl('usersList'));
    }
  }

  /**
   * Send activation code.
   * @access public
   * @return Displayed template.
   */
  public function activateAction(Request $request)
  {
    if($this->validateCSRF())
    {
      $id = (int)$request->attributes->get('id');
      $userData = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($id);

      $flashSess = $request->getSession();
      // make user account to active
      $this->enMan->createQueryBuilder()
      ->update('User\ProfilesBundle\Entity\Users', 'u')
      ->set('u.userState', 1)
      ->where('u.id_us = ?1')
      ->setParameter(1, $id)
      ->getQuery()
      ->execute();
      // remove activation code
      $this->enMan->createQueryBuilder()->delete('User\ProfilesBundle\Entity\UsersCodes', 'uc')
      ->where('uc.user = ?1')
      ->setParameter(1, $id)
      ->getQuery()
      ->execute();

      // remove cache files
      $this->cacheManager->cleanDirCache('users/');

      // send activation mail
      $emtEnt = new EmailsTemplates;
      $template = file_get_contents(rootDir.'mails/activated.maildoc');
      $message = \Swift_Message::newInstance()
      ->setSubject("Activation du compte")
      ->setFrom($this->from['mail'])
      ->setTo($userData['email'])
      ->setContentType("text/html")
      ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
      $this->get('mailer')->send($message);    
    
      $flashSess->setFlash('userResult', 4);
 
      return $this->redirect($this->generateUrl('usersList'));
    }
  }

}