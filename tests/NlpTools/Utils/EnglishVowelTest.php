<?php
namespace NlpTools\Utils;
use NlpTools\Utils\VowelAbstractFactory;

/**
 *
 * @author Dan Cardin
 */
class VowelTest extends \PHPUnit_Framework_TestCase
{
    public function testIsVowel()
    {       
        $vowelChecker = VowelAbstractFactory::factory("English");
        $this->assertTrue($vowelChecker->isVowel("man", 1));
    }
    
    public function testYIsVowel()
    {
        $vowelChecker = VowelAbstractFactory::factory("English");
        $this->assertTrue($vowelChecker->isVowel("try", 2));
    }
}


