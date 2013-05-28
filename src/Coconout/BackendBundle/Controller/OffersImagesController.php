<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager;
use Others\Image;
use Frontend\FrontBundle\Helper\FrontendHelper;

class OffersImagesController extends BackendController
{

  /**
   * List offers images.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $images = $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->getAllDatabaseImages(array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('ofim'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:OffersImages:list.html.php', array('images' => $images, 'pager' => $pager->setPages(), 'deleteSuccess' => (int)$flashSess->getFlash('deleteSuccess'),
    'ticket' => $this->sessionTicket));
  }

  /**
   * Edit one image.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $imageRow = $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->getOneImage($id);
    $flashSess = $request->getSession();
    if($request->getMethod() == 'POST') 
    {
      $frontendHelper = new FrontendHelper();
      $options = array('maxSize' => $this->config['images']['maxSize'], 'extensions' => $this->config['images']['extensions'], 
        'files' => array('imageName'),
        'required' => array(false),
        'isUploadify' => false,
        'names' => array($imageRow[0]['imageName']));
      $imgClass = new Image($options, $frontendHelper);
      if(!$imgClass->hasErrors)
      {
        $fileOptions = array('directory' => $this->config['images']['offersDir'].'/'.(int)$imageRow[0]['id_of'].'/',
          'alias' => $this->config['images']['configuration']['offer']['prefix'],
          'dimensions' => $this->config['images']['configuration']['offer']['dims'],
          'ratio' => $this->config['images']['configuration']['offer']['ratio'],
          'thumbs' => true);
        $imgClass->uploadFiles($fileOptions);
        $flashSess->setFlash('editSuccess', 1);
      }
      else
      {
        $values = array(implode(', ', $this->config['images']['extensions']), Tools::convertSize('b', 'mb', $options['maxSize']).' mb');
        $flashSess->setFlash('formErrors', array(str_replace($this->config['images']['variables'], $values, $this->config['images']['messages'][$imgClass->errors[0]['type']])));
      } 
      return $this->redirect($this->generateUrl('offersImgEdit', array('id' => $id)));
    }
    return $this->render('CoconoutBackendBundle:OffersImages:edit.html.php', array('formErrors' => (array)$flashSess->getFlash('formErrors'), 'imageId' => $id,
    'image' => $imageRow[0], 'isSuccess' => (int)$flashSess->getFlash('editSuccess')));
  }

  /**
   * Delete offer.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    if($this->validateCSRF())
    {
      $id = (int)$request->attributes->get('id');
      $flashSess = $request->getSession();
      $imageRow = $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->getOneImage($id);
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        $this->enMan->createQueryBuilder()->delete('Catalogue\ImagesBundle\Entity\OffersImages', 'oi')
        ->where('oi.id_oi = ?1')
        ->setParameter(1, $id)
        ->getQuery()
        ->execute();
        $dir = $this->config['images']['offersDir'].'/'.$imageRow[0]['id_of'].'/';
        foreach($this->config['images']['configuration']['offer']['prefix'] as $p => $prefix)
        {
          @unlink($dir.$prefix.$imageRow[0]['imageName']);
        }
        @unlink($dir.$imageRow[0]['imageName']);
        // update number of offer images
        $this->enMan->createQueryBuilder()
        ->update('Catalogue\OffersBundle\Entity\Offers', 'o')
        ->set('o.offerImages', 'o.offerImages - 1')
        ->where('o.id_of = ?1')
        ->setParameter(1, (int)$imageRow[0]['id_of'])
        ->getQuery()
        ->execute();
        $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'ofim');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        $flashSess->setFlash('deleteSuccess', 1);
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        $flashSess->setFlash('deleteSuccess', 2);
      }
      return $this->redirect($this->generateUrl('offersImgList', array()));
    }
  }

}