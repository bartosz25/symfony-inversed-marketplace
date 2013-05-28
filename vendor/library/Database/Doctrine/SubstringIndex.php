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

class SubstringIndex extends FunctionNode
{

  public $sqlField = "";
  public $separator = "";
  public $length = ""; 
  public $name = "SUBSTRING_INDEX";

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function parse(\Doctrine\ORM\Query\Parser $parser)
  { 
    $parser->match(Lexer::T_IDENTIFIER);
    $parser->match(Lexer::T_OPEN_PARENTHESIS);
    $this->sqlField = $parser->ArithmeticExpression();
    $parser->match(Lexer::T_COMMA);
    $this->separator = $parser->StringPrimary();
    $parser->match(Lexer::T_COMMA);
    $this->length = $parser->SimpleArithmeticExpression();
    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
  }
  
  public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
  {
    return 'SUBSTRING_INDEX('.$sqlWalker->walkArithmeticExpression($this->sqlField).', '.$sqlWalker->walkStringPrimary($this->separator).','.$sqlWalker->walkStringPrimary($this->length).')';
  }
}