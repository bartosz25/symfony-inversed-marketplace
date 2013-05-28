<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager;
use User\ProfilesBundle\Entity\UsersNewslettersHistory;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class NewslettersController extends BackendController
{

  /**
   * Organizes newsletter.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    if(isset($_GET['make']))
    {
      $this->enMan->getRepository('FrontendFrontBundle:Stats')->setNewsletterCount();
      return $this->redirect($this->generateUrl('newslettersList'));
    }
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $subscribers = $this->enMan->getRepository('UserProfilesBundle:Users')->getAllSubscribers(array('type' => 'all', 'dateFormat' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('UserProfilesBundle:Users')->countAllSubscribers('all'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Newsletters:list.html.php', array('subscribers' => $subscribers, 'pager' => $pager->setPages(), 
    'sendSuccess' => (int)$flashSess->getFlash('sendSuccess'), 'ticket' => $this->sessionTicket)); 
  }

  /**
   * Send a new newsletter.
   * @access public
   * @return Displayed template.
   */
  public function sendAction(Request $request)
  {
    if($this->validateCSRF() === false)
    {
      echo 'CSRF error';
      die();
    }
    if(isset($_GET['make']))
    {
      $this->enMan->getRepository('FrontendFrontBundle:Stats')->setNewsletterCount();
      return $this->redirect($this->generateUrl('newslettersSend'));
    }
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $subscribers1 = $this->enMan->getRepository('UserAlertsBundle:UsersAdsAlerts')->getNotSendSubscribers(array('dateFormat' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $subscribers2 = $this->enMan->getRepository('UserAlertsBundle:UsersCategoriesAlerts')->getNotSendSubscribers(array('dateFormat' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $merges = array_merge($subscribers1, $subscribers2);
    $subscribers = array();
    foreach($merges as $i => $item)
    {
      if(!array_key_exists($item['id_us'], $subscribers))
      {
        $subscribers[$item['id_us']] = $item;
      }
    }
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('news'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Newsletters:send.html.php', array('subscribers' => $subscribers, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket)); 
  }

  /**
   * Send mail with new informations of subscriber data.
   * This mail is send once a week.
   * @access public
   * @return Displayed template.
   */
  public function sendMailAction(Request $request)
  {
    if($this->validateCSRF() === false)
    {
      return false;
    }
    $result = array('result' => 0);
    $user = (int)$request->attributes->get('user');
    $helper = new FrontendHelper();
    // identification key for this newsletter
    $key = sha1(time().rand(0,9999));
    // first, get new informations by ads
    $adsInfos = $this->enMan->getRepository('AdItemsBundle:AdsModified')->getNewsByAd($user);
    $aInfos = array();
    if(count($adsInfos) == 0)
    {
      $aInfos[] = "pas de nouveautés";
    }
    else
    {
      foreach($adsInfos as $i => $info)
      {
        $modifs = unserialize($info['modifiedText']);
        $modifsReturn = array();
        foreach($modifs as $m => $modif)
        {
          $modifsReturn[] = date('d-m-Y H:i', $modif['date']).' : '.$modif['text'];
        }
        if(count($modifsReturn) == 0)
        {
          $modifsReturn[] = "pas de nouveautés";
        }
        $adUrl = $this->generateUrl('adsShowOne', array('id' => $info['id_ad'], 'category' => $info['categoryUrl'], 'url' => $helper->makeUrl($info['adName'])));
        $aInfos[] = '<li><a href="'.$this->siteUrl.''.$adUrl.'?src=subscribe&key='.$key.'">'.$info['adName'].'</a> '.implode('<br />-', $modifsReturn).'</li>';
      }
    }
    // secondly, get new informations by categories
    $catsInfos = $this->enMan->getRepository('CategoryCategoriesBundle:CategoriesModified')->getNewsByCategory($user);
    $cInfos = array();
    if(count($catsInfos) == 0)
    {
      $cInfos[] = "pas de nouveautés";
    }
    else
    {
      foreach($catsInfos as $i => $info)
      {
        $modifs = unserialize($info['modifiedText']);
        $modifsReturn = array();
        foreach($modifs as $m => $modif)
        {
          $modifsReturn[] = date('d-m-Y H:i', $modif['date']).' : '.$modif['text'];
        }
        if(count($modifsReturn) == 0)
        {
          $modifsReturn[] = "pas de nouveautés";
        }
        $catUrl = $this->generateUrl('adsByCategory', array('category' => $info['categoryUrl']));
        $cInfos[] = '<li><a href="'.$this->siteUrl.''.$catUrl.'?src=subscribe&key='.$key.'">'.$info['categoryName'].'</a> '.implode('<br />-', $modifsReturn).'</li>';
      }
    }
    // get and merge template
    $template = file_get_contents(rootDir.'mails/newsletter_subscriber.maildoc');
    // start SQL transaction
    $this->enMan->getConnection()->beginTransaction();
    try
    {
      // update alerts states and dates
      $this->enMan->createQueryBuilder()->update('User\AlertsBundle\Entity\UsersAdsAlerts', 'uaa')
      ->set('uaa.alertState', 1)
      ->set('uaa.alertDate', '?1')
      ->where('uaa.alertUser = ?2')
      ->setParameter(1, date('Y-m-d H:i:s'))
      ->setParameter(2, $user)
      ->getQuery()
      ->execute();
      $this->enMan->createQueryBuilder()->update('User\AlertsBundle\Entity\UsersCategoriesAlerts', 'uca')
      ->set('uca.alertState', 1)
      ->set('uca.alertDate', '?1')
      ->where('uca.alertUser = ?2')
      ->setParameter(1, date('Y-m-d H:i:s'))
      ->setParameter(2, $user)
      ->getQuery()
      ->execute();
      // get user e-mail address
      $userData = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($user);
      // mail's template
      // avoid backend.php in the URL, set app prefix
      $emtEnt = new EmailsTemplates;
      $this->setBaseUrl($this->getRouteUrl()); 
      $tplVals = array('{ADS_LIST}', '{CATEGORIES_LIST}', '{IMAGE_URL}');
      $realVals = array(implode('', $aInfos), implode('', $cInfos), $this->generateUrl('newsletterImage', array('key' => $key), true));
      $templateMail = str_replace($tplVals, $realVals, $template);
      $message = \Swift_Message::newInstance()
      ->setSubject("Nouveautés sur UneMeilleureOffre.com")
      ->setFrom($this->from['mail'])
      ->setTo($userData['email'])
      ->setContentType("text/html")
      ->setBody($emtEnt->getHeaderTemplate().$templateMail.$emtEnt->getFooterTemplate());
      $this->get('mailer')->send($message);
      // insert new row into users_newsletters_history
      $unhEnt = new UsersNewslettersHistory();
      $unhEnt->setData(array('historyUser' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $user),
      'historyKey' => $key, 'historyDate' => '', 'historyReceived' => 0, 'historyAdsVisits' => 0, 
      'historyCatsVisits' => 0));
      $this->enMan->persist($unhEnt);
      $this->enMan->flush();
      // return to coconout.php in the URL
      $this->setBaseUrl($this->getBackendUrl()); 
      // commit SQL transaction
      $this->enMan->getConnection()->commit();
      $result = array('result' => 1);
    }
    catch(Exception $e)
    {
      $this->enMan->getConnection()->rollback();
      $this->enMan->close();
      throw $e;
    }
    // check who is the next receiver (if nobody, end the sending)
    $subscribers1 = $this->enMan->getRepository('UserAlertsBundle:UsersAdsAlerts')->getNotSendSubscribers(array('maxResults' => 1, 'start' => 0));
    $subscribers2 = $this->enMan->getRepository('UserAlertsBundle:UsersCategoriesAlerts')->getNotSendSubscribers(array('maxResults' => 1, 'start' => 0));
    if(count($subscribers1) == 0 && count($subscribers2) == 0)      
    {
      // update alerts states and dates
      $this->enMan->createQueryBuilder()->update('User\AlertsBundle\Entity\UsersAdsAlerts', 'uaa')
      ->set('uaa.alertState', 0)
      ->getQuery()
      ->execute();
      $this->enMan->createQueryBuilder()->update('User\AlertsBundle\Entity\UsersCategoriesAlerts', 'uca')
      ->set('uca.alertState', 0)
      ->getQuery()
      ->execute();
      $this->enMan->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Stats', 's')
      ->set('s.statValue', 0)
      ->where('s.key_st = ?1')
      ->setParameter(1, 'news')
      ->getQuery()
      ->execute();
      $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\AdsModified', 'am')
      ->set('am.modifiedText', '?1')
      ->setParameter(1, '')
      ->getQuery()
      ->execute();
      $this->enMan->createQueryBuilder()->update('Category\CategoriesBundle\Entity\CategoriesModified', 'cm')
      ->set('cm.modifiedText', '?1')
      ->setParameter(1, '')
      ->getQuery()
      ->execute();
    }
    echo json_encode($result);
    die();
    
  }

}