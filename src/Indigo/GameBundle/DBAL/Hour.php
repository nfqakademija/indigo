<?php

namespace Indigo\GameBundle\DBAL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

class Hour extends FunctionNode
{

    public $dateExpression;

    public function parse (Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql (SqlWalker $sqlWalker)
    {
        return 'HOUR(' . $this->dateExpression
            ->dispatch($sqlWalker) . ')';
    }
}