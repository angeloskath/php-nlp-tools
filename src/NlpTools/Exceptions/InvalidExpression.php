<?php
namespace NlpTools\Exceptions;

/**
 * Used by the tokenization, primarily
 * @author Dan Cardin
 */
class InvalidExpression extends \Exception
{
    public static function invalidRegex($pattern, $replacement)
    {
        throw new InvalidExpression("The pattern '{$pattern}', and the replacement '{$replacement}' caused an error.");
    }
}
