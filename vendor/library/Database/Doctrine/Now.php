<?php
/**
* DoctrineExtensions Mysql Function Pack
*
* LICENSE
*
* This source file is subject to the new BSD license that is bundled
* with this package in the file LICENSE.txt.
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to kontakt@beberlei.de so I can send you a copy immediately.
*/

namespace Database\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class Now extends FunctionNode
{
  public $name = "NOW";

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function parse(\Doctrine\ORM\Query\Parser $parser)
  { 
    $parser->match(Lexer::T_IDENTIFIER);
    $parser->match(Lexer::T_OPEN_PARENTHESIS);
    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
  }

  public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
  {
    return 'NOW()';
  }
}