<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager;
use Frontend\FrontBundle\Entity\Tags;
use Frontend\FrontBundle\Form\EditTag;

class TagsController extends BackendController
{

  /**
   * List tags.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $tags = $this->enMan->getRepository('FrontendFrontBundle:Tags')->getCompletList(array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('tags'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Tags:list.html.php', array('tags' => $tags, 'pager' => $pager->setPages(), 'isSuccess' => $flashSess->getFlash('tagResult'),
    'ticket' => $this->sessionTicket));    
  }

  /**
   * Add tag.
   * @access public
   * @return Displayed template.
   */
  public function addAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $tagsEnt = new Tags();
    Tags::$em = $this->enMan;
    Tags::setSessionToken($this->sessionTicket);
    $tagsEnt->setTicket($this->sessionTicket);
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $tagsEnt->setTagName($flashData['tagName']);
    }
    $formEdit = $this->createForm(new EditTag(), $tagsEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEdit->bindRequest($request);
      $data = $request->request->all('EditTag');
      if($formEdit->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $tagsEnt->setData(array('tagName' => $data['EditTag']['tagName'], 'tagAds' => 0, 'tagOffers' => 0));
          $this->enMan->persist($tagsEnt);
          $this->enMan->flush();

          $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'tags');

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('editSuccess', 2);
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
        $flashSess->setFlash('formData', $data['EditTag']);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
      }
      return $this->redirect($this->generateUrl('tagsAdd', array()));
    }
    return $this->render('CoconoutBackendBundle:Tags:edit.html.php', array('form' => $formEdit->createView(), 'formErrors' => $flashSess->getFlash('formErrors'),
    'id' => 0, 'add' => true, 'edit' => false, 'isSuccess' => $flashSess->getFlash('editSuccess')));
  }

  /**
   * Edit tag.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $tagData = $this->enMan->getRepository('FrontendFrontBundle:Tags')->find($id);
    $tagsEnt = new Tags();
    Tags::$em = $this->enMan;
    Tags::setSessionToken($this->sessionTicket);
    $tagsEnt->setTicket($this->sessionTicket);
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $tagData->setTagName($flashData['tagName']);
    }
    $formEdit = $this->createForm(new EditTag(), $tagData);
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
          $this->enMan->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Tags', 't')
          ->set('t.tagName', '?1')
          ->where('t.id_ta = ?2')
          ->setParameter(1, $data['EditTag']['tagName'])
          ->setParameter(2, $id)
          ->getQuery()
          ->execute();

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('editSuccess', 1);
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
        $flashSess->setFlash('formData', $data['EditTag']);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
      }
      return $this->redirect($this->generateUrl('tagsEdit', array('id' => $id)));
    }
    return $this->render('CoconoutBackendBundle:Tags:edit.html.php', array('form' => $formEdit->createView(), 'formErrors' => $flashSess->getFlash('formErrors'),
    'id' => $id, 'add' => false, 'edit' => true, 'isSuccess' => $flashSess->getFlash('editSuccess')));
  }

  /**
   * Delete tag.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    if($this->validateCSRF() === true)
    {
      $id = (int)$request->attributes->get('id');
      $flashSess = $request->getSession();
      // remove tag
      $this->enMan->createQueryBuilder()->delete('Frontend\FrontBundle\Entity\Tags', 't')
      ->where('t.id_ta = ?1')
      ->setParameter(1, $id)
      ->getQuery()
      ->execute();
      // decrement stats counter
      $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'tags');

      $flashSess->setFlash('tagResult', 2);
      return $this->redirect($this->generateUrl('tagsList'));
    }
  }

}