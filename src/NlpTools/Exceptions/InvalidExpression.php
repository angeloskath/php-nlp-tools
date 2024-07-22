<?php

declare(strict_types=1);

namespace NlpTools\Exceptions;

/**
 * Used by the tokenization, primarily
 * @author Dan Cardin
 */
class InvalidExpression extends \Exception
{
    public static function invalidRegex(string $pattern, string $replacement): never
    {
        throw new InvalidExpression(sprintf("The pattern '%s', and the replacement '%s' caused an error.", $pattern, $replacement));
    }
}
