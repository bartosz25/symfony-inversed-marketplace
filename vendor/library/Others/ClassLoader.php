<?php
namespace Others;
require_once($_SERVER["DOCUMENT_ROOT"]."/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php");

class ClassLoader extends UniversalClassLoader
{
    // private $namespaces = array();
    // private $prefixes = array();
    // private $namespaceFallbacks = array();
    // private $prefixFallbacks = array();
    private $cacheFile = "";

    public function __construct($path)
    {
        $this->cacheFile = $path;
        if(file_exists($path))
        {
            require_once($path);
        }
    }
    
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            require $file;
        }
    }

}
