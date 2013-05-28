<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController; 

class IndexController extends BackendController
{

  /**
   * Show index page.
   * @return Displayed template.
   */
  public function indexAction(Request $request)
  {
    return $this->render('CoconoutBackendBundle:Index:index.html.php', array());
  }

}