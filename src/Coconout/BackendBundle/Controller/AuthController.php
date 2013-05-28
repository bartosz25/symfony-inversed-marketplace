<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Coconout\BackendBundle\Controller\BackendController; 
use Security\SaltCellar;

class AuthController extends BackendController
{

  /**
   * Login the user.
   * @return Displayed template.
   */
  public function loginAction(Request $request)
  {
// $cellar = new SaltCellar($this->saltData);
// $salt = $cellar->getSalt(date('Y-m-d'), time());
// $passSalt = sha1($cellar->setHash(array('salt' => $salt, 'mdp' => 'admin', 'login' => 'admin'), date('n', time())));
// echo $passSalt;
    // parent::checkPage('User\ProfilesBundle\Controller\ProfilesController::loginAction');
    $flashSess = $request->getSession();
    $message = '';
    return $this->render('CoconoutBackendBundle:Auth:login.html.php', array());
  }

  /**
   * Logout the user.
   * @return Displayed template.
   */
  public function logoutAction(Request $request)
  {
    $attributes = $this->user->getAttributes();
    // update last login time
    $this->enMan->getRepository('Coconout\BackendBundle\Entity\Admins')->updateLastLogin((int)$attributes['id']);
    $request->getSession()->invalidate();
    $this->container->get('security.context')->setToken($this->user->setLogout());
    return $this->redirect($this->generateUrl('login', array()));
  }

}