<?php
namespace User\AlertsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use User\AlertsBundle\Entity\UsersCategoriesAlerts;
use Category\CategoriesBundle\Entity\CategoriesModified;

class UsersCategoriesAlertsController extends FrontController
{

  /**
   * Subscribe to one category. If is called with AJAX, returns response in JSON. Otherwise, show template.
   * @access public
   * @return Displayed template if not called with AJAX, JSON otherwise.
   */
  public function subscribeCategoryAction(Request $request)
  {
    $category = (int)$request->attributes->get('category');
    $userAttr = $this->user->getAttributes();
    $response['isError'] = 1;
    $validCSRF = $this->validateCSRF();
    // First, check user's categories subscribe limit
    if($validCSRF === true && $userAttr['subscription']['cats'] < $this->config['subscribe']['cats'])
    {
      if(!$this->enMan->getRepository('UserAlertsBundle:UsersCategoriesAlerts')->alreadySubscribed($userAttr['id'], $category))
      {
        // start transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          // check if category exists in categories_modified
          if(!$this->enMan->getRepository('CategoryCategoriesBundle:CategoriesModified')->ifExists($category))
          {
            $camEnt = new CategoriesModified;
            // insert new modified element
            $camEnt->setData(array('modifiedCategory' => $this->enMan->getReference('Category\CategoriesBundle\Entity\Categories', $category),
            'modifiedText' => serialize(array()), 'modifiedFirstModif' => new \DateTime(),
            'modifiedLastUser' => 0
            ));
            $this->enMan->persist($camEnt);
            $this->enMan->flush();
          }
          else
          {
            $camEnt = $this->enMan->getReference('Category\CategoriesBundle\Entity\CategoriesModified', $category);
          }
          $ucaEnt = new UsersCategoriesAlerts;
          $ucaEnt->setData(array('alertUser' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']), 
          'alertCategory' => $camEnt, 'alertState' => 0, 'alertDate' => new \DateTime()));
          $this->enMan->persist($ucaEnt);
          $this->enMan->flush();

          // update categories subscribtions
          $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.aboCats', 'u.aboCats + 1')
          ->where('u.id_us = ?1')
          ->setParameter(1, (int)$userAttr['id'])
          ->getQuery()
          ->execute();

          // commit SQL transaction
          $this->enMan->getConnection()->commit();

          $response['isError'] = 0;
          $response['message'] = "Vous vous êtes correctement abonné à cette catégorie";
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
        $response['message'] = "Vous êtes déjà abonné à cette catégorie";
      }
    }
    else
    {
      $response['message'] = "La limite d'abonnements a été atteinte.";
    } 
    // $isAjax = (int)$request->request->get('isAjax');die();
    // In fonction of call type, return adapted response
    if($request->request->get('isAjax') !== null)
    {
      echo json_encode($response);
      die();
    }
    return $this->render('UserAlertsBundle:UsersCategoriesAlerts:subscribeCategory.html.php', array('response' => $response)); 
  }

}