<?php
namespace Frontend\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ExceptionController extends Controller  
{
  /**
   * Array with all exceptions handled separetely.
   * @access protected
   * @var array
   */
  protected $_exceptions = array('Ad\ItemsBundle\AdNotFoundException' => 'FrontendFrontBundle:Exception:adNotFoundException.html.php',
  'Catalogue\OffersBundle\OfferNotFoundException' => 'FrontendFrontBundle:Exception:offerNotFoundException.html.php');

  public function handleExceptionAction(Request $request)
  {
    $template = 'FrontendFrontBundle:Exception:handleException.html.php';
    $params = array();
    $exception = $request->attributes->get('exception');
    foreach($this->_exceptions as $e => $exc)
    {
      if($exception->getClass() == $e)
      {
        $excClass = new $e('');
        $params = $excClass->getParameters($this->get('doctrine')->getEntityManager());
        $template = $exc;
        break;
      }
    }
    $response = $this->render($template, array('params' => $params));
    $response->setStatusCode(404);
    return $response;
  }

}