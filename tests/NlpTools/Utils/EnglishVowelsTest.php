<?php

declare(strict_types=1);

namespace NlpTools\Utils;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Dan Cardin
 */
class EnglishVowelsTest extends TestCase
{
    public function testIsVowel(): void
    {
        $vowelsAbstractFactory = VowelsAbstractFactory::factory("English");
        $this->assertTrue($vowelsAbstractFactory->isVowel("man", 1));
    }

    public function testYIsVowel(): void
    {
        $vowelsAbstractFactory = VowelsAbstractFactory::factory("English");
        $this->assertTrue($vowelsAbstractFactory->isVowel("try", 2));
    }
}
