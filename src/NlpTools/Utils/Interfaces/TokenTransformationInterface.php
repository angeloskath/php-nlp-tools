<?php
namespace NlpTools\Utils\Interfaces;

/**
 * An interface for altering a token
 * @author Dan Cardin (yooper)
 */
interface TokenTransformationInterface
{
    /**
     * Accept a token and return either
     * 1) an unchanged token
     * 2) a modified token
     * 3) null
     */
    public function transform($token);
}
