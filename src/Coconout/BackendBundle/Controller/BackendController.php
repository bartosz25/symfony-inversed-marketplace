<?php

namespace Coconout\BackendBundle\Controller;

use Frontend\FrontBundle\Controller\FrontController; 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection as DI;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Security\Security;
use Security\FilterXss;
use Security\AuthenticationToken;

class BackendController extends FrontController implements DI\ContainerAwareInterface {

  public $saltData = array(
    'years' => array(2011 => 'sdq32&', 2012 => 'Ctr%"', 2013 => 'µ*zny'),
    'months' => array(1 => '"""jz23', 2 => '02^sSE', 3 => '^d920^q', 4 => 'GzSSW<<>szoea', 5 => '$ssseC2;9', 
    6 => '|o9gdl;s', 7 => '&;$s$$q', 8 => '[sxdi', 9 => ':KFDe', 10 => '%orueh', 11 => '*soepz*$GzSSW<<>sGzSSW<<>s', 12 => '?,zzezosqQS'),
    'days' => array(1 => '@sdq_', 2 => 'nsdq&"', 3 => '%PzMpiny', 4 => '@34Ezez', 5 => '=*µµ**ds', 6 => '=JsozEEE', 7 => '!osuSD', 8 => '§dsq_E6_---', 9 => '/seok6|--|6|__|-',
    10 => '^tess^', 11 => '/T__^\42&\\', 12 => '%bv', 13 => '+oksq44923', 14 => ')odjnfhdfµµµ387', 15 => '$$dfssed==JS', 16 => '^dsqk44?S', 17 => '..:dsine2', 18 => '_JS2---2UIne', 19 => '.--:2ckssnSS2', 20 => '---PJNsuyz222', 21 => '!djsq-___$s',
    22 => '.24R909dzq^', 23 => '\vcx', 24 => '&n,n,,$', 25 => ',:nbfj', 26 => '!SDQ:!;ii', 27 => '$kmhhggfez', 28 => '%eksu2398', 29 => '++=KJSUY', 30 => '|USH23', 31 => '??sdqsd§!24')
  );
  protected $environment = 'dev';

  function setContainer(DI\ContainerInterface $container = null)
  {
    $this->container = $container;
    $this->initController();
  }

  public function initController()
  {
    parent::initController();
  }

  public function setBaseUrl($baseUrl)
  {
    $generator = $this->container->get('router')->getGenerator();
// var_dump($generator->getOption('baseurl'));
    $requestContext = $generator->getContext();
    $requestContext->setBaseUrl($baseUrl);
// var_dump($requestContext);
    $generator->setContext($requestContext);
// var_dump($generator);
  }

  public function getDevUrl()
  {
    return '/app_dev.php';
  }

  public function getProdUrl()
  {
    return '/';
  }

  public function getBackendUrl()
  {
    return '/coconout.php';
  }

  public function getRouteUrl()
  {
    $method = 'get'.ucfirst($this->environment).'Url';
    return $this->$method();
  }

}