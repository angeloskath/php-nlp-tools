<?php
namespace TextAnalysis\Exceptions;

/**
 * Used by the tokenization, primarily
 * @author dcardin
 */
class InvalidExpression extends \Exception
{
    static public function invalidRegex($pattern, $replacement)
    {
        throw new InvalidExpression("The pattern '{$pattern}', and the replacement '{$replacement}' caused an error.");
    }
}

