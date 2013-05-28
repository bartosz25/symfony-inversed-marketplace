<?php
echo '(diiiii';die();
namespace Migapi\IndexBundle;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Migapi\IndexBundle\Twig\Extension\DemoExtension;

class ControllerListener
{
    protected $extension;

    public function __construct(DemoExtension $extension)
    { echo 'construct migapi';die();
        $this->extension = $extension;
    }

	
	
    public function onCoreController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->extension->setController($event->getController());
        }
    }
}
