<?php
namespace Order\OrdersBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Others\Pager;

class OrdersCommentsController extends FrontController
{
 
  /**
   * Gets order list by user.
   * @access public
   * @return Displayed template.
   */
  public function listByOrderAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $id = (int)$request->attributes->get('id');
    $userAttr = $this->user->getAttributes();
    // first, check if can access into this page
    $order = $this->enMan->getRepository('OrderOrdersBundle:Orders')->getOrderByUsers($id, (int)$userAttr['id']);
    if(isset($order['id_ad']) && $order['id_ad'] == $id)
    {
      // get order comments and make the pagination
      $comments = $this->enMan->getRepository('OrderOrdersBundle:OrdersComments')->getCommentsByOrder(array(
        'date' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'],
        'start' => $this->config['pager']['perPage']*($page-1)
        ), $id
      );
      $pager = new Pager(array('before' => $this->config['pager']['before'],
                   'after' => $this->config['pager']['after'], 'all' => $order['orderComments'],
                   'page' => $page, 'perPage' => $this->config['pager']['perPage']
      ));   
      return $this->render('OrderOrdersBundle:OrdersComments:commentsList.html.php', array('commentsList' => $comments, 'pager' => $pager->setPages(), 'id' => $id));
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

}