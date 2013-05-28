<?php
namespace Security;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Bundle\FrameworkBundle\EventListener\SessionListener as ParentListener;
use User\ProfilesBundle\Entity\UsersSessions;

class SessionListener extends ParentListener
{
    private $container;
    private $autoStart;

    public function __construct(ContainerInterface $container, $autoStart = false)
    {
        $this->container = $container;
        $this->autoStart = $autoStart;
		parent::__construct($container, $autoStart);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

      if(isset($_GET['sid']) && $_GET['sid'] !=  "" && ctype_alnum($_GET['sid'])) 
      {
// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/234', $_GET['sid']);
  // for uploadify calls, get the session id from the database
  // $_POST['sid'] isn't the real session id. It's the random key related to the session id.
        $session = $this->container->get('doctrine')->getEntityManager()->getRepository('UserProfilesBundle:UsersSessions')->getByRandom($_GET['sid']);
        if(isset($session['id_us']))
        {
          session_id($session['sessionUse']);
// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/aaa', $session['serverUse']);
          $_SESSION['serverData'] = unserialize($session['serverUse']);
    // $server = unserialize($session['serverUse']);
    // foreach($server as $d => $data)
    // {
      // $_SERVER[$d] = $data;
    // }
        }
      }
      parent::onKernelRequest($event);
    }
}
