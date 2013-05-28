<?php
namespace Templating;

use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;

class ExtendedPhp extends PhpEngine 
{

  /**
   * Parses template and doesn't put it into the Response. In result, you can for exemple 
   * download the template instead of show it.
   * @access public
   * @param string $name Template's name to render.
   * @param array $parameters Array of rendered parameters.
   * @return string Rendered template.
   */
  public function parseTemplate( $name, array $parameters = array())
  {
    $storage = $this->load($name);
    // attach the global variables
    $parameters = array_replace($this->getGlobals(), $parameters);
    return $this->evaluate($storage, $parameters);
  }

}