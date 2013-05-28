<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
// require_once($_SERVER["DOCUMENT_ROOT"]."/../vendor/symfony/src/Symfony/Component/ClassLoader/MapClassLoader.php");
// use Symfony\Component\ClassLoader\MapClassLoader;
// require_once($_SERVER["DOCUMENT_ROOT"]."/../vendor/library/Others/ClassLoader.php");
use Doctrine\Common\Annotations\AnnotationRegistry;
// $loader = new ClassLoader;
// $loader->setCacheFile($_SERVER["DOCUMENT_ROOT"]."/../app/config/classes_paths.php");

// $map = array(
  // "Symfony" => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
  // "Symfony\Component\DependencyInjection\ContainerAware" => __DIR__.'/../vendor/symfony/src/Symfony/Component/DependencyInjection/ContainerAware.php'
// );

// $loader = new MapClassLoader($map);
// // $loader->

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
    'Sensio'           => __DIR__.'/../vendor/bundles',
    'JMS'              => __DIR__.'/../vendor/bundles',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'   => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine'         => __DIR__.'/../vendor/doctrine/lib',
    'Monolog'          => __DIR__.'/../vendor/monolog/src',
    'Assetic'          => __DIR__.'/../vendor/assetic/src',
    'Metadata'         => __DIR__.'/../vendor/metadata/src',
    'Ad'             => __DIR__.'/../src',
    'Catalogue'             => __DIR__.'/../src',
    'Category'             => __DIR__.'/../src',
    'Frontend'             => __DIR__.'/../src',
    'Geography'             => __DIR__.'/../src',
    'Message'             => __DIR__.'/../src',
    'Order'             => __DIR__.'/../src',
    'User'             => __DIR__.'/../src',
    'Database'         => __DIR__.'/../vendor/library',
    'Validators'         => __DIR__.'/../vendor/library',
    'Security'         => __DIR__.'/../vendor/library',
    'Others'         => __DIR__.'/../vendor/library',
    'Ebay'         => __DIR__.'/../vendor/library',
    'Templating'         => __DIR__.'/../vendor/library',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__.'/../vendor/twig-extensions/lib',
    'Twig_'            => __DIR__.'/../vendor/twig/lib',
));
 
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallbacks(array(__DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs'));
}

$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

// Swiftmailer needs a special autoloader to allow
// the lazy loading of the init file (which is expensive)
require_once __DIR__.'/../vendor/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/lib/swift_init.php');
