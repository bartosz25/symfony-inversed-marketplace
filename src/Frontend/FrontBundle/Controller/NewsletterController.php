<?php
namespace Frontend\FrontBundle\Controller;

use Frontend\FrontBundle\Controller\FrontController;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends FrontController  {

  public function showTrackLogoAction(Request $request)
  {
    $trackNumber = $request->attributes->get('key');
    if(ctype_alnum($trackNumber))
    {
      $this->enMan->getRepository('UserProfilesBundle:UsersNewslettersHistory')->makeAsRead($trackNumber);
      ob_start();
      header ("Content-type: image/png");
      echo file_get_contents(rootDir.'web/images/cancel.png');
      die();
    }
  }

}