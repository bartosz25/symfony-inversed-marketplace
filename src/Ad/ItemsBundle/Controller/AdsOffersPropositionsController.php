<?php
namespace Ad\ItemsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use Ad\ItemsBundle\Entity\AdsOffers;
use Others\Pager;
use User\ProfilesBundle\Entity\Users;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates; 

class AdsOffersPropositionsController extends FrontController
{

  /**
   * List of offers which an ad author wants to buy.
   * @return Displayed template
   */
  public function listPropositionsAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $propositions = $this->enMan->getRepository('AdItemsBundle:AdsOffersPropositions')
    ->getPropositionsListByUser(array('column' => $column, 'how' => $how, 
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('AdItemsBundle:AdsOffersPropositions')->countForUser((int)$userAttr['id']),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdItemsBundle:AdsOffersPropositions:propositionsTable.html.php', array('propositions' => $propositions, 'pager' => $pager->setPages(), 'page' => $page,
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('annonce', 'offre')), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdItemsBundle:AdsOffersPropositions:listPropositions.html.php', array('propositions' => $propositions, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('annonce', 'offre')), 'how' => $how, 'column' => $column));
  }

  /**
   * Accepts or denies a proposition to put the offer into ad. The action is done by offer's author.
   * @access public
   * @return JSON message.
   */
  public function acceptOrDenyAction(Request $request)
  {
    $ad = (int)$request->attributes->get('ad');
    $offer = (int)$request->attributes->get('offer');
    $type = $request->attributes->get('action');
    $userAttr = $this->user->getAttributes();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $ad = (int)$request->attributes->get('id');
      $offer = (int)$request->attributes->get('id2');
      $type = 'accepter';
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $ad = (int)$request->attributes->get('id');
      $offer = (int)$request->attributes->get('id2');
      $type = 'accepter';
      $validCSRF = true;
    }
    elseif($this->isTest)
    {
      $userAttr = array('id' => 2);
      $validCSRF = true;
    }
    $data = $this->enMan->getRepository('AdItemsBundle:AdsOffersPropositions')->propositonExists($ad, $offer, (int)$userAttr['id']);
    if($validCSRF === true && ($type == 'accepter' || $type == 'refuser') && isset($data['id_ad']) && $data['id_ad'] == $ad && isset($data['id_of']) && $offer == $data['id_of'])
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        $tplVals = array('{AD_TITLE}', '{OFFER_NAME}', '{LOGIN}');
        $realVals = array($data['adName'], $data['offerName'], $this->user->getUser());
        switch($type)
        {
          case 'accepter':
            // add offer to ads_offers table
            $aofEnt = new AdsOffers;
            $aofEnt->setAdsIdAd($this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $ad));
            $aofEnt->setOffersIdOf($this->enMan->getReference('Catalogue\OffersBundle\Entity\Offers', $offer));
            $aofEnt->setAddedDate('');
            $this->enMan->persist($aofEnt);
            $this->enMan->flush();

            $i = 1;

            // notify ad's author about the new offer
            $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/offer_accepted.message'));
            // $templateMail = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/offer_accepted.maildoc'));
            $title = "Proposition a été acceptée";
            $message = "Proposition a été correctement acceptée";

            // update ads_modified table with the last modification
            $this->enMan->getRepository('AdItemsBundle:AdsModified')->adModified($ad, 'offer_accepted');
          break;
          case 'refuser':
            $i = -1;
            $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/offer_denied.message'));
            // $templateMail = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/offer_denied.maildoc'));
            $title = "Proposition a été réfusée";
            $message = "Proposition a été correctement supprimée";
          break;
        }
        $q = $this->enMan->createQueryBuilder()->delete('Ad\ItemsBundle\Entity\AdsOffersPropositions', 'aop')
        ->where('aop.ads_id_ad = ?1 AND aop.offers_id_of = ?2 AND aop.users_id_us = ?3')
        ->setParameter(1, $ad)
        ->setParameter(2, $offer)
        ->setParameter(3, $userAttr['id'])
        ->getQuery();
        $p = $q->execute();

        // update offers quantity for this ad
        $this->enMan->getRepository('AdItemsBundle:Ads')->updateOffersQuantity($i, $ad);		

        // Send private message
        $author = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
        $messageVals = array(
          'title' => $title,
          'content' => $template,
          'type' => 2,
          'state' => 1
        );
        $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($author, $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $data['id_us']), $messageVals);

        $emtEnt = new EmailsTemplates;
        $mail = \Swift_Message::newInstance()
        ->setSubject($title)
        ->setFrom($this->from['mail'])
        ->setTo($data['email'])
        ->setContentType("text/html")
        ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
        $this->get('mailer')->send($mail);

        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        if($this->isTest)
        {
          return new Response('accepted_successfully');
        }
        $ret['isError'] = 0;
        $ret['message'] = $message;
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    elseif($validCSRF === false)
    {
      $ret['isError'] = 1;
      $ret['message'] = "Votre session a expiré. Veuillez réessayer";
    }
    else
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      $ret['isError'] = 1;
      $ret['message'] = "Une erreur s'est produite";
    }
    echo json_encode($ret);
	die();
 }

}