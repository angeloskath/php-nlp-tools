<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use PHPUnit\Framework\TestCase;

/**
 * TODO: Add checks for the edges of the token list
 */
class WordDocumentTest extends TestCase
{
    /**
     * @var array<int, string>
     */
    protected array $tokens;

    protected function setUp(): void
    {
        $this->tokens = ["The", "quick", "brown", "fox", "jumped", "over", "the", "lazy", "dog"];
    }

    /**
     * Test that the WordDocument correctly represents the ith token
     */
    public function testTokenSelection(): void
    {
        foreach ($this->tokens as $i => $t) {
            // no context
            $doc = new WordDocument($this->tokens, $i, 0);
            [$w, $prev, $next] = $doc->getDocumentData();

            $this->assertEquals(
                $t,
                $w,
                sprintf('The %sth token should be %s not %s', $i, $t, $w)
            );

            // no context means prev,next are empty
            $this->assertCount(
                0,
                $prev
            );
            $this->assertCount(
                0,
                $next
            );
        }
    }

    /**
     * Start with the 5th word and increase the amount of context
     * until it reaches the edges of the token list. Check the
     * previous tokens.
     */
    public function testPrevContext(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $doc = new WordDocument($this->tokens, 4, $i);
            [$_, $prev, $_] = $doc->getDocumentData();

            $this->assertCount(
                $i,
                $prev,
                sprintf('With %d words context prev should be %d words long', $i, $i)
            );
            for (
                $j = 3,$y = $i - 1;
                $j >= 4 - $i;
                $y--,$j--
            ) {
                $this->assertEquals(
                    $this->tokens[$j],
                    $prev[$y]
                );
            }
        }
    }

    /**
     * Start with the 5th word and increase the amount of context
     * until it reaches the edges of the token list. Check the
     * next tokens.
     */
    public function testNextContext(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $doc = new WordDocument($this->tokens, 4, $i);
            [$_, $_, $next] = $doc->getDocumentData();

            $this->assertCount(
                $i,
                $next,
                sprintf('With %d words context next should be %d words long', $i, $i)
            );
            for ($j = 5; $j < 5 + $i; $j++) {
                $this->assertEquals(
                    $this->tokens[$j],
                    $next[$j - 5]
                );
            }
        }
    }
}
