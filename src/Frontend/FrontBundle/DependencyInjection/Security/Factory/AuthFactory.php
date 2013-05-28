<?php
namespace Frontend\FrontBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory ;

class AuthFactory extends AbstractFactory
{
  public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
  { 
    $providerId = 'security.authentication.provider.'.$id;
    $container
    ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.auth'))
    ->replaceArgument(0, new Reference($userProvider));
	
    $listenerId = 'security.authentication.listener.'.$id;
    $listener = $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.auth'))
    ->replaceArgument(2, $id);

    return array($providerId, $listenerId, $defaultEntryPoint);
  }

  public function getPosition()
  {
    return 'pre_auth';
  }

  public function getKey()
  {
    return 'auth';
  }

  public function addConfiguration(NodeDefinition $node)
  {

  }

  protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
  {
// echo 'createAuthProvider';die();
// $provider = 'security.authentication.provider.'.$id;
// $container
// ->setDefinition($provider, new DefinitionDecorator('auth.security.authentication.provider'))
// ->replaceArgument(0, new Reference($userProviderId))
// ->replaceArgument(2, 'secured_area')
// ->addArgument($id)
// ;
return 'security.authentication.provider.auth';
// return $provider;
  }

  protected function getListenerId()
  {
    return 'security.authentication.listener.auth';
  }  

}