<?php
namespace Ad\OpinionsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Ad\ItemsBundle\Entity\Ads;
use Others\Pager;
use Order\OrdersBundle\Entity\Orders;
use Ad\OpinionsBundle\Entity\AdsOpinions;
use Ad\OpinionsBundle\Form\Write;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates; 

class AdsOpinionsController extends FrontController
{

  /**
   * Write new comment
   * @return Displayed template.
   */
  public function writeAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $id = (int)$request->attributes->get('id');
    $alreadyWrote =  $this->enMan->getRepository('AdOpinionsBundle:AdsOpinions')->alreadyWrote($id, (int)$userAttr['id']);
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $alreadyWrote = false;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $alreadyWrote = false;
    }
    // get order data (only when connected user is seller or buyer)
    $order = $this->enMan->getRepository('OrderOrdersBundle:Orders')->getOrderByUsers($id, (int)$userAttr['id']);
    // check if connected user is seller or buyer and if he didn't write an opinion
    if(isset($order['id_ad']) && $order['id_ad'] == $id && (int)$order['adOffer'] != 0 && !$alreadyWrote)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $ordEnt = new Orders;
      $ordEnt->setOrderState((int)$order['orderState']);
      // check if the last state (if not, redirect to order form page)
      if($ordEnt->isTheLastState())
      {
        $aopEnt = new AdsOpinions;
        if(count($flashData = $flashSess->getFlash('formData')) > 0)
        {
          $aopEnt->setData(array('opinionTitle' => $flashData['Write']['opinionTitle'], 'opinionText' => $flashData['Write']['opinionText'],
          'opinionNote' => (int)$flashData['Write']['opinionNote']));
        }
        AdsOpinions::setSessionToken($this->sessionTicket);
        $aopEnt->setTicket($this->sessionTicket);
        $form = $this->createForm(new Write(), $aopEnt);
        // submit request
        $data = $request->request->all('Write');
        if($request->getMethod() == 'POST')
        {
          $form->bindRequest($request);
          if($form->isValid())
          {
            // start transaction
            $this->enMan->getConnection()->beginTransaction();
            try
            {
              $emailAddress = $order['buyerEmail'];
              $user2 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $order['buyerId']);
              if($userAttr['id'] != $order['id_us'])
              {
                $emailAddress = $order['email'];
                $user2 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$order['id_us']);
              }
              // add opinion
              $adRef = $this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $id);
              $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
              $aopEnt->setData(array('opinionAd' => $adRef, 'opinionAuthor' => $userRef, 'opinionReceiver' => $user2,
              'opinionTitle' => $data['Write']['opinionTitle'], 'opinionText' => $data['Write']['opinionText'], 'opinionNote' => (int)$data['Write']['opinionNote'],
              'opinionDate' => new \DateTime()));
              $this->enMan->persist($aopEnt);
              $this->enMan->flush();
              // add user note
		      $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
              ->set('u.userNotes', 'u.userNotes + ?1')
              ->set('u.userNotesQuantity', 'u.userNotesQuantity + 1')
              ->where('u.id_us = ?2')
              ->setParameter(1, (int)$data['Write']['opinionNote'])
              ->setParameter(2, (int)$userAttr['id'])
              ->getQuery()
              ->execute();
              // notify user about new opinion
              $tplVals = array('{USER}', '{ORDER}');
              $realVals = array($this->user->getUser(), $id);
              $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/notify_new_opinion.message'));

              $messageVals = array(
                'title' => "Nouvelle opinion pour la commande no ".$id,
                'content' => $template,
                'type' => 2,
                'state' => 1
              );

              // send informations about new state
              $message = \Swift_Message::newInstance()
              ->setSubject($messageVals['title'])
              ->setFrom($this->from['mail'])
              ->setTo($emailAddress)
              ->setContentType("text/html")
              ->setBody($template);  
              // Add new private message
              $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($userRef, $user2, $messageVals);
              // commit SQL transaction
              $this->enMan->getConnection()->commit();
              // Send e-mail notyfication
              $emtEnt = new EmailsTemplates;
              $this->get('mailer')->send($emtEnt->getHeaderTemplate().$message.$emtEnt->getFooterTemplate()); 
              $flashSess->setFlash('opinionSuccess', 1);
              return $this->redirect($formUrl);
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
            $flashSess->setFlash('formErrors', $this->getAllFormErrors($form));
          }
        }
        return $this->render('AdOpinionsBundle:Opinions:write.html.php', array('form' => $form->createView(),
        'id' => $id, 'isSuccess' => $flashSess->getFlash('opinionSuccess', -1), 'formErrors' => $flashSess->getFlash('formErrors', array())));
      }
      else
      {
        $flashSess->setFlash('formData', $data);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($form));
        return $this->redirect($this->generateUrl('opinionWrite', array('id' => $id)));
      }
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->render('AdOpinionsBundle:Opinions:alreadyWritten.html.php', array('id' => $id));
  }

  /**
   * Gets comments by user.
   * @access public
   * @return Displayed template.
   */
  public function listByUserAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $type = $request->attributes->get('type');
    $userAttr = $this->user->getAttributes();
    $options = array('column' => $column, 'how' => $how, 'maxResults' => $this->config['pager']['perPage'],
    'start' => $this->config['pager']['perPage']*($page-1), 'date' => $this->config['sql']['dateFormat']);
    if($type == 'ecrits')
    {
      $all = $this->enMan->getRepository('AdOpinionsBundle:AdsOpinions')->countGivenOpinions((int)$userAttr['id']);
      $opinions = $this->enMan->getRepository('AdOpinionsBundle:AdsOpinions')->getOpinionsByUser($options, (int)$userAttr['id'], 'author');
    }
    elseif($type == 'recus')
    {
      $all = $userAttr['stats']['opinions'];
      $opinions = $this->enMan->getRepository('AdOpinionsBundle:AdsOpinions')->getOpinionsByUser($options, (int)$userAttr['id'], 'receiver');
    }
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $all,
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if((int)$request->request->get('isAjax') == 1)
    {
      
      return $this->render('AdOpinionsBundle:Opinions:userOpinionsTable.html.php', array('opinions' => $opinions, 'pager' => $pager->setPages(),
      'type' => $type, 'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    elseif($isPartial)
    {
      return $this->render('AdOpinionsBundle:Opinions:userOpinionsTable.html.php', array('type' => $type, 'opinions' => $opinions, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdOpinionsBundle:Opinions:listByUser.html.php', array('opinions' => $opinions, 'pager' => $pager->setPages(),
    'type' => $type, 'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
  }

}