<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager; 
use Catalogue\OffersBundle\Form\AddCatalogue;
use Catalogue\OffersBundle\Entity\Catalogues;

class CataloguesController extends BackendController
{

  /**
   * List catalogues.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $catalogues = $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getAllCatBackend(array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('cata'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Catalogues:list.html.php', array('catalogues' => $catalogues, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket));    
  }

  /**
   * Edit catalogue.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $catEnt = new Catalogues;
    $catEnt->setDataAdded($this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getOneCatalogueData($id));
    if($catEnt->getIdCat() != '')
    {
      if(count($flashData = $flashSess->getFlash('formData')) > 0)
      {
        $catEnt->setDataAdded($flashData['AddCatalogue']);
      }
      $data = $request->request->all('AddCatalogue');
      Catalogues::setSessionToken($this->sessionTicket);
      $catEnt->setTicket($this->sessionTicket);
      // form fields list => form submitted
      $formEdit = $this->createForm(new AddCatalogue, $catEnt);
      if($request->getMethod() == 'POST') 
      {
        $formEdit->bindRequest($request); 
        if($formEdit->isValid())
        {
          // start SQL transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            // textual data edited (update Ad entity)
            $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Catalogues', 'c')
            ->set('c.catalogueName', '?1')
            ->set('c.catalogueDesc', '?2')
            ->where('c.id_cat = ?3')
            ->setParameter(1, $data['AddCatalogue']['catalogueName'])
            ->setParameter(2, $data['AddCatalogue']['catalogueDesc'])
            ->setParameter(3, $id)
            ->getQuery();
            $p = $q->execute();

            // remove cache files
            $this->cacheManager->cleanDirCache('catalogues/'.$id.'/');

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
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('formErrors', $this->getAllFormErrors($formAdd));
        } 
        return $this->redirect($this->generateUrl('cataloguesEdit', array('id' => $id)));
      }
      return $this->render('CoconoutBackendBundle:Catalogues:edit.html.php', array('edit' => true, 'add' => false, 'form' => $formEdit->createView(),
      'formErrors' => (array)$flashSess->getFlash('formErrors'), 'catalogueId' => $id,
      'isSuccess' => (int)$flashSess->getFlash('editSuccess')));
    }
  }

}