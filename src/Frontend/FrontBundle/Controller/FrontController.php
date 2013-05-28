<?php

namespace Frontend\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection as DI;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Security\Security;
use Security\FilterXss;
use Security\AuthenticationToken;
use Security\CsrfMaker;
use Validators\Csrf;
use Validators\CsrfValidator;

class FrontController extends Controller implements DI\ContainerAwareInterface {

  public $saltData = array(
    'years' => array(2011 => 'sdq32&', 2012 => 'Ctr%"', 2013 => 'µ*zny'),
    'months' => array(1 => '"""jz23', 2 => '02^sSE', 3 => '^d920^q', 4 => 'GzSSW<<>szoea', 5 => '$ssseC2;9', 
    6 => '|o9gdl;s', 7 => '&;$s$$q', 8 => '[sxdi', 9 => ':KFDe', 10 => '%orueh', 11 => '*soepz*$GzSSW<<>sGzSSW<<>s', 12 => '?,zzezosqQS'),
    'days' => array(1 => '@sdq_', 2 => 'nsdq&"', 3 => '%PzMpiny', 4 => '@34Ezez', 5 => '=*µµ**ds', 6 => '=JsozEEE', 7 => '!osuSD', 8 => '§dsq_E6_---', 9 => '/seok6|--|6|__|-',
    10 => '^tess^', 11 => '/T__^\42&\\', 12 => '%bv', 13 => '+oksq44923', 14 => ')odjnfhdfµµµ387', 15 => '$$dfssed==JS', 16 => '^dsqk44?S', 17 => '..:dsine2', 18 => '_JS2---2UIne', 19 => '.--:2ckssnSS2', 20 => '---PJNsuyz222', 21 => '!djsq-___$s',
    22 => '.24R909dzq^', 23 => '\vcx', 24 => '&n,n,,$', 25 => ',:nbfj', 26 => '!SDQ:!;ii', 27 => '$kmhhggfez', 28 => '%eksu2398', 29 => '++=KJSUY', 30 => '|USH23', 31 => '??sdqsd§!24')
  ); 
  public $fingerHash = array('start' => 'bi$^nsdo29',
  'end' => 'bongsiqo3%%1*');
  protected $csrfSalt = array('start' => 'sdqp292)à&1)', 'end' => 'sdqlsdqierqnjgsd');
  public $adminId;
  public $isTest = false;
  // France id from the database
  const FRANCE_ID = 1;

  /** 
   * List with controllers which lock the CSRF token generation.
   * @access protected
   * @var array
   */
// TODO : y rajouter toutes les méthodes de suppression / désabonnement / rejet du frontoffice
  protected $lockToken = array('User\AddressesBundle\Controller\AddressesController::addAddressAction',
  'User\ProfilesBundle\Controller\ProfilesController::editPasswordAction', 'User\ProfilesBundle\Controller\ProfilesController::editEmailAction',
  'User\ProfilesBundle\Controller\ProfilesController::editCardAction', 'Ad\ItemsBundle\Controller\ItemsController::addAdAction', 
  'Ad\ItemsBundle\Controller\ItemsController::editAdAction', 'Catalogue\OffersBundle\Controller\CataloguesController::addCatalogueAction',
  'Catalogue\OffersBundle\Controller\CataloguesController::editCatalogueAction', 'Catalogue\OffersBundle\Controller\OffersController::addOfferAction',
  'Catalogue\OffersBundle\Controller\OffersController::editOfferAction', 'Message\MessagesBundle\Controller\MessagesController::writeAction',
  'Message\MessagesBundle\Controller\MessagesController::readAction', 'Ad\QuestionsBundle\Controller\QuestionsController::readAction',
  'Ad\QuestionsBundle\Controller\RepliesController::editAction', 'User\AddressesBundle\Controller\AddressesController::editAddressAction',
  'User\ProfilesBundle\Controller\ProfilesController::registerAction', 'User\ProfilesBundle\Controller\ProfilesController::forgottenAction',
  'Ad\OpinionsBundle\Controller\AdsOpinionsController::write', 'User\AlertsBundle\Controller\UsersAdsAlertsController::subscribeAdAction',
  'User\AlertsBundle\Controller\UsersAlertsController::deleteAction', 'Coconout\BackendBundle\Controller\AdsController::acceptOrDenyAction',
  'Catalogue\OffersBundle\Controller\OffersController::deleteOffersQueueAction', 'Catalogue\OffersBundle\Controller\CataloguesController::deleteCatalogueAction',
'Coconout\BackendBundle\Controller\AdsController::editAction', 'Coconout\BackendBundle\Controller\AdsController::deleteAction',
'Coconout\BackendBundle\Controller\QuestionsController::editAction', 'Coconout\BackendBundle\Controller\QuestionsController::deleteAction',
'Coconout\BackendBundle\Controller\RepliesController::editAction', 'Coconout\BackendBundle\Controller\RepliesController::deleteAction',
'Coconout\BackendBundle\Controller\OffersController::editAction', 'Coconout\BackendBundle\Controller\OffersController::deleteAction',
'Coconout\BackendBundle\Controller\OffersImagesController::editAction', 'Coconout\BackendBundle\Controller\OffersImagesController::deleteAction',
'Coconout\BackendBundle\Controller\CataloguesController::editAction', 'Coconout\BackendBundle\Controller\CataloguesController::deleteAction',
'Coconout\BackendBundle\Controller\UsersController::editAction', 'Coconout\BackendBundle\Controller\UsersController::deleteAction',
'Coconout\BackendBundle\Controller\UsersController::activateAction', 'Coconout\BackendBundle\Controller\UsersController::sendCodesAction',
'Coconout\BackendBundle\Controller\TagsController::editAction', 'Coconout\BackendBundle\Controller\TagsController::deleteAction',
'Coconout\BackendBundle\Controller\TagsController::addAction', 'Coconout\BackendBundle\Controller\NewslettersController::sendAction');

  function setContainer(DI\ContainerInterface $container = null)
  {
    $this->container = $container;
    $this->initController();
  }

  public function initController()
  {
//echo $this->container->get('request')->get('_controller');
//die();
    if($this->container->get('kernel')->getEnvironment() == 'test')
    {
      // for test environment, we put AuthenticationToken manually
      $this->setTestToken();
      $this->isTest = true;
    }
    else
    {
      // user 
      $this->user = $this->container->get('security.context')->getToken();
    }
    // if(!$this->checkPage())
    // {
      // // redirect to the page "Only for not logged users"
      // return $this->redirect($this->generateUrl('accessDenied'));
      // die();
    // }
    // frontend page config
    $this->config = array(
    'pager' => array('perPage' =>  50, 'before' => 3, 'after' => 2, 'maxResults' => 3000, 'perPageQueue' => 2,
    'between' => 6));

    // cache structure
    $this->config['cache'] = array(
      'users' => 'users/', 'catalogues' => 'catalogues/', 'tags' => 'tags/', 'ads' => 'ads/', 'categories' => 'categories/',
      'cities' => 'cities/', 'regions' => 'regions/', 'offers' => 'offers/'
    );

    // entity manager
    $this->enMan = $this->get('doctrine')->getEntityManager(); 

    // we need to enregister some SQL functions (like DATE_FORMAT, SUBSTRING_INDEX, RAND)
    $this->enMan->getConfiguration()->addCustomDatetimeFunction('DATE_FORMAT', 'Database\Doctrine\DateFormat');
    $this->enMan->getConfiguration()->addCustomStringFunction('SUBSTRING_INDEX', 'Database\Doctrine\SubstringIndex');
    $this->enMan->getConfiguration()->addCustomStringFunction('RAND', 'Database\Doctrine\Rand');
    $this->enMan->getConfiguration()->addCustomStringFunction('REPLACE', 'Database\Doctrine\Replace');
    $this->enMan->getConfiguration()->addCustomStringFunction('CONCAT_WS', 'Database\Doctrine\ConcatWS');
    $this->enMan->getConfiguration()->addCustomStringFunction('DATEDIFF', 'Database\Doctrine\Datediff');
    $this->enMan->getConfiguration()->addCustomStringFunction('UNIX_TIMESTAMP', 'Database\Doctrine\UnixTimestamp');
    $this->enMan->getConfiguration()->addCustomStringFunction('NOW', 'Database\Doctrine\Now');

    // set cache handlerDoctrine\Common\Cache
    $this->cacheManager = new \Database\Doctrine\FileCache();
    $this->cacheManager->setBaseDir(rootDir.'cache/');
    $this->cacheManager->setCacheStructure($this->config['cache']);
    $this->enMan->getConfiguration()->setResultCacheImpl($this->cacheManager);
    $this->sqlFormatDatetime = "%d-%m-%Y %Hh%i"; // @deprecated, voir où utilisé
 

    // SQL configuration
    $this->config['sql'] = array('dateFormat' => '%d-%m-%Y %Hh%i', 'onlyDateFormat' => '%d-%m-%Y');

    // messages configuration
    $this->config['messages'] = array('maxMessages' => 25, 'maxRecipers' => 2);

    // XSS filter
    $this->filtXss = new FilterXss(FilterXss::STRICT_MODE, array());

    // mailing informations
    $this->from = array('mail' => 'bartkonieczny@yahoo.fr');

    // website configuration
    $this->config['site'] = array('domain' => 'gagu');

    // subscribtion configuration
    $this->config['subscribe'] = array('cats' => 20, 'ads' => 20);
 
    // RSS configuration
    $this->config['rss'] = array('perPage' => 50);

    // images configuration (max size etc.)
    $this->config['images'] = array('maxSize' => 1048576, 'offersTmpDir' => rootDir.'/web/images/offers/tmp/',
      'offersDir' => rootDir.'/web/images/offers/',
      'extensions' => array('jpg', 'jpeg', 'gif', 'png'),
      'messages' => array('fileExt' => 'Une mauvaise extension du fichier. Les extensions acceptées sont: {EXTENSIONS}',
                    'tooBig' => 'Le fichier est trop grande. La taille maximale est {MAXSIZE}',
                    'reqNotUpl' => 'Le transfert du fichier est obligatoire'),
      'variables' => array('{EXTENSIONS}', '{MAXSIZE}'),
      'defaults' => array('ads' => 'ad_default.jpg', 'offers' => 'offer_default.jpg',
                    'avatar' => 'user_avatar.jpg'),
      'configuration' => array(
        'avatar' => array('prefix' => array(''), 'dims' => array('150x50'), 'ratio' => array('AUTO')),
        'offer' => array('maxImages' => 2 /*5*/, 'prefix' => array('', 'small_', 'medium_'), 'dims' => array('150x300', '50x30', '125x103'), 'ratio' => array('AUTO', 'AUTO', 'AUTO'))
      )
    );

    // view data
    $this->config['view'] = array('dirs' => array('offersImg' => '/images/offers/', 'offersTmp' => '/images/offers/tmp/'));

    // uploadify
    $this->config['uploadify'] = array('ext' => '*.jpg;*.png;*.gif;*.jpeg', 'desc' => 'jpg, png et gif',
    'maxSize' => $this->config['images']['maxSize']);

    // dialog messages 
    $this->config['dialogMessages'] = array('errorGeneral' => "Une erreur inattendu s'est produite.");

    // config for deleted elements
    $this->config['deleted'] = array('offerDeleted' => 'Offre supprimée', 'adDeleted' => 'Annonce supprimée',
    'userDeleted' => 'Utilisateur supprimé');

    // admin's id in the users table
    $this->adminId = 1;

    // site's name
    $this->siteUrl = 'http://gagu/';

    // dirs config
    $this->config['dirs'] = array('templates' => rootDir.'templates/',
    'templateEbay' => rootDir.'templates/ebay/');

    // eBay config
    $this->config['ebay'] = array('creds' => $creds = array('dev' => "", 'app' => "", 'cert' => ""),
    'currency' => 'EUR', 'maxResults' => 3 /*50*/, 'maxPage' => 10, 'systemName' => 'ebay');

    // CSRF protection
    $sessionValues = $this->container->get('request')->getSession();
    $csrfClass = new CsrfMaker($sessionValues, $this->csrfSalt, array('session' => 'ticket', 'post' => 'ticket', 'get' => 'ticket'));
    $csrfClass->setLocked((bool)in_array($this->container->get('request')->get('_controller'), $this->lockToken));
// $csrfClass->setTimeLockingLimit(10);// 10 sec [TEST]
// $csrfClass->setTimeLimit(60); // 1 min [TEST]
    $csrfClass->initCsrf();
    $this->sessionTicket = $sessionValues->get('ticket');
  }

  /**
   * Checks if logged user can access the ressource.
   * @access private
   * @param string $checkedController Name of controller which will be checked.
   * @return Page redirection.
   */
  public function checkPage($checkedController)
  {
    $visitorPages = array('User\ProfilesBundle\Controller\ProfilesController::registerAction', 'User\ProfilesBundle\Controller\ProfilesController::forgottenAction',
    'User\ProfilesBundle\Controller\ProfilesController::loginAction', 'User\ProfilesBundle\Controller\ProfilesController::confirmAction',
    'User\ProfilesBundle\Controller\ProfilesController::forgottenNewAction');
    if(!$this->isTest && ($this->user instanceof AnonymousToken == false) && $this->user->isAuthenticated() && $this->getRequest()->attributes->get('_controller') == $checkedController)
    {
      // redirect to the page "Only for not logged users"
      Header("Location: ".$this->generateUrl('accessDenied'));
      die();
    }
  }

  /**
   * Checks if the page has to been render partially.
   * @access public
   * @return boolean True if partial rendering, false otherwise.
   */
  public function checkIfPartial()
  {
    return (bool)(isset($_GET['partial']) || isset($_POST['partial']));
  }
 

  /**
   * Checks if user is connected. 
   * @access public
   * @return True if connected, false otherwise.
   */
  public function checkIfConnected()
  {
    return (bool)($this->user instanceof AuthenticationToken && $this->user->isAuthenticated());
  }

  /**
   * Validates CSRF token.
   * @access public
   * @return bool True if token is correct, false otherwise.
   */
  public function validateCSRF()
  {
    $ticket = '';
    if(isset($_GET['ticket'])) $ticket = $_GET['ticket'];
    $constraint = new Csrf(array('message' => '', 'sessionToken' => $this->sessionTicket, 'field' => ''));
    $validator = new CsrfValidator;
    return $validator->isValid($ticket, $constraint);
  }

  /**
   * Executes access test for an user.
   * @access public
   * @return void
   */
  public function testAccess($testedValue, $receivedValue)
  {//echo "Tested value $testedValue :: received value $receivedValue";
    return (string)($testedValue == $receivedValue);
  }

  /**
   * Checks if user calls the page from subscriber newsletter.
   * @access public
   * @param string $type Type of visited page.
   * @return void
   */
  public function ifFromNewsletter($type)
  {
    if(isset($_GET['key']) && isset($_GET['src']) && $_GET['src'] == "subscribe" && ctype_alnum($_GET['key']))
    {
      $source = $_GET['src'];
      $field = "historyAdsVisits";
      if($type == "category")
      {
        $field = "historyCatsVisits";
      }
      $this->enMan->getRepository('UserProfilesBundle:UsersNewslettersHistory')->updateVisit($field, $_GET['key']);
    }
  }

  /**
   * Sets token when application is tested with PHPUnit. It was impossible do pass the session throught the $client.
   * It's why we create the AuthenticationToken manually.
   * @access public
   * @return void
   */
  public function setTestToken()
  {
    // test user from gagu_test database : bartosz6 / bartosz6  
    $auth = new AuthenticationToken('bartosz6', '25c46c85d36078b512ae90724fec9c58acf42fd0', 'frontend', array('ROLE_ADMIN'));
    $auth->setAttributes(array('id' => 6, 'email' => 'bartkonieczny@gmail.com'));
    $secContext = $this->container->get('security.context');
    $secContext->setToken($auth); 
    $this->user = $this->container->get('security.context')->getToken();
  }

  /**
   * Checks if the url doesn't contain the first page number. If it contains the number, make a 301 redirection 
   * to the page without the number.
   * @access public
   * @param string $requestUri Actual page link.
   * @param string $route Called route.
   * @param int $page Page parameter.
   * @param array $params Other parameters used in the route.
   * @return void
   // */
  // public function checkPageParam($requestUri, $route, $page, $params = array())
  // {echo $this->generateUrl($route, array_merge($params, array('page' => 2)));die();
    // if($requestUri == $this->generateUrl($route, array_merge($params, array('page' => 1))))
    // {echo 'page';die();
      // Header( "HTTP/1.1 301 Moved Permanently" );
      // Header("Location: ".$this->generateUrl($route, $params));
      // die();
    // }
  // }

// methods to get all form errors
  public function getAllFormErrors($formObj)
  {
    $children = $formObj->getChildren();
    $allErrors = array();
    foreach($children as $child)
    { 
      $vars = $child->createView()->getVars();
      $errors = $child->getErrors();
      foreach($errors as $error) 
      {
        $allErrors[$vars["name"]][] = $this->convertFormErrorObjToString($error);
      }
    }
    // For hidden fields, errors are appended to PARENT ($formObj), not for the children elements
    foreach($formObj->getErrors() as $e => $error)
    {
      $params = $error->getMessageParameters();
      if(isset($params["{{field}}"]))
      {
        $allErrors[$params["{{field}}"]][] = $error->getMessageTemplate();
      }
    }
    return $allErrors;
  }

  private function convertFormErrorObjToString($error) 
  {
    $errorMessageTemplate = $error->getMessageTemplate();
    foreach ($error->getMessageParameters() as $key => $value) 
    {
      $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
    }
    return $errorMessageTemplate;
  }

}