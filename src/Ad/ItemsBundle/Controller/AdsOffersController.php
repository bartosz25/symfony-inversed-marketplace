<?php
namespace Ad\ItemsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use Others\Pager;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates; 

class AdsOffersController extends FrontController
{

  /**
   * Lists offers which are participating on ads.
   * @access public
   * @return Displayed template
   */
  public function listActivatedAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $offers = $this->enMan->getRepository('AdItemsBundle:AdsOffers')
    ->getOffersListByUser(array('column' => $column, 'how' => $how, 'date' => $this->config['sql']['dateFormat'],
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('AdItemsBundle:AdsOffers')->countActivedOffers((int)$userAttr['id']),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdItemsBundle:AdsOffers:userAdsOffersTable.html.php', array('offers' => $offers, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdItemsBundle:AdsOffers:listActivated.html.php', array('offers' => $offers, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
  }

  /**
   * Removes offer from ad.
   * @access public
   * @return JSON message
   */
  public function removeFromAdAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $ad = (int)$request->attributes->get('ad');
    $offer = (int)$request->attributes->get('offer');
    $strictMode = true;
    // For the test we check if offer belongs to user
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $offer = (int)$request->attributes->get('id');
      $ad = (int)$request->attributes->get('id2');
      $strictMode = false;
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $offer = (int)$request->attributes->get('id');echo $offer;echo '---';
      $ad = (int)$request->attributes->get('id2');echo $ad;
      $strictMode = false;
      $validCSRF = true;
    }
    // check if ad isn't expired and if offer belongs to connected user
    $data = $this->enMan->getRepository('AdItemsBundle:AdsOffers')->checkOffer($ad, $offer, (int)$userAttr['id'], $strictMode);
    if($validCSRF === true && isset($data['id_ad']) && $data['id_ad'] == $ad && isset($data['id_of']) && $data['id_of'] == $offer)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try 
      {
        // check if offer was in the table ads as the choosen offer
        $wasChoosen = false;
        $text = '';
        if($data['adOffer'] == $offer)
        {
          $wasChoosen = true;
          // make offers_id_of to 0 in the table ads for this ad and this offer
          $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
          ->set('a.adOffer', 0)
          ->set('a.adTax', 0)
          ->set('a.adOfferPrice', 0)
          ->where('a.id_ad = ?1')
          ->setParameter(1, $ad)
          ->getQuery();
          $p = $q->execute();
          $text = "C'était une offre que vous avez sélectionnée. Vous devez choisir une nouvelle offre";
        }
        // update offers quantity for this ad
        $this->enMan->getRepository('AdItemsBundle:Ads')->updateOffersQuantity(-1, $ad);
        // delete offer proposition from ads_offers
        $q = $this->enMan->createQueryBuilder()->delete('Ad\ItemsBundle\Entity\AdsOffers', 'ao')
        ->where('ao.ads_id_ad = ?1 AND ao.offers_id_of = ?2')
        ->setParameter(1, $ad)
        ->setParameter(2, $offer)
        ->getQuery();
        $p = $q->execute();

        // update offers quantity for this ad
        $this->enMan->getRepository('AdItemsBundle:Ads')->updateOffersQuantity(-1, $ad);

        // notify ad's author about the retire of this offer
        // if offer was presented in the table ads as a choosen offer, add some supplementary text to message
        $vars = array('{OFFER_NAME}', '{AD_NAME}', '{LOGIN}', '{TEXT}');
        $values = array($data['offerName'], $data['adName'], $data['login'], $text); 
        $template = str_replace($vars, $values, file_get_contents(rootDir.'messages/offer_retired.message'));
        $title = "Une offre a été retirée de l'annonce ".$data['adName'];

        // Insert message into database
        $messageVals = array(
          'title' => $title,
          'content' => $template,
          'type' => 2,
          'state' => 1
        );
        $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']), $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $data['id_us']), $messageVals);


        // commit SQL transaction
        $this->enMan->getConnection()->commit();

        // send e-mail notyfication
        $emtEnt = new EmailsTemplates;
        $message = \Swift_Message::newInstance()
        ->setSubject($title)
        ->setFrom($this->from['mail'])
        ->setTo($data['email'])
        ->setContentType("text/html")
        ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
        $this->get('mailer')->send($message);

        $return['result'] = 1;
        $return['message'] = "L'offre a été correctement supprimée";
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
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      $return['result'] = 0;
      $return['message'] = "L'offre n'existe pas";
    }
    echo json_encode($return);
    die();
  }

  /**
   * Accept offer. On GET request we demand to choose accept type.
   * For POST request, we modify offer state for this ad.
   * @access public
   * @return JSON message
   */
  public function acceptAction(Request $request)
  {
    $ad = (int)$request->attributes->get('ad');
    $offer = (int)$request->attributes->get('offer');
    $userAttr = $this->user->getAttributes();
    // For the test we check if ad belongs to user and offer not
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $offer = (int)$request->attributes->get('id');
      $ad = (int)$request->attributes->get('id2');
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $offer = (int)$request->attributes->get('id');
      $ad = (int)$request->attributes->get('id2');
      $validCSRF = true;
    }
    $data = $this->enMan->getRepository('AdItemsBundle:AdsOffers')->checkOfferAd($ad, $offer, (int)$userAttr['id'], $this->config['sql']['onlyDateFormat']);
    // if ad doesn't belong to user or offer belongs to user
    if($validCSRF === true && !isset($data['id_of']) || $offer != $data['id_of'])
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      if($request->getMethod() == 'POST')
      {
        echo json_encode(array('result' => 0, 'message' => "Cette offre n'existe pas"));
        die();
      }
      return $this->redirect($this->generateUrl('badElement'));
    }
    if($request->getMethod() == 'POST')
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // check which action we must execute (only 2 accepted : "final" and "normal")
        $type = $request->request->get('type');
        $suffix = "";
        if($type == 'normal' || $type == 'final')
        {
          if($type == 'final')
          {
            $suffix = "L'annonce est désormais finie.";
          }
          $vars = array('{OFFER_NAME}', '{AD_NAME}', '{USER}');
          $values = array($data['offerName'], $data['adName'], $this->user->getUser());
          $template = str_replace($vars, $values, file_get_contents(rootDir.'messages/valid_offer_'.$type.'.message'));
          $title = "Votre offre est la meilleure dans l'annonce ".$data['adName'];

          // Insert message into database
          $messageVals = array(
            'title' => $title,
            'content' => $template,
            'type' => 2,
            'state' => 1
          );
          $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']), $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $data['id_us']), $messageVals);

          // send e-mail notyfication
          $message1 = \Swift_Message::newInstance()
          ->setSubject($title)
          ->setFrom($this->from['mail'])
          ->setTo($data['email'])
          ->setContentType("text/html")
          ->setBody($template);

          // update offers_id_of value
          $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
          ->set('a.adOffer', $offer)
          ->set('a.adTax', (float)$data['offerTax'])
          ->set('a.adOfferPrice', (float)$data['offerPrice'])
          ->where('a.id_ad = ?1')
          ->setParameter(1, $ad)
          ->getQuery();
          $p = $q->execute();
          // warn previous offer's author that he lost his advantage
          if((int)$data['adOffer'] != 0)
          {
            $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer((int)$data['adOffer']);
            $vars = array('{OFFER_NAME}', '{AD_NAME}', '{USER}');
            $values = array($offer['offerName'], $data['adName'], $this->user->getUser());
            $template = str_replace($vars, $values, file_get_contents(rootDir.'messages/offer_not_distinguished.message'));
            $title = "Une offre remplacée la vôtre dans l'annonce ".$data['adName'];

            // Insert message into database
            $messageVals = array(
              'title' => $title,
              'content' => $template,
              'type' => 2,
              'state' => 1
            );
            $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']), $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $offer['id_us']), $messageVals);

            // send e-mail notyfication
            $emtEnt = new EmailsTemplates;
            $message2 = \Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom($this->from['mail'])
            ->setTo($data['email'])
            ->setContentType("text/html")
            ->setBody($template);
            $this->get('mailer')->send($emtEnt->getHeaderTemplate().$message2.$emtEnt->getFooterTemplate());
          }
          $this->get('mailer')->send($emtEnt->getHeaderTemplate().$message1.$emtEnt->getFooterTemplate());
        }
        // update ads_modified table with the last modification
        $this->enMan->getRepository('AdItemsBundle:AdsModified')->adModified($ad, 'offer_accepted');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();

        $return['result'] = 1;
        $return['message'] = "L'offre a été validée en tant que favorite.".$suffix;
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
      echo json_encode($return);
      die();
    }
    elseif($validCSRF !== true)
    {
      echo json_encode(array('result' => 0, 'message' => "Votre session a expiré"));
      die();
    }
    else
    { 
      return $this->render('AdItemsBundle:AdsOffers:accept.html.php', array('offer' => $offer, 'ad' => $ad, 'data' => $data));
    }
  }

  /**
   * Lists offers for user's ads.
   * @access public
   * @return Displayed template
   */
  public function listByUserAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $offers = $this->enMan->getRepository('AdItemsBundle:AdsOffers')
    ->getOffersListByUserAds(array('column' => $column, 'how' => $how, 'date' => $this->config['sql']['dateFormat'],
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('AdItemsBundle:AdsOffers')->countOffersForUserAds((int)$userAttr['id']),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdItemsBundle:AdsOffers:offersTableAdsUser.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'page' => $page,
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('annonce', 'offre')), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdItemsBundle:AdsOffers:listByUser.html.php', array('offers' => $offers, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('annonce', 'offre')), 'how' => $how, 'column' => $column));
  }

  /**
   * Delete offer. Action is done only by ad's author.
   * @access public
   * @return JSON message
   */
  public function deleteAction(Request $request)
  {
    $ad = (int)$request->attributes->get('ad');
    $offer = (int)$request->attributes->get('offer');
    $userAttr = $this->user->getAttributes();
    // For the test we check if ad belongs to user and offer not
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $offer = (int)$request->attributes->get('id');
      $ad = (int)$request->attributes->get('id2');
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $offer = (int)$request->attributes->get('id');
      $ad = (int)$request->attributes->get('id2');
      $validCSRF = true;
    }
    $data = $this->enMan->getRepository('AdItemsBundle:AdsOffers')->checkOfferAd($ad, $offer, (int)$userAttr['id'], $this->config['sql']['onlyDateFormat']);
    // if ad doesn't belong to user or offer belongs to user
    if($validCSRF === false || !isset($data['id_of']) || $offer != $data['id_of'])
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      echo json_encode(array('isError' => 1, 'message' => "Cette offre n'existe pas"));
      die();
    }
    elseif($validCSRF === true && isset($data['id_of']) && $offer == $data['id_of']) 
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        $vars = array('{OFFER_NAME}', '{AD_NAME}', '{USER}');
        $values = array($data['offerName'], $data['adName'], $this->user->getUser());
        $template = str_replace($vars, $values, file_get_contents(rootDir.'messages/delete_offer.message'));
        $title = "Votre offre a été supprimée de l'annonce ".$data['adName'];

        // Insert message into database
        $messageVals = array(
          'title' => $title,
          'content' => $template,
          'type' => 2,
          'state' => 1
        );
        $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']), $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $data['id_us']), $messageVals);

        // update offers quantity for this ad
        $this->enMan->getRepository('AdItemsBundle:Ads')->updateOffersQuantity(-1, $ad);

        // send e-mail notyfication
        $message1 = \Swift_Message::newInstance()
        ->setSubject($title)
        ->setFrom($this->from['mail'])
        ->setTo($data['email'])
        ->setContentType("text/html")
        ->setBody($template);

        if((int)$data['adOffer'] != 0)
        {
          $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
          ->set('a.adOffer', '0')
          ->set('a.adTax', 0)
          ->set('a.adOfferPrice', 0)
          ->where('a.id_ad = ?1')
          ->setParameter(1, $ad)
          ->getQuery();
          $p = $q->execute();
        }
        // delete offer from ads_offers table
        $q2 = $this->enMan->createQueryBuilder()->delete('Ad\ItemsBundle\Entity\AdsOffers', 'ao')
        ->where('ao.ads_id_ad = ?1 AND ao.offers_id_of = ?2')
        ->setParameter(1, $ad)
        ->setParameter(2, $offer)
        ->getQuery();
        $p = $q2->execute();

        // update ads_modified table with the last modification
        $this->enMan->getRepository('AdItemsBundle:AdsModified')->adModified($ad, 'offer_deleted');

        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        $emtEnt = new EmailsTemplates;
        $this->get('mailer')->send($emtEnt->getHeaderTemplate().$message1.$emtEnt->getFooterTemplate());
        $this->get('mailer')->send($emtEnt->getHeaderTemplate().$message2.$emtEnt->getFooterTemplate());

        $return['isError'] = 0;
        $return['message'] = "L'offre a été supprimée";
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
      echo json_encode($return);
      die();
    }
  }

}