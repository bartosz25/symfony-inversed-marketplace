<?php
namespace Catalogue\OffersBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Catalogue\OffersBundle\Form\AddCatalogue;
use Catalogue\OffersBundle\Entity\Catalogues;
use Others\Pager;
use User\ProfilesBundle\Entity\Users;
use Frontend\FrontBundle\Helper\FrontendHelper;

class CataloguesController extends FrontController
{

  /**
   * Add catalogue action. 
   * @access public
   * @return Displayed template.
   */
  public function addCatalogueAction(Request $request)
  {
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $catEnt = new Catalogues;
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $catEnt->setDataAdded($flashData['AddCatalogue']);
    }
    Catalogues::setSessionToken($this->sessionTicket);
    $catEnt->setTicket($this->sessionTicket);
    $formAdd = $this->createForm(new AddCatalogue(), $catEnt);
    if($request->getMethod() == 'POST') 
    {  
      $formAdd->bindRequest($request); 
      $data = $request->request->all('AddCatalogue');
      if($formAdd->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $attr = $this->user->getAttributes();
          // set relations objects
          $user = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$attr['id']);
          $catEnt->setCatalogueProp($user);
          $catEnt->setCatalogueOffers(0);
          $catEnt->setCatalogueDeleted(0);
          // add to catalogue table  
          $this->enMan->persist($catEnt);
          $this->enMan->flush();
          // update catalogues counter
          $qb = $this->enMan->createQueryBuilder();
          $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.userCatalogues', 'u.userCatalogues + 1')
          ->where('u.id_us = ?1')
          ->setParameter(1, (int)$attr['id'])
          ->getQuery();
          $p = $q->execute();
          // increment counter
          $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'cata');
          $flashSess->setFlash('addSuccess', 1);
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
        $flashSess->setFlash('formData', $data);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formAdd));
      } 
      return $this->redirect($this->generateUrl('catalogueAdd'));
    }
    return $this->render('CatalogueOffersBundle:Catalogues:add.html.php', array('add' => true, 'edit' => false, 'form' => $formAdd->createView(),
    'formErrors' => (array)$flashSess->getFlash('formErrors'), 'catalogueId' => 0, 'isSuccess' => (int)$flashSess->getFlash('addSuccess', -1), 'titleBox' => "Ajouter un catalogue"));
  }

  /**
   * Edits a catalogue.
   * @access public
   * @return Displayed template
   */
  public function editCatalogueAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 0)
    {
      $attr = $this->user->getAttributes();
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $attr = array('id' => (int)$request->attributes->get('user'));
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attr = array('id' => (int)$request->attributes->get('elUser1'));
    }
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $catEnt = new Catalogues;
    $catEnt->setDataAdded($this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCatalogueData($id, (int)$attr['id']));
    if($catEnt->getIdCat() != '')
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
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
        return $this->redirect($this->generateUrl('catalogueEdit', array('id' => $id)));
      }
      return $this->render('CatalogueOffersBundle:Catalogues:add.html.php', array('edit' => true, 'add' => false, 'form' => $formEdit->createView(),
      'formErrors' => (array)$flashSess->getFlash('formErrors'), 'catalogueId' => $id,
      'isSuccess' => (int)$flashSess->getFlash('editSuccess', -1), 'titleBox' => "Editer un catalogue"));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Deletes a catalogue.
   * @access public
   * @return Displayed template
   */
  public function deleteCatalogueAction(Request $request)
  {
    $flashSess = $request->getSession();
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $attr = $this->user->getAttributes();
    $result = 0;
    if($isTest == 1 && $testResult == 0)
    {
      $attr = array('id' => (int)$request->attributes->get('user'));
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attr = array('id' => (int)$request->attributes->get('elUser1'));
    }
    $catalogue = $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCatalogueData($id, (int)$attr['id']);
    $return = array('isError' => 1, 'message' => "Une erreur est survenue lors de la suppression du catalogue");
    if(isset($catalogue['id_cat']) && $catalogue['id_cat'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // we can delete the catalogue only when any offer isn't used in the actual ad
      // get all offers; we will delete offer by offer; the catalogue will be deleted at the end
      // if no offers, delete catalogue immediately
      // check if any offer participates in the ad
      $offersAd = (int)$this->enMan->getRepository('CatalogueOffersBundle:Offers')->catalogueOfferInAd($id);
      if((int)$catalogue['catalogueOffers'] == 0)
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          // delete catalogue first
          if($offersAd == 0)
          {
            $this->enMan->createQueryBuilder()->delete('Catalogue\OffersBundle\Entity\Catalogues', 'c')
            ->where('c.id_cat = ?1')
            ->setParameter(1, $id)
            ->getQuery()
            ->execute();
          }
          else
          {
            $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Catalogues', 'c')
            ->set('c.catalogueDeleted', '?1')
            ->where('c.id_cat = ?2')
            ->setParameter(1, 1)
            ->setParameter(2, $id)
            ->getQuery()
            ->execute();            
          }
          // update catalogues counter
          $qb = $this->enMan->createQueryBuilder();
          $q = $qb->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.userCatalogues', 'u.userCatalogues - 1')
          ->where('u.id_us = ?1')
          ->setParameter(1, (int)$attr['id'])
          ->getQuery();
          $p = $q->execute();
          // increment counter
          $this->enMan->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'cata');
          $flashSess->setFlash('addSuccess', 1);
          $result = 1;
          $return['isError'] = 0;
          $return['message'] = "Le catalogue a été correctement supprimé";
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          if($this->isTest)
          {
            return new Response('deleted_successfully');
          }
        }
        catch(Exception $e)
        {
          $this->enMan->getConnection()->rollback();
          $this->enMan->close();
          throw $e;
        }
      }
      if(isset($_GET['r']) && $_GET['r'] == 'json')
      {
        echo json_encode($result);
        die();
      }
      return $this->render('CatalogueOffersBundle:Catalogues:delete.html.php', array('catalogue' => $catalogue, 'inAd' => (bool)($offersAd > 0), 'offersAd' => $offersAd, 
      'perQueue' => $this->config['pager']['perPageQueue'], 'ticket' => $this->sessionTicket));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * User's catalogues list.
   * @access public
   * @return Displayed template
   */
  public function listUserCataloguesAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $catalogues = $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')
    ->getCataloguesByUser(array('column' => $column, 'how' => $how,
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['catalogues'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('CatalogueOffersBundle:Catalogues:cataloguesTable.html.php', array('catalogues' => $catalogues, 'pager' => $pager->setPages(), 'page' => $page,
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('nom', 'nombre_offres')), 'how' => $how, 'column' => $column));
    }
    return $this->render('CatalogueOffersBundle:Catalogues:listUserCatalogues.html.php', array('catalogues' => $catalogues, 'pager' => $pager->setPages(), 'page' => $page,
    'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('nom', 'nombre_offres')), 'how' => $how, 'column' => $column));
  }

  /**
   * User's catalogues list.
   * @access public
   * @return Displayed template
   */
  public function listCataloguesByUserAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $id = (int)$request->attributes->get('id');
    $catalogues = $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')
    ->getCataloguesByUser(array(
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), $id);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('UserProfilesBundle:Users')->getStats('userCatalogues', $id),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('CatalogueOffersBundle:Catalogues:listCataloguesByUser.html.php', array('userId' => $id, 'catalogues' => $catalogues, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket));
  }

  /**
   * Show catalogue's content.
   * + cache saving
   * @access public
   * @return Displayed template.
   */
  public function showCatalogueAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $id = (int)$request->attributes->get('id');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $cacheName = $this->config['cache']['catalogues'].$id.'/page_'.$page.'_'.$column.'_'.$how;
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')
    ->getOffersByCatalogue(array('cacheName' => $cacheName, 'date' => $this->config['sql']['dateFormat'], 'how' => $how, 'column' => $column,
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), $id);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getOffersCount($id),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('CatalogueOffersBundle:Catalogues:catalogueTable.html.php', array('offers' => $offers, 'page' => $page, 'catalogueId' => $id, 'catalogueUrl' => $request->attributes->get('url'), 'pager' => $pager->setPages(), 'url' => $request->attributes->get('url'),
      'class' => $helper->getClassesBySorter($how, $column, array('titre', 'prix', 'date', 'categorie')), 'how' => $how, 'column' => $column));
    }
    return $this->render('CatalogueOffersBundle:Catalogues:listCatalogueContent.html.php', array('user' => (int)$request->attributes->get('user'), 'url' => $request->attributes->get('url'),
	'class' => $helper->getClassesBySorter($how, $column, array('titre', 'prix', 'date', 'categorie')), 'how' => $how, 'column' => $column,'catalogue' => $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getOneCatalogueData($id), 'page' => $page, 'id' => $id, 'offers' => $offers, 'pager' => $pager->setPages()));
  }

}